<?php
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
        content="Accounting software for online resellers and thrift flippers. Track cost of goods, sourcing receipts, and margins by channel. Free desktop app for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for resellers, ebay reseller bookkeeping, amazon fba accounting, thrift flipper accounting, online reseller tax software">

    <meta property="og:title" content="Argo Books for Resellers: Cost of Goods, Sourcing Receipts, and Real Margins">
    <meta property="og:description"
        content="Track what every item cost you, where it sold, and what's left in inventory. Free desktop app for resellers.">
    <meta property="og:url" content="https://argorobots.com/for-resellers/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Resellers&subtitle=Cost+of+goods%2C+sourcing+receipts%2C+and+real+margins+for+online+resellers+and+thrift+flippers&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Resellers: Cost of Goods, Sourcing Receipts, and Real Margins">
    <meta name="twitter:description"
        content="Track what every item cost you, where it sold, and what's left in inventory.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Resellers&subtitle=Cost+of+goods%2C+sourcing+receipts%2C+and+real+margins+for+online+resellers+and+thrift+flippers&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-resellers/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Resellers", "item": "https://argorobots.com/for-resellers/"}
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

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
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
        <div id="includeHeader"></div>
    </header>
    <main>

    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Accounting software for resellers</h1>
            <p class="hero-subtitle animate-fade-in">Track what every item cost you, where it sold, and what the margin actually was. Sourcing receipts, inventory, and the tax-time picture, all in one app.</p>
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
                <span class="section-label">Made for Resellers</span>
                <h2>Buy low, sell higher, remember exactly what each one cost</h2>
                <p class="section-desc">Reselling is the garage sale at 7 AM, the auction lot at noon, the wholesale pallet on Tuesday, and a shelf in the garage that's worth more than it looks. At tax time, the IRS wants clean cost-of-goods numbers. Argo Books tracks what you paid for each item, what you sold it for, and what's still sitting in inventory, so the margin is real and the deductions are real.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('shopping-bag', 28, '', 1.5) ?>
                    </div>
                    <h3>Every item, from purchase to sale</h3>
                    <p>Add an item to inventory at the price you paid: the thrift price, the auction-lot unit cost, the wholesale per-piece. When it sells, log the sale at the price you got. The cost-of-goods number that flows into your taxes is exactly what you spent, not an estimate.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from the thrift store, the auction, or the wholesale lot</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Sourcing, Shipping Supplies, or Vehicle so when the year wraps up, every deductible expense is sitting in a category, not in a shoebox.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('bar-chart', 28, '', 1.5) ?>
                    </div>
                    <h3>See the margin before tax time, not after</h3>
                    <p>Argo Books shows revenue, cost of goods, and the gap between them in real time. You stop running the business on vibes. Slow-selling categories show up as slow. Profitable ones get more shelf space.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline, free tier covers solo resellers</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the garage or at the auction, no monthly subscription climbing every year. The free tier covers most side-hustle and solo full-time resellers forever.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of inventory list. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/inventory-dashboard.svg" alt="The Argo Books inventory list showing items with cost, status, and sale price">
                </div>
                <p class="screenshot-caption">Inventory with each item's cost and sale price tracked.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of receipt-scan UI on a thrift store receipt. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a thrift store receipt">
                </div>
                <p class="screenshot-caption">A sourcing receipt scanned and tagged in seconds.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of main dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing revenue, expenses, and cost of goods">
                </div>
                <p class="screenshot-caption">Your dashboard with revenue, expenses, and margin in real time.</p>
            </div>
        </div>
    </section>

    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>What Argo Books isn't</h2>
                <p class="section-desc">Argo Books does not connect directly to eBay, Amazon, Etsy, or Mercari. It does not pull your marketplace sales in automatically, it does not print shipping labels, and it does not sync inventory across channels. If you sell at high volume and need that automation, tools like A2X, Link My Books, or QuickBooks Commerce are built for it. For solo and side-hustle resellers who can spend ten minutes a week logging sales by hand or importing a marketplace CSV, Argo Books gives you the cost-of-goods, margin, and tax-prep picture without the integration costs. Free desktop app, no monthly fee creeping up, your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most resellers stay on the free tier. Premium adds predictive analytics so you can see which categories are trending up and which are dying, unlimited invoicing, and priority support.</p>
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
                        <h3>Can I track what I paid for each item versus what it sold for?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Add the item to inventory at the price you paid (the thrift price, the auction lot share, the wholesale unit cost), and when it sells, log the sale.</p>
                            <p>The cost-of-goods number for your taxes lines up with what you actually spent.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I record sales across eBay, Amazon, and Facebook Marketplace?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, by tagging each sale with the channel as the customer or category. Argo Books does not pull the sale in automatically from those platforms, so you enter them manually or import a CSV the marketplace gives you.</p>
                            <p>Many resellers do this weekly and treat it like an end-of-week routine.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track mileage to sourcing trips as an expense?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Log mileage as an expense line with the date and the trip distance, tag it Sourcing or Vehicle, and the totals show up on the expense report.</p>
                            <p>At tax time, the mileage deduction is sitting where you put it.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet at a garage sale or auction?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to scan receipts, add inventory, or build a record.</p>
                            <p>Take photos at the auction, enter them later that night at the kitchen table.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books sync with my eBay or Amazon account automatically?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No. Argo Books does not connect directly to eBay, Amazon, Etsy, or Mercari APIs.</p>
                            <p>If you sell at high volume and need automated sync, A2X, Link My Books, or QuickBooks Commerce integrate. For a side hustle or solo reseller, a weekly manual or CSV-import workflow with Argo Books gives you clean cost-of-goods and tax-prep numbers without paying for an integration.</p>
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
                            <p>Yes, forever. The free tier covers all core features including inventory management and <?= $free_invoices ?> invoices per month.</p>
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
                    <h2>Ready to know your margin in real time?</h2>
                    <p>Download Argo Books for free. Add your first item to inventory, scan a sourcing receipt, and log a sale in under ten minutes.</p>
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
            <div id="includeFooter"></div>
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
