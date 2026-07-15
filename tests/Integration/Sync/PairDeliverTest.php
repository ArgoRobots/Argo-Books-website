<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairDeliverTest extends DatabaseTestCase
{
    public function test_delivering_to_a_claimed_pairing_sets_ciphertext_and_status(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);

        $result = deliver_pairing_key($pairing['token'], 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5');

        $this->assertTrue($result);

        $stmt = $this->pdo->prepare(
            'SELECT status, encrypted_sync_key FROM mobile_sync_pairings WHERE pairing_token = ?'
        );
        $stmt->execute([$pairing['token']]);
        $row = $stmt->fetch();
        $this->assertNotFalse($row);
        $this->assertSame('delivered', $row['status']);
        $this->assertSame('ZmFrZS1lbmNyeXB0ZWQta2V5', $row['encrypted_sync_key']);
    }

    public function test_delivering_to_a_pending_pairing_fails(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $result = deliver_pairing_key($pairing['token'], 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5');

        $this->assertFalse($result);

        $stmt = $this->pdo->prepare(
            'SELECT status, encrypted_sync_key FROM mobile_sync_pairings WHERE pairing_token = ?'
        );
        $stmt->execute([$pairing['token']]);
        $row = $stmt->fetch();
        $this->assertSame('pending', $row['status']);
        $this->assertNull($row['encrypted_sync_key']);
    }

    public function test_delivering_to_another_owners_pairing_fails(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);

        $result = deliver_pairing_key($pairing['token'], 'owner-hash-EVIL', 'ZmFrZS1lbmNyeXB0ZWQta2V5');

        $this->assertFalse($result);

        $stmt = $this->pdo->prepare(
            'SELECT status, encrypted_sync_key FROM mobile_sync_pairings WHERE pairing_token = ?'
        );
        $stmt->execute([$pairing['token']]);
        $row = $stmt->fetch();
        $this->assertSame('claimed', $row['status']);
        $this->assertNull($row['encrypted_sync_key']);
    }

    public function test_delivering_to_an_already_delivered_pairing_fails(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);

        $first = deliver_pairing_key($pairing['token'], 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5');
        $this->assertTrue($first);

        $second = deliver_pairing_key($pairing['token'], 'owner-hash-1', 'YW5vdGhlci1rZXk');
        $this->assertFalse($second);

        $stmt = $this->pdo->prepare(
            'SELECT encrypted_sync_key FROM mobile_sync_pairings WHERE pairing_token = ?'
        );
        $stmt->execute([$pairing['token']]);
        $row = $stmt->fetch();
        $this->assertSame('ZmFrZS1lbmNyeXB0ZWQta2V5', $row['encrypted_sync_key']);
    }

    public function test_unknown_token_fails(): void
    {
        $result = deliver_pairing_key(bin2hex(random_bytes(16)), 'owner-hash-1', 'ZmFrZS1lbmNyeXB0ZWQta2V5');
        $this->assertFalse($result);
    }
}
