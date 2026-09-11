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
 * Call ONCE PER INDIVIDUAL Refund (Stripe Refund.id), as
 * apply_stripe_charge_refunds() does. Keying by individual refund ID (instead
 * of payment intent) lets multiple partial refunds coexist as separate
 * negative-payment rows, and matches the key the desktop refund flow writes.
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

/**
 * Apply a charge.refunded event: one ledger row per refund on the charge,
 * then complete any refund_requests those refunds were issued for.
 */
function apply_stripe_charge_refunds(
    PDO $pdo,
    \Stripe\Charge $charge,
    bool $isProduction,
    ?string $connectedAccount = null
): void {
    $providerPaymentId = $charge->payment_intent;

    if (empty($providerPaymentId)) {
        // Charges without a payment_intent (rare, legacy direct charges)
        // can't be tied to a portal payment record; skip silently.
        error_log("Portal Stripe webhook: skipping charge.refunded for {$charge->id}: no payment_intent");
        return;
    }

    // Use the original payment's currency (already stored in our DB) so refunds
    // honour the exact currency used at capture time even if Stripe normalised.
    $stmt = $pdo->prepare(
        'SELECT currency FROM portal_payments
         WHERE provider_payment_id = ? AND amount > 0 LIMIT 1'
    );
    $stmt->execute([$providerPaymentId]);
    $row = $stmt->fetch();
    if (!$row) {
        // No matching portal payment: likely a charge that didn't originate
        // from the customer portal (e.g. license/subscription, or a refund
        // for a deleted/migrated record). Log so support can trace
        // disappearing-refund tickets.
        error_log("Portal Stripe webhook: charge.refunded for {$charge->id} (PI {$providerPaymentId}) has no matching portal payment row");
        return;
    }
    $refundCurrency = strtoupper($row['currency'] ?? 'USD');
    $zeroDecimalCurrencies = ['BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'];
    $divisor = in_array($refundCurrency, $zeroDecimalCurrencies) ? 1 : 100;

    // Always one row per Refund, keyed by its id. The desktop refund flow
    // writes the same key, so a refund it already recorded is a no-op here,
    // and each partial refund gets its own row.
    $refunds = stripe_charge_refunds($charge, $connectedAccount);
    foreach ($refunds as $refundObj) {
        $refundAmount = ($refundObj->amount ?? 0) / $divisor;
        if ($refundAmount <= 0) continue;
        apply_stripe_refund_to_db(
            $pdo,
            $providerPaymentId,
            $refundAmount,
            $charge->id,
            $isProduction,
            $refundObj->id
        );
    }

    // Reconcile against refund_requests if this refund was initiated by our
    // /api/portal/refunds/ flow (refund metadata carries argo_request_id).
    // Idempotent: no-op if already completed; transitions processing/cooling_off
    // → completed otherwise.
    try {
        require_once __DIR__ . '/../_audit.php';
        require_once __DIR__ . '/../_refund_helpers.php';
        foreach ($refunds as $refundObj) {
            $argoRequestId = $refundObj->metadata['argo_request_id'] ?? null;
            if ($argoRequestId !== null && is_numeric($argoRequestId)) {
                $stmt = $pdo->prepare("SELECT * FROM refund_requests WHERE id = ?");
                $stmt->execute([(int)$argoRequestId]);
                $req = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($req && $req['state'] !== 'completed' && $req['state'] !== 'cancelled') {
                    // CAS guard so a race with the synchronous execute
                    // path can't fire two completion notifications. Only
                    // the UPDATE that flips the state actually notifies.
                    $upd = $pdo->prepare("UPDATE refund_requests SET state='completed', provider_refund_id = ?, completed_at = NOW(), cancel_token = NULL, updated_at = NOW() WHERE id = ? AND state IN ('processing','cooling_off')");
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
    } catch (\Throwable $e) {
        error_log('refund_requests reconciliation in stripe webhook failed: ' . $e->getMessage());
    }
}

/**
 * Every refund on the charge. Since API version 2022-11-15 a Charge no longer
 * includes `refunds`, and webhook payloads use the endpoint's API version, so
 * the list usually has to be fetched from the connected account that owns the
 * charge. Throws on API failure so the webhook can ask Stripe to retry.
 *
 * @return \Stripe\Refund[]
 */
function stripe_charge_refunds(\Stripe\Charge $charge, ?string $connectedAccount): array
{
    if (isset($charge->refunds) && empty($charge->refunds->has_more)) {
        return $charge->refunds->data;
    }

    $opts = $connectedAccount ? ['stripe_account' => $connectedAccount] : [];
    $list = \Stripe\Refund::all(['charge' => $charge->id, 'limit' => 100], $opts);
    return iterator_to_array($list->autoPagingIterator(), false);
}
