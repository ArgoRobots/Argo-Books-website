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
        'q' => 'What bank statement formats can I import?',
        'a' => 'Argo Books imports bank statements as CSV, Excel (.xlsx and .xls), or PDF. Export a statement from your online banking, drop the file in, and each transaction line is read and pre-filled for you.',
    ],
    [
        'q' => 'Do I need to connect my bank account?',
        'a' => 'No. There is no bank login, no connection, and no third-party aggregator. Argo Books works entirely from the statement file you export yourself, so nothing is ever linked to your bank.',
    ],
    [
        'q' => 'Does it record transactions automatically?',
        'a' => 'Every line is pre-filled for you with a type, category, and supplier or customer, but nothing is saved until you review and confirm. You stay in control of what goes into your books.',
    ],
    [
        'q' => 'How is importing different from matching?',
        'a' => 'Import turns each bank line into a new categorized expense or revenue. Matching compares your statement against records you have already entered, confirms the ones that line up, and shows anything missing from your books. You can use either, or both.',
    ],
    [
        'q' => 'How many bank statements can I import per month?',
        'a' => 'The Free plan includes <?= $argo_bank_limit ?> AI bank imports per month and Premium includes <?= $argo_premium_bank_limit ?>. Reading a CSV or Excel file without AI categorization doesn\'t count against your limit, and even at the limit you can still import and fill lines in by hand.',
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
    <meta name="description" content="Import a bank statement (CSV, Excel, or PDF) into Argo Books and every line becomes a categorized expense or revenue, ready to review. Match against your books too. No bank login required.">
    <meta name="keywords" content="bank statement import, import bank statement CSV, bank statement to accounting software, bank reconciliation software, categorize bank transactions, PDF bank statement import, bank matching, no bank connection bookkeeping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Bank Statement Import | Argo Books">
    <meta property="og:description" content="Drop in a bank statement and every line becomes a categorized expense or revenue. Match against your books, all without connecting your bank.">
    <meta property="og:url" content="https://argorobots.com/features/bank-statement-import/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bank Statement Import | Argo Books">
    <meta name="twitter:description" content="Drop in a bank statement and every line becomes a categorized expense or revenue. Match against your books, all without connecting your bank.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/bank-statement-import/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Bank Statement Import" => "/features/bank-statement-import/"]) ?></script>

    <!-- FAQ Schema, built from the same array as the accordion further down -->
    <script type="application/ld+json"><?= argo_faq_schema($faqs) ?></script>

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
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Import a bank statement (CSV, Excel, or PDF) into Argo Books and every line becomes a categorized expense or revenue, ready to review. Match against your books too. No bank login required.",
            "featureList": "Bank statement import, Automatic transaction matching, Duplicate detection, Multi-format support"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Bank Statement Import | Argo Books</title>

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
                    <h1>A month of banking,<br>in one go.</h1>
                    <p class="fp-hero-sub">Drop in the statement your bank gives you and Argo Books reads the transactions, matches the ones you have already recorded, and leaves you only the genuinely new entries to confirm.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and the file never leaves your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="bank-import">
                    <?= argo_feature_demo('bank-import') ?>
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
                <h2 class="fp-h2">Three steps, one file</h2>
                <p class="fp-lede">Typing a statement in by hand is where an afternoon goes. Importing it is where ten minutes goes.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Export from your bank</h3>
                    <p>Whatever format they offer. CSV, Excel and the common statement layouts are all read.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Drop the file in</h3>
                    <p>Columns are worked out for you, so there is no mapping screen to fight with before anything happens.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Confirm what is new</h3>
                    <p>Anything already in your books is matched and set aside. You review the rest and save.</p>
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
                    <div class="fp-eyebrow">The part that saves the time</div>
                    <h2 class="fp-h2">It knows what you have already recorded</h2>
                    <p class="fp-lede">The slow part of importing a statement is not reading the file, it is working out which lines you already entered. Argo Books matches on amount, date and description, so scanned receipts and manual entries are not duplicated when the statement arrives.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Existing transactions matched, not duplicated</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Columns detected without a mapping step</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Review everything before a single record is saved</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/bank-statement-matching.svg"
                         alt="Argo Books matching imported bank transactions against records that already exist in the books"
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
                <h2>Import a month of transactions in minutes</h2>
                <p>No account, no credit card, and no bank login handed over.</p>
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
                <h2 class="fp-h2">What changes when the statement does the typing</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Catch-up stops taking a weekend</h3>
                    <p>A backlog of statements becomes a job you finish in one sitting rather than one you keep putting off.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>The numbers match the bank</h3>
                    <p>Working from the statement itself means your books agree with your account instead of drifting apart.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('shield', 20) ?></div>
                    <h3>No bank credentials involved</h3>
                    <p>You export a file and import it. Argo Books never asks for your online banking login.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('search', 20) ?></div>
                    <h3>Nothing gets counted twice</h3>
                    <p>Duplicate detection means a receipt you scanned last week does not reappear when the statement lands.</p>
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
                    <p>One import a month is often the whole of your bookkeeping.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>High transaction volumes that would be unreasonable to type in.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Fuel, materials and supplier payments brought in together.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Anyone catching up</h3>
                    <p>Months of backlog cleared file by file rather than line by line.</p>
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
                <h2 class="fp-h2">Other ways to get data in</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../receipt-scanning/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('receipt-scan', 20) ?></div>
                    <h3>AI receipt scanning</h3>
                    <p>Photograph a receipt and the expense writes itself, line items and all.</p>
                </a>
                <a href="../spreadsheet-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document-upload', 20) ?></div>
                    <h3>Spreadsheet import</h3>
                    <p>Bring across records you already keep in Excel or CSV.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Where every imported transaction lands.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>More history in means a sharper forecast out.</p>
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
                <h2>Stop typing your bank statement in</h2>
                <p>Download Argo Books and import your first statement today. Free plan, no credit card, and your data stays on your own machine.</p>
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
