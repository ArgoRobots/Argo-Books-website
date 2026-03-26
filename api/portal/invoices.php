<?php
/**
 * Portal Invoices API Endpoint
 *
 * POST /api/portal/invoices - Publish or update an invoice from Argo Books
 *
 * Requires API key authentication (Argo Books -> Server).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['POST']);

handle_publish_invoice();

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
    $passProcessingFee = filter_var($data['passProcessingFee'] ?? true, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

    if ($existing) {
        // Update existing invoice
        $stmt = $db->prepare(
            'UPDATE portal_invoices SET
                customer_name = ?, customer_email = ?, invoice_data = ?,
                status = ?, total_amount = ?, balance_due = ?,
                currency = ?, due_date = ?, pass_processing_fee = ?,
                updated_at = NOW()
             WHERE company_id = ? AND invoice_id = ?'
        );
        $stmt->bind_param(
            'ssssddssisis',
            $customerName, $customerEmail, $invoiceData,
            $status, $totalAmount, $balanceDue,
            $currency, $dueDate, $passProcessingFee,
            $companyId, $invoiceId
        );
    } else {
        // Create new invoice
        $environment = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
        $stmt = $db->prepare(
            'INSERT INTO portal_invoices
             (company_id, invoice_id, invoice_token, customer_token,
              customer_name, customer_email, invoice_data,
              status, total_amount, balance_due, currency, due_date,
              pass_processing_fee, environment, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->bind_param(
            'isssssssddssiss',
            $companyId, $invoiceId, $invoiceToken, $customerToken,
            $customerName, $customerEmail, $invoiceData,
            $status, $totalAmount, $balanceDue, $currency, $dueDate,
            $passProcessingFee, $environment
        );
    }

    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        $db->close();
        error_log('Portal invoice DB error: ' . $error);
        send_error_response(500, 'Failed to save invoice. Please try again.', 'DB_ERROR');
    }
    $stmt->close();
    $db->close();

    $portalBaseUrl = $_ENV['PORTAL_BASE_URL'] ?? 'https://argorobots.com';
    $invoiceUrl = $portalBaseUrl . '/invoice/' . $invoiceToken;
    $portalUrl = $portalBaseUrl . '/portal/' . $customerToken;

    // Send notification email if requested (never let email failure block the publish)
    $emailSent = false;
    $sendEmail = filter_var($data['sendEmail'] ?? false, FILTER_VALIDATE_BOOLEAN);
    if ($sendEmail && !empty($customerEmail)) {
        try {
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
        } catch (\Throwable $e) {
            error_log('Invoice publish email failed: ' . $e->getMessage());
            $emailSent = false;
        }
    }

    // Return currently connected payment methods so the desktop app stays in sync
    $paymentMethods = get_available_payment_methods($company);

    send_json_response(200, [
        'success' => true,
        'invoiceToken' => $invoiceToken,
        'customerToken' => $customerToken,
        'invoiceUrl' => $invoiceUrl,
        'portalUrl' => $portalUrl,
        'emailSent' => $emailSent,
        'payment_methods' => $paymentMethods,
        'message' => $existing ? 'Invoice updated' : 'Invoice published',
        'timestamp' => date('c')
    ]);
}
