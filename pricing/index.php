<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../resources/icons.php';

$pricing = get_pricing_config();
$plans = get_plan_features();
$monthlyPrice = $pricing['premium_monthly_price'];
$yearlyPrice = $pricing['premium_yearly_price'];
$yearlySavings = ($monthlyPrice * 12) - $yearlyPrice;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Argo Books Pricing. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, Windows Hello, AI-powered insights, and more.">
    <meta name="keywords"
        content="argo books pricing, buy full version, unlimited products, business software pricing, finance tracker, premium subscription">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Pricing - Argo Books">
    <meta property="og:description"
        content="Argo Books Pricing. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, AI-powered insights, and more.">
    <meta property="og:url" content="https://argorobots.com/pricing/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Pricing - Argo Books">
    <meta name="twitter:description"
        content="Argo Books Pricing. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, AI-powered insights, and more.">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/pricing/">

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How does the Free plan work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books is free to download and use — no credit card, no trial period, no strings attached. The Free plan includes all core features: unlimited products, unlimited transactions, real-time analytics, receipt management, 25 invoices per month, 5 AI receipt scans per month, and 100 AI spreadsheet imports per month. You can use it for as long as you like."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What does Premium unlock?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Premium removes all limits and adds powerful tools to help your business scale. You get unlimited invoicing, 500 AI receipt scans per month, predictive analytics that forecast trends in your data, biometric login security via Windows Hello, and priority customer support. Premium is available at $<?php echo number_format($monthlyPrice, 0); ?> CAD/month or $<?php echo number_format($yearlyPrice, 0); ?> CAD/year — the annual plan saves you $<?php echo number_format($yearlySavings, 0); ?> per year."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I cancel or change my plan anytime?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. You can cancel your Premium subscription at any time from your customer portal — no phone calls, no hoops to jump through. Your Premium features stay active until the end of your current billing period. You can also switch between monthly and yearly billing whenever it suits you."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need to install anything?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes — Argo Books is a desktop application, which is what makes it fast and private. Download the installer for Windows, macOS, or Linux, and you're up and running in under a minute. Because your data lives on your computer, Argo Books works offline too. You only need an internet connection for AI-powered features like receipt scanning and spreadsheet import."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is my payment information secure?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Absolutely. All payments are processed through Stripe, PayPal, or Square — we never see or store your card details. These are PCI-compliant payment processors trusted by millions of businesses worldwide. Your Argo Books business data is also encrypted locally with AES-256-GCM, the same standard used by banks."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What if I need help getting started?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "We've got you covered. All users have access to our documentation and community forum. Premium subscribers get priority support with faster response times. You can also reach us through our contact page — we're a small team and we personally read every message."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Pricing - Argo Books</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../features/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
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
            <div class="hero-badge">
                <?= svg_icon('star', 16) ?>
                <span>Pricing</span>
            </div>
            <h1>Choose Your Plan</h1>
            <p class="hero-subtitle">Unlock the full power of Argo Books</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="container">
            <div class="pricing-cards-wrapper">
                <!-- Free Card -->
                <a href="../downloads/" class="pricing-card-link">
                    <div class="pricing-card free-card">
                        <h2>Free</h2>
                        <div class="card-price">
                            <span class="currency">$</span>
                            <span class="amount">0</span>
                            <span class="period">forever</span>
                        </div>
                        <p class="price-note">No credit card required</p>

                        <ul class="card-features">
                            <?php foreach ($plans['free']['features'] as $feature): ?>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span><?= render_feature_label($feature) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="card-cta">
                            <span class="cta-button free-cta">Download for Free</span>
                        </div>
                    </div>
                </a>

                <!-- Premium Subscription Card -->
                <a href="premium/" class="pricing-card-link">
                    <div class="pricing-card ai-card">
                        <div class="card-badge ai-badge">Most popular</div>
                        <h2>Premium</h2>
                        <div class="card-price">
                            <span class="currency">$</span>
                            <span class="amount"><?php echo number_format($monthlyPrice, 0); ?></span>
                            <span class="period">CAD/month</span>
                        </div>
                        <p class="price-note">or $<?php echo number_format($yearlyPrice, 0); ?> CAD/year (save $<?php echo number_format($yearlySavings, 0); ?>)</p>

                        <ul class="card-features">
                            <?php foreach ($plans['premium']['features'] as $feature): ?>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span><?= render_feature_label($feature) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="card-cta">
                            <span class="cta-button ai-cta">Get Premium</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- =============================================
         PREMIUM FEATURES INTRO
         ============================================= -->
    <section class="premium-intro">
        <div class="container">
            <div class="premium-intro-inner animate-on-scroll">
                <div class="premium-intro-badge">
                    <?= svg_icon('star', 16) ?>
                    <span>Premium Features</span>
                </div>
                <h2>Everything you unlock with Premium</h2>
                <p>The complete toolkit for running your business — AI-powered, unlimited, and built to save you time.</p>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL: AI Receipt Scanning
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">AI-Powered</span>
                    <h2>Scan receipts in seconds, not minutes</h2>
                    <p>Point your camera at any receipt and Argo Books extracts the supplier, date, and total automatically — no typing required. Receipts are categorized and attached to your expense records instantly.</p>
                    <ul class="feature-checklist">
                        <li><?= svg_icon('check', 20) ?><span>500 receipt scans per month</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Auto-categorized by expense type</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Attached directly to your transaction records</span></li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/receipt-archive.svg" alt="AI receipt scanning archive showing scanned receipts organized by category" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL: Predictive Analytics
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Smart Insights</span>
                    <h2>See where your revenue is heading</h2>
                    <p>Argo Books analyzes your historical data to surface trends and forecast revenue. Built-in charts show where your business is headed — no spreadsheets or external tools needed.</p>
                    <ul class="feature-checklist">
                        <li><?= svg_icon('check', 20) ?><span>Revenue and expense forecasting</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Seasonal trend detection</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Profit margin and anomaly alerts</span></li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/analytics-dashboard.svg" alt="Predictive analytics dashboard showing revenue forecasts and trend charts" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         STATS BANNER
         ============================================= -->
    <section class="highlight-banner">
        <div class="container">
            <div class="highlight-grid animate-on-scroll">
                <div class="highlight-item">
                    <h3>500</h3>
                    <p>AI receipt scans per month</p>
                </div>
                <div class="highlight-item">
                    <h3>Unlimited</h3>
                    <p>Invoices you can send</p>
                </div>
                <div class="highlight-item">
                    <h3>Online</h3>
                    <p>Payments via Stripe, PayPal &amp; Square</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL: Invoices & Payments
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Get Paid Faster</span>
                    <h2>Send unlimited invoices and accept online payments</h2>
                    <p>The free plan caps invoices at 25 per month. Premium removes that limit entirely — and adds online payment links so customers can pay directly from the invoice by credit card.</p>
                    <ul class="feature-checklist">
                        <li><?= svg_icon('check', 20) ?><span>Unlimited invoices with no monthly cap</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Online payment links via Stripe, PayPal &amp; Square</span></li>
                        <li><?= svg_icon('check', 20) ?><span>Real-time status tracking from draft to paid</span></li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/invoice-payment.svg" alt="Invoice payment page showing a secure online payment form with invoice details" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         BENEFITS GRID
         ============================================= -->
    <section class="benefits-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">What You Get</span>
                <h2 class="section-title">Six reasons to go Premium</h2>
                <p class="section-desc">Every Premium feature is designed to save you time, reduce manual work, and give you a clearer picture of your business.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('receipt-scan', 22) ?>
                    </div>
                    <h3>AI Receipt Scanning</h3>
                    <p>Snap a photo and let AI handle the data entry. Suppliers, dates, and totals are extracted and categorized automatically.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>Historical trends become forward-looking forecasts. Know what's coming before your next bank statement arrives.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Unlimited Invoices</h3>
                    <p>Send as many invoices as your business demands and accept online payments directly — no monthly cap, ever.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Biometric Login</h3>
                    <p>Log in with your face or fingerprint via Windows Hello. No passwords to remember, forget, or compromise.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('message-circle', 22) ?>
                    </div>
                    <h3>Priority Support</h3>
                    <p>Premium subscribers get to the front of the queue — real help, faster, when something needs attention.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Higher AI Limits</h3>
                    <p>500 receipt scans and expanded AI headroom each month — built to keep up as your business grows.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Ready to unlock Premium?</h3>
                <p>Cancel anytime. No lock-in.</p>
                <div class="inline-cta-buttons">
                    <a href="premium/" class="btn-cta btn-cta-primary">
                        <span>Get Premium</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../downloads/" class="btn-cta btn-cta-outline">
                        <span>Download Free</span>
                    </a>
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
                        <h3>How does the Free plan work?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Argo Books is free to download and use — no credit card, no trial period, no strings attached. The Free plan includes all core features: unlimited products, unlimited transactions, real-time analytics, receipt management, 25 invoices per month, 5 AI receipt scans per month, and 100 AI spreadsheet imports per month.</p>
                            <p>You can use it for as long as you like. When your business needs more, upgrading to Premium takes just a few clicks.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What does Premium unlock?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Premium removes all limits and adds powerful tools to help your business scale. You get unlimited invoicing, 500 AI receipt scans per month, predictive analytics that forecast trends in your data, biometric login security via Windows Hello, and priority customer support.</p>
                            <p>Premium is available at <strong>$<?php echo number_format($monthlyPrice, 0); ?> CAD/month</strong> or <strong>$<?php echo number_format($yearlyPrice, 0); ?> CAD/year</strong> — the annual plan saves you $<?php echo number_format($yearlySavings, 0); ?> per year.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I cancel or change my plan anytime?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes. You can cancel your Premium subscription at any time from your <a href="../portal/">customer portal</a> — no phone calls, no hoops to jump through. Your Premium features stay active until the end of your current billing period.</p>
                            <p>You can also switch between monthly and yearly billing whenever it suits you. If you believe you're entitled to a refund, <a href="../contact.php">get in touch</a> and we'll review your request. <a href="../legal/refund.php">View full refund policy</a></p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do I need to install anything?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes — Argo Books is a desktop application, which is what makes it fast and private. Download the installer for Windows, macOS, or Linux, and you're up and running in under a minute. Because your data lives on your computer, Argo Books works offline too.</p>
                            <p>You only need an internet connection for AI-powered features like receipt scanning and spreadsheet import. <a href="../downloads/">Download Argo Books</a></p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is my payment information secure?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Absolutely. All payments are processed through Stripe, PayPal, or Square — we never see or store your card details. These are PCI-compliant payment processors trusted by millions of businesses worldwide.</p>
                            <p>Your Argo Books business data is also encrypted locally with AES-256-GCM, the same standard used by banks.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What if I need help getting started?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>We've got you covered. All users have access to our <a href="../documentation/">documentation</a> and <a href="../community/">community forum</a>. Premium subscribers get priority support with faster response times.</p>
                            <p>You can also reach us through our <a href="../contact.php">contact page</a> — we're a small team and we personally read every message.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                }
            });
        }, observerOptions);
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        const faqItems = document.querySelectorAll('.faq-item');
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            question.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                faqItems.forEach(otherItem => {
                    otherItem.classList.remove('active');
                });
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });
    });
    </script>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>The complete toolkit for $<?php echo number_format($monthlyPrice, 0); ?>/month</h2>
                    <p>Everything in Free, plus AI receipt scanning, predictive analytics, unlimited invoices, and biometric login.</p>
                    <div class="cta-buttons">
                        <a href="premium/" class="btn-cta btn-cta-primary">
                            <span>Get Premium</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="../downloads/" class="btn-cta btn-cta-ghost">
                            <span>Download Free</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div id="includeFooter"></div>
        </footer>
    </div>
</body>

</html>
