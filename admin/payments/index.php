<?php
session_start();
require_once '../../db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Set page variables for the header
$page_title = "Payment Portal";
$page_description = "Monitor portal payments, companies, invoices, and payment analytics";

global $pdo;

// ============================================================
// DATA QUERIES
// ============================================================

// --- Overview Stats ---
$total_revenue = 0;
$monthly_revenue = 0;
$total_fees = 0;
$active_companies = 0;
$total_invoices = 0;
$overdue_invoices = 0;

try {
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'completed'");
    $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $monthly_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COALESCE(SUM(processing_fee), 0) as total FROM portal_payments WHERE status = 'completed'");
    $total_fees = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_companies");
    $active_companies = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_invoices");
    $total_invoices = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_invoices WHERE status = 'overdue'");
    $overdue_invoices = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
} catch (PDOException $e) {
    error_log("Payment stats error: " . $e->getMessage());
}

// --- Daily Revenue (last 30 days) ---
$daily_revenue = [];
try {
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, SUM(amount) as revenue
        FROM portal_payments
        WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date ASC
    ");
    $daily_revenue = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Daily revenue error: " . $e->getMessage());
}

// --- Payment Method Distribution ---
$method_distribution = [];
try {
    $stmt = $pdo->query("
        SELECT payment_method, COUNT(*) as count, SUM(amount) as total
        FROM portal_payments
        WHERE status = 'completed'
        GROUP BY payment_method
    ");
    $method_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Method distribution error: " . $e->getMessage());
}

// --- Invoice Status Distribution ---
$invoice_statuses = [];
try {
    $stmt = $pdo->query("
        SELECT status, COUNT(*) as count
        FROM portal_invoices
        GROUP BY status
    ");
    $invoice_statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Invoice status error: " . $e->getMessage());
}

// --- Recent Payments (last 10) ---
$recent_payments = [];
try {
    $stmt = $pdo->query("
        SELECT p.created_at, c.company_name, p.customer_name, p.amount,
               p.currency, p.payment_method, p.status
        FROM portal_payments p
        LEFT JOIN portal_companies c ON p.company_id = c.id
        ORDER BY p.created_at DESC
        LIMIT 10
    ");
    $recent_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Recent payments error: " . $e->getMessage());
}

// --- All Transactions (for Transactions tab) ---
$transactions = [];
$tx_search = $_GET['tx_search'] ?? '';
$tx_method = $_GET['tx_method'] ?? '';
$tx_status = $_GET['tx_status'] ?? '';
$tx_company = $_GET['tx_company'] ?? '';

try {
    $query = "
        SELECT p.*, c.company_name
        FROM portal_payments p
        LEFT JOIN portal_companies c ON p.company_id = c.id
        WHERE 1=1
    ";
    $params = [];

    if (!empty($tx_search)) {
        $query .= " AND (p.customer_name LIKE ? OR p.reference_number LIKE ? OR c.company_name LIKE ?)";
        $search_param = '%' . $tx_search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    if (!empty($tx_method)) {
        $query .= " AND p.payment_method = ?";
        $params[] = $tx_method;
    }
    if (!empty($tx_status)) {
        $query .= " AND p.status = ?";
        $params[] = $tx_status;
    }
    if (!empty($tx_company)) {
        $query .= " AND c.id = ?";
        $params[] = $tx_company;
    }

    $query .= " ORDER BY p.created_at DESC LIMIT 200";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Transactions error: " . $e->getMessage());
}

// --- Companies ---
$companies = [];
try {
    $stmt = $pdo->query("
        SELECT c.*,
               COUNT(p.id) as total_payments,
               COALESCE(SUM(CASE WHEN p.status = 'completed' THEN p.amount ELSE 0 END), 0) as total_revenue,
               MAX(p.created_at) as last_payment_date
        FROM portal_companies c
        LEFT JOIN portal_payments p ON p.company_id = c.id
        GROUP BY c.id
        ORDER BY total_revenue DESC
    ");
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Companies error: " . $e->getMessage());
}

// --- Invoices (for Invoices tab) ---
$invoices = [];
$inv_search = $_GET['inv_search'] ?? '';
$inv_status = $_GET['inv_status'] ?? '';
$inv_company = $_GET['inv_company'] ?? '';

try {
    $query = "
        SELECT i.*, c.company_name,
               COALESCE(SUM(CASE WHEN p.status = 'completed' THEN p.amount ELSE 0 END), 0) as paid_amount
        FROM portal_invoices i
        LEFT JOIN portal_companies c ON i.company_id = c.id
        LEFT JOIN portal_payments p ON p.invoice_id = i.id
        WHERE 1=1
    ";
    $params = [];

    if (!empty($inv_search)) {
        $query .= " AND (i.customer_name LIKE ? OR i.invoice_id LIKE ? OR c.company_name LIKE ?)";
        $search_param = '%' . $inv_search . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }
    if (!empty($inv_status)) {
        $query .= " AND i.status = ?";
        $params[] = $inv_status;
    }
    if (!empty($inv_company)) {
        $query .= " AND c.id = ?";
        $params[] = $inv_company;
    }

    $query .= " GROUP BY i.id ORDER BY i.created_at DESC LIMIT 200";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Invoices error: " . $e->getMessage());
}

// --- Revenue Analytics ---
$revenue_by_company = [];
$revenue_by_method_monthly = [];
$avg_transaction_trend = [];
$fees_trend = [];

try {
    // Top 10 companies by revenue
    $stmt = $pdo->query("
        SELECT c.company_name, SUM(p.amount) as revenue
        FROM portal_payments p
        JOIN portal_companies c ON p.company_id = c.id
        WHERE p.status = 'completed'
        GROUP BY c.id
        ORDER BY revenue DESC
        LIMIT 10
    ");
    $revenue_by_company = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Revenue by method per month (last 12 months)
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, payment_method, SUM(amount) as revenue
        FROM portal_payments
        WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month, payment_method
        ORDER BY month ASC
    ");
    $revenue_by_method_monthly = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Average transaction value per month
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, AVG(amount) as avg_amount, COUNT(*) as count
        FROM portal_payments
        WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month
        ORDER BY month ASC
    ");
    $avg_transaction_trend = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Processing fees per month
    $stmt = $pdo->query("
        SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(processing_fee) as fees, SUM(amount) as revenue
        FROM portal_payments
        WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY month
        ORDER BY month ASC
    ");
    $fees_trend = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Revenue analytics error: " . $e->getMessage());
}

// Revenue summary metrics
$total_tx_count = 0;
$avg_payment_size = 0;
$prev_month_revenue = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count, AVG(amount) as avg_amount FROM portal_payments WHERE status = 'completed'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_tx_count = $row['count'];
    $avg_payment_size = $row['avg_amount'] ?? 0;

    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'completed' AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $prev_month_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    error_log("Revenue summary error: " . $e->getMessage());
}

$mom_growth = ($prev_month_revenue > 0) ? (($monthly_revenue - $prev_month_revenue) / $prev_month_revenue * 100) : 0;
$fee_percentage = ($total_revenue > 0) ? ($total_fees / $total_revenue * 100) : 0;

// --- Failed Payments & Refunds ---
$failed_payments = [];
$refunded_payments = [];
$total_failed = 0;
$total_refunded = 0;
$total_refund_amount = 0;
$failure_rate = 0;

try {
    $stmt = $pdo->query("
        SELECT p.created_at, c.company_name, p.customer_name, p.amount,
               p.currency, p.payment_method, p.status,
               p.provider_payment_id
        FROM portal_payments p
        LEFT JOIN portal_companies c ON p.company_id = c.id
        WHERE p.status = 'failed'
        ORDER BY p.created_at DESC
        LIMIT 100
    ");
    $failed_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("
        SELECT p.created_at, c.company_name, p.customer_name, p.amount,
               p.currency, p.payment_method, p.provider_payment_id
        FROM portal_payments p
        LEFT JOIN portal_companies c ON p.company_id = c.id
        WHERE p.status = 'refunded'
        ORDER BY p.created_at DESC
        LIMIT 100
    ");
    $refunded_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_payments WHERE status = 'failed'");
    $total_failed = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->query("SELECT COUNT(*) as count, COALESCE(SUM(amount), 0) as total FROM portal_payments WHERE status = 'refunded'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_refunded = $row['count'];
    $total_refund_amount = $row['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM portal_payments");
    $all_payments_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $failure_rate = ($all_payments_count > 0) ? ($total_failed / $all_payments_count * 100) : 0;
} catch (PDOException $e) {
    error_log("Failed payments error: " . $e->getMessage());
}

// --- Company list for filter dropdowns ---
$company_options = [];
try {
    $stmt = $pdo->query("SELECT id, company_name FROM portal_companies ORDER BY company_name ASC");
    $company_options = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Company options error: " . $e->getMessage());
}

include '../admin_header.php';
?>

<link rel="stylesheet" href="style.css">

<div class="container payments-page">

    <!-- Tab Navigation -->
    <div class="tab-buttons">
        <div class="tab-button active" onclick="switchTab('overview')">Overview</div>
        <div class="tab-button" onclick="switchTab('transactions')">Transactions</div>
        <div class="tab-button" onclick="switchTab('companies')">Companies</div>
        <div class="tab-button" onclick="switchTab('invoices')">Invoices</div>
        <div class="tab-button" onclick="switchTab('revenue')">Payment Analytics</div>
        <div class="tab-button" onclick="switchTab('failures')">Failed & Refunds</div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: OVERVIEW -->
    <!-- ============================================================ -->
    <div id="overview-tab" class="tab-content active">
        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Payments</h3>
                <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="subtext">All time (CAD)</div>
            </div>
            <div class="stat-card">
                <h3>Payments This Month</h3>
                <div class="stat-value">$<?php echo number_format($monthly_revenue, 2); ?></div>
                <div class="subtext">Last 30 days</div>
            </div>
            <div class="stat-card">
                <h3>Processing Fees</h3>
                <div class="stat-value">$<?php echo number_format($total_fees, 2); ?></div>
                <div class="subtext">Total collected</div>
            </div>
            <div class="stat-card">
                <h3>Active Companies</h3>
                <div class="stat-value"><?php echo number_format($active_companies); ?></div>
                <div class="subtext">Portal businesses</div>
            </div>
            <div class="stat-card">
                <h3>Total Invoices</h3>
                <div class="stat-value"><?php echo number_format($total_invoices); ?></div>
                <div class="subtext">All statuses</div>
            </div>
            <div class="stat-card">
                <h3>Overdue Invoices</h3>
                <div class="stat-value <?php echo $overdue_invoices > 0 ? 'text-red' : ''; ?>"><?php echo number_format($overdue_invoices); ?></div>
                <div class="subtext">Require attention</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="chart-row">
            <div class="chart-container">
                <h3>Daily Payments (Last 30 Days)</h3>
                <?php if (empty($daily_revenue)): ?>
                    <div class="chart-no-data"><p>No payment data yet</p></div>
                <?php else: ?>
                    <canvas id="dailyRevenueChart"></canvas>
                <?php endif; ?>
            </div>
            <div class="chart-container">
                <h3>Payment Method Distribution</h3>
                <?php if (empty($method_distribution)): ?>
                    <div class="chart-no-data"><p>No payment data yet</p></div>
                <?php else: ?>
                    <canvas id="methodChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-container">
                <h3>Invoice Status Breakdown</h3>
                <?php if (empty($invoice_statuses)): ?>
                    <div class="chart-no-data"><p>No invoice data yet</p></div>
                <?php else: ?>
                    <canvas id="invoiceStatusChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="table-container">
            <div class="table-header">
                <h2>Recent Payments</h2>
            </div>
            <?php if (empty($recent_payments)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No payments recorded yet</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime($payment['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($payment['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                                    <td>$<?php echo number_format($payment['amount'], 2); ?> <?php echo htmlspecialchars($payment['currency'] ?? 'CAD'); ?></td>
                                    <td><span class="badge badge-method"><?php echo htmlspecialchars(ucfirst($payment['payment_method'])); ?></span></td>
                                    <td><span class="badge badge-<?php echo $payment['status']; ?>"><?php echo ucfirst($payment['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: TRANSACTIONS -->
    <!-- ============================================================ -->
    <div id="transactions-tab" class="tab-content">
        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="filters-form" id="tx-filters-form">
                <input type="hidden" name="tab" value="transactions">
                <div class="filter-group">
                    <input type="text" name="tx_search" placeholder="Search customer, reference, company..." value="<?php echo htmlspecialchars($tx_search); ?>">
                </div>
                <div class="filter-group">
                    <select name="tx_method" onchange="document.getElementById('tx-filters-form').submit()">
                        <option value="">All Methods</option>
                        <option value="stripe" <?php echo $tx_method === 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                        <option value="paypal" <?php echo $tx_method === 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                        <option value="square" <?php echo $tx_method === 'square' ? 'selected' : ''; ?>>Square</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="tx_status" onchange="document.getElementById('tx-filters-form').submit()">
                        <option value="">All Statuses</option>
                        <option value="completed" <?php echo $tx_status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="pending" <?php echo $tx_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="failed" <?php echo $tx_status === 'failed' ? 'selected' : ''; ?>>Failed</option>
                        <option value="refunded" <?php echo $tx_status === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="tx_company" onchange="document.getElementById('tx-filters-form').submit()">
                        <option value="">All Companies</option>
                        <?php foreach ($company_options as $co): ?>
                            <option value="<?php echo $co['id']; ?>" <?php echo $tx_company == $co['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($co['company_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-small btn-primary-blue">Search</button>
                <?php if (!empty($tx_search) || !empty($tx_method) || !empty($tx_status) || !empty($tx_company)): ?>
                    <a href="?tab=transactions" class="btn btn-small btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>Portal Transactions</h2>
                <span class="result-count"><?php echo count($transactions); ?> results</span>
            </div>
            <?php if (empty($transactions)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No transactions found</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Invoice #</th>
                                <th>Amount</th>
                                <th>Fee</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Synced</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($tx['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($tx['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tx['customer_name']); ?></td>
                                    <td class="key-field"><?php echo htmlspecialchars($tx['invoice_id'] ?? 'N/A'); ?></td>
                                    <td>$<?php echo number_format($tx['amount'], 2); ?></td>
                                    <td>$<?php echo number_format($tx['processing_fee'] ?? 0, 2); ?></td>
                                    <td><span class="badge badge-method"><?php echo htmlspecialchars(ucfirst($tx['payment_method'])); ?></span></td>
                                    <td><span class="badge badge-<?php echo $tx['status']; ?>"><?php echo ucfirst($tx['status']); ?></span></td>
                                    <td>
                                        <?php if ($tx['synced_to_argo']): ?>
                                            <span class="badge badge-success">Synced</span>
                                        <?php else: ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="key-field"><?php echo htmlspecialchars($tx['reference_number'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: COMPANIES -->
    <!-- ============================================================ -->
    <div id="companies-tab" class="tab-content">
        <div class="table-container">
            <div class="table-header">
                <h2>Portal Companies</h2>
                <span class="result-count"><?php echo count($companies); ?> companies</span>
            </div>
            <?php if (empty($companies)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No companies registered yet</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Connected Providers</th>
                                <th>Total Payments</th>
                                <th>Total Payments</th>
                                <th>Last Payment</th>
                                <th style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companies as $company): ?>
                                <tr class="expandable-row" onclick="toggleCompanyDetail(<?php echo $company['id']; ?>)">
                                    <td><strong><?php echo htmlspecialchars($company['company_name']); ?></strong></td>
                                    <td>
                                        <div class="provider-badges">
                                            <span class="provider-badge <?php echo !empty($company['stripe_account_id']) ? 'connected' : 'disconnected'; ?>" title="Stripe">Stripe</span>
                                            <span class="provider-badge <?php echo !empty($company['paypal_merchant_id']) ? 'connected' : 'disconnected'; ?>" title="PayPal">PayPal</span>
                                            <span class="provider-badge <?php echo !empty($company['square_merchant_id']) ? 'connected' : 'disconnected'; ?>" title="Square">Square</span>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($company['total_payments']); ?></td>
                                    <td>$<?php echo number_format($company['total_revenue'], 2); ?></td>
                                    <td><?php echo $company['last_payment_date'] ? date('M j, Y', strtotime($company['last_payment_date'])) : 'Never'; ?></td>
                                    <td class="expand-arrow-cell"><span class="expand-arrow">&#9662;</span></td>
                                </tr>
                                <tr class="detail-row" id="company-detail-<?php echo $company['id']; ?>" style="display: none;">
                                    <td colspan="6">
                                        <div class="company-detail">
                                            <!-- General Info -->
                                            <div class="detail-section">
                                                <h4 class="detail-section-title">General</h4>
                                                <div class="detail-section-grid">
                                                    <div class="detail-item">
                                                        <span class="detail-label">Owner Email</span>
                                                        <span class="detail-value"><?php echo htmlspecialchars($company['owner_email'] ?? 'N/A'); ?></span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">API Key</span>
                                                        <span class="detail-value key-field"><?php
                                                            $key = $company['api_key'] ?? '';
                                                            echo $key ? htmlspecialchars(substr($key, 0, 8) . '...' . substr($key, -4)) : 'N/A';
                                                        ?></span>
                                                    </div>
                                                    <div class="detail-item">
                                                        <span class="detail-label">Created</span>
                                                        <span class="detail-value"><?php echo $company['created_at'] ? date('M j, Y', strtotime($company['created_at'])) : 'N/A'; ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Provider Connections -->
                                            <div class="detail-section">
                                                <h4 class="detail-section-title">Payment Providers</h4>
                                                <div class="provider-cards">
                                                    <div class="provider-card <?php echo !empty($company['stripe_account_id']) ? 'provider-connected' : 'provider-disconnected'; ?>">
                                                        <div class="provider-card-header">
                                                            <span class="provider-card-name">Stripe</span>
                                                            <span class="provider-card-status"><?php echo !empty($company['stripe_account_id']) ? 'Connected' : 'Not connected'; ?></span>
                                                        </div>
                                                        <?php if (!empty($company['stripe_account_id'])): ?>
                                                        <div class="provider-card-details">
                                                            <div class="detail-item">
                                                                <span class="detail-label">Account ID</span>
                                                                <span class="detail-value key-field"><?php echo htmlspecialchars($company['stripe_account_id']); ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Email</span>
                                                                <span class="detail-value"><?php echo htmlspecialchars($company['stripe_email'] ?? 'N/A'); ?></span>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="provider-card <?php echo !empty($company['paypal_merchant_id']) ? 'provider-connected' : 'provider-disconnected'; ?>">
                                                        <div class="provider-card-header">
                                                            <span class="provider-card-name">PayPal</span>
                                                            <span class="provider-card-status"><?php echo !empty($company['paypal_merchant_id']) ? 'Connected' : 'Not connected'; ?></span>
                                                        </div>
                                                        <?php if (!empty($company['paypal_merchant_id'])): ?>
                                                        <div class="provider-card-details">
                                                            <div class="detail-item">
                                                                <span class="detail-label">Merchant ID</span>
                                                                <span class="detail-value key-field"><?php echo htmlspecialchars($company['paypal_merchant_id']); ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Email</span>
                                                                <span class="detail-value"><?php echo htmlspecialchars($company['paypal_email'] ?? 'N/A'); ?></span>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div class="provider-card <?php echo !empty($company['square_merchant_id']) ? 'provider-connected' : 'provider-disconnected'; ?>">
                                                        <div class="provider-card-header">
                                                            <span class="provider-card-name">Square</span>
                                                            <span class="provider-card-status"><?php echo !empty($company['square_merchant_id']) ? 'Connected' : 'Not connected'; ?></span>
                                                        </div>
                                                        <?php if (!empty($company['square_merchant_id'])): ?>
                                                        <div class="provider-card-details">
                                                            <div class="detail-item">
                                                                <span class="detail-label">Merchant ID</span>
                                                                <span class="detail-value key-field"><?php echo htmlspecialchars($company['square_merchant_id']); ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Location ID</span>
                                                                <span class="detail-value key-field"><?php echo htmlspecialchars($company['square_location_id'] ?? 'N/A'); ?></span>
                                                            </div>
                                                            <div class="detail-item">
                                                                <span class="detail-label">Email</span>
                                                                <span class="detail-value"><?php echo htmlspecialchars($company['square_email'] ?? 'N/A'); ?></span>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: INVOICES -->
    <!-- ============================================================ -->
    <div id="invoices-tab" class="tab-content">
        <!-- Filters -->
        <div class="filters-bar">
            <form method="GET" class="filters-form" id="inv-filters-form">
                <input type="hidden" name="tab" value="invoices">
                <div class="filter-group">
                    <input type="text" name="inv_search" placeholder="Search customer, invoice ID, company..." value="<?php echo htmlspecialchars($inv_search); ?>">
                </div>
                <div class="filter-group">
                    <select name="inv_status" onchange="document.getElementById('inv-filters-form').submit()">
                        <option value="">All Statuses</option>
                        <option value="draft" <?php echo $inv_status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="sent" <?php echo $inv_status === 'sent' ? 'selected' : ''; ?>>Sent</option>
                        <option value="viewed" <?php echo $inv_status === 'viewed' ? 'selected' : ''; ?>>Viewed</option>
                        <option value="pending" <?php echo $inv_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="partial" <?php echo $inv_status === 'partial' ? 'selected' : ''; ?>>Partial</option>
                        <option value="paid" <?php echo $inv_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="overdue" <?php echo $inv_status === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                        <option value="cancelled" <?php echo $inv_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="filter-group">
                    <select name="inv_company" onchange="document.getElementById('inv-filters-form').submit()">
                        <option value="">All Companies</option>
                        <?php foreach ($company_options as $co): ?>
                            <option value="<?php echo $co['id']; ?>" <?php echo $inv_company == $co['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($co['company_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-small btn-primary-blue">Search</button>
                <?php if (!empty($inv_search) || !empty($inv_status) || !empty($inv_company)): ?>
                    <a href="?tab=invoices" class="btn btn-small btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>Portal Invoices</h2>
                <span class="result-count"><?php echo count($invoices); ?> results</span>
            </div>
            <?php if (empty($invoices)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No invoices found</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Balance Due</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $inv): ?>
                                <tr class="<?php echo $inv['status'] === 'overdue' ? 'row-overdue' : ''; ?>">
                                    <td class="key-field"><?php echo htmlspecialchars($inv['invoice_id']); ?></td>
                                    <td><?php echo htmlspecialchars($inv['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($inv['customer_name']); ?></td>
                                    <td>$<?php echo number_format($inv['total_amount'], 2); ?></td>
                                    <td>$<?php echo number_format($inv['balance_due'], 2); ?></td>
                                    <td><span class="badge badge-invoice-<?php echo $inv['status']; ?>"><?php echo ucfirst($inv['status']); ?></span></td>
                                    <td><?php echo $inv['due_date'] ? date('M j, Y', strtotime($inv['due_date'])) : 'N/A'; ?></td>
                                    <td><?php echo date('M j, Y', strtotime($inv['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 5: REVENUE ANALYTICS -->
    <!-- ============================================================ -->
    <div id="revenue-tab" class="tab-content">
        <!-- Summary Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Payments</h3>
                <div class="stat-value">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="subtext">All time</div>
            </div>
            <div class="stat-card">
                <h3>Avg Payment Size</h3>
                <div class="stat-value">$<?php echo number_format($avg_payment_size, 2); ?></div>
                <div class="subtext"><?php echo number_format($total_tx_count); ?> transactions</div>
            </div>
            <div class="stat-card">
                <h3>MoM Growth</h3>
                <div class="stat-value <?php echo $mom_growth >= 0 ? 'text-green' : 'text-red'; ?>">
                    <?php echo ($mom_growth >= 0 ? '+' : '') . number_format($mom_growth, 1); ?>%
                </div>
                <div class="subtext">vs previous 30 days</div>
            </div>
            <div class="stat-card">
                <h3>Fee Rate</h3>
                <div class="stat-value"><?php echo number_format($fee_percentage, 2); ?>%</div>
                <div class="subtext">$<?php echo number_format($total_fees, 2); ?> total fees</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-row">
            <div class="chart-container">
                <h3>Top 10 Companies by Payments</h3>
                <?php if (empty($revenue_by_company)): ?>
                    <div class="chart-no-data"><p>No payment data yet</p></div>
                <?php else: ?>
                    <canvas id="companyRevenueChart"></canvas>
                <?php endif; ?>
            </div>
            <div class="chart-container">
                <h3>Payments by Method (Monthly)</h3>
                <?php if (empty($revenue_by_method_monthly)): ?>
                    <div class="chart-no-data"><p>No data yet</p></div>
                <?php else: ?>
                    <canvas id="methodMonthlyChart"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div class="chart-row">
            <div class="chart-container">
                <h3>Average Transaction Value</h3>
                <?php if (empty($avg_transaction_trend)): ?>
                    <div class="chart-no-data"><p>No data yet</p></div>
                <?php else: ?>
                    <canvas id="avgTransactionChart"></canvas>
                <?php endif; ?>
            </div>
            <div class="chart-container">
                <h3>Processing Fees Over Time</h3>
                <?php if (empty($fees_trend)): ?>
                    <div class="chart-no-data"><p>No data yet</p></div>
                <?php else: ?>
                    <canvas id="feesTrendChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 6: FAILED PAYMENTS & REFUNDS -->
    <!-- ============================================================ -->
    <div id="failures-tab" class="tab-content">
        <!-- Summary Stats -->
        <div class="stats-grid stats-grid-3">
            <div class="stat-card">
                <h3>Failed Payments</h3>
                <div class="stat-value text-red"><?php echo number_format($total_failed); ?></div>
                <div class="subtext">All time</div>
            </div>
            <div class="stat-card">
                <h3>Failure Rate</h3>
                <div class="stat-value"><?php echo number_format($failure_rate, 1); ?>%</div>
                <div class="subtext">Of all payment attempts</div>
            </div>
            <div class="stat-card">
                <h3>Total Refunded</h3>
                <div class="stat-value"><?php echo number_format($total_refunded); ?></div>
                <div class="subtext">$<?php echo number_format($total_refund_amount, 2); ?> CAD</div>
            </div>
        </div>

        <!-- Failed Payments Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Failed Payments</h2>
            </div>
            <?php if (empty($failed_payments)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No failed payments</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Provider ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($failed_payments as $fp): ?>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime($fp['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($fp['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($fp['customer_name']); ?></td>
                                    <td>$<?php echo number_format($fp['amount'], 2); ?> <?php echo htmlspecialchars($fp['currency'] ?? 'CAD'); ?></td>
                                    <td><span class="badge badge-method"><?php echo htmlspecialchars(ucfirst($fp['payment_method'])); ?></span></td>
                                    <td class="key-field"><?php echo htmlspecialchars($fp['provider_payment_id'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Refunded Payments Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Refunded Payments</h2>
            </div>
            <?php if (empty($refunded_payments)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">No refunded payments</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Company</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Provider ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($refunded_payments as $rp): ?>
                                <tr>
                                    <td><?php echo date('M j, Y g:i A', strtotime($rp['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($rp['company_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($rp['customer_name']); ?></td>
                                    <td>$<?php echo number_format($rp['amount'], 2); ?> <?php echo htmlspecialchars($rp['currency'] ?? 'CAD'); ?></td>
                                    <td><span class="badge badge-method"><?php echo htmlspecialchars(ucfirst($rp['payment_method'])); ?></span></td>
                                    <td class="key-field"><?php echo htmlspecialchars($rp['provider_payment_id'] ?? 'N/A'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
// ============================================================
// Tab Switching
// ============================================================
function switchTab(tabName) {
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.remove('active'));

    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => button.classList.remove('active'));

    const selectedTab = document.getElementById(tabName + '-tab');
    if (selectedTab) selectedTab.classList.add('active');

    event.target.classList.add('active');
}

// Restore tab from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const tabParam = urlParams.get('tab');
if (tabParam) {
    const btn = document.querySelector(`.tab-button[onclick*="'${tabParam}'"]`);
    if (btn) btn.click();
}

// ============================================================
// Company Detail Toggle
// ============================================================
function toggleCompanyDetail(companyId) {
    const detailRow = document.getElementById('company-detail-' + companyId);
    if (detailRow) {
        const isHidden = detailRow.style.display === 'none';
        detailRow.style.display = isHidden ? 'table-row' : 'none';
        const expandableRow = detailRow.previousElementSibling;
        if (expandableRow) expandableRow.classList.toggle('expanded', isHidden);
    }
}

// ============================================================
// Charts
// ============================================================
const chartColors = {
    blue: 'rgba(59, 130, 246, 0.8)',
    purple: 'rgba(139, 92, 246, 0.8)',
    green: 'rgba(16, 185, 129, 0.8)',
    red: 'rgba(239, 68, 68, 0.8)',
    amber: 'rgba(245, 158, 11, 0.8)',
    sky: 'rgba(14, 165, 233, 0.8)',
    pink: 'rgba(236, 72, 153, 0.8)',
    indigo: 'rgba(99, 102, 241, 0.8)',
    emerald: 'rgba(52, 211, 153, 0.8)',
    cyan: 'rgba(6, 182, 212, 0.8)'
};

const methodColors = {
    stripe: chartColors.purple,
    paypal: chartColors.blue,
    square: chartColors.green
};

// --- Daily Revenue Chart ---
<?php if (!empty($daily_revenue)): ?>
new Chart(document.getElementById('dailyRevenueChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($daily_revenue, 'date')); ?>,
        datasets: [{
            label: 'Payments ($)',
            data: <?php echo json_encode(array_map('floatval', array_column($daily_revenue, 'revenue'))); ?>,
            borderColor: chartColors.blue,
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } },
            x: { ticks: { maxTicksLimit: 10 } }
        }
    }
});
<?php endif; ?>

// --- Payment Method Distribution Chart ---
<?php if (!empty($method_distribution)): ?>
new Chart(document.getElementById('methodChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_map(function($m) { return ucfirst($m['payment_method']); }, $method_distribution)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_map('floatval', array_column($method_distribution, 'total'))); ?>,
            backgroundColor: <?php echo json_encode(array_map(function($m) {
                $colors = ['stripe' => 'rgba(139, 92, 246, 0.8)', 'paypal' => 'rgba(59, 130, 246, 0.8)', 'square' => 'rgba(16, 185, 129, 0.8)'];
                return $colors[$m['payment_method']] ?? 'rgba(107, 114, 128, 0.8)';
            }, $method_distribution)); ?>
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: ctx => ctx.label + ': $' + ctx.parsed.toLocaleString(undefined, {minimumFractionDigits: 2}) } }
        }
    }
});
<?php endif; ?>

// --- Invoice Status Chart ---
<?php if (!empty($invoice_statuses)): ?>
new Chart(document.getElementById('invoiceStatusChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_map(function($s) { return ucfirst($s['status']); }, $invoice_statuses)); ?>,
        datasets: [{
            label: 'Invoices',
            data: <?php echo json_encode(array_map('intval', array_column($invoice_statuses, 'count'))); ?>,
            backgroundColor: <?php echo json_encode(array_map(function($s) {
                $colors = [
                    'draft' => 'rgba(107, 114, 128, 0.7)',
                    'sent' => 'rgba(59, 130, 246, 0.7)',
                    'viewed' => 'rgba(14, 165, 233, 0.7)',
                    'pending' => 'rgba(245, 158, 11, 0.7)',
                    'partial' => 'rgba(139, 92, 246, 0.7)',
                    'paid' => 'rgba(16, 185, 129, 0.7)',
                    'overdue' => 'rgba(239, 68, 68, 0.7)',
                    'cancelled' => 'rgba(107, 114, 128, 0.4)'
                ];
                return $colors[$s['status']] ?? 'rgba(107, 114, 128, 0.5)';
            }, $invoice_statuses)); ?>,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
<?php endif; ?>

// --- Company Revenue Chart ---
<?php if (!empty($revenue_by_company)): ?>
new Chart(document.getElementById('companyRevenueChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($revenue_by_company, 'company_name')); ?>,
        datasets: [{
            label: 'Payments ($)',
            data: <?php echo json_encode(array_map('floatval', array_column($revenue_by_company, 'revenue'))); ?>,
            backgroundColor: Object.values(chartColors).slice(0, <?php echo count($revenue_by_company); ?>),
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } }
    }
});
<?php endif; ?>

// --- Revenue by Method Monthly Chart ---
<?php if (!empty($revenue_by_method_monthly)): ?>
(function() {
    const rawData = <?php echo json_encode($revenue_by_method_monthly); ?>;
    const months = [...new Set(rawData.map(r => r.month))];
    const methods = [...new Set(rawData.map(r => r.payment_method))];

    const datasets = methods.map(method => {
        const data = months.map(month => {
            const entry = rawData.find(r => r.month === month && r.payment_method === method);
            return entry ? parseFloat(entry.revenue) : 0;
        });
        return {
            label: method.charAt(0).toUpperCase() + method.slice(1),
            data: data,
            backgroundColor: methodColors[method] || chartColors.indigo,
            borderColor: methodColors[method] || chartColors.indigo,
            fill: true,
            tension: 0.3
        };
    });

    new Chart(document.getElementById('methodMonthlyChart'), {
        type: 'line',
        data: { labels: months, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, stacked: true, ticks: { callback: v => '$' + v.toLocaleString() } },
                x: { ticks: { maxTicksLimit: 12 } }
            }
        }
    });
})();
<?php endif; ?>

// --- Avg Transaction Value Chart ---
<?php if (!empty($avg_transaction_trend)): ?>
new Chart(document.getElementById('avgTransactionChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($avg_transaction_trend, 'month')); ?>,
        datasets: [{
            label: 'Avg Transaction ($)',
            data: <?php echo json_encode(array_map('floatval', array_column($avg_transaction_trend, 'avg_amount'))); ?>,
            borderColor: chartColors.emerald,
            backgroundColor: 'rgba(52, 211, 153, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '$' + v.toFixed(2) } },
            x: { ticks: { maxTicksLimit: 12 } }
        }
    }
});
<?php endif; ?>

// --- Fees Trend Chart ---
<?php if (!empty($fees_trend)): ?>
new Chart(document.getElementById('feesTrendChart'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($fees_trend, 'month')); ?>,
        datasets: [{
            label: 'Fees ($)',
            data: <?php echo json_encode(array_map('floatval', array_column($fees_trend, 'fees'))); ?>,
            borderColor: chartColors.amber,
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            fill: true,
            tension: 0.3,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '$' + v.toFixed(2) } },
            x: { ticks: { maxTicksLimit: 12 } }
        }
    }
});
<?php endif; ?>
</script>
