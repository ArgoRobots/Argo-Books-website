<?php
$pageTitle = 'Product Management';
$pageDescription = 'Learn how to manage products and categories in Argo Books. Create categories, add products, and organize your inventory.';
$currentPage = 'product-management';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Learn how to create categories and manage products in Argo Books.</p>

            <h2>Why Categories Matter</h2>
            <p>Categories are the foundation of how Argo Books organizes your data. Every product must belong to a category. By grouping products into categories, you can:</p>
            <ul>
                <li><strong>Track spending by area:</strong> See how much you're spending on "Office Supplies" vs "Raw Materials" at a glance</li>
                <li><strong>Generate meaningful reports:</strong> Reports and analytics break down data by category, giving you actionable insights</li>
                <li><strong>Stay organized as you grow:</strong> As your product catalog grows, categories keep everything manageable</li>
            </ul>
            <p>For example, a bakery might create categories like "Ingredients," "Packaging," and "Equipment" to track different types of expenses separately.</p>

            <h2>Creating Categories</h2>
            <ol class="steps-list">
                <li>Go to "Categories" in the navigation menu (under Management)</li>
                <li>Click "Add Category"</li>
                <li>Enter the category name</li>
                <li>Save the category</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Create your categories before adding products. This makes it easier to assign each product to the right category as you go.
            </div>

            <h2>Adding Products</h2>
            <ol class="steps-list">
                <li>Go to "Products/Services" in the navigation menu (under Management)</li>
                <li>Click "Add Product"</li>
                <li>Enter the product name</li>
                <li>Select a category</li>
                <li>Set the unit price and any other details</li>
                <li>Save the product</li>
            </ol>

            <h2>Product Fields</h2>
            <p>When adding or editing a product, you can set the following:</p>
            <ul>
                <li><strong>Name:</strong> The product or service name</li>
                <li><strong>Category:</strong> Which category the product belongs to</li>
                <li><strong>Unit Price:</strong> The default price per unit</li>
                <li><strong>Supplier:</strong> The vendor you purchase this product from</li>
                <li><strong>Description:</strong> Additional notes about the product</li>
            </ul>

            <h2>Managing Existing Products</h2>
            <p>From the Products/Services page, you can edit or delete any product using the action buttons. Changes to a product's details (like price or category) apply going forward and do not modify past transactions.</p>

            <div class="warning-box">
                <strong>Important:</strong> Free version users are limited to 10 products. <a class="link" href="../../../upgrade/">Upgrade to the paid version</a> for unlimited products.
            </div>

            <div class="page-navigation">
                <a href="dashboard.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Dashboard
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
