<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Analytics';
$pageDescription = 'Explore business analytics in Argo Books. View interactive charts, geographic data, customer insights, and performance metrics across seven specialized tabs.';
$currentPage = 'analytics';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Analytics page provides detailed visualizations and metrics to help you understand your business performance. Data is organized into seven tabs, each focused on a different aspect of your operations.</p>

            <h2>Global Controls</h2>
            <p>Two controls at the top of the page apply across all tabs:</p>
            <ul>
                <li><strong>Date Range:</strong> Filter all data by a preset period or select a custom date range with start and end dates</li>
                <li><strong>Chart Type:</strong> Switch between Line, Column, Step Line, Area, and Scatter chart styles</li>
            </ul>

            <h2>Dashboard Tab</h2>
            <p>A high-level summary of your financial performance:</p>
            <ul>
                <li><strong>Total Purchases:</strong> Sum of all purchase transactions in the selected period</li>
                <li><strong>Total Revenue:</strong> Sum of all revenue transactions</li>
                <li><strong>Net Profit:</strong> Revenue minus expenses</li>
                <li><strong>Profit Margin:</strong> Percentage of revenue retained as profit</li>
            </ul>
            <p>Each metric card shows the value and a percentage change compared to the previous period. Two charts display <strong>Expense Trends</strong> and <strong>Revenue vs. Expenses</strong> over time.</p>

            <h2>Geographic Tab</h2>
            <p>Visualize where your business activity is concentrated on a world map:</p>
            <ul>
                <li><strong>Origin Mode:</strong> Shows supplier countries based on your purchases</li>
                <li><strong>Destination Mode:</strong> Shows customer countries based on your sales</li>
            </ul>
            <p>Toggle between modes using the Origin/Destination switch above the map.</p>

            <h2>Operational Tab</h2>
            <p>Track operational efficiency metrics:</p>
            <ul>
                <li><strong>Active Accountants:</strong> Number of accountants currently managing records</li>
                <li><strong>Transactions Processed:</strong> Total transactions handled in the period</li>
            </ul>
            <p>Charts track accountant activity and transaction processing volumes over time.</p>

            <h2>Performance Tab</h2>
            <p>Monitor business growth and financial performance:</p>
            <ul>
                <li><strong>Revenue Growth:</strong> Rate of revenue increase over time</li>
                <li><strong>Transaction Values:</strong> Average and total transaction amounts</li>
                <li><strong>Shipping Costs:</strong> Shipping expense tracking and trends</li>
            </ul>

            <h2>Customers Tab</h2>
            <p>Understand your customer base with demographic and retention data:</p>
            <ul>
                <li>Customer demographics breakdown</li>
                <li>Retention rates and lifetime value trends</li>
            </ul>

            <h2>Returns Tab</h2>
            <p>Analyze return patterns and their financial impact:</p>
            <ul>
                <li>Return reasons breakdown</li>
                <li>Refund amounts and return rate trends</li>
            </ul>

            <h2>Losses Tab</h2>
            <p>Track lost or damaged inventory and related claims:</p>
            <ul>
                <li>Loss cause analysis</li>
                <li>Insurance claim tracking</li>
            </ul>

            <h2>Chart Interactions</h2>
            <p>All charts support the following interactions:</p>
            <ul>
                <li><strong>Zoom:</strong> Scroll or pinch to zoom into specific time periods</li>
                <li><strong>Right-click menu:</strong> Save chart as image, export to Google Sheets, export to Excel, or reset zoom</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> If a chart appears empty, try expanding the date range. A hint will appear when additional data is available outside the current range.
            </div>

            <div class="page-navigation">
                <a href="dashboard.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Dashboard
                </a>
                <a href="predictive-analytics.php" class="nav-button next">
                    Next: Predictive Analytics
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
