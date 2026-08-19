<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans         = get_plan_features();
$pricing       = get_pricing_config();
$argo_monthly  = (int) $pricing['premium_monthly_price'];
$argo_yearly   = (int) $pricing['premium_yearly_price'];
$hb_starter    = competitor_price('honeybook', 'starter');    // 40
$hb_essentials = competitor_price('honeybook', 'essentials'); // 67
$hb_premium    = competitor_price('honeybook', 'premium');    // 149
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
        content="HoneyBook alternatives that handle your actual bookkeeping, not just client flow and invoices. Compare expense tracking, reporting and invoicing in one place.">
    <meta name="keywords"
        content="HoneyBook alternatives, HoneyBook alternative, client management alternative, freelance bookkeeping software, invoicing and accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="HoneyBook Alternatives That Keep Your Actual Books">
    <meta property="og:description"
        content="HoneyBook books clients and sends invoices. Here are the alternatives that also track expenses, stock and profit.">
    <meta property="og:url" content="https://argorobots.com/compare/honeybook-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="HoneyBook Alternatives That Keep Your Actual Books">
    <meta name="twitter:description"
        content="HoneyBook books clients and sends invoices. Here are the alternatives that also track expenses, stock and profit.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/honeybook-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "HoneyBook alternatives" => "/compare/honeybook-alternatives/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. HoneyBook has no free plan, only a 7-day trial, and paid plans start at $<?= $hb_starter ?> CAD/month."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is HoneyBook accounting software?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not really. HoneyBook is a client-flow and CRM platform for service solopreneurs: proposals, contracts, scheduling, a client portal, lead forms, invoicing, and payments. It does not do real bookkeeping, and it even integrates with QuickBooks Online to handle the actual accounting. Argo Books is your actual books, with expense and revenue tracking, financial reports, and invoicing in one app."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. HoneyBook is cloud-only, with a mobile app, and requires a constant internet connection."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to HoneyBook?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is far more affordable, and there is no per-client fee. The Free plan covers most small business needs at no cost. Premium is just $<?= $argo_monthly ?> CAD/month (or $<?= $argo_yearly ?>/year). HoneyBook has no free plan and runs about $<?= $hb_starter ?> to $<?= $hb_premium ?> CAD/month across its Starter, Essentials, and Premium tiers. And because HoneyBook is not accounting software, many users still pay for separate books on top."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. HoneyBook is web-based and also has a mobile app for iOS and Android."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>HoneyBook Alternatives That Also Do Your Books | Argo Books</title>

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
                <span class="hero-eyebrow">HoneyBook alternatives</span>
                <h1>HoneyBook <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">HoneyBook runs your client pipeline and gets you paid. Argo Books actually keeps your books. See where each one fits, and why Argo does the books and the invoicing in one app.</p>
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
                <h2>What's the difference between Argo Books and HoneyBook?</h2>
                <p class="section-desc">They solve different problems. HoneyBook is a client-flow and CRM platform for service solopreneurs: proposals, contracts, scheduling, a client portal, lead forms, invoicing, and payments. Argo Books is bookkeeping: expense and revenue tracking, financial reports, inventory, and invoicing in one app. Many HoneyBook users still run separate accounting; Argo does the books and the invoicing together.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over HoneyBook?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>It actually keeps your books.</strong> Invoicing, expenses, receipts, inventory, and reports in one clean app. HoneyBook isn't accounting software, so with it you'd still need a separate tool for your books.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A genuinely free plan.</strong> All the core features forever, no trial and no credit card. HoneyBook has no free plan, just a 7-day trial.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows and Linux. Your books open instantly and keep working with no internet, while HoneyBook is cloud-only.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI built into your books.</strong> Receipt scanning, spreadsheet import, and predictive analytics come included, aimed at your bookkeeping rather than your client pipeline.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month. No per-client fees, and no HoneyBook-style $<?= $hb_starter ?>+ CAD/month floor.</span>
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
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs HoneyBook's rising plans.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">HoneyBook</text>

                                <!-- Bars: width scaled so $149 (widest) = 387px, i.e. ~2.6px per $1.
                                     Label column runs to x=200 so no label truncates. -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="200" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="212" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="196" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="200" y="184" width="39" height="18" rx="3" fill="#3f63e8"/>
                                <text x="249" y="197" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- HoneyBook Starter $40 -->
                                <text x="40" y="256" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Starter</text>
                                <rect x="200" y="244" width="104" height="18" rx="3" fill="#ef4444"/>
                                <text x="314" y="257" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_starter ?></text>

                                <!-- HoneyBook Essentials $67 -->
                                <text x="40" y="300" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Essentials</text>
                                <rect x="200" y="288" width="174" height="18" rx="3" fill="#ef4444"/>
                                <text x="384" y="301" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_essentials ?></text>

                                <!-- HoneyBook Premium $149 -->
                                <text x="40" y="344" font-size="13" font-weight="600" fill="#0f172a">HoneyBook Premium</text>
                                <rect x="200" y="332" width="387" height="18" rx="3" fill="#ef4444"/>
                                <text x="597" y="345" font-size="13" font-weight="600" fill="#0f172a">$<?= $hb_premium ?></text>

                                <!-- Baseline -->
                                <line x1="200" y1="372" x2="200" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Not your books</span>
                        <span class="diff-callout-sub">HoneyBook manages clients; Argo keeps the actual books.</span>
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
                            <th class="brand-col">HoneyBook<span class="th-sub">Starter: $<?= $hb_starter ?> CAD/month</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Expense &amp; revenue tracking (bookkeeping)</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Financial reports (P&amp;L, balance sheet)</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Invoicing &amp; payments</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Proposals &amp; contracts</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Client scheduling &amp; calendar</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Client portal</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Inventory management</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Desktop app (offline-capable)</td>
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
                <h2>Argo Books vs HoneyBook: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Real bookkeeping and invoicing in one app</strong>, so you're not stitching together separate tools</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows and Linux</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI included</strong>: receipt scanning, spreadsheet import, and predictive analytics</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month with no per-client fees</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No proposals or contracts, so HoneyBook is the better fit if you send those to book clients</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No client scheduling or calendar built in</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No client portal or lead-capture forms; Argo keeps your books, it isn't a client CRM</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>HoneyBook cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No free plan</strong> and pricey: about $<?= $hb_starter ?> to $<?= $hb_premium ?> CAD/month</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Not accounting software</strong>, so you'll still need separate books for expenses and reports</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, no offline desktop access to your data</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>HoneyBook pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Excellent client flow: proposals, contracts, and scheduling in one place</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Client portal plus lead-capture forms to bring new work in</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Built to book clients and get paid, with HoneyBook AI to help along the way</span></li>
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
                <p class="section-desc">Both tools help small businesses get paid, but they focus on different things. HoneyBook shines at client flow: proposals, contracts, and scheduling. Argo Books focuses on your actual books, offline access, and inventory.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>More affordable</h3>
                    <p>HoneyBook has no free plan and runs about $<?= $hb_starter ?> to $<?= $hb_premium ?> CAD/month. Argo Books has a free version with core features, and Premium is a fraction of the cost.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Actually your books</h3>
                    <p>HoneyBook manages clients and invoices, then hands off to QuickBooks for the accounting. Argo Books keeps the books itself, invoicing included, so it's one tool instead of two.</p>
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
                <p>HoneyBook excels at running a client pipeline: proposals, contracts, scheduling, a client portal, and lead forms, all built to book clients and get you paid. If that client flow is your core need, HoneyBook is a genuinely strong tool.</p>
                <p>But HoneyBook isn't accounting software, so you'll still need something for your actual books. If you want expense tracking, financial reports, inventory, and invoicing in one app, without paying $<?= $hb_starter ?>+ CAD/month for a tool that then hands off to QuickBooks, Argo Books is built for you.</p>
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
                    'sage-50-alternatives'         => 'Sage 50 alternatives',
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
                            <p>HoneyBook has no free plan, only a 7-day trial, and paid plans start at $<?= $hb_starter ?> CAD/month.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is HoneyBook accounting software?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Not really. HoneyBook is a client-flow and CRM platform for service solopreneurs: proposals, contracts, scheduling, a client portal, lead forms, invoicing, and payments. It doesn't do real bookkeeping, no expense tracking, no financial statements, no inventory.</p>
                            <p>In fact, HoneyBook integrates with QuickBooks Online to handle the actual accounting. Argo Books is your actual books, with expense and revenue tracking, financial reports, and invoicing in one app.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>HoneyBook is cloud-only, with a mobile app, and requires a constant internet connection to access your data.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to HoneyBook?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books is far more affordable, and there's no per-client fee. The Free plan covers most small business needs at no cost. Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong> (or $<?= $argo_yearly ?>/year). HoneyBook has no free plan and runs about $<?= $hb_starter ?> to $<?= $hb_premium ?> CAD/month across its Starter, Essentials, and Premium tiers.</p>
                            <p>And because HoneyBook isn't accounting software, many users still pay for separate books on top.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong> and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>HoneyBook is web-based and also has a mobile app for iOS and Android.</p>
                        
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
                    <h2>Ready to keep your books in one place?</h2>
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
