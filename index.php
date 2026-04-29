<?php
session_start();
require_once __DIR__ . '/community/users/user_functions.php';
require_once __DIR__ . '/track_referral.php';
require_once __DIR__ . '/statistics.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/resources/icons.php';

$pricing = get_pricing_config();
$plans = get_plan_features();
$monthlyPrice = $pricing['premium_monthly_price'];
$yearlyPrice = $pricing['premium_yearly_price'];
$yearlySavings = ($monthlyPrice * 12) - $yearlyPrice;

track_page_view($_SERVER['REQUEST_URI']);

// Check for remember me cookie and auto-login user if valid
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    check_remember_me();
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
        content="Transform your small business with Argo Books. Smart receipt scanning, spreadsheet import, predictive analytics, inventory management and more. Free software.">
    <meta name="keywords"
        content="receipt scanning, smart spreadsheet import, predictive analytics, business software, inventory management, rental management, invoice generator, small business automation, data import">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books - Smart Business Management Software">
    <meta property="og:description"
        content="Transform your business with smart receipt scanning, predictive analytics, inventory management and automated invoicing.">
    <meta property="og:url" content="https://argorobots.com/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books - Smart Business Management Software">
    <meta name="twitter:description"
        content="Transform your business with smart receipt scanning, predictive analytics, and automated business management.">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

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

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-17210317271"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-17210317271');
    </script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "description": "Smart business management software with receipt scanning, predictive analytics, and inventory management",
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
            "softwareVersion": "1.0.4",
            "datePublished": "2025-05-01",
            "dateModified": "2025-11-28"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books - Smart Business Management Software</title>

    <script src="resources/scripts/jquery-3.6.0.js"></script>
    <script src="resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="resources/styles/custom-colors.css">
    <link rel="stylesheet" href="resources/styles/button.css">
    <link rel="stylesheet" href="resources/header/style.css">
    <link rel="stylesheet" href="resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
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
                <div class="hero-app-title animate-fade-in">Argo Books</div>
                <h1 class="hero-title animate-fade-in-up">
                    The smarter way to<br>
                    <span class="text-gradient">run your business</span>
                </h1>
                <p class="hero-subtitle animate-fade-in-up delay-1">
                    Simple, modern accounting software built for small businesses — with automation that saves time and keeps your finances organized
                </p>
                <div class="hero-cta animate-fade-in-up delay-2">
                    <a href="downloads" class="btn btn-primary btn-lg">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 20) ?>
                    </a>
                    <a href="/features/" class="btn btn-secondary btn-lg">
                        <span>See Features</span>
                    </a>
                </div>
            </div>
            <div class="hero-visual animate-fade-in-up delay-2">
                <div class="hero-device">
                    <div class="device-frame">
                        <img src="resources/images/dashboard.webp" alt="Argo Books Dashboard" class="device-screen" width="2400" height="1524">
                        <button class="hero-play-btn" id="heroPlayBtn" aria-label="Watch demo video">
                            <?= svg_icon('play-filled', 28) ?>
                        </button>
                    </div>
                    <div class="floating-card floating-card-1 animate-float">
                        <div class="floating-card-icon">
                            <?= svg_icon('loading', 24) ?>
                        </div>
                        <div class="floating-card-content">
                            <span class="floating-card-label">Smart Scanning</span>
                            <span class="floating-card-value">Receipt processed</span>
                        </div>
                    </div>
                    <div class="floating-card floating-card-2 animate-float-delayed">
                        <div class="floating-card-icon success">
                            <?= svg_icon('trending-up', 24) ?>
                        </div>
                        <div class="floating-card-content">
                            <span class="floating-card-label">Revenue Up</span>
                            <span class="floating-card-value success">+24% this month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Tabbed Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">Features</span>
                <h2 class="section-title">Everything you need to grow</h2>
                <p class="section-description">Easy-to-use accounting software with smart receipt scanning, spreadsheet import, predictive analytics, and inventory management. Everything you need to run your business.</p>
            </div>

            <div class="features-tabs">
                <div class="features-tabs-nav animate-on-scroll">
                    <button class="tab-btn active" data-tab="ai-receipts">
                        <div class="tab-icon">
                            <?= svg_icon('receipt-scan-detail', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">AI Receipt Scanning</span>
                            <span class="tab-subtitle">Snap, scan, done</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="expenses">
                        <div class="tab-icon">
                            <?= svg_icon('dollar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Expense & Revenue Tracking</span>
                            <span class="tab-subtitle">Track every dollar</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="predictive">
                        <div class="tab-icon">
                            <?= svg_icon('analytics', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Predictive Analytics</span>
                            <span class="tab-subtitle">Forecast your future</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="inventory">
                        <div class="tab-icon">
                            <?= svg_icon('package', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Inventory Management</span>
                            <span class="tab-subtitle">Track every item</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="rental">
                        <div class="tab-icon">
                            <?= svg_icon('calendar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Rental Management</span>
                            <span class="tab-subtitle">Bookings made easy</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="customers">
                        <div class="tab-icon">
                            <?= svg_icon('users', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Customer Management</span>
                            <span class="tab-subtitle">Know your customers</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="invoices">
                        <div class="tab-icon">
                            <?= svg_icon('document', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Invoicing</span>
                            <span class="tab-subtitle">Professional invoices</span>
                        </div>
                    </button>
                </div>

                <div class="features-tabs-content">
                    <!-- AI Receipt Scanning -->
                    <div class="tab-content active" id="tab-ai-receipts">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Scan receipts instantly</h3>
                                <p>Take a photo of any receipt with your phone or upload from your computer. The system automatically extracts supplier, date, amount, and line items with 99% accuracy.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Works with photos from your phone</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Automatic categorization</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Searchable receipt archive</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup receipt-mockup">
                                        <div class="phone-frame">
                                            <div class="phone-screen">
                                                <div class="scan-animation" id="receiptScanAnimation">
                                                    <div class="scan-line"></div>
                                                    <div class="scan-complete-indicator">
                                                        <?= svg_icon('check', null, '', 3, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                                                    </div>
                                                </div>
                                                <div class="receipt-preview">
                                                    <div class="receipt-header">
                                                        <div class="receipt-store-name scan-text">Office Depot #1284</div>
                                                        <div class="receipt-date scan-text">Mar 14, 2026 &nbsp; 2:47 PM</div>
                                                    </div>
                                                    <div class="receipt-items">
                                                        <div class="receipt-item scan-text">
                                                            <span>Copy Paper (5 ream)</span>
                                                            <span>$42.99</span>
                                                        </div>
                                                        <div class="receipt-item scan-text">
                                                            <span>Ink Cartridge BK</span>
                                                            <span>$34.99</span>
                                                        </div>
                                                        <div class="receipt-item scan-text">
                                                            <span>Desk Organizer</span>
                                                            <span>$24.49</span>
                                                        </div>
                                                        <div class="receipt-item scan-text">
                                                            <span>Sticky Notes (12pk)</span>
                                                            <span>$8.99</span>
                                                        </div>
                                                        <div class="receipt-item receipt-tax scan-text">
                                                            <span>Tax</span>
                                                            <span>$8.89</span>
                                                        </div>
                                                    </div>
                                                    <div class="receipt-total scan-text">
                                                        <span>Total</span>
                                                        <span>$120.35</span>
                                                    </div>
                                                </div>
                                                <div class="ai-badge" id="aiBadge">
                                                    <span class="badge-text-scanning">
                                                        <?= svg_icon('clock', 16) ?>
                                                        AI Processing...
                                                    </span>
                                                    <span class="badge-text-complete">
                                                        <?= svg_icon('check', 16) ?>
                                                        Scan Complete!
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Tracking -->
                    <div class="tab-content" id="tab-expenses">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Track expenses and revenue with ease</h3>
                                <p>Our intuitive interface makes recording expenses and revenue simple. Smart input validation prevents errors before they happen, so your books are always accurate.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Easy-to-use guided forms</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Smart validation prevents mistakes</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Categorize and organize transactions</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup expenses-mockup">
                                        <div class="expense-form-header">
                                            <span class="form-title">New Transaction</span>
                                        </div>
                                        <div class="expense-form-fields">
                                            <div class="form-field">
                                                <span class="field-label">Type</span>
                                                <div class="field-toggle">
                                                    <span class="toggle-option">Expense</span>
                                                    <span class="toggle-option active">Revenue</span>
                                                </div>
                                            </div>
                                            <div class="form-field">
                                                <span class="field-label">Amount</span>
                                                <span class="field-value">$85.00</span>
                                            </div>
                                            <div class="form-field">
                                                <span class="field-label">Category</span>
                                                <span class="field-value">Books</span>
                                            </div>
                                        </div>
                                        <div class="expense-form-validation">
                                            <div class="validation-check">
                                                <?= svg_icon('check', 16) ?>
                                                <span>Valid amount</span>
                                            </div>
                                            <div class="validation-check">
                                                <?= svg_icon('check', 16) ?>
                                                <span>Category selected</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Predictive Analytics -->
                    <div class="tab-content" id="tab-predictive">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>See the future of your business</h3>
                                <p>Our predictive engine analyzes your historical data to forecast sales trends, identify seasonal patterns, and help you make data-driven decisions.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Sales trend forecasting</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Seasonal pattern detection</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Revenue projections</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup chart-mockup">
                                        <div class="chart-header">
                                            <span class="chart-title">Sales Forecast</span>
                                            <span class="chart-period">Next 6 months</span>
                                        </div>
                                        <div class="chart-area">
                                            <svg viewBox="0 0 300 150" class="forecast-chart">
                                                <defs>
                                                    <linearGradient id="chartGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                        <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:0.3"/>
                                                        <stop offset="100%" style="stop-color:#3b82f6;stop-opacity:0"/>
                                                    </linearGradient>
                                                </defs>
                                                <path d="M0,130 C40,135 60,95 105,90 C150,85 155,65 200,50 L200,150 L0,150 Z" fill="url(#chartGradient)"/>
                                                <path d="M0,130 C40,135 60,95 105,90 C150,85 155,65 200,50" fill="none" stroke="#3b82f6" stroke-width="3"/>
                                                <path d="M200,50 C230,40 250,30 265,35 C280,40 285,20 300,15" fill="none" stroke="#3b82f6" stroke-width="3" stroke-dasharray="5,5" opacity="0.5"/>
                                                <circle cx="200" cy="50" r="5" fill="#3b82f6"/>
                                            </svg>
                                            <div class="prediction-badge">
                                                <span class="prediction-arrow">+18%</span>
                                                <span class="prediction-text">Predicted Growth</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Inventory Management -->
                    <div class="tab-content" id="tab-inventory">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Complete inventory control</h3>
                                <p>Track stock levels, set reorder points, and never run out of your best-selling items. Real-time visibility across all your products.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Real-time stock tracking</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Low stock alerts</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Purchase order management</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup inventory-mockup">
                                        <div class="inventory-grid">
                                            <div class="inventory-item">
                                                <div class="item-icon"><?= svg_icon('shape-square', 24) ?></div>
                                                <div class="item-details">
                                                    <span class="item-name">Widget Pro</span>
                                                    <span class="item-stock high">142 in stock</span>
                                                </div>
                                                <div class="item-bar high"></div>
                                            </div>
                                            <div class="inventory-item">
                                                <div class="item-icon"><?= svg_icon('shape-circle', 24) ?></div>
                                                <div class="item-details">
                                                    <span class="item-name">Basic Kit</span>
                                                    <span class="item-stock medium">34 in stock</span>
                                                </div>
                                                <div class="item-bar medium"></div>
                                            </div>
                                            <div class="inventory-item">
                                                <div class="item-icon"><?= svg_icon('shape-hexagon', 24) ?></div>
                                                <div class="item-details">
                                                    <span class="item-name">Deluxe Bundle</span>
                                                    <span class="item-stock low">8 in stock</span>
                                                </div>
                                                <div class="item-bar low"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Management -->
                    <div class="tab-content" id="tab-rental">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Simplify your rentals</h3>
                                <p>Manage equipment rentals, track availability, and handle bookings with ease. Perfect for rental businesses of any size.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Availability calendar</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Booking management</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Return tracking</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup calendar-mockup">
                                        <div class="calendar-header">
                                            <span class="cal-month">December 2025</span>
                                            <div class="cal-nav">
                                                <button><?= svg_icon('chevron-left-sm', 16) ?></button>
                                                <button><?= svg_icon('chevron-right-sm', 16) ?></button>
                                            </div>
                                        </div>
                                        <div class="calendar-grid">
                                            <div class="cal-day header">Su</div>
                                            <div class="cal-day header">Mo</div>
                                            <div class="cal-day header">Tu</div>
                                            <div class="cal-day header">We</div>
                                            <div class="cal-day header">Th</div>
                                            <div class="cal-day header">Fr</div>
                                            <div class="cal-day header">Sa</div>
                                            <div class="cal-day">1</div>
                                            <div class="cal-day booked">2</div>
                                            <div class="cal-day booked">3</div>
                                            <div class="cal-day booked">4</div>
                                            <div class="cal-day">5</div>
                                            <div class="cal-day">6</div>
                                            <div class="cal-day">7</div>
                                            <div class="cal-day">8</div>
                                            <div class="cal-day">9</div>
                                            <div class="cal-day available">10</div>
                                            <div class="cal-day available">11</div>
                                            <div class="cal-day available">12</div>
                                            <div class="cal-day">13</div>
                                            <div class="cal-day">14</div>
                                        </div>
                                        <div class="calendar-legend">
                                            <span class="legend-item"><span class="dot booked"></span> Booked</span>
                                            <span class="legend-item"><span class="dot available"></span> Available</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Management -->
                    <div class="tab-content" id="tab-customers">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Build lasting relationships</h3>
                                <p>Keep track of customer information, purchase history, and preferences. Deliver personalized service that keeps them coming back.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Customer profiles</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Purchase history</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Notes and preferences</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup customers-mockup">
                                        <div class="customer-card">
                                            <div class="customer-avatar">JD</div>
                                            <div class="customer-info">
                                                <span class="customer-name">Jane Doe</span>
                                                <span class="customer-email">jane@company.com</span>
                                            </div>
                                            <div class="customer-stats">
                                                <div class="stat">
                                                    <span class="stat-val">$4,230</span>
                                                    <span class="stat-lbl">Total Spent</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-val">12</span>
                                                    <span class="stat-lbl">Orders</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="customer-card">
                                            <div class="customer-avatar alt">MS</div>
                                            <div class="customer-info">
                                                <span class="customer-name">Mike Smith</span>
                                                <span class="customer-email">mike@store.com</span>
                                            </div>
                                            <div class="customer-stats">
                                                <div class="stat">
                                                    <span class="stat-val">$2,890</span>
                                                    <span class="stat-lbl">Total Spent</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-val">8</span>
                                                    <span class="stat-lbl">Orders</span>
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
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Professional invoices in seconds</h3>
                                <p>Create, send, and track invoices with ease. Customize templates, set payment terms, and get paid faster.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Customizable templates</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Automatic numbering</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Payment tracking</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup invoice-mockup">
                                        <div class="invoice-header">
                                            <div class="invoice-logo">INVOICE</div>
                                        </div>
                                        <div class="invoice-meta">
                                            <div class="meta-item">
                                                <span class="meta-label">Date</span>
                                                <span class="meta-value">Nov 28, 2025</span>
                                            </div>
                                            <div class="meta-item">
                                                <span class="meta-label">Due</span>
                                                <span class="meta-value">Dec 28, 2025</span>
                                            </div>
                                        </div>
                                        <div class="invoice-total">
                                            <span class="total-label">Total Due</span>
                                            <span class="total-value">$1,234.00</span>
                                        </div>
                                        <div class="invoice-status paid">Paid</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- AI Spreadsheet Import Section -->
    <section class="ai-import-section">
        <div class="container">
            <div class="ai-import-header animate-on-scroll">
                <span class="section-tag">Smart Import</span>
                <h2 class="section-title">Import any spreadsheet in seconds</h2>
                <p class="section-description">Drop your file in and Argo Books handles the rest. No reformatting, no manual mapping, no headaches.</p>
            </div>

            <div class="ai-import-flow animate-on-scroll" id="aiImportDemo">
                <!-- Source: messy spreadsheet -->
                <div class="ai-flow-source">
                    <div class="ai-flow-card ai-flow-card-source">
                        <div class="ai-flow-card-header">
                            <div class="ai-flow-file-icon"><?= svg_icon('table', 16) ?></div>
                            <span class="ai-flow-file-name">bill_export.xlsx</span>
                            <span class="ai-flow-file-badge">Excel</span>
                        </div>
                        <div class="ai-flow-table">
                            <div class="ai-flow-row ai-flow-row-header">
                                <span>Supplier</span>
                                <span>Amt Owed</span>
                                <span>Pay By</span>
                                <span>Memo</span>
                            </div>
                            <div class="ai-flow-row" data-row="0">
                                <span>Acme Supply Co</span>
                                <span>$4,280.00</span>
                                <span>12/15/2025</span>
                                <span>INV-3847</span>
                            </div>
                            <div class="ai-flow-row" data-row="1">
                                <span>TechFlow LLC</span>
                                <span>$950.50</span>
                                <span>01/02/2026</span>
                                <span>PO-9912</span>
                            </div>
                            <div class="ai-flow-row ai-flow-row-faded" data-row="2">
                                <span>NovaCorp Int'l</span>
                                <span>$12,100.00</span>
                                <span>11/30/2025</span>
                                <span>Contract #441</span>
                            </div>
                        </div>
                        <div class="ai-flow-row-count">+384 rows</div>
                    </div>
                </div>

                <!-- Center: processing hub -->
                <div class="ai-flow-center">
                    <div class="ai-flow-connector-line ai-flow-connector-left"></div>
                    <div class="ai-flow-hub" id="aiFlowHub">
                        <div class="ai-flow-hub-ring">
                            <div class="ai-hub-glow"></div>
                            <svg viewBox="0 0 80 80" class="ai-hub-svg">
                                <circle cx="40" cy="40" r="36" fill="none" stroke="#e0e7ff" stroke-width="2"/>
                                <circle cx="40" cy="40" r="36" fill="none" stroke="url(#hubGrad)" stroke-width="3" stroke-dasharray="226" stroke-dashoffset="226" stroke-linecap="round" class="ai-hub-progress"/>
                                <defs>
                                    <linearGradient id="hubGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#3b82f6"/>
                                        <stop offset="100%" style="stop-color:#2563eb"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="ai-hub-icon">
                                <?= svg_icon('bolt', 24) ?>
                            </div>
                        </div>
                        <span class="ai-flow-hub-label" id="aiHubLabel">Analyzing...</span>
                    </div>
                    <div class="ai-flow-connector-line ai-flow-connector-right"></div>
                    <!-- Animated particles -->
                    <div class="ai-flow-particles" id="aiParticles"></div>
                </div>

                <!-- Destination: clean imported fields -->
                <div class="ai-flow-dest">
                    <div class="ai-flow-card ai-flow-card-dest">
                        <div class="ai-flow-card-header ai-flow-card-header-dest">
                            <span class="ai-flow-dest-title">Argo Books</span>
                            <span class="ai-flow-dest-badge" id="aiMatchBadge">0/4</span>
                        </div>
                        <div class="ai-flow-fields" id="aiFlowFields">
                            <div class="ai-flow-field" data-field="0">
                                <div class="ai-flow-field-label">Supplier Name</div>
                                <div class="ai-flow-field-value">Acme Supply Co</div>
                                <div class="ai-flow-field-tag ai-flow-field-tag-text"><?= svg_icon('check', 12) ?> Text</div>
                            </div>
                            <div class="ai-flow-field" data-field="1">
                                <div class="ai-flow-field-label">Balance Due</div>
                                <div class="ai-flow-field-value">$4,280.00</div>
                                <div class="ai-flow-field-tag ai-flow-field-tag-currency"><?= svg_icon('check', 12) ?> Currency</div>
                            </div>
                            <div class="ai-flow-field" data-field="2">
                                <div class="ai-flow-field-label">Due Date</div>
                                <div class="ai-flow-field-value">Dec 15, 2025</div>
                                <div class="ai-flow-field-tag ai-flow-field-tag-date"><?= svg_icon('check', 12) ?> Date</div>
                            </div>
                            <div class="ai-flow-field" data-field="3">
                                <div class="ai-flow-field-label">Reference #</div>
                                <div class="ai-flow-field-value">INV-3847</div>
                                <div class="ai-flow-field-tag ai-flow-field-tag-id"><?= svg_icon('check', 12) ?> ID</div>
                            </div>
                        </div>
                        <div class="ai-flow-footer" id="aiFlowFooter">
                            <?= svg_icon('check', 16) ?>
                            <span>Ready to import 387 rows</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature pills -->
            <div class="ai-import-pills animate-on-scroll">
                <div class="ai-pill">
                    <div class="ai-pill-icon"><?= svg_icon('bolt', 18) ?></div>
                    <div class="ai-pill-text">
                        <strong>Instant detection</strong>
                        <span>Names, emails, dates, currencies — recognized automatically</span>
                    </div>
                </div>
                <div class="ai-pill">
                    <div class="ai-pill-icon"><?= svg_icon('table', 18) ?></div>
                    <div class="ai-pill-text">
                        <strong>Any format</strong>
                        <span>Messy columns, unusual names, mixed data — no cleanup needed</span>
                    </div>
                </div>
                <div class="ai-pill">
                    <div class="ai-pill-icon"><?= svg_icon('shield', 18) ?></div>
                    <div class="ai-pill-text">
                        <strong>Private &amp; secure</strong>
                        <span>Encrypted processing — never stored or used for training</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tax-Ready / Reports Section -->
    <section class="tax-ready-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">Reports &amp; Insights</span>
                <h2 class="section-title">Stay tax-ready with clean books and instant reports</h2>
                <p class="section-description">Every transaction is categorized and matched to its receipt as you go. Generate any report you need in seconds — no spreadsheets, no scrambling at year-end.</p>
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
                            <text x="160" y="32" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="600" fill="#64748b" text-anchor="middle">Argo Books — Dashboard</text>
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
                            <text x="64" y="74" font-family="-apple-system,system-ui,sans-serif" font-size="7.5" fill="#94a3b8">April 2026 — Q2 update</text>
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
                            <path d="M 228 36 l 3 3 l 6 -6" stroke="#15803d" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <text x="246" y="40" font-family="-apple-system,system-ui,sans-serif" font-size="8" font-weight="700" fill="#15803d">All matched</text>
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
                    <span class="section-tag">Security</span>
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
                            <span class="terminal-title">your_company.argo — encrypted view</span>
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
                <span class="section-tag">Pricing</span>
                <h2 class="section-title">Pick a plan that's right for you</h2>
                <p class="section-description">Start free, upgrade when you need more. No hidden fees, no surprises.</p>
            </div>

            <div class="pricing-grid">
                <!-- Free Plan -->
                <div class="pricing-card animate-on-scroll">
                    <div class="pricing-header">
                        <span class="pricing-tag">Free Forever</span>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="amount">0</span>
                        </div>
                        <p class="pricing-description">Perfect for getting started</p>
                    </div>
                    <ul class="pricing-features">
                        <?php foreach ($plans['free']['features'] as $feature): ?>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span><?= render_feature_label($feature) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="downloads" class="btn btn-secondary btn-block">Get Started Free</a>
                </div>

                <!-- Premium Plan -->
                <div class="pricing-card ai-card animate-on-scroll">
                    <div class="pricing-header">
                        <span class="pricing-tag ai">Premium</span>
                        <div class="pricing-amount">
                            <span class="currency">$</span>
                            <span class="amount"><?php echo number_format($monthlyPrice, 0); ?></span>
                            <span class="period">CAD/month</span>
                        </div>
                        <p class="pricing-alt">or $<?php echo number_format($yearlyPrice, 0); ?> CAD/year (save $<?php echo number_format($yearlySavings, 0); ?>)</p>
                        <p class="pricing-description">Unlock the full power of Argo Books</p>
                    </div>
                    <ul class="pricing-features">
                        <?php foreach ($plans['premium']['features'] as $feature): ?>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span><?= render_feature_label($feature) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="pricing/premium/" class="btn btn-ai btn-block">Subscribe to Premium</a>
                </div>
            </div>
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
                            <span>Get Started Free</span>
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
                            No credit card required
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
            <div id="includeFooter"></div>
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

        // Receipt scan animation cycle
        const scanAnimation = document.getElementById('receiptScanAnimation');
        const aiBadge = document.getElementById('aiBadge');
        const scanLine = scanAnimation ? scanAnimation.querySelector('.scan-line') : null;
        const scanTexts = scanAnimation ? scanAnimation.parentElement.querySelectorAll('.scan-text') : [];
        let scanTimeouts = [];

        function clearScanTimeouts() {
            scanTimeouts.forEach(t => clearTimeout(t));
            scanTimeouts = [];
        }

        function runScanCycle() {
            if (!scanAnimation || !aiBadge || !scanLine) return;
            clearScanTimeouts();

            // Reset all states
            scanAnimation.classList.remove('scan-complete');
            aiBadge.classList.remove('complete');
            scanTexts.forEach(el => {
                el.classList.remove('highlighted', 'extracted');
            });

            // Run single scan pass
            scanLine.style.animation = 'none';
            scanLine.offsetHeight;
            scanLine.style.animation = 'scanLine 2.5s linear 1 forwards';

            // Highlight each text line exactly when the scan line crosses it
            const scanDuration = 2500;
            const containerRect = scanAnimation.getBoundingClientRect();
            const containerHeight = containerRect.height || 1;
            scanTexts.forEach((el) => {
                const elRect = el.getBoundingClientRect();
                const elCenterY = (elRect.top - containerRect.top) + (elRect.height / 2);
                const progress = Math.max(0, Math.min(1, elCenterY / containerHeight));
                const highlightAt = progress * scanDuration;
                scanTimeouts.push(setTimeout(() => {
                    el.classList.add('highlighted');
                }, highlightAt));
            });

            // After scan completes, transition highlights to extracted (green)
            scanTimeouts.push(setTimeout(() => {
                scanTexts.forEach((el, i) => {
                    scanTimeouts.push(setTimeout(() => {
                        el.classList.remove('highlighted');
                        el.classList.add('extracted');
                    }, i * 80));
                });
            }, scanDuration + 200));

            // Show complete state
            const completeAt = scanDuration + 200 + (scanTexts.length * 80) + 300;
            scanTimeouts.push(setTimeout(() => {
                scanAnimation.classList.add('scan-complete');
                aiBadge.classList.add('complete');

                // Restart cycle after showing complete
                scanTimeouts.push(setTimeout(() => {
                    runScanCycle();
                }, 3000));
            }, completeAt));
        }

        // Start the scan animation cycle
        if (scanAnimation) {
            runScanCycle();
        }

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
        function animateExpenses(t) {
            const mockup = document.querySelector('.expenses-mockup');
            if (!mockup) return;

            const fields = mockup.querySelectorAll('.form-field');
            const checks = mockup.querySelectorAll('.validation-check');
            const toggleOptions = mockup.querySelectorAll('.toggle-option');

            // Initial animation: fields and checks appear once
            mockup.classList.add('animating');
            fields.forEach(f => f.classList.remove('field-visible'));
            checks.forEach(c => c.classList.remove('check-visible'));

            fields.forEach((field, i) => {
                t(() => field.classList.add('field-visible'), 300 + i * 400);
            });

            checks.forEach((check, i) => {
                t(() => check.classList.add('check-visible'), 2400 + i * 300);
            });

            // After initial build, cycle only the expense/revenue toggle
            function toggleCycle() {
                // Flip to Revenue
                t(() => {
                    toggleOptions.forEach(opt => opt.classList.toggle('active'));
                    const amountField = fields[1]?.querySelector('.field-value');
                    if (amountField) amountField.textContent = '$120.50';
                    const catField = fields[2]?.querySelector('.field-value');
                    if (catField) catField.textContent = 'Office Supplies';
                }, 0);

                // Flip back to Expense
                t(() => {
                    toggleOptions.forEach(opt => opt.classList.toggle('active'));
                    const amountField = fields[1]?.querySelector('.field-value');
                    if (amountField) amountField.textContent = '$85.00';
                    const catField = fields[2]?.querySelector('.field-value');
                    if (catField) catField.textContent = 'Books';

                    // Continue cycling
                    t(() => toggleCycle(), 5000);
                }, 5000);
            }

            // Start toggle cycling after initial animation settles
            t(() => toggleCycle(), 5000);
        }

        // Predictive Analytics animation
        function animatePredictive(t) {
            const mockup = document.querySelector('.chart-mockup');
            if (!mockup) return;

            function runCycle() {
                const svg = mockup.querySelector('.forecast-chart');
                const paths = svg.querySelectorAll('path');
                const circle = svg.querySelector('circle');
                const badge = mockup.querySelector('.prediction-badge');

                mockup.classList.add('animating');

                // Add draw classes to SVG elements
                if (paths[0]) paths[0].classList.add('draw-fill');   // gradient fill
                if (paths[1]) paths[1].classList.add('draw-line');   // main line
                if (paths[2]) paths[2].classList.add('draw-dashed'); // dashed forecast
                if (circle) circle.classList.add('draw-dot');

                // Show prediction badge after dashed line finishes
                t(() => {
                    if (badge) badge.classList.add('badge-visible');
                }, 3500);

                // Hold and restart
                t(() => {
                    mockup.classList.remove('animating');
                    [paths[0], paths[1], paths[2]].forEach(p => {
                        if (p) p.className = '';
                    });
                    if (circle) circle.className = '';
                    if (badge) badge.classList.remove('badge-visible');
                    t(() => runCycle(), 300);
                }, 6000);
            }

            runCycle();
        }

        // Inventory Management animation
        function animateInventory(t) {
            const mockup = document.querySelector('.inventory-mockup');
            if (!mockup) return;

            function runCycle() {
                const items = mockup.querySelectorAll('.inventory-item');
                const bars = mockup.querySelectorAll('.item-bar');

                mockup.classList.add('animating');

                // Stagger items appearing
                items.forEach((item, i) => {
                    t(() => item.classList.add('item-visible'), 200 + i * 300);
                });

                // Fill bars after items visible
                bars.forEach((bar, i) => {
                    t(() => bar.classList.add('bar-fill'), 1200 + i * 200);
                });

                // Hold and restart
                t(() => {
                    mockup.classList.remove('animating');
                    items.forEach(item => {
                        item.classList.remove('item-visible');
                    });
                    bars.forEach(bar => bar.classList.remove('bar-fill'));
                    t(() => runCycle(), 300);
                }, 6000);
            }

            runCycle();
        }

        // Rental Management animation
        function animateRental(t) {
            const mockup = document.querySelector('.calendar-mockup');
            if (!mockup) return;

            function runCycle() {
                const days = mockup.querySelectorAll('.cal-day:not(.header)');
                const bookedDays = mockup.querySelectorAll('.cal-day.booked');
                const availDays = mockup.querySelectorAll('.cal-day.available');

                mockup.classList.add('animating');

                // Fade in days row by row (7 per row)
                days.forEach((day, i) => {
                    const row = Math.floor(i / 7);
                    t(() => day.classList.add('day-visible'), 200 + row * 250);
                });

                // Highlight booked days
                bookedDays.forEach((day, i) => {
                    t(() => day.classList.add('day-highlight'), 1200 + i * 200);
                });

                // Highlight available days
                availDays.forEach((day, i) => {
                    t(() => day.classList.add('day-highlight'), 2000 + i * 200);
                });

                // Hold and restart
                t(() => {
                    mockup.classList.remove('animating');
                    days.forEach(d => d.classList.remove('day-visible', 'day-highlight'));
                    t(() => runCycle(), 300);
                }, 5500);
            }

            runCycle();
        }

        // Customer Management animation
        function animateCustomers(t) {
            const mockup = document.querySelector('.customers-mockup');
            if (!mockup) return;

            function animateCounter(el, target, prefix, duration) {
                const start = 0;
                const startTime = performance.now();
                function update(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(start + (target - start) * eased);
                    el.textContent = prefix + current.toLocaleString();
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            function runCycle() {
                const cards = mockup.querySelectorAll('.customer-card');

                mockup.classList.add('animating');

                // Slide in cards
                cards.forEach((card, i) => {
                    t(() => {
                        card.classList.add('card-visible');

                        // Count up stats after card lands
                        t(() => {
                            const statVals = card.querySelectorAll('.stat-val');
                            statVals.forEach(sv => {
                                sv.classList.add('counting');
                                const text = sv.textContent;
                                if (text.startsWith('$')) {
                                    const num = parseInt(text.replace(/[$,]/g, ''));
                                    animateCounter(sv, num, '$', 1000);
                                } else {
                                    const num = parseInt(text);
                                    animateCounter(sv, num, '', 800);
                                }
                            });
                        }, 500);
                    }, 300 + i * 500);
                });

                // Hold and restart
                t(() => {
                    mockup.classList.remove('animating');
                    cards.forEach(card => card.classList.remove('card-visible'));
                    const statVals = mockup.querySelectorAll('.stat-val');
                    statVals.forEach(sv => {
                        sv.classList.remove('counting');
                        // Restore original values
                    });
                    // Reset original stat values
                    const firstStats = cards[0]?.querySelectorAll('.stat-val');
                    if (firstStats) {
                        firstStats[0].textContent = '$4,230';
                        firstStats[1].textContent = '12';
                    }
                    const secondStats = cards[1]?.querySelectorAll('.stat-val');
                    if (secondStats) {
                        secondStats[0].textContent = '$2,890';
                        secondStats[1].textContent = '8';
                    }
                    t(() => runCycle(), 300);
                }, 5500);
            }

            runCycle();
        }

        // Invoicing animation
        function animateInvoices(t) {
            const mockup = document.querySelector('.invoice-mockup');
            if (!mockup) return;

            function animateCounter(el, target, prefix, suffix, duration) {
                const startTime = performance.now();
                function update(now) {
                    const elapsed = now - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = (target * eased).toFixed(2);
                    el.textContent = prefix + parseFloat(current).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + suffix;
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
            }

            function runCycle() {
                const header = mockup.querySelector('.invoice-header');
                const meta = mockup.querySelector('.invoice-meta');
                const total = mockup.querySelector('.invoice-total');
                const status = mockup.querySelector('.invoice-status');
                const totalVal = mockup.querySelector('.total-value');

                mockup.classList.add('animating');

                // Sequential build-up
                t(() => { if (header) header.classList.add('section-visible'); }, 300);
                t(() => { if (meta) meta.classList.add('section-visible'); }, 700);
                t(() => {
                    if (total) total.classList.add('section-visible');
                    // Count up total
                    if (totalVal) animateCounter(totalVal, 1234, '$', '', 1000);
                }, 1100);

                // Stamp in paid badge
                t(() => {
                    if (status) status.classList.add('stamp-visible');
                }, 2300);

                // Hold and restart
                t(() => {
                    mockup.classList.remove('animating');
                    [header, meta, total].forEach(el => {
                        if (el) el.classList.remove('section-visible');
                    });
                    if (status) status.classList.remove('stamp-visible');
                    if (totalVal) totalVal.textContent = '$1,234.00';
                    t(() => runCycle(), 300);
                }, 5800);
            }

            runCycle();
        }

        // AI Import flow animation
        const aiImportDemo = document.getElementById('aiImportDemo');
        if (aiImportDemo) {
            let aiAnimationStarted = false;
            const particleColors = ['#3b82f6', '#60a5fa', '#2563eb', '#93c5fd'];

            function spawnParticles(container, direction, count) {
                for (let i = 0; i < count; i++) {
                    setTimeout(() => {
                        const p = document.createElement('div');
                        p.className = 'ai-particle particle-' + direction;
                        p.style.background = particleColors[Math.floor(Math.random() * particleColors.length)];
                        p.style.marginTop = (Math.random() * 20 - 10) + 'px';
                        container.appendChild(p);
                        setTimeout(() => p.remove(), 1300);
                    }, i * 120);
                }
            }

            function runAiImportAnimation() {
                const hub = document.getElementById('aiFlowHub');
                const hubLabel = document.getElementById('aiHubLabel');
                const hubRing = hub.querySelector('.ai-flow-hub-ring');
                const progress = hub.querySelector('.ai-hub-progress');
                const hubIcon = hub.querySelector('.ai-hub-icon');
                const fields = document.querySelectorAll('#aiFlowFields .ai-flow-field');
                const footer = document.getElementById('aiFlowFooter');
                const badge = document.getElementById('aiMatchBadge');
                const particles = document.getElementById('aiParticles');
                const connLeft = document.querySelector('.ai-flow-connector-left');
                const connRight = document.querySelector('.ai-flow-connector-right');
                const sourceRows = document.querySelectorAll('.ai-flow-row[data-row]');

                // Reset
                progress.style.transition = 'none';
                progress.style.strokeDashoffset = '226';
                hubLabel.textContent = 'Analyzing...';
                hubLabel.classList.remove('complete');
                hubIcon.classList.remove('complete');
                hubRing.classList.remove('active', 'complete');
                fields.forEach(f => f.classList.remove('visible'));
                footer.classList.remove('visible');
                badge.textContent = '0/4';
                badge.classList.remove('complete');
                connLeft.classList.remove('active');
                connRight.classList.remove('active');
                sourceRows.forEach(r => r.classList.remove('ai-flow-row-highlight'));

                // Step 1: Activate connectors + start progress ring + glow (0.3s)
                setTimeout(() => {
                    connLeft.classList.add('active');
                    hubRing.classList.add('active');
                    spawnParticles(particles, 'left', 8);
                    requestAnimationFrame(() => {
                        progress.style.transition = '';
                        requestAnimationFrame(() => {
                            progress.style.strokeDashoffset = '0';
                        });
                    });
                }, 300);

                // Step 2: Highlight source rows one by one (0.6s, 1.0s, 1.4s)
                sourceRows.forEach((row, i) => {
                    setTimeout(() => {
                        row.classList.add('ai-flow-row-highlight');
                    }, 600 + i * 400);
                });

                // Step 3: Hub complete + right connector + full glow (2.2s)
                setTimeout(() => {
                    hubLabel.textContent = 'Imported!';
                    hubLabel.classList.add('complete');
                    hubIcon.classList.add('complete');
                    hubRing.classList.add('complete');
                    connRight.classList.add('active');
                    spawnParticles(particles, 'right', 8);
                }, 2200);

                // Step 4: Reveal destination fields one by one (2.6s+)
                fields.forEach((field, i) => {
                    setTimeout(() => {
                        field.classList.add('visible');
                        badge.textContent = (i + 1) + '/4';
                    }, 2600 + i * 350);
                });

                // Step 5: Show footer + mark complete (4.2s)
                setTimeout(() => {
                    footer.classList.add('visible');
                    badge.classList.add('complete');
                }, 4200);

                // Step 6: Hold, then restart (8.5s)
                setTimeout(() => {
                    runAiImportAnimation();
                }, 8500);
            }

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

                // Step 3: Mark complete (7.6s — when last row finishes scrambling)
                setTimeout(() => {
                    bar.classList.add('done');
                    label.textContent = 'Encryption complete — stored locally';
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
