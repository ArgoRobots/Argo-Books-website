<?php
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../config/pricing.php';
$pricing = get_pricing_config();
$standardPrice = $pricing['standard_price'];
$monthlyPrice = $pricing['premium_monthly_price'];
$yearlyPrice = $pricing['premium_yearly_price'];
$discount = $pricing['premium_discount'];
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
        content="Upgrade Argo Books. Get Standard for $<?php echo number_format($standardPrice, 0); ?> CAD lifetime access or subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month. Unlimited products, Windows Hello, AI-powered insights, and more.">
    <meta name="keywords"
        content="upgrade argo books, buy full version, lifetime access software, unlimited products, business software pricing, finance tracker, sales tracker standard, premium subscription">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Upgrade - Argo Books">
    <meta property="og:description"
        content="Upgrade Argo Books. Get Standard for $<?php echo number_format($standardPrice, 0); ?> CAD lifetime access or subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month.">
    <meta property="og:url" content="https://argorobots.com/upgrade/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Upgrade - Argo Books">
    <meta name="twitter:description"
        content="Upgrade Argo Books. Get Standard for $<?php echo number_format($standardPrice, 0); ?> CAD lifetime access or subscribe to Premium for $<?php echo number_format($monthlyPrice, 0); ?>/month.">

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
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
                <span>Pricing</span>
            </div>
            <h1>Upgrade Your Experience</h1>
            <p class="hero-subtitle">Choose the plan that's right for your business</p>
        </div>
    </section>

    <section class="pricing-section">
        <div class="container">
            <div class="pricing-cards-wrapper">
                <!-- Standard Plan Card -->
                <a href="standard/" class="pricing-card-link">
                    <div class="upgrade-pricing-card premium-card">
                        <div class="card-badge">One-Time Payment</div>
                        <h2>Standard</h2>
                        <div class="card-price">
                            <span class="currency">$</span>
                            <span class="amount"><?php echo number_format($standardPrice, 0); ?></span>
                            <span class="period">CAD</span>
                        </div>
                        <p class="price-note">Lifetime access</p>

                        <ul class="card-features">
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Unlimited products</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Biometric login security</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Lifetime updates</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Priority support</span>
                            </li>
                        </ul>

                        <div class="card-highlight">
                            Pay once, use forever
                        </div>

                        <div class="card-cta">
                            <span class="cta-button premium-cta">Get Standard</span>
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
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Everything in Standard</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Invoices & payments</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>AI receipt scanning <span style="font-size: 0.85em; opacity: 0.8;">(500/month)</span></span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                                <span>Predictive sales analysis</span>
                            </li>
                        </ul>

                        <div class="card-highlight ai-highlight">
                            Standard users get a $<?php echo number_format($discount, 0); ?> discount!
                        </div>

                        <div class="card-cta">
                            <span class="cta-button ai-cta">Get Premium</span>
                        </div>
                    </div>
                </a>
            </div>

            <p class="pricing-note">30-day money back guarantee on all purchases</p>
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
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>No, you don't have to pay. We offer a free version that you can use indefinitely. The free version includes all essential features needed to manage your basic business operations, with a limit of up to 10 products. If you need to track more products, consider upgrading to Standard.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What's the difference between Standard and Premium?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p><strong>Standard ($<?php echo number_format($standardPrice, 0); ?> one-time)</strong> unlocks unlimited products, Windows Hello security, lifetime updates, and priority support. <strong>Premium ($<?php echo number_format($monthlyPrice, 0); ?>/month)</strong> adds invoices & payments, AI-powered features like receipt scanning, and predictive analysis.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How does the $<?php echo number_format($discount, 0); ?> discount for Standard users work?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>If you've purchased Standard, you get $<?php echo number_format($discount, 0); ?> off your first year of Premium. Just enter your license key when subscribing, and your first year drops from $<?php echo number_format($yearlyPrice, 0); ?> to $<?php echo number_format($yearlyPrice - $discount, 0); ?>. After that, it renews at the regular $<?php echo number_format($yearlyPrice, 0); ?>/year price. You will still have access to your standard license key after you switch to the Premium subscription.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I cancel the Premium subscription anytime?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
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
                        <h3>Is Standard a one-time payment?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, pay once and get lifetime access to all Standard features, including future updates. No recurring charges.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I transfer my license to a different computer?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Yes, you can reactivate Argo Books on your new computer, but this will deactivate it on your previous device. Each license can only be active on one computer at a time.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <h3>When will I receive my license key?</h3>
                        <div class="faq-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="6,9 12,15 18,9"/>
                            </svg>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="faq-answer-content">
                            <p>Immediately after your payment is confirmed, your license key will be sent to the email you provided during checkout. This process usually only takes a few seconds.</p>
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
