<?php
declare(strict_types=1);

namespace Tests\Unit\Portal;

use PHPUnit\Framework\TestCase;

final class GenerateReferenceNumberTest extends TestCase
{
    public function test_format_PAY_yyyymmdd_six_hex(): void
    {
        $ref = generate_reference_number();
        $this->assertMatchesRegularExpression('/^PAY-\d{8}-[0-9A-F]{6}$/', $ref);
    }

    public function test_date_segment_is_today(): void
    {
        // Capture the date before AND after generation. If the day rolls
        // over between calls (microsecond-window flake), accept either.
        $before = date('Ymd');
        $ref = generate_reference_number();
        $after = date('Ymd');

        $this->assertContains(substr($ref, 4, 8), [$before, $after]);
    }
}
