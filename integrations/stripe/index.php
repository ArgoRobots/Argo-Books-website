<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_cfg = get_pricing_config();
$argo_monthly = (int) $argo_cfg['premium_monthly_price'];
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
        content="Connect your Stripe account to Argo Books with a read-only key. Every sale imports with the product, customer, tax, and discount already filled in, and Stripe fees are tracked automatically.">
    <meta name="keywords"
        content="Stripe integration, connect Stripe to accounting software, Stripe sales import, Stripe fees tracking, Stripe refunds accounting, Stripe bookkeeping software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Stripe Integration | Argo Books">
    <meta property="og:description"
        content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
    <meta property="og:url" content="https://argorobots.com/integrations/stripe/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Stripe Integration | Argo Books">
    <meta name="twitter:description"
        content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/integrations/stripe/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Integrations", "item": "https://argorobots.com/integrations/"},
                {"@type": "ListItem", "position": 3, "name": "Stripe", "item": "https://argorobots.com/integrations/stripe/"}
            ]
        }
    </script>

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How does the Argo Books Stripe integration work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Connect a restricted, read-only Stripe key under Settings > Integrations, then click Sync now. Argo Books reads your charges, refunds, fees, and customers and turns them into revenue entries, expense entries, and customer records ready to review and import."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can Argo Books move money through my Stripe account?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. The integration uses a restricted, read-only key, so Argo Books can read your payouts and charges but can never issue a charge, send a payout, or change anything in your Stripe account."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Will importing my bank statement double count my Stripe payouts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books recognizes a Stripe payout deposit on your bank statement and skips it automatically, since the individual sales were already recorded through the Stripe integration."
                    }
                }
            ]
        }
    </script>

    <!-- SoftwareApplication Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, macOS",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Connect Stripe to Argo Books with a restricted, read-only key. Every charge becomes a revenue entry with the product, customer, tax, and discount filled in, fees are tracked as linked expenses, and refunds and bank payouts are handled correctly.",
            "featureList": "Stripe sales import, Automatic fee tracking, Refund handling, Read-only key connection, Bank import duplicate detection"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Stripe Integration | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../../features/style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">

    <!-- Page-local addition: a 4-column variant of the shared steps-grid for
         this page's 4-step "How It Works" list (features/style.css only
         defines the 3-column version). Collapses to one column below 900px,
         matching the shared .steps-grid breakpoint. -->
    <style>
        .steps-grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        @media (max-width: 900px) {
            .steps-grid-4 {
                grid-template-columns: 1fr;
                max-width: 500px;
                margin: 0 auto;
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- =============================================
         HERO SECTION
         ============================================= -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Your Stripe sales, in your books, automatically.</h1>
            <p class="hero-subtitle animate-fade-in">Connect your Stripe account with a read-only key and every sale flows in with the right product, customer, tax, and discount.</p>
            <div class="hero-ctas animate-fade-in">
                <a href="../../downloads/" class="btn-cta btn-cta-primary">
                    <span>Get Started Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
                <a href="../../pricing/" class="btn-cta btn-cta-outline">
                    <span>View Pricing</span>
                </a>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT YOU GET
         ============================================= -->
    <section class="why-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">What You Get</span>
                <h2 class="section-title">Everything Stripe sends, sorted for you</h2>
                <p class="section-desc">Five things happen automatically the moment you connect.</p>
            </div>
            <div class="why-grid">
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('receipt', 28) ?>
                    </div>
                    <h3>Every sale, in full detail</h3>
                    <p>Each Stripe charge becomes a revenue entry with the product, the customer (auto-created), sales tax, and any discount.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('dollar', 28) ?>
                    </div>
                    <h3>Fees tracked automatically</h3>
                    <p>Stripe processing fees are recorded as expenses linked to each sale, so your profit is accurate.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('refresh', 28) ?>
                    </div>
                    <h3>Refunds handled correctly</h3>
                    <p>A Stripe refund marks the original sale as returned, not a mystery expense.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('check', 28, '', 2.5) ?>
                    </div>
                    <h3>No double-counting</h3>
                    <p>When you also import your bank statement, Argo recognizes the Stripe payout deposit and skips it, so revenue is never counted twice.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('key', 28) ?>
                    </div>
                    <h3>Read-only and safe</h3>
                    <p>You connect with a restricted, read-only key. Argo can read your payouts and charges but can never move money or change anything.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('eye', 28) ?>
                    </div>
                    <h3>You stay in control</h3>
                    <p>Sync when you want, review a summary before anything is imported, and undo a whole sync in one step.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>See it for yourself</h3>
                <p>Connect Stripe and watch your next batch of sales import automatically.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRIVACY / READ-ONLY DETAIL (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your Stripe data stays under your control</h2>
                    <p>Argo Books connects to Stripe with a restricted key that you create and can revoke at any time. That key only ever grants read access, so Argo Books can pull in your charges, refunds, payouts, and customers, but it has no way to issue a charge, send a payout, or change a setting in your Stripe account.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Restricted, read-only key, created and controlled by you</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Argo Books can never move money or change your Stripe settings</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Revoke the key from Stripe at any time to disconnect</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/privacy-local-storage.svg" alt="Your data stays local: encrypted, offline-capable, no cloud" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS, 4 Steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">Connect once, sync whenever you like</h2>
                <p class="section-desc">Four steps from Stripe to fully recorded books.</p>
            </div>
            <div class="steps-grid steps-grid-4">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Create a restricted key</h3>
                    <p>In your Stripe dashboard, create a restricted key with read-only access to charges, refunds, payouts, and customers.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Paste it into Argo Books</h3>
                    <p>Under Settings > Integrations, paste the key to connect your Stripe account.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Click Sync now</h3>
                    <p>Argo Books reads your recent activity and prepares a summary of what's about to import.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">4</div>
                    <h3>Review and import</h3>
                    <p>Check the summary, adjust anything you like, and import. Nothing is saved until you confirm.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2: primary download CTA + setup guide -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Ready to connect Stripe?</h3>
                <p>Download Argo Books, add your Stripe key, and see your sales import in minutes.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="<?= $base ?>documentation/pages/features/stripe-integration.php" class="btn-cta btn-cta-outline">
                        <span>Read the setup guide</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         RELATED
         ============================================= -->
    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Related</span>
                <h2 class="section-title">Works great with</h2>
                <p class="section-desc">Stripe pairs naturally with the rest of your books.</p>
            </div>
            <div class="related-grid">
                <a href="../../features/bank-statement-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('bank', 22) ?>
                    </div>
                    <h3>Bank Statement Import</h3>
                    <p>Import your bank statement too, and Argo Books skips the Stripe payout deposit automatically so revenue is never doubled up.</p>
                </a>
                <a href="../" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>All Integrations</h3>
                    <p>See what's live now and what's coming next.</p>
                </a>
            </div>
        </div>
    </section>

    </main>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to connect your Stripe account?</h2>
                    <p>Download Argo Books for free, connect Stripe in a few minutes, and watch your sales import automatically.</p>
                    <div class="cta-buttons">
                        <a href="../../downloads/" class="btn-cta btn-cta-primary">
                            <span>Download for Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="../../pricing/" class="btn-cta btn-cta-ghost">
                            <span>View Pricing</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
        </footer>
    </div>

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
        });
    </script>
</body>

</html>
