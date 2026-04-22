<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Dashboard';
$pageDescription = 'Learn about the Argo Books Dashboard. View business metrics, recent transactions, and key insights at a glance.';
$currentPage = 'dashboard';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Dashboard is your home screen in Argo Books. It gives you a quick overview of your business performance and highlights items that need your attention.</p>

            <img src="../../../resources/images/dashboard.webp" alt="Argo Books Dashboard" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>Business Metrics</h2>
            <p>At the top of the Dashboard, four summary cards show your key numbers for the selected time period:</p>
            <ul>
                <li><strong>Total Expenses:</strong> Sum of all expense transactions</li>
                <li><strong>Total Revenue:</strong> Income from all revenue transactions</li>
                <li><strong>Outstanding Invoices:</strong> Total value and count of unpaid invoices</li>
                <li><strong>Active Rentals:</strong> Number of currently active rentals and any overdue</li>
            </ul>

            <h2>Quick Actions</h2>
            <p>Below the metrics, a row of Quick Action buttons lets you jump straight into common tasks without navigating through the sidebar. The following actions are available:</p>
            <ul>
                <li>New Invoice</li>
                <li>New Expense</li>
                <li>New Revenue</li>
                <li>Scan Receipt (Premium)</li>
                <li>New Customer</li>
                <li>New Supplier</li>
                <li>New Product</li>
                <li>Record Payment</li>
                <li>New Rental Item</li>
                <li>New Rental</li>
                <li>New Category</li>
                <li>New Department</li>
                <li>New Location</li>
                <li>Purchase Order</li>
                <li>Stock Adjustment</li>
            </ul>
            <p>You can customize which actions appear here by clicking the settings icon next to the Quick Actions heading.</p>

            <h2>Charts</h2>
            <p>The Dashboard includes two interactive charts:</p>
            <ul>
                <li><strong>Profits Overview:</strong> Tracks your profit (revenue minus expenses) over time</li>
                <li><strong>Expenses vs Revenue:</strong> Compares your spending and income side by side</li>
            </ul>
            <p>Use the date range dropdown in the top-right corner and the chart type selector to adjust the view.</p>

            <h2>Recent Activity</h2>
            <p>At the bottom of the Dashboard, two panels show your latest activity:</p>
            <ul>
                <li><strong>Recent Transactions:</strong> Your most recent expenses and revenue entries with amounts and dates</li>
                <li><strong>Active Rentals:</strong> Currently rented items with customer, return date, and daily rate</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> The Dashboard updates automatically as you add new transactions, so it always reflects your current business data.
            </div>

            <div class="page-navigation">
                <a href="../getting-started/version-comparison.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Free vs. Paid Version</span>
                </a>
                <a href="analytics.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Analytics &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
