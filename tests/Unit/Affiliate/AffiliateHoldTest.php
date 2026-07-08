<?php
declare(strict_types=1);

namespace Tests\Unit\Affiliate;

use PHPUnit\Framework\TestCase;

require_once PROJECT_ROOT . '/community/affiliate/affiliate_functions.php';

/**
 * The hold window splits commission into eligible (payable now, past the hold)
 * and pending (still inside the hold window, could still be refunded).
 * compute_commission_split() is pure and takes a reference "now" so it's
 * deterministic.
 */
final class AffiliateHoldTest extends TestCase
{
    private const START = '2026-01-15 00:00:00';
    private const RATE = 0.5;
    private const WINDOW = 12;
    private const ENV = 'production';
    private const FEE_PCT = 2.9;
    private const FEE_FIXED = 0.30;
    private const HOLD = 30;
    private const NOW = '2026-03-01 00:00:00'; // cutoff = 2026-01-30 00:00:00
    private const CHARGED = 15.74;             // $15.00 base + $0.74 fee

    private function payment(string $created_at, string $status = 'completed', string $env = 'production', float $amount = self::CHARGED): array
    {
        return ['amount' => $amount, 'status' => $status, 'created_at' => $created_at, 'environment' => $env];
    }

    private function split(array $payments): array
    {
        return compute_commission_split($payments, self::START, self::RATE, self::WINDOW, self::ENV, self::FEE_PCT, self::FEE_FIXED, self::HOLD, self::NOW);
    }

    public function test_old_payment_is_eligible(): void
    {
        // Jan 20 is before the Jan 30 cutoff -> payable now.
        $r = $this->split([$this->payment('2026-01-20 09:00:00')]);
        $this->assertSame(7.50, $r['eligible']);
        $this->assertSame(0.00, $r['pending']);
    }

    public function test_recent_payment_is_pending(): void
    {
        // Feb 20 is inside the 30-day hold window -> pending.
        $r = $this->split([$this->payment('2026-02-20 09:00:00')]);
        $this->assertSame(0.00, $r['eligible']);
        $this->assertSame(7.50, $r['pending']);
    }

    public function test_boundary_at_cutoff_is_eligible(): void
    {
        // Exactly at the cutoff counts as seasoned (<=).
        $r = $this->split([$this->payment('2026-01-30 00:00:00')]);
        $this->assertSame(7.50, $r['eligible']);
        $this->assertSame(0.00, $r['pending']);
    }

    public function test_just_after_cutoff_is_pending(): void
    {
        $r = $this->split([$this->payment('2026-01-30 09:00:00')]);
        $this->assertSame(0.00, $r['eligible']);
        $this->assertSame(7.50, $r['pending']);
    }

    public function test_mixed_splits_correctly(): void
    {
        $r = $this->split([
            $this->payment('2026-01-20 09:00:00'),                         // eligible 7.50
            $this->payment('2026-02-20 09:00:00'),                         // pending  7.50
            $this->payment('2027-03-01 09:00:00'),                         // outside 12mo window
            $this->payment('2026-02-01 09:00:00', 'refunded'),             // refunded, ignored
            $this->payment('2026-02-01 09:00:00', 'completed', 'sandbox'), // wrong env, ignored
        ]);
        $this->assertSame(7.50, $r['eligible']);
        $this->assertSame(7.50, $r['pending']);
    }

    public function test_refunded_counts_in_neither(): void
    {
        $r = $this->split([$this->payment('2026-01-20 09:00:00', 'refunded')]);
        $this->assertSame(0.00, $r['eligible']);
        $this->assertSame(0.00, $r['pending']);
    }
}
