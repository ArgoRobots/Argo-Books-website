<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

final class SchemaTest extends DatabaseTestCase
{
    public function test_mobile_sync_tables_exist(): void
    {
        foreach (['mobile_sync_pairings', 'mobile_sync_devices', 'mobile_sync_snapshots', 'mobile_sync_queue'] as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assertNotFalse($stmt->fetch(), "Table $table should exist");
        }
    }
}
