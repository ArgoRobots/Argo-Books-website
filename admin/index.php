<?php
session_start();
require_once '../db_connect.php';
require_once __DIR__ . '/../resources/icons.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get database connection
$db = get_db_connection();

// --- Revenue & Subscription Queries ---

// Revenue & License Queries
$total_subscription_revenue = 0;
$monthly_subscription_revenue = 0;
$total_active_licenses = 0;
$total_inactive_licenses = 0;

try {
    global $pdo;

    // Total subscription revenue (excluding key-based)
    $stmt = $pdo->query("SELECT COALESCE(SUM(ps.amount), 0) as total FROM premium_subscriptions ps LEFT JOIN premium_subscription_keys psk ON psk.subscription_id = ps.subscription_id WHERE psk.id IS NULL");
    $total_subscription_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // This month subscription revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(ps.amount), 0) as total FROM premium_subscriptions ps LEFT JOIN premium_subscription_keys psk ON psk.subscription_id = ps.subscription_id WHERE psk.id IS NULL AND ps.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $monthly_subscription_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Active licenses
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status = 'active'");
    $total_active_licenses = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Inactive licenses (cancelled, expired, past_due, payment_failed)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status != 'active'");
    $total_inactive_licenses = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Monthly revenue over time (last 12 months) - subscriptions
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(ps.created_at, '%Y-%m') as month, COALESCE(SUM(ps.amount), 0) as total
        FROM premium_subscriptions ps
        LEFT JOIN premium_subscription_keys psk ON psk.subscription_id = ps.subscription_id
        WHERE psk.id IS NULL AND ps.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(ps.created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $subscription_revenue_by_month = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subscription_revenue_by_month[$row['month']] = (float)$row['total'];
    }

    // License status breakdown
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM premium_subscriptions GROUP BY status");
    $license_breakdown = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $license_breakdown[$row['status']] = (int)$row['count'];
    }

    // New subscriptions this month
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $new_subscriptions_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

    // Cancelled this month
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status = 'cancelled' AND cancelled_at >= DATE_FORMAT(NOW(), '%Y-%m-01')");
    $cancelled_this_month = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

} catch (Exception $e) {
    error_log("Error fetching subscription stats: " . $e->getMessage());
    $subscription_revenue_by_month = [];
    $license_breakdown = [];
    $new_subscriptions_this_month = 0;
    $cancelled_this_month = 0;
}

// Build revenue data for last 12 months
$revenue_labels = [];
$subscription_data = [];

for ($i = 11; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i months"));
    $month_label = date('M Y', strtotime("-$i months"));
    $revenue_labels[] = $month_label;
    $subscription_data[] = $subscription_revenue_by_month[$month_key] ?? 0;
}

// Calculate totals
$total_licenses = $total_active_licenses + $total_inactive_licenses;

// Calculate last month revenue for comparison
$last_month_key = date('Y-m', strtotime('-1 month'));
$last_month_revenue = $subscription_revenue_by_month[$last_month_key] ?? 0;
$revenue_change = $last_month_revenue > 0 ? round(($monthly_subscription_revenue - $last_month_revenue) / $last_month_revenue * 100, 1) : 0;

include 'admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="dashboard-home">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Admin Dashboard</h1>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><?= svg_icon('dollar', 24) ?></div>
            <div class="stat-label">Revenue This Month</div>
            <div class="stat-value">$<?php echo number_format($monthly_subscription_revenue, 2); ?></div>
            <?php if ($revenue_change != 0): ?>
                <div class="stat-change <?php echo $revenue_change >= 0 ? 'positive' : 'negative'; ?>">
                    <?= svg_icon($revenue_change >= 0 ? 'trending-up' : 'chevron-down', 14) ?>
                    <?php echo ($revenue_change >= 0 ? '+' : '') . $revenue_change; ?>% vs last month
                </div>
            <?php endif; ?>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><?= svg_icon('analytics', 24) ?></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">$<?php echo number_format($total_subscription_revenue, 2); ?></div>
            <div class="stat-sub">From premium subscriptions</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><?= svg_icon('shield-check', 24) ?></div>
            <div class="stat-label">Active Licenses</div>
            <div class="stat-value"><?php echo number_format($total_active_licenses); ?></div>
            <div class="stat-sub">of <?php echo number_format($total_licenses); ?> total</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><?= svg_icon('shield-filled', 24) ?></div>
            <div class="stat-label">Inactive Licenses</div>
            <div class="stat-value"><?php echo number_format($total_inactive_licenses); ?></div>
            <div class="stat-sub"><?php echo $total_licenses > 0 ? round($total_inactive_licenses / $total_licenses * 100, 1) : 0; ?>% of total</div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="chart-section">
        <div class="chart-header">
            <h2 class="section-title">Revenue Over Time</h2>
        </div>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div class="bottom-grid">
        <!-- Revenue Breakdown -->
        <div class="panel">
            <h2 class="section-title">Monthly Revenue</h2>
            <div class="breakdown-items">
                <div class="breakdown-item">
                    <div class="breakdown-label">
                        <?= svg_icon('subscription', 16) ?>
                        <span>Subscription Revenue</span>
                    </div>
                    <div class="breakdown-value">$<?php echo number_format($monthly_subscription_revenue, 2); ?></div>
                </div>
            </div>
        </div>

        <!-- License Overview -->
        <div class="panel">
            <h2 class="section-title">License Overview</h2>
            <div class="license-stats">
                <div class="license-bar">
                    <?php if ($total_licenses > 0): ?>
                        <div class="license-bar-active" style="width: <?php echo round($total_active_licenses / $total_licenses * 100, 1); ?>%"></div>
                    <?php endif; ?>
                </div>
                <div class="license-details">
                    <?php
                    $status_labels = [
                        'active' => ['Active', 'emerald'],
                        'cancelled' => ['Cancelled', 'red'],
                        'expired' => ['Expired', 'gray'],
                        'past_due' => ['Past Due', 'amber'],
                        'payment_failed' => ['Payment Failed', 'red']
                    ];
                    foreach ($status_labels as $status => $info):
                        $count = $license_breakdown[$status] ?? 0;
                        if ($count > 0):
                    ?>
                        <div class="license-detail">
                            <span class="license-dot license-dot-<?php echo $info[1]; ?>"></span>
                            <span class="license-detail-label"><?php echo $info[0]; ?></span>
                            <span class="license-detail-value"><?php echo number_format($count); ?></span>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>

        <!-- Subscription Activity -->
        <div class="panel">
            <h2 class="section-title">This Month</h2>
            <div class="month-stats">
                <div class="month-stat">
                    <div class="month-stat-value positive-text"><?php echo number_format($new_subscriptions_this_month); ?></div>
                    <div class="month-stat-label">New Subscriptions</div>
                </div>
                <div class="month-stat">
                    <div class="month-stat-value negative-text"><?php echo number_format($cancelled_this_month); ?></div>
                    <div class="month-stat-label">Cancelled</div>
                </div>
                <div class="month-stat">
                    <div class="month-stat-value"><?php echo $new_subscriptions_this_month - $cancelled_this_month >= 0 ? '+' : ''; ?><?php echo number_format($new_subscriptions_this_month - $cancelled_this_month); ?></div>
                    <div class="month-stat-label">Net Change</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="nav-cards">
        <a href="license/" class="nav-card">
            <div class="nav-card-icon"><?= svg_icon('shield-filled', 24) ?></div>
            <div class="nav-card-title">Subscriptions</div>
            <div class="nav-card-description">Manage Premium subscriptions and keys</div>
        </a>
        <a href="payments/" class="nav-card">
            <div class="nav-card-icon"><?= svg_icon('credit-card-filled', 24) ?></div>
            <div class="nav-card-title">Payment Portal</div>
            <div class="nav-card-description">Monitor portal payments and invoices</div>
        </a>
        <a href="app-stats/" class="nav-card">
            <div class="nav-card-icon"><?= svg_icon('bar-chart-filled', 24) ?></div>
            <div class="nav-card-title">App Statistics</div>
            <div class="nav-card-description">View application analytics and metrics</div>
        </a>
        <a href="website-stats/" class="nav-card">
            <div class="nav-card-icon"><?= svg_icon('globe-filled', 24) ?></div>
            <div class="nav-card-title">Website Stats</div>
            <div class="nav-card-description">View website analytics and downloads</div>
        </a>
    </div>
</div>

<script>
// Revenue Over Time Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($revenue_labels); ?>,
        datasets: [
            {
                label: 'Revenue',
                data: <?php echo json_encode($subscription_data); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.8)',
                borderRadius: 4,
                borderSkipped: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#f8fafc',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 8,
                titleFont: { size: 13, weight: '600' },
                bodyFont: { size: 12 },
                callbacks: {
                    label: function(context) {
                        return '$' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                }
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: {
                    color: '#94a3b8',
                    font: { size: 11 },
                    maxRotation: 45
                }
            },
            y: {
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: {
                    color: '#94a3b8',
                    font: { size: 11 },
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
