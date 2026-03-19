<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Analytics';
$pageDescription = 'Explore business analytics in Argo Books. View interactive charts, geographic data, customer insights, and performance metrics across eight specialized tabs.';
$currentPage = 'analytics';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Analytics page provides detailed visualizations and metrics to help you understand your business performance. Data is organized into eight tabs, each focused on a different aspect of your operations.</p>

            <img src="../../../resources/images/analytics.webp" alt="Argo Books Analytics" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>Global Controls</h2>
            <p>Two controls at the top of the page apply across all tabs:</p>
            <ul>
                <li><strong>Date Range:</strong> Filter all data by a preset period or select a custom date range with start and end dates</li>
                <li><strong>Chart Type:</strong> Switch between Line, Column, Step Line, Area, and Scatter chart styles</li>
            </ul>

            <h2>Dashboard Tab</h2>
            <p>A high-level summary of your financial performance:</p>
            <ul>
                <li><strong>Profits:</strong> Track net profit over time</li>
                <li><strong>Expenses vs Revenue:</strong> Compare expense and revenue totals side by side</li>
                <li><strong>Revenue Trends:</strong> Visualize revenue changes over time</li>
                <li><strong>Revenue Distribution:</strong> Breakdown of revenue by category</li>
                <li><strong>Expense Trends:</strong> Visualize expense changes over time</li>
                <li><strong>Expense Distribution:</strong> Breakdown of expenses by category</li>
            </ul>

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
            <p>Monitor business growth and financial performance:</p>
            <ul>
                <li><strong>Avg Transaction Value:</strong> Average monetary value per transaction</li>
                <li><strong>Total Transactions:</strong> Number of transactions processed in the period</li>
                <li><strong>Average Shipping Cost:</strong> Average shipping per transaction</li>
            </ul>

            <h2>Customers Tab</h2>
            <p>Understand your customer base with retention and value data:</p>
            <ul>
                <li><strong>Top Customers by Revenue:</strong> Ranked list of your highest-value customers</li>
                <li><strong>Customer Payment Status:</strong> Breakdown of customer payment states</li>
                <li><strong>Customer Growth:</strong> Chart tracking customer base growth over time</li>
                <li><strong>Customer Lifetime Value:</strong> Chart showing lifetime value trends</li>
                <li><strong>Active vs Inactive Customers:</strong> Breakdown of active and inactive customer counts</li>
                <li><strong>Rentals per Customer:</strong> Distribution of rental activity across customers</li>
            </ul>

            <h2>Taxes Tab</h2>
            <p>Monitor tax collection, liabilities, and rates across your business:</p>
            <ul>
                <li><strong>Tax Collected vs Paid:</strong> Chart comparing tax collected and tax paid over time</li>
                <li><strong>Tax Rate Distribution:</strong> Breakdown of transactions by tax rate bracket</li>
                <li><strong>Tax Liability Trend:</strong> Chart tracking net tax liability over time</li>
                <li><strong>Tax by Category:</strong> See which product categories generate the most tax</li>
                <li><strong>Tax by Product:</strong> Drill down to individual product-level tax amounts</li>
                <li><strong>Expense vs Revenue Tax:</strong> Compare tax amounts against expense and revenue totals</li>
            </ul>

            <h2>Returns Tab</h2>
            <p>Analyze return patterns and their financial impact:</p>
            <ul>
                <li><strong>Returns Over Time:</strong> Chart tracking return volume trends</li>
                <li><strong>Return Reasons:</strong> Breakdown of why items were returned</li>
                <li><strong>Financial Impact of Returns:</strong> Chart showing the monetary impact of returns over time</li>
                <li><strong>Returns by Category:</strong> See which product categories have the most returns</li>
                <li><strong>Returns by Product:</strong> Drill down to individual product-level loss data</li>
                <li><strong>Expense vs Revenue Returns:</strong> Compare returns against expense and revenue totals</li>
            </ul>

            <h2>Losses Tab</h2>
            <p>Track lost or damaged inventory and related claims:</p>
            <ul>
                <li><strong>Losses Over Time:</strong> Chart tracking loss incident trends</li>
                <li><strong>Loss Reasons:</strong> Breakdown of causes for lost or damaged inventory</li>
                <li><strong>Financial Impact of Losses:</strong> Chart showing the monetary impact of losses over time</li>
                <li><strong>Losses by Category:</strong> See which product categories are most affected</li>
                <li><strong>Losses by Product:</strong> Drill down to individual product-level loss data</li>
                <li><strong>Expense vs Revenue Losses:</strong> Compare losses against expense and revenue totals</li>
            </ul>

            <h2>Chart Interactions</h2>
            <p>All charts support the following interactions:</p>
            <ul>
                <li><strong>Zoom:</strong> Ctrl+scroll to zoom into specific time periods</li>
                <li><strong>Right-click menu:</strong> Save chart as image, export to Google Sheets, export to Excel, or reset zoom</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> If a chart appears empty, try expanding the date range.
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

<?php include '../../docs-footer.php'; ?>
