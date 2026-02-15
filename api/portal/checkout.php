<?php
/**
 * Portal Checkout API Endpoint
 *
 * POST /api/portal/checkout - Create a payment session for an invoice
 *
 * Handles creating payment intents/orders for Stripe, PayPal, and Square.
 * Money flows to the Argo Books user's connected account (Stripe Connect,
 * PayPal for Marketplaces, Square OAuth), NOT to ArgoRobots.
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

// Validate invoice is payable
if ($invoice['status'] === 'paid') {
    send_error_response(400, 'This invoice has already been paid.', 'ALREADY_PAID');
}
if ($invoice['status'] === 'cancelled') {
    send_error_response(400, 'This invoice has been cancelled.', 'CANCELLED');
}

// Validate payment amount
$requestedAmount = floatval($data['amount']);
$balanceDue = floatval($invoice['balance_due']);

if ($requestedAmount <= 0) {
    send_error_response(400, 'Payment amount must be greater than zero.', 'INVALID_AMOUNT');
}
if ($requestedAmount > $balanceDue + 0.01) { // Small tolerance for floating point
    send_error_response(400, 'Payment amount exceeds balance due.', 'AMOUNT_EXCEEDS_BALANCE');
}

$method = strtolower($data['method']);
$currency = strtolower($invoice['currency'] ?: 'usd');
$amountCents = (int) round($requestedAmount * 100);
$companyId = $invoice['company_id'];

// Get environment-based keys
$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

try {
    switch ($method) {
        case 'stripe':
            handle_stripe_checkout($invoice, $amountCents, $currency, $requestedAmount, $data);
            break;
        case 'paypal':
            handle_paypal_checkout($invoice, $requestedAmount, $currency, $data);
            break;
        case 'square':
            handle_square_checkout($invoice, $amountCents, $currency, $requestedAmount, $data);
            break;
        default:
            send_error_response(400, 'Invalid payment method. Must be stripe, paypal, or square.', 'INVALID_METHOD');
    }
} catch (Exception $e) {
    error_log('Portal checkout error: ' . $e->getMessage());
    send_error_response(500, 'Payment processing error: ' . $e->getMessage(), 'PAYMENT_ERROR');
}

/**
 * Create a Stripe PaymentIntent using Stripe Connect (money goes to the business's Stripe account)
 */
function handle_stripe_checkout(array $invoice, int $amountCents, string $currency, float $amount, array $data): void
{
    global $is_production;

    $stripeAccountId = $invoice['stripe_account_id'] ?? '';
    if (empty($stripeAccountId)) {
        send_error_response(400, 'Stripe is not configured for this business.', 'STRIPE_NOT_CONNECTED');
    }

    $stripe_secret_key = $is_production
        ? $_ENV['STRIPE_LIVE_SECRET_KEY']
        : $_ENV['STRIPE_SANDBOX_SECRET_KEY'];

    \Stripe\Stripe::setApiKey($stripe_secret_key);

    $paymentIntentParams = [
        'amount' => $amountCents,
        'currency' => $currency,
        'payment_method_types' => ['card'],
        'application_fee_amount' => 0, // Free feature - no platform fee
        'metadata' => [
            'portal_invoice_id' => $invoice['invoice_id'],
            'portal_company_id' => $invoice['company_id'],
            'customer_name' => $invoice['customer_name'],
        ],
        'description' => 'Invoice ' . $invoice['invoice_id'] . ' - ' . ($invoice['company_name'] ?? ''),
    ];

    // Add receipt email if provided
    if (!empty($data['email'])) {
        $paymentIntentParams['receipt_email'] = $data['email'];
    }

    // Create PaymentIntent on the connected account
    $paymentIntent = \Stripe\PaymentIntent::create(
        $paymentIntentParams,
        ['stripe_account' => $stripeAccountId]
    );

    send_json_response(200, [
        'success' => true,
        'method' => 'stripe',
        'client_secret' => $paymentIntent->client_secret,
        'payment_intent_id' => $paymentIntent->id,
        'stripe_account_id' => $stripeAccountId,
        'amount' => $amount,
        'currency' => $currency,
        'timestamp' => date('c')
    ]);
}

/**
 * Handle PayPal checkout (returns order details for client-side approval)
 */
function handle_paypal_checkout(array $invoice, float $amount, string $currency, array $data): void
{
    $paypalMerchantId = $invoice['paypal_merchant_id'] ?? '';
    $paypalEmail = $invoice['paypal_email'] ?? '';
    if (empty($paypalMerchantId) && empty($paypalEmail)) {
        send_error_response(400, 'PayPal is not configured for this business.', 'PAYPAL_NOT_CONNECTED');
    }

    // Determine if merchant_id is a real PayPal account ID or an email
    $isEmailBased = empty($paypalMerchantId) || filter_var($paypalMerchantId, FILTER_VALIDATE_EMAIL);

    // For PayPal, the order is created client-side using the PayPal SDK.
    // We return the merchant info so the frontend can create the order with the correct payee.
    send_json_response(200, [
        'success' => true,
        'method' => 'paypal',
        'merchant_id' => $isEmailBased ? null : $paypalMerchantId,
        'paypal_email' => $isEmailBased ? ($paypalEmail ?: $paypalMerchantId) : $paypalEmail,
        'invoice_id' => $invoice['invoice_id'],
        'amount' => number_format($amount, 2, '.', ''),
        'currency' => strtoupper($currency),
        'description' => 'Invoice ' . $invoice['invoice_id'],
        'timestamp' => date('c')
    ]);
}

/**
 * Handle Square checkout
 */
function handle_square_checkout(array $invoice, int $amountCents, string $currency, float $amount, array $data): void
{
    global $is_production;

    $squareMerchantId = $invoice['square_merchant_id'] ?? '';
    if (empty($squareMerchantId)) {
        send_error_response(400, 'Square is not configured for this business.', 'SQUARE_NOT_CONNECTED');
    }

    // For Square with OAuth, the payment is processed using the business's access token.
    // Get the encrypted access token from the company record.
    $db = get_db_connection();
    $stmt = $db->prepare('SELECT square_access_token, square_location_id FROM portal_companies WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $invoice['company_id']);
    $stmt->execute();
    $company = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();

    if (empty($company['square_access_token'])) {
        send_error_response(400, 'Square is not properly configured for this business.', 'SQUARE_NOT_CONFIGURED');
    }

    // If a source_id is provided, process the payment directly
    if (!empty($data['source_id'])) {
        process_square_payment($invoice, $company, $data, $amountCents, $currency, $amount);
        return;
    }

    // Otherwise return config for client-side card form
    $squareAppId = $is_production
        ? $_ENV['SQUARE_LIVE_APP_ID']
        : $_ENV['SQUARE_SANDBOX_APP_ID'];

    send_json_response(200, [
        'success' => true,
        'method' => 'square',
        'app_id' => $squareAppId,
        'location_id' => $company['square_location_id'] ?? '',
        'invoice_id' => $invoice['invoice_id'],
        'amount' => $amount,
        'currency' => strtoupper($currency),
        'timestamp' => date('c')
    ]);
}

/**
 * Process a Square payment using the business's access token
 */
function process_square_payment(array $invoice, array $company, array $data, int $amountCents, string $currency, float $amount): void
{
    global $is_production;

    $accessToken = $company['square_access_token'];
    $locationId = $company['square_location_id'] ?? '';
    $apiBaseUrl = $is_production
        ? 'https://connect.squareup.com/v2'
        : 'https://connect.squareupsandbox.com/v2';

    $idempotencyKey = $data['idempotency_key'] ?? (time() . bin2hex(random_bytes(4)));
    $referenceNumber = generate_reference_number();

    $paymentData = [
        'source_id' => $data['source_id'],
        'idempotency_key' => $idempotencyKey,
        'amount_money' => [
            'amount' => $amountCents,
            'currency' => strtoupper($currency)
        ],
        'autocomplete' => true,
        'location_id' => $locationId,
        'reference_id' => $invoice['invoice_id'],
        'note' => 'Invoice ' . $invoice['invoice_id']
    ];

    $ch = curl_init("$apiBaseUrl/payments");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($paymentData),
        CURLOPT_HTTPHEADER => [
            "Square-Version: 2025-10-16",
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ]
    ]);

    $responseData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        $paymentResult = json_decode($responseData, true);

        if (isset($paymentResult['payment']) && $paymentResult['payment']['status'] === 'COMPLETED') {
            $payment = $paymentResult['payment'];

            // Record the payment
            $result = record_portal_payment([
                'company_id' => $invoice['company_id'],
                'invoice_id' => $invoice['invoice_id'],
                'customer_name' => $invoice['customer_name'],
                'amount' => $amount,
                'currency' => strtoupper($currency),
                'payment_method' => 'square',
                'provider_payment_id' => $payment['id'],
                'provider_transaction_id' => $payment['id'],
                'reference_number' => $referenceNumber,
                'status' => 'completed',
            ]);

            send_json_response(200, [
                'success' => true,
                'method' => 'square',
                'reference_number' => $referenceNumber,
                'provider_payment_id' => $payment['id'],
                'amount' => $amount,
                'currency' => strtoupper($currency),
                'invoice_id' => $invoice['invoice_id'],
                'timestamp' => date('c')
            ]);
        } else {
            send_error_response(400, 'Payment was not completed.', 'PAYMENT_INCOMPLETE');
        }
    } else {
        $errors = json_decode($responseData, true);
        $errorMsg = 'Square payment failed';
        if (isset($errors['errors'][0]['detail'])) {
            $errorMsg = $errors['errors'][0]['detail'];
        }
        error_log("Square portal payment error: $errorMsg");
        send_error_response(500, $errorMsg, 'SQUARE_ERROR');
    }
}
