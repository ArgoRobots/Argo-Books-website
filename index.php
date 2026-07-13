<?php
session_start();
require_once __DIR__ . '/community/users/user_functions.php';
require_once __DIR__ . '/track_referral.php';
require_once __DIR__ . '/statistics.php';
require_once __DIR__ . '/resources/icons.php';

track_page_view($_SERVER['REQUEST_URI']);

// Check for remember me cookie and auto-login user if valid
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    check_remember_me();
}

// Pull the current app version + release date from the Sparkle update feed so
// the homepage JSON-LD stays in sync with releases. Fallbacks cover the case
// where the feed is missing or malformed; both values are echoed via
// json_encode below so anything unexpected can't break the structured-data
// block.
$current_version = '2.0.7';
$current_release_date = '2026-01-01';
$update_xml = @simplexml_load_file(__DIR__ . '/avalonia-update.xml');
if ($update_xml !== false && isset($update_xml->channel->item[0])) {
    $item = $update_xml->channel->item[0];
    $sparkle_version = (string) $item->children('sparkle', true)->version;
    if ($sparkle_version !== '') {
        $current_version = $sparkle_version;
    }
    $parsed_pub_date = strtotime((string) $item->pubDate);
    if ($parsed_pub_date !== false) {
        $current_release_date = date('Y-m-d', $parsed_pub_date);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Argo Books is free accounting and invoicing software for small business. Track income and expenses, send invoices, and scan receipts in minutes.">
    <meta name="keywords"
        content="receipt scanning, smart spreadsheet import, predictive analytics, business software, inventory management, rental management, invoice generator, small business automation, data import">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books: Free Accounting Software for Small Business">
    <meta property="og:description"
        content="Free accounting and invoicing software for small business. Track income and expenses, send invoices, and scan receipts in minutes.">
    <meta property="og:url" content="https://argorobots.com/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books: Free Accounting Software for Small Business">
    <meta name="twitter:description"
        content="Free accounting and invoicing software for small business. Track income and expenses, send invoices, and scan receipts in minutes.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">
    <meta name="geo.position" content="52.1579;-106.6702">
    <meta name="ICBM" content="52.1579, -106.6702">

     <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/">

    <!-- Preconnect hints -->
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.googletagmanager.com">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "description": "Free accounting and invoicing software for small business, with receipt scanning, spreadsheet import, and inventory management",
            "url": "https://argorobots.com/",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, macOS, Linux",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "availability": "https://schema.org/InStock"
            },
            "publisher": {
                "@type": "Organization",
                "name": "Argo",
                "url": "https://argorobots.com/",
                "address": {
                    "@type": "PostalAddress",
                    "addressLocality": "Saskatoon",
                    "addressRegion": "SK",
                    "addressCountry": "CA"
                }
            },
            "downloadUrl": "https://argorobots.com/downloads",
            "softwareVersion": <?php echo json_encode($current_version); ?>,
            "datePublished": "2025-05-01",
            "dateModified": <?php echo json_encode($current_release_date); ?>
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books: Free Accounting Software for Small Business</title>

    <script defer src="resources/scripts/main.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="style.css?v=<?= @filemtime(__DIR__ . '/style.css') ?>">
    <link rel="stylesheet" href="resources/styles/custom-colors.css">
    <link rel="stylesheet" href="resources/styles/button.css">
    <link rel="stylesheet" href="resources/styles/pricing-cards.css">
    <link rel="stylesheet" href="resources/header/style.css">
    <link rel="stylesheet" href="resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
            <div class="hero-gradient-orb hero-orb-3"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title animate-fade-in-up">
                    <span class="hero-app-title hero-title-line">Argo Books</span>
                    <span class="hero-title-line">Simple accounting</span>
                    <span class="hero-title-line text-gradient">software for</span>
                    <span class="hero-title-line text-gradient">small business</span>
                </h1>
                <p class="hero-subtitle animate-fade-in-up delay-1">
                    Track income and expenses, send invoices, and scan receipts in minutes.
                    Automated imports and tax-ready reports keep you organized all year.
                    Free to start, no accounting knowledge needed.
                </p>
                <div class="hero-cta animate-fade-in-up delay-2">
                    <a href="downloads" class="btn btn-primary btn-lg">
                        <span>Get Started For Free</span>
                        <?= svg_icon('arrow-right', 20) ?>
                    </a>
                </div>
            </div>
            <div class="hero-visual animate-fade-in-up delay-2">
                <div class="hero-device">
                    <div class="device-frame">
                        <img src="resources/images/dashboard.webp"
                             srcset="resources/images/dashboard-800.webp 800w, resources/images/dashboard-1200.webp 1200w, resources/images/dashboard-1600.webp 1600w, resources/images/dashboard.webp 2400w"
                             sizes="(max-width: 768px) 90vw, 600px"
                             alt="Argo Books Dashboard" class="device-screen" width="2400" height="1528" fetchpriority="high">
                        <button class="hero-play-btn" id="heroPlayBtn" aria-label="Watch demo video">
                            <?= svg_icon('play-filled', 28) ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Tabbed Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">The tools your business actually runs on</h2>
                <p class="section-description">Smart receipt scanning, invoicing, analytics, and inventory tracking, all in one easy app.</p>
            </div>

            <div class="features-tabs">
                <div class="features-tabs-nav animate-on-scroll">
                    <button class="tab-btn active" data-tab="ai-receipts">
                        <div class="tab-icon">
                            <?= svg_icon('receipt-scan-detail', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Receipt Scanning</span>
                            <span class="tab-subtitle">Snap a photo and your books update instantly</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="invoices">
                        <div class="tab-icon">
                            <?= svg_icon('document', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Invoicing</span>
                            <span class="tab-subtitle">Create, send, and track invoices to get paid</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="expenses">
                        <div class="tab-icon">
                            <?= svg_icon('dollar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Expense & Revenue Tracking</span>
                            <span class="tab-subtitle">Every dollar in and out, auto-categorized</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="customers">
                        <div class="tab-icon">
                            <?= svg_icon('users', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Customer Management</span>
                            <span class="tab-subtitle">Contacts, purchase history, and balances</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="predictive">
                        <div class="tab-icon">
                            <?= svg_icon('analytics', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Predictive Analytics</span>
                            <span class="tab-subtitle">See next month's cash flow in advance</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="inventory">
                        <div class="tab-icon">
                            <?= svg_icon('package', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Inventory Management</span>
                            <span class="tab-subtitle">Stock counts that stay accurate as you sell</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="rental">
                        <div class="tab-icon">
                            <?= svg_icon('calendar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Rental Management</span>
                            <span class="tab-subtitle">Bookings, availability, and returns tracked</span>
                        </div>
                    </button>
                </div>

                <div class="features-tabs-content">
                    <!-- AI Receipt Scanning -->
                    <div class="tab-content active" id="tab-ai-receipts">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Scan Receipt</div>
                                                    <div class="scan-stage" id="receiptScan">
                                                        <div class="scan-receipt">
                                                            <div class="rcpt-paper">
                                                                <div class="rcpt-store scan-row" data-field="merchant">OFFICE DEPOT</div>
                                                                <div class="rcpt-meta scan-row" data-field="date">Store #1284 &middot; Mar 14, 2026</div>
                                                                <div class="rcpt-divider"></div>
                                                                <div class="rcpt-line scan-row" data-field="item-0"><span>COPY PAPER 5RM</span><span>42.99</span></div>
                                                                <div class="rcpt-line scan-row" data-field="item-1"><span>INK CART BK</span><span>34.99</span></div>
                                                                <div class="rcpt-line scan-row" data-field="item-2"><span>DESK ORGANIZER</span><span>24.49</span></div>
                                                                <div class="rcpt-line scan-row" data-field="item-3"><span>STICKY NOTES 12</span><span>8.99</span></div>
                                                                <div class="rcpt-divider"></div>
                                                                <div class="rcpt-line rcpt-tax scan-row" data-field="tax"><span>TAX</span><span>8.89</span></div>
                                                                <div class="rcpt-line rcpt-total scan-row" data-field="total"><span>TOTAL</span><span>120.35</span></div>
                                                                <div class="rcpt-barcode"><svg viewBox="0 0 158 34" preserveAspectRatio="none" aria-hidden="true"><path d="M0 0h2v34h-2z M4 0h1v34h-1z M7 0h3v34h-3z M12 0h1v34h-1z M15 0h2v34h-2z M19 0h1v34h-1z M22 0h3v34h-3z M27 0h1v34h-1z M30 0h2v34h-2z M34 0h1v34h-1z M37 0h3v34h-3z M42 0h2v34h-2z M46 0h1v34h-1z M49 0h2v34h-2z M53 0h3v34h-3z M58 0h1v34h-1z M61 0h1v34h-1z M64 0h3v34h-3z M69 0h2v34h-2z M73 0h1v34h-1z M76 0h2v34h-2z M80 0h1v34h-1z M83 0h3v34h-3z M88 0h1v34h-1z M91 0h2v34h-2z M95 0h3v34h-3z M100 0h1v34h-1z M103 0h2v34h-2z M107 0h1v34h-1z M110 0h3v34h-3z M115 0h2v34h-2z M119 0h1v34h-1z M122 0h2v34h-2z M126 0h3v34h-3z M131 0h1v34h-1z M134 0h1v34h-1z M137 0h2v34h-2z M141 0h3v34h-3z M146 0h1v34h-1z M149 0h2v34h-2z M153 0h1v34h-1z M156 0h2v34h-2z"/></svg></div>
                                                            </div>
                                                            <div class="scan-beam" id="scanBeam"></div>
                                                        </div>
                                                        <div class="scan-form">
                                                            <div class="ef-head">
                                                                <span class="ef-title">New Expense</span>
                                                                <span class="ef-status" id="efStatus"><?= svg_icon('check', 13) ?> Added to Expenses</span>
                                                            </div>
                                                            <div class="ef-field" data-field="merchant">
                                                                <span class="ef-label">Merchant</span>
                                                                <span class="ef-value">Office Depot</span>
                                                                <span class="ef-check"><?= svg_icon('check', 14) ?></span>
                                                            </div>
                                                            <div class="ef-field" data-field="date">
                                                                <span class="ef-label">Date</span>
                                                                <span class="ef-value">Mar 14, 2026</span>
                                                                <span class="ef-check"><?= svg_icon('check', 14) ?></span>
                                                            </div>
                                                            <div class="ef-field" data-field="category">
                                                                <span class="ef-label">Category</span>
                                                                <span class="ef-value"><span class="ef-pill">Office Supplies</span></span>
                                                                <span class="ef-check"><?= svg_icon('check', 14) ?></span>
                                                            </div>
                                                            <div class="ef-lines">
                                                                <div class="ef-line" data-field="item-0"><span>Copy Paper (5 ream)</span><span>$42.99</span></div>
                                                                <div class="ef-line" data-field="item-1"><span>Ink Cartridge BK</span><span>$34.99</span></div>
                                                                <div class="ef-line" data-field="item-2"><span>Desk Organizer</span><span>$24.49</span></div>
                                                                <div class="ef-line" data-field="item-3"><span>Sticky Notes (12pk)</span><span>$8.99</span></div>
                                                            </div>
                                                            <div class="ef-totals">
                                                                <div class="ef-trow" data-field="tax"><span>Tax</span><span>$8.89</span></div>
                                                                <div class="ef-trow ef-grand" data-field="total"><span>Total</span><span class="ef-total-val">$120.35</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Generation -->
                    <div class="tab-content" id="tab-invoices">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio" id="invoiceStudio" style="--inv-accent: 227 79% 58%;">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">New Invoice</div>
                                                    <div class="invoice-doc theme-contemporary" id="invoiceDoc">
                                                <div class="inv-brandbar"></div>
                                                <div class="inv-top inv-anim">
                                                    <div class="inv-brand-group">
                                                        <div class="inv-brand">
                                                            <span class="inv-mark"><svg viewBox="650 48 2400 2400" fill="currentColor" aria-hidden="true"><path fill-rule="nonzero" d="M 1825.109375 1914.148438 L 1295.769531 2027.972656 C 1292.101562 2028.761719 1288.289062 2027.59375 1285.679688 2024.890625 C 1283.089844 2022.1875 1282.070312 2018.335938 1283 2014.699219 L 1357.828125 1722.378906 L 1361.988281 1733.847656 L 715.601562 1267.910156 C 712.378906 1265.589844 710.6875 1261.691406 711.191406 1257.75 C 711.695312 1253.820312 714.3125 1250.46875 718.011719 1249.03125 L 917.507812 1171.558594 L 911.125 1184.988281 L 789.367188 808.660156 C 788.113281 804.78125 789.140625 800.53125 792.023438 797.660156 C 794.90625 794.78125 799.160156 793.769531 803.035156 795.03125 L 1179.679688 918.160156 L 1166.179688 924.648438 L 1242.941406 720.011719 C 1244.339844 716.28125 1247.671875 713.621094 1251.628906 713.089844 C 1255.570312 712.550781 1259.5 714.230469 1261.839844 717.460938 L 1570.75 1143.378906 L 1551.320312 1151.621094 L 1415.289062 379.421875 C 1414.609375 375.519531 1416.101562 371.558594 1419.191406 369.089844 C 1422.28125 366.621094 1426.46875 366.03125 1430.121094 367.550781 L 1651.171875 459.621094 L 1637.519531 464.410156 L 1838.308594 98.511719 C 1840.210938 95.050781 1843.839844 92.898438 1847.789062 92.890625 C 1851.738281 92.890625 1855.378906 95.03125 1857.28125 98.480469 L 2059.390625 464.289062 L 2045.660156 459.570312 L 2266.378906 365.238281 C 2270.019531 363.679688 2274.230469 364.238281 2277.351562 366.699219 C 2280.460938 369.160156 2281.980469 373.121094 2281.308594 377.03125 L 2148.550781 1148.769531 L 2129.089844 1140.609375 L 2436.378906 714 C 2438.710938 710.769531 2442.628906 709.070312 2446.570312 709.589844 C 2450.519531 710.109375 2453.871094 712.75 2455.289062 716.480469 L 2532.820312 920.359375 L 2519.289062 913.929688 L 2895.609375 789.078125 C 2899.480469 787.800781 2903.738281 788.789062 2906.628906 791.660156 C 2909.53125 794.519531 2910.570312 798.769531 2909.339844 802.648438 L 2788.570312 1180.890625 L 2782.050781 1167.460938 L 2983.929688 1242.929688 C 2987.671875 1244.320312 2990.339844 1247.660156 2990.871094 1251.621094 C 2991.410156 1255.570312 2989.71875 1259.5 2986.488281 1261.839844 L 2341.378906 1728.984375 L 2345.511719 1717.496094 L 2422.128906 2012.820312 C 2423.070312 2016.453125 2422.058594 2020.3125 2419.46875 2023.023438 C 2416.878906 2025.734375 2413.070312 2026.910156 2409.398438 2026.132812 L 1879.988281 1914.027344 L 1893.050781 1902.972656 L 1914.019531 2394.578125 C 1914.140625 2397.527344 1913.058594 2400.398438 1911.019531 2402.53125 C 1908.980469 2404.664062 1906.148438 2405.867188 1903.199219 2405.867188 L 1804.46875 2405.867188 C 1801.519531 2405.867188 1798.710938 2404.671875 1796.671875 2402.554688 C 1794.628906 2400.433594 1793.539062 2397.578125 1793.640625 2394.636719 L 1812.011719 1903.15625 L 1825.109375 1914.148438 Z"/></svg></span>
                                                            <span class="inv-bizname">Maple &amp; Co.</span>
                                                            <div class="inv-status" id="invStatus">Paid</div>
                                                        </div>
                                                        <div class="inv-docref">INVOICE &middot; #INV-0042</div>
                                                    </div>
                                                </div>
                                                <div class="inv-billto inv-anim">
                                                    <span class="inv-label">Bill to</span>
                                                    <span class="inv-client">Sarah Miller</span>
                                                    <span class="inv-client-sub">123 Hollywood Blvd, Los Angeles</span>
                                                </div>
                                                <div class="inv-table">
                                                    <div class="inv-row inv-row-head inv-anim">
                                                        <span>Description</span><span>Qty</span><span>Amount</span>
                                                    </div>
                                                    <div class="inv-row inv-item">
                                                        <span>Logo &amp; brand design</span><span>1</span><span>$600.00</span>
                                                    </div>
                                                    <div class="inv-row inv-item">
                                                        <span>Website build</span><span>1</span><span>$480.00</span>
                                                    </div>
                                                    <div class="inv-row inv-item">
                                                        <span>Hosting (annual)</span><span>1</span><span>$154.00</span>
                                                    </div>
                                                </div>
                                                <div class="inv-totals inv-anim">
                                                    <div class="inv-subtotal"><span>Subtotal</span><span>$1,234.00</span></div>
                                                    <div class="inv-amountdue"><span>Amount Due</span><span class="inv-total-value">$1,234.00</span></div>
                                                </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="studio-panel color-panel panel-hidden" id="colorPanel">
                                            <div class="color-wheel" id="colorWheel">
                                                <div class="color-thumb" id="colorThumb"></div>
                                            </div>
                                            <div class="light-slider" id="lightSlider">
                                                <div class="light-thumb" id="lightThumb"></div>
                                            </div>
                                        </div>

                                        <div class="studio-panel template-panel panel-hidden" id="templatePanel">
                                            <button class="tmpl-btn" data-theme="theme-modern" type="button">
                                                <span class="tmpl-thumb tmpl-modern"></span>Modern
                                            </button>
                                            <button class="tmpl-btn active" data-theme="theme-contemporary" type="button">
                                                <span class="tmpl-thumb tmpl-contemporary"></span>Contemporary
                                            </button>
                                            <button class="tmpl-btn" data-theme="theme-classic" type="button">
                                                <span class="tmpl-thumb tmpl-classic"></span>Classic
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Tracking -->
                    <div class="tab-content" id="tab-expenses">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Overview</div>
                                                    <div class="exp-stage" id="expenseStage">
                                                        <div class="exp-inner">
                                                            <div class="txn-table">
                                                                <div class="txn-head"><span>Date</span><span>Description</span><span>Category</span><span class="txn-amt">Amount</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 14</span><span class="txn-desc">Client payment</span><span class="txn-cat"><span>Consulting</span></span><span class="txn-amt rev">+$1,200.00</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 13</span><span class="txn-desc">Office supplies</span><span class="txn-cat"><span>Office</span></span><span class="txn-amt exp">&minus;$120.35</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 12</span><span class="txn-desc">Website project</span><span class="txn-cat"><span>Consulting</span></span><span class="txn-amt rev">+$2,400.00</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 11</span><span class="txn-desc">Software license</span><span class="txn-cat"><span>Software</span></span><span class="txn-amt exp">&minus;$49.00</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 10</span><span class="txn-desc">Product sale</span><span class="txn-cat"><span>Sales</span></span><span class="txn-amt rev">+$340.00</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 9</span><span class="txn-desc">Utilities</span><span class="txn-cat"><span>Office</span></span><span class="txn-amt exp">&minus;$210.00</span></div>
                                                                <div class="txn-row"><span class="txn-date">Mar 8</span><span class="txn-desc">Consulting fee</span><span class="txn-cat"><span>Consulting</span></span><span class="txn-amt rev">+$1,500.00</span></div>
                                                            </div>

                                                            <div class="exp-chart exp-chart-line" id="expChartLine">
                                                                <div class="exp-chart-head">
                                                                    <span>Revenue vs Expenses</span>
                                                                    <span class="exp-legend"><i class="lg-dot lg-rev"></i>Rev<i class="lg-dot lg-exp"></i>Exp</span>
                                                                </div>
                                                                <svg class="exp-line-svg" viewBox="0 0 240 108" preserveAspectRatio="none" aria-hidden="true">
                                                                    <defs>
                                                                        <linearGradient id="revGrad" x1="0" y1="0" x2="0" y2="1">
                                                                            <stop offset="0" stop-color="#10b981" stop-opacity="0.32"/>
                                                                            <stop offset="1" stop-color="#10b981" stop-opacity="0"/>
                                                                        </linearGradient>
                                                                        <linearGradient id="expGrad" x1="0" y1="0" x2="0" y2="1">
                                                                            <stop offset="0" stop-color="#ef4444" stop-opacity="0.20"/>
                                                                            <stop offset="1" stop-color="#ef4444" stop-opacity="0"/>
                                                                        </linearGradient>
                                                                    </defs>
                                                                    <g class="exp-grid">
                                                                        <line x1="8" y1="24" x2="232" y2="24"/>
                                                                        <line x1="8" y1="48" x2="232" y2="48"/>
                                                                        <line x1="8" y1="72" x2="232" y2="72"/>
                                                                        <line x1="8" y1="96" x2="232" y2="96"/>
                                                                    </g>
                                                                    <path d="M12 94 C27 94 41 90 56 90 C71 90 85 92 100 92 C115 92 129 84 144 84 C159 84 173 86 188 86 C203 86 213 78 228 78 L228 104 L12 104 Z" fill="url(#expGrad)"/>
                                                                    <path d="M12 80 C27 80 41 68 56 68 C71 68 85 72 100 72 C115 72 129 52 144 52 C159 52 173 46 188 46 C203 46 213 28 228 28 L228 104 L12 104 Z" fill="url(#revGrad)"/>
                                                                    <path class="exp-line-e" d="M12 94 C27 94 41 90 56 90 C71 90 85 92 100 92 C115 92 129 84 144 84 C159 84 173 86 188 86 C203 86 213 78 228 78"/>
                                                                    <path class="exp-line-r" d="M12 80 C27 80 41 68 56 68 C71 68 85 72 100 72 C115 72 129 52 144 52 C159 52 173 46 188 46 C203 46 213 28 228 28"/>
                                                                    <g class="exp-dots-e">
                                                                        <circle class="exp-dot-e" cx="12" cy="94" r="2.1"/><circle class="exp-dot-e" cx="56" cy="90" r="2.1"/><circle class="exp-dot-e" cx="100" cy="92" r="2.1"/><circle class="exp-dot-e" cx="144" cy="84" r="2.1"/><circle class="exp-dot-e" cx="188" cy="86" r="2.1"/><circle class="exp-dot-e" cx="228" cy="78" r="2.1"/>
                                                                    </g>
                                                                    <g class="exp-dots-r">
                                                                        <circle class="exp-dot-r" cx="12" cy="80" r="2.3"/><circle class="exp-dot-r" cx="56" cy="68" r="2.3"/><circle class="exp-dot-r" cx="100" cy="72" r="2.3"/><circle class="exp-dot-r" cx="144" cy="52" r="2.3"/><circle class="exp-dot-r" cx="188" cy="46" r="2.3"/>
                                                                    </g>
                                                                    <circle class="exp-pulse" cx="228" cy="28" r="3.6" fill="#10b981"/>
                                                                    <circle cx="228" cy="28" r="3.6" fill="#10b981" stroke="#fff" stroke-width="1.5"/>
                                                                </svg>
                                                                <div class="exp-axis"><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span></div>
                                                            </div>

                                                            <div class="exp-chart exp-chart-bars" id="expChartBars">
                                                                <div class="exp-chart-head">
                                                                    <span>Cash flow</span>
                                                                    <span class="exp-net">+$<span class="exp-net-val">0</span></span>
                                                                </div>
                                                                <div class="exp-bars">
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:70%"></span><span class="exp-bar exp-bar-exp" style="--h:44%"></span></div>
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:56%"></span><span class="exp-bar exp-bar-exp" style="--h:38%"></span></div>
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:66%"></span><span class="exp-bar exp-bar-exp" style="--h:50%"></span></div>
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:84%"></span><span class="exp-bar exp-bar-exp" style="--h:46%"></span></div>
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:78%"></span><span class="exp-bar exp-bar-exp" style="--h:54%"></span></div>
                                                                    <div class="exp-bar-group"><span class="exp-bar exp-bar-rev" style="--h:96%"></span><span class="exp-bar exp-bar-exp" style="--h:48%"></span></div>
                                                                </div>
                                                                <div class="exp-axis exp-axis-bars"><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Management -->
                    <div class="tab-content" id="tab-customers">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Customers</div>
                                                    <div class="cust-stage" id="customerStage">
                                                        <div class="cust-inner">
                                                            <div class="cust-table">
                                                                <div class="cust-head"><span>Customer</span><span>Company</span><span class="cust-spent">Total spent</span><span class="cust-status-h">Status</span></div>
                                                                <div class="cust-row selected"><span class="cust-who"><span class="cust-av av-1">JD</span><span class="cust-name">Jane Doe</span></span><span class="cust-co">Acme Inc</span><span class="cust-spent">$4,230</span><span class="cust-status"><span class="badge-active">Active</span></span></div>
                                                                <div class="cust-row"><span class="cust-who"><span class="cust-av av-2">MS</span><span class="cust-name">Mike Smith</span></span><span class="cust-co">Store Co</span><span class="cust-spent">$2,890</span><span class="cust-status"><span class="badge-active">Active</span></span></div>
                                                                <div class="cust-row"><span class="cust-who"><span class="cust-av av-3">AL</span><span class="cust-name">Ana Lopez</span></span><span class="cust-co">Lopez Studio</span><span class="cust-spent">$6,100</span><span class="cust-status"><span class="badge-vip">VIP</span></span></div>
                                                                <div class="cust-row"><span class="cust-who"><span class="cust-av av-4">RK</span><span class="cust-name">Raj Kumar</span></span><span class="cust-co">Kumar &amp; Sons</span><span class="cust-spent">$1,450</span><span class="cust-status"><span class="badge-active">Active</span></span></div>
                                                                <div class="cust-row"><span class="cust-who"><span class="cust-av av-2">EW</span><span class="cust-name">Emma Wong</span></span><span class="cust-co">Wong Design</span><span class="cust-spent">$3,720</span><span class="cust-status"><span class="badge-active">Active</span></span></div>
                                                                <div class="cust-row"><span class="cust-who"><span class="cust-av av-3">TB</span><span class="cust-name">Tom Brown</span></span><span class="cust-co">Brown LLC</span><span class="cust-spent">$980</span><span class="cust-status"><span class="badge-new">New</span></span></div>
                                                            </div>

                                                            <div class="cust-profile" id="custProfile">
                                                                <div class="cust-profile-head">
                                                                    <span class="cust-av-lg av-1">JD</span>
                                                                    <div class="cust-profile-id">
                                                                        <span class="cust-profile-name">Jane Doe</span>
                                                                        <span class="cust-profile-email">jane@acme.com</span>
                                                                    </div>
                                                                </div>
                                                                <div class="cust-profile-stats">
                                                                    <div class="cps"><span class="cps-val">$<span class="cps-ltv">0</span></span><span class="cps-lbl">Lifetime</span></div>
                                                                    <div class="cps"><span class="cps-val">12</span><span class="cps-lbl">Orders</span></div>
                                                                    <div class="cps"><span class="cps-val">2024</span><span class="cps-lbl">Since</span></div>
                                                                </div>
                                                                <span class="cust-spark-lbl">Purchases</span>
                                                                <svg class="cust-spark-svg" viewBox="0 0 220 58" preserveAspectRatio="none" aria-hidden="true">
                                                                    <defs>
                                                                        <linearGradient id="custSpark" x1="0" y1="0" x2="0" y2="1">
                                                                            <stop offset="0" stop-color="#3f63e8" stop-opacity="0.30"/>
                                                                            <stop offset="1" stop-color="#3f63e8" stop-opacity="0"/>
                                                                        </linearGradient>
                                                                    </defs>
                                                                    <path d="M6 44 C24 42 38 36 60 38 C84 40 100 26 130 28 C160 30 184 18 214 10 L214 54 L6 54 Z" fill="url(#custSpark)"/>
                                                                    <path class="cust-spark-line" d="M6 44 C24 42 38 36 60 38 C84 40 100 26 130 28 C160 30 184 18 214 10"/>
                                                                    <circle class="exp-pulse" cx="214" cy="10" r="3.4" fill="#3f63e8"/>
                                                                    <circle cx="214" cy="10" r="3.4" fill="#3f63e8" stroke="#fff" stroke-width="1.4"/>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Predictive Analytics -->
                    <div class="tab-content" id="tab-predictive">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Forecast</div>
                                                    <div class="fc-stage" id="forecastStage">
                                                        <div class="fc-kpis">
                                                            <div class="fc-kpi"><span class="fc-kpi-val">$<span class="fc-next">0</span></span><span class="fc-kpi-lbl">Next month</span></div>
                                                            <div class="fc-kpi"><span class="fc-kpi-val fc-up">+18%</span><span class="fc-kpi-lbl">Projected growth</span></div>
                                                            <div class="fc-kpi"><span class="fc-kpi-val"><span class="fc-conf">0</span>%</span><span class="fc-kpi-lbl">Confidence</span></div>
                                                        </div>
                                                        <div class="fc-chart">
                                                            <div class="fc-chart-head">
                                                                <span>Sales forecast</span>
                                                                <span class="fc-legend"><i class="fc-lg-line"></i>History<i class="fc-lg-dash"></i>Forecast</span>
                                                            </div>
                                                            <svg class="fc-svg" viewBox="0 0 320 150" preserveAspectRatio="none" aria-hidden="true">
                                                                <defs>
                                                                    <linearGradient id="fcArea" x1="0" y1="0" x2="0" y2="1">
                                                                        <stop offset="0" stop-color="#3f63e8" stop-opacity="0.28"/>
                                                                        <stop offset="1" stop-color="#3f63e8" stop-opacity="0"/>
                                                                    </linearGradient>
                                                                    <linearGradient id="fcCone" x1="0" y1="0" x2="0" y2="1">
                                                                        <stop offset="0" stop-color="#3f63e8" stop-opacity="0.18"/>
                                                                        <stop offset="1" stop-color="#3f63e8" stop-opacity="0.03"/>
                                                                    </linearGradient>
                                                                </defs>
                                                                <g class="fc-grid">
                                                                    <line x1="10" y1="34" x2="310" y2="34"/>
                                                                    <line x1="10" y1="70" x2="310" y2="70"/>
                                                                    <line x1="10" y1="106" x2="310" y2="106"/>
                                                                </g>
                                                                <g class="fc-history">
                                                                    <path d="M10 120 C40 122 60 96 90 92 C120 88 140 70 170 64 C185 61 195 56 200 54 L200 138 L10 138 Z" fill="url(#fcArea)"/>
                                                                    <path class="fc-hline" d="M10 120 C40 122 60 96 90 92 C120 88 140 70 170 64 C185 61 195 56 200 54"/>
                                                                </g>
                                                                <g class="fc-forecast">
                                                                    <path d="M200 54 C225 44 245 32 270 26 C290 23 300 14 310 10 L310 40 C300 36 290 40 270 44 C245 49 225 51 200 54 Z" fill="url(#fcCone)"/>
                                                                    <path class="fc-fline" d="M200 54 C225 48 245 40 270 36 C290 33 300 26 310 22"/>
                                                                    <line class="fc-now" x1="200" y1="20" x2="200" y2="138"/>
                                                                    <circle cx="200" cy="54" r="4.2" fill="#3f63e8" stroke="#fff" stroke-width="1.6"/>
                                                                    <circle class="exp-pulse" cx="200" cy="54" r="4.2" fill="#3f63e8"/>
                                                                </g>
                                                            </svg>
                                                            <div class="fc-axis"><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span class="fc-fut">May</span><span class="fc-fut">Jun</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Inventory Management -->
                    <div class="tab-content" id="tab-inventory">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Inventory</div>
                                                    <div class="inv-stage" id="inventoryStage">
                                                        <div class="inv-grid">
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-1"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Widget Pro</span><span class="inv-sku">WGT-01</span></span>
                                                                    <span class="inv-badge badge-ok">In stock</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="142">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-ok" style="--fill: 88%"></span></div>
                                                            </div>
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-2"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Cable Set</span><span class="inv-sku">CBL-04</span></span>
                                                                    <span class="inv-badge badge-ok">In stock</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="96">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-ok" style="--fill: 64%"></span></div>
                                                            </div>
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-3"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Basic Kit</span><span class="inv-sku">KIT-09</span></span>
                                                                    <span class="inv-badge badge-low">Low</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="34">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-low" style="--fill: 34%"></span></div>
                                                            </div>
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-4"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Power Adapter</span><span class="inv-sku">PWR-11</span></span>
                                                                    <span class="inv-badge badge-low">Low</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="21">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-low" style="--fill: 24%"></span></div>
                                                            </div>
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-5"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Deluxe Bundle</span><span class="inv-sku">DLX-22</span></span>
                                                                    <span class="inv-badge badge-crit">Critical</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="8">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-crit" style="--fill: 10%"></span></div>
                                                            </div>
                                                            <div class="inv-card">
                                                                <div class="inv-card-top">
                                                                    <span class="inv-chip chip-6"><?= svg_icon('package', 18) ?></span>
                                                                    <span class="inv-meta"><span class="inv-name">Starter Pack</span><span class="inv-sku">STR-07</span></span>
                                                                    <span class="inv-badge badge-ok">In stock</span>
                                                                </div>
                                                                <div class="inv-count"><span class="inv-num" data-target="73">0</span><span class="inv-unit">units</span></div>
                                                                <div class="inv-bar"><span class="inv-fill fill-ok" style="--fill: 52%"></span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Management -->
                    <div class="tab-content" id="tab-rental">
                        <div class="tab-content-inner tab-content-inner--solo">
                            <div class="tab-content-visual">
                                <div class="feature-visual-card invoice-studio-card">
                                    <div class="invoice-studio">
                                        <div class="invoice-window">
                                            <div class="app-topbar">
                                                <span class="app-brand"><img src="resources/images/argo-logo/argo-books-icon-transparent.png" alt="" class="app-brand-img">Argo Books</span>
                                            </div>
                                            <div class="app-body">
                                                <div class="app-nav" aria-hidden="true">
                                                    <span class="app-nav-btn"><?= svg_icon('grid', 18) ?></span>
                                                    <span class="app-nav-btn app-nav-btn--active"><?= svg_icon('calendar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('trending-up', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('document', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('receipt', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('dollar', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('credit-card', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('users', 18) ?></span>
                                                    <span class="app-nav-btn"><?= svg_icon('package', 18) ?></span>
                                                </div>
                                                <div class="app-content">
                                                    <div class="app-page-title">Rentals</div>
                                                    <div class="rent-stage" id="rentalStage">
                                                        <div class="rent-inner">
                                                            <div class="rent-cal">
                                                                <div class="rent-cal-head">
                                                                    <span class="rent-month">December 2025</span>
                                                                    <span class="rent-nav"><i><?= svg_icon('chevron-left-sm', 13) ?></i><i><?= svg_icon('chevron-right-sm', 13) ?></i></span>
                                                                </div>
                                                                <div class="rent-grid">
                                                                    <span class="rent-wd">Su</span><span class="rent-wd">Mo</span><span class="rent-wd">Tu</span><span class="rent-wd">We</span><span class="rent-wd">Th</span><span class="rent-wd">Fr</span><span class="rent-wd">Sa</span>
                                                                    <span class="rent-day rent-empty"></span><span class="rent-day">1</span><span class="rent-day">2</span><span class="rent-day">3</span><span class="rent-day">4</span><span class="rent-day">5</span><span class="rent-day">6</span>
                                                                    <span class="rent-day">7</span><span class="rent-day">8</span><span class="rent-day rent-seq">9</span><span class="rent-day rent-seq">10</span><span class="rent-day rent-seq">11</span><span class="rent-day rent-seq">12</span><span class="rent-day">13</span>
                                                                    <span class="rent-day">14</span><span class="rent-day">15</span><span class="rent-day booked">16</span><span class="rent-day booked">17</span><span class="rent-day">18</span><span class="rent-day">19</span><span class="rent-day">20</span>
                                                                    <span class="rent-day">21</span><span class="rent-day">22</span><span class="rent-day booked">23</span><span class="rent-day booked">24</span><span class="rent-day">25</span><span class="rent-day">26</span><span class="rent-day">27</span>
                                                                    <span class="rent-day">28</span><span class="rent-day">29</span><span class="rent-day">30</span><span class="rent-day">31</span><span class="rent-day rent-empty"></span><span class="rent-day rent-empty"></span><span class="rent-day rent-empty"></span>
                                                                </div>
                                                            </div>

                                                            <div class="rent-booking" id="rentBooking">
                                                                <div class="rent-bk-head">
                                                                    <span class="rent-bk-icon"><?= svg_icon('package', 16) ?></span>
                                                                    <span class="rent-bk-id"><span class="rent-bk-item">HD Projector</span><span class="rent-bk-cust">Sarah Miller</span></span>
                                                                    <span class="rent-bk-badge">Out</span>
                                                                </div>
                                                                <div class="rent-bk-row"><span class="rent-bk-lbl">Rental</span><span class="rent-bk-val">Dec 9 &ndash; Dec 12</span></div>
                                                                <div class="rent-bk-row"><span class="rent-bk-lbl">Returns</span><span class="rent-bk-val rent-bk-ret">in 3 days</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Import / Switching Section -->
    <section class="ai-import-section">
        <div class="container">
            <div class="ai-import-header animate-on-scroll">
                <h2 class="section-title">Switch in minutes, not weekends</h2>
                <p class="section-description">Bring your history with you. Drop in a spreadsheet or a bank statement and Argo Books turns it into clean, organized books. No reformatting, no manual mapping, no re-typing.</p>
            </div>

            <div class="imp-demo animate-on-scroll" id="aiImportDemo">
                <div class="imp-sheet">
                    <div class="imp-sheet-head">
                        <span class="imp-file"><?= svg_icon('table', 16) ?> bill_export.xlsx</span>
                        <span class="imp-pill" id="impPill"><?= svg_icon('check', 12) ?> Auto-mapped</span>
                    </div>
                    <div class="imp-table">
                        <div class="imp-row imp-row-head">
                            <div class="imp-col" data-c="0"><span class="imp-raw">Supplier</span><span class="imp-clean">Supplier Name<i class="imp-tag">Text</i></span></div>
                            <div class="imp-col" data-c="1"><span class="imp-raw">Amt Owed</span><span class="imp-clean">Balance Due<i class="imp-tag">Currency</i></span></div>
                            <div class="imp-col" data-c="2"><span class="imp-raw">Pay By</span><span class="imp-clean">Due Date<i class="imp-tag">Date</i></span></div>
                            <div class="imp-col" data-c="3"><span class="imp-raw">Memo</span><span class="imp-clean">Reference #<i class="imp-tag">ID</i></span></div>
                        </div>
                        <div class="imp-row">
                            <span>Acme Supply Co</span>
                            <span class="imp-cell" data-c="1"><span class="imp-raw">4280</span><span class="imp-clean">$4,280.00</span></span>
                            <span class="imp-cell" data-c="2"><span class="imp-raw">12/15/2025</span><span class="imp-clean">Dec 15, 2025</span></span>
                            <span>INV-3847</span>
                        </div>
                        <div class="imp-row">
                            <span>TechFlow LLC</span>
                            <span class="imp-cell" data-c="1"><span class="imp-raw">950.5</span><span class="imp-clean">$950.50</span></span>
                            <span class="imp-cell" data-c="2"><span class="imp-raw">01/02/2026</span><span class="imp-clean">Jan 2, 2026</span></span>
                            <span>PO-9912</span>
                        </div>
                        <div class="imp-row imp-row-faded">
                            <span>NovaCorp Int'l</span>
                            <span class="imp-cell" data-c="1"><span class="imp-raw">12100</span><span class="imp-clean">$12,100.00</span></span>
                            <span class="imp-cell" data-c="2"><span class="imp-raw">11/30/2025</span><span class="imp-clean">Nov 30, 2025</span></span>
                            <span>Contract #441</span>
                        </div>
                    </div>
                    <div class="imp-foot">
                        <span class="imp-rows">+384 more rows</span>
                        <span class="imp-done" id="impDone"><?= svg_icon('check', 14) ?> Ready to import 387 rows</span>
                    </div>
                </div>
            </div>

            <!-- Capability strip -->
            <div class="ai-import-points animate-on-scroll">
                <div class="ai-point">
                    <strong>Bring your whole history</strong>
                    <span>Import months of spreadsheets or bank statements at once. CSV, Excel, or PDF.</span>
                </div>
                <div class="ai-point">
                    <strong>Categorized for you</strong>
                    <span>Statement lines become sorted expenses and revenue, and it learns your preferences as you go.</span>
                </div>
                <div class="ai-point">
                    <strong>No bank login required</strong>
                    <span>Just the file you download from your bank. Encrypted on your device, never stored or used for training.</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tax-Ready / Reports Section -->
    <section class="tax-ready-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">Stay tax-ready with clean books and instant reports</h2>
                <p class="section-description">Every transaction is categorized and matched to its receipt as you go. Generate any report you need in seconds. No spreadsheets, no scrambling at year-end.</p>
            </div>

            <div class="tax-ready-grid">
                <!-- Card 1: Reports -->
                <div class="tax-ready-card animate-on-scroll">
                    <div class="tax-ready-visual">
                        <svg viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <!-- Back doc, tilted left: Cash Flow -->
                            <g transform="translate(28 32) rotate(-8 75 95)">
                                <rect width="150" height="190" rx="10" fill="#ffffff" stroke="#e2e8f0"/>
                                <rect width="150" height="30" rx="10" fill="#eff6ff"/>
                                <rect y="20" width="150" height="10" fill="#eff6ff"/>
                                <text x="12" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#1d4ed8" letter-spacing="0.3">CASH FLOW</text>
                                <text x="100" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#60a5fa">Q4 2025</text>
                                <text x="12" y="48" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8" letter-spacing="0.3">INFLOW</text>
                                <text x="12" y="62" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#15803d">$84.2k</text>
                                <text x="80" y="48" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8" letter-spacing="0.3">OUTFLOW</text>
                                <text x="80" y="62" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#dc2626">$36.0k</text>
                                <line x1="12" y1="80" x2="138" y2="80" stroke="#f1f5f9"/>
                                <line x1="12" y1="100" x2="138" y2="100" stroke="#f1f5f9"/>
                                <line x1="12" y1="120" x2="138" y2="120" stroke="#f1f5f9"/>
                                <line x1="12" y1="140" x2="138" y2="140" stroke="#f1f5f9"/>
                                <polyline points="14,128 36,118 58,122 80,104 102,108 124,90 138,96" stroke="#22c55e" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="14" cy="128" r="1.5" fill="#22c55e"/>
                                <circle cx="58" cy="122" r="1.5" fill="#22c55e"/>
                                <circle cx="102" cy="108" r="1.5" fill="#22c55e"/>
                                <circle cx="138" cy="96" r="1.5" fill="#22c55e"/>
                                <polyline points="14,140 36,134 58,136 80,130 102,132 124,128 138,130" stroke="#fca5a5" stroke-width="1.4" fill="none" stroke-dasharray="2 2"/>
                                <text x="14" y="154" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Oct</text>
                                <text x="80" y="154" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Nov</text>
                                <text x="124" y="154" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Dec</text>
                                <line x1="12" y1="166" x2="138" y2="166" stroke="#e2e8f0"/>
                                <text x="12" y="178" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Net change</text>
                                <text x="138" y="178" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#15803d" text-anchor="end">+$48.2k</text>
                            </g>
                            <!-- Back doc, tilted right: Balance Sheet -->
                            <g transform="translate(146 32) rotate(8 75 95)">
                                <rect width="150" height="190" rx="10" fill="#ffffff" stroke="#e2e8f0"/>
                                <rect width="150" height="30" rx="10" fill="#faf5ff"/>
                                <rect y="20" width="150" height="10" fill="#faf5ff"/>
                                <text x="12" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#7c3aed" letter-spacing="0.3">BALANCE SHEET</text>
                                <text x="108" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#a78bfa">2025</text>
                                <text x="12" y="46" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8" letter-spacing="0.3">ASSETS</text>
                                <text x="138" y="46" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8" letter-spacing="0.3" text-anchor="end">LIABILITIES</text>
                                <g font-family="-apple-system,system-ui,sans-serif" font-size="7">
                                    <text x="12" y="62" fill="#334155">Cash</text>
                                    <text x="76" y="62" text-anchor="end" font-weight="600" fill="#0f172a">$24,800</text>
                                    <text x="82" y="62" fill="#334155">Loans</text>
                                    <text x="138" y="62" text-anchor="end" font-weight="600" fill="#0f172a">$8,400</text>
                                    <text x="12" y="78" fill="#334155">Receivable</text>
                                    <text x="76" y="78" text-anchor="end" font-weight="600" fill="#0f172a">$12,300</text>
                                    <text x="82" y="78" fill="#334155">Cards</text>
                                    <text x="138" y="78" text-anchor="end" font-weight="600" fill="#0f172a">$3,210</text>
                                    <text x="12" y="94" fill="#334155">Inventory</text>
                                    <text x="76" y="94" text-anchor="end" font-weight="600" fill="#0f172a">$18,500</text>
                                    <text x="82" y="94" fill="#334155">Tax owed</text>
                                    <text x="138" y="94" text-anchor="end" font-weight="600" fill="#0f172a">$4,120</text>
                                    <text x="12" y="110" fill="#334155">Equipment</text>
                                    <text x="76" y="110" text-anchor="end" font-weight="600" fill="#0f172a">$9,200</text>
                                    <text x="82" y="110" fill="#334155">Vendors</text>
                                    <text x="138" y="110" text-anchor="end" font-weight="600" fill="#0f172a">$2,800</text>
                                </g>
                                <line x1="12" y1="120" x2="138" y2="120" stroke="#e2e8f0"/>
                                <text x="12" y="134" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">TOTAL</text>
                                <text x="76" y="134" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#7c3aed" text-anchor="end">$64,800</text>
                                <text x="138" y="134" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#7c3aed" text-anchor="end">$18,530</text>
                                <text x="12" y="152" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8" letter-spacing="0.3">EQUITY RATIO</text>
                                <text x="138" y="152" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#7c3aed" text-anchor="end">71%</text>
                                <rect x="12" y="158" width="126" height="7" rx="3.5" fill="#f3e8ff"/>
                                <rect x="12" y="158" width="89" height="7" rx="3.5" fill="#a78bfa"/>
                                <text x="12" y="178" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8">Healthy &gt; 60%</text>
                            </g>
                            <!-- Front doc: Income Statement -->
                            <g transform="translate(85 18)">
                                <rect width="150" height="200" rx="12" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.2"/>
                                <rect width="150" height="32" rx="12" fill="#eff6ff"/>
                                <rect y="22" width="150" height="10" fill="#eff6ff"/>
                                <text x="12" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#1d4ed8" letter-spacing="0.3">INCOME STATEMENT</text>
                                <text x="106" y="20" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#60a5fa">Q4 2025</text>
                                <text x="14" y="52" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#94a3b8">Net income</text>
                                <text x="14" y="76" font-family="-apple-system,system-ui,sans-serif" font-size="20" font-weight="700" fill="#0f172a">$48,210</text>
                                <rect x="100" y="62" width="38" height="16" rx="8" fill="#dbeafe"/>
                                <path d="M 106 73 l 3 -4 l 3 4 z" fill="#1d4ed8"/>
                                <text x="115" y="73" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#1d4ed8">+18%</text>
                                <line x1="14" y1="88" x2="136" y2="88" stroke="#e2e8f0"/>
                                <rect x="16" y="118" width="14" height="34" rx="2" fill="#bfdbfe"/>
                                <rect x="36" y="110" width="14" height="42" rx="2" fill="#93c5fd"/>
                                <rect x="56" y="100" width="14" height="52" rx="2" fill="#60a5fa"/>
                                <rect x="76" y="90" width="14" height="62" rx="2" fill="#3b82f6"/>
                                <rect x="96" y="80" width="14" height="72" rx="2" fill="#2563eb"/>
                                <rect x="116" y="86" width="14" height="66" rx="2" fill="#1d4ed8"/>
                                <line x1="14" y1="152" x2="136" y2="152" stroke="#e2e8f0"/>
                                <text x="22" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">F</text>
                                <text x="42" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">M</text>
                                <text x="62" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">A</text>
                                <text x="82" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">M</text>
                                <text x="102" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">J</text>
                                <text x="122" y="160" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="middle">J</text>
                                <text x="14" y="174" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Revenue</text>
                                <text x="136" y="174" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#0f172a" text-anchor="end">$128.4k</text>
                                <text x="14" y="186" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Expenses</text>
                                <text x="136" y="186" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#0f172a" text-anchor="end">$80.2k</text>
                            </g>
                        </svg>
                    </div>
                    <h3>Automatic tax reports</h3>
                    <p>Every statement and tax summary, generated in real time.</p>
                </div>

                <!-- Card 2: Dashboard -->
                <div class="tax-ready-card animate-on-scroll">
                    <div class="tax-ready-visual">
                        <svg viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="dashAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.45"/>
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                </linearGradient>
                                <linearGradient id="dashAreaGrad2" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#a78bfa" stop-opacity="0.28"/>
                                    <stop offset="100%" stop-color="#a78bfa" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <!-- Window -->
                            <rect x="12" y="14" width="296" height="212" rx="14" fill="#ffffff" stroke="#e2e8f0"/>
                            <!-- Title bar -->
                            <rect x="12" y="14" width="296" height="28" rx="14" fill="#f8fafc"/>
                            <rect x="12" y="30" width="296" height="12" fill="#f8fafc"/>
                            <line x1="12" y1="42" x2="308" y2="42" stroke="#e2e8f0"/>
                            <circle cx="26" cy="28" r="3" fill="#cbd5e1"/>
                            <circle cx="36" cy="28" r="3" fill="#cbd5e1"/>
                            <circle cx="46" cy="28" r="3" fill="#cbd5e1"/>
                            <text x="160" y="32" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="600" fill="#64748b" text-anchor="middle">Argo Books: Dashboard</text>
                            <!-- Sidebar -->
                            <rect x="12" y="42" width="42" height="184" fill="#fbfbfd"/>
                            <line x1="54" y1="42" x2="54" y2="226" stroke="#f1f5f9"/>
                            <!-- Sidebar nav: active -->
                            <rect x="22" y="56" width="22" height="22" rx="6" fill="#eff6ff"/>
                            <rect x="28" y="64" width="10" height="2" rx="1" fill="#1d4ed8"/>
                            <rect x="28" y="68" width="10" height="2" rx="1" fill="#1d4ed8"/>
                            <rect x="28" y="72" width="6" height="2" rx="1" fill="#1d4ed8"/>
                            <!-- Sidebar nav: chart -->
                            <rect x="27" y="98" width="3" height="6" rx="1" fill="#94a3b8"/>
                            <rect x="32" y="94" width="3" height="10" rx="1" fill="#94a3b8"/>
                            <rect x="37" y="90" width="3" height="14" rx="1" fill="#94a3b8"/>
                            <!-- Sidebar nav: clock -->
                            <circle cx="33" cy="123" r="6" stroke="#94a3b8" stroke-width="1.4" fill="none"/>
                            <line x1="33" y1="119" x2="33" y2="123" stroke="#94a3b8" stroke-width="1.4"/>
                            <line x1="33" y1="123" x2="36" y2="125" stroke="#94a3b8" stroke-width="1.4"/>
                            <!-- Sidebar nav: trending -->
                            <path d="M 27 154 l 6 -6 l 4 4 l 5 -6" stroke="#94a3b8" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- Sidebar nav: profile -->
                            <circle cx="33" cy="178" r="3" fill="#94a3b8"/>
                            <path d="M 27 188 q 6 -7 12 0" stroke="#94a3b8" stroke-width="1.4" fill="none"/>
                            <!-- Header -->
                            <text x="64" y="62" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#0f172a">Welcome back</text>
                            <text x="64" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" fill="#94a3b8">April 2026, Q2 update</text>
                            <rect x="252" y="60" width="46" height="14" rx="7" fill="#f1f5f9"/>
                            <text x="275" y="70" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="600" fill="#475569" text-anchor="middle">Apr 2026</text>
                            <!-- KPI tile: revenue -->
                            <rect x="64" y="90" width="74" height="34" rx="8" fill="#eff6ff" stroke="#dbeafe"/>
                            <text x="71" y="102" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#60a5fa" letter-spacing="0.3">REVENUE</text>
                            <text x="71" y="117" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#1d4ed8">$12,840</text>
                            <!-- KPI tile: expenses -->
                            <rect x="146" y="90" width="74" height="34" rx="8" fill="#fef2f2" stroke="#fee2e2"/>
                            <text x="153" y="102" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#fca5a5" letter-spacing="0.3">EXPENSES</text>
                            <text x="153" y="117" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#b91c1c">$4,120</text>
                            <!-- KPI tile: profit -->
                            <rect x="228" y="90" width="74" height="34" rx="8" fill="#f0fdf4" stroke="#dcfce7"/>
                            <text x="235" y="102" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#4ade80" letter-spacing="0.3">PROFIT</text>
                            <text x="235" y="117" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#15803d">$8,720</text>
                            <!-- Main chart panel -->
                            <rect x="64" y="130" width="238" height="90" rx="9" fill="#fcfcfd" stroke="#f1f5f9"/>
                            <text x="74" y="144" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">Revenue this month</text>
                            <circle cx="252" cy="142" r="2.5" fill="#3b82f6"/>
                            <text x="258" y="144" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#64748b">2026</text>
                            <circle cx="278" cy="142" r="2.5" fill="#a78bfa"/>
                            <text x="284" y="144" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#64748b">2025</text>
                            <line x1="80" y1="174" x2="296" y2="174" stroke="#f1f5f9"/>
                            <line x1="80" y1="190" x2="296" y2="190" stroke="#f1f5f9"/>
                            <line x1="80" y1="206" x2="296" y2="206" stroke="#f1f5f9"/>
                            <text x="76" y="176" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="end">15k</text>
                            <text x="76" y="192" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="end">10k</text>
                            <text x="76" y="208" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8" text-anchor="end">5k</text>
                            <!-- Previous year line -->
                            <path d="M 84 200 L 102 196 L 120 198 L 138 192 L 156 194 L 174 188 L 192 190 L 210 184 L 228 186 L 246 180 L 264 182 L 282 178 L 296 180 L 296 210 L 84 210 Z" fill="url(#dashAreaGrad2)"/>
                            <polyline points="84,200 102,196 120,198 138,192 156,194 174,188 192,190 210,184 228,186 246,180 264,182 282,178 296,180" stroke="#a78bfa" stroke-width="1.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- Current year line -->
                            <path d="M 84 198 L 102 192 L 120 194 L 138 184 L 156 186 L 174 174 L 192 178 L 210 164 L 228 168 L 246 156 L 264 160 L 282 152 L 296 156 L 296 210 L 84 210 Z" fill="url(#dashAreaGrad)"/>
                            <polyline points="84,198 102,192 120,194 138,184 156,186 174,174 192,178 210,164 228,168 246,156 264,160 282,152 296,156" stroke="#3b82f6" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            <!-- X axis -->
                            <text x="84" y="220" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Mon</text>
                            <text x="138" y="220" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Wed</text>
                            <text x="192" y="220" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Fri</text>
                            <text x="246" y="220" font-family="-apple-system,system-ui,sans-serif" font-size="6" fill="#94a3b8">Sun</text>
                        </svg>
                    </div>
                    <h3>A dashboard worth checking</h3>
                    <p>Revenue, expenses, and profit at a glance.</p>
                </div>

                <!-- Card 3: Audit-ready -->
                <div class="tax-ready-card animate-on-scroll">
                    <div class="tax-ready-visual">
                        <svg viewBox="0 0 320 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <!-- Container -->
                            <rect x="14" y="14" width="292" height="212" rx="12" fill="#ffffff" stroke="#e2e8f0"/>
                            <!-- Header -->
                            <text x="28" y="38" font-family="-apple-system,system-ui,sans-serif" font-size="11" font-weight="700" fill="#0f172a">Transactions</text>
                            <text x="28" y="52" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#94a3b8">April 2026 · 247 entries</text>
                            <rect x="218" y="26" width="76" height="20" rx="10" fill="#dcfce7"/>
                            <path d="M 224 36 l 3 3 l 6 -6" stroke="#15803d" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <text x="242" y="39" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#15803d">All matched</text>
                            <!-- Column headers -->
                            <text x="28" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" font-weight="700" fill="#94a3b8" letter-spacing="0.3">DATE</text>
                            <text x="74" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" font-weight="700" fill="#94a3b8" letter-spacing="0.3">DESCRIPTION</text>
                            <text x="160" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" font-weight="700" fill="#94a3b8" letter-spacing="0.3">CATEGORY</text>
                            <text x="288" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" font-weight="700" fill="#94a3b8" letter-spacing="0.3" text-anchor="end">AMOUNT</text>
                            <line x1="28" y1="80" x2="292" y2="80" stroke="#e2e8f0"/>
                            <!-- Row 1: Office -->
                            <g transform="translate(0 92)">
                                <text x="28" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Apr 12</text>
                                <path d="M 60 -8 L 67 -8 L 70 -5 L 70 4 L 60 4 Z" fill="#ffffff" stroke="#cbd5e1"/>
                                <path d="M 67 -8 L 67 -5 L 70 -5 Z" fill="#cbd5e1"/>
                                <line x1="62" y1="-2" x2="68" y2="-2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="0" x2="68" y2="0" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="2" x2="65" y2="2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <text x="74" y="-1" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">Bright Office Co.</text>
                                <text x="74" y="9" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8">Card •• 4242</text>
                                <rect x="160" y="-8" width="40" height="14" rx="7" fill="#eff6ff"/>
                                <circle cx="167" cy="-1" r="2" fill="#3b82f6"/>
                                <text x="172" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#1d4ed8">Office</text>
                                <text x="270" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#0f172a" text-anchor="end">$248.30</text>
                                <circle cx="284" cy="-1" r="6" fill="#f0fdf4"/>
                                <path d="M 281 -1 l 2 2 l 4 -4" stroke="#15803d" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </g>
                            <line x1="28" y1="106" x2="292" y2="106" stroke="#f1f5f9"/>
                            <!-- Row 2: Travel -->
                            <g transform="translate(0 122)">
                                <text x="28" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Apr 14</text>
                                <path d="M 60 -8 L 67 -8 L 70 -5 L 70 4 L 60 4 Z" fill="#ffffff" stroke="#cbd5e1"/>
                                <path d="M 67 -8 L 67 -5 L 70 -5 Z" fill="#cbd5e1"/>
                                <line x1="62" y1="-2" x2="68" y2="-2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="0" x2="68" y2="0" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="2" x2="65" y2="2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <text x="74" y="-1" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">Skyline Airways</text>
                                <text x="74" y="9" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8">YYZ → SFO</text>
                                <rect x="160" y="-8" width="40" height="14" rx="7" fill="#faf5ff"/>
                                <circle cx="167" cy="-1" r="2" fill="#7c3aed"/>
                                <text x="172" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#7c3aed">Travel</text>
                                <text x="270" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#0f172a" text-anchor="end">$1,420.00</text>
                                <circle cx="284" cy="-1" r="6" fill="#f0fdf4"/>
                                <path d="M 281 -1 l 2 2 l 4 -4" stroke="#15803d" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </g>
                            <line x1="28" y1="136" x2="292" y2="136" stroke="#f1f5f9"/>
                            <!-- Row 3: Meals -->
                            <g transform="translate(0 152)">
                                <text x="28" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Apr 18</text>
                                <path d="M 60 -8 L 67 -8 L 70 -5 L 70 4 L 60 4 Z" fill="#ffffff" stroke="#cbd5e1"/>
                                <path d="M 67 -8 L 67 -5 L 70 -5 Z" fill="#cbd5e1"/>
                                <line x1="62" y1="-2" x2="68" y2="-2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="0" x2="68" y2="0" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="2" x2="65" y2="2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <text x="74" y="-1" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">The Corner Café</text>
                                <text x="74" y="9" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8">Client meeting</text>
                                <rect x="160" y="-8" width="36" height="14" rx="7" fill="#fffbeb"/>
                                <circle cx="167" cy="-1" r="2" fill="#f59e0b"/>
                                <text x="172" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#b45309">Meals</text>
                                <text x="270" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#0f172a" text-anchor="end">$84.50</text>
                                <circle cx="284" cy="-1" r="6" fill="#f0fdf4"/>
                                <path d="M 281 -1 l 2 2 l 4 -4" stroke="#15803d" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </g>
                            <line x1="28" y1="166" x2="292" y2="166" stroke="#f1f5f9"/>
                            <!-- Row 4: Software -->
                            <g transform="translate(0 182)">
                                <text x="28" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#64748b">Apr 22</text>
                                <path d="M 60 -8 L 67 -8 L 70 -5 L 70 4 L 60 4 Z" fill="#ffffff" stroke="#cbd5e1"/>
                                <path d="M 67 -8 L 67 -5 L 70 -5 Z" fill="#cbd5e1"/>
                                <line x1="62" y1="-2" x2="68" y2="-2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="0" x2="68" y2="0" stroke="#cbd5e1" stroke-width="0.8"/>
                                <line x1="62" y1="2" x2="65" y2="2" stroke="#cbd5e1" stroke-width="0.8"/>
                                <text x="74" y="-1" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" font-weight="700" fill="#0f172a">PixelSuite Pro</text>
                                <text x="74" y="9" font-family="-apple-system,system-ui,sans-serif" font-size="6.5" fill="#94a3b8">Monthly · auto-pay</text>
                                <rect x="160" y="-8" width="50" height="14" rx="7" fill="#ecfeff"/>
                                <circle cx="167" cy="-1" r="2" fill="#06b6d4"/>
                                <text x="172" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="700" fill="#0369a1">Software</text>
                                <text x="270" y="2" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#0f172a" text-anchor="end">$54.99</text>
                                <circle cx="284" cy="-1" r="6" fill="#f0fdf4"/>
                                <path d="M 281 -1 l 2 2 l 4 -4" stroke="#15803d" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            </g>
                            <line x1="28" y1="200" x2="292" y2="200" stroke="#f1f5f9"/>
                            <!-- Footer -->
                            <text x="28" y="216" font-family="-apple-system,system-ui,sans-serif" font-size="7" fill="#94a3b8">Showing 4 of 247</text>
                            <text x="292" y="216" font-family="-apple-system,system-ui,sans-serif" font-size="7" font-weight="600" fill="#3b82f6" text-anchor="end">Next →</text>
                        </svg>
                    </div>
                    <h3>Know where every dollar goes</h3>
                    <p>Every receipt matched, every transaction categorized.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section class="security-section">
        <div class="container">
            <div class="security-layout">
                <div class="security-text animate-on-scroll">
                    <h2 class="section-title text-left">This is what your data looks like on disk</h2>
                    <p class="section-description text-left">Every transaction, invoice, and customer record is encrypted with AES-256-GCM before it's saved. Even if someone copied your files, they'd see nothing useful.</p>

                    <div class="security-stats">
                        <div class="security-stat">
                            <span class="security-stat-value">AES-256</span>
                            <span class="security-stat-label">Encryption standard</span>
                        </div>
                        <div class="security-stat">
                            <span class="security-stat-value">Local</span>
                            <span class="security-stat-label">Data never leaves your PC</span>
                        </div>
                        <div class="security-stat">
                            <span class="security-stat-value">Bio</span>
                            <span class="security-stat-label">Fingerprint &amp; face unlock</span>
                        </div>
                    </div>
                </div>

                <div class="security-terminal-wrap animate-on-scroll">
                    <div class="security-terminal" id="securityTerminal">
                        <div class="terminal-bar">
                            <div class="terminal-dots">
                                <span></span><span></span><span></span>
                            </div>
                            <span class="terminal-title">your_company.argo, encrypted view</span>
                        </div>
                        <div class="terminal-body">
                            <div class="terminal-section">
                                <div class="terminal-label">Your data</div>
                                <div class="terminal-plain" id="termPlain">
                                    <div class="terminal-row"><span class="t-key">supplier</span> <span class="t-val">"Acme Supply Co"</span></div>
                                    <div class="terminal-row"><span class="t-key">amount</span> <span class="t-val">"$4,280.00"</span></div>
                                    <div class="terminal-row"><span class="t-key">due_date</span> <span class="t-val">"2025-12-15"</span></div>
                                    <div class="terminal-row"><span class="t-key">reference</span> <span class="t-val">"INV-3847"</span></div>
                                </div>
                            </div>
                            <div class="terminal-encrypt-bar" id="termEncryptBar">
                                <div class="terminal-encrypt-icon"><?= svg_icon('lock', 14) ?></div>
                                <span id="termEncryptLabel">AES-256-GCM encrypting...</span>
                                <div class="terminal-encrypt-progress"><div class="terminal-encrypt-fill" id="termEncryptFill"></div></div>
                            </div>
                            <div class="terminal-section">
                                <div class="terminal-label">What's stored on disk</div>
                                <div class="terminal-cipher" id="termCipher">
                                    <div class="terminal-row cipher-row"><span class="t-cipher" data-plain="supplier  &quot;Acme Supply Co&quot;"></span></div>
                                    <div class="terminal-row cipher-row"><span class="t-cipher" data-plain="amount    &quot;$4,280.00&quot;"></span></div>
                                    <div class="terminal-row cipher-row"><span class="t-cipher" data-plain="due_date  &quot;2025-12-15&quot;"></span></div>
                                    <div class="terminal-row cipher-row"><span class="t-cipher" data-plain="reference &quot;INV-3847&quot;"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">Free to start. Upgrade when you need more.</h2>
                <p class="section-description">Start free, upgrade when you need more. No hidden fees, no surprises.</p>
            </div>

            <?php
            $pricingCardsOptions = [
                'free_cta_url'     => 'downloads',
                'premium_cta_url'  => 'pricing/premium/',
            ];
            include __DIR__ . '/partials/pricing-cards.php';
            ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-header animate-on-scroll">
                <h2>We're here to help</h2>
                <p>Have questions about Argo Books? Our team is ready to assist you.</p>
            </div>
            <div class="contact-grid">
                <div class="contact-card animate-on-scroll">
                    <div class="contact-icon">
                        <?= svg_icon('mail', null, '', 1.5) ?>
                    </div>
                    <h3>Email Support</h3>
                    <p>Get help with technical issues, account questions, or general inquiries.</p>
                    <a href="mailto:support@argorobots.com" class="contact-link">
                        support@argorobots.com
                        <?= svg_icon('arrow-right', 16) ?>
                    </a>
                </div>
                <div class="contact-card animate-on-scroll">
                    <div class="contact-icon feedback">
                        <?= svg_icon('chat', null, '', 1.5) ?>
                    </div>
                    <h3>Send Feedback</h3>
                    <p>Share your ideas, feature requests, or suggestions to help us improve.</p>
                    <a href="mailto:feedback@argorobots.com" class="contact-link">
                        feedback@argorobots.com
                        <?= svg_icon('arrow-right', 16) ?>
                    </a>
                </div>
                <div class="contact-card animate-on-scroll">
                    <div class="contact-icon community">
                        <?= svg_icon('users', null, '', 1.5) ?>
                    </div>
                    <h3>Community</h3>
                    <p>Join our community to connect with other users and share tips.</p>
                    <a href="community/" class="contact-link">
                        Visit Community
                        <?= svg_icon('arrow-right', 16) ?>
                    </a>
                </div>
                <div class="contact-card animate-on-scroll">
                    <div class="contact-icon docs">
                        <?= svg_icon('book', null, '', 1.5) ?>
                    </div>
                    <h3>Documentation</h3>
                    <p>Browse guides, tutorials, and references to get the most out of Argo Books.</p>
                    <a href="documentation/" class="contact-link">
                        View Docs
                        <?= svg_icon('arrow-right', 16) ?>
                    </a>
                </div>
            </div>
            <div class="contact-cta animate-on-scroll">
                <p>Want to reach out directly?</p>
                <a href="contact-us/" class="btn btn-primary">
                    <?= svg_icon('message-circle', 20) ?>
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- Featured On Section -->
    <section class="featured-on">
        <div class="container">
            <span class="featured-on-label">Featured on</span>
            <?php
            // Badge list rendered twice back-to-back so the marquee can loop seamlessly
            $featured_badges = [
                ['href' => 'https://topfreeaitools.com', 'img' => 'topfreeaitools.png', 'w' => 230, 'alt' => 'Featured on Top Free AI Tools'],
                ['href' => 'https://startupfa.me/s/argo-books', 'img' => 'startupfame.webp', 'w' => 171, 'alt' => 'Argo Books - Featured on Startup Fame'],
                ['href' => 'https://launch-list.org/product/argo-books', 'img' => 'launchlist.svg',     'w' => 165, 'alt' => 'Featured on Launch List'],
                ['href' => 'https://twelve.tools', 'img' => 'twelvetools.svg',    'w' => 200, 'alt' => 'Featured on Twelve Tools'],
                ['href' => 'https://wired.business', 'img' => 'wiredbusiness.svg',  'w' => 200, 'alt' => 'Featured on Wired Business'],
                ['href' => 'https://auraplusplus.com/projects/argo-books', 'img' => 'auraplusplus.svg',   'w' => 184, 'alt' => 'Featured on Aura++'],
                ['href' => 'https://submitmysaas.com/projects/argo-books', 'img' => 'submitmysaas-top1.png', 'w' => 237, 'alt' => 'SubmitMySaas Top 1 Daily Winner'],
                ['href' => 'https://www.productlaunchify.com/projects/argo-books', 'img' => 'productlaunchify.svg', 'w' => 227, 'alt' => 'Featured on Product Launchify'],
                ['href' => 'https://www.scrolllaunch.com', 'img' => 'scrolllaunch.svg', 'w' => 248, 'alt' => 'Featured on ScrollLaunch'],
                ['href' => 'https://starterbest.com', 'img' => 'https://starterbest.com/badages-awards.svg', 'w' => 184, 'alt' => 'Featured on Starter Best'],
                ['href' => 'https://dayslaunch.com', 'img' => 'https://dayslaunch.com/badages-awards.svg', 'w' => 200, 'alt' => 'Featured on Days Launch'],
                ['href' => 'https://toolrain.com/item/argo-books', 'img' => 'toolrain.svg', 'w' => 184, 'alt' => 'Listed on ToolRain'],
                ['href' => 'https://saasfame.com/item/argo-books', 'img' => 'https://saasfame.com/badge-light.svg', 'w' => 170, 'alt' => 'Featured on SaaSFame'],
                ['href' => 'https://deeplaunch.io', 'img' => 'https://deeplaunch.io/badge/badge_light.svg', 'w' => 188, 'alt' => 'Featured on DeepLaunch.io'],
                ['href' => 'https://directoryhunt.org/', 'img' => 'https://directoryhunt.org/assets/Badges/featured.svg', 'w' => 199, 'alt' => 'Featured on DirectoryHunt.org'],
                ['href' => 'https://proofstories.io/directory/products/argo-books/', 'img' => 'https://proofstories.io/directory/badges/l/argo-books.svg', 'w' => 189, 'alt' => 'Listed on ProofStories'],
                ['href' => 'https://dofollow.tools', 'img' => 'https://dofollow.tools/badge/badge_light.svg', 'w' => 188, 'alt' => 'Featured on Dofollow.Tools'],
                ['href' => 'https://open-launch.com/projects/argo-books', 'img' => 'https://open-launch.com/api/badge/84f80c1b-1825-496d-b98b-67919dd95a77/featured-light.svg', 'w' => 216, 'alt' => 'Featured on Open-Launch'],
                ['href' => 'https://fazier.com/launches/argorobots.com', 'img' => 'https://fazier.com/api/v1//public/badges/launch_badges.svg?badge_type=featured&theme=light', 'w' => 229, 'alt' => 'Featured on Fazier'],
            ];
            ?>
            <div class="featured-on-marquee">
                <div class="featured-on-track">
                    <?php for ($pass = 0; $pass < 2; $pass++): ?>
                        <?php foreach ($featured_badges as $badge): ?>
                            <a href="<?= htmlspecialchars($badge['href']) ?>" target="_blank" rel="noopener"<?= $pass === 1 ? ' aria-hidden="true" tabindex="-1"' : '' ?>>
                                <?php
                                // Badges are self-hosted under resources/images/featured/. An absolute
                                // URL is used only where the directory's verifier requires its own hosted
                                // badge image to be referenced directly (e.g. Starter Best).
                                $src = strpos($badge['img'], '://') !== false
                                    ? $badge['img']
                                    : 'resources/images/featured/' . $badge['img'];
                                ?>
                                <img src="<?= htmlspecialchars($src) ?>" style="width: <?= (int) $badge['w'] ?>px; height: 54px;" width="<?= (int) $badge['w'] ?>" height="54" alt="<?= htmlspecialchars($badge['alt']) ?>" />
                            </a>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="video-modal-backdrop"></div>
        <div class="video-modal-content">
            <button class="video-modal-close" aria-label="Close video">&times;</button>
            <div class="video-modal-player">
                <iframe id="videoIframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
            </div>
        </div>
    </div>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content animate-on-scroll">
                    <h2>Ready to transform your business?</h2>
                    <p>Start using Argo Books to save time, reduce errors, and grow smarter.</p>
                    <div class="cta-buttons">
                        <a href="downloads" class="btn btn-white btn-lg">
                            <span>Get Started For Free</span>
                            <?= svg_icon('arrow-right', 20) ?>
                        </a>
                        <a href="pricing/" class="btn btn-outline-white btn-lg">
                            <span>View Pricing</span>
                        </a>
                    </div>
                    <div class="cta-features">
                        <span class="cta-feature">
                            <?= svg_icon('check', 16) ?>
                            Free to start
                        </span>
                        <span class="cta-feature">
                            <?= svg_icon('check', 16) ?>
                            No account required
                        </span>
                        <span class="cta-feature">
                            <?= svg_icon('check', 16) ?>
                            Setup in minutes
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/resources/footer/footer.php'; ?>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Video modal
        const videoModal = document.getElementById('videoModal');
        const videoIframe = document.getElementById('videoIframe');
        const videoUrl = 'https://www.youtube.com/embed/Dsn38p5g3Zg';

        document.getElementById('heroPlayBtn').addEventListener('click', function() {
            videoIframe.src = videoUrl + '?autoplay=1&rel=0&modestbranding=1';
            videoModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        function closeVideoModal() {
            videoModal.classList.remove('active');
            videoIframe.src = '';
            document.body.style.overflow = '';
        }

        videoModal.querySelector('.video-modal-close').addEventListener('click', closeVideoModal);
        videoModal.querySelector('.video-modal-backdrop').addEventListener('click', closeVideoModal);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && videoModal.classList.contains('active')) {
                closeVideoModal();
            }
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Pricing cycle toggle
        document.querySelectorAll('.pcards-cycle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const cycle = this.dataset.cycle;
                document.querySelectorAll('.pcards-cycle-btn').forEach(b => {
                    const isActive = b === this;
                    b.classList.toggle('active', isActive);
                    b.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                document.querySelectorAll('[data-active-cycle]').forEach(c => {
                    c.dataset.activeCycle = cycle;
                });
            });
        });

        // Feature tabs
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        let activeTabAnimation = null;

        function clearTabAnimation() {
            if (activeTabAnimation) {
                activeTabAnimation.forEach(id => clearTimeout(id));
                activeTabAnimation = null;
            }
        }

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab;

                clearTabAnimation();

                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                document.getElementById('tab-' + tabId).classList.add('active');

                // Reset animation classes on all mockups
                document.querySelectorAll('.animating').forEach(el => el.classList.remove('animating'));

                startTabAnimation(tabId);
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Download conversion tracking
        const downloadLinks = document.querySelectorAll('a[href="downloads"]');
        downloadLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                gtag('event', 'conversion', {
                    'send_to': 'AW-17210317271/niGZCJv2vbkbENezwo5A'
                });
            });
        });

        // Receipt scan & extract animation (loops)
        (function initReceiptScan() {
            const stage = document.getElementById('receiptScan');
            if (!stage) return;
            const status = document.getElementById('efStatus');
            const totalVal = stage.querySelector('.ef-total-val');
            let timers = [];
            function t(fn, d) { timers.push(setTimeout(fn, d)); }
            function clearTimers() { timers.forEach(clearTimeout); timers = []; }

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = '$' + (target * eased).toLocaleString('en-US', {
                        minimumFractionDigits: 2, maximumFractionDigits: 2
                    });
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            const seq = [
                { at: 380,  f: 'merchant' },
                { at: 720,  f: 'date' },
                { at: 1020, f: 'category', formOnly: true },
                { at: 1320, f: 'item-0' },
                { at: 1560, f: 'item-1' },
                { at: 1800, f: 'item-2' },
                { at: 2040, f: 'item-3' },
                { at: 2380, f: 'tax' },
                { at: 2680, f: 'total' }
            ];

            function run() {
                clearTimers();
                stage.classList.remove('done');
                stage.querySelectorAll('.scan-row').forEach(el => el.classList.remove('detected'));
                stage.querySelectorAll('.ef-field, .ef-line, .ef-trow').forEach(el => el.classList.remove('in'));
                if (status) status.classList.remove('in');
                if (totalVal) totalVal.textContent = '$0.00';

                // restart the beam sweep
                stage.classList.remove('scanning');
                void stage.offsetWidth;
                stage.classList.add('scanning');

                seq.forEach(s => {
                    t(() => {
                        if (!s.formOnly) {
                            const row = stage.querySelector('.scan-row[data-field="' + s.f + '"]');
                            if (row) row.classList.add('detected');
                        }
                        stage.querySelectorAll(
                            '.ef-field[data-field="' + s.f + '"], .ef-line[data-field="' + s.f + '"], .ef-trow[data-field="' + s.f + '"]'
                        ).forEach(el => el.classList.add('in'));
                        if (s.f === 'total' && totalVal) countUp(totalVal, 120.35, 800);
                    }, s.at);
                });

                // completion + loop
                t(() => {
                    stage.classList.remove('scanning');
                    stage.classList.add('done');
                    if (status) status.classList.add('in');
                }, 3150);
                t(run, 6400);
            }
            run();
        })();

        // Feature tab animation controller
        function startTabAnimation(tabId) {
            const timeouts = [];
            function t(fn, delay) {
                timeouts.push(setTimeout(fn, delay));
            }

            switch (tabId) {
                case 'expenses': animateExpenses(t); break;
                case 'predictive': animatePredictive(t); break;
                case 'inventory': animateInventory(t); break;
                case 'rental': animateRental(t); break;
                case 'customers': animateCustomers(t); break;
                case 'invoices': animateInvoices(t); break;
            }

            activeTabAnimation = timeouts;
        }

        // Expense & Revenue Tracking animation
        // Expense & Revenue overview: table streams in, charts pop up and animate
        function animateExpenses(t) {
            const stage = document.getElementById('expenseStage');
            if (!stage) return;
            const rows = stage.querySelectorAll('.txn-row');
            const chartLine = document.getElementById('expChartLine');
            const chartBars = document.getElementById('expChartBars');
            const netVal = stage.querySelector('.exp-net-val');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                rows.forEach(r => r.classList.remove('in'));
                if (chartLine) chartLine.classList.remove('in');
                if (chartBars) chartBars.classList.remove('in');
                if (netVal) netVal.textContent = '0';

                rows.forEach((r, i) => t(() => r.classList.add('in'), 250 + i * 170));
                const afterRows = 250 + rows.length * 170;

                t(() => { if (chartLine) chartLine.classList.add('in'); }, afterRows + 200);
                t(() => {
                    if (chartBars) chartBars.classList.add('in');
                    if (netVal) countUp(netVal, 12089, 1000);
                }, afterRows + 1000);

                t(run, afterRows + 4600);
            }
            run();
        }

        // Predictive Analytics animation
        // Predictive Analytics: KPIs count up, history draws, forecast extends into the cone
        function animatePredictive(t) {
            const stage = document.getElementById('forecastStage');
            if (!stage) return;
            const chart = stage.querySelector('.fc-chart');
            const kpis = stage.querySelectorAll('.fc-kpi');
            const next = stage.querySelector('.fc-next');
            const conf = stage.querySelector('.fc-conf');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                kpis.forEach(k => k.classList.remove('in'));
                if (chart) chart.classList.remove('in');
                if (next) next.textContent = '0';
                if (conf) conf.textContent = '0';

                kpis.forEach((k, i) => t(() => k.classList.add('in'), 250 + i * 160));
                t(() => {
                    if (next) countUp(next, 48200, 1200);
                    if (conf) countUp(conf, 94, 1200);
                }, 650);
                t(() => { if (chart) chart.classList.add('in'); }, 750);

                t(run, 6400);
            }
            run();
        }

        // Inventory Management animation
        // Inventory: stock cards stream in, counts count up, stock bars fill
        function animateInventory(t) {
            const stage = document.getElementById('inventoryStage');
            if (!stage) return;
            const cards = stage.querySelectorAll('.inv-card');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                cards.forEach(c => {
                    c.classList.remove('in');
                    const num = c.querySelector('.inv-num');
                    if (num) num.textContent = '0';
                });

                cards.forEach((c, i) => {
                    t(() => {
                        c.classList.add('in');
                        const num = c.querySelector('.inv-num');
                        if (num) countUp(num, parseInt(num.dataset.target, 10), 800);
                    }, 200 + i * 150);
                });

                t(run, 200 + cards.length * 150 + 4200);
            }
            run();
        }

        // Rental Management: calendar fades in, booked range fills, booking card pops up
        function animateRental(t) {
            const stage = document.getElementById('rentalStage');
            if (!stage) return;
            const seqDays = stage.querySelectorAll('.rent-seq');
            const booking = document.getElementById('rentBooking');

            function run() {
                stage.classList.remove('shown');
                seqDays.forEach(d => d.classList.remove('booked'));
                if (booking) booking.classList.remove('in');

                t(() => stage.classList.add('shown'), 100);
                seqDays.forEach((d, i) => t(() => d.classList.add('booked'), 800 + i * 200));
                t(() => { if (booking) booking.classList.add('in'); }, 800 + seqDays.length * 200 + 350);

                t(run, 6200);
            }
            run();
        }

        // Customer Management animation
        // Customer Management: directory streams in, profile card pops up
        function animateCustomers(t) {
            const stage = document.getElementById('customerStage');
            if (!stage) return;
            const rows = stage.querySelectorAll('.cust-row');
            const profile = document.getElementById('custProfile');
            const ltv = stage.querySelector('.cps-ltv');

            function countUp(el, target, duration) {
                const start = performance.now();
                function step(now) {
                    const p = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - p, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('en-US');
                    if (p < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            }

            function run() {
                rows.forEach(r => r.classList.remove('in'));
                if (profile) profile.classList.remove('in');
                if (ltv) ltv.textContent = '0';

                rows.forEach((r, i) => t(() => r.classList.add('in'), 250 + i * 150));
                const afterRows = 250 + rows.length * 150;

                t(() => {
                    if (profile) profile.classList.add('in');
                    if (ltv) countUp(ltv, 4230, 1100);
                }, afterRows + 300);

                t(run, afterRows + 4600);
            }
            run();
        }

        // Invoicing animation
        // Invoice Studio: one-shot intro build, then reveal interactive controls
        function animateInvoices(t) {
            const studio = document.getElementById('invoiceStudio');
            const doc = document.getElementById('invoiceDoc');
            if (!studio || !doc) return;

            const colorPanel = document.getElementById('colorPanel');
            const templatePanel = document.getElementById('templatePanel');
            const status = document.getElementById('invStatus');
            const totalVal = doc.querySelector('.inv-total-value');

            function animateCounter(el, target, duration) {
                const startTime = performance.now();
                function update(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = '$' + (target * eased).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            // Reset to the intro state
            doc.classList.add('intro');
            doc.querySelectorAll('.inv-anim, .inv-item').forEach(el => el.classList.remove('in'));
            if (status) status.classList.remove('in');
            if (colorPanel) colorPanel.classList.add('panel-hidden');
            if (templatePanel) templatePanel.classList.add('panel-hidden');

            // Staggered build of each section
            const steps = doc.querySelectorAll('.inv-top, .inv-billto, .inv-row-head, .inv-item, .inv-totals');
            steps.forEach((el, i) => t(() => el.classList.add('in'), 300 + i * 280));

            const lastAt = 300 + (steps.length - 1) * 280; // when the totals row lands

            // Count the total up as it appears
            if (totalVal) {
                totalVal.textContent = '$0.00';
                t(() => animateCounter(totalVal, 1234, 900), lastAt);
            }

            // Stamp "Paid"
            t(() => { if (status) status.classList.add('in'); }, lastAt + 600);

            // Reveal the interactive controls, then drop the intro state
            t(() => {
                if (colorPanel) colorPanel.classList.remove('panel-hidden');
                if (templatePanel) templatePanel.classList.remove('panel-hidden');
            }, lastAt + 1000);
            t(() => doc.classList.remove('intro'), lastAt + 1700);
        }

        // Invoice Studio: live color wheel + template switching (set up once)
        function initInvoiceStudio() {
            const studio = document.getElementById('invoiceStudio');
            const doc = document.getElementById('invoiceDoc');
            if (!studio || !doc) return;

            const wheel = document.getElementById('colorWheel');
            const thumb = document.getElementById('colorThumb');
            const lightSlider = document.getElementById('lightSlider');
            const lightThumb = document.getElementById('lightThumb');

            let hue = 227, sat = 79, light = 58;

            function apply() {
                studio.style.setProperty('--inv-accent', hue + ' ' + sat + '% ' + light + '%');
                if (lightSlider) {
                    lightSlider.style.background =
                        'linear-gradient(to right, hsl(' + hue + ' ' + sat + '% 72%), hsl(' + hue + ' ' + sat + '% 20%))';
                }
            }

            function pickColor(clientX, clientY) {
                const rect = wheel.getBoundingClientRect();
                const r = rect.width / 2;
                let x = clientX - rect.left - r;
                let y = clientY - rect.top - r;
                let dist = Math.sqrt(x * x + y * y);
                if (dist > r) { x = x / dist * r; y = y / dist * r; dist = r; }
                let ang = Math.atan2(y, x) * 180 / Math.PI;
                if (ang < 0) ang += 360;
                hue = Math.round(ang);
                sat = Math.round(48 + (dist / r) * 37); // 48–85, kept tasteful
                thumb.style.left = (r + x) + 'px';
                thumb.style.top = (r + y) + 'px';
                apply();
            }

            function pickLight(clientX) {
                const rect = lightSlider.getBoundingClientRect();
                let p = (clientX - rect.left) / rect.width;
                p = Math.max(0, Math.min(1, p));
                light = Math.round(64 - p * 30); // 64 (light) → 34 (dark)
                lightThumb.style.left = (p * 100) + '%';
                apply();
            }

            function makeDrag(pickFn) {
                return function (e) {
                    e.preventDefault();
                    const move = ev => {
                        const pt = ev.touches ? ev.touches[0] : ev;
                        pickFn(pt.clientX, pt.clientY);
                    };
                    move(e);
                    const up = () => {
                        document.removeEventListener('mousemove', move);
                        document.removeEventListener('mouseup', up);
                        document.removeEventListener('touchmove', move);
                        document.removeEventListener('touchend', up);
                    };
                    document.addEventListener('mousemove', move);
                    document.addEventListener('mouseup', up);
                    document.addEventListener('touchmove', move, { passive: false });
                    document.addEventListener('touchend', up);
                };
            }

            if (wheel) {
                const drag = makeDrag((x, y) => pickColor(x, y));
                wheel.addEventListener('mousedown', drag);
                wheel.addEventListener('touchstart', drag, { passive: false });
            }
            if (lightSlider) {
                const drag = makeDrag(x => pickLight(x));
                lightSlider.addEventListener('mousedown', drag);
                lightSlider.addEventListener('touchstart', drag, { passive: false });
            }

            const tmplBtns = studio.querySelectorAll('.tmpl-btn');
            tmplBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    doc.classList.remove('theme-modern', 'theme-contemporary', 'theme-classic');
                    doc.classList.add(btn.dataset.theme);
                    tmplBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                });
            });

            // Place the wheel thumb at the starting accent
            if (thumb && wheel) {
                const r = 80; // half of the 160px wheel
                const rad = hue * Math.PI / 180;
                const d = ((sat - 48) / 37) * r;
                thumb.style.left = (r + Math.cos(rad) * d) + 'px';
                thumb.style.top = (r + Math.sin(rad) * d) + 'px';
            }
            apply();
        }
        initInvoiceStudio();

        // AI Import flow animation
        const aiImportDemo = document.getElementById('aiImportDemo');
        if (aiImportDemo) {
            let aiImportTimers = [];
            const impPill = document.getElementById('impPill');
            const impDone = document.getElementById('impDone');

            function runAiImportAnimation() {
                aiImportTimers.forEach(clearTimeout);
                aiImportTimers = [];

                // Reset to the messy state
                aiImportDemo.querySelectorAll('[data-c]').forEach(el => el.classList.remove('mapped'));
                if (impPill) impPill.classList.remove('in');
                if (impDone) impDone.classList.remove('in');

                // Map each column left to right (header renames + data reformats)
                [0, 1, 2, 3].forEach((c, i) => {
                    aiImportTimers.push(setTimeout(() => {
                        aiImportDemo.querySelectorAll('[data-c="' + c + '"]').forEach(el => el.classList.add('mapped'));
                    }, 800 + i * 480));
                });

                // Confirmations, then loop
                aiImportTimers.push(setTimeout(() => {
                    if (impPill) impPill.classList.add('in');
                    if (impDone) impDone.classList.add('in');
                }, 800 + 4 * 480 + 250));
                aiImportTimers.push(setTimeout(runAiImportAnimation, 800 + 4 * 480 + 3400));
            }

            let aiAnimationStarted = false;
            const aiObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !aiAnimationStarted) {
                        aiAnimationStarted = true;
                        runAiImportAnimation();
                    }
                });
            }, { threshold: 0.2 });

            aiObserver.observe(aiImportDemo);
        }

        // Security encryption terminal animation
        const secTerminal = document.getElementById('securityTerminal');
        if (secTerminal) {
            let secAnimStarted = false;
            const cipherChars = '0123456789abcdef';

            function randomHex(len) {
                let s = '';
                for (let i = 0; i < len; i++) s += cipherChars[Math.floor(Math.random() * cipherChars.length)];
                return s;
            }

            function scrambleText(el, duration) {
                const plain = el.dataset.plain;
                const len = plain.length;
                const startTime = Date.now();
                const interval = setInterval(() => {
                    const elapsed = Date.now() - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const locked = Math.floor(progress * len);
                    let result = '';
                    for (let i = 0; i < len; i++) {
                        if (i < locked) {
                            result += randomHex(1);
                        } else {
                            result += plain[i];
                        }
                    }
                    el.textContent = result;
                    if (progress >= 1) {
                        clearInterval(interval);
                        // Keep cycling the cipher text
                        const cycleInterval = setInterval(() => {
                            el.textContent = randomHex(len);
                        }, 200);
                        el._cycleInterval = cycleInterval;
                    }
                }, 100);
                return interval;
            }

            function runEncryptionAnimation() {
                const bar = document.getElementById('termEncryptBar');
                const fill = document.getElementById('termEncryptFill');
                const label = document.getElementById('termEncryptLabel');
                const ciphers = secTerminal.querySelectorAll('.t-cipher');

                // Reset
                bar.classList.remove('active', 'done');
                fill.style.transition = 'none';
                fill.style.width = '0%';
                label.textContent = 'AES-256-GCM encrypting...';
                ciphers.forEach(el => {
                    if (el._cycleInterval) clearInterval(el._cycleInterval);
                    el.textContent = el.dataset.plain;
                });

                // Step 1: Show plain text for 2s, then start encrypt bar
                setTimeout(() => {
                    bar.classList.add('active');
                    requestAnimationFrame(() => {
                        fill.style.transition = 'width 5.1s ease-in-out';
                        requestAnimationFrame(() => { fill.style.width = '100%'; });
                    });
                }, 2500);

                // Step 2: Scramble each row staggered (after 2s plain text delay)
                ciphers.forEach((el, i) => {
                    setTimeout(() => scrambleText(el, 1800), 2800 + i * 1000);
                });

                // Step 3: Mark complete (7.6s, when last row finishes scrambling)
                setTimeout(() => {
                    bar.classList.add('done');
                    label.textContent = 'Encryption complete. Stored locally';
                }, 7600);

                // Step 4: Hold, then restart (12s)
                setTimeout(() => runEncryptionAnimation(), 12000);
            }

            const secObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !secAnimStarted) {
                        secAnimStarted = true;
                        runEncryptionAnimation();
                    }
                });
            }, { threshold: 0.3 });

            secObserver.observe(secTerminal);
        }
    });
    </script>
</body>

</html>
