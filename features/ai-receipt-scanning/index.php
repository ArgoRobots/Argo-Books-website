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
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Argo Books' AI receipt scanner eliminates manual data entry and keeps your books accurate.">
    <meta name="keywords"
        content="AI receipt scanner, OCR receipt, automatic receipt scanning, receipt data extraction, receipt management software, scan receipts app, receipt OCR software, digital receipt organizer, receipt tracker, expense receipt app">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="AI Receipt Scanning — Argo Books">
    <meta property="og:description"
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry and keep your books accurate.">
    <meta property="og:url" content="https://argorobots.com/features/ai-receipt-scanning/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=AI+Receipt+Scanning&subtitle=Snap+a+photo%2C+let+AI+do+the+rest.+Automatic+data+extraction+for+your+business+receipts.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AI Receipt Scanning — Argo Books">
    <meta name="twitter:description"
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=AI+Receipt+Scanning&subtitle=Snap+a+photo%2C+let+AI+do+the+rest.+Automatic+data+extraction+for+your+business+receipts.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/ai-receipt-scanning/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "AI Receipt Scanning", "item": "https://argorobots.com/features/ai-receipt-scanning/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>AI Receipt Scanning — Argo Books</title>

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
                <?= svg_icon('receipt-scan-detail', 16) ?>
                <span>AI Receipt Scanning</span>
            </div>
            <h1 class="animate-fade-in">Turn any receipt into organized data — instantly</h1>
            <p class="hero-subtitle animate-fade-in">Stop typing. Start scanning. Argo Books reads your receipts with AI and pulls out every detail — store name, items, totals, and taxes — so your books stay accurate without the busywork.</p>
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
                    <h2>Manual receipt entry is costing you hours every week</h2>
                    <p>If you're still typing receipt data into spreadsheets or accounting software by hand, you're not just wasting time — you're introducing errors. Misread totals, forgotten line items, and lost receipts add up fast. By tax season, you're scrambling to reconstruct months of expenses from a shoebox of faded paper.</p>
                    <p>Argo Books fixes this with AI-powered receipt scanning that reads your receipts in seconds and logs every detail directly into your expense records — accurately, every time.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic extraction of store name, date, and address</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Line-by-line item detection with individual prices</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Tax, tip, and total calculation verification</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works with printed receipts, handwritten notes, and faded ink</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <!-- REPLACE: Screenshot of the Argo Books receipt scanning interface showing a receipt being scanned with extracted data fields -->
                    <img src="../../resources/images/dashboard.webp" alt="AI Receipt Scanning interface in Argo Books showing automatic data extraction" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Try AI receipt scanning free</h3>
                <p>Download Argo Books and scan your first receipt in under a minute. No credit card required.</p>
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
                    <h3>98%</h3>
                    <p>Accuracy on printed receipts</p>
                </div>
                <div class="highlight-item">
                    <h3>&lt; 3 seconds</h3>
                    <p>Average processing time per receipt</p>
                </div>
                <div class="highlight-item">
                    <h3>0</h3>
                    <p>Receipts you need to type manually</p>
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
                    <h2>AI that reads receipts like a human — but faster</h2>
                    <p>Our receipt scanner uses advanced optical character recognition (OCR) combined with a large language model to understand your receipts contextually — not just reading characters, but understanding what they mean. It knows the difference between a subtotal and a tax line. It can parse multi-column layouts, handle faded thermal paper, and even read handwritten amounts.</p>
                    <p>After scanning, the AI structures the data into clean, organized fields: vendor, date, individual items, quantities, prices, subtotal, tax, and total. You review the results, make any corrections, and save — all in under a minute.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>OCR + AI language model for contextual understanding</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Handles faded thermal paper, wrinkled receipts, and poor lighting</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multi-currency support for international purchases</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Review and edit before saving — you're always in control</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <!-- REPLACE: Screenshot showing the extracted data fields after a scan is complete (the review/edit screen) -->
                    <img src="../../resources/images/dashboard.webp" alt="AI receipt data extraction results showing structured fields" loading="lazy">
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
                <h2 class="section-title">Three steps to paperless receipts</h2>
                <p class="section-desc">From paper receipt to organized expense record in under a minute. No typing, no guessing, no lost receipts.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Snap a photo or upload an image</h3>
                    <p>Take a photo of your receipt with your phone and transfer it, or drag and drop an image file directly into Argo Books. We support JPEG, PNG, WebP, and most common image formats. You can even scan multiple receipts in a row.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>AI reads and structures the data</h3>
                    <p>Our AI engine reads the receipt and pulls out every relevant detail — vendor name, date, individual line items with prices, subtotals, tax breakdowns, tips, and the total amount. It handles messy formatting, faded ink, and even handwriting.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Review, categorize, and save</h3>
                    <p>Check the extracted data, assign a category (like "Office Supplies" or "Travel"), make any quick corrections, and save. The receipt image is attached to the record automatically — so you have a digital copy ready for tax time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop losing receipts</h3>
                <p>Every receipt scanned, categorized, and stored — ready for tax season. Get started in minutes.</p>
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
         DETAIL SECTION 3: Organized + Searchable
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Stay Organized</span>
                    <h2>Every receipt, filed and searchable</h2>
                    <p>Once scanned, your receipts become part of your financial records. Every expense is categorized, timestamped, and linked to the original receipt image. No more digging through shoeboxes before tax season — everything is organized and searchable from day one.</p>
                    <p>Need to find that $342 equipment purchase from last July? Search by amount, date, vendor, or category and find it in seconds. Argo Books turns your chaotic receipt pile into a clean, audit-ready expense log.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Receipts attached to expense records automatically</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Search by vendor, amount, date, or category</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Original receipt images stored alongside extracted data</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Export expense reports for accountants and tax filing</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <!-- REPLACE: Screenshot of expense list/search view with receipt thumbnails attached to records -->
                    <img src="../../resources/images/dashboard.webp" alt="Organized expense records with receipt images attached" loading="lazy">
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
                <h2 class="section-title">More than just a scanner</h2>
                <p class="section-desc">AI receipt scanning isn't just about saving time — it changes how you manage your business finances.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('clock', 22) ?>
                    </div>
                    <h3>Save hours every month</h3>
                    <p>Stop typing receipt data by hand. Most small business owners spend 3-5 hours per month on manual receipt entry. AI scanning cuts that to minutes.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>Eliminate data entry errors</h3>
                    <p>Manual entry leads to typos, wrong totals, and missing items. The AI reads receipts accurately and catches math errors humans miss.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Tax-ready records all year</h3>
                    <p>Every scanned receipt is categorized and stored with the original image. When tax season arrives, your records are already organized and audit-ready.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Process receipts in seconds</h3>
                    <p>No more "I'll do it later" — scan receipts the moment you get them. The entire process from photo to saved expense takes under 30 seconds.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('search', 22) ?>
                    </div>
                    <h3>Find any receipt instantly</h3>
                    <p>Search your expense records by vendor, amount, date, or category. No more digging through files — every receipt is digital and searchable.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Better expense insights</h3>
                    <p>When all your receipts are digitized, you can see spending patterns you'd otherwise miss — like which vendors cost you the most or where costs are rising.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Save 3-5 hours every month</h3>
                <p>Join small business owners who eliminated manual receipt entry with Argo Books.</p>
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
                <h2 class="section-title">Built for the way you actually work</h2>
                <p class="section-desc">Whether you're a freelancer with a few receipts a week or a retail shop with dozens per day, AI scanning adapts to your workflow.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Freelancers & consultants
                    </h3>
                    <p>Track client-related expenses by scanning receipts on the go. Attach scanned receipts to invoices as proof of expenses for client reimbursement.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Retail & e-commerce
                    </h3>
                    <p>Scan supplier invoices and purchase receipts to keep inventory costs accurate. Know exactly what you paid for every product on your shelves.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Service businesses
                    </h3>
                    <p>Scan fuel receipts, material purchases, and equipment costs. Categorize expenses by job or project to track profitability on every engagement.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('document', 22) ?>
                        Anyone at tax time
                    </h3>
                    <p>No more January panic. Every receipt is already scanned, categorized, and stored — ready to hand to your accountant or file with your return.</p>
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
                    <h2>Your receipts stay on your computer</h2>
                    <p>Unlike cloud-based receipt scanners that upload your financial data to third-party servers, Argo Books is a desktop application. Your receipts and expense data are stored locally on your device — not on someone else's cloud.</p>
                    <p>The AI processing happens securely, and your scanned receipt images are saved alongside your financial records on your own machine. You control your data, always.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app — your data stays on your computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No third-party cloud storage of your financial documents</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Receipt images stored locally alongside expense records</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works offline — scan and save without internet</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <!-- REPLACE: Screenshot showing local data storage or the privacy/security aspect of the app -->
                    <img src="../../resources/images/dashboard.webp" alt="Argo Books desktop application with local data storage" loading="lazy">
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
                <p class="section-desc">AI receipt scanning is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Scanned receipts flow directly into your expense records. Every transaction is categorized and ready for reports.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>More expense data means better forecasts. AI scanning feeds your analytics engine with accurate, detailed financial data.</p>
                </a>
                <a href="../ai-spreadsheet-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>AI Spreadsheet Import</h3>
                    <p>Have existing receipt data in spreadsheets? Import it instantly with AI-powered column mapping — then scan new receipts going forward.</p>
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
                    <h2>Ready to ditch manual data entry?</h2>
                    <p>Download Argo Books and start scanning receipts with AI. Free to get started — no credit card, no trial period.</p>
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