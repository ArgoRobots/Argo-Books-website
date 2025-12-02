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

// Define sidebar navigation structure with category folders
$sidebarSections = [
    'Getting Started' => [
        'folder' => 'getting-started',
        'pages' => [
            'system-requirements' => 'System Requirements',
            'installation' => 'Installation Guide',
            'quick-start' => 'Quick Start Tutorial',
            'version-comparison' => 'Free vs. Paid Version'
        ]
    ],
    'Core Features' => [
        'folder' => 'features',
        'pages' => [
            'product-management' => 'Product Management',
            'sales-tracking' => 'Purchase/Sales Tracking',
            'receipts' => 'Receipt Management',
            'spreadsheet-import' => 'Spreadsheet Import',
            'spreadsheet-export' => 'Spreadsheet Export',
            'report-generator' => 'Report Generator',
            'advanced-search' => 'Advanced Search'
        ]
    ],
    'Reference' => [
        'folder' => 'reference',
        'pages' => [
            'accepted-countries' => 'Accepted Countries',
            'supported-currencies' => 'Supported Currencies',
            'supported-languages' => 'Supported Languages'
        ]
    ],
    'Security' => [
        'folder' => 'security',
        'pages' => [
            'encryption' => 'Encryption',
            'password' => 'Password Protection',
            'backups' => 'Regular Backups',
            'anonymous-data' => 'Anonymous Usage Data'
        ]
    ]
];

// Determine base path based on current file location
$docBasePath = '';
if (isset($pageCategory)) {
    $docBasePath = '../';
}

// Function to check if a page is active
function isActivePage($page, $currentPage) {
    return $page === $currentPage;
}
?>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <nav class="sidebar-nav">
        <?php foreach ($sidebarSections as $sectionTitle => $section): ?>
        <div class="nav-section">
            <h3><?php echo htmlspecialchars($sectionTitle); ?></h3>
            <ul class="nav-links">
                <?php foreach ($section['pages'] as $pageSlug => $pageTitle): ?>
                <li>
                    <a href="<?php echo $docBasePath . $section['folder'] . '/' . $pageSlug; ?>.php"
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
