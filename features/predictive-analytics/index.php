<?php require_once __DIR__ . '/../../resources/icons.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <meta name="description" content="Use AI-powered predictive analytics to forecast sales trends, detect seasonal patterns, and make data-driven business decisions with Argo Books.">
    <meta name="keywords" content="predictive analytics, financial forecasting, business analytics, sales trend forecasting, AI business analytics">

    <meta property="og:title" content="Predictive Analytics — Argo Books">
    <meta property="og:description" content="Use AI-powered predictive analytics to forecast sales trends, detect seasonal patterns, and make data-driven business decisions with Argo Books.">
    <meta property="og:url" content="https://argorobots.com/features/predictive-analytics/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Predictive Analytics — Argo Books">
    <meta name="twitter:description" content="Use AI-powered predictive analytics to forecast sales trends, detect seasonal patterns, and make data-driven business decisions with Argo Books.">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/features/predictive-analytics/">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Predictive Analytics", "item": "https://argorobots.com/features/predictive-analytics/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Predictive Analytics — Argo Books</title>

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
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
            <div class="hero-badge animate-fade-in">
                <?= svg_icon('analytics', 16) ?>
                <span>Predictive Analytics</span>
            </div>
            <h1 class="animate-fade-in">Predictive Analytics</h1>
            <p class="hero-subtitle animate-fade-in">See what's coming before it happens. AI-powered forecasting analyzes your data to predict trends, spot seasonal patterns, and help you make smarter decisions.</p>
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

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to get started?</h2>
                    <p>Download Argo Books for free and see the difference for yourself.</p>
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
            <div id="includeFooter"></div>
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
