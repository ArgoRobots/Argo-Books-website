<?php
// Shared feature-tour section. Consumed by the comparison page; the
// landing page still renders its own inline copy for now.
require_once __DIR__ . '/../icons.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../includes/site-base-path.php';
$ft_base = site_base_path();
?>
    <section id="features" class="features-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <h2 class="section-title">The tools your business actually runs on</h2>
                <p class="section-description">Smart receipt scanning, invoicing, analytics, and inventory tracking, all in one easy app.</p>
            </div>

            <div class="features-tabs">
                <div class="features-tabs-nav animate-on-scroll">
                    <button class="tab-btn active" data-tab="ai-receipts">
                        <div class="tab-icon">
                            <?= svg_icon('receipt-scan-detail', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Receipt Scanning</span>
                            <span class="tab-subtitle">Snap a photo and your books update instantly</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="invoices">
                        <div class="tab-icon">
                            <?= svg_icon('document', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Invoicing</span>
                            <span class="tab-subtitle">Create, send, and track invoices to get paid</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="expenses">
                        <div class="tab-icon">
                            <?= svg_icon('dollar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Expense & Revenue Tracking</span>
                            <span class="tab-subtitle">Every dollar in and out, auto-categorized</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="customers">
                        <div class="tab-icon">
                            <?= svg_icon('users', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Customer Management</span>
                            <span class="tab-subtitle">Contacts, purchase history, and balances</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="predictive">
                        <div class="tab-icon">
                            <?= svg_icon('analytics', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Predictive Analytics</span>
                            <span class="tab-subtitle">See next month's cash flow in advance</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="inventory">
                        <div class="tab-icon">
                            <?= svg_icon('package', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Inventory Management</span>
                            <span class="tab-subtitle">Stock counts that stay accurate as you sell</span>
                        </div>
                    </button>
                    <button class="tab-btn" data-tab="rental">
                        <div class="tab-icon">
                            <?= svg_icon('calendar', 24) ?>
                        </div>
                        <div class="tab-text">
                            <span class="tab-title">Rental Management</span>
                            <span class="tab-subtitle">Bookings, availability, and returns tracked</span>
                        </div>
                    </button>
                </div>

                <div class="features-tabs-content">
                    <!-- AI Receipt Scanning -->
                    <?php
                    // Panel markup lives in partials/feature-demo.php so the landing page,
                    // these comparison pages, and the feature-page heroes all render the
                    // same demos from one source.
                    foreach (argo_feature_demo_keys() as $ftIndex => $ftKey): ?>
                        <div class="tab-content<?= $ftIndex === 0 ? ' active' : '' ?>" id="tab-<?= $ftKey ?>">
                            <?= argo_feature_demo($ftKey) ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </section>
