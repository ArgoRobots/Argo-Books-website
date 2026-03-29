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
                    "name": "Do I have to pay to use Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No, you don't have to pay. We offer a free version that you can use indefinitely. The free version includes all essential features needed to manage your basic business operations, with unlimited products. If you need more advanced features, consider upgrading to Premium."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What does Premium include?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Premium ($<?php echo number_format($monthlyPrice, 0); ?>/month) unlocks unlimited products, biometric login security, invoices & payments, predictive analytics, and priority support."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I cancel the Premium subscription anytime?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, you can cancel your Premium subscription at any time. Your Premium features will remain active until the end of your current billing period."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is there a yearly plan?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, you can choose to pay $<?php echo number_format($yearlyPrice, 0); ?> CAD/year instead of monthly, saving you $<?php echo number_format($yearlySavings, 0); ?> per year."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Pricing - Argo Books</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

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
                        <div class="card-badge ai-badge">AI-Powered</div>
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

    <!-- Premium Features Showcase -->
    <section class="premium-showcase">
        <div class="container">
            <div class="premium-showcase-header animate-on-scroll">
                <span class="section-label">What's in Premium</span>
                <h2>Features that grow your business</h2>
                <p>Premium unlocks powerful AI tools and unlimited capabilities that save you hours every week.</p>
            </div>
        </div>
    </section>

    <!-- Feature 1: AI Receipt Scanning -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label purple">AI-Powered</span>
                    <h2>Scan receipts with AI — 500 per month</h2>
                    <p>Take a photo of any receipt with your phone or upload from your computer. Our AI automatically extracts vendor, date, amount, and line items with 98% accuracy. No more manual data entry.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works with photos from your phone or scanned images</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Extracts vendor, date, line items, tax, and totals</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic categorization and searchable archive</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>500 scans per month with Premium (100 on Free)</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/receipt-archive.svg" alt="AI receipt scanning automatically extracts vendor, date, line items and totals from any receipt" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Feature 2: Predictive Analytics -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label purple">AI-Powered</span>
                    <h2>Predict your revenue, expenses, and growth</h2>
                    <p>Our AI analyzes your historical data to forecast sales trends, detect seasonal patterns, and surface actionable insights — automatically. No data science skills needed.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Revenue, expense, and profit forecasting</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Seasonal pattern and trend detection</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>AI-generated business insights in plain language</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>88% average forecast accuracy</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/analytics-dashboard.svg" alt="Predictive analytics dashboard showing forecasted revenue, expenses, and profit with trend charts" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Feature 3: Unlimited Invoices & Payments -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Unlimited</span>
                    <h2>Unlimited invoices with online payments</h2>
                    <p>Create professional invoices in seconds, send them by email, and let customers pay online with a single click. Track every invoice from draft to paid — no more chasing payments.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Unlimited invoices per month</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Online payment links via Stripe, PayPal, and Square</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Customizable templates with your company branding</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Real-time status tracking from draft to paid</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/features/invoice-dashboard.svg" alt="Invoice dashboard showing invoice list with status tracking and financial summary" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Feature 4: Biometric Security -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Security</span>
                    <h2>Biometric login keeps your data safe</h2>
                    <p>Unlock Argo Books with your fingerprint or face using Windows Hello. Combined with AES-256 encryption and local-only storage, your financial data stays private and protected.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Windows Hello fingerprint and face authentication</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>AES-256 encryption — the same standard used by banks</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Data stored locally on your computer, not in the cloud</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Quick, secure access without typing passwords</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../resources/images/privacy-local-storage.svg" alt="Your data stays local — encrypted with AES-256 and protected by biometric login" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Premium CTA -->
    <section class="premium-cta-section">
        <div class="container">
            <div class="premium-cta-inner animate-on-scroll">
                <h3>Ready to unlock Premium?</h3>
                <p>Get AI receipt scanning, predictive analytics, unlimited invoices, and biometric security — all for $<?php echo number_format($monthlyPrice, 0); ?>/month.</p>
                <a href="premium/" class="cta-button-link">
                    <span>Get Premium</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Do I have to pay to use Argo Books?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No, you don't have to pay. We offer a free version that you can use indefinitely. The free version includes all essential features needed to manage your basic business operations, with unlimited products. If you need more advanced features, consider upgrading to Premium.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What does Premium include?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Premium ($<?php echo number_format($monthlyPrice, 0); ?>/month) unlocks unlimited products, biometric login security, invoices & payments, predictive analytics, and priority support.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I cancel the Premium subscription anytime?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, you can cancel your Premium subscription at any time. Your Premium features will remain active until the end of your current billing period.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Is there a yearly plan?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, you can choose to pay $<?php echo number_format($yearlyPrice, 0); ?> CAD/year instead of monthly, saving you $<?php echo number_format($yearlySavings, 0); ?> per year.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // FAQ accordion
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

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>
</body>

</html>
