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
    error_log('Portal PayPal webhook: PORTAL_PAYPAL_WEBHOOK_ID not configured - rejecting request');
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Webhook not configured']);
    exit;
}

// PayPal webhook verification
$paypalClientId = $is_production
    ? ($_ENV['PAYPAL_LIVE_CLIENT_ID'] ?? '')
    : ($_ENV['PAYPAL_SANDBOX_CLIENT_ID'] ?? '');
$paypalSecret = $is_production
    ? ($_ENV['PAYPAL_LIVE_CLIENT_SECRET'] ?? '')
    : ($_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'] ?? '');
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
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$response = curl_exec($ch);
if ($response === false) {
    error_log('PayPal token request failed: ' . curl_error($ch));
    send_json_response(200, ['received' => true]); // acknowledge to prevent retries
    exit;
}
$tokenResponse = json_decode($response, true);

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
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$verifyResponse = json_decode(curl_exec($ch), true);

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
        $resource = $data['resource'] ?? [];
        error_log('Portal PayPal webhook: Payment capture completed for order ' . ($resource['id'] ?? 'unknown'));

        $captureAmount = floatval($resource['amount']['value'] ?? 0);
        $captureCurrency = strtoupper($resource['amount']['currency_code'] ?? 'USD');
        $captureId = $resource['id'] ?? '';
        $orderId = $resource['supplementary_data']['related_ids']['order_id'] ?? '';
        $invoiceRef = $resource['invoice_id'] ?? ($resource['custom_id'] ?? '');

        if (!empty($captureId) && $captureAmount > 0 && !empty($invoiceRef)) {
            // Look up the invoice by invoice_id to get the company_id
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
                    'amount' => $captureAmount,
                    'currency' => $captureCurrency,
                    'payment_method' => 'paypal',
                    'provider_payment_id' => $orderId ?: $captureId,
                    'provider_transaction_id' => $captureId,
                    'reference_number' => generate_reference_number(),
                    'status' => 'completed',
                    'payment_environment' => $is_production ? 'production' : 'sandbox',
                ]);
            }
        }
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
