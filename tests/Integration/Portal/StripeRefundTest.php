<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

final class StripeRefundTest extends DatabaseTestCase
{
    public function test_inserts_negative_payment_and_flips_original_to_refunded(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-REF-001', 200.00, balanceDue: 0.00, status: 'paid');

        $providerPaymentId = 'pi_test_refund_' . bin2hex(random_bytes(4));

        // Seed a completed original payment row
        $this->pdo->prepare(
            "INSERT INTO portal_payments
             (company_id, invoice_id, customer_name, amount, processing_fee,
              currency, payment_method, provider_payment_id, provider_transaction_id,
              reference_number, status, payment_environment, created_at)
             VALUES (?, 'INV-REF-001', 'Test Customer', 200.00, 0.00,
                     'USD', 'stripe', ?, 'ch_orig', ?, 'completed', 'sandbox', NOW())"
        )->execute([$companyId, $providerPaymentId, 'PAY-' . date('Ymd') . '-AAAAAA']);

        $ok = apply_stripe_refund_to_db($this->pdo, $providerPaymentId, 200.00, 'ch_refund_xyz', false);
        $this->assertTrue($ok);

        // Negative refund payment row exists
        $stmt = $this->pdo->prepare(
            'SELECT amount, status FROM portal_payments WHERE provider_payment_id = ?'
        );
        $stmt->execute(['refund_' . $providerPaymentId]);
        $refund = $stmt->fetch();
        $this->assertNotFalse($refund);
        $this->assertSame(-200.00, (float) $refund['amount']);
        $this->assertSame('refunded', $refund['status']);

        // Original payment row flipped to refunded
        $stmt = $this->pdo->prepare(
            'SELECT status FROM portal_payments WHERE provider_payment_id = ?'
        );
        $stmt->execute([$providerPaymentId]);
        $this->assertSame('refunded', $stmt->fetch()['status']);
    }

    public function test_refund_increases_invoice_balance_due_capped_at_total(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-REF-002', 100.00, balanceDue: 0.00, status: 'paid');

        $providerPaymentId = 'pi_test_cap_' . bin2hex(random_bytes(4));
        $this->pdo->prepare(
            "INSERT INTO portal_payments
             (company_id, invoice_id, customer_name, amount, processing_fee,
              currency, payment_method, provider_payment_id, provider_transaction_id,
              reference_number, status, payment_environment, created_at)
             VALUES (?, 'INV-REF-002', 'Test Customer', 100.00, 0.00,
                     'USD', 'stripe', ?, 'ch_orig2', ?, 'completed', 'sandbox', NOW())"
        )->execute([$companyId, $providerPaymentId, 'PAY-' . date('Ymd') . '-BBBBBB']);

        // Refund larger than total — should cap balance_due at total_amount
        apply_stripe_refund_to_db($this->pdo, $providerPaymentId, 500.00, 'ch_refund_cap', false);

        $stmt = $this->pdo->prepare(
            'SELECT balance_due, status FROM portal_invoices WHERE company_id = ? AND invoice_id = ?'
        );
        $stmt->execute([$companyId, 'INV-REF-002']);
        $invoice = $stmt->fetch();
        $this->assertSame(100.00, (float) $invoice['balance_due']);
        $this->assertSame('sent', $invoice['status']);
    }

    public function test_returns_false_when_no_matching_completed_payment(): void
    {
        $ok = apply_stripe_refund_to_db($this->pdo, 'pi_does_not_exist', 50.00, 'ch_x', false);
        $this->assertFalse($ok);
    }
}
