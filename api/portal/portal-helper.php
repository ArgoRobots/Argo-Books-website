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

    $db = get_db_connection();
    $stmt = $db->prepare('SELECT * FROM portal_companies WHERE api_key = ? LIMIT 1');
    $stmt->bind_param('s', $providedApiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $company = $result->fetch_assoc();
    $stmt->close();
    $db->close();

    return $company ?: null;
}

/**
 * Check rate limiting for token lookups by IP address
 *
 * @param string $ip Client IP address
 * @param int $maxAttempts Maximum failed lookups allowed (default: 10)
 * @param int $windowSeconds Time window in seconds (default: 900 = 15 minutes)
 * @return bool True if rate limit exceeded
 */
function is_rate_limited(string $ip, int $maxAttempts = 10, int $windowSeconds = 900): bool
{
    $rateFile = __DIR__ . '/rate_limits.json';

    $rateLimits = [];
    if (file_exists($rateFile)) {
        $content = file_get_contents($rateFile);
        $rateLimits = json_decode($content, true) ?: [];
    }

    $now = time();

    // Clean up expired entries
    foreach ($rateLimits as $key => $data) {
        if ($now - $data['first_attempt'] > $windowSeconds) {
            unset($rateLimits[$key]);
        }
    }

    $key = 'portal_' . md5($ip);

    if (!isset($rateLimits[$key])) {
        return false;
    }

    return $rateLimits[$key]['count'] >= $maxAttempts;
}

/**
 * Record a failed token lookup attempt for rate limiting
 *
 * @param string $ip Client IP address
 */
function record_failed_lookup(string $ip): void
{
    $rateFile = __DIR__ . '/rate_limits.json';
    $windowSeconds = 900;

    $rateLimits = [];
    if (file_exists($rateFile)) {
        $content = file_get_contents($rateFile);
        $rateLimits = json_decode($content, true) ?: [];
    }

    $now = time();
    $key = 'portal_' . md5($ip);

    // Clean up expired entries
    foreach ($rateLimits as $k => $data) {
        if ($now - $data['first_attempt'] > $windowSeconds) {
            unset($rateLimits[$k]);
        }
    }

    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'count' => 1,
            'first_attempt' => $now
        ];
    } else {
        $rateLimits[$key]['count']++;
    }

    file_put_contents($rateFile, json_encode($rateLimits), LOCK_EX);
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
 * Get payment history for a customer token
 *
 * @param string $customerToken Customer portal token
 * @return array Array of payment records
 */
function get_payments_by_customer_token(string $customerToken): array
{
    $db = get_db_connection();

    // First get all invoice_ids for this customer
    $stmt = $db->prepare(
        'SELECT pi.invoice_id, pi.company_id
         FROM portal_invoices pi
         WHERE pi.customer_token = ?'
    );
    $stmt->bind_param('s', $customerToken);
    $stmt->execute();
    $result = $stmt->get_result();

    $invoiceIds = [];
    $companyId = null;
    while ($row = $result->fetch_assoc()) {
        $invoiceIds[] = $row['invoice_id'];
        $companyId = $row['company_id'];
    }
    $stmt->close();

    if (empty($invoiceIds) || !$companyId) {
        $db->close();
        return [];
    }

    // Get payments for those invoices
    $placeholders = implode(',', array_fill(0, count($invoiceIds), '?'));
    $types = str_repeat('s', count($invoiceIds));

    $stmt = $db->prepare(
        "SELECT pp.*
         FROM portal_payments pp
         WHERE pp.company_id = ?
           AND pp.invoice_id IN ({$placeholders})
         ORDER BY pp.created_at DESC"
    );

    $params = array_merge([$companyId], $invoiceIds);
    $types = 'i' . $types;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
    $stmt->close();
    $db->close();

    return $payments;
}

/**
 * Record a portal payment and update the invoice balance
 *
 * @param array $params Payment parameters
 * @return array Result with success status
 */
function record_portal_payment(array $params): array
{
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

    // Check for duplicate payment (idempotency)
    if (!empty($providerPaymentId)) {
        $stmt = $db->prepare(
            'SELECT id, reference_number FROM portal_payments
             WHERE provider_payment_id = ? LIMIT 1'
        );
        $stmt->bind_param('s', $providerPaymentId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($existing) {
            $db->close();
            return [
                'success' => true,
                'reference_number' => $existing['reference_number'],
                'message' => 'Payment already recorded'
            ];
        }
    }

    // Insert payment record
    $stmt = $db->prepare(
        'INSERT INTO portal_payments
         (company_id, invoice_id, customer_name, amount, processing_fee,
          currency, payment_method, provider_payment_id, provider_transaction_id,
          reference_number, status, synced_to_argo, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())'
    );
    $stmt->bind_param(
        'issddssssss',
        $companyId, $invoiceId, $customerName, $amount, $processingFee,
        $currency, $paymentMethod, $providerPaymentId, $providerTransactionId,
        $referenceNumber, $status
    );

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        $db->close();
        return [
            'success' => false,
            'message' => 'Failed to record payment: ' . $error
        ];
    }
    $paymentId = $stmt->insert_id;
    $stmt->close();

    // Update invoice balance if payment completed
    if ($status === 'completed') {
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
        $stmt->bind_param('dddis', $amount, $amount, $amount, $companyId, $invoiceId);
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
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: ' . implode(', ', $allowed) . ', OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, Authorization');
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
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Api-Key, Authorization');
    header('X-Content-Type-Options: nosniff');
}

/**
 * Get the client IP address
 *
 * @return string Client IP
 */
function get_client_ip(): string
{
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
