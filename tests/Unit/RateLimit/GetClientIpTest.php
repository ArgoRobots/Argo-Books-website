<?php
declare(strict_types=1);

namespace Tests\Unit\RateLimit;

use PHPUnit\Framework\TestCase;

final class GetClientIpTest extends TestCase
{
    private array $serverBackup;
    private ?string $envBackup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = [
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
            'HTTP_X_FORWARDED_FOR' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
        ];
        $this->envBackup = $_ENV['TRUSTED_PROXY_IPS'] ?? null;
        unset($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_X_FORWARDED_FOR'], $_ENV['TRUSTED_PROXY_IPS']);
    }

    protected function tearDown(): void
    {
        foreach ($this->serverBackup as $key => $value) {
            if ($value === null) {
                unset($_SERVER[$key]);
            } else {
                $_SERVER[$key] = $value;
            }
        }
        if ($this->envBackup === null) {
            unset($_ENV['TRUSTED_PROXY_IPS']);
        } else {
            $_ENV['TRUSTED_PROXY_IPS'] = $this->envBackup;
        }
        parent::tearDown();
    }

    public function test_returns_remote_addr_when_no_forwarded_for(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.42';
        $this->assertSame('203.0.113.42', get_client_ip());
    }

    public function test_returns_remote_addr_when_forwarded_for_present_but_proxy_not_trusted(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.42';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.7';
        // No TRUSTED_PROXY_IPS set, so the XFF header must be ignored.
        $this->assertSame('203.0.113.42', get_client_ip());
    }

    public function test_uses_first_forwarded_for_ip_when_proxy_is_trusted(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.7, 10.0.0.5';
        $_ENV['TRUSTED_PROXY_IPS'] = '10.0.0.1';
        $this->assertSame('198.51.100.7', get_client_ip());
    }

    public function test_falls_back_to_zeros_when_remote_addr_missing(): void
    {
        // No REMOTE_ADDR set at all
        $this->assertSame('0.0.0.0', get_client_ip());
    }
}
