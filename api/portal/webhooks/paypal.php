<?php
/**
 * PayPal Webhook for Portal Payments
 *
 * POST /api/portal/webhooks/paypal
 *
 * Handles PayPal webhook events for invoice payments.
 * Backup confirmation for payments processed through the portal.
 */

require_once __DIR__ . '/../portal-helper.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

// Verify webhook signature with PayPal
$webhookId = $_ENV['PORTAL_PAYPAL_WEBHOOK_ID'] ?? '';
if (empty($webhookId)) {
    error_log('Portal PayPal webhook: No webhook ID configured');
    http_response_code(200); // Acknowledge but log error
    echo json_encode(['received' => true]);
    exit;
}

// PayPal webhook verification
$paypalClientId = $is_production
    ? $_ENV['PAYPAL_LIVE_CLIENT_ID']
    : $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
$paypalSecret = $is_production
    ? $_ENV['PAYPAL_LIVE_CLIENT_SECRET']
    : $_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'];
$paypalBaseUrl = $is_production
    ? 'https://api-m.paypal.com'
    : 'https://api-m.sandbox.paypal.com';

// Get access token
$ch = curl_init("$paypalBaseUrl/v1/oauth2/token");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
    CURLOPT_USERPWD => "$paypalClientId:$paypalSecret",
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
]);
$tokenResponse = json_decode(curl_exec($ch), true);
curl_close($ch);

if (empty($tokenResponse['access_token'])) {
    error_log('Portal PayPal webhook: Failed to get access token');
    http_response_code(200);
    echo json_encode(['received' => true]);
    exit;
}

// Verify the webhook event
$verifyBody = json_encode([
    'auth_algo' => $_SERVER['HTTP_PAYPAL_AUTH_ALGO'] ?? '',
    'cert_url' => $_SERVER['HTTP_PAYPAL_CERT_URL'] ?? '',
    'transmission_id' => $_SERVER['HTTP_PAYPAL_TRANSMISSION_ID'] ?? '',
    'transmission_sig' => $_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG'] ?? '',
    'transmission_time' => $_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME'] ?? '',
    'webhook_id' => $webhookId,
    'webhook_event' => $data,
]);

$ch = curl_init("$paypalBaseUrl/v1/notifications/verify-webhook-signature");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $verifyBody,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$tokenResponse['access_token']}",
        "Content-Type: application/json"
    ]
]);
$verifyResponse = json_decode(curl_exec($ch), true);
curl_close($ch);

if (($verifyResponse['verification_status'] ?? '') !== 'SUCCESS') {
    error_log('Portal PayPal webhook: Signature verification failed');
    http_response_code(400);
    exit;
}

// Handle the event
$eventType = $data['event_type'] ?? '';

switch ($eventType) {
    case 'PAYMENT.CAPTURE.COMPLETED':
        // Payment completed - this is handled by process-payment.php
        // This webhook serves as a backup confirmation
        error_log('Portal PayPal webhook: Payment capture completed for order ' . ($data['resource']['id'] ?? 'unknown'));
        break;

    case 'PAYMENT.CAPTURE.REFUNDED':
        error_log('Portal PayPal webhook: Payment refunded for capture ' . ($data['resource']['id'] ?? 'unknown'));
        break;

    default:
        // Acknowledge unknown events
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
