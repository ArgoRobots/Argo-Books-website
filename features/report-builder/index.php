<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_cfg = get_pricing_config();
$argo_monthly = (int) $argo_cfg['premium_monthly_price'];
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
        content="Build professional accounting reports in Argo Books: Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary. A drag-and-drop designer, your branding, and clean PDF export. Free to use.">
    <meta name="keywords"
        content="accounting report software, income statement software, balance sheet software, general ledger software, financial report builder, tax summary report, report designer, free accounting reports, cash flow statement software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Report Builder | Argo Books">
    <meta property="og:description"
        content="Build Income Statements, Balance Sheets, and more from your own data, design them your way, and export a clean PDF. Free to use.">
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
    <meta name="twitter:description"
        content="Build Income Statements, Balance Sheets, and more from your own data, design them your way, and export a clean PDF. Free to use.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/report-builder/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Report Builder", "item": "https://argorobots.com/features/report-builder/"}
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
                    "name": "What reports can I create in Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books includes the core financial statements: Income Statement, Balance Sheet, Cash Flow Statement, General Ledger, AR Aging, Tax Summary, and Sales by Product, plus analytics-style overview templates. You can also start from a blank report and build your own."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I customize how a report looks?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. A three-step designer lets you drag, resize, align, and arrange charts, tables, labels, and images on the page, with snapping, undo and redo, and multi-page layouts. You control the page size, orientation, margins, colors, and your branded header and footer."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What can I export a report to?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Finished reports export as a PDF for printing and sharing, or as a high-quality PNG or JPEG image. The PDF is a true multi-page document with your branding on every page."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is the report builder a paid feature?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. The full report builder, including every accounting statement and the designer, is part of Argo Books at no cost, with no premium plan required and no usage limit."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it use the right tax terms for my country?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books labels tax lines with the right terminology for your country, such as GST/HST in Canada, VAT in the UK and EU, or Sales Tax in the US, and it adjusts statement wording to match common accounting conventions."
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
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Build professional accounting reports in Argo Books, including Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary, with a drag-and-drop designer, branded headers, per-country tax terms, and PDF export.",
            "featureList": "Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, Tax Summary, Drag-and-drop report designer, Branded headers and footers, Per-country tax terminology, PDF, PNG and JPEG export"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Report Builder | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
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
            <h1 class="animate-fade-in">Build professional financial reports, your way</h1>
            <p class="hero-subtitle animate-fade-in">Pick a template and your Income Statement, Balance Sheet, or tax summary is built from your own data in a couple of clicks. Fine-tune the layout only if you want to, then export a clean, branded PDF, free to use.</p>
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
         DETAIL SECTION 1: Problem + Solution
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">The Problem</span>
                    <h2>Real accounting reports, without the accountant</h2>
                    <p>Getting a proper Income Statement or Balance Sheet usually means wrestling with a spreadsheet or paying someone to make one. In Argo Books you just pick the report you want and it's ready in a couple of clicks, built straight from your own records with no formulas and no setup, so the numbers are always current and add up.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Built automatically from your own expenses, revenue, and invoices</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Or start from a blank report and build your own</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/report-types.svg" alt="Report template picker showing Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, and Tax Summary" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Run your first report free</h3>
                <p>Download Argo Books and generate an Income Statement or Balance Sheet in minutes. No credit card needed.</p>
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
                    <h3>Every statement</h3>
                    <p>From Income Statement to Tax Summary</p>
                </div>
                <div class="highlight-item">
                    <h3>Drag-and-drop</h3>
                    <p>Design each report exactly your way</p>
                </div>
                <div class="highlight-item">
                    <h3>Free</h3>
                    <p>No premium plan, no usage limits</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: The Designer (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">The Designer</span>
                    <h2>Design each report exactly how you want</h2>
                    <p>Every template is ready to export the moment you pick it, but when you do want to change how a report looks, the designer gives you full control. Drop in charts, tables, labels, and images, then drag, resize, align, and arrange everything on the page. Grid snapping, undo and redo, and multi-page layouts are all built in.</p>
                    <p>Set the page size, orientation, margins, and colors, and switch your branded header and footer on or off, all with a live preview of the finished page.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Drag, resize, align, and snap elements to a grid</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Add charts, tables, labels, images, and summaries</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Full undo and redo, with layouts that span multiple pages</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/report-designer.svg" alt="Report designer with an elements panel, a live report canvas, and a page settings properties panel" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS, 3 Steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">Three steps from data to finished report</h2>
                <p class="section-desc">Pick a template and it's ready to export. Step two is only there for when you want to change how the report looks.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Pick a template</h3>
                    <p>Choose a statement or a blank report, name it, and set the date range you want to cover.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Design the layout</h3>
                    <p>Arrange charts, tables, and text on the page, then set your branding, page size, and margins.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Preview and export</h3>
                    <p>Preview every page, then export a clean PDF, PNG, or JPEG, ready to print or send.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Reports that look like a pro made them</h3>
                <p>Your branding, your layout, your numbers, in a clean PDF.</p>
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
         DETAIL SECTION 3: Professional + accurate
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Looks Professional</span>
                    <h2>Professional reports, ready for tax time</h2>
                    <p>Every report carries your company header, with your logo, name, address, and contact details, plus a footer and page numbers. The result looks like something an accountant handed you, not a spreadsheet printout.</p>
                    <p>Argo Books uses the right tax terms for your country, from GST/HST to VAT to Sales Tax, and converts figures to your display currency with an "Amounts in" note so it's always clear which currency you're reading.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Your logo, company details, and page numbers on every page</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>The right tax terms for your country, from GST/HST to VAT</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Figures shown in your display currency, clearly labeled</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/report-balance-sheet.svg" alt="A finished Balance Sheet with a branded company header, GST/HST line, and an Amounts in CAD note" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         BENEFITS GRID
         ============================================= -->
    <section class="benefits-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why It Matters</span>
                <h2 class="section-title">More than a report generator</h2>
                <p class="section-desc">The reports competitors lock behind a subscription, ready to design and export for free.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('report', 22) ?>
                    </div>
                    <h3>Every core statement</h3>
                    <p>Income Statement, Balance Sheet, Cash Flow, General Ledger, AR Aging, Tax Summary, and Sales by Product.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Design it your way</h3>
                    <p>A drag-and-drop designer with charts, tables, alignment tools, snapping, and multi-page layouts.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Your branding</h3>
                    <p>Your logo, company details, and page numbers on a clean, professional header and footer.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>The right tax terms</h3>
                    <p>GST/HST, VAT, or Sales Tax, chosen automatically for your country, with matching statement wording.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('refresh', 22) ?>
                    </div>
                    <h3>Multi-currency</h3>
                    <p>Figures convert to your display currency and are clearly labeled, so mixed-currency books still read cleanly.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>Free, with no limits</h3>
                    <p>The full report builder is part of Argo Books. No premium plan, no per-report cost, no cap.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Generate your reports today</h3>
                <p>Turn your records into professional statements in a few minutes.</p>
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
                <h2 class="section-title">Reports for every moment that matters</h2>
                <p class="section-desc">Tax time, funding, or just knowing where you stand, there's a statement for it.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Tax time
                    </h3>
                    <p>Hand your accountant a Tax Summary and Income Statement that are already done, with the right tax labels for your country.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('trending-up', 22) ?>
                        Loans and investors
                    </h3>
                    <p>Show a lender or investor a clean, branded Balance Sheet and Income Statement that make your business look the part.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('analytics', 22) ?>
                        Knowing your numbers
                    </h3>
                    <p>Run an Income Statement any time to see whether you're actually making money, not just guessing from your bank balance.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('clock', 22) ?>
                        Chasing receivables
                    </h3>
                    <p>Use AR Aging to see exactly who owes you and for how long, so nothing slips past the point of collecting.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 4: Privacy (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your reports never leave your computer</h2>
                    <p>Argo Books is a desktop app, not a cloud service. Your reports are built and rendered right on your own machine, from data that stays on your device. Nothing is uploaded to generate a statement.</p>
                    <p>The finished PDF or image is saved wherever you choose, and stays entirely under your control.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app: reports are generated on your computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Your financial data never leaves your device to build a report</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Exported files are saved locally, wherever you want them</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/privacy-local-storage.svg" alt="Your data stays local: encrypted, offline-capable, no cloud" loading="lazy">
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
                <p class="section-desc">Your reports are only as good as the data behind them.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Every expense and revenue you record flows straight into your Income Statement, Balance Sheet, and tax reports.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>Drop forecast and trend charts straight onto a report to pair your statements with a look at what's ahead.</p>
                </a>
                <a href="../invoicing/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Invoicing</h3>
                    <p>Unpaid invoices feed your AR Aging report, so you always know who still owes you and for how long.</p>
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
                    <h2>Ready to build your reports?</h2>
                    <p>Download Argo Books and turn your records into professional financial statements in minutes. Free to start, with no credit card and no trial.</p>
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
            <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
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
