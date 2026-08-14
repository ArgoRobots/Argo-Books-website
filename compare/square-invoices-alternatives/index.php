<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$sq_plus = competitor_price('square-invoices', 'plus'); // 30 CAD
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
        content="Square Invoices alternatives that also track expenses, stock and reports. Compare free invoicing options that grow into full small business accounting.">
    <meta name="keywords"
        content="Square Invoices alternatives, Square Invoices alternative, free invoicing software, invoicing and accounting software, small business invoicing">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Square Invoices Alternatives: Billing Plus the Books">
    <meta property="og:description"
        content="Square Invoices is free and stops at billing. Here are the alternatives that also track expenses, stock and reports.">
    <meta property="og:url" content="https://argorobots.com/compare/square-invoices-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Square Invoices Alternatives: Billing Plus the Books">
    <meta name="twitter:description"
        content="Square Invoices is free and stops at billing. Here are the alternatives that also track expenses, stock and reports.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/square-invoices-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Square Invoices alternatives" => "/compare/square-invoices-alternatives/"]) ?></script>

    <!-- FAQ Schema, mirrors the visible accordion below -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Is Square Invoices really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Square Invoices has a free tier with unlimited invoices, estimates and contracts, unlimited users and unlimited customers. Payment processing fees apply when a customer pays by card. Their Plus plan is $<?= $sq_plus ?> CAD/month and adds custom templates, milestone payment schedules and project tracking."
                    }
                },
                {
                    "@type": "Question",
                    "name": "So why use Argo Books instead?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Because invoicing is one part of running the books. Argo Books adds expense and revenue tracking, AI receipt scanning, inventory management, financial reports and predictive analytics. It also works offline and keeps your data on your own computer, which Square Invoices does not do."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I still take card payments with Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books connects to your own Stripe, PayPal or Square account, so you keep your existing rates and payout schedule rather than being tied to one processor."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption. Square Invoices is cloud-based and needs a connection."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Argo Books free as well?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card and no trial period. It includes <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices a month plus AI receipt scanning and inventory. Premium is $<?= $argo_monthly ?> CAD/month and adds predictive analytics, higher limits and biometric login."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Square Invoices Alternatives: Invoicing Plus Books | Argo Books</title>

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
                <span class="hero-eyebrow">Square Invoices alternatives</span>
                <h1>Square Invoices <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">Square Invoices is genuinely free for unlimited invoicing. The question is not price, it's whether invoicing alone is enough.</p>
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
                <h2>What's the difference between Argo Books and Square Invoices?</h2>
                <p class="section-desc">This one is not about cost. Square Invoices is free and does not cap your invoices, and we are not going to pretend otherwise. The difference is scope: Square bills your customers, Argo Books keeps your books, and only one of the two works without an internet connection.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Square Invoices?</h3>
                    <ul class="why-list">
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Your whole books, not just billing.</strong> Expenses, receipts, inventory, reports and forecasting are built in, where Square Invoices covers the invoice and the payment.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Yours, and offline.</strong> A native desktop app for Windows, macOS, and Linux. Your records open instantly with no internet, and your data stays on your machine rather than on Square's servers.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>AI that's built in.</strong> Receipt scanning turns a photo into a filed expense, and spreadsheet import brings your history across in one go.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>You are not tied to one processor.</strong> Argo Books connects to your own Stripe, PayPal or Square account, so you keep your rates and your relationship.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Free to start, and honest about it.</strong> Both are free. Argo's free tier includes AI receipt scanning and inventory, which invoicing tools generally do not.</span>
                            </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipsquareinvoices"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <?php
                                // Bars are scaled against a 40 top of scale. Square Invoices
                                // publishes in CAD, so the labels carry the currency
                                // and the bars are only a relative visual, not a conversion.
                                $barX0  = 205;
                                $barMax = 340;
                                $scaleTop = 40;
                                $premW = (int) round($argo_monthly / $scaleTop * $barMax);
                                $plusW = (int) round($sq_plus / $scaleTop * $barMax);
                            ?>
                            <g clip-path="url(#dmClipsquareinvoices)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both are free to start. Square Invoices does not cap invoicing.</text>

                                <rect x="40" y="99" width="12" height="12" rx="3" fill="#3f63e8"/>
                                <text x="58" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="150" y="99" width="12" height="12" rx="3" fill="#ef4444"/>
                                <text x="168" y="109" font-size="13" fill="#0f172a">Square Invoices</text>

                                <rect x="205" y="145" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="162" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="145" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="162" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="201" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Square Invoices Free</text>
                                <rect x="205" y="201" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="218" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="257" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="274" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="257" width="<?= $premW ?>" height="26" rx="5" fill="#3f63e8"/>
                                <text x="<?= 205 + $premW + 8 ?>" y="274" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?> CAD</text>
                                <rect x="205" y="313" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="330" font-size="13" font-weight="600" fill="#0f172a">Square Invoices Plus</text>
                                <rect x="205" y="313" width="<?= $plusW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $plusW + 8 ?>" y="330" font-size="13" font-weight="700" fill="#ef4444">$<?= $sq_plus ?> CAD</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Both free to start</span>
                        <span class="diff-callout-sub">The difference is scope, not price: Argo keeps the books as well</span>
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
                            <th class="brand-col">Square Invoices<span class="th-sub">Free, unlimited invoices</span></th>
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
                            <td><span class="check-no"><?= svg_icon('x', 18) ?></span></td>
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
                <h2>Argo Books vs Square Invoices: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Full bookkeeping</strong>: expenses, revenue, inventory, reports and forecasting</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows, macOS, and Linux, with your data stored locally</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Bring your own processor</strong>, including Square itself, so you keep your own rates</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Desktop-first, so there's no browser or mobile-web access the way a cloud tool offers</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No point-of-sale hardware, which is Square's core strength</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A newer platform with a smaller ecosystem than longer-established tools</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Square Invoices cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Invoicing only</strong>: no expense tracking, no inventory, no financial reports</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, so no internet means no access to your invoices</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Tied to Square processing</strong> rather than letting you choose a provider</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No AI</strong> receipt scanning, spreadsheet import or forecasting</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Square Invoices pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Genuinely free</strong> with unlimited invoices, estimates and contracts</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Unlimited users and customers on the free tier</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Excellent if you already use Square for point of sale</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Strong mobile apps and instant payment acceptance</span></li>
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
                <h2>Free either way, so compare what you get</h2>
                <p class="section-desc">Square Invoices costs nothing and sends unlimited invoices, so the honest comparison is about scope. Argo Books is also free to start, and covers the bookkeeping that has to happen after the invoice is paid.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('document', 30, '', 1.5) ?>
                    </div>
                    <h3>Books, not just invoices</h3>
                    <p>Square Invoices ends at the payment. Argo Books tracks the expense side, scans receipts, manages stock, and produces the reports your accountant asks for.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Works offline</h3>
                    <p>Square Invoices is cloud-only. Argo Books is a desktop app that works without a connection, with your data stored locally on your device.</p>
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
                <p>Square Invoices is a strong free product, and we are not going to invent a price gap that does not exist. If you already run Square for point of sale, unlimited free invoicing inside the same account is hard to argue with, and their mobile apps and payment hardware are better than anything a desktop bookkeeping tool will offer you.</p>
                <p>What it does not do is keep your books. There is no expense tracking, no inventory, no financial reporting, and nothing works without a connection. If invoicing is genuinely all you need, use Square. If you also need to know what you spent, what you hold, and whether you made money, that is the gap Argo Books fills, and it is free to start too.</p>
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
            ob_start(); ?>Is Square Invoices really free?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Square Invoices has a free tier with unlimited invoices, estimates and contracts, unlimited users and unlimited customers. Payment processing fees apply when a customer pays by card.</p>
                            <p>Their Plus plan is $<?= $sq_plus ?> CAD/month and adds custom templates, milestone payment schedules and project tracking.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>So why use Argo Books instead?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Because invoicing is one part of running the books. Argo Books adds expense and revenue tracking, AI receipt scanning, inventory management, financial reports and predictive analytics.</p>
                            <p>It also works offline and keeps your data on your own computer, which Square Invoices does not do.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I still take card payments with Argo Books?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books connects to your own Stripe, PayPal or Square account, so you keep your existing rates and payout schedule rather than being tied to one processor.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption.</p>
                            <p>Square Invoices is cloud-based and needs a connection.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is Argo Books free as well?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books has a free tier you can use forever, with no credit card and no trial period. It includes <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices a month plus AI receipt scanning and inventory.</p>
                            <p>Premium is $<?= $argo_monthly ?> CAD/month and adds predictive analytics, higher limits and biometric login.</p>
                        
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
                    <h2>Ready for the rest of your books?</h2>
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
