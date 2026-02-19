<?php
session_start();
require_once 'community/users/user_functions.php';
require_once 'track_referral.php';
require_once 'statistics.php';
require_once __DIR__ . '/config/pricing.php';
require_once __DIR__ . '/resources/icons.php';

$pricing = get_pricing_config();
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
        content="Transform your small business with Argo Books. AI-powered receipt scanning, predictive analytics, inventory management and more. Free software.">
    <meta name="keywords"
        content="AI receipt scanning, predictive analytics, business software, inventory management, rental management, invoice generator, small business automation">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books - AI-Powered Business Management">
    <meta property="og:description"
        content="Transform your business with AI receipt scanning, predictive analytics, inventory management and automated invoicing.">
    <meta property="og:url" content="https://argorobots.com/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books - AI-Powered Business Management">
    <meta name="twitter:description"
        content="Transform your business with AI receipt scanning, predictive analytics, and automated business management.">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Saskatoon">
    <meta name="geo.position" content="52.1579;-106.6702">
    <meta name="ICBM" content="52.1579, -106.6702">

     <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/">

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
            "description": "AI-powered business management software with receipt scanning, predictive analytics, and inventory management",
            "url": "https://argorobots.com/",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows",
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

    <link rel="shortcut icon" type="image/x-icon" href="resources/images/argo-logo/A-logo.ico">
    <title>Argo Books - AI-Powered Business Management Software</title>

    <script src="resources/scripts/jquery-3.6.0.js"></script>
    <script src="resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="resources/styles/custom-colors.css">
    <link rel="stylesheet" href="resources/styles/button.css">
    <link rel="stylesheet" href="resources/styles/faq.css">
    <link rel="stylesheet" href="resources/header/style.css">
    <link rel="stylesheet" href="resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

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
                    <a href="#features" class="btn btn-secondary btn-lg">
                        <span>See Features</span>
                    </a>
                </div>
            </div>
            <div class="hero-visual animate-fade-in-up delay-2">
                <div class="hero-device">
                    <div class="device-frame">
                        <img src="resources/images/main.webp" alt="Argo Books Dashboard" class="device-screen">
                    </div>
                    <div class="floating-card floating-card-1 animate-float">
                        <div class="floating-card-icon">
                            <?= svg_icon('loading', 24) ?>
                        </div>
                        <div class="floating-card-content">
                            <span class="floating-card-label">AI Scanning</span>
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

    <!-- Trusted By Section -->
    <!-- <section class="trusted-section">
        <div class="container">
            <p class="trusted-label animate-on-scroll">Trusted by businesses across Canada</p>
            <div class="trusted-logos animate-on-scroll">
                <div class="trusted-logo">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="currentColor" opacity="0.5">
                        <text x="0" y="28" font-size="14" font-weight="600">Small Retail</text>
                    </svg>
                </div>
                <div class="trusted-logo">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="currentColor" opacity="0.5">
                        <text x="0" y="28" font-size="14" font-weight="600">E-commerce</text>
                    </svg>
                </div>
                <div class="trusted-logo">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="currentColor" opacity="0.5">
                        <text x="0" y="28" font-size="14" font-weight="600">Services</text>
                    </svg>
                </div>
                <div class="trusted-logo">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="currentColor" opacity="0.5">
                        <text x="0" y="28" font-size="14" font-weight="600">Rental</text>
                    </svg>
                </div>
                <div class="trusted-logo">
                    <svg width="120" height="40" viewBox="0 0 120 40" fill="currentColor" opacity="0.5">
                        <text x="0" y="28" font-size="14" font-weight="600">Wholesale</text>
                    </svg>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Features Tabbed Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">Features</span>
                <h2 class="section-title">Everything you need to grow</h2>
                <p class="section-description">Easy-to-use accounting software with AI-powered receipt scanning, predictive analytics, and inventory management. Everything you need to run your business.</p>
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
                    <!-- Payment System tab - TEMPORARILY DISABLED
                    <button class="tab-btn" data-tab="payments">
                        <div class="tab-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Payment System</span>
                            <span class="tab-subtitle">Get paid faster</span>
                        </div>
                    </button>
                    -->
                </div>

                <div class="features-tabs-content">
                    <!-- AI Receipt Scanning -->
                    <div class="tab-content active" id="tab-ai-receipts">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Scan receipts with AI</h3>
                                <p>Take a photo of any receipt with your phone or upload from your computer. Our AI automatically extracts vendor, date, amount, and line items with 98% accuracy.</p>
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
                                                        <div class="skeleton-text w-60"></div>
                                                        <div class="skeleton-text w-40"></div>
                                                    </div>
                                                    <div class="receipt-items">
                                                        <div class="receipt-item">
                                                            <div class="skeleton-text w-70"></div>
                                                            <div class="skeleton-text w-20"></div>
                                                        </div>
                                                        <div class="receipt-item">
                                                            <div class="skeleton-text w-50"></div>
                                                            <div class="skeleton-text w-20"></div>
                                                        </div>
                                                        <div class="receipt-item">
                                                            <div class="skeleton-text w-60"></div>
                                                            <div class="skeleton-text w-20"></div>
                                                        </div>
                                                    </div>
                                                    <div class="receipt-total">
                                                        <span>Total</span>
                                                        <span>$127.43</span>
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
                                        <div class="expense-form-preview">
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
                                                <path d="M0,120 Q50,100 100,80 T200,50 T300,20 L300,150 L0,150 Z" fill="url(#chartGradient)"/>
                                                <path d="M0,120 Q50,100 100,80 T200,50 T300,20" fill="none" stroke="#3b82f6" stroke-width="3"/>
                                                <path d="M200,50 Q250,30 300,20" fill="none" stroke="#3b82f6" stroke-width="3" stroke-dasharray="5,5" opacity="0.5"/>
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
                                        <div class="invoice-preview">
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

                    <!-- Payment System - TEMPORARILY DISABLED
                    <div class="tab-content" id="tab-payments">
                        <div class="tab-content-inner">
                            <div class="tab-content-text">
                                <h3>Accept payments anywhere</h3>
                                <p>Let customers pay invoices online with credit cards or bank transfers. Integrated with Stripe, PayPal, and Square for seamless transactions.</p>
                                <ul class="feature-list">
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Online payment links</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Multiple payment methods</span>
                                    </li>
                                    <li>
                                        <?= svg_icon('check', 20) ?>
                                        <span>Automatic reconciliation</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content-visual">
                                <div class="feature-visual-card">
                                    <div class="visual-mockup payments-mockup">
                                        <div class="payment-providers">
                                            <div class="provider-card">
                                                <img src="resources/images/Stripe-logo.svg" alt="Stripe" class="provider-logo">
                                            </div>
                                            <div class="provider-card">
                                                <img src="resources/images/PayPal-logo.svg" alt="PayPal" class="provider-logo">
                                            </div>
                                            <div class="provider-card">
                                                <img src="resources/images/Square-logo.svg" alt="Square" class="provider-logo">
                                            </div>
                                        </div>
                                        <div class="payment-success">
                                            <div class="success-icon">
                                                <?= svg_icon('circle-check', 32) ?>
                                            </div>
                                            <span class="success-text">Payment Received</span>
                                            <span class="success-amount">$1,234.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">How It Works</span>
                <h2 class="section-title">Up and running in minutes</h2>
                <p class="section-description">Getting started with Argo Books is simple. No complicated setup, no learning curve.</p>
            </div>

            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <div class="step-icon">
                        <?= svg_icon('download', 32) ?>
                    </div>
                    <h3>Download</h3>
                    <p>Get Argo Books for free. Installation takes less than 2 minutes.</p>
                </div>
                <div class="step-connector animate-on-scroll"></div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <div class="step-icon">
                        <?= svg_icon('pencil', 32) ?>
                    </div>
                    <h3>Set Up</h3>
                    <p>Add your products, customers, and preferences. Import existing data if you have it.</p>
                </div>
                <div class="step-connector animate-on-scroll"></div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <?= svg_icon('trending-up', 32) ?>
                    </div>
                    <h3>Grow</h3>
                    <p>Start tracking sales, managing inventory, and watching your business insights grow.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Experience Section - TEMPORARILY DISABLED
    <section class="mobile-section">
        <div class="container">
            <div class="mobile-content">
                <div class="mobile-visual animate-on-scroll">
                    <div class="mobile-phones">
                        <div class="mobile-phone phone-1">
                            <div class="mobile-phone-screen">
                                <div class="mobile-screen-placeholder">
                                    <div class="mobile-screen-header">
                                        <div class="mobile-screen-logo">A</div>
                                        <span class="mobile-screen-title">Argo Books</span>
                                    </div>
                                    <div class="mobile-screen-stats">
                                        <div class="mobile-stat-card">
                                            <span class="stat-label">Today's Sales</span>
                                            <span class="stat-value">$2,450</span>
                                        </div>
                                        <div class="mobile-stat-card">
                                            <span class="stat-label">Growth</span>
                                            <span class="stat-value positive">+18%</span>
                                        </div>
                                    </div>
                                    <div class="mobile-screen-chart">
                                        <span class="mobile-chart-title">Weekly Revenue</span>
                                        <div class="mobile-chart-bars">
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mobile-phone phone-2">
                            <div class="mobile-phone-screen">
                                <div class="mobile-screen-placeholder">
                                    <div class="mobile-screen-header">
                                        <div class="mobile-screen-logo">Y</div>
                                        <span class="mobile-screen-title">Your Company</span>
                                    </div>
                                    <div class="mobile-screen-stats">
                                        <div class="mobile-stat-card">
                                            <span class="stat-label">Scanned</span>
                                            <span class="stat-value">24</span>
                                        </div>
                                        <div class="mobile-stat-card">
                                            <span class="stat-label">This Month</span>
                                            <span class="stat-value">$890</span>
                                        </div>
                                    </div>
                                    <div class="mobile-screen-chart">
                                        <span class="mobile-chart-title">Recent Receipts</span>
                                        <div class="mobile-chart-bars">
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                            <div class="mobile-chart-bar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mobile-text animate-on-scroll">
                    <span class="section-tag">Mobile App</span>
                    <h2 class="section-title text-left">Your business in your pocket</h2>
                    <p class="section-description text-left">Access your business data anywhere with the Argo Books mobile app. Scan receipts on the go, check real-time analytics, and stay connected to your business 24/7.</p>

                    <div class="mobile-features">
                        <div class="mobile-feature-item">
                            <div class="mobile-feature-icon">
                                <?= svg_icon('receipt-scan', 24) ?>
                            </div>
                            <div class="mobile-feature-text">
                                <h4>Snap & Scan Receipts</h4>
                                <p>Take a photo of any receipt and let AI extract all the details automatically</p>
                            </div>
                        </div>
                        <div class="mobile-feature-item">
                            <div class="mobile-feature-icon">
                                <?= svg_icon('analytics', 24) ?>
                            </div>
                            <div class="mobile-feature-text">
                                <h4>Real-time Dashboard</h4>
                                <p>Monitor sales, inventory, and key metrics wherever you are</p>
                            </div>
                        </div>
                        <div class="mobile-feature-item">
                            <div class="mobile-feature-icon">
                                <?= svg_icon('chat', 24) ?>
                            </div>
                            <div class="mobile-feature-text">
                                <h4>Instant Notifications</h4>
                                <p>Get alerts for low stock, large orders, and important business events</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    -->

    <!-- Security Section -->
    <section class="security-section">
        <div class="container">
            <div class="security-content">
                <div class="security-text animate-on-scroll">
                    <span class="section-tag">Security</span>
                    <h2 class="section-title text-left">Your data, protected</h2>
                    <p class="section-description text-left">We take security seriously. Your business data is encrypted with the same technology used by banks and government agencies.</p>

                    <div class="security-features">
                        <div class="security-item">
                            <div class="security-icon">
                                <?= svg_icon('shield', 24) ?>
                            </div>
                            <div class="security-detail">
                                <h4>AES-256 Encryption</h4>
                                <p>Bank-grade encryption protects all your data</p>
                            </div>
                        </div>
                        <div class="security-item">
                            <div class="security-icon">
                                <?= svg_icon('lock', 24) ?>
                            </div>
                            <div class="security-detail">
                                <h4>Local Storage</h4>
                                <p>Your data stays on your computer, not in the cloud</p>
                            </div>
                        </div>
                        <div class="security-item">
                            <div class="security-icon">
                                <?= svg_icon('biometric-clock', 24) ?>
                            </div>
                            <div class="security-detail">
                                <h4>Biometric Login</h4>
                                <p>Fingerprint and face unlock for quick, secure access</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="security-visual animate-on-scroll">
                    <div class="shield-graphic">
                        <svg viewBox="0 0 200 240" class="shield-svg">
                            <defs>
                                <linearGradient id="shieldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3b82f6"/>
                                    <stop offset="100%" style="stop-color:#1d4ed8"/>
                                </linearGradient>
                            </defs>
                            <path d="M100 10 L180 40 L180 100 Q180 160 100 220 Q20 160 20 100 L20 40 Z" fill="url(#shieldGradient)" opacity="0.1"/>
                            <path d="M100 30 L160 55 L160 100 Q160 145 100 195 Q40 145 40 100 L40 55 Z" fill="url(#shieldGradient)" opacity="0.2"/>
                            <path d="M100 50 L140 70 L140 100 Q140 130 100 170 Q60 130 60 100 L60 70 Z" fill="url(#shieldGradient)"/>
                            <polyline points="75,110 95,130 130,90" fill="none" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
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
                <h2 class="section-title">Simple, transparent pricing</h2>
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
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Up to 10 products</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Unlimited transactions</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Real-time analytics</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Receipt management</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Core features</span>
                        </li>
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
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Everything in Free</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Unlimited products</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Biometric login security</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Invoices & payments</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>AI receipt scanning <span>(500/month)</span></span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Predictive analytics</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Priority support</span>
                        </li>
                    </ul>
                    <a href="upgrade/premium/" class="btn btn-ai btn-block">Subscribe to Premium</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">FAQ</span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-description">Everything you need to know about Argo Books. Can't find the answer you're looking for? Feel free to contact us.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>What platforms does Argo Books support?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Argo Books is available for Windows, macOS, and Linux.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Is my data secure?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Absolutely. Your data is stored locally on your computer using AES-256 encryption - the same standard used by banks and government agencies. We don't store your business data on our servers. Premium users can also enable Windows Hello for biometric authentication.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>How does AI receipt scanning work?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Simply take a photo of your receipt with your phone or upload an image. Our AI analyzes the receipt and automatically extracts the vendor name, date, individual line items, taxes, and total amount. The extracted data is then added to your expense records with 98% accuracy.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Can I try Argo Books for free?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes! Argo Books has a free tier that includes up to 10 products, unlimited transactions, real-time analytics, and receipt management. No credit card required to get started. You can upgrade whenever you're ready.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>What's included in the Premium subscription?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>The Premium subscription ($<?php echo number_format($monthlyPrice, 0); ?>/month or $<?php echo number_format($yearlyPrice, 0); ?>/year) includes invoices & payments, AI-powered receipt scanning, and predictive analytics. These features use advanced machine learning to help you understand your business better and make smarter decisions.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>What is your refund policy?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>You can cancel your Premium subscription at any time. Your Premium features will remain active until the end of your current billing period. If you believe you are entitled to a refund, please contact us and we'll review your request. <a href="legal/refund.php" class="link">View full refund policy</a></p>
                        </div>
                    </div>
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
                    Contact Form
                </a>
            </div>
        </div>
    </section>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content animate-on-scroll">
                    <h2>Ready to transform your business?</h2>
                    <p>Join thousands of businesses using Argo Books to save time, reduce errors, and grow smarter.</p>
                    <div class="cta-buttons">
                        <a href="downloads" class="btn btn-white btn-lg">
                            <span>Get Started Free</span>
                            <?= svg_icon('arrow-right', 20) ?>
                        </a>
                        <a href="upgrade/" class="btn btn-outline-white btn-lg">
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

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const tabId = btn.dataset.tab;

                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                btn.classList.add('active');
                document.getElementById('tab-' + tabId).classList.add('active');
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

        function runScanCycle() {
            if (!scanAnimation || !aiBadge || !scanLine) return;

            // Reset to scanning state
            scanAnimation.classList.remove('scan-complete');
            aiBadge.classList.remove('complete');

            // Reset scan line animation
            scanLine.style.animation = 'none';
            scanLine.offsetHeight; // Trigger reflow
            scanLine.style.animation = 'scanLine 1.5s ease-in-out 3';

            // After 3 scans (4.5 seconds), show complete state
            setTimeout(() => {
                scanAnimation.classList.add('scan-complete');
                aiBadge.classList.add('complete');

                // After showing complete for 3 seconds, restart the cycle
                setTimeout(() => {
                    runScanCycle();
                }, 3000);
            }, 4500);
        }

        // Start the scan animation cycle
        if (scanAnimation) {
            runScanCycle();
        }

        // FAQ Accordion
        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                // Toggle current item without closing others
                item.classList.toggle('active');
            });
        });
    });
    </script>
</body>

</html>
