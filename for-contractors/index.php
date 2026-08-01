<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_contractors');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-contractors';
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
        content="Accounting software for general contractors and tradespeople. Built for progress billing, materials, and change orders. Free desktop app for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for contractors, contractor bookkeeping software, construction invoicing software, contractor accounting app, free accounting software contractor">

    <meta property="og:title" content="Argo Books for Contractors: Bookkeeping Built for Progress Billing">
    <meta property="og:description"
        content="Deposits, mid-job draws, materials, and change orders, without the bookkeeping headache. Free desktop app for contractors.">
    <meta property="og:url" content="https://argorobots.com/for-contractors/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Contractors: Bookkeeping Built for Progress Billing">
    <meta name="twitter:description"
        content="Deposits, mid-job draws, materials, and change orders, without the bookkeeping headache.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-contractors/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Contractors" => "/for-contractors/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I bill a deposit, a mid-job draw, and a final balance?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Most contractors send three invoices on a multi-week job: a deposit invoice before work begins, a draw invoice at a milestone like framing or rough-in, and a final invoice when the work is signed off. Argo Books tracks what's been paid on each so the final balance lines up with what the customer still owes."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I bill change orders without re-issuing the original invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Add each change order as its own line item on the next progress invoice, or send a separate change-order invoice. Keeping changes on their own lines makes it easy for the customer to see exactly what they signed off on versus the original scope."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track materials and labor separately?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. List materials and labor on separate lines of the invoice, or roll materials into a single marked-up line if that's how you priced the bid. On the expense side, scan the supply-house receipt and tag it Materials, Equipment, or Subcontractor so the report later actually means something."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet at the job site?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books do job costing per project?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not the way QuickBooks Contractor or a dedicated job-costing tool does. Argo Books tracks expenses by category and revenue by customer and invoice, which covers most solo contractors and small crews. If you need a true per-project P&L across labor, materials, subs, and overhead, a job-costing tool is a better fit. Many contractors run a simpler bookkeeping tool alongside their estimating or scheduling software."
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
    <title>Argo Books for Contractors: Bookkeeping Built for Progress Billing</title>

    <script src="../resources/scripts/main.js"></script>
    <!-- Drives the invoicing mockup in the hero. -->
    <script src="../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../compare/style.css">
    <link rel="stylesheet" href="../for/style.css">
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

    <!-- Hero. Split, with the live invoicing mockup beside the copy rather
         than centred text on a gradient. The demo markup and its loop are the
         same ones the landing page and feature pages use. -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Accounting software<br>for contractors</h1>
                    <p class="fp-hero-sub">Built for progress billing: deposits, mid-job draws, materials, and change orders, without the bookkeeping headache.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See what's included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card, and your books stay on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="invoices">
                    <?= argo_feature_demo('invoices') ?>
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
                <span class="section-label">Made for Contractors</span>
                <h2>Built for the way contractors actually get paid</h2>
                <p class="section-desc">A contractor invoice isn't one number on one piece of paper. It's a deposit before any work starts, a draw when the framing is up or the rough-in is done, change orders the homeowner asked for after the bid, materials at cost or with a markup, and a final balance with the deposit and draws already credited. Argo Books handles the books so you can stay on the tools.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('clipboard-check', 20) ?>
                    </div>
                    <h3>Bill a deposit, a mid-job draw, and a final balance</h3>
                    <p>Send a deposit invoice before the first day, a draw invoice when framing or rough-in is signed off, and a final balance with the deposit and draws already credited. Argo Books tracks what's been paid on each, so the closing balance is exactly what's still owed.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from Home Depot, the lumber yard, or the supply house</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Materials, Subcontractor, Equipment Rental, or Permit so when the customer asks for an itemized statement, you can answer in two minutes instead of two hours.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works without internet, your data stays on your computer</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed at the job trailer, no monthly subscription climbing every year, no website to log into when the cell signal cuts out. The free tier covers most solo contractors and small crews forever.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('bolt', 20) ?>
                    </div>
                    <h3>Send the final invoice the day you wrap</h3>
                    <p>Walk through with the customer, open Argo Books, and send the final invoice before you pack the truck. Customers can pay through Stripe or Square, so the balance can clear before the deposit on the next job needs to land.</p>
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
                <p>Argo Books is bookkeeping software, not construction-management software. It does not do job costing per project, crew scheduling, or bid and estimating. If you're trying to replace Buildertrend, CoConstruct, or QuickBooks Contractor for those, run them side by side: those for the project, Argo Books for your books.</p>
                <p>It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo contractors and small crews stay on the free tier. Premium adds predictive analytics for cashflow planning between jobs, unlimited invoicing, and priority support.</p>
            </div>
            <?php
            // The real cards, the same ones the landing and pricing pages use,
            // so a retheme there carries here instead of drifting.
            include __DIR__ . '/../partials/pricing-cards.php';
            ?>
        </div>
    </section>

    <!-- The ten for- pages did not link to each other at all, which wastes the
         internal linking these pages exist to earn. -->
    <section class="fp-section-tight">
        <div class="fp-wrap">
            <div class="fp-head-c animate-on-scroll">
                <div class="fp-eyebrow fp-eyebrow-c">Other trades</div>
                <h2 class="fp-h2">Argo Books for your line of work</h2>
            </div>
            <div class="fp-related animate-on-scroll">
                <a href="../for-landscapers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('leaf', 20) ?></div>
                    <h3>Landscapers</h3>
                    <p>Seasonal cash flow, materials at cost, and recurring maintenance billing.</p>
                </a>
                <a href="../for-repair-shops/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('wrench', 20) ?></div>
                    <h3>Repair shops</h3>
                    <p>Parts, labour and job history tracked against the customer who booked it.</p>
                </a>
                <a href="../for-cleaning-companies/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('spray-bottle', 20) ?></div>
                    <h3>Cleaning companies</h3>
                    <p>Recurring invoices, supplies, and staff costs per contract.</p>
                </a>
                <a href="../for-solo-operators/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('user', 20) ?></div>
                    <h3>Solo operators</h3>
                    <p>One person, one price, and books that do not need a bookkeeper.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Can I bill a deposit, a mid-job draw, and a final balance?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Most contractors send three invoices on a multi-week job: a deposit invoice before work begins, a draw invoice at a milestone like framing or rough-in, and a final invoice when the work is signed off.</p>
                            <p>Argo Books tracks what's been paid on each so the final balance lines up with what the customer still owes.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I bill change orders without re-issuing the original invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Add each change order as its own line item on the next progress invoice, or send a separate change-order invoice.</p>
                            <p>Keeping changes on their own lines makes it easy for the customer to see exactly what they signed off on versus the original scope.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I track materials and labor separately?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. List materials and labor on separate lines of the invoice, or roll materials into a single marked-up line if that's how you priced the bid.</p>
                            <p>On the expense side, scan the supply-house receipt and tag it Materials, Equipment, or Subcontractor so the report later actually means something.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet at the job site?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books do job costing per project?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Not the way QuickBooks Contractor or a dedicated job-costing tool does. Argo Books tracks expenses by category and revenue by customer and invoice, which covers most solo contractors and small crews.</p>
                            <p>If you need a true per-project P&L across labor, materials, subs, and overhead, a job-costing tool is a better fit. Many contractors run a simpler bookkeeping tool alongside their estimating or scheduling software.</p>
                        
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
        <p>Want the bookkeeping side in plain language? Read our guide to <a href="../bookkeeping-for-contractors/">bookkeeping for contractors</a>.</p>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to clean up the books before the next bid?</h2>
                    <p>Download Argo Books for free. Set up your first customer, scan a supply-house receipt, and send a progress invoice in under ten minutes.</p>
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
