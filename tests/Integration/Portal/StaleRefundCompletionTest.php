<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

/**
 * When the refund webhook never arrives, the stale-processing cron is what
 * completes the refund, so it has to write the ledger row the desktop syncs.
 */
final class StaleRefundCompletionTest extends DatabaseTestCase
{
    private int $companyId;

    private function seedProcessingStripeRefund(int $amountCents): array
    {
        $this->companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($this->companyId, 'INV-STALE-1', 100.00, balanceDue: 0.00, status: 'paid');
        $paymentIntentId = 'pi_stale_' . bin2hex(random_bytes(4));
        $this->pdo->prepare(
            "INSERT INTO portal_payments
             (company_id, invoice_id, customer_name, amount, processing_fee,
              currency, payment_method, provider_payment_id, provider_transaction_id,
              reference_number, status, payment_environment, created_at)
             VALUES (?, 'INV-STALE-1', 'Test Customer', 100.00, 0.00,
                     'USD', 'stripe', ?, 'ch_stale', ?, 'completed', 'sandbox', NOW())"
        )->execute([$this->companyId, $paymentIntentId, 'PAY-' . date('Ymd') . '-ST0001']);

        $this->pdo->prepare(
            "INSERT INTO refund_requests
             (company_id, invoice_id, invoice_number, customer_name, provider,
              provider_payment_id, amount_cents, currency, state)
             VALUES (?, 'INV-STALE-1', 'INV-STALE-1', 'Test Customer', 'stripe', ?, ?, 'USD', 'processing')"
        )->execute([$this->companyId, $paymentIntentId, $amountCents]);
        $requestId = (int) $this->pdo->lastInsertId();

        // Same row shape the cron selects.
        $stmt = $this->pdo->prepare(
            'SELECT r.*, c.environment, c.stripe_account_id
             FROM refund_requests r INNER JOIN portal_companies c ON c.id = r.company_id
             WHERE r.id = ?'
        );
        $stmt->execute([$requestId]);
        return $stmt->fetch();
    }

    private function company(array $row): array
    {
        return ['environment' => $row['environment'], 'stripe_account_id' => $row['stripe_account_id']];
    }

    public function test_records_the_refund_in_the_payment_ledger(): void
    {
        $row = $this->seedProcessingStripeRefund(4000);

        $this->assertTrue(refund_complete_from_stale_cron($this->pdo, $row, $this->company($row), 're_stale_1'));

        $stmt = $this->pdo->prepare('SELECT amount, status FROM portal_payments WHERE provider_payment_id = ?');
        $stmt->execute(['refund_re_stale_1']);
        $ledger = $stmt->fetch();
        $this->assertNotFalse($ledger);
        $this->assertSame(-40.00, (float) $ledger['amount']);
        $this->assertSame('refunded', $ledger['status']);

        $stmt = $this->pdo->prepare('SELECT balance_due, status FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$this->companyId, 'INV-STALE-1']);
        $invoice = $stmt->fetch();
        $this->assertSame(40.00, (float) $invoice['balance_due']);
        $this->assertSame('partial', $invoice['status']);

        $stmt = $this->pdo->prepare('SELECT state, provider_refund_id FROM refund_requests WHERE id = ?');
        $stmt->execute([$row['id']]);
        $request = $stmt->fetch();
        $this->assertSame('completed', $request['state']);
        $this->assertSame('re_stale_1', $request['provider_refund_id']);
    }

    public function test_a_second_run_does_not_record_the_refund_twice(): void
    {
        $row = $this->seedProcessingStripeRefund(4000);

        refund_complete_from_stale_cron($this->pdo, $row, $this->company($row), 're_stale_2');
        $this->assertFalse(refund_complete_from_stale_cron($this->pdo, $row, $this->company($row), 're_stale_2'));

        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM portal_payments WHERE company_id = ? AND amount < 0');
        $stmt->execute([$this->companyId]);
        $this->assertSame(1, (int) $stmt->fetchColumn());

        $stmt = $this->pdo->prepare('SELECT balance_due FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$this->companyId, 'INV-STALE-1']);
        $this->assertSame(40.00, (float) $stmt->fetch()['balance_due']);
    }
}
