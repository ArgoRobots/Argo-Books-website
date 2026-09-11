<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

/**
 * payment_intent.succeeded can be recorded before the customer's browser
 * confirms the payment. The confirmation must then be treated as the same
 * payment, not validated against a balance the webhook already cleared.
 */
final class StripePaymentRecordedByWebhookTest extends DatabaseTestCase
{
    private function recordAsWebhook(int $companyId, string $invoiceId, string $paymentIntentId, float $amount, float $fee = 0.00): array
    {
        return record_portal_payment([
            'company_id' => $companyId,
            'invoice_id' => $invoiceId,
            'customer_name' => 'Test Customer',
            'amount' => $amount,
            'processing_fee' => $fee,
            'currency' => 'USD',
            'payment_method' => 'stripe',
            'provider_payment_id' => $paymentIntentId,
            'provider_transaction_id' => 'ch_' . $paymentIntentId,
            'status' => 'completed',
            'payment_environment' => 'sandbox',
        ]);
    }

    public function test_finds_the_payment_already_recorded_for_this_invoice_only(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-WH-1', 100.00);
        $paymentIntentId = 'pi_wh_' . bin2hex(random_bytes(4));
        $recorded = $this->recordAsWebhook($companyId, 'INV-WH-1', $paymentIntentId, 100.00);

        $found = find_recorded_portal_payment($paymentIntentId, $companyId, 'INV-WH-1');
        $this->assertNotNull($found);
        $this->assertSame($recorded['reference_number'], $found['reference_number']);

        $this->assertNull(find_recorded_portal_payment($paymentIntentId, $companyId, 'INV-WH-2'));
        $this->assertNull(find_recorded_portal_payment('pi_never_seen', $companyId, 'INV-WH-1'));
    }

    public function test_confirming_after_the_webhook_fills_in_the_fee_without_paying_the_invoice_twice(): void
    {
        $companyId = $this->seedPortalCompany();
        $this->seedPortalInvoice($companyId, 'INV-WH-3', 100.00);
        $paymentIntentId = 'pi_wh_' . bin2hex(random_bytes(4));
        $first = $this->recordAsWebhook($companyId, 'INV-WH-3', $paymentIntentId, 103.20);

        $fee = stripe_metadata_processing_fee(['processing_fee' => '3.20'], 103.20);
        $second = $this->recordAsWebhook($companyId, 'INV-WH-3', $paymentIntentId, 103.20, $fee);

        $this->assertFalse($second['inserted']);
        $this->assertSame($first['reference_number'], $second['reference_number']);

        $stmt = $this->pdo->prepare('SELECT processing_fee FROM portal_payments WHERE provider_payment_id = ?');
        $stmt->execute([$paymentIntentId]);
        $this->assertSame(3.20, (float) $stmt->fetch()['processing_fee']);

        $stmt = $this->pdo->prepare('SELECT balance_due, status FROM portal_invoices WHERE company_id = ? AND invoice_id = ?');
        $stmt->execute([$companyId, 'INV-WH-3']);
        $invoice = $stmt->fetch();
        $this->assertSame(0.00, (float) $invoice['balance_due']);
        $this->assertSame('paid', $invoice['status']);
    }

    public function test_metadata_fee_is_ignored_unless_it_is_a_plausible_part_of_the_payment(): void
    {
        $this->assertSame(3.20, stripe_metadata_processing_fee(['processing_fee' => '3.20'], 103.20));
        $this->assertSame(0.00, stripe_metadata_processing_fee([], 103.20));
        $this->assertSame(0.00, stripe_metadata_processing_fee(['processing_fee' => 'abc'], 103.20));
        $this->assertSame(0.00, stripe_metadata_processing_fee(['processing_fee' => '-1.00'], 103.20));
        $this->assertSame(0.00, stripe_metadata_processing_fee(['processing_fee' => '103.20'], 103.20));
    }
}
