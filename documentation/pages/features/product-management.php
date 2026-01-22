<?php
$pageTitle = 'Product Management';
$pageDescription = 'Learn how to manage products and categories in Argo Books. Create categories, add products, and organize your inventory.';
$currentPage = 'product-management';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Learn how to create categories and manage products in Argo Books.</p>

            <h2>Creating Categories to organize your products</h2>
            <ol class="steps-list">
                <li>Go to "Categories" in the sidebar (under Management)</li>
                <li>Click "Add Category"</li>
                <li>Enter the category name</li>
                <li>Save the category</li>
            </ol>

            <h2>Adding Products</h2>
            <ol class="steps-list">
                <li>Go to "Products/Services" in the sidebar (under Management)</li>
                <li>Click "Add Product"</li>
                <li>Enter the product name</li>
                <li>Select a category to keep things organized</li>
                <li>Set pricing and other optional fields</li>
                <li>Save the product</li>
            </ol>

            <div class="warning-box">
                <strong>Important:</strong> Free version users are limited to 10 products. <a class="link" href="../../../upgrade/">Upgrade to the paid version</a> for unlimited products.
            </div>

            <div class="page-navigation">
                <a href="../getting-started/version-comparison.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Free vs. Paid Version
                </a>
                <a href="sales-tracking.php" class="nav-button next">
                    Next: Expense/Revenue Tracking
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
