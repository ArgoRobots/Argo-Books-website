<?php
declare(strict_types=1);

namespace Tests\Integration\Sync;

use Tests\Helpers\DatabaseTestCase;

require_once __DIR__ . '/../../../api/sync/sync-helper.php';

/**
 * A single pairing session (one QR token + its paired short code) must yield
 * exactly one paired device: whichever path (QR redeem or manual code claim)
 * wins first, the other must fail.
 */
final class PairMutualExclusionTest extends DatabaseTestCase
{
    public function test_redeeming_qr_token_after_code_already_claimed_fails(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $claimed = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertIsArray($claimed);

        $redeemed = consume_pairing_token($pairing['token']);
        $this->assertNull($redeemed, 'QR redeem must fail once the pairing has been claimed via short code');
    }

    public function test_claiming_code_after_qr_token_already_redeemed_fails(): void
    {
        $pairing = create_pairing_token('owner-hash-1', 'company-uid-1', 'Acme Co');

        $redeemed = consume_pairing_token($pairing['token']);
        $this->assertIsArray($redeemed);

        $claimed = claim_pairing_code($pairing['short_code'], 'ZmFrZS1wdWJsaWMta2V5', 'Pixel 8');
        $this->assertNull($claimed, 'Code claim must fail once the pairing has been redeemed via QR');
    }
}
