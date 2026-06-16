<?php
declare(strict_types=1);

namespace Tests\Unit\Affiliate;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/community/affiliate/affiliate_functions.php';

/**
 * Pure commission math: 50% of completed payments within each subscription's
 * 12-month window, env-scoped. compute_commission() takes plain arrays so it
 * needs no database.
 */
final class AffiliateCommissionTest extends TestCase
{
    private const START = '2026-01-15 00:00:00';
    private const RATE = 0.5;
    private const WINDOW = 12;
    private const ENV = 'production';

    private function payment(string $created_at, string $status = 'completed', string $env = 'production', float $amount = 10.00): array
    {
        return ['amount' => $amount, 'status' => $status, 'created_at' => $created_at, 'environment' => $env];
    }

    public function test_initial_payment_counts(): void
    {
        $earned = compute_commission([$this->payment('2026-01-20 09:00:00')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(5.00, $earned);
    }

    public function test_renewal_inside_window_counts(): void
    {
        // Month 11, still inside the 12-month window.
        $earned = compute_commission([$this->payment('2026-12-20 09:00:00')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(5.00, $earned);
    }

    public function test_payment_after_window_excluded(): void
    {
        // Month 13, past the window.
        $earned = compute_commission([$this->payment('2027-03-01 09:00:00')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(0.00, $earned);
    }

    public function test_window_boundary_is_exclusive(): void
    {
        // Exactly start + 12 months must NOT count (< upper bound).
        $earned = compute_commission([$this->payment('2027-01-15 00:00:00')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(0.00, $earned);
    }

    public function test_refunded_payment_excluded(): void
    {
        $earned = compute_commission([$this->payment('2026-02-01 09:00:00', 'refunded')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(0.00, $earned);
    }

    public function test_failed_payment_excluded(): void
    {
        $earned = compute_commission([$this->payment('2026-02-01 09:00:00', 'failed')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(0.00, $earned);
    }

    public function test_wrong_environment_excluded(): void
    {
        $earned = compute_commission([$this->payment('2026-02-01 09:00:00', 'completed', 'sandbox')], self::START, self::RATE, self::WINDOW, self::ENV);
        $this->assertSame(0.00, $earned);
    }

    public function test_mixed_set_sums_only_qualifying_payments(): void
    {
        $payments = [
            $this->payment('2026-01-20 09:00:00'),                       // counts  (10)
            $this->payment('2026-12-20 09:00:00'),                       // counts  (10)
            $this->payment('2027-03-01 09:00:00'),                       // too late
            $this->payment('2026-02-01 09:00:00', 'refunded'),           // refunded
            $this->payment('2026-02-01 09:00:00', 'completed', 'sandbox'), // wrong env
        ];
        // (10 + 10) * 0.5 = 10.00
        $this->assertSame(10.00, compute_commission($payments, self::START, self::RATE, self::WINDOW, self::ENV));
    }

    public function test_empty_payments_is_zero(): void
    {
        $this->assertSame(0.00, compute_commission([], self::START, self::RATE, self::WINDOW, self::ENV));
    }
}
