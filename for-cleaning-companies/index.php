<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_cleaning_companies');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-cleaning-companies';
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

    <meta name="description"
        content="Accounting software for residential and commercial cleaning companies. Built for recurring invoices, supply costs, and same-day billing. Free desktop app for Windows and Linux.">
    <meta name="keywords"
        content="accounting software for cleaning companies, cleaning business bookkeeping, janitorial accounting software, residential cleaning invoicing, recurring invoice software cleaning">

    <meta property="og:title" content="Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers">
    <meta property="og:description"
        content="Recurring invoices, marked-up supplies, and same-day billing, without the bookkeeping headache. Free desktop app.">
    <meta property="og:url" content="https://argorobots.com/for-cleaning-companies/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers">
    <meta name="twitter:description"
        content="Recurring invoices, marked-up supplies, and same-day billing, without the bookkeeping headache.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-cleaning-companies/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Cleaning Companies" => "/for-cleaning-companies/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I set up a recurring invoice for the same client every week or month?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Set the client, the amount, and the frequency once, and Argo Books generates the invoice on schedule. The client gets the same clean invoice every time, you get a payment record every time, and you stop forgetting to bill the recurring residential."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I bill supplies as a line on the invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. List supplies as their own line, either at cost or with a small markup for sourcing and handling. Many commercial cleaners build supplies into the base rate and never itemize. Residential one-offs sometimes itemize for transparency. Both work in Argo Books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I see which clients or properties are most profitable?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can see total revenue per customer, and total spend per category. Argo Books does not run a per-property profit-and-loss the way a dedicated job-costing tool does, so a ten-house route gets one combined view. If knowing the margin on a single property is critical, a job-costing tool is a better fit."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to log a cleaning, scan a receipt, or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books schedule cleanings or send arrival texts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not run a scheduling calendar, dispatch crews, or send 'on the way' texts. Jobber, ZenMaid, and Maidily are built for that side. Run them alongside Argo Books: those for the schedule, Argo Books for the books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I run payroll for my cleaners?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, for Canadian staff. Enter each person's hours and Argo Books works out CPP, EI and federal and provincial income tax from the CRA's own tables, for every province and territory, then prints the pay stubs and records the wages in your books. Cleaners on different schedules can go on the same pay run, and at year end it prepares your T4 slips and the file the CRA needs. Payroll is part of Premium at $<?= $argo_monthly ?> CAD/month, with no per-employee fee. It does not cover staff outside Canada."
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
    <title>Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers</title>

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

    <!-- Hero. Split, with the live mockup beside the copy rather than centred
         text on a gradient. Same demo markup and loop the landing page uses. -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Accounting software for cleaning companies</h1>
                    <p class="fp-hero-sub">Built for recurring invoices, supply costs, and the difference between a profitable client and one that's quietly losing you money.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="customers">
                    <?= argo_feature_demo('customers') ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SmartScreen walkthrough, revealed by lp-direct-download.php after a
         Windows direct-download click. -->
    <div class="container">
        <?php include __DIR__ . '/../resources/smartscreen-guide/guide.php'; ?>
    </div>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Cleaning Companies</span>
                <h2>Recurring clients, real numbers, no spreadsheet</h2>
                <p class="section-desc">A cleaning business is one client at 9 AM, three more in the afternoon, the same houses next week, and a stack of supply receipts on the dashboard of the car. Residential or commercial, solo or with a crew, the work that pays the bills is the recurring contract that's billed on time, every time. Argo Books handles the books so you can keep cleaning.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('refresh', 20) ?>
                    </div>
                    <h3>Recurring invoices for the same client every week or month</h3>
                    <p>Set the client, the amount, and the frequency once. Argo Books builds the invoice on schedule, every week or every month, with the same line items and the same total. You stop forgetting to bill the residential routes, and the commercial accounts come in clean every cycle.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from Costco, the chemical supplier, or the equipment store</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Chemicals, Paper Goods, Equipment, or Vehicle so when you raise your rates next year, you can show the customer where the cost actually went up.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('send', 20) ?>
                    </div>
                    <h3>Send the one-time deep clean invoice from the driveway</h3>
                    <p>Finish the move-out clean, sit in the truck for two minutes, open Argo Books, hit send. Customers can pay through Stripe or Square, and the deposit on next week's recurring is already on autopilot.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('user-focused', 20) ?>
                    </div>
                    <h3>Pay your cleaners without a separate payroll service</h3>
                    <p>Enter the hours and Argo Books works out CPP, EI and income tax from the CRA's own tables, prints the pay stubs, and puts the wages straight into your books. Staff on different schedules or in different provinces can go on the same run, and your T4s are ready in January. Payroll is on Premium and covers Canadian staff.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="honest-take">
        <div class="container">
            <div class="honest-card animate-on-scroll">
                <div class="honest-icon">
                    <?= svg_icon('info', 28) ?>
                </div>
                <h3>What Argo Books isn't</h3>
                <p>Argo Books is bookkeeping software, not field-service software. It does not run a cleaning calendar, dispatch crews to addresses, send "on the way" texts to clients, or run a per-property profit-and-loss. If you need Jobber, ZenMaid, or Maidily for scheduling and crew routing, run them alongside Argo Books: those for the schedule, Argo Books for the books. Payroll covers Canadian staff only, so a crew outside Canada needs a separate payroll service. If those are dealbreakers, that's fair. If they're not, the desktop app is free, it works offline, the recurring invoices run themselves, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most cleaning businesses stay on the free tier. Premium adds predictive analytics for slow-month planning, unlimited invoicing for larger commercial routes, and priority support.</p>
            </div>
            <?php
            // The real cards, the same ones the landing and pricing pages use.
            include __DIR__ . '/../partials/pricing-cards.php';
            ?>
        </div>
    </section>

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
                <a href="../for-landscapers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('leaf', 20) ?></div>
                    <h3>Landscapers</h3>
                    <p>Seasonal cash flow, materials at cost, recurring maintenance.</p>
                </a>
                <a href="../for-solo-operators/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('user', 20) ?></div>
                    <h3>Solo operators</h3>
                    <p>One person, one price, books that need no bookkeeper.</p>
                </a>
                <a href="../for-auto-detailing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('car', 20) ?></div>
                    <h3>Auto detailing</h3>
                    <p>Per-vehicle jobs, products used, and repeat customers.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Can I set up a recurring invoice for the same client every week or month?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Set the client, the amount, and the frequency once, and Argo Books generates the invoice on schedule.</p>
                            <p>The client gets the same clean invoice every time, you get a payment record every time, and you stop forgetting to bill the recurring residential.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I bill supplies as a line on the invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. List supplies as their own line, either at cost or with a small markup for sourcing and handling.</p>
                            <p>Many commercial cleaners build supplies into the base rate and never itemize. Residential one-offs sometimes itemize for transparency. Both work in Argo Books.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I see which clients or properties are most profitable?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>You can see total revenue per customer, and total spend per category. Argo Books does not run a per-property profit-and-loss the way a dedicated job-costing tool does, so a ten-house route gets one combined view.</p>
                            <p>If knowing the margin on a single property is critical, a job-costing tool is a better fit.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to log a cleaning, scan a receipt, or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books schedule cleanings or send arrival texts?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>No. Argo Books does not run a scheduling calendar, dispatch crews, or send "on the way" texts.</p>
                            <p>Jobber, ZenMaid, and Maidily are built for that side. Run them alongside Argo Books: those for the schedule, Argo Books for the books.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I run payroll for my cleaners?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, for Canadian staff. Enter each person's hours and Argo Books works out CPP, EI and federal and provincial income tax from the CRA's own tables, for every province and territory, then prints the pay stubs and records the wages in your books.</p>
                            <p>Cleaners on different schedules can go on the same pay run, and at year end it prepares your T4 slips and the file the CRA needs. Payroll is part of Premium at $<?= $argo_monthly ?> CAD/month, with no per-employee fee. It does not cover staff outside Canada.</p>

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
        <p>Want the bookkeeping side in plain language? Read our guide to <a href="../bookkeeping-for-cleaning-companies/">bookkeeping for cleaning companies</a>.</p>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to put the recurring routes on autopilot?</h2>
                    <p>Download Argo Books for free. Set up your first client, build a recurring weekly invoice, and scan a supply receipt in under ten minutes.</p>
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
            const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-visible');
                    }
                });
            }, observerOptions);
            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

        });
    </script>
<?php include __DIR__ . '/../resources/smartscreen-guide/lp-direct-download.php'; ?>
</body>

</html>
