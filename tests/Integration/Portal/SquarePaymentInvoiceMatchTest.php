<?php
declare(strict_types=1);

namespace Tests\Integration\Portal;

use Tests\Helpers\DatabaseTestCase;

/**
 * Invoice numbers are only unique per company, so a Square payment must be
 * matched to the merchant that took it, never to whoever owns the number.
 */
final class SquarePaymentInvoiceMatchTest extends DatabaseTestCase
{
    private function companyOnSquare(string $merchantId, string $environment = 'sandbox'): int
    {
        $id = $this->seedPortalCompany();
        $this->pdo->prepare('UPDATE portal_companies SET square_merchant_id = ?, environment = ? WHERE id = ?')
            ->execute([$merchantId, $environment, $id]);
        return $id;
    }

    private function completedPayment(string $merchantId, array $payment): array
    {
        return [
            'merchant_id' => $merchantId,
            'type' => 'payment.updated',
            'data' => ['object' => ['payment' => $payment + [
                'id' => 'sq_pay_' . bin2hex(random_bytes(4)),
                'status' => 'COMPLETED',
                'amount_money' => ['amount' => 5000, 'currency' => 'USD'],
            ]]],
        ];
    }

    public function test_credits_the_invoice_of_the_merchant_that_took_the_payment(): void
    {
        $otherCompany = $this->companyOnSquare('MERCHANT_OTHER');
        $this->seedPortalInvoice($otherCompany, 'INV-100', 50.00);
        $payingCompany = $this->companyOnSquare('MERCHANT_MINE');
        $this->seedPortalInvoice($payingCompany, 'INV-100', 50.00);

        $match = square_find_portal_invoice(
            $this->pdo,
            $this->completedPayment('MERCHANT_MINE', ['reference_id' => 'INV-100']),
            'sandbox'
        );

        $this->assertNotNull($match);
        $this->assertSame($payingCompany, (int) $match['company_id']);
    }

    public function test_ignores_payments_taken_by_a_merchant_not_connected_to_the_portal(): void
    {
        $company = $this->companyOnSquare('MERCHANT_MINE');
        $this->seedPortalInvoice($company, 'INV-200', 50.00);

        $match = square_find_portal_invoice(
            $this->pdo,
            $this->completedPayment('MERCHANT_STRANGER', ['reference_id' => 'INV-200']),
            'sandbox'
        );

        $this->assertNull($match);
    }

    public function test_ignores_a_payment_that_names_the_invoice_only_in_its_note(): void
    {
        // A point-of-sale payment where the seller typed an invoice number
        // into the note. The portal's own checkout always sets reference_id.
        $company = $this->companyOnSquare('MERCHANT_MINE');
        $this->seedPortalInvoice($company, 'INV-300', 50.00);

        $match = square_find_portal_invoice(
            $this->pdo,
            $this->completedPayment('MERCHANT_MINE', ['note' => 'INV-300']),
            'sandbox'
        );

        $this->assertNull($match);
    }

    public function test_ignores_a_company_connected_in_the_other_environment(): void
    {
        $company = $this->companyOnSquare('MERCHANT_MINE', 'production');
        $this->seedPortalInvoice($company, 'INV-400', 50.00);

        $match = square_find_portal_invoice(
            $this->pdo,
            $this->completedPayment('MERCHANT_MINE', ['reference_id' => 'INV-400']),
            'sandbox'
        );

        $this->assertNull($match);
    }
}
