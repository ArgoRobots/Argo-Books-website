<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_resellers');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-resellers';
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
        content="Accounting software for online resellers and thrift flippers. Track cost of goods, sourcing receipts, and margins by channel. Free desktop app for Windows and Linux.">
    <meta name="keywords"
        content="accounting software for resellers, ebay reseller bookkeeping, amazon fba accounting, thrift flipper accounting, online reseller tax software">

    <meta property="og:title" content="Argo Books for Resellers: Cost of Goods, Sourcing Receipts, and Real Margins">
    <meta property="og:description"
        content="Track what every item cost you, where it sold, and what's left in inventory. Free desktop app for resellers.">
    <meta property="og:url" content="https://argorobots.com/for-resellers/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Resellers: Cost of Goods, Sourcing Receipts, and Real Margins">
    <meta name="twitter:description"
        content="Track what every item cost you, where it sold, and what's left in inventory.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-resellers/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Resellers" => "/for-resellers/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I track what I paid for each item versus what it sold for?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Add the item to inventory at the price you paid (the thrift price, the auction lot share, the wholesale unit cost), and when it sells, log the sale. The cost-of-goods number for your taxes lines up with what you actually spent."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I record sales across eBay, Amazon, and Facebook Marketplace?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, by tagging each sale with the channel as the customer or category. Argo Books does not pull the sale in automatically from those platforms, so you enter them manually or import a CSV the marketplace gives you. Many resellers do this weekly and treat it like an end-of-week routine."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track mileage to sourcing trips as an expense?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Log mileage as an expense line with the date and the trip distance, tag it Sourcing or Vehicle, and the totals show up on the expense report. At tax time, the mileage deduction is sitting where you put it."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet at a garage sale or auction?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your laptop and does not need an internet connection to scan receipts, add inventory, or build a record. Take photos at the auction, enter them later that night at the kitchen table."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books sync with my eBay or Amazon account automatically?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not connect directly to eBay, Amazon, Etsy, or Mercari APIs. If you sell at high volume and need automated sync, A2X, Link My Books, or QuickBooks Commerce integrate. For a side hustle or solo reseller, a weekly manual or CSV-import workflow with Argo Books gives you clean cost-of-goods and tax-prep numbers without paying for an integration."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is it really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, forever. The free tier covers all core features including inventory management and <?= $free_invoices ?> invoices per month. Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books for Resellers: Cost of Goods, Sourcing Receipts, and Real Margins</title>

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
                    <h1>Accounting software for resellers</h1>
                    <p class="fp-hero-sub">Track what every item cost you, where it sold, and what the margin actually was. Sourcing receipts, inventory, and the tax-time picture, all in one app.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="inventory">
                    <?= argo_feature_demo('inventory') ?>
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
                <span class="section-label">Made for Resellers</span>
                <h2>Buy low, sell higher, remember exactly what each one cost</h2>
                <p class="section-desc">Reselling is the garage sale at 7 AM, the auction lot at noon, the wholesale pallet on Tuesday, and a shelf in the garage that's worth more than it looks. At tax time, the IRS wants clean cost-of-goods numbers. Argo Books tracks what you paid for each item, what you sold it for, and what's still sitting in inventory, so the margin is real and the deductions are real.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shopping-bag', 20) ?>
                    </div>
                    <h3>Every item, from purchase to sale</h3>
                    <p>Add an item to inventory at the price you paid: the thrift price, the auction-lot unit cost, the wholesale per-piece. When it sells, log the sale at the price you got. The cost-of-goods number that flows into your taxes is exactly what you spent, not an estimate.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from the thrift store, the auction, or the wholesale lot</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Sourcing, Shipping Supplies, or Vehicle so when the year wraps up, every deductible expense is sitting in a category, not in a shoebox.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('bar-chart', 20) ?>
                    </div>
                    <h3>See the margin before tax time, not after</h3>
                    <p>Argo Books shows revenue, cost of goods, and the gap between them in real time. You stop running the business on vibes. Slow-selling categories show up as slow. Profitable ones get more shelf space.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works offline, free tier covers solo resellers</h3>
                    <p>Argo Books runs natively on Windows and Linux. No internet needed in the garage or at the auction, no monthly subscription climbing every year. The free tier covers most side-hustle and solo full-time resellers forever.</p>
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
                <p>Argo Books does not connect directly to eBay, Amazon, Etsy, or Mercari. It does not pull your marketplace sales in automatically, it does not print shipping labels, and it does not sync inventory across channels. If you sell at high volume and need that automation, tools like A2X, Link My Books, or QuickBooks Commerce are built for it. For solo and side-hustle resellers who can spend ten minutes a week logging sales by hand or importing a marketplace CSV, Argo Books gives you the cost-of-goods, margin, and tax-prep picture without the integration costs. Free desktop app, no monthly fee creeping up, your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most resellers stay on the free tier. Premium adds predictive analytics so you can see which categories are trending up and which are dying, unlimited invoicing, and priority support.</p>
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
                <a href="../for-local-wholesalers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('truck', 20) ?></div>
                    <h3>Local wholesalers</h3>
                    <p>Stock, supplier orders and trade accounts on terms.</p>
                </a>
                <a href="../for-rental-businesses/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('calendar', 20) ?></div>
                    <h3>Rental businesses</h3>
                    <p>Bookings, availability and returns on one calendar.</p>
                </a>
                <a href="../for-software-companies/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('code-window', 20) ?></div>
                    <h3>Software companies</h3>
                    <p>Subscriptions, contractor costs and runway.</p>
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
            ob_start(); ?>Can I track what I paid for each item versus what it sold for?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Add the item to inventory at the price you paid (the thrift price, the auction lot share, the wholesale unit cost), and when it sells, log the sale.</p>
                            <p>The cost-of-goods number for your taxes lines up with what you actually spent.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I record sales across eBay, Amazon, and Facebook Marketplace?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, by tagging each sale with the channel as the customer or category. Argo Books does not pull the sale in automatically from those platforms, so you enter them manually or import a CSV the marketplace gives you.</p>
                            <p>Many resellers do this weekly and treat it like an end-of-week routine.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I track mileage to sourcing trips as an expense?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Log mileage as an expense line with the date and the trip distance, tag it Sourcing or Vehicle, and the totals show up on the expense report.</p>
                            <p>At tax time, the mileage deduction is sitting where you put it.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet at a garage sale or auction?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to scan receipts, add inventory, or build a record.</p>
                            <p>Take photos at the auction, enter them later that night at the kitchen table.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books sync with my eBay or Amazon account automatically?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>No. Argo Books does not connect directly to eBay, Amazon, Etsy, or Mercari APIs.</p>
                            <p>If you sell at high volume and need automated sync, A2X, Link My Books, or QuickBooks Commerce integrate. For a side hustle or solo reseller, a weekly manual or CSV-import workflow with Argo Books gives you clean cost-of-goods and tax-prep numbers without paying for an integration.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is it really free?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, forever. The free tier covers all core features including inventory management and <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            echo argo_faq_grid($faqs); ?>
        </div>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to know your margin in real time?</h2>
                    <p>Download Argo Books for free. Add your first item to inventory, scan a sourcing receipt, and log a sale in under ten minutes.</p>
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
