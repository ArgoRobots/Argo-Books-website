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
        'q' => 'How accurate are the revenue forecasts?',
        'a' => 'Argo Books achieves an average of 88% forecast accuracy in backtesting. Every prediction includes a confidence score so you know exactly how reliable it is. The more data Argo Books has to work with, the more accurate forecasts become over time.',
    ],
    [
        'q' => 'Do I need technical skills to use predictive analytics?',
        'a' => 'Not at all. The analytics engine runs automatically in the background with zero configuration. No formulas, no spreadsheets, no data science degree required. Just use Argo Books normally and forecasts are generated from your real business data. Results are presented in clear, visual charts that anyone can understand.',
    ],
    [
        'q' => 'Can Argo Books detect seasonal patterns in my business?',
        'a' => 'Yes. Argo Books automatically detects bi-monthly and seasonal cycles in your revenue and expenses, and factors these patterns into every forecast. This means your projections account for predictable fluctuations like holiday rushes or slow summer months, giving you a more realistic picture of what\'s ahead.',
    ],
    [
        'q' => 'Is predictive analytics included in the Free plan?',
        'a' => 'Basic real-time analytics are included in the Free plan. Predictive analytics, including revenue forecasting, trend detection, and confidence scoring, is a Premium feature. It\'s one of the most powerful reasons to upgrade, especially for businesses that want to plan ahead with data-driven insights.',
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
    <meta name="description" content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta name="keywords" content="predictive analytics, financial forecasting, business analytics, sales trend forecasting, ML business analytics, revenue forecasting software, expense prediction, seasonal pattern detection, machine learning forecasting, small business analytics">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Predictive Analytics | Argo Books">
    <meta property="og:description" content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta property="og:url" content="https://argorobots.com/features/predictive-analytics/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Predictive Analytics | Argo Books">
    <meta name="twitter:description" content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/predictive-analytics/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Predictive Analytics" => "/features/predictive-analytics/"]) ?></script>

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
            "description": "Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.",
            "featureList": "Cash flow forecasting, Revenue and expense projections, Confidence ranges, Trend analysis"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Predictive Analytics | Argo Books</title>

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
                    <h1>See next month<br>before it arrives.</h1>
                    <p class="fp-hero-sub">Argo Books reads the history already in your books and projects where revenue, expenses and cash are heading, with an honest range around the estimate.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and the analysis runs on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="predictive">
                    <?= argo_feature_demo('predictive') ?>
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
                <h2 class="fp-h2">Three steps, and it keeps itself current</h2>
                <p class="fp-lede">A forecast is only useful if it is built from what actually happened. This one is, and it updates as the month goes on.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Keep recording as normal</h3>
                    <p>Invoices, expenses and payments are all the input the forecast needs. There is nothing extra to fill in.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>It finds the pattern</h3>
                    <p>Seasonality, growth and recurring costs are picked out of your own history rather than an industry average.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Read the range, not just the line</h3>
                    <p>Every projection comes with a confidence band, so you can see how sure it is before you spend against it.</p>
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
                    <div class="fp-eyebrow">Honest numbers</div>
                    <h2 class="fp-h2">A forecast that admits what it does not know</h2>
                    <p class="fp-lede">A single confident line is easy to draw and easy to be wrong about. Argo Books shows the projection inside a band that widens the further out it looks, so a quiet month reads as a risk rather than a surprise.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Projections built from your own transaction history</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Confidence range that widens with distance</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Revenue, expenses and net cash flow each projected</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/analytics-dashboard.svg"
                         alt="The Argo Books analytics dashboard showing a cash flow forecast with a confidence band around the projection"
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
                <h2>Find out where the next quarter is heading</h2>
                <p>No account, no credit card, and no data science required.</p>
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
                <h2 class="fp-h2">What changes when you can see ahead</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>Slow months stop being surprises</h3>
                    <p>A dip you can see coming six weeks out is a planning problem. The same dip discovered on the day is a cash problem.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>You can time the big spends</h3>
                    <p>Knowing what cash looks like in eight weeks is the difference between buying the equipment now and buying it after the quiet season.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('bolt', 20) ?></div>
                    <h3>The picture updates as you work</h3>
                    <p>Every transaction you record sharpens the projection, so it is never based on last quarter alone.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>No guessing at the inputs</h3>
                    <p>It reads your real invoices and expenses, so the forecast reflects your business rather than a generic template.</p>
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
                    <h3><?= svg_icon('calendar', 19) ?> Seasonal businesses</h3>
                    <p>See the quiet stretch coming while there is still time to do something about it.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Freelancers</h3>
                    <p>Spot the gap between projects before it becomes an empty month.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Plan stock and cash around what demand has actually done, not what you hope it does.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Growing businesses</h3>
                    <p>Decide whether the next hire or the next van is affordable before committing.</p>
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
                <h2 class="fp-h2">What the forecast is built from</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>The transaction history every projection is calculated from.</p>
                </a>
                <a href="../invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Outstanding invoices feed expected income into the forecast.</p>
                </a>
                <a href="../report-builder/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('report', 20) ?></div>
                    <h3>Report builder</h3>
                    <p>Turn the projection into something you can show a lender or an accountant.</p>
                </a>
                <a href="../bank-statement-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('bank', 20) ?></div>
                    <h3>Bank statement import</h3>
                    <p>More history in means a sharper forecast out.</p>
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
                <h2>Stop running your business on last month</h2>
                <p>Download Argo Books and see where your numbers are heading. Free plan, no credit card, and your data stays on your own machine.</p>
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
