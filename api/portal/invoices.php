<?php
/**
 * Portal Invoices API Endpoint
 *
 * POST /api/portal/invoices - Publish or update an invoice from Argo Books
 * GET  /api/portal/invoices?token={invoice_token} - Get invoice data for customer-facing page
 *
 * POST requires API key authentication (Argo Books -> Server).
 * GET uses invoice token for customer access (no login required).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['GET', 'POST']);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handle_get_invoice();
} else {
    handle_publish_invoice();
}

/**
 * GET: Retrieve invoice data by token (customer-facing)
 */
function handle_get_invoice(): void
{
    $token = $_GET['token'] ?? '';

    if (empty($token) || !preg_match('/^[a-fA-F0-9]{48}$/', $token)) {
        send_error_response(400, 'Invalid or missing invoice token.', 'INVALID_TOKEN');
    }

    // Rate limiting check
    $clientIp = get_client_ip();
    if (is_rate_limited($clientIp)) {
        send_error_response(429, 'Too many requests. Please try again later.', 'RATE_LIMITED');
    }

    $invoice = get_invoice_by_token($token);

    if (!$invoice) {
        record_failed_lookup($clientIp);
        send_error_response(404, 'Invoice not found.', 'NOT_FOUND');
    }

    // Determine available payment methods
    $paymentMethods = get_available_payment_methods($invoice);

    // Build customer-safe response (hide sensitive internal data)
    $response = [
        'success' => true,
        'invoice' => [
            'invoice_id' => $invoice['invoice_id'],
            'invoice_token' => $invoice['invoice_token'],
            'customer_name' => $invoice['customer_name'],
            'status' => $invoice['status'],
            'total_amount' => floatval($invoice['total_amount']),
            'balance_due' => floatval($invoice['balance_due']),
            'currency' => $invoice['currency'],
            'due_date' => $invoice['due_date'],
            'created_at' => $invoice['created_at'],
            'invoice_data' => $invoice['invoice_data'],
        ],
        'company' => [
            'name' => $invoice['company_name'],
            'logo_url' => $invoice['company_logo_url'],
        ],
        'payment_methods' => $paymentMethods,
        'timestamp' => date('c')
    ];

    send_json_response(200, $response);
}

/**
 * POST: Publish or update an invoice from Argo Books
 */
function handle_publish_invoice(): void
{
    // Authenticate the request
    $company = authenticate_portal_request();
    if (!$company) {
        send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
    }

    // Parse request body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
    }

    // Validate required fields
    $required = ['invoiceId', 'customerName', 'totalAmount', 'balanceDue'];
    $missing = [];
    foreach ($required as $field) {
        if (empty($data[$field]) && $data[$field] !== 0 && $data[$field] !== '0') {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        send_error_response(400, 'Missing required fields: ' . implode(', ', $missing), 'MISSING_FIELDS');
    }

    $db = get_db_connection();
    $companyId = $company['id'];
    $invoiceId = $data['invoiceId'];

    // Check if invoice already exists (update vs create)
    $stmt = $db->prepare(
        'SELECT id, invoice_token, customer_token FROM portal_invoices
         WHERE company_id = ? AND invoice_id = ? LIMIT 1'
    );
    $stmt->bind_param('is', $companyId, $invoiceId);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $invoiceToken = $existing['invoice_token'] ?? generate_portal_token();
    $customerToken = $existing['customer_token'] ?? '';

    // Determine customer token: use existing or generate new based on customer email
    $customerEmail = $data['customerEmail'] ?? '';
    if (empty($customerToken) && !empty($customerEmail)) {
        // Check if this customer already has a token with this company
        $stmt = $db->prepare(
            'SELECT customer_token FROM portal_invoices
             WHERE company_id = ? AND customer_email = ? AND customer_token != ""
             LIMIT 1'
        );
        $stmt->bind_param('is', $companyId, $customerEmail);
        $stmt->execute();
        $existingCustomer = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $customerToken = $existingCustomer['customer_token'] ?? generate_portal_token();
    } elseif (empty($customerToken)) {
        $customerToken = generate_portal_token();
    }

    // Prepare invoice_data JSON
    $invoiceData = json_encode($data['invoiceData'] ?? $data);
    $customerName = $data['customerName'];
    $totalAmount = floatval($data['totalAmount']);
    $balanceDue = floatval($data['balanceDue']);
    $currency = $data['currency'] ?? 'USD';
    $dueDate = $data['dueDate'] ?? null;
    $status = $data['status'] ?? 'sent';

    if ($existing) {
        // Update existing invoice
        $stmt = $db->prepare(
            'UPDATE portal_invoices SET
                customer_name = ?, customer_email = ?, invoice_data = ?,
                status = ?, total_amount = ?, balance_due = ?,
                currency = ?, due_date = ?, updated_at = NOW()
             WHERE company_id = ? AND invoice_id = ?'
        );
        $stmt->bind_param(
            'ssssddsssi',
            $customerName, $customerEmail, $invoiceData,
            $status, $totalAmount, $balanceDue,
            $currency, $dueDate, $companyId, $invoiceId
        );
    } else {
        // Create new invoice
        $stmt = $db->prepare(
            'INSERT INTO portal_invoices
             (company_id, invoice_id, invoice_token, customer_token,
              customer_name, customer_email, invoice_data,
              status, total_amount, balance_due, currency, due_date,
              created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->bind_param(
            'isssssssddss',
            $companyId, $invoiceId, $invoiceToken, $customerToken,
            $customerName, $customerEmail, $invoiceData,
            $status, $totalAmount, $balanceDue, $currency, $dueDate
        );
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        $db->close();
        send_error_response(500, 'Failed to save invoice: ' . $error, 'DB_ERROR');
    }
    $stmt->close();
    $db->close();

    $portalBaseUrl = $_ENV['PORTAL_BASE_URL'] ?? 'https://argorobots.com';
    $invoiceUrl = $portalBaseUrl . '/invoice/' . $invoiceToken;
    $portalUrl = $portalBaseUrl . '/portal/' . $customerToken;

    // Send notification email if requested
    $emailSent = false;
    $sendEmail = filter_var($data['sendEmail'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if ($sendEmail && !empty($customerEmail)) {
        $emailResult = send_invoice_notification([
            'customerEmail' => $customerEmail,
            'customerName' => $customerName,
            'companyName' => $company['company_name'],
            'invoiceId' => $invoiceId,
            'totalAmount' => $totalAmount,
            'balanceDue' => $balanceDue,
            'currency' => $currency,
            'dueDate' => $dueDate,
            'invoiceUrl' => $invoiceUrl,
            'portalUrl' => $portalUrl,
        ]);
        $emailSent = $emailResult['success'];
    }

    send_json_response(200, [
        'success' => true,
        'invoiceToken' => $invoiceToken,
        'customerToken' => $customerToken,
        'invoiceUrl' => $invoiceUrl,
        'portalUrl' => $portalUrl,
        'emailSent' => $emailSent,
        'message' => $existing ? 'Invoice updated' : 'Invoice published',
        'timestamp' => date('c')
    ]);
}
