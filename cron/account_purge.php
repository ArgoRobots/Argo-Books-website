<?php
/**
 * Account Purge Cron Job
 *
 * Permanently deletes user accounts whose 30-day deletion grace period has expired.
 * Users schedule deletion from their profile; logging back in cancels the request.
 *
 * RECOMMENDED SCHEDULE: Daily at 4:00 AM
 *   0 4 * * * /usr/bin/php /path/to/account_purge.php
 *
 * Manual execution:
 *   php account_purge.php
 */

set_time_limit(120);

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request)
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';

function logPurge($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";

    $logFile = __DIR__ . '/logs/account_purge_' . date('Y-m-d') . '.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

logPurge('Starting account purge check...');

try {
    global $pdo;

    // Find accounts past their scheduled deletion date
    $stmt = $pdo->prepare("
        SELECT id, username, email, deletion_scheduled_at
        FROM community_users
        WHERE deletion_scheduled_at IS NOT NULL
        AND deletion_scheduled_at <= NOW()
    ");
    $stmt->execute();
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    logPurge("Found " . count($accounts) . " accounts due for deletion");

    $deletedCount = 0;
    $failedCount = 0;

    foreach ($accounts as $account) {
        $userId = $account['id'];
        $username = $account['username'];

        try {
            $pdo->beginTransaction();

            // Cancel any active premium subscriptions (no FK cascade on this table)
            $stmt = $pdo->prepare("
                UPDATE premium_subscriptions
                SET status = 'cancelled',
                    auto_renew = 0,
                    cancelled_at = NOW(),
                    updated_at = NOW()
                WHERE user_id = ?
                AND status = 'active'
            ");
            $stmt->execute([$userId]);
            $cancelledSubs = $stmt->rowCount();

            if ($cancelledSubs > 0) {
                logPurge("Cancelled $cancelledSubs active subscription(s) for user $username (#$userId)");
            }

            // Delete the user (FK cascades handle related community data)
            $stmt = $pdo->prepare("DELETE FROM community_users WHERE id = ?");
            $stmt->execute([$userId]);

            $pdo->commit();

            logPurge("Deleted account: $username (#$userId) - scheduled at {$account['deletion_scheduled_at']}");
            $deletedCount++;

        } catch (Exception $e) {
            $pdo->rollBack();
            logPurge("Failed to delete account $username (#$userId): " . $e->getMessage(), 'ERROR');
            $failedCount++;
        }
    }

    logPurge("Account purge complete. Deleted: $deletedCount, Failed: $failedCount");

} catch (PDOException $e) {
    logPurge("Database error: " . $e->getMessage(), 'ERROR');
    exit(1);
}
