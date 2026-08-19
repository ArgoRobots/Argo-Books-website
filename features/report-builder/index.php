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
        'q' => 'What reports can I create in Argo Books?',
        'a' => 'Argo Books includes the core financial statements: Income Statement, Balance Sheet, Cash Flow Statement, General Ledger, AR Aging, Tax Summary, and Sales by Product, plus analytics-style overview templates. You can also start from a blank report and build your own.',
    ],
    [
        'q' => 'Can I customize how a report looks?',
        'a' => 'Yes. A three-step designer lets you drag, resize, align, and arrange charts, tables, labels, and images on the page, with snapping, undo and redo, and multi-page layouts. You control the page size, orientation, margins, colors, and your branded header and footer.',
    ],
    [
        'q' => 'What can I export a report to?',
        'a' => 'Finished reports export as a PDF for printing and sharing, or as a high-quality PNG or JPEG image. The PDF is a true multi-page document with your branding on every page.',
    ],
    [
        'q' => 'Is the report builder a paid feature?',
        'a' => 'No. The full report builder, including every accounting statement and the designer, is part of Argo Books at no cost, with no premium plan required and no usage limit.',
    ],
    [
        'q' => 'Does it use the right tax terms for my country?',
        'a' => 'Yes. Argo Books labels tax lines with the right terminology for your country, such as GST/HST in Canada, VAT in the UK and EU, or Sales Tax in the US, and it adjusts statement wording to match common accounting conventions.',
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
    <meta name="description" content="Build professional accounting reports in Argo Books: Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary. A drag-and-drop designer, your branding, and clean PDF export. Free to use.">
    <meta name="keywords" content="accounting report software, income statement software, balance sheet software, general ledger software, financial report builder, tax summary report, report designer, free accounting reports, cash flow statement software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Report Builder | Argo Books">
    <meta property="og:description" content="Build Income Statements, Balance Sheets, and more from your own data, design them your way, and export a clean PDF. Free to use.">
    <meta property="og:url" content="https://argorobots.com/features/report-builder/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Report Builder | Argo Books">
    <meta name="twitter:description" content="Build Income Statements, Balance Sheets, and more from your own data, design them your way, and export a clean PDF. Free to use.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/report-builder/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Report Builder" => "/features/report-builder/"]) ?></script>

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
            "description": "Build professional accounting reports in Argo Books: Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary. A drag-and-drop designer, your branding, and clean PDF export. Free to use.",
            "featureList": "Custom report builder, Profit and loss statements, Balance sheets, Export to PDF and spreadsheet"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Report Builder | Argo Books</title>

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
                    <h1>The statement<br>your accountant asked for.</h1>
                    <p class="fp-hero-sub">Profit and loss, balance sheet, and whatever else you need, built from the records already in your books and exported in a format anyone can open.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your reports are generated on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="report">
                    <?= argo_feature_demo('report') ?>
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
                <h2 class="fp-h2">Three steps to a finished report</h2>
                <p class="fp-lede">A report is only worth having if producing it does not take longer than reading it.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Pick the report</h3>
                    <p>Profit and loss, balance sheet, expense summary, or a layout you build yourself.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Set the period</h3>
                    <p>A month, a quarter, a financial year, or any range you choose.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Export and send it</h3>
                    <p>PDF for reading, spreadsheet for working with. Both come out ready to hand over.</p>
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
                    <div class="fp-eyebrow">Not just the standard set</div>
                    <h2 class="fp-h2">Build the report you actually need</h2>
                    <p class="fp-lede">The standard statements cover what an accountant asks for. The builder covers everything else: group by customer, by category or by period, filter to the part you care about, and save the layout so next quarter takes one click.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Profit and loss and balance sheet out of the box</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Custom layouts you can save and reuse</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Export to PDF or spreadsheet</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/report-types.svg"
                         alt="The report types available in Argo Books, including profit and loss, balance sheet and expense summaries"
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
                <h2>Produce your first statement in minutes</h2>
                <p>No account, no credit card, and no accounting knowledge needed.</p>
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
                <h2 class="fp-h2">What changes when reports are one click</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Year end stops being a project</h3>
                    <p>When the statement takes a minute to produce, handing something over to an accountant is not a week of preparation.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>The figures come from the records</h3>
                    <p>Reports are generated from your actual transactions, so there is no re-keying step to get wrong.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('search', 20) ?></div>
                    <h3>You can answer your own questions</h3>
                    <p>Group and filter the way you think about the business rather than the way a fixed template does.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>Periods are comparable</h3>
                    <p>Run the same report across quarters and the change is visible instead of inferred.</p>
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
                    <p>A profit and loss for the year without paying someone to assemble it.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Margin and category reporting across a lot of transactions.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Service businesses</h3>
                    <p>Profitability by job or customer, not just overall.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Anyone with an accountant</h3>
                    <p>Hand over a statement in a format they can work with immediately.</p>
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
                <h2 class="fp-h2">What reports are built from</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>The transaction records every report is generated from.</p>
                </a>
                <a href="../invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Billed and paid figures flow straight into your statements.</p>
                </a>
                <a href="../inventory-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('package', 20) ?></div>
                    <h3>Inventory management</h3>
                    <p>Stock value and cost of goods feed the balance sheet.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>Look forward as well as back, from the same records.</p>
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
                <h2>Stop assembling statements by hand</h2>
                <p>Download Argo Books and produce your first report today. Free plan, no credit card, and your data stays on your own machine.</p>
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
