<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_landscapers');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-landscapers';
$download_url = '../downloads/?source=' . $cta_source;
$pricing_url  = '../pricing/?source=' . $cta_source;
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
        content="Accounting software for landscaping businesses. Built for deposits, materials, and seasonal cashflow, without the bookkeeping headache. Free download for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for landscapers, landscaping bookkeeping software, lawn care accounting, landscaper invoicing software, free accounting software landscaping">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Argo Books for Landscapers: Bookkeeping Built for the Way You Bill">
    <meta property="og:description"
        content="Deposits, materials, and seasonal cashflow, without the bookkeeping headache. Free desktop app for landscaping businesses.">
    <meta property="og:url" content="https://argorobots.com/for-landscapers/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Landscapers: Bookkeeping Built for the Way You Bill">
    <meta name="twitter:description"
        content="Deposits, materials, and seasonal cashflow, without the bookkeeping headache. Free desktop app for landscaping businesses.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/for-landscapers/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Landscapers", "item": "https://argorobots.com/for-landscapers/"}
            ]
        }
    </script>

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Do I need Argo Books year-round, or just during the season?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Year-round. Winter is when you sort through receipts, set your next-season prices, and see where last year went. The free tier covers winter use with no monthly fee."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track equipment depreciation?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can record equipment purchases and categorize them, and Argo Books will show you the spend in your reports. It does not run a depreciation schedule the way a tax filing software would. Check with your accountant on the tax side."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice. You only need internet when you actually send an invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I bill a deposit and final balance on the same invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Two ways: send a single invoice with the deposit listed at the top and a balance due, or send a deposit invoice now and a balance invoice when the job is done. Both work. The second is what most landscapers use for multi-week installs."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is there a phone app?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not yet. Argo Books is a desktop application for Windows, Mac, and Linux. If you need to send an invoice in the field, you can take receipt photos on your phone and import them when you are back at the laptop."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is it really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, forever. The free tier covers all core features and <?= $free_invoices ?> invoices per month. Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books for Landscapers: Bookkeeping Built for the Way You Bill</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../compare/style.css">
    <link rel="stylesheet" href="../for/style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/styles/faq.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Accounting software for landscaping businesses</h1>
            <p class="hero-subtitle animate-fade-in">Built for the way you actually bill: deposits, materials, and seasonal cashflow, without the bookkeeping headache.</p>
            <div class="hero-ctas animate-fade-in">
                <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary">
                    <span>Download Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
                <a href="#features" class="btn-cta btn-cta-outline">
                    <span>See What's Included</span>
                </a>
            </div>
            <p class="hero-reassurance animate-fade-in">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
        </div>
    </section>

    <!-- Made-for-landscapers Intro -->
    <section class="made-for-intro">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Landscapers</span>
                <h2>We built this for the way landscapers actually work</h2>
                <p class="section-desc">Landscaping isn't one job at a time. It's a deposit on the install, a draw mid-project, a final balance, a stack of fuel and material receipts, and a winter slowdown that hits every year. Argo Books handles the books so you can stay outside.</p>
            </div>
        </div>
    </section>

    <!-- Four Feature Blocks -->
    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('dollar', 28, '', 1.5) ?>
                    </div>
                    <h3>Invoice with a deposit, a draw, and a final balance</h3>
                    <p>Set a deposit up front, send a draw invoice when site prep or planting is done, and a final balance when the job's signed off. Argo Books tracks what's been paid on each so you don't have to keep a separate spreadsheet of who owes what.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt at the gas pump, the nursery, or Home Depot</h3>
                    <p>Take a photo, and Argo Books pulls the vendor, date, and amount automatically. Tag it Fuel, Materials, or Equipment so when you look back in March, you actually know where the money went.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('monitor', 28, '', 1.5) ?>
                    </div>
                    <h3>Works without internet, your data stays on your computer</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet required, no monthly subscription climbing every year, no logging into a website to send an invoice. The free tier covers most solo landscaping businesses forever.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('send', 28, '', 1.5) ?>
                    </div>
                    <h3>Invoice the same day you finished the job</h3>
                    <p>Wrap up a property, open Argo Books at the truck or the kitchen table, hit send. Customers can pay through Stripe or Square, and the deposit on the next job can come in before you start it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshots Strip -->
    <section class="screenshot-strip">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">In the App</span>
                <h2>What it actually looks like</h2>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of invoice editor showing deposit, line items, amount paid, and balance. See spec at docs/superpowers/specs/2026-05-28-for-landscapers-landing-page-design.md -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="An Argo Books invoice with a deposit, line items, and a remaining balance">
                </div>
                <p class="screenshot-caption">Setting a deposit and balance on a landscaping install invoice.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of the receipt-scan UI mid-scan showing extracted vendor, date, amount, and line items. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a photographed receipt">
                </div>
                <p class="screenshot-caption">A receipt from the nursery scanned and categorized in seconds.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of the main dashboard. The existing dashboard.webp may work as a fallback per spec. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing recent revenue, expenses, and outstanding invoices">
                </div>
                <p class="screenshot-caption">Your dashboard at a glance.</p>
            </div>
        </div>
    </section>

    <!-- Honest Take -->
    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>What Argo Books isn't</h2>
                <p class="section-desc">Argo Books is bookkeeping software, not field-service software. It does not do crew scheduling, route optimization, or per-property job costing. If you're trying to replace Jobber for those, run them side by side: Jobber for scheduling, Argo Books for your books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
                <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary honest-take-cta">
                    <span>Download Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Strip -->
    <section class="pricing-comparison">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Pricing</span>
                <h2>Start free, upgrade only if you need more</h2>
                <p class="pricing-strip-intro">Most landscaping businesses stay on the free tier. Premium adds predictive analytics for seasonal cashflow planning, unlimited invoicing, and priority support.</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-col animate-on-scroll">
                    <div class="pricing-box argo-box">
                        <div class="pricing-box-header">
                            <span class="pricing-brand">Argo Free</span>
                        </div>
                        <div class="pricing-tiers">
                            <div class="pricing-tier">
                                <span class="tier-name">Free</span>
                                <div class="tier-price">
                                    <span class="tier-amount">$0</span>
                                    <span class="tier-period">forever</span>
                                </div>
                                <ul class="tier-features">
                                    <?php foreach ($plans['free']['features'] as $f): ?>
                                    <li><?= svg_icon('check', 14) ?> <?= render_feature_label($f) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pricing-col animate-on-scroll">
                    <div class="pricing-box argo-box">
                        <div class="pricing-box-header">
                            <span class="pricing-brand">Argo Premium</span>
                        </div>
                        <div class="pricing-tiers">
                            <div class="pricing-tier">
                                <span class="tier-name">Premium</span>
                                <div class="tier-price">
                                    <span class="tier-amount">$<?= $argo_monthly ?></span>
                                    <span class="tier-period">CAD / month</span>
                                </div>
                                <ul class="tier-features">
                                    <?php foreach ($plans['premium']['features'] as $f): ?>
                                    <li><?= svg_icon('check', 14) ?> <?= render_feature_label($f) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do I need Argo Books year-round, or just during the season?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Year-round. Winter is when you sort through receipts, set your next-season prices, and see where last year went.</p>
                            <p>The free tier covers winter use with no monthly fee.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track equipment depreciation?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>You can record equipment purchases and categorize them, and Argo Books will show you the spend in your reports.</p>
                            <p>It does not run a depreciation schedule the way a tax filing software would. Check with your accountant on the tax side.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice.</p>
                            <p>You only need internet when you actually send an invoice or take a payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I bill a deposit and final balance on the same invoice?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Two ways: send a single invoice with the deposit listed at the top and a balance due, or send a deposit invoice now and a balance invoice when the job's done.</p>
                            <p>Both work. The second is what most landscapers use for multi-week installs.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is there a phone app?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Not yet. Argo Books is a desktop application for Windows, Mac, and Linux.</p>
                            <p>If you need to send an invoice in the field, you can take receipt photos on your phone and import them when you're back at the laptop.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is it really free?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, forever. The free tier covers all core features and <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to clean up the books before the next season?</h2>
                    <p>Download Argo Books for free. Set up your first customer, scan a receipt, and send an invoice in under ten minutes.</p>
                    <div class="cta-buttons">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary">
                            <span>Download Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="<?= htmlspecialchars($pricing_url) ?>" class="btn-cta btn-cta-ghost">
                            <span>View Pricing</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
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

            // FAQ accordion
            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>
