<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Cron Activity';
$page_description = "What each cron job has been doing.";

// ─── Time range ─────────────────────────────────────────────────────────────
$range = $_GET['range'] ?? '7d';
$rangeMap = [
    'today' => ['label' => 'Today',        'interval' => '1 DAY'],
    '7d'    => ['label' => 'Last 7 days',  'interval' => '7 DAY'],
    '30d'   => ['label' => 'Last 30 days', 'interval' => '30 DAY'],
    'all'   => ['label' => 'All time',     'interval' => null],
];
if (!isset($rangeMap[$range])) $range = '7d';
$rangeLabel = $rangeMap[$range]['label'];
$rangeInterval = $rangeMap[$range]['interval'];

// ─── Per-cron display config ────────────────────────────────────────────────
// Each entry: ordered metric keys with friendly labels. The page sums the
// metric across all runs in the time range. Crons not yet writing to
// cron_runs will show "No runs in range" with zeros across the board.
$cronConfig = [
    'outreach_pipeline' => [
        'label'     => 'Outreach Pipeline',
        'frequency' => 'every hour',
        'metrics'   => [
            'leads_discovered'          => 'Leads discovered',
            'first_emails_sent'         => 'First emails sent',
            'followups_sent'            => 'Follow-ups sent',
            'drafts_generated'          => 'Drafts generated',
            'followup_drafts_generated' => 'Follow-up drafts',
            'ab_tests_promoted'         => 'A/B tests promoted',
            'shopify_rejected'          => 'Shopify stores rejected',
        ],
        'expected_interval_hours' => 2,
    ],
    'subscription_renewal' => [
        'label'     => 'Subscription Renewal',
        'frequency' => 'daily',
        'metrics'   => [
            'renewals_succeeded'      => 'Renewals successful',
            'renewals_failed'         => 'Renewals failed',
            'subscriptions_suspended' => 'Subscriptions suspended',
            'subscriptions_expired'   => 'Subscriptions expired',
        ],
        'expected_interval_hours' => 48,
    ],
    'account_purge' => [
        'label'     => 'Account Purge',
        'frequency' => 'daily',
        'metrics'   => [
            'accounts_deleted' => 'Accounts deleted',
        ],
        'expected_interval_hours' => 48,
    ],
    'reply_checker' => [
        'label'     => 'Reply Checker',
        'frequency' => 'hourly',
        'metrics'   => [
            'replies_matched' => 'Replies matched',
            'emails_scanned'  => 'Emails scanned',
        ],
        'expected_interval_hours' => 2,
    ],
    'refund_cooling_off_promoter' => [
        'label'     => 'Refund Cooling-Off Promoter',
        'frequency' => 'every minute',
        'metrics'   => [
            'refunds_promoted'       => 'Refunds promoted',
            'refunds_auto_cancelled' => 'Refunds auto-cancelled',
        ],
        'expected_interval_hours' => 1,
    ],
    'refund_stale_processing_reconcile' => [
        'label'     => 'Refund Stale Processing',
        'frequency' => 'every 5 minutes',
        'metrics'   => [
            'refunds_reconciled' => 'Refunds reconciled',
        ],
        'expected_interval_hours' => 1,
    ],
    'refund_stale_request_cleanup' => [
        'label'     => 'Refund Stale Request Cleanup',
        'frequency' => 'hourly',
        'metrics'   => [
            'requests_cancelled' => 'Requests cancelled',
        ],
        'expected_interval_hours' => 2,
    ],
    'refund_velocity_baseline_recompute' => [
        'label'     => 'Refund Velocity Baselines',
        'frequency' => 'nightly',
        'metrics'   => [
            'baselines_recomputed' => 'Baselines recomputed',
        ],
        'expected_interval_hours' => 36,
    ],
];

// ─── Aggregate runs in the time range ───────────────────────────────────────
$intervalClause = $rangeInterval ? "started_at >= DATE_SUB(NOW(), INTERVAL $rangeInterval)" : '1=1';
$rowsByCron = [];
try {
    $stmt = $pdo->query("SELECT cron_name, status, started_at, completed_at, metrics, error_message
        FROM cron_runs
        WHERE $intervalClause
        ORDER BY started_at DESC");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $rowsByCron[$r['cron_name']][] = $r;
    }
} catch (PDOException $e) {
    $tableMissing = true;
}

// Latest run across all time (even outside the range) — used for the status
// pill so a card without recent activity still tells you when it last ran.
$latestByCron = [];
try {
    $stmt = $pdo->query("SELECT t1.cron_name, t1.started_at, t1.completed_at, t1.status, t1.error_message, t1.metrics
        FROM cron_runs t1
        INNER JOIN (SELECT cron_name, MAX(id) AS max_id FROM cron_runs GROUP BY cron_name) t2
          ON t1.id = t2.max_id");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $latestByCron[$r['cron_name']] = $r;
    }
} catch (PDOException $e) {
    // table missing — fall through to empty state
}

// ─── Helpers ────────────────────────────────────────────────────────────────
function fmt_int($n) {
    return number_format((int) $n);
}

function relative_time_ago($datetime) {
    if (!$datetime) return 'never';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    return floor($diff / 86400) . ' days ago';
}

function cron_status_pill($latest, $expectedIntervalHours) {
    if (!$latest) return ['class' => 'status-stale', 'label' => 'No runs'];
    if ($latest['status'] === 'error') return ['class' => 'status-error', 'label' => 'Error'];
    if ($latest['status'] === 'running') return ['class' => 'status-running', 'label' => 'Running'];
    $ageHours = (time() - strtotime($latest['started_at'])) / 3600;
    if ($ageHours > $expectedIntervalHours * 2) return ['class' => 'status-stale', 'label' => 'Stale'];
    return ['class' => 'status-ok', 'label' => 'OK'];
}

function aggregate_metrics($runs, $metricKeys) {
    $totals = array_fill_keys(array_keys($metricKeys), 0);
    foreach ($runs as $r) {
        if (empty($r['metrics'])) continue;
        $m = json_decode($r['metrics'], true);
        if (!is_array($m)) continue;
        foreach ($metricKeys as $key => $_) {
            if (isset($m[$key]) && is_numeric($m[$key])) {
                $totals[$key] += (int) $m[$key];
            }
        }
    }
    return $totals;
}

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="page-header">
    <div>
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <p class="text-muted"><?= htmlspecialchars($page_description) ?></p>
    </div>
    <div class="range-selector">
        <?php foreach ($rangeMap as $key => $r): ?>
            <a href="?range=<?= $key ?>" class="range-btn <?= $range === $key ? 'active' : '' ?>">
                <?= htmlspecialchars($r['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (!empty($tableMissing)): ?>
    <div class="empty-state">
        <strong>cron_runs table not found.</strong>
        Run the SQL in mysql_schema.sql to create it. The page will populate once any cron has run.
    </div>
<?php else: ?>
    <div class="cron-cards">
    <?php foreach ($cronConfig as $cronName => $cfg):
        $runs = $rowsByCron[$cronName] ?? [];
        $latest = $latestByCron[$cronName] ?? null;
        $totals = aggregate_metrics($runs, $cfg['metrics']);
        $pill = cron_status_pill($latest, $cfg['expected_interval_hours']);
        $hasErrorInRange = false;
        foreach ($runs as $r) if ($r['status'] === 'error') { $hasErrorInRange = true; break; }
    ?>
        <div class="cron-card">
            <div class="cron-card-header">
                <div>
                    <h2><?= htmlspecialchars($cfg['label']) ?></h2>
                    <div class="cron-meta">
                        <span class="cron-freq"><?= htmlspecialchars($cfg['frequency']) ?></span>
                        <span class="cron-runs"><?= count($runs) ?> runs in range</span>
                        <span class="cron-last">last: <?= htmlspecialchars(relative_time_ago($latest['started_at'] ?? null)) ?></span>
                    </div>
                </div>
                <span class="cron-pill <?= $pill['class'] ?>"><?= htmlspecialchars($pill['label']) ?></span>
            </div>

            <?php if (empty($runs)): ?>
                <div class="cron-empty">No runs recorded in <?= htmlspecialchars(strtolower($rangeLabel)) ?>.</div>
            <?php else: ?>
                <div class="cron-metrics">
                    <?php foreach ($cfg['metrics'] as $key => $label): ?>
                        <div class="cron-metric">
                            <div class="metric-value"><?= fmt_int($totals[$key]) ?></div>
                            <div class="metric-label"><?= htmlspecialchars($label) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($latest): ?>
                <details class="cron-detail">
                    <summary>Last run detail · <?= htmlspecialchars($latest['started_at']) ?> · <?= htmlspecialchars($latest['status']) ?></summary>
                    <?php if (!empty($latest['error_message'])): ?>
                        <pre class="cron-error"><?= htmlspecialchars($latest['error_message']) ?></pre>
                    <?php endif; ?>
                    <?php
                        $latestMetrics = !empty($latest['metrics']) ? json_decode($latest['metrics'], true) : null;
                        if (is_array($latestMetrics) && !empty($latestMetrics)):
                    ?>
                        <table class="cron-metric-table">
                            <?php foreach ($latestMetrics as $k => $v): ?>
                                <tr>
                                    <td class="k"><?= htmlspecialchars((string) $k) ?></td>
                                    <td class="v"><?= htmlspecialchars(is_scalar($v) ? (string) $v : json_encode($v)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php elseif ($latest['status'] === 'ok'): ?>
                        <p class="text-muted">Last run completed with no recorded metrics.</p>
                    <?php endif; ?>
                </details>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
</body>
</html>
