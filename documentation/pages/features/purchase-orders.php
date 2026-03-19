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
                <li>Add line items by selecting products and entering quantities and unit costs</li>
                <li>Set the order date and expected delivery date</li>
                <li>Add shipping cost and any notes</li>
                <li>Save the purchase order</li>
            </ol>

            <h2>Purchase Order Details</h2>
            <p>Each purchase order includes:</p>
            <ul>
                <li><strong>Supplier:</strong> The vendor you're ordering from</li>
                <li><strong>Line Items:</strong> Products, quantities ordered, and unit costs</li>
                <li><strong>Order Date:</strong> When the order was placed</li>
                <li><strong>Expected Delivery Date:</strong> When you expect the order to arrive</li>
                <li><strong>Shipping Cost:</strong> Delivery or freight charges</li>
                <li><strong>Total Amount:</strong> Calculated from line items plus shipping</li>
                <li><strong>Notes:</strong> Any additional information about the order</li>
            </ul>

            <h2>Order Status</h2>
            <p>Purchase orders move through these statuses as they progress:</p>
            <ul>
                <li><strong>Draft:</strong> Order created but not yet finalized</li>
                <li><strong>Pending:</strong> Awaiting approval</li>
                <li><strong>Sent:</strong> Order sent to the supplier</li>
                <li><strong>Partially Received:</strong> Some items from the order have arrived</li>
                <li><strong>Received:</strong> All items have been received</li>
                <li><strong>Cancelled:</strong> Order has been cancelled</li>
            </ul>

            <h2>Receiving Orders</h2>
            <p>When goods arrive from a supplier, you can record the received quantities:</p>
            <ol class="steps-list">
                <li>Open the purchase order</li>
                <li>Enter the quantity received for each line item</li>
                <li>Save to update the order</li>
            </ol>
            <p>Inventory stock levels are updated automatically when you receive items. If only some items arrive, the order status changes to "Partially Received" until all items are accounted for.</p>

            <h2>Managing Purchase Orders</h2>
            <p>From the Purchase Orders page, you can view, edit, or delete existing orders. Use the search and filter options to find specific orders by supplier, date, or product.</p>

            <div class="page-navigation">
                <a href="inventory.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Inventory Management</span>
                </a>
                <a href="returns.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Returns &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
