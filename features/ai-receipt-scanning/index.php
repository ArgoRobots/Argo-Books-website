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
        content="AI receipt scanner, OCR receipt, automatic receipt scanning, receipt data extraction, receipt management software, scan receipts app, receipt OCR software, digital receipt organizer">

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

    <!-- Hero Section -->
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
            <h1 class="animate-fade-in">AI Receipt Scanning</h1>
            <p class="hero-subtitle animate-fade-in">Snap a photo of any receipt and let AI extract every detail — store name, items, totals, and taxes — in seconds. No more manual data entry.</p>
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

    <!-- Feature Detail Section -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <h2>Stop typing receipts by hand</h2>
                    <p>Every receipt you type manually is time you could spend growing your business. Argo Books uses advanced AI and optical character recognition (OCR) to read your receipts instantly. Just take a photo or upload an image, and the AI does the rest — pulling out vendor names, individual line items, subtotals, taxes, and totals with high accuracy.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic extraction of store name, date, and address</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Line-by-line item detection with prices</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Tax and total calculation verification</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works with printed and handwritten receipts</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Review and edit results before saving</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Supports photos from your phone camera or image files</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <!-- Replace this image with an AI receipt scanning screenshot showing the scan UI -->
                    <img src="../../resources/images/dashboard.webp" alt="AI Receipt Scanning in Argo Books" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">Three steps to paperless receipts</h2>
                <p class="section-desc">Digitizing your receipts takes less than a minute. Here's how it works.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Snap or upload</h3>
                    <p>Take a photo of your receipt with your phone, or drag and drop an image file directly into Argo Books. We support JPEG, PNG, and most common image formats.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>AI extracts the data</h3>
                    <p>Our AI engine reads the receipt and pulls out every detail — vendor name, date, individual items with prices, subtotals, taxes, tips, and the total amount. It handles faded ink, crumpled paper, and even handwriting.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Review and save</h3>
                    <p>Check the extracted data, make any corrections if needed, and save it directly to your expense records. The receipt image is attached automatically for your records — perfect for tax time.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Features Section -->
    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Related Features</span>
                <h2 class="section-title">Works great with</h2>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>Scanned receipts flow directly into your expense records for seamless bookkeeping.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>More expense data means better forecasts. AI scanning feeds your analytics engine automatically.</p>
                </a>
                <a href="../ai-spreadsheet-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>AI Spreadsheet Import</h3>
                    <p>Already have receipts in a spreadsheet? Import them instantly with AI column mapping.</p>
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
                    <p>Download Argo Books and start scanning receipts with AI today. It's free to get started.</p>
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