<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$argo_yearly  = (int) $pricing['premium_yearly_price'];
$sage_pro     = competitor_price('sage', 'pro');     // 68  (monthly-equivalent; $814/yr, 1 user)
$sage_premium = competitor_price('sage', 'premium'); // 102 ($1,219/yr, 2 users)
$sage_quantum = competitor_price('sage', 'quantum'); // 470 ($5,636/yr, 5 users)
$sage_autoentry = competitor_price('sage', 'autoentry'); // 145 (AutoEntry document-capture add-on, 500 credits)
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
        content="Sage 50 alternatives for small businesses that want desktop accounting without the price or the complexity. Compare cross-platform options on features and cost.">
    <meta name="keywords"
        content="Sage 50 alternatives, Sage 50 alternative, Sage alternative, desktop accounting software, cross-platform accounting software, cheap accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Sage 50 Alternatives: A Fraction of the Price">
    <meta property="og:description"
        content="Sage 50 is powerful, expensive and Windows-only. Here are the modern cross-platform alternatives, minus the complexity.">
    <meta property="og:url" content="https://argorobots.com/compare/sage-50-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sage 50 Alternatives: A Fraction of the Price">
    <meta name="twitter:description"
        content="Sage 50 is powerful, expensive and Windows-only. Here are the modern cross-platform alternatives, minus the complexity.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/sage-50-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Sage 50 alternatives" => "/compare/sage-50-alternatives/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. Sage 50 has no free plan, only a time-limited trial before paid plans that start around $<?= $sage_pro ?> CAD/month (billed annually)."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books run on Linux?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books runs natively on Windows and Linux from the same app. Sage 50 is a Windows-only desktop program, so Linux users are left out. If you are not on Windows, Argo Books is the more flexible choice."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Argo Books as powerful as Sage 50?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "It depends on what you need. Sage 50 is deeper for complex accounting: advanced inventory with serial numbers and bill of materials, job and project costing, departmental accounting, and payroll add-ons. Argo Books is deliberately simpler. For most owners who want clean books, invoicing, expenses, inventory, and reports without an accounting degree, Argo Books does the job at a fraction of the price. If you run a large or complex operation that needs Sage's depth, Sage 50 may be the better fit."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to Sage 50?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is dramatically more affordable. The Free plan covers most small business needs at no cost, and Premium is just $<?= $argo_monthly ?> CAD/month (or $<?= $argo_yearly ?>/year). Sage 50 is billed annually and runs from about $814/year (roughly $<?= $sage_pro ?>/month, 1 user) up to $5,636/year (roughly $<?= $sage_quantum ?>/month, 5 users). Sage 50 also has no free plan."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows and Linux. Because it's a desktop app, it's fast and responsive, and it works offline. Sage 50 is also a desktop app, but it is Windows-only, so Linux users are not supported."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Sage 50 Alternatives: Modern, Cheaper, Cross-Platform | Argo Books</title>

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
                <span class="hero-eyebrow">Sage 50 alternatives</span>
                <h1>Sage 50 <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">A simpler, more affordable way to manage your small business finances. All the essentials, none of Sage's price tag, learning curve, or Windows-only limits.</p>
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
                <h2>What's the difference between Argo Books and Sage 50?</h2>
                <p class="section-desc">Sage 50 is deep, mature desktop accounting built for established or complex businesses, but it's pricey, Windows-only, and has a steep learning curve. Argo Books is a fraction of the cost, modern and simple, cross-platform, with AI built in, for owners who don't need Sage's depth.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Sage 50?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Everything in one clean app.</strong> Invoicing, expenses, receipts, inventory, and forecasting together, with no accounting jargon and no double-entry to learn.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A genuinely free plan.</strong> All the core features forever, no trial and no credit card. Sage 50 has no free plan, only a time-limited trial.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Modern and cross-platform.</strong> A native desktop app that runs on Windows and Linux, with a clean modern interface. Sage 50 is powerful, but it's Windows-only and its interface looks and feels its age.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI that's included, not upsold.</strong> Receipt scanning, bank-statement import, and spreadsheet import are built into Premium at $<?= $argo_monthly ?>/mo. On Sage 50 that same document capture is a paid add-on, AutoEntry, at about $<?= $sage_autoentry ?> CAD/month for 500 credits.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month. No annual lock-in and none of Sage's four-figure yearly bills.</span>
                        </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs Sage 50's plans (billed annually).</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">Sage 50</text>

                                <!-- Bars: width scaled so $<?= $sage_quantum ?> (widest) = 430px, i.e. ~0.915px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="217" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $<?= $argo_monthly ?> -->
                                <text x="40" y="192" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="180" width="14" height="18" rx="3" fill="#3f63e8"/>
                                <text x="229" y="193" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- Sage Pro $<?= $sage_pro ?> -->
                                <text x="40" y="240" font-size="13" font-weight="600" fill="#0f172a">Sage Pro</text>
                                <rect x="205" y="228" width="62" height="18" rx="3" fill="#ef4444"/>
                                <text x="277" y="241" font-size="13" font-weight="600" fill="#0f172a">$<?= $sage_pro ?></text>

                                <!-- Sage Premium $<?= $sage_premium ?> -->
                                <text x="40" y="284" font-size="13" font-weight="600" fill="#0f172a">Sage Premium</text>
                                <rect x="205" y="272" width="93" height="18" rx="3" fill="#ef4444"/>
                                <text x="308" y="285" font-size="13" font-weight="600" fill="#0f172a">$<?= $sage_premium ?></text>

                                <!-- Sage Quantum $<?= $sage_quantum ?> -->
                                <text x="40" y="328" font-size="13" font-weight="600" fill="#0f172a">Sage Quantum</text>
                                <rect x="205" y="316" width="430" height="18" rx="3" fill="#ef4444"/>
                                <text x="597" y="329" font-size="13" font-weight="600" fill="#ffffff" text-anchor="end">$<?= $sage_quantum ?></text>

                                <!-- Baseline -->
                                <line x1="205" y1="356" x2="205" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Enterprise pricing</span>
                        <span class="diff-callout-sub">Sage 50 runs about $814&ndash;$5,600+ a year</span>
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
                            <th class="brand-col">Sage 50<span class="th-sub">Pro: $<?= $sage_pro ?> CAD/mo (billed annually)</span></th>
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
                            <td>Inventory management</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Desktop app (offline-capable)</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Runs on Windows &amp; Linux</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>No accounting knowledge required</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>AI receipt scanning</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
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
                            <td>Advanced inventory (serial/BOM), job costing &amp; payroll</td>
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
                <h2>Argo Books vs Sage 50: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month vs Sage from around $<?= $sage_pro ?>/month (billed yearly)</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Modern and simple</strong>, built for business owners with no accounting degree required</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Truly cross-platform</strong>, runs on Windows and Linux from one app</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics included</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Not as deep as Sage for complex accounting: no job or project costing</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No serial-number or bill-of-materials inventory for advanced stock control</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No departmental accounting or built-in payroll</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Sage 50 cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Expensive</strong>: from around $<?= $sage_pro ?>/month (~$814/yr) up to $5,636/yr, and billed annually</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Windows-only</strong>, so Linux users are left out</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Steep learning curve and a dated interface</strong>: dense menus and toolbars that feel a decade or two behind, built for accountants rather than owners</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No free plan</strong>, only a time-limited trial</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Document capture costs extra</strong>: receipt and statement capture (AutoEntry) is a usage-based add-on at about $<?= $sage_autoentry ?> CAD/month for 500 credits, and there's no predictive analytics</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Sage 50 pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Extremely deep, mature accounting that scales to complex businesses</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Advanced inventory with serial numbers, bill of materials, and multiple locations</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Job and project costing, departmental accounting, and payroll add-ons</span></li>
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
                <p class="section-desc">Both tools are desktop accounting apps, but they focus on different things. Sage 50 shines at deep, complex accounting for established businesses. Argo Books focuses on being simple, affordable, and cross-platform.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>A fraction of the cost</h3>
                    <p>Sage 50 runs from about $814/year up to $5,636/year, billed annually. Argo Books has a free version with core features, and Premium is just $<?= $argo_monthly ?> CAD/month.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Modern &amp; cross-platform</h3>
                    <p>Sage 50 is powerful but Windows-only with a steep learning curve. Argo Books is the opposite: so simple that anyone can keep their own books from day one, with no training and no accounting background, on Windows or Linux.</p>
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
                <p>Sage 50 is deep, mature desktop accounting: advanced inventory, job costing, departmental accounting, and payroll. If you run an established or complex business that genuinely needs that depth, Sage 50 is a powerful tool.</p>
                <p>But if you're a small business that wants clean, simple books without the four-figure yearly bill, the steep learning curve, or the Windows-only limits, and you'd like AI and a free plan, Argo Books is built for you.</p>
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
                    'argo-books-vs-wave'           => 'Argo Books vs. Wave',
                    'argo-books-vs-freshbooks'     => 'Argo Books vs. FreshBooks',
                    'argo-books-vs-xero'           => 'Argo Books vs. Xero',
                    'zipbooks-alternatives'        => 'ZipBooks alternatives',
                    'odoo-accounting-alternatives' => 'Odoo accounting alternatives',
                    'honeybook-alternatives'       => 'HoneyBook alternatives',
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
                            <p>Sage 50 has no free plan, only a time-limited trial before paid plans that start around $<?= $sage_pro ?> CAD/month (billed annually).</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books run on Linux?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books runs natively on <strong>Windows</strong> and <strong>Linux</strong> from the same app.</p>
                            <p>Sage 50 is a Windows-only desktop program, so Linux users are left out. If you're not on Windows, Argo Books is the more flexible choice.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is Argo Books as powerful as Sage 50?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>It depends on what you need. Sage 50 is deeper for complex accounting: advanced inventory with serial numbers and bill of materials, job and project costing, departmental accounting, and payroll add-ons.</p>
                            <p>Argo Books is deliberately simpler. For most owners who want clean books, invoicing, expenses, inventory, and reports without an accounting degree, Argo Books does the job at a fraction of the price. If you run a large or complex operation that needs Sage's depth, Sage 50 may be the better fit.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to Sage 50?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books is dramatically more affordable. The Free plan covers most small business needs at no cost, and Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong> (or $<?= $argo_yearly ?>/year).</p>
                            <p>Sage 50 is billed annually and runs from about $814/year (roughly $<?= $sage_pro ?>/month, 1 user) up to $5,636/year (roughly $<?= $sage_quantum ?>/month, 5 users). Sage 50 also has no free plan.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong> and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, and it works offline.</p>
                            <p>Sage 50 is also a desktop app, but it's Windows-only, so Linux users aren't supported.</p>
                        
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
