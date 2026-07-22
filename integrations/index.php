<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../track_referral.php';
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
        content="Connect Argo Books to the tools you already use. Import your Stripe sales, fees, and customers automatically, with more integrations on the way.">
    <meta name="keywords"
        content="Argo Books integrations, Stripe integration, Square integration, PayPal integration, Shopify integration, Etsy integration, WooCommerce integration, Gumroad integration, Argo Books API, accounting software integrations, connect Stripe to accounting software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Integrations | Argo Books">
    <meta property="og:description"
        content="Connect Argo Books to the tools you already use. Import your Stripe sales, fees, and customers automatically, with more integrations on the way.">
    <meta property="og:url" content="https://argorobots.com/integrations/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Integrations | Argo Books">
    <meta name="twitter:description"
        content="Connect Argo Books to the tools you already use. Import your Stripe sales, fees, and customers automatically, with more integrations on the way.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/integrations/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Integrations", "item": "https://argorobots.com/integrations/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Integrations | Argo Books</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../features/style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">

    <!-- Page-local additions: live/coming-soon badges and a muted card state
         for integrations that aren't built yet. Reuses features/style.css for
         everything else, these two states don't exist there. -->
    <style>
        .integration-card-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 14px;
        }

        .integration-card-badge.live {
            background: var(--emerald-100);
            color: var(--emerald-700);
        }

        .integration-card-badge.soon {
            background: var(--gray-bg-light);
            color: var(--gray-500);
        }

        .feature-card.integration-card-disabled {
            cursor: default;
            opacity: 0.8;
        }

        .feature-card.integration-card-disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .integration-card-disabled .feature-card-icon {
            background: var(--gray-bg-light) !important;
            color: var(--gray-400) !important;
        }

        .integration-card-disabled h3 {
            color: var(--gray-600);
        }
    </style>
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <h1 class="animate-fade-in">Integrations</h1>
            <p class="hero-subtitle animate-fade-in">Bring the tools you already use into your books. Connect your existing tools so your sales, fees, and customers flow into Argo Books automatically.</p>
            <div class="hero-ctas animate-fade-in">
                <a href="../downloads/" class="btn-cta btn-cta-primary">
                    <span>Get Started Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
                <a href="../pricing/" class="btn-cta btn-cta-outline">
                    <span>View Pricing</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Integrations Grid Section -->
    <section class="features-overview">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Connect Your Tools</span>
                <h2 class="section-title">Where you take payments</h2>
                <p class="section-desc">Start with Stripe today. We're building out support for the other platforms below next.</p>
            </div>
            <div class="features-grid">
                <!-- Stripe (live) -->
                <a href="<?= $base ?>integrations/stripe/" class="feature-card animate-on-scroll">
                    <span class="integration-card-badge live">Live</span>
                    <div class="feature-card-icon blue">
                        <?= svg_icon('credit-card', 24) ?>
                    </div>
                    <h3>Stripe</h3>
                    <p>Import your Stripe sales, fees, tax, and customers into your books automatically.</p>
                    <span class="feature-card-link">Learn more <?= svg_icon('arrow-right', 16) ?></span>
                </a>

                <!-- Square (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon purple">
                        <?= svg_icon('shape-square', 24) ?>
                    </div>
                    <h3>Square</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- PayPal (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon cyan">
                        <?= svg_icon('credit-card', 24) ?>
                    </div>
                    <h3>PayPal</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- Shopify (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon amber">
                        <?= svg_icon('shopping-bag', 24) ?>
                    </div>
                    <h3>Shopify</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- Etsy (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon red">
                        <?= svg_icon('package', 24) ?>
                    </div>
                    <h3>Etsy</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- WooCommerce (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon purple">
                        <?= svg_icon('globe', 24) ?>
                    </div>
                    <h3>WooCommerce</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- Gumroad (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon cyan">
                        <?= svg_icon('download', 24) ?>
                    </div>
                    <h3>Gumroad</h3>
                    <p>Coming soon.</p>
                </div>

                <!-- Argo API (coming soon) -->
                <div class="feature-card integration-card-disabled animate-on-scroll">
                    <span class="integration-card-badge soon">Coming Soon</span>
                    <div class="feature-card-icon green">
                        <?= svg_icon('bolt', 24) ?>
                    </div>
                    <h3>Argo API</h3>
                    <p>Connect your own website or app and send sales into your books directly. Coming soon.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Connect Section -->
    <section class="why-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why Connect</span>
                <h2 class="section-title">Less typing, more accurate books</h2>
                <p class="section-desc">Integrations pull the details in for you, so nothing gets missed and nothing gets entered twice.</p>
            </div>
            <div class="why-grid">
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('check', 28, '', 2.5) ?>
                    </div>
                    <h3>No manual data entry</h3>
                    <p>Sales, fees, and customers come in automatically, with the right amounts and categories already filled in.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('shield', 28) ?>
                    </div>
                    <h3>Read-only, always</h3>
                    <p>Every integration connects with a restricted, read-only key. Argo Books can read your data, but it can never move money.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('dollar', 28) ?>
                    </div>
                    <h3>Free to connect</h3>
                    <p>Integrations are included on every plan. Connect as many as you like at no extra cost.</p>
                </div>
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
                    <h2>Ready to connect your tools?</h2>
                    <p>Download Argo Books for free and connect Stripe in a few minutes.</p>
                    <div class="cta-buttons">
                        <a href="../downloads/" class="btn-cta btn-cta-primary">
                            <span>Download for Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="../pricing/" class="btn-cta btn-cta-ghost">
                            <span>View Pricing</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
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
