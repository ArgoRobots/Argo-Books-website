<?php
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
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Auto+Detailing&subtitle=Tiered+packages%2C+ceramic+coating+jobs%2C+supply+receipts%2C+and+recurring+memberships&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Auto Detailing: Packages, Add-Ons, and the Books, in One App">
    <meta name="twitter:description"
        content="Tiered packages, ceramic coating jobs, supply receipts, and recurring memberships.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Auto+Detailing&subtitle=Tiered+packages%2C+ceramic+coating+jobs%2C+supply+receipts%2C+and+recurring+memberships&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-auto-detailing/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Auto Detailing", "item": "https://argorobots.com/for-auto-detailing/"}
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
            <h1 class="animate-fade-in">Accounting software for auto detailing</h1>
            <p class="hero-subtitle animate-fade-in">Built for tiered packages, ceramic coating jobs, and the supply receipts that quietly add up. From the express wash to the full multi-day correction.</p>
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
                <span class="section-label">Made for Detailers</span>
                <h2>Tiered packages, real margins, less paperwork</h2>
                <p class="section-desc">Detailing is the package menu (express, full, ceramic coating), the up-charge when the back seat has more dog hair than fabric, and the supply stack that keeps growing in the trailer. Mobile or shop-based, solo or with a few hands, the work that builds the business is repeat customers paying premium for premium work. Argo Books handles the books so you can keep cutting paint.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('document-lines', 28, '', 1.5) ?>
                    </div>
                    <h3>Base package and add-ons on one clean invoice</h3>
                    <p>Express, Full, or Ceramic Coating on the top line. Pet hair, heavy dirt, headlight restoration, or engine bay each on their own line. The customer sees the base price and what the extras added, which keeps the up-charge conversation short and the bill itemized.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('refresh', 28, '', 1.5) ?>
                    </div>
                    <h3>Recurring invoices for memberships and fleet accounts</h3>
                    <p>Monthly maintenance memberships and weekly fleet washes both run on the same recurring engine. Set the client, the package, and the frequency once, and the invoice goes out on time every cycle.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from the detail supply house or the gas station</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Supplies, Ceramic Products, Fuel, or Equipment so you can actually see what the supply stack costs you each month and price the next package accordingly.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline in the driveway, free tier covers solo detailers</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the customer's driveway, no monthly subscription climbing every year. Mobile detailers can build the invoice with no signal, send it when they're back in coverage. The free tier covers most solo detailers forever.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of detailing invoice with packages + add-ons. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="An auto detailing invoice showing a ceramic coating package and pet-hair add-on line">
                </div>
                <p class="screenshot-caption">A ceramic coating invoice with the package on top and add-ons below.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of receipt-scan UI on a detail-supply receipt. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a detail-supply receipt">
                </div>
                <p class="screenshot-caption">A receipt from the detail supply house scanned and tagged.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of main dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing revenue, expenses, and outstanding invoices">
                </div>
                <p class="screenshot-caption">Your dashboard with revenue, supply spend, and what's outstanding.</p>
            </div>
        </div>
    </section>

    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>What Argo Books isn't</h2>
                <p class="section-desc">Argo Books is bookkeeping software, not booking software. It does not run a customer-facing booking calendar, take online appointments through your website, or send "on the way" texts before you arrive. Mobile Tech RX, Urable, and DetailPlus handle that side. It also does not run a dedicated ceramic coating warranty database. If you need either, run them alongside Argo Books: those for booking and warranties, Argo Books for the books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo detailers and one-shop operations stay on the free tier. Premium adds predictive analytics for slow-season planning, unlimited invoicing, and priority support.</p>
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
                        <h3>Can I list a base package and add-ons on the same invoice?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The base package (Express, Full, Ceramic Coating) goes on the top line. Add-ons (pet hair, heavy dirt, headlight restoration, engine bay) each get their own line.</p>
                            <p>The customer sees what the base price was and what the extras added, which keeps the up-charge conversation short.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I set up recurring monthly invoices for membership clients?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Set the client, the package, and the frequency once. Argo Books generates the invoice on schedule for monthly maintenance memberships or weekly fleet accounts.</p>
                            <p>You stop forgetting to bill the regulars.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track ceramic coating warranty information per customer?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>You can record warranty details on the invoice notes and on the customer record, so the information lives with the customer history. Argo Books does not run a dedicated warranty database with expiration alerts.</p>
                            <p>If that level of warranty tracking is critical, a detail-specific tool like Urable handles it, and you can keep Argo Books for the books.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet (mobile detailers in driveways)?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to build the invoice in the driveway.</p>
                            <p>You only need internet when you actually send the invoice or take a card payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books have an online booking calendar?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No. Argo Books does not run a customer-facing booking calendar or accept reservations through your website.</p>
                            <p>Mobile Tech RX, Urable, and DetailPlus handle that side. Run them alongside Argo Books: those for booking, Argo Books for the books.</p>
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
                            <p>Yes, forever. The free tier covers all core features and <?= $free_invoices ?> invoices per month.</p>
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
                    <h2>Ready to bill like the work is worth it?</h2>
                    <p>Download Argo Books for free. Set up your first package, scan a supply receipt, and send a detailing invoice in under ten minutes.</p>
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
