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

// Get stats
// Total community posts
$result = $db->query('SELECT COUNT(*) as count FROM community_posts');
$total_posts = $result->fetch_assoc()['count'] ?? 0;

// Total users
$result = $db->query('SELECT COUNT(*) as count FROM community_users');
$total_users = $result->fetch_assoc()['count'] ?? 0;

// Premium subscriptions
$total_subscriptions = 0;
$monthly_subscriptions = 0;
$total_subscription_revenue = 0;
try {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status = 'active'");
    $total_subscriptions = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthly_subscriptions = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM premium_subscriptions");
    $total_subscription_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error fetching subscription stats: " . $e->getMessage());
}

// Portal payments
$monthly_portal_payments = 0;
$total_portal_payments_amount = 0;
$monthly_portal_payments_amount = 0;
try {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_payments WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthly_portal_payments = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'completed'");
    $total_portal_payments_amount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthly_portal_payments_amount = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    error_log("Error fetching portal payment stats: " . $e->getMessage());
}

// Users registered in the last 30 days
$result = $db->query('SELECT COUNT(*) as count FROM community_users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
$monthly_users = $result->fetch_assoc()['count'] ?? 0;

// Posts created in the last 30 days
$result = $db->query('SELECT COUNT(*) as count FROM community_posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
$monthly_posts = $result->fetch_assoc()['count'] ?? 0;

// App users from data-logs (unique IP addresses)
$total_app_users = 0;
$monthly_app_users = 0;
$dataDir = __DIR__ . '/data-logs/';
$thirtyDaysAgo = strtotime('-30 days');

if (is_dir($dataDir)) {
    $allIPs = [];
    $monthlyIPs = [];
    $dataFiles = glob($dataDir . '*.json');

    foreach ($dataFiles as $file) {
        $jsonData = file_get_contents($file);
        if ($jsonData === false) continue;

        $fileData = json_decode($jsonData, true);
        if ($fileData === null || !isset($fileData['dataPoints'])) continue;

        foreach ($fileData['dataPoints'] as $category => $dataPoints) {
            foreach ($dataPoints as $dataPoint) {
                if (!empty($dataPoint['hashedIP'])) {
                    $allIPs[$dataPoint['hashedIP']] = true;

                    if (!empty($dataPoint['timestamp'])) {
                        $timestamp = strtotime($dataPoint['timestamp']);
                        if ($timestamp >= $thirtyDaysAgo) {
                            $monthlyIPs[$dataPoint['hashedIP']] = true;
                        }
                    }
                }
            }
        }
    }

    $total_app_users = count($allIPs);
    $monthly_app_users = count($monthlyIPs);
}

// Monthly downloads from statistics table
$monthly_downloads = 0;
$total_downloads = 0;
try {
    $dl_result = $db->query("SELECT COUNT(*) as count FROM statistics WHERE event_type IN ('download_win', 'download_mac', 'download_linux', 'download_avalonia') AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthly_downloads = $dl_result->fetch_assoc()['count'] ?? 0;
    $dl_total_result = $db->query("SELECT COUNT(*) as count FROM statistics WHERE event_type IN ('download_win', 'download_mac', 'download_linux', 'download_avalonia')");
    $total_downloads = $dl_total_result->fetch_assoc()['count'] ?? 0;
} catch (Exception $e) {
    error_log("Error fetching download stats: " . $e->getMessage());
}

// Activity event count from query parameter
$activity_count = isset($_GET['activity']) ? (int)$_GET['activity'] : 10;
$allowed_counts = [5, 10, 25, 50];
if (!in_array($activity_count, $allowed_counts)) {
    $activity_count = 10;
}
$per_source_limit = $activity_count; // fetch enough per source to fill the total

// Get recent activity items for timeline
$recent_items = [];

// Recent user registrations
$result = $db->query("SELECT username, created_at, email_verified FROM community_users ORDER BY created_at DESC LIMIT $per_source_limit");
while ($row = $result->fetch_assoc()) {
    $recent_items[] = [
        'type' => 'user',
        'time' => $row['created_at'],
        'description' => 'New user registered: ' . htmlspecialchars($row['username']),
        'status' => $row['email_verified'] ? 'verified' : 'pending'
    ];
}

// Recent subscription events (new and cancelled)
try {
    global $pdo;
    // New subscriptions
    $stmt = $pdo->query("
        SELECT ps.email, ps.billing_cycle, ps.created_at, ps.subscription_id
        FROM premium_subscriptions ps
        ORDER BY ps.created_at DESC
        LIMIT $per_source_limit
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $label = htmlspecialchars($row['email'] ?: $row['subscription_id']);
        $cycle = ucfirst($row['billing_cycle']);
        $recent_items[] = [
            'type' => 'subscription',
            'time' => $row['created_at'],
            'description' => "Subscription added: $label ($cycle)",
            'status' => 'active'
        ];
    }
    // Cancelled subscriptions
    $stmt = $pdo->query("
        SELECT ps.email, ps.cancelled_at, ps.subscription_id
        FROM premium_subscriptions ps
        WHERE ps.status = 'cancelled' AND ps.cancelled_at IS NOT NULL
        ORDER BY ps.cancelled_at DESC
        LIMIT $per_source_limit
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $label = htmlspecialchars($row['email'] ?: $row['subscription_id']);
        $recent_items[] = [
            'type' => 'cancellation',
            'time' => $row['cancelled_at'],
            'description' => "Subscription cancelled: $label",
            'status' => 'cancelled'
        ];
    }
} catch (Exception $e) {
    error_log("Error fetching subscription activity: " . $e->getMessage());
}

// Sort by time
usort($recent_items, function ($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Keep only the requested number
$recent_items = array_slice($recent_items, 0, $activity_count);

// System health checks - comprehensive production-ready implementation
$system_health = [];
$health_details = [];
$overall_status = 'operational';

// 1. Database connectivity and performance
try {
    $start_time = microtime(true);
    $db->query('SELECT 1');
    $db_response_time = round((microtime(true) - $start_time) * 1000, 2);

    // Get MySQL version
    $mysql_version_result = $db->query('SELECT VERSION() as version');
    $mysql_version = $mysql_version_result->fetch_assoc()['version'] ?? 'Unknown';

    // Get database size
    $db_name = $db->query("SELECT DATABASE() as db_name")->fetch_assoc()['db_name'];
    $size_result = $db->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = '$db_name'");
    $db_size = $size_result->fetch_assoc()['size_mb'] ?? 0;

    $system_health['database'] = $db_response_time < 100 ? 'operational' : 'warning';
    $health_details['database'] = [
        'response_time' => $db_response_time . ' ms',
        'version' => $mysql_version,
        'size' => $db_size . ' MB / 10 GB'
    ];
} catch (Exception $e) {
    $system_health['database'] = 'error';
    $health_details['database'] = ['error' => 'Connection failed'];
    $overall_status = 'error';
}

// 2. PHP Environment
$php_version = phpversion();
$memory_limit = ini_get('memory_limit');

// Convert shorthand to full notation (128M -> 128 MB)
if (preg_match('/^(\d+)([KMG])$/', $memory_limit, $matches)) {
    $memory_limit = $matches[1] . ' ' . $matches[2] . 'B';
}

$memory_usage = round(memory_get_usage(true) / 1024 / 1024, 2);
$max_execution_time = ini_get('max_execution_time');

$system_health['php'] = 'operational';
$health_details['php'] = [
    'version' => $php_version,
    'memory_usage' => $memory_usage . ' MB',
    'memory_limit' => $memory_limit,
    'max_execution_time' => $max_execution_time . 's'
];

// 3. Session Directory
$session_path = session_save_path() ?: sys_get_temp_dir();
$session_writable = is_writable($session_path);
$system_health['sessions'] = $session_writable ? 'operational' : 'error';
$health_details['sessions'] = [
    'writable' => $session_writable ? 'Yes' : 'No'
];

if (!$session_writable && $overall_status === 'operational') {
    $overall_status = 'warning';
}

// 4. Upload Directory
$upload_path = $_SERVER['DOCUMENT_ROOT'] . '/admin/data-logs';

if (file_exists($upload_path)) {
    $upload_writable = is_writable($upload_path);
    $system_health['uploads'] = $upload_writable ? 'operational' : 'warning';
    $health_details['uploads'] = [
        'writable' => $upload_writable ? 'Yes' : 'No'
    ];

    if (!$upload_writable && $overall_status === 'operational') {
        $overall_status = 'warning';
    }
} else {
    $system_health['uploads'] = 'warning';
    $health_details['uploads'] = ['status' => 'Directory not found'];
}

include 'admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="dashboard-home">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Dashboard Overview</h1>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-label">Active Subscriptions</div>
            <div class="stat-value"><?php echo number_format($total_subscriptions); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?php echo number_format($total_app_users); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Community Posts</div>
            <div class="stat-value"><?php echo number_format($total_posts); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Payments</div>
            <div class="stat-value">$<?php echo number_format($total_portal_payments_amount, 2); ?></div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="nav-cards">
        <a href="license/" class="nav-card">
            <div class="nav-card-icon">
                <?= svg_icon('shield-filled', 24) ?>
            </div>
            <div class="nav-card-title">Subscriptions</div>
            <div class="nav-card-description">Manage Premium subscriptions and keys</div>
            <div class="nav-card-stats">
                <div class="nav-card-stat">
                    <span class="nav-card-stat-label">New Subscriptions This Month</span>
                    <span class="nav-card-stat-value"><?php echo number_format($monthly_subscriptions); ?></span>
                </div>
                <div class="nav-card-stat">
                    <span class="nav-card-stat-label">Total Revenue</span>
                    <span class="nav-card-stat-value">$<?php echo number_format($total_subscription_revenue, 2); ?></span>
                </div>
            </div>
        </a>

        <a href="app-stats/" class="nav-card">
            <div class="nav-card-icon">
                <?= svg_icon('bar-chart-filled', 24) ?>
            </div>
            <div class="nav-card-title">App Statistics</div>
            <div class="nav-card-description">View application analytics and metrics</div>
            <div class="nav-card-stat">
                <span class="nav-card-stat-label">New Users This Month</span>
                <span class="nav-card-stat-value"><?php echo number_format($monthly_app_users); ?></span>
            </div>
        </a>

        <a href="website-stats/" class="nav-card">
            <div class="nav-card-icon">
                <?= svg_icon('globe-filled', 24) ?>
            </div>
            <div class="nav-card-title">Total Downloads</div>
            <div class="nav-card-description">View website analytics and download metrics</div>
            <div class="nav-card-stat">
                <span class="nav-card-stat-label">Downloads This Month</span>
                <span class="nav-card-stat-value"><?php echo number_format($monthly_downloads); ?></span>
            </div>
        </a>

        <a href="payments/" class="nav-card">
            <div class="nav-card-icon">
                <?= svg_icon('credit-card-filled', 24) ?>
            </div>
            <div class="nav-card-title">Payment Portal</div>
            <div class="nav-card-description">Monitor portal payments and invoices</div>
            <div class="nav-card-stats">
                <div class="nav-card-stat">
                    <span class="nav-card-stat-label">Transactions This Month</span>
                    <span class="nav-card-stat-value"><?php echo number_format($monthly_portal_payments); ?></span>
                </div>
                <div class="nav-card-stat">
                    <span class="nav-card-stat-label">Total Payments This Month</span>
                    <span class="nav-card-stat-value">$<?php echo number_format($monthly_portal_payments_amount, 2); ?></span>
                </div>
            </div>
        </a>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Activity Timeline -->
        <div class="activity-section">
            <div class="activity-header">
                <h2 class="section-title">Recent Activity <button class="activity-info-btn" onclick="document.getElementById('activityInfoModal').classList.add('show')" title="What's tracked?"><?= svg_icon('info', 16) ?></button></h2>
                <select class="activity-count-select" onchange="window.location.href='?activity=' + this.value">
                    <?php foreach ($allowed_counts as $count): ?>
                        <option value="<?= $count ?>" <?= $activity_count === $count ? 'selected' : '' ?>><?= $count ?> events</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="timeline">
                <?php if (empty($recent_items)): ?>
                    <p style="color: #94a3b8; text-align: center; padding: 2rem 0;">No recent activity</p>
                <?php else: ?>
                    <?php foreach ($recent_items as $item): ?>
                        <div class="timeline-item type-<?php echo $item['type']; ?>">
                            <div class="timeline-time">
                                <?php
                                $time = strtotime($item['time']);
                                $diff = time() - $time;
                                if ($diff < 60) {
                                    echo 'Just now';
                                } elseif ($diff < 3600) {
                                    echo floor($diff / 60) . ' minutes ago';
                                } elseif ($diff < 86400) {
                                    echo floor($diff / 3600) . ' hours ago';
                                } else {
                                    echo date('M j, Y g:i A', $time);
                                }
                                ?>
                            </div>
                            <div class="timeline-description"><?php echo $item['description']; ?></div>
                            <span class="timeline-status <?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- System Health -->
        <div class="health-section">
            <h2 class="section-title">System Health</h2>
            <div class="health-items">
                <?php foreach ($system_health as $component => $status): ?>
                    <div class="health-item">
                        <div class="health-item-header">
                            <span class="health-item-name"><?php echo ucfirst(str_replace('_', ' ', $component)); ?></span>
                            <div class="health-status">
                                <div class="health-indicator <?php echo $status === 'error' ? 'error' : ($status === 'warning' ? 'warning' : ''); ?>"></div>
                                <span><?php echo ucfirst($status); ?></span>
                            </div>
                        </div>
                        <?php if (isset($health_details[$component]) && !empty($health_details[$component])): ?>
                            <div class="health-details">
                                <?php foreach ($health_details[$component] as $key => $value): ?>
                                    <div class="health-detail-item">
                                        <span class="health-detail-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?>:</span>
                                        <span class="health-detail-value"><?php echo htmlspecialchars($value); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="health-divider"></div>
            <div class="health-summary">
                <div class="health-summary-text">
                    <?php
                    if ($overall_status === 'operational') {
                        echo 'All systems operational';
                    } elseif ($overall_status === 'warning') {
                        echo 'Some systems require attention';
                    } else {
                        echo 'Critical issues detected';
                    }
                    ?>
                </div>
                <div class="health-summary-status <?php echo $overall_status; ?>">
                    <?php echo strtoupper($overall_status); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Info Modal -->
<div class="activity-info-modal" id="activityInfoModal">
    <div class="activity-info-modal-backdrop" onclick="document.getElementById('activityInfoModal').classList.remove('show')"></div>
    <div class="activity-info-modal-content">
        <div class="activity-info-modal-header">
            <h3>What's Tracked</h3>
            <button class="activity-info-modal-close" onclick="document.getElementById('activityInfoModal').classList.remove('show')"><?= svg_icon('x', 20) ?></button>
        </div>
        <div class="activity-info-modal-body">
            <p>The Recent Activity timeline shows the most recent events across these categories:</p>
            <ul>
                <li>
                    <span class="activity-info-dot"></span>
                    <div>
                        <strong>User Registrations</strong>
                        <span>New community user sign-ups with verification status</span>
                    </div>
                </li>
                <li>
                    <span class="activity-info-dot"></span>
                    <div>
                        <strong>Subscription Added</strong>
                        <span>New premium subscriptions (monthly or yearly)</span>
                    </div>
                </li>
                <li>
                    <span class="activity-info-dot"></span>
                    <div>
                        <strong>Subscription Cancelled</strong>
                        <span>Premium subscriptions that were cancelled</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>