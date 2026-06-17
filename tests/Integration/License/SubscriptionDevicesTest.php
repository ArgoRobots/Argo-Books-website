<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\IntegrationTestCase;

final class SubscriptionDevicesTest extends IntegrationTestCase
{
    private const SUB = 'PREM-DEVT-EST1-AAAA-BBBB';

    protected function tearDown(): void
    {
        $this->pdo->prepare('DELETE FROM premium_subscription_devices WHERE subscription_id = ?')
            ->execute([self::SUB]);
        parent::tearDown();
    }

    public function test_register_then_count_and_is_registered(): void
    {
        $this->assertSame(0, count_subscription_devices(self::SUB));
        $this->assertFalse(is_device_registered(self::SUB, 'dev-a'));

        register_subscription_device(self::SUB, 'dev-a', 'Windows');

        $this->assertSame(1, count_subscription_devices(self::SUB));
        $this->assertTrue(is_device_registered(self::SUB, 'dev-a'));
    }

    public function test_register_is_idempotent_per_device(): void
    {
        register_subscription_device(self::SUB, 'dev-a');
        register_subscription_device(self::SUB, 'dev-a');
        $this->assertSame(1, count_subscription_devices(self::SUB));
    }

    public function test_remove_device(): void
    {
        register_subscription_device(self::SUB, 'dev-a');
        register_subscription_device(self::SUB, 'dev-b');
        $this->assertTrue(remove_subscription_device(self::SUB, 'dev-a'));
        $this->assertFalse(is_device_registered(self::SUB, 'dev-a'));
        $this->assertSame(1, count_subscription_devices(self::SUB));
        $this->assertFalse(remove_subscription_device(self::SUB, 'dev-a')); // already gone
    }

    public function test_get_devices_returns_rows(): void
    {
        register_subscription_device(self::SUB, 'dev-a', 'Windows');
        $devices = get_subscription_devices(self::SUB);
        $this->assertCount(1, $devices);
        $this->assertSame('dev-a', $devices[0]['device_id']);
        $this->assertSame('Windows', $devices[0]['device_label']);
    }
}
