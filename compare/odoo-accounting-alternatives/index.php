<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans         = get_plan_features();
$pricing       = get_pricing_config();
$argo_monthly  = (int) $pricing['premium_monthly_price'];
$odoo_standard = competitor_price('odoo', 'standard');
$odoo_custom   = competitor_price('odoo', 'custom');
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
        content="Odoo Accounting alternatives with one flat price instead of per-user ERP billing. Compare simpler small business options on features, setup effort and cost.">
    <meta name="keywords"
        content="Odoo accounting alternatives, Odoo alternative, ERP alternative for small business, flat price accounting software, simple accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Odoo Accounting Alternatives: One Flat Price">
    <meta property="og:description"
        content="Odoo bills per user and per module and expects an implementation. Here are the alternatives that are one flat price and ready to use.">
    <meta property="og:url" content="https://argorobots.com/compare/odoo-accounting-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Odoo Accounting Alternatives: One Flat Price">
    <meta name="twitter:description"
        content="Odoo bills per user and per module and expects an implementation. Here are the alternatives that are one flat price and ready to use.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/odoo-accounting-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Odoo accounting alternatives" => "/compare/odoo-accounting-alternatives/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. Odoo's free plan is limited to a single app, and adding a second module starts at $<?= $odoo_standard ?> CAD/user/month."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. Odoo Online requires a constant internet connection, and self-hosted Odoo requires significant IT infrastructure to set up and maintain."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books have CRM or HR features?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books is focused on finance management, inventory, invoicing, and financial reporting. If you need CRM, HR, manufacturing, or other enterprise modules, Odoo is the better choice. Argo Books is designed to do fewer things really well: it's simple to learn and doesn't require a consultant to set up."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to Odoo?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is much simpler and more affordable. The Free plan covers most small business needs at no cost. Premium is just $<?= $argo_monthly ?> CAD/month. Odoo's free tier is limited to one app, and as soon as you need invoicing plus inventory (two apps), pricing jumps to $<?= $odoo_standard ?>+ CAD/user/month. Costs escalate quickly as you add modules and users."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. Odoo Online is web-based, and self-hosted Odoo can run on any server but requires technical expertise to deploy."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Odoo Accounting Alternatives: No Per-User Billing | Argo Books</title>

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
                <span class="hero-eyebrow">Odoo alternatives</span>
                <h1>Odoo accounting <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">A simpler, more affordable way to manage your small business finances. All the essentials, none of the ERP complexity or the per-user price creep.</p>
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
                <h2>What's the difference between Argo Books and Odoo?</h2>
                <p class="section-desc">Both can handle your finances. The difference is scope. Odoo is a full modular ERP built for growing, multi-department companies and priced per user; Argo Books is built for the business owner who just needs their books, and priced as one flat plan for the whole team.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Odoo?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Everything in one clean app.</strong> Invoicing, expenses, receipts, inventory, and forecasting together, with no ERP modules to install or configure and no accounting jargon to learn.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A genuinely usable free plan.</strong> All the core finance features forever, no credit card. Odoo's free plan is limited to a single app, so a second module already means paying per user.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your books open instantly and keep working with no internet, with no server to host or maintain.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI that's included, not upsold.</strong> Receipt scanning, spreadsheet import, and predictive analytics come built in, with no consultant or implementation project required.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month, flat. No per-user fees, so your cost doesn't climb as your team grows.</span>
                        </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative cost mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClip2"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <g clip-path="url(#dmClip2)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <!-- Title -->
                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Argo is one flat price. Odoo bills per user.</text>

                                <!-- Legend -->
                                <rect x="40" y="100" width="14" height="10" rx="2" fill="#3f63e8"/>
                                <text x="60" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="100" width="14" height="10" rx="2" fill="#ef4444"/>
                                <text x="186" y="109" font-size="13" fill="#0f172a">Odoo Standard</text>

                                <!-- Bars: width proportional to price, max ($220) = 380px wide -->
                                <!-- Argo Books Premium (flat) $15 -->
                                <text x="40" y="156" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium (flat)</text>
                                <rect x="40" y="166" width="26" height="26" rx="5" fill="#3f63e8"/>
                                <text x="76" y="184" font-size="14" font-weight="700" fill="#0f172a">$15</text>

                                <!-- Odoo, 1 user $44 -->
                                <text x="40" y="216" font-size="13" font-weight="600" fill="#0f172a">Odoo, 1 user</text>
                                <rect x="40" y="226" width="76" height="26" rx="5" fill="#ef4444"/>
                                <text x="126" y="244" font-size="14" font-weight="700" fill="#0f172a">$44</text>

                                <!-- Odoo, 3 users $132 -->
                                <text x="40" y="276" font-size="13" font-weight="600" fill="#0f172a">Odoo, 3 users</text>
                                <rect x="40" y="286" width="228" height="26" rx="5" fill="#ef4444"/>
                                <text x="278" y="304" font-size="14" font-weight="700" fill="#0f172a">$132</text>

                                <!-- Odoo, 5 users $220 -->
                                <text x="40" y="336" font-size="13" font-weight="600" fill="#0f172a">Odoo, 5 users</text>
                                <rect x="40" y="346" width="380" height="26" rx="5" fill="#ef4444"/>
                                <text x="430" y="364" font-size="14" font-weight="700" fill="#0f172a">$220</text>

                                <!-- Flat-price reminder -->
                                <rect x="40" y="402" width="26" height="18" rx="5" fill="#eef2fe"/>
                                <line x1="53" y1="406" x2="53" y2="416" stroke="#3f63e8" stroke-width="2.4" stroke-linecap="round"/>
                                <text x="76" y="416" font-size="13" font-weight="600" fill="#3f63e8">Argo stays $15 for the whole team</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Billed per user</span>
                        <span class="diff-callout-sub">Odoo charges per user, per month. Argo is one flat price for your whole team</span>
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
                            <th class="brand-col">Odoo<span class="th-sub">One App Free / $<?= $odoo_standard ?>+ CAD/user/mo</span></th>
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
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
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
                            <td>CRM &amp; sales pipeline</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>HR &amp; payroll</td>
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
                <h2>Argo Books vs Odoo: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month for your whole team, with no per-user fees</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>All your finances in one app</strong>: invoicing, expenses, inventory, and reporting, with no ERP modules to configure</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux, with no server to host</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics included</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Simple from day one</strong>, no consultant or implementation project to get started</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No CRM or sales pipeline, so Odoo is the better fit if you need those</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No HR or payroll modules</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A focused finance tool, not a full modular suite with hundreds of apps</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Odoo cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Priced per user</strong>: from $<?= $odoo_standard ?> CAD/user/month, so cost climbs fast as your team grows</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Complex to set up</strong>, a full ERP that often needs configuration or a consultant</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Developer-oriented</strong>, and the free plan is limited to a single app</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Odoo pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Extremely powerful, a full modular ERP that scales to complex needs</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Huge app ecosystem: CRM, HR, manufacturing, e-commerce, and hundreds more</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Deeply customizable for growing, multi-department companies</span></li>
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
                <h2>Built for small businesses, not enterprise ERP</h2>
                <p class="section-desc">Odoo is a full ERP suite with hundreds of apps designed for mid-to-large businesses. Argo Books is purpose-built for small businesses that need finance and inventory management without the complexity.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>More affordable</h3>
                    <p>Odoo charges per user per month, and costs add up fast as your team grows. Argo Books has a free version and Premium at a flat $<?= $argo_monthly ?> CAD/month, with no per-user fees.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Simple from day one</h3>
                    <p>Odoo's learning curve is steep: it's a full ERP with hundreds of modules. Argo Books is focused and intuitive, so you can get started in minutes, not weeks.</p>
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
                <p>Odoo is a powerful, full-featured ERP platform with CRM, HR, manufacturing, e-commerce, and hundreds of other modules. If your business needs an all-in-one enterprise system, Odoo is hard to beat.</p>
                <p>But if you're a small business that just needs straightforward finance management, inventory tracking, and invoicing without configuring an entire ERP, Argo Books gets you there in minutes, not weeks.</p>
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
                    'argo-books-vs-quickbooks' => 'Argo Books vs. QuickBooks',
                    'argo-books-vs-wave'       => 'Argo Books vs. Wave',
                    'argo-books-vs-freshbooks' => 'Argo Books vs. FreshBooks',
                    'argo-books-vs-xero'       => 'Argo Books vs. Xero',
                    'zipbooks-alternatives'    => 'ZipBooks alternatives',
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
                            <p>Odoo's free plan is limited to a single app, and adding a second module starts at $<?= $odoo_standard ?> CAD/user/month.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>Odoo Online requires a constant internet connection, and self-hosted Odoo requires significant IT infrastructure to set up and maintain.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books have CRM or HR features?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>No. Argo Books is focused on finance management, inventory, invoicing, and financial reporting. If you need CRM, HR, manufacturing, or other enterprise modules, Odoo is the better choice.</p>
                            <p>Argo Books is designed to do fewer things really well: it's simple to learn and doesn't require a consultant to set up.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to Odoo?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books is much simpler and more affordable. The Free plan covers most small business needs at no cost. Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong>. Odoo's free tier is limited to one app, and as soon as you need invoicing plus inventory (two apps), pricing jumps to $<?= $odoo_standard ?>+ CAD/user/month.</p>
                            <p>Costs escalate quickly as you add modules and users.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong>, <strong>macOS</strong>, and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>Odoo Online is web-based, and self-hosted Odoo can run on any server but requires technical expertise to deploy.</p>
                        
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
