<?php
declare(strict_types=1);

namespace Tests\Unit\License;

use PHPUnit\Framework\TestCase;

final class GenerateLicenseKeyTest extends TestCase
{
    public function test_format_matches_PREM_dashed_groups(): void
    {
        $key = generate_license_key();
        $this->assertMatchesRegularExpression(
            '/^PREM-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/',
            $key
        );
    }

    public function test_total_length_is_24_characters(): void
    {
        $this->assertSame(24, strlen(generate_license_key()));
    }

    public function test_uniqueness_across_1000_invocations(): void
    {
        $keys = [];
        for ($i = 0; $i < 1000; $i++) {
            $keys[] = generate_license_key();
        }
        $this->assertCount(1000, array_unique($keys), 'All 1000 generated keys should be unique');
    }
}
