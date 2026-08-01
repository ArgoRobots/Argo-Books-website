<?php
require_once __DIR__ . '/../partials/schema.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/feature-demo.php';
require_once __DIR__ . '/../resources/icons.php';
require_once __DIR__ . '/../config/pricing.php';
require_once __DIR__ . '/../track_referral.php';
require_once __DIR__ . '/../statistics.php';

if (PHP_SAPI !== 'cli') {
    track_page_view('paid_lp_software_companies');
}

$plans        = get_plan_features();
$pricing      = get_pricing_config();
$argo_monthly = (int) $pricing['premium_monthly_price'];
$free_invoices = (int) $pricing['free_invoice_monthly_limit'];

$cta_source = 'paid-lp-software-companies';
$download_url = '../downloads/?source=' . $cta_source;
$pricing_url  = '../pricing/?source=' . $cta_source;
$stripe_page  = '../integrations/stripe/';
$stripe_docs  = '../documentation/pages/integrations/stripe-integration.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <meta name="description"
        content="Accounting software for software and SaaS companies. Connect Stripe with a read-only key and import charges, fees, tax, refunds, and payouts straight into your books. Free desktop app for Windows, Mac, and Linux.">
    <meta name="keywords"
        content="accounting software for saas, saas bookkeeping software, stripe accounting software, accounting software for software companies, indie hacker bookkeeping, stripe to accounting import">

    <meta property="og:title" content="Argo Books for Software and SaaS Companies: Stripe Revenue Straight Into Your Books">
    <meta property="og:description"
        content="Connect Stripe with a read-only key. Charges become revenue, processing fees become expenses, refunds and payouts sort themselves out.">
    <meta property="og:url" content="https://argorobots.com/for-software-companies/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Argo Books for Software and SaaS Companies: Stripe Revenue Straight Into Your Books">
    <meta name="twitter:description"
        content="Connect Stripe with a read-only key and stop copying charges into a spreadsheet.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <link rel="canonical" href="https://argorobots.com/for-software-companies/">

    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "For Software Companies" => "/for-software-companies/"]) ?></script>

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How does the Stripe connection work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "You create a restricted key in your Stripe dashboard with read access to balance transactions, charges, and payouts, then paste it into Settings, Integrations in Argo Books. The key is read-only, so Argo can see your Stripe activity but can never move money, issue refunds, or change anything in your Stripe account."
                    }
                },
                {
                    "@type": "Question",
                    "name": "What actually gets imported from Stripe?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Each Stripe charge becomes a revenue entry with the product name, the customer (created automatically if they are new), any sales tax, and any discount. Processing fees are recorded as expenses linked to the sale. Refunds mark the original sale as returned. Revenue is recorded gross with the fee as a separate expense, so your books stay standard."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Will my Stripe payouts get double-counted when I import my bank statement?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books remembers every Stripe payout it has seen, so when you later import your bank statement the matching deposit is skipped instead of being added as a second piece of revenue. This is the part people usually get wrong by hand."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is the sync automatic?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No, and that is deliberate. You click Sync now and Argo shows you a summary of the sales and fees it found before anything is written. Nothing is imported until you confirm, and a sync can be undone in one step."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I see my MRR and ARR?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Indirectly, and for a lot of software businesses that is enough. You can report revenue over any window you like: This Month, Last 30 Days, This Year, Last 365 Days, or a custom start and end date. If you bill monthly only, your monthly revenue total is your MRR and your yearly total is your ARR, read straight off the dashboard. The gap opens up if you sell annual plans or one-time charges. Argo records an annual charge in full on the day it clears rather than spreading it over twelve months, so a monthly total spikes whenever someone prepays, and a true MRR figure normalizes that away. Stripe's own dashboard already gives you the normalized run rate, and there is no churn or cohort retention reporting in Argo Books."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Does it handle deferred revenue on annual plans?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. An annual plan is recorded as revenue on the date the charge went through, not spread across twelve months. If you are on cash-basis accounting, which most small software businesses are, that is exactly what you want. If your accountant needs accrual-basis deferred revenue schedules, Argo Books will not produce them."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is it really free?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes, forever. The Stripe integration is part of the core app, not a paid add-on, and the free tier includes <?= $free_invoices ?> invoices per month. Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start."
                    }
                }
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Argo Books for Software and SaaS Companies: Stripe Revenue Straight Into Your Books</title>

    <script src="../resources/scripts/main.js"></script>
    <!-- Drives the mockup in the hero. -->
    <script src="../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../compare/style.css">
    <link rel="stylesheet" href="../resources/styles/for-pages.css">
    <link rel="stylesheet" href="../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../resources/styles/pricing-cards.css">
    <link rel="stylesheet" href="../features/feature-page.css">
    <link rel="stylesheet" href="../resources/styles/smartscreen-guide.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/link.css">
    <link rel="stylesheet" href="../resources/styles/faq.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
    <!-- Brand typefaces (Fraunces display + IBM Plex Sans body), matched to the rest of the site -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../resources/styles/typography.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- Hero. Split, with the live mockup beside the copy rather than centred
         text on a gradient. Same demo markup and loop the landing page uses. -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Accounting software for software and SaaS companies</h1>
                    <p class="fp-hero-sub">Your revenue already lives in Stripe. Connect it with a read-only key and Argo Books turns every charge into a proper book entry: sales, processing fees, tax, discounts, customers, and refunds.</p>
                    <div class="fp-hero-act">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="fp-btn fp-btn-primary js-direct-download">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="#stripe" class="fp-textlink">See the Stripe Integration</a>
                    </div>
                    <p class="fp-hero-facts">Free desktop app for Windows, Mac, and Linux. No account, no credit card.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="expenses">
                    <?= argo_feature_demo('expenses') ?>
                </div>
            </div>
        </div>
    </section>

    <!-- SmartScreen walkthrough, revealed by lp-direct-download.php after a
         Windows direct-download click. -->
    <div class="container">
        <?php include __DIR__ . '/../resources/smartscreen-guide/guide.php'; ?>
    </div>

    <section id="stripe" class="feature-blocks">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Made for Software Companies</span>
                <h2>You built the product. The bookkeeping should not be the hard part</h2>
                <p class="section-desc">Running a software business means your entire income statement is sitting in one place: Stripe. Hundreds of small charges, a processing fee on every one of them, refunds, tax on some of it, and a payout every few days that lands in your bank as a single lump sum. Most founders end up exporting a CSV every month and cleaning it up by hand. Argo Books reads it straight from Stripe instead, and it is the only free desktop accounting app that does.</p>
            </div>
            <div class="section-header animate-on-scroll">
                <span class="section-label">The Stripe Integration</span>
                <h2>Connect once with a read-only key</h2>
                <p class="section-desc">Create a restricted key in your Stripe dashboard with read access to balance transactions, charges, and payouts. Paste it into Settings, Integrations. That is the whole setup. Argo can see your Stripe activity and can never move a dollar of it.</p>
            </div>

            <div class="fp-benefits">
                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('code-window', 20) ?>
                    </div>
                    <h3>Every charge becomes a real book entry</h3>
                    <p>Not a lump sum. Each Stripe charge imports with the product name, the customer (created for you if they are new), the sales tax, and any discount you applied. Processing fees are recorded as expenses linked to the sale, so revenue stays gross and your margin is honest.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('bank', 20) ?>
                    </div>
                    <h3>Payouts that don't get counted twice</h3>
                    <p>Argo Books remembers every Stripe payout it has seen. When you import your bank statement later, the matching deposit is skipped instead of landing as a second copy of the same revenue. This is the mistake that quietly inflates a founder's numbers every year, and here it just doesn't happen.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('shield-check', 20) ?>
                    </div>
                    <h3>Read-only, reviewed, and undoable</h3>
                    <p>The key can only read. The sync is on demand, never automatic. Argo shows you a summary of the sales and fees it found and imports nothing until you confirm, and any sync can be undone in one step. You are never one wrong click away from a messy ledger.</p>
                </div>

                <div class="fp-benefit animate-on-scroll">
                    <div class="fp-benefit-ic">
                        <?= svg_icon('receipt-scan-detail', 20) ?>
                    </div>
                    <h3>The cost side of a software business</h3>
                    <p>Hosting, domains, API credits, error monitoring, design contractors, ad spend. Snap or drop in the receipt and Argo pulls the vendor, date, and amount automatically. Tag it once and your real cost base is sitting next to your Stripe revenue instead of scattered across a dozen inboxes.</p>
                </div>
            </div>

            <p class="section-desc animate-on-scroll" style="text-align: center; margin-top: 28px;">
                See the <a href="<?= htmlspecialchars($stripe_page) ?>" class="link">full Stripe integration walkthrough</a>, or jump straight to the <a href="<?= htmlspecialchars($stripe_docs) ?>" class="link">setup steps in the docs</a>.
            </p>
        </div>
    </section>

    <section class="honest-take">
        <div class="container">
            <div class="honest-card animate-on-scroll">
                <div class="honest-icon">
                    <?= svg_icon('info', 28) ?>
                </div>
                <h3>What Argo Books isn't</h3>
                <p>Argo Books does the books, not the billing. Filter revenue to any date range for your monthly or yearly total, which is your MRR and ARR if you bill monthly only. It won't split a yearly prepayment over twelve months, so a month where someone pays up front looks unusually big, and it won't report churn. Stripe's dashboard covers both. Stripe also keeps handling plans, upgrades, and failed payments, and you click sync when you want new activity pulled in. What Argo does is turn a pile of Stripe charges into books you can hand to an accountant, free, on your own computer.</p>
                <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary js-direct-download honest-take-cta">
                    <span>Download Free</span>
                    <?= svg_icon('arrow-right', 18) ?>
                </a>
            </div>
        </div>
    </section>

    <section class="pricing-comparison">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Pricing</span>
                <h2>Start free, upgrade only if you need more</h2>
                <p class="pricing-strip-intro">The Stripe integration is part of the core app, not a paid tier. Premium adds predictive analytics for forecasting where revenue is heading, unlimited invoicing for the enterprise deals you bill directly, and priority support.</p>
            </div>
            <?php
            // The real cards, the same ones the landing and pricing pages use.
            include __DIR__ . '/../partials/pricing-cards.php';
            ?>
        </div>
    </section>

    <!-- The for- pages did not link to each other, which wastes the internal
         linking they exist to earn. -->
    <section class="fp-section-tight">
        <div class="fp-wrap">
            <div class="fp-head-c animate-on-scroll">
                <div class="fp-eyebrow fp-eyebrow-c">More trades</div>
                <h2 class="fp-h2">Argo Books for your line of work</h2>
            </div>
            <div class="fp-related animate-on-scroll">
                <a href="../for-solo-operators/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('user', 20) ?></div>
                    <h3>Solo operators</h3>
                    <p>One person, one price, books that need no bookkeeper.</p>
                </a>
                <a href="../for-resellers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('tag', 20) ?></div>
                    <h3>Resellers</h3>
                    <p>Cost, margin and stock across everything you list.</p>
                </a>
                <a href="../for-rental-businesses/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('calendar', 20) ?></div>
                    <h3>Rental businesses</h3>
                    <p>Bookings, availability and returns on one calendar.</p>
                </a>
                <a href="../for-local-wholesalers/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('truck', 20) ?></div>
                    <h3>Local wholesalers</h3>
                    <p>Stock, supplier orders and trade accounts on terms.</p>
                </a>
            </div>
        </div>
    </section>

    <section class="faq">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <?php $faqs = [];
            ob_start(); ?>How does the Stripe connection work?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>You create a restricted key in your Stripe dashboard with read access to balance transactions, charges, and payouts, then paste it into Settings, Integrations in Argo Books.</p>
                            <p>The key is read-only, so Argo can see your Stripe activity but can never move money, issue refunds, or change anything in your Stripe account.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>What actually gets imported from Stripe?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Each Stripe charge becomes a revenue entry with the product name, the customer (created automatically if they are new), any sales tax, and any discount. Processing fees are recorded as expenses linked to the sale. Refunds mark the original sale as returned.</p>
                            <p>Revenue is recorded gross with the fee as a separate expense, so your books stay standard.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Will my Stripe payouts get double-counted when I import my bank statement?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>No. Argo Books remembers every Stripe payout it has seen, so when you later import your bank statement the matching deposit is skipped instead of being added as a second piece of revenue.</p>
                            <p>This is the part people usually get wrong by hand.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is the sync automatic?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>No, and that is deliberate. You click Sync now and Argo shows you a summary of the sales and fees it found before anything is written.</p>
                            <p>Nothing is imported until you confirm, and a sync can be undone in one step.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Can I see my MRR and ARR?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Indirectly, and for a lot of software businesses that is enough. You can report revenue over any window you like: This Month, Last 30 Days, This Year, Last 365 Days, or a custom start and end date. If you bill monthly only, your monthly revenue total is your MRR and your yearly total is your ARR, read straight off the dashboard.</p>
                            <p>The gap opens up if you sell annual plans or one-time charges. Argo records an annual charge in full on the day it clears rather than spreading it over twelve months, so a monthly total spikes whenever someone prepays, and a true MRR figure normalizes that away. Stripe's own dashboard already gives you the normalized run rate, and there is no churn or cohort retention reporting in Argo Books.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Does it handle deferred revenue on annual plans?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>No. An annual plan is recorded as revenue on the date the charge went through, not spread across twelve months.</p>
                            <p>If you are on cash-basis accounting, which most small software businesses are, that is exactly what you want. If your accountant needs accrual-basis deferred revenue schedules, Argo Books will not produce them.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            ob_start(); ?>Is it really free?<?php $q = ob_get_clean();
            ob_start(); ?>
                            <p>Yes, forever. The Stripe integration is part of the core app, not a paid add-on, and the free tier includes <?= $free_invoices ?> invoices per month.</p>
                            <p>Premium ($<?= $argo_monthly ?> CAD/month) adds predictive analytics, unlimited invoicing, and priority support. No credit card to start.</p>
                        
            <?php $faqs[] = ['q_html' => $q, 'a_html' => ob_get_clean()];
            echo argo_faq_grid($faqs); ?>
        </div>
    </section>

    </main>

    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to stop cleaning up Stripe exports?</h2>
                    <p>Download Argo Books for free. Create a read-only Stripe key, run your first sync, and see a month of charges land as real book entries in under ten minutes.</p>
                    <div class="cta-buttons">
                        <a href="<?= htmlspecialchars($download_url) ?>" class="btn-cta btn-cta-primary js-direct-download">
                            <span>Download Free</span>
                            <?= svg_icon('arrow-right', 18) ?>
                        </a>
                        <a href="<?= htmlspecialchars($pricing_url) ?>" class="btn-cta btn-cta-ghost">
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
            const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-visible');
                    }
                });
            }, observerOptions);
            document.querySelectorAll('.animate-on-scroll').forEach(el => observer.observe(el));

        });
    </script>
<?php include __DIR__ . '/../resources/smartscreen-guide/lp-direct-download.php'; ?>
</body>

</html>
