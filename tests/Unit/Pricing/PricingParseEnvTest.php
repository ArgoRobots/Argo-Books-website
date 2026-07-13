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

    public function test_web_receipt_scan_limits_present_in_config(): void
    {
        $cfg = get_pricing_config();
        $this->assertArrayHasKey('web_receipt_scan_daily_limit', $cfg);
        $this->assertArrayHasKey('web_receipt_scan_global_daily_cap', $cfg);
        $this->assertIsInt($cfg['web_receipt_scan_daily_limit']);
        $this->assertIsInt($cfg['web_receipt_scan_global_daily_cap']);
        $this->assertGreaterThanOrEqual(1, $cfg['web_receipt_scan_daily_limit']);
        $this->assertGreaterThanOrEqual(1, $cfg['web_receipt_scan_global_daily_cap']);
    }

    public function test_web_receipt_scan_limits_parse_from_env(): void
    {
        $_ENV['WEB_RECEIPT_SCAN_DAILY_LIMIT'] = '7';
        $_ENV['WEB_RECEIPT_SCAN_GLOBAL_DAILY_CAP'] = '1200';
        $this->assertSame(7, _pricing_parse_int_env('WEB_RECEIPT_SCAN_DAILY_LIMIT', 3));
        $this->assertSame(1200, _pricing_parse_int_env('WEB_RECEIPT_SCAN_GLOBAL_DAILY_CAP', 500));
        unset($_ENV['WEB_RECEIPT_SCAN_DAILY_LIMIT'], $_ENV['WEB_RECEIPT_SCAN_GLOBAL_DAILY_CAP']);
    }
}
