<?php
declare(strict_types=1);

namespace Tests\Unit\Pricing;

use PHPUnit\Framework\TestCase;

final class PricingParseEnvTest extends TestCase
{
    private const TEST_KEY = '__TEST_PRICING_PARSE__';

    protected function tearDown(): void
    {
        unset($_ENV[self::TEST_KEY]);
        parent::tearDown();
    }

    public function test_parse_env_returns_default_when_unset(): void
    {
        unset($_ENV[self::TEST_KEY]);
        $this->assertSame(7.50, _pricing_parse_env(self::TEST_KEY, 7.50));
    }

    public function test_parse_env_returns_default_for_non_numeric(): void
    {
        $_ENV[self::TEST_KEY] = 'abc';
        $this->assertSame(7.50, _pricing_parse_env(self::TEST_KEY, 7.50));
    }

    public function test_parse_env_returns_default_for_negative(): void
    {
        $_ENV[self::TEST_KEY] = '-5.00';
        $this->assertSame(7.50, _pricing_parse_env(self::TEST_KEY, 7.50));
    }

    public function test_parse_env_rounds_to_two_decimals(): void
    {
        $_ENV[self::TEST_KEY] = '12.345';
        $this->assertSame(12.35, _pricing_parse_env(self::TEST_KEY, 0));
    }

    public function test_parse_int_env_returns_default_for_zero(): void
    {
        $_ENV[self::TEST_KEY] = '0';
        $this->assertSame(50, _pricing_parse_int_env(self::TEST_KEY, 50));
    }

    public function test_parse_int_env_floors_floats(): void
    {
        $_ENV[self::TEST_KEY] = '3.7';
        $this->assertSame(3, _pricing_parse_int_env(self::TEST_KEY, 0));
    }
}
