<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

final class RecordPortalPaymentTest extends DatabaseTestCase
{
    public function test_records_payment_and_marks_invoice_paid_for_full_amount(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-FULL-001', 100.00, currency: 'USD');

        $result = record_portal_payment([
            'company_id' => $companyId,
            'invoice_id' => 'INV-FULL-001',
            'customer_name' => 'Test Customer',
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'provider_payment_id' => 'pi_test_full_' . bin2hex(random_bytes(4)),
            'status' => 'completed',
        ]);

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['reference_number']);

        $stmt = $this->pdo->prepare('SELECT status, balance_due FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$companyId, 'INV-FULL-001']);
        $invoice = $stmt->fetch();
        $this->assertSame('paid', $invoice['status']);
        $this->assertSame(0.00, (float) $invoice['balance_due']);
    }

    public function test_partial_payment_marks_invoice_partial(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-PART-001', 100.00);

        record_portal_payment([
            'company_id' => $companyId,
            'invoice_id' => 'INV-PART-001',
            'customer_name' => 'Test Customer',
            'amount' => 40.00,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'provider_payment_id' => 'pi_test_partial_' . bin2hex(random_bytes(4)),
            'status' => 'completed',
        ]);

        $stmt = $this->pdo->prepare('SELECT status, balance_due FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$companyId, 'INV-PART-001']);
        $invoice = $stmt->fetch();
        $this->assertSame('partial', $invoice['status']);
        $this->assertSame(60.00, (float) $invoice['balance_due']);
    }

    public function test_duplicate_provider_payment_id_returns_existing_reference_without_double_charging(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-DUP-001', 100.00);

        $providerPaymentId = 'pi_test_dup_' . bin2hex(random_bytes(4));

        $first = record_portal_payment([
            'company_id' => $companyId,
            'invoice_id' => 'INV-DUP-001',
            'customer_name' => 'Test Customer',
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'provider_payment_id' => $providerPaymentId,
            'status' => 'completed',
        ]);

        $second = record_portal_payment([
            'company_id' => $companyId,
            'invoice_id' => 'INV-DUP-001',
            'customer_name' => 'Test Customer',
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'provider_payment_id' => $providerPaymentId,
            'status' => 'completed',
        ]);

        $this->assertTrue($first['success']);
        $this->assertTrue($second['success']);
        $this->assertSame($first['reference_number'], $second['reference_number']);

        // Only one payment row should exist for the duplicate provider_payment_id
        $stmt = $this->pdo->prepare('SELECT COUNT(*) AS cnt FROM portal_payments WHERE provider_payment_id = ?');
        $stmt->execute([$providerPaymentId]);
        $this->assertSame(1, (int) $stmt->fetch()['cnt']);

        // Invoice should be paid exactly once (balance_due = 0, not -100)
        $stmt = $this->pdo->prepare('SELECT balance_due FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$companyId, 'INV-DUP-001']);
        $this->assertSame(0.00, (float) $stmt->fetch()['balance_due']);
    }
}
