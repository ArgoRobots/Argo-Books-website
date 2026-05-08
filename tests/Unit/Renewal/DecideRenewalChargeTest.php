<?php
declare(strict_types=1);

namespace Tests\Unit\Renewal;

use PHPUnit\Framework\TestCase;

final class DecideRenewalChargeTest extends TestCase
{
    private array $config;

    protected function setUp(): void
    {
        parent::setUp();
        // Mirrors .env.testing values so the test is self-contained
        // even if .env.testing later drifts. The processing-fee values are
        // also set in .env.testing so calculate_processing_fee() agrees.
        $this->config = [
            'premium_monthly_price' => 10.00,
            'premium_yearly_price'  => 100.00,
        ];
    }

    public function test_full_credit_coverage_charges_zero(): void
    {
        // Credit > monthly price: full coverage, no charge.
        $decision = decide_renewal_charge(15.00, 'monthly', $this->config);

        $this->assertTrue($decision['useCredit']);
        $this->assertSame(10.00, $decision['creditUsed']);
        $this->assertSame(0.00, $decision['amountToCharge']);
    }

    public function test_partial_credit_charges_difference_plus_fee(): void
    {
        // Credit $3 against monthly $10: charge $7 + fee.
        // Fee = 7 * 0.029 + 0.30 = 0.503 → 0.50
        $decision = decide_renewal_charge(3.00, 'monthly', $this->config);

        $this->assertFalse($decision['useCredit']);
        $this->assertSame(3.00, $decision['creditUsed']);
        $this->assertSame(7.50, $decision['amountToCharge']);
    }

    public function test_no_credit_charges_full_amount_plus_fee(): void
    {
        // Yearly $100, no credit: charge $100 + fee.
        // Fee = 100 * 0.029 + 0.30 = 3.20
        $decision = decide_renewal_charge(0.00, 'yearly', $this->config);

        $this->assertFalse($decision['useCredit']);
        $this->assertSame(0.0, $decision['creditUsed']);
        $this->assertSame(103.20, $decision['amountToCharge']);
    }

    public function test_exact_credit_match_counts_as_full_coverage(): void
    {
        // Credit equals price exactly: still treated as full coverage.
        $decision = decide_renewal_charge(10.00, 'monthly', $this->config);

        $this->assertTrue($decision['useCredit']);
        $this->assertSame(10.00, $decision['creditUsed']);
        $this->assertSame(0.00, $decision['amountToCharge']);
    }
}
