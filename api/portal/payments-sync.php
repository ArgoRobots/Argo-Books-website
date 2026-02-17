<?php
/**
 * Portal Payments Sync API Endpoint
 *
 * GET  /api/portal/payments-sync?since={timestamp} - Pull new payments (Argo Books syncing)
 * POST /api/portal/payments-sync                    - Confirm payments as synced
 *
 * Requires API key authentication (Argo Books -> Server).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['GET', 'POST']);

// Authenticate the request
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$companyId = $company['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    handle_pull_payments($companyId);
} else {
    handle_confirm_sync($companyId);
}

/**
 * GET: Pull new payments since last sync
 */
function handle_pull_payments(int $companyId): void
{
    $since = $_GET['since'] ?? null;

    $db = get_db_connection();

    if ($since) {
        // Get payments created after the given timestamp
        $stmt = $db->prepare(
            'SELECT pp.*, pi.invoice_token, pi.customer_token
             FROM portal_payments pp
             LEFT JOIN portal_invoices pi ON pp.company_id = pi.company_id AND pp.invoice_id = pi.invoice_id
             WHERE pp.company_id = ?
               AND (pp.synced_to_argo = 0 OR pp.created_at > ?)
             ORDER BY pp.created_at ASC'
        );
        $stmt->bind_param('is', $companyId, $since);
    } else {
        // Get all unsynced payments
        $stmt = $db->prepare(
            'SELECT pp.*, pi.invoice_token, pi.customer_token
             FROM portal_payments pp
             LEFT JOIN portal_invoices pi ON pp.company_id = pi.company_id AND pp.invoice_id = pi.invoice_id
             WHERE pp.company_id = ? AND pp.synced_to_argo = 0
             ORDER BY pp.created_at ASC'
        );
        $stmt->bind_param('i', $companyId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
        $payments[] = [
            'id' => (int) $row['id'],
            'invoice_id' => $row['invoice_id'],
            'customer_name' => $row['customer_name'],
            'amount' => floatval($row['amount']),
            'processing_fee' => floatval($row['processing_fee']),
            'currency' => $row['currency'],
            'payment_method' => $row['payment_method'],
            'provider_payment_id' => $row['provider_payment_id'],
            'provider_transaction_id' => $row['provider_transaction_id'],
            'reference_number' => $row['reference_number'],
            'status' => $row['status'],
            'synced' => (bool) $row['synced_to_argo'],
            'created_at' => $row['created_at'],
        ];
    }
    $stmt->close();
    $db->close();

    send_json_response(200, [
        'success' => true,
        'payments' => $payments,
        'count' => count($payments),
        'timestamp' => date('c')
    ]);
}

/**
 * POST: Confirm payments as synced to Argo Books
 */
function handle_confirm_sync(int $companyId): void
{
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
    }

    $paymentIds = $data['payment_ids'] ?? [];

    if (empty($paymentIds) || !is_array($paymentIds)) {
        send_error_response(400, 'Missing or invalid payment_ids array.', 'MISSING_FIELDS');
    }

    // Sanitize IDs
    $paymentIds = array_map('intval', $paymentIds);
    $placeholders = implode(',', array_fill(0, count($paymentIds), '?'));
    $types = str_repeat('i', count($paymentIds));

    $db = get_db_connection();

    // Mark payments as synced (only for this company's payments)
    $stmt = $db->prepare(
        "UPDATE portal_payments
         SET synced_to_argo = 1
         WHERE company_id = ? AND id IN ({$placeholders})"
    );

    $params = array_merge([$companyId], $paymentIds);
    $types = 'i' . $types;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();
    $db->close();

    send_json_response(200, [
        'success' => true,
        'synced_count' => $affectedRows,
        'message' => "{$affectedRows} payment(s) marked as synced.",
        'timestamp' => date('c')
    ]);
}
