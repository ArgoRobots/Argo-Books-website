<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_contractors');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-contractors';
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
        content="Accounting software for general contractors and tradespeople. Built for progress billing, materials, and change orders. Free desktop app for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for contractors, contractor bookkeeping software, construction invoicing software, contractor accounting app, free accounting software contractor">

    <meta property="og:title" content="Argo Books for Contractors: Bookkeeping Built for Progress Billing">
    <meta property="og:description"
        content="Deposits, mid-job draws, materials, and change orders, without the bookkeeping headache. Free desktop app for contractors.">
    <meta property="og:url" content="https://argorobots.com/for-contractors/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Contractors&subtitle=Bookkeeping+built+for+progress+billing%3A+deposits%2C+draws%2C+materials%2C+and+change+orders&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Contractors: Bookkeeping Built for Progress Billing">
    <meta name="twitter:description"
        content="Deposits, mid-job draws, materials, and change orders, without the bookkeeping headache.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books+for+Contractors&subtitle=Bookkeeping+built+for+progress+billing%3A+deposits%2C+draws%2C+materials%2C+and+change+orders&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-contractors/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "For Contractors", "item": "https://argorobots.com/for-contractors/"}
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
                    "name": "Can I bill a deposit, a mid-job draw, and a final balance?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Most contractors send three invoices on a multi-week job: a deposit invoice before work begins, a draw invoice at a milestone like framing or rough-in, and a final invoice when the work is signed off. Argo Books tracks what's been paid on each so the final balance lines up with what the customer still owes."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I bill change orders without re-issuing the original invoice?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Add each change order as its own line item on the next progress invoice, or send a separate change-order invoice. Keeping changes on their own lines makes it easy for the customer to see exactly what they signed off on versus the original scope."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I track materials and labor separately?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. List materials and labor on separate lines of the invoice, or roll materials into a single marked-up line if that's how you priced the bid. On the expense side, scan the supply-house receipt and tag it Materials, Equipment, or Subcontractor so the report later actually means something."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it work without internet at the job site?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice. You only need internet when you actually send the invoice or take a payment."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does Argo Books do job costing per project?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not the way QuickBooks Contractor or a dedicated job-costing tool does. Argo Books tracks expenses by category and revenue by customer and invoice, which covers most solo contractors and small crews. If you need a true per-project P&L across labor, materials, subs, and overhead, a job-costing tool is a better fit. Many contractors run a simpler bookkeeping tool alongside their estimating or scheduling software."
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
    <title>Argo Books for Contractors: Bookkeeping Built for Progress Billing</title>

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
            <h1 class="animate-fade-in">Accounting software for contractors</h1>
            <p class="hero-subtitle animate-fade-in">Built for progress billing: deposits, mid-job draws, materials, and change orders, without the bookkeeping headache.</p>
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
                <span class="section-label">Made for Contractors</span>
                <h2>Built for the way contractors actually get paid</h2>
                <p class="section-desc">A contractor invoice isn't one number on one piece of paper. It's a deposit before any work starts, a draw when the framing is up or the rough-in is done, change orders the homeowner asked for after the bid, materials at cost or with a markup, and a final balance with the deposit and draws already credited. Argo Books handles the books so you can stay on the tools.</p>
            </div>
        </div>
    </section>

    <section id="features" class="feature-blocks">
        <div class="container">
            <div class="feature-block-grid">
                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon">
                        <?= svg_icon('clipboard-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Bill a deposit, a mid-job draw, and a final balance</h3>
                    <p>Send a deposit invoice before the first day, a draw invoice when framing or rough-in is signed off, and a final balance with the deposit and draws already credited. Argo Books tracks what's been paid on each, so the closing balance is exactly what's still owed.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon green">
                        <?= svg_icon('receipt-scan-detail', 28, '', 1.5) ?>
                    </div>
                    <h3>Snap a receipt from Home Depot, the lumber yard, or the supply house</h3>
                    <p>Take a photo and Argo Books pulls the vendor, date, and amount automatically. Tag it Materials, Subcontractor, Equipment Rental, or Permit so when the customer asks for an itemized statement, you can answer in two minutes instead of two hours.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon purple">
                        <?= svg_icon('shield-check', 28, '', 1.5) ?>
                    </div>
                    <h3>Works without internet, your data stays on your computer</h3>
                    <p>Argo Books runs natively on Windows, Mac, and Linux. No internet needed at the job trailer, no monthly subscription climbing every year, no website to log into when the cell signal cuts out. The free tier covers most solo contractors and small crews forever.</p>
                </div>

                <div class="feature-block animate-on-scroll">
                    <div class="feature-block-icon amber">
                        <?= svg_icon('bolt', 28, '', 1.5) ?>
                    </div>
                    <h3>Send the final invoice the day you wrap</h3>
                    <p>Walk through with the customer, open Argo Books, and send the final invoice before you pack the truck. Customers can pay through Stripe or Square, so the balance can clear before the deposit on the next job needs to land.</p>
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

            <!-- PLACEHOLDER: replace with fresh capture of progress-billing invoice showing deposit + draws + final balance. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/features/invoice-preview.svg" alt="A contractor invoice showing a deposit, mid-job draw, and final balance">
                </div>
                <p class="screenshot-caption">A contractor invoice with the deposit and draw already credited.</p>
            </div>

            <!-- PLACEHOLDER: replace with fresh capture of receipt-scan UI on a supply-house receipt. -->
            <div class="screenshot-item animate-on-scroll">
                <div class="screenshot-frame">
                    <img src="../resources/images/ai-receipt-scanner.webp" alt="The Argo Books receipt scanner extracting fields from a supply-house receipt">
                </div>
                <p class="screenshot-caption">A receipt from the supply house scanned and tagged in seconds.</p>
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
                <p class="section-desc">Argo Books is bookkeeping software, not construction-management software. It does not do job costing per project, crew scheduling, or bid and estimating. If you're trying to replace Buildertrend, CoConstruct, or QuickBooks Contractor for those, run them side by side: those for the project, Argo Books for your books. It also doesn't do payroll yet. If those are dealbreakers, that's fair. If they're not, the desktop app is free, the books stay simple, and your data stays on your computer.</p>
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
                <p class="pricing-strip-intro">Most solo contractors and small crews stay on the free tier. Premium adds predictive analytics for cashflow planning between jobs, unlimited invoicing, and priority support.</p>
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
                        <h3>Can I bill a deposit, a mid-job draw, and a final balance?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Most contractors send three invoices on a multi-week job: a deposit invoice before work begins, a draw invoice at a milestone like framing or rough-in, and a final invoice when the work is signed off.</p>
                            <p>Argo Books tracks what's been paid on each so the final balance lines up with what the customer still owes.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I bill change orders without re-issuing the original invoice?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. Add each change order as its own line item on the next progress invoice, or send a separate change-order invoice.</p>
                            <p>Keeping changes on their own lines makes it easy for the customer to see exactly what they signed off on versus the original scope.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I track materials and labor separately?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. List materials and labor on separate lines of the invoice, or roll materials into a single marked-up line if that's how you priced the bid.</p>
                            <p>On the expense side, scan the supply-house receipt and tag it Materials, Equipment, or Subcontractor so the report later actually means something.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does it work without internet at the job site?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. The desktop app runs natively on your computer and does not need an internet connection to record expenses or build an invoice.</p>
                            <p>You only need internet when you actually send the invoice or take a payment.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Does Argo Books do job costing per project?</h3>
                        <div class="faq-icon"><?= svg_icon('chevron-down') ?></div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Not the way QuickBooks Contractor or a dedicated job-costing tool does. Argo Books tracks expenses by category and revenue by customer and invoice, which covers most solo contractors and small crews.</p>
                            <p>If you need a true per-project P&L across labor, materials, subs, and overhead, a job-costing tool is a better fit. Many contractors run a simpler bookkeeping tool alongside their estimating or scheduling software.</p>
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
                    <h2>Ready to clean up the books before the next bid?</h2>
                    <p>Download Argo Books for free. Set up your first customer, scan a supply-house receipt, and send a progress invoice in under ten minutes.</p>
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
