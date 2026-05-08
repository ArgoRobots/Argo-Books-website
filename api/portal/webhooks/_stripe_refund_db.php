<?php
declare(strict_types=1);

/**
 * Apply a Stripe refund to the portal payments / invoices tables.
 *
 * Extracted from api/portal/webhooks/stripe.php so the DB-only side of the
 * refund flow can be exercised without standing up a Stripe webhook test
 * harness. The caller (the webhook handler) still owns extraction of the
 * refund amount from the SDK Charge object — currency-divisor logic stays
 * out of this function.
 *
 * Steps:
 *   1. Find the original completed payment row by provider_payment_id.
 *   2. Insert a negative payment row marked status=refunded
 *      (uses provider_payment_id 'refund_' . $providerPaymentId so the
 *       INSERT ... ON DUPLICATE KEY UPDATE in record_portal_payment() makes
 *       repeated refund webhooks idempotent).
 *   3. Flip the original payment row to status=refunded.
 *   4. Bump invoice.balance_due by the refund amount, capped at total_amount;
 *      flip status back to 'sent' once fully refunded, else 'partial'.
 *
 * Returns false when no matching completed original payment exists; true
 * after all four steps run.
 */
function apply_stripe_refund_to_db(
    PDO $pdo,
    string $providerPaymentId,
    float $refundAmount,
    string $chargeId,
    bool $isProduction
): bool {
    $stmt = $pdo->prepare(
        'SELECT id, company_id, invoice_id, customer_name, currency
         FROM portal_payments
         WHERE provider_payment_id = ? AND status = "completed"
         LIMIT 1'
    );
    $stmt->execute([$providerPaymentId]);
    $originalPayment = $stmt->fetch();

    if (!$originalPayment) {
        return false;
    }

    record_portal_payment([
        'company_id' => $originalPayment['company_id'],
        'invoice_id' => $originalPayment['invoice_id'],
        'customer_name' => $originalPayment['customer_name'],
        'amount' => -$refundAmount,
        'currency' => $originalPayment['currency'],
        'payment_method' => 'stripe',
        'provider_payment_id' => 'refund_' . $providerPaymentId,
        'provider_transaction_id' => $chargeId,
        'reference_number' => generate_reference_number(),
        'status' => 'refunded',
        'payment_environment' => $isProduction ? 'production' : 'sandbox',
    ]);

    $stmt = $pdo->prepare(
        'UPDATE portal_payments SET status = "refunded" WHERE id = ?'
    );
    $stmt->execute([$originalPayment['id']]);

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

    return true;
}
