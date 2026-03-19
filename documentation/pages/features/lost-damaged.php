<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Lost & Damaged Inventory';
$pageDescription = 'Learn how to record and track lost or damaged inventory items in Argo Books.';
$currentPage = 'lost-damaged';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Track items that have been lost, stolen, or damaged to keep your inventory records accurate and maintain a clear audit trail of inventory losses.</p>

            <h2>Lost & Damaged Dashboard</h2>
            <p>The Lost / Damaged page (under Tracking in the sidebar) displays four summary cards at the top:</p>
            <ul>
                <li><strong>Total Lost/Damaged:</strong> Total number of recorded loss events</li>
                <li><strong>Lost Items:</strong> Items recorded as lost or stolen</li>
                <li><strong>Damaged Items:</strong> Items recorded as damaged</li>
                <li><strong>Total Loss Value:</strong> Combined financial value of all losses</li>
            </ul>

            <h2>Recording a Loss</h2>
            <ol class="steps-list">
                <li>Go to "Lost / Damaged" under the Tracking section in the sidebar</li>
                <li>Click "Add Record" to create a new entry</li>
                <li>Select the product affected</li>
                <li>Enter the date the loss was discovered</li>
                <li>Select a reason (e.g., Lost, Stolen, Damaged, Expired)</li>
                <li>Enter the loss value</li>
                <li>Save the record</li>
            </ol>

            <h2>What Happens When You Record a Loss</h2>
            <ul>
                <li><strong>Inventory Update:</strong> Stock levels are reduced automatically to reflect the lost or damaged items</li>
                <li><strong>Financial Tracking:</strong> The loss value is recorded for accurate bookkeeping and reporting</li>
                <li><strong>Analytics:</strong> Losses appear in the Analytics dashboard under the Losses tab, helping you identify patterns</li>
            </ul>

            <h2>Viewing Loss History</h2>
            <p>The table shows all recorded losses with the product name, date, reason, loss value, and action buttons. Use the search and date range filter to find specific records.</p>

            <div class="page-navigation">
                <a href="returns.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Returns</span>
                </a>
                <a href="receipts.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Receipt Management &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
