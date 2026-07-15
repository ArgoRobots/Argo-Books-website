<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairEndpointsTest extends DatabaseTestCase
{
    public function test_redeem_creates_device_row_bound_to_company(): void
    {
        // Desktop side: create a pairing token.
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $token = $pairing['token'];

        // Phone side (what redeem.php does): consume the token, insert a device row.
        $binding = consume_pairing_token($token);
        $this->assertNotNull($binding);

        $deviceToken = bin2hex(random_bytes(32));
        $this->pdo->prepare(
            'INSERT INTO mobile_sync_devices (device_token_hash, owner_identity_hash, company_uid, device_label)
             VALUES (?, ?, ?, ?)'
        )->execute([hash('sha256', $deviceToken), $binding['owner_identity_hash'], $binding['company_uid'], 'Pixel 8']);

        // The device token now authenticates for that company.
        $_SERVER['HTTP_X_SYNC_DEVICE_TOKEN'] = $deviceToken;
        $auth = authenticate_sync_device();
        unset($_SERVER['HTTP_X_SYNC_DEVICE_TOKEN']);
        $this->assertSame('company-uid-1', $auth['company_uid']);
    }
}
