<?php
// tools/index.php
//
// Free tools hub. A simple directory page that links out to the free,
// standalone tools we offer (invoice generator, invoice templates).
// Served at argorobots.com/tools/ via Apache DirectoryIndex.
//
// Unlike the tool pages themselves (which use the isolated invoice-generator
// layout), this hub is a normal marketing page: real site header + footer so
// visitors can navigate the rest of the site from here.

require_once __DIR__ . '/../resources/icons.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('tools_hub');
}

// Each entry renders one card. `icon` / `icon_class` reuse the shared
// svg_icon() set and the about-us feature-icon color variants.
$tools = [
    [
        'name'        => 'Free Receipt Scanner',
        'description' => 'Snap or upload a receipt and get every line item, each tax line, and the total in seconds. No signup, nothing stored.',
        'href'        => '../free-receipt-scanner/',
        'cta'         => 'Open the scanner',
        'icon'        => 'receipt',
        'icon_class'  => '',
    ],
    [
        'name'        => 'Free Profit Analyzer',
        'description' => 'Upload a spreadsheet and instantly see where your business is losing money: fees, unprofitable products, and your true margin. No signup.',
        'href'        => '../profit-analyzer/',
        'cta'         => 'Open the analyzer',
        'icon'        => 'analytics',
        'icon_class'  => 'green',
    ],
    [
        'name'        => 'Free Invoice Generator',
        'description' => 'Create and download a professional invoice in seconds. No signup, no watermark. Export to PDF or Word.',
        'href'        => '../invoice-generator/',
        'cta'         => 'Open the generator',
        'icon'        => 'document',
        'icon_class'  => '',
    ],
    [
        'name'        => 'Free Estimate Generator',
        'description' => 'Build a professional estimate or quote in seconds. No signup, no watermark. Export to PDF or Word.',
        'href'        => '../estimate-generator/',
        'cta'         => 'Open the generator',
        'icon'        => 'document',
        'icon_class'  => 'green',
    ],
    [
        'name'        => 'Free Purchase Order Generator',
        'description' => 'Send vendors a clean purchase order in seconds. No signup, no watermark. Export to PDF or Word.',
        'href'        => '../purchase-order-generator/',
        'cta'         => 'Open the generator',
        'icon'        => 'package',
        'icon_class'  => 'amber',
    ],
    [
        'name'        => 'Free Self-Employed Tax Calculator',
        'description' => 'Estimate your self-employment taxes and how much to set aside each quarter. United States and Canada, 2026.',
        'href'        => '../self-employed-tax-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'pie-chart',
        'icon_class'  => 'indigo',
    ],
    [
        'name'        => 'Free Etsy Fee Calculator',
        'description' => 'See every fee Etsy takes from a sale and what you actually keep. Or enter the profit you want and get the price to list at. US, Canada, UK, and Australia.',
        'href'        => '../etsy-fee-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'tag',
        'icon_class'  => 'purple',
    ],
    [
        'name'        => 'Free Craft Pricing Calculator',
        'description' => 'Price your handmade products to actually pay you. Add materials, labour, and markup to get a selling price, profit, and margin.',
        'href'        => '../craft-pricing-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'shopping-bag',
        'icon_class'  => 'cyan',
    ],
    [
        'name'        => 'Free Candle Pricing Calculator',
        'description' => 'Price candles from what a batch really costs in wax, wicks, jars, and fragrance. Get cost per candle, a selling price, and your margin.',
        'href'        => '../candle-pricing-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'bolt',
        'icon_class'  => 'amber',
    ],
    [
        'name'        => 'Free Soap Pricing Calculator',
        'description' => 'Cost a whole batch of handmade soap and get your true cost per bar, a price, and your real margin. Not a lye calculator.',
        'href'        => '../soap-pricing-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'spray-bottle',
        'icon_class'  => 'cyan',
    ],
    [
        'name'        => 'Free Tumbler Pricing Calculator',
        'description' => 'Sublimation, vinyl, or epoxy. Work out what a custom tumbler costs once your time is counted, then what to charge.',
        'href'        => '../tumbler-pricing-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'shopping-bag',
        'icon_class'  => 'purple',
    ],
    [
        'name'        => 'Free Cake Pricing Calculator',
        'description' => 'Ingredients, decorating time, board and box, delivery. What a cake really costs you, and what to charge for it.',
        'href'        => '../cake-pricing-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'star',
        'icon_class'  => 'indigo',
    ],
    [
        'name'        => 'Free Craft Fair Calculator',
        'description' => 'How many sales cover your booth fee and fuel, and whether the market day actually paid you once your hours are counted.',
        'href'        => '../craft-fair-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'map-pin',
        'icon_class'  => 'green',
    ],
    [
        'name'        => 'Free Hourly Rate Calculator',
        'description' => 'What to charge when self-employed, once unbillable time, business costs, and tax are all covered. Most people undercharge by half.',
        'href'        => '../hourly-rate-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'clock',
        'icon_class'  => '',
    ],
    [
        'name'        => 'Free Mileage Deduction Calculator',
        'description' => 'What your business driving is worth at tax time. Handles the 2026 US mid-year rate change and the tiered rates in Canada and the UK.',
        'href'        => '../mileage-deduction-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'car',
        'icon_class'  => 'green',
    ],
    [
        'name'        => 'Free Late Fee Calculator',
        'description' => 'Interest and fees owed on an overdue invoice, simple or compounding, and what the total comes to today.',
        'href'        => '../late-fee-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'clock',
        'icon_class'  => 'amber',
    ],
    [
        'name'        => 'Free Markup vs Margin Calculator',
        'description' => 'Enter any two of cost, price, markup, or margin and get the rest. They are not the same number, and mixing them up costs money.',
        'href'        => '../markup-margin-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'trending-up',
        'icon_class'  => 'indigo',
    ],
    [
        'name'        => 'Free Break-Even Calculator',
        'description' => 'How many sales it takes before you stop losing money, and what each one is worth after that point.',
        'href'        => '../break-even-calculator/',
        'cta'         => 'Open the calculator',
        'icon'        => 'bar-chart',
        'icon_class'  => 'cyan',
    ],
    [
        'name'        => 'Free Invoice Templates',
        'description' => 'Ready-made invoice templates in PDF, Word, Excel, Google Docs, and Google Sheets. Pick a style and start billing.',
        'href'        => '../invoice-template/',
        'cta'         => 'Browse templates',
        'icon'        => 'grid',
        'icon_class'  => 'purple',
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
        content="Free tools for small businesses from Argo Books. Generate invoices, grab invoice templates, and more. No signup required.">
    <meta name="keywords"
        content="free business tools, free invoice generator, free invoice templates, small business tools, argo books tools">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Free Tools - Argo Books">
    <meta property="og:description"
        content="Free tools for small businesses from Argo Books. Generate invoices, grab invoice templates, and more. No signup required.">
    <meta property="og:url" content="https://argorobots.com/tools/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Free Tools - Argo Books">
    <meta name="twitter:description"
        content="Free tools for small businesses from Argo Books. Generate invoices, grab invoice templates, and more. No signup required.">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/tools/">

    <!-- CollectionPage Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "Free Tools",
            "url": "https://argorobots.com/tools/",
            "description": "Free tools for small businesses from Argo Books."
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Free Tools - Argo Books</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
    <!-- Brand typefaces (Fraunces display + IBM Plex Sans body), matched to the rest of the site -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../resources/styles/typography.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Free Tools</h1>
            <p class="hero-subtitle animate-fade-in">Handy tools for small businesses. Free to use, no signup required.</p>
        </div>
    </section>

    <!-- Tools Grid -->
    <section class="tools-section">
        <div class="container">
            <div class="tools-grid">
                <?php foreach ($tools as $tool): ?>
                    <a class="tool-card animate-on-scroll" href="<?= htmlspecialchars($tool['href']) ?>">
                        <div class="feature-icon <?= htmlspecialchars($tool['icon_class']) ?>">
                            <?= svg_icon($tool['icon'], null, '', 1.5) ?>
                        </div>
                        <h2><?= htmlspecialchars($tool['name']) ?></h2>
                        <p><?= htmlspecialchars($tool['description']) ?></p>
                        <span class="tool-card-cta">
                            <span><?= htmlspecialchars($tool['cta']) ?></span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    </main>

    <!-- Contact + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-card animate-on-scroll">
                    <h2>Want the full toolkit?</h2>
                    <p>Argo Books is free accounting software that does all of this and more: invoicing, payments, receipt scanning, and reporting.</p>
                    <a href="../downloads/" class="btn btn-primary">
                        <span>Download Argo Books</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
        </footer>
    </div>

    <script>
        // Scroll animations
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
