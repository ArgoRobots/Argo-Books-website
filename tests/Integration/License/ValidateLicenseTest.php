<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\DatabaseTestCase;

final class ValidateLicenseTest extends DatabaseTestCase
{
    private const DEVICE = 'device-hash-test-001';

    public function test_returns_invalid_key_for_unredeemed_key(): void
    {
        $key = $this->seedPremiumKey();
        $result = validate_license($key, self::DEVICE);

        $this->assertFalse($result['success']);
        $this->assertSame('invalid_key', $result['status']);
    }

    public function test_returns_wrong_device_when_device_id_mismatches(): void
    {
        $subId = 'PREM-TEST-SUB1-AAAA-BBBB';
        $this->seedSubscription($subId, (new \DateTime('+30 days'))->format('Y-m-d H:i:s'));
        $key = $this->seedRedeemedKey(self::DEVICE, $subId);

        $result = validate_license($key, 'different-device-hash');

        $this->assertFalse($result['success']);
        $this->assertSame('wrong_device', $result['status']);
    }

    public function test_returns_expired_when_subscription_end_date_in_past(): void
    {
        $subId = 'PREM-TEST-SUB2-CCCC-DDDD';
        $pastDate = (new \DateTime('-5 days'))->format('Y-m-d H:i:s');
        $this->seedSubscription($subId, $pastDate, status: 'active');
        $key = $this->seedRedeemedKey(self::DEVICE, $subId);

        $result = validate_license($key, self::DEVICE);

        $this->assertFalse($result['success']);
        $this->assertSame('expired', $result['status']);

        // Verify the side effect: subscription row flipped to 'expired'.
        $stmt = $this->pdo->prepare('SELECT status FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$subId]);
        $this->assertSame('expired', $stmt->fetch()['status']);
    }

    public function test_returns_valid_when_redeemed_active_matching_device(): void
    {
        $subId = 'PREM-TEST-SUB3-EEEE-FFFF';
        $this->seedSubscription($subId, (new \DateTime('+90 days'))->format('Y-m-d H:i:s'));
        $key = $this->seedRedeemedKey(self::DEVICE, $subId);

        $result = validate_license($key, self::DEVICE);

        $this->assertTrue($result['success']);
        $this->assertSame('valid', $result['status']);
    }

    public function test_returns_invalid_when_subscription_row_missing(): void
    {
        // Redeemed key pointing at a non-existent subscription_id
        $key = $this->seedRedeemedKey(self::DEVICE, 'PREM-NOSUCH-SUBS-RIPT-IONX');

        $result = validate_license($key, self::DEVICE);

        $this->assertFalse($result['success']);
        $this->assertSame('invalid_key', $result['status']);
    }

    public function test_valid_when_device_in_set_even_if_not_primary(): void
    {
        $subId = 'PREM-TEST-MULT-DEV1-AAAA';
        $this->seedSubscription($subId, (new \DateTime('+90 days'))->format('Y-m-d H:i:s'));
        // Primary device on the key is 'device-primary', but 'device-second'
        // is also a registered device in the new table.
        $key = $this->seedRedeemedKey('device-primary', $subId);
        $this->pdo->prepare(
            'INSERT INTO premium_subscription_devices (subscription_id, device_id) VALUES (?, ?), (?, ?)'
        )->execute([$subId, 'device-primary', $subId, 'device-second']);

        $result = validate_license($key, 'device-second');
        $this->assertTrue($result['success']);
        $this->assertSame('valid', $result['status']);
    }

    public function test_wrong_device_when_not_in_set(): void
    {
        $subId = 'PREM-TEST-MULT-DEV2-BBBB';
        $this->seedSubscription($subId, (new \DateTime('+90 days'))->format('Y-m-d H:i:s'));
        $key = $this->seedRedeemedKey('device-primary', $subId);
        $this->pdo->prepare(
            'INSERT INTO premium_subscription_devices (subscription_id, device_id) VALUES (?, ?)'
        )->execute([$subId, 'device-primary']);

        $result = validate_license($key, 'device-unknown');
        $this->assertFalse($result['success']);
        $this->assertSame('wrong_device', $result['status']);
    }
}
