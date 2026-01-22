<?php
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Installation Guide
                </a>
                <a href="version-comparison.php" class="nav-button next">
                    Next: Free vs. Paid Version
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
