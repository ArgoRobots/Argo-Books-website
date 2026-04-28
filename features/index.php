<?php require_once __DIR__ . '/../resources/icons.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Discover Argo Books features: AI receipt scanning, expense tracking, analytics, inventory, invoicing, and more.">
    <meta name="keywords"
        content="Argo Books features, AI receipt scanning, expense tracking software, predictive analytics, inventory management, invoicing software, rental management, customer management, spreadsheet import">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Features — AI-Powered Business Tools | Argo Books">
    <meta property="og:description"
        content="Discover Argo Books features: AI receipt scanning, expense tracking, analytics, inventory, invoicing, and more.">
    <meta property="og:url" content="https://argorobots.com/features/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+Features&subtitle=AI-powered+business+tools+for+small+businesses.+Receipt+scanning%2C+expense+tracking%2C+analytics%2C+and+more.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Features — AI-Powered Business Tools | Argo Books">
    <meta name="twitter:description"
        content="Discover Argo Books features: AI receipt scanning, expense tracking, analytics, inventory, invoicing, and more.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+Features&subtitle=AI-powered+business+tools+for+small+businesses.+Receipt+scanning%2C+expense+tracking%2C+analytics%2C+and+more.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Features — AI-Powered Business Tools | Argo Books</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
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
        </div>
        <div class="container">
            <div class="hero-badge animate-fade-in">
                <?= svg_icon('bolt', 16) ?>
                <span>Features</span>
            </div>
            <h1 class="animate-fade-in">Everything you need to run your business</h1>
            <p class="hero-subtitle animate-fade-in">AI-powered tools that automate bookkeeping, track inventory, generate invoices, and give you real-time insights — so you can focus on growing your business.</p>
            <div class="hero-ctas animate-fade-in">
                <a href="../downloads/" class="btn-cta btn-cta-primary">
                    <span>Get Started Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
                <a href="../pricing/" class="btn-cta btn-cta-outline">
                    <span>View Pricing</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Grid Section -->
    <section class="features-overview">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">What's Included</span>
                <h2 class="section-title">Powerful tools, simple to use</h2>
                <p class="section-desc">From AI-powered receipt scanning to predictive analytics, Argo Books gives you everything you need to manage your finances — without the learning curve.</p>
            </div>
            <div class="features-grid">
                <!-- AI Receipt Scanning -->
                <a href="receipt-scanning/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon blue">
                        <?= svg_icon('receipt-scan-detail', 24) ?>
                    </div>
                    <h3>Receipt Scanning</h3>
                    <p>Snap a photo of any receipt and let AI extract the details automatically. No more manual data entry — just scan, review, and save.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Expense & Revenue Tracking -->
                <a href="expense-revenue-tracking/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon green">
                        <?= svg_icon('dollar', 24) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Track every dollar coming in and going out. Categorize transactions, monitor cash flow, and keep your books accurate with guided forms that prevent mistakes.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Predictive Analytics -->
                <a href="predictive-analytics/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon purple">
                        <?= svg_icon('analytics', 24) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>See what's coming before it happens. Our AI engine analyzes your financial data to forecast trends, spot seasonal patterns, and help you plan ahead.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Inventory Management -->
                <a href="inventory-management/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon amber">
                        <?= svg_icon('package', 24) ?>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Track stock levels in real time, set low-stock alerts, and manage your entire product catalog. Never run out of your best-selling items again.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Rental Management -->
                <a href="rental-management/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon cyan">
                        <?= svg_icon('calendar', 24) ?>
                    </div>
                    <h3>Rental Management</h3>
                    <p>Manage bookings, track rental periods, and handle returns all in one place. Perfect for equipment rental, event supplies, or any rental-based business.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Customer Management -->
                <a href="customer-management/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon red">
                        <?= svg_icon('users', 24) ?>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Keep a complete record of your customers, their purchase history, and contact details. Build stronger relationships with the people who matter most.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Invoicing -->
                <a href="invoicing/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon blue">
                        <?= svg_icon('document', 24) ?>
                    </div>
                    <h3>Invoicing</h3>
                    <p>Create professional invoices in seconds. Customize templates, track payment status, and get paid faster with clean, branded invoices your clients will love.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- AI Spreadsheet Import -->
                <a href="spreadsheet-import/" class="feature-card animate-on-scroll">
                    <div class="feature-card-icon green">
                        <?= svg_icon('document-upload', 24) ?>
                    </div>
                    <h3>Spreadsheet Import</h3>
                    <p>Import data from any spreadsheet format. Our AI automatically maps columns, detects data types, and imports everything cleanly — no manual mapping required.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Argo Books Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why Argo Books</span>
                <h2 class="section-title">Built different, on purpose</h2>
                <p class="section-desc">We built Argo Books for people who want powerful tools without the complexity, the bloated pricing, or the cloud dependency.</p>
            </div>
            <div class="why-grid">
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('check', 28, '', 2.5) ?>
                    </div>
                    <h3>No accounting knowledge needed</h3>
                    <p>Guided forms, smart validation, and a clean interface make it easy for anyone to track finances — even if you've never used accounting software before.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('shield', 28) ?>
                    </div>
                    <h3>Works offline — your data stays local</h3>
                    <p>Argo Books is a desktop app. Your financial data never leaves your computer. No cloud servers, no data sharing, no internet required to get work done.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('dollar', 28) ?>
                    </div>
                    <h3>Free forever — premium for power users</h3>
                    <p>The free version covers the essentials with unlimited products. Premium unlocks AI features, invoicing, and more — all for a fraction of what competitors charge.</p>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to get started?</h2>
                    <p>Download Argo Books for free and see how simple managing your business can be.</p>
                    <div class="cta-buttons">
                        <a href="../downloads/" class="btn-cta btn-cta-primary">
                            <span>Download for Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="../pricing/" class="btn-cta btn-cta-ghost">
                            <span>View Pricing</span>
                        </a>
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
        });
    </script>
</body>

</html>