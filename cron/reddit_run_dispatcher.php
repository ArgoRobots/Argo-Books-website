<?php
declare(strict_types=1);

/**
 * reddit_run_dispatcher.php
 *
 * Bridges the admin "Run discovery now" button to a real CLI run.
 *
 * This host disables exec/shell_exec/proc_open, so the admin button can't spawn
 * a background process, and an inline run inside the web request is killed by
 * PHP-FPM's request_terminate_timeout (~30s) long before discovery finishes.
 * So the button just records reddit_settings.manual_run_requested_at; this cron
 * polls for that flag and runs reddit_monitor via CLI, which has no such limit.
 *
 * Lightweight by design: when no run is requested it does a single SELECT and
 * exits, so a tight schedule is cheap. It does not write to cron_runs;
 * reddit_monitor tracks its own run when the dispatcher fires it.
 *
 * Schedule: every 2 minutes.
 *   *\/2 * * * * /usr/bin/php /home/argorobots/public_html/cron/reddit_run_dispatcher.php
 */

// CLI / cron only.
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
require_once __DIR__ . '/../db_connect.php';

global $pdo;

// Any pending request?
$requestedAt = $pdo->query("SELECT manual_run_requested_at FROM reddit_settings WHERE id = 1")
                   ->fetchColumn();
if (!$requestedAt) {
    exit(0); // nothing requested
}

$ageSec = time() - strtotime((string) $requestedAt);

// Claim the request immediately so a second dispatcher tick can't double-fire
// it while reddit_monitor is still starting up.
$pdo->prepare("UPDATE reddit_settings SET manual_run_requested_at = NULL WHERE id = 1")->execute();

// Ignore a stale request (e.g. the dispatcher was paused) so an old click can't
// trigger a surprise run minutes or hours later.
if ($ageSec > 900) {
    error_log('[reddit_run_dispatcher] Ignoring stale run request (' . $ageSec . 's old).');
    exit(0);
}

// Run discovery via CLI. REDDIT_FORCE_RUN tells reddit_monitor to run even when
// the master enable toggle is off (a manual request is an explicit override).
// reddit_monitor holds its own lock file, so if a run is already in progress
// this require is a no-op there.
define('REDDIT_FORCE_RUN', true);
require __DIR__ . '/reddit_monitor.php';
