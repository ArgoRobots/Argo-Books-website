<?php
/**
 * Documentation Page Header Component
 *
 * Usage: Set $pageTitle, $pageDescription, $currentPage, and $pageCategory before including
 */

// Default values
if (!isset($pageTitle)) {
    $pageTitle = 'Documentation';
}
if (!isset($pageDescription)) {
    $pageDescription = 'Argo Books documentation and user guide.';
}

// Determine base path for resources based on page location
$resourcePath = isset($pageCategory) ? '../../../' : '../';
$docsPath = isset($pageCategory) ? '../../' : '';

$fullTitle = $pageTitle . ' - Argo Books Documentation';

// Category display names and colors
$categoryInfo = [
    'getting-started' => ['name' => 'Getting Started', 'color' => 'emerald'],
    'features' => ['name' => 'Core Features', 'color' => 'blue'],
    'reference' => ['name' => 'Reference', 'color' => 'amber'],
    'security' => ['name' => 'Security', 'color' => 'purple']
];

$currentCategory = $categoryInfo[$pageCategory] ?? ['name' => 'Documentation', 'color' => 'blue'];
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
    <meta name="keywords"
        content="argo books documentation, argo books tutorial, business software guide, <?php echo htmlspecialchars(strtolower($pageTitle)); ?>">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($fullTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:url" content="https://argorobots.com/documentation/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($fullTitle); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/documentation/">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $resourcePath; ?>resources/images/argo-logo/A-logo.ico">
    <title><?php echo htmlspecialchars($fullTitle); ?></title>

    <script src="<?php echo $resourcePath; ?>resources/scripts/jquery-3.6.0.js"></script>
    <script src="<?php echo $resourcePath; ?>resources/scripts/main.js"></script>
    <script src="<?php echo $resourcePath; ?>resources/scripts/levenshtein.js"></script>
    <script src="<?php echo $docsPath; ?>search.js"></script>

    <link rel="stylesheet" href="<?php echo $docsPath; ?>style.css">
    <link rel="stylesheet" href="<?php echo $docsPath; ?>search.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/custom-colors.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/link.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/button.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/header/style.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/footer/style.css">
</head>

<body class="docs-page">
    <header>
        <div id="includeHeader"></div>
    </header>

    <!-- Sub-page Hero with Search -->
    <div class="docs-subpage-hero">
        <div class="subpage-hero-content">
            <!-- Breadcrumb -->
            <nav class="docs-breadcrumb">
                <a href="<?php echo $docsPath; ?>">Documentation</a>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"></path>
                </svg>
                <a href="<?php echo $docsPath; ?>#<?php echo $pageCategory; ?>"><?php echo $currentCategory['name']; ?></a>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 18l6-6-6-6"></path>
                </svg>
                <span><?php echo htmlspecialchars($pageTitle); ?></span>
            </nav>

            <!-- Search Bar -->
            <div class="subpage-search">
                <div class="search-input-wrapper">
                    <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <input type="text" id="docSearchInput" placeholder="Search documentation..." aria-label="Search documentation" data-base-path="<?php echo $docsPath; ?>">
                    <kbd class="search-shortcut">Ctrl+K</kbd>
                </div>
                <div id="searchResults" class="search-results"></div>
            </div>
        </div>
    </div>

    <div class="docs-layout">
        <!-- Sidebar -->
        <?php include $docsPath . 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="docs-main-content">
            <!-- Page Header -->
            <div class="docs-page-header">
                <span class="docs-category-badge <?php echo $currentCategory['color']; ?>"><?php echo $currentCategory['name']; ?></span>
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>

