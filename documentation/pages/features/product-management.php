<?php
require_once __DIR__ . '/../../../resources/icons.php';
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
                <li>Go to "Categories" in the navigation menu (under Management)</li>
                <li>Click "Add Category"</li>
                <li>Enter the category name</li>
                <li>Save the category</li>
            </ol>

            <h2>Adding Products</h2>
            <ol class="steps-list">
                <li>Go to "Products/Services" in the navigation menu (under Management)</li>
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
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Free vs. Paid Version
                </a>
                <a href="sales-tracking.php" class="nav-button next">
                    Next: Expense/Revenue Tracking
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
