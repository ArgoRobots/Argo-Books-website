<?php
/**
 * reddit_status_check.php
 *
 * For each thread with status='replied', re-checks the posted comment via
 * Reddit API on a staggered schedule (30min / 2h / 6h / 24h / 72h after
 * posting), classifies reply_status, captures engagement (upvotes + replies),
 * then rolls up per-subreddit removal stats and applies the auto-disable rule.
 * Idempotent: each row tracks its own check_count + last_checked_at so we never
 * over-check.
 *
 * Schedule: every 2 hours.
 *   0 *\/2 * * * /usr/bin/php /home/argorobots/public_html/cron/reddit_status_check.php
 */

set_time_limit(600);

if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. CLI/cron only.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/lib/outreach_helpers.php';
require_once __DIR__ . '/lib/reddit_helpers.php';
require_once __DIR__ . '/lib/run_tracker.php';

// ─── Lock file ───

$lockDir = __DIR__ . '/logs';
if (!is_dir($lockDir)) mkdir($lockDir, 0755, true);
$lockFile = $lockDir . '/reddit_status_check.lock';
$lockFp = fopen($lockFile, 'c');
if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "Reddit status check already running. Exiting.\n";
    exit(0);
}

// Schedule (in seconds since reply_posted_at) for checks 1..5
const CHECK_OFFSETS_SEC = [1800, 7200, 21600, 86400, 259200];

$startedAt = date('Y-m-d H:i:s');
$updated = 0;
$removedCount = 0;
$lastError = null;

$runId = cron_run_start($pdo, 'reddit_status_check');

try {
    // Find candidates: replied, comment id known, check_count < 5
    // Filter to those whose next scheduled check has passed.
    $stmt = $pdo->query("
        SELECT id, subreddit, reply_comment_id, reply_posted_at, reply_status_check_count, reply_status
        FROM reddit_threads
        WHERE status = 'replied'
          AND reply_comment_id IS NOT NULL
          AND reply_status_check_count < 5
          AND (reply_status IS NULL OR reply_status NOT IN ('deleted_by_user'))
    ");
    $candidates = $stmt->fetchAll();

    $now = time();
    foreach ($candidates as $row) {
        $count = (int) $row['reply_status_check_count'];
        $postedAt = strtotime($row['reply_posted_at']);
        if ($count >= 5 || !$postedAt) continue;

        $offset = CHECK_OFFSETS_SEC[$count] ?? null;
        if ($offset === null) continue;
        if (($now - $postedAt) < $offset) continue; // not yet time for next check

        $result = reddit_check_comment_status($pdo, $row['reply_comment_id']);
        $status = $result['status']; // 'live' | 'removed' | 'removed_or_shadowbanned' | 'deleted_by_user' | null

        if ($status === null) {
            // API failed; don't increment count so we retry next run.
            reddit_log("Status check: API failure for thread {$row['id']} (comment {$row['reply_comment_id']})");
            continue;
        }

        $upd = $pdo->prepare("
            UPDATE reddit_threads
            SET reply_status = ?,
                reply_status_checked_at = NOW(),
                reply_status_check_count = reply_status_check_count + 1,
                reply_upvotes = ?,
                reply_replies_count = ?
            WHERE id = ?
        ");
        $upd->execute([
            $status,
            $result['upvotes'],
            $result['replies'],
            $row['id'],
        ]);
        $updated++;
        if (in_array($status, ['removed', 'removed_or_shadowbanned'], true)) {
            $removedCount++;
        }
    }

    // ─── Roll up per-subreddit removal rates ───

    $rollup = $pdo->query("
        SELECT subreddit,
               COUNT(*) AS total,
               SUM(CASE WHEN reply_status IN ('removed', 'removed_or_shadowbanned') THEN 1 ELSE 0 END) AS removed
        FROM reddit_threads
        WHERE status = 'replied'
          AND reply_status_check_count >= 1
          AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY subreddit
    ")->fetchAll();

    // Reset all subreddits to zero so subs with no recent replies show 0/0
    $pdo->exec("UPDATE reddit_subreddits SET replies_30d = 0, removal_rate_30d = 0.00");

    $rollupStmt = $pdo->prepare("
        UPDATE reddit_subreddits
        SET replies_30d = ?, removal_rate_30d = ?
        WHERE name = ?
    ");
    foreach ($rollup as $r) {
        $total = (int) $r['total'];
        $removed = (int) $r['removed'];
        $rate = $total > 0 ? round(($removed / $total) * 100, 2) : 0.0;
        $rollupStmt->execute([$total, $rate, $r['subreddit']]);
    }

    // ─── Auto-disable rule ───

    $cfg = $pdo->query("SELECT auto_disable_removal_rate, auto_disable_min_replies FROM reddit_settings WHERE id = 1")->fetch();
    $threshold = (int) ($cfg['auto_disable_removal_rate'] ?? 60);
    $minReplies = (int) ($cfg['auto_disable_min_replies'] ?? 3);

    $disableStmt = $pdo->prepare("
        UPDATE reddit_subreddits
        SET auto_disabled_at = NOW(),
            auto_disabled_reason = 'high_removal_rate'
        WHERE enabled = 1
          AND auto_disabled_at IS NULL
          AND replies_30d >= ?
          AND removal_rate_30d >= ?
    ");
    $disableStmt->execute([$minReplies, $threshold]);
    $disabledCount = $disableStmt->rowCount();
    if ($disabledCount > 0) {
        reddit_log("Auto-disabled $disabledCount subreddit(s) for high removal rate.");
    }

    // ─── Diagnostics ───

    $upd = $pdo->prepare("UPDATE reddit_settings SET last_status_check_at = ? WHERE id = 1");
    $upd->execute([$startedAt]);

    cron_metric_set('replies_checked', $updated);
    cron_metric_set('replies_removed', $removedCount);
    cron_run_finish($pdo, $runId, 'ok');
    echo "Reddit status check complete: $updated reply rows updated, " . count($rollup) . " subreddits rolled up.\n";
} catch (Throwable $e) {
    $lastError = $e->getMessage();
    reddit_log("Status check crashed: $lastError");
    cron_metric_set('replies_checked', $updated);
    cron_metric_set('replies_removed', $removedCount);
    cron_run_finish($pdo, $runId, 'error', $lastError);
    echo "Reddit status check FAILED: $lastError\n";
    exit(1);
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
}
