<?php
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

// Define sidebar navigation structure
$sidebarSections = [
    'Getting Started' => [
        'system-requirements' => 'System Requirements',
        'installation' => 'Installation Guide',
        'quick-start' => 'Quick Start Tutorial',
        'version-comparison' => 'Free vs. Paid Version'
    ],
    'Core Features' => [
        'product-management' => 'Product Management',
        'sales-tracking' => 'Purchase/Sales Tracking',
        'receipts' => 'Receipt Management',
        'spreadsheet-import' => 'Spreadsheet Import',
        'spreadsheet-export' => 'Spreadsheet Export',
        'report-generator' => 'Report Generator',
        'advanced-search' => 'Advanced Search'
    ],
    'Reference' => [
        'accepted-countries' => 'Accepted Countries',
        'supported-currencies' => 'Supported Currencies',
        'supported-languages' => 'Supported Languages'
    ],
    'Security' => [
        'encryption' => 'Encryption',
        'password' => 'Password Protection',
        'backups' => 'Regular Backups',
        'anonymous-data' => 'Anonymous Usage Data'
    ]
];

// Function to check if a page is active
function isActivePage($page, $currentPage) {
    return $page === $currentPage;
}
?>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php foreach ($sidebarSections as $sectionTitle => $pages): ?>
        <div class="nav-section">
            <h3><?php echo htmlspecialchars($sectionTitle); ?></h3>
            <ul class="nav-links">
                <?php foreach ($pages as $pageSlug => $pageTitle): ?>
                <li>
                    <a href="<?php echo $pageSlug; ?>.php"
                       class="<?php echo isActivePage($pageSlug, $currentPage) ? 'active' : ''; ?>"
                       title="<?php echo htmlspecialchars($pageTitle); ?>">
                        <?php echo htmlspecialchars($pageTitle); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </nav>
</aside>
