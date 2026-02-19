<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Returns';
$pageDescription = 'Learn how to process and track product returns in Argo Books.';
$currentPage = 'returns';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Track product returns to keep your financial records and inventory levels accurate. The Returns page lets you record returned items and automatically adjusts your stock counts.</p>

            <h2>Recording a Return</h2>
            <ol class="steps-list">
                <li>Go to "Returns" in the navigation menu (under Transactions)</li>
                <li>Click "Add Return"</li>
                <li>Select the product(s) being returned and enter quantities</li>
                <li>Set the return date and add any notes about the reason</li>
                <li>Save the return</li>
            </ol>

            <h2>What Happens When You Record a Return</h2>
            <ul>
                <li><strong>Inventory Update:</strong> Stock levels are adjusted automatically to reflect the returned items</li>
                <li><strong>Financial Records:</strong> The return is recorded as a separate transaction for accurate bookkeeping</li>
                <li><strong>Audit Trail:</strong> A complete history of returns is maintained for reference</li>
            </ul>

            <h2>Viewing Return History</h2>
            <p>The Returns page shows all recorded returns with details including the product, quantity, date, and any notes. Use the search and filter options to find specific returns.</p>

            <div class="page-navigation">
                <a href="purchase-orders.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Purchase Orders
                </a>
                <a href="receipts.php" class="nav-button next">
                    Next: Receipt Management
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
