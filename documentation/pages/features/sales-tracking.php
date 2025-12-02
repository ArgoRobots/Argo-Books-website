<?php
$pageTitle = 'Purchase/Sales Tracking';
$pageDescription = 'Learn how to track purchases and sales in Argo Books. Add transactions, manage orders, and keep accurate records.';
$currentPage = 'sales-tracking';

$pageCategory = 'features';
include '../../docs-header.php';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Adding Purchases and Sales</h1>
                <p>Track all your business transactions with Argo Books.</p>

                <ol class="steps-list">
                    <li>Click "Add Purchase" or "Add Sale" in the top menu</li>
                    <li>Enter the order number and select your name from the accountants list</li>
                    <li>Select the product from the dropdown (must be added in <a class="link"
                            href="product-management.php">Product Management</a> first)</li>
                    <li>Enter the quantity and price per unit</li>
                    <li>Add shipping costs, taxes, and any other fees</li>
                    <li>Optionally attach a receipt</li>
                    <li>Click "Add" to save</li>
                </ol>

                <div class="info-box">
                    <strong>Tip:</strong> Use the "Multiple items" checkbox when adding multiple products to a single
                    purchase or sale.
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
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
