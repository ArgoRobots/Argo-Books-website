<?php
declare(strict_types=1);

namespace Tests\Integration\Renewal;

use Tests\Helpers\DatabaseTestCase;

/**
 * Covers reconcile_paypal_initial_capture(), the rule that keeps the first
 * PayPal sale (which checkout already recorded under a placeholder id) from
 * being double-counted as a renewal. The PayPal subscription webhook routes
 * through it.
 */
final class PayPalInitialCaptureReconcileTest extends DatabaseTestCase
{
    private const SUB_ID     = 'PREM-RECO-NCIL-EEEE-TEST';
    private const PAYPAL_SUB = 'I-RECONCILETEST01';
    private const REAL_SALE  = 'SALE-REAL-0001';

    public function test_reconciles_placeholder_to_real_sale_id(): void
    {
        $this->insertPayment(self::PAYPAL_SUB, 'initial'); // checkout placeholder

        $result = reconcile_paypal_initial_capture(
            $this->pdo,
            self::SUB_ID,
            self::PAYPAL_SUB,
            self::REAL_SALE
        );

        $this->assertTrue($result);
        $this->assertSame(self::REAL_SALE, $this->onlyTransactionId());
    }

    public function test_returns_false_when_no_placeholder_exists(): void
    {
        // A subscription whose initial capture was already reconciled has no
        // placeholder (txn = the PayPal sub id) left to match.
        $this->insertPayment(self::REAL_SALE, 'initial');

        $result = reconcile_paypal_initial_capture(
            $this->pdo,
            self::SUB_ID,
            self::PAYPAL_SUB,
            'SALE-REAL-0002'
        );

        $this->assertFalse($result);
        // Nothing was touched.
        $this->assertSame(self::REAL_SALE, $this->onlyTransactionId());
    }

    public function test_second_call_is_idempotent(): void
    {
        $this->insertPayment(self::PAYPAL_SUB, 'initial');

        $this->assertTrue(reconcile_paypal_initial_capture($this->pdo, self::SUB_ID, self::PAYPAL_SUB, self::REAL_SALE));
        // A re-delivery finds the placeholder gone (txn is now the real id), so
        // there is nothing left to reconcile.
        $this->assertFalse(reconcile_paypal_initial_capture($this->pdo, self::SUB_ID, self::PAYPAL_SUB, self::REAL_SALE));
        $this->assertSame(self::REAL_SALE, $this->onlyTransactionId());
    }

    public function test_only_initial_row_is_reconciled_not_a_renewal(): void
    {
        // A renewal that (pathologically) shares the PayPal sub id as its txn
        // must not be hijacked: only payment_type='initial' is reconciled.
        $this->insertPayment(self::PAYPAL_SUB, 'renewal');

        $result = reconcile_paypal_initial_capture(
            $this->pdo,
            self::SUB_ID,
            self::PAYPAL_SUB,
            self::REAL_SALE
        );

        $this->assertFalse($result);
        $this->assertSame(self::PAYPAL_SUB, $this->onlyTransactionId());
    }

    private function insertPayment(string $transactionId, string $type): void
    {
        $this->pdo->prepare(
            "INSERT INTO premium_subscription_payments
             (subscription_id, amount, currency, payment_method, transaction_id,
              status, payment_type, environment, created_at)
             VALUES (?, 10.00, 'CAD', 'paypal', ?, 'completed', ?, 'sandbox', NOW())"
        )->execute([self::SUB_ID, $transactionId, $type]);
    }

    private function onlyTransactionId(): string
    {
        $stmt = $this->pdo->prepare(
            "SELECT transaction_id FROM premium_subscription_payments
             WHERE subscription_id = ? ORDER BY id ASC"
        );
        $stmt->execute([self::SUB_ID]);
        $ids = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $this->assertCount(1, $ids, 'expected exactly one payment row for the subscription');
        return (string) $ids[0];
    }
}
