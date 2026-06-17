<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\IntegrationTestCase;

/**
 * redeem_premium_key() and _recreate_subscription_for_key() each call
 * $pdo->beginTransaction() internally, so these tests can't use the
 * transaction-rolled DatabaseTestCase. They use IntegrationTestCase, which
 * tracks fixture rows and deletes them in tearDown().
 */
final class RedeemPremiumKeyTest extends IntegrationTestCase
{
    private const DEVICE = 'device-hash-redeem-001';

    public function test_first_redemption_creates_subscription_and_marks_key_redeemed(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 6);

        $result = redeem_premium_key($key, self::DEVICE);

        $this->assertTrue($result['success']);
        $this->assertSame('active', $result['status']);
        $this->assertNotEmpty($result['subscription_id']);

        // Track for cleanup
        $this->trackSubscription($result['subscription_id']);

        // Verify subscription row was inserted
        $stmt = $this->pdo->prepare('SELECT * FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$result['subscription_id']]);
        $sub = $stmt->fetch();
        $this->assertNotFalse($sub);
        $this->assertSame('active', $sub['status']);
        $this->assertSame('monthly', $sub['billing_cycle']);

        // Verify key marked redeemed
        $stmt = $this->pdo->prepare('SELECT redeemed_at, device_id FROM premium_subscription_keys WHERE subscription_key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $this->assertNotNull($row['redeemed_at']);
        $this->assertSame(self::DEVICE, $row['device_id']);
    }

    public function test_first_redemption_uses_yearly_billing_for_12_month_duration(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 12);
        $result = redeem_premium_key($key, self::DEVICE);
        $this->trackSubscription($result['subscription_id']);

        $stmt = $this->pdo->prepare('SELECT billing_cycle FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$result['subscription_id']]);
        $this->assertSame('yearly', $stmt->fetch()['billing_cycle']);
    }

    public function test_first_redemption_with_zero_duration_creates_lifetime_subscription(): void
    {
        $key = $this->seedPremiumKey(durationMonths: 0);
        $result = redeem_premium_key($key, self::DEVICE);
        $this->trackSubscription($result['subscription_id']);

        $stmt = $this->pdo->prepare('SELECT end_date FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$result['subscription_id']]);
        $endDate = $stmt->fetch()['end_date'];

        // 100 years out (give or take). Assert at least 99 years from now
        $minExpected = (new \DateTime('+99 years'))->format('Y-m-d');
        $this->assertGreaterThanOrEqual($minExpected, substr($endDate, 0, 10));
    }

    public function test_re_redemption_active_subscription_transfers_device_id(): void
    {
        $subId = 'PREM-RERED-EMPT-AAAA-BBBB';
        $this->seedSubscription($subId, (new \DateTime('+30 days'))->format('Y-m-d H:i:s'));
        $key = $this->seedRedeemedKey('original-device', $subId);

        $result = redeem_premium_key($key, 'new-device-after-transfer');

        $this->assertTrue($result['success']);
        $this->assertSame('active', $result['status']);
        $this->assertSame($subId, $result['subscription_id']);

        // Verify device_id transferred on the key
        $stmt = $this->pdo->prepare('SELECT device_id FROM premium_subscription_keys WHERE subscription_key = ?');
        $stmt->execute([$key]);
        $this->assertSame('new-device-after-transfer', $stmt->fetch()['device_id']);
    }

    public function test_re_redemption_expired_subscription_returns_expired(): void
    {
        $subId = 'PREM-EXPIR-EDDD-AAAA-CCCC';
        $this->seedSubscription($subId, (new \DateTime('-5 days'))->format('Y-m-d H:i:s'), status: 'active');
        $key = $this->seedRedeemedKey('original-device', $subId);

        $result = redeem_premium_key($key, 'new-device');

        $this->assertFalse($result['success']);
        $this->assertSame('expired', $result['status']);

        // Side effect: subscription marked expired in DB
        $stmt = $this->pdo->prepare('SELECT status FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$subId]);
        $this->assertSame('expired', $stmt->fetch()['status']);
    }

    public function test_re_redemption_missing_subscription_recreates_it(): void
    {
        // Key marked redeemed but pointing at a subscription_id that doesn't exist
        $key = $this->seedRedeemedKey('original-device', 'PREM-NOSUCH-SUBS-RIPT-IONA', durationMonths: 6);

        $result = redeem_premium_key($key, 'new-device');

        $this->assertTrue($result['success']);
        $this->assertSame('active', $result['status']);
        $this->assertNotSame('PREM-NOSUCH-SUBS-RIPT-IONA', $result['subscription_id']);

        $this->trackSubscription($result['subscription_id']);

        // New subscription row exists
        $stmt = $this->pdo->prepare('SELECT status FROM premium_subscriptions WHERE subscription_id = ?');
        $stmt->execute([$result['subscription_id']]);
        $this->assertSame('active', $stmt->fetch()['status']);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM premium_subscription_devices WHERE subscription_id LIKE 'PREM-%'");
        parent::tearDown();
    }

    public function test_first_redemption_registers_the_device(): void
    {
        $key = $this->seedPremiumKey(12);
        $result = redeem_premium_key($key, 'dev-1');

        $this->assertTrue($result['success']);
        $this->assertSame('active', $result['status']);
        $this->trackSubscription($result['subscription_id']);

        $this->assertTrue(is_device_registered($result['subscription_id'], 'dev-1'));
        $this->assertSame(1, count_subscription_devices($result['subscription_id']));
    }

    public function test_additional_device_registers_when_under_limit(): void
    {
        if (get_max_devices() < 2) {
            $this->markTestSkipped('Device limit < 2; multi-device add not testable.');
        }
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);

        $second = redeem_premium_key($key, 'dev-2');
        $this->assertTrue($second['success']);
        $this->assertSame(2, count_subscription_devices($first['subscription_id']));
    }

    public function test_device_over_limit_is_rejected_with_device_limit_reached(): void
    {
        // Limit-agnostic: fill exactly the limit, then one more must be rejected.
        $max = get_max_devices();
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        for ($i = 2; $i <= $max; $i++) {
            redeem_premium_key($key, 'dev-' . $i);
        }

        $over = redeem_premium_key($key, 'dev-' . ($max + 1));
        $this->assertFalse($over['success']);
        $this->assertSame('device_limit_reached', $over['status']);
        $this->assertCount($max, $over['devices']);
        $this->assertFalse(is_device_registered($first['subscription_id'], 'dev-' . ($max + 1)));
    }

    public function test_reusing_same_device_does_not_consume_a_new_slot(): void
    {
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        $again = redeem_premium_key($key, 'dev-1');

        $this->assertTrue($again['success']);
        $this->assertSame(1, count_subscription_devices($first['subscription_id']));
    }
}
