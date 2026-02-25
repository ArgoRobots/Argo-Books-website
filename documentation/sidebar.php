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

// Define sidebar navigation structure with category folders and icons
$sidebarSections = [
    'Getting Started' => [
        'folder' => 'pages/getting-started',
        'pages' => [
            'system-requirements' => [
                'title' => 'System Requirements',
                'icon' => svg_icon('monitor', 18)
            ],
            'installation' => [
                'title' => 'Installation Guide',
                'icon' => svg_icon('download', 18)
            ],
            'quick-start' => [
                'title' => 'Quick Start Tutorial',
                'icon' => svg_icon('play', 18)
            ],
            'version-comparison' => [
                'title' => 'Free vs. Paid Version',
                'icon' => svg_icon('table', 18)
            ]
        ]
    ],
    'Core Features' => [
        'folder' => 'pages/features',
        'pages' => [
            'dashboard' => [
                'title' => 'Dashboard',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'
            ],
            'analytics' => [
                'title' => 'Analytics',
                'icon' => svg_icon('analytics', 18)
            ],
            'predictive-analytics' => [
                'title' => 'Predictive Analytics',
                'icon' => svg_icon('analytics', 18)
            ],
            'report-generator' => [
                'title' => 'Report Generator',
                'icon' => svg_icon('document-lines', 18)
            ],
            'sales-tracking' => [
                'title' => 'Expense/Revenue Tracking',
                'icon' => svg_icon('trending-up', 18)
            ],
            'invoicing' => [
                'title' => 'Invoicing & Payments',
                'icon' => svg_icon('document', 18)
            ],
            'rental' => [
                'title' => 'Rental Management',
                'icon' => svg_icon('calendar', 18)
            ],
            'customers' => [
                'title' => 'Customer Management',
                'icon' => svg_icon('users', 18)
            ],
            'product-management' => [
                'title' => 'Product Management',
                'icon' => svg_icon('package', 18)
            ],
            'suppliers' => [
                'title' => 'Supplier Management',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'
            ],
            'inventory' => [
                'title' => 'Inventory Management',
                'icon' => svg_icon('package', 18)
            ],
            'purchase-orders' => [
                'title' => 'Purchase Orders',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M9 15l3-3 3 3"/></svg>'
            ],
            'returns' => [
                'title' => 'Returns',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>'
            ],
            'receipts' => [
                'title' => 'Receipt Management',
                'icon' => svg_icon('receipt', 18)
            ],
            'receipt-scanning' => [
                'title' => 'AI Receipt Scanning',
                'icon' => svg_icon('receipt-scan', 18)
            ],
            'spreadsheet-import' => [
                'title' => 'Spreadsheet Import',
                'icon' => svg_icon('document-upload', 18)
            ],
            'spreadsheet-export' => [
                'title' => 'Spreadsheet Export',
                'icon' => svg_icon('document-download', 18)
            ],
            'history-modal' => [
                'title' => 'Version History',
                'icon' => svg_icon('clock', 18)
            ]
        ]
    ],
    'Reference' => [
        'folder' => 'pages/reference',
        'pages' => [
            'accepted-countries' => [
                'title' => 'Accepted Countries',
                'icon' => svg_icon('globe', 18)
            ],
            'supported-currencies' => [
                'title' => 'Supported Currencies',
                'icon' => svg_icon('dollar', 18)
            ],
            'supported-languages' => [
                'title' => 'Supported Languages',
                'icon' => svg_icon('translate', 18)
            ],
            'keyboard_shortcuts' => [
                'title' => 'Keyboard Shortcuts',
                'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M6 8h.01M10 8h.01M14 8h.01M18 8h.01M8 12h.01M12 12h.01M16 12h.01M7 16h10"/></svg>'
            ]
        ]
    ],
    'Security' => [
        'folder' => 'pages/security',
        'pages' => [
            'encryption' => [
                'title' => 'Encryption',
                'icon' => svg_icon('lock', 18)
            ],
            'password' => [
                'title' => 'Password Protection',
                'icon' => svg_icon('key', 18)
            ],
            'backups' => [
                'title' => 'Regular Backups',
                'icon' => svg_icon('database', 18)
            ],
            'anonymous-data' => [
                'title' => 'Anonymous Usage Data',
                'icon' => svg_icon('eye-off', 18)
            ]
        ]
    ]
];

// Determine base path based on current file location
// From index.php: folders already include 'pages/' prefix, so no base needed
// From subpages: need '../../' to go up from pages/category/ to documentation/
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
<aside class="sidebar">
    <nav class="sidebar-nav">
        <!-- Home Button -->
        <a href="<?php echo $docBasePath; ?>index.php" class="sidebar-home-btn">
            <?= svg_icon('house', 18) ?>
            <span>Documentation Home</span>
        </a>

        <?php foreach ($sidebarSections as $sectionTitle => $section): ?>
        <div class="nav-section">
            <h3><?php echo htmlspecialchars($sectionTitle); ?></h3>
            <ul class="nav-links">
                <?php foreach ($section['pages'] as $pageSlug => $pageData): ?>
                <li>
                    <a href="<?php echo $docBasePath . $section['folder'] . '/' . $pageSlug; ?>.php"
                       class="<?php echo isActivePage($pageSlug, $currentPage) ? 'active' : ''; ?>"
                       title="<?php echo htmlspecialchars($pageData['title']); ?>">
                        <span class="nav-icon"><?php echo $pageData['icon']; ?></span>
                        <span class="nav-text"><?php echo htmlspecialchars($pageData['title']); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>
    </nav>
</aside>
