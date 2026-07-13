<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class SnapshotEndpointsTest extends DatabaseTestCase
{
    private function upsertSnapshot(string $companyUid, string $owner, string $ciphertext): void
    {
        // Mirrors the UPSERT in put.php.
        $this->pdo->prepare(
            'INSERT INTO mobile_sync_snapshots (company_uid, owner_identity_hash, ciphertext)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE ciphertext = VALUES(ciphertext), owner_identity_hash = VALUES(owner_identity_hash)'
        )->execute([$companyUid, $owner, $ciphertext]);
    }

    public function test_put_then_get_returns_latest_ciphertext(): void
    {
        $this->upsertSnapshot('company-uid-1', 'owner-1', 'BLOB-A');
        $this->upsertSnapshot('company-uid-1', 'owner-1', 'BLOB-B'); // overwrite

        $stmt = $this->pdo->prepare('SELECT ciphertext FROM mobile_sync_snapshots WHERE company_uid = ?');
        $stmt->execute(['company-uid-1']);
        $this->assertSame('BLOB-B', $stmt->fetch()['ciphertext']);

        // Exactly one row per company_uid.
        $count = $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_snapshots WHERE company_uid='company-uid-1'")->fetch()['c'];
        $this->assertEquals(1, $count);
    }
}
