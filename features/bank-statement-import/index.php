<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_cfg = get_pricing_config();
$argo_monthly = (int) $argo_cfg['premium_monthly_price'];
$argo_bank_limit = (int) $argo_cfg['bank_import_monthly_limit'];
$argo_premium_bank_limit = (int) $argo_cfg['premium_bank_import_monthly_limit'];
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
        content="Import a bank statement (CSV, Excel, or PDF) into Argo Books and every line becomes a categorized expense or revenue, ready to review. Match against your books too. No bank login required.">
    <meta name="keywords"
        content="bank statement import, import bank statement CSV, bank statement to accounting software, bank reconciliation software, categorize bank transactions, PDF bank statement import, bank matching, no bank connection bookkeeping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Bank Statement Import | Argo Books">
    <meta property="og:description"
        content="Drop in a bank statement and every line becomes a categorized expense or revenue. Match against your books, all without connecting your bank.">
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
    <meta name="twitter:description"
        content="Drop in a bank statement and every line becomes a categorized expense or revenue. Match against your books, all without connecting your bank.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/bank-statement-import/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Bank Statement Import", "item": "https://argorobots.com/features/bank-statement-import/"}
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
                    "name": "What bank statement formats can I import?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books imports bank statements as CSV, Excel (.xlsx and .xls), or PDF. Export a statement from your online banking, drop the file in, and each transaction line is read and pre-filled for you."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need to connect my bank account?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. There is no bank login, no connection, and no third-party aggregator. Argo Books works entirely from the statement file you export yourself, so nothing is ever linked to your bank."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it record transactions automatically?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Every line is pre-filled for you with a type, category, and supplier or customer, but nothing is saved until you review and confirm. You stay in control of what goes into your books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How is importing different from matching?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Import turns each bank line into a new categorized expense or revenue. Matching compares your statement against records you have already entered, confirms the ones that line up, and shows anything missing from your books. You can use either, or both."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How many bank statements can I import per month?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "The Free plan includes <?= $argo_bank_limit ?> AI bank imports per month and Premium includes <?= $argo_premium_bank_limit ?>. Reading a CSV or Excel file without AI categorization doesn't count against your limit, and even at the limit you can still import and fill lines in by hand."
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
            "description": "Import bank statements into Argo Books from CSV, Excel, or PDF. Each line becomes a categorized expense or revenue, with a separate matching mode to reconcile against your existing records. No bank connection required.",
            "featureList": "CSV, Excel and PDF bank statements, Automatic categorization, Bank matching with month calendar, No bank login, Full undo for every import"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Bank Statement Import | Argo Books</title>

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
            <h1 class="animate-fade-in">Turn your bank statement into clean books</h1>
            <p class="hero-subtitle animate-fade-in">Drop in a CSV, Excel, or PDF statement in any format and every line comes back as a categorized expense or revenue, ready to review. No template to match, no bank login, no copying and pasting.</p>
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
                    <h2>Stop typing your statement line by line</h2>
                    <p>Catching up on a month of banking usually means squinting at a PDF and retyping every row. Argo Books reads the whole statement for you and turns each line into a categorized expense or revenue, with the type, category, and supplier or customer already filled in.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Reads any bank's format, in CSV, Excel, or PDF, with no template to set up</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Each line comes back categorized and ready to review</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No bank login, no connection, no aggregator</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/bank-statement-import.svg" alt="Bank statement import review showing each transaction categorized as an expense or revenue with a category and amount" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Catch up on your books in minutes</h3>
                <p>Download Argo Books and import your first statement in a few minutes. No credit card needed.</p>
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
                    <h3>CSV, Excel, PDF</h3>
                    <p>Whatever your bank exports</p>
                </div>
                <div class="highlight-item">
                    <h3>No bank login</h3>
                    <p>Nothing is ever connected to your bank</p>
                </div>
                <div class="highlight-item">
                    <h3><?= $argo_bank_limit ?> free</h3>
                    <p>AI bank imports every month on the free plan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Matching (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Bank Matching</span>
                    <h2>Check your statement against your books</h2>
                    <p>Already recording as you go? Use matching instead. Argo Books lines up each bank transaction with the expense or revenue you already entered, confirms the ones that agree, and flags anything that's missing, so you can be sure your books and your bank tell the same story.</p>
                    <p>A month calendar view shows matched and unmatched activity at a glance, and a separate list surfaces records in your books that never showed up on the statement.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Matches bank lines to your recorded expenses and revenue</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Month calendar view of matched and unmatched days</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Runs on your computer, with no data sent out to match</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/bank-statement-matching.svg" alt="Bank matching page with a month calendar showing matched, suggested, and unmatched transactions" loading="lazy">
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
                <h2 class="section-title">Three steps from statement to sorted</h2>
                <p class="section-desc">Export, drop, review. Argo Books does the reading and the categorizing so you don't have to.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Export from your bank</h3>
                    <p>Download a statement from your online banking as a CSV, Excel, or PDF file. Any bank works.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Drop in the file</h3>
                    <p>Argo Books reads every line, even statements with header notes or unusual columns, and pre-fills each one.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Review and import</h3>
                    <p>Check the categorized lines, adjust anything you like, and import. Or switch to matching to reconcile against your books.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Never re-type a bank statement again</h3>
                <p>Every transaction read, categorized, and ready to confirm.</p>
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
         DETAIL SECTION 3: Smart pre-fill
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Filled In For You</span>
                    <h2>Every line pre-filled before you review</h2>
                    <p>Argo Books fills each line in two passes. First, it applies rules it has learned from your past imports and matches obvious names against your existing products, suppliers, and customers. Then AI fills in whatever is left, choosing a product and category and a supplier or customer for each transaction.</p>
                    <p>Nothing is written to your books until you confirm, and every import you approve teaches Argo Books your merchants, so the next statement fills in even faster.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Picks the product, category, and supplier or customer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Learns each merchant so repeat statements pre-fill themselves</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>You review and confirm before anything is saved</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/bank-statement-prefill.svg" alt="A single bank line pre-filled with a type, product and category, and supplier, with a learned-rule badge" loading="lazy">
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
                <h2 class="section-title">More than a statement reader</h2>
                <p class="section-desc">It changes how you catch up, how you check your books, and how much of it you have to do by hand.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('clock', 22) ?>
                    </div>
                    <h3>Catch up in minutes</h3>
                    <p>A whole month of transactions, read and categorized, in the time it used to take to do a handful by hand.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>No bank connection</h3>
                    <p>No login, no linked account, no aggregator. You export the file, and that's the only thing Argo Books ever sees.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Handles messy statements</h3>
                    <p>Header notes, odd column names, separate debit and credit columns: Argo Books finds the real data and reads it.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('bank', 22) ?>
                    </div>
                    <h3>Learns your merchants</h3>
                    <p>Confirm an import once and Argo Books remembers it, so the same merchant fills in automatically next time.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('calendar', 22) ?>
                    </div>
                    <h3>Spot what's missing</h3>
                    <p>Matching mode shows any records in your books that never appeared on the statement, so nothing slips through.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('refresh', 22) ?>
                    </div>
                    <h3>Fully undoable</h3>
                    <p>Every import and every match is a single undo step. If something looks off, roll it back and try again.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Import your first statement today</h3>
                <p>Get a month of banking into Argo Books in a few minutes.</p>
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
                <h2 class="section-title">Built for real-world bookkeeping</h2>
                <p class="section-desc">Whether you're catching up, switching over, or double-checking, bank import handles it.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Monthly catch-up
                    </h3>
                    <p>Fell behind? Import last month's statement and turn a whole page of transactions into sorted records in one sitting.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('bank', 22) ?>
                        Bringing in your history
                    </h3>
                    <p>New to Argo Books? Import past statements to build up your records instead of starting from an empty slate.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('refresh', 22) ?>
                        Reconciling your books
                    </h3>
                    <p>Match your statement against what you've recorded to confirm everything agrees and catch anything you missed.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('document', 22) ?>
                        Accountant handoffs
                    </h3>
                    <p>Keep clean, categorized records that line up with your bank, so tax time and accountant reviews go smoothly.</p>
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
                    <h2>Your banking stays yours</h2>
                    <p>Argo Books is a desktop app, not a cloud service, and it never connects to your bank. CSV and Excel statements are read right on your computer, and matching runs entirely on your machine.</p>
                    <p>Only reading a PDF statement and the optional AI categorization send transaction lines out for analysis. There's never a bank login involved, because there's no bank connection to begin with.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No bank login, connection, or third-party aggregator</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>CSV and Excel are read on your own computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Matching happens locally, with nothing sent out</span>
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
                <p class="section-desc">Bank import works even better alongside these.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Imported transactions land straight in your expense and revenue records, categorized and ready for reports.</p>
                </a>
                <a href="../spreadsheet-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>Spreadsheet Import</h3>
                    <p>Bring in customers, products, and more from any spreadsheet, with columns mapped for you automatically.</p>
                </a>
                <a href="../receipt-scanning/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('receipt-scan-detail', 22) ?>
                    </div>
                    <h3>Receipt Scanning</h3>
                    <p>Scan receipts for new purchases as they happen, and import your statement to catch anything that slipped by.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Guides</span>
                <h2 class="section-title">Related guides</h2>
                <p class="section-desc">Go deeper with these step-by-step guides.</p>
            </div>
            <div class="related-grid">
                <a href="../../import-bank-transactions-from-csv-into-accounting-software/" class="related-card animate-on-scroll">
                    <div class="related-card-icon"><?= svg_icon('book', 22) ?></div>
                    <h3>Import bank transactions from CSV</h3>
                    <p>Pull your bank history in with automatic column mapping and categorization.</p>
                </a>
                <a href="../../how-to-move-from-spreadsheets-to-bookkeeping-software/" class="related-card animate-on-scroll">
                    <div class="related-card-icon"><?= svg_icon('book', 22) ?></div>
                    <h3>Move from spreadsheets to software</h3>
                    <p>When and how to make the switch to real bookkeeping software cleanly.</p>
                </a>
                <a href="../../how-to-convert-excel-spreadsheet-to-accounting-software/" class="related-card animate-on-scroll">
                    <div class="related-card-icon"><?= svg_icon('book', 22) ?></div>
                    <h3>Convert Excel to accounting software</h3>
                    <p>Move your existing spreadsheets into real software without losing history.</p>
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
                    <h2>Ready to import your statement?</h2>
                    <p>Download Argo Books and turn your next bank statement into clean, categorized records in minutes. Free to start, with no credit card and no trial.</p>
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
