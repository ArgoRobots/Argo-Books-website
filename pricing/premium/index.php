<?php
session_start();
require_once __DIR__ . '/../../community/users/user_functions.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../resources/icons.php';

$pricing = get_pricing_config();
$monthlyPrice = $pricing['premium_monthly_price'];
$yearlyPrice = $pricing['premium_yearly_price'];
$yearlySavings = ($monthlyPrice * 12) - $yearlyPrice;

// Require login to access Premium subscription page
require_login('pricing/premium/');

$user_id = $_SESSION['user_id'];
$user = get_user($user_id);

// Check if user already has an active subscription
$existing_subscription = get_user_premium_subscription($user_id);
if ($existing_subscription && in_array($existing_subscription['status'], ['active', 'cancelled'])) {
    // User already has a subscription (active or cancelled but not expired)
    if ($existing_subscription['status'] === 'active' ||
        ($existing_subscription['status'] === 'cancelled' && strtotime($existing_subscription['end_date']) > time())) {
        header('Location: ../../community/users/subscription.php');
        exit;
    }
}
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
        content="Subscribe to Argo Books Premium. Get invoices & payments, AI receipt scanning, and predictive analytics. $<?php echo number_format($monthlyPrice, 0); ?>/month or $<?php echo number_format($yearlyPrice, 0); ?>/year.">
    <meta name="keywords"
        content="argo premium features, invoices payments, ai receipt scanning, predictive analytics, finance tracker, sales tracker subscription">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Premium Subscription - Argo Books">
    <meta property="og:description"
        content="Subscribe to Argo Books Premium. Get invoices & payments, AI receipt scanning, and predictive analytics. $<?php echo number_format($monthlyPrice, 0); ?>/month or $<?php echo number_format($yearlyPrice, 0); ?>/year.">
    <meta property="og:url" content="https://argorobots.com/pricing/premium/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Premium Subscription - Argo Books</title>

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/link.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <section class="hero ai-hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="ai-badge-large">Premium Features</div>
            <h1>Unlock Premium for Your Business</h1>
            <p>Transform your finance tracking with invoices & payments and AI-powered features. Get intelligent insights, automated receipt
                scanning, and predictive analytics.</p>
        </div>
    </section>

    <section class="ai-features-showcase">
        <div class="container">
            <h2>What's Included</h2>
            <div class="ai-features-grid">
                <div class="ai-feature-card">
                    <div class="ai-feature-icon">
                        <?= svg_icon('document-lines') ?>
                    </div>
                    <h3>Unlimited Invoices & Payments</h3>
                    <p>Send unlimited invoices and track payments with ease. Free users get 25 invoices per month — upgrade for unlimited billing.</p>
                </div>
                <div class="ai-feature-card">
                    <div class="ai-feature-icon">
                        <?= svg_icon('calendar-dots') ?>
                    </div>
                    <h3>Receipt Scanning</h3>
                    <p>Automatically extract data from receipts using advanced image recognition. Save hours of manual
                        data entry.</p>
                </div>
                <div class="ai-feature-card">
                    <div class="ai-feature-icon">
                        <?= svg_icon('package-detail') ?>
                    </div>
                    <h3>predictive analytics</h3>
                    <p>Forecast future trends based on your historical data. Make informed decisions with AI-powered
                        predictions.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="ai-pricing-section">
        <div class="container">
            <h2>Choose Your Plan</h2>
            <p class="pricing-subtitle">Select your billing frequency</p>

            <div class="billing-toggle">
                <button type="button" class="billing-option active" data-billing="monthly">Monthly</button>
                <button type="button" class="billing-option" data-billing="yearly">Yearly (Save $<?php echo number_format($yearlySavings, 0); ?>)</button>
            </div>

            <div class="pricing-display">
                <div class="price-box" id="price-display">
                    <div class="current-price">
                        <span class="currency">$</span>
                        <span class="amount" id="price-amount"><?php echo number_format($monthlyPrice, 0); ?></span>
                        <span class="period" id="price-period">CAD/month</span>
                    </div>
                </div>
            </div>

            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                <div class="payment-grid">
                    <button class="payment-btn" id="pay-paypal">
                        <img src="../../resources/images/PayPal-logo.svg" alt="PayPal" loading="lazy">
                        <span>Pay with PayPal</span>
                    </button>
                    <button class="payment-btn" id="pay-stripe">
                        <img class="Stripe" src="../../resources/images/Stripe-logo.svg" alt="Stripe" loading="lazy">
                        <span>Pay with Stripe</span>
                    </button>
                    <button class="payment-btn" id="pay-square">
                        <img class="Square" src="../../resources/images/Square-logo.svg" alt="Square" loading="lazy">
                        <span>Pay with Square</span>
                    </button>
                </div>
            </div>

            <div class="subscription-info">
                <p><strong>Subscription Terms:</strong></p>
                <ul>
                    <li>Cancel anytime - no long-term commitment</li>
                    <li>Automatic renewal unless cancelled</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="ai-faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I cancel my subscription?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, you can cancel your subscription at any time. Your Premium features will remain active until the end of your current billing period.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What happens to my data if I cancel?</h3>
                        <div class="faq-icon">
                            <?= svg_icon('chevron-down') ?>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Your data remains safe in Argo Books. You'll just lose access to Premium-specific features until you resubscribe.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentBilling = 'monthly';

            const monthlyPrice = <?php echo $monthlyPrice; ?>;
            const yearlyPrice = <?php echo $yearlyPrice; ?>;

            // Billing toggle
            document.querySelectorAll('.billing-option').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.billing-option').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentBilling = this.dataset.billing;
                    updatePriceDisplay();
                });
            });

            function updatePriceDisplay() {
                const priceAmount = document.getElementById('price-amount');
                const pricePeriod = document.getElementById('price-period');

                if (currentBilling === 'monthly') {
                    priceAmount.textContent = monthlyPrice;
                    pricePeriod.textContent = 'CAD/month';
                } else {
                    priceAmount.textContent = yearlyPrice;
                    pricePeriod.textContent = 'CAD/year';
                }
            }

            // Payment button handlers
            function getCheckoutUrl(method) {
                const params = new URLSearchParams({
                    method: method,
                    billing: currentBilling
                });

                return 'checkout/?' + params.toString();
            }

            document.getElementById('pay-paypal').addEventListener('click', function() {
                window.location.href = getCheckoutUrl('paypal');
            });

            document.getElementById('pay-stripe').addEventListener('click', function() {
                window.location.href = getCheckoutUrl('stripe');
            });

            document.getElementById('pay-square').addEventListener('click', function() {
                window.location.href = getCheckoutUrl('square');
            });
        });
    </script>
</body>

</html>
