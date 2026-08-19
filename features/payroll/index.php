<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];
$argo_yearly = (int) get_pricing_config()['premium_yearly_price'];

// One array drives both the visible accordion and the FAQPage schema, so the
// two cannot drift apart.
//
// Several of these answers say what payroll does NOT do: it does not file, it
// does not remit, it is not an ROE, and it is Canada only. That is deliberate.
// Payroll is the one feature where someone who misreads the scope finds out at
// a filing deadline, and a refund is the cheapest thing that goes wrong then.
$faqs = [
    [
        'q' => 'Which provinces and territories does payroll cover?',
        'a' => 'All of them. Argo Books calculates federal and provincial income tax, CPP and EI for every province and territory, and Quebec is handled through its own system: QPP, QPIP, Quebec income tax and the federal abatement, with RL-1 slips at year end. Payroll is Canada only, so it cannot pay staff in the United States or anywhere else.',
    ],
    [
        'q' => 'Is payroll included in the Free plan?',
        'a' => "No, payroll is a Premium feature. Premium is \${$argo_monthly} CAD a month, or \${$argo_yearly} a year, and includes everything else Premium covers rather than being priced per employee or per pay run. Most payroll services charge a monthly base fee plus a few dollars per person on top, so the comparison is worth doing with your own headcount.",
    ],
    [
        'q' => 'Where do the tax figures come from?',
        'a' => 'From the CRA\'s own payroll deduction formulas, and from Revenu Quebec for Quebec. None of the rates are written into the app: each edition is a data file that Argo Books checks and loads, and every figure is verified against the published tables before it ships.',
    ],
    [
        'q' => 'The CRA changes the rates twice a year. What do I have to do?',
        'a' => 'Nothing. New rates take effect on 1 January and 1 July each year, and Argo Books fetches the new edition the first time you run a pay run that needs it. There is no update to install and no table to type in. If it cannot get the new edition, it tells you and declines to calculate rather than quietly using last period\'s figures.',
    ],
    [
        'q' => 'Does Argo Books file my T4s or send money to the CRA?',
        'a' => 'No. It prepares the T4 slips and summary as PDFs and builds the XML file the CRA accepts, so the figures are worked out and the file is ready, but you upload it and you make the payment yourself. Argo Books also tells you what is owed and the date it is due, so the remittance is not a number you have to work out.',
    ],
    [
        'q' => 'Can it produce a Record of Employment?',
        'a' => 'It produces the worksheet, not the ROE itself. Service Canada issues ROEs through ROE Web, so a printed sheet is not a filing. What Argo Books does is gather the earnings and hours for the right number of pay periods, which is the part that otherwise means adding up 27 periods by hand on a five day deadline.',
    ],
    [
        'q' => 'Can I pay hourly staff, or people on different schedules?',
        'a' => 'Yes. Each employee is salaried or hourly, on a weekly, biweekly, semi-monthly or monthly schedule, and one pay run can include people on different provinces and different rates. Bonuses and vacation pay are entered on the run itself.',
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
    <meta name="description" content="Run Canadian payroll in Argo Books. CPP, EI and income tax worked out from the CRA's own tables for every province, pay stubs for your staff, and T4, RL-1 and ROE paperwork at year end.">
    <meta name="keywords" content="Canadian payroll software, small business payroll Canada, CPP EI calculator, T4 software, payroll deductions Canada, RL-1 Quebec payroll, record of employment, desktop payroll software, T4 XML filing">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Payroll for Canadian Businesses | Argo Books">
    <meta property="og:description" content="Pay your staff without a separate payroll service. CPP, EI and income tax from the CRA's own tables, pay stubs, and T4s at year end.">
    <meta property="og:url" content="https://argorobots.com/features/payroll/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Payroll for Canadian Businesses | Argo Books">
    <meta name="twitter:description" content="Pay your staff without a separate payroll service. CPP, EI and income tax from the CRA's own tables, pay stubs, and T4s at year end.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/payroll/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Payroll" => "/features/payroll/"]) ?></script>

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
                "price": "<?= $argo_monthly ?>",
                "priceCurrency": "CAD",
                "description": "Payroll is included with Premium at $<?= $argo_monthly ?>/month. Free plan available for the rest of Argo Books."
            },
            "description": "Run Canadian payroll in Argo Books. CPP, EI and income tax worked out from the CRA's own tables for every province, pay stubs for your staff, and T4, RL-1 and ROE paperwork at year end.",
            "featureList": "Canadian payroll deductions, Pay stubs, T4 slips and XML, RL-1 slips for Quebec, Record of Employment worksheet"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Payroll for Canadian Businesses | Argo Books</title>

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
                    <h1>Pay your staff,<br>without a payroll service.</h1>
                    <p class="fp-hero-sub">Argo Books works out CPP, EI and income tax from the CRA's own tables, prints the pay stubs, posts the wages to your books, and has your T4s ready in January.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Canadian payroll, on Premium at $<?= $argo_monthly ?> CAD a month. Every figure is worked out on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="payroll">
                    <?= argo_feature_demo('payroll') ?>
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
                <h2 class="fp-h2">Three steps, every payday</h2>
                <p class="fp-lede">You enter what someone earned. Everything after that is arithmetic, and the arithmetic is the part you are paying somebody else for today.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Add your people once</h3>
                    <p>Province, salary or hourly rate, how often they are paid, and the claim amounts from their TD1. That is the setup, and it is the last time you type it.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Enter the pay period</h3>
                    <p>Hours for anyone hourly, plus any bonus or vacation pay. Deductions and net pay appear as you type, for everyone on the run at once.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Approve it</h3>
                    <p>Pay stubs are ready to hand over, the wages land in your books as expenses, and you are told what the CRA is owed and when.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRODUCT BLOCK: the deduction engine
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">The figures</div>
                    <h2 class="fp-h2">Every province and territory, Quebec included</h2>
                    <p class="fp-lede">Quebec administers its own income tax, pension plan and parental insurance, so it is a second calculation rather than a variation on the first one. Argo Books does both. Everywhere else, CPP and the second CPP contribution, EI, federal tax and provincial tax are each tracked against their annual maximums, so contributions stop in the period they are meant to stop rather than at the end of the year.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Every province and territory, and Quebec's separate QPP and QPIP</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Annual maximums tracked, so a ceiling reached mid-period is handled</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Salaried or hourly, weekly through to monthly, mixed in one run</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Bonuses taxed as one-off pay rather than as a raise</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/payroll-deductions.svg"
                         alt="An Argo Books pay stub showing gross pay, the CPP, EI, federal and provincial tax deducted, and the resulting net pay"
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
                <h2>Payroll without the per-employee bill</h2>
                <p>Included with Premium at $<?= $argo_monthly ?> a month, however many people you pay.</p>
            </div>
            <a href="../../pricing/" class="fp-btn fp-btn-primary">
                <span>See pricing</span>
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
                <h2 class="fp-h2">The parts of payroll that go wrong quietly</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('calendar-dots', 20) ?></div>
                    <h3>You are told what is due, and when</h3>
                    <p>A regular remitter pays by the 15th of the month after payday. Argo Books names that date and the amount that belongs to it, rather than leaving you to work out which month you are paying for.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('refresh', 20) ?></div>
                    <h3>Rate changes arrive on their own</h3>
                    <p>The CRA reissues its tables every January and July. The new edition is fetched the first time you need it, so there is nothing to install and no chance of running January on last year's figures.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('shield-check', 20) ?></div>
                    <h3>It stops rather than guesses</h3>
                    <p>If it does not hold the table covering a pay date, it says so and refuses to calculate. A wrong deduction on someone's pay is the kind of error nothing downstream catches.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>The wages are already in your books</h3>
                    <p>Approving a run records what each person was actually paid as an expense, so payday matches your bank statement instead of being a figure you copy across from somewhere else.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRODUCT BLOCK: year end
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-split-flip fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">Year end</div>
                    <h2 class="fp-h2">Nothing to add up in January</h2>
                    <p class="fp-lede">The slips are built from the pay runs you already approved, so there is no year to reassemble: T4s as PDFs to hand out and as the XML file the CRA accepts, and RL-1 slips for Quebec staff from the same screen. You still upload the file and make the payment yourself.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>T4 slips and summary as PDFs, plus the CRA's XML with its transmittal record</span></li>
                        <li><?= svg_icon('check', 17) ?><span>RL-1 slips and summary for Quebec employees</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Amendments and cancellations chosen per employee, not all or nothing</span></li>
                        <li><?= svg_icon('check', 17) ?><span>A Record of Employment worksheet when somebody leaves</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/payroll-year-end.svg"
                         alt="An Argo Books year end screen showing a T4 slip with its boxes filled in, and the slips, summary and CRA XML file ready to export"
                         loading="lazy" width="600" height="500">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         PRIVACY
         ============================================= -->
    <section class="fp-section">
        <div class="fp-wrap">
            <div class="fp-split fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">Privacy</div>
                    <h2 class="fp-h2">Your staff records stay on your computer</h2>
                    <p class="fp-lede">Payroll holds the most sensitive data in the business: names, social insurance numbers and what everyone earns. Argo Books is a desktop application, so those records are written to your own machine rather than uploaded to a payroll provider, and the deduction figures are calculated there too.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Employee records and pay history stored locally</span></li>
                        <li><?= svg_icon('check', 17) ?><span>No third-party payroll provider holding your staff data</span></li>
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
                <h2 class="fp-h2">Built for a small Canadian payroll</h2>
            </div>
            <div class="fp-who fp-reveal">
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Two or three staff</h3>
                    <p>Where a payroll service's monthly base fee is most of what you would pay.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('user', 19) ?> Owner-managers</h3>
                    <p>Paying yourself a salary, with the CPP and EI exemptions that go with it.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Hourly crews whose hours change every period.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('map-pin', 19) ?> Staff in more than one province</h3>
                    <p>Different provinces on the same pay run, Quebec included.</p>
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
                <h2 class="fp-h2">What payroll touches</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense &amp; revenue tracking</h3>
                    <p>Where the wages land once a run is approved.</p>
                </a>
                <a href="../bank-statement-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('bank', 20) ?></div>
                    <h3>Bank statement import</h3>
                    <p>Payday shows up on the statement already recorded.</p>
                </a>
                <a href="../report-builder/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('report', 20) ?></div>
                    <h3>Report builder</h3>
                    <p>Wages counted in your income statement like any other cost.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>Your largest recurring outgoing, in the forecast.</p>
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
                <h2>Run your next payday in Argo Books</h2>
                <p>Download it free, add your people, and see the deductions before you pay for anything. Payroll is included with Premium at $<?= $argo_monthly ?> CAD a month.</p>
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
