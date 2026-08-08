<?php
declare(strict_types=1);

/**
 * portal_invoice_reminders.php
 *
 * Sends automatic overdue-invoice reminders to a merchant's customers on a
 * fixed 3/7/14-day cadence, then stops. Opt-in per company via
 * portal_companies.reminders_enabled (set from Argo Books through
 * PUT /api/portal/preferences).
 *
 * Runs server-side so reminders keep going out while Argo Books is closed,
 * which is the whole point of the feature.
 *
 * Schedule: daily at 9:00 AM.
 *   0 9 * * * /usr/bin/php /home/argorobots/public_html/cron/portal_invoice_reminders.php
 *
 * Flags:
 *   --dry-run   Log what would be sent, send nothing, write no reminder rows.
 */

set_time_limit(300);

// CLI/cron only (a web request has REMOTE_ADDR; cron/CLI does not). Without
// this, anyone could trigger a mass customer email run over HTTP.
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../api/portal/portal-helper.php';
require_once __DIR__ . '/lib/run_tracker.php';

global $pdo;

// stage => days past due. Fixed, deliberately not user-configurable.
const PORTAL_REMINDER_STAGES = [1 => 3, 2 => 7, 3 => 14];

// Max reminders across all companies in one run, to stay inside Resend's
// rate limits and the script's time budget.
const PORTAL_REMINDER_RUN_CAP = 200;

// No single merchant may consume the whole run budget.
const PORTAL_REMINDER_COMPANY_CAP = 50;

// Never chase an invoice more than this many days past due. Bounds the scan,
// and stops a long outage or a late re-enable from firing a "final reminder"
// at an invoice from months ago.
const PORTAL_REMINDER_MAX_AGE_DAYS = 45;

// Balances below this are rounding noise (typically a processing-fee residue
// on a partial payment) and are not worth an email.
const PORTAL_REMINDER_MIN_BALANCE = 1.00;

$dryRun = in_array('--dry-run', $argv ?? [], true);

/**
 * Records a failure that happens before the run proper can start, then stops.
 *
 * Writes a failed run to cron_runs so it appears on the admin Cron Activity page.
 * That page is the only place these are actually read: cron mail is not configured,
 * and the daily log cannot be written because it lives in the directory that failed.
 */
function reminders_abort(PDO $pdo, string $message): never
{
    error_log('portal_invoice_reminders: ' . $message);
    try {
        $runId = cron_run_start($pdo, 'portal_invoice_reminders');
        cron_run_finish($pdo, $runId, 'error', $message);
    } catch (Throwable $e) {
        error_log('portal_invoice_reminders: could not record the failure: ' . $e->getMessage());
    }
    exit(1);
}

// ─── Lock file to prevent overlapping runs ───
// A lock that cannot be created is recorded, not swallowed. Exiting silently here
// is indistinguishable from the cron never firing, and with no shell on the server
// that is expensive to diagnose.
$lockDir = __DIR__ . '/logs';
if (!is_dir($lockDir) && !@mkdir($lockDir, 0755, true) && !is_dir($lockDir)) {
    reminders_abort($pdo, "cannot create $lockDir (check permissions)");
}

$lockFile = $lockDir . '/portal_invoice_reminders.lock';
$lock = fopen($lockFile, 'c');
if ($lock === false) {
    reminders_abort($pdo, "cannot open $lockFile (check permissions)");
}

if (!flock($lock, LOCK_EX | LOCK_NB)) {
    // Another run holds the lock. Quiet is correct here.
    exit(0);
}

$logFile = __DIR__ . '/logs/portal_invoice_reminders_' . date('Y-m-d') . '.log';
$logLine = static function (string $msg) use ($logFile): void {
    @file_put_contents($logFile, '[' . date('H:i:s') . '] ' . $msg . "\n", FILE_APPEND);
};

$runId = cron_run_start($pdo, 'portal_invoice_reminders');
$sentThisRun = 0;
$skipped = 0;
$failed = 0;
$scanned = 0;
$perCompany = [];

try {
    $environment = current_environment();
    $minDue = PORTAL_REMINDER_STAGES[1];

    // Candidate selection. Several clauses here are load-bearing:
    //
    //  - pi.due_date > DATE(pc.reminders_enabled_at) is the entire "enabling
    //    never releases a backlog" guarantee. Kept in SQL so there is exactly
    //    one place to get it wrong. reminders_enabled_at IS NOT NULL is a
    //    fail-closed guard for rows where the flag was set by hand.
    //  - pi.environment = ? is mandatory. Sandbox and production share this
    //    database, so without it a sandbox test invoice emails a real customer.
    //  - pc.locked = 0 keeps a company under fraud review from emailing anyone.
    $stmt = $pdo->prepare(
        'SELECT pi.id, pi.company_id, pi.invoice_id, pi.invoice_token,
                pi.customer_name, pi.customer_email, pi.status,
                pi.total_amount, pi.balance_due, pi.currency, pi.due_date,
                pi.pass_processing_fee,
                pc.company_name, pc.owner_email, pc.email_verified_at,
                DATEDIFF(CURDATE(), pi.due_date) AS days_overdue
         FROM portal_invoices pi
         INNER JOIN portal_companies pc ON pc.id = pi.company_id
         WHERE pc.reminders_enabled = 1
           AND pc.is_active = 1
           AND pc.locked = 0
           AND pc.reminders_enabled_at IS NOT NULL
           AND pi.due_date IS NOT NULL
           AND pi.due_date > DATE(pc.reminders_enabled_at)
           AND pi.environment = ?
           AND pi.balance_due >= ?
           AND pi.status NOT IN ("paid", "cancelled", "draft")
           AND pi.customer_email IS NOT NULL AND pi.customer_email <> ""
           AND pi.due_date <= DATE_SUB(CURDATE(), INTERVAL ? DAY)
           AND pi.due_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
         ORDER BY pi.due_date ASC
         LIMIT 1000'
    );
    $stmt->execute([$environment, PORTAL_REMINDER_MIN_BALANCE, $minDue, PORTAL_REMINDER_MAX_AGE_DAYS]);
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $scanned = count($candidates);

    $portalBaseUrl = env('SITE_URL', 'https://argorobots.com');

    foreach ($candidates as $inv) {
        if ($sentThisRun >= PORTAL_REMINDER_RUN_CAP) {
            $logLine("Run cap reached (" . PORTAL_REMINDER_RUN_CAP . "); remaining candidates deferred to tomorrow.");
            break;
        }

        $companyId = (int)$inv['company_id'];
        if (($perCompany[$companyId] ?? 0) >= PORTAL_REMINDER_COMPANY_CAP) {
            continue;
        }

        $invoiceRowId = (int)$inv['id'];
        $daysOverdue = (int)$inv['days_overdue'];

        // Highest eligible stage wins rather than catching up, so a cron that
        // missed a week sends one reminder, not three in a row.
        $stage = null;
        foreach ([3, 2, 1] as $s) {
            if ($daysOverdue >= PORTAL_REMINDER_STAGES[$s]) {
                $stage = $s;
                break;
            }
        }
        if ($stage === null) {
            continue;
        }

        // Stages only ever move forward. An invoice whose due date was edited
        // after a reminder went out can look un-sent by days_overdue alone.
        $prev = $pdo->prepare(
            'SELECT MAX(stage) AS max_stage, MAX(sent_at) AS last_sent
             FROM portal_invoice_reminders WHERE portal_invoice_id = ?'
        );
        $prev->execute([$invoiceRowId]);
        $prevRow = $prev->fetch(PDO::FETCH_ASSOC);

        if ($prevRow && $prevRow['max_stage'] !== null && (int)$prevRow['max_stage'] >= $stage) {
            continue;
        }
        // Minimum 48-hour gap. The natural cadence gaps are 4 and 7 days, so
        // this only ever catches day-boundary and DST edge cases.
        if ($prevRow && !empty($prevRow['last_sent']) && strtotime((string)$prevRow['last_sent']) > time() - 172800) {
            continue;
        }

        if ($dryRun) {
            $logLine("DRY RUN would send stage {$stage} for invoice {$inv['invoice_id']} (company {$companyId}, {$daysOverdue}d overdue) to {$inv['customer_email']}");
            $sentThisRun++;
            $perCompany[$companyId] = ($perCompany[$companyId] ?? 0) + 1;
            continue;
        }

        // Claim the stage BEFORE sending. UNIQUE (portal_invoice_id, stage)
        // means a zero rowCount is another run (or an earlier day) already
        // owning this touch, so this is the whole duplicate-send defence.
        $claim = $pdo->prepare(
            'INSERT IGNORE INTO portal_invoice_reminders
                (portal_invoice_id, company_id, stage, status,
                 due_date_at_send, balance_at_send, recipient_email, created_at)
             VALUES (?, ?, ?, "sending", ?, ?, ?, NOW())'
        );
        $claim->execute([
            $invoiceRowId, $companyId, $stage,
            $inv['due_date'], $inv['balance_due'], $inv['customer_email'],
        ]);
        if ($claim->rowCount() === 0) {
            continue;
        }
        $reminderId = (int)$pdo->lastInsertId();

        // Re-read state immediately before sending. The batch SELECT above can
        // be tens of seconds stale on a full run, which is plenty of time for
        // the customer to have paid online or the merchant to have cancelled.
        $fresh = $pdo->prepare(
            'SELECT status, balance_due, customer_email FROM portal_invoices WHERE id = ? LIMIT 1'
        );
        $fresh->execute([$invoiceRowId]);
        $now = $fresh->fetch(PDO::FETCH_ASSOC);

        $haltReason = null;
        if (!$now) {
            $haltReason = 'not_found';
        } elseif (in_array($now['status'], ['paid', 'cancelled'], true)) {
            $haltReason = $now['status'];
        } elseif ((float)$now['balance_due'] < PORTAL_REMINDER_MIN_BALANCE) {
            $haltReason = 'zero_balance';
        } elseif (empty($now['customer_email'])) {
            $haltReason = 'no_email';
        } else {
            $sup = $pdo->prepare(
                'SELECT 1 FROM email_suppressions
                 WHERE LOWER(email) = LOWER(?) AND context IN ("portal", "all_marketing") LIMIT 1'
            );
            $sup->execute([$now['customer_email']]);
            if ($sup->fetch()) {
                $haltReason = 'suppressed';
            }
        }

        if ($haltReason !== null) {
            $pdo->prepare('UPDATE portal_invoice_reminders SET status = "skipped", halt_reason = ? WHERE id = ?')
                ->execute([$haltReason, $reminderId]);
            $skipped++;
            cron_metric_incr('reminders_skipped');
            $logLine("Skipped invoice {$inv['invoice_id']} stage {$stage}: {$haltReason}");
            continue;
        }

        // Only give the customer a reply-to when the merchant's address is
        // verified, matching the bar every other owner-directed portal mail uses.
        $replyTo = !empty($inv['email_verified_at']) ? (string)($inv['owner_email'] ?? '') : '';

        $result = ['success' => false, 'message' => 'not attempted'];
        try {
            $result = send_invoice_reminder([
                'customerEmail' => $now['customer_email'],
                'customerName' => $inv['customer_name'],
                'companyName' => $inv['company_name'],
                'invoiceId' => $inv['invoice_id'],
                'balanceDue' => $now['balance_due'],
                'currency' => $inv['currency'],
                'dueDate' => $inv['due_date'],
                'invoiceUrl' => rtrim($portalBaseUrl, '/') . '/invoice/' . $inv['invoice_token'],
                'passProcessingFee' => !empty($inv['pass_processing_fee']),
                'stage' => $stage,
                'daysOverdue' => $daysOverdue,
                'replyToEmail' => $replyTo,
            ]);
        } catch (Throwable $e) {
            $result = ['success' => false, 'message' => $e->getMessage()];
        }

        if (!empty($result['success'])) {
            $pdo->prepare('UPDATE portal_invoice_reminders SET status = "sent", sent_at = NOW() WHERE id = ?')
                ->execute([$reminderId]);
            $sentThisRun++;
            $perCompany[$companyId] = ($perCompany[$companyId] ?? 0) + 1;
            cron_metric_incr('reminders_sent');
            cron_metric_incr('stage' . $stage . '_sent');
            $logLine("Sent stage {$stage} for invoice {$inv['invoice_id']} to {$now['customer_email']} ({$daysOverdue}d overdue)");
        } else {
            // Deliberately not retried. The next stage still fires on schedule,
            // so a transient SMTP failure costs one touch instead of risking a
            // duplicate. Retrying is how you end up sending four reminders.
            $pdo->prepare('UPDATE portal_invoice_reminders SET status = "failed", error_message = ? WHERE id = ?')
                ->execute([substr((string)($result['message'] ?? 'unknown'), 0, 255), $reminderId]);
            $failed++;
            cron_metric_incr('reminders_failed');
            $logLine("FAILED stage {$stage} for invoice {$inv['invoice_id']}: " . ($result['message'] ?? 'unknown'));
        }
    }

    cron_metric_incr('invoices_scanned', $scanned);

    $summary = ($dryRun ? '[DRY RUN] ' : '')
        . "Scanned: $scanned, Sent: $sentThisRun, Skipped: $skipped, Failed: $failed";
    echo $summary . "\n";
    $logLine($summary);

    cron_run_finish($pdo, $runId, 'ok', $summary);
} catch (Throwable $e) {
    error_log('portal_invoice_reminders cron error: ' . $e->getMessage());
    $logLine('ERROR: ' . $e->getMessage());
    cron_run_finish($pdo, $runId, 'error', $e->getMessage());
    throw $e;
} finally {
    flock($lock, LOCK_UN);
    fclose($lock);
}
