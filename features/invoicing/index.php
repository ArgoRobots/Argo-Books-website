<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];

// One array drives both the visible accordion and the FAQPage schema.
$faqs = [
    [
        'q' => 'Can customers pay invoices online through Argo Books?',
        'a' => 'Yes. Every invoice includes a secure online payment link so your customers can pay by credit card with a single click. Argo Books supports Stripe, Square, and PayPal. You choose which payment gateway works best for your business. Payments are tracked automatically, so you always know which invoices are outstanding and which have been paid.',
    ],
    [
        'q' => 'Can I customize how my invoices look?',
        'a' => 'Yes. Invoices are sent via professional email templates that include your company logo, billing details, and itemized line items. You can choose from multiple templates and customize the content to match your brand. Every invoice looks polished and professional, with no design skills required.',
    ],
    [
        'q' => 'How does invoice tracking work?',
        'a' => 'Argo Books tracks every invoice from draft to paid with color-coded status badges so you can see where things stand at a glance. Summary cards on the invoicing dashboard show your outstanding, paid, and overdue totals in real time. You\'ll never have to wonder whether a client has paid. It\'s all right there.',
    ],
    [
        'q' => 'How many invoices can I send per month?',
        'a' => 'The Free plan includes <?= $argo_free_invoice_limit ?> invoices per month, which is plenty for most small businesses and freelancers getting started. If you need unlimited invoicing, the Premium plan removes all limits so you can send as many invoices as your business requires.',
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
    <meta name="description" content="Create professional invoices with Argo Books. Customizable templates, automatic line-item calculations, online payment links, and payment tracking that help you get paid faster.">
    <meta name="keywords" content="invoice software, invoice generator, professional invoicing, small business invoicing, invoice templates, online invoice payments, invoice tracking, send invoices, payment reminders, invoice management">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Invoicing | Argo Books">
    <meta property="og:description" content="Create professional invoices with Argo Books. Customizable templates, payment tracking, and online payment links that help you get paid faster.">
    <meta property="og:url" content="https://argorobots.com/features/invoicing/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Invoicing | Argo Books">
    <meta name="twitter:description" content="Create professional invoices with Argo Books. Customizable templates, payment tracking, and online payment links that help you get paid faster.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/invoicing/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Invoicing" => "/features/invoicing/"]) ?></script>

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
            "description": "Create professional invoices with Argo Books. Customizable templates, automatic line-item calculations, online payment links, and payment tracking that help you get paid faster.",
            "featureList": "Invoice creation and templates, Online payment collection, Payment reminders, Invoice status tracking"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Invoicing | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>
    <!-- Mockup animations, shared with the landing and comparison pages. -->
    <script src="../../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&amp;family=IBM+Plex+Mono:wght@400;500;600&amp;family=IBM+Plex+Sans:wght@400;500;600;700&amp;display=swap">
    <link rel="stylesheet" href="../../resources/styles/typography.css">
    <link rel="stylesheet" href="../../resources/styles/feature-tour.css">
    <link rel="stylesheet" href="../feature-page.css">
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
                    <h1>Send it today.<br>Get paid this week.</h1>
                    <p class="fp-hero-sub">Build an invoice in a couple of minutes, send it with a payment link attached, and watch it move from sent to paid without chasing anyone by email.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your invoices stay on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="invoices">
                    <?= argo_feature_demo('invoices') ?>
                </div>
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
                <h2 class="fp-h2">Three steps, about two minutes</h2>
                <p class="fp-lede">The gap between finishing the work and getting paid is mostly admin. This removes the admin.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Fill in the work</h3>
                    <p>Pick the customer, add line items, and let the totals and tax work themselves out. Saved details fill most of it in for you.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Choose how it looks</h3>
                    <p>Swap the template and the accent colour to something that matches your business, then preview exactly what your customer will open.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Send it with a payment link</h3>
                    <p>The invoice goes out with a link to pay by card. You see the status change the moment they do.</p>
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
                    <div class="fp-eyebrow">After you send</div>
                    <h2 class="fp-h2">Know what is paid, and what is not</h2>
                    <p class="fp-lede">Every invoice carries a status: draft, sent, viewed, paid, overdue. The dashboard totals what is outstanding so you can see at a glance who owes you and for how long, and send a reminder without writing the email yourself.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Outstanding and overdue totals on one screen</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Automatic payment reminders on the schedule you set</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Card payments through Stripe, PayPal or Square</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/invoice-dashboard.svg"
                         alt="The Argo Books invoice dashboard showing outstanding, paid and overdue totals with a list of recent invoices"
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
                <h2>Send your first invoice in about two minutes</h2>
                <p>No account, no credit card, and no setup before you can bill someone.</p>
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
                <h2 class="fp-h2">What changes when invoicing is not a chore</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Billing stops slipping to the weekend</h3>
                    <p>When an invoice takes two minutes instead of half an hour, it goes out the day the work finishes rather than whenever you get around to it.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('credit-card', 20) ?></div>
                    <h3>Paying you takes one click</h3>
                    <p>A payment link in the invoice removes the step where your customer means to pay you and then forgets for three weeks.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('bell', 20) ?></div>
                    <h3>Reminders you do not have to write</h3>
                    <p>Overdue invoices chase themselves on the schedule you set, so you are not the one sending the awkward email.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>The numbers match your books</h3>
                    <p>A paid invoice becomes revenue in your records automatically, so what you billed and what you banked stay in step.</p>
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
                    <p>Bill by project or by hour, and see which clients pay on time and which ones need a reminder.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Quote, invoice and collect from the same record, without a separate app for each step.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and wholesale</h3>
                    <p>Invoice trade customers on terms while retail sales settle immediately.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Anyone with a slow payer</h3>
                    <p>Overdue totals and automatic reminders make the follow-up routine instead of personal.</p>
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
                <h2 class="fp-h2">What invoicing connects to</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../customer-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('users', 20) ?></div>
                    <h3>Customer management</h3>
                    <p>Contacts, purchase history and balances, so a new invoice starts half filled in.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Paid invoices land in your revenue records without a second entry.</p>
                </a>
                <a href="../report-builder/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('report', 20) ?></div>
                    <h3>Report builder</h3>
                    <p>Turn what you have billed into statements you can hand to an accountant.</p>
                </a>
                <a href="../../invoice-generator/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Free invoice generator</h3>
                    <p>Make a one-off invoice in your browser without installing anything.</p>
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
                <h2>Stop waiting to get paid</h2>
                <p>Download Argo Books and send your first invoice today. Free plan, no credit card, and your data stays on your own machine.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var targets = document.querySelectorAll('.fp-reveal');
            if (!('IntersectionObserver' in window) ||
                window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                targets.forEach(function (el) { el.classList.add('is-in'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            targets.forEach(function (el) { observer.observe(el); });
        });
    </script>
</body>

</html>
