<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\IntegrationTestCase;

final class RedeemEndpointShapeTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM premium_subscription_devices WHERE subscription_id LIKE 'PREM-%'");
        parent::tearDown();
    }

    public function test_device_limit_reached_payload_is_json_serializable(): void
    {
        $max = get_max_devices();
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        for ($i = 2; $i <= $max; $i++) {
            redeem_premium_key($key, 'dev-' . $i);
        }
        $limited = redeem_premium_key($key, 'dev-' . ($max + 1));

        $json = json_encode($limited);
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('device_limit_reached', $decoded['status']);
        $this->assertArrayHasKey('devices', $decoded);
        $this->assertArrayHasKey('max_devices', $decoded);
    }
}
