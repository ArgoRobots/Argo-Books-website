<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];

// One array drives both the visible accordion and the FAQPage schema.
$faqs = [
    [
        'q' => 'What file formats does spreadsheet import support?',
        'a' => 'Argo Books supports Excel (.xlsx) and CSV files. Drag and drop your file and Argo Books detects your columns, maps them to the right fields, and imports everything. No manual formatting or templates needed.',
    ],
    [
        'q' => 'What types of data can I import?',
        'a' => 'You can import customers, products, expenses, revenue, invoices, and more. Argo Books reads your column headers and figures out what each spreadsheet contains, whether you\'re moving from another tool or cleaning up old spreadsheets.',
    ],
    [
        'q' => 'Do I need to manually map columns?',
        'a' => 'Usually not. Argo Books reads your column headers and maps them to the right fields for you. You can review and adjust the mapping before importing, but most imports go through with a quick confirmation.',
    ],
    [
        'q' => 'How many records can I import per month?',
        'a' => 'The Free plan includes <?= $argo_import_limit ?> spreadsheet imports per month, which is plenty for getting started or migrating in batches. Premium users have no limit. Each file counts as one import, no matter how many rows it contains.',
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
    <meta name="description" content="Drop an Excel or CSV file into Argo Books and your customers, products, invoices, and expenses are mapped and imported for you, with no manual setup.">
    <meta name="keywords" content="spreadsheet import, CSV import software, Excel import tool, automatic column mapping, data migration tool, bulk data import, spreadsheet to accounting, business data import, Excel to bookkeeping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Spreadsheet Import | Argo Books">
    <meta property="og:description" content="Drop a spreadsheet, get clean records. Argo Books imports your customers, products, invoices, and expenses from Excel or CSV files automatically.">
    <meta property="og:url" content="https://argorobots.com/features/spreadsheet-import/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Spreadsheet Import | Argo Books">
    <meta name="twitter:description" content="Drop a spreadsheet, get clean records. Argo Books imports your customers, products, invoices, and expenses from Excel or CSV files automatically.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/spreadsheet-import/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Spreadsheet Import" => "/features/spreadsheet-import/"]) ?></script>

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
            "description": "Drop an Excel or CSV file into Argo Books and your customers, products, invoices, and expenses are mapped and imported for you, with no manual setup.",
            "featureList": "Spreadsheet import, AI column mapping, Data validation, Excel and CSV support"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Spreadsheet Import | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&amp;family=IBM+Plex+Mono:wght@400;500;600&amp;family=IBM+Plex+Sans:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="../../resources/styles/typography.css">
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
                    <h1>Bring your<br>spreadsheet with you.</h1>
                    <p class="fp-hero-sub">Drop in the Excel or CSV file you already keep and Argo Books works out which column is which, checks the data, and shows you exactly what it will create before anything is saved.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and the file is read on your own computer.</p>
                </div>

                <div class="fp-hero-still">
                    <img src="../../resources/images/features/ai-column-mapping.svg"
                         alt="Argo Books reading a spreadsheet and mapping its columns to the right fields automatically"
                         width="600" height="500" fetchpriority="high">
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
                <h2 class="fp-h2">Three steps, whatever your columns are called</h2>
                <p class="fp-lede">Most import tools make you describe your own spreadsheet to them first. This one reads it.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Drop the file in</h3>
                    <p>Excel or CSV, however you have laid it out. There is no template to match first.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>It maps the columns</h3>
                    <p>AI works out which column holds the date, the amount, the supplier and the rest, whatever you happened to call them.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Check and import</h3>
                    <p>You see what will be created, with anything questionable flagged, and nothing is saved until you say so.</p>
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
                    <div class="fp-eyebrow">Real files, not tidy ones</div>
                    <h2 class="fp-h2">It copes with how spreadsheets actually look</h2>
                    <p class="fp-lede">Merged cells, a title row above the headings, inconsistent date formats, blank rows in the middle. The files people really keep are messy, and the import is built for those rather than for a clean example.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Merged cells and stray header rows handled</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Mixed date and number formats normalised</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Everything validated and previewed before import</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/ai-import-validation.svg"
                         alt="Argo Books validating imported spreadsheet rows and flagging the ones that need attention"
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
                <h2>Move your records across in minutes</h2>
                <p>No account, no credit card, and no template to fill in first.</p>
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
                <h2 class="fp-h2">What changes when switching is easy</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Years of history in one go</h3>
                    <p>The reason people stay on a spreadsheet is the cost of moving off it. That cost is a file drop.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>Nothing saves until you approve it</h3>
                    <p>The preview shows exactly what will be created, so a bad import is caught before it becomes a cleanup job.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('search', 20) ?></div>
                    <h3>Bad rows get flagged, not swallowed</h3>
                    <p>Missing amounts and impossible dates are surfaced for you to fix rather than imported quietly.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('bolt', 20) ?></div>
                    <h3>No mapping screen to fight</h3>
                    <p>The columns are worked out from the file itself, which is the step that usually makes people give up.</p>
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
                    <h3><?= svg_icon('document', 19) ?> Anyone switching over</h3>
                    <p>Move off a spreadsheet without retyping the last three years.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Freelancers</h3>
                    <p>One file, and your whole history is in the app.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Product lists and sales exports brought in as they are.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Businesses leaving another tool</h3>
                    <p>Export from the old system, import here, keep the history.</p>
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
                <a href="../bank-statement-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('bank', 20) ?></div>
                    <h3>Bank statement import</h3>
                    <p>A month of banking in one file, matched against what you already have.</p>
                </a>
                <a href="../receipt-scanning/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('receipt-scan', 20) ?></div>
                    <h3>AI receipt scanning</h3>
                    <p>Photograph a receipt and the expense record writes itself.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Where your imported records land.</p>
                </a>
                <a href="../inventory-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('package', 20) ?></div>
                    <h3>Inventory management</h3>
                    <p>Import a product list and start tracking stock from it.</p>
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
                <h2>Bring your history with you</h2>
                <p>Download Argo Books and import your spreadsheet today. Free plan, no credit card, and your data stays on your own machine.</p>
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
