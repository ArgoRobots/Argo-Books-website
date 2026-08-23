<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$mgr_cloud = competitor_price('manager', 'cloud'); // 83 CAD (59 USD converted), desktop edition is free
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
        content="Manager.io alternatives for local, offline bookkeeping. Compare desktop accounting apps on ease of use, AI features and invoicing, with your data staying on your machine.">
    <meta name="keywords"
        content="Manager.io alternatives, Manager.io alternative, free desktop accounting software, local accounting software, offline bookkeeping, desktop accounting app">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Manager.io Alternatives: Local Books Without the Learning Curve">
    <meta property="og:description"
        content="Manager.io keeps your data on your machine and expects you to know double-entry. Here is the alternative that keeps the local data and drops the prerequisite.">
    <meta property="og:url" content="https://argorobots.com/compare/manager-io-alternatives/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Manager.io Alternatives: Local Books Without the Learning Curve">
    <meta name="twitter:description"
        content="Manager.io keeps your data on your machine and expects you to know double-entry. Here is the alternative that keeps the local data and drops the prerequisite.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/manager-io-alternatives/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Manager.io alternatives" => "/compare/manager-io-alternatives/"]) ?></script>

    <!-- FAQ Schema, mirrors the visible accordion below -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Is Manager.io free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Manager.io's desktop edition is a free download with no feature limits. Their cloud edition is $<?= $mgr_cloud ?> CAD/month for remote and multi-user access. Argo Books also has a free tier, with Premium at $<?= $argo_monthly ?> CAD/month."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do both keep my data on my own computer?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. This is the main thing the two have in common. Both are desktop applications that store your books locally rather than on someone else's servers. Argo Books encrypts your local data with AES-256."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need accounting knowledge to use Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books uses guided forms and plain language: the amount, the category, who it was with. You do not need to know debits from credits. Manager.io is built around traditional double-entry accounting and expects familiarity with journals and a chart of accounts."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What does Argo Books have that Manager.io does not?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "AI receipt scanning, AI spreadsheet import and predictive cash flow analytics, none of which Manager.io offers. Argo Books also has a more modern interface built around everyday tasks rather than accounting structures."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What platforms does Argo Books run on?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books runs natively on Windows and Linux, like Manager.io's desktop edition."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Manager.io Alternatives: Desktop Accounting Compared | Argo Books</title>

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
                <span class="hero-eyebrow">Manager.io alternatives</span>
                <h1>Manager.io <span class="text-gradient">alternatives</span></h1>
                <p class="hero-subtitle">Both run on your machine and keep your data local. Argo Books assumes you are a business owner, not a bookkeeper.</p>
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
                <h2>What's the difference between Argo Books and Manager.io?</h2>
                <p class="section-desc">Manager.io is the closest thing to Argo Books in philosophy: a real desktop application, free to download, with your data on your own disk. Where they part company is the audience. Manager.io is built around traditional double-entry accounting; Argo Books is built for the person running the business.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Manager.io?</h3>
                    <ul class="why-list">
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>No accounting knowledge required.</strong> Manager.io expects you to understand double-entry, chart of accounts and journal entries. Argo Books asks what you spent and who you paid.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>AI that's built in.</strong> Receipt scanning turns a photo into a filed expense, spreadsheet import maps your columns automatically, and predictive analytics forecasts your cash flow. Manager.io has none of these.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Cloud access without $<?= $mgr_cloud ?> CAD a month.</strong> Manager.io's cloud edition is $<?= $mgr_cloud ?> CAD/month. Argo Books Premium is $<?= $argo_monthly ?> CAD/month.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Modern interface.</strong> Argo Books is built to look and behave like software from this decade rather than a forms-and-tables admin panel.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Local data on both sides.</strong> You do not have to give up privacy to get usability. Argo Books keeps your books on your machine, encrypted, exactly as Manager.io does.</span>
                            </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipmanager"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <?php
                                // Bars are scaled against a 60 top of scale. Manager.io
                                // publishes in USD; competitors.json holds
                                // the CAD conversion, so these bars and labels are all CAD.
                                $barX0  = 205;
                                $barMax = 340;
                                $scaleTop = 60;
                                $premW = (int) round($argo_monthly / $scaleTop * $barMax);
                                $cloudW = (int) round($mgr_cloud / $scaleTop * $barMax);
                            ?>
                            <g clip-path="url(#dmClipmanager)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">Both have a free desktop edition. Cloud is where they diverge.</text>

                                <rect x="40" y="99" width="12" height="12" rx="3" fill="#3f63e8"/>
                                <text x="58" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="150" y="99" width="12" height="12" rx="3" fill="#ef4444"/>
                                <text x="168" y="109" font-size="13" fill="#0f172a">Manager.io</text>

                                <rect x="205" y="145" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="162" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="145" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="162" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="201" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Manager Desktop</text>
                                <rect x="205" y="201" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="218" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="257" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="274" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="257" width="<?= $premW ?>" height="26" rx="5" fill="#3f63e8"/>
                                <text x="<?= 205 + $premW + 8 ?>" y="274" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?> CAD</text>
                                <rect x="205" y="313" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="330" font-size="13" font-weight="600" fill="#0f172a">Manager Cloud</text>
                                <rect x="205" y="313" width="<?= $cloudW ?>" height="26" rx="5" fill="#ef4444"/>
                                <text x="<?= 205 + $cloudW + 8 ?>" y="330" font-size="13" font-weight="700" fill="#ef4444">$<?= $mgr_cloud ?> CAD</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Same principle, different audience</span>
                        <span class="diff-callout-sub">Both keep your data local; Argo does not require accounting training</span>
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
                            <th class="brand-col">Manager.io<span class="th-sub">Desktop: Free</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Expense & revenue tracking</td>
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
                            <td>Invoicing & payments</td>
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
                            <td>Inventory management</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
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
                <h2>Argo Books vs Manager.io: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>No accounting jargon</strong>, built for business owners rather than bookkeepers</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows and Linux, with your data stored locally</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Modern interface</strong> designed around the tasks you actually do each week</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Less depth for traditional accountants than a full double-entry system</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A newer platform with a smaller ecosystem than longer-established tools</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Fewer country-specific tax modules than Manager.io's extensive list</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Manager.io cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Steep learning curve</strong>, built around double-entry accounting and journal entries</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No AI</strong> receipt scanning, spreadsheet import or forecasting</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Dated interface</strong> that reads as an admin panel rather than an app</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Cloud edition is $<?= $mgr_cloud ?> CAD/month</strong>, well above Argo Books Premium</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Manager.io pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Genuinely free</strong> desktop edition with no feature limits</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Full double-entry accounting with real depth for those who want it</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Your data stays on your own machine, like Argo Books</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Broad international tax and multi-currency support</span></li>
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
                <h2>Local data without the accounting degree</h2>
                <p class="section-desc">Manager.io proves people want desktop accounting that keeps data local. Argo Books agrees with that and takes out the part where you have to learn double-entry bookkeeping first.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('users', 30, '', 1.5) ?>
                    </div>
                    <h3>Built for owners, not bookkeepers</h3>
                    <p>Manager.io is organised around accounts, journals and ledgers. Argo Books is organised around invoices, expenses, receipts and stock, in the language you already use.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('bolt', 30, '', 1.5) ?>
                    </div>
                    <h3>AI that does the typing</h3>
                    <p>Receipt scanning, spreadsheet import and cash flow forecasting are included. Manager.io has no AI features at all.</p>
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
                <p>Manager.io deserves credit. It is free, genuinely capable, keeps your data on your own machine, and its international tax support is broader than ours. If you are comfortable with double-entry accounting and want maximum depth at no cost, it is a legitimate choice and we would rather say so than pretend otherwise.</p>
                <p>The catch is who it is for. It expects you to understand chart of accounts and journal entries, and its interface has not moved on in years. Argo Books takes the same principle, your books on your own computer, and builds it for someone who runs a business rather than someone who trained in accounting, with AI doing the data entry.</p>
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
            ob_start(); ?>Is Manager.io free?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Manager.io's desktop edition is a free download with no feature limits. Their cloud edition is $<?= $mgr_cloud ?> CAD/month for remote and multi-user access.</p>
                            <p>Argo Books also has a free tier, with Premium at $<?= $argo_monthly ?> CAD/month.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Do both keep my data on my own computer?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. This is the main thing the two have in common. Both are desktop applications that store your books locally rather than on someone else's servers.</p>
                            <p>Argo Books encrypts your local data with AES-256.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Do I need accounting knowledge to use Argo Books?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>No. Argo Books uses guided forms and plain language: the amount, the category, who it was with. You do not need to know debits from credits.</p>
                            <p>Manager.io is built around traditional double-entry accounting and expects familiarity with journals and a chart of accounts.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What does Argo Books have that Manager.io does not?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>AI receipt scanning, AI spreadsheet import and predictive cash flow analytics, none of which Manager.io offers.</p>
                            <p>Argo Books also has a more modern interface built around everyday tasks rather than accounting structures.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What platforms does Argo Books run on?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Argo Books runs natively on Windows and Linux, like Manager.io's desktop edition.</p>
                        
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
                    <h2>Ready for local books without the learning curve?</h2>
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
