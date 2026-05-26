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
           AND payment_type = 'renewal'
           AND created_at > DATE_SUB(NOW(), INTERVAL 23 HOUR)
         LIMIT 1"
    );
    $stmt->execute([$subscriptionId]);
    return $stmt->fetch() !== false;
}
