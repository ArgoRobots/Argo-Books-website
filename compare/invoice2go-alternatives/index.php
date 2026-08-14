<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$i2g_starter      = competitor_price('invoice2go', 'starter');      // 11 CAD (7.99 USD converted), 2 invoices/mo
$i2g_professional = competitor_price('invoice2go', 'professional'); // 17 CAD (11.99 USD converted), 5 invoices/mo
$i2g_premium      = competitor_price('invoice2go', 'premium');      // 63 CAD (44.99 USD converted), unlimited
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
        content="Invoice2Go alternatives without monthly invoice caps. Compare invoicing apps that also track expenses, inventory and profit rather than just billing.">
    <meta name="keywords"
        content="Invoice2Go alternatives, Invoice2Go alternative, unlimited invoicing software, invoicing app for small business, free invoicing software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Invoice2Go Alternatives Without the Invoice Caps">
    <meta property="og:description"
        content="Invoice2Go caps how many invoices you send and stops at billing. Here are the alternatives with no caps and the whole books.">
    <meta property="og:url" content="https://argorobots.com/compare/invoice2go-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Invoice2Go Alternatives Without the Invoice Caps">
    <meta name="twitter:description"
        content="Invoice2Go caps how many invoices you send and stops at billing. Here are the alternatives with no caps and the whole books.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/invoice2go-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Invoice2Go alternatives" => "/compare/invoice2go-alternatives/"]) ?></script>

    <!-- FAQ Schema, mirrors the visible accordion below -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How many invoices can I send with Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books Premium does not cap the number of invoices you send. The Free plan includes <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month. Invoice2Go caps invoices on its cheaper plans: 2 per month on Starter and 5 per month on Professional. Only their Premium tier is unlimited."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Argo Books cheaper than Invoice2Go?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books Premium is $<?= $argo_monthly ?> CAD/month. Invoice2Go Premium, the only tier without an invoice cap, is $<?= $i2g_premium ?> CAD/month. Invoice2Go publishes in US dollars, so the CAD figure here is converted at the current rate and moves with it."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books do more than invoicing?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is full bookkeeping software: expenses, revenue, AI receipt scanning, inventory management, financial reports and predictive analytics, with invoicing as one part of it. Invoice2Go is focused on invoicing and payments rather than keeping your books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption. Invoice2Go is cloud-based and needs a connection to reach your invoices."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. Invoice2Go is web and mobile based."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Invoice2Go Alternatives With No Invoice Limits | Argo Books</title>

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
                <span class="hero-eyebrow">Invoice2Go alternatives</span>
                <h1>Invoice2Go <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">Invoice2Go's cheapest plan allows two invoices a month. Argo Books gives you full bookkeeping, offline, for less than their unlimited tier.</p>
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
                <h2>What's the difference between Argo Books and Invoice2Go?</h2>
                <p class="section-desc">Invoice2Go is an invoicing app. Argo Books is the whole set of books, with invoicing inside it. The other difference is the caps: Invoice2Go's cheaper tiers limit how many invoices you can send, so the plan that actually compares to Argo is their most expensive one.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Invoice2Go?</h3>
                    <ul class="why-list">
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>No invoice caps to work around.</strong> Invoice2Go Starter allows 2 invoices a month and Professional allows 5. Argo Books Premium does not cap invoices at all.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Your actual books, not just invoicing.</strong> Expenses, receipts, inventory, reports and forecasting are all included, where Invoice2Go stops at billing.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your books open instantly with no internet, and your data stays on your machine.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>AI that's built in.</strong> Receipt scanning, spreadsheet import, and predictive analytics come included rather than as an upsell.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>One predictable price in CAD.</strong> Invoice2Go publishes in USD. The figures here are converted to CAD so the comparison is like for like.</span>
                            </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipinvoice2go"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <?php
                                // Bars are scaled against a 50 top of scale. Invoice2Go
                                // publishes in USD; competitors.json holds
                                // the CAD conversion, so these bars and labels are all CAD.
                                $barX0  = 205;
                                $barMax = 340;
                                $scaleTop = 50;
                                $premW = (int) round($argo_monthly / $scaleTop * $barMax);
                                $startW = (int) round($i2g_starter / $scaleTop * $barMax);
                                $premiumW = (int) round($i2g_premium / $scaleTop * $barMax);
                            ?>
                            <g clip-path="url(#dmClipinvoice2go)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both shown in CAD. Invoice2Go publishes in USD.</text>

                                <rect x="40" y="99" width="12" height="12" rx="3" fill="#3f63e8"/>
                                <text x="58" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="150" y="99" width="12" height="12" rx="3" fill="#ef4444"/>
                                <text x="168" y="109" font-size="13" fill="#0f172a">Invoice2Go</text>

                                <rect x="205" y="145" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="162" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="145" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="162" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="201" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="201" width="<?= $premW ?>" height="26" rx="5" fill="#3f63e8"/>
                                <text x="<?= 205 + $premW + 8 ?>" y="218" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?> CAD</text>
                                <rect x="205" y="257" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="274" font-size="13" font-weight="600" fill="#0f172a">Invoice2Go Starter</text>
                                <rect x="205" y="257" width="<?= $startW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $startW + 8 ?>" y="274" font-size="13" font-weight="700" fill="#ef4444">$<?= $i2g_starter ?> CAD</text>
                                <rect x="205" y="313" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="330" font-size="13" font-weight="600" fill="#0f172a">Invoice2Go Premium</text>
                                <rect x="205" y="313" width="<?= $premiumW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $premiumW + 8 ?>" y="330" font-size="13" font-weight="700" fill="#ef4444">$<?= $i2g_premium ?> CAD</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Unlimited for less</span>
                        <span class="diff-callout-sub">Argo Books Premium costs less than Invoice2Go Premium and keeps your whole books</span>
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
                            <th class="brand-col">Invoice2Go<span class="th-sub">Premium: $<?= $i2g_premium ?> CAD/month</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Expense & revenue tracking</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Financial reports</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-partial">Limited</span></td>
                        </tr>
                        <tr>
                            <td>Invoicing & payments</td>
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
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Biometric login security</td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
                        </tr>
                        <tr>
                            <td>Local data storage</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
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
                <h2>Argo Books vs Invoice2Go: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>No invoice caps</strong> on Premium, so billing volume never forces an upgrade</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux, with your data stored locally</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Full bookkeeping</strong>: expenses, inventory, reports and forecasting, not just invoices</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Priced in CAD</strong> at $<?= $argo_monthly ?>/month, so the amount never moves with the exchange rate</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Desktop-first, so there's no browser or mobile-web access the way a cloud tool offers</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A newer platform with a smaller ecosystem than longer-established tools</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Invoice2Go cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Invoice caps on cheaper plans</strong>: 2 a month on Starter, 5 on Professional</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Invoicing only</strong>, with no inventory, no forecasting, and limited bookkeeping</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, so no internet means no access to your invoices</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Priced in USD</strong>, so what you actually pay moves with the exchange rate</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Invoice2Go pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Strong mobile apps for invoicing on the move</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Card payment processing built into the invoice</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Long-established with a large existing user base</span></li>
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
                <h2>Unlimited invoicing without the unlimited price</h2>
                <p class="section-desc">Invoice2Go's plan ladder is built around how many invoices you send. Argo Books charges one flat price and does not meter your billing, then adds the bookkeeping Invoice2Go leaves out.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>No caps, lower price</h3>
                    <p>Invoice2Go's unlimited tier is $<?= $i2g_premium ?> CAD/month. Argo Books Premium is $<?= $argo_monthly ?> CAD/month with no invoice cap, plus receipt scanning, inventory and forecasting.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Works offline</h3>
                    <p>Invoice2Go is cloud-only. Argo Books is a desktop app that works without a connection, with your data stored locally on your device.</p>
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
                <p>Invoice2Go does invoicing well, and its mobile apps are genuinely good if billing from a phone is the main thing you need. If you only send a handful of invoices and never want to think about expenses, receipts or reports, it may be enough on its own.</p>
                <p>The trap is the plan ladder. The tiers most people look at first cap you at two or five invoices a month, so the realistic comparison is their unlimited tier, which is their most expensive. Argo Books is cheaper than that, does not cap invoices, and keeps your whole books rather than just the billing.</p>
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
            ob_start(); ?>How many invoices can I send with Argo Books?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books Premium does not cap the number of invoices you send. The Free plan includes <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month.</p>
                            <p>Invoice2Go caps invoices on its cheaper plans: 2 per month on Starter and 5 per month on Professional. Only their Premium tier is unlimited.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is Argo Books cheaper than Invoice2Go?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books Premium is $<?= $argo_monthly ?> CAD/month. Invoice2Go Premium, the only tier without an invoice cap, is $<?= $i2g_premium ?> CAD/month.</p>
                            <p>Invoice2Go publishes in US dollars, so the CAD figure here is converted at the current rate and moves with it.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books do more than invoicing?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is full bookkeeping software: expenses, revenue, AI receipt scanning, inventory management, financial reports and predictive analytics, with invoicing as one part of it.</p>
                            <p>Invoice2Go is focused on invoicing and payments rather than keeping your books.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption.</p>
                            <p>Invoice2Go is cloud-based and needs a connection to reach your invoices.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on Windows, macOS, and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>Invoice2Go is web and mobile based.</p>
                        
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
                    <h2>Ready to invoice without a cap?</h2>
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
