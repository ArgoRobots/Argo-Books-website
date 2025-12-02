<?php
$pageTitle = 'Free vs. Paid Version';
$pageDescription = 'Compare Argo Books free and paid versions. Learn about features, limitations, and which version is right for your business.';
$currentPage = 'version-comparison';

include '../../docs-header.php';
$pageCategory = 'getting-started';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <div class="description">
                    <h1>Free vs. Paid Version</h1>
                    <p>Argo Books offers two versions to match your business needs. Start with our free version,
                        perfect for small businesses just getting started with inventory tracking. As your business
                        grows, seamlessly upgrade to our paid version for unlimited products and advanced features.</p>
                    <p>Not sure which version is right for you? <a href="../" class="link">Try our free
                            version first</a> – you can always <a href="../upgrade/" class="link">upgrade
                            later</a> when you need more features.</p>
                </div>

                <div class="version-cards">
                    <div class="version-card">
                        <div class="card-header">
                            <h3 class="version-title">Free Version</h3>
                            <p class="version-subtitle">Perfect for small businesses</p>
                        </div>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <svg class="limit-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M8 12h8"></path>
                                </svg>
                                <span class="feature-text">Limited to 10 products</span>
                            </li>
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">Basic password protection</span>
                            </li>
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">Basic support via email</span>
                            </li>
                        </ul>
                    </div>

                    <div class="version-card">
                        <div class="card-header">
                            <h3 class="version-title">Paid Version</h3>
                            <p class="version-subtitle">For growing businesses</p>
                        </div>
                        <ul class="feature-list">
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">Unlimited products</span>
                            </li>
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">Windows Hello integration</span>
                            </li>
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">AI search</span>
                            </li>
                            <li class="feature-item">
                                <svg class="check-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                                <span class="feature-text">Priority support</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="page-navigation">
                    <a href="quick-start.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Quick Start Tutorial
                    </a>
                    <a href="../features/product-management.php" class="nav-button next">
                        Next: Product Management
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
