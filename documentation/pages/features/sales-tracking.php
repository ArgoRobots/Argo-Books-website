<?php
$pageTitle = 'Expense/Revenue Tracking';
$pageDescription = 'Learn how to track expenses and revenue in Argo Books. Add transactions, manage orders, and keep accurate records.';
$currentPage = 'sales-tracking';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Track all your business transactions with Argo Books.</p>

            <h2>Adding Transactions</h2>
            <ol class="steps-list">
                <li>Go to "Expenses" or "Revenue" in the sidebar (under Transactions)</li>
                <li>Click the "Add Expense" or "Add Revenue" button</li>
                <li>Enter the amount, category, and date</li>
                <li>Optionally attach a receipt</li>
                <li>Click "Save" to add the transaction</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> You can add multiple products to a transaction by clicking the "Add item" button.
            </div>

            <div class="page-navigation">
                <a href="product-management.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Product Management
                </a>
                <a href="receipts.php" class="nav-button next">
                    Next: Receipt Management
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
