<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_auto_detailing');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-auto-detailing';
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
        content="Accounting software for mobile detailers, shop-based detailers, and ceramic coating specialists. Built for tiered packages, add-ons, and recurring memberships. Free desktop app.">
    <meta name="keywords"
        content="accounting software for auto detailing, mobile detailer bookkeeping, ceramic coating invoicing, detail shop accounting, auto detailing business software">

    <meta property="og:title" content="Argo Books for Auto Detailing: Packages, Add-Ons, and the Books, in One App">
    <meta property="og:description"
        content="Tiered packages, ceramic coating jobs, supply receipts, and recurring memberships. Free desktop app for detailers.">
    <meta property="og:url" content="https://argorobots.com/for-auto-detailing/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Auto Detailing: Packages, Add-Ons, and the Books, in One App">
    <meta name="twitter:description"
        content="Tiered packages, ceramic coating jobs, supply receipts, and recurring memberships.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-auto-detailing/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Auto Detailing" => "/for-auto-detailing/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Can I list a base package and add-ons on the same invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The base package (Express, Full, Ceramic Coating) goes on the top line. Add-ons (pet hair, heavy dirt, headlight restoration, engine bay) each get their own line. The customer sees what the base price was and what the extras added, which keeps the up-charge conversation short."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I set up recurring monthly invoices for membership clients?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Set the client, the package, and the frequency once. Argo Books generates the invoice on schedule for monthly maintenance memberships or weekly fleet accounts. You stop forgetting to bill the regulars."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track ceramic coating warranty information per customer?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can record warranty details on the invoice notes and on the customer record, so the information lives with the customer history. Argo Books does not run a dedicated warranty database with expiration alerts. If that level of warranty tracking is critical, a detail-specific tool like Urable handles it, and you can keep Argo Books for the books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet (mobile detailers in driveways)?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your laptop and does not need an internet connection to build the invoice in the driveway. You only need internet when you actually send the invoice or take a card payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books have an online booking calendar?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not run a customer-facing booking calendar or accept reservations through your website. Mobile Tech RX, Urable, and DetailPlus handle that side. Run them alongside Argo Books: those for booking, Argo Books for the books."
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
    <title>Argo Books for Auto Detailing: Packages, Add-Ons, and the Books, in One App</title>

    <script src="../resources/scripts/main.js"></script>
    <!-- Drives the mockup in the hero. -->
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

    <!-- Hero. Split, with the live mockup beside the copy rather than centred
         text on a gradient. Same demo markup and loop the landing page uses. -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Accounting software for auto detailing</h1>
                    <p class="fp-hero-sub">Built for tiered packages, ceramic coating jobs, and the supply receipts that quietly add up. From the express wash to the full multi-day correction.</p>
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

    <!-- SmartScreen walkthrough, revealed by lp-direct-download.php after a
         Windows direct-download click. -->
    <div class="container">
        <?php include __DIR__ . '/../resources/smartscreen-guide/guide.php'; ?>
    </div>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Detailers</span>
                <h2>Tiered packages, real margins, less paperwork</h2>
                <p class="section-desc">Detailing is the package menu (express, full, ceramic coating), the up-charge when the back seat has more dog hair than fabric, and the supply stack that keeps growing in the trailer. Mobile or shop-based, solo or with a few hands, the work that builds the business is repeat customers paying premium for premium work. Argo Books handles the books so you can keep cutting paint.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('document-lines', 20) ?>
                    </div>
                    <h3>Base package and add-ons on one clean invoice</h3>
                    <p>Express, Full, or Ceramic Coating on the top line. Pet hair, heavy dirt, headlight restoration, or engine bay each on their own line. The customer sees the base price and what the extras added, which keeps the up-charge conversation short and the bill itemized.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('refresh', 20) ?>
                    </div>
                    <h3>Recurring invoices for memberships and fleet accounts</h3>
                    <p>Monthly maintenance memberships and weekly fleet washes both run on the same recurring engine. Set the client, the package, and the frequency once, and the invoice goes out on time every cycle.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from the detail supply house or the gas station</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Supplies, Ceramic Products, Fuel, or Equipment so you can actually see what the supply stack costs you each month and price the next package accordingly.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works offline in the driveway, free tier covers solo detailers</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the customer's driveway, no monthly subscription climbing every year. Mobile detailers can build the invoice with no signal, send it when they're back in coverage. The free tier covers most solo detailers forever.</p>
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
                <p>Argo Books is bookkeeping software, not booking software. It does not run a customer-facing booking calendar, take online appointments through your website, or send "on the way" texts before you arrive. Mobile Tech RX, Urable, and DetailPlus handle that side. It also does not run a dedicated ceramic coating warranty database. If you need either, run them alongside Argo Books: those for booking and warranties, Argo Books for the books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo detailers and one-shop operations stay on the free tier. Premium adds predictive analytics for slow-season planning, unlimited invoicing, and priority support.</p>
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
                <a href="../for-repair-shops/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('wrench', 20) ?></div>
                    <h3>Repair shops</h3>
                    <p>Parts, labour and job history against the customer who booked it.</p>
                </a>
                <a href="../for-cleaning-companies/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('spray-bottle', 20) ?></div>
                    <h3>Cleaning companies</h3>
                    <p>Recurring invoices, supplies and staff cost per contract.</p>
                </a>
                <a href="../for-solo-operators/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('user', 20) ?></div>
                    <h3>Solo operators</h3>
                    <p>One person, one price, books that need no bookkeeper.</p>
                </a>
                <a href="../for-contractors/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('hard-hat', 20) ?></div>
                    <h3>Contractors</h3>
                    <p>Deposits, mid-job draws, materials and change orders.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Can I list a base package and add-ons on the same invoice?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The base package (Express, Full, Ceramic Coating) goes on the top line. Add-ons (pet hair, heavy dirt, headlight restoration, engine bay) each get their own line.</p>
                            <p>The customer sees what the base price was and what the extras added, which keeps the up-charge conversation short.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I set up recurring monthly invoices for membership clients?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Set the client, the package, and the frequency once. Argo Books generates the invoice on schedule for monthly maintenance memberships or weekly fleet accounts.</p>
                            <p>You stop forgetting to bill the regulars.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I track ceramic coating warranty information per customer?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>You can record warranty details on the invoice notes and on the customer record, so the information lives with the customer history. Argo Books does not run a dedicated warranty database with expiration alerts.</p>
                            <p>If that level of warranty tracking is critical, a detail-specific tool like Urable handles it, and you can keep Argo Books for the books.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet (mobile detailers in driveways)?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to build the invoice in the driveway.</p>
                            <p>You only need internet when you actually send the invoice or take a card payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books have an online booking calendar?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>No. Argo Books does not run a customer-facing booking calendar or accept reservations through your website.</p>
                            <p>Mobile Tech RX, Urable, and DetailPlus handle that side. Run them alongside Argo Books: those for booking, Argo Books for the books.</p>
                        
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
                    <h2>Ready to bill like the work is worth it?</h2>
                    <p>Download Argo Books for free. Set up your first package, scan a supply receipt, and send a detailing invoice in under ten minutes.</p>
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
