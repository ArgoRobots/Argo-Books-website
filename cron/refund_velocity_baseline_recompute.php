<?php
declare(strict_types=1);

/**
 * refund_velocity_baseline_recompute.php
 *
 * Refreshes refund_velocity_baselines for every active company. Used by the
 * adaptive velocity engine to derive per-company thresholds (3× / 10× /
 * 25% / 50% multipliers) from each company's own history rather than fixed
 * dollar amounts that would either choke big shops or let small ones drain.
 *
 * Schedule: nightly at 02:00.
 *   0 2 * * * php /var/www/argo-books-website/cron/refund_velocity_baseline_recompute.php
 */

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request).
// Without this, fraud-threshold baselines could be recomputed via HTTP.
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/lib/run_tracker.php';

global $pdo;

$runId = cron_run_start($pdo, 'refund_velocity_baseline_recompute');

try {

$pdo->exec("
    INSERT INTO refund_velocity_baselines
        (company_id, daily_avg_refund_cents, daily_avg_refund_count, revenue_30d_cents, last_recomputed_at)
    SELECT
        c.id,
        COALESCE(refund_stats.avg_daily_cents, 0),
        COALESCE(refund_stats.avg_daily_count, 0),
        COALESCE(rev_stats.revenue_cents, 0),
        NOW()
    FROM portal_companies c
    LEFT JOIN (
        SELECT company_id,
               SUM(amount_cents) / 30 AS avg_daily_cents,
               COUNT(*) / 30 AS avg_daily_count
        FROM refund_requests
        WHERE state = 'completed'
          AND completed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY company_id
    ) refund_stats ON refund_stats.company_id = c.id
    LEFT JOIN (
        SELECT p.company_id,
               ROUND(SUM(p.amount) * 100) AS revenue_cents
        FROM portal_payments p
        WHERE p.status = 'completed'
          AND p.amount > 0
          AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY p.company_id
    ) rev_stats ON rev_stats.company_id = c.id
    WHERE c.is_active = 1
    ON DUPLICATE KEY UPDATE
        daily_avg_refund_cents = VALUES(daily_avg_refund_cents),
        daily_avg_refund_count = VALUES(daily_avg_refund_count),
        revenue_30d_cents = VALUES(revenue_30d_cents),
        last_recomputed_at = NOW()
");

$updated = $pdo->query("SELECT COUNT(*) FROM refund_velocity_baselines WHERE last_recomputed_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)")->fetchColumn();
echo "Refreshed baselines for $updated companies\n";
cron_metric_incr('baselines_recomputed', (int) $updated);
cron_run_finish($pdo, $runId, 'ok');

} catch (Throwable $e) {
    cron_run_finish($pdo, $runId, 'error', $e->getMessage());
    throw $e;
}
