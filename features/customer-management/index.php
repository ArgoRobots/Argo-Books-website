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
        'q' => 'Does Argo Books include a built-in customer database?',
        'a' => 'Yes. Argo Books includes a built-in customer database where you can store names, emails, phone numbers, addresses, and notes for every client. It integrates directly with invoicing, revenue tracking, and rental management, so when you create an invoice or rental, your customer details auto-populate without re-entering anything.',
    ],
    [
        'q' => 'How do I find customers in Argo Books?',
        'a' => 'You can instantly search customers by name, email, or ID, and filter by country, status, or date added. Whether you have 10 customers or 10,000, finding the right record takes seconds.',
    ],
    [
        'q' => 'Is my customer data private and secure?',
        'a' => 'Absolutely. Argo Books is a desktop application, so all customer data is stored locally on your computer. Nothing is uploaded to cloud servers. Your data is encrypted with AES-256-GCM, the same standard used by banks and government agencies. You have full control over your customer information at all times.',
    ],
    [
        'q' => 'Can I import my existing customer list?',
        'a' => 'Yes. You can import customers from Excel or CSV files using the AI Spreadsheet Import feature. The AI automatically detects your columns and maps them to the right fields, so you can migrate your existing customer data into Argo Books in minutes, with no manual data entry required.',
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
    <meta name="description" content="Track customer information, purchase history, and contact details with Argo Books. A simple customer database built for small businesses, organizing contacts, addresses, and notes without a full CRM.">
    <meta name="keywords" content="customer management, CRM, customer tracking, customer database, small business CRM, customer profiles, contact management, customer address book, customer notes, customer purchase history">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Customer Management | Argo Books">
    <meta property="og:description" content="Track customer information, purchase history, and contact details with Argo Books. Simple customer management built for small businesses.">
    <meta property="og:url" content="https://argorobots.com/features/customer-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Customer Management | Argo Books">
    <meta name="twitter:description" content="Track customer information, purchase history, and contact details with Argo Books. Simple customer management built for small businesses.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/customer-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Customer Management" => "/features/customer-management/"]) ?></script>

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
            "description": "Track customer information, purchase history, and contact details with Argo Books. A simple customer database built for small businesses, organizing contacts, addresses, and notes without a full CRM.",
            "featureList": "Customer directory, Purchase history, Outstanding balances, Contact management"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Customer Management | Argo Books</title>

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
                    <h1>Every customer.<br>Every balance.</h1>
                    <p class="fp-hero-sub">One record per customer holding their contact details, everything they have ever bought, and exactly what they still owe you.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your customer list stays on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="customers">
                    <?= argo_feature_demo('customers') ?>
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
                <h2 class="fp-h2">Three steps, and the record fills itself</h2>
                <p class="fp-lede">Most customer lists rot because keeping them current is a separate job. This one updates as a side effect of billing.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Add them once</h3>
                    <p>Name, contact details, and any terms you have agreed. That is the last time you type any of it.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Sell and invoice as normal</h3>
                    <p>Every invoice, payment and sale attaches itself to the customer it belongs to, with no filing on your part.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Open the record when you need it</h3>
                    <p>Their full history, their balance, and when they last paid you, on one screen.</p>
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
                    <div class="fp-eyebrow">The whole picture</div>
                    <h2 class="fp-h2">What they bought, and what they owe</h2>
                    <p class="fp-lede">A customer record is not just an address book entry. It carries every invoice raised, every payment received, and the balance between the two, so you can answer "are we square?" without opening three other screens.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Outstanding balance per customer, kept current automatically</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Full purchase and payment history on one record</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Search and filter by name, balance or last activity</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/customer-dashboard.svg"
                         alt="The Argo Books customer directory showing contact details, outstanding balances and recent activity"
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
                <h2>Get your customer list in order</h2>
                <p>No account, no credit card, and no import needed to start.</p>
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
                <h2 class="fp-h2">What changes when the history is in one place</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('search', 20) ?></div>
                    <h3>You can answer questions immediately</h3>
                    <p>When a customer calls about an invoice from March, the answer is one search away instead of a scroll through email.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>You know who owes you</h3>
                    <p>Balances are calculated from real invoices and payments, so the number is current rather than remembered.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>Invoices start half written</h3>
                    <p>Billing an existing customer pulls their details in automatically, which is where most invoicing time actually goes.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>You can see who is worth keeping</h3>
                    <p>Purchase history over time shows which customers grow, which shrink, and which quietly cost you money.</p>
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
                    <h3><?= svg_icon('users', 19) ?> Freelancers</h3>
                    <p>Keep repeat clients and their rates straight without a spreadsheet on the side.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and wholesale</h3>
                    <p>Trade accounts with running balances alongside one-off retail sales.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Job history per customer, so a return visit starts from what happened last time.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('calendar', 19) ?> Anyone on repeat business</h3>
                    <p>See at a glance who has not bought in a while and is worth a call.</p>
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
                <h2 class="fp-h2">What customer records connect to</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Bill a saved customer in a couple of clicks with their details already filled in.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Payments received land against both the customer and your revenue.</p>
                </a>
                <a href="../rental-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('calendar', 20) ?></div>
                    <h3>Rental management</h3>
                    <p>Bookings and returns tracked against the customer who took the item.</p>
                </a>
                <a href="../report-builder/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('report', 20) ?></div>
                    <h3>Report builder</h3>
                    <p>Turn customer activity into statements and summaries.</p>
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
                <h2>Know your customers, not just their names</h2>
                <p>Download Argo Books and build your customer list today. Free plan, no credit card, and your data stays on your own machine.</p>
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
