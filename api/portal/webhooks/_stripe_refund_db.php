<?php
declare(strict_types=1);

/**
 * Apply a Stripe refund to the portal payments / invoices tables.
 *
 * Extracted from api/portal/webhooks/stripe.php so the DB-only side of the
 * refund flow can be exercised without standing up a Stripe webhook test
 * harness. The caller (the webhook handler) still owns extraction of the
 * refund amount from the SDK Charge object; currency-divisor logic stays
 * out of this function.
 *
 * Call ONCE PER INDIVIDUAL Refund (Stripe Refund.id). When a charge has
 * multiple partial refunds, the webhook ships the cumulative amount on
 * $charge->amount_refunded but also lists each individual refund in
 * $charge->refunds->data. Iterate that list and call this function per
 * refund. Keying by individual refund ID (instead of payment intent) lets
 * multiple partial refunds coexist as separate negative-payment rows.
 *
 * The original payment row is only flipped to status='refunded' once the
 * sum of all refund amounts against it covers the original payment amount;
 * partial refunds leave it as 'completed'.
 *
 * Returns false when no matching original payment exists; true otherwise.
 *
 * NOTE on the $pdo parameter: $pdo is used for the SELECT/UPDATE statements
 * inside this function, but the call to record_portal_payment() reaches for
 * `global $pdo` independently. Callers MUST ensure $GLOBALS['pdo'] points
 * at the same connection passed in (production code does this via
 * db_connect.php; tests do it via tests/bootstrap.php). Passing a different
 * PDO would cause the negative-payment INSERT to land on a different
 * connection than the rest of this function. Don't do that.
 */
function apply_stripe_refund_to_db(
    PDO $pdo,
    string $providerPaymentId,
    float $refundAmount,
    string $chargeId,
    bool $isProduction,
    ?string $refundId = null
): bool {
    // Find the original payment regardless of its status: partial-refund
    // scenarios leave it in 'completed' AND we still want the second refund
    // webhook to land. Matching only on status='completed' was the previous
    // behavior and dropped subsequent partial refunds entirely.
    $stmt = $pdo->prepare(
        'SELECT id, company_id, invoice_id, customer_name, currency, amount, status
         FROM portal_payments
         WHERE provider_payment_id = ? AND amount > 0
         LIMIT 1'
    );
    $stmt->execute([$providerPaymentId]);
    $originalPayment = $stmt->fetch();

    if (!$originalPayment) {
        return false;
    }

    // Key the negative-payment row by the individual Refund ID when known so
    // multiple partial refunds on one charge produce distinct rows. Fall back
    // to the payment-intent-derived key for legacy callers (older tests) that
    // exercise a single full-refund scenario.
    $refundKey = $refundId !== null
        ? 'refund_' . $refundId
        : 'refund_' . $providerPaymentId;

    // Capture whether the refund row was newly inserted. record_portal_payment
    // is idempotent via the UNIQUE index on provider_payment_id; if Stripe
    // retries the same Refund event, the second call no-ops here. We must
    // also skip the invoice balance / original-payment-status updates below
    // on those retries; otherwise the refund would be double-applied to
    // the invoice.
    $recordResult = record_portal_payment([
        'company_id' => $originalPayment['company_id'],
        'invoice_id' => $originalPayment['invoice_id'],
        'customer_name' => $originalPayment['customer_name'],
        'amount' => -$refundAmount,
        'currency' => $originalPayment['currency'],
        'payment_method' => 'stripe',
        'provider_payment_id' => $refundKey,
        'provider_transaction_id' => $chargeId,
        'reference_number' => generate_reference_number(),
        'status' => 'refunded',
        'payment_environment' => $isProduction ? 'production' : 'sandbox',
    ]);
    if (empty($recordResult['inserted'])) {
        // Duplicate event (webhook retry, or refund already synced via the
        // synchronous execute path). Books are already up to date.
        return true;
    }

    // Flip the original payment to 'refunded' only once cumulative refunds
    // cover the original amount. Scope the sum to refunds against THIS
    // specific charge (provider_transaction_id = chargeId), not the whole
    // invoice, so refunds on a sibling payment for the same invoice can't
    // inflate this total and incorrectly flip the wrong payment. Tiny
    // epsilon for cent-level float drift.
    $sumStmt = $pdo->prepare(
        "SELECT COALESCE(SUM(amount), 0) AS refunded_total
         FROM portal_payments
         WHERE amount < 0
           AND payment_method = 'stripe'
           AND provider_transaction_id = ?"
    );
    $sumStmt->execute([$chargeId]);
    // Compare in integer cents so a chain of partial refunds can't drift past
    // the threshold via repeated float rounding.
    $refundedCents = (int)round(abs((float)$sumStmt->fetch()['refunded_total']) * 100);
    $originalCents = (int)round((float)$originalPayment['amount'] * 100);
    if ($refundedCents >= $originalCents) {
        $stmt = $pdo->prepare(
            'UPDATE portal_payments SET status = "refunded" WHERE id = ?'
        );
        $stmt->execute([$originalPayment['id']]);
    }

    // MySQL evaluates SET assignments left-to-right; later expressions see
    // already-updated columns. Compute status BEFORE updating balance_due so
    // the CASE reads the pre-refund balance; otherwise a partial refund
    // (e.g. $50 of $100 paid) computes "balance_due_new (=$50) + refund
    // (=$50) >= total (=$100)" → TRUE → status flips to "sent" instead of
    // "partial".
    $stmt = $pdo->prepare(
        'UPDATE portal_invoices
         SET status = CASE
                 WHEN balance_due + ? >= total_amount THEN "sent"
                 ELSE "partial"
             END,
             balance_due = LEAST(total_amount, balance_due + ?),
             updated_at = NOW()
         WHERE company_id = ? AND invoice_id = ?'
    );
    $stmt->execute([
        $refundAmount, $refundAmount,
        $originalPayment['company_id'], $originalPayment['invoice_id']
    ]);

    return true;
}
