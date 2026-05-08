<?php
declare(strict_types=1);

namespace Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;

/**
 * authenticate_device_request() doesn't touch the database — it just hashes
 * the X-Device-Id header — so this lives in Unit despite belonging to the
 * auth family logically.
 */
final class AuthenticateDeviceRequestTest extends TestCase
{
    private ?string $serverBackup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER['HTTP_X_DEVICE_ID'] ?? null;
        unset($_SERVER['HTTP_X_DEVICE_ID']);
    }

    protected function tearDown(): void
    {
        if ($this->serverBackup === null) {
            unset($_SERVER['HTTP_X_DEVICE_ID']);
        } else {
            $_SERVER['HTTP_X_DEVICE_ID'] = $this->serverBackup;
        }
        parent::tearDown();
    }

    public function test_returns_null_when_no_header(): void
    {
        $this->assertNull(authenticate_device_request());
    }

    public function test_returns_sha256_hash_when_header_present(): void
    {
        $_SERVER['HTTP_X_DEVICE_ID'] = 'raw-machine-id-abc-123';
        $this->assertSame(
            hash('sha256', 'raw-machine-id-abc-123'),
            authenticate_device_request()
        );
    }

    public function test_same_input_produces_same_hash(): void
    {
        $_SERVER['HTTP_X_DEVICE_ID'] = 'consistent-device-id';
        $first = authenticate_device_request();
        $second = authenticate_device_request();
        $this->assertSame($first, $second);
    }

    public function test_different_inputs_produce_different_hashes(): void
    {
        $_SERVER['HTTP_X_DEVICE_ID'] = 'device-A';
        $a = authenticate_device_request();

        $_SERVER['HTTP_X_DEVICE_ID'] = 'device-B';
        $b = authenticate_device_request();

        $this->assertNotSame($a, $b);
    }
}
