<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\DatabaseTestCase;

require_once PROJECT_ROOT . '/api/receipt/scan_quota.php';

/**
 * Production and dev share one database, so a free-tier counter keyed only on
 * device_<hash> lets a scan made while testing against dev eat the allowance a
 * real customer paid for on the same machine. The counter row is per
 * environment. Tests run as sandbox.
 */
final class ReceiptScanQuotaEnvironmentSeparationTest extends DatabaseTestCase
{
    private const IDENTIFIER = 'device_test_env_separation_hash';

    public function test_a_production_row_at_its_limit_does_not_block_a_sandbox_scan(): void
    {
        $this->seedUsageRow('production', 5, 5);

        $take = receipt_scan_quota_consume($this->pdo, self::IDENTIFIER, 5);

        $this->assertTrue($take['allowed']);
        $this->assertSame(1, $take['scan_count']);
        $this->assertSame(5, $this->scanCountFor('production'));
    }

    public function test_consuming_in_sandbox_leaves_the_production_counter_alone(): void
    {
        $this->seedUsageRow('production', 2, 5);

        receipt_scan_quota_consume($this->pdo, self::IDENTIFIER, 5);
        receipt_scan_quota_consume($this->pdo, self::IDENTIFIER, 5);

        $this->assertSame(2, $this->scanCountFor('production'));
        $this->assertSame(2, $this->scanCountFor('sandbox'));
    }

    private function seedUsageRow(string $environment, int $scanCount, int $limit): void
    {
        $this->pdo->prepare(
            'INSERT INTO receipt_scan_usage
             (license_key, usage_month, scan_count, monthly_limit, environment)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([self::IDENTIFIER, date('Y-m-01'), $scanCount, $limit, $environment]);
    }

    private function scanCountFor(string $environment): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT scan_count FROM receipt_scan_usage
             WHERE license_key = ? AND usage_month = ? AND environment = ?'
        );
        $stmt->execute([self::IDENTIFIER, date('Y-m-01'), $environment]);
        return (int) ($stmt->fetchColumn() ?: 0);
    }
}
