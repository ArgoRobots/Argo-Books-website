<?php
/**
 * Portal Process Payment Endpoint
 *
 * POST /api/portal/process-payment - Finalize a payment after client-side confirmation
 *
 * Called after Stripe confirmCardPayment() or PayPal order capture to record
 * the payment in our database and update the invoice balance.
 */

require_once __DIR__ . '/portal-helper.php';
require_once __DIR__ . '/../../vendor/autoload.php';

set_portal_headers();
require_method(['POST']);

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

// Validate required fields
$required = ['invoice_token', 'method', 'amount'];
$missing = [];
foreach ($required as $field) {
    if (!isset($data[$field]) || ($data[$field] === '' && $data[$field] !== 0)) {
        $missing[] = $field;
    }
}
if (!empty($missing)) {
    send_error_response(400, 'Missing required fields: ' . implode(', ', $missing), 'MISSING_FIELDS');
}

// Get invoice data
$invoice = get_invoice_by_token($data['invoice_token']);
if (!$invoice) {
    send_error_response(404, 'Invoice not found.', 'NOT_FOUND');
}

$method = strtolower($data['method']);
$amount = floatval($data['amount']);
$referenceNumber = generate_reference_number();
$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

try {
    switch ($method) {
        case 'stripe':
            process_stripe_payment($invoice, $data, $amount, $referenceNumber);
            break;
        case 'paypal':
            process_paypal_payment($invoice, $data, $amount, $referenceNumber);
            break;
        default:
            // Square payments are processed directly in checkout.php
            send_error_response(400, 'Invalid payment method for this endpoint.', 'INVALID_METHOD');
    }
} catch (Exception $e) {
    error_log('Portal process-payment error: ' . $e->getMessage());
    send_error_response(500, 'Payment processing error: ' . $e->getMessage(), 'PAYMENT_ERROR');
}

/**
 * Process a confirmed Stripe payment
 */
function process_stripe_payment(array $invoice, array $data, float $amount, string $referenceNumber): void
{
    global $is_production;

    $paymentIntentId = $data['payment_intent_id'] ?? '';
    if (empty($paymentIntentId)) {
        send_error_response(400, 'Missing payment_intent_id.', 'MISSING_FIELD');
    }

    // Verify the payment intent status with Stripe
    $stripeAccountId = $invoice['stripe_account_id'] ?? '';
    $stripe_secret_key = $is_production
        ? $_ENV['STRIPE_LIVE_SECRET_KEY']
        : $_ENV['STRIPE_SANDBOX_SECRET_KEY'];

    \Stripe\Stripe::setApiKey($stripe_secret_key);

    $paymentIntent = \Stripe\PaymentIntent::retrieve(
        $paymentIntentId,
        ['stripe_account' => $stripeAccountId]
    );

    if ($paymentIntent->status !== 'succeeded') {
        send_error_response(400, 'Payment has not been completed. Status: ' . $paymentIntent->status, 'PAYMENT_NOT_COMPLETED');
    }

    // Verify amount matches
    $paidAmountCents = $paymentIntent->amount;
    $expectedCents = (int) round($amount * 100);
    if (abs($paidAmountCents - $expectedCents) > 1) { // 1 cent tolerance
        error_log("Portal Stripe amount mismatch: paid={$paidAmountCents} expected={$expectedCents}");
        send_error_response(400, 'Payment amount mismatch.', 'AMOUNT_MISMATCH');
    }

    // Record the payment
    $result = record_portal_payment([
        'company_id' => $invoice['company_id'],
        'invoice_id' => $invoice['invoice_id'],
        'customer_name' => $invoice['customer_name'],
        'amount' => $amount,
        'currency' => strtoupper($invoice['currency'] ?: 'usd'),
        'payment_method' => 'stripe',
        'provider_payment_id' => $paymentIntentId,
        'provider_transaction_id' => $paymentIntent->latest_charge ?? $paymentIntentId,
        'reference_number' => $referenceNumber,
        'status' => 'completed',
    ]);

    if (!$result['success']) {
        send_error_response(500, $result['message'], 'RECORD_FAILED');
    }

    send_json_response(200, [
        'success' => true,
        'method' => 'stripe',
        'reference_number' => $result['reference_number'],
        'amount' => $amount,
        'currency' => strtoupper($invoice['currency'] ?: 'usd'),
        'invoice_id' => $invoice['invoice_id'],
        'timestamp' => date('c')
    ]);
}

/**
 * Process a confirmed PayPal payment
 */
function process_paypal_payment(array $invoice, array $data, float $amount, string $referenceNumber): void
{
    global $is_production;

    $orderId = $data['order_id'] ?? '';
    if (empty($orderId)) {
        send_error_response(400, 'Missing PayPal order_id.', 'MISSING_FIELD');
    }

    // Verify the order with PayPal
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
        send_error_response(500, 'Failed to authenticate with PayPal.', 'PAYPAL_AUTH_FAILED');
    }

    $accessToken = $tokenResponse['access_token'];

    // Verify order details
    $ch = curl_init("$paypalBaseUrl/v2/checkout/orders/$orderId");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]
    ]);
    $orderResponse = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (empty($orderResponse['id']) || $orderResponse['status'] !== 'COMPLETED') {
        send_error_response(400, 'PayPal order is not completed.', 'PAYPAL_NOT_COMPLETED');
    }

    // Extract capture ID
    $captureId = '';
    if (isset($orderResponse['purchase_units'][0]['payments']['captures'][0]['id'])) {
        $captureId = $orderResponse['purchase_units'][0]['payments']['captures'][0]['id'];
    }

    // Record the payment
    $result = record_portal_payment([
        'company_id' => $invoice['company_id'],
        'invoice_id' => $invoice['invoice_id'],
        'customer_name' => $invoice['customer_name'],
        'amount' => $amount,
        'currency' => strtoupper($invoice['currency'] ?: 'usd'),
        'payment_method' => 'paypal',
        'provider_payment_id' => $orderId,
        'provider_transaction_id' => $captureId ?: $orderId,
        'reference_number' => $referenceNumber,
        'status' => 'completed',
    ]);

    if (!$result['success']) {
        send_error_response(500, $result['message'], 'RECORD_FAILED');
    }

    send_json_response(200, [
        'success' => true,
        'method' => 'paypal',
        'reference_number' => $result['reference_number'],
        'amount' => $amount,
        'currency' => strtoupper($invoice['currency'] ?: 'usd'),
        'invoice_id' => $invoice['invoice_id'],
        'timestamp' => date('c')
    ]);
}
