<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Dashboard';
$pageDescription = 'Learn about the Argo Books Dashboard. Build your own home screen from stat cards, charts, tables and quick actions, and rearrange them at any time.';
$currentPage = 'dashboard';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Dashboard is your home screen in Argo Books. It is built from widgets that you choose, size, and arrange yourself, so the numbers you check every morning are the ones on the screen.</p>

            <img src="../../../resources/images/dashboard.webp" alt="Argo Books Dashboard" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>Page Controls</h2>
            <p>Two controls in the top-right corner apply to the whole page:</p>
            <ul>
                <li><strong>Date Range:</strong> the period every stat card and chart is calculated over</li>
                <li><strong>Chart:</strong> the chart style used by every chart widget</li>
            </ul>

            <h2>Editing Your Dashboard</h2>
            <p>Click <strong>Edit Dashboard</strong> to start rearranging. While you are in edit mode, each widget grows a small toolbar:</p>
            <ul>
                <li><strong>Drag handle:</strong> drag a widget to reorder it, within a row or into another row</li>
                <li><strong>Settings:</strong> choose the widget's width, and set any options the widget has of its own</li>
                <li><strong>Remove:</strong> take the widget off the dashboard</li>
            </ul>
            <p>Use <strong>Add Widget</strong> to open the catalog, or <strong>Reset to Default</strong> to go back to the standard layout. Nothing is kept until you click <strong>Save</strong>, and <strong>Cancel</strong> discards the whole editing session.</p>

            <div class="info-box">
                <strong>Tip:</strong> Widget widths are set as a share of the row: a quarter, a third, a half, three quarters, or the full width. Widgets flow into rows automatically, so a widget that does not fit beside the ones already there moves to the next row.
            </div>

            <h2>What You Can Add</h2>

            <h3>Stat cards</h3>
            <p>Single-number cards for the selected date range:</p>
            <div class="two-column-list">
                <ul>
                    <li>Revenue</li>
                    <li>Expenses</li>
                    <li>Net Profit</li>
                    <li>Outstanding Invoices</li>
                    <li>Overdue Invoices</li>
                </ul>
                <ul>
                    <li>Total Customers</li>
                    <li>Inventory Value</li>
                    <li>Active Rentals</li>
                    <li>Next Remittance Due (payroll)</li>
                </ul>
            </div>

            <h3>Charts</h3>
            <p>Every chart from the <a class="link" href="analytics.php">Analytics</a> page can be placed on the Dashboard, apart from the world map. That covers revenue, expenses, profit, transactions, customers, tax, returns, losses, geography, and products. Add the two or three you actually watch instead of switching to Analytics for them.</p>

            <h3>Tables</h3>
            <ul>
                <li><strong>Recent Transactions:</strong> your latest revenue and expense entries</li>
                <li><strong>Active Rentals:</strong> currently active and overdue rentals</li>
                <li><strong>Top Customers:</strong> your highest revenue customers</li>
            </ul>

            <h3>Everything else</h3>
            <ul>
                <li><strong>Quick Actions:</strong> shortcut buttons for common tasks</li>
                <li><strong>Setup Checklist:</strong> the getting started guide, useful on a brand new company</li>
                <li><strong>Low Stock Alerts:</strong> inventory items below their reorder point</li>
                <li><strong>Upcoming Due Dates:</strong> invoices falling due soon</li>
                <li><strong>Overdue Rentals:</strong> rentals past their return date</li>
            </ul>

            <h2>Quick Actions</h2>
            <p>The Quick Actions widget puts common tasks one click away without navigating through the sidebar. Open the widget's settings to pick which of these appear:</p>
            <div class="two-column-list">
                <ul>
                    <li>New Invoice</li>
                    <li>New Expense</li>
                    <li>New Revenue</li>
                    <li>Scan Receipt</li>
                    <li>Bank Statement</li>
                    <li>New Customer</li>
                    <li>New Supplier</li>
                </ul>
                <ul>
                    <li>New Product</li>
                    <li>New Rental Item</li>
                    <li>New Rental</li>
                    <li>New Category</li>
                    <li>New Location</li>
                    <li>Purchase Order</li>
                    <li>Stock Adjustment</li>
                </ul>
            </div>

            <div class="info-box">
                <strong>Tip:</strong> The Dashboard updates automatically as you add new transactions, so it always reflects your current business data. Note that the sample company is a preview only, so dashboard changes made there are not kept.
            </div>

            <div class="info-box">
                <strong>Cash basis:</strong> the Dashboard counts money you have actually collected, so an unpaid invoice shows under Outstanding Invoices rather than in Revenue. See <a class="link" href="../reference/how-numbers-are-calculated.php#cash-vs-accrual">How Numbers Are Calculated</a>.
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
