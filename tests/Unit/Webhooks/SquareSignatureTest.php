<?php
declare(strict_types=1);

namespace Tests\Unit\Webhooks;

use PHPUnit\Framework\TestCase;

final class SquareSignatureTest extends TestCase
{
    private const URL = 'https://example.com/api/portal/webhooks/square';
    private const KEY = 'test_secret_key_xyz';
    private const PAYLOAD = '{"type":"payment.completed","data":{"id":"abc123"}}';

    private function expectedSignature(string $url, string $payload, string $key): string
    {
        return base64_encode(hash_hmac('sha256', $url . $payload, $key, true));
    }

    public function test_returns_true_for_valid_signature(): void
    {
        $sig = $this->expectedSignature(self::URL, self::PAYLOAD, self::KEY);
        $this->assertTrue(verify_square_webhook_signature(self::URL, self::PAYLOAD, self::KEY, $sig));
    }

    public function test_returns_false_for_tampered_payload(): void
    {
        $sig = $this->expectedSignature(self::URL, self::PAYLOAD, self::KEY);
        $tamperedPayload = '{"type":"payment.completed","data":{"id":"WRONG"}}';
        $this->assertFalse(verify_square_webhook_signature(self::URL, $tamperedPayload, self::KEY, $sig));
    }

    public function test_returns_false_for_empty_signature(): void
    {
        $this->assertFalse(verify_square_webhook_signature(self::URL, self::PAYLOAD, self::KEY, ''));
    }

    public function test_returns_false_for_empty_key(): void
    {
        $sig = $this->expectedSignature(self::URL, self::PAYLOAD, self::KEY);
        $this->assertFalse(verify_square_webhook_signature(self::URL, self::PAYLOAD, '', $sig));
    }
}
