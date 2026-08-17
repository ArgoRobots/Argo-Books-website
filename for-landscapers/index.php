<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
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
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Landscapers" => "/for-landscapers/"]) ?></script>

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
                    "name": "Can I run payroll for my crew?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, for Canadian staff. Enter each person's hours and Argo Books works out CPP, EI and federal and provincial income tax from the CRA's own tables, for every province and territory, then prints the pay stubs and records the wages in your books. Seasonal crews coming and going are handled, including the Record of Employment worksheet when someone finishes for the year, and at year end it prepares your T4 slips and the file the CRA needs. Payroll is part of Premium at $<?= $argo_monthly ?> CAD/month, with no per-employee fee. It does not cover staff outside Canada."
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
    <!-- Drives the mockup in the hero. -->
    <script src="../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../compare/style.css">
    <link rel="stylesheet" href="../resources/styles/for-pages.css">
    <link rel="stylesheet" href="../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../resources/styles/pricing-cards.css">
    <link rel="stylesheet" href="../features/feature-page.css">
    <link rel="stylesheet" href="../resources/styles/smartscreen-guide.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/styles/faq.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
    <!-- Brand typefaces (Fraunces display + IBM Plex Sans body), matched to the rest of the site -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../resources/styles/typography.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <!-- Hero. Split, with the live mockup beside the copy rather than centred
         text on a gradient. Same demo markup and loop the landing page uses. -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Accounting software for landscaping businesses</h1>
                    <p class="fp-hero-sub">Built for the way you actually bill: deposits, materials, and seasonal cashflow, without the bookkeeping headache.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="invoices">
                    <?= argo_feature_demo('invoices') ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Made-for-landscapers Intro -->
    <!-- SmartScreen walkthrough, revealed by lp-direct-download.php after a
         Windows direct-download click. -->
    <div class="container">
        <?php include __DIR__ . '/../resources/smartscreen-guide/guide.php'; ?>
    </div>

    <!-- Four Feature Blocks -->
    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Landscapers</span>
                <h2>We built this for the way landscapers actually work</h2>
                <p class="section-desc">Landscaping isn't one job at a time. It's a deposit on the install, a draw mid-project, a final balance, a stack of fuel and material receipts, and a winter slowdown that hits every year. Argo Books handles the books so you can stay outside.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('dollar', 20) ?>
                    </div>
                    <h3>Invoice with a deposit, a draw, and a final balance</h3>
                    <p>Set a deposit up front, send a draw invoice when site prep or planting is done, and a final balance when the job's signed off. Argo Books tracks what's been paid on each so you don't have to keep a separate spreadsheet of who owes what.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt at the gas pump, the nursery, or Home Depot</h3>
                    <p>Take a photo, and Argo Books pulls the vendor, date, and amount automatically. Tag it Fuel, Materials, or Equipment so when you look back in March, you actually know where the money went.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('user-focused', 20) ?>
                    </div>
                    <h3>Pay the crew without a separate payroll service</h3>
                    <p>Enter the hours and Argo Books works out CPP, EI and income tax from the CRA's own tables, prints the pay stubs, and puts the wages straight into your books. Seasonal staff coming and going is handled, including the Record of Employment worksheet when someone finishes for the year. Payroll is on Premium and covers Canadian staff.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('send', 20) ?>
                    </div>
                    <h3>Invoice the same day you finished the job</h3>
                    <p>Wrap up a property, open Argo Books at the truck or the kitchen table, hit send. Customers can pay through Stripe or Square, and the deposit on the next job can come in before you start it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Screenshots Strip -->
    <!-- Honest Take -->
    <!-- Pricing Strip -->
    <section class="honest-take">
        <div class="container">
            <div class="honest-card animate-on-scroll">
                <div class="honest-icon">
                    <?= svg_icon('info', 28) ?>
                </div>
                <h3>What Argo Books isn't</h3>
                <p>Argo Books is bookkeeping software, not field-service software. It does not do crew scheduling, route optimization, or per-property job costing. If you're trying to replace Jobber for those, run them side by side: Jobber for scheduling, Argo Books for your books. Payroll covers Canadian staff only, so a crew outside Canada needs a separate payroll service. If those are dealbreakers, that's fair. If they're not, the desktop app is free, it works without internet, and your data stays on your computer.</p>
                <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary js-direct-download honest-take-cta">
                    <span>Download Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <section class="pricing-comparison">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Pricing</span>
                <h2>Start free, upgrade only if you need more</h2>
                <p class="pricing-strip-intro">Most landscaping businesses stay on the free tier. Premium adds predictive analytics for seasonal cashflow planning, unlimited invoicing, and priority support.</p>
            </div>
            <?php
            // The real cards, the same ones the landing and pricing pages use.
            include __DIR__ . '/../partials/pricing-cards.php';
            ?>
        </div>
    </section>

    <!-- FAQ Section -->
    <!-- The for- pages did not link to each other, which wastes the internal
         linking they exist to earn. -->
    <section class="fp-section-tight">
        <div class="fp-wrap">
            <div class="fp-head-c animate-on-scroll">
                <div class="fp-eyebrow fp-eyebrow-c">More trades</div>
                <h2 class="fp-h2">Argo Books for your line of work</h2>
            </div>
            <div class="fp-related animate-on-scroll">
                <a href="../for-contractors/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('hard-hat', 20) ?></div>
                    <h3>Contractors</h3>
                    <p>Deposits, mid-job draws, materials and change orders.</p>
                </a>
                <a href="../for-cleaning-companies/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('spray-bottle', 20) ?></div>
                    <h3>Cleaning companies</h3>
                    <p>Recurring invoices, supplies and staff cost per contract.</p>
                </a>
                <a href="../for-repair-shops/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('wrench', 20) ?></div>
                    <h3>Repair shops</h3>
                    <p>Parts, labour and job history against the customer who booked it.</p>
                </a>
                <a href="../for-solo-operators/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('user', 20) ?></div>
                    <h3>Solo operators</h3>
                    <p>One person, one price, books that need no bookkeeper.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Do I need Argo Books year-round, or just during the season?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Year-round. Winter is when you sort through receipts, set your next-season prices, and see where last year went.</p>
                            <p>The free tier covers winter use with no monthly fee.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I track equipment depreciation?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>You can record equipment purchases and categorize them, and Argo Books will show you the spend in your reports.</p>
                            <p>It does not run a depreciation schedule the way a tax filing software would. Check with your accountant on the tax side.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice.</p>
                            <p>You only need internet when you actually send an invoice or take a payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I bill a deposit and final balance on the same invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Two ways: send a single invoice with the deposit listed at the top and a balance due, or send a deposit invoice now and a balance invoice when the job's done.</p>
                            <p>Both work. The second is what most landscapers use for multi-week installs.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is there a phone app?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Not yet. Argo Books is a desktop application for Windows, Mac, and Linux.</p>
                            <p>If you need to send an invoice in the field, you can take receipt photos on your phone and import them when you're back at the laptop.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I run payroll for my crew?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, for Canadian staff. Enter each person's hours and Argo Books works out CPP, EI and federal and provincial income tax from the CRA's own tables, for every province and territory, then prints the pay stubs and records the wages in your books.</p>
                            <p>Seasonal crews coming and going are handled, including the Record of Employment worksheet when someone finishes for the year, and at year end it prepares your T4 slips and the file the CRA needs. Payroll is part of Premium at $<?= $argo_monthly ?> CAD/month, with no per-employee fee. It does not cover staff outside Canada.</p>

            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is it really free?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, forever. The free tier covers all core features and <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            echo argo_faq_grid($faqs); ?>
        </div>
    </section>

    <section class="container" style="max-width:720px;text-align:center;padding-bottom:48px;">
        <p>Want the bookkeeping side in plain language? Read our guide to <a href="../bookkeeping-for-landscapers/">bookkeeping for landscapers</a>.</p>
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
                        <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary js-direct-download">
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

        });
    </script>
<?php include __DIR__ . '/../resources/smartscreen-guide/lp-direct-download.php'; ?>
</body>

</html>
