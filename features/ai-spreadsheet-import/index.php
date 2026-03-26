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
        content="Import spreadsheets into Argo Books with AI-powered column mapping. Supports Excel and CSV. Automatically detects entity types, maps columns, validates data, and imports everything cleanly.">
    <meta name="keywords"
        content="AI spreadsheet import, CSV import software, Excel import tool, automatic column mapping, data migration tool, bulk data import, AI data mapping, spreadsheet to accounting, business data import, Excel to bookkeeping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="AI Spreadsheet Import — Argo Books">
    <meta property="og:description"
        content="Import spreadsheets into Argo Books with AI-powered column mapping. Supports Excel and CSV. Automatic entity detection and clean imports every time.">
    <meta property="og:url" content="https://argorobots.com/features/ai-spreadsheet-import/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=AI+Spreadsheet+Import&subtitle=Drop+a+spreadsheet%2C+let+AI+map+it.+Automatic+column+mapping+for+your+business+data.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AI Spreadsheet Import — Argo Books">
    <meta name="twitter:description"
        content="Import spreadsheets into Argo Books with AI-powered column mapping. Supports Excel and CSV. Automatic entity detection and clean imports.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=AI+Spreadsheet+Import&subtitle=Drop+a+spreadsheet%2C+let+AI+map+it.+Automatic+column+mapping+for+your+business+data.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/ai-spreadsheet-import/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "AI Spreadsheet Import", "item": "https://argorobots.com/features/ai-spreadsheet-import/"}
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
                    "name": "Can Argo Books import data from Excel and CSV files?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Drop any Excel (.xlsx) or CSV file and AI automatically detects your columns, maps them to the right fields, and imports your data cleanly into Argo Books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What types of data can I import with AI Spreadsheet Import?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can import customers, products, expenses, revenue, invoices, and more. AI detects the entity type from your column headers and maps everything automatically."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does the AI spreadsheet import require manual column mapping?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. AI reads your column headers and automatically maps them to Argo Books fields. You can review and adjust the mapping before importing, but manual work is rarely needed."
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
            "description": "Import spreadsheets into Argo Books with AI-powered column mapping. Supports Excel and CSV with automatic entity detection and clean data imports.",
            "featureList": "AI-powered column mapping, Excel and CSV support, Automatic entity type detection, Data validation before import"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">

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
                <span>AI Spreadsheet Import</span>
            </div>
            <h1 class="animate-fade-in">Drop a spreadsheet, let AI do the mapping</h1>
            <p class="hero-subtitle animate-fade-in">Import customers, products, expenses, invoices, and more from any Excel or CSV file. AI reads your columns, detects your data types, and maps everything into Argo Books — no manual mapping required.</p>
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
                    <h2>Migrating business data shouldn't take days</h2>
                    <p>Switching software or consolidating spreadsheets usually means hours of manual data entry — or paying someone to do it. Argo Books uses AI to read your spreadsheets, understand what each column means, and import everything automatically. What used to take days now takes minutes.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic detection of 20+ entity types — customers, products, invoices, expenses, and more</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>AI maps columns to the right fields, even with non-standard headers</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multi-sheet Excel files processed in one import</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-column-mapping.svg" alt="AI column mapping showing source columns mapped to target fields with confidence scores" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Try AI spreadsheet import free</h3>
                <p>Download Argo Books and import your first spreadsheet in under five minutes. No credit card required.</p>
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
                    <h3>20+</h3>
                    <p>Entity types automatically detected</p>
                </div>
                <div class="highlight-item">
                    <h3>Excel &amp; CSV</h3>
                    <p>Multi-sheet and multi-delimiter support</p>
                </div>
                <div class="highlight-item">
                    <h3>0</h3>
                    <p>Rows you need to map by hand</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: How the AI works
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Under the Hood</span>
                    <h2>Two-tier AI that adapts to your data</h2>
                    <p>Not all spreadsheets are the same. Simple files with clean headers get fast, deterministic column mapping. Complex files — with merged cells, inconsistent formatting, or pivot-table layouts — get processed row-by-row with AI that understands context.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Tier 1: Direct column mapping for clean, well-structured files</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Tier 2: AI row processing for complex or messy data</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Confidence scores on every mapping — you see exactly how sure the AI is</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-import-analysis.svg" alt="AI import analysis showing 13 sheets detected with 1,882 rows and direct mapping results" loading="lazy">
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
                <p class="section-desc">From a messy Excel file to organized business records in minutes. AI handles the mapping — you just review and confirm.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Drop in your file</h3>
                    <p>Upload an Excel (.xlsx) or CSV file. Multi-sheet workbooks are fully supported.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>AI maps your columns</h3>
                    <p>The AI detects entity types, maps columns to the right fields, and validates your data — all with confidence scores you can review.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Review and import</h3>
                    <p>Confirm the mapping, auto-fix any issues, and import. Products are auto-categorized and a full summary shows what was added, updated, or skipped.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop re-typing your spreadsheets</h3>
                <p>Every row imported, mapped, and validated — ready to use. Get started in minutes.</p>
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
                    <span class="section-label">Validation Built In</span>
                    <h2>Catch problems before they hit your books</h2>
                    <p>Before anything is imported, Argo Books validates your entire dataset. Missing suppliers referenced in expenses? A customer name that doesn't exist yet? The validation step catches these issues and offers to auto-create missing records so your import goes smoothly.</p>
                    <p>You decide what gets auto-fixed and what gets skipped. After import, a detailed summary shows exactly how many records were inserted, updated, or skipped — and why. Every import creates an undo snapshot, so you can always roll back if needed.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Pre-import validation detects missing references and data issues</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Auto-create missing customers, suppliers, categories, and more</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Skip duplicate records automatically to avoid double-entry</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Undo snapshot created before every import for easy rollback</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/ai-import-validation.svg" alt="Import validation showing 883 auto-fixable issues with missing department references" loading="lazy">
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
                <p class="section-desc">AI spreadsheet import isn't just about moving data — it changes how you migrate, consolidate, and manage your business records.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('clock', 22) ?>
                    </div>
                    <h3>Migrate in minutes, not days</h3>
                    <p>Switching from another system? Import years of customer, product, and transaction data in a single session instead of weeks of manual entry.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>No mapping expertise needed</h3>
                    <p>You don't need to know field names or database schemas. The AI figures out what your columns mean — even when headers are abbreviated or non-standard.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Safe imports with undo</h3>
                    <p>Every import creates a snapshot before changes are applied. If something doesn't look right, roll back to exactly where you were with one click.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Handles messy data gracefully</h3>
                    <p>Real-world spreadsheets aren't perfect. The AI handles merged cells, inconsistent formatting, mixed data types, and pivot-table layouts without breaking.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('table', 22) ?>
                    </div>
                    <h3>Multi-sheet in one import</h3>
                    <p>Excel files with multiple sheets — customers on one tab, products on another, invoices on a third — are analyzed and imported together in a single operation.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Auto-categorize after import</h3>
                    <p>After your products are imported, AI automatically categorizes uncategorized items — so your inventory is organized from day one without extra work.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Import your existing data today</h3>
                <p>Join business owners who migrated their entire operation to Argo Books in minutes.</p>
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
                <h2 class="section-title">Built for real-world data migration</h2>
                <p class="section-desc">Whether you're switching from another tool, consolidating spreadsheets, or onboarding a new business — AI import handles it.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Switching from other software
                    </h3>
                    <p>Export your data from QuickBooks, Wave, or any other tool as a spreadsheet. Argo Books AI maps everything into the right place — customers, invoices, expenses, and all.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Bulk product &amp; inventory setup
                    </h3>
                    <p>Got a supplier price list or product catalog in Excel? Import hundreds of products with prices, descriptions, and categories in one shot instead of adding them one by one.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Consolidating scattered records
                    </h3>
                    <p>Years of customer lists, expense logs, and transaction records spread across multiple spreadsheets? Combine them into a single, organized system with AI-powered import.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('document', 22) ?>
                        Accountant handoffs
                    </h3>
                    <p>Your accountant sends data in spreadsheets. Import their formatted reports directly — no reformatting needed. The AI understands standard accounting layouts.</p>
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
                    <p>Unlike cloud-based import tools that upload your business data to third-party servers, Argo Books is a desktop application. Your spreadsheets are read locally on your device. Only a small sample of your data is sent to the AI for analysis — never the full file.</p>
                    <p>The AI processes column headers and a handful of representative rows to understand your data structure. Your complete dataset never leaves your machine. You control your data, always.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app — your spreadsheets are read locally</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Only a small data sample is sent for AI analysis, never the full file</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Imported data stored locally alongside your financial records</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Country-aware address schemas adapt to your locale automatically</span>
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
                <p class="section-desc">AI spreadsheet import is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Imported transactions flow directly into your expense and revenue records. Every entry is categorized and ready for reports.</p>
                </a>
                <a href="../ai-receipt-scanning/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('receipt-scan-detail', 22) ?>
                    </div>
                    <h3>AI Receipt Scanning</h3>
                    <p>Import your historical data with spreadsheets, then scan new receipts going forward. A complete solution for past and present expenses.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>More data means better forecasts. Importing your full transaction history gives the analytics engine a head start on accurate predictions.</p>
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
                    <p>Download Argo Books and let AI handle the spreadsheet mapping. Free to get started — no credit card, no trial period.</p>
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
