<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$qb_easystart = competitor_price('quickbooks', 'easystart');
$qb_plus      = competitor_price('quickbooks', 'plus');
$qb_advanced  = competitor_price('quickbooks', 'advanced');
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
        content="A QuickBooks alternative that runs on your own computer with no subscription creep. Compare price, offline access and features against QuickBooks Online.">
    <meta name="keywords"
        content="QuickBooks alternative, QuickBooks alternative without subscription, offline QuickBooks alternative, desktop accounting software, QuickBooks Desktop replacement">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="A QuickBooks Alternative Without the Subscription">
    <meta property="og:description"
        content="QuickBooks raises its price every year and needs a connection. Here is the alternative that runs on your machine for one flat price.">
    <meta property="og:url" content="https://argorobots.com/compare/argo-books-vs-quickbooks/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="A QuickBooks Alternative Without the Subscription">
    <meta name="twitter:description"
        content="QuickBooks raises its price every year and needs a connection. Here is the alternative that runs on your machine for one flat price.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/argo-books-vs-quickbooks/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "QuickBooks alternative" => "/compare/argo-books-vs-quickbooks/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. QuickBooks does not offer a free plan; pricing starts at $<?= $qb_easystart ?> CAD/month after a limited trial."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. QuickBooks Online requires a constant internet connection to access your data."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books support payroll or tax filing?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not yet. Argo Books is focused on finance management, inventory, invoicing, and financial reporting. If payroll and integrated tax filing are critical for your business right now, QuickBooks may be a better fit for those specific needs. Argo Books is always adding new features based on user feedback."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to QuickBooks?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is dramatically more affordable. The Free plan covers most small business needs at no cost. Premium is just $<?= $argo_monthly ?> CAD/month. QuickBooks starts at $<?= $qb_easystart ?> CAD/month for EasyStart and goes up to $<?= $qb_advanced ?>/month for Advanced, and that's before add-ons like payroll. Argo Books has no hidden fees or client limits."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. QuickBooks Online is web-based, and QuickBooks Desktop (Windows only) has been discontinued for new purchases in favor of the cloud version."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>QuickBooks Alternative Without a Subscription | Argo Books</title>

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
    <link rel="stylesheet" href="../../resources/styles/link.css">
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
                <span class="hero-eyebrow">QuickBooks alternative</span>
                <h1>Argo Books <span class="text-gradient">vs QuickBooks</span></h1>
                <p class="hero-subtitle">A simpler, more affordable way to manage your small business finances. All the essentials, none of the accounting jargon or the price creep.</p>
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
                <h2>What's the difference between Argo Books and QuickBooks?</h2>
                <p class="section-desc">Both handle the accounting basics. The difference is who they're built for. QuickBooks is built for accountants and priced for growth; Argo Books is built for the business owner doing their own books, and priced to stay that way.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over QuickBooks?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Everything in one clean app.</strong> Invoicing, expenses, receipts, inventory, and forecasting together, with no accounting jargon and no double-entry to learn.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A genuinely free plan.</strong> All the core features forever, no trial and no credit card. QuickBooks has no free tier at all.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your books open instantly and keep working with no internet, and your data stays on your machine.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI that's included, not upsold.</strong> Receipt scanning, spreadsheet import, and predictive analytics come built in, not bolted on as pricey add-ons.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month. No per-client fees, no upsells, no yearly price hikes.</span>
                        </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative dashboard mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">QuickBooks keeps climbing. Argo Books stays flat.</text>

                                <!-- Legend (under the subtitle, clear of the callout) -->
                                <line x1="40" y1="104" x2="58" y2="104" stroke="#ef4444" stroke-width="2.6" stroke-linecap="round"/>
                                <text x="64" y="109" font-size="13" fill="#0f172a">QuickBooks</text>
                                <line x1="166" y1="104" x2="184" y2="104" stroke="#3f63e8" stroke-width="2.6" stroke-linecap="round"/>
                                <text x="190" y="109" font-size="13" fill="#0f172a">Argo Books</text>

                                <!-- Gridlines + y labels -->
                                <g stroke="#f1f5f9" stroke-width="1">
                                    <line x1="88" y1="194" x2="560" y2="194"/>
                                    <line x1="88" y1="289" x2="560" y2="289"/>
                                    <line x1="88" y1="384" x2="560" y2="384"/>
                                </g>
                                <g font-size="14" font-weight="600" fill="#0f172a" text-anchor="end">
                                    <text x="76" y="199">$<?= $qb_easystart ?></text>
                                    <text x="76" y="294">$<?= $argo_monthly ?></text>
                                    <text x="76" y="389">$0</text>
                                </g>

                                <!-- Savings gap -->
                                <path d="M88 270 L206 251 L324 232 L442 213 L560 194 L560 289 L88 289 Z" fill="#10b981" opacity="0.10"/>
                                <text x="300" y="263" text-anchor="middle" font-size="14" font-weight="600" fill="#15803d">You keep the difference</text>

                                <!-- QuickBooks line -->
                                <polyline points="88,270 206,251 324,232 442,213 560,194" fill="none" stroke="#ef4444" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
                                <g fill="#ef4444"><circle cx="88" cy="270" r="3"/><circle cx="206" cy="251" r="3"/><circle cx="324" cy="232" r="3"/><circle cx="442" cy="213" r="3"/></g>
                                <text x="92" y="260" font-size="12" font-weight="600" fill="#0f172a">was $<?= (int) round($qb_easystart / 1.7) ?></text>

                                <!-- Argo line -->
                                <line x1="88" y1="289" x2="560" y2="289" stroke="#3f63e8" stroke-width="2.6" stroke-linecap="round"/>
                                <g fill="#3f63e8"><circle cx="88" cy="289" r="3"/><circle cx="206" cy="289" r="3"/><circle cx="324" cy="289" r="3"/><circle cx="442" cy="289" r="3"/></g>

                                <!-- End markers -->
                                <circle class="dm-pulse" cx="560" cy="194" r="5" fill="#ef4444"/>
                                <circle cx="560" cy="194" r="3.2" fill="#ef4444" stroke="#ffffff" stroke-width="1.3"/>
                                <circle cx="560" cy="289" r="3.6" fill="#3f63e8" stroke="#ffffff" stroke-width="1.3"/>

                                <!-- End pills -->
                                <text x="595" y="174" text-anchor="middle" font-size="11" font-weight="600" fill="#ef4444">&amp; climbing</text>
                                <rect x="564" y="182" width="62" height="22" rx="6" fill="#fee2e2"/>
                                <text x="595" y="197" text-anchor="middle" font-size="13" font-weight="700" fill="#b91c1c">$<?= $qb_easystart ?>/mo</text>
                                <rect x="564" y="278" width="62" height="22" rx="6" fill="#eef2fe"/>
                                <text x="595" y="293" text-anchor="middle" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?>/mo</text>
                                <text x="595" y="313" text-anchor="middle" font-size="11" font-weight="600" fill="#0f172a">forever</text>

                                <!-- X axis -->
                                <g font-size="14" font-weight="600" fill="#0f172a" text-anchor="middle">
                                    <text x="88" y="408">2022</text>
                                    <text x="206" y="408">2023</text>
                                    <text x="324" y="408">2024</text>
                                    <text x="442" y="408">2025</text>
                                    <text x="560" y="408">2026</text>
                                </g>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">No price creep</span>
                        <span class="diff-callout-sub">QuickBooks rose ~70% in 5 years</span>
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
                            <th class="brand-col">Argo Premium<span class="th-sub">$<?= $argo_monthly ?> CAD / month</span></th>
                            <th class="brand-col">QuickBooks<span class="th-sub">EasyStart: $<?= $qb_easystart ?> CAD / month</span></th>
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
                            <td>Desktop app (offline-capable)</td>
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
                            <td>Unlimited products</td>
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
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Biometric login security</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Payroll</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Tax filing</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Third-party app integrations</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
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
                <h2>Argo Books vs QuickBooks: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>No accounting jargon</strong>, built for business owners rather than accountants</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics included</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month with no upsells or yearly hikes</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No payroll yet, so QuickBooks is the better fit if you run payroll today</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No integrated tax filing yet</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A newer platform with a smaller ecosystem than a 20-year incumbent</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>QuickBooks cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>High, rising prices</strong>: $<?= $qb_easystart ?> to $<?= $qb_advanced ?> CAD/month, up around 70% in five years</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Steeper learning curve</strong>, it assumes double-entry accounting knowledge</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Core features gated</strong> behind pricier tiers, like inventory on Plus</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>QuickBooks pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Built-in payroll and integrated tax filing</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Hundreds of third-party integrations and a mature ecosystem</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Deep reporting and advanced tools for larger, accountant-run teams</span></li>
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
                <h2>Built for small businesses, not accountants</h2>
                <p class="section-desc">QuickBooks assumes double-entry knowledge, surfaces accounting jargon throughout the UI, and gates useful features behind expensive tiers. Argo Books was built for business owners who want to manage finances without the learning curve or the price creep.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>No price creep</h3>
                    <p>QuickBooks has raised their prices twice since we launched this comparison page, and we've had to update these numbers each time. They increased their prices by 70% in the last 5 years. How much will they increase it in the next 5 years?</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>No feature gating</h3>
                    <p>QuickBooks locks inventory management and other core features behind their $<?= $qb_plus ?>+/month plans. Argo Books Premium gives you everything for $<?= $argo_monthly ?> CAD/month: no upsells, no surprises.</p>
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

    <!-- Confusion Stats -->
    <section class="honest-take">
        <div class="container">
            <div class="honest-card animate-on-scroll">
                <div class="honest-icon">
                    <?= svg_icon('help-circle', 30) ?>
                </div>
                <h3>The most confusing office tool in America</h3>
                <p>According to a <a class="link" href="https://www.digitaljournal.com/tech-science/the-most-puzzling-office-apps-in-the-u-s-revealed/article" target="_blank" rel="noopener noreferrer">study by Digital Adoption</a>, QuickBooks is the most confusing office application in the U.S., generating over 68,000 support-related Google searches every month. The most common query? "QuickBooks customer service," searched 19,000 times per month in the U.S. alone.</p>
                <p>Argo Books takes the opposite approach. No accounting jargon, no double-entry complexity, just a clean, intuitive interface designed for business owners, not accountants.</p>
            </div>
        </div>
    </section>

    <!-- Honest Take -->
    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>QuickBooks is powerful, but is it right for you?</h2>
                <p class="section-desc">QuickBooks is a mature platform with payroll, tax filing, and hundreds of integrations. If your business needs those features today, it's a solid choice. But as a publicly traded company, Intuit's priorities don't always align with small business owners, and it shows in the rising prices, aggressive feature gating, and complexity you don't need. Argo Books is built for you. Simple pricing, no upsells, and every feature included in one plan.</p>
                <p class="section-desc">Still weighing your options? Read our roundup of the <a class="link" href="../../best-quickbooks-alternatives/">best QuickBooks alternatives</a>, an honest look at Wave, Xero, FreshBooks, Zoho, and more, with where each one fits best.</p>
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
                    'argo-books-vs-wave'           => 'Argo Books vs. Wave',
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
                            <p>QuickBooks does not offer a free plan. Pricing starts at $<?= $qb_easystart ?> CAD/month after a limited trial.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>QuickBooks Online requires a constant internet connection to access your data.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books support payroll or tax filing?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Not yet. Argo Books is focused on finance management, inventory, invoicing, and financial reporting. If payroll and integrated tax filing are critical for your business right now, QuickBooks may be a better fit for those specific needs.</p>
                            <p>We're always adding new features based on user feedback.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to QuickBooks?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Argo Books is dramatically more affordable. The Free plan covers most small business needs at no cost. Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong>. QuickBooks starts at $<?= $qb_easystart ?> CAD/month for EasyStart and goes up to $<?= $qb_advanced ?>/month for Advanced, and that's before add-ons like payroll.</p>
                            <p>Argo Books has no hidden fees or client limits.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Argo Books runs natively on <strong>Windows</strong>, <strong>macOS</strong>, and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>QuickBooks Online is web-based, and QuickBooks Desktop (Windows only) has been discontinued for new purchases in favor of the cloud version.</p>
                        
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
