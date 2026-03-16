<?php
/**
 * Cron Management UI (Admin Tab)
 *
 * A secure interface for managing subscription renewals with:
 * - Dashboard showing subscriptions due for renewal
 * - Log viewer
 */

session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../resources/icons.php';

// Require admin login (includes 2FA if enabled)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$error = '';

// Get subscriptions data
$pendingRenewals = [];
$recentPayments = [];
$stats = [];

try {
    // Get subscriptions due for renewal (within next 7 days)
    $stmt = $pdo->prepare("
        SELECT
            s.*,
            u.username,
            u.email as user_email,
            DATEDIFF(s.end_date, NOW()) as days_until_renewal
        FROM premium_subscriptions s
        JOIN community_users u ON s.user_id = u.id
        WHERE s.status = 'active'
        AND s.payment_method != 'free_key'
        AND s.end_date <= DATE_ADD(NOW(), INTERVAL 7 DAY)
        AND s.auto_renew = 1
        ORDER BY s.end_date ASC
    ");
    $stmt->execute();
    $pendingRenewals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get recent payment history
    $stmt = $pdo->prepare("
        SELECT
            p.*,
            s.email,
            u.username
        FROM premium_subscription_payments p
        JOIN premium_subscriptions s ON p.subscription_id = s.subscription_id
        JOIN community_users u ON s.user_id = u.id
        WHERE s.payment_method != 'free_key'
        ORDER BY p.created_at DESC
        LIMIT 20
    ");
    $stmt->execute();
    $recentPayments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get stats
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status = 'active' AND payment_method != 'free_key'");
    $stats['active_subscriptions'] = $stmt->fetch()['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscriptions WHERE status = 'active' AND payment_method != 'free_key' AND end_date <= DATE_ADD(NOW(), INTERVAL 1 DAY) AND auto_renew = 1");
    $stats['due_today'] = $stmt->fetch()['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM premium_subscription_payments p JOIN premium_subscriptions s ON p.subscription_id = s.subscription_id WHERE p.status = 'completed' AND s.payment_method != 'free_key' AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['successful_30d'] = $stmt->fetch()['count'];

    $stmt = $pdo->query("
        SELECT COUNT(DISTINCT p.subscription_id) AS count
        FROM premium_subscription_payments p
        JOIN premium_subscriptions s ON p.subscription_id = s.subscription_id
        WHERE p.status = 'failed'
        AND s.payment_method != 'free_key'
        AND p.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stats['failed_30d'] = $stmt->fetch()['count'];

} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Get available log files
$logFiles = [];
$logsDir = __DIR__ . '/../../cron/logs';
if (is_dir($logsDir)) {
    $files = glob($logsDir . '/subscription_renewal_*.log');
    rsort($files);
    $logFiles = array_slice($files, 0, 10);
}

// Handle log viewing
$selectedLog = '';
$logContent = '';
if (isset($_GET['view_log'])) {
    $requestedLog = basename($_GET['view_log']);
    $logPath = $logsDir . '/' . $requestedLog;
    if (file_exists($logPath) && strpos($requestedLog, 'subscription_renewal_') === 0) {
        $selectedLog = $requestedLog;
        $logContent = file_get_contents($logPath);
    }
}

$page_title = 'Subscription Renewal Management';
$page_description = 'Monitor and manage Premium subscription renewals';

include '../admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="cron-dashboard">
    <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['active_subscriptions'] ?? 0; ?></div>
            <div class="stat-label">Active Subscriptions</div>
        </div>
        <div class="stat-card <?php echo ($stats['due_today'] ?? 0) > 0 ? 'warning' : ''; ?>">
            <div class="stat-value"><?php echo $stats['due_today'] ?? 0; ?></div>
            <div class="stat-label">Due Today</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value"><?php echo $stats['successful_30d'] ?? 0; ?></div>
            <div class="stat-label">Successful (30 days)</div>
        </div>
        <div class="stat-card <?php echo ($stats['failed_30d'] ?? 0) > 0 ? 'danger' : ''; ?>">
            <div class="stat-value"><?php echo $stats['failed_30d'] ?? 0; ?></div>
            <div class="stat-label">Failed (30 days)</div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Pending Renewals -->
        <div class="panel">
            <div class="panel-header">
                <h2>Upcoming Renewals (7 days)</h2>
                <span style="color: #6b7280; font-size: 0.875rem;"><?php echo count($pendingRenewals); ?> subscriptions</span>
            </div>
            <div class="panel-content">
                <?php if (empty($pendingRenewals)): ?>
                    <div class="no-data">
                        <?= svg_icon('check-filled') ?>
                        <p>No renewals due in the next 7 days</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pendingRenewals as $renewal): ?>
                        <?php
                        $daysUntil = $renewal['days_until_renewal'];
                        if ($daysUntil <= 0) {
                            $badgeClass = 'badge-due-now';
                            $badgeText = 'Due Now';
                        } elseif ($daysUntil <= 1) {
                            $badgeClass = 'badge-due-soon';
                            $badgeText = 'Tomorrow';
                        } else {
                            $badgeClass = 'badge-upcoming';
                            $badgeText = $daysUntil . ' days';
                        }
                        ?>
                        <div class="renewal-item">
                            <div class="renewal-info">
                                <h4><?php echo htmlspecialchars($renewal['username']); ?></h4>
                                <p><?php echo htmlspecialchars($renewal['email']); ?> - <?php echo ucfirst($renewal['billing_cycle']); ?> ($<?php echo number_format($renewal['billing_cycle'] === 'yearly' ? 50 : 5, 2); ?>)</p>
                                <p>Credit: $<?php echo number_format($renewal['credit_balance'] ?? 0, 2); ?></p>
                            </div>
                            <span class="renewal-badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="panel">
            <div class="panel-header">
                <h2>Recent Payment Activity</h2>
            </div>
            <div class="panel-content">
                <?php if (empty($recentPayments)): ?>
                    <div class="no-data">
                        <?= svg_icon('credit-card-filled') ?>
                        <p>No recent payment activity</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentPayments as $payment): ?>
                        <?php
                        $providerColors = [
                            'paypal' => '#003087',
                            'stripe' => '#635bff',
                            'square' => '#006aff'
                        ];
                        $providerNames = [
                            'paypal' => 'PayPal',
                            'stripe' => 'Stripe',
                            'square' => 'Square'
                        ];
                        $providerColor = $providerColors[strtolower($payment['payment_method'])] ?? '#6b7280';
                        $providerName = $providerNames[strtolower($payment['payment_method'])] ?? ucfirst($payment['payment_method']);
                        ?>
                        <div class="payment-item">
                            <div>
                                <strong><?php echo htmlspecialchars($payment['username']); ?></strong>
                                <?php if ($payment['payment_type'] === 'credit' && floatval($payment['amount']) == 0): ?>
                                    <span style="color: #7c3aed; margin-left: 5px; font-style: italic;">Credit (discount)</span>
                                <?php else: ?>
                                    <span style="color: #6b7280; margin-left: 5px;">$<?php echo number_format($payment['amount'], 2); ?></span>
                                <?php endif; ?>
                                <br>
                                <span style="color: <?php echo $providerColor; ?>; font-size: 0.75rem; font-weight: 600;"><?php echo $providerName; ?></span>
                                <span style="color: #9ca3af; font-size: 0.75rem; font-family: monospace; margin-left: 8px;"><?php echo htmlspecialchars($payment['subscription_id']); ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="color: #9ca3af; font-size: 0.75rem;">
                                    <?php echo date('M j, g:i A', strtotime($payment['created_at'])); ?>
                                </span>
                                <span class="payment-status status-<?php echo $payment['status']; ?>">
                                    <?php echo ucfirst($payment['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Logs Viewer -->
        <div class="panel full-width" id="logs">
            <div class="panel-header">
                <h2>Renewal Logs</h2>
            </div>
            <div class="panel-content">
                <?php if (empty($logFiles)): ?>
                    <div class="no-data">
                        <?= svg_icon('document-filled') ?>
                        <p>No log files available yet. Logs are created when the renewal process runs.</p>
                    </div>
                <?php else: ?>
                    <div class="log-selector">
                        <form method="get" action="#logs">
                            <select name="view_log" onchange="this.form.submit()">
                                <option value="">Select a log file...</option>
                                <?php foreach ($logFiles as $logFile): ?>
                                    <?php $fileName = basename($logFile); ?>
                                    <option value="<?php echo htmlspecialchars($fileName); ?>" <?php echo $selectedLog === $fileName ? 'selected' : ''; ?>>
                                        <?php echo $fileName; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <?php if ($logContent): ?>
                        <div class="log-viewer"><?php echo htmlspecialchars($logContent); ?></div>
                    <?php else: ?>
                        <p style="color: #6b7280; text-align: center;">Select a log file to view its contents.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
