<?php
/**
 * Portal Customer API Endpoint
 *
 * GET /api/portal/customer?token={customer_token} - Get all invoices and payments for a customer
 *
 * Uses customer token for access (no login required).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['GET']);

$token = $_GET['token'] ?? '';

if (empty($token) || !preg_match('/^[a-fA-F0-9]{48}$/', $token)) {
    send_error_response(400, 'Invalid or missing customer token.', 'INVALID_TOKEN');
}

// Rate limiting check
$clientIp = get_client_ip();
if (is_rate_limited($clientIp)) {
    send_error_response(429, 'Too many requests. Please try again later.', 'RATE_LIMITED');
}

$result = get_invoices_by_customer_token($token);

if (!$result['company']) {
    record_failed_lookup($clientIp);
    send_error_response(404, 'Customer portal not found.', 'NOT_FOUND');
}

// Get payment history
$payments = get_payments_by_customer_token($token);

// Determine available payment methods
$paymentMethods = get_available_payment_methods($result['company']);

// Categorize invoices
$activeInvoices = [];
$paidInvoices = [];
$allInvoices = [];

foreach ($result['invoices'] as $inv) {
    $invoiceSummary = [
        'invoice_id' => $inv['invoice_id'],
        'invoice_token' => $inv['invoice_token'],
        'customer_name' => $inv['customer_name'],
        'status' => $inv['status'],
        'total_amount' => floatval($inv['total_amount']),
        'balance_due' => floatval($inv['balance_due']),
        'currency' => $inv['currency'],
        'due_date' => $inv['due_date'],
        'created_at' => $inv['created_at'],
        'invoice_data' => $inv['invoice_data'],
    ];

    $allInvoices[] = $invoiceSummary;

    if (in_array($inv['status'], ['sent', 'viewed', 'partial', 'overdue', 'pending'])) {
        $activeInvoices[] = $invoiceSummary;
    } elseif ($inv['status'] === 'paid') {
        $paidInvoices[] = $invoiceSummary;
    }
}

// Build payment history response
$paymentHistory = [];
foreach ($payments as $pay) {
    $paymentHistory[] = [
        'invoice_id' => $pay['invoice_id'],
        'amount' => floatval($pay['amount']),
        'currency' => $pay['currency'],
        'payment_method' => $pay['payment_method'],
        'reference_number' => $pay['reference_number'],
        'status' => $pay['status'],
        'created_at' => $pay['created_at'],
    ];
}

// Calculate totals
$totalOutstanding = array_sum(array_column($activeInvoices, 'balance_due'));

send_json_response(200, [
    'success' => true,
    'company' => [
        'name' => $result['company']['company_name'],
        'logo_url' => $result['company']['company_logo_url'],
    ],
    'active_invoices' => $activeInvoices,
    'paid_invoices' => $paidInvoices,
    'all_invoices' => $allInvoices,
    'payment_history' => $paymentHistory,
    'payment_methods' => $paymentMethods,
    'total_outstanding' => round($totalOutstanding, 2),
    'timestamp' => date('c')
]);
