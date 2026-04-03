<?php
session_start();
require_once 'community/users/user_functions.php';
require_once 'track_referral.php';
require_once 'statistics.php';
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
        content="Transform your small business with Argo Books. AI-powered receipt scanning, AI spreadsheet import, predictive analytics, inventory management and more. Free software.">
    <meta name="keywords"
        content="AI receipt scanning, AI spreadsheet import, predictive analytics, business software, inventory management, rental management, invoice generator, small business automation, data import">

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
            "description": "AI-powered business management software with receipt scanning, predictive analytics, and inventory management",
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

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "What is Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is desktop accounting and business management software for small businesses, freelancers, and self-employed professionals. It lets you track income and expenses, create professional invoices, scan receipts with AI, manage products and inventory, and run real-time financial reports — all from your own computer. Unlike cloud-only tools, your data stays on your machine, giving you full control and privacy."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Download and install Argo Books on Windows, macOS, or Linux — no account required to get started. From the dashboard you can add products, record transactions, generate invoices, and scan receipts using AI. Your data is stored locally with AES-256 encryption, and real-time analytics give you an instant snapshot of your business health. Premium users unlock predictive analytics, unlimited invoicing, and more."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I use Argo Books for free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes — Argo Books has a free tier that you can use forever, no credit card required. The Free plan includes all core features: unlimited products, unlimited transactions, real-time analytics, receipt management, 25 invoices per month, 5 AI receipt scans per month, and 100 AI spreadsheet imports per month. When your business is ready for more, you can upgrade to Premium at any time."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is my business data secure?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Your data is stored locally on your computer — not on a remote server — so you always have full control. Argo Books uses AES-256-GCM encryption, the same standard used by banks and government agencies. Premium users can also enable biometric login via Windows Hello for an extra layer of security. We never store your business data on our servers."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Which plan is right for me — Free or Premium?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "The Free plan is ideal if you're just getting started or run a small operation. It gives you everything you need to manage products, track income and expenses, and generate basic reports. Upgrade to Premium if you need unlimited invoicing, higher AI scanning limits, predictive analytics, biometric security, and priority support. Premium is available monthly or yearly with savings on the annual plan."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. It's a desktop application, so it works offline without an internet connection. You only need connectivity for features like AI receipt scanning, license activation, and software updates."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books offer customer support?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "All users have access to our documentation and community forum where you can ask questions, report bugs, and request features. Premium subscribers get priority support with faster response times. You can also reach us directly through our contact page — we're a small team and we read every message."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="resources/images/argo-logo/argo-icon.ico">
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

    <!-- Features Tabbed Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">Features</span>
                <h2 class="section-title">Everything you need to grow</h2>
                <p class="section-description">Easy-to-use accounting software with AI-powered receipt scanning, smart spreadsheet import, predictive analytics, and inventory management. Everything you need to run your business.</p>
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
                                <h3>Scan receipts with AI</h3>
                                <p>Take a photo of any receipt with your phone or upload from your computer. Our AI automatically extracts supplier, date, amount, and line items with 99% accuracy.</p>
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

                </div>
            </div>
        </div>
    </section>

    <!-- AI Spreadsheet Import Section -->
    <section class="ai-import-section">
        <div class="container">
            <div class="ai-import-content">
                <div class="ai-import-text animate-on-scroll">
                    <span class="section-tag">AI-Powered</span>
                    <h2 class="section-title text-left">Import any spreadsheet — instantly</h2>
                    <p class="section-description text-left">Just upload your spreadsheet — our AI figures out what each column means and puts everything in the right place for you.</p>

                    <div class="ai-import-features">
                        <div class="ai-import-feature">
                            <div class="ai-feature-icon">
                                <?= svg_icon('bolt', 24) ?>
                            </div>
                            <div class="ai-feature-detail">
                                <h4>Instant Column Detection</h4>
                                <p>AI recognizes names, emails, phone numbers, dates, currencies, and more — regardless of column names</p>
                            </div>
                        </div>
                        <div class="ai-import-feature">
                            <div class="ai-feature-icon">
                                <?= svg_icon('table', 24) ?>
                            </div>
                            <div class="ai-feature-detail">
                                <h4>Any Format, Any Layout</h4>
                                <p>Works with messy spreadsheets, unusual column names, and mixed data — no reformatting needed</p>
                            </div>
                        </div>
                        <div class="ai-import-feature">
                            <div class="ai-feature-icon">
                                <?= svg_icon('shield', 24) ?>
                            </div>
                            <div class="ai-feature-detail">
                                <h4>Private &amp; Secure</h4>
                                <p>Your data is encrypted and processed securely — it's never stored or used for training</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ai-import-visual animate-on-scroll">
                    <div class="ai-import-demo" id="aiImportDemo">
                        <!-- Spreadsheet source -->
                        <div class="demo-spreadsheet">
                            <div class="demo-spreadsheet-header">
                                <div class="demo-file-icon"><?= svg_icon('table', 16) ?></div>
                                <span class="demo-file-name">bill_export.xlsx</span>
                                <span class="demo-file-badge">Excel</span>
                            </div>
                            <div class="demo-spreadsheet-table">
                                <div class="demo-table-row demo-table-header-row">
                                    <span>Supplier</span>
                                    <span>Amt Owed</span>
                                    <span>Pay By</span>
                                    <span>Memo</span>
                                </div>
                                <div class="demo-table-row">
                                    <span>Acme Supply Co</span>
                                    <span>$4,280.00</span>
                                    <span>12/15/2025</span>
                                    <span>INV-3847</span>
                                </div>
                                <div class="demo-table-row">
                                    <span>TechFlow LLC</span>
                                    <span>$950.50</span>
                                    <span>01/02/2026</span>
                                    <span>PO-9912</span>
                                </div>
                                <div class="demo-table-row demo-table-faded">
                                    <span>NovaCorp Int'l</span>
                                    <span>$12,100.00</span>
                                    <span>11/30/2025</span>
                                    <span>Contract #441</span>
                                </div>
                            </div>
                            <div class="demo-row-count">384 more rows...</div>
                        </div>

                        <!-- AI Processing indicator -->
                        <div class="demo-ai-processor" id="aiProcessor">
                            <div class="ai-processor-ring">
                                <svg viewBox="0 0 48 48" class="processor-ring-svg">
                                    <circle cx="24" cy="24" r="20" fill="none" stroke="#e2e8f0" stroke-width="3"/>
                                    <circle cx="24" cy="24" r="20" fill="none" stroke="url(#aiGradient)" stroke-width="3" stroke-dasharray="126" stroke-dashoffset="126" stroke-linecap="round" class="processor-progress"/>
                                    <defs>
                                        <linearGradient id="aiGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#6366f1"/>
                                            <stop offset="100%" style="stop-color:#8b5cf6"/>
                                        </linearGradient>
                                    </defs>
                                </svg>
                                <span class="processor-label">AI</span>
                            </div>
                            <span class="processor-text">Mapping columns...</span>
                        </div>

                        <!-- Mapping results -->
                        <div class="demo-mapping-results" id="aiMappingResults">
                            <div class="demo-mapping-header">
                                <span class="demo-mapping-title">Mapped Fields</span>
                                <span class="demo-mapping-badge">4/4 matched</span>
                            </div>
                            <div class="demo-mapping-row" data-delay="0">
                                <div class="demo-map-source">Supplier</div>
                                <div class="demo-map-arrow">
                                    <svg width="20" height="12" viewBox="0 0 20 12"><path d="M0 6h16M13 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="demo-map-target">Supplier Name</div>
                                <div class="demo-map-confidence">97%</div>
                            </div>
                            <div class="demo-mapping-row" data-delay="1">
                                <div class="demo-map-source">Amt Owed</div>
                                <div class="demo-map-arrow">
                                    <svg width="20" height="12" viewBox="0 0 20 12"><path d="M0 6h16M13 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="demo-map-target">Balance Due</div>
                                <div class="demo-map-confidence">93%</div>
                            </div>
                            <div class="demo-mapping-row" data-delay="2">
                                <div class="demo-map-source">Pay By</div>
                                <div class="demo-map-arrow">
                                    <svg width="20" height="12" viewBox="0 0 20 12"><path d="M0 6h16M13 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="demo-map-target">Due Date</div>
                                <div class="demo-map-confidence">91%</div>
                            </div>
                            <div class="demo-mapping-row" data-delay="3">
                                <div class="demo-map-source">Memo</div>
                                <div class="demo-map-arrow">
                                    <svg width="20" height="12" viewBox="0 0 20 12"><path d="M0 6h16M13 1l5 5-5 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="demo-map-target">Reference #</div>
                                <div class="demo-map-confidence">86%</div>
                            </div>
                            <div class="demo-mapping-footer" id="aiMappingFooter">
                                <div class="demo-mapping-success">
                                    <?= svg_icon('check', 16) ?>
                                    <span>Ready to import 387 rows</span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    <p>Add your products, customers, and suppliers. Import existing data from any spreadsheet — our AI handles the formatting automatically.</p>
                </div>
                <div class="step-connector animate-on-scroll"></div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <div class="step-icon">
                        <?= svg_icon('trending-up', 32) ?>
                    </div>
                    <h3>Grow</h3>
                    <p>Start tracking sales, managing inventory, and watch your business grow.</p>
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
                    <img src="resources/images/privacy-local-storage.svg" alt="Your data stays local — encrypted with AES-256, stored offline, no cloud dependency" loading="lazy">
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

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-tag">FAQ</span>
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-description">Everything you need to know about Argo Books. Can't find the answer you're looking for? <a href="contact.php">Get in touch</a> — we read every message.</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>What is Argo Books?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Argo Books is desktop accounting and business management software built for small businesses, freelancers, and self-employed professionals. It lets you track income and expenses, create professional invoices, scan receipts with AI, manage products and inventory, and run real-time financial reports — all from your own computer.</p>
                            <p>Unlike cloud-only tools, your data stays on your machine, giving you full control and privacy. Argo Books is available on Windows, macOS, and Linux.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>How does Argo Books work?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Download and install Argo Books on Windows, macOS, or Linux — no account required to get started. From the dashboard you can add products, record transactions, generate invoices, and scan receipts using AI. Your data is stored locally with AES-256 encryption, and real-time analytics give you an instant snapshot of your business health.</p>
                            <p>Premium users unlock predictive analytics, unlimited invoicing, and priority support. <a href="downloads/">Download Argo Books</a> and start managing your business in minutes.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Can I use Argo Books for free?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes — Argo Books has a free tier that you can use forever, no credit card required. The Free plan includes all core features: unlimited products, unlimited transactions, real-time analytics, receipt management, 25 invoices per month, 5 AI receipt scans per month, and 100 AI spreadsheet imports per month.</p>
                            <p>When your business is ready for more, you can upgrade to Premium at any time. <a href="pricing/">Compare plans</a></p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Is my business data secure?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Your data is stored locally on your computer — not on a remote server — so you always have full control. Argo Books uses AES-256-GCM encryption, the same standard used by banks and government agencies. Premium users can also enable biometric login via Windows Hello for an extra layer of security.</p>
                            <p>We never store your business data on our servers. Your books are yours and yours alone.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Which plan is right for me — Free or Premium?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>The <strong>Free plan</strong> is ideal if you're just getting started or run a small operation. It gives you everything you need to manage products, track income and expenses, and generate basic reports.</p>
                            <p>Upgrade to <strong>Premium</strong> ($<?php echo number_format($monthlyPrice, 0); ?>/month or $<?php echo number_format($yearlyPrice, 0); ?>/year) if you need unlimited invoicing, higher AI scanning limits, predictive analytics, biometric security, and priority support. Save with the annual plan. <a href="pricing/">View pricing details</a></p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>What platforms does Argo Books run on?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Argo Books runs natively on <strong>Windows</strong>, <strong>macOS</strong>, and <strong>Linux</strong>. It's a desktop application, so it works offline without an internet connection. You only need connectivity for features like AI receipt scanning, license activation, and software updates.</p>
                            <p><a href="downloads/">Download for your platform</a></p>
                        </div>
                    </div>
                </div>

                <div class="faq-item animate-on-scroll">
                    <div class="faq-question">
                        <h3>Does Argo Books offer customer support?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>All users have access to our <a href="documentation/">documentation</a> and <a href="community/">community forum</a> where you can ask questions, report bugs, and request features. Premium subscribers get priority support with faster response times.</p>
                            <p>You can also reach us directly through our <a href="contact.php">contact page</a> — we're a small team and we read every message.</p>
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

    </main>

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
            scanLine.style.animation = 'scanLine 2.5s ease-in-out 1 forwards';

            // Highlight each text line as scan passes over it
            const scanDuration = 2500;
            const lineCount = scanTexts.length;
            scanTexts.forEach((el, i) => {
                const highlightAt = (scanDuration / (lineCount + 1)) * (i + 1);
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

        // AI Import demo animation
        const aiImportDemo = document.getElementById('aiImportDemo');
        if (aiImportDemo) {
            let aiAnimationStarted = false;

            function runAiImportAnimation() {
                const processor = document.getElementById('aiProcessor');
                const results = document.getElementById('aiMappingResults');
                const rows = results.querySelectorAll('.demo-mapping-row');
                const footer = document.getElementById('aiMappingFooter');

                // Reset
                processor.classList.remove('active');
                results.classList.remove('active');
                rows.forEach(r => r.classList.remove('visible'));
                footer.classList.remove('visible');
                const progressRing = processor.querySelector('.processor-progress');
                progressRing.style.transition = 'none';
                progressRing.style.strokeDashoffset = '126';
                processor.querySelector('.processor-text').textContent = 'Mapping columns...';

                // Step 1: Show AI processor (0.5s)
                setTimeout(() => {
                    processor.classList.add('active');
                    // Re-enable transition, then animate progress ring
                    requestAnimationFrame(() => {
                        progressRing.style.transition = '';
                        requestAnimationFrame(() => {
                            progressRing.style.strokeDashoffset = '0';
                        });
                    });
                }, 500);

                // Step 2: Show mapping results card (2s)
                setTimeout(() => {
                    processor.querySelector('.processor-text').textContent = 'Complete!';
                    results.classList.add('active');
                }, 2000);

                // Step 3: Reveal rows one by one (2.3s, 2.6s, 2.9s, 3.2s)
                rows.forEach((row, i) => {
                    setTimeout(() => {
                        row.classList.add('visible');
                    }, 2300 + (i * 300));
                });

                // Step 4: Show footer (3.8s)
                setTimeout(() => {
                    footer.classList.add('visible');
                }, 3800);

                // Step 5: Hold for 4s, then restart (7.8s)
                setTimeout(() => {
                    runAiImportAnimation();
                }, 8000);
            }

            const aiObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !aiAnimationStarted) {
                        aiAnimationStarted = true;
                        runAiImportAnimation();
                    }
                });
            }, { threshold: 0.3 });

            aiObserver.observe(aiImportDemo);
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
