<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Returns';
$pageDescription = 'Learn how to process and track product returns in Argo Books.';
$currentPage = 'returns';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Track product returns to keep your financial records and inventory levels accurate. Argo Books supports both expense returns (returning items to a supplier) and customer returns (customers returning items to you).</p>

            <h2>Recording a Return</h2>
            <p>To return items from an expense transaction back to a supplier:</p>
            <ol class="steps-list">
                <li>Go to "Expenses" in the navigation menu (under Transactions)</li>
                <li>Find the expense transaction containing the items to return</li>
                <li>Click "Mark as Returned" in the action buttons</li>
                <li>Select a return reason (Defective, Wrong Item, Not as Described, Changed Mind, or Other)</li>
                <li>Enter the return details and refund amount</li>
                <li>Save the return</li>
            </ol>

            <h2>What Happens When You Record a Return</h2>
            <ul>
                <li><strong>Inventory Update:</strong> Stock levels are adjusted automatically to reflect the returned items</li>
                <li><strong>Financial Records:</strong> The return is recorded with the refund amount for accurate bookkeeping</li>
                <li><strong>Audit Trail:</strong> A complete history of returns is maintained for reference</li>
            </ul>

            <h2>Viewing Return History</h2>
            <p>The Returns page shows all recorded returns with details including the product, supplier or customer name, date, reason, refund amount, and any notes. Use the search and filter options to find specific returns by type, reason, or date range.</p>

            <h2>Undoing a Return</h2>
            <p>If a return was recorded by mistake, you can undo it. This reverses the return and restores the quantities to the original transaction.</p>

            <div class="page-navigation">
                <a href="purchase-orders.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Purchase Orders</span>
                </a>
                <a href="receipts.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Receipt Management &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
