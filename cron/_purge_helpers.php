<?php
declare(strict_types=1);

/**
 * Pure helpers extracted from account_purge.php so the per-account purge
 * logic can be exercised without running the cron's top-level dispatch loop.
 *
 * - find_accounts_due_for_purge: SELECT for accounts past the deletion grace
 * - purge_pending_account: cancels active subs + DELETE the user row in a
 *   single transaction; returns a result array. FK cascades on community_*
 *   tables clean up the related data.
 */

/**
 * Return the rows from community_users whose deletion_scheduled_at is set
 * and has already passed.
 *
 * @return array<int,array{id:int,username:string,email:string,deletion_scheduled_at:string}>
 */
function find_accounts_due_for_purge(PDO $pdo): array
{
    $stmt = $pdo->prepare(
        "SELECT id, username, email, deletion_scheduled_at
         FROM community_users
         WHERE deletion_scheduled_at IS NOT NULL
           AND deletion_scheduled_at <= NOW()"
    );
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Permanently delete a community_users row and cancel any active premium
 * subscriptions linked to it. Wrapped in a single transaction; on failure,
 * the whole purge for this account is rolled back.
 *
 * Note on subscriptions: there is no FK between premium_subscriptions and
 * community_users, so we must explicitly mark active subs cancelled before
 * the user is deleted (otherwise they'd live on as orphaned auto-renewing
 * rows pointing at a non-existent user_id).
 *
 * Returns ['success' => true, 'cancelled_subs' => int, 'deleted' => int]
 * or ['success' => false, 'error' => string].
 */
function purge_pending_account(PDO $pdo, int $userId): array
{
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "UPDATE premium_subscriptions
             SET status = 'cancelled',
                 auto_renew = 0,
                 cancelled_at = NOW(),
                 updated_at = NOW()
             WHERE user_id = ?
               AND status = 'active'"
        );
        $stmt->execute([$userId]);
        $cancelledSubs = $stmt->rowCount();

        $stmt = $pdo->prepare("DELETE FROM community_users WHERE id = ?");
        $stmt->execute([$userId]);
        $deleted = $stmt->rowCount();

        $pdo->commit();

        return [
            'success' => true,
            'cancelled_subs' => $cancelledSubs,
            'deleted' => $deleted,
        ];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}
