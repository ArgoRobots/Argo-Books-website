<?php
declare(strict_types=1);

/**
 * Pure helpers extracted from subscription_renewal.php so the renewal logic
 * can be exercised without running the cron's top-level dispatch loop.
 *
 * - calculateNewEndDate: date math with a "stale end_date" guard
 * - decide_renewal_charge: credit-balance decision tree returning the
 *   billed amount including the processing fee
 * - recently_renewed: 23-hour idempotency guard against double-charges
 */

require_once __DIR__ . '/../../config/pricing.php';

/**
 * Calculate new subscription end date.
 *
 * Bases the new period on the LATER of the existing end_date or NOW(). When a
 * cron run is delayed and end_date is already in the past, extending from the
 * stale end_date can leave the new end_date still in the past, causing the
 * subscription to be picked up again and re-charged on the next run.
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

/**
 * Reconcile the checkout placeholder for a PayPal subscription's initial
 * capture.
 *
 * At checkout, process-subscription.php records the first PayPal payment
 * immediately, using the PayPal subscription id as the placeholder
 * transaction_id because the real sale id doesn't exist until PayPal actually
 * captures the money. When PayPal later reports that first sale via the
 * PAYMENT.SALE.COMPLETED webhook, its real id should REPLACE the placeholder
 * rather than create a second, phantom "renewal" row.
 *
 * Returns true if a placeholder row existed for this subscription (and now
 * carries $realTxnId), false if there was nothing to reconcile — in which case
 * the caller should treat the sale as a genuine new payment.
 */
function reconcile_paypal_initial_capture(PDO $pdo, string $subscriptionId, string $paypalSubscriptionId, string $realTxnId): bool
{
    $find = $pdo->prepare(
        "SELECT id FROM premium_subscription_payments
         WHERE subscription_id = ? AND transaction_id = ? AND payment_type = 'initial'
         LIMIT 1"
    );
    $find->execute([$subscriptionId, $paypalSubscriptionId]);
    $rowId = $find->fetchColumn();

    if ($rowId === false) {
        return false;
    }

    // The `transaction_id <> ?` guard makes a re-run a harmless no-op once the
    // real id is already attached.
    $upd = $pdo->prepare(
        "UPDATE premium_subscription_payments
         SET transaction_id = ?
         WHERE id = ? AND transaction_id <> ?"
    );
    $upd->execute([$realTxnId, $rowId, $realTxnId]);

    return true;
}

/**
 * Decide how much to charge for a renewal given the customer's credit balance.
 *
 * Returns:
 *   useCredit:       true only when credit fully covers the renewal
 *   creditUsed:      amount of credit consumed (0 when no credit)
 *   baseAmount:      pre-fee renewal price for the billing cycle
 *   amountToCharge:  final amount to charge the card, INCLUDING the
 *                     processing fee. 0 when fully credit-covered.
 *
 * The processing fee is intentionally added inside this helper so the call
 * site reflects the same one-shot decision the cron already makes, the
 * single source of truth for "what does the card see this cycle?".
 */
function decide_renewal_charge(float $creditBalance, string $billing, array $pricingConfig): array
{
    $baseAmount = ($billing === 'yearly')
        ? (float) $pricingConfig['premium_yearly_price']
        : (float) $pricingConfig['premium_monthly_price'];

    $useCredit = false;
    $creditUsed = 0.0;
    $amountToCharge = $baseAmount;

    if ($creditBalance > 0) {
        if ($creditBalance >= $baseAmount) {
            $useCredit = true;
            $creditUsed = $baseAmount;
            $amountToCharge = 0.0;
        } else {
            $creditUsed = $creditBalance;
            $amountToCharge = $baseAmount - $creditBalance;
        }
    }

    if ($amountToCharge > 0) {
        $amountToCharge += calculate_processing_fee($amountToCharge);
    }

    return [
        'useCredit' => $useCredit,
        'creditUsed' => $creditUsed,
        'baseAmount' => $baseAmount,
        'amountToCharge' => $amountToCharge,
    ];
}

/**
 * Idempotency guard for the renewal cron: returns true if a successful
 * renewal payment has already landed for this subscription within the last
 * 23 hours. Protects against double-charges when the cron fires twice
 * (overlapping runs, manual retries, accidental re-invokes).
 */
function recently_renewed(PDO $pdo, string $subscriptionId): bool
{
    $stmt = $pdo->prepare(
        "SELECT 1 FROM premium_subscription_payments
         WHERE subscription_id = ?
           AND status = 'completed'
           AND payment_type IN ('renewal', 'credit')
           AND created_at > DATE_SUB(NOW(), INTERVAL 23 HOUR)
         LIMIT 1"
    );
    $stmt->execute([$subscriptionId]);
    return $stmt->fetch() !== false;
}
