<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_cleaning_companies');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-cleaning-companies';
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
        content="Accounting software for residential and commercial cleaning companies. Built for recurring invoices, supply costs, and same-day billing. Free desktop app for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for cleaning companies, cleaning business bookkeeping, janitorial accounting software, residential cleaning invoicing, recurring invoice software cleaning">

    <meta property="og:title" content="Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers">
    <meta property="og:description"
        content="Recurring invoices, marked-up supplies, and same-day billing, without the bookkeeping headache. Free desktop app.">
    <meta property="og:url" content="https://argorobots.com/for-cleaning-companies/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Cleaning+Companies&subtitle=Recurring+invoices%2C+supply+costs%2C+and+same-day+billing+for+residential+and+commercial+cleaners&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers">
    <meta name="twitter:description"
        content="Recurring invoices, marked-up supplies, and same-day billing, without the bookkeeping headache.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Cleaning+Companies&subtitle=Recurring+invoices%2C+supply+costs%2C+and+same-day+billing+for+residential+and+commercial+cleaners&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-cleaning-companies/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Cleaning Companies", "item": "https://argorobots.com/for-cleaning-companies/"}
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
                    "name": "Can I set up a recurring invoice for the same client every week or month?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Set the client, the amount, and the frequency once, and Argo Books generates the invoice on schedule. The client gets the same clean invoice every time, you get a payment record every time, and you stop forgetting to bill the recurring residential."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I bill supplies as a line on the invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. List supplies as their own line, either at cost or with a small markup for sourcing and handling. Many commercial cleaners build supplies into the base rate and never itemize. Residential one-offs sometimes itemize for transparency. Both work in Argo Books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I see which clients or properties are most profitable?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You can see total revenue per customer, and total spend per category. Argo Books does not run a per-property profit-and-loss the way a dedicated job-costing tool does, so a ten-house route gets one combined view. If knowing the margin on a single property is critical, a job-costing tool is a better fit."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to log a cleaning, scan a receipt, or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books schedule cleanings or send arrival texts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books does not run a scheduling calendar, dispatch crews, or send 'on the way' texts. Jobber, ZenMaid, and Maidily are built for that side. Run them alongside Argo Books: those for the schedule, Argo Books for the books."
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
    <title>Argo Books for Cleaning Companies: Recurring Invoices and Real Numbers</title>

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
            <h1 class="animate-fade-in">Accounting software for cleaning companies</h1>
            <p class="hero-subtitle animate-fade-in">Built for recurring invoices, supply costs, and the difference between a profitable client and one that's quietly losing you money.</p>
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
                <span class="section-label">Made for Cleaning Companies</span>
                <h2>Recurring clients, real numbers, no spreadsheet</h2>
                <p class="section-desc">A cleaning business is one client at 9 AM, three more in the afternoon, the same houses next week, and a stack of supply receipts on the dashboard of the car. Residential or commercial, solo or with a crew, the work that pays the bills is the recurring contract that's billed on time, every time. Argo Books handles the books so you can keep cleaning.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('refresh', 28, '', 1.5) ?>
                    </div>
                    <h3>Recurring invoices for the same client every week or month</h3>
                    <p>Set the client, the amount, and the frequency once. Argo Books builds the invoice on schedule, every week or every month, with the same line items and the same total. You stop forgetting to bill the residential routes, and the commercial accounts come in clean every cycle.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from Costco, the chemical supplier, or the equipment store</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Chemicals, Paper Goods, Equipment, or Vehicle so when you raise your rates next year, you can show the customer where the cost actually went up.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('send', 28, '', 1.5) ?>
                    </div>
                    <h3>Send the one-time deep clean invoice from the driveway</h3>
                    <p>Finish the move-out clean, sit in the truck for two minutes, open Argo Books, hit send. Customers can pay through Stripe or Square, and the deposit on next week's recurring is already on autopilot.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works offline, free tier covers solo cleaners and small crews</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed in the truck or at the office, no monthly subscription climbing every year, no website to load when you're trying to close out the day. The free tier covers most cleaning businesses forever.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of recurring invoice setup. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="A recurring cleaning invoice in Argo Books with weekly frequency set">
                </div>
                <p class="screenshot-caption">A recurring weekly invoice for a residential client.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of receipt-scan UI on a supply receipt. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a Costco supply receipt">
                </div>
                <p class="screenshot-caption">A supply receipt from Costco scanned and tagged.</p>
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
                <p class="section-desc">Argo Books is bookkeeping software, not field-service software. It does not run a cleaning calendar, dispatch crews to addresses, send "on the way" texts to clients, or run a per-property profit-and-loss. If you need Jobber, ZenMaid, or Maidily for scheduling and crew routing, run them alongside Argo Books: those for the schedule, Argo Books for the books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the recurring invoices run themselves, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most cleaning businesses stay on the free tier. Premium adds predictive analytics for slow-month planning, unlimited invoicing for larger commercial routes, and priority support.</p>
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
                        <h3>Can I set up a recurring invoice for the same client every week or month?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Set the client, the amount, and the frequency once, and Argo Books generates the invoice on schedule.</p>
                            <p>The client gets the same clean invoice every time, you get a payment record every time, and you stop forgetting to bill the recurring residential.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I bill supplies as a line on the invoice?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. List supplies as their own line, either at cost or with a small markup for sourcing and handling.</p>
                            <p>Many commercial cleaners build supplies into the base rate and never itemize. Residential one-offs sometimes itemize for transparency. Both work in Argo Books.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I see which clients or properties are most profitable?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>You can see total revenue per customer, and total spend per category. Argo Books does not run a per-property profit-and-loss the way a dedicated job-costing tool does, so a ten-house route gets one combined view.</p>
                            <p>If knowing the margin on a single property is critical, a job-costing tool is a better fit.</p>
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
                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to log a cleaning, scan a receipt, or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books schedule cleanings or send arrival texts?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No. Argo Books does not run a scheduling calendar, dispatch crews, or send "on the way" texts.</p>
                            <p>Jobber, ZenMaid, and Maidily are built for that side. Run them alongside Argo Books: those for the schedule, Argo Books for the books.</p>
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
                    <h2>Ready to put the recurring routes on autopilot?</h2>
                    <p>Download Argo Books for free. Set up your first client, build a recurring weekly invoice, and scan a supply receipt in under ten minutes.</p>
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
