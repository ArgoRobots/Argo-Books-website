<?php
$pageTitle = 'AI Features';
$pageDescription = 'Explore Argo Books AI-powered features including receipt scanning, predictive analytics, and business insights.';
$currentPage = 'ai-features';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> AI features are available with the Premium subscription.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Unlock the power of artificial intelligence to streamline your business operations.
            Argo Books Premium includes three AI-powered features that save you time and provide intelligent
            insights to help grow your business.</p>

            <div class="feature-cards">
                <a href="receipt-scanning.php" class="feature-card">
                    <div class="feature-card-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <path d="M7 8h10M7 12h6"/>
                            <circle cx="17" cy="14" r="3"/>
                        </svg>
                    </div>
                    <h3>AI Receipt Scanning</h3>
                    <p>Transform paper receipts into digital records instantly. Take a photo and let the AI extract vendor, date, items, and totals automatically.</p>
                    <span class="feature-card-link">Learn more →</span>
                </a>

                <a href="predictive-analytics.php" class="feature-card">
                    <div class="feature-card-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 3v18h18"/>
                            <path d="M18 9l-5 5-4-4-3 3"/>
                        </svg>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>See the future of your business with AI-powered forecasting. Get revenue predictions, seasonal pattern detection, and inventory forecasts.</p>
                    <span class="feature-card-link">Learn more →</span>
                </a>

                <a href="business-insights.php" class="feature-card">
                    <div class="feature-card-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4M12 8h.01"/>
                        </svg>
                    </div>
                    <h3>AI Business Insights</h3>
                    <p>Receive intelligent recommendations to optimize your business. Get smart alerts, profit optimization tips, and growth opportunities.</p>
                    <span class="feature-card-link">Learn more →</span>
                </a>
            </div>

            <h2>Privacy & Data Security</h2>
            <p>Your data is processed securely:</p>
            <ul>
                <li>All AI processing uses encrypted connections</li>
                <li>Receipt images are processed and immediately deleted</li>
                <li>Your business data is never used to train AI models</li>
                <li>You remain in full control of your data</li>
            </ul>

            <div class="page-navigation">
                <a href="invoicing.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Invoicing & Payments
                </a>
                <a href="receipt-scanning.php" class="nav-button next">
                    Next: AI Receipt Scanning
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
