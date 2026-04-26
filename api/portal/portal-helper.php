<?php
/**
 * Payment Portal Helper Functions
 *
 * Shared functionality for the customer payment portal.
 * Handles token management, database operations, rate limiting,
 * and payment processing for published invoices.
 */

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../smtp_mailer.php';
require_once __DIR__ . '/../../rate_limit_helper.php';

/**
 * Generate a cryptographically secure token (48-character hex string = 192 bits of entropy)
 *
 * @return string 48-character hex token
 */
function generate_portal_token(): string
{
    return bin2hex(random_bytes(24));
}

/**
 * Generate a human-readable payment reference number
 *
 * @return string Reference number (e.g., "PAY-20250213-A3B7C9")
 */
function generate_reference_number(): string
{
    $date = date('Ymd');
    $random = strtoupper(bin2hex(random_bytes(3)));
    return "PAY-{$date}-{$random}";
}

/**
 * Authenticate an API request using the portal API key.
 * Supports both X-Api-Key and Authorization: Bearer headers.
 *
 * @return array|null Returns company data if valid, null otherwise
 */
function authenticate_portal_request(): ?array
{
    $providedApiKey = '';
    if (!empty($_SERVER['HTTP_X_API_KEY'])) {
        $providedApiKey = $_SERVER['HTTP_X_API_KEY'];
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            $providedApiKey = $matches[1];
        }
    }

    if (empty($providedApiKey)) {
        return null;
    }

    $apiKeyHash = hash('sha256', $providedApiKey);
    global $pdo;
    if ($pdo === null) {
        error_log('authenticate_portal_request: database connection unavailable');
        return null;
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM portal_companies WHERE api_key_hash = ? LIMIT 1');
        $stmt->execute([$apiKeyHash]);
        $company = $stmt->fetch();
    } catch (PDOException $e) {
        error_log('authenticate_portal_request: DB error: ' . $e->getMessage());
        return null;
    }

    return $company ?: null;
}

/**
 * Authenticate an API request using a premium license key.
 * Supports X-License-Key header and Authorization: Bearer header.
 *
 * @return array|null Returns ['license_key_hash' => string, 'subscription_id' => string] if valid, null otherwise
 */
function authenticate_license_request(): ?array
{
    $licenseKey = '';
    if (!empty($_SERVER['HTTP_X_LICENSE_KEY'])) {
        $licenseKey = $_SERVER['HTTP_X_LICENSE_KEY'];
    } elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
            $licenseKey = $matches[1];
        }
    }

    if (empty($licenseKey)) {
        return null;
    }

    global $pdo;
    if ($pdo === null) {
        error_log("License authentication failed: database connection unavailable");
        return null;
    }
    try {
        $stmt = $pdo->prepare("
            SELECT subscription_key, subscription_id, redeemed_at
            FROM premium_subscription_keys
            WHERE subscription_key = ?
        ");
        $stmt->execute([$licenseKey]);
        $premiumKey = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$premiumKey || $premiumKey['redeemed_at'] === null) {
            return null;
        }

        $stmt = $pdo->prepare("
            SELECT status, end_date
            FROM premium_subscriptions
            WHERE subscription_id = ?
        ");
        $stmt->execute([$premiumKey['subscription_id']]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$subscription) {
            return null;
        }

        $now = new DateTime();
        $endDate = new DateTime($subscription['end_date']);

        if (!in_array($subscription['status'], ['active', 'cancelled']) || $endDate <= $now) {
            return null;
        }

        return [
            'license_key_hash' => hash('sha256', $licenseKey),
            'subscription_id' => $premiumKey['subscription_id'],
        ];
    } catch (PDOException $e) {
        error_log("License authentication error: " . $e->getMessage());
        return null;
    }
}

/**
 * Authenticate a request using a device ID (for free features like Google Sheets).
 * Uses X-Device-Id header.
 *
 * @return string|null Device ID hash if present, null otherwise
 */
function authenticate_device_request(): ?string
{
    $deviceId = $_SERVER['HTTP_X_DEVICE_ID'] ?? '';
    if (empty($deviceId)) {
        return null;
    }
    return hash('sha256', $deviceId);
}

/**
 * Backwards-compatible alias for record_rate_limit_attempt().
 * Used by portal/invoice.php and portal/index.php for failed token lookups.
 */
function record_failed_lookup(string $ip): void
{
    record_rate_limit_attempt($ip, 'portal');
}

/**
 * Check and enforce rate limiting for payment endpoints.
 * Atomically checks the limit and records the attempt under a single file lock
 * to prevent concurrent requests from bypassing the limit.
 * Allows 20 payment attempts per IP per 15 minutes.
 * Sends a 429 response and exits if rate limited.
 */
function enforce_payment_rate_limit(): void
{
    $ip = get_client_ip();
    $maxAttempts = 20;
    $windowSeconds = 900;
    $prefix = 'payment';

    // Atomic check + increment under a single lock
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    $key = $prefix . '_' . hash('sha256', $ip);
    $isLimited = isset($rateLimits[$key]) && $rateLimits[$key]['count'] >= $maxAttempts;

    if ($isLimited) {
        if ($handle) {
            write_rate_limits_unlock($handle, $rateLimits);
        }
        send_error_response(429, 'Too many payment attempts. Please try again later.', 'RATE_LIMITED');
    }

    // Record this attempt while still holding the lock
    $now = time();
    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'count' => 1,
            'first_attempt' => $now
        ];
    } else {
        $rateLimits[$key]['count']++;
    }

    if ($handle) {
        write_rate_limits_unlock($handle, $rateLimits);
    }
}

/**
 * Get invoice data by invoice token (for customer-facing page)
 *
 * @param string $token Invoice token
 * @return array|null Invoice data or null if not found
 */
function get_invoice_by_token(string $token): ?array
{
    global $pdo;
    if ($pdo === null) {
        error_log('get_invoice_by_token: database connection unavailable');
        return null;
    }
    $stmt = $pdo->prepare(
        'SELECT pi.*, pc.company_name, pc.company_logo_url,
                pc.stripe_account_id, pc.paypal_merchant_id,
                pc.square_merchant_id
         FROM portal_invoices pi
         JOIN portal_companies pc ON pi.company_id = pc.id
         WHERE pi.invoice_token = ?
         LIMIT 1'
    );
    $stmt->execute([$token]);
    $invoice = $stmt->fetch();

    if ($invoice && !empty($invoice['invoice_data'])) {
        $invoice['invoice_data'] = json_decode($invoice['invoice_data'], true);
    }

    return $invoice ?: null;
}

/**
 * Get all invoices for a customer by customer token
 *
 * @param string $customerToken Customer portal token
 * @return array Array with 'company' and 'invoices' keys
 */
function get_invoices_by_customer_token(string $customerToken): array
{
    global $pdo;
    if ($pdo === null) {
        error_log('get_invoices_by_customer_token: database connection unavailable');
        return ['company' => null, 'invoices' => []];
    }

    // Get company info from the first matching invoice
    $stmt = $pdo->prepare(
        'SELECT pi.company_id, pc.company_name, pc.company_logo_url,
                pc.stripe_account_id, pc.paypal_merchant_id,
                pc.square_merchant_id
         FROM portal_invoices pi
         JOIN portal_companies pc ON pi.company_id = pc.id
         WHERE pi.customer_token = ?
         LIMIT 1'
    );
    $stmt->execute([$customerToken]);
    $company = $stmt->fetch();

    if (!$company) {
        return ['company' => null, 'invoices' => []];
    }

    // Get all invoices for this customer token
    $stmt = $pdo->prepare(
        'SELECT id, invoice_id, invoice_token, customer_name, customer_email,
                invoice_data, status, total_amount, balance_due, currency,
                due_date, created_at, updated_at
         FROM portal_invoices
         WHERE customer_token = ?
         ORDER BY due_date ASC'
    );
    $stmt->execute([$customerToken]);

    $invoices = [];
    while ($row = $stmt->fetch()) {
        if (!empty($row['invoice_data'])) {
            $row['invoice_data'] = json_decode($row['invoice_data'], true);
        }
        $invoices[] = $row;
    }

    return [
        'company' => $company,
        'invoices' => $invoices
    ];
}

/**
 * Record a portal payment and update the invoice balance
 *
 * @param array $params Payment parameters
 * @return array Result with success status
 */
function record_portal_payment(array $params): array
{
    if (!is_numeric($params['amount']) || $params['amount'] == 0) {
        return ['success' => false, 'error' => 'Invalid payment amount'];
    }
    $status = $params['status'] ?? 'completed';
    if ($params['amount'] < 0 && $status !== 'refunded') {
        return ['success' => false, 'error' => 'Negative amounts are only allowed for refunds'];
    }

    global $pdo;

    $companyId = $params['company_id'];
    $invoiceId = $params['invoice_id'];
    $customerName = $params['customer_name'] ?? '';
    $amount = $params['amount'];
    $processingFee = $params['processing_fee'] ?? 0.00;
    $currency = $params['currency'] ?? 'USD';
    $paymentMethod = $params['payment_method'];
    $providerPaymentId = $params['provider_payment_id'] ?? '';
    $providerTransactionId = $params['provider_transaction_id'] ?? '';
    $referenceNumber = $params['reference_number'] ?? generate_reference_number();
    $status = $params['status'] ?? 'completed';
    $paymentEnvironment = $params['payment_environment'] ?? null;

    // Use INSERT ... ON DUPLICATE KEY UPDATE to prevent race conditions on duplicate payments.
    // SCHEMA REQUIREMENT: A UNIQUE index on `provider_payment_id` is required in portal_payments
    // for this to work correctly. e.g.: ALTER TABLE portal_payments ADD UNIQUE INDEX (provider_payment_id);
    $stmt = $pdo->prepare(
        'INSERT INTO portal_payments
         (company_id, invoice_id, customer_name, amount, processing_fee,
          currency, payment_method, provider_payment_id, provider_transaction_id,
          reference_number, status, synced_to_argo, payment_environment, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW())
         ON DUPLICATE KEY UPDATE id=id'
    );

    try {
        $stmt->execute([
            $companyId, $invoiceId, $customerName, $amount, $processingFee,
            $currency, $paymentMethod, $providerPaymentId, $providerTransactionId,
            $referenceNumber, $status, $paymentEnvironment
        ]);
    } catch (PDOException $e) {
        error_log('Portal payment DB error: ' . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to record payment'
        ];
    }

    // ON DUPLICATE KEY UPDATE sets rowCount() to 2 when a duplicate is found.
    // rowCount() == 1 means a new row was inserted.
    $affectedRows = $stmt->rowCount();
    $paymentId = $pdo->lastInsertId();

    if ($affectedRows !== 1 && !empty($providerPaymentId)) {
        // Duplicate payment detected — return existing reference
        $stmt = $pdo->prepare(
            'SELECT reference_number FROM portal_payments WHERE provider_payment_id = ? LIMIT 1'
        );
        $stmt->execute([$providerPaymentId]);
        $existing = $stmt->fetch();
        return [
            'success' => true,
            'reference_number' => $existing['reference_number'] ?? $referenceNumber,
            'message' => 'Payment already recorded'
        ];
    }

    // Update invoice balance if payment completed.
    // Subtract only the invoice portion (excluding processing fee) from the balance.
    // The processing fee covers payment provider costs and is not part of the invoice total.
    if ($status === 'completed') {
        $invoiceAmount = max(0, $amount - $processingFee);

        // Single atomic UPDATE so two concurrent payments can never read the
        // same balance and overwrite each other (lost-update race).
        //
        // SET-clause order matters here: MySQL evaluates SET assignments
        // left-to-right and references to a column in subsequent assignments
        // see the NEW (just-assigned) value. We need the CASE to compare
        // against the OLD balance_due, so `status = CASE …` MUST come BEFORE
        // `balance_due = GREATEST(…)` — otherwise a $50 payment on a $100
        // invoice would compute new_balance=50 then evaluate (50 - 50 <= 0)
        // and incorrectly set status='paid' instead of 'partial'.
        $stmt = $pdo->prepare(
            'UPDATE portal_invoices
             SET status = CASE
                     WHEN balance_due - ? <= 0 THEN "paid"
                     WHEN balance_due - ? < total_amount THEN "partial"
                     ELSE status
                 END,
                 balance_due = GREATEST(0, balance_due - ?),
                 updated_at = NOW()
             WHERE company_id = ? AND invoice_id = ?'
        );
        $stmt->execute([$invoiceAmount, $invoiceAmount, $invoiceAmount, $companyId, $invoiceId]);
    }

    return [
        'success' => true,
        'payment_id' => $paymentId,
        'reference_number' => $referenceNumber,
        'message' => 'Payment recorded successfully'
    ];
}

/**
 * Get the available payment methods for a company
 *
 * @param array $company Company data from portal_companies
 * @return array Available payment methods
 */
function get_available_payment_methods(array $company): array
{
    $methods = [];

    if (!empty($company['stripe_account_id'])) {
        $methods[] = 'stripe';
    }
    if (!empty($company['paypal_merchant_id'])) {
        $methods[] = 'paypal';
    }
    if (!empty($company['square_merchant_id'])) {
        $methods[] = 'square';
    }

    return $methods;
}

/**
 * Send a JSON API response and exit
 *
 * @param int $statusCode HTTP status code
 * @param array $data Response data
 */
function send_json_response(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Send a JSON error response and exit
 *
 * @param int $statusCode HTTP status code
 * @param string $message Error message
 * @param string $errorCode Error code identifier
 */
function send_error_response(int $statusCode, string $message, string $errorCode = 'ERROR'): void
{
    send_json_response($statusCode, [
        'success' => false,
        'message' => $message,
        'errorCode' => $errorCode,
        'timestamp' => date('c')
    ]);
}

/**
 * Validate that the request method matches the expected method
 *
 * @param string|array $allowed Allowed HTTP method(s)
 */
function require_method($allowed): void
{
    if (is_string($allowed)) {
        $allowed = [$allowed];
    }

    // Handle preflight OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        $allowedOrigin = env('SITE_URL', 'https://argorobots.com');
        header('Access-Control-Allow-Origin: ' . $allowedOrigin);
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowed) . ', OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, X-License-Key, X-Device-Id, Authorization');
        http_response_code(204);
        exit;
    }

    if (!in_array($_SERVER['REQUEST_METHOD'], $allowed)) {
        send_error_response(405, 'Method not allowed. Allowed: ' . implode(', ', $allowed), 'METHOD_NOT_ALLOWED');
    }
}

/**
 * Set standard CORS and security headers for portal API responses
 */
function set_portal_headers(): void
{
    $originSource = env('SITE_URL', 'https://argorobots.com');
    $parsed = parse_url($originSource);
    $allowedOrigin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? 'argorobots.com');
    if (!empty($parsed['port'])) {
        $allowedOrigin .= ':' . $parsed['port'];
    }
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, X-License-Key, X-Device-Id, Authorization');
    header('X-Content-Type-Options: nosniff');
}

/**
 * Send a generic invoice notification email to a customer.
 * Uses PHPMailer with SMTP settings from environment variables.
 *
 * @param array $params Email parameters:
 *   - customerEmail: Recipient email
 *   - customerName: Recipient name
 *   - companyName: Sender business name
 *   - invoiceId: Invoice number for display
 *   - totalAmount: Invoice total
 *   - balanceDue: Current balance due
 *   - currency: Currency code (USD, CAD, etc.)
 *   - dueDate: Due date string (Y-m-d or similar)
 *   - invoiceUrl: Full URL to view/pay the invoice
 *   - portalUrl: Full URL to the customer's portal (optional)
 * @return array Result with 'success' and 'message'
 */
function send_invoice_notification(array $params): array
{
    $customerEmail = $params['customerEmail'] ?? '';
    $customerName = $params['customerName'] ?? '';
    $companyName = $params['companyName'] ?? '';
    $invoiceId = $params['invoiceId'] ?? '';
    $balanceDue = $params['balanceDue'] ?? 0;
    $currency = $params['currency'] ?? 'USD';
    $dueDate = $params['dueDate'] ?? '';
    $invoiceUrl = $params['invoiceUrl'] ?? '';

    if (empty($customerEmail) || empty($invoiceUrl)) {
        return ['success' => false, 'message' => 'Missing customer email or invoice URL'];
    }

    // Sanitize inputs against email header injection (strip CRLF and control chars)
    $customerName = preg_replace('/[\r\n\x00-\x1F]/', '', $customerName);
    $customerEmail = preg_replace('/[\r\n\x00-\x1F]/', '', $customerEmail);
    $companyName = preg_replace('/[\r\n\x00-\x1F]/', '', $companyName);

    $currencySymbol = $currency === 'CAD' ? 'CA$' : '$';
    $formattedAmount = $currencySymbol . number_format(floatval($balanceDue), 2) . ' ' . $currency;
    $formattedDueDate = $dueDate ? date('F j, Y', strtotime($dueDate)) : '';
    $subject = "Invoice {$invoiceId} from {$companyName}";

    $safeCompany = htmlspecialchars($companyName);
    $detailRows = [
        ['Invoice', htmlspecialchars($invoiceId), 'padding: 8px 0; text-align: right; font-size: 14px; font-weight: 600; color: #111827;'],
        ['Amount Due', htmlspecialchars($formattedAmount), 'padding: 8px 0; text-align: right; font-size: 18px; font-weight: 700; color: #111827;'],
    ];
    if (!empty($formattedDueDate)) {
        $detailRows[] = ['Due Date', htmlspecialchars($formattedDueDate)];
    }

    $html = build_portal_email_html([
        'headerGradient' => 'linear-gradient(135deg, #2563eb, #1e40af)',
        'headerTitle' => $companyName,         // escaped by helper
        'greetingName' => $customerName,       // escaped by helper
        'introHtml' => 'You have a new invoice from <strong>' . $safeCompany . '</strong>.',
        'detailRows' => $detailRows,
        'ctaButton' => ['url' => $invoiceUrl, 'text' => 'View & Pay Invoice', 'color' => '#2563eb'],
        'closingHtml' => 'If you have any questions about this invoice, please contact ' . $safeCompany . ' directly.',
    ]);

    try {
        $fromEmail = env('INVOICE_DEFAULT_FROM_EMAIL', 'noreply@argorobots.com');
        $fromName = env('INVOICE_DEFAULT_FROM_NAME', 'Argo Books');

        $mailer = create_smtp_mailer();
        if ($mailer) {
            $mailer->setFrom($fromEmail, $fromName);
            $mailer->addAddress($customerEmail, $customerName);
            $mailer->addReplyTo($fromEmail, $fromName);
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            $mailer->send();
            return ['success' => true, 'message' => 'Email sent'];
        }

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'Reply-To: ' . $fromEmail,
            'X-Mailer: ArgoBooks/1.0'
        ];

        $to = $customerName ? '"' . str_replace('"', '', $customerName) . '" <' . $customerEmail . '>' : $customerEmail;
        $result = mail($to, $subject, $html, implode("\r\n", $headers));

        if ($result) {
            return ['success' => true, 'message' => 'Email sent'];
        } else {
            error_log('Portal invoice notification: mail() returned false for ' . $customerEmail);
            return ['success' => false, 'message' => 'mail() returned false'];
        }
    } catch (\Throwable $e) {
        error_log('Portal invoice notification email failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()];
    }
}

/**
 * Send a payment confirmation email to the customer after a successful payment.
 *
 * @param array $params Email parameters:
 *   - customerEmail: Recipient email
 *   - customerName: Recipient name
 *   - companyName: Sender business name
 *   - invoiceId: Invoice number for display
 *   - amount: Payment amount (float)
 *   - currency: Currency code (USD, CAD, etc.)
 *   - referenceNumber: Payment reference number
 *   - paymentMethod: Payment method used (stripe, paypal, square)
 * @return array Result with 'success' and 'message'
 */
function send_payment_confirmation(array $params): array
{
    $customerEmail = $params['customerEmail'] ?? '';
    $customerName = $params['customerName'] ?? '';
    $companyName = $params['companyName'] ?? '';
    $invoiceId = $params['invoiceId'] ?? '';
    $amount = $params['amount'] ?? 0;
    $currency = $params['currency'] ?? 'USD';
    $referenceNumber = $params['referenceNumber'] ?? '';
    $paymentMethod = $params['paymentMethod'] ?? '';

    if (empty($customerEmail)) {
        return ['success' => false, 'message' => 'Missing customer email'];
    }

    // Sanitize inputs against email header injection (strip CRLF and control chars)
    $customerName = preg_replace('/[\r\n\x00-\x1F]/', '', $customerName);
    $customerEmail = preg_replace('/[\r\n\x00-\x1F]/', '', $customerEmail);
    $companyName = preg_replace('/[\r\n\x00-\x1F]/', '', $companyName);

    $currencySymbol = $currency === 'CAD' ? 'CA$' : '$';
    $formattedAmount = $currencySymbol . number_format(floatval($amount), 2) . ' ' . $currency;
    $subject = "Payment Confirmation - Invoice {$invoiceId}";

    $methodLabels = [
        'stripe' => 'Credit Card (Stripe)',
        'paypal' => 'PayPal',
        'square' => 'Credit Card (Square)',
    ];
    $methodLabel = $methodLabels[$paymentMethod] ?? ucfirst($paymentMethod);

    $safeCompany = htmlspecialchars($companyName);
    $detailRows = [
        ['Invoice', htmlspecialchars($invoiceId), 'padding: 8px 0; text-align: right; font-size: 14px; font-weight: 600; color: #111827;'],
        ['Amount Paid', htmlspecialchars($formattedAmount), 'padding: 8px 0; text-align: right; font-size: 18px; font-weight: 700; color: #111827;'],
        ['Payment Method', htmlspecialchars($methodLabel)],
        ['Reference', htmlspecialchars($referenceNumber), 'padding: 8px 0; text-align: right; font-size: 14px; font-family: monospace; color: #111827;'],
        ['Date', date('F j, Y')],
    ];

    $html = build_portal_email_html([
        'headerGradient' => 'linear-gradient(135deg, #059669, #047857)',
        'headerTitle' => 'Payment Confirmed',
        'greetingName' => $customerName,       // escaped by helper
        'introHtml' => 'Your payment to <strong>' . $safeCompany . '</strong> has been received. Here are the details:',
        'detailRows' => $detailRows,
        'ctaButton' => null,
        'closingHtml' => 'If you have any questions about this payment, please contact ' . $safeCompany . ' directly.',
    ]);

    try {
        $fromEmail = env('INVOICE_DEFAULT_FROM_EMAIL', 'noreply@argorobots.com');
        $fromName = env('INVOICE_DEFAULT_FROM_NAME', 'Argo Books');

        $mailer = create_smtp_mailer();
        if ($mailer) {
            $mailer->setFrom($fromEmail, $fromName);
            $mailer->addAddress($customerEmail, $customerName);
            $mailer->addReplyTo($fromEmail, $fromName);
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            $mailer->send();
            return ['success' => true, 'message' => 'Confirmation email sent'];
        }

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'Reply-To: ' . $fromEmail,
            'X-Mailer: ArgoBooks/1.0'
        ];

        $to = $customerName ? '"' . str_replace('"', '', $customerName) . '" <' . $customerEmail . '>' : $customerEmail;
        $result = mail($to, $subject, $html, implode("\r\n", $headers));

        if ($result) {
            return ['success' => true, 'message' => 'Confirmation email sent'];
        } else {
            error_log('Portal payment confirmation: mail() returned false for ' . $customerEmail);
            return ['success' => false, 'message' => 'mail() returned false'];
        }
    } catch (\Throwable $e) {
        error_log('Portal payment confirmation email failed: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()];
    }
}

/**
 * Build the HTML shell for a portal customer email (invoice notification or
 * payment confirmation). Uses table-based layout with inline styles for
 * maximum email client compatibility.
 *
 * @param array $params Keys:
 *   - headerGradient: string  CSS gradient for the header bar
 *   - headerTitle:    string  Title shown in the header bar
 *   - greetingName:   string  Optional name to append after "Hi"
 *   - introHtml:      string  Intro paragraph HTML (after greeting)
 *   - detailRows:     array   List of [label, valueHtml, valueStyle?] rows
 *   - ctaButton:      array|null  ['url' => ..., 'text' => ..., 'color' => ...]
 *   - closingHtml:    string  Closing paragraph HTML (above footer)
 */
function build_portal_email_html(array $params): string
{
    // Plain-text params — escaped by the helper. Callers should pass raw values.
    $headerTitle  = htmlspecialchars((string)($params['headerTitle'] ?? ''), ENT_QUOTES, 'UTF-8');
    $greetingName = htmlspecialchars((string)($params['greetingName'] ?? ''), ENT_QUOTES, 'UTF-8');

    // HTML-by-design params — callers must sanitize/escape any interpolated
    // user data before passing in. Named with *Html to flag the contract.
    $introHtml   = (string)($params['introHtml'] ?? '');
    $closingHtml = (string)($params['closingHtml'] ?? '');

    // Controlled CSS values — not user input.
    $headerGradient = $params['headerGradient'] ?? 'linear-gradient(135deg, #2563eb, #1e40af)';

    $detailRows = $params['detailRows'] ?? [];
    $ctaButton  = $params['ctaButton'] ?? null;

    $rowsHtml = '';
    foreach ($detailRows as $row) {
        // Row label is semantically plain text — escape here.
        // Row value may be pre-formatted HTML (e.g., bold amounts) — callers must escape.
        $label = htmlspecialchars((string)($row[0] ?? ''), ENT_QUOTES, 'UTF-8');
        $value = (string)($row[1] ?? '');
        $valueStyle = $row[2] ?? 'padding: 8px 0; text-align: right; font-size: 14px; color: #111827;';
        $rowsHtml .= '
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">' . $label . '</td>
                                                <td style="' . $valueStyle . '">' . $value . '</td>
                                            </tr>';
    }

    $ctaHtml = '';
    if ($ctaButton && !empty($ctaButton['url']) && !empty($ctaButton['text'])) {
        $ctaColor = $ctaButton['color'] ?? '#2563eb';
        $ctaHtml = '
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="' . htmlspecialchars($ctaButton['url'], ENT_QUOTES, 'UTF-8') . '" style="display: inline-block; padding: 14px 40px; background-color: ' . $ctaColor . '; color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                                            ' . htmlspecialchars($ctaButton['text'], ENT_QUOTES, 'UTF-8') . '
                                        </a>
                                    </td>
                                </tr>
                            </table>';
    }

    $greeting = 'Hi' . ($greetingName !== '' ? ' ' . $greetingName : '') . ',';

    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: ' . $headerGradient . '; padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700;">' . $headerTitle . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #374151; line-height: 1.5;">
                                ' . $greeting . '
                            </p>
                            <p style="margin: 0 0 24px; font-size: 16px; color: #374151; line-height: 1.5;">
                                ' . $introHtml . '
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">' . $rowsHtml . '
                                        </table>
                                    </td>
                                </tr>
                            </table>
' . $ctaHtml . '
                            <p style="margin: 28px 0 0; font-size: 13px; color: #9ca3af; line-height: 1.5; text-align: center;">
                                ' . $closingHtml . '
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 32px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                Powered by <a href="' . site_url() . '" style="color: #2563eb; text-decoration: none;">Argo Books</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}
