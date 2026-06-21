<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_local_wholesalers');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-local-wholesalers';
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
        content="Accounting software for local wholesalers supplying restaurants, retailers, and specialty shops. Inventory, net-30 terms, standing orders, built in. Free desktop app.">
    <meta name="keywords"
        content="accounting software for wholesalers, wholesale distribution bookkeeping, small wholesaler accounting, local distributor software, inventory and invoicing software wholesale">

    <meta property="og:title" content="Argo Books for Local Wholesalers: Inventory, Net-30, and Standing Orders">
    <meta property="og:description"
        content="Inventory, net-30 invoicing, and standing orders for local distributors. Free desktop app for Windows, Mac, and Linux.">
    <meta property="og:url" content="https://argorobots.com/for-local-wholesalers/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Local Wholesalers: Inventory, Net-30, and Standing Orders">
    <meta name="twitter:description"
        content="Inventory, net-30 invoicing, and standing orders for local distributors.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-local-wholesalers/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Local Wholesalers", "item": "https://argorobots.com/for-local-wholesalers/"}
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
                    "name": "Does Argo Books actually track inventory and reorder points?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Inventory Management is a built-in feature, not an add-on. Track stock levels, set reorder points for the SKUs that move, and see what's running low before the regulars call asking."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I set net-30 or net-60 payment terms?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Set the payment terms on the invoice when you send it. The due date is calculated for you, the invoice carries the terms language, and your receivables report shows what's overdue versus what's still inside its window."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I set up a standing order for my recurring accounts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Set the customer, the line items, the quantities, and the frequency once. Argo Books generates the invoice on schedule, every week or every month, so the standing accounts never get skipped."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to log a sale, update stock, or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books connect to EDI or my retail customers' purchase order systems?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not do Electronic Data Interchange, retail-chain purchase order ingestion, or warehouse management with bin locations and pick paths. If you sell into national chains that require EDI, NetSuite, Cin7, or Unleashed are built for that scale. For local wholesalers serving dozens of small accounts, Argo Books fits."
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
    <title>Argo Books for Local Wholesalers: Inventory, Net-30, and Standing Orders</title>

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
            <h1 class="animate-fade-in">Accounting software for local wholesalers</h1>
            <p class="hero-subtitle animate-fade-in">Built for net-30 invoicing, standing orders, and the inventory that has to be on the truck Tuesday morning. Inventory management is included, not an upsell.</p>
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
                <span class="section-label">Made for Local Wholesalers</span>
                <h2>Stock on the truck, money in the bank</h2>
                <p class="section-desc">A wholesale business is the case of stock on the truck, the customer who promised to pay next week, and the receivables report that tells you whether they actually did. Whether you supply restaurants, retail shops, salons, or specialty stores, the work that pays the bills is the standing order delivered on time and the invoice that gets paid by the due date.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('package', 28, '', 1.5) ?>
                    </div>
                    <h3>Inventory and reorder points, built in</h3>
                    <p>Track stock levels for every SKU, set a reorder point on the items that move, and Argo Books flags what's running low before the standing customer calls asking. Receive new stock, log it against the supplier, and the inventory and the books update together.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('calendar', 28, '', 1.5) ?>
                    </div>
                    <h3>Net-30, net-60, and standing orders</h3>
                    <p>Set payment terms when you send the invoice. The due date is calculated, the receivables report shows what's overdue versus what's still inside its window, and standing orders generate themselves on schedule so nothing slips because a regular customer was off your radar this week.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from the manufacturer or the freight bill</h3>
                    <p>Take a photo of the supplier invoice or the freight bill when stock comes in. Argo Books pulls the vendor, date, and amount automatically. Tag it Inventory Purchase, Freight, or Returns so the cost-of-goods picture lines up with what you actually paid.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline, free tier covers small distributors</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the warehouse or on the route, no monthly subscription climbing every year, no website to load when you're packing a truck. The free tier covers most small distributors forever.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of inventory dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/inventory-dashboard.svg" alt="The Argo Books inventory dashboard showing stock levels, reorder points, and movement">
                </div>
                <p class="screenshot-caption">Inventory dashboard with stock levels and reorder points.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of net-30 invoice. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="A net-30 wholesale invoice in Argo Books with line items and terms">
                </div>
                <p class="screenshot-caption">A net-30 wholesale invoice with the line items priced per case.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of main dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing revenue, expenses, and outstanding receivables">
                </div>
                <p class="screenshot-caption">Your dashboard with revenue, expenses, and what's overdue.</p>
            </div>
        </div>
    </section>

    <section class="honest-take-alt">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">An Honest Take</span>
                <h2>What Argo Books isn't</h2>
                <p class="section-desc">Argo Books handles inventory, customer accounts, net-30 invoicing, and standing orders for local-scale wholesale. It is not a warehouse management system, it does not do Electronic Data Interchange with national retail chains, and it does not optimize delivery routes. If you sell into Walmart, Loblaws, or Sysco-scale customers, NetSuite, Cin7, or Unleashed are built for that and Argo Books is not the right fit. It also doesn't do payroll yet. For local distributors with dozens of small accounts, Argo Books is the right size. Free desktop app, inventory built in, books stay simple.</p>
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
                <p class="pricing-strip-intro">Most local distributors stay on the free tier. Premium adds predictive analytics for stock and cashflow planning, unlimited invoicing for larger account loads, and priority support.</p>
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
                        <h3>Does Argo Books actually track inventory and reorder points?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Inventory Management is a built-in feature, not an add-on.</p>
                            <p>Track stock levels, set reorder points for the SKUs that move, and see what's running low before the regulars call asking.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I set net-30 or net-60 payment terms?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Set the payment terms on the invoice when you send it.</p>
                            <p>The due date is calculated for you, the invoice carries the terms language, and your receivables report shows what's overdue versus what's still inside its window.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I set up a standing order for my recurring accounts?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Set the customer, the line items, the quantities, and the frequency once.</p>
                            <p>Argo Books generates the invoice on schedule, every week or every month, so the standing accounts never get skipped.</p>
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
                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to log a sale, update stock, or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books connect to EDI or my retail customers' purchase order systems?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No. Argo Books does not do Electronic Data Interchange (EDI, the digital purchase-order format big chains require from their suppliers), retail-chain purchase order ingestion, or warehouse management with bin locations and pick paths.</p>
                            <p>If you sell into national chains that require EDI, NetSuite, Cin7, or Unleashed are built for that scale. For local wholesalers serving dozens of small accounts, Argo Books fits.</p>
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
                    <h2>Ready to know what's in stock and who owes you what?</h2>
                    <p>Download Argo Books for free. Add your first SKU, set a reorder point, and send a net-30 invoice in under ten minutes.</p>
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
