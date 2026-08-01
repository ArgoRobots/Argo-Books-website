<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_rental_businesses');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-rental-businesses';
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
        content="Accounting software for equipment, tool, party, and AV rental businesses. Built-in rental management tracks what's out, who has it, and what they owe. Free desktop app.">
    <meta name="keywords"
        content="accounting software for rental business, rental management software, equipment rental bookkeeping, party rental accounting, tool rental software">

    <meta property="og:title" content="Argo Books for Rental Businesses: Rental Tracking and Books, in One App">
    <meta property="og:description"
        content="Track what's out, who has it, and when it's coming back. Rental management built in. Free desktop app.">
    <meta property="og:url" content="https://argorobots.com/for-rental-businesses/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Rental Businesses: Rental Tracking and Books, in One App">
    <meta name="twitter:description"
        content="Track what's out, who has it, and when it's coming back. Rental management built in.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-rental-businesses/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Rental Businesses" => "/for-rental-businesses/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "Does Argo Books actually have rental management built in?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Rental Management is a built-in feature, not an add-on. Track items in your fleet, see what's out, who has it, and when it's due back. When the rental closes, the invoice already knows the rental period and rate."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I charge a security deposit separately from the rental fee?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Bill the security deposit as its own line, take payment, and refund it (or apply it against damage) when the item is returned. The rental fee is a separate line item with its own period and rate."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track late returns and damage charges?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. When an item is returned late or damaged, add a line to the rental invoice for the extra days at your late rate, or for the damage or replacement cost. If you already collected a security deposit, credit it against the charge so the customer only owes the remainder."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to log a rental, check an item back in, or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books take online reservations or send pickup reminders?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not yet. Argo Books tracks rentals and handles the books once a rental is booked, but it does not run an online booking calendar on your website or send automated SMS reminders. If those are critical, tools like Booqable, Rentle, or EZRentOut handle the booking side, and you can run Argo Books alongside for the bookkeeping."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is it really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, forever. The free tier covers all core features including rental management and <?= $free_invoices ?> invoices per month. Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books for Rental Businesses: Rental Tracking and Books, in One App</title>

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
                    <h1>Accounting software for rental businesses</h1>
                    <p class="fp-hero-sub">Built around what you rent, who has it, when it's coming back, and what they owe. Rental management is included, not an add-on.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="rental">
                    <?= argo_feature_demo('rental') ?>
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
                <span class="section-label">Made for Rental Businesses</span>
                <h2>Your fleet, your customers, your books, in one app</h2>
                <p class="section-desc">A rental business lives in three places: the yard where the equipment sits, the customer site where the equipment is in use, and the books where the deposit, the rental fee, and any late or damage charges have to land. Whether you rent tools, party tents, scaffolding, AV gear, or bounce houses, Argo Books keeps the three in sync.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('package-detail', 20) ?>
                    </div>
                    <h3>Track what's out, who has it, and when it's coming back</h3>
                    <p>Argo Books has rental management built in. Add an item to your fleet, log it out to a customer with a rental period and rate, and when it comes back, the invoice already knows what's owed. No spreadsheet, no sticky notes on the office wall.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('credit-card', 20) ?>
                    </div>
                    <h3>Deposit, rental fee, damage hold, all on the right line</h3>
                    <p>Bill the security deposit as its own line, the rental at the daily or weekly rate, and any late-return or damage charge as a separate line when the item comes back. Refund the deposit, apply it against damage, or roll the leftover into the next rental. The customer sees exactly what they paid.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt when you buy stock for the fleet</h3>
                    <p>Take a photo of the supplier receipt when you buy a new generator, a new tent, or a new case of replacement parts. Argo Books pulls the vendor, date, and amount automatically. Tag it Fleet Purchase or Repair so when you look at margins next quarter, the numbers are sitting where you put them.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works offline, free tier covers small fleets</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the yard, no monthly subscription climbing every year, no website to wait on when you're checking out a customer. The free tier covers most small fleets forever.</p>
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
                <p>Argo Books has rental management for the operating and bookkeeping side, but it is not an online booking platform. It does not run a reservation calendar on your website, send automated pickup-and-return SMS reminders, or handle customer-facing self-service rentals. If those are critical, Booqable, Rentle, or EZRentOut handle the booking, and Argo Books handles the books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the rental tracking is built in, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most small rental businesses stay on the free tier. Premium adds predictive analytics for seasonal demand planning, unlimited invoicing, and priority support.</p>
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
                <a href="../for-resellers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('tag', 20) ?></div>
                    <h3>Resellers</h3>
                    <p>Cost, margin and stock across everything you list.</p>
                </a>
                <a href="../for-contractors/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('hard-hat', 20) ?></div>
                    <h3>Contractors</h3>
                    <p>Deposits, mid-job draws, materials and change orders.</p>
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
            ob_start(); ?>Does Argo Books actually have rental management built in?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Rental Management is a built-in feature, not an add-on. Track items in your fleet, see what's out, who has it, and when it's due back.</p>
                            <p>When the rental closes, the invoice already knows the rental period and rate.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I charge a security deposit separately from the rental fee?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Bill the security deposit as its own line, take payment, and refund it (or apply it against damage) when the item is returned.</p>
                            <p>The rental fee is a separate line item with its own period and rate.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I track late returns and damage charges?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. When an item is returned late or damaged, add a line to the rental invoice for the extra days at your late rate, or for the damage or replacement cost.</p>
                            <p>If you already collected a security deposit, credit it against the charge so the customer only owes the remainder.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to log a rental, check an item back in, or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books take online reservations or send pickup reminders?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Not yet. Argo Books tracks rentals and handles the books once a rental is booked, but it does not run an online booking calendar on your website or send automated SMS reminders.</p>
                            <p>If those are critical, tools like Booqable, Rentle, or EZRentOut handle the booking side, and you can run Argo Books alongside for the bookkeeping.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is it really free?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes, forever. The free tier covers all core features including rental management and <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            echo argo_faq_grid($faqs); ?>
        </div>
    </section>

    <section class="container" style="max-width:720px;text-align:center;padding-bottom:48px;">
        <p>Want the bookkeeping side in plain language? Read our guide to <a href="../bookkeeping-for-rental-businesses/">bookkeeping for rental businesses</a>.</p>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to track your fleet and your books in one place?</h2>
                    <p>Download Argo Books for free. Add your first rental item, check it out to a customer, and build the closing invoice in under ten minutes.</p>
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
