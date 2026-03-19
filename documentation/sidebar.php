<?php
require_once __DIR__ . '/../resources/icons.php';
/**
 * Documentation Sidebar Component
 *
 * Usage: Include this file in documentation pages
 * Set $currentPage variable before including to highlight the active page
 * Example: $currentPage = 'installation'; include 'sidebar.php';
 */

// Get current page from variable or URL
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
}

// Define sidebar navigation structure with category folders
$sidebarSections = [
    'Getting Started' => [
        'folder' => 'pages/getting-started',
        'pages' => [
            'system-requirements' => ['title' => 'System Requirements'],
            'installation' => ['title' => 'Installation Guide'],
            'quick-start' => ['title' => 'Quick Start Tutorial'],
            'version-comparison' => ['title' => 'Free vs. Paid Version']
        ]
    ],
    'Core Features' => [
        'folder' => 'pages/features',
        'pages' => [
            'dashboard' => ['title' => 'Dashboard'],
            'analytics' => ['title' => 'Analytics'],
            'predictive-analytics' => ['title' => 'Predictive Analytics'],
            'report-generator' => ['title' => 'Report Generator'],
            'sales-tracking' => ['title' => 'Expense/Revenue Tracking'],
            'invoicing' => ['title' => 'Invoicing & Payments'],
            'rental' => ['title' => 'Rental Management'],
            'customers' => ['title' => 'Customer Management'],
            'product-management' => ['title' => 'Product Management'],
            'suppliers' => ['title' => 'Supplier Management'],
            'inventory' => ['title' => 'Inventory Management'],
            'purchase-orders' => ['title' => 'Purchase Orders'],
            'returns' => ['title' => 'Returns'],
            'receipts' => ['title' => 'Receipt Management'],
            'receipt-scanning' => ['title' => 'AI Receipt Scanning'],
            'spreadsheet-import' => ['title' => 'AI Spreadsheet Import'],
            'spreadsheet-export' => ['title' => 'Spreadsheet Export'],
            'history-modal' => ['title' => 'Version History']
        ]
    ],
    'Reference' => [
        'folder' => 'pages/reference',
        'pages' => [
            'accepted-countries' => ['title' => 'Accepted Countries'],
            'supported-currencies' => ['title' => 'Supported Currencies'],
            'supported-languages' => ['title' => 'Supported Languages'],
            'keyboard_shortcuts' => ['title' => 'Keyboard Shortcuts']
        ]
    ],
    'Security' => [
        'folder' => 'pages/security',
        'pages' => [
            'encryption' => ['title' => 'Encryption'],
            'password' => ['title' => 'Password Protection'],
            'backups' => ['title' => 'Regular Backups'],
            'anonymous-data' => ['title' => 'Anonymous Usage Data']
        ]
    ]
];

// Determine base path based on current file location
$docBasePath = isset($pageCategory) ? '../../' : '';

// Function to check if a page is active
function isActivePage($page, $currentPage) {
    return $page === $currentPage;
}
?>

<!-- Mobile Sidebar Toggle Button -->
<button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle navigation menu">
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
</button>

<!-- Sidebar Navigation -->
<aside class="sidebar" id="docsSidebar">
    <nav class="sidebar-nav">
        <!-- Search -->
        <div class="sidebar-search search-container">
            <div class="search-input-wrapper">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" id="docSearchInput" placeholder="Search docs..." aria-label="Search documentation" data-base-path="<?php echo $docBasePath; ?>">
                <kbd class="search-shortcut">Ctrl+K</kbd>
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>

        <?php foreach ($sidebarSections as $sectionTitle => $section): ?>
        <?php
            $isSectionActive = false;
            foreach ($section['pages'] as $pageSlug => $pageData) {
                if (isActivePage($pageSlug, $currentPage)) {
                    $isSectionActive = true;
                    break;
                }
            }
        ?>
        <div class="nav-section <?php echo $isSectionActive ? 'expanded' : ''; ?>">
            <button class="nav-section-toggle" type="button" aria-expanded="<?php echo $isSectionActive ? 'true' : 'false'; ?>">
                <span><?php echo htmlspecialchars($sectionTitle); ?></span>
                <svg class="nav-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
            <ul class="nav-links">
                <?php foreach ($section['pages'] as $pageSlug => $pageData): ?>
                <li>
                    <a href="<?php echo $docBasePath . $section['folder'] . '/' . $pageSlug; ?>.php"
                       class="<?php echo isActivePage($pageSlug, $currentPage) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($pageData['title']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </nav>
</aside>
