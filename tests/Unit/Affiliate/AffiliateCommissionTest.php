<?php
declare(strict_types=1);

namespace Tests\Unit\Affiliate;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/community/affiliate/affiliate_functions.php';

/**
 * Pure commission math: 50% of the subscription price (processing fee stripped)
 * on completed payments within each subscription's 12-month window, env-scoped.
 * compute_commission() takes plain arrays so it needs no database.
 *
 * Amounts here are the fee-inclusive figures actually charged: a $15.00
 * subscription plus a 2.9% + $0.30 processing fee = $15.74. Commission is 50%
 * of the $15.00 base = $7.50.
 */
final class AffiliateCommissionTest extends TestCase
{
    private const START = '2026-01-15 00:00:00';
    private const RATE = 0.5;
    private const WINDOW = 12;
    private const ENV = 'production';
    private const FEE_PCT = 2.9;
    private const FEE_FIXED = 0.30;
    private const CHARGED = 15.74; // $15.00 base + $0.74 fee
    private const BASE_COMMISSION = 7.50; // 50% of the $15.00 base

    private function payment(string $created_at, string $status = 'completed', string $env = 'production', float $amount = self::CHARGED): array
    {
        return ['amount' => $amount, 'status' => $status, 'created_at' => $created_at, 'environment' => $env];
    }

    private function earned(array $payments): float
    {
        return compute_commission($payments, self::START, self::RATE, self::WINDOW, self::ENV, self::FEE_PCT, self::FEE_FIXED);
    }

    public function test_initial_payment_counts_on_base_price(): void
    {
        $this->assertSame(self::BASE_COMMISSION, $this->earned([$this->payment('2026-01-20 09:00:00')]));
    }

    public function test_processing_fee_is_stripped(): void
    {
        // Without stripping this would be 0.5 * 15.74 = 7.87; on the base it's 7.50.
        $this->assertSame(7.50, $this->earned([$this->payment('2026-01-20 09:00:00')]));
    }

    public function test_renewal_inside_window_counts(): void
    {
        // Month 11, still inside the 12-month window.
        $this->assertSame(self::BASE_COMMISSION, $this->earned([$this->payment('2026-12-20 09:00:00')]));
    }

    public function test_payment_after_window_excluded(): void
    {
        // Month 13, past the window.
        $this->assertSame(0.00, $this->earned([$this->payment('2027-03-01 09:00:00')]));
    }

    public function test_window_boundary_is_exclusive(): void
    {
        // Exactly start + 12 months must NOT count (< upper bound).
        $this->assertSame(0.00, $this->earned([$this->payment('2027-01-15 00:00:00')]));
    }

    public function test_refunded_payment_excluded(): void
    {
        $this->assertSame(0.00, $this->earned([$this->payment('2026-02-01 09:00:00', 'refunded')]));
    }

    public function test_failed_payment_excluded(): void
    {
        $this->assertSame(0.00, $this->earned([$this->payment('2026-02-01 09:00:00', 'failed')]));
    }

    public function test_wrong_environment_excluded(): void
    {
        $this->assertSame(0.00, $this->earned([$this->payment('2026-02-01 09:00:00', 'completed', 'sandbox')]));
    }

    public function test_zero_dollar_credit_charge_earns_nothing(): void
    {
        // A fully credit-covered $0.00 charge must not go negative from the fee math.
        $this->assertSame(0.00, $this->earned([$this->payment('2026-02-01 09:00:00', 'completed', 'production', 0.00)]));
    }

    public function test_mixed_set_sums_only_qualifying_payments(): void
    {
        $payments = [
            $this->payment('2026-01-20 09:00:00'),                         // counts  (base 15)
            $this->payment('2026-12-20 09:00:00'),                         // counts  (base 15)
            $this->payment('2027-03-01 09:00:00'),                         // too late
            $this->payment('2026-02-01 09:00:00', 'refunded'),             // refunded
            $this->payment('2026-02-01 09:00:00', 'completed', 'sandbox'), // wrong env
        ];
        // (15 + 15) * 0.5 = 15.00
        $this->assertSame(15.00, $this->earned($payments));
    }

    public function test_empty_payments_is_zero(): void
    {
        $this->assertSame(0.00, $this->earned([]));
    }
}
