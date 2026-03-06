<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Quick Start Tutorial';
$pageDescription = 'Get started quickly with Argo Books. Learn the basic steps to set up your business and start tracking products and revenue.';
$currentPage = 'quick-start';
$pageCategory = 'getting-started';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Get up and running with Argo Books in just a few steps. When you first open the application, a setup checklist will guide you through the initial configuration.</p>

            <h2>1. Create Your First Company</h2>
            <p>When you launch Argo Books for the first time, you'll be prompted to create a company. Choose your default currency and enter your company name. Each company's data is stored in a single <strong>.argo</strong> file that you can back up, copy, or move between computers.</p>

            <div class="info-box">
                <strong>Tip:</strong> Want to explore the app first? You can open the <strong>Sample Company</strong> from the welcome screen to see Argo Books populated with example data before setting up your own.
            </div>

            <h2>2. Add Your Suppliers</h2>
            <p>Navigate to "Suppliers" under the Management section in the sidebar. Add the businesses you purchase from, including their contact information and address details. Suppliers can be linked to your expense transactions later.</p>

            <h2>3. Set Up Categories</h2>
            <p>Go to "Categories" under Management to create categories for organizing your products and transactions. Categories help you group similar items together, making it easier to track spending and generate meaningful reports. For example, you might create categories like "Office Supplies," "Electronics," or "Raw Materials."</p>

            <h2>4. Add Your Products</h2>
            <p>Navigate to "Products/Services" under Management. Add the items your business sells or uses, assigning each one to a category. You can set pricing, supplier, and other details for each product.</p>

            <div class="info-box">
                <strong>Note:</strong> The free version supports up to 10 products. <a class="link" href="../../../pricing/">Upgrade to Premium</a> for unlimited products.
            </div>

            <h2>5. Start Tracking Expenses and Revenue</h2>
            <p>You're ready to go. Navigate to "Expenses" or "Revenue" under the Transactions section to start recording your business transactions. Each entry can include multiple products, a receipt attachment, customer, and more.</p>

            <h2>In-App Tutorials</h2>
            <p>Argo Books includes a built-in tutorial system that walks you through key features step by step, directly inside the app. When you open certain pages for the first time, a tutorial will appear explaining how to use the feature. You can also re-access tutorials at any time from the help menu.</p>

            <div class="page-navigation">
                <a href="installation.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Installation Guide
                </a>
                <a href="version-comparison.php" class="nav-button next">
                    Next: Free vs. Paid Version
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
