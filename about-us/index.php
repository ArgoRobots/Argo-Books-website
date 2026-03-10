<?php require_once __DIR__ . '/../resources/icons.php';
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
        content="Learn about Argo, the Canada-based startup creating affordable finance management software for small businesses. Our mission: Better tools, built by entrepreneurs who understand your challenges.">
    <meta name="keywords"
        content="about argo books, Canada startup, small business software company, affordable business tools, finance management developers, canadian software company, Canadian, saskatchewan tech company">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="About Us - Argo Books | Canadian Software Company">
    <meta property="og:description"
        content="Learn about Argo, the Canada-based startup creating affordable finance management software for small businesses. Our mission: Better tools, built by entrepreneurs who understand your challenges.">
    <meta property="og:url" content="https://argorobots.com/about-us/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="About Us - Argo Books | Canadian Software Company">
    <meta name="twitter:description"
        content="Learn about Argo, the Canada-based startup creating affordable finance management software for small businesses. Our mission: Better tools, built by entrepreneurs who understand your challenges.">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">
    <meta name="geo.position" content="52.1579;-106.6702">
    <meta name="ICBM" content="52.1579, -106.6702">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/about-us/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>About Us - Argo Books | Canadian Software Company</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="hero-badge animate-fade-in">
                <?= svg_icon('map-pin', 16) ?>
                <span>Saskatoon, SK, Canada</span>
            </div>
            <h1 class="animate-fade-in">About Argo Books</h1>
            <p class="hero-subtitle animate-fade-in">Building the future of small business management, one feature at a time.</p>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-content animate-on-scroll">
                    <span class="section-label">Our Mission</span>
                    <h2>Affordable tools for every business</h2>
                    <p>We believe in giving you the tools you need to manage your business affordably. Most finance
                        management software require expensive monthly subscriptions and are difficult to use. We
                        created Argo Books to be different.</p>
                    <div class="mission-points">
                        <div class="mission-point">
                            <div class="point-icon">
                                <?= svg_icon('check-rounded') ?>
                            </div>
                            <p>Better than spreadsheets, simpler than enterprise software</p>
                        </div>
                        <div class="mission-point">
                            <div class="point-icon">
                                <?= svg_icon('check-rounded') ?>
                            </div>
                            <p>Easy to use but packed with powerful features</p>
                        </div>
                        <div class="mission-point">
                            <div class="point-icon">
                                <?= svg_icon('check-rounded') ?>
                            </div>
                            <p>Flexible pricing — Use it for free, or unlock more with the premium version</p>
                        </div>
                    </div>
                </div>
                <div class="mission-image animate-on-scroll">
                    <img src="../resources/images/dashboard.webp" alt="Argo Books Interface">
                </div>
            </div>
        </div>
    </section>

    <!-- Product Overview Section -->
    <section class="product-overview">
        <div class="container">
            <div class="overview-content animate-on-scroll">
                <span class="section-label">The Product</span>
                <h2>What We Build</h2>
                <p>Argo Books is a free, easy-to-use, yet powerful cross-platform app built for small
                    businesses, startups, and solo entrepreneurs who need an affordable solution to manage finances, track inventory,
                    and grow their business.</p>
            </div>
            <div class="features-grid">
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon">
                        <?= svg_icon('receipt-scan-detail', null, '', 1.5) ?>
                    </div>
                    <h4>AI Receipt Scanning</h4>
                    <p>Snap a photo and let AI extract all the details automatically</p>
                </div>
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon purple">
                        <?= svg_icon('analytics', null, '', 1.5) ?>
                    </div>
                    <h4>Predictive Analytics</h4>
                    <p>Forecast sales trends and make data-driven decisions</p>
                </div>
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon green">
                        <?= svg_icon('package', null, '', 1.5) ?>
                    </div>
                    <h4>Inventory Management</h4>
                    <p>Track stock levels and never run out of best-sellers</p>
                </div>
                <div class="feature-item animate-on-scroll">
                    <div class="feature-icon amber">
                        <?= svg_icon('document', null, '', 1.5) ?>
                    </div>
                    <h4>Invoicing & Payments</h4>
                    <p>Create professional invoices and get paid faster</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="our-story">
        <div class="container">
            <div class="story-grid">
                <div class="story-image animate-on-scroll">
                    <img src="../resources/images/saskatoon.webp" alt="Saskatoon Skyline">
                    <div class="image-badge">
                        <?= svg_icon('map-pin', 16) ?>
                        Saskatoon, SK, Canada
                    </div>
                </div>
                <div class="story-content animate-on-scroll">
                    <span class="section-label">Our Story</span>
                    <h2>Built by entrepreneurs, for entrepreneurs</h2>
                    <p>From humble beginnings with a simple goal: Create the finance tracking tool we
                        wished existed for our own small businesses.</p>
                    <p>What sets us apart is our first-hand experience with the challenges small businesses face. We're
                        not a large corporation with venture capital funding — we're a small, self-funded business that
                        understands what it means to watch every dollar and make smart investments in technology.</p>
                    <div class="story-stats">
                        <div class="stat">
                            <span class="stat-value">2024</span>
                            <span class="stat-label">Founded</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">100%</span>
                            <span class="stat-label">Self-funded</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">Free</span>
                            <span class="stat-label">Core Version</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values">
        <div class="container">
            <div class="values-header animate-on-scroll">
                <span class="section-label">What Drives Us</span>
                <h2>Our Values</h2>
            </div>
            <div class="values-grid">
                <div class="value-card animate-on-scroll">
                    <div class="value-icon">
                        <?= svg_icon('innovation') ?>
                    </div>
                    <h3>Innovation</h3>
                    <p>We constantly evolve our software to meet the changing needs of modern businesses.</p>
                </div>

                <div class="value-card animate-on-scroll">
                    <div class="value-icon purple">
                        <?= svg_icon('user-focused') ?>
                    </div>
                    <h3>User-Focused</h3>
                    <p>Every feature we build starts with understanding our users' needs and challenges.</p>
                </div>

                <div class="value-card animate-on-scroll">
                    <div class="value-icon green">
                        <?= svg_icon('reliability') ?>
                    </div>
                    <h3>Reliability</h3>
                    <p>We build software you can trust with your business data and daily operations.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Future Section -->
    <section class="future">
        <div class="container">
            <div class="future-content animate-on-scroll">
                <span class="section-label">What's Next</span>
                <h2>Looking Forward</h2>
                <p>As a growing Canadian startup, we're excited about what's ahead. Our roadmap is filled with new
                    features and improvements based directly on user feedback. We're committed to expanding our offerings
                    while maintaining the simplicity and affordability that makes Argo special.</p>
                <a href="../whats-new/" class="btn btn-secondary">
                    <span>View Changelog</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- Contact Section -->
        <section class="contact-section">
            <div class="container">
                <div class="contact-card animate-on-scroll">
                    <h2>Let's Build Together</h2>
                    <p>Have questions or suggestions? We'd love to hear from you and make Argo Books even better.</p>
                    <a href="../contact-us/" class="btn btn-primary">
                        <span>Contact Us</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div id="includeFooter"></div>
        </footer>
    </div>

    <script>
        // Scroll animations
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
