<?php
/**
 * Portal Status API Endpoint
 *
 * GET /api/portal/status - Check portal connection status and company info
 *
 * Requires API key authentication (Argo Books -> Server).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['GET']);

// Authenticate the request
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$companyId = $company['id'];
$db = get_db_connection();

// Get invoice and payment statistics
$stmt = $db->prepare(
    'SELECT
         COUNT(*) as total_invoices,
         SUM(CASE WHEN status IN ("sent", "viewed", "partial", "overdue", "pending") THEN 1 ELSE 0 END) as active_invoices,
         SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid_invoices,
         SUM(CASE WHEN status IN ("sent", "viewed", "partial", "overdue", "pending") THEN balance_due ELSE 0 END) as total_outstanding
     FROM portal_invoices
     WHERE company_id = ?'
);
$stmt->bind_param('i', $companyId);
$stmt->execute();
$invoiceStats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get unsynced payment count
$stmt = $db->prepare(
    'SELECT COUNT(*) as unsynced_count,
            COALESCE(SUM(amount), 0) as unsynced_amount
     FROM portal_payments
     WHERE company_id = ? AND synced_to_argo = 0 AND status = "completed"'
);
$stmt->bind_param('i', $companyId);
$stmt->execute();
$syncStats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get this month's online revenue
$stmt = $db->prepare(
    'SELECT COALESCE(SUM(amount), 0) as monthly_revenue,
            COUNT(*) as monthly_transactions
     FROM portal_payments
     WHERE company_id = ? AND status = "completed"
       AND YEAR(created_at) = YEAR(NOW())
       AND MONTH(created_at) = MONTH(NOW())'
);
$stmt->bind_param('i', $companyId);
$stmt->execute();
$revenueStats = $stmt->get_result()->fetch_assoc();
$stmt->close();

$db->close();

// Determine connected payment methods
$paymentMethods = get_available_payment_methods($company);

send_json_response(200, [
    'success' => true,
    'connected' => true,
    'portalUrl' => 'https://argorobots.com/portal/',
    'company' => [
        'name' => $company['company_name'],
        'logo_url' => $company['company_logo_url'],
    ],
    'payment_methods' => $paymentMethods,
    'connectedProviders' => [
        'stripeConnected' => !empty($company['stripe_account_id']),
        'stripeEmail' => $company['stripe_email'] ?? null,
        'paypalConnected' => !empty($company['paypal_merchant_id']),
        'paypalEmail' => $company['paypal_email'] ?? null,
        'squareConnected' => !empty($company['square_merchant_id']),
        'squareEmail' => $company['square_email'] ?? null,
    ],
    'statistics' => [
        'total_invoices' => (int) $invoiceStats['total_invoices'],
        'active_invoices' => (int) $invoiceStats['active_invoices'],
        'paid_invoices' => (int) $invoiceStats['paid_invoices'],
        'total_outstanding' => round(floatval($invoiceStats['total_outstanding'] ?? 0), 2),
        'unsynced_payments' => (int) $syncStats['unsynced_count'],
        'unsynced_amount' => round(floatval($syncStats['unsynced_amount'] ?? 0), 2),
        'monthly_revenue' => round(floatval($revenueStats['monthly_revenue'] ?? 0), 2),
        'monthly_transactions' => (int) $revenueStats['monthly_transactions'],
    ],
    'timestamp' => date('c')
]);
