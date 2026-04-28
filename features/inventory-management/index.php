<?php require_once __DIR__ . '/../../resources/icons.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, stock adjustments, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta name="keywords"
        content="inventory management software, stock tracking, product catalog management, small business inventory, inventory alerts, purchase orders, stock adjustments, warehouse management, reorder points, low stock alerts">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Inventory Management — Argo Books">
    <meta property="og:description"
        content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta property="og:url" content="https://argorobots.com/features/inventory-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Inventory+Management&subtitle=Real-time+stock+tracking%2C+purchase+orders%2C+low-stock+alerts%2C+and+multi-location+support.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Inventory Management — Argo Books">
    <meta name="twitter:description"
        content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Inventory+Management&subtitle=Real-time+stock+tracking%2C+purchase+orders%2C+low-stock+alerts%2C+and+multi-location+support.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/inventory-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Inventory Management", "item": "https://argorobots.com/features/inventory-management/"}
            ]
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
                    "name": "Can Argo Books track inventory across multiple locations?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. You can add unlimited locations — warehouses, stores, offices, or any other facility — and track per-location stock levels, inventory value, and capacity. Everything is visible from a single dashboard, so you always know what you have and where it is."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How do low-stock alerts work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can set a reorder point for each product. When stock drops to that level, Argo Books flags it with a color-coded status badge so you know it's time to restock. No more surprise stockouts — you'll see the warning before it becomes a problem."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I create and manage purchase orders?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Create purchase orders with supplier details and itemized line items directly in Argo Books. When you mark an order as received, stock levels update automatically — no manual adjustments needed. It keeps your inventory accurate without the extra work."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is inventory management included in the Free plan?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Inventory management is a core feature available on both the Free and Premium plans. You get unlimited products, multi-location tracking, low-stock alerts, and purchase orders at no cost. Premium adds predictive analytics to help you forecast demand and plan inventory purchases ahead of time."
                    }
                }
            ]
        }
    </script>

    <!-- SoftwareApplication Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, macOS",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "description": "Free plan available. Premium for $10/month."
            },
            "description": "Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, stock adjustments, and multi-location support.",
            "featureList": "Real-time stock tracking, Low-stock alerts and reorder points, Purchase order management, Multi-location inventory support"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Inventory Management — Argo Books</title>

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>
    <main>

    <!-- =============================================
         HERO SECTION
         ============================================= -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="hero-badge animate-fade-in">
                <?= svg_icon('package', 16) ?>
                <span>Inventory Management</span>
            </div>
            <h1 class="animate-fade-in">Know exactly what you have, where it is, and when to reorder</h1>
            <p class="hero-subtitle animate-fade-in">Track every product across multiple locations with real-time stock levels, automatic reorder alerts, purchase orders, and stock adjustments. Complete inventory control without the complexity.</p>
            <div class="hero-ctas animate-fade-in">
                <a href="../../downloads/" class="btn-cta btn-cta-primary">
                    <span>Get Started Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
                <a href="../../pricing/" class="btn-cta btn-cta-outline">
                    <span>View Pricing</span>
                </a>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 1: The Problem + Solution
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">The Problem</span>
                    <h2>Running out of stock costs you sales and customers</h2>
                    <p>Most small businesses track inventory in messy spreadsheets. You find out something is out of stock when a customer asks for it. Reorders happen too late, counts are wrong, and you have no idea what's sitting in which location. Argo Books gives you a real-time view of every product, every location, and every stock level — so you always know what you have and when it's time to reorder.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic low-stock and out-of-stock status badges</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Get alerted before you run out</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multi-location support — track stock across warehouses and stores</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/inventory-dashboard.svg" alt="Argo Books inventory dashboard showing product list with stock levels, categories, locations, reorder points, and status badges" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Start tracking your inventory today</h3>
                <p>Download Argo Books and add your first product in under a minute. No credit card required.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Download Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../../pricing/" class="btn-cta btn-cta-outline">
                        <span>See Pricing</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         STATS BANNER
         ============================================= -->
    <section class="highlight-banner">
        <div class="container">
            <div class="highlight-grid animate-on-scroll">
                <div class="highlight-item">
                    <h3>Real-time</h3>
                    <p>Stock levels update as you sell and restock</p>
                </div>
                <div class="highlight-item">
                    <h3>Multi-location</h3>
                    <p>Track stock across warehouses and stores</p>
                </div>
                <div class="highlight-item">
                    <h3>Automatic</h3>
                    <p>Low-stock alerts before you run out</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Purchase Orders
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Purchase Orders</span>
                    <h2>Order from suppliers and track every delivery</h2>
                    <p>Create purchase orders in seconds. Select a supplier, add products and quantities, and Argo Books calculates line totals automatically. When an order arrives, mark it as received and stock levels update automatically.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic stock updates when orders are received</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Expected delivery dates and supplier tracking</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Total order value and pending approval summaries at a glance</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/inventory-purchase-orders.svg" alt="Argo Books purchase orders page showing order list with supplier names, totals, status badges, and expected delivery dates" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS — 3 Steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">Three steps to organized inventory</h2>
                <p class="section-desc">From scattered stock counts to a complete inventory system. No warehouse experience needed — Argo Books keeps it simple.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Add your products</h3>
                    <p>Add items to your catalog with names, SKUs, categories, and locations. Set reorder points so Argo Books alerts you before stock runs low.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Track stock in real time</h3>
                    <p>Stock levels update as you sell, restock, and adjust. See in-stock, reserved, and available quantities for every product at every location.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Reorder before you run out</h3>
                    <p>Low-stock alerts tell you when it's time to reorder. Create purchase orders, send them to suppliers, and mark them received when stock arrives.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop guessing what's in stock</h3>
                <p>Real-time stock levels mean you always know what you have. Get started with Argo Books in minutes.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 3: Stock Adjustments
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Stock Adjustments</span>
                    <h2>Every stock change is tracked with a reason</h2>
                    <p>Not every stock change comes from a sale or a purchase order. Items get damaged, returned, moved between locations, or counted during audits. The stock adjustments page gives you a complete, auditable log of every change — with the product, location, before and after quantities, and a written reason for each adjustment.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Complete audit trail — every adjustment logged with date, reason, and user</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Before and after quantities for every stock change</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Links to purchase orders and customer sales for full traceability</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/inventory-dashboard.svg" alt="Argo Books stock adjustments page showing adjustment history with before/after quantities, reasons, and color-coded changes" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 4: Locations
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Multi-Location</span>
                    <h2>Track stock across every warehouse, store, and office</h2>
                    <p>Add as many locations as you need — warehouses, retail stores, offices, or storage units. The locations dashboard shows total stock items, total inventory value, and other key metrics.</p>
                    <p>Every product in your catalog is tracked per location, so you always know not just how many you have in total, but exactly where each unit is.</p>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/inventory-purchase-orders.svg" alt="Argo Books locations page showing warehouse locations with addresses, managers, stock counts, and capacity percentages" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         BENEFITS GRID — 6 benefit cards
         ============================================= -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why It Matters</span>
                <h2 class="section-title">More than just counting stock</h2>
                <p class="section-desc">Inventory management in Argo Books isn't a complex warehouse system — it's the stock tracking your business actually needs to avoid stockouts and waste.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Never run out of stock</h3>
                    <p>Configurable low-stock alerts before you hit zero. See which products need attention at a glance with color-coded status badges.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>Accurate counts, always</h3>
                    <p>Every sale, purchase order, and stock adjustment updates your counts in real time. No manual spreadsheet updates — your numbers are always current.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('map-pin', 22) ?>
                    </div>
                    <h3>Multi-location visibility</h3>
                    <p>Track the same product across multiple warehouses and stores. Know not just how many you have, but exactly where each unit is at any moment.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Purchase order tracking</h3>
                    <p>Create POs, send them to suppliers, and mark them received. Stock levels update automatically when orders arrive — no double entry required.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Complete audit trail</h3>
                    <p>Every stock change is logged with a reason, timestamp, and before/after quantities. Full traceability for audits, discrepancies, and compliance.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>See your inventory value</h3>
                    <p>Know the total dollar value of your inventory at any moment. Track value per location, per product category, and across your entire operation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Take control of your inventory</h3>
                <p>Join small business owners who stopped guessing and started tracking with Argo Books.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Download Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../" class="btn-cta btn-cta-outline">
                        <span>View All Features</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         USE CASES SECTION
         ============================================= -->
    <section class="use-cases-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Who It's For</span>
                <h2 class="section-title">Built for every business that sells or manages products</h2>
                <p class="section-desc">Whether you stock 10 items or 10,000 — inventory management scales with your business.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Retail &amp; e-commerce
                    </h3>
                    <p>Track product stock across your store and warehouse. Know which items are selling fast, which are sitting, and when to reorder from suppliers — before you lose a sale.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Rental businesses
                    </h3>
                    <p>Track rental equipment availability, reserved units, and items being rented. See what's available at each location and manage returns with stock adjustments.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Service businesses
                    </h3>
                    <p>Track parts, supplies, and materials needed for jobs. Set reorder points on consumables so you never show up to a job site without what you need.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('house', 22) ?>
                        Warehouses &amp; distribution
                    </h3>
                    <p>Manage stock across multiple warehouse locations. Track incoming shipments with purchase orders and outgoing stock with adjustments and sales records.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 5: Privacy & Security
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your inventory data stays on your computer</h2>
                    <p>Unlike cloud-based inventory systems that upload your product catalog, stock levels, and supplier details to third-party servers, Argo Books is a desktop application. Your data — products, quantities, and purchase orders — is stored locally on your device.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app — your inventory data stays on your computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No third-party cloud storage of your product or supplier data</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Export to CSV or Excel anytime — no vendor lock-in</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works offline — manage inventory without internet access</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/privacy-local-storage.svg" alt="Your data stays local — encrypted, offline-capable, no cloud" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         RELATED FEATURES
         ============================================= -->
    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Related Features</span>
                <h2 class="section-title">Works great with</h2>
                <p class="section-desc">Inventory management is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense &amp; Revenue Tracking</h3>
                    <p>Purchase orders flow into your expense records automatically. Track cost of goods sold alongside your inventory for a complete financial picture.</p>
                </a>
                <a href="../invoicing/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Invoicing</h3>
                    <p>Create invoices from your product catalog. Line items pull directly from your inventory, and stock levels update when invoices are paid.</p>
                </a>
                <a href="../rental-management/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('calendar', 22) ?>
                    </div>
                    <h3>Rental Management</h3>
                    <p>Rental items are tracked in your inventory. See which units are available, which are being rented, and when they're due back — all in one place.</p>
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
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to take control of your inventory?</h2>
                    <p>Download Argo Books and start tracking stock in minutes. Free to get started — no credit card, no trial period.</p>
                    <div class="cta-buttons">
                        <a href="../../downloads/" class="btn-cta btn-cta-primary">
                            <span>Download for Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="../../pricing/" class="btn-cta btn-cta-ghost">
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
