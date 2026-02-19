<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Quick Start Tutorial';
$pageDescription = 'Get started quickly with Argo Books. Learn the basic steps to set up your business and start tracking products and revenue.';
$currentPage = 'quick-start';
$pageCategory = 'getting-started';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Get up and running with Argo Books in just a few steps.</p>

            <ol class="steps-list">
                <li>Choose your default currency and create your first company</li>
                <li>Add your suppliers</li>
                <li>Set up categories to organize your products</li>
                <li>Add your initial products</li>
                <li>Add expenses and revenue</li>
            </ol>

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
