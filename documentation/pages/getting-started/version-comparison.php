<?php
$pageTitle = 'Free vs. Paid Version';
$pageDescription = 'Compare Argo Books free and premium versions. Learn about features, limitations, and which version is right for your business.';
$currentPage = 'version-comparison';
$pageCategory = 'getting-started';

require_once __DIR__ . '/../../../config/pricing.php';
require_once __DIR__ . '/../../../resources/icons.php';

$pricing = get_pricing_config();
$monthlyPrice = $pricing['premium_monthly_price'];
$yearlyPrice = $pricing['premium_yearly_price'];
$yearlySavings = ($monthlyPrice * 12) - $yearlyPrice;

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books offers two tiers to match your business needs. Start with our free version,
            perfect for small businesses just getting started with inventory tracking. As your business
            grows, upgrade to Premium for unlimited products and AI-powered features.</p>

            <p>Not sure which version is right for you? <a href="../../../downloads/" class="link">Try our free
                version first</a> – you can always <a href="../../../pricing/" class="link">upgrade
                later</a> when you need more features.</p>

            <div class="version-cards">
                <!-- Free Version -->
                <div class="version-card">
                    <div class="card-header">
                        <h3 class="version-title">Free</h3>
                        <p class="version-subtitle">Perfect for getting started</p>
                        <div class="version-price">$0</div>
                    </div>
                    <ul class="feature-list">
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Unlimited products</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Unlimited transactions</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Real-time analytics</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Receipt management</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">5 invoices / month</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">AI spreadsheet import <span>(100/month)</span></span>
                        </li>
                    </ul>
                    <a href="../../../downloads/" class="btn btn-gray">Get Started for Free</a>
                </div>

                <!-- Premium Version -->
                <div class="version-card premium">
                    <div class="card-header">
                        <h3 class="version-title">Premium</h3>
                        <p class="version-subtitle">Unlock the full power of Argo Books</p>
                        <div class="version-price">$<?php echo number_format($monthlyPrice, 0); ?> <span class="price-period">CAD/month</span></div>
                        <p class="price-alt">or $<?php echo number_format($yearlyPrice, 0); ?> CAD/year (save $<?php echo number_format($yearlySavings, 0); ?>)</p>
                    </div>
                    <ul class="feature-list">
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Everything in Free</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Unlimited products</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Biometric login security</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Unlimited invoices & payments</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">AI receipt scanning <span>(500/month)</span></span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Predictive analytics</span>
                        </li>
                        <li class="feature-item">
                            <?= svg_icon('check-alt', 20, 'check-icon') ?>
                            <span class="feature-text">Priority support</span>
                        </li>
                    </ul>
                    <a href="../../../pricing/premium/" class="btn btn-purple">Subscribe to Premium</a>
                </div>
            </div>

            <h2 style="margin-top: 3rem;">Feature Comparison</h2>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Free</th>
                            <th>Premium</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Products</td>
                            <td>Up to 10</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Transactions</td>
                            <td>Unlimited</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Real-time Analytics</td>
                            <td>✓</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Receipt Management</td>
                            <td>✓</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Excel Import/Export</td>
                            <td>✓</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>AI Spreadsheet Import</td>
                            <td>✓</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Report Generator</td>
                            <td>✓</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Biometric Login</td>
                            <td>—</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Invoices & Payments</td>
                            <td>5 invoices / month</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>AI Receipt Scanning</td>
                            <td>—</td>
                            <td>(500 receipts / month)</td>
                        </tr>
                        <tr>
                            <td>Predictive Analytics</td>
                            <td>—</td>
                            <td>✓</td>
                        </tr>
                        <tr>
                            <td>Support</td>
                            <td>Standard</td>
                            <td>Priority</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="page-navigation">
                <a href="quick-start.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Quick Start Tutorial</span>
                </a>
                <a href="../features/dashboard.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Dashboard &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
