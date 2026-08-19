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
        'q' => 'Do I need accounting experience to track expenses?',
        'a' => "Not at all. Argo Books uses guided forms with smart defaults and built-in validation to make recording expenses and revenue simple for anyone. You don't need to know debits from credits. Just fill in the amount, category, and date, and Argo Books handles the rest.",
    ],
    [
        'q' => 'Can I track both expenses and revenue in one place?',
        'a' => 'Yes. Argo Books has dedicated expense and revenue pages with real-time summary cards showing monthly totals, transaction counts, and net profit. You get a complete picture of your business finances without switching between apps or spreadsheets.',
    ],
    [
        'q' => 'How does receipt management work?',
        'a' => "You can attach receipts to any expense record for your records. Even better, AI receipt scanning can automatically create expense entries from receipt photos. It extracts the supplier name, line items, taxes, and total with 99.9% accuracy. All receipts are stored in a searchable archive so you're always ready for tax time.",
    ],
    [
        'q' => 'Can I import existing expense data into Argo Books?',
        'a' => "Yes. If you have expense or revenue records in a spreadsheet, you can import them using the AI Spreadsheet Import feature. Just drop your Excel or CSV file and the AI maps your columns to the right fields automatically. It's the fastest way to get up and running with your existing financial data.",
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
    <meta name="description"
        content="Track business expenses and revenue with Argo Books. Guided forms, smart validation, receipt archiving, and real-time profit monitoring make bookkeeping simple for any small business.">
    <meta name="keywords"
        content="expense tracking software, revenue tracking, business expense tracker, income and expense tracking, small business bookkeeping, profit tracking software, transaction management, expense management app">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Expense &amp; Revenue Tracking | Argo Books">
    <meta property="og:description"
        content="Track every expense and revenue transaction in one place. Guided forms, smart validation, and real-time profit monitoring keep your books accurate.">
    <meta property="og:url" content="https://argorobots.com/features/expense-revenue-tracking/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Expense &amp; Revenue Tracking | Argo Books">
    <meta name="twitter:description"
        content="Track every expense and revenue transaction in one place, with real-time profit monitoring.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/expense-revenue-tracking/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Expense & Revenue Tracking" => "/features/expense-revenue-tracking/"]) ?></script>

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
            "description": "Track business expenses and revenue with guided forms, smart validation, receipt archiving, and real-time profit monitoring.",
            "featureList": "Expense and revenue tracking, Guided transaction forms, Receipt archive with search, Real-time profit summary"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Expense &amp; Revenue Tracking | Argo Books</title>

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
         HERO. Live demo beside the headline. The panel markup comes from
         partials/feature-demo.php and the loop from feature-tour.js, both
         shared with the landing and comparison pages.
         ============================================= -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Every dollar in.<br>Every dollar out.</h1>
                    <p class="fp-hero-sub">Record an expense or a sale in about ten seconds, and watch the monthly totals and net profit move as you type. No debits, no credits, no accounting course.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your books stay on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="expenses">
                    <?= argo_feature_demo('expenses') ?>
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
                <h2 class="fp-h2">Three steps, about ten seconds</h2>
                <p class="fp-lede">A spreadsheet lets you type anything into any cell. A guided form does not, which is why the numbers still add up a year later.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Record the transaction</h3>
                    <p>Pick expense or revenue, then fill in the amount, the category and who it was with. Smart defaults fill most of it for you.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>It checks before it saves</h3>
                    <p>Required fields are enforced and amounts are verified, so nothing lands in your books half finished or in the wrong column.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>See the picture change</h3>
                    <p>Monthly revenue, expenses and net profit update the moment you save. No waiting for month end to find out where you stand.</p>
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
                    <div class="fp-eyebrow">Both sides of the ledger</div>
                    <h2 class="fp-h2">Money in and money out, in one list</h2>
                    <p class="fp-lede">Revenue and expenses live in the same place, with matching forms and the same search. Sort by supplier, customer, date, amount or status, and edit a row without opening a separate page. Every change is kept, so you can see what moved and when.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Summary cards for monthly revenue, expenses and net profit</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Search and filter by supplier, customer, date, amount or status</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Edit straight from the row, with full history and undo</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/expense-revenue-stats.svg"
                         alt="Argo Books expense and revenue summary cards showing monthly totals, transaction counts and net profit"
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
                <h2>Get your finances organized in minutes</h2>
                <p>No account, no credit card, and no accounting experience needed.</p>
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
                <h2 class="fp-h2">What changes when it is all in one place</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>You know if you are profitable today</h3>
                    <p>Summary cards move as you add transactions, so the answer is on screen instead of waiting until the end of the month.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>Guided forms catch the mistakes</h3>
                    <p>Smart defaults and validation mean nothing saves with a missing category, a blank date, or an amount in the wrong column.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('search', 20) ?></div>
                    <h3>Any transaction, in seconds</h3>
                    <p>Everything is indexed. Search by supplier, customer, amount or date and find it years later without scrolling a spreadsheet.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('shield', 20) ?></div>
                    <h3>Tax-ready all year</h3>
                    <p>Every entry is timestamped and categorized, with a receipt attached where it matters, so January is not a rebuilding job.</p>
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
                    <p class="fp-lede">Argo Books is a desktop application, not a cloud service holding your finances on someone else's server. Transactions, receipts and reports are written to your own machine, and you can back them up or move them like any other file.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Transactions and receipts stored locally</span></li>
                        <li><?= svg_icon('check', 17) ?><span>No third-party cloud storage of your financial records</span></li>
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
                    <p>Keep project income and business costs separate, and see profit per client when it is time to bill.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Record every sale and supplier purchase, track cost of goods, and see which suppliers cost you the most.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Service businesses</h3>
                    <p>Log revenue by customer and costs by job, then compare profitability across service types and periods.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Side hustles</h3>
                    <p>Start with a few transactions a week and scale to hundreds without the interface getting in the way.</p>
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
                <h2 class="fp-h2">Where your transactions come from</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../receipt-scanning/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('receipt-scan', 20) ?></div>
                    <h3>AI receipt scanning</h3>
                    <p>Photograph a receipt and the expense record writes itself, line items and all.</p>
                </a>
                <a href="../bank-statement-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('bank', 20) ?></div>
                    <h3>Bank statement import</h3>
                    <p>Bring a month of transactions in at once instead of typing them one by one.</p>
                </a>
                <a href="../spreadsheet-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document-upload', 20) ?></div>
                    <h3>Spreadsheet import</h3>
                    <p>Move the history you already keep in Excel or CSV across in one go.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>Enough history turns into a forecast of what next month is likely to cost.</p>
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
                <h2>Stop guessing whether you are profitable</h2>
                <p>Download Argo Books and record your first transaction today. Free plan, no credit card, and your data stays on your own machine.</p>
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
