<?php
declare(strict_types=1);

/**
 * payroll_rate_reminder.php
 *
 * Emails the admin that CRA's payroll deduction tables are about to change, so a new rate
 * file can be prepared and uploaded before it takes effect.
 *
 * CRA publishes twice a year, effective January 1 and July 1. A pay run calculated after a
 * changeover with the previous edition produces deductions that look plausible and are
 * wrong, and the app has no way to detect that on its own, so this reminder is the only
 * thing standing between a missed deadline and every customer's first pay run of the period
 * being incorrect.
 *
 * Sent from the 10th to the 20th of December and June, roughly two weeks of lead time. Sends
 * once per window, tracked in cron_runs, so a daily schedule does not produce eleven emails.
 *
 * Always sent. There is no preference to disable it: the whole point is that it arrives in a
 * month when payroll is not on anyone's mind.
 *
 * Schedule: daily.
 *   0 8 * * * /usr/bin/php /home/argorobots/public_html/cron/payroll_rate_reminder.php
 */

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request).
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../email_sender.php';
require_once __DIR__ . '/lib/run_tracker.php';

global $pdo;

$runId = cron_run_start($pdo, 'payroll_rate_reminder');

try {

$month = (int) date('n');
$day   = (int) date('j');
$year  = (int) date('Y');

// December warns about the January edition, June about July's.
if ($month === 12) {
    $edition       = ($year + 1) . '-01';
    $effectiveDate = 'January 1, ' . ($year + 1);
} elseif ($month === 6) {
    $edition       = $year . '-07';
    $effectiveDate = 'July 1, ' . $year;
} else {
    cron_metric_set('in_window', 0);
    cron_run_finish($pdo, $runId, 'ok');
    exit(0);
}

if ($day < 10 || $day > 20) {
    cron_metric_set('in_window', 0);
    cron_run_finish($pdo, $runId, 'ok');
    exit(0);
}

cron_metric_set('in_window', 1);

// One email per window. Checking cron_runs rather than adding a table keeps the state where
// every other cron already keeps it, and a re-run on the same day cannot send twice.
$already = $pdo->prepare("
    SELECT COUNT(*) FROM cron_runs
    WHERE cron_name = 'payroll_rate_reminder'
      AND status = 'ok'
      AND error_message LIKE ?
      AND started_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
");
$already->execute(['%sent for ' . $edition . '%']);

if ((int) $already->fetchColumn() > 0) {
    cron_metric_set('already_sent', 1);
    cron_run_finish($pdo, $runId, 'ok');
    exit(0);
}

$to = $pdo->query('SELECT notification_email FROM admin_notification_prefs WHERE id = 1')->fetchColumn();
if (!$to) {
    $to = 'contact@argorobots.com';
}

$subject = 'Payroll rates change on ' . $effectiveDate;

$body = '
    <p>CRA\'s payroll deduction tables change on <strong>' . htmlspecialchars($effectiveDate) . '</strong>.
    A new rate file needs to be prepared and uploaded before then.</p>

    <p>Any pay run calculated on or after that date without the new file will either refuse to
    calculate, or worse, use the wrong numbers. Argo Books cannot detect this on its own.</p>

    <p><strong>Edition to prepare:</strong> ' . htmlspecialchars($edition) . '</p>

    <p>Full instructions, including where to get the numbers and how to verify them, are in
    <code>docs/Payroll rate updates.md</code> in the desktop repository.</p>

    <p>Rough steps:</p>
    <ol>
        <li>Get the new figures from CRA\'s T4127 guide and the CPP, CPP2 and EI rate pages.</li>
        <li>Check every derived maximum reproduces from its own rate.</li>
        <li>Build <code>' . htmlspecialchars($edition) . '.json</code> and check it against CRA\'s calculator.</li>
        <li>Upload it so existing installs pick it up without an app update.</li>
    </ol>
';

$sent = send_styled_email($to, $subject, $body, 'orange');

cron_metric_set('email_sent', $sent ? 1 : 0);

if (!$sent) {
    error_log('payroll_rate_reminder: failed to send reminder for edition ' . $edition);
    cron_run_finish($pdo, $runId, 'error', 'Could not send reminder for ' . $edition);
    exit(1);
}

cron_run_finish($pdo, $runId, 'ok', 'Reminder sent for ' . $edition . ', effective ' . $effectiveDate);

} catch (Throwable $e) {
    error_log('payroll_rate_reminder: ' . $e->getMessage());
    cron_run_finish($pdo, $runId, 'error', $e->getMessage());
    exit(1);
}
