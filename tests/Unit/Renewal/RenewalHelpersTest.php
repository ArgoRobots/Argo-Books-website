<?php
declare(strict_types=1);

namespace Tests\Unit\Renewal;

use DateTime;
use PHPUnit\Framework\TestCase;

final class RenewalHelpersTest extends TestCase
{
    public function test_yearly_adds_one_year_from_future_end_date(): void
    {
        $futureEnd = (new DateTime('+30 days'))->format('Y-m-d H:i:s');
        $expected = (new DateTime('+30 days'))->modify('+1 year')->format('Y-m-d');
        $newEnd = calculateNewEndDate($futureEnd, 'yearly');
        $this->assertSame($expected, substr($newEnd, 0, 10));
    }

    public function test_monthly_adds_one_month_from_future_end_date(): void
    {
        $futureEnd = (new DateTime('+10 days'))->format('Y-m-d H:i:s');
        $expected = (new DateTime('+10 days'))->modify('+1 month')->format('Y-m-d');
        $newEnd = calculateNewEndDate($futureEnd, 'monthly');
        $this->assertSame($expected, substr($newEnd, 0, 10));
    }

    public function test_extends_from_now_when_end_date_is_in_past(): void
    {
        // End date 30 days ago. Without the "stale end_date" guard, the new
        // end date would be 30 days ago + 1 month ≈ now, potentially still in
        // the past — risking another renewal pickup on the next cron run.
        $pastEnd = (new DateTime('-30 days'))->format('Y-m-d H:i:s');
        $expectedMin = (new DateTime('+27 days'))->format('Y-m-d');
        $expectedMax = (new DateTime('+33 days'))->format('Y-m-d');

        $newEnd = calculateNewEndDate($pastEnd, 'monthly');
        $newEndDate = substr($newEnd, 0, 10);

        $this->assertGreaterThanOrEqual($expectedMin, $newEndDate);
        $this->assertLessThanOrEqual($expectedMax, $newEndDate);
    }

    public function test_yearly_with_past_end_date_extends_from_now(): void
    {
        $pastEnd = (new DateTime('-90 days'))->format('Y-m-d H:i:s');
        $expectedMin = (new DateTime('+360 days'))->format('Y-m-d');
        $expectedMax = (new DateTime('+370 days'))->format('Y-m-d');

        $newEnd = calculateNewEndDate($pastEnd, 'yearly');
        $newEndDate = substr($newEnd, 0, 10);

        $this->assertGreaterThanOrEqual($expectedMin, $newEndDate);
        $this->assertLessThanOrEqual($expectedMax, $newEndDate);
    }
}
