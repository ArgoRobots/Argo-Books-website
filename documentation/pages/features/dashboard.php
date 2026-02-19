<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Dashboard';
$pageDescription = 'Learn about the Argo Books Dashboard. View business metrics, recent transactions, and key insights at a glance.';
$currentPage = 'dashboard';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Dashboard is your home screen in Argo Books. It gives you a quick overview of your business performance and highlights items that need your attention.</p>

            <h2>Business Metrics</h2>
            <p>The Dashboard displays key financial metrics for your selected time period, including:</p>
            <ul>
                <li><strong>Total Revenue:</strong> Income from all revenue transactions</li>
                <li><strong>Total Expenses:</strong> Sum of all expense transactions</li>
                <li><strong>Net Profit:</strong> Revenue minus expenses</li>
                <li><strong>Transaction Count:</strong> Number of transactions recorded</li>
            </ul>

            <h2>Alerts and Notifications</h2>
            <p>The Dashboard highlights items that need your attention:</p>
            <ul>
                <li><strong>Low Stock Alerts:</strong> Products that have fallen below their reorder point</li>
                <li><strong>Overdue Invoices:</strong> Unpaid invoices past their due date</li>
                <li><strong>Overdue Rentals:</strong> Rental items that haven't been returned on time</li>
            </ul>

            <h2>Recent Activity</h2>
            <p>View your most recent transactions and activity at a glance, without needing to navigate to the Expenses or Revenue pages.</p>

            <h2>Charts and Visualizations</h2>
            <p>The Dashboard includes interactive charts showing trends in your revenue, expenses, and profit over time. Use the date range controls to zoom in on specific periods.</p>

            <div class="info-box">
                <strong>Tip:</strong> The Dashboard updates automatically as you add new transactions, so it always reflects your current business data.
            </div>

            <div class="page-navigation">
                <a href="../getting-started/version-comparison.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Free vs. Paid Version
                </a>
                <a href="product-management.php" class="nav-button next">
                    Next: Product Management
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
