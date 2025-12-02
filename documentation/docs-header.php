<?php
/**
 * Documentation Page Header Component
 *
 * Usage: Set $pageTitle and $pageDescription before including this file
 * Example:
 *   $pageTitle = 'Installation Guide';
 *   $pageDescription = 'Learn how to install Argo Books on your computer.';
 *   include 'docs-header.php';
 */

// Default values if not set
if (!isset($pageTitle)) {
    $pageTitle = 'Documentation';
}
if (!isset($pageDescription)) {
    $pageDescription = 'Argo Books documentation and user guide.';
}

// Determine base path for resources based on page location
// Pages in pages/category/ need to go up 3 levels, index needs to go up 1 level
$resourcePath = isset($pageCategory) ? '../../../' : '../';
$docsPath = isset($pageCategory) ? '../../' : '';

$fullTitle = $pageTitle . ' - Argo Books Documentation';
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

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-AB">
    <meta name="geo.placename" content="Calgary">
    <meta name="geo.position" content="51.0447;-114.0719">
    <meta name="ICBM" content="51.0447, -114.0719">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/documentation/">

    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $resourcePath; ?>resources/images/argo-logo/A-logo.ico">
    <title><?php echo htmlspecialchars($fullTitle); ?></title>

    <script src="<?php echo $docsPath; ?>main.js"></script>
    <script src="<?php echo $resourcePath; ?>resources/scripts/jquery-3.6.0.js"></script>
    <script src="<?php echo $resourcePath; ?>resources/scripts/main.js"></script>

    <link rel="stylesheet" href="<?php echo $docsPath; ?>style.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/custom-colors.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/link.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/styles/button.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/header/style.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/header/dark.css">
    <link rel="stylesheet" href="<?php echo $resourcePath; ?>resources/footer/style.css">
</head>

<body>
    <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle documentation menu">
        <span class="toggle-text">Docs Menu</span>
        <svg class="menu-icon" width="20" height="20" viewBox="0 0 20 20" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M2 4h12M2 8h16M2 12h12M2 16h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
        </svg>
    </button>

    <header>
        <div id="includeHeader"></div>
    </header>

    <div class="docs-container">
