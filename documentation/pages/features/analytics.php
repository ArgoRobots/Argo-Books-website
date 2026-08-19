<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Analytics';
$pageDescription = 'Explore business analytics in Argo Books. View interactive charts, geographic data, product, customer, tax, returns, losses and refund insights across nine tabs.';
$currentPage = 'analytics';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Analytics page provides detailed visualizations and metrics to help you understand your business performance. Data is organized into nine tabs, each focused on a different aspect of your operations.</p>

            <img src="../../../resources/images/analytics.webp" alt="Argo Books Analytics" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>Global Controls</h2>
            <p>Two controls at the top of the page apply across all tabs:</p>
            <ul>
                <li><strong>Date Range:</strong> Filter all data by a preset period or select a custom date range with start and end dates</li>
                <li><strong>Chart Type:</strong> Switch between Column, Line, Step Line, Area, and Scatter chart styles</li>
            </ul>

            <h2>Dashboard Tab</h2>
            <p>A high-level summary of your financial performance. Four cards across the top show <strong>Total Expenses</strong>, <strong>Total Revenue</strong>, <strong>Net Profit</strong> and <strong>Profit Margin</strong>, followed by:</p>
            <ul>
                <li><strong>Profits:</strong> Track net profit over time</li>
                <li><strong>Expenses vs Revenue:</strong> Compare expense and revenue totals side by side</li>
                <li><strong>Revenue Trends:</strong> Visualize revenue changes over time</li>
                <li><strong>Revenue Distribution:</strong> Breakdown of revenue by category</li>
                <li><strong>Expense Trends:</strong> Visualize expense changes over time</li>
                <li><strong>Expense Distribution:</strong> Breakdown of expenses by category</li>
            </ul>

            <h2>Products Tab</h2>
            <p>Sales performance one product at a time. Cards show <strong>Product Revenue</strong>, <strong>Units Sold</strong>, <strong>Avg Sale Price</strong> and <strong>Products Sold</strong>.</p>
            <ul>
                <li><strong>Product picker:</strong> search for a product, or step through your catalog with the previous and next arrows</li>
                <li><strong>Revenue trend:</strong> a chart of the selected product's revenue over the date range</li>
                <li><strong>Per-product figures:</strong> units sold, revenue, and average sale price for the selected product</li>
            </ul>

            <div class="info-box">
                <strong>Note:</strong> These figures show revenue, not profit, and they will not add up exactly to your Total Revenue. <a class="link" href="../reference/how-numbers-are-calculated.php#sales-by-product">How Numbers Are Calculated</a> explains why.
            </div>

            <h2>Geographic Tab</h2>
            <p>Visualize where your business activity is concentrated:</p>
            <ul>
                <li><strong>Countries of Origin:</strong> Breakdown of supplier countries based on your purchases</li>
                <li><strong>Companies of Origin:</strong> Breakdown of individual suppliers by transaction volume</li>
                <li><strong>Countries of Destination:</strong> Breakdown of customer countries based on your sales</li>
                <li><strong>Companies of Destination:</strong> Breakdown of individual customers by transaction volume</li>
                <li><strong>World Map Overview:</strong> Interactive map showing transaction activity by country, with an Origin/Destination toggle to switch between supplier and customer views</li>
            </ul>

            <h2>Performance Tab</h2>
            <p>Monitor business growth and financial performance. Cards show <strong>Revenue Growth</strong>, <strong>Total Transactions</strong>, <strong>Avg Transaction Value</strong> and <strong>Avg Shipping Cost</strong>, each compared against the previous period, alongside charts for:</p>
            <ul>
                <li><strong>Average Transaction Value:</strong> Average monetary value per transaction over time</li>
                <li><strong>Total Transactions:</strong> Number of transactions processed over time</li>
                <li><strong>Average Shipping Costs:</strong> Average shipping per transaction over time</li>
            </ul>

            <h2>Customers Tab</h2>
            <p>Understand your customer base with retention and value data. Cards show <strong>Total Customers</strong>, <strong>New Customers</strong>, <strong>Retention Rate</strong> and <strong>Avg Customer Value</strong>, alongside:</p>
            <ul>
                <li><strong>Top Customers by Revenue:</strong> Ranked list of your highest-value customers</li>
                <li><strong>Customer Payment Status:</strong> Breakdown of customer payment states</li>
                <li><strong>Customer Growth:</strong> Chart tracking customer base growth over time</li>
                <li><strong>Customer Lifetime Value:</strong> Chart showing lifetime value trends</li>
                <li><strong>Active vs Inactive Customers:</strong> Breakdown of active and inactive customer counts</li>
                <li><strong>Rentals per Customer:</strong> Distribution of rental activity across customers</li>
            </ul>

            <div class="info-box">
                <strong>Active or inactive?</strong> A customer counts as active if they made at least one purchase inside the selected date range, so widening the range moves customers from inactive to active. Select All Time to see lifetime activity.
            </div>

            <h2>Taxes Tab</h2>
            <p>Monitor tax collection, liabilities, and rates across your business. Cards show <strong>Tax Collected</strong>, <strong>Tax Paid</strong>, <strong>Net Tax Liability</strong> and <strong>Effective Tax Rate</strong>, alongside:</p>
            <ul>
                <li><strong>Tax Collected vs Paid:</strong> Chart comparing tax collected and tax paid over time</li>
                <li><strong>Tax Rate Distribution:</strong> Breakdown of transactions by tax rate bracket</li>
                <li><strong>Tax Liability Trend:</strong> Chart tracking net tax liability over time</li>
                <li><strong>Tax by Category:</strong> See which product categories generate the most tax</li>
                <li><strong>Tax by Product:</strong> Drill down to individual product-level tax amounts</li>
                <li><strong>Expense vs Revenue Tax:</strong> Compare tax amounts against expense and revenue totals</li>
            </ul>

            <h2>Returns Tab</h2>
            <p>Analyze return patterns and their financial impact. Cards show <strong>Total Returns</strong>, <strong>Return Rate</strong>, <strong>Financial Impact</strong> and <strong>Avg Resolution Time</strong>, alongside:</p>
            <ul>
                <li><strong>Returns Over Time:</strong> Chart tracking return volume trends</li>
                <li><strong>Return Reasons:</strong> Breakdown of why items were returned</li>
                <li><strong>Financial Impact of Returns:</strong> Chart showing the monetary impact of returns over time</li>
                <li><strong>Returns by Category:</strong> See which product categories have the most returns</li>
                <li><strong>Returns by Product:</strong> Drill down to individual product-level return data</li>
                <li><strong>Expense vs Revenue Returns:</strong> Compare returns against expense and revenue totals</li>
            </ul>

            <h2>Losses Tab</h2>
            <p>Track lost or damaged inventory and related claims. Cards show <strong>Total Losses</strong>, <strong>Loss Rate</strong>, <strong>Financial Impact</strong> and <strong>Insurance Claims</strong>, alongside:</p>
            <ul>
                <li><strong>Losses Over Time:</strong> Chart tracking loss incident trends</li>
                <li><strong>Loss Reasons:</strong> Breakdown of causes for lost or damaged inventory</li>
                <li><strong>Financial Impact of Losses:</strong> Chart showing the monetary impact of losses over time</li>
                <li><strong>Losses by Category:</strong> See which product categories are most affected</li>
                <li><strong>Losses by Product:</strong> Drill down to individual product-level loss data</li>
                <li><strong>Expense vs Revenue Losses:</strong> Compare losses against expense and revenue totals</li>
            </ul>

            <h2>Refunds Tab</h2>
            <p>Refund activity for the <strong>last 90 days</strong>. This tab uses its own fixed window rather than the date range at the top of the page. Cards show <strong>Total Refunded</strong>, <strong>Refund Rate</strong> as a share of revenue, and <strong>Avg Time to Refund</strong>, alongside:</p>
            <ul>
                <li><strong>Top refunded customers:</strong> who is sending the most back</li>
                <li><strong>Top refunded items:</strong> which products are being refunded</li>
                <li><strong>Top reasons:</strong> why refunds were issued</li>
                <li><strong>By channel:</strong> how the refunds were made</li>
                <li><strong>Refunds by month:</strong> the last twelve months, for longer-term context</li>
            </ul>
            <p>If you have not issued any refunds in the past 90 days, the tab says so rather than showing empty charts.</p>

            <h2>Chart Interactions</h2>
            <p>All charts support the following interactions:</p>
            <ul>
                <li><strong>Zoom:</strong> Ctrl+scroll to zoom into specific time periods</li>
                <li><strong>Right-click menu:</strong> Save as Image, Export to Google Sheets, Export to Excel, or Reset Zoom</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> If a chart appears empty, try expanding the date range.
            </div>

            <div class="info-box">
                <strong>Want these on your home screen?</strong> Almost every chart on this page can be added to your <a class="link" href="dashboard.php">Dashboard</a> as a widget, so you do not have to come here to check the two or three you watch daily.
            </div>

            <div class="page-navigation">
                <a href="dashboard.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Dashboard</span>
                </a>
                <a href="predictive-analytics.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Predictive Analytics &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
