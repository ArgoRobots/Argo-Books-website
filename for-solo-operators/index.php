<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_solo_operators');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-solo-operators';
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
        content="Accounting software for solo operators with inventory: candle makers, soap makers, jewelers, garage workshops, single-person retail. Track materials, finished goods, and margins. Free desktop app.">
    <meta name="keywords"
        content="accounting software for solo business with inventory, small product maker bookkeeping, craft business accounting, etsy maker accounting, single owner inventory software">

    <meta property="og:title" content="Argo Books for Solo Operators with Inventory: One Person, All the Hats">
    <meta property="og:description"
        content="Materials, finished products, and real margins for one-person businesses. Free desktop app.">
    <meta property="og:url" content="https://argorobots.com/for-solo-operators/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Solo+Operators&subtitle=Materials%2C+finished+products%2C+and+real+margins+for+one-person+businesses+with+inventory&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Solo Operators with Inventory: One Person, All the Hats">
    <meta name="twitter:description"
        content="Materials, finished products, and real margins for one-person businesses.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Solo+Operators&subtitle=Materials%2C+finished+products%2C+and+real+margins+for+one-person+businesses+with+inventory&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-solo-operators/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Solo Operators", "item": "https://argorobots.com/for-solo-operators/"}
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
                    "name": "Can I track raw materials and finished goods separately?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books has inventory management built in. Track wax, fragrance oils, wicks, and jars as raw materials, and your candle line as finished products. When you batch-make a hundred candles, record the materials used and the finished count, so the inventory shows what's actually on the shelf."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I see margin per product?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Record the unit cost when you produce the item and the sale price when it sells, and Argo Books shows the gap. Slow-margin products show up as slow, profitable ones get more attention. You stop pricing based on what feels right and start pricing based on what works."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I record cash sales from craft fairs and markets?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Log a batch sale at the end of the market day with the total revenue, quantity per product, and the day's expenses (booth fee, parking, fuel). Inventory drops, revenue lands, and the day's costs are deducted before tax time, not at it."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet at a craft fair?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your laptop and does not need an internet connection to log a sale, update inventory, or scan a receipt. You only need internet when you actually send an invoice or take a card payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books sync with my Shopify or Etsy shop?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not connect directly to Shopify, Etsy, Square, or other e-commerce platforms. It also does not print shipping labels or calculate sales tax across every jurisdiction automatically. If you sell at high volume online, Shopify or Square's built-in accounting may fit better. For solo operators selling at markets, in local boutiques, and through one online shop they update weekly, Argo Books gives you the books without the monthly fees."
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
    <title>Argo Books for Solo Operators with Inventory: One Person, All the Hats</title>

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
            <h1 class="animate-fade-in">Accounting software for solo operators with inventory</h1>
            <p class="hero-subtitle animate-fade-in">Built for one person doing all the jobs: materials, finished goods, customer sales, and the receipts that keep your taxes honest.</p>
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
                <span class="section-label">Made for Solo Operators</span>
                <h2>When you're the maker, the packer, the seller, and the bookkeeper</h2>
                <p class="section-desc">A small batch of candles, a tray of soap, a shelf of leather goods, a garage shop turning out one piece at a time. When one person does all the jobs, the books are the job that always gets pushed to Sunday night. Argo Books tracks materials, finished inventory, and sales without making you learn double-entry to do it.</p>
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
                    <h3>Raw materials and finished goods, tracked together</h3>
                    <p>Track wax, fragrance, jars, and wicks as raw materials. Track your candle line as finished products. When you batch a hundred, record the materials used and the count produced. Inventory always reflects what's actually on the shelf, not what was there last spring.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from the supplier or the craft store</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Materials, Packaging, Shipping Supplies, or Booth Fees so when tax time comes, every deductible expense is sitting in a category.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('pie-chart', 28, '', 1.5) ?>
                    </div>
                    <h3>See your margin per product, not just per month</h3>
                    <p>Argo Books shows the gap between what each product cost you to make and what it sold for. Slow-margin items show up as slow. The bestsellers tell you what to make more of. You stop pricing based on vibes and start pricing based on what actually works.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline at the craft fair, free tier covers solo operators</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed at the market booth, no monthly subscription climbing every year, no website to load when the venue wifi is gone. The free tier covers most solo operators forever.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of inventory showing materials + finished goods. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/inventory-dashboard.svg" alt="The Argo Books inventory showing raw materials and finished products separately">
                </div>
                <p class="screenshot-caption">Inventory with raw materials and finished goods side by side.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of receipt-scan UI on a craft-supply receipt. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a craft-supply receipt">
                </div>
                <p class="screenshot-caption">A supplier receipt scanned and categorized in seconds.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of main dashboard. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/dashboard.webp" alt="The Argo Books dashboard showing revenue, expenses, and margin">
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
                <p class="section-desc">Argo Books does not connect directly to Shopify, Etsy, Square, or other e-commerce platforms. It does not print shipping labels and it does not calculate sales tax across every state or province automatically. If you sell at high volume online and need that automation built in, Shopify's or Square's built-in accounting may fit better. For solo operators selling at markets, in local boutiques, and through one online shop they update weekly, Argo Books gives you the inventory, margins, and bookkeeping picture without monthly fees stacking up. Free desktop app, your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo operators stay on the free tier. Premium adds predictive analytics so you can see which products are trending up and which are dying, unlimited invoicing, and priority support.</p>
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
                        <h3>Can I track raw materials and finished goods separately?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Argo Books has inventory management built in. Track wax, fragrance oils, wicks, and jars as raw materials, and your candle line as finished products.</p>
                            <p>When you batch-make a hundred candles, record the materials used and the finished count, so the inventory shows what's actually on the shelf.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I see margin per product?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Record the unit cost when you produce the item and the sale price when it sells, and Argo Books shows the gap.</p>
                            <p>Slow-margin products show up as slow, profitable ones get more attention. You stop pricing based on what feels right and start pricing based on what works.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I record cash sales from craft fairs and markets?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Log a batch sale at the end of the market day with the total revenue, quantity per product, and the day's expenses (booth fee, parking, fuel).</p>
                            <p>Inventory drops, revenue lands, and the day's costs are deducted before tax time, not at it.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet at a craft fair?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to log a sale, update inventory, or scan a receipt.</p>
                            <p>You only need internet when you actually send an invoice or take a card payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books sync with my Shopify or Etsy shop?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No. Argo Books does not connect directly to Shopify, Etsy, Square, or other e-commerce platforms. It also does not print shipping labels or calculate sales tax across every jurisdiction automatically.</p>
                            <p>If you sell at high volume online, Shopify or Square's built-in accounting may fit better. For solo operators selling at markets, in local boutiques, and through one online shop they update weekly, Argo Books gives you the books without the monthly fees.</p>
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
                    <h2>Ready to know what each batch actually earned you?</h2>
                    <p>Download Argo Books for free. Track your first raw material, log your first finished batch, and see margin per product in under ten minutes.</p>
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
