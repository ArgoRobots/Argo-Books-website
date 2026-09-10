<?php
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../partials/fonts.php';
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../track_referral_event.php';
require_once __DIR__ . '/link_install.php';

// Deliberately not including track_referral.php: that records a landing event,
// and this page is opened by the app rather than chosen by a visitor. Counting
// it as a website visit would inflate the top of the funnel with every install.
$machine_uuid = $_GET['m'] ?? null;
$visitor_id   = $_COOKIE[ARGO_VISITOR_COOKIE] ?? null;

if (welcome_valid_uuid($machine_uuid) && welcome_valid_uuid($visitor_id)) {
    welcome_link_install($pdo, $machine_uuid, $visitor_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <meta name="description"
        content="You've installed Argo Books. Here's the fastest way to see what it does, and where your books are kept.">

    <!-- Opened by the app on first run, so it should never rank or be crawled as a
         landing page. It is written to make sense to a person either way. -->
    <meta name="robots" content="noindex, follow">

    <meta property="og:title" content="Welcome to Argo Books">
    <meta property="og:description"
        content="You've installed Argo Books. Here's the fastest way to see what it does.">
    <meta property="og:url" content="https://argorobots.com/welcome/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">

    <link rel="canonical" href="https://argorobots.com/welcome/">
    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Welcome to Argo Books</title>

    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
    <?= argo_font_links('default', '    ') ?>
    <link rel="stylesheet" href="../resources/styles/typography.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

        <section class="wc-hero">
            <div class="wc-hero-inner">
                <h1>Argo Books is installed</h1>
                <p class="wc-lead">
                    Nothing to set up and no account to create. Here's the quickest way to
                    find out whether it fits how you work.
                </p>
            </div>
        </section>

        <div class="wc-container">

            <section class="wc-start">
                <h2>Start here</h2>
                <p>
                    Argo Books opens on a welcome screen. Click
                    <strong>Create New Company</strong> and enter your business name. Once it's
                    created, a guided tour offers to show you around: choose
                    <strong>Start Tour</strong>.
                </p>
                <p>
                    Would you rather look around first? Choose
                    <strong>Skip Tutorial</strong>, then <strong>Explore Sample Company</strong>.
                    That opens a company already filled in with invoices, expenses and stock,
                    so you can try things without entering anything first. Nothing you do to
                    it touches real data.
                </p>
                <p>Either way, the two things worth trying first:</p>
                <ol class="wc-steps">
                    <li>Go to <strong>Receipts</strong> and scan one. Photograph a real receipt if you have one nearby: the supplier, date, total and tax are read for you.</li>
                    <li>Open <strong>Reports</strong> and generate a profit and loss statement.</li>
                </ol>
                <p class="wc-aside">
                    You can create your own company at any point from the File menu. The
                    sample stays where it is.
                </p>
            </section>

            <section class="wc-cards">
                <h2>What it does</h2>
                <div class="wc-card-grid">
                    <a class="wc-card" href="../documentation/pages/features/receipt-scanning.php">
                        <h3>Receipt scanning</h3>
                        <p>Photograph a receipt and the supplier, date, total and tax are filled in for you.</p>
                    </a>
                    <a class="wc-card" href="../documentation/pages/features/invoicing.php">
                        <h3>Invoicing</h3>
                        <p>Build an invoice, send it, and take payment without leaving your books.</p>
                    </a>
                    <a class="wc-card" href="../documentation/pages/features/bank-statement-import.php">
                        <h3>Bank statement import</h3>
                        <p>Bring in a statement or spreadsheet instead of typing transactions one at a time.</p>
                    </a>
                    <a class="wc-card" href="../documentation/pages/features/report-generator.php">
                        <h3>Reports</h3>
                        <p>Profit and loss, balance sheet, and tax-ready summaries built from what you've entered.</p>
                    </a>
                </div>
            </section>

            <section class="wc-data">
                <h2>Where your books are kept</h2>
                <p>
                    On your own computer, in a single file you can see, copy and back up.
                    Argo Books works with no internet connection, and your records aren't
                    sitting on anyone else's server waiting for a subscription to lapse.
                </p>
                <p>
                    That also means backups are yours to keep. Copy the file somewhere safe
                    now and then, the same way you would any document that matters.
                </p>
            </section>

            <section class="wc-next">
                <h2>If you get stuck</h2>
                <ul class="wc-links">
                    <li><a class="link" href="../documentation/pages/getting-started/quick-start.php">Quick start tutorial</a>, a walkthrough of the first hour</li>
                    <li><a class="link" href="../documentation/">Documentation</a>, for everything else</li>
                    <li><a class="link" href="../community/">Community</a>, to ask a question or say what's missing</li>
                    <li><a class="link" href="../whats-new/">What's new</a>, to see what changed in this version</li>
                </ul>
            </section>

        </div>

    </main>

    <footer class="footer">
        <?php include __DIR__ . '/../resources/footer/footer.php'; ?>
    </footer>
</body>

</html>
