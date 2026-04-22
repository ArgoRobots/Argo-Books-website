<?php
/**
 * Stripe Webhook for Portal Payments
 *
 * POST /api/portal/webhooks/stripe
 *
 * Handles Stripe Connect webhook events for invoice payments.
 * This is a backup confirmation - payments are also confirmed client-side
 * via process-payment.php. The webhook ensures we don't miss payments
 * even if the customer closes their browser before the confirmation page.
 */

require_once __DIR__ . '/../portal-helper.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

// Portal webhook secret (separate from the license sales webhook secret)
$webhook_secret = $_ENV['PORTAL_STRIPE_WEBHOOK_SECRET'] ?? '';

if (empty($webhook_secret)) {
    error_log('Portal Stripe webhook: No webhook secret configured');
    http_response_code(500);
    echo json_encode(['error' => 'Webhook not configured']);
    exit;
}

$stripe_secret_key = $is_production
    ? ($_ENV['STRIPE_LIVE_SECRET_KEY'] ?? '')
    : ($_ENV['STRIPE_SANDBOX_SECRET_KEY'] ?? '');

\Stripe\Stripe::setApiKey($stripe_secret_key);

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $webhook_secret);
} catch (\UnexpectedValueException $e) {
    error_log('Portal Stripe webhook: Invalid payload');
    http_response_code(400);
    exit;
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    error_log('Portal Stripe webhook: Invalid signature');
    http_response_code(400);
    exit;
}

// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        handle_payment_succeeded($paymentIntent);
        break;

    case 'charge.refunded':
        $charge = $event->data->object;
        handle_refund($charge);
        break;

    default:
        // Acknowledge other events without processing
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);

/**
 * Handle a successful payment intent
 */
function handle_payment_succeeded(\Stripe\PaymentIntent $paymentIntent): void
{
    global $is_production;
    $metadata = $paymentIntent->metadata;
    $invoiceId = $metadata['portal_invoice_id'] ?? '';
    $companyId = $metadata['portal_company_id'] ?? '';

    if (empty($invoiceId) || empty($companyId)) {
        // Not a portal payment (could be a license purchase)
        return;
    }

    $currency = strtoupper($paymentIntent->currency);
    $zeroDecimalCurrencies = ['BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'];
    $divisor = in_array($currency, $zeroDecimalCurrencies) ? 1 : 100;
    $amount = $paymentIntent->amount / $divisor;

    // Record the payment (idempotent - will skip if already recorded)
    record_portal_payment([
        'company_id' => (int) $companyId,
        'invoice_id' => $invoiceId,
        'customer_name' => $metadata['customer_name'] ?? '',
        'amount' => $amount,
        'currency' => $currency,
        'payment_method' => 'stripe',
        'provider_payment_id' => $paymentIntent->id,
        'provider_transaction_id' => $paymentIntent->latest_charge ?? $paymentIntent->id,
        'reference_number' => generate_reference_number(),
        'status' => 'completed',
        'payment_environment' => $is_production ? 'production' : 'sandbox',
    ]);
}

/**
 * Handle a refund
 */
function handle_refund(\Stripe\Charge $charge): void
{
    global $is_production;
    $providerPaymentId = $charge->payment_intent;

    if (empty($providerPaymentId)) {
        return;
    }

    global $pdo;

    // Find the original payment
    $stmt = $pdo->prepare(
        'SELECT id, company_id, invoice_id, customer_name, currency
         FROM portal_payments
         WHERE provider_payment_id = ? AND status = "completed"
         LIMIT 1'
    );
    $stmt->execute([$providerPaymentId]);
    $originalPayment = $stmt->fetch();

    if (!$originalPayment) {
        return;
    }

    // Calculate refund amount
    $refundCurrency = strtoupper($originalPayment['currency'] ?? 'USD');
    $zeroDecimalCurrencies = ['BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'];
    $divisor = in_array($refundCurrency, $zeroDecimalCurrencies) ? 1 : 100;
    $refundAmount = $charge->amount_refunded / $divisor;

    // Record the refund as a negative payment
    record_portal_payment([
        'company_id' => $originalPayment['company_id'],
        'invoice_id' => $originalPayment['invoice_id'],
        'customer_name' => $originalPayment['customer_name'],
        'amount' => -$refundAmount,
        'currency' => $originalPayment['currency'],
        'payment_method' => 'stripe',
        'provider_payment_id' => 'refund_' . $providerPaymentId,
        'provider_transaction_id' => $charge->id,
        'reference_number' => generate_reference_number(),
        'status' => 'refunded',
        'payment_environment' => $is_production ? 'production' : 'sandbox',
    ]);

    // Update the original payment status
    $stmt = $pdo->prepare(
        'UPDATE portal_payments SET status = "refunded" WHERE id = ?'
    );
    $stmt->execute([$originalPayment['id']]);

    // Update invoice balance (add refund amount back)
    $stmt = $pdo->prepare(
        'UPDATE portal_invoices
         SET balance_due = LEAST(total_amount, balance_due + ?),
             status = CASE
                 WHEN balance_due + ? >= total_amount THEN "sent"
                 ELSE "partial"
             END,
             updated_at = NOW()
         WHERE company_id = ? AND invoice_id = ?'
    );
    $stmt->execute([
        $refundAmount, $refundAmount,
        $originalPayment['company_id'], $originalPayment['invoice_id']
    ]);
}
