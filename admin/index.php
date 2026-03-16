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

try {
    global $pdo;

    // --- MRR (Monthly Recurring Revenue) last 12 months ---
    // Use actual completed payments, normalizing yearly payments to monthly equivalent (/12)
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(p.created_at, '%Y-%m') as month,
            COALESCE(SUM(
                CASE WHEN s.billing_cycle = 'yearly' THEN p.amount / 12
                     ELSE p.amount
                END
            ), 0) as mrr
        FROM premium_subscription_payments p
        JOIN premium_subscriptions s ON p.subscription_id = s.subscription_id
        WHERE p.status = 'completed'
        AND s.payment_method != 'free_key'
        AND p.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(p.created_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $mrr_by_month = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $mrr_by_month[$row['month']] = round((float)$row['mrr'], 2);
    }

    // --- Active vs Inactive license counts per month (last 12 months) ---
    // For each month, count subscriptions that were active at end of that month using date-based logic
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(months.month_date, '%Y-%m') as month,
            COALESCE(SUM(CASE
                WHEN s.created_at <= LAST_DAY(months.month_date)
                     AND (s.end_date IS NULL OR s.end_date > LAST_DAY(months.month_date))
                     AND (s.cancelled_at IS NULL OR s.cancelled_at > LAST_DAY(months.month_date))
                     AND s.payment_method != 'free_key'
                THEN 1 ELSE 0 END), 0) as active_count,
            COALESCE(SUM(CASE
                WHEN s.created_at <= LAST_DAY(months.month_date)
                     AND s.payment_method != 'free_key'
                     AND (
                         (s.end_date IS NOT NULL AND s.end_date <= LAST_DAY(months.month_date))
                         OR (s.cancelled_at IS NOT NULL AND s.cancelled_at <= LAST_DAY(months.month_date))
                     )
                THEN 1 ELSE 0 END), 0) as inactive_count
        FROM (
            SELECT DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL n MONTH) as month_date
            FROM (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3
                UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
                UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
            ) nums
        ) months
        LEFT JOIN premium_subscriptions s ON s.payment_method != 'free_key'
        GROUP BY months.month_date
        ORDER BY months.month_date ASC
    ");
    $license_by_month = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $license_by_month[$row['month']] = [
            'active' => (int)$row['active_count'],
            'inactive' => (int)$row['inactive_count']
        ];
    }

    // --- Churn rate per month (last 12 months) ---
    // cancelled_in_month / active_at_start_of_month * 100
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(cancelled_at, '%Y-%m') as month,
            COUNT(*) as cancelled_count
        FROM premium_subscriptions
        WHERE cancelled_at IS NOT NULL
        AND payment_method != 'free_key'
        AND cancelled_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(cancelled_at, '%Y-%m')
        ORDER BY month ASC
    ");
    $cancelled_by_month = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cancelled_by_month[$row['month']] = (int)$row['cancelled_count'];
    }

    // Active count at start of each month (created before month and not yet cancelled/expired)
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(months.month_date, '%Y-%m') as month,
            COUNT(s.subscription_id) as active_start
        FROM (
            SELECT DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-01'), INTERVAL n MONTH) as month_date
            FROM (
                SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3
                UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7
                UNION SELECT 8 UNION SELECT 9 UNION SELECT 10 UNION SELECT 11
            ) nums
        ) months
        LEFT JOIN premium_subscriptions s
            ON s.created_at < months.month_date
            AND s.payment_method != 'free_key'
            AND (s.cancelled_at IS NULL OR s.cancelled_at >= months.month_date)
            AND (s.end_date >= months.month_date OR s.end_date IS NULL)
        GROUP BY months.month_date
        ORDER BY months.month_date ASC
    ");
    $active_start_by_month = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $active_start_by_month[$row['month']] = (int)$row['active_start'];
    }

} catch (Exception $e) {
    error_log("Error fetching subscription stats: " . $e->getMessage());
    $mrr_by_month = [];
    $license_by_month = [];
    $cancelled_by_month = [];
    $active_start_by_month = [];
}

// Build arrays for last 12 months
$chart_labels = [];
$mrr_data = [];
$cumulative_data = [];
$active_data = [];
$inactive_data = [];
$churn_data = [];

$running_total = 0;

for ($i = 11; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i months"));
    $month_label = date('M Y', strtotime("-$i months"));
    $chart_labels[] = $month_label;

    // MRR
    $mrr = $mrr_by_month[$month_key] ?? 0;
    $mrr_data[] = $mrr;

    // Cumulative revenue
    $running_total += $mrr;
    $cumulative_data[] = round($running_total, 2);

    // Active vs Inactive
    $active_data[] = $license_by_month[$month_key]['active'] ?? 0;
    $inactive_data[] = $license_by_month[$month_key]['inactive'] ?? 0;

    // Churn rate
    $cancelled = $cancelled_by_month[$month_key] ?? 0;
    $active_start = $active_start_by_month[$month_key] ?? 0;
    $churn_data[] = $active_start > 0 ? round($cancelled / $active_start * 100, 1) : 0;
}

include 'admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="dashboard-home">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Admin Dashboard</h1>
    </div>

    <!-- Charts Grid (replaces stat cards) -->
    <div class="charts-grid">
        <!-- MRR Chart -->
        <div class="chart-card">
            <h2 class="chart-card-title">Monthly Recurring Revenue</h2>
            <div class="chart-card-container">
                <canvas id="mrrChart"></canvas>
            </div>
        </div>

        <!-- Cumulative Revenue Chart -->
        <div class="chart-card">
            <h2 class="chart-card-title">Cumulative Revenue</h2>
            <div class="chart-card-container">
                <canvas id="cumulativeChart"></canvas>
            </div>
        </div>

        <!-- Active vs Inactive Chart -->
        <div class="chart-card">
            <h2 class="chart-card-title">Active vs Inactive Licenses</h2>
            <div class="chart-card-container">
                <canvas id="licensesChart"></canvas>
            </div>
        </div>

        <!-- Churn Rate Chart -->
        <div class="chart-card">
            <h2 class="chart-card-title">Monthly Churn Rate</h2>
            <div class="chart-card-container">
                <canvas id="churnChart"></canvas>
            </div>
        </div>
    </div>

</div>

<script>
const chartLabels = <?php echo json_encode($chart_labels); ?>;

const sharedTooltip = {
    backgroundColor: '#1e293b',
    titleColor: '#f8fafc',
    bodyColor: '#cbd5e1',
    padding: 12,
    cornerRadius: 8,
    titleFont: { size: 13, weight: '600' },
    bodyFont: { size: 12 }
};

const sharedScaleX = {
    grid: { display: false },
    ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 45 }
};

// 1. MRR Line Chart
new Chart(document.getElementById('mrrChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'MRR',
            data: <?php echo json_encode($mrr_data); ?>,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#6366f1',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                ...sharedTooltip,
                callbacks: {
                    label: ctx => '$' + ctx.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                }
            }
        },
        scales: {
            x: sharedScaleX,
            y: {
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => '$' + v.toLocaleString() }
            }
        }
    }
});

// 2. Cumulative Revenue Area Chart
new Chart(document.getElementById('cumulativeChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Cumulative Revenue',
            data: <?php echo json_encode($cumulative_data); ?>,
            borderColor: '#8b5cf6',
            backgroundColor: 'rgba(139, 92, 246, 0.15)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#8b5cf6',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                ...sharedTooltip,
                callbacks: {
                    label: ctx => '$' + ctx.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})
                }
            }
        },
        scales: {
            x: sharedScaleX,
            y: {
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => '$' + v.toLocaleString() }
            }
        }
    }
});

// 3. Active vs Inactive Stacked Bar Chart
new Chart(document.getElementById('licensesChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [
            {
                label: 'Active',
                data: <?php echo json_encode($active_data); ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 4,
                borderSkipped: false
            },
            {
                label: 'Inactive',
                data: <?php echo json_encode($inactive_data); ?>,
                backgroundColor: 'rgba(239, 68, 68, 0.6)',
                borderRadius: 4,
                borderSkipped: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: { usePointStyle: true, pointStyle: 'circle', padding: 15, font: { size: 11 } }
            },
            tooltip: sharedTooltip
        },
        scales: {
            x: { ...sharedScaleX, stacked: true },
            y: {
                stacked: true,
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 }
            }
        }
    }
});

// 4. Churn Rate Line Chart
new Chart(document.getElementById('churnChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Churn Rate',
            data: <?php echo json_encode($churn_data); ?>,
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            fill: true,
            tension: 0.3,
            pointBackgroundColor: '#ef4444',
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                ...sharedTooltip,
                callbacks: {
                    label: ctx => ctx.parsed.y + '% churn'
                }
            }
        },
        scales: {
            x: sharedScaleX,
            y: {
                grid: { color: 'rgba(148, 163, 184, 0.1)' },
                ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => v + '%' },
                min: 0
            }
        }
    }
});
</script>
