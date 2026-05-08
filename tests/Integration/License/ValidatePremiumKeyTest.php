<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\DatabaseTestCase;

final class ValidatePremiumKeyTest extends DatabaseTestCase
{
    public function test_returns_invalid_for_unknown_key(): void
    {
        $result = validate_premium_key('PREM-DOES-NOTE-XIST-AAAA');
        $this->assertFalse($result['success']);
        $this->assertSame('Invalid premium key.', $result['message']);
    }

    public function test_returns_valid_status_for_unredeemed_key(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 12);
        $result = validate_premium_key($key);

        $this->assertTrue($result['success']);
        $this->assertSame('valid', $result['status']);
        $this->assertSame($key, $result['key']);
    }

    public function test_returns_redeemed_status_for_already_redeemed_key(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 12);
        $this->pdo->prepare(
            "UPDATE premium_subscription_keys
             SET redeemed_at = NOW(), device_id = ?, subscription_id = ?
             WHERE subscription_key = ?"
        )->execute(['device-hash-abc', 'PREM-FAKE-SUB1-AAAA-BBBB', $key]);

        $result = validate_premium_key($key);

        $this->assertTrue($result['success']);
        $this->assertSame('redeemed', $result['status']);
        $this->assertSame('PREM-FAKE-SUB1-AAAA-BBBB', $result['subscription_id']);
    }

    public function test_response_includes_duration_months(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 6);
        $result = validate_premium_key($key);
        $this->assertSame(6, (int) $result['duration_months']);
    }
}
