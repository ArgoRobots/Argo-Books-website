<?php
/**
 * Square Webhook for Portal Payments
 *
 * POST /api/portal/webhooks/square
 *
 * Handles Square webhook events for invoice payments.
 * Backup confirmation for payments processed through the portal.
 */

require_once __DIR__ . '/../portal-helper.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$payload = file_get_contents('php://input');

// Verify webhook signature (mandatory)
$signature = $_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '';
$webhookSignatureKey = $_ENV['PORTAL_SQUARE_WEBHOOK_SIGNATURE_KEY'] ?? '';

if (empty($webhookSignatureKey)) {
    error_log('Portal Square webhook: PORTAL_SQUARE_WEBHOOK_SIGNATURE_KEY not configured - rejecting request');
    http_response_code(500);
    exit;
}

if (empty($signature)) {
    error_log('Portal Square webhook: Missing signature header');
    http_response_code(401);
    exit;
}

$notificationUrl = ($_ENV['APP_URL'] ?? 'https://argorobots.com') . '/api/portal/webhooks/square';
$stringToSign = $notificationUrl . $payload;
$expectedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $webhookSignatureKey, true));

if (!hash_equals($expectedSignature, $signature)) {
    error_log('Portal Square webhook: Invalid signature');
    http_response_code(400);
    exit;
}

$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$eventType = $data['type'] ?? '';

switch ($eventType) {
    case 'payment.completed':
        // Payment completed - handled primarily by checkout.php
        // This webhook serves as a backup confirmation
        $payment = $data['data']['object']['payment'] ?? [];
        $paymentId = $payment['id'] ?? 'unknown';
        error_log("Portal Square webhook: Payment completed - $paymentId");

        $squareAmount = floatval($payment['amount_money']['amount'] ?? 0) / 100;
        $squareCurrency = strtoupper($payment['amount_money']['currency'] ?? 'USD');
        $squareOrderId = $payment['order_id'] ?? '';
        $squareReferenceId = $payment['reference_id'] ?? '';
        $squareNote = $payment['note'] ?? '';

        // Try to extract invoice reference from reference_id or note
        $invoiceRef = $squareReferenceId ?: $squareNote;

        if (!empty($paymentId) && $paymentId !== 'unknown' && $squareAmount > 0 && !empty($invoiceRef)) {
            $db = get_db_connection();
            $stmt = $db->prepare(
                'SELECT company_id, invoice_id, customer_name FROM portal_invoices WHERE invoice_id = ? LIMIT 1'
            );
            $stmt->bind_param('s', $invoiceRef);
            $stmt->execute();
            $invoiceRecord = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $db->close();

            if ($invoiceRecord) {
                record_portal_payment([
                    'company_id' => (int) $invoiceRecord['company_id'],
                    'invoice_id' => $invoiceRecord['invoice_id'],
                    'customer_name' => $invoiceRecord['customer_name'] ?? '',
                    'amount' => $squareAmount,
                    'currency' => $squareCurrency,
                    'payment_method' => 'square',
                    'provider_payment_id' => $paymentId,
                    'provider_transaction_id' => $squareOrderId ?: $paymentId,
                    'reference_number' => generate_reference_number(),
                    'status' => 'completed',
                    'payment_environment' => ($_ENV['APP_ENV'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox',
                ]);
            }
        }
        break;

    case 'refund.created':
    case 'refund.updated':
        $refundId = $data['data']['object']['refund']['id'] ?? 'unknown';
        error_log("Portal Square webhook: Refund event - $refundId");
        break;

    default:
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
