<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
require_once __DIR__ . '/../../partials/fonts.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];

// One array drives both the visible accordion and the FAQPage schema.
$faqs = [
    [
        'q' => 'How does the Argo Books Stripe integration work?',
        'a' => 'Connect a restricted, read-only Stripe key under Settings > Integrations, then click Sync now. Argo Books reads your charges, refunds, fees, and customers and turns them into revenue entries, expense entries, and customer records ready to review and import.',
    ],
    [
        'q' => 'Can Argo Books move money through my Stripe account?',
        'a' => 'No. The integration uses a restricted, read-only key, so Argo Books can read your payouts and charges but can never issue a charge, send a payout, or change anything in your Stripe account.',
    ],
    [
        'q' => 'Will importing my bank statement double count my Stripe payouts?',
        'a' => 'No. Argo Books recognizes a Stripe payout deposit on your bank statement and skips it automatically, since the individual sales were already recorded through the Stripe integration.',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Connect your Stripe account to Argo Books with a read-only key. Every sale imports with the product, customer, tax, and discount already filled in, and Stripe fees are tracked automatically.">
    <meta name="keywords" content="Stripe integration, connect Stripe to accounting software, Stripe sales import, Stripe fees tracking, Stripe refunds accounting, Stripe bookkeeping software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Stripe Integration | Argo Books">
    <meta property="og:description" content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
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
    <meta name="twitter:description" content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/integrations/stripe/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Integrations" => "/integrations/", "Stripe Integration" => "/integrations/stripe/"]) ?></script>

    <!-- FAQ Schema, built from the same array as the accordion further down -->
    <script type="application/ld+json"><?= argo_faq_schema($faqs) ?></script>

    <!-- SoftwareApplication Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, Linux",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Connect your Stripe account to Argo Books with a read-only key. Every sale imports with the product, customer, tax, and discount already filled in, and Stripe fees are tracked automatically.",
            "featureList": "Stripe payment collection, Automatic payment sync, Payout matching, Card and wallet support"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Stripe Integration | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>
    <!-- Mockup animations, shared with the landing and comparison pages. -->
    <script src="../../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <?= argo_font_links('editorial', '    ') ?>
    <link rel="stylesheet" href="../../resources/styles/typography.css">
    <link rel="stylesheet" href="../../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../../features/feature-page.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
    </header>
    <main>

    <!-- =============================================
         HERO
         ============================================= -->
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>Take card payments.<br>Keep your books straight.</h1>
                    <p class="fp-hero-sub">Connect your own Stripe account and send invoices with a pay link attached. When the money lands, the payment is already recorded against the right invoice.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and you keep your own Stripe account and its rates.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="stripe">
                    <?= argo_feature_demo('stripe') ?>
                </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">How it works</div>
                <h2 class="fp-h2">Three steps, and it runs itself</h2>
                <p class="fp-lede">The awkward part of card payments is not taking them, it is matching the payout to the invoice afterwards.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Connect your Stripe account</h3>
                    <p>You link the account you already have. Argo Books never holds your funds and never sits between you and your money.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Send invoices with a pay link</h3>
                    <p>Your customer pays by card, Apple Pay or Google Pay without creating an account or calling you back.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Watch it settle itself</h3>
                    <p>The payment is recorded against the invoice, the invoice flips to paid, and the revenue lands in your books.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRODUCT BLOCK
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">After the payment</div>
                    <h2 class="fp-h2">Payouts matched to the invoices they came from</h2>
                    <p class="fp-lede">Stripe pays out in batches, which is where most people lose the thread between what a customer paid and what hit the bank. Argo Books keeps the link, so a payout can be traced back to the invoices inside it and your records agree with your statement.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Payments recorded against the invoice automatically</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Processor fees captured rather than quietly absorbed</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Your own Stripe account, your own rates and payout schedule</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/stripe-sync-confirm.svg"
                         alt="Argo Books matching a Stripe payout back to the individual invoices it settled"
                         loading="lazy" width="600" height="500">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         The page's one mid-page CTA.
         ============================================= -->
    <section class="fp-midcta">
        <div class="fp-wrap fp-midcta-in">
            <div>
                <h2>Start taking card payments</h2>
                <p>No account, no credit card, and you connect the Stripe account you already have.</p>
            </div>
            <a href="../../downloads/" class="fp-btn fp-btn-primary">
                <span>Download free</span>
                <?= svg_icon('arrow-right', 17) ?>
            </a>
        </div>
    </section>

    <!-- =============================================
         BENEFITS
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Why it matters</div>
                <h2 class="fp-h2">What changes when payment and bookkeeping are the same step</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('credit-card', 20) ?></div>
                    <h3>Paying you takes one click</h3>
                    <p>A pay link in the invoice removes the gap where a customer means to pay and then forgets for three weeks.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>No matching payouts by hand</h3>
                    <p>The link between a payment and its invoice is kept for you, so a batched payout is traceable instead of a puzzle.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Fees show up in the numbers</h3>
                    <p>Processing costs are recorded rather than silently eaten, so your margins reflect what you actually netted.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('shield', 20) ?></div>
                    <h3>It stays your account</h3>
                    <p>Argo Books connects to your Stripe, it does not resell it. Your rates, your payouts, your relationship.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRIVACY
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-split-flip fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">Privacy</div>
                    <h2 class="fp-h2">Your books stay on your computer</h2>
                    <p class="fp-lede">Argo Books is a desktop application, not a cloud service holding your finances on someone else's server. Your records are written to your own machine, and you can back them up or move them like any other file.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Records and documents stored locally</span></li>
                        <li><?= svg_icon('check', 17) ?><span>No third-party cloud storage of your financial data</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Your data moves and backs up like any other file</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/privacy-local-storage.svg"
                         alt="The Argo Books folder open on a local disk, showing receipts, invoices and the database file stored on this computer"
                         loading="lazy" width="600" height="500">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHO IT'S FOR
         ============================================= -->
    <section class="fp-section-tight">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Who it's for</div>
                <h2 class="fp-h2">Built for the way you actually work</h2>
            </div>
            <div class="fp-who fp-reveal">
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Freelancers</h3>
                    <p>Get paid by card without asking clients for a bank transfer.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Take payment on completion rather than waiting on terms.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Card volume that reconciles itself against your books.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Anyone chasing payment</h3>
                    <p>Removing friction from paying you is the cheapest way to get paid sooner.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         FAQ. Accordion and schema both come from $faqs.
         ============================================= -->
    <section class="fp-faq">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Questions</div>
                <h2 class="fp-h2">Before you download</h2>
            </div>
            <?= argo_faq_grid($faqs) ?>
        </div>
    </section>

    <!-- =============================================
         RELATED
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-head-c fp-reveal">
                <div class="fp-eyebrow fp-eyebrow-c">Works with</div>
                <h2 class="fp-h2">What Stripe connects to</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../../features/invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Send an invoice with a payment link and watch it flip to paid.</p>
                </a>
                <a href="../../features/expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Card payments land in your revenue records automatically.</p>
                </a>
                <a href="../../features/customer-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('users', 20) ?></div>
                    <h3>Customer management</h3>
                    <p>Payment history kept against the customer who made it.</p>
                </a>
                <a href="../../features/report-builder/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('report', 20) ?></div>
                    <h3>Report builder</h3>
                    <p>Turn settled payments into statements and summaries.</p>
                </a>
            </div>
        </div>
    </section>

    </main>

    <!-- Final CTA and footer share one dark block. dark-section-wrapper is what
         lets the footer's orbs bleed up past the footer's own box. -->
    <div class="dark-section-wrapper fp-outro">
        <section class="fp-outro-cta cta-section">
            <div class="fp-wrap">
                <h2>Get paid the way your customers want to pay</h2>
                <p>Download Argo Books and connect your Stripe account today. Free plan, no credit card, and your data stays on your own machine.</p>
                <div class="fp-btns">
                    <a href="../../downloads/" class="fp-btn fp-btn-primary">
                        <span>Download free</span>
                        <?= svg_icon('arrow-right', 17) ?>
                    </a>
                    <a href="../../pricing/" class="fp-btn fp-btn-onnavy">
                        <span>See pricing</span>
                    </a>
                </div>
            </div>
        </section>

        <footer class="footer">
            <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
        </footer>
    </div>

    <script defer src="../../resources/scripts/reveal.js"></script>
</body>

</html>
