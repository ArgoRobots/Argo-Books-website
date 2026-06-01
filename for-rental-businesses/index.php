<?php
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
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Rental+Businesses&subtitle=Track+what%27s+out%2C+who+has+it%2C+and+when+it%27s+coming+back.+Rental+management+built+in.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Rental Businesses: Rental Tracking and Books, in One App">
    <meta name="twitter:description"
        content="Track what's out, who has it, and when it's coming back. Rental management built in.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Rental+Businesses&subtitle=Track+what%27s+out%2C+who+has+it%2C+and+when+it%27s+coming+back.+Rental+management+built+in.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-rental-businesses/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Rental Businesses", "item": "https://argorobots.com/for-rental-businesses/"}
            ]
        }
    </script>

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

    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Accounting software for rental businesses</h1>
            <p class="hero-subtitle animate-fade-in">Built around what you rent, who has it, when it's coming back, and what they owe. Rental management is included, not an add-on.</p>
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

    <section class="made-for-intro">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Rental Businesses</span>
                <h2>Your fleet, your customers, your books, in one app</h2>
                <p class="section-desc">A rental business lives in three places: the yard where the equipment sits, the customer site where the equipment is in use, and the books where the deposit, the rental fee, and any late or damage charges have to land. Whether you rent tools, party tents, scaffolding, AV gear, or bounce houses, Argo Books keeps the three in sync.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('package-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Track what's out, who has it, and when it's coming back</h3>
                    <p>Argo Books has rental management built in. Add an item to your fleet, log it out to a customer with a rental period and rate, and when it comes back, the invoice already knows what's owed. No spreadsheet, no sticky notes on the office wall.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('credit-card', 28, '', 1.5) ?>
                    </div>
                    <h3>Deposit, rental fee, damage hold, all on the right line</h3>
                    <p>Bill the security deposit as its own line, the rental at the daily or weekly rate, and any late-return or damage charge as a separate line when the item comes back. Refund the deposit, apply it against damage, or roll the leftover into the next rental. The customer sees exactly what they paid.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt when you buy stock for the fleet</h3>
                    <p>Take a photo of the supplier receipt when you buy a new generator, a new tent, or a new case of replacement parts. Argo Books pulls the vendor, date, and amount automatically. Tag it Fleet Purchase or Repair so when you look at margins next quarter, the numbers are sitting where you put them.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline, free tier covers small fleets</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the yard, no monthly subscription climbing every year, no website to wait on when you're checking out a customer. The free tier covers most small fleets forever.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="screenshot-strip">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">In the App</span>
                <h2>What it actually looks like</h2>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of rental management screen showing items in fleet + status. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/rental-items.svg" alt="The Argo Books rental management screen showing fleet items, status, and current rentals">
                </div>
                <p class="screenshot-caption">Your fleet, with what's out, what's in, and what's coming back.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of rental invoice showing deposit + rental fee + late/damage lines. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="A rental invoice showing deposit, rental fee, and a late-return line">
                </div>
                <p class="screenshot-caption">A rental invoice with the deposit, rental fee, and a late line.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of main dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing revenue, expenses, and outstanding invoices">
                </div>
                <p class="screenshot-caption">Your dashboard with revenue, expenses, and what's outstanding.</p>
            </div>
        </div>
    </section>

    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>What Argo Books isn't</h2>
                <p class="section-desc">Argo Books has rental management for the operating and bookkeeping side, but it is not an online booking platform. It does not run a reservation calendar on your website, send automated pickup-and-return SMS reminders, or handle customer-facing self-service rentals. If those are critical, Booqable, Rentle, or EZRentOut handle the booking, and Argo Books handles the books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the rental tracking is built in, and your data stays on your computer.</p>
                <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary honest-take-cta">
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

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books actually have rental management built in?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Rental Management is a built-in feature, not an add-on. Track items in your fleet, see what's out, who has it, and when it's due back.</p>
                            <p>When the rental closes, the invoice already knows the rental period and rate.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I charge a security deposit separately from the rental fee?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Bill the security deposit as its own line, take payment, and refund it (or apply it against damage) when the item is returned.</p>
                            <p>The rental fee is a separate line item with its own period and rate.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track late returns and damage charges?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. When an item is returned late or damaged, add a line to the rental invoice for the extra days at your late rate, or for the damage or replacement cost.</p>
                            <p>If you already collected a security deposit, credit it against the charge so the customer only owes the remainder.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to log a rental, check an item back in, or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books take online reservations or send pickup reminders?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Not yet. Argo Books tracks rentals and handles the books once a rental is booked, but it does not run an online booking calendar on your website or send automated SMS reminders.</p>
                            <p>If those are critical, tools like Booqable, Rentle, or EZRentOut handle the booking side, and you can run Argo Books alongside for the bookkeeping.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is it really free?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, forever. The free tier covers all core features including rental management and <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to track your fleet and your books in one place?</h2>
                    <p>Download Argo Books for free. Add your first rental item, check it out to a customer, and build the closing invoice in under ten minutes.</p>
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
            const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-visible');
                    }
                });
            }, observerOptions);
            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

            const faqItems = document.querySelectorAll('.faq-item');
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    faqItems.forEach(otherItem => otherItem.classList.remove('active'));
                    if (!isActive) item.classList.add('active');
                });
            });
        });
    </script>
</body>

</html>
