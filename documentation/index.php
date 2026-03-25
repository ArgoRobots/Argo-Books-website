<?php
require_once __DIR__ . '/../resources/icons.php';
$pageTitle = 'Documentation & User Guide';
$pageDescription = 'Complete Argo Books documentation and user guide. Learn installation, features, tutorials for expense/revenue tracking, product management, analytics, Excel import/export, and security settings.';
$currentPage = 'index';
$isDocsLanding = true;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="argo books documentation, argo books tutorial, business software guide, user manual">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> - Argo Books">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="https://argorobots.com/documentation/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?> - Argo Books">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/documentation/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Argo Books</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>
    <script src="../resources/scripts/ScrollToCenter.js"></script>
    <script src="../resources/scripts/levenshtein.js"></script>
    <script src="search.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body class="docs-landing">
    <header>
        <div id="includeHeader"></div>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge">
                <?= svg_icon('book-open', 16) ?>
                Documentation
            </div>
            <h1>How can we help you?</h1>
            <p class="hero-subtitle">Everything you need to master Argo Books - from quick setup to advanced features</p>

            <!-- Search Bar -->
            <div class="hero-search">
                <div class="search-input-wrapper">
                    <?= svg_icon('search', 20, 'search-icon') ?>
                    <input type="text" id="docSearchInput" placeholder="Search for topics, features, tutorials..." aria-label="Search documentation">
                    <kbd class="search-shortcut">Ctrl+K</kbd>
                </div>
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
        <div class="hero-decoration">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="quick-links">
        <a href="pages/getting-started/quick-start.php" class="quick-link">
            <?= svg_icon('bolt', 20) ?>
            Quick Start
        </a>
        <a href="../downloads/" class="quick-link">
            <?= svg_icon('download', 20) ?>
            Download
        </a>
        <a href="../contact-us/" class="quick-link">
            <?= svg_icon('chat', 20) ?>
            Contact Support
        </a>
    </section>

    <!-- Main Content -->
    <div class="docs-main">
        <!-- Getting Started Section -->
        <section class="docs-category" data-category="getting-started">
            <div class="category-header">
                <div class="category-icon getting-started">
                    <?= svg_icon('play-circle', 24) ?>
                </div>
                <div class="category-info">
                    <h2>Getting Started</h2>
                    <p>New to Argo Books? Begin your journey here</p>
                </div>
            </div>
            <div class="category-cards">
                <a href="pages/getting-started/system-requirements.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('monitor', 20) ?>
                    </div>
                    <h3>System Requirements</h3>
                    <p>Windows, macOS, and Linux requirements</p>
                </a>
                <a href="pages/getting-started/installation.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('download', 20) ?>
                    </div>
                    <h3>Installation Guide</h3>
                    <p>Download and install on your computer</p>
                </a>
                <a href="pages/getting-started/quick-start.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('bolt', 20) ?>
                    </div>
                    <h3>Quick Start Tutorial</h3>
                    <p>Get up and running in minutes</p>
                </a>
                <a href="pages/getting-started/version-comparison.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('clipboard-check', 20) ?>
                    </div>
                    <h3>Free vs. Paid Version</h3>
                    <p>Compare features and choose wisely</p>
                </a>
            </div>
        </section>

        <!-- Core Features Section -->
        <section class="docs-category" data-category="features">
            <div class="category-header">
                <div class="category-icon features">
                    <?= svg_icon('grid', 24) ?>
                </div>
                <div class="category-info">
                    <h2>Core Features</h2>
                    <p>Master the powerful tools at your fingertips</p>
                </div>
            </div>
            <div class="category-cards">
                <a href="pages/features/dashboard.php" class="doc-card">
                    <div class="card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </div>
                    <h3>Dashboard</h3>
                    <p>Business overview and key metrics</p>
                </a>
                <a href="pages/features/analytics.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('analytics', 20) ?>
                    </div>
                    <h3>Analytics</h3>
                    <p>Interactive charts and business insights</p>
                </a>
                <a href="pages/features/predictive-analytics.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('analytics', 20) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>ML-powered forecasting and insights</p>
                </a>
                <a href="pages/features/report-generator.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('pie-chart', 20) ?>
                    </div>
                    <h3>Report Generator</h3>
                    <p>Create charts, tables, and analytics</p>
                </a>
                <a href="pages/features/sales-tracking.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('bar-chart', 20) ?>
                    </div>
                    <h3>Expense/Revenue Tracking</h3>
                    <p>Track all business transactions</p>
                </a>
                <a href="pages/features/invoicing.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('document', 20) ?>
                    </div>
                    <h3>Invoicing & Payments</h3>
                    <p>Create invoices, accept payments</p>
                </a>
                <a href="pages/features/rental.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('calendar', 20) ?>
                    </div>
                    <h3>Rental Management</h3>
                    <p>Bookings and availability calendar</p>
                </a>
                <a href="pages/features/customers.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('users', 20) ?>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Track profiles and expense history</p>
                </a>
                <a href="pages/features/product-management.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('shopping-bag', 20) ?>
                    </div>
                    <h3>Product Management</h3>
                    <p>Organize categories and inventory</p>
                </a>
                <a href="pages/features/suppliers.php" class="doc-card">
                    <div class="card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>Supplier Management</h3>
                    <p>Manage vendors and purchase sources</p>
                </a>
                <a href="pages/features/inventory.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('package', 20) ?>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Track stock levels and reorder points</p>
                </a>
                <a href="pages/features/purchase-orders.php" class="doc-card">
                    <div class="card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <path d="M12 18v-6"></path>
                            <path d="M9 15l3-3 3 3"></path>
                        </svg>
                    </div>
                    <h3>Purchase Orders</h3>
                    <p>Track orders placed with suppliers</p>
                </a>
                <a href="pages/features/returns.php" class="doc-card">
                    <div class="card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="1 4 1 10 7 10"></polyline>
                            <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                        </svg>
                    </div>
                    <h3>Returns</h3>
                    <p>Process and track product returns</p>
                </a>
                <a href="pages/features/receipts.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('document', 20) ?>
                    </div>
                    <h3>Receipt Management</h3>
                    <p>Attach and manage digital receipts</p>
                </a>
                <a href="pages/features/receipt-scanning.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('receipt-scan', 20) ?>
                    </div>
                    <h3>AI Receipt Scanning</h3>
                    <p>Extract data from receipts with AI</p>
                </a>
                <a href="pages/features/spreadsheet-import.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('document-plus', 20) ?>
                    </div>
                    <h3>AI Spreadsheet Import</h3>
                    <p>Import from any Excel or CSV file with AI</p>
                </a>
                <a href="pages/features/spreadsheet-export.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('document-upload', 20) ?>
                    </div>
                    <h3>Spreadsheet Export</h3>
                    <p>Export data to Excel for backup</p>
                </a>
                <a href="pages/features/history-modal.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('clock', 20) ?>
                    </div>
                    <h3>Version History</h3>
                    <p>Review, undo, and redo changes</p>
                </a>
            </div>
        </section>

        <!-- Reference Section -->
        <section class="docs-category" data-category="reference">
            <div class="category-header">
                <div class="category-icon reference">
                    <?= svg_icon('book-question', 24) ?>
                </div>
                <div class="category-info">
                    <h2>Reference</h2>
                    <p>Detailed tables and specifications</p>
                </div>
            </div>
            <div class="category-cards compact">
                <a href="pages/reference/supported-currencies.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('dollar', 20) ?>
                    </div>
                    <h3>Supported Currencies</h3>
                    <p>28 currencies available</p>
                </a>
                <a href="pages/reference/supported-languages.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('translate', 20) ?>
                    </div>
                    <h3>Supported Languages</h3>
                    <p>54 languages supported</p>
                </a>
                <a href="pages/reference/keyboard_shortcuts.php" class="doc-card">
                    <div class="card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <path d="M6 8h.01M10 8h.01M14 8h.01M18 8h.01M8 12h.01M12 12h.01M16 12h.01M7 16h10"></path>
                        </svg>
                    </div>
                    <h3>Keyboard Shortcuts</h3>
                    <p>Report Generator shortcuts</p>
                </a>
            </div>
        </section>

        <!-- Security Section -->
        <section class="docs-category" data-category="security">
            <div class="category-header">
                <div class="category-icon security">
                    <?= svg_icon('shield-check', 24) ?>
                </div>
                <div class="category-info">
                    <h2>Security</h2>
                    <p>Keep your business data safe and protected</p>
                </div>
            </div>
            <div class="category-cards">
                <a href="pages/security/encryption.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('lock', 20) ?>
                    </div>
                    <h3>Encryption</h3>
                    <p>AES-256 encryption for your data</p>
                </a>
                <a href="pages/security/password.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('key', 20) ?>
                    </div>
                    <h3>Password Protection</h3>
                    <p>Setup password and biometric login</p>
                </a>
                <a href="pages/security/backups.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('save', 20) ?>
                    </div>
                    <h3>Regular Backups</h3>
                    <p>Create backups to prevent data loss</p>
                </a>
                <a href="pages/security/anonymous-data.php" class="doc-card">
                    <div class="card-icon">
                        <?= svg_icon('eye', 20) ?>
                    </div>
                    <h3>Anonymous Usage Data</h3>
                    <p>Privacy and data collection settings</p>
                </a>
            </div>
        </section>
    </div>
    <!-- Help Banner -->
    <section class="help-banner">
        <div class="help-content">
            <div class="help-icon">
                <?= svg_icon('help-circle', 32) ?>
            </div>
            <div class="help-text">
                <h3>Still have questions?</h3>
                <p>Our support team is ready to help you succeed with Argo Books</p>
            </div>
            <a href="../contact-us/" class="help-button">Contact Support</a>
        </div>
    </section>

    </main>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        // Keyboard shortcut for search
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('docSearchInput').focus();
            }
        });
    </script>
</body>

</html>
