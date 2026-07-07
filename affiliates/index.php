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

    <meta name="description" content="Join the Argo Books affiliate program and earn 50% recurring commission for every customer you refer. Free to join, real-time dashboard, fast payouts.">
    <meta name="keywords" content="Argo Books affiliate program, accounting software affiliate, recurring commission, refer and earn">

    <meta property="og:title" content="Affiliate Program: Earn 50% Recurring | Argo Books">
    <meta property="og:description" content="Earn 50% commission for every customer you refer to Argo Books, for their first 12 months.">
    <meta property="og:url" content="https://argorobots.com/affiliates/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Affiliate Program: Earn 50% Recurring | Argo Books">
    <meta name="twitter:description" content="Earn 50% commission for every customer you refer to Argo Books, for their first 12 months.">

    <link rel="canonical" href="https://argorobots.com/affiliates/">

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Affiliate Program: Earn 50% Recurring | Argo Books</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>

    <main>
        <section class="aff-hero">
            <div class="container">
                <h1>Get paid when you sleep with affiliate commissions</h1>
                <p class="aff-subtitle">Join the Argo Books affiliate program and earn 50% commission for every customer you refer.</p>

                <div class="aff-offer">
                    <ul class="aff-offer-list">
                        <li><?= svg_icon('circle-check', 20) ?> 50% of all payments for 12 months</li>
                        <li><?= svg_icon('circle-check', 20) ?> Includes recurring subscriptions</li>
                        <li><?= svg_icon('circle-check', 20) ?> Real-time dashboard for clicks &amp; earnings</li>
                    </ul>
                    <a href="../community/affiliate/" class="btn btn-blue aff-cta">
                        <span>Become an affiliate</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <p class="aff-cta-note">Free to join. Sign in or create a free Argo account to apply.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
    </footer>
</body>

</html>
