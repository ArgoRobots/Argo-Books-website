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
        content="Drop an Excel or CSV file into Argo Books and your customers, products, invoices, and expenses are mapped and imported for you — no manual setup.">
    <meta name="keywords"
        content="spreadsheet import, CSV import software, Excel import tool, automatic column mapping, data migration tool, bulk data import, spreadsheet to accounting, business data import, Excel to bookkeeping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Spreadsheet Import — Argo Books">
    <meta property="og:description"
        content="Drop a spreadsheet, get clean records. Argo Books imports your customers, products, invoices, and expenses from Excel or CSV files automatically.">
    <meta property="og:url" content="https://argorobots.com/features/spreadsheet-import/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Spreadsheet+Import&subtitle=Drop+a+spreadsheet%2C+get+clean+records.+Automatic+column+mapping+for+your+business+data.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Spreadsheet Import — Argo Books">
    <meta name="twitter:description"
        content="Drop a spreadsheet, get clean records. Argo Books imports your customers, products, invoices, and expenses from Excel or CSV files automatically.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Spreadsheet+Import&subtitle=Drop+a+spreadsheet%2C+get+clean+records.+Automatic+column+mapping+for+your+business+data.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/spreadsheet-import/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Spreadsheet Import", "item": "https://argorobots.com/features/spreadsheet-import/"}
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
                    "name": "What file formats does spreadsheet import support?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books supports Excel (.xlsx) and CSV files. Drag and drop your file and Argo Books detects your columns, maps them to the right fields, and imports everything. No manual formatting or templates needed."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What types of data can I import?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can import customers, products, expenses, revenue, invoices, and more. Argo Books reads your column headers and figures out what each spreadsheet contains — whether you're moving from another tool or cleaning up old spreadsheets."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need to manually map columns?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Usually not. Argo Books reads your column headers and maps them to the right fields for you. You can review and adjust the mapping before importing, but most imports go through with a quick confirmation."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How many records can I import per month?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "The Free plan includes 100 spreadsheet imports per month, which is plenty for getting started or migrating in batches. Premium users have no limit. Each file counts as one import, no matter how many rows it contains."
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
            "description": "Import spreadsheets into Argo Books with automatic column mapping. Supports Excel and CSV files with clean, validated imports.",
            "featureList": "Automatic column mapping, Excel and CSV support, Data validation before import, One-click undo for every import"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Spreadsheet Import — Argo Books</title>

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
                <?= svg_icon('document-upload', 16) ?>
                <span>Spreadsheet Import</span>
            </div>
            <h1 class="animate-fade-in">Drop a spreadsheet, get clean records</h1>
            <p class="hero-subtitle animate-fade-in">Import customers, products, expenses, and invoices from any Excel or CSV file. Argo Books reads your columns and puts everything in the right place — no manual setup.</p>
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
                    <h2>Moving your data shouldn't take days</h2>
                    <p>Switching software or tidying up old spreadsheets usually means hours of copying and pasting. Argo Books reads your file, figures out what each column is, and imports everything for you. Days of work, done in minutes.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Handles customers, products, invoices, expenses, suppliers, and more</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Reads your column headers — even unusual ones — and maps them for you</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Excel files with multiple sheets import in one go</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-column-mapping.svg" alt="Column mapping showing source columns matched to target fields with confidence scores" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Try it free</h3>
                <p>Download Argo Books and import your first spreadsheet in under five minutes. No credit card needed.</p>
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
                    <h3>Any format</h3>
                    <p>Excel or CSV, one sheet or many</p>
                </div>
                <div class="highlight-item">
                    <h3>Minutes</h3>
                    <p>From dropping the file to finished import</p>
                </div>
                <div class="highlight-item">
                    <h3>Full undo</h3>
                    <p>Roll back any import with one click</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Handles any file
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Any File Works</span>
                    <h2>Clean files or messy files — both import</h2>
                    <p>Real-world spreadsheets come in all shapes. Tidy files with clear headers import quickly. Messy files — merged cells, odd formatting, pivot-table layouts — still work, because Argo Books reads them row by row until everything makes sense.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Quick mapping for well-organized files</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Careful row-by-row reading for messy files</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Confidence scores show how sure each mapping is</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-import-analysis.svg" alt="Import analysis showing 13 sheets detected with 1,882 rows and mapping results" loading="lazy">
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
                <h2 class="section-title">Three steps from spreadsheet to clean data</h2>
                <p class="section-desc">Go from a messy Excel file to organized records in minutes. The mapping is handled for you — just review and confirm.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Drop in your file</h3>
                    <p>Upload an Excel (.xlsx) or CSV file. Multi-sheet workbooks work too.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Columns get mapped</h3>
                    <p>Your columns are matched to the right fields and your data is checked — with confidence scores you can review.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Review and import</h3>
                    <p>Confirm the mapping, fix any issues, and import. A summary shows what was added, updated, or skipped.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop re-typing your spreadsheets</h3>
                <p>Every row imported, mapped, and checked — ready to use.</p>
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
         DETAIL SECTION 3: Validation + Smart Handling
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Checked Before Import</span>
                    <h2>Catch problems before they hit your books</h2>
                    <p>Before anything is imported, Argo Books checks your file for problems. A missing supplier on an expense? A customer name that doesn't exist yet? You'll see it — and you can create the missing records with one click.</p>
                    <p>You decide what gets fixed and what gets skipped. Afterward, a summary shows what was added, updated, or skipped — and why. Every import creates an undo point, so you can roll back anytime.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Spots problems before anything is imported</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Create missing customers, suppliers, and categories in one click</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Skips duplicates so records don't double up</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>One-click undo for every import</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-import-validation.svg" alt="Import validation showing auto-fixable issues with missing references" loading="lazy">
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
                <h2 class="section-title">More than just a file importer</h2>
                <p class="section-desc">Import isn't just about moving data — it changes how you migrate, consolidate, and keep track of your records.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('clock', 22) ?>
                    </div>
                    <h3>Migrate in minutes, not days</h3>
                    <p>Switching systems? Import years of data in one session — not weeks of typing.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>No setup knowledge needed</h3>
                    <p>You don't need to know field names or formats. Argo Books figures out what each column means, even with unusual headers.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Safe imports with undo</h3>
                    <p>Every import saves a snapshot first. If something looks off, roll back to exactly where you were.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Handles messy data</h3>
                    <p>Merged cells, uneven formatting, pivot tables — real-world files work just fine.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('table', 22) ?>
                    </div>
                    <h3>Multi-sheet in one import</h3>
                    <p>Customers on one tab, products on another? Both come in at once.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Products get categorized</h3>
                    <p>Imported products are sorted into categories automatically — no tidying up afterward.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Import your existing data today</h3>
                <p>Move your records into Argo Books in a few minutes.</p>
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
                <h2 class="section-title">Built for real-world use</h2>
                <p class="section-desc">Whether you're switching software, cleaning up spreadsheets, or starting fresh — import handles it.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Switching from other software
                    </h3>
                    <p>Export your data from QuickBooks, Wave, or anything else as a spreadsheet. Argo Books puts customers, invoices, expenses, and everything else in the right place.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Bulk product setup
                    </h3>
                    <p>Got a supplier price list in Excel? Import hundreds of products — with prices, descriptions, and categories — in one go.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Consolidating scattered records
                    </h3>
                    <p>Years of customer lists and expense logs spread across multiple spreadsheets? Combine them into one organized system.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('document', 22) ?>
                        Accountant handoffs
                    </h3>
                    <p>Accountants send spreadsheets. Import their reports directly — no reformatting needed.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 4: Privacy & Security
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your spreadsheets stay on your computer</h2>
                    <p>Argo Books is a desktop app, not a cloud service. Your spreadsheets are read on your own computer. Only a small sample is sent out for analysis — never the whole file.</p>
                    <p>Just the column headers and a handful of sample rows leave your machine. Your full dataset stays with you.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app — files are read on your computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Only a small sample leaves your machine, never the full file</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>All imported records stay on your device</span>
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
                <p class="section-desc">Spreadsheet import works even better alongside these.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Imported transactions flow straight into your expense and revenue records, categorized and ready for reports.</p>
                </a>
                <a href="../receipt-scanning/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('receipt-scan-detail', 22) ?>
                    </div>
                    <h3>Receipt Scanning</h3>
                    <p>Import your historical data as spreadsheets, then scan new receipts going forward — past and present covered.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>More data means better forecasts. Importing your full history helps predictions stay accurate from day one.</p>
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
                    <h2>Ready to import your data?</h2>
                    <p>Download Argo Books and get your records imported in minutes. Free to start — no credit card, no trial.</p>
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
