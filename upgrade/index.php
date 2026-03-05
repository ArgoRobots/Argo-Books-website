<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../resources/icons.php';

$pricing = get_pricing_config();
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
        content="Upgrade Argo Books. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, Windows Hello, AI-powered insights, and more.">
    <meta name="keywords"
        content="upgrade argo books, buy full version, unlimited products, business software pricing, finance tracker, premium subscription">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Upgrade - Argo Books">
    <meta property="og:description"
        content="Upgrade Argo Books. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, AI-powered insights, and more.">
    <meta property="og:url" content="https://argorobots.com/upgrade/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Upgrade - Argo Books">
    <meta name="twitter:description"
        content="Upgrade Argo Books. Subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, AI-powered insights, and more.">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/upgrade/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/A-logo.ico">
    <title>Upgrade - Argo Books</title>

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
            <h1>Upgrade Your Experience</h1>
            <p class="hero-subtitle">Unlock the full power of Argo Books</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="container">
            <div class="pricing-cards-wrapper">
                <!-- Free Card -->
                <a href="../downloads/" class="pricing-card-link">
                    <div class="upgrade-pricing-card free-card">
                        <h2>Free</h2>
                        <div class="card-price">
                            <span class="currency">$</span>
                            <span class="amount">0</span>
                            <span class="period">forever</span>
                        </div>
                        <p class="price-note">No credit card required</p>

                        <ul class="card-features">
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Up to 10 products</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Expense & revenue tracking</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Financial reports</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Cross-platform desktop app</span>
                            </li>
                        </ul>

                        <div class="card-cta">
                            <span class="cta-button free-cta">Download Free</span>
                        </div>
                    </div>
                </a>

                <!-- Premium Subscription Card -->
                <a href="premium/" class="pricing-card-link">
                    <div class="upgrade-pricing-card ai-card">
                        <div class="card-badge ai-badge">AI-Powered</div>
                        <h2>Premium</h2>
                        <div class="card-price">
                            <span class="currency">$</span>
                            <span class="amount"><?php echo number_format($monthlyPrice, 0); ?></span>
                            <span class="period">CAD/month</span>
                        </div>
                        <p class="price-note">or $<?php echo number_format($yearlyPrice, 0); ?> CAD/year (save $<?php echo number_format($yearlySavings, 0); ?>)</p>

                        <ul class="card-features">
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Unlimited products</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Biometric login security</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Invoices & payments</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>AI receipt scanning <span>(500/month)</span></span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>predictive analytics</span>
                            </li>
                            <li>
                                <?= svg_icon('check-pricing') ?>
                                <span>Priority support</span>
                            </li>
                        </ul>

                        <div class="card-cta">
                            <span class="cta-button ai-cta">Get Premium</span>
                        </div>
                    </div>
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
                            <p>No, you don't have to pay. We offer a free version that you can use indefinitely. The free version includes all essential features needed to manage your basic business operations, with a limit of up to 10 products. If you need more, consider upgrading to Premium.</p>
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
                            <p>Premium ($<?php echo number_format($monthlyPrice, 0); ?>/month) unlocks unlimited products, biometric login security, invoices & payments, AI-powered features like receipt scanning, predictive analytics, and priority support.</p>
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

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>
</body>

</html>
