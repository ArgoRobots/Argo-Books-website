<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
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
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Solo Operators with Inventory: One Person, All the Hats">
    <meta name="twitter:description"
        content="Materials, finished products, and real margins for one-person businesses.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-solo-operators/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Solo Operators" => "/for-solo-operators/"]) ?></script>

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
                    <h1>Accounting software for solo operators with inventory</h1>
                    <p class="fp-hero-sub">Built for one person doing all the jobs: materials, finished goods, customer sales, and the receipts that keep your taxes honest.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#features" class="fp-textlink">See What's Included</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="ai-receipts">
                    <?= argo_feature_demo('ai-receipts') ?>
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
                <span class="section-label">Made for Solo Operators</span>
                <h2>When you're the maker, the packer, the seller, and the bookkeeper</h2>
                <p class="section-desc">A small batch of candles, a tray of soap, a shelf of leather goods, a garage shop turning out one piece at a time. When one person does all the jobs, the books are the job that always gets pushed to Sunday night. Argo Books tracks materials, finished inventory, and sales without making you learn double-entry to do it.</p>
            </div>
            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('package-detail', 20) ?>
                    </div>
                    <h3>Raw materials and finished goods, tracked together</h3>
                    <p>Track wax, fragrance, jars, and wicks as raw materials. Track your candle line as finished products. When you batch a hundred, record the materials used and the count produced. Inventory always reflects what's actually on the shelf, not what was there last spring.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>Snap a receipt from the supplier or the craft store</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Materials, Packaging, Shipping Supplies, or Booth Fees so when tax time comes, every deductible expense is sitting in a category.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('pie-chart', 20) ?>
                    </div>
                    <h3>See your margin per product, not just per month</h3>
                    <p>Argo Books shows the gap between what each product cost you to make and what it sold for. Slow-margin items show up as slow. The bestsellers tell you what to make more of. You stop pricing based on vibes and start pricing based on what actually works.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Works offline at the craft fair, free tier covers solo operators</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed at the market booth, no monthly subscription climbing every year, no website to load when the venue wifi is gone. The free tier covers most solo operators forever.</p>
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
                <p>Argo Books does not connect directly to Shopify, Etsy, Square, or other e-commerce platforms. It does not print shipping labels and it does not calculate sales tax across every state or province automatically. If you sell at high volume online and need that automation built in, Shopify's or Square's built-in accounting may fit better. For solo operators selling at markets, in local boutiques, and through one online shop they update weekly, Argo Books gives you the inventory, margins, and bookkeeping picture without monthly fees stacking up. Free desktop app, your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo operators stay on the free tier. Premium adds predictive analytics so you can see which products are trending up and which are dying, unlimited invoicing, and priority support.</p>
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
                <a href="../for-contractors/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('hard-hat', 20) ?></div>
                    <h3>Contractors</h3>
                    <p>Deposits, mid-job draws, materials and change orders.</p>
                </a>
                <a href="../for-landscapers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('leaf', 20) ?></div>
                    <h3>Landscapers</h3>
                    <p>Seasonal cash flow, materials at cost, recurring maintenance.</p>
                </a>
                <a href="../for-cleaning-companies/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('spray-bottle', 20) ?></div>
                    <h3>Cleaning companies</h3>
                    <p>Recurring invoices, supplies and staff cost per contract.</p>
                </a>
                <a href="../for-repair-shops/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('wrench', 20) ?></div>
                    <h3>Repair shops</h3>
                    <p>Parts, labour and job history against the customer who booked it.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>Can I track raw materials and finished goods separately?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Argo Books has inventory management built in. Track wax, fragrance oils, wicks, and jars as raw materials, and your candle line as finished products.</p>
                            <p>When you batch-make a hundred candles, record the materials used and the finished count, so the inventory shows what's actually on the shelf.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I see margin per product?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Record the unit cost when you produce the item and the sale price when it sells, and Argo Books shows the gap.</p>
                            <p>Slow-margin products show up as slow, profitable ones get more attention. You stop pricing based on what feels right and start pricing based on what works.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I record cash sales from craft fairs and markets?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. Log a batch sale at the end of the market day with the total revenue, quantity per product, and the day's expenses (booth fee, parking, fuel).</p>
                            <p>Inventory drops, revenue lands, and the day's costs are deducted before tax time, not at it.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it work without internet at a craft fair?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>Yes. The desktop app runs natively on your laptop and does not need an internet connection to log a sale, update inventory, or scan a receipt.</p>
                            <p>You only need internet when you actually send an invoice or take a card payment.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does Argo Books sync with my Shopify or Etsy shop?<?php $q = ob_get_clean();
            ob_start(); ?>

                            <p>No. Argo Books does not connect directly to Shopify, Etsy, Square, or other e-commerce platforms. It also does not print shipping labels or calculate sales tax across every jurisdiction automatically.</p>
                            <p>If you sell at high volume online, Shopify or Square's built-in accounting may fit better. For solo operators selling at markets, in local boutiques, and through one online shop they update weekly, Argo Books gives you the books without the monthly fees.</p>
                        
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
                    <h2>Ready to know what each batch actually earned you?</h2>
                    <p>Download Argo Books for free. Track your first raw material, log your first finished batch, and see margin per product in under ten minutes.</p>
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
