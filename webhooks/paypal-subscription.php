<?php
/**
 * PayPal Subscription Webhook Handler
 *
 * This endpoint receives webhook notifications from PayPal for subscription events.
 * Configure this URL in your PayPal Developer Dashboard:
 * https://yourdomain.com/webhooks/paypal-subscription.php
 *
 * Required Events to Subscribe:
 * - BILLING.SUBSCRIPTION.ACTIVATED
 * - BILLING.SUBSCRIPTION.CANCELLED
 * - BILLING.SUBSCRIPTION.EXPIRED
 * - BILLING.SUBSCRIPTION.SUSPENDED
 * - BILLING.SUBSCRIPTION.PAYMENT.FAILED
 * - PAYMENT.SALE.COMPLETED
 * - PAYMENT.SALE.DENIED
 * - PAYMENT.SALE.REFUNDED
 */

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../email_sender.php';
require_once __DIR__ . '/paypal-helper.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Get the raw request body
$rawBody = file_get_contents('php://input');

if (empty($rawBody)) {
    http_response_code(400);
    exit('Empty request body');
}

// Parse the webhook payload
$event = json_decode($rawBody, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit('Invalid JSON');
}

// Get the webhook ID from environment
$isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production';
$webhookId = $isProduction
    ? ($_ENV['PAYPAL_LIVE_WEBHOOK_ID'] ?? '')
    : ($_ENV['PAYPAL_SANDBOX_WEBHOOK_ID'] ?? '');

// Verify webhook signature — mandatory in BOTH sandbox and production. Without
// a configured webhook ID we cannot verify the request and any unauthenticated
// caller could POST fake billing events to extend subscriptions or insert
// payment rows.
if (empty($webhookId)) {
    $envLabel = $isProduction ? 'production' : 'sandbox';
    error_log("CRITICAL: PayPal webhook ID not configured in $envLabel - rejecting request");
    http_response_code(500);
    exit('Webhook not configured');
}

$headers = getallheaders();
if (!verifyPayPalWebhookSignature($headers, $rawBody, $webhookId)) {
    logPayPalWebhookEvent($event['event_type'] ?? 'UNKNOWN', $event, 'SIGNATURE_VERIFICATION_FAILED');
    http_response_code(401);
    exit('Invalid signature');
}

// Extract event details
$eventType = $event['event_type'] ?? '';
$resource = $event['resource'] ?? [];

// Log the event
logPayPalWebhookEvent($eventType, $event, 'received');

try {
    switch ($eventType) {
        case 'BILLING.SUBSCRIPTION.ACTIVATED':
            handleSubscriptionActivated($resource);
            break;

        case 'BILLING.SUBSCRIPTION.CANCELLED':
            handleSubscriptionCancelled($resource);
            break;

        case 'BILLING.SUBSCRIPTION.EXPIRED':
            handleSubscriptionExpired($resource);
            break;

        case 'BILLING.SUBSCRIPTION.SUSPENDED':
            handleSubscriptionSuspended($resource);
            break;

        case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
            handlePaymentFailed($resource);
            break;

        case 'PAYMENT.SALE.COMPLETED':
            handlePaymentCompleted($resource);
            break;

        case 'PAYMENT.SALE.DENIED':
            handlePaymentDenied($resource);
            break;

        case 'PAYMENT.SALE.REFUNDED':
            handlePaymentRefunded($resource);
            break;

        default:
            // Log unhandled event types for debugging
            logPayPalWebhookEvent($eventType, $event, 'unhandled');
            break;
    }

    // Respond with success
    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    error_log("PayPal webhook error: " . $e->getMessage());
    logPayPalWebhookEvent($eventType, $event, 'ERROR: ' . $e->getMessage());

    // Still respond with 200 to prevent PayPal from retrying
    // The error is logged for manual investigation
    http_response_code(200);
    echo json_encode(['status' => 'error', 'message' => 'Internal error logged']);
}

/**
 * Handle subscription activated event
 * Called when a subscription is activated — either a new subscription or a reactivation of a suspended one
 */
function handleSubscriptionActivated($resource) {
    global $pdo;

    $paypalSubscriptionId = $resource['id'] ?? '';

    if (empty($paypalSubscriptionId)) {
        throw new Exception("Missing subscription ID in activated event");
    }

    // Check if we already have this subscription in our database
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$paypalSubscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        // Subscription exists, update status to active
        $stmt = $pdo->prepare("
            UPDATE premium_subscriptions
            SET status = 'active', auto_renew = 1, updated_at = NOW()
            WHERE paypal_subscription_id = ?
        ");
        $stmt->execute([$paypalSubscriptionId]);

        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.ACTIVATED', $resource, 'subscription_reactivated');
    } else {
        // This is a new subscription created outside our normal flow
        // This shouldn't normally happen but handle it gracefully
        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.ACTIVATED', $resource, 'new_subscription_not_in_db');
    }
}

/**
 * Handle subscription cancelled event
 */
function handleSubscriptionCancelled($resource) {
    global $pdo;

    $paypalSubscriptionId = $resource['id'] ?? '';

    if (empty($paypalSubscriptionId)) {
        throw new Exception("Missing subscription ID in cancelled event");
    }

    // Race fix for cycle-switch flow: when a user switches PayPal billing
    // cycles, we cancel the old subscription server-side AFTER committing
    // the new one to the DB. The cancel triggers a BILLING.SUBSCRIPTION.
    // CANCELLED webhook for the OLD id. Without this guard, the handler
    // below would mark the row (now pointing at the NEW sub) as cancelled
    // and zero out credit_balance. The previous_paypal_subscription_id
    // column is set in the same transaction as paypal_subscription_id, so
    // this lookup is deterministic and survives webhook delivery delays.
    $stmt = $pdo->prepare("
        SELECT subscription_id FROM premium_subscriptions
        WHERE previous_paypal_subscription_id = ?
        LIMIT 1
    ");
    $stmt->execute([$paypalSubscriptionId]);
    if ($stmt->fetch()) {
        logPayPalWebhookEvent(
            'BILLING.SUBSCRIPTION.CANCELLED',
            $resource,
            'cycle_switch_old_sub_cancel_ignored'
        );
        return;
    }

    // Find subscription in our database
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$paypalSubscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        // Update subscription status to cancelled
        $stmt = $pdo->prepare("
            UPDATE premium_subscriptions
            SET status = 'cancelled',
                auto_renew = 0,
                credit_balance = 0,
                cancelled_at = NOW(),
                updated_at = NOW()
            WHERE paypal_subscription_id = ?
        ");
        $stmt->execute([$paypalSubscriptionId]);

        // Send cancellation email
        try {
            send_premium_subscription_cancelled_email(
                $subscription['email'],
                $subscription['subscription_id'],
                $subscription['end_date']
            );
        } catch (Exception $e) {
            error_log("Failed to send cancellation email: " . $e->getMessage());
        }

        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.CANCELLED', $resource, 'subscription_cancelled');
    } else {
        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.CANCELLED', $resource, 'subscription_not_found');
    }
}

/**
 * Handle subscription expired event
 */
function handleSubscriptionExpired($resource) {
    global $pdo;

    $paypalSubscriptionId = $resource['id'] ?? '';

    if (empty($paypalSubscriptionId)) {
        throw new Exception("Missing subscription ID in expired event");
    }

    // Update subscription status
    $stmt = $pdo->prepare("
        UPDATE premium_subscriptions
        SET status = 'expired', auto_renew = 0, updated_at = NOW()
        WHERE paypal_subscription_id = ?
    ");
    $stmt->execute([$paypalSubscriptionId]);

    logPayPalWebhookEvent('BILLING.SUBSCRIPTION.EXPIRED', $resource, 'subscription_expired');
}

/**
 * Handle subscription suspended event (payment issues)
 */
function handleSubscriptionSuspended($resource) {
    global $pdo;

    $paypalSubscriptionId = $resource['id'] ?? '';

    if (empty($paypalSubscriptionId)) {
        throw new Exception("Missing subscription ID in suspended event");
    }

    // Find subscription
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$paypalSubscriptionId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        // Update subscription status to payment_failed
        $stmt = $pdo->prepare("
            UPDATE premium_subscriptions
            SET status = 'payment_failed', updated_at = NOW()
            WHERE paypal_subscription_id = ?
        ");
        $stmt->execute([$paypalSubscriptionId]);

        // Send payment failed notification
        try {
            send_payment_failed_email(
                $subscription['email'],
                $subscription['subscription_id'],
                'Your PayPal subscription payment has failed. Please update your payment method.'
            );
        } catch (Exception $e) {
            error_log("Failed to send payment failed email: " . $e->getMessage());
        }

        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.SUSPENDED', $resource, 'subscription_suspended');
    }
}

/**
 * Handle payment failed event
 */
function handlePaymentFailed($resource) {
    global $pdo;

    $billingAgreementId = $resource['billing_agreement_id'] ?? '';

    if (empty($billingAgreementId)) {
        // Try to get from parent resource
        $billingAgreementId = $resource['id'] ?? '';
    }

    // Find subscription
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$billingAgreementId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        // Log the failed payment using the subscription's actual currency,
        // not a hardcoded 'CAD' that misrepresents non-CAD subscriptions.
        $subCurrency = $subscription['currency'] ?? 'CAD';
        $stmt = $pdo->prepare("
            INSERT INTO premium_subscription_payments (
                subscription_id, amount, currency, payment_method,
                transaction_id, status, payment_type, error_message, created_at
            ) VALUES (?, 0, ?, 'paypal', NULL, 'failed', 'renewal', 'PayPal payment failed', NOW())
        ");
        $stmt->execute([$subscription['subscription_id'], $subCurrency]);

        logPayPalWebhookEvent('BILLING.SUBSCRIPTION.PAYMENT.FAILED', $resource, 'payment_failed_logged');
    }
}

/**
 * Handle successful payment (renewal) event
 */
function handlePaymentCompleted($resource) {
    global $pdo;

    $billingAgreementId = $resource['billing_agreement_id'] ?? '';
    $transactionId = $resource['id'] ?? '';
    $amount = $resource['amount']['total'] ?? 0;
    $currency = $resource['amount']['currency'] ?? 'CAD';
    $state = $resource['state'] ?? '';

    if (empty($billingAgreementId) || $state !== 'completed') {
        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'skipped_invalid_state');
        return;
    }

    // Find subscription
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$billingAgreementId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$subscription) {
        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'subscription_not_found');
        return;
    }

    // Check if this is the initial payment (transaction already exists) or a renewal
    $stmt = $pdo->prepare("SELECT id FROM premium_subscription_payments WHERE transaction_id = ?");
    $stmt->execute([$transactionId]);

    if ($stmt->fetch()) {
        // Transaction already processed
        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'duplicate_transaction');
        return;
    }

    // Determine if this is a renewal (subscription already has payments)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM premium_subscription_payments WHERE subscription_id = ?");
    $stmt->execute([$subscription['subscription_id']]);
    $paymentCount = $stmt->fetch()['count'] ?? 0;

    $paymentType = $paymentCount > 0 ? 'renewal' : 'initial';

    // Detect "first bill after a cycle switch": process-subscription.php
    // already set end_date and sent the cycle-changed email when the user
    // approved the new PayPal sub. This webhook arrives whenever PayPal
    // first bills the new sub (usually seconds, but can be hours if PayPal
    // is queued or having an outage). Without this guard, the renewal
    // handler would extend end_date a SECOND time and send a duplicate.
    //
    // Detection is deterministic: real renewals fire when end_date is at
    // or near NOW. A cycle switch resets end_date to today + full cycle,
    // so for the first-bill-after-switch case end_date is far in the
    // future. Threshold is 70% of the cycle to allow some slack — even
    // an "early" PayPal renewal won't arrive when end_date is still 70%
    // of a cycle out, but a freshly-switched sub will be ~100%.
    $cycleSecs = ($subscription['billing_cycle'] === 'yearly')
        ? 365 * 86400
        : 30 * 86400;
    $secsUntilEnd = strtotime($subscription['end_date']) - time();
    $cycleSwitchFirstBill = !empty($subscription['last_cycle_change_at'])
        && $secsUntilEnd > (0.7 * $cycleSecs);

    // Log the payment
    $stmt = $pdo->prepare("
        INSERT INTO premium_subscription_payments (
            subscription_id, amount, currency, payment_method,
            transaction_id, status, payment_type, created_at
        ) VALUES (?, ?, ?, 'paypal', ?, 'completed', ?, NOW())
    ");
    $stmt->execute([
        $subscription['subscription_id'],
        $amount,
        $currency,
        $transactionId,
        $paymentType
    ]);

    if ($cycleSwitchFirstBill) {
        // First bill after a cycle switch — record the sale (above), but
        // don't extend end_date and don't send a renewal email. Both were
        // already handled when the user confirmed the switch.
        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'cycle_switch_first_bill_recorded');
    } elseif ($paymentType === 'renewal') {
        $billing = $subscription['billing_cycle'];
        $newEndDate = calculateNewEndDate($subscription['end_date'], $billing);

        $stmt = $pdo->prepare("
            UPDATE premium_subscriptions
            SET end_date = ?,
                status = 'active',
                updated_at = NOW()
            WHERE subscription_id = ?
        ");
        $stmt->execute([$newEndDate, $subscription['subscription_id']]);

        // Send renewal receipt email
        try {
            send_premium_subscription_receipt(
                $subscription['email'],
                $subscription['subscription_id'],
                $billing,
                $amount,
                $newEndDate,
                $transactionId,
                'paypal'
            );
        } catch (Exception $e) {
            error_log("Failed to send renewal receipt: " . $e->getMessage());
        }

        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'renewal_processed');
    } else {
        logPayPalWebhookEvent('PAYMENT.SALE.COMPLETED', $resource, 'initial_payment_logged');
    }
}

/**
 * Handle payment denied event
 */
function handlePaymentDenied($resource) {
    global $pdo;

    $billingAgreementId = $resource['billing_agreement_id'] ?? '';
    $transactionId = $resource['id'] ?? '';

    if (empty($billingAgreementId)) {
        return;
    }

    // Find subscription
    $stmt = $pdo->prepare("SELECT * FROM premium_subscriptions WHERE paypal_subscription_id = ?");
    $stmt->execute([$billingAgreementId]);
    $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subscription) {
        // Log the failed payment using the subscription's actual currency.
        $subCurrency = $subscription['currency'] ?? 'CAD';
        $stmt = $pdo->prepare("
            INSERT INTO premium_subscription_payments (
                subscription_id, amount, currency, payment_method,
                transaction_id, status, payment_type, error_message, created_at
            ) VALUES (?, 0, ?, 'paypal', ?, 'failed', 'renewal', 'Payment denied by PayPal', NOW())
        ");
        $stmt->execute([$subscription['subscription_id'], $subCurrency, $transactionId]);

        // Send notification
        try {
            send_payment_failed_email(
                $subscription['email'],
                $subscription['subscription_id'],
                'Your PayPal payment was denied. Please update your payment method.'
            );
        } catch (Exception $e) {
            error_log("Failed to send payment denied email: " . $e->getMessage());
        }

        logPayPalWebhookEvent('PAYMENT.SALE.DENIED', $resource, 'payment_denied_logged');
    }
}

/**
 * Handle payment refunded event
 */
function handlePaymentRefunded($resource) {
    global $pdo;

    $saleId = $resource['sale_id'] ?? '';
    $refundId = $resource['id'] ?? '';
    $amount = $resource['amount']['total'] ?? 0;

    if (empty($saleId)) {
        return;
    }

    // Find the original payment
    $stmt = $pdo->prepare("SELECT * FROM premium_subscription_payments WHERE transaction_id = ?");
    $stmt->execute([$saleId]);
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($payment) {
        // Log the refund using the original payment's currency, or fall back
        // to the PayPal-supplied refund currency, never a hardcoded 'CAD'.
        $refundCurrency = $payment['currency']
            ?? $resource['amount']['currency']
            ?? 'CAD';
        $stmt = $pdo->prepare("
            INSERT INTO premium_subscription_payments (
                subscription_id, amount, currency, payment_method,
                transaction_id, status, payment_type, created_at
            ) VALUES (?, ?, ?, 'paypal', ?, 'refunded', 'renewal', NOW())
        ");
        $stmt->execute([$payment['subscription_id'], -$amount, $refundCurrency, $refundId]);

        logPayPalWebhookEvent('PAYMENT.SALE.REFUNDED', $resource, 'refund_logged');
    }
}

/**
 * Calculate new subscription end date.
 *
 * Bases the new period on the LATER of the existing end_date or NOW() so a
 * delayed renewal doesn't leave the new end_date still in the past.
 */
function calculateNewEndDate($currentEndDate, $billing) {
    $endDateTime = new DateTime($currentEndDate);
    $now = new DateTime('now');
    if ($endDateTime < $now) {
        $endDateTime = $now;
    }

    if ($billing === 'yearly') {
        $endDateTime->add(new DateInterval('P1Y'));
    } else {
        $endDateTime->add(new DateInterval('P1M'));
    }

    return $endDateTime->format('Y-m-d H:i:s');
}
