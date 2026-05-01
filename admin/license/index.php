<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../email_sender.php';
require_once __DIR__ . '/../../license_functions.php';
require_once __DIR__ . '/../../config/pricing.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_csrf_token() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return false;
    }
    return true;
}

// Set page variables for the header
$page_title = "Subscription Administration";
$page_description = "Manage Premium subscriptions and free subscription keys";

// Function to get Premium subscriptions
function get_premium_subscriptions($search_filter = '')
{
    global $pdo;
    $subscriptions = [];

    try {
        $query = "
            SELECT s.*, u.username
            FROM premium_subscriptions s
            LEFT JOIN community_users u ON s.user_id = u.id
            WHERE s.payment_method != 'free_key'
              AND s.environment = ?
        ";
        $params = [current_environment()];

        if (!empty($search_filter)) {
            $query .= " AND (s.email LIKE ? OR s.subscription_id LIKE ? OR u.username LIKE ?)";
            $search_param = '%' . $search_filter . '%';
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
        }

        $query .= " ORDER BY s.created_at DESC LIMIT 100";

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching Premium subscriptions: " . $e->getMessage());
    }

    return $subscriptions;
}

// Function to get Premium subscription keys (free/promo)
function get_premium_subscription_keys()
{
    global $pdo;
    $keys = [];

    try {
        $stmt = $pdo->query("SELECT * FROM premium_subscription_keys ORDER BY created_at DESC");
        $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching Premium subscription keys: " . $e->getMessage());
    }

    return $keys;
}

// Function to generate Premium subscription key
function generate_premium_subscription_key($email = null, $duration_months = 1, $notes = '')
{
    global $pdo;

    // Reuse the shared license key generator with 'premium' type prefix
    $key = generate_license_key('premium');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO premium_subscription_keys (subscription_key, email, duration_months, notes)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$key, $email ?: null, $duration_months, $notes ?: null]);
        return $key;
    } catch (PDOException $e) {
        error_log("Error generating Premium subscription key: " . $e->getMessage());
        return false;
    }
}

// Get chart data - Premium subscriptions by date
function get_subscription_chart_data()
{
    global $pdo;
    $data = [];

    try {
        $stmt = $pdo->prepare("
            SELECT DATE(created_at) as date,
                   COUNT(*) as total,
                   SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active
            FROM premium_subscriptions
            WHERE payment_method != 'free_key'
              AND environment = ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ");
        $stmt->execute([current_environment()]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching subscription chart data: " . $e->getMessage());
    }

    return $data;
}

// Get usage data for a list of license keys
function get_license_usage($license_keys)
{
    global $pdo;
    $usage = [];
    if (empty($license_keys)) return $usage;

    $usage_month = date('Y-m-01');
    $placeholders = implode(',', array_fill(0, count($license_keys), '?'));

    try {
        $stmt = $pdo->prepare("SELECT license_key, scan_count FROM receipt_scan_usage WHERE license_key IN ($placeholders) AND usage_month = ?");
        $stmt->execute(array_merge($license_keys, [$usage_month]));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $usage[$row['license_key']]['receipt_scans'] = (int)$row['scan_count'];
        }

        $stmt = $pdo->prepare("SELECT license_key, scan_count FROM ai_import_usage WHERE license_key IN ($placeholders) AND usage_month = ?");
        $stmt->execute(array_merge($license_keys, [$usage_month]));
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!isset($usage[$row['license_key']])) $usage[$row['license_key']] = ['receipt_scans' => 0];
            $usage[$row['license_key']]['ai_imports'] = (int)$row['scan_count'];
        }
    } catch (PDOException $e) {
        error_log("Error fetching license usage: " . $e->getMessage());
    }

    return $usage;
}

// Handle usage reset via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_usage'])) {
    header('Content-Type: application/json');

    if (!verify_csrf_token()) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }

    $license_keys = $_POST['license_keys'] ?? [];
    if (!is_array($license_keys)) $license_keys = [$license_keys];
    $license_keys = array_filter(array_map('trim', $license_keys));

    if (empty($license_keys)) {
        echo json_encode(['success' => false, 'error' => 'No license keys provided']);
        exit;
    }

    $usage_month = date('Y-m-01');
    $placeholders = implode(',', array_fill(0, count($license_keys), '?'));
    $params = array_merge($license_keys, [$usage_month]);

    try {
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

// Handle form submissions
$generated_sub_key = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $_SESSION['message'] = "Invalid request. Please try again.";
        $_SESSION['message_type'] = 'error';
        header('Location: index.php');
        exit;
    }

    if (isset($_POST['generate_sub_key'])) {
        $email = !empty($_POST['sub_email']) ? filter_var($_POST['sub_email'], FILTER_VALIDATE_EMAIL) : null;
        $duration = intval($_POST['duration_months'] ?? 1);
        $notes = trim($_POST['notes'] ?? '');

        // Require a valid email address
        if (!$email) {
            $_SESSION['message'] = "Please enter a valid email address.";
            $_SESSION['message_type'] = 'error';
            header('Location: index.php#free-sub-keys');
            exit;
        }

        // Allow 0 for permanent, otherwise 1-24 months
        if ($duration < 0) $duration = 1;
        if ($duration > 24 && $duration !== 0) $duration = 24;

        $generated_sub_key = generate_premium_subscription_key($email, $duration, $notes);

        if ($generated_sub_key) {
            $_SESSION['generated_sub_key'] = $generated_sub_key;
            $_SESSION['sub_key_duration'] = $duration;
            $_SESSION['sub_key_email'] = $email;

            // Send email notification
            $email_sent = send_free_subscription_key_email($email, $generated_sub_key, $duration, $notes);
            $_SESSION['message'] = $email_sent
                ? "Free subscription key generated and email sent to $email!"
                : "Free subscription key generated, but failed to send email to $email.";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Failed to generate subscription key.";
            $_SESSION['message_type'] = 'error';
        }
        header('Location: index.php#free-sub-keys');
        exit;
    } elseif (isset($_POST['bulk_sub_key_action'])) {
        // Handle bulk actions for subscription keys
        $action = $_POST['bulk_sub_key_action'];
        $key_ids = $_POST['selected_sub_keys'] ?? [];

        if (!empty($key_ids)) {
            $count = count($key_ids);
            $placeholders = implode(',', array_fill(0, $count, '?'));

            if ($action === 'delete') {
                try {
                    $stmt = $pdo->prepare("DELETE FROM premium_subscription_keys WHERE id IN ($placeholders)");
                    $stmt->execute($key_ids);
                    $deleted = $stmt->rowCount();
                    if ($deleted > 0) {
                        $_SESSION['message'] = "$deleted subscription key(s) deleted successfully.";
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = "No keys deleted.";
                        $_SESSION['message_type'] = 'error';
                    }
                } catch (PDOException $e) {
                    $_SESSION['message'] = "Error deleting keys.";
                    $_SESSION['message_type'] = 'error';
                }
            } elseif ($action === 'resend_email') {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM premium_subscription_keys WHERE id IN ($placeholders)");
                    $stmt->execute($key_ids);
                    $keys_to_resend = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $sent = 0;
                    $skipped = 0;
                    foreach ($keys_to_resend as $k) {
                        if (empty($k['email'])) {
                            $skipped++;
                            continue;
                        }
                        if (send_free_subscription_key_email($k['email'], $k['subscription_key'], $k['duration_months'], $k['notes'] ?? '')) {
                            $sent++;
                        }
                    }

                    $msg = "$sent email(s) resent successfully.";
                    if ($skipped > 0) {
                        $msg .= " $skipped key(s) skipped (no email address).";
                    }
                    $_SESSION['message'] = $msg;
                    $_SESSION['message_type'] = $sent > 0 ? 'success' : 'error';
                } catch (PDOException $e) {
                    error_log("Error resending license emails: " . $e->getMessage());
                    $_SESSION['message'] = "Error resending emails.";
                    $_SESSION['message_type'] = 'error';
                }
            }

            header('Location: index.php#free-sub-keys');
            exit;
        }
    } elseif (isset($_POST['bulk_subscription_action'])) {
        // Handle bulk actions for Premium subscriptions (Give Credit)
        $action = $_POST['bulk_subscription_action'];
        $subscription_ids = $_POST['selected_subscriptions'] ?? [];
        $credit_amount = floatval($_POST['credit_amount'] ?? 0);
        $credit_note = trim($_POST['credit_note'] ?? '');

        if (!empty($subscription_ids) && $action === 'give_credit' && $credit_amount > 0) {
            $count = count($subscription_ids);
            $placeholders = implode(',', array_fill(0, $count, '?'));
            $success_count = 0;
            $email_success = 0;

            try {
                // Update credit_balance for selected subscriptions. Env filter
                // prevents a stale prod form submit from mutating sandbox subs
                // (and vice versa) — without it, only the follow-up SELECT was
                // env-scoped, which is too late.
                $stmt = $pdo->prepare("
                    UPDATE premium_subscriptions
                    SET credit_balance = credit_balance + ?
                    WHERE id IN ($placeholders)
                      AND environment = ?
                ");
                $params = array_merge([$credit_amount], $subscription_ids, [current_environment()]);
                $stmt->execute($params);
                $success_count = $stmt->rowCount();

                // Log the credit addition for debugging
                error_log("Bulk credit: Added \$$credit_amount to " . count($subscription_ids) . " subscriptions. IDs: " . implode(',', $subscription_ids) . ". Rows affected: $success_count");

                // Send emails to affected users and verify credit was saved.
                // Env filter prevents a stale form submit from touching subs from a different environment.
                $stmt = $pdo->prepare("
                    SELECT id, email, subscription_id, credit_balance
                    FROM premium_subscriptions
                    WHERE id IN ($placeholders)
                      AND environment = ?
                ");
                $stmt->execute(array_merge($subscription_ids, [current_environment()]));
                $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Log the actual credit balances after update
                foreach ($subscriptions as $s) {
                    error_log("Bulk credit verify: Subscription {$s['subscription_id']} now has credit_balance = {$s['credit_balance']}");
                }

                foreach ($subscriptions as $sub) {
                    if (send_free_credit_email($sub['email'], $credit_amount, $credit_note, $sub['subscription_id'])) {
                        $email_success++;
                    }
                }

                $_SESSION['message'] = "\$$credit_amount credit added to $success_count subscription(s). $email_success email(s) sent.";
                $_SESSION['message_type'] = 'success';
            } catch (PDOException $e) {
                error_log("Error adding bulk credit: " . $e->getMessage());
                $_SESSION['message'] = "Error adding credit to subscriptions.";
                $_SESSION['message_type'] = 'error';
            }

            header('Location: index.php#premium-subscriptions');
            exit;
        } elseif (!empty($subscription_ids) && $action === 'resend_email') {
            $count = count($subscription_ids);
            $placeholders = implode(',', array_fill(0, $count, '?'));

            try {
                $stmt = $pdo->prepare("
                    SELECT email, subscription_id, billing_cycle, end_date
                    FROM premium_subscriptions
                    WHERE id IN ($placeholders)
                      AND environment = ?
                ");
                $stmt->execute(array_merge($subscription_ids, [current_environment()]));
                $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $sent = 0;
                foreach ($subscriptions as $sub) {
                    if (resend_subscription_id_email($sub['email'], $sub['subscription_id'], $sub['billing_cycle'], $sub['end_date'])) {
                        $sent++;
                    }
                }

                $_SESSION['message'] = "License key email resent to $sent of $count recipient(s).";
                $_SESSION['message_type'] = $sent > 0 ? 'success' : 'error';
            } catch (PDOException $e) {
                error_log("Error resending subscription emails: " . $e->getMessage());
                $_SESSION['message'] = "Error resending emails.";
                $_SESSION['message_type'] = 'error';
            }

            header('Location: index.php#premium-subscriptions');
            exit;
        } else {
            $_SESSION['message'] = "Please select subscriptions and enter a valid credit amount.";
            $_SESSION['message_type'] = 'error';
            header('Location: index.php#premium-subscriptions');
            exit;
        }
    }
}

// Check for session messages
if (isset($_SESSION['generated_sub_key'])) {
    $generated_sub_key = $_SESSION['generated_sub_key'];
    unset($_SESSION['generated_sub_key']);
}
$sub_key_duration = $_SESSION['sub_key_duration'] ?? 0;
unset($_SESSION['sub_key_duration']);
$sub_key_email = $_SESSION['sub_key_email'] ?? '';
unset($_SESSION['sub_key_email']);

// Get filter parameters
// Get data
$premium_subscriptions = get_premium_subscriptions();
$premium_subscription_keys = get_premium_subscription_keys();
$subscription_chart_data = get_subscription_chart_data();

// Get usage data and limits
$pricing_config = get_pricing_config();
$receipt_limit = $pricing_config['receipt_scan_monthly_limit'];
$ai_import_limit = $pricing_config['ai_import_monthly_limit'];

$all_sub_ids = array_column($premium_subscriptions, 'subscription_id');
$all_key_ids = array_column($premium_subscription_keys, 'subscription_key');
$usage_data = get_license_usage(array_merge($all_sub_ids, $all_key_ids));

$active_ai_subs = 0;
foreach ($premium_subscriptions as $sub) {
    if ($sub['status'] === 'active') $active_ai_subs++;
}

$unredeemed_keys = 0;
foreach ($premium_subscription_keys as $key) {
    if ($key['redeemed_at'] === null) $unredeemed_keys++;
}

// Flash messages
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../search.css">
<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<style>
.btn-copy {
    display: inline-block;
    margin-left: 8px;
    padding: 2px 8px;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    cursor: pointer;
    vertical-align: middle;
    transition: all 0.15s ease;
    min-width: 60px;
    text-align: center;
}
.btn-copy:hover {
    background: #e5e7eb;
    color: #374151;
}
.btn-copy.copied {
    background: #d1fae5;
    color: #065f46;
    border-color: #6ee7b7;
}
[data-theme="dark"] .btn-copy {
    background: #1e293b;
    color: #94a3b8;
    border-color: #334155;
}
[data-theme="dark"] .btn-copy:hover {
    background: #334155;
    color: #e2e8f0;
}
[data-theme="dark"] .btn-copy.copied {
    background: #064e3b;
    color: #6ee7b7;
    border-color: #065f46;
}
</style>

<script>
    const subscriptionChartData = <?php echo json_encode($subscription_chart_data); ?>;
</script>
<script src="main.js"></script>

<div class="container">
    <!-- Section Tabs -->
    <div class="section-tabs">
        <button class="section-tab active" data-tab="premium-subscriptions">Premium Subscriptions</button>
        <button class="section-tab" data-tab="free-sub-keys">Free Premium Subscription Keys</button>
    </div>

    <?php if (!empty($message)): ?>
        <div class="<?php echo $message_type === 'success' ? 'success-message' : 'error-message'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Premium Subscriptions Tab -->
    <div id="premium-subscriptions" class="tab-content active">
        <div class="stats-grid">
            <div class="stat-card active">
                <h3>Active Subscriptions</h3>
                <div class="value"><?php echo $active_ai_subs; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Subscriptions</h3>
                <div class="value"><?php echo count($premium_subscriptions); ?></div>
            </div>
        </div>

        <!-- Subscription Chart -->
        <div class="chart-container">
            <h2>Premium Subscriptions Over Time</h2>
            <canvas id="subscriptionsChart"></canvas>
        </div>

        <div class="table-container">
            <h2>Premium Subscriptions</h2>

            <div style="margin-bottom: 12px;">
                <input type="text" id="subscriptionSearch" placeholder="Search by email, ID, or username..." oninput="filterTable(this, 'subscription-table-body')" style="max-width: 350px;">
            </div>

            <!-- Bulk Actions for Premium Subscriptions -->
            <div class="bulk-actions-container" id="subscription-bulk-actions">
                <div class="selection-info">
                    <span id="subscription-selected-count">0</span> selected
                </div>
                <div class="bulk-buttons">
                    <button type="button" class="btn btn-bulk btn-purple" id="open-credit-modal" disabled>
                        Give Credit
                    </button>
                    <button type="button" class="btn btn-bulk btn-blue" id="subscription-bulk-resend" disabled>
                        Resend Email
                    </button>
                    <button type="button" class="btn btn-bulk btn-reset-usage" id="subscription-bulk-reset-usage" disabled>
                        Reset Usage
                    </button>
                </div>
            </div>

            <?php if (empty($premium_subscriptions)): ?>
                <p>No Premium subscriptions found.</p>
            <?php else: ?>
                <form id="subscription-bulk-form" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="bulk_subscription_action" id="bulk_subscription_action_input" value="give_credit">
                    <input type="hidden" name="credit_amount" id="credit_amount_input">
                    <input type="hidden" name="credit_note" id="credit_note_input">
                    <div class="table-responsive">
                        <table data-paginate="25">
                            <thead>
                                <tr>
                                    <th class="checkbox-column">
                                        <div class="checkbox">
                                            <input type="checkbox" id="subscription-select-all">
                                            <label for="subscription-select-all"></label>
                                        </div>
                                    </th>
                                    <th>License Key</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Plan</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Receipt Scans</th>
                                    <th>AI Imports</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tbody id="subscription-table-body">
                                <?php foreach ($premium_subscriptions as $sub): ?>
                                    <tr>
                                        <td class="checkbox-column">
                                            <div class="checkbox">
                                                <input type="checkbox" name="selected_subscriptions[]" value="<?php echo $sub['id']; ?>" class="subscription-checkbox" id="sub-<?php echo $sub['id']; ?>" data-license-key="<?php echo htmlspecialchars($sub['subscription_id']); ?>">
                                                <label for="sub-<?php echo $sub['id']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="key-field"><?php echo htmlspecialchars($sub['subscription_id']); ?></td>
                                        <td><?php echo htmlspecialchars($sub['username'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($sub['email']); ?></td>
                                        <td><?php echo ucfirst($sub['billing_cycle']); ?> - $<?php echo number_format($sub['amount'], 2); ?></td>
                                        <td>
                                            <?php
                                            $providerNames = ['paypal' => 'PayPal', 'stripe' => 'Stripe', 'square' => 'Square'];
                                            $providerColors = ['paypal' => '#003087', 'stripe' => '#635bff', 'square' => '#006aff'];
                                            $method = strtolower($sub['payment_method'] ?? '');
                                            $providerName = $providerNames[$method] ?? ucfirst($sub['payment_method'] ?? 'N/A');
                                            $providerColor = $providerColors[$method] ?? '#6b7280';
                                            ?>
                                            <span style="color: <?php echo $providerColor; ?>; font-weight: 600;"><?php echo $providerName; ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $sub['status']; ?>">
                                                <?php echo ucfirst($sub['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($sub['start_date'])); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($sub['end_date'])); ?></td>
                                        <?php
                                            $sub_key = $sub['subscription_id'];
                                            $scans = $usage_data[$sub_key]['receipt_scans'] ?? 0;
                                            $imports = $usage_data[$sub_key]['ai_imports'] ?? 0;
                                        ?>
                                        <td><span class="usage-count <?php echo $scans >= $receipt_limit ? 'usage-maxed' : ''; ?>"><?php echo $scans; ?> / <?php echo $receipt_limit; ?></span></td>
                                        <td><span class="usage-count <?php echo $imports >= $ai_import_limit ? 'usage-maxed' : ''; ?>"><?php echo $imports; ?> / <?php echo $ai_import_limit; ?></span></td>
                                        <td>$<?php echo number_format($sub['credit_balance'] ?? 0, 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Credit Modal -->
    <div id="credit-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Give Free Credit</h3>
                <button type="button" class="modal-close" id="close-credit-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Add free credit to <strong><span id="modal-selected-count">0</span></strong> selected subscription(s).</p>
                <div class="form-group">
                    <label for="modal-credit-amount">Credit Amount ($)</label>
                    <input type="number" id="modal-credit-amount" min="0.01" step="0.01" placeholder="e.g., 5.00" required>
                </div>
                <div class="form-group">
                    <label for="modal-credit-note">Note (optional)</label>
                    <textarea id="modal-credit-note" rows="3" placeholder="e.g., Thank you for being a loyal customer!"></textarea>
                </div>
                <p class="modal-note">An email will be sent to each user notifying them of the free credit.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-credit-modal">Cancel</button>
                <button type="button" class="btn btn-purple" id="confirm-give-credit">Give Credit</button>
            </div>
        </div>
    </div>

    <!-- Free Subscription Keys Tab -->
    <div id="free-sub-keys" class="tab-content">
        <div class="stats-grid">
            <div class="stat-card free">
                <h3>Available Keys</h3>
                <div class="value"><?php echo $unredeemed_keys; ?></div>
            </div>
            <div class="stat-card pending">
                <h3>Redeemed Keys</h3>
                <div class="value"><?php echo count($premium_subscription_keys) - $unredeemed_keys; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Generated</h3>
                <div class="value"><?php echo count($premium_subscription_keys); ?></div>
            </div>
        </div>

        <div class="table-container" style="padding: 20px 20px 30px 20px;">
            <h2>Generate Free Subscription Key</h2>
            <?php if ($generated_sub_key): ?>
                <div class="key-display">
                    <strong>Key Generated:</strong><br>
                    <code><?php echo htmlspecialchars($generated_sub_key); ?></code><br>
                    <small>Duration: <?php echo $sub_key_duration == 0 ? 'Permanent' : $sub_key_duration . ' month(s)'; ?> | For: <?php echo htmlspecialchars($sub_key_email); ?></small>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="generate-form-grid">
                    <div class="form-group">
                        <label for="sub_email">Recipient Email</label>
                        <input type="email" id="sub_email" name="sub_email" placeholder="customer@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="duration_months">Duration</label>
                        <select id="duration_months" name="duration_months">
                            <option value="1">1 month</option>
                            <option value="3">3 months</option>
                            <option value="6">6 months</option>
                            <option value="12">12 months</option>
                            <option value="0">Permanent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <input type="text" id="notes" name="notes" placeholder="e.g., Giveaway winner">
                         <p style="position: absolute; margin: 5px 0 20px; color: var(--gray-500);">This will also appear in the recipient's email.</p>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="generate_sub_key" class="btn btn-purple" style="margin-top: 24px;">Generate Key</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <h2>Free Subscription Keys</h2>

            <!-- Bulk Actions for Subscription Keys -->
            <div class="bulk-actions-container" id="sub-key-bulk-actions">
                <div class="selection-info">
                    <span id="sub-key-selected-count">0</span> selected
                </div>
                <div class="bulk-buttons">
                    <button type="button" class="btn btn-bulk btn-purple" id="sub-key-bulk-resend" disabled>Resend Email</button>
                    <button type="button" class="btn btn-bulk btn-reset-usage" id="sub-key-bulk-reset-usage" disabled>Reset Usage</button>
                    <button type="button" class="btn btn-bulk btn-delete" id="sub-key-bulk-delete" data-action="delete" disabled>Delete Selected</button>
                </div>
            </div>

            <div style="margin-bottom: 12px;">
                <input type="text" id="freeKeysSearch" placeholder="Search by key, email, or notes..." oninput="filterTable(this, 'free-keys-table-body')" style="max-width: 350px;">
            </div>

            <?php if (empty($premium_subscription_keys)): ?>
                <p>No free subscription keys generated yet.</p>
            <?php else: ?>
                <form id="sub-key-bulk-form" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="bulk_sub_key_action" id="bulk_sub_key_action_input">
                    <div class="table-responsive">
                        <table data-paginate="25">
                            <thead>
                                <tr>
                                    <th class="checkbox-column">
                                        <div class="checkbox">
                                            <input type="checkbox" id="sub-key-select-all">
                                            <label for="sub-key-select-all"></label>
                                        </div>
                                    </th>
                                    <th>Key</th>
                                    <th>Duration</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Receipt Scans</th>
                                    <th>AI Imports</th>
                                    <th>Created</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody id="free-keys-table-body">
                                <?php foreach ($premium_subscription_keys as $key): ?>
                                    <tr class="<?php echo $key['redeemed_at'] ? 'redeemed-row' : ''; ?>">
                                        <td class="checkbox-column">
                                            <div class="checkbox">
                                                <input type="checkbox" name="selected_sub_keys[]" value="<?php echo $key['id']; ?>" class="sub-key-checkbox" id="sub-key-<?php echo $key['id']; ?>" data-license-key="<?php echo htmlspecialchars($key['subscription_key']); ?>">
                                                <label for="sub-key-<?php echo $key['id']; ?>"></label>
                                            </div>
                                        </td>
                                        <td class="key-field">
                                            <?php echo htmlspecialchars($key['subscription_key']); ?>
                                            <button type="button" class="btn-copy" onclick="copyToClipboard('<?php echo htmlspecialchars($key['subscription_key'], ENT_QUOTES); ?>', this)" title="Copy key">Copy</button>
                                        </td>
                                        <td><?php echo $key['duration_months'] == 0 ? '<span style="color:#8b5cf6;font-weight:500;">Permanent</span>' : $key['duration_months'] . ' month' . ($key['duration_months'] > 1 ? 's' : ''); ?></td>
                                        <td><?php echo $key['email'] ? htmlspecialchars($key['email']) : '<span style="color:#9ca3af;">Any user</span>'; ?></td>
                                        <td>
                                            <?php if ($key['redeemed_at']): ?>
                                                <span class="badge badge-redeemed">Redeemed</span>
                                            <?php else: ?>
                                                <span class="badge badge-free">Available</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php
                                            $fk = $key['subscription_key'];
                                            $fk_scans = $usage_data[$fk]['receipt_scans'] ?? 0;
                                            $fk_imports = $usage_data[$fk]['ai_imports'] ?? 0;
                                            $fk_redeemed = !empty($key['redeemed_at']);
                                        ?>
                                        <td>
                                            <?php if ($fk_redeemed): ?>
                                                <span class="usage-count <?php echo $fk_scans >= $receipt_limit ? 'usage-maxed' : ''; ?>"><?php echo $fk_scans; ?> / <?php echo $receipt_limit; ?></span>
                                            <?php else: ?>
                                                <span class="usage-na">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($fk_redeemed): ?>
                                                <span class="usage-count <?php echo $fk_imports >= $ai_import_limit ? 'usage-maxed' : ''; ?>"><?php echo $fk_imports; ?> / <?php echo $ai_import_limit; ?></span>
                                            <?php else: ?>
                                                <span class="usage-na">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($key['created_at'])); ?></td>
                                        <td><?php echo $key['notes'] ? htmlspecialchars($key['notes']) : '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        btn.textContent = 'Copied!';
        btn.classList.add('copied');
        setTimeout(function() {
            btn.textContent = 'Copy';
            btn.classList.remove('copied');
        }, 1500);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Tab switching is handled centrally by admin/section-tabs.js

    // Subscription Key Bulk Selection
    const subKeySelectAll = document.getElementById('sub-key-select-all');
    const subKeyCheckboxes = document.querySelectorAll('.sub-key-checkbox');
    const subKeySelectedCount = document.getElementById('sub-key-selected-count');
    const subKeyBulkDelete = document.getElementById('sub-key-bulk-delete');
    const subKeyBulkResend = document.getElementById('sub-key-bulk-resend');
    const subKeyBulkResetUsage = document.getElementById('sub-key-bulk-reset-usage');
    const subKeyBulkForm = document.getElementById('sub-key-bulk-form');
    const subKeyBulkActionInput = document.getElementById('bulk_sub_key_action_input');

    function updateSubKeySelection() {
        const checked = document.querySelectorAll('.sub-key-checkbox:checked');
        const count = checked.length;
        subKeySelectedCount.textContent = count;
        subKeyBulkDelete.disabled = count === 0;
        if (subKeyBulkResend) subKeyBulkResend.disabled = count === 0;
        if (subKeyBulkResetUsage) subKeyBulkResetUsage.disabled = count === 0;
    }

    if (subKeySelectAll) {
        subKeySelectAll.addEventListener('change', function() {
            subKeyCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSubKeySelection();
        });
    }

    subKeyCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSubKeySelection();
            // Update select all checkbox state
            if (subKeySelectAll) {
                const allChecked = document.querySelectorAll('.sub-key-checkbox:checked').length === subKeyCheckboxes.length;
                subKeySelectAll.checked = allChecked && subKeyCheckboxes.length > 0;
            }
        });
    });

    if (subKeyBulkDelete) {
        subKeyBulkDelete.addEventListener('click', function() {
            const checked = document.querySelectorAll('.sub-key-checkbox:checked');
            if (checked.length === 0) return;

            if (confirm(`Are you sure you want to delete ${checked.length} subscription key(s)?`)) {
                subKeyBulkActionInput.value = 'delete';
                subKeyBulkForm.submit();
            }
        });
    }

    if (subKeyBulkResend) {
        subKeyBulkResend.addEventListener('click', function() {
            const checked = document.querySelectorAll('.sub-key-checkbox:checked');
            if (checked.length === 0) return;

            if (confirm(`Resend license key email to ${checked.length} recipient(s)?`)) {
                subKeyBulkActionInput.value = 'resend_email';
                subKeyBulkForm.submit();
            }
        });
    }

    // Premium Subscription Bulk Selection
    const subscriptionSelectAll = document.getElementById('subscription-select-all');
    const subscriptionCheckboxes = document.querySelectorAll('.subscription-checkbox');
    const subscriptionSelectedCount = document.getElementById('subscription-selected-count');
    const openCreditModalBtn = document.getElementById('open-credit-modal');
    const subscriptionBulkResend = document.getElementById('subscription-bulk-resend');
    const subscriptionBulkResetUsage = document.getElementById('subscription-bulk-reset-usage');
    const subscriptionBulkForm = document.getElementById('subscription-bulk-form');
    const bulkSubscriptionActionInput = document.getElementById('bulk_subscription_action_input');
    const creditAmountInput = document.getElementById('credit_amount_input');
    const creditNoteInput = document.getElementById('credit_note_input');

    // Modal elements
    const creditModal = document.getElementById('credit-modal');
    const closeCreditModalBtn = document.getElementById('close-credit-modal');
    const cancelCreditModalBtn = document.getElementById('cancel-credit-modal');
    const confirmGiveCreditBtn = document.getElementById('confirm-give-credit');
    const modalSelectedCount = document.getElementById('modal-selected-count');
    const modalCreditAmount = document.getElementById('modal-credit-amount');
    const modalCreditNote = document.getElementById('modal-credit-note');

    function updateSubscriptionSelection() {
        const checked = document.querySelectorAll('.subscription-checkbox:checked');
        const count = checked.length;
        subscriptionSelectedCount.textContent = count;
        if (openCreditModalBtn) {
            openCreditModalBtn.disabled = count === 0;
        }
        if (subscriptionBulkResend) {
            subscriptionBulkResend.disabled = count === 0;
        }
        if (subscriptionBulkResetUsage) {
            subscriptionBulkResetUsage.disabled = count === 0;
        }
    }

    if (subscriptionSelectAll) {
        subscriptionSelectAll.addEventListener('change', function() {
            subscriptionCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSubscriptionSelection();
        });
    }

    subscriptionCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSubscriptionSelection();
            // Update select all checkbox state
            if (subscriptionSelectAll) {
                const checkedCount = document.querySelectorAll('.subscription-checkbox:checked').length;
                const allChecked = checkedCount === subscriptionCheckboxes.length;
                subscriptionSelectAll.checked = allChecked && subscriptionCheckboxes.length > 0;
                subscriptionSelectAll.indeterminate = checkedCount > 0 && !allChecked;
            }
        });
    });

    // Modal functionality
    function openModal() {
        const count = document.querySelectorAll('.subscription-checkbox:checked').length;
        if (count === 0) return;
        modalSelectedCount.textContent = count;
        modalCreditAmount.value = '';
        modalCreditNote.value = '';
        creditModal.style.display = 'flex';
        modalCreditAmount.focus();
    }

    function closeModal() {
        creditModal.style.display = 'none';
    }

    if (openCreditModalBtn) {
        openCreditModalBtn.addEventListener('click', openModal);
    }

    if (subscriptionBulkResend) {
        subscriptionBulkResend.addEventListener('click', function() {
            const checked = document.querySelectorAll('.subscription-checkbox:checked');
            if (checked.length === 0) return;

            if (confirm(`Resend license key email to ${checked.length} recipient(s)?`)) {
                bulkSubscriptionActionInput.value = 'resend_email';
                creditAmountInput.value = '0';
                subscriptionBulkForm.submit();
            }
        });
    }

    if (closeCreditModalBtn) {
        closeCreditModalBtn.addEventListener('click', closeModal);
    }

    if (cancelCreditModalBtn) {
        cancelCreditModalBtn.addEventListener('click', closeModal);
    }

    // Close modal on overlay click (only if mousedown also started on backdrop)
    let modalMouseDownTarget = null;
    if (creditModal) {
        creditModal.addEventListener('mousedown', function(e) {
            modalMouseDownTarget = e.target;
        });
        creditModal.addEventListener('click', function(e) {
            if (e.target === creditModal && modalMouseDownTarget === creditModal) {
                closeModal();
            }
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && creditModal && creditModal.style.display === 'flex') {
            closeModal();
        }
    });

    // Confirm give credit
    if (confirmGiveCreditBtn) {
        confirmGiveCreditBtn.addEventListener('click', function() {
            const amount = parseFloat(modalCreditAmount.value);
            const note = modalCreditNote.value.trim();
            const checkedCount = document.querySelectorAll('.subscription-checkbox:checked').length;

            if (isNaN(amount) || amount <= 0) {
                alert('Please enter a valid credit amount greater than 0.');
                modalCreditAmount.focus();
                return;
            }

            if (confirm(`Are you sure you want to give $${amount.toFixed(2)} credit to ${checkedCount} subscription(s)?`)) {
                bulkSubscriptionActionInput.value = 'give_credit';
                creditAmountInput.value = amount;
                creditNoteInput.value = note;
                subscriptionBulkForm.submit();
            }
        });
    }

    // Allow Enter key in credit amount field to submit
    if (modalCreditAmount) {
        modalCreditAmount.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmGiveCreditBtn.click();
            }
        });
    }

    // Reset Usage helper
    function resetUsage(checkboxSelector) {
        const checked = document.querySelectorAll(checkboxSelector + ':checked');
        if (checked.length === 0) return;

        if (!confirm(`Reset usage for ${checked.length} selected license(s)? This resets the current month's receipt scan and AI import counts to 0.`)) return;

        const formData = new FormData();
        formData.append('reset_usage', '1');
        formData.append('csrf_token', '<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>');
        checked.forEach(cb => formData.append('license_keys[]', cb.dataset.licenseKey));

        fetch('index.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(() => alert('Network error resetting usage.'));
    }

    if (subscriptionBulkResetUsage) {
        subscriptionBulkResetUsage.addEventListener('click', () => resetUsage('.subscription-checkbox'));
    }

    if (subKeyBulkResetUsage) {
        subKeyBulkResetUsage.addEventListener('click', () => resetUsage('.sub-key-checkbox'));
    }
});

function filterTable(input, tbodyId) {
    const filter = input.value.toLowerCase();
    const tbody = document.getElementById(tbodyId);
    const rows = tbody.querySelectorAll('tr');
    rows.forEach(row => {
        row.classList.remove('pg-hidden');
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
    // Reset pagination after filtering
    const table = tbody.closest('table');
    if (table && table._paginator) table._paginator.reset();
}
</script>
