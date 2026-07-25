<?php
require_once __DIR__ . '/../resources/icons.php';

// Single source for the comparison hub grid. Add a competitor here (and create
// its compare/argo-books-vs-<slug>/ page) and it shows up automatically.
$comparisons = [
    ['slug' => 'quickbooks', 'name' => 'QuickBooks', 'hook' => 'Simpler and far more affordable, with none of the price creep.'],
    ['slug' => 'freshbooks', 'name' => 'FreshBooks', 'hook' => 'Your actual books, not just invoicing, and no per-client limits.'],
    ['slug' => 'wave',       'name' => 'Wave',       'hook' => 'Both free to start, but Argo does more, including inventory and offline.'],
    ['slug' => 'xero',       'name' => 'Xero',       'hook' => 'Less complexity, a lower price, and no invoice caps.'],
    ['slug' => 'zipbooks',   'name' => 'ZipBooks',   'hook' => 'A more capable free plan, and a cheaper upgrade.'],
    ['slug' => 'odoo',       'name' => 'Odoo',       'hook' => 'One flat price instead of Odoo\'s per-user ERP billing.'],
    ['slug' => 'honeybook',  'name' => 'HoneyBook',  'hook' => 'Argo keeps your actual books; HoneyBook just books clients and invoices.'],
    ['slug' => 'sage',       'name' => 'Sage',       'hook' => 'A fraction of Sage 50\'s price, modern and cross-platform, minus the complexity.'],
    ['slug' => 'zoho-books', 'name' => 'Zoho Books', 'hook' => 'Desktop, offline, and standalone, without getting pulled into Zoho\'s cloud suite.'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <meta name="description" content="See how Argo Books compares to QuickBooks, Wave, FreshBooks, Xero, ZipBooks and Odoo. Side-by-side comparisons of features, pricing, and ease of use.">
    <meta name="keywords" content="Argo Books comparison, QuickBooks alternative, accounting software comparison, small business accounting alternatives">

    <meta property="og:title" content="Compare Argo Books to Other Accounting Software">
    <meta property="og:description" content="Side-by-side comparisons of Argo Books vs QuickBooks, Wave, FreshBooks, Xero, ZipBooks, and Odoo.">
    <meta property="og:url" content="https://argorobots.com/compare/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <link rel="canonical" href="https://argorobots.com/compare/">
    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Compare Argo Books to QuickBooks, Wave, Xero &amp; More | Argo Books</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body class="compare-page compare-hub-page">
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="hero-content animate-fade-in">
                <span class="hero-eyebrow">Comparisons</span>
                <h1>How Argo Books <span class="text-gradient">compares</span></h1>
                <p class="hero-subtitle">See how Argo Books stacks up against the other accounting and invoicing tools, side by side, on features, pricing, and ease of use.</p>
                <div class="hero-ctas">
                    <a href="../downloads/" class="btn-cta btn-cta-primary">
                        <span>Try Argo Books Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../pricing/" class="btn-cta btn-cta-outline">
                        <span>View Pricing</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison grid -->
    <section class="compare-hub">
        <div class="container">
            <div class="hub-grid">
                <?php foreach ($comparisons as $c): ?>
                <a class="hub-card animate-on-scroll" href="argo-books-vs-<?= $c['slug'] ?>/">
                    <span class="hub-card-title">Argo Books vs <?= $c['name'] ?></span>
                    <span class="hub-card-hook"><?= $c['hook'] ?></span>
                    <span class="hub-card-cta">Compare <?= svg_icon('arrow-right', 16) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to see the difference yourself?</h2>
                    <p>Download Argo Books for free, no credit card, and try it against whatever you use today.</p>
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
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) entry.target.classList.add('animate-visible');
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));
        });
    </script>
</body>

</html>
