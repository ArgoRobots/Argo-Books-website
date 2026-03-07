<?php
session_start();
require_once '../../db_connect.php';
require_once '../../email_sender.php';
require_once '../../community/report/ban_check.php';

require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';

// Check if user is already logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for the header
$page_title = "User Account Management";
$page_description = "Manage community user accounts, view user statistics, and moderate users";

// Handle bulk user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_ids = $_POST['selected_ids'] ?? [];

    if (!empty($selected_ids)) {
        $db = get_db_connection();
        $success_count = 0;
        $fail_count = 0;

        if ($action === 'delete') {
            foreach ($selected_ids as $user_id) {
                $stmt = $db->prepare('DELETE FROM community_users WHERE id = ?');
                $stmt->bind_param('i', $user_id);
                
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $fail_count++;
                }
            }

            if ($success_count > 0) {
                $msg = $success_count . ' user' . ($success_count > 1 ? 's' : '') . ' deleted successfully.';
                if ($fail_count > 0) {
                    $msg .= ' ' . $fail_count . ' failed.';
                }
                $_SESSION['message'] = $msg;
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Failed to delete users.';
                $_SESSION['message_type'] = 'error';
            }
        } elseif ($action === 'unban') {
            foreach ($selected_ids as $user_id) {
                // Deactivate all active bans for this user
                $stmt = $db->prepare('UPDATE user_bans SET is_active = 0, unbanned_at = NOW(), unbanned_by = NULL WHERE user_id = ? AND is_active = 1');
                $stmt->bind_param('i', $user_id);
                
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    // Get user info for email
                    $stmt2 = $db->prepare('SELECT username, email FROM community_users WHERE id = ?');
                    $stmt2->bind_param('i', $user_id);
                    $stmt2->execute();
                    $result = $stmt2->get_result();
                    $user = $result->fetch_assoc();
                    $stmt2->close();
                    
                    if ($user) {
                        send_unban_notification_email($user['email'], $user['username']);
                        $success_count++;
                    }
                } else {
                    $fail_count++;
                }
            }

            if ($success_count > 0) {
                $msg = $success_count . ' user' . ($success_count > 1 ? 's' : '') . ' unbanned successfully.';
                if ($fail_count > 0) {
                    $msg .= ' ' . $fail_count . ' failed.';
                }
                $_SESSION['message'] = $msg;
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'No active bans found for selected users.';
                $_SESSION['message_type'] = 'error';
            }
        }

        // Redirect to prevent form resubmission
        $redirect_params = [];
        if (!empty($_GET['search'])) $redirect_params[] = 'search=' . urlencode($_GET['search']);
        if (!empty($_GET['ban_status'])) $redirect_params[] = 'ban_status=' . urlencode($_GET['ban_status']);
        $redirect_url = 'index.php' . (!empty($redirect_params) ? '?' . implode('&', $redirect_params) : '');
        header('Location: ' . $redirect_url);
        exit;
    }
}

// Handle usage reset via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_usage'])) {
    header('Content-Type: application/json');
    $user_ids = $_POST['user_ids'] ?? [];
    if (!is_array($user_ids)) $user_ids = [$user_ids];
    $user_ids = array_filter(array_map('intval', $user_ids), function($id) { return $id > 0; });

    if (empty($user_ids)) {
        echo json_encode(['success' => false, 'error' => 'No valid user IDs']);
        exit;
    }

    global $pdo;
    $usage_month = date('Y-m-01');

    try {
        $all_keys = [];
        foreach ($user_ids as $user_id) {
            // Get all license keys for this user (direct + free key via email)
            $stmt = $pdo->prepare("
                SELECT subscription_id FROM premium_subscriptions WHERE user_id = ?
                UNION
                SELECT psk.subscription_key
                FROM premium_subscription_keys psk
                JOIN community_users u ON u.email = psk.email
                WHERE u.id = ? AND psk.email IS NOT NULL AND psk.redeemed_at IS NOT NULL
            ");
            $stmt->execute([$user_id, $user_id]);
            $all_keys = array_merge($all_keys, $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        $all_keys = array_unique($all_keys);

        if (empty($all_keys)) {
            echo json_encode(['success' => true, 'message' => 'No subscriptions found']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($all_keys), '?'));
        $params = array_merge($all_keys, [$usage_month]);

        $stmt = $pdo->prepare("UPDATE receipt_scan_usage SET scan_count = 0 WHERE license_key IN ($placeholders) AND usage_month = ?");
        $stmt->execute($params);

        $stmt = $pdo->prepare("UPDATE ai_import_usage SET scan_count = 0 WHERE license_key IN ($placeholders) AND usage_month = ?");
        $stmt->execute($params);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Usage reset error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
    exit;
}

// Function to get all license keys (as used in usage tables) linked to a user.
// For paid subscriptions: license_key = premium_subscriptions.subscription_id
// For free keys: license_key = premium_subscription_keys.subscription_key (the original key)
// because the app sends the original key to the usage API.
function _get_user_license_keys_query() {
    return "
        SELECT ps.subscription_id as license_key, u.id as user_id
        FROM premium_subscriptions ps
        JOIN community_users u ON ps.user_id = u.id
        WHERE ps.user_id IS NOT NULL
        UNION
        SELECT psk.subscription_key as license_key, u.id as user_id
        FROM premium_subscription_keys psk
        JOIN community_users u ON u.email = psk.email
        WHERE psk.email IS NOT NULL AND psk.redeemed_at IS NOT NULL
    ";
}

// Function to get usage data for all users
function get_user_usage_data() {
    global $pdo;
    $usage_month = date('Y-m-01');
    $data = [];
    $user_keys_query = _get_user_license_keys_query();

    try {
        // Receipt scan usage per user
        $stmt = $pdo->prepare("
            SELECT uk.user_id, COALESCE(SUM(rsu.scan_count), 0) as receipt_scans
            FROM ($user_keys_query) uk
            LEFT JOIN receipt_scan_usage rsu ON rsu.license_key = uk.license_key AND rsu.usage_month = ?
            GROUP BY uk.user_id
        ");
        $stmt->execute([$usage_month]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[$row['user_id']]['receipt_scans'] = (int)$row['receipt_scans'];
        }

        // AI import usage per user
        $stmt = $pdo->prepare("
            SELECT uk.user_id, COALESCE(SUM(aiu.scan_count), 0) as ai_imports
            FROM ($user_keys_query) uk
            LEFT JOIN ai_import_usage aiu ON aiu.license_key = uk.license_key AND aiu.usage_month = ?
            GROUP BY uk.user_id
        ");
        $stmt->execute([$usage_month]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($data[$row['user_id']])) {
                $data[$row['user_id']] = ['receipt_scans' => 0];
            }
            $data[$row['user_id']]['ai_imports'] = (int)$row['ai_imports'];
        }

        // Track which users have active premium subscriptions (direct or via free key)
        $stmt = $pdo->prepare("
            SELECT DISTINCT user_id FROM (
                SELECT user_id FROM premium_subscriptions
                WHERE user_id IS NOT NULL AND status IN ('active', 'cancelled') AND end_date > NOW()
                UNION
                SELECT u.id as user_id
                FROM premium_subscription_keys psk
                JOIN community_users u ON u.email = psk.email
                JOIN premium_subscriptions ps ON ps.subscription_id = psk.subscription_id
                WHERE psk.email IS NOT NULL AND psk.subscription_id IS NOT NULL
                AND ps.status IN ('active', 'cancelled') AND ps.end_date > NOW()
            ) combined
        ");
        $stmt->execute();
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $uid) {
            if (!isset($data[$uid])) {
                $data[$uid] = ['receipt_scans' => 0, 'ai_imports' => 0];
            }
            $data[$uid]['has_premium'] = true;
        }
    } catch (PDOException $e) {
        error_log("Error fetching usage data: " . $e->getMessage());
    }

    return $data;
}

// Function to get all users with optional filters
function get_all_users($search = '', $date_from = '', $date_to = '', $ban_status = 'all')
{
    $db = get_db_connection();
    $users = [];

    $query = 'SELECT u.* FROM community_users u WHERE 1=1';
    $types = '';
    $params = [];

    if (!empty($search)) {
        $query .= ' AND (u.username LIKE ? OR u.email LIKE ?)';
        $search_param = '%' . $search . '%';
        $types .= 'ss';
        $params[] = $search_param;
        $params[] = $search_param;
    }

    if (!empty($date_from)) {
        $query .= ' AND DATE(u.created_at) >= ?';
        $types .= 's';
        $params[] = $date_from;
    }

    if (!empty($date_to)) {
        $query .= ' AND DATE(u.created_at) <= ?';
        $types .= 's';
        $params[] = $date_to;
    }

    // Handle ban status filter
    if ($ban_status === 'banned') {
        $query .= ' AND EXISTS (SELECT 1 FROM user_bans b WHERE b.user_id = u.id AND b.is_active = 1 AND (b.expires_at IS NULL OR b.expires_at > NOW()))';
    } elseif ($ban_status === 'unbanned') {
        $query .= ' AND NOT EXISTS (SELECT 1 FROM user_bans b WHERE b.user_id = u.id AND b.is_active = 1 AND (b.expires_at IS NULL OR b.expires_at > NOW()))';
    }

    $query .= ' ORDER BY u.created_at DESC';

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['is_banned'] = is_user_banned($row['id']);
            $users[] = $row;
        }
    } else {
        $result = $db->query($query);

        while ($row = $result->fetch_assoc()) {
            $row['is_banned'] = is_user_banned($row['id']);
            $users[] = $row;
        }
    }

    return $users;
}

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_preset = isset($_GET['date_preset']) ? trim($_GET['date_preset']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$ban_status = isset($_GET['ban_status']) ? trim($_GET['ban_status']) : 'all';

// Calculate date range based on preset
if (!empty($date_preset) && $date_preset !== 'custom') {
    $date_to = date('Y-m-d'); // Today

    switch ($date_preset) {
        case 'today':
            $date_from = date('Y-m-d');
            break;
        case 'last_week':
            $date_from = date('Y-m-d', strtotime('-7 days'));
            break;
        case 'last_month':
            $date_from = date('Y-m-d', strtotime('-30 days'));
            break;
        case 'last_year':
            $date_from = date('Y-m-d', strtotime('-365 days'));
            break;
        case 'last_3_years':
            $date_from = date('Y-m-d', strtotime('-1095 days'));
            break;
        case 'last_5_years':
            $date_from = date('Y-m-d', strtotime('-1825 days'));
            break;
    }
}

// Get users (filtered)
$users = get_all_users($search, $date_from, $date_to, $ban_status);

// Get user statistics for dashboard
$db = get_db_connection();

// Total users
$total_users = count($users);

// Verified users count
$verified_count = 0;
foreach ($users as $user) {
    if ($user['email_verified']) {
        $verified_count++;
    }
}

// Admin users count
$admin_count = 0;
foreach ($users as $user) {
    if ($user['role'] === 'admin') {
        $admin_count++;
    }
}

// Banned users count
$banned_count = 0;
foreach ($users as $user) {
    if ($user['is_banned']) {
        $banned_count++;
    }
}

// Get usage data and limits
$usage_data = get_user_usage_data();
$pricing_config = get_pricing_config();
$receipt_limit = $pricing_config['receipt_scan_monthly_limit'];
$ai_import_limit = $pricing_config['ai_import_monthly_limit'];

// Check for flash messages
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

include '../admin_header.php';
?>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../search.css">
<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<div class="container">
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="stat-value"><?php echo $total_users; ?></div>
        </div>
        <div class="stat-card">
            <h3>Verified Users</h3>
            <div class="stat-value"><?php echo $verified_count; ?></div>
        </div>
        <div class="stat-card">
            <h3>Admin Users</h3>
            <div class="stat-value"><?php echo $admin_count; ?></div>
        </div>
        <div class="stat-card">
            <h3>Banned Users</h3>
            <div class="stat-value"><?php echo $banned_count; ?></div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2>Users</h2>
            <div class="search-container">
                <form method="GET" action="" class="search-form">
                    <input type="text" 
                           id="search" 
                           name="search" 
                           placeholder="Search by username or email..."
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="search-input">
                    <button type="submit" class="search-button">
                        <?= svg_icon('search') ?>
                        Search
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="index.php" class="clear-button">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Filter Options -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
                
                <div class="filter-group">
                    <label for="date_preset">Date Range</label>
                    <select name="date_preset" id="date_preset" onchange="this.form.submit()">
                        <option value="">All Time</option>
                        <option value="today" <?php echo $date_preset === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="last_week" <?php echo $date_preset === 'last_week' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="last_month" <?php echo $date_preset === 'last_month' ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="last_year" <?php echo $date_preset === 'last_year' ? 'selected' : ''; ?>>Last Year</option>
                        <option value="last_3_years" <?php echo $date_preset === 'last_3_years' ? 'selected' : ''; ?>>Last 3 Years</option>
                        <option value="last_5_years" <?php echo $date_preset === 'last_5_years' ? 'selected' : ''; ?>>Last 5 Years</option>
                        <option value="custom" <?php echo $date_preset === 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="ban_status">Ban Status</label>
                    <select name="ban_status" id="ban_status" onchange="this.form.submit()">
                        <option value="all" <?php echo $ban_status === 'all' ? 'selected' : ''; ?>>All Users</option>
                        <option value="banned" <?php echo $ban_status === 'banned' ? 'selected' : ''; ?>>Banned</option>
                        <option value="unbanned" <?php echo $ban_status === 'unbanned' ? 'selected' : ''; ?>>Not Banned</option>
                    </select>
                </div>

                <div id="custom_date_range" class="custom-date-range" style="display: <?php echo $date_preset === 'custom' ? 'flex' : 'none'; ?>;">
                    <div class="filter-group">
                        <label for="date_from">From</label>
                        <input type="date" 
                               name="date_from" 
                               id="date_from"
                               value="<?php echo htmlspecialchars($date_from); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="date_to">To</label>
                        <input type="date" 
                               name="date_to" 
                               id="date_to"
                               value="<?php echo htmlspecialchars($date_to); ?>">
                    </div>
                    <button type="submit" class="apply-button">Apply</button>
                </div>
            </form>
        </div>

        <?php if (empty($users)): ?>
            <div class="no-results">
                <?= svg_icon('alert-circle') ?>
                <p>No users found matching your criteria</p>
            </div>
        <?php else: ?>
            <form id="bulk-form" method="POST" action="">
                <div class="bulk-actions-bar">
                    <div class="selection-info">
                        <span id="selected-count">0</span> users selected
                    </div>
                    <div class="bulk-actions">
                        <button type="button" class="btn btn-bulk btn-unban" data-action="unban" disabled>
                            <?= svg_icon('shield-check') ?>
                            Unban Selected
                        </button>
                        <button type="button" class="btn btn-bulk btn-reset-usage" data-action="reset_usage" disabled>
                            <?= svg_icon('refresh') ?>
                            Reset Usage
                        </button>
                        <button type="button" class="btn btn-bulk btn-delete" data-action="delete" disabled>
                            <?= svg_icon('trash') ?>
                            Delete Selected
                        </button>
                    </div>
                </div>

                <input type="hidden" name="bulk_action" id="bulk_action_input">

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="checkbox-column">
                                    <div class="checkbox">
                                        <input type="checkbox" id="select-all">
                                        <label for="select-all"></label>
                                    </div>
                                </th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Verified</th>
                                <th>Banned</th>
                                <th>Receipt Scans</th>
                                <th>AI Imports</th>
                                <th>Created</th>
                                <th>Last Login</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="checkbox-column">
                                        <div class="checkbox">
                                            <input type="checkbox" 
                                                name="selected_ids[]" 
                                                value="<?php echo htmlspecialchars($user['id']); ?>"
                                                class="row-checkbox"
                                                data-banned="<?php echo $user['is_banned'] ? '1' : '0'; ?>"
                                                id="user-<?php echo htmlspecialchars($user['id']); ?>">
                                            <label for="user-<?php echo htmlspecialchars($user['id']); ?>"></label>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $user['role'] === 'admin' ? 'admin' : 'user'; ?>">
                                            <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['email_verified']): ?>
                                            <span class="badge badge-success">Verified</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['is_banned']): ?>
                                            <span class="badge badge-banned">Banned</span>
                                        <?php else: ?>
                                            <span class="badge badge-active">Active</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php
                                        $uid = $user['id'];
                                        $has_usage = isset($usage_data[$uid]);
                                        $scans = $has_usage ? ($usage_data[$uid]['receipt_scans'] ?? 0) : 0;
                                        $imports = $has_usage ? ($usage_data[$uid]['ai_imports'] ?? 0) : 0;
                                        $has_premium = $has_usage && !empty($usage_data[$uid]['has_premium']);
                                    ?>
                                    <td>
                                        <?php if ($has_premium): ?>
                                            <span class="usage-count <?php echo $scans >= $receipt_limit ? 'usage-maxed' : ''; ?>"><?php echo $scans; ?> / <?php echo $receipt_limit; ?></span>
                                        <?php else: ?>
                                            <span class="usage-na">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($has_premium): ?>
                                            <span class="usage-count <?php echo $imports >= $ai_import_limit ? 'usage-maxed' : ''; ?>"><?php echo $imports; ?> / <?php echo $ai_import_limit; ?></span>
                                        <?php else: ?>
                                            <span class="usage-na">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))); ?></td>
                                    <td><?php echo $user['last_login'] ? htmlspecialchars(date('Y-m-d', strtotime($user['last_login']))) : 'Never'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bulk selection functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');
        const bulkButtons = document.querySelectorAll('.btn-bulk');
        const unbanButton = document.querySelector('.btn-unban');
        const resetUsageButton = document.querySelector('.btn-reset-usage');
        const deleteButton = document.querySelector('.btn-delete');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkForm = document.getElementById('bulk-form');
        const bulkActionInput = document.getElementById('bulk_action_input');

        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const count = checkedBoxes.length;
            selectedCountSpan.textContent = count;

            // Check if any banned users are selected
            let bannedUsersSelected = 0;
            checkedBoxes.forEach(checkbox => {
                if (checkbox.dataset.banned === '1') {
                    bannedUsersSelected++;
                }
            });

            // Enable/disable buttons based on selection
            if (count === 0) {
                unbanButton.disabled = true;
                resetUsageButton.disabled = true;
                deleteButton.disabled = true;
            } else {
                // Only enable unban if at least one banned user is selected
                unbanButton.disabled = bannedUsersSelected === 0;
                resetUsageButton.disabled = false;
                deleteButton.disabled = false;
            }

            // Update select-all checkbox state
            if (count === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (count === rowCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox changes
        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Bulk action buttons
        bulkButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.dataset.action;
                const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
                const count = checkedBoxes.length;

                if (count === 0) return;

                let confirmMessage = '';

                if (action === 'delete') {
                    confirmMessage = `Are you sure you want to delete ${count} user${count > 1 ? 's' : ''}? This action cannot be undone.`;
                } else if (action === 'unban') {
                    confirmMessage = `Are you sure you want to unban ${count} user${count > 1 ? 's' : ''}? They will be able to post again.`;
                } else if (action === 'reset_usage') {
                    confirmMessage = `Are you sure you want to reset all usage counts for ${count} user${count > 1 ? 's' : ''}?`;
                }

                if (confirm(confirmMessage)) {
                    if (action === 'reset_usage') {
                        // Handle reset usage via AJAX
                        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
                        const formData = new FormData();
                        formData.append('reset_usage', '1');
                        userIds.forEach(id => formData.append('user_ids[]', id));

                        resetUsageButton.disabled = true;
                        resetUsageButton.textContent = 'Resetting...';

                        fetch('index.php', { method: 'POST', body: formData })
                            .then(r => r.json())
                            .then(data => {
                                if (data.success) {
                                    sessionStorage.setItem('scrollPosition', window.scrollY);
                                    window.location.reload();
                                } else {
                                    alert('Reset failed: ' + (data.error || 'Unknown error'));
                                    resetUsageButton.disabled = false;
                                    resetUsageButton.textContent = 'Reset Usage';
                                }
                            })
                            .catch(() => {
                                alert('Network error');
                                resetUsageButton.disabled = false;
                                resetUsageButton.textContent = 'Reset Usage';
                            });
                        return;
                    }

                    bulkActionInput.value = action;
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                    bulkForm.submit();
                }
            });
        });

        // Initial count
        updateSelectedCount();

        // Date preset select handling
        const datePresetSelect = document.getElementById('date_preset');
        const customDateRange = document.getElementById('custom_date_range');

        datePresetSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'flex';
            } else {
                customDateRange.style.display = 'none';
            }
        });

        // If user clicks on date inputs, select custom option
        const dateInputs = customDateRange.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            input.addEventListener('focus', function() {
                datePresetSelect.value = 'custom';
                customDateRange.style.display = 'flex';
            });
        });

        // Restore scroll position if it exists in sessionStorage
        if (sessionStorage.getItem('scrollPosition')) {
            window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
            sessionStorage.removeItem('scrollPosition');
        }

        // Save scroll position when submitting forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            });
        });

        // Also save position when clicking links
        const links = document.querySelectorAll('a[href^="index.php"]');
        links.forEach(link => {
            link.addEventListener('click', function() {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            });
        });

        // Auto-clear search when textbox is emptied
        const searchInput = document.querySelector('#search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                if (this.value.trim() === '') {
                    const datePreset = document.getElementById('date_preset').value;
                    const banStatus = document.getElementById('ban_status').value;

                    if (!datePreset && banStatus === 'all') {
                        sessionStorage.setItem('scrollPosition', window.scrollY);
                        window.location.href = 'index.php';
                    }
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                    this.value = '';
                    const datePreset = document.getElementById('date_preset').value;
                    const banStatus = document.getElementById('ban_status').value;

                    if (!datePreset && banStatus === 'all') {
                        window.location.href = 'index.php';
                    }
                }
            });
        }
    });


</script>

        </main>
    </div>
</body>

</html>