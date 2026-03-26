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
        content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental, event companies, and any rental-based business.">
    <meta name="keywords"
        content="rental management software, booking management, equipment rental tracking, rental business software, rental inventory, rental returns, rental invoicing, equipment booking, rental deposits, overdue rentals">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Rental Management — Argo Books">
    <meta property="og:description"
        content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental and any rental-based business.">
    <meta property="og:url" content="https://argorobots.com/features/rental-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Rental+Management&subtitle=Track+bookings%2C+rental+periods%2C+returns%2C+and+revenue.+Built+for+equipment+rental+businesses.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Rental Management — Argo Books">
    <meta name="twitter:description"
        content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental and any rental-based business.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Rental+Management&subtitle=Track+bookings%2C+rental+periods%2C+returns%2C+and+revenue.+Built+for+equipment+rental+businesses.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/rental-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Rental Management", "item": "https://argorobots.com/features/rental-management/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Rental Management — Argo Books</title>

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
                <?= svg_icon('calendar', 16) ?>
                <span>Rental Management</span>
            </div>
            <h1 class="animate-fade-in">Track every rental from booking to return</h1>
            <p class="hero-subtitle animate-fade-in">Manage bookings, track rental periods, collect deposits, handle returns, and monitor revenue — all in one place. Built for equipment rental, event supplies, and any rental-based business.</p>
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
                    <h2>Lost rentals and missed returns cost you money</h2>
                    <p>Most rental businesses track bookings in spreadsheets or on paper. Items go out without proper records, return dates are forgotten, deposits aren't tracked, and overdue rentals slip through the cracks. Argo Books gives you a complete rental management system — every booking, every item, every customer, every date — so nothing falls through the cracks.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Full rental lifecycle — Active, Overdue, and Returned status tracking</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Deposit tracking with paid/unpaid status per rental</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic overdue detection when return dates pass</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Revenue tracking with total earned across all rentals</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/rental-records.svg" alt="Argo Books rental records page showing rental bookings with items, customers, dates, status badges, deposits, and revenue totals" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Start tracking your rentals today</h3>
                <p>Download Argo Books and create your first rental booking in under a minute. No credit card required.</p>
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
                    <p>Active, overdue, and returned status tracking</p>
                </div>
                <div class="highlight-item">
                    <h3>Automatic</h3>
                    <p>Overdue detection when return dates pass</p>
                </div>
                <div class="highlight-item">
                    <h3>Complete</h3>
                    <p>Revenue, deposits, and invoicing built in</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Rental Items
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Rental Items</span>
                    <h2>Manage your rental catalog with availability tracking</h2>
                    <p>The rental items page shows every item in your rental catalog with total units, how many are currently available, how many are rented out, and pricing for daily and weekly rates. Each item has a status — Available or All Rented — so you can see at a glance what's ready to go out.</p>
                    <p>Set deposit amounts per item, track availability across your entire fleet, and see summary cards showing total items, available units, rented-out counts, and items in maintenance. When a rental is created, available counts update automatically.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Available vs. rented counts update in real time</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Daily and weekly pricing per item</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Configurable deposit amounts for each rental item</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Status badges — Available, All Rented, In Maintenance</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/rental-items.svg" alt="Argo Books rental items catalog showing equipment with availability counts, daily and weekly pricing, deposit amounts, and status badges" loading="lazy">
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
                <h2 class="section-title">Three steps to organized rentals</h2>
                <p class="section-desc">From handwritten booking slips to a complete rental system. No complex setup — Argo Books keeps it simple.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Add rental items</h3>
                    <p>Add your equipment to the rental catalog with daily and weekly rates, deposit amounts, and total unit counts. Set up your fleet once and start renting.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Create rental bookings</h3>
                    <p>Select an item and customer, set start and due dates, and specify the quantity. Argo Books tracks the rental from active to returned automatically.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Track returns and revenue</h3>
                    <p>Mark rentals as returned when items come back. Overdue rentals are flagged automatically. Revenue and deposit totals update in real time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop losing track of your rental equipment</h3>
                <p>Real-time availability and overdue alerts mean you always know what's out and when it's due back. Get started in minutes.</p>
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
         DETAIL SECTION 3: Rental Records
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Rental Records</span>
                    <h2>Every rental tracked from start to return</h2>
                    <p>The rental records page gives you a complete view of every rental — past and present. Each record shows the rental ID, item name with pricing, customer, quantity, start date, due date, status, total charged, deposit amount, and whether it's been paid. Summary cards at the top show total rentals, active bookings, overdue items, and total revenue earned.</p>
                    <p>Color-coded status badges make it easy to spot which rentals are active (green), overdue (red), or returned (blue). Filter by status to see only what needs attention. Click any rental to view full details, generate an invoice, or mark it as returned.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Summary cards — total rentals, active, overdue, and total revenue</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Color-coded status — Active (green), Overdue (red), Returned (blue)</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Deposit and payment tracking per rental</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>One-click invoice generation from any rental record</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/rental-records.svg" alt="Argo Books rental records dashboard showing rental bookings with status badges, customer names, dates, deposits, and revenue tracking" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         BENEFITS GRID — 6 benefit cards
         ============================================= -->
    <section class="benefits-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why It Matters</span>
                <h2 class="section-title">More than just tracking bookings</h2>
                <p class="section-desc">Rental management in Argo Books isn't a complex booking system — it's the rental tracking your business actually needs to stay organized and profitable.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Never miss a return</h3>
                    <p>Overdue rentals are flagged automatically when the due date passes. See which items need to come back at a glance — no manual date checking required.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Track every dollar</h3>
                    <p>Revenue totals, deposit amounts, and payment status are tracked per rental. Know exactly how much you've earned and how much is still outstanding.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('package', 22) ?>
                    </div>
                    <h3>Real-time availability</h3>
                    <p>Available and rented counts update as bookings are created and returned. Always know what's in your warehouse and what's out with a customer.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Integrated invoicing</h3>
                    <p>Generate invoices directly from rental records. Customer details, item pricing, and rental dates auto-populate — send a professional invoice in seconds.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Deposit management</h3>
                    <p>Set deposit amounts per item and track paid/unpaid status per rental. Know which deposits have been collected and which are still outstanding.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('users', 22) ?>
                    </div>
                    <h3>Customer rental history</h3>
                    <p>Every rental is linked to a customer profile. See a customer's complete rental history — what they rented, when, and how much they paid.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Take control of your rental business</h3>
                <p>Join rental businesses that stopped losing equipment and started tracking every booking with Argo Books.</p>
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
                <h2 class="section-title">Built for every business that rents equipment</h2>
                <p class="section-desc">Whether you rent out 5 items or 500 — rental management scales with your business.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Equipment rental
                    </h3>
                    <p>Track tools, machinery, and equipment across multiple customers. Set daily and weekly rates, collect deposits, and flag overdue returns automatically.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Event &amp; AV rental
                    </h3>
                    <p>Manage projectors, sound systems, displays, and event equipment. Track which items are booked for upcoming events and when they're due back.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('house', 22) ?>
                        Property &amp; vehicle rental
                    </h3>
                    <p>Track rental periods for properties, vehicles, or spaces. Monitor rental revenue, collect deposits, and generate invoices for each booking.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Libraries &amp; shared resources
                    </h3>
                    <p>Manage shared equipment pools, lending libraries, or community resources. Track who has what, when it's due back, and availability for the next booking.</p>
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
                <p class="section-desc">Rental management is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../inventory-management/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('package', 22) ?>
                    </div>
                    <h3>Inventory Management</h3>
                    <p>Rental items live in your inventory. Track total units, available stock, and items out on loan — all from the same product catalog.</p>
                </a>
                <a href="../customer-management/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('users', 22) ?>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Every rental is linked to a customer profile. See rental history per customer, auto-populate billing details, and track deposit payments.</p>
                </a>
                <a href="../invoicing/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Invoicing</h3>
                    <p>Generate invoices directly from rental records. Item pricing, dates, and customer details auto-populate — send professional invoices in seconds.</p>
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
                    <h2>Ready to organize your rentals?</h2>
                    <p>Download Argo Books and start tracking bookings in minutes. Free to get started — no credit card, no trial period.</p>
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
