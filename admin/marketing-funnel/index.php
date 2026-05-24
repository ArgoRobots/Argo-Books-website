<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Marketing Funnel";
$page_description = "Conversion funnel and ad-spend tracking for referral campaigns";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// POST handlers — ad-spend create/update + delete only.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Reload the page and try again.');
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'spend_save') {
            $source_code = trim($_POST['spend_source_code'] ?? '');
            $period_month = trim($_POST['spend_period'] ?? '');
            $amount = (float)($_POST['spend_amount'] ?? 0);
            $currency = trim($_POST['spend_currency'] ?? 'CAD');
            $notes = trim($_POST['spend_notes'] ?? '');

            $period_start = null;
            if (preg_match('/^(\d{4})-(\d{2})$/', $period_month, $m)) {
                $period_start = $m[1] . '-' . $m[2] . '-01';
            }
            if ($source_code !== '' && $period_start !== null && $amount >= 0) {
                $stmt = $pdo->prepare(
                    'INSERT INTO campaign_spend (source_code, period_start, amount, currency, notes)
                     VALUES (?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE amount = VALUES(amount), currency = VALUES(currency), notes = VALUES(notes)'
                );
                $stmt->execute([$source_code, $period_start, $amount, $currency, $notes]);
                $_SESSION['success_message'] = 'Ad spend saved.';
            } else {
                $_SESSION['success_message'] = 'Could not save ad spend, check the source, month, and amount.';
            }
            header('Location: index.php?tab=spend');
            exit;
        } elseif ($_POST['action'] === 'spend_delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = $pdo->prepare('DELETE FROM campaign_spend WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['success_message'] = 'Ad spend deleted.';
            header('Location: index.php?tab=spend');
            exit;
        }
    }
}

/**
 * Active referral links, used to populate the Source filter pills and the
 * Source <select> in the ad-spend modal. The funnel + comparison-table data
 * is queried separately by get_funnel_per_source().
 */
function get_active_referral_links(): array
{
    global $pdo;
    $stmt = $pdo->query(
        'SELECT id, source_code, name, is_active
           FROM referral_links
          WHERE is_active = 1
          ORDER BY name ASC'
    );
    return $stmt->fetchAll();
}

/**
 * Returns the start datetime for the active period filter. Periods:
 *   '30d' (default) | '90d' | 'all'
 */
function funnel_period_start(string $period): ?string
{
    switch ($period) {
        case '90d': return date('Y-m-d 00:00:00', strtotime('-90 days'));
        case 'all': return null;
        case '30d':
        default:    return date('Y-m-d 00:00:00', strtotime('-30 days'));
    }
}

/**
 * Count funnel-stage totals for the given period + source filter.
 *
 * Top-of-funnel stages (landing through premium_signup) count distinct
 * visitors so a user firing the same event twice doesn't double-count.
 *
 * premium_paid and premium_churned count distinct subscription_id instead:
 * they're subscription-keyed events that can fire for visitors we never
 * resolved (webhook context), and premium_paid is restricted to the initial
 * payment so renewals/retries don't inflate the "paid" stage above signups.
 */
function get_funnel_stage_counts(?string $period_start, ?string $source_code): array
{
    global $pdo;

    $where_clauses = ['environment = ?'];
    $params = [current_environment()];

    if ($period_start !== null) {
        $where_clauses[] = 'created_at >= ?';
        $params[] = $period_start;
    }
    if ($source_code !== null && $source_code !== '') {
        $where_clauses[] = 'source_code = ?';
        $params[] = $source_code;
    }
    $where = implode(' AND ', $where_clauses);

    $sql = "
        SELECT
          COUNT(DISTINCT CASE WHEN event_type='landing'        THEN visitor_id END) AS landing,
          COUNT(DISTINCT CASE WHEN event_type='downloads_page' THEN visitor_id END) AS downloads_page,
          COUNT(DISTINCT CASE WHEN event_type='download_click' THEN visitor_id END) AS download_click,
          COUNT(DISTINCT CASE WHEN event_type='app_first_run'  THEN visitor_id END) AS app_first_run,
          COUNT(DISTINCT CASE WHEN event_type='premium_signup' THEN visitor_id END) AS premium_signup,
          COUNT(DISTINCT CASE WHEN event_type='premium_paid'
                                AND JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.payment_type')) = 'initial'
                               THEN subscription_id END) AS premium_paid,
          COUNT(DISTINCT CASE WHEN event_type='premium_churned' THEN subscription_id END) AS premium_churned
        FROM referral_events
        WHERE $where";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();

    return [
        'landing'         => (int)($row['landing'] ?? 0),
        'downloads_page'  => (int)($row['downloads_page'] ?? 0),
        'download_click'  => (int)($row['download_click'] ?? 0),
        'app_first_run'   => (int)($row['app_first_run'] ?? 0),
        'premium_signup'  => (int)($row['premium_signup'] ?? 0),
        'premium_paid'    => (int)($row['premium_paid'] ?? 0),
        'premium_churned' => (int)($row['premium_churned'] ?? 0),
    ];
}

/**
 * Per-source totals for the comparison table. Joins event counts, ad spend,
 * and premium subscription payment revenue all in a single result set.
 */
function get_funnel_per_source(?string $period_start, string $environment): array
{
    global $pdo;

    $params = [$environment];
    $event_period_clause = '';
    if ($period_start !== null) {
        $event_period_clause = ' AND re.created_at >= ?';
        $params[] = $period_start;
    }

    $spend_period_clause = '';
    $params_spend = [];
    if ($period_start !== null) {
        $spend_period_clause = 'WHERE period_start >= ?';
        $params_spend[] = date('Y-m-01', strtotime($period_start));
    }

    // Revenue must scope to the same period as events + spend, otherwise the
    // funnel mixes period-scoped events with all-time revenue and inflates LTV.
    $rev_period_clause = '';
    $params_rev_extra = [];
    if ($period_start !== null) {
        $rev_period_clause = ' AND p.created_at >= ?';
        $params_rev_extra[] = $period_start;
    }

    $sql = "
        SELECT
          rl.source_code,
          rl.name,
          COALESCE(ev.landings,    0)      AS landings,
          COALESCE(ev.dl_pages,    0)      AS dl_pages,
          COALESCE(ev.dl_clicks,   0)      AS dl_clicks,
          COALESCE(ev.first_runs,  0)      AS first_runs,
          COALESCE(ev.signups,     0)      AS signups,
          COALESCE(ev.paying,      0)      AS paying,
          COALESCE(ev.churned,     0)      AS churned,
          COALESCE(sp.total_spend, 0)      AS spend,
          COALESCE(rv.total_revenue, 0)    AS revenue
        FROM referral_links rl
        LEFT JOIN (
            SELECT
              source_code,
              COUNT(DISTINCT CASE WHEN event_type='landing'        THEN visitor_id END) AS landings,
              COUNT(DISTINCT CASE WHEN event_type='downloads_page' THEN visitor_id END) AS dl_pages,
              COUNT(DISTINCT CASE WHEN event_type='download_click' THEN visitor_id END) AS dl_clicks,
              COUNT(DISTINCT CASE WHEN event_type='app_first_run'  THEN visitor_id END) AS first_runs,
              COUNT(DISTINCT CASE WHEN event_type='premium_signup' THEN visitor_id END) AS signups,
              COUNT(DISTINCT CASE WHEN event_type='premium_paid'
                                   AND JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.payment_type')) = 'initial'
                                  THEN subscription_id END) AS paying,
              COUNT(DISTINCT CASE WHEN event_type='premium_churned' THEN subscription_id END) AS churned
            FROM referral_events re
            WHERE re.environment = ? $event_period_clause
            GROUP BY source_code
        ) ev ON ev.source_code = rl.source_code
        LEFT JOIN (
            SELECT source_code, SUM(amount) AS total_spend
              FROM campaign_spend
              $spend_period_clause
            GROUP BY source_code
        ) sp ON sp.source_code = rl.source_code
        LEFT JOIN (
            SELECT re2.source_code, SUM(p.amount) AS total_revenue
              FROM referral_events re2
              JOIN premium_subscription_payments p
                ON p.subscription_id = re2.subscription_id
               AND p.status = 'completed'
             WHERE re2.event_type = 'premium_signup'
               AND re2.environment = ?
               $rev_period_clause
             GROUP BY re2.source_code
        ) rv ON rv.source_code = rl.source_code
        WHERE rl.is_active = 1
        ORDER BY landings DESC, rl.created_at DESC";

    $bind = array_merge($params, $params_spend, [$environment], $params_rev_extra);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($bind);
    return $stmt->fetchAll();
}

/**
 * Top landing pages by distinct-visitor count, for the All-traffic funnel
 * breakdown. Trailing query/hash strings are stripped server-side via a
 * SUBSTRING_INDEX so `/?ref=foo` and `/` collapse into the same bucket.
 */
function get_landing_page_breakdown(?string $period_start, string $environment): array
{
    global $pdo;

    $where = ['environment = ?', "event_type = 'landing'"];
    $params = [$environment];
    if ($period_start !== null) {
        $where[] = 'created_at >= ?';
        $params[] = $period_start;
    }
    $where_sql = implode(' AND ', $where);

    $sql = "
        SELECT
            SUBSTRING_INDEX(SUBSTRING_INDEX(page_url, '?', 1), '#', 1) AS clean_path,
            COUNT(DISTINCT visitor_id) AS visitors
        FROM referral_events
        WHERE $where_sql
        GROUP BY clean_path
        ORDER BY visitors DESC
        LIMIT 20";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Map a request path to a human-readable label for the landing-pages chart.
 */
function friendly_landing_label(?string $path): string
{
    $p = strtolower(trim((string)$path));
    if ($p === '' || $p === '/' || $p === '/index.php') return 'Home';
    if ($p === '/downloads/' || $p === '/downloads') return 'Downloads page';

    if (preg_match('#^/compare/argo-books-vs-([a-z0-9-]+)/?$#', $p, $m)) {
        return ucwords(str_replace('-', ' ', $m[1])) . ' comparison';
    }
    return $path;
}

function get_campaign_spend_rows(): array
{
    global $pdo;
    $stmt = $pdo->query(
        'SELECT id, source_code, period_start, amount, currency, notes, updated_at
           FROM campaign_spend
          ORDER BY period_start DESC, source_code ASC'
    );
    return $stmt->fetchAll();
}

// Resolve which tab is active. Default to funnel.
$allowed_tabs = ['funnel', 'spend'];
$current_tab  = $_GET['tab'] ?? 'funnel';
if (!in_array($current_tab, $allowed_tabs, true)) {
    $current_tab = 'funnel';
}

$funnel_period_key = $_GET['funnel_period'] ?? '30d';
if (!in_array($funnel_period_key, ['30d', '90d', 'all'], true)) {
    $funnel_period_key = '30d';
}
$funnel_period_start_dt = funnel_period_start($funnel_period_key);

$funnel_source_filter = $_GET['source'] ?? '';
if ($funnel_source_filter !== '' && !preg_match('/^[a-zA-Z0-9_-]+$/', $funnel_source_filter)) {
    $funnel_source_filter = '';
}

$referral_links = get_active_referral_links();

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message">
            <?php
            echo htmlspecialchars($_SESSION['success_message']);
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="section-tabs">
        <button class="section-tab <?php echo $current_tab === 'funnel' ? 'active' : ''; ?>" data-tab="funnel">Funnel</button>
        <button class="section-tab <?php echo $current_tab === 'spend'  ? 'active' : ''; ?>" data-tab="spend">Spend</button>
    </div>

    <div id="funnel" class="tab-content <?php echo $current_tab === 'funnel' ? 'active' : ''; ?>">
    <?php
        $funnel_counts = get_funnel_stage_counts($funnel_period_start_dt, $funnel_source_filter ?: null);
        $per_source = get_funnel_per_source($funnel_period_start_dt, current_environment());

        // Landing-page breakdown: only meaningful for "All traffic" since a
        // specific tracked source usually points at a single destination page.
        $landing_breakdown = [];
        $landing_chart_labels = [];
        $landing_chart_counts = [];
        if ($funnel_source_filter === '') {
            $rows = get_landing_page_breakdown($funnel_period_start_dt, current_environment());
            $top = array_slice($rows, 0, 7);
            $rest = array_slice($rows, 7);
            $other_total = array_sum(array_map(fn($r) => (int)$r['visitors'], $rest));
            foreach ($top as $r) {
                $landing_breakdown[] = [
                    'label'    => friendly_landing_label($r['clean_path']),
                    'visitors' => (int)$r['visitors'],
                ];
            }
            if ($other_total > 0) {
                $landing_breakdown[] = ['label' => 'Other', 'visitors' => $other_total];
            }
            foreach ($landing_breakdown as $r) {
                $landing_chart_labels[] = $r['label'];
                $landing_chart_counts[] = $r['visitors'];
            }
        }

        $total_spend = 0.0;
        $total_revenue = 0.0;
        $total_paying = 0;
        foreach ($per_source as $row) {
            if ($funnel_source_filter !== '' && $row['source_code'] !== $funnel_source_filter) {
                continue;
            }
            $total_spend   += (float)$row['spend'];
            $total_revenue += (float)$row['revenue'];
            $total_paying  += (int)$row['paying'];
        }
        $cac = $total_paying > 0 ? $total_spend / $total_paying : null;
        $ltv = $total_paying > 0 ? $total_revenue / $total_paying : null;
        $ltv_cac = ($cac !== null && $cac > 0 && $ltv !== null) ? $ltv / $cac : null;

        $ratio_class = 'ratio-organic';
        $ratio_suffix = 'free';
        if ($total_spend > 0) {
            if ($ltv_cac === null) {
                $ratio_class = 'ratio-losing';
                $ratio_suffix = 'no customers';
            } elseif ($ltv_cac < 1.0) {
                $ratio_class = 'ratio-losing';
                $ratio_suffix = 'losing money';
            } elseif ($ltv_cac < 3.0) {
                $ratio_class = 'ratio-marginal';
                $ratio_suffix = 'marginal';
            } else {
                $ratio_class = 'ratio-profitable';
                $ratio_suffix = 'profitable';
            }
        }

        $stage_defs = [
            ['key' => 'landing',         'label' => 'Landing'],
            ['key' => 'downloads_page',  'label' => 'Downloads page'],
            ['key' => 'download_click',  'label' => 'Download click'],
            ['key' => 'app_first_run',   'label' => 'App first run'],
            ['key' => 'premium_signup',  'label' => 'Premium signup'],
            ['key' => 'premium_paid',    'label' => 'Premium paid'],
        ];

        $top_count = max(1, $funnel_counts[$stage_defs[0]['key']]);
        $funnel_stages = [];
        $prev_count = null;
        foreach ($stage_defs as $sd) {
            $c = (int)$funnel_counts[$sd['key']];
            $pct_of_top = $top_count > 0 ? round(($c / $top_count) * 100, 1) : 0;
            $retained = ($prev_count !== null && $prev_count > 0) ? round(($c / $prev_count) * 100, 1) : null;
            $lost     = ($retained !== null) ? round(100 - $retained, 1) : null;
            $funnel_stages[] = [
                'key' => $sd['key'], 'label' => $sd['label'], 'count' => $c,
                'pct_of_top' => $pct_of_top, 'retained' => $retained, 'lost' => $lost,
            ];
            $prev_count = $c;
        }

        $biggest_drop_index = null;
        $biggest_drop_pct = 101;
        foreach ($funnel_stages as $i => $s) {
            if ($s['retained'] !== null && $s['retained'] < $biggest_drop_pct) {
                $biggest_drop_pct = $s['retained'];
                $biggest_drop_index = $i;
            }
        }
    ?>
    <div class="funnel-controls">
        <span class="control-label">Period:</span>
        <div class="funnel-pill-row">
            <?php foreach (['30d' => 'Last 30 days', '90d' => 'Last 90 days', 'all' => 'All time'] as $pkey => $plabel):
                $href_params = ['tab' => 'funnel', 'funnel_period' => $pkey];
                if ($funnel_source_filter !== '') $href_params['source'] = $funnel_source_filter;
                $href = 'index.php?' . http_build_query($href_params);
            ?>
                <a href="<?php echo htmlspecialchars($href); ?>"
                   class="funnel-pill <?php echo $funnel_period_key === $pkey ? 'active' : ''; ?>">
                    <?php echo $plabel; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <span class="control-label" style="margin-left:auto;">Source:</span>
        <div class="funnel-pill-row">
            <a href="<?php echo htmlspecialchars('index.php?' . http_build_query(['tab' => 'funnel', 'funnel_period' => $funnel_period_key])); ?>"
               class="funnel-pill <?php echo $funnel_source_filter === '' ? 'active' : ''; ?>">All traffic</a>
            <?php foreach ($referral_links as $rl):
                $href = 'index.php?' . http_build_query([
                    'tab' => 'funnel', 'funnel_period' => $funnel_period_key, 'source' => $rl['source_code']
                ]);
            ?>
                <a href="<?php echo htmlspecialchars($href); ?>"
                   class="funnel-pill <?php echo $funnel_source_filter === $rl['source_code'] ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($rl['source_code']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card hero">
            <h3>Ad Spend</h3>
            <div class="value">$<?php echo number_format($total_spend, 2); ?></div>
            <div class="subtext"><?php echo $funnel_period_key === 'all' ? 'all time' : 'in selected period'; ?></div>
        </div>
        <div class="stat-card hero">
            <h3>Paying Customers</h3>
            <div class="value"><?php echo number_format($total_paying); ?></div>
            <div class="subtext">initial Premium payments</div>
        </div>
        <div class="stat-card hero">
            <h3>Customer Acquisition Cost (CAC)</h3>
            <div class="value"><?php echo $cac !== null ? '$' . number_format($cac, 2) : '—'; ?></div>
            <div class="subtext">spend &divide; paying customers</div>
        </div>
        <div class="stat-card hero <?php echo $ratio_class; ?>">
            <h3>Lifetime Value : CAC</h3>
            <div class="value">
                <?php
                    if ($total_spend == 0 && $total_paying > 0) {
                        echo '∞';
                    } elseif ($ltv_cac !== null) {
                        echo number_format($ltv_cac, 2) . '×';
                    } else {
                        echo '—';
                    }
                ?>
                <span class="suffix"><?php echo htmlspecialchars($ratio_suffix); ?></span>
            </div>
            <div class="subtext">
                LTV: <?php echo $ltv !== null ? '$' . number_format($ltv, 2) : '—'; ?>
            </div>
        </div>
    </div>

    <div class="funnel-container">
        <h2>
            Conversion Funnel
            <?php if ($funnel_source_filter !== ''): ?>
                <span class="source-tag"><?php echo htmlspecialchars($funnel_source_filter); ?></span>
            <?php else: ?>
                <span class="source-tag">all traffic, including visitors with no referral link</span>
            <?php endif; ?>
        </h2>

        <div class="funnel funnel-bars">
            <?php foreach ($funnel_stages as $i => $stage): ?>
                <div class="funnel-row" data-stage="<?php echo htmlspecialchars($stage['key']); ?>">
                    <div class="funnel-label"><?php echo htmlspecialchars($stage['label']); ?></div>
                    <div class="funnel-bar-wrap">
                        <div class="funnel-bar funnel-bar-<?php echo htmlspecialchars($stage['key']); ?>"
                             style="--target-pct: <?php echo $stage['pct_of_top']; ?>%"></div>
                    </div>
                    <div class="funnel-count"><?php echo number_format($stage['count']); ?></div>
                </div>
                <?php if ($i + 1 < count($funnel_stages) && $funnel_stages[$i+1]['retained'] !== null):
                    $next = $funnel_stages[$i+1];
                    $is_biggest = ($biggest_drop_index === $i + 1);
                ?>
                    <div class="funnel-dropoff <?php echo $is_biggest ? 'biggest' : ''; ?>">
                        <span class="kept"><?php echo $next['retained']; ?>% retained</span>
                        <span class="sep">·</span>
                        <span class="lost"><?php echo $next['lost']; ?>% left</span>
                        <?php if ($is_biggest): ?>
                            <span class="sep">·</span>
                            <span class="kept">biggest drop-off</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($landing_breakdown)): ?>
            <div class="landing-breakdown">
                <h3>Where landings come from</h3>
                <div class="landing-breakdown-body">
                    <div class="landing-breakdown-chart">
                        <canvas id="landingPagesChart"></canvas>
                    </div>
                    <ul class="landing-breakdown-list">
                        <?php $total_landings = array_sum($landing_chart_counts); ?>
                        <?php foreach ($landing_breakdown as $i => $row):
                            $pct = $total_landings > 0 ? round(($row['visitors'] / $total_landings) * 100, 1) : 0;
                        ?>
                            <li>
                                <span class="swatch" data-swatch-idx="<?php echo $i; ?>"></span>
                                <span class="lbl"><?php echo htmlspecialchars($row['label']); ?></span>
                                <span class="pct"><?php echo $pct; ?>%</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <div class="table-header-actions">
            <h2>Compare all sources</h2>
        </div>
        <div class="table-responsive">
            <table class="compare-table" data-paginate="25">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Landings</th>
                        <th>Pages</th>
                        <th>Clicks</th>
                        <th>Signups</th>
                        <th>Paying</th>
                        <th>Spend</th>
                        <th>Revenue</th>
                        <th>CAC</th>
                        <th>LTV</th>
                        <th>LTV:CAC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($per_source as $r):
                        $rspend = (float)$r['spend'];
                        $rrev   = (float)$r['revenue'];
                        $rpaying = (int)$r['paying'];
                        $rcac = $rpaying > 0 ? $rspend / $rpaying : null;
                        $rltv = $rpaying > 0 ? $rrev / $rpaying : null;
                        $rratio = ($rcac !== null && $rcac > 0 && $rltv !== null) ? $rltv / $rcac : null;
                        $rclass = 'organic';
                        if ($rspend > 0) {
                            if ($rratio === null) $rclass = 'losing';
                            elseif ($rratio < 1.0) $rclass = 'losing';
                            elseif ($rratio < 3.0) $rclass = 'marginal';
                            else $rclass = 'profitable';
                        }
                        $row_href = 'index.php?' . http_build_query([
                            'tab' => 'funnel', 'funnel_period' => $funnel_period_key, 'source' => $r['source_code']
                        ]);
                    ?>
                        <tr onclick="window.location.href='<?php echo htmlspecialchars($row_href); ?>'">
                            <td><code><?php echo htmlspecialchars($r['source_code']); ?></code></td>
                            <td><?php echo number_format((int)$r['landings']); ?></td>
                            <td><?php echo number_format((int)$r['dl_pages']); ?></td>
                            <td><?php echo number_format((int)$r['dl_clicks']); ?></td>
                            <td><?php echo number_format((int)$r['signups']); ?></td>
                            <td><?php echo number_format($rpaying); ?></td>
                            <td>$<?php echo number_format($rspend, 2); ?></td>
                            <td>$<?php echo number_format($rrev, 2); ?></td>
                            <td><?php echo $rcac !== null ? '$' . number_format($rcac, 2) : '—'; ?></td>
                            <td><?php echo $rltv !== null ? '$' . number_format($rltv, 2) : '—'; ?></td>
                            <td class="ratio-cell <?php echo $rclass; ?>">
                                <?php
                                    if ($rspend == 0 && $rpaying > 0) echo '∞';
                                    elseif ($rratio !== null) echo number_format($rratio, 2) . '×';
                                    else echo '—';
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div><!-- /#funnel -->

    <div id="spend" class="tab-content <?php echo $current_tab === 'spend' ? 'active' : ''; ?>">
    <?php $spend_rows = get_campaign_spend_rows(); ?>
    <div class="table-container">
        <div class="spend-actions">
            <h2 style="margin:0;">Ad Spend</h2>
            <button id="addSpendBtn" class="btn btn-blue">Add ad spend</button>
        </div>
        <p class="subtext" style="margin-top:0;">
            Enter what you actually paid each ad platform per month. The Funnel tab uses this to compute
            Customer Acquisition Cost (CAC) and the Lifetime Value : CAC ratio.
        </p>

        <?php if (empty($spend_rows)): ?>
            <div class="empty-state">
                <p>No ad spend recorded yet.</p>
                <p>Add a month of spend to start seeing CAC and LTV:CAC on the Funnel tab.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table data-paginate="25">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Notes</th>
                            <th>Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spend_rows as $sr): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($sr['source_code']); ?></code></td>
                                <td><?php echo htmlspecialchars(date('F Y', strtotime($sr['period_start']))); ?></td>
                                <td>$<?php echo number_format((float)$sr['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($sr['currency']); ?></td>
                                <td><?php echo htmlspecialchars(substr($sr['notes'] ?? '', 0, 60)); ?></td>
                                <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($sr['updated_at']))); ?></td>
                                <td class="action-buttons">
                                    <button class="btn-small btn-blue"
                                            onclick='editSpend(<?php echo json_encode($sr); ?>)'>Edit</button>
                                    <button class="btn-small btn-red"
                                            onclick="deleteSpend(<?php echo (int)$sr['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    </div><!-- /#spend -->
</div>

<!-- Ad spend modal -->
<div id="spendModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeSpendModal()">&times;</span>
        <h2 id="spendModalTitle">Add ad spend</h2>
        <form id="spendForm" method="POST" action="index.php">
            <input type="hidden" name="action" value="spend_save">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="spend_source_code">Source *</label>
                <select name="spend_source_code" id="spend_source_code" required
                        style="width:100%; padding:10px 12px; border:1px solid var(--gray-input-border); border-radius:6px; font-size:14px;">
                    <option value="">-- choose a source --</option>
                    <?php foreach ($referral_links as $rl): ?>
                        <option value="<?php echo htmlspecialchars($rl['source_code']); ?>">
                            <?php echo htmlspecialchars($rl['source_code']) . ' (' . htmlspecialchars($rl['name']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>Pick a referral link the spend should be attributed to.</small>
            </div>
            <div class="form-group">
                <label for="spend_period">Month *</label>
                <input type="month" name="spend_period" id="spend_period" required>
                <small>Spend is tracked one calendar month at a time.</small>
            </div>
            <div class="form-group">
                <label for="spend_amount">Amount *</label>
                <input type="number" name="spend_amount" id="spend_amount" step="0.01" min="0" required>
                <small>Total ad-platform spend for the chosen source + month.</small>
            </div>
            <div class="form-group">
                <label for="spend_currency">Currency</label>
                <input type="text" name="spend_currency" id="spend_currency" maxlength="3" value="CAD">
            </div>
            <div class="form-group">
                <label for="spend_notes">Notes</label>
                <textarea name="spend_notes" id="spend_notes" rows="2"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-blue">Save</button>
                <button type="button" class="btn btn-gray" onclick="closeSpendModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const csrfToken = <?php echo json_encode($_SESSION['csrf_token']); ?>;

    // Spend modal open/close + edit + delete
    function openSpendModal() {
        const m = document.getElementById('spendModal');
        if (m) m.style.display = 'block';
    }
    window.closeSpendModal = function() {
        const m = document.getElementById('spendModal');
        if (!m) return;
        m.style.display = 'none';
        document.getElementById('spendForm').reset();
        document.getElementById('spend_source_code').removeAttribute('disabled');
        document.getElementById('spendModalTitle').textContent = 'Add ad spend';
    };
    window.editSpend = function(row) {
        document.getElementById('spend_source_code').value = row.source_code;
        // Disabled so the user can't repoint an existing row to a different source
        // (the UNIQUE key on source_code+period_start would then collide).
        document.getElementById('spend_source_code').setAttribute('disabled', 'disabled');
        const periodStr = (row.period_start || '').slice(0, 7); // YYYY-MM
        document.getElementById('spend_period').value = periodStr;
        document.getElementById('spend_amount').value = row.amount;
        document.getElementById('spend_currency').value = row.currency;
        document.getElementById('spend_notes').value = row.notes || '';
        document.getElementById('spendModalTitle').textContent = 'Edit ad spend';
        openSpendModal();
    };
    window.deleteSpend = function(id) {
        if (!confirm('Delete this ad-spend entry?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?tab=spend';
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = 'csrf_token';
        tokenInput.value = csrfToken;
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'spend_delete';
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(tokenInput);
        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    };
    // Re-enable the source dropdown on submit so its value is included in POST
    const spendFormEl = document.getElementById('spendForm');
    if (spendFormEl) {
        spendFormEl.addEventListener('submit', function() {
            document.getElementById('spend_source_code').removeAttribute('disabled');
        });
    }
    const addSpendBtnEl = document.getElementById('addSpendBtn');
    if (addSpendBtnEl) {
        addSpendBtnEl.addEventListener('click', openSpendModal);
    }

    // Close modal when clicking outside (only if mousedown also started on backdrop)
    let modalMouseDownTarget = null;
    window.addEventListener('mousedown', function(event) {
        modalMouseDownTarget = event.target;
    });
    window.addEventListener('click', function(event) {
        const sModal = document.getElementById('spendModal');
        if (sModal && event.target === sModal && modalMouseDownTarget === sModal) {
            closeSpendModal();
        }
    });

    // Restore scroll position
    if (sessionStorage.getItem('scrollPosition')) {
        window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
        sessionStorage.removeItem('scrollPosition');
    }

    // Save scroll position when clicking funnel filter pills so the page
    // reload doesn't jump back to the top.
    document.querySelectorAll('.funnel-pill').forEach(link => {
        link.addEventListener('click', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
    });

    // Landing-page breakdown doughnut (only rendered on All-traffic funnel).
    (function () {
        const canvas = document.getElementById('landingPagesChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = <?php echo json_encode($landing_chart_labels, JSON_UNESCAPED_SLASHES); ?>;
        const counts = <?php echo json_encode($landing_chart_counts); ?>;
        if (!labels.length) return;

        // Reuse the palette other admin charts use so themes stay consistent.
        const palette = [
            'rgba(59, 130, 246, 0.85)',   // blue
            'rgba(139, 92, 246, 0.85)',   // purple
            'rgba(16, 185, 129, 0.85)',   // emerald
            'rgba(245, 158, 11, 0.85)',   // amber
            'rgba(239, 68, 68, 0.85)',    // red
            'rgba(14, 165, 233, 0.85)',   // sky
            'rgba(168, 85, 247, 0.85)',   // violet
            'rgba(107, 114, 128, 0.85)',  // gray (Other)
        ];
        const colors = labels.map((_, i) => palette[i % palette.length]);

        document.querySelectorAll('.landing-breakdown-list .swatch').forEach(el => {
            const idx = parseInt(el.getAttribute('data-swatch-idx'), 10);
            if (!Number.isNaN(idx) && colors[idx]) {
                el.style.backgroundColor = colors[idx];
            }
        });

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{ data: counts, backgroundColor: colors, borderWidth: 0 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '55%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((ctx.parsed / total) * 100).toFixed(1) : 0;
                                return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
