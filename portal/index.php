<?php
/**
 * Customer Portal Page
 *
 * Displays all invoices and payment history for a customer.
 * URL: /portal/{token} (rewritten by .htaccess)
 *
 * Tabbed interface: Active Invoices | Payment History | All Invoices
 * No login required - token-based access.
 */

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../api/portal/portal-helper.php';

$token = $_GET['token'] ?? '';

// Validate token format
if (empty($token) || !preg_match('/^[a-fA-F0-9]{48}$/', $token)) {
    http_response_code(404);
    include __DIR__ . '/../error-pages/404.html';
    exit;
}

// Rate limiting
$clientIp = get_client_ip();
if (is_rate_limited($clientIp)) {
    http_response_code(429);
    echo '<!DOCTYPE html><html><head><title>Too Many Requests</title></head><body><h1>Too many requests. Please try again later.</h1></body></html>';
    exit;
}

// Get customer data
$result = get_invoices_by_customer_token($token);

if (!$result['company']) {
    record_failed_lookup($clientIp);
    http_response_code(404);
    include __DIR__ . '/../error-pages/404.html';
    exit;
}

$company = $result['company'];
$invoices = $result['invoices'];
$payments = get_payments_by_customer_token($token);
$paymentMethods = get_available_payment_methods($company);

$companyName = $company['company_name'] ?? '';
$companyLogo = $company['company_logo_url'] ?? '';

// Categorize invoices
$activeInvoices = [];
$paidInvoices = [];
$totalOutstanding = 0;

foreach ($invoices as $inv) {
    $invStatus = $inv['status'];
    $balanceDue = floatval($inv['balance_due']);

    // Check if overdue
    if ($inv['due_date'] && !in_array($invStatus, ['paid', 'cancelled'])) {
        if (strtotime($inv['due_date']) < time() && $invStatus !== 'partial') {
            $invStatus = 'overdue';
        }
    }
    $inv['display_status'] = $invStatus;

    if (in_array($invStatus, ['sent', 'viewed', 'partial', 'overdue', 'pending'])) {
        $activeInvoices[] = $inv;
        $totalOutstanding += $balanceDue;
    } elseif ($invStatus === 'paid') {
        $paidInvoices[] = $inv;
    }
}

// Get customer name from first invoice
$customerName = !empty($invoices) ? ($invoices[0]['customer_name'] ?? '') : '';

// Currency info
$currency = !empty($invoices) ? ($invoices[0]['currency'] ?: 'USD') : 'USD';
$currencySymbol = $currency === 'CAD' ? 'CA$' : '$';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice Portal - <?php echo htmlspecialchars($companyName); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/resources/images/argo-logo/A-logo.ico">

    <link rel="stylesheet" href="/resources/styles/custom-colors.css">
    <link rel="stylesheet" href="/portal/style.css">
</head>
<body>
    <div class="portal-page">
        <!-- Header -->
        <header class="portal-header">
            <div class="portal-header-inner">
                <?php if (!empty($companyLogo)): ?>
                    <img src="<?php echo htmlspecialchars($companyLogo); ?>" alt="<?php echo htmlspecialchars($companyName); ?>" class="company-logo">
                <?php endif; ?>
                <div class="company-info">
                    <h1 class="company-name"><?php echo htmlspecialchars($companyName); ?></h1>
                    <span class="portal-subtitle">Invoice Portal</span>
                </div>
            </div>
        </header>

        <main class="portal-main">
            <!-- Welcome Section -->
            <div class="portal-welcome">
                <h2>Hello<?php echo $customerName ? ', ' . htmlspecialchars($customerName) : ''; ?></h2>
                <?php if ($totalOutstanding > 0): ?>
                    <div class="outstanding-summary">
                        <span class="outstanding-label">Total Outstanding</span>
                        <span class="outstanding-amount"><?php echo $currencySymbol . number_format($totalOutstanding, 2); ?> <?php echo $currency; ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tabs -->
            <div class="portal-tabs">
                <button class="tab-btn active" data-tab="active">
                    Active Invoices
                    <?php if (count($activeInvoices) > 0): ?>
                        <span class="tab-count"><?php echo count($activeInvoices); ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn" data-tab="history">
                    Payment History
                    <?php if (count($payments) > 0): ?>
                        <span class="tab-count"><?php echo count($payments); ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn" data-tab="all">
                    All Invoices
                    <span class="tab-count"><?php echo count($invoices); ?></span>
                </button>
            </div>

            <!-- Active Invoices Tab -->
            <div class="tab-content active" id="tab-active">
                <?php if (empty($activeInvoices)): ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.4">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <p>No outstanding invoices. You're all caught up!</p>
                    </div>
                <?php else: ?>
                    <div class="invoice-cards">
                        <?php foreach ($activeInvoices as $inv):
                            $invData = $inv['invoice_data'] ?? [];
                            $invStatus = $inv['display_status'];
                            $invBalance = floatval($inv['balance_due']);
                            $invTotal = floatval($inv['total_amount']);
                            $invCurrency = $inv['currency'] ?: 'USD';
                            $invSymbol = $invCurrency === 'CAD' ? 'CA$' : '$';
                        ?>
                            <div class="invoice-card <?php echo $invStatus === 'overdue' ? 'overdue' : ''; ?>">
                                <div class="card-header">
                                    <div class="card-id"><?php echo htmlspecialchars($inv['invoice_id']); ?></div>
                                    <span class="status-badge status-<?php echo htmlspecialchars($invStatus); ?>">
                                        <?php echo ucfirst(htmlspecialchars($invStatus)); ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="card-dates">
                                        <?php if ($inv['due_date']): ?>
                                            <span class="<?php echo $invStatus === 'overdue' ? 'text-danger' : ''; ?>">
                                                Due: <?php echo date('M j, Y', strtotime($inv['due_date'])); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-amounts">
                                        <?php if ($invBalance < $invTotal): ?>
                                            <span class="card-total-label">Total: <?php echo $invSymbol . number_format($invTotal, 2); ?></span>
                                        <?php endif; ?>
                                        <span class="card-balance"><?php echo $invSymbol . number_format($invBalance, 2); ?> <?php echo $invCurrency; ?></span>
                                        <span class="card-balance-label">Balance Due</span>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="/invoice/<?php echo htmlspecialchars($inv['invoice_token']); ?>" class="btn-pay">
                                        View & Pay
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Payment History Tab -->
            <div class="tab-content" id="tab-history" style="display: none;">
                <?php if (empty($payments)): ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.4">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <p>No payment history yet.</p>
                    </div>
                <?php else: ?>
                    <div class="payment-history-table-wrapper">
                        <table class="payment-history-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $pay):
                                    $payAmount = floatval($pay['amount']);
                                    $payCurrency = $pay['currency'] ?: 'USD';
                                    $paySymbol = $payCurrency === 'CAD' ? 'CA$' : '$';
                                ?>
                                    <tr>
                                        <td><?php echo date('M j, Y', strtotime($pay['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($pay['invoice_id']); ?></td>
                                        <td class="method-cell">
                                            <?php echo ucfirst(htmlspecialchars($pay['payment_method'])); ?>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($pay['reference_number']); ?></code></td>
                                        <td class="amount-cell <?php echo $pay['status'] === 'refunded' ? 'refunded' : ''; ?>">
                                            <?php echo $paySymbol . number_format(abs($payAmount), 2); ?> <?php echo $payCurrency; ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($pay['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($pay['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- All Invoices Tab -->
            <div class="tab-content" id="tab-all" style="display: none;">
                <?php if (empty($invoices)): ?>
                    <div class="empty-state">
                        <p>No invoices found.</p>
                    </div>
                <?php else: ?>
                    <div class="all-invoices-table-wrapper">
                        <table class="all-invoices-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Due Date</th>
                                    <th>Total</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $inv):
                                    $invStatus = $inv['status'];
                                    $invBalance = floatval($inv['balance_due']);
                                    $invTotal = floatval($inv['total_amount']);
                                    $invCurrency = $inv['currency'] ?: 'USD';
                                    $invSymbol = $invCurrency === 'CAD' ? 'CA$' : '$';

                                    if ($inv['due_date'] && !in_array($invStatus, ['paid', 'cancelled'])) {
                                        if (strtotime($inv['due_date']) < time() && $invStatus !== 'partial') {
                                            $invStatus = 'overdue';
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($inv['invoice_id']); ?></strong></td>
                                        <td><?php echo date('M j, Y', strtotime($inv['created_at'])); ?></td>
                                        <td class="<?php echo $invStatus === 'overdue' ? 'text-danger' : ''; ?>">
                                            <?php echo $inv['due_date'] ? date('M j, Y', strtotime($inv['due_date'])) : '-'; ?>
                                        </td>
                                        <td><?php echo $invSymbol . number_format($invTotal, 2); ?></td>
                                        <td><?php echo $invSymbol . number_format($invBalance, 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($invStatus); ?>">
                                                <?php echo ucfirst(htmlspecialchars($invStatus)); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="/invoice/<?php echo htmlspecialchars($inv['invoice_token']); ?>" class="btn-view">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <!-- Footer -->
        <footer class="portal-footer">
            <p>Powered by <a href="https://argorobots.com" target="_blank" rel="noopener">Argo Books</a></p>
        </footer>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var targetTab = this.getAttribute('data-tab');

                // Update active tab button
                document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');

                // Show target tab content
                document.querySelectorAll('.tab-content').forEach(function(tc) {
                    tc.style.display = 'none';
                    tc.classList.remove('active');
                });
                var target = document.getElementById('tab-' + targetTab);
                if (target) {
                    target.style.display = 'block';
                    target.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
