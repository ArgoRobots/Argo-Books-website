<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairStatusTest extends DatabaseTestCase
{
    public function test_pending_pairing_returns_pending_status_with_no_key(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $result = get_pairing_status($pairing['token'], 'owner-hash-1');

        $this->assertIsArray($result);
        $this->assertSame('pending', $result['status']);
        $this->assertArrayNotHasKey('phone_public_key', $result);
        $this->assertArrayNotHasKey('device_label', $result);
    }

    public function test_claimed_pairing_returns_claimed_status_with_phone_public_key(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $claim = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claim);

        $result = get_pairing_status($pairing['token'], 'owner-hash-1');

        $this->assertIsArray($result);
        $this->assertSame('claimed', $result['status']);
        $this->assertSame('ZmFrZS1wdWJsaWMta2V5', $result['phone_public_key']);
        $this->assertSame('Pixel 8', $result['device_label']);
    }

    public function test_pairing_owned_by_a_different_owner_is_not_visible(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $result = get_pairing_status($pairing['token'], 'owner-hash-EVIL');

        $this->assertNull($result);
    }

    public function test_unknown_token_returns_null(): void
    {
        $result = get_pairing_status(bin2hex(random_bytes(16)), 'owner-hash-1');
        $this->assertNull($result);
    }
}
