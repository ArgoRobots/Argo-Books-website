<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];

// One array drives both the visible accordion and the FAQPage schema.
$faqs = [
    [
        'q' => 'Can Argo Books track inventory across multiple locations?',
        'a' => 'Yes. You can add unlimited locations, such as warehouses, stores, offices, or any other facility, and track per-location stock levels, inventory value, and capacity. Everything is visible from a single dashboard, so you always know what you have and where it is.',
    ],
    [
        'q' => 'How do low-stock alerts work?',
        'a' => 'You can set a reorder point for each product. When stock drops to that level, Argo Books flags it with a color-coded status badge so you know it\'s time to restock. No more surprise stockouts. You\'ll see the warning before it becomes a problem.',
    ],
    [
        'q' => 'Can I create and manage purchase orders?',
        'a' => 'Yes. Create purchase orders with supplier details and itemized line items directly in Argo Books. When you mark an order as received, stock levels update automatically, with no manual adjustments needed. It keeps your inventory accurate without the extra work.',
    ],
    [
        'q' => 'Is inventory management included in the Free plan?',
        'a' => 'Yes. Inventory management is a core feature available on both the Free and Premium plans. You get unlimited products, multi-location tracking, low-stock alerts, and purchase orders at no cost. Premium adds predictive analytics to help you forecast demand and plan inventory purchases ahead of time.',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, stock adjustments, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta name="keywords" content="inventory management software, stock tracking, product catalog management, small business inventory, inventory alerts, purchase orders, stock adjustments, warehouse management, reorder points, low stock alerts">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Inventory Management | Argo Books">
    <meta property="og:description" content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta property="og:url" content="https://argorobots.com/features/inventory-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Inventory Management | Argo Books">
    <meta name="twitter:description" content="Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, and multi-location support. Argo Books makes inventory simple for small businesses.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/inventory-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Inventory Management" => "/features/inventory-management/"]) ?></script>

    <!-- FAQ Schema, built from the same array as the accordion further down -->
    <script type="application/ld+json"><?= argo_faq_schema($faqs) ?></script>

    <!-- SoftwareApplication Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, Linux",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, stock adjustments, and multi-location support. Argo Books makes inventory simple for small businesses.",
            "featureList": "Stock level tracking, Low stock alerts, Purchase orders, Product cost tracking"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Inventory Management | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>
    <!-- Mockup animations, shared with the landing and comparison pages. -->
    <script src="../../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&amp;family=IBM+Plex+Mono:wght@400;500;600&amp;family=IBM+Plex+Sans:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="../../resources/styles/typography.css">
    <link rel="stylesheet" href="../../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../feature-page.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- =============================================
         HERO
         ============================================= -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Stock counts that<br>stay correct.</h1>
                    <p class="fp-hero-sub">Levels move as you sell and restock, so the number on screen is the number on the shelf, and you find out about a shortage before a customer does.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your stock data stays on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="inventory">
                    <?= argo_feature_demo('inventory') ?>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">How it works</div>
                <h2 class="fp-h2">Three steps, then it keeps up on its own</h2>
                <p class="fp-lede">Stock counts go wrong when updating them is a separate task somebody has to remember. Here it is a side effect of selling.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>List what you carry</h3>
                    <p>Product, cost, price and how many you have. Import a spreadsheet if you already keep one.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Sell and restock as normal</h3>
                    <p>Every sale takes stock down and every purchase order puts it back, without a second entry.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Get told before you run out</h3>
                    <p>Set a reorder point per product and the low-stock warning arrives while there is still time to order.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRODUCT BLOCK
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">Beyond the count</div>
                    <h2 class="fp-h2">What each product actually costs you</h2>
                    <p class="fp-lede">Knowing you have eleven left is useful. Knowing what those eleven cost, what they sell for, and which supplier gave you the better price is what tells you whether the product is worth carrying at all.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Cost and margin held per product, not just quantity</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Purchase orders that update stock when they arrive</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Low stock alerts on the reorder point you choose</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/inventory-dashboard.svg"
                         alt="The Argo Books inventory dashboard showing stock levels, low stock warnings and product costs"
                         loading="lazy" width="600" height="500">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         The page's one mid-page CTA.
         ============================================= -->
    <section class="fp-midcta">
        <div class="fp-wrap fp-midcta-in">
            <div>
                <h2>Get an accurate stock count today</h2>
                <p>No account, no credit card, and you can import the list you already have.</p>
            </div>
            <a href="../../downloads/" class="fp-btn fp-btn-primary">
                <span>Download free</span>
                <?= svg_icon('arrow-right', 17) ?>
            </a>
        </div>
    </section>

    <!-- =============================================
         BENEFITS
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Why it matters</div>
                <h2 class="fp-h2">What changes when the count is trustworthy</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('package', 20) ?></div>
                    <h3>You stop selling what you do not have</h3>
                    <p>A count that updates as you sell is a count you can quote from without checking the shelf first.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('bolt', 20) ?></div>
                    <h3>Reordering happens on time</h3>
                    <p>Low stock warnings fire on your reorder point, not when a customer asks for something you cannot supply.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>You can see which products earn</h3>
                    <p>Cost and price held together turn a stock list into a margin list, which is the one that matters.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>No more counting twice</h3>
                    <p>Sales and purchase orders both move stock automatically, so the spreadsheet reconciliation disappears.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRIVACY
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-split-flip fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">Privacy</div>
                    <h2 class="fp-h2">Your books stay on your computer</h2>
                    <p class="fp-lede">Argo Books is a desktop application, not a cloud service holding your finances on someone else's server. Your records are written to your own machine, and you can back them up or move them like any other file.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Records and documents stored locally</span></li>
                        <li><?= svg_icon('check', 17) ?><span>No third-party cloud storage of your financial data</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Your data moves and backs up like any other file</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/privacy-local-storage.svg"
                         alt="The Argo Books folder open on a local disk, showing receipts, invoices and the database file stored on this computer"
                         loading="lazy" width="600" height="500">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHO IT'S FOR
         ============================================= -->
    <section class="fp-section-tight">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Who it's for</div>
                <h2 class="fp-h2">Built for the way you actually work</h2>
            </div>
            <div class="fp-who fp-reveal">
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Keep shelf and storefront counts in step without a nightly stocktake.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades</h3>
                    <p>Track parts and materials so a job does not stop halfway through.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('truck', 19) ?> Wholesalers</h3>
                    <p>Manage larger quantities and supplier orders from the same list.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Makers and small brands</h3>
                    <p>Watch component stock and finished goods without a warehouse system.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         FAQ. Accordion and schema both come from $faqs.
         ============================================= -->
    <section class="fp-faq">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Questions</div>
                <h2 class="fp-h2">Before you download</h2>
            </div>
            <?= argo_faq_grid($faqs) ?>
        </div>
    </section>

    <!-- =============================================
         RELATED
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Works with</div>
                <h2 class="fp-h2">What inventory connects to</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Sell from your product list and stock adjusts as the invoice goes out.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Purchase orders become expenses without retyping them.</p>
                </a>
                <a href="../spreadsheet-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document-upload', 20) ?></div>
                    <h3>Spreadsheet import</h3>
                    <p>Bring the stock list you already keep in Excel across in one go.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>Sales history turns into a view of what you will need to reorder.</p>
                </a>
            </div>
        </div>
    </section>

    </main>

    <!-- Final CTA and footer share one dark block. dark-section-wrapper is what
         lets the footer's orbs bleed up past the footer's own box. -->
    <div class="dark-section-wrapper fp-outro">
        <section class="fp-outro-cta cta-section">
            <div class="fp-wrap">
                <h2>Stop guessing what is on the shelf</h2>
                <p>Download Argo Books and get your stock under control. Free plan, no credit card, and your data stays on your own machine.</p>
                <div class="fp-btns">
                    <a href="../../downloads/" class="fp-btn fp-btn-primary">
                        <span>Download free</span>
                        <?= svg_icon('arrow-right', 17) ?>
                    </a>
                    <a href="../../pricing/" class="fp-btn fp-btn-onnavy">
                        <span>See pricing</span>
                    </a>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var targets = document.querySelectorAll('.fp-reveal');
            if (!('IntersectionObserver' in window) ||
                window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                targets.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            targets.forEach(function (el) { observer.observe(el); });
        });
    </script>
</body>

</html>
