<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for the header
$page_title = "Referral Link Tracking";
$page_description = "Create and manage referral links to track ad/sponsor performance";

// Generate a per-session CSRF token used by every form on this page.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check — every state-mutating POST must include the session token.
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Reload the page and try again.');
    }

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create') {
            $source_code = trim($_POST['source_code']);
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $target_url = trim($_POST['target_url']);

            $stmt = $pdo->prepare('INSERT INTO referral_links (source_code, name, description, target_url) VALUES (?, ?, ?, ?)');
            $stmt->execute([$source_code, $name, $description, $target_url]);

            $_SESSION['success_message'] = 'Referral link created successfully!';
            header('Location: index.php');
            exit;
        } elseif ($_POST['action'] === 'update') {
            $id = (int)$_POST['id'];
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $target_url = trim($_POST['target_url']);
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            $stmt = $pdo->prepare('UPDATE referral_links SET name = ?, description = ?, target_url = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$name, $description, $target_url, $is_active, $id]);

            $_SESSION['success_message'] = 'Referral link updated successfully!';
            header('Location: index.php');
            exit;
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];

            $stmt = $pdo->prepare('DELETE FROM referral_links WHERE id = ?');
            $stmt->execute([$id]);

            $_SESSION['success_message'] = 'Referral link deleted successfully!';
            header('Location: index.php');
            exit;
        } elseif ($_POST['action'] === 'spend_save') {
            $source_code = trim($_POST['spend_source_code'] ?? '');
            $period_month = trim($_POST['spend_period'] ?? '');
            $amount = (float)($_POST['spend_amount'] ?? 0);
            $currency = trim($_POST['spend_currency'] ?? 'CAD');
            $notes = trim($_POST['spend_notes'] ?? '');

            // Validate period_month: YYYY-MM
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
                $_SESSION['success_message'] = 'Could not save ad spend — check the source, month, and amount.';
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

// Function to get all referral links
function get_referral_links()
{
    global $pdo;
    $query = "
        SELECT
            rl.*,
            COUNT(DISTINCT rv.id) as total_visits,
            SUM(CASE WHEN rv.converted = 1 THEN 1 ELSE 0 END) as conversions
        FROM referral_links rl
        LEFT JOIN referral_visits rv ON rl.source_code = rv.source_code
        GROUP BY rl.id
        ORDER BY total_visits DESC, rl.created_at DESC";

    $stmt = $pdo->query($query);

    $data = [];
    while ($row = $stmt->fetch()) {
        $data[] = $row;
    }

    return $data;
}

// Function to get referral visits by source
function get_visits_by_source($limit = 10)
{
    global $pdo;
    $query = "
        SELECT
            rv.source_code,
            COUNT(*) as visit_count,
            SUM(CASE WHEN rv.converted = 1 THEN 1 ELSE 0 END) as conversions,
            COUNT(DISTINCT rv.ip_address) as unique_visitors
        FROM referral_visits rv
        INNER JOIN referral_links rl ON rl.source_code = rv.source_code
        GROUP BY rv.source_code
        ORDER BY visit_count DESC
        LIMIT ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$limit]);

    $data = [];
    while ($row = $stmt->fetch()) {
        $data[] = $row;
    }

    return $data;
}

// Function to get visits over time by source
function get_visits_over_time($period = 'day', $limit = 30, $source_code = null)
{
    global $pdo;

    $sql_period = '';
    $display_format = '';
    switch ($period) {
        case 'day':
            $sql_period = 'DATE(visited_at)';
            $display_format = 'DATE(visited_at)';
            break;
        case 'week':
            $sql_period = 'YEARWEEK(visited_at)';
            $display_format = 'CONCAT("Week ", WEEK(visited_at), ", ", YEAR(visited_at))';
            break;
        case 'month':
            $sql_period = 'DATE_FORMAT(visited_at, "%Y-%m")';
            $display_format = 'DATE_FORMAT(visited_at, "%b %Y")';
            break;
    }

    if ($source_code) {
        $query = "
            SELECT
                $sql_period as period,
                $display_format as display_period,
                COUNT(*) as count,
                SUM(CASE WHEN rv.converted = 1 THEN 1 ELSE 0 END) as conversions
            FROM referral_visits rv
            INNER JOIN referral_links rl ON rl.source_code = rv.source_code
            WHERE rv.source_code = ?
            GROUP BY period
            ORDER BY period DESC
            LIMIT ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$source_code, $limit]);
    } else {
        $query = "
            SELECT
                $sql_period as period,
                $display_format as display_period,
                COUNT(*) as count,
                SUM(CASE WHEN rv.converted = 1 THEN 1 ELSE 0 END) as conversions
            FROM referral_visits rv
            INNER JOIN referral_links rl ON rl.source_code = rv.source_code
            GROUP BY period
            ORDER BY period DESC
            LIMIT ?";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$limit]);
    }

    $data = [];
    while ($row = $stmt->fetch()) {
        $data[] = $row;
    }

    return $data;
}

// Function to get geographic distribution
function get_referral_countries($limit = 10)
{
    global $pdo;
    $query = "
        SELECT
            rv.country_code,
            COUNT(*) as visit_count,
            SUM(CASE WHEN rv.converted = 1 THEN 1 ELSE 0 END) as conversions
        FROM referral_visits rv
        INNER JOIN referral_links rl ON rl.source_code = rv.source_code
        WHERE rv.country_code IS NOT NULL AND rv.country_code != ''
        GROUP BY rv.country_code
        ORDER BY visit_count DESC
        LIMIT ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$limit]);

    $data = [];
    while ($row = $stmt->fetch()) {
        $data[] = $row;
    }

    return $data;
}

/**
 * Funnel-aggregation helpers
 * --------------------------
 * All of these run against referral_events (created in mysql_schema.sql) and
 * scope to the current environment so sandbox testing doesn't pollute prod stats.
 */

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

    // We compute everything in one query so periods stay in sync between
    // events, spend, and revenue.
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

    // Bind order: ev params, then spend params, then env + optional period for rv.
    $bind = array_merge($params, $params_spend, [$environment], $params_rev_extra);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($bind);
    return $stmt->fetchAll();
}

/**
 * All ad-spend rows for the Spend tab, sorted newest period first.
 */
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

/**
 * Resolve which tab is active. Defaults to overview so the existing admin
 * experience is unchanged for users who haven't seen the new tabs yet.
 */
$allowed_tabs = ['overview', 'funnel', 'spend'];
$current_tab  = $_GET['tab'] ?? 'overview';
if (!in_array($current_tab, $allowed_tabs, true)) {
    $current_tab = 'overview';
}

/** Funnel-tab filter inputs */
$funnel_period_key = $_GET['funnel_period'] ?? '30d';
if (!in_array($funnel_period_key, ['30d', '90d', 'all'], true)) {
    $funnel_period_key = '30d';
}
$funnel_period_start_dt = funnel_period_start($funnel_period_key);

$funnel_source_filter = $_GET['source'] ?? '';
if ($funnel_source_filter !== '' && !preg_match('/^[a-zA-Z0-9_-]+$/', $funnel_source_filter)) {
    $funnel_source_filter = '';
}

// Get statistics
$referral_links = get_referral_links();
$visits_by_source = get_visits_by_source(15);
$period = isset($_GET['period']) ? $_GET['period'] : 'day';
$allowed_periods = ['day', 'week', 'month'];
if (!in_array($period, $allowed_periods)) {
    $period = 'day';
}

$visits_over_time = get_visits_over_time($period, 30);
$referral_countries = get_referral_countries();

// Prepare data for charts
$source_labels = [];
$source_visit_counts = [];
$source_conversion_counts = [];

foreach ($visits_by_source as $item) {
    $source_labels[] = $item['source_code'];
    $source_visit_counts[] = (int)$item['visit_count'];
    $source_conversion_counts[] = (int)$item['conversions'];
}

// Prepare time series data
$time_labels = [];
$time_visit_counts = [];
$time_conversion_counts = [];

$visits_over_time = array_reverse($visits_over_time);
foreach ($visits_over_time as $item) {
    $time_labels[] = isset($item['display_period']) ? $item['display_period'] : $item['period'];
    $time_visit_counts[] = (int)$item['count'];
    $time_conversion_counts[] = (int)$item['conversions'];
}

// Prepare country data
$country_labels = [];
$country_visit_counts = [];
$country_conversion_counts = [];

// Country code to name mapping
$country_name_map = [
    'US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom',
    'AU' => 'Australia', 'DE' => 'Germany', 'FR' => 'France', 'JP' => 'Japan',
    'CN' => 'China', 'IN' => 'India', 'BR' => 'Brazil', 'MX' => 'Mexico',
    'IT' => 'Italy', 'ES' => 'Spain', 'NL' => 'Netherlands', 'SE' => 'Sweden',
    'CH' => 'Switzerland', 'PL' => 'Poland', 'BE' => 'Belgium', 'NO' => 'Norway',
    'AT' => 'Austria', 'DK' => 'Denmark', 'FI' => 'Finland', 'IE' => 'Ireland',
    'NZ' => 'New Zealand', 'SG' => 'Singapore', 'HK' => 'Hong Kong', 'KR' => 'South Korea',
    'RU' => 'Russia', 'ZA' => 'South Africa', 'AR' => 'Argentina', 'CL' => 'Chile'
];

foreach ($referral_countries as $country) {
    $code = $country['country_code'];
    $country_labels[] = $country_name_map[$code] ?? $code;
    $country_visit_counts[] = (int)$country['visit_count'];
    $country_conversion_counts[] = (int)$country['conversions'];
}

// Calculate total stats
$total_visits = array_sum($source_visit_counts);
$total_conversions = array_sum($source_conversion_counts);
$conversion_rate = $total_visits > 0 ? round(($total_conversions / $total_visits) * 100, 1) : 0;

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

    <!-- Tab strip (shared markup + JS with other admin pages) -->
    <div class="section-tabs">
        <button class="section-tab <?php echo $current_tab === 'overview' ? 'active' : ''; ?>" data-tab="overview">Overview</button>
        <button class="section-tab <?php echo $current_tab === 'funnel'   ? 'active' : ''; ?>" data-tab="funnel">Funnel</button>
        <button class="section-tab <?php echo $current_tab === 'spend'    ? 'active' : ''; ?>" data-tab="spend">Spend</button>
    </div>

    <div id="overview" class="tab-content <?php echo $current_tab === 'overview' ? 'active' : ''; ?>">
    <!-- Summary Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Visits</h3>
            <div class="value"><?php echo number_format($total_visits); ?></div>
            <div class="subtext">from referral sources</div>
        </div>
        <div class="stat-card">
            <h3>Total Conversions</h3>
            <div class="value"><?php echo number_format($total_conversions); ?></div>
            <div class="subtext">license purchases</div>
        </div>
        <div class="stat-card">
            <h3>Conversion Rate</h3>
            <div class="value"><?php echo $conversion_rate; ?>%</div>
            <div class="subtext">visit to purchase</div>
        </div>
        <div class="stat-card">
            <h3>Active Sources</h3>
            <div class="value"><?php echo count($visits_by_source); ?></div>
            <div class="subtext">referral sources</div>
        </div>
    </div>

    <!-- Period selection for time series chart -->
    <div class="period-selection">
        <span>Time Period:</span>
        <div class="period-buttons">
            <?php
            $periods = [
                'day' => 'Daily',
                'week' => 'Weekly',
                'month' => 'Monthly'
            ];

            foreach ($periods as $periodKey => $periodName) {
                $activeClass = ($period === $periodKey) ? 'active' : '';
                echo "<a href=\"?period={$periodKey}\" class=\"period-btn {$activeClass}\">{$periodName}</a>";
            }
            ?>
        </div>
    </div>

    <!-- Charts -->
    <div class="chart-row">
        <div class="chart-container">
            <h2>Visits by Source</h2>
            <canvas id="sourceVisitsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Conversions by Source</h2>
            <canvas id="sourceConversionsChart"></canvas>
        </div>
    </div>

    <div class="chart-row">
        <div class="chart-container">
            <h2>Visits Over Time</h2>
            <canvas id="visitsTimeChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Conversions Over Time</h2>
            <canvas id="conversionsTimeChart"></canvas>
        </div>
    </div>

    <div class="chart-row">
        <div class="chart-container">
            <h2>Top Countries</h2>
            <canvas id="countriesChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Conversion Rate by Source</h2>
            <canvas id="conversionRateChart"></canvas>
        </div>
    </div>

    <!-- Referral Links Management -->
    <div class="table-container">
        <div class="table-header-actions">
            <h2>Manage Referral Links</h2>
            <button id="createLinkBtn" class="btn btn-blue">Create New Link</button>
        </div>

        <div class="table-responsive">
            <table data-paginate="25">
                <thead>
                    <tr>
                        <th>Source Code</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Target URL</th>
                        <th>Visits</th>
                        <th>Conversions</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($referral_links as $link): ?>
                        <?php
                        $conv_rate = $link['total_visits'] > 0 ? round(($link['conversions'] / $link['total_visits']) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($link['source_code']); ?></code></td>
                            <td><?php echo htmlspecialchars($link['name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($link['description'], 0, 50)) . (strlen($link['description']) > 50 ? '...' : ''); ?></td>
                            <td><a href="<?php echo htmlspecialchars($link['target_url']); ?>" target="_blank" class="link-preview"><?php echo htmlspecialchars(substr($link['target_url'], 0, 30)) . (strlen($link['target_url']) > 30 ? '...' : ''); ?></a></td>
                            <td><?php echo number_format($link['total_visits']); ?></td>
                            <td><?php echo number_format($link['conversions']); ?></td>
                            <td><?php echo $conv_rate; ?>%</td>
                            <td>
                                <span class="status-badge <?php echo $link['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $link['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button onclick="editLink(<?php echo htmlspecialchars(json_encode($link)); ?>)" class="btn-small btn-blue" title="Edit">Edit</button>
                                <button onclick="deleteLink(<?php echo $link['id']; ?>)" class="btn-small btn-red" title="Delete">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div><!-- /#overview -->

    <div id="funnel" class="tab-content <?php echo $current_tab === 'funnel' ? 'active' : ''; ?>">
    <?php
        $funnel_counts = get_funnel_stage_counts($funnel_period_start_dt, $funnel_source_filter ?: null);
        $per_source = get_funnel_per_source($funnel_period_start_dt, current_environment());

        // Compute aggregate totals + CAC/LTV for the hero cards
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

        // Classify the ratio for color tier
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

        // Build the funnel stages array with computed percentages
        // We hide app_first_run if there are zero events (Phase 3 not yet deployed)
        $stage_defs = [
            ['key' => 'landing',         'label' => 'Landing'],
            ['key' => 'downloads_page',  'label' => 'Downloads page'],
            ['key' => 'download_click',  'label' => 'Download click'],
            ['key' => 'app_first_run',   'label' => 'App first run'],
            ['key' => 'premium_signup',  'label' => 'Premium signup'],
            ['key' => 'premium_paid',    'label' => 'Premium paid'],
        ];
        // Hide app_first_run row entirely if no data yet
        if ($funnel_counts['app_first_run'] === 0) {
            $stage_defs = array_values(array_filter($stage_defs, fn($s) => $s['key'] !== 'app_first_run'));
        }

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

        // Mark the biggest drop-off (smallest retained % among stages where retained is defined)
        $biggest_drop_index = null;
        $biggest_drop_pct = 101;
        foreach ($funnel_stages as $i => $s) {
            if ($s['retained'] !== null && $s['retained'] < $biggest_drop_pct) {
                $biggest_drop_pct = $s['retained'];
                $biggest_drop_index = $i;
            }
        }
    ?>
    <!-- Funnel controls -->
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
               class="funnel-pill <?php echo $funnel_source_filter === '' ? 'active' : ''; ?>">All sources</a>
            <?php foreach ($referral_links as $rl):
                if (!$rl['is_active']) continue;
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

    <!-- Hero metrics -->
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
            <div class="subtext">spend ÷ paying customers</div>
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

    <!-- Funnel bars -->
    <div class="funnel-container">
        <h2>
            Conversion Funnel
            <?php if ($funnel_source_filter !== ''): ?>
                <span class="source-tag"><?php echo htmlspecialchars($funnel_source_filter); ?></span>
            <?php else: ?>
                <span class="source-tag">all sources</span>
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
    </div>

    <!-- Comparison table -->
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
    <?php
        $spend_rows = get_campaign_spend_rows();
        $active_sources_for_spend = array_filter($referral_links, fn($l) => $l['is_active']);
    ?>
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
                    <?php foreach ($referral_links as $rl): if (!$rl['is_active']) continue; ?>
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

<!-- Create/Edit Modal -->
<div id="linkModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Create Referral Link</h2>

        <form id="linkForm" method="POST">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="linkId">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="source_code">Source Code *</label>
                <input type="text" name="source_code" id="source_code" required pattern="[a-zA-Z0-9_-]+" title="Only letters, numbers, hyphens, and underscores allowed">
                <small>Used in URL: ?source=CODE (alphanumeric, hyphens, underscores only)</small>
            </div>

            <div class="form-group">
                <label for="name">Display Name *</label>
                <input type="text" name="name" id="name" required>
                <small>A friendly name for this referral source</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" rows="3"></textarea>
                <small>Optional notes about this referral source</small>
            </div>

            <div class="form-group">
                <label for="target_url">Target URL *</label>
                <input type="url" name="target_url" id="target_url" required>
                <small>The page users will land on (usually your homepage)</small>
            </div>

            <div class="form-group checkbox-group" id="activeCheckboxGroup" style="display: none;">
                <label>
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked>
                    Active
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-blue">Save</button>
                <button type="button" class="btn btn-gray" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    const sourceLabels = <?php echo json_encode($source_labels); ?>;
    const sourceVisitCounts = <?php echo json_encode($source_visit_counts); ?>;
    const sourceConversionCounts = <?php echo json_encode($source_conversion_counts); ?>;
    const timeLabels = <?php echo json_encode($time_labels); ?>;
    const timeVisitCounts = <?php echo json_encode($time_visit_counts); ?>;
    const timeConversionCounts = <?php echo json_encode($time_conversion_counts); ?>;
    const countryLabels = <?php echo json_encode($country_labels); ?>;
    const countryVisitCounts = <?php echo json_encode($country_visit_counts); ?>;
    const csrfToken = <?php echo json_encode($_SESSION['csrf_token']); ?>;

    document.addEventListener('DOMContentLoaded', function() {
        const sourceVisitsEl = document.getElementById('sourceVisitsChart');
        if (!sourceVisitsEl) {
            // Overview-tab charts don't exist on Funnel/Spend tabs.
            return;
        }
        // Visits by Source Chart
        const ctxSourceVisits = sourceVisitsEl.getContext('2d');
        new Chart(ctxSourceVisits, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [{
                    label: 'Visits',
                    data: sourceVisitCounts,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Visits: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });

        // Conversions by Source Chart
        const ctxSourceConversions = document.getElementById('sourceConversionsChart').getContext('2d');
        new Chart(ctxSourceConversions, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [{
                    label: 'Conversions',
                    data: sourceConversionCounts,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Conversions: ${context.raw.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });

        // Visits Over Time Chart
        const ctxVisitsTime = document.getElementById('visitsTimeChart').getContext('2d');
        new Chart(ctxVisitsTime, {
            type: 'line',
            data: {
                labels: timeLabels,
                datasets: [{
                    label: 'Visits',
                    data: timeVisitCounts,
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    borderColor: 'rgba(245, 158, 11, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(245, 158, 11, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Conversions Over Time Chart
        const ctxConversionsTime = document.getElementById('conversionsTimeChart').getContext('2d');
        new Chart(ctxConversionsTime, {
            type: 'line',
            data: {
                labels: timeLabels,
                datasets: [{
                    label: 'Conversions',
                    data: timeConversionCounts,
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Countries Chart
        const ctxCountries = document.getElementById('countriesChart').getContext('2d');
        new Chart(ctxCountries, {
            type: 'bar',
            data: {
                labels: countryLabels,
                datasets: [{
                    label: 'Visits',
                    data: countryVisitCounts,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Conversion Rate by Source Chart
        const conversionRates = sourceLabels.map((label, index) => {
            const visits = sourceVisitCounts[index];
            const conversions = sourceConversionCounts[index];
            return visits > 0 ? (conversions / visits) * 100 : 0;
        });

        const ctxConversionRate = document.getElementById('conversionRateChart').getContext('2d');
        new Chart(ctxConversionRate, {
            type: 'bar',
            data: {
                labels: sourceLabels,
                datasets: [{
                    label: 'Conversion Rate (%)',
                    data: conversionRates,
                    backgroundColor: 'rgba(139, 92, 246, 0.7)',
                    borderColor: 'rgba(139, 92, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1) + '%';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Conversion Rate: ${context.raw.toFixed(2)}%`;
                            }
                        }
                    }
                }
            }
        });
    });

    // Modal Functions
    function openModal() {
        document.getElementById('linkModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('linkModal').style.display = 'none';
        document.getElementById('linkForm').reset();
        document.getElementById('formAction').value = 'create';
        document.getElementById('modalTitle').textContent = 'Create Referral Link';
        document.getElementById('source_code').removeAttribute('readonly');
        document.getElementById('activeCheckboxGroup').style.display = 'none';
    }

    function editLink(link) {
        document.getElementById('formAction').value = 'update';
        document.getElementById('linkId').value = link.id;
        document.getElementById('source_code').value = link.source_code;
        document.getElementById('source_code').setAttribute('readonly', 'readonly');
        document.getElementById('name').value = link.name;
        document.getElementById('description').value = link.description;
        document.getElementById('target_url').value = link.target_url;
        document.getElementById('is_active').checked = link.is_active == 1;
        document.getElementById('activeCheckboxGroup').style.display = 'block';
        document.getElementById('modalTitle').textContent = 'Edit Referral Link';
        openModal();
    }

    function deleteLink(id) {
        if (confirm('Are you sure you want to delete this referral link? This will not delete visit history.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'csrf_token';
            tokenInput.value = csrfToken;
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            form.appendChild(tokenInput);
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    const createLinkBtnEl = document.getElementById('createLinkBtn');
    if (createLinkBtnEl) {
        createLinkBtnEl.addEventListener('click', openModal);
    }

    // Close modal when clicking outside (only if mousedown also started on backdrop)
    let modalMouseDownTarget = null;
    window.addEventListener('mousedown', function(event) {
        modalMouseDownTarget = event.target;
    });
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('linkModal');
        if (modal && event.target === modal && modalMouseDownTarget === modal) {
            closeModal();
        }
        const sModal = document.getElementById('spendModal');
        if (sModal && event.target === sModal && modalMouseDownTarget === sModal) {
            closeSpendModal();
        }
    });

    // -------- Spend-tab JS: modal open/close + edit + delete --------
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
    // On submit, re-enable the source dropdown so its value is included in POST
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

    // Restore scroll position
    if (sessionStorage.getItem('scrollPosition')) {
        window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
        sessionStorage.removeItem('scrollPosition');
    }

    // Save scroll position when clicking period links
    const links = document.querySelectorAll('a[href^="?period="]');
    links.forEach(link => {
        link.addEventListener('click', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });
    });
</script>
<script src="../section-tabs.js"></script>

<?php
// Footer is typically included in admin_header.php or handled separately
?>
