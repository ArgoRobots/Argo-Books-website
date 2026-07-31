<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans             = get_plan_features();
$pricing           = get_pricing_config();
$argo_monthly      = (int) $pricing['premium_monthly_price'];
$argo_yearly       = (int) $pricing['premium_yearly_price'];
$zoho_free         = competitor_price('zoho-books', 'free');         // 0
$zoho_standard     = competitor_price('zoho-books', 'standard');     // 15
$zoho_professional = competitor_price('zoho-books', 'professional'); // 30
$zoho_premium      = competitor_price('zoho-books', 'premium');      // 40
$zoho_elite        = competitor_price('zoho-books', 'elite');        // 165
$zoho_ultimate     = competitor_price('zoho-books', 'ultimate');     // 290
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
        content="Argo Books vs Zoho Books: Compare features, pricing, and ease of use. See why small businesses choose Argo Books, a simple offline desktop alternative to Zoho Books with a free plan that has no revenue cap.">
    <meta name="keywords"
        content="Argo Books vs Zoho Books, Zoho Books alternative, Zoho Books alternative Canada, offline accounting software, simple bookkeeping software, desktop accounting app, small business accounting, free accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books vs Zoho Books: A Simpler, Offline Alternative">
    <meta property="og:description"
        content="Compare Argo Books and Zoho Books side by side. See why small businesses choose Argo Books for a simple, offline desktop app with a free plan that has no revenue cap.">
    <meta property="og:url" content="https://argorobots.com/compare/argo-books-vs-zoho-books/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books vs Zoho Books: A Simpler, Offline Alternative">
    <meta name="twitter:description"
        content="Compare Argo Books and Zoho Books side by side. See why small businesses choose Argo Books for a simple, offline desktop app with a free plan that has no revenue cap.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/argo-books-vs-zoho-books/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Argo Books vs Zoho Books" => "/compare/argo-books-vs-zoho-books/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no revenue cap. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. Zoho Books also has a free plan, but it is limited to micro-businesses: it is capped by your annual revenue. Argo's free plan has no revenue cap."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. Zoho Books is cloud-only, so it needs an internet connection and your books live on Zoho's servers."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Argo Books as powerful as Zoho Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Honestly, Zoho Books is broader and deeper, especially at its higher tiers, with multi-currency, projects and time tracking, cashflow forecasting, heavy customization, and a huge integration marketplace. Argo Books is deliberately simpler. It is an offline desktop app, standalone rather than part of a 40-app suite, and it covers what most small businesses actually need: invoicing, expenses, AI receipt scanning, bank matching, inventory, and reports."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to Zoho Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "They are priced about the same. Both are free to start. Argo Premium is $<?= $argo_monthly ?> CAD/month (or $<?= $argo_yearly ?>/year), the same entry price as Zoho Books Standard at $<?= $zoho_standard ?> CAD/month. Zoho's plans then rise to Professional at $<?= $zoho_professional ?> and Premium at $<?= $zoho_premium ?>, and up to $<?= $zoho_elite ?> and $<?= $zoho_ultimate ?> CAD/month for its Elite and Ultimate tiers. The real difference isn't the price, it's what kind of tool each one is."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. Zoho Books is web-based and also has mobile apps for iOS and Android."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books vs Zoho Books: Simpler & Offline | Argo Books</title>

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
                <span class="hero-eyebrow">Zoho Books alternative</span>
                <h1>Argo Books <span class="text-gradient">vs Zoho Books</span></h1>
                <p class="hero-subtitle">A simpler, offline way to manage your small business finances. All the essentials in one native desktop app, with a free plan that has no revenue cap, no 40-app suite to navigate.</p>
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
                <h2>What's the difference between Argo Books and Zoho Books?</h2>
                <p class="section-desc">Both cover the small business basics, both are free to start, and their paid plans begin at about the same price. The difference is what kind of tool each one is. Zoho Books is a powerful, feature-dense cloud app and one piece of a 40-plus app suite; Argo Books is a simple, standalone desktop app that works offline and is built for the owner doing their own books.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Zoho Books?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A free plan with no revenue cap.</strong> All the core features forever, no trial and no credit card. Zoho Books has a free plan too, but it's capped by your annual revenue.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your books open instantly and keep working with no internet, and your data stays on your machine. Zoho Books is cloud-only.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Genuinely simple.</strong> Invoicing, expenses, receipts, inventory, and forecasting in one clean app with no accounting jargon. Zoho Books is powerful, but that power comes with density and a learning curve.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Standalone, not a suite.</strong> Argo is one focused tool, not a slice of a 40-app ecosystem full of cross-sells and add-ons.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month, with AI receipt scanning included in one flat plan.</span>
                        </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 540" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="538" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="540" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">About the same to start, but Zoho's tiers climb to $<?= $zoho_ultimate ?>.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">Zoho Books</text>

                                <!-- Bars: width scaled so $290 (widest) = 400px, i.e. ~1.38px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="153" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="189" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Premium $15 -->
                                <text x="40" y="199" font-size="13" font-weight="600" fill="#0f172a">Argo Premium</text>
                                <rect x="175" y="186" width="21" height="18" rx="3" fill="#3f63e8"/>
                                <text x="208" y="199" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Zoho Free $0 -->
                                <text x="40" y="245" font-size="13" font-weight="600" fill="#0f172a">Zoho Free</text>
                                <rect x="175" y="232" width="2" height="18" rx="3" fill="#ef4444"/>
                                <text x="189" y="245" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_free ?></text>

                                <!-- Zoho Standard $15 -->
                                <text x="40" y="291" font-size="13" font-weight="600" fill="#0f172a">Zoho Standard</text>
                                <rect x="175" y="278" width="21" height="18" rx="3" fill="#ef4444"/>
                                <text x="208" y="291" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_standard ?></text>

                                <!-- Zoho Professional $30 -->
                                <text x="40" y="337" font-size="13" font-weight="600" fill="#0f172a">Zoho Professional</text>
                                <rect x="175" y="324" width="41" height="18" rx="3" fill="#ef4444"/>
                                <text x="228" y="337" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_professional ?></text>

                                <!-- Zoho Premium $40 -->
                                <text x="40" y="383" font-size="13" font-weight="600" fill="#0f172a">Zoho Premium</text>
                                <rect x="175" y="370" width="55" height="18" rx="3" fill="#ef4444"/>
                                <text x="242" y="383" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_premium ?></text>

                                <!-- Zoho Elite $165 -->
                                <text x="40" y="429" font-size="13" font-weight="600" fill="#0f172a">Zoho Elite</text>
                                <rect x="175" y="416" width="228" height="18" rx="3" fill="#ef4444"/>
                                <text x="415" y="429" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_elite ?></text>

                                <!-- Zoho Ultimate $290 -->
                                <text x="40" y="475" font-size="13" font-weight="600" fill="#0f172a">Zoho Ultimate</text>
                                <rect x="175" y="462" width="400" height="18" rx="3" fill="#ef4444"/>
                                <text x="587" y="475" font-size="13" font-weight="600" fill="#0f172a">$<?= $zoho_ultimate ?></text>

                                <!-- Baseline -->
                                <line x1="175" y1="486" x2="175" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="538" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Free, but capped</span>
                        <span class="diff-callout-sub">Zoho's free plan is limited by your revenue; Argo's isn't</span>
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
                            <th class="brand-col">Argo Premium<span class="th-sub">$<?= $argo_monthly ?> CAD/month</span></th>
                            <th class="brand-col">Zoho Books<span class="th-sub">Standard: $<?= $zoho_standard ?> CAD/month</span></th>
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
                            <td>AI receipt scanning</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Bank reconciliation</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Inventory management</td>
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
                            <td>Predictive analytics</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Multi-currency</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Projects &amp; time tracking</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Hundreds of third-party integrations</td>
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
                <h2>Argo Books vs Zoho Books: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free plan with no revenue cap</strong>, every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Genuinely simple</strong>, built for business owners rather than accountants</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Standalone</strong>, one focused tool rather than a slice of a 40-app suite</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Canadian (CAD)</strong> with AI receipt scanning included in one flat $<?= $argo_monthly ?>/mo plan</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No multi-currency, so Zoho Books fits better if you bill in several currencies</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No project or time tracking for billable-hours work</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A small integration library, not Zoho's huge marketplace</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Zoho Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, so no offline access and your books live on Zoho's servers</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Free plan is capped</strong> by your annual revenue</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Part of a sprawling ecosystem</strong>, with the upsells and density a 40-app suite brings</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Zoho Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>A genuinely capable free plan and a low starting price</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Deep features at higher tiers: multi-currency, projects, forecasting, heavy customization</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>A huge marketplace of integrations, and it scales as you grow</span></li>
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
                <h2>Everything you need, nothing you don't</h2>
                <p class="section-desc">Both tools work for small businesses, but they focus on different things. Zoho Books goes wide and deep with features and integrations. Argo Books focuses on simplicity, offline access, and staying out of your way.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>Free without the cap</h3>
                    <p>Zoho's free plan is limited by your annual revenue. Argo's free plan has core features with no revenue cap, and Premium is one flat price with AI receipt scanning included.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Works offline</h3>
                    <p>Zoho Books is cloud-only: no internet, no access, and your books live on their servers. Argo Books is a desktop app that works offline, so you're never locked out of your own data.</p>
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
                <p>Zoho Books is a strong, affordable, feature-rich product. It goes broader and deeper than Argo, especially at its higher tiers, with multi-currency, projects and time tracking, cashflow forecasting, heavy customization, and a huge integration marketplace. If you want a powerful cloud suite that scales, Zoho Books is a great tool.</p>
                <p>But if you'd rather have a simple, standalone desktop app that works offline, keeps your books on your own machine, and gives you a free plan with no revenue cap, Argo Books is built for you.</p>
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
                    'argo-books-vs-quickbooks'  => 'QuickBooks',
                    'argo-books-vs-wave'        => 'Wave',
                    'argo-books-vs-freshbooks'  => 'FreshBooks',
                    'argo-books-vs-xero'        => 'Xero',
                    'argo-books-vs-zipbooks'    => 'ZipBooks',
                    'argo-books-vs-odoo'        => 'Odoo',
                    'argo-books-vs-honeybook'   => 'HoneyBook',
                    'argo-books-vs-sage'        => 'Sage',
                ];
                foreach ($other_comparisons as $slug => $name): ?>
                <a class="compare-card" href="../<?= $slug ?>/">
                    <span>Argo Books vs. <?= $name ?></span>
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
                            <p>Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no revenue cap. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning.</p>
                            <p>Zoho Books also has a free plan, but it's limited to micro-businesses: it's capped by your annual revenue. Argo's free plan has no revenue cap.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>Zoho Books is cloud-only, so it needs an internet connection and your books live on Zoho's servers.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is Argo Books as powerful as Zoho Books?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Honestly, Zoho Books is broader and deeper, especially at its higher tiers, with multi-currency, projects and time tracking, cashflow forecasting, heavy customization, and a huge integration marketplace.</p>
                            <p>Argo Books is deliberately simpler. It's an offline desktop app, standalone rather than part of a 40-app suite, and it covers what most small businesses actually need: invoicing, expenses, AI receipt scanning, bank matching, inventory, and reports.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to Zoho Books?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>They're priced about the same, and both are free to start. Argo Premium is <strong>$<?= $argo_monthly ?> CAD/month</strong> (or $<?= $argo_yearly ?>/year), the same entry price as Zoho Books Standard at $<?= $zoho_standard ?> CAD/month.</p>
                            <p>Zoho's plans then rise to Professional at $<?= $zoho_professional ?> and Premium at $<?= $zoho_premium ?>, and up to $<?= $zoho_elite ?> and $<?= $zoho_ultimate ?> CAD/month for its Elite and Ultimate tiers. The real difference isn't the price, it's what kind of tool each one is.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong>, <strong>macOS</strong>, and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>Zoho Books is web-based and also has mobile apps for iOS and Android.</p>
                        
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
                    <h2>Ready to try a simpler alternative?</h2>
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
