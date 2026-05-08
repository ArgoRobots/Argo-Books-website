<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;

final class CalculateProcessingFeeTest extends TestCase
{
    public function test_returns_zero_for_zero_subtotal(): void
    {
        $this->assertSame(0.00, calculate_processing_fee(0));
    }

    public function test_returns_zero_for_negative_subtotal(): void
    {
        $this->assertSame(0.00, calculate_processing_fee(-50));
    }

    public function test_calculates_290_percent_plus_fixed_for_positive_subtotal(): void
    {
        // .env.testing sets PROCESSING_FEE_PERCENT=2.90, PROCESSING_FEE_FIXED=0.30
        // 100 * 0.029 + 0.30 = 3.20
        $this->assertSame(3.20, calculate_processing_fee(100));
    }

    public function test_rounds_to_two_decimals(): void
    {
        // 33.33 * 0.029 + 0.30 = 1.26657 → rounds to 1.27
        $this->assertSame(1.27, calculate_processing_fee(33.33));
    }

    public function test_handles_large_subtotal(): void
    {
        // 1000 * 0.029 + 0.30 = 29.30
        $this->assertSame(29.30, calculate_processing_fee(1000));
    }
}
