<?php
$pageTitle = 'Documentation & User Guide';
$pageDescription = 'Complete Argo Books documentation and user guide. Learn installation, features, tutorials for sales tracking, product management, analytics, Excel import/export, and security settings.';
$currentPage = 'index';

include 'docs-header.php';
include 'sidebar.php';
?>
        <script src="../resources/scripts/ScrollToCenter.js"></script>
        <script src="../resources/scripts/levenshtein.js"></script>
        <script src="search.js"></script>
        <link rel="stylesheet" href="search.css">

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Argo Books Documentation</h1>
                <!-- SEARCH BAR -->
                <div class="search-container">
                    <div class="search-box">
                        <input type="text" id="docSearchInput" placeholder="Search documentation... (e.g., 'installation', 'export', 'password')"
                            aria-label="Search documentation">
                        <button id="searchButton" aria-label="Search">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="searchResults" class="search-results" style="display: none;"></div>
                </div>
                <!-- SEARCH BAR -->
                <p>Welcome to the Argo Books documentation. This guide will help you get started and make the
                    most of our software.</p>

                <div class="contact-box">
                    <p><strong>Need Help?</strong> If you have questions or need assistance, please <a
                            href="../contact-us/" class="link">contact us</a>. Our support team is here to help
                        you succeed with Argo Books.</p>
                </div>
            </section>

            <!-- Getting Started Overview -->
            <section class="article">
                <h2>Getting Started</h2>
                <p>New to Argo Books? Start here to learn the basics.</p>
                <div class="section-links">
                    <a href="pages/getting-started/system-requirements.php" class="section-link-card">
                        <h3>System Requirements</h3>
                        <p>Check if your system meets the requirements for Windows, macOS, or Linux.</p>
                    </a>
                    <a href="pages/getting-started/installation.php" class="section-link-card">
                        <h3>Installation Guide</h3>
                        <p>Download and install Argo Books on your computer.</p>
                    </a>
                    <a href="pages/getting-started/quick-start.php" class="section-link-card">
                        <h3>Quick Start Tutorial</h3>
                        <p>Get up and running quickly with a step-by-step guide.</p>
                    </a>
                    <a href="pages/getting-started/version-comparison.php" class="section-link-card">
                        <h3>Free vs. Paid Version</h3>
                        <p>Compare features and choose the right version for you.</p>
                    </a>
                </div>
            </section>

            <!-- Core Features Overview -->
            <section class="article">
                <h2>Core Features</h2>
                <p>Learn how to use the main features of Argo Books.</p>
                <div class="section-links">
                    <a href="pages/features/product-management.php" class="section-link-card">
                        <h3>Product Management</h3>
                        <p>Create categories and manage your product inventory.</p>
                    </a>
                    <a href="pages/features/sales-tracking.php" class="section-link-card">
                        <h3>Purchase/Sales Tracking</h3>
                        <p>Track all your business transactions.</p>
                    </a>
                    <a href="pages/features/receipts.php" class="section-link-card">
                        <h3>Receipt Management</h3>
                        <p>Attach and manage digital receipts for transactions.</p>
                    </a>
                    <a href="pages/features/spreadsheet-import.php" class="section-link-card">
                        <h3>Spreadsheet Import</h3>
                        <p>Import data from Excel spreadsheets.</p>
                    </a>
                    <a href="pages/features/spreadsheet-export.php" class="section-link-card">
                        <h3>Spreadsheet Export</h3>
                        <p>Export your data to Excel for backup or analysis.</p>
                    </a>
                    <a href="pages/features/report-generator.php" class="section-link-card">
                        <h3>Report Generator</h3>
                        <p>Create professional reports with charts and analytics.</p>
                    </a>
                    <a href="pages/features/advanced-search.php" class="section-link-card">
                        <h3>Advanced Search</h3>
                        <p>Use powerful search operators and AI-powered queries.</p>
                    </a>
                </div>
            </section>

            <!-- Reference Overview -->
            <section class="article">
                <h2>Reference</h2>
                <p>Detailed reference information for data import and localization.</p>
                <div class="section-links">
                    <a href="pages/reference/accepted-countries.php" class="section-link-card">
                        <h3>Accepted Countries</h3>
                        <p>View accepted country names and variants for import.</p>
                    </a>
                    <a href="pages/reference/supported-currencies.php" class="section-link-card">
                        <h3>Supported Currencies</h3>
                        <p>View the 28 supported currencies.</p>
                    </a>
                    <a href="pages/reference/supported-languages.php" class="section-link-card">
                        <h3>Supported Languages</h3>
                        <p>View the 54 supported languages.</p>
                    </a>
                </div>
            </section>

            <!-- Security Overview -->
            <section class="article">
                <h2>Security</h2>
                <p>Learn how to keep your business data safe and secure.</p>
                <div class="section-links">
                    <a href="pages/security/encryption.php" class="section-link-card">
                        <h3>Encryption</h3>
                        <p>Learn about AES-256 encryption for your data.</p>
                    </a>
                    <a href="pages/security/password.php" class="section-link-card">
                        <h3>Password Protection</h3>
                        <p>Set up password protection and Windows Hello.</p>
                    </a>
                    <a href="pages/security/backups.php" class="section-link-card">
                        <h3>Regular Backups</h3>
                        <p>Create backups to prevent data loss.</p>
                    </a>
                    <a href="pages/security/anonymous-data.php" class="section-link-card">
                        <h3>Anonymous Usage Data</h3>
                        <p>Manage privacy settings and data collection.</p>
                    </a>
                </div>
            </section>
        </main>

<?php include 'docs-footer.php'; ?>
