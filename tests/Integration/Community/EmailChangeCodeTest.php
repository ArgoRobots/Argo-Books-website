<?php
declare(strict_types=1);

namespace Tests\Integration\Community;

use Tests\Helpers\DatabaseTestCase;

require_once PROJECT_ROOT . '/community/users/user_functions.php';

/**
 * The email-change code is six digits, so it is only safe with a small number
 * of guesses per code and a short life.
 */
final class EmailChangeCodeTest extends DatabaseTestCase
{
    // Codes are 100000-999999, so this is never a valid one.
    private const WRONG_CODE = '000000';

    private function newAddress(): string
    {
        return 'new_' . bin2hex(random_bytes(4)) . '@example.test';
    }

    public function test_requesting_a_change_leaves_the_current_email_verified(): void
    {
        $userId = $this->seedCommunityUser();
        start_email_change($userId, $this->newAddress());

        $stmt = $this->pdo->prepare('SELECT email_verified FROM community_users WHERE id = ?');
        $stmt->execute([$userId]);
        $this->assertSame(1, (int) $stmt->fetchColumn());
    }

    public function test_correct_code_is_accepted_once(): void
    {
        $userId = $this->seedCommunityUser();
        $address = $this->newAddress();
        $code = start_email_change($userId, $address);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $this->assertSame('ok', verify_email_change_code($userId, $code, $confirmed));
        $this->assertSame($address, $confirmed);
        $this->assertSame('no_code', verify_email_change_code($userId, $code));
    }

    /**
     * Two sessions on one account: a code for an address the user can read
     * must not confirm a different address requested elsewhere.
     */
    public function test_code_confirms_only_the_address_it_was_sent_to(): void
    {
        $userId = $this->seedCommunityUser();
        start_email_change($userId, 'someone.else@example.test');
        $ownAddress = $this->newAddress();
        $code = start_email_change($userId, $ownAddress);

        $this->assertSame('ok', verify_email_change_code($userId, $code, $confirmed));
        $this->assertSame($ownAddress, $confirmed);
    }

    public function test_code_stops_working_after_too_many_wrong_guesses(): void
    {
        $userId = $this->seedCommunityUser();
        $code = start_email_change($userId, $this->newAddress());

        for ($i = 0; $i < EMAIL_CHANGE_MAX_ATTEMPTS; $i++) {
            $this->assertSame('invalid', verify_email_change_code($userId, self::WRONG_CODE));
        }

        $this->assertSame('too_many_attempts', verify_email_change_code($userId, $code));
    }

    public function test_expired_code_is_rejected(): void
    {
        $userId = $this->seedCommunityUser();
        $code = start_email_change($userId, $this->newAddress());

        $this->pdo->prepare('UPDATE community_users SET email_change_expires_at = NOW() - INTERVAL 1 MINUTE WHERE id = ?')
            ->execute([$userId]);

        $this->assertSame('expired', verify_email_change_code($userId, $code));
    }

    public function test_a_new_code_replaces_the_old_one_and_resets_the_count(): void
    {
        $userId = $this->seedCommunityUser();
        $oldCode = start_email_change($userId, $this->newAddress());
        for ($i = 0; $i < EMAIL_CHANGE_MAX_ATTEMPTS - 1; $i++) {
            verify_email_change_code($userId, self::WRONG_CODE);
        }

        do {
            $newCode = start_email_change($userId, $this->newAddress());
        } while ($newCode === $oldCode);

        $this->assertSame('invalid', verify_email_change_code($userId, $oldCode));
        $this->assertSame('ok', verify_email_change_code($userId, $newCode));
    }

    public function test_no_pending_change_accepts_nothing(): void
    {
        $userId = $this->seedCommunityUser();
        $this->assertSame('no_code', verify_email_change_code($userId, '123456'));
    }
}
