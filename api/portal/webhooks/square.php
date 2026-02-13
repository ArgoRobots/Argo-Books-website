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

// Verify webhook signature
$signature = $_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '';
$webhookSignatureKey = $_ENV['PORTAL_SQUARE_WEBHOOK_SIGNATURE_KEY'] ?? '';

if (!empty($webhookSignatureKey) && !empty($signature)) {
    $notificationUrl = ($_ENV['APP_URL'] ?? 'https://argorobots.com') . '/api/portal/webhooks/square';
    $stringToSign = $notificationUrl . $payload;
    $expectedSignature = base64_encode(hash_hmac('sha256', $stringToSign, $webhookSignatureKey, true));

    if (!hash_equals($expectedSignature, $signature)) {
        error_log('Portal Square webhook: Invalid signature');
        http_response_code(400);
        exit;
    }
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
        $paymentId = $data['data']['object']['payment']['id'] ?? 'unknown';
        error_log("Portal Square webhook: Payment completed - $paymentId");
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
