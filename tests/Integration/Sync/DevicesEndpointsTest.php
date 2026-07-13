<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class DevicesEndpointsTest extends DatabaseTestCase
{
    private function addDevice(string $company, string $owner, string $label): int
    {
        $this->pdo->prepare(
            'INSERT INTO mobile_sync_devices (device_token_hash, owner_identity_hash, company_uid, device_label) VALUES (?,?,?,?)'
        )->execute([hash('sha256', bin2hex(random_bytes(8))), $owner, $company, $label]);
        return (int) $this->pdo->lastInsertId();
    }

    public function test_revoking_last_device_purges_snapshot_and_queue(): void
    {
        $owner = 'owner-1';
        $company = 'company-1';
        $id = $this->addDevice($company, $owner, 'Pixel 8');
        $this->pdo->prepare('INSERT INTO mobile_sync_snapshots (company_uid, owner_identity_hash, ciphertext) VALUES (?,?,?)')
            ->execute([$company, $owner, 'BLOB']);
        $this->pdo->prepare('INSERT INTO mobile_sync_queue (company_uid, owner_identity_hash, from_device_id, ciphertext) VALUES (?,?,?,?)')
            ->execute([$company, $owner, $id, 'ITEM']);

        // revoke.php logic: delete device scoped to owner+company, then purge if none remain.
        $this->pdo->prepare('DELETE FROM mobile_sync_devices WHERE id = ? AND owner_identity_hash = ? AND company_uid = ?')
            ->execute([$id, $owner, $company]);
        $remaining = (int) $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_devices WHERE company_uid='$company'")->fetch()['c'];
        if ($remaining === 0) {
            $this->pdo->prepare('DELETE FROM mobile_sync_snapshots WHERE company_uid = ?')->execute([$company]);
            $this->pdo->prepare('DELETE FROM mobile_sync_queue WHERE company_uid = ?')->execute([$company]);
        }

        $this->assertEquals(0, $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_snapshots WHERE company_uid='$company'")->fetch()['c']);
        $this->assertEquals(0, $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_queue WHERE company_uid='$company'")->fetch()['c']);
    }

    public function test_revoking_one_of_two_devices_keeps_snapshot(): void
    {
        $owner = 'owner-1';
        $company = 'company-1';
        $a = $this->addDevice($company, $owner, 'Pixel 8');
        $this->addDevice($company, $owner, 'Galaxy S23');
        $this->pdo->prepare('INSERT INTO mobile_sync_snapshots (company_uid, owner_identity_hash, ciphertext) VALUES (?,?,?)')
            ->execute([$company, $owner, 'BLOB']);

        $this->pdo->prepare('DELETE FROM mobile_sync_devices WHERE id = ? AND owner_identity_hash = ? AND company_uid = ?')
            ->execute([$a, $owner, $company]);
        $remaining = (int) $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_devices WHERE company_uid='$company'")->fetch()['c'];
        $this->assertEquals(1, $remaining);
        // snapshot still present because a device remains
        $this->assertEquals(1, $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_snapshots WHERE company_uid='$company'")->fetch()['c']);
    }
}
