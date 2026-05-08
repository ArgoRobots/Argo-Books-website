<?php
declare(strict_types=1);

namespace Tests\Integration\Cron;

use Tests\Helpers\IntegrationTestCase;

/**
 * purge_pending_account() opens its own transaction, so this uses
 * IntegrationTestCase (manual cleanup). find_accounts_due_for_purge() is
 * tested in the same class because the seed/cleanup needs match.
 *
 * Cleanup strategy: track every community_users.id we created and DELETE
 * them in tearDown. We also DELETE any premium_subscriptions where
 * user_id is one of those tracked IDs (so subscription rows the helper
 * cancelled but didn't delete don't leak).
 */
final class AccountPurgeTest extends IntegrationTestCase
{
    /** @var int[] */
    private array $createdUserIds = [];

    protected function tearDown(): void
    {
        if (!empty($this->createdUserIds)) {
            $placeholders = implode(',', array_fill(0, count($this->createdUserIds), '?'));
            $this->pdo->prepare(
                "DELETE FROM premium_subscriptions WHERE user_id IN ($placeholders)"
            )->execute($this->createdUserIds);
            $this->pdo->prepare(
                "DELETE FROM community_users WHERE id IN ($placeholders)"
            )->execute($this->createdUserIds);
        }
        parent::tearDown();
    }

    /**
     * Inline community_users seeder — IntegrationTestCase doesn't expose
     * one, and DatabaseTestCase's version uses transaction-rollback that
     * doesn't apply here.
     */
    private function seedUser(?string $deletionScheduledAt): int
    {
        $suffix = bin2hex(random_bytes(4));
        $stmt = $this->pdo->prepare(
            "INSERT INTO community_users
             (username, email, password_hash, role, email_verified, deletion_scheduled_at, created_at)
             VALUES (?, ?, 'fake_hash', 'user', 1, ?, NOW())"
        );
        $stmt->execute([
            "purge_test_{$suffix}",
            "purge_{$suffix}@example.test",
            $deletionScheduledAt,
        ]);
        $id = (int) $this->pdo->lastInsertId();
        $this->createdUserIds[] = $id;
        return $id;
    }

    private function seedActiveSubForUser(int $userId, string $subscriptionId): void
    {
        $this->pdo->prepare(
            "INSERT INTO premium_subscriptions
             (subscription_id, user_id, billing_cycle, amount, currency, start_date, end_date,
              status, payment_method, transaction_id, auto_renew, environment, created_at)
             VALUES (?, ?, 'monthly', 10.00, 'CAD', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY),
                     'active', 'stripe', ?, 1, 'sandbox', NOW())"
        )->execute([$subscriptionId, $userId, $subscriptionId]);
    }

    public function test_find_excludes_users_without_deletion_scheduled_at(): void
    {
        $userId = $this->seedUser(null);
        $foundIds = array_map('intval', array_column(find_accounts_due_for_purge($this->pdo), 'id'));
        $this->assertNotContains($userId, $foundIds);
    }

    public function test_find_excludes_accounts_with_future_deletion_date(): void
    {
        $userId = $this->seedUser(null);
        $this->pdo->prepare(
            "UPDATE community_users SET deletion_scheduled_at = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = ?"
        )->execute([$userId]);

        $foundIds = array_map('intval', array_column(find_accounts_due_for_purge($this->pdo), 'id'));
        $this->assertNotContains($userId, $foundIds);
    }

    public function test_find_includes_accounts_with_past_deletion_date(): void
    {
        $userId = $this->seedUser(null);
        $this->pdo->prepare(
            "UPDATE community_users SET deletion_scheduled_at = DATE_SUB(NOW(), INTERVAL 1 DAY) WHERE id = ?"
        )->execute([$userId]);

        $foundIds = array_map('intval', array_column(find_accounts_due_for_purge($this->pdo), 'id'));
        $this->assertContains($userId, $foundIds);
    }

    public function test_purge_deletes_user_row(): void
    {
        $userId = $this->seedUser(null);

        $result = purge_pending_account($this->pdo, $userId);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['deleted']);

        $stmt = $this->pdo->prepare("SELECT 1 FROM community_users WHERE id = ?");
        $stmt->execute([$userId]);
        $this->assertFalse($stmt->fetch(), 'community_users row should be gone');
    }

    public function test_purge_cancels_active_subscriptions_for_user(): void
    {
        $userId = $this->seedUser(null);
        $this->seedActiveSubForUser($userId, 'PREM-PURGE-SUB1-AAAA');
        // No need to track in parent's seededSubscriptions — our own
        // tearDown deletes by user_id which catches all subs we seeded.

        $result = purge_pending_account($this->pdo, $userId);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['cancelled_subs']);

        $stmt = $this->pdo->prepare(
            "SELECT status, auto_renew FROM premium_subscriptions WHERE subscription_id = ?"
        );
        $stmt->execute(['PREM-PURGE-SUB1-AAAA']);
        $sub = $stmt->fetch();
        $this->assertSame('cancelled', $sub['status']);
        $this->assertSame(0, (int) $sub['auto_renew']);
    }

    public function test_purge_does_not_touch_already_cancelled_subs(): void
    {
        $userId = $this->seedUser(null);
        $this->pdo->prepare(
            "INSERT INTO premium_subscriptions
             (subscription_id, user_id, billing_cycle, amount, currency, start_date, end_date,
              status, payment_method, transaction_id, auto_renew, environment, created_at, cancelled_at)
             VALUES (?, ?, 'monthly', 10.00, 'CAD', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY),
                     'cancelled', 'stripe', ?, 0, 'sandbox', NOW(), DATE_SUB(NOW(), INTERVAL 5 DAY))"
        )->execute(['PREM-PURGE-SUB2-AAAA', $userId, 'PREM-PURGE-SUB2-AAAA']);

        $result = purge_pending_account($this->pdo, $userId);

        $this->assertTrue($result['success']);
        // The pre-existing cancelled sub should NOT count as a fresh cancellation
        $this->assertSame(0, $result['cancelled_subs']);
    }

    public function test_purge_returns_zero_deleted_when_user_does_not_exist(): void
    {
        $result = purge_pending_account($this->pdo, 999_999_999);

        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['deleted']);
        $this->assertSame(0, $result['cancelled_subs']);
    }
}
