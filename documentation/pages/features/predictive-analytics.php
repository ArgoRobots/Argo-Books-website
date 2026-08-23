<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Predictive Analytics';
$pageDescription = 'Forecast revenue, expenses and profit, spot anomalies in your books, and get seasonal patterns and recommendations, all calculated on your own device.';
$currentPage = 'predictive-analytics';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> Predictive Analytics is available with the Premium plan.
                    <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>The Insights page, under Main in the sidebar, analyzes your historical data to forecast where your business is heading, flag anything unusual, and suggest what to act on. All analysis happens locally on your device. Your data is never uploaded to the cloud, the models run entirely on your computer.</p>

            <img src="../../../resources/images/insights.webp" alt="Predictive Analytics Insights" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>Forecasting</h2>
            <p>The forecast covers the period you select and projects:</p>
            <ul>
                <li><strong>Revenue</strong> and its growth rate against the current period</li>
                <li><strong>Expenses</strong> and their growth rate</li>
                <li><strong>Profit</strong>, and the margin that implies</li>
                <li><strong>New customers</strong> expected over the period</li>
            </ul>
            <p>Every figure comes with a range rather than a single number, and you can switch the whole forecast between three scenarios:</p>
            <ul>
                <li><strong>Conservative:</strong> the low end of the range</li>
                <li><strong>Baseline:</strong> the most likely outcome</li>
                <li><strong>Optimistic:</strong> the high end of the range</li>
            </ul>

            <h2>How Forecasting Works</h2>
            <ol class="steps-list">
                <li>The system analyzes your historical transaction data</li>
                <li>Statistical algorithms identify patterns, trends, and seasonality</li>
                <li>Machine learning models combined with mathematical analysis generate predictions</li>
                <li>Forecasts are updated as new data comes in</li>
            </ol>

            <div class="info-box">
                <p><strong>Note:</strong> Insights need at least five recorded transactions before they will run at all, and they get considerably more useful with more history. Several months of data is a reasonable target for a forecast you would actually plan around.</p>
            </div>

            <h2>Prediction Accuracy</h2>
            <p>Each forecast carries a confidence level based on how consistent your history is:</p>
            <ul>
                <li><strong>High Confidence:</strong> Strong historical patterns support this forecast</li>
                <li><strong>Medium Confidence:</strong> Some uncertainty in the prediction</li>
                <li><strong>Low Confidence:</strong> Limited data or unusual patterns detected</li>
            </ul>
            <p>You can also open past predictions to see how previous forecasts compared against what actually happened, which is the honest way to judge whether to trust the current one.</p>

            <h2>Anomaly Detection</h2>
            <p>Argo Books watches for things that look out of line with your own history and surfaces them so nothing quietly slips past:</p>
            <ul>
                <li><strong>Unusual expense spike:</strong> spending well above your normal pattern</li>
                <li><strong>Unusual revenue drop:</strong> income noticeably below what the period would suggest</li>
                <li><strong>Unusually large transaction:</strong> a single entry far outside your typical size</li>
                <li><strong>Return rate above normal:</strong> more coming back than usual</li>
                <li><strong>Inventory depletion alert:</strong> stock heading for zero at your current rate of sale</li>
            </ul>

            <h2>Seasonal Patterns</h2>
            <p>The system looks for repeating cycles in your revenue and reports what it finds, including how strong the pattern is and over what cycle length, whether that is yearly, semi-annual or quarterly. When there is no strong cycle, it falls back to identifying the month that consistently outperforms your average.</p>
            <p>Seasonal patterns help you:</p>
            <ul>
                <li>Plan inventory levels for peak seasons</li>
                <li>Schedule marketing campaigns effectively</li>
                <li>Manage staffing based on expected demand</li>
                <li>Set realistic revenue targets</li>
            </ul>

            <h2>Recommendations</h2>
            <p>Alongside the forecast, Argo Books highlights things worth acting on:</p>
            <ul>
                <li><strong>Top performing product:</strong> what is actually carrying your revenue</li>
                <li><strong>Customer retention opportunity:</strong> customers who have gone quiet</li>
                <li><strong>Payment collection needed:</strong> money you are owed and have not chased</li>
                <li><strong>Supplier concentration risk:</strong> too much of your buying with one vendor</li>
                <li><strong>Revenue concentration risk:</strong> too much of your income from one customer</li>
            </ul>

            <div class="warning-box">
                <strong>Disclaimer:</strong> Predictive analytics and forecasts are generated by statistical models and machine learning algorithms. They are estimates based on historical data and should not be used as the sole basis for financial decisions. Actual results may differ from predictions. Always use your own judgment and consult a qualified professional for important business or financial decisions.
            </div>

            <div class="page-navigation">
                <a href="analytics.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Analytics</span>
                </a>
                <a href="report-generator.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Report Generator &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
