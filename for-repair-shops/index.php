<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_repair_shops');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-repair-shops';
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
        content="Accounting software for auto, appliance, electronics, and small-engine repair shops. Built for diagnostic fees, parts markup, and labor on one invoice. Free desktop app.">
    <meta name="keywords"
        content="accounting software for repair shops, repair shop bookkeeping, auto repair invoicing software, appliance repair accounting, small engine repair software">

    <meta property="og:title" content="Argo Books for Repair Shops: Parts, Labor, and the Books, Together">
    <meta property="og:description"
        content="Diagnostic fee, parts at your markup, and labor at your shop rate, on one clean invoice. Free desktop app for repair shops.">
    <meta property="og:url" content="https://argorobots.com/for-repair-shops/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Repair Shops: Parts, Labor, and the Books, Together">
    <meta name="twitter:description"
        content="Diagnostic fee, parts at your markup, and labor at your shop rate, on one clean invoice.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-repair-shops/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Repair Shops" => "/for-repair-shops/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I itemize the diagnostic fee, parts, and labor on one invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Add each as its own line: a diagnostic fee, each part with quantity and unit price, and labor in hours at your shop rate. Customers see exactly what they paid for, which cuts down on the 'why is the bill this high' conversation at pickup."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I mark up parts on the invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, two ways. List parts at the price the customer pays (your cost plus markup) and keep the wholesale cost in the expense record. Or itemize parts at cost with a separate handling-and-stocking line. Either approach works. Most shops keep the markup invisible and just show the customer-facing price."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I take a deposit on a big repair before ordering parts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Send a deposit invoice with the parts-cost line, take the payment, and when the repair is done send the final invoice with the deposit already credited. The remaining balance is what the customer pays at pickup."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet at the bench?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your shop computer and does not need an internet connection to record expenses or build an invoice. You only need internet when you actually send the invoice or take a card payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books have work orders or customer text messaging?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not in the way Shopmonkey, Tekmetric, or RepairShopr do. Argo Books handles the books, the invoice, and the receipt of payment. If you need a work-order queue, a customer-facing pickup notification, or a VIN-and-labor-guide library, a dedicated shop management tool is a better fit. Many shops run one of those alongside a simpler bookkeeping tool."
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
    <title>Argo Books for Repair Shops: Parts, Labor, and the Books, Together</title>

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
                    <h1>Accounting software for repair shops</h1>
                    <p class="fp-hero-sub">Built for the way you actually bill: the diagnostic fee, parts at your markup, and labor at your shop rate, on one clean invoice.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
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
                <span class="section-label">Made for Repair Shops</span>
                <h2>Diagnostic fee, parts, labor, paid</h2>
                <p class="section-desc">A repair invoice is the diagnostic fee, the labor at your shop rate, the parts at your markup, and sometimes a deposit before the part even leaves the supplier. Whether you fix cars, appliances, small engines, or electronics, Argo Books handles the books so you can stay at the bench.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('document-lines', 20) ?>
                    </div>
                    <h3>Diagnostic fee, parts, and labor on one clean invoice</h3>
                    <p>Itemize the diagnostic fee, each part at the price the customer pays, and labor in hours at your shop rate. The customer sees exactly what they paid for, which cuts the awkward conversation at pickup down to a thank-you and a card swipe.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from the parts supplier or hardware store</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Parts, Shop Supplies, Tools, or Fluids so when you actually look at margins next quarter, the numbers are sitting where you put them.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works offline at the bench, your data stays on your computer</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed to log a repair or build an invoice, no monthly subscription climbing every year, no website timing out when the shop wifi flakes. The free tier covers most one- and two-person shops forever.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('credit-card', 20) ?>
                    </div>
                    <h3>Get paid before the customer drives off</h3>
                    <p>Hand over the keys, swipe the card through Stripe or Square, and the invoice is marked paid. Or email the invoice on the spot and the customer can pay from the parking lot. Either way, you don't carry the balance home.</p>
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
                <p>Argo Books is bookkeeping software, not shop management software. It does not run a work-order queue, send pickup-ready texts to customers, or look up labor times from a VIN. If you need Shopmonkey, Tekmetric, or RepairShopr for the front-of-shop workflow, run them side by side: those for the queue, Argo Books for your books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most one- and two-person shops stay on the free tier. Premium adds predictive analytics for slow-season planning, unlimited invoicing, and priority support.</p>
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
                <a href="../for-auto-detailing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('car', 20) ?></div>
                    <h3>Auto detailing</h3>
                    <p>Per-vehicle jobs, products used, and repeat customers.</p>
                </a>
                <a href="../for-contractors/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('hard-hat', 20) ?></div>
                    <h3>Contractors</h3>
                    <p>Deposits, mid-job draws, materials and change orders.</p>
                </a>
                <a href="../for-local-wholesalers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('truck', 20) ?></div>
                    <h3>Local wholesalers</h3>
                    <p>Stock, supplier orders and trade accounts on terms.</p>
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
            ob_start(); ?>Can I itemize the diagnostic fee, parts, and labor on one invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Add each as its own line: a diagnostic fee, each part with quantity and unit price, and labor in hours at your shop rate.</p>
                            <p>Customers see exactly what they paid for, which cuts down on the "why is the bill this high" conversation at pickup.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I mark up parts on the invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, two ways. List parts at the price the customer pays (your cost plus markup) and keep the wholesale cost in the expense record. Or itemize parts at cost with a separate handling-and-stocking line.</p>
                            <p>Either approach works. Most shops keep the markup invisible and just show the customer-facing price.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I take a deposit on a big repair before ordering parts?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Send a deposit invoice with the parts-cost line, take the payment, and when the repair is done send the final invoice with the deposit already credited.</p>
                            <p>The remaining balance is what the customer pays at pickup.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet at the bench?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your shop computer and does not need an internet connection to record expenses or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a card payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books have work orders or customer text messaging?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Not in the way Shopmonkey, Tekmetric, or RepairShopr do. Argo Books handles the books, the invoice, and the receipt of payment.</p>
                            <p>If you need a work-order queue, a customer-facing pickup notification, or a VIN-and-labor-guide library, a dedicated shop management tool is a better fit. Many shops run one of those alongside a simpler bookkeeping tool.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is it really free?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, forever. The free tier covers all core features and <?= $free_invoices ?> invoices per month.</p>
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
                    <h2>Ready to close out the books like you close out a repair?</h2>
                    <p>Download Argo Books for free. Set up your first customer, scan a parts receipt, and send a repair invoice in under ten minutes.</p>
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
