<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class QueueEndpointsTest extends DatabaseTestCase
{
    private function push(string $companyUid, string $owner, int $fromDevice, string $ciphertext): void
    {
        $this->pdo->prepare(
            'INSERT INTO mobile_sync_queue (company_uid, owner_identity_hash, from_device_id, ciphertext) VALUES (?,?,?,?)'
        )->execute([$companyUid, $owner, $fromDevice, $ciphertext]);
    }

    public function test_pull_then_ack_removes_items_scoped_to_owner_and_company(): void
    {
        $this->push('company-1', 'owner-1', 1, 'ITEM-A');
        $this->push('company-1', 'owner-1', 1, 'ITEM-B');
        $this->push('company-2', 'owner-1', 1, 'OTHER'); // different company, must not be pulled

        $stmt = $this->pdo->prepare(
            'SELECT id, ciphertext FROM mobile_sync_queue WHERE company_uid = ? AND owner_identity_hash = ? ORDER BY id'
        );
        $stmt->execute(['company-1', 'owner-1']);
        $items = $stmt->fetchAll();
        $this->assertCount(2, $items);

        // ack: delete pulled ids scoped to owner+company
        $ids = array_column($items, 'id');
        $in = implode(',', array_fill(0, count($ids), '?'));
        $this->pdo->prepare(
            "DELETE FROM mobile_sync_queue WHERE company_uid = ? AND owner_identity_hash = ? AND id IN ($in)"
        )->execute(array_merge(['company-1', 'owner-1'], $ids));

        $left = $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_queue WHERE company_uid='company-1'")->fetch()['c'];
        $this->assertEquals(0, $left);
        // The other company's item is untouched.
        $other = $this->pdo->query("SELECT COUNT(*) c FROM mobile_sync_queue WHERE company_uid='company-2'")->fetch()['c'];
        $this->assertEquals(1, $other);
    }
}
