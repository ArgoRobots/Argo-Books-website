<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairKeyTest extends DatabaseTestCase
{
    public function test_before_delivery_returns_pending_and_keeps_the_pairing_row(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);

        $result = fetch_and_consume_pairing_key($claim['device_token']);

        $this->assertSame(['pending' => true], $result);

        $stmt = $this->pdo->prepare('SELECT COUNT(*) c FROM mobile_sync_pairings WHERE short_code = ?');
        $stmt->execute([$pairing['short_code']]);
        $this->assertEquals(1, $stmt->fetch()['c']);
    }

    public function test_after_delivery_returns_key_and_deletes_the_pairing_row_but_keeps_the_device(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);
        $delivered = deliver_pairing_key($pairing['token'], 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5');
        $this->assertTrue($delivered);

        $result = fetch_and_consume_pairing_key($claim['device_token']);

        $this->assertSame(['encrypted_sync_key' => 'ZmFrZS1lbmNyeXB0ZWQta2V5'], $result);

        $pairingCount = $this->pdo->prepare('SELECT COUNT(*) c FROM mobile_sync_pairings WHERE short_code = ?');
        $pairingCount->execute([$pairing['short_code']]);
        $this->assertEquals(0, $pairingCount->fetch()['c']);

        $deviceCount = $this->pdo->prepare(
            'SELECT COUNT(*) c FROM mobile_sync_devices WHERE device_token_hash = ?'
        );
        $deviceCount->execute([hash('sha256', $claim['device_token'])]);
        $this->assertEquals(1, $deviceCount->fetch()['c']);
    }

    public function test_second_poll_after_fetching_the_key_finds_nothing(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);
        $this->assertTrue(deliver_pairing_key($pairing['token'], 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5'));

        $first = fetch_and_consume_pairing_key($claim['device_token']);
        $this->assertSame(['encrypted_sync_key' => 'ZmFrZS1lbmNyeXB0ZWQta2V5'], $first);

        $second = fetch_and_consume_pairing_key($claim['device_token']);
        $this->assertNull($second);
    }

    public function test_unknown_device_token_returns_null(): void
    {
        $result = fetch_and_consume_pairing_key(bin2hex(random_bytes(32)));
        $this->assertNull($result);
    }

    public function test_empty_device_token_returns_null(): void
    {
        $result = fetch_and_consume_pairing_key('');
        $this->assertNull($result);
    }
}
