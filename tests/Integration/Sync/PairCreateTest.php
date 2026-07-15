<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

final class PairCreateTest extends DatabaseTestCase
{
    public function test_create_pairing_token_returns_token_and_short_code(): void
    {
        $result = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('short_code', $result);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $result['token']);
        $this->assertMatchesRegularExpression('/^[23456789ABCDEFGHJKMNPQRSTVWXYZ]{8}$/', $result['short_code']);

        $stmt = $this->pdo->prepare('SELECT short_code, status FROM mobile_sync_pairings WHERE pairing_token = ?');
        $stmt->execute([$result['token']]);
        $row = $stmt->fetch();
        $this->assertNotFalse($row);
        $this->assertSame($result['short_code'], $row['short_code']);
        $this->assertSame('pending', $row['status']);
    }

    public function test_generate_pairing_short_code_uses_expected_alphabet_and_length(): void
    {
        $code = generate_pairing_short_code();
        $this->assertSame(8, strlen($code));
        $this->assertMatchesRegularExpression('/^[23456789ABCDEFGHJKMNPQRSTVWXYZ]{8}$/', $code);
    }
}
