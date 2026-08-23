<?php
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../track_referral.php';
$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
// No competitor price here. Most people already own Excel or use Google
// Sheets free, so the honest cost of a spreadsheet is time, not licensing.
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
        content="Argo Books vs spreadsheets: when Excel or Google Sheets stops being enough for bookkeeping, and what you gain by moving. Import your existing spreadsheet in one go.">
    <meta name="keywords"
        content="accounting software vs Excel, Excel bookkeeping alternative, replace spreadsheet accounting, Google Sheets bookkeeping, small business accounting software, spreadsheet import accounting">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books vs Spreadsheets: When Excel Stops Being Enough">
    <meta property="og:description"
        content="Spreadsheets are free and flexible until they are not. See what changes when your books stop being a file you maintain by hand.">
    <meta property="og:url" content="https://argorobots.com/compare/argo-books-vs-spreadsheet/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books vs Spreadsheets: When Excel Stops Being Enough">
    <meta name="twitter:description"
        content="Spreadsheets are free and flexible until they are not. See what changes when your books stop being a file you maintain by hand.">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/compare/argo-books-vs-spreadsheet/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Argo Books vs Spreadsheets" => "/compare/argo-books-vs-spreadsheet/"]) ?></script>

    <!-- FAQ Schema, mirrors the visible accordion below -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I import my existing spreadsheet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books has AI spreadsheet import: drop in your Excel or CSV file and it works out which column holds the date, the amount, the supplier and the rest, whatever you happened to call them. You see exactly what will be created, with anything questionable flagged, and nothing is saved until you approve it."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is my spreadsheet layout a problem?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Usually not. The import is built for real files rather than tidy ones: merged cells, a title row above the headings, inconsistent date formats and blank rows in the middle are all handled."
                    }
                },
                {
                    "@type": "Question",
                    "name": "When should I move off a spreadsheet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "When you stop trusting it. The usual signals are checking a total twice, finding a formula that stopped covering recent rows, or realising you cannot answer a question without building another sheet. If you record a few transactions a month and never need a report, a spreadsheet is still the right answer."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is Argo Books harder than a spreadsheet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "It is different rather than harder. You fill in guided forms instead of typing into cells, and the totals, categories and reports are produced for you rather than maintained by you. No accounting knowledge is required."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books keep my data on my computer like a spreadsheet file?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books is a desktop application and your books are stored locally on your own machine, encrypted with AES-256. You can back them up or move them like any other file."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books vs Spreadsheets: When Excel Stops Being Enough | Argo Books</title>

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
                <span class="hero-eyebrow">Spreadsheets alternative</span>
                <h1>Argo Books <span class="text-gradient">vs Spreadsheets</span></h1>
                <p class="hero-subtitle">A spreadsheet is the right answer right up until it isn't. Bring yours across in one go and keep the history.</p>
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
                <h2>What's the difference between Argo Books and Spreadsheets?</h2>
                <p class="section-desc">Almost everyone starts in a spreadsheet, and for a while that is the correct choice: it is free, you already know it, and it does exactly what you tell it. The trouble starts when the file gets long enough that you stop trusting it, and when a typed formula quietly changes a number you already reported.</p>
            </div>
            <div class="diff-split">
                <div class="diff-copy animate-on-scroll">
                    <h3>Why choose Argo Books over Spreadsheets?</h3>
                    <ul class="why-list">
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Nothing to maintain by hand.</strong> No formulas to drag down, no ranges to extend, no column that silently stops being included in the total.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>It checks before it saves.</strong> Guided forms enforce the fields and verify the amounts, so a transaction cannot land half finished or in the wrong column.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Receipts stop being a separate pile.</strong> Photograph a receipt and the expense writes itself with the line items attached, rather than being typed in and filed somewhere else.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Reports you don't build.</strong> Profit and loss, balance sheet and expense summaries generate from your records, rather than being a second sheet you maintain.</span>
                            </li>
                            <li>
                                <span class="why-check"><?= svg_icon('check', 15) ?></span>
                                <span><strong>Bring the history with you.</strong> AI spreadsheet import reads your existing file, works out which column is which, and shows you what it will create before anything is saved.</span>
                            </li>
                    </ul>
                </div>
                <div class="diff-visual animate-on-scroll">
                    <div class="diff-mockup">
                        <!-- Decorative price-comparison mockup. aria-hidden so it adds no
                             indexable text (no duplicate-content/SEO impact). -->
                        <svg viewBox="0 0 640 460" role="img" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                            <defs>
                                <clipPath id="dmClipspreadsheet"><rect x="1" y="1" width="638" height="458" rx="18"/></clipPath>
                            </defs>
                            <?php
                                // Bars are scaled against a 20 top of scale. Spreadsheets
                                // publishes in CAD, so the labels carry the currency
                                // and the bars are only a relative visual, not a conversion.
                                $barX0  = 205;
                                $barMax = 340;
                                $scaleTop = 20;
                                $premW = (int) round($argo_monthly / $scaleTop * $barMax);
                            ?>
                            <g clip-path="url(#dmClipspreadsheet)">
                                <rect x="0" y="0" width="640" height="460" fill="#ffffff"/>

                                <text x="40" y="54" font-family="Fraunces, Georgia, serif" font-size="21" font-weight="700" fill="#0f172a">What you'll pay per month</text>
                                <text x="40" y="80" font-size="14" fill="#0f172a">A spreadsheet costs nothing. The cost is the time it takes you.</text>

                                <rect x="40" y="99" width="12" height="12" rx="3" fill="#3f63e8"/>
                                <text x="58" y="109" font-size="13" fill="#0f172a">Argo Books</text>
                                <rect x="150" y="99" width="12" height="12" rx="3" fill="#ef4444"/>
                                <text x="168" y="109" font-size="13" fill="#0f172a">Spreadsheets</text>

                                <rect x="205" y="145" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="162" font-size="13" font-weight="600" fill="#0f172a">Argo Free</text>
                                <rect x="205" y="145" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="162" font-size="13" font-weight="700" fill="#64748b">$0</text>
                                <rect x="205" y="201" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="218" font-size="13" font-weight="600" fill="#0f172a">Spreadsheet</text>
                                <rect x="205" y="201" width="4" height="26" rx="2" fill="#cbd5e1"/>
                                <text x="219" y="218" font-size="13" font-weight="700" fill="#64748b">Already owned</text>
                                <rect x="205" y="257" width="340" height="26" rx="5" fill="#f8fafc"/>
                                <text x="40" y="274" font-size="13" font-weight="600" fill="#0f172a">Argo Books Premium</text>
                                <rect x="205" y="257" width="<?= $premW ?>" height="26" rx="5" fill="#3f63e8"/>
                                <text x="<?= 205 + $premW + 8 ?>" y="274" font-size="13" font-weight="700" fill="#3f63e8">$<?= $argo_monthly ?> CAD</text>
                            </g>
                            <rect x="1" y="1" width="638" height="458" rx="18" fill="none" stroke="#e2e8f0" stroke-width="1"/>
                        </svg>
                    </div>
                    <div class="diff-callout">
                        <span class="diff-callout-title">Free either way</span>
                        <span class="diff-callout-sub">Argo Free costs nothing too, and does the parts a spreadsheet cannot</span>
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
                            <th class="brand-col">Spreadsheets<span class="th-sub">Excel, Sheets or LibreOffice</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Expense & revenue tracking</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-partial">Manual</span></td>
                        </tr>
                        <tr>
                            <td>Financial reports</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-partial">Manual</span></td>
                        </tr>
                        <tr>
                            <td>Invoicing & payments</td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-yes"><?= svg_icon('check', 18) ?></span></td>
                            <td><span class="check-partial">Manual</span></td>
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
                            <td><span class="check-partial">Manual</span></td>
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
                <h2>Argo Books vs Spreadsheets: pros &amp; cons</h2>
            </div>
            <div class="pros-cons-grid">
                <div class="pc-card pc-argo animate-on-scroll">
                    <div class="pc-block">
                        <h3>Argo Books pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free forever plan</strong> with every core feature, no trial and no credit card</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Nothing breaks when you add a row</strong>: no formulas, ranges or references to maintain</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Works offline</strong> as a native desktop app for Windows and Linux, with your data stored locally, exactly like a file on your disk</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>AI built in</strong>: receipt scanning, spreadsheet import, and predictive analytics</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Reports generate themselves</strong> from your records rather than being a sheet you build</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Argo Books cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>Less flexible than a blank grid: it does bookkeeping, not arbitrary calculation</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>You work within its structure rather than inventing your own layout</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span>A newer platform with a smaller ecosystem than the world's most-used software</span></li>
                        </ul>
                    </div>
                </div>
                <div class="pc-card pc-competitor animate-on-scroll">
                    <div class="pc-block">
                        <h3>Spreadsheets cons</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Everything is manual</strong>: every total, category and report is a formula you maintain</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>Silent errors</strong>: a mistyped range or an overwritten formula changes a number without telling you</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No receipts attached</strong>, so proof of an expense lives somewhere else entirely</span></li>
                            <li><span class="pc-ico pc-con"><?= svg_icon('x', 16) ?></span><span><strong>No forecasting or stock tracking</strong>, and reporting means building another sheet by hand</span></li>
                        </ul>
                    </div>
                    <div class="pc-block">
                        <h3>Spreadsheets pros</h3>
                        <ul class="pc-list">
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span><strong>Free</strong>, or already paid for as part of software you own</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Completely flexible: it does exactly what you tell it to</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>You already know how to use it, with no new software to learn</span></li>
                            <li><span class="pc-ico pc-pro"><?= svg_icon('check', 16) ?></span><span>Perfectly adequate for a handful of transactions a month</span></li>
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
                <h2>Keep the spreadsheet habit, lose the spreadsheet work</h2>
                <p class="section-desc">Nobody should move off a spreadsheet before they need to. The signal is when you stop trusting the file: when you check a total twice, or find a formula that stopped covering the last few rows.</p>
            </div>
            <div class="diff-grid">
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon">
                        <?= svg_icon('document-upload', 30, '', 1.5) ?>
                    </div>
                    <h3>Bring your file across</h3>
                    <p>AI spreadsheet import reads your Excel or CSV file, works out which column holds the date, the amount and the supplier, and previews everything before saving. Years of history move in one go.</p>
                </div>
                <div class="diff-card animate-on-scroll">
                    <div class="diff-icon purple">
                        <?= svg_icon('check', 30, '', 1.5) ?>
                    </div>
                    <h3>It checks your work</h3>
                    <p>Guided forms enforce required fields and verify amounts, so nothing saves half finished. A spreadsheet lets you type anything into any cell.</p>
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
                <p>A spreadsheet is genuinely the right tool at the start. It is free, infinitely flexible, and you already know how to use it. If you record a handful of transactions a month and your reporting need is a single total at year end, moving to accounting software would be overhead for no gain, and we would rather you kept the spreadsheet.</p>
                <p>The moment it stops working is usually not dramatic. It is the day you check a total twice because you are not sure, or find that a formula stopped covering the last twenty rows. At that point the file has become something you maintain instead of something that helps you. Argo Books imports it, keeps the history, and takes over the maintenance.</p>
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
                    'manager-io-alternatives'  => 'Manager.io alternatives',
                    'argo-books-vs-xero'       => 'Argo Books vs. Xero',
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
            ob_start(); ?>Can I import my existing spreadsheet?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books has AI spreadsheet import: drop in your Excel or CSV file and it works out which column holds the date, the amount, the supplier and the rest, whatever you happened to call them.</p>
                            <p>You see exactly what will be created, with anything questionable flagged, and nothing is saved until you approve it.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is my spreadsheet layout a problem?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Usually not. The import is built for real files rather than tidy ones: merged cells, a title row above the headings, inconsistent date formats and blank rows in the middle are all handled.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>When should I move off a spreadsheet?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>When you stop trusting it. The usual signals are checking a total twice, finding a formula that stopped covering recent rows, or realising you cannot answer a question without building another sheet.</p>
                            <p>If you record a few transactions a month and never need a report, a spreadsheet is still the right answer.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is Argo Books harder than a spreadsheet?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>It is different rather than harder. You fill in guided forms instead of typing into cells, and the totals, categories and reports are produced for you rather than maintained by you.</p>
                            <p>No accounting knowledge is required.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books keep my data on my computer like a spreadsheet file?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes. Argo Books is a desktop application and your books are stored locally on your own machine, encrypted with AES-256. You can back them up or move them like any other file.</p>
                        
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
                    <h2>Ready to stop maintaining the file?</h2>
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
