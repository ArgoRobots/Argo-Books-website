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
require_once __DIR__ . '/_stripe_refund_db.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

// Portal webhook secret — separate sandbox/live values mirror the API-key pair
// below so all .env files can carry both. Falls back to the legacy single
// PORTAL_STRIPE_WEBHOOK_SECRET name to avoid breaking existing deployments.
$webhook_secret = $is_production
    ? ($_ENV['STRIPE_LIVE_PORTAL_WEBHOOK_SECRET'] ?? $_ENV['PORTAL_STRIPE_WEBHOOK_SECRET'] ?? '')
    : ($_ENV['STRIPE_SANDBOX_PORTAL_WEBHOOK_SECRET'] ?? $_ENV['PORTAL_STRIPE_WEBHOOK_SECRET'] ?? '');

if (empty($webhook_secret)) {
    error_log('Portal Stripe webhook: No webhook secret configured (env: ' . ($is_production ? 'production' : 'sandbox') . ')');
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
    global $is_production, $pdo;
    $providerPaymentId = $charge->payment_intent;

    if (empty($providerPaymentId)) {
        // Charges without a payment_intent (rare, legacy direct charges)
        // can't be tied to a portal payment record; skip silently.
        error_log("Portal Stripe webhook: skipping charge.refunded for {$charge->id} — no payment_intent");
        return;
    }

    // Currency divisor logic stays here because it depends on the SDK Charge.
    // Use the original payment's currency (already stored in our DB) so refunds
    // honour the exact currency used at capture time even if Stripe normalised.
    $stmt = $pdo->prepare(
        'SELECT currency FROM portal_payments
         WHERE provider_payment_id = ? AND amount > 0 LIMIT 1'
    );
    $stmt->execute([$providerPaymentId]);
    $row = $stmt->fetch();
    if (!$row) {
        // No matching portal payment — likely a charge that didn't originate
        // from the customer portal (e.g. license/subscription, or a refund
        // for a deleted/migrated record). Log so support can trace
        // disappearing-refund tickets.
        error_log("Portal Stripe webhook: charge.refunded for {$charge->id} (PI {$providerPaymentId}) has no matching portal payment row");
        return;
    }
    $refundCurrency = strtoupper($row['currency'] ?? 'USD');
    $zeroDecimalCurrencies = ['BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'];
    $divisor = in_array($refundCurrency, $zeroDecimalCurrencies) ? 1 : 100;

    // Iterate individual Refund objects rather than using the cumulative
    // $charge->amount_refunded. The webhook ships every refund-on-this-charge
    // and we want distinct negative-payment rows per refund — a second
    // partial refund would otherwise hit the unique-key dedup on a single
    // payment-intent-keyed row and silently get dropped.
    if (isset($charge->refunds) && !empty($charge->refunds->data)) {
        foreach ($charge->refunds->data as $refundObj) {
            $refundAmount = ($refundObj->amount ?? 0) / $divisor;
            if ($refundAmount <= 0) continue;
            apply_stripe_refund_to_db(
                $pdo,
                $providerPaymentId,
                $refundAmount,
                $charge->id,
                $is_production,
                $refundObj->id
            );
        }
    } else {
        // Fallback: no per-refund detail in the event (rare). Process the
        // cumulative amount under a single row keyed by payment intent.
        $refundAmount = $charge->amount_refunded / $divisor;
        apply_stripe_refund_to_db($pdo, $providerPaymentId, $refundAmount, $charge->id, $is_production);
    }

    // Reconcile against refund_requests if this refund was initiated by our
    // /api/portal/refunds/ flow (refund metadata carries argo_request_id).
    // Idempotent: no-op if already completed; transitions processing/cooling_off
    // → completed otherwise. The companion record_into_portal_payments insert
    // already happened at execute-time, so this is purely a state transition.
    try {
        if (isset($charge->refunds) && !empty($charge->refunds->data)) {
            require_once __DIR__ . '/../_audit.php';
            require_once __DIR__ . '/../_refund_helpers.php';
            foreach ($charge->refunds->data as $refundObj) {
                $argoRequestId = $refundObj->metadata['argo_request_id'] ?? null;
                if ($argoRequestId !== null && is_numeric($argoRequestId)) {
                    $stmt = $pdo->prepare("SELECT * FROM refund_requests WHERE id = ?");
                    $stmt->execute([(int)$argoRequestId]);
                    $req = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($req && $req['state'] !== 'completed' && $req['state'] !== 'cancelled') {
                        // CAS guard so a race with the synchronous execute
                        // path can't fire two completion notifications. Only
                        // the UPDATE that flips the state actually notifies.
                        $upd = $pdo->prepare("UPDATE refund_requests SET state='completed', provider_refund_id = ?, completed_at = NOW(), updated_at = NOW() WHERE id = ? AND state IN ('processing','cooling_off')");
                        $upd->execute([$refundObj->id, (int)$argoRequestId]);
                        if ($upd->rowCount() > 0) {
                            audit_log($pdo, (int)$req['company_id'], 'completed', 'webhook', null, (int)$argoRequestId, null, [
                                'provider_refund_id' => $refundObj->id,
                                'reconciled_via_webhook' => true,
                            ]);
                            $req['state'] = 'completed';
                            $req['provider_refund_id'] = $refundObj->id;
                            refund_notify_completion($pdo, $req);
                        }
                    }
                }
            }
        }
    } catch (\Throwable $e) {
        error_log('refund_requests reconciliation in stripe webhook failed: ' . $e->getMessage());
    }
}
