<?php
/**
 * Payment Portal Helper Functions
 *
 * Shared functionality for the customer payment portal.
 * Handles token management, database operations, rate limiting,
 * and payment processing for published invoices.
 */

require_once __DIR__ . '/../../db_connect.php';

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
    $db = get_db_connection();

    $stmt = $db->prepare('SELECT * FROM portal_companies WHERE api_key_hash = ? LIMIT 1');
    $stmt->bind_param('s', $apiKeyHash);
    $stmt->execute();
    $company = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $db->close();
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
 * Read rate limits file with exclusive lock to prevent TOCTOU race conditions.
 * Returns the parsed array and keeps the file handle open for atomic updates.
 *
 * @param int $windowSeconds Time window for cleanup
 * @return array{rateLimits: array, handle: resource|null}
 */
function read_rate_limits_locked(int $windowSeconds = 900): array
{
    $rateFile = __DIR__ . '/rate_limits.json';
    $handle = fopen($rateFile, 'c+');
    if (!$handle) {
        return ['rateLimits' => [], 'handle' => null];
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return ['rateLimits' => [], 'handle' => null];
    }

    $content = stream_get_contents($handle);
    $rateLimits = json_decode($content, true) ?: [];

    // Clean up expired entries
    $now = time();
    foreach ($rateLimits as $key => $data) {
        if ($now - ($data['first_attempt'] ?? 0) > $windowSeconds) {
            unset($rateLimits[$key]);
        }
    }

    return ['rateLimits' => $rateLimits, 'handle' => $handle];
}

/**
 * Write rate limits and release the file lock.
 *
 * @param resource $handle File handle from read_rate_limits_locked
 * @param array $rateLimits Updated rate limits data
 */
function write_rate_limits_unlock($handle, array $rateLimits): void
{
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($rateLimits));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

/**
 * Check rate limiting for an IP address and action type.
 * Uses file locking to prevent race conditions under concurrent requests.
 *
 * @param string $ip Client IP address
 * @param int $maxAttempts Maximum attempts allowed (default: 10)
 * @param int $windowSeconds Time window in seconds (default: 900 = 15 minutes)
 * @param string $prefix Key prefix for different rate limit buckets (default: 'portal')
 * @return bool True if rate limit exceeded
 */
function is_rate_limited(string $ip, int $maxAttempts = 10, int $windowSeconds = 900, string $prefix = 'portal'): bool
{
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    $key = $prefix . '_' . hash('sha256', $ip);
    $isLimited = isset($rateLimits[$key]) && $rateLimits[$key]['count'] >= $maxAttempts;

    if ($handle) {
        // Write back cleaned data and release lock
        write_rate_limits_unlock($handle, $rateLimits);
    }

    return $isLimited;
}

/**
 * Record a rate-limited action attempt for an IP address.
 * Uses file locking to prevent race conditions under concurrent requests.
 *
 * @param string $ip Client IP address
 * @param string $prefix Key prefix for different rate limit buckets (default: 'portal')
 */
function record_rate_limit_attempt(string $ip, string $prefix = 'portal'): void
{
    $windowSeconds = 900;
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    if (!$handle) {
        return;
    }

    $now = time();
    $key = $prefix . '_' . hash('sha256', $ip);

    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'count' => 1,
            'first_attempt' => $now
        ];
    } else {
        $rateLimits[$key]['count']++;
    }

    write_rate_limits_unlock($handle, $rateLimits);
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
    $db = get_db_connection();
    $stmt = $db->prepare(
        'SELECT pi.*, pc.company_name, pc.company_logo_url,
                pc.stripe_account_id, pc.paypal_merchant_id,
                pc.square_merchant_id
         FROM portal_invoices pi
         JOIN portal_companies pc ON pi.company_id = pc.id
         WHERE pi.invoice_token = ?
         LIMIT 1'
    );
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();
    $stmt->close();
    $db->close();

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
    $db = get_db_connection();

    // Get company info from the first matching invoice
    $stmt = $db->prepare(
        'SELECT pi.company_id, pc.company_name, pc.company_logo_url,
                pc.stripe_account_id, pc.paypal_merchant_id,
                pc.square_merchant_id
         FROM portal_invoices pi
         JOIN portal_companies pc ON pi.company_id = pc.id
         WHERE pi.customer_token = ?
         LIMIT 1'
    );
    $stmt->bind_param('s', $customerToken);
    $stmt->execute();
    $result = $stmt->get_result();
    $company = $result->fetch_assoc();
    $stmt->close();

    if (!$company) {
        $db->close();
        return ['company' => null, 'invoices' => []];
    }

    // Get all invoices for this customer token
    $stmt = $db->prepare(
        'SELECT id, invoice_id, invoice_token, customer_name, customer_email,
                invoice_data, status, total_amount, balance_due, currency,
                due_date, created_at, updated_at
         FROM portal_invoices
         WHERE customer_token = ?
         ORDER BY due_date ASC'
    );
    $stmt->bind_param('s', $customerToken);
    $stmt->execute();
    $result = $stmt->get_result();

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['invoice_data'])) {
            $row['invoice_data'] = json_decode($row['invoice_data'], true);
        }
        $invoices[] = $row;
    }
    $stmt->close();
    $db->close();

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
    if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
        return ['success' => false, 'error' => 'Invalid payment amount'];
    }

    $db = get_db_connection();

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
    $stmt = $db->prepare(
        'INSERT INTO portal_payments
         (company_id, invoice_id, customer_name, amount, processing_fee,
          currency, payment_method, provider_payment_id, provider_transaction_id,
          reference_number, status, synced_to_argo, payment_environment, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW())
         ON DUPLICATE KEY UPDATE id=id'
    );
    $stmt->bind_param(
        'issddsssssss',
        $companyId, $invoiceId, $customerName, $amount, $processingFee,
        $currency, $paymentMethod, $providerPaymentId, $providerTransactionId,
        $referenceNumber, $status, $paymentEnvironment
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        error_log('Portal payment DB error: ' . $error);
        $stmt->close();
        $db->close();
        return [
            'success' => false,
            'message' => 'Failed to record payment'
        ];
    }

    // ON DUPLICATE KEY UPDATE sets affected_rows to 2 when a duplicate is found.
    // affected_rows == 1 means a new row was inserted.
    $affectedRows = $stmt->affected_rows;
    $paymentId = $stmt->insert_id;
    $stmt->close();

    if ($affectedRows !== 1 && !empty($providerPaymentId)) {
        // Duplicate payment detected — return existing reference
        $stmt = $db->prepare(
            'SELECT reference_number FROM portal_payments WHERE provider_payment_id = ? LIMIT 1'
        );
        $stmt->bind_param('s', $providerPaymentId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $db->close();
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
        $stmt = $db->prepare(
            'UPDATE portal_invoices
             SET balance_due = GREATEST(0, balance_due - ?),
                 status = CASE
                     WHEN balance_due - ? <= 0 THEN "paid"
                     WHEN balance_due - ? < total_amount THEN "partial"
                     ELSE status
                 END,
                 updated_at = NOW()
             WHERE company_id = ? AND invoice_id = ?'
        );
        $stmt->bind_param('dddis', $invoiceAmount, $invoiceAmount, $invoiceAmount, $companyId, $invoiceId);
        $stmt->execute();
        $stmt->close();
    }

    $db->close();

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
        $allowedOrigin = $_ENV['PORTAL_BASE_URL'] ?? $_ENV['APP_URL'] ?? 'https://argorobots.com';
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
    $allowedOrigin = $_ENV['PORTAL_BASE_URL'] ?? $_ENV['APP_URL'] ?? 'https://argorobots.com';
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, X-License-Key, X-Device-Id, Authorization');
    header('X-Content-Type-Options: nosniff');
}

/**
 * Get the client IP address
 *
 * @return string Client IP
 */
function get_client_ip(): string
{
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Only trust X-Forwarded-For when the request comes from a known trusted proxy.
    // Configure via environment: TRUSTED_PROXY_IPS="203.0.113.10,203.0.113.11"
    $trustedProxyConfig = $_ENV['TRUSTED_PROXY_IPS'] ?? getenv('TRUSTED_PROXY_IPS') ?: '';
    if (!empty($trustedProxyConfig) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $trustedProxies = array_map('trim', explode(',', $trustedProxyConfig));
        if (in_array($remoteAddr, $trustedProxies, true)) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
    }

    return $remoteAddr;
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

    $html = build_invoice_email_html([
        'customerName' => $customerName,
        'companyName' => $companyName,
        'invoiceId' => $invoiceId,
        'formattedAmount' => $formattedAmount,
        'formattedDueDate' => $formattedDueDate,
        'invoiceUrl' => $invoiceUrl,
    ]);

    try {
        $fromEmail = $_ENV['INVOICE_DEFAULT_FROM_EMAIL'] ?? getenv('INVOICE_DEFAULT_FROM_EMAIL') ?: 'noreply@argorobots.com';
        $fromName = $_ENV['INVOICE_DEFAULT_FROM_NAME'] ?? getenv('INVOICE_DEFAULT_FROM_NAME') ?: 'Argo Books';

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

    $html = build_payment_confirmation_email_html([
        'customerName' => $customerName,
        'companyName' => $companyName,
        'invoiceId' => $invoiceId,
        'formattedAmount' => $formattedAmount,
        'referenceNumber' => $referenceNumber,
        'paymentMethod' => $methodLabel,
    ]);

    try {
        $fromEmail = $_ENV['INVOICE_DEFAULT_FROM_EMAIL'] ?? getenv('INVOICE_DEFAULT_FROM_EMAIL') ?: 'noreply@argorobots.com';
        $fromName = $_ENV['INVOICE_DEFAULT_FROM_NAME'] ?? getenv('INVOICE_DEFAULT_FROM_NAME') ?: 'Argo Books';

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
 * Build the HTML for a payment confirmation email.
 * Uses table-based layout with inline styles for maximum email client compatibility.
 */
function build_payment_confirmation_email_html(array $params): string
{
    $customerName = htmlspecialchars($params['customerName'] ?? '');
    $companyName = htmlspecialchars($params['companyName'] ?? '');
    $invoiceId = htmlspecialchars($params['invoiceId'] ?? '');
    $formattedAmount = htmlspecialchars($params['formattedAmount'] ?? '');
    $referenceNumber = htmlspecialchars($params['referenceNumber'] ?? '');
    $paymentMethod = htmlspecialchars($params['paymentMethod'] ?? '');

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
                    <!-- Header bar -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #059669, #047857); padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700;">Payment Confirmed</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #374151; line-height: 1.5;">
                                Hi' . ($customerName ? ' ' . $customerName : '') . ',
                            </p>
                            <p style="margin: 0 0 24px; font-size: 16px; color: #374151; line-height: 1.5;">
                                Your payment to <strong>' . $companyName . '</strong> has been received. Here are the details:
                            </p>

                            <!-- Payment summary card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Invoice</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 14px; font-weight: 600; color: #111827;">' . $invoiceId . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Amount Paid</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 18px; font-weight: 700; color: #111827;">' . $formattedAmount . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Payment Method</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 14px; color: #111827;">' . $paymentMethod . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Reference</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 14px; font-family: monospace; color: #111827;">' . $referenceNumber . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Date</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 14px; color: #111827;">' . date('F j, Y') . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0; font-size: 13px; color: #9ca3af; line-height: 1.5; text-align: center;">
                                If you have any questions about this payment, please contact ' . $companyName . ' directly.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                Powered by <a href="https://argorobots.com" style="color: #2563eb; text-decoration: none;">Argo Books</a>
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

/**
 * Build the HTML for a generic invoice notification email.
 * Uses table-based layout with inline styles for maximum email client compatibility.
 */
function build_invoice_email_html(array $params): string
{
    $customerName = htmlspecialchars($params['customerName'] ?? '');
    $companyName = htmlspecialchars($params['companyName'] ?? '');
    $invoiceId = htmlspecialchars($params['invoiceId'] ?? '');
    $formattedAmount = htmlspecialchars($params['formattedAmount'] ?? '');
    $formattedDueDate = htmlspecialchars($params['formattedDueDate'] ?? '');
    $invoiceUrl = htmlspecialchars($params['invoiceUrl'] ?? '');

    $dueDateRow = '';
    if (!empty($formattedDueDate)) {
        $dueDateRow = '
                        <tr>
                            <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Due Date</td>
                            <td style="padding: 8px 0; text-align: right; font-size: 14px; color: #111827;">' . $formattedDueDate . '</td>
                        </tr>';
    }

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
                    <!-- Header bar -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb, #1e40af); padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: 700;">' . $companyName . '</h1>
                        </td>
                    </tr>
                    <!-- Body -->
                    <tr>
                        <td style="padding: 32px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #374151; line-height: 1.5;">
                                Hi' . ($customerName ? ' ' . $customerName : '') . ',
                            </p>
                            <p style="margin: 0 0 24px; font-size: 16px; color: #374151; line-height: 1.5;">
                                You have a new invoice from <strong>' . $companyName . '</strong>.
                            </p>

                            <!-- Invoice summary card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Invoice</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 14px; font-weight: 600; color: #111827;">' . $invoiceId . '</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Amount Due</td>
                                                <td style="padding: 8px 0; text-align: right; font-size: 18px; font-weight: 700; color: #111827;">' . $formattedAmount . '</td>
                                            </tr>' . $dueDateRow . '
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="' . $invoiceUrl . '" style="display: inline-block; padding: 14px 40px; background-color: #2563eb; color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px;">
                                            View &amp; Pay Invoice
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 28px 0 0; font-size: 13px; color: #9ca3af; line-height: 1.5; text-align: center;">
                                If you have any questions about this invoice, please contact ' . $companyName . ' directly.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 32px; border-top: 1px solid #e5e7eb; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af;">
                                Powered by <a href="https://argorobots.com" style="color: #2563eb; text-decoration: none;">Argo Books</a>
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
