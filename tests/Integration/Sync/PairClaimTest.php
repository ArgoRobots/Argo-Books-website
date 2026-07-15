<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairClaimTest extends DatabaseTestCase
{
    public function test_valid_claim_returns_token_marks_row_claimed_and_creates_device(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $result = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');

        $this->assertIsArray($result);
        $this->assertSame('company-uid-1', $result['company_uid']);
        $this->assertSame('Acme Co', $result['company_label']);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $result['device_token']);

        $stmt = $this->pdo->prepare(
            'SELECT status, phone_public_key, device_label, claimed_at, device_token_hash
             FROM mobile_sync_pairings WHERE short_code = ?'
        );
        $stmt->execute([$pairing['short_code']]);
        $row = $stmt->fetch();
        $this->assertNotFalse($row);
        $this->assertSame('claimed', $row['status']);
        $this->assertSame('ZmFrZS1wdWJsaWMta2V5', $row['phone_public_key']);
        $this->assertSame('Pixel 8', $row['device_label']);
        $this->assertNotNull($row['claimed_at']);
        $this->assertSame(hash('sha256', $result['device_token']), $row['device_token_hash']);

        $deviceStmt = $this->pdo->prepare(
            'SELECT owner_identity_hash, company_uid, device_label, device_token_hash
             FROM mobile_sync_devices WHERE device_token_hash = ?'
        );
        $deviceStmt->execute([hash('sha256', $result['device_token'])]);
        $device = $deviceStmt->fetch();
        $this->assertNotFalse($device);
        $this->assertSame('owner-hash-1', $device['owner_identity_hash']);
        $this->assertSame('company-uid-1', $device['company_uid']);
        $this->assertSame('Pixel 8', $device['device_label']);
    }

    public function test_claim_normalizes_lowercase_and_stray_characters(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');
        $messyCode = strtolower(substr($pairing['short_code'], 0, 4)) . '-' . substr($pairing['short_code'], 4);

        $result = claim_pairing_code($messyCode, 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');

        $this->assertIsArray($result);
        $this->assertSame('company-uid-1', $result['company_uid']);
    }

    public function test_unknown_code_returns_null(): void
    {
        $result = claim_pairing_code('ZZZZZZZZ', 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertNull($result);
    }

    public function test_expired_pending_row_returns_null(): void
    {
        $code = 'ABCD2345';
        $this->pdo->prepare(
            "INSERT INTO mobile_sync_pairings (pairing_token, short_code, owner_identity_hash, company_uid, company_label, status, expires_at)
             VALUES (?, ?, 'owner-hash-1', 'company-uid-1', 'Acme Co', 'pending', DATE_SUB(NOW(), INTERVAL 1 MINUTE))"
        )->execute([bin2hex(random_bytes(16)), $code]);

        $result = claim_pairing_code($code, 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertNull($result);
    }

    public function test_second_claim_of_same_code_returns_null(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $first = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($first);

        $second = claim_pairing_code($pairing['short_code'], 'YW5vdGhlci1rZXk', 'Galaxy S23');
        $this->assertNull($second);

        // Only one device row was created (the second claim attempt is a no-op).
        $count = $this->pdo->query(
            "SELECT COUNT(*) c FROM mobile_sync_devices WHERE company_uid = 'company-uid-1'"
        )->fetch()['c'];
        $this->assertEquals(1, $count);
    }

    public function test_ip_over_the_sync_claim_rate_limit_is_blocked(): void
    {
        $ip = '192.0.2.' . random_int(1, 254);
        try {
            for ($i = 0; $i < 10; $i++) {
                record_rate_limit_attempt($ip, 'sync_claim', 900);
            }
            $this->assertTrue(is_rate_limited($ip, 10, 900, 'sync_claim'));
            // A different bucket for the same IP is unaffected.
            $this->assertFalse(is_rate_limited($ip, 10, 900, 'sync_redeem'));
        } finally {
            clear_rate_limit_attempts($ip, 'sync_claim');
        }
    }
}
