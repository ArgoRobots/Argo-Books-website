<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Purchase Orders';
$pageDescription = 'Learn how to create and manage purchase orders in Argo Books to track orders placed with suppliers.';
$currentPage = 'purchase-orders';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Purchase orders help you track orders placed with your suppliers. Create purchase orders to document what you're ordering, from whom, and at what cost.</p>

            <h2>Creating a Purchase Order</h2>
            <ol class="steps-list">
                <li>Go to "Purchase Orders" in the navigation menu (under Transactions)</li>
                <li>Click "Add Purchase Order"</li>
                <li>Select a supplier</li>
                <li>Add line items by selecting products and entering quantities</li>
                <li>Set the order date and any notes</li>
                <li>Save the purchase order</li>
            </ol>

            <h2>Purchase Order Details</h2>
            <p>Each purchase order includes:</p>
            <ul>
                <li><strong>Supplier:</strong> The vendor you're ordering from</li>
                <li><strong>Line Items:</strong> Products, quantities, and unit prices</li>
                <li><strong>Order Date:</strong> When the order was placed</li>
                <li><strong>Total Amount:</strong> Calculated from line items</li>
            </ul>

            <h2>Managing Purchase Orders</h2>
            <p>From the Purchase Orders page, you can view, edit, or delete existing orders. Use the search and filter options to find specific orders by supplier, date, or product.</p>

            <div class="info-box">
                <strong>Tip:</strong> Purchase orders work alongside expense tracking. When goods arrive, record the corresponding expense transaction to keep your financial records accurate.
            </div>

            <div class="page-navigation">
                <a href="sales-tracking.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Expense/Revenue Tracking
                </a>
                <a href="returns.php" class="nav-button next">
                    Next: Returns
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
