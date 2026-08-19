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
$fb_lite      = competitor_price('freshbooks', 'lite');
$fb_plus      = competitor_price('freshbooks', 'plus');
$fb_premium   = competitor_price('freshbooks', 'premium');
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
        content="Argo Books vs FreshBooks: Compare features, pricing, and ease of use. See why small businesses choose Argo Books as a simpler, more affordable FreshBooks alternative.">
    <meta name="keywords"
        content="Argo Books vs FreshBooks, FreshBooks alternative, FreshBooks alternative Canada, cheap FreshBooks alternative, simple bookkeeping software, small business accounting, affordable accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books vs FreshBooks: A Simpler, More Affordable Alternative">
    <meta property="og:description"
        content="Compare Argo Books and FreshBooks side by side. See why small businesses are choosing Argo Books for simpler, more affordable finance management.">
    <meta property="og:url" content="https://argorobots.com/compare/argo-books-vs-freshbooks/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books vs FreshBooks: A Simpler, More Affordable Alternative">
    <meta name="twitter:description"
        content="Compare Argo Books and FreshBooks side by side. See why small businesses are choosing Argo Books for simpler, more affordable finance management.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/argo-books-vs-freshbooks/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Argo Books vs FreshBooks" => "/compare/argo-books-vs-freshbooks/"]) ?></script>

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
                        "text": "Yes. Argo Books has a free tier you can use forever, with no credit card, no trial period, and no strings attached. The Free plan includes all core features, <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month, and AI receipt scanning. FreshBooks only offers a 30-day free trial before requiring a paid plan starting at $<?= $fb_lite ?> CAD/month."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books work offline?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy. FreshBooks is cloud-only and requires a constant internet connection to access your data."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books have time tracking?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not yet. FreshBooks has built-in time tracking, which is great for freelancers and consultants who bill by the hour. If billable hours are a core part of your business, FreshBooks may be a better fit for that specific need. Argo Books is focused on product-based businesses, inventory management, and financial reporting."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does Argo Books pricing compare to FreshBooks?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is significantly more affordable. The Free plan covers most small business needs at no cost. Premium is just $<?= $argo_monthly ?> CAD/month (or $<?= $argo_yearly ?>/year). FreshBooks starts at $<?= $fb_lite ?> CAD/month for its Lite plan with a 5-client limit, and goes up to $<?= $fb_premium ?>/month for Premium. Argo Books has no client limits on any plan."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows and Linux. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners. FreshBooks is web-based and also has a mobile app for iOS and Android."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books vs FreshBooks: Simpler & More Affordable | Argo Books</title>

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
                <span class="hero-eyebrow">FreshBooks alternative</span>
                <h1>Argo Books <span class="text-gradient">vs FreshBooks</span></h1>
                <p class="hero-subtitle">A simpler, more affordable way to manage your small business finances. All the essentials, none of the accounting jargon or the per-client fees.</p>
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
                <h2>What's the difference between Argo Books and FreshBooks?</h2>
                <p class="section-desc">Both handle the small business basics. The difference is who they're built for. FreshBooks is built for freelancers billing by the hour and priced per client; Argo Books is built for the business owner doing their own books, with no client limits and one flat price.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over FreshBooks?</h3>
                    <ul class="why-list">
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Everything in one clean app.</strong> Invoicing, expenses, receipts, inventory, and forecasting together, with no accounting jargon and no double-entry to learn.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>A genuinely free plan.</strong> All the core features forever, no trial and no credit card. FreshBooks only gives you a 30-day trial.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>Yours, and offline.</strong> A native desktop app for Windows and Linux. Your books open instantly and keep working with no internet, and your data stays on your machine.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>AI that's included, not upsold.</strong> Receipt scanning, spreadsheet import, and predictive analytics come built in, not features FreshBooks offers at all.</span>
                        </li>
                        <li>
                            <span class="why-check"><?= svg_icon('check', 15) ?></span>
                            <span><strong>One predictable price.</strong> Everything in Premium for $<?= $argo_monthly ?> CAD/month. No per-client fees and no client limits on any plan.</span>
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
                                <text x="40" y="80" font-size="14" fill="#0f172a">One flat Argo price vs FreshBooks' rising plans.</text>

                                <!-- Legend -->
                                <rect x="40" y="98" width="16" height="10" rx="2" fill="#3f63e8"/>
                                <text x="62" y="107" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="166" y="98" width="16" height="10" rx="2" fill="#ef4444"/>
                                <text x="188" y="107" font-size="13" fill="#0f172a">FreshBooks</text>

                                <!-- Bars: width scaled so $72 (widest) = 430px, i.e. ~5.97px per $1 -->
                                <!-- Argo Free $0 -->
                                <text x="40" y="152" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="175" y="140" width="2" height="18" rx="3" fill="#3f63e8"/>
                                <text x="187" y="153" font-size="13" font-weight="600" fill="#0f172a">$0</text>

                                <!-- Argo Books Premium $15 -->
                                <text x="40" y="196" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="175" y="184" width="90" height="18" rx="3" fill="#3f63e8"/>
                                <text x="275" y="197" font-size="13" font-weight="600" fill="#0f172a">$<?= $argo_monthly ?></text>

                                <!-- FreshBooks Lite $26 -->
                                <text x="40" y="256" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Lite</text>
                                <rect x="175" y="244" width="155" height="18" rx="3" fill="#ef4444"/>
                                <text x="340" y="257" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_lite ?></text>

                                <!-- FreshBooks Plus $42 -->
                                <text x="40" y="300" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Plus</text>
                                <rect x="175" y="288" width="251" height="18" rx="3" fill="#ef4444"/>
                                <text x="436" y="301" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_plus ?></text>

                                <!-- FreshBooks Premium $72 -->
                                <text x="40" y="344" font-size="13" font-weight="600" fill="#0f172a">FreshBooks Premium</text>
                                <rect x="175" y="332" width="430" height="18" rx="3" fill="#ef4444"/>
                                <text x="615" y="345" font-size="13" font-weight="600" fill="#0f172a">$<?= $fb_premium ?></text>

                                <!-- Baseline -->
                                <line x1="175" y1="372" x2="175" y2="128" stroke="#e2e8f0" stroke-width="1"/>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">No client limits</span>
                        <span class="diff-callout-sub">FreshBooks caps clients on its cheaper plans</span>
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
                            <th class="brand-col">FreshBooks<span class="th-sub">Lite: $<?= $fb_lite ?> CAD/month</span></th>
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
                            <td>Time tracking</td>
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
                            <td>Mobile app</td>
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
                <h2>Argo Books vs FreshBooks: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>No accounting jargon</strong>, built for business owners rather than accountants</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows and Linux</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics included</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>One flat price</strong>, Premium is $<?= $argo_monthly ?> CAD/month with no per-client fees or client limits</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No time tracking, so FreshBooks is the better fit if you bill by the hour</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No client portal for clients to view and pay invoices online</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No mobile app yet, it's desktop-first</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>FreshBooks cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No free plan</strong> and per-client pricing: $<?= $fb_lite ?> to $<?= $fb_premium ?> CAD/month, with clients capped on cheaper tiers</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud-only</strong>, no offline desktop access to your own books</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No inventory management</strong>, so it doesn't suit product-based businesses</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No AI tools</strong>: no receipt scanning, spreadsheet import, or predictive analytics</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>No biometric login security</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>FreshBooks pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Built-in time tracking, great for freelancers and consultants billing by the hour</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Client portal so clients can view and pay invoices online</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Mobile apps for iOS and Android, plus polished, strong invoicing</span></li>
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
                <p class="section-desc">Both tools work for small businesses, but they focus on different things. FreshBooks shines at invoicing and time tracking. Argo Books focuses on simplicity, offline access, and inventory.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('dollar', 30, '', 1.5) ?>
                    </div>
                    <h3>More affordable</h3>
                    <p>FreshBooks starts at $<?= $fb_lite ?> CAD/month for just 5 clients. Argo Books has a free version with core features, and Premium is a fraction of the cost with no client limits.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>Works offline</h3>
                    <p>FreshBooks is cloud-only: no internet, no access. Argo Books is a desktop app that works offline, so you're never locked out of your own data.</p>
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
                <p>FreshBooks excels at invoicing, time tracking, and client management, especially for freelancers and service-based businesses. If those are your core needs, FreshBooks is a great tool.</p>
                <p>But if you're a product-based small business that needs inventory management, offline access, and straightforward finance tracking without paying $<?= $fb_lite ?>+ CAD/month, Argo Books is built for you.</p>
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
                            <p>FreshBooks only offers a 30-day free trial before requiring a paid plan starting at $<?= $fb_lite ?> CAD/month.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books work offline?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application that runs natively on your computer, so it works even without an internet connection. Your data is stored locally with AES-256 encryption, giving you full control and privacy.</p>
                            <p>FreshBooks is cloud-only and requires a constant internet connection to access your data.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books have time tracking?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Not yet. FreshBooks has built-in time tracking, which is great for freelancers and consultants who bill by the hour. If billable hours are a core part of your business, FreshBooks may be a better fit for that specific need.</p>
                            <p>Argo Books is focused on product-based businesses, inventory management, and financial reporting.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>How does Argo Books pricing compare to FreshBooks?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books is significantly more affordable. The Free plan covers most small business needs at no cost. Premium is just <strong>$<?= $argo_monthly ?> CAD/month</strong> (or $<?= $argo_yearly ?>/year). FreshBooks starts at $<?= $fb_lite ?> CAD/month for its Lite plan with a 5-client limit, and goes up to $<?= $fb_premium ?>/month for Premium.</p>
                            <p>Argo Books has no client limits on any plan.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on <strong>Windows</strong> and <strong>Linux</strong>. Because it's a desktop app, it's fast and responsive, with no browser tabs and no loading spinners.</p>
                            <p>FreshBooks is web-based and also has a mobile app for iOS and Android.</p>
                        
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
