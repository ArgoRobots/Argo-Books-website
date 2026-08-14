<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans            = get_plan_features();
$pricing          = get_pricing_config();
$argo_monthly     = (int) $pricing['premium_monthly_price'];
$wave_pro         = competitor_price('wave', 'pro');
$wave_receipt_mo  = competitor_price('wave', 'receipt_addon', 'monthly');
$wave_receipt_yr  = competitor_price('wave', 'receipt_addon', 'yearly');
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
        content="Argo Books vs Wave: Compare features, pricing, and ease of use. See why small businesses choose Argo Books over Wave for offline-capable finance and inventory management.">
    <meta name="keywords"
        content="Argo Books vs Wave, Wave alternative, Wave accounting alternative, simple bookkeeping software, small business accounting, affordable accounting software, offline accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books vs Wave: Offline-Capable Finance & Inventory Management">
    <meta property="og:description"
        content="Compare Argo Books and Wave side by side. See why small businesses are choosing Argo Books for offline-capable finance and inventory management.">
    <meta property="og:url" content="https://argorobots.com/compare/argo-books-vs-wave/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books vs Wave: Offline-Capable Finance & Inventory Management">
    <meta name="twitter:description"
        content="Compare Argo Books and Wave side by side. See why small businesses are choosing Argo Books for offline-capable finance and inventory management.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/argo-books-vs-wave/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Argo Books vs Wave" => "/compare/argo-books-vs-wave/"]) ?></script>

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Is Argo Books really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. Wave also offers a free Starter plan, but features like auto bank import require the Pro plan at $<?= $wave_pro ?> CAD/month, and receipt scanning costs another $<?= $wave_receipt_mo ?>/month or $<?= $wave_receipt_yr ?>/year on the free Starter plan (it's included on Pro)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. Wave is cloud-only and requires a constant internet connection to access your data."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books support bank transaction imports?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not yet. Wave's Pro plan includes automatic bank transaction imports, which is convenient for matching transactions against your books. If automatic bank feeds are critical for your workflow, Wave may be a better fit for now. Argo Books is always adding new features based on user feedback."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to Wave?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Both offer free plans. Wave's Starter is free with manual transaction entry, and the Pro plan is $<?= $wave_pro ?> CAD/month for auto bank imports. Receipt scanning costs another $<?= $wave_receipt_mo ?>/month or $<?= $wave_receipt_yr ?>/year on Wave's free Starter plan (it's included on Pro). Argo Books Premium is just $<?= $argo_monthly ?> CAD/month with unlimited invoicing, AI receipt scanning included, and predictive analytics, less than half of Wave's Pro plan."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. Wave is web-based and also has a mobile app for iOS and Android."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books vs Wave: Offline-Capable & Feature-Rich | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>

    <!-- Brand typefaces, matched to the home page so this comparison reads as
         the same product. Fraunces = display, IBM Plex Sans = body. -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body class="compare-page">
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="hero-content animate-fade-in">
                <span class="hero-eyebrow">Wave alternative</span>
                <h1>Argo Books <span class="text-gradient">vs Wave</span></h1>
                <p class="hero-subtitle">Both free to start, but built for different businesses. Argo Books does more for product businesses: inventory, offline access, and AI receipt scanning included free.</p>
                <div class="hero-ctas">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Try Argo Books Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../../pricing/" class="btn-cta btn-cta-outline">
                        <span>View Pricing</span>
                    </a>
                </div>
            </div>
            <div class="hero-visual animate-fade-in">
                <div class="hero-device">
                    <img src="../../resources/images/dashboard.webp"
                         srcset="../../resources/images/dashboard-800.webp 800w, ../../resources/images/dashboard-1200.webp 1200w, ../../resources/images/dashboard-1600.webp 1600w"
                         sizes="(max-width: 900px) 90vw, 540px"
                         alt="The Argo Books dashboard" width="2400" height="1528" fetchpriority="high">
                </div>
            </div>
        </div>
    </section>

    <!-- Differences: narrative + product visual -->
    <section class="differences">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">The short version</span>
                <h2>What's the difference between Argo Books and Wave?</h2>
                <p class="section-desc">Both are free to start. The difference is what you get. Wave keeps things simple for freelancers and service businesses; Argo Books does more for product businesses, with inventory, offline access, and AI receipt scanning included free, for less on the paid plan.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Wave?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Everything in one clean app.</strong> Invoicing, expenses, receipts, inventory, and forecasting together, with no accounting jargon and no double-entry to learn.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A free plan that does more.</strong> All the core features forever, plus inventory and AI receipt scanning at no extra cost. Wave's free Starter charges extra for receipt scanning.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your books open instantly and keep working with no internet, and your data stays on your machine. Wave is cloud-only.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI that's included, not upsold.</strong> Receipt scanning, spreadsheet import, and predictive analytics come built in. Wave charges about $<?= $wave_receipt_mo ?>/month for receipt scanning on its free plan.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month, less than Wave Pro at $<?= $wave_pro ?> CAD/month. No per-add-on fees.</span>
                        </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative cost comparison. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both free to start. Argo does more for less on paid.</text>

                                <!-- Argo Free -->
                                <text x="40" y="130" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="118" width="4" height="20" rx="2" fill="#cbd5e1"/>
                                <text x="188" y="133" font-size="13" font-weight="700" fill="#0f172a">$0</text>

                                <!-- Wave Starter -->
                                <text x="40" y="174" font-size="13" font-weight="600" fill="#0f172a">Wave Starter</text>
                                <rect x="175" y="162" width="4" height="20" rx="2" fill="#cbd5e1"/>
                                <text x="188" y="177" font-size="13" font-weight="700" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium -->
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="175" y="206" width="<?= $argo_monthly * 13 ?>" height="20" rx="4" fill="#3f63e8"/>
                                <text x="<?= 175 + $argo_monthly * 13 + 10 ?>" y="221" font-size="13" font-weight="700" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Wave Pro -->
                                <text x="40" y="262" font-size="13" font-weight="600" fill="#0f172a">Wave Pro</text>
                                <rect x="175" y="250" width="<?= $wave_pro * 13 ?>" height="20" rx="4" fill="#ef4444"/>
                                <text x="<?= 175 + $wave_pro * 13 + 10 ?>" y="265" font-size="13" font-weight="700" fill="#0f172a">$<?= $wave_pro ?></text>

                                <!-- Divider -->
                                <line x1="40" y1="300" x2="600" y2="300" stroke="#f1f5f9" stroke-width="1"/>

                                <!-- Receipt scanning row -->
                                <text x="40" y="338" font-size="14" font-weight="600" fill="#0f172a">Receipt scanning</text>
                                <rect x="40" y="352" width="132" height="28" rx="7" fill="#dcfce7"/>
                                <text x="106" y="371" text-anchor="middle" font-size="13" font-weight="600" fill="#15803d">Free on Argo</text>
                                <rect x="184" y="352" width="180" height="28" rx="7" fill="#fee2e2"/>
                                <text x="274" y="371" text-anchor="middle" font-size="13" font-weight="600" fill="#b91c1c">+$<?= $wave_receipt_mo ?>/mo on Wave</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Scanning costs extra</span>
                        <span class="diff-callout-sub">Wave charges about $<?= $wave_receipt_mo ?>/month for receipt scanning Argo includes free</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature showcase (shared partial, also used on the landing page) -->
    <?php include __DIR__ . '/../../resources/sections/feature-tour.php'; ?>

    <!-- Feature Comparison Table -->
    <section class="comparison-table-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Feature Comparison</span>
                <h2>Side by side</h2>
            </div>
            <div class="table-wrapper animate-on-scroll">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th class="feature-col">Feature</th>
                            <th class="brand-col">Argo Free<span class="th-sub">$0 forever</span></th>
                            <th class="brand-col">Argo Books Premium<span class="th-sub">$<?= $argo_monthly ?> CAD/month</span></th>
                            <th class="brand-col">Wave<span class="th-sub">Pro: $<?= $wave_pro ?> CAD/month</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Expense &amp; revenue tracking</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Financial reports</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Invoicing &amp; payments</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Desktop app (offline-capable)</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>No accounting knowledge required</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Unlimited products</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Inventory management</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>AI receipt scanning</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>AI spreadsheet import</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Predictive analytics</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Biometric login security</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Auto bank transaction import</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Mobile app</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Payroll</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Pros & Cons -->
    <section class="pros-cons-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">The honest verdict</span>
                <h2>Argo Books vs Wave: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan that does more</strong>, with inventory and AI receipt scanning included at no extra cost</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>No accounting jargon</strong>, built for business owners rather than accountants</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics included</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month, less than Wave Pro at $<?= $wave_pro ?></span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No automatic bank transaction import yet</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No mobile app yet, Wave has iOS and Android apps</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No payroll yet, so Wave is the better fit if you run payroll today</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Wave cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Receipt scanning costs extra</strong>, about $<?= $wave_receipt_mo ?>/month on the free Starter plan</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, no offline access and no desktop app</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No inventory</strong>, spreadsheet import, predictive analytics, or biometric login</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Wave pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>A genuinely free Starter plan with no time limit</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Simple and quick to set up, great for freelancers and service businesses</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Mobile apps for iOS and Android, plus built-in payroll</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Differences -->
    <section class="key-differences">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why Switch?</span>
                <h2>Built for product businesses, not just service providers</h2>
                <p class="section-desc">Wave is great for freelancers and service businesses. Argo Books is built for small businesses that sell products and need inventory management, offline access, and predictive analytics.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>Inventory management</h3>
                    <p>Wave has no inventory features at all. Argo Books Premium includes full inventory management, so you can track stock levels alongside your finances.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Works offline</h3>
                    <p>Wave is cloud-only: no internet, no access. Argo Books is a desktop app that works offline, so you're never locked out of your own data.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon green">
                        <?= svg_icon('map-pin', 30, '', 1.5) ?>
                    </div>
                    <h3>Made in Canada</h3>
                    <p>Built by a Canadian startup that understands Canadian small businesses. Our pricing is in CAD, and our team is based in Saskatchewan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Honest Take -->
    <section class="honest-take">
        <div class="container">
            <div class="honest-card animate-on-scroll">
                <div class="honest-icon">
                    <?= svg_icon('info', 28) ?>
                </div>
                <h3>An honest take</h3>
                <p>Wave is an excellent free option for freelancers and service-based businesses that need invoicing, basic accounting, and bank transaction imports. If those are your core needs, Wave is a solid choice.</p>
                <p>But if you sell physical products and need inventory management, offline access, predictive analytics, or biometric security (features Wave doesn't offer), Argo Books is built for you.</p>
                <a href="../../downloads/" class="btn-cta btn-cta-primary honest-take-cta">
                    <span>Get Started Now</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Other comparisons -->
    <section class="other-comparisons">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Keep comparing</span>
                <h2>How does Argo Books compare to other accounting software?</h2>
            </div>
            <div class="compare-cards animate-on-scroll">
                <?php
                $other_comparisons = [
                    'argo-books-vs-quickbooks'     => 'Argo Books vs. QuickBooks',
                    'argo-books-vs-freshbooks'     => 'Argo Books vs. FreshBooks',
                    'argo-books-vs-xero'           => 'Argo Books vs. Xero',
                    'zipbooks-alternatives'        => 'ZipBooks alternatives',
                    'odoo-accounting-alternatives' => 'Odoo accounting alternatives',
                ];
                foreach ($other_comparisons as $slug => $name): ?>
                <a class="compare-card" href="../<?= $slug ?>/">
                    <span><?= $name ?></span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Is Argo Books really free?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning.</p>
                            <p>Wave also offers a free Starter plan, but features like auto bank import require the Pro plan at $<?= $wave_pro ?> CAD/month, and receipt scanning costs another $<?= $wave_receipt_mo ?>/month or $<?= $wave_receipt_yr ?>/year on the free Starter plan (it's included on Pro).</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>Wave is cloud-only and requires a constant internet connection to access your data.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books support bank transaction imports?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Not yet. Wave's Pro plan includes automatic bank transaction imports, which is convenient for matching transactions against your books. If automatic bank feeds are critical for your workflow, Wave may be a better fit for now.</p>
                            <p>We're always adding new features based on user feedback.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to Wave?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Both offer free plans. Wave's Starter is free with manual transaction entry, and the Pro plan is $<?= $wave_pro ?> CAD/month for auto bank imports. Receipt scanning costs another $<?= $wave_receipt_mo ?>/month or $<?= $wave_receipt_yr ?>/year on Wave's free Starter plan (it's included on Pro).</p>
                            <p>Argo Books Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong> with unlimited invoicing, AI receipt scanning included, and predictive analytics, less than half of Wave's Pro plan.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong>, <strong>macOS</strong>, and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>Wave is web-based and also has a mobile app for iOS and Android.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            echo argo_faq_grid($faqs); ?>
        </div>
    </section>

    </main>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to try a more capable free option?</h2>
                    <p>Download Argo Books for free and see the difference for yourself.</p>
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
            // Scroll animations
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
    <script src="../../resources/scripts/feature-tour.js"></script>
</body>

</html>
