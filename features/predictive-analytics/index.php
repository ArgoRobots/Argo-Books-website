<?php require_once __DIR__ . '/../../resources/icons.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta name="keywords"
        content="predictive analytics, financial forecasting, business analytics, sales trend forecasting, ML business analytics, revenue forecasting software, expense prediction, seasonal pattern detection, machine learning forecasting, small business analytics">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Predictive Analytics — Argo Books">
    <meta property="og:description"
        content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta property="og:url" content="https://argorobots.com/features/predictive-analytics/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Predictive+Analytics&subtitle=See+what%E2%80%99s+coming+before+it+happens.+AI-powered+forecasting+for+your+business.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Predictive Analytics — Argo Books">
    <meta name="twitter:description"
        content="Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Predictive+Analytics&subtitle=See+what%E2%80%99s+coming+before+it+happens.+AI-powered+forecasting+for+your+business.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/predictive-analytics/">

    <!-- Breadcrumb Schema -->
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

    <!-- FAQ Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [
                {
                    "@type": "Question",
                    "name": "How accurate are the revenue forecasts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books achieves an average of 88% forecast accuracy in backtesting. Every prediction includes a confidence score so you know exactly how reliable it is. The more data Argo Books has to work with, the more accurate forecasts become over time."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Do I need technical skills to use predictive analytics?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Not at all. The analytics engine runs automatically in the background with zero configuration — no formulas, no spreadsheets, no data science degree required. Just use Argo Books normally and forecasts are generated from your real business data. Results are presented in clear, visual charts that anyone can understand."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can Argo Books detect seasonal patterns in my business?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Argo Books automatically detects bi-monthly and seasonal cycles in your revenue and expenses, and factors these patterns into every forecast. This means your projections account for predictable fluctuations like holiday rushes or slow summer months, giving you a more realistic picture of what's ahead."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is predictive analytics included in the Free plan?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Basic real-time analytics are included in the Free plan. Predictive analytics — including revenue forecasting, trend detection, and confidence scoring — is a Premium feature. It's one of the most powerful reasons to upgrade, especially for businesses that want to plan ahead with data-driven insights."
                    }
                }
            ]
        }
    </script>

    <!-- SoftwareApplication Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Argo Books",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Windows, macOS",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "CAD",
                "description": "Free plan available. Premium for $10/month."
            },
            "description": "Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.",
            "featureList": "AI-powered revenue and expense forecasting, Seasonal pattern detection, Accuracy tracking with confidence scores, Plain-language business insights"
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

    <!-- =============================================
         HERO SECTION
         ============================================= -->
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
            <h1 class="animate-fade-in">See what's coming before it happens</h1>
            <p class="hero-subtitle animate-fade-in">AI-powered forecasting analyzes your data to predict trends, detect seasonal patterns, and surface insights that help you make smarter business decisions — automatically.</p>
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

    <!-- =============================================
         DETAIL SECTION 1: The Problem + Solution
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">The Problem</span>
                    <h2>Most small businesses are flying blind on finances</h2>
                    <p>You know what happened last month. But do you know what's coming next month? Most small business owners make financial decisions based on gut feeling, not data. Argo Books changes that by using machine learning to forecast your future revenue, expenses, and profit — based on your actual business data.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Forecast next month's revenue, expenses, and profit automatically</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Detect seasonal patterns in your business cycle</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Get actionable insights — not just charts and numbers</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No data science skills needed — Argo Books handles everything</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/analytics-dashboard.svg" alt="Argo Books predictive analytics dashboard showing forecasted revenue, expenses, and profit with trend chart" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>See what's coming — with Premium</h3>
                <p>Predictive analytics is included in Argo Books Premium for $10/month. Download for free, add your data, and upgrade whenever you're ready to forecast.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Download Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../../pricing/" class="btn-cta btn-cta-outline">
                        <span>See Pricing</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         STATS BANNER
         ============================================= -->
    <section class="highlight-banner">
        <div class="container">
            <div class="highlight-grid animate-on-scroll">
                <div class="highlight-item">
                    <h3>88%</h3>
                    <p>Average forecast accuracy in backtesting</p>
                </div>
                <div class="highlight-item">
                    <h3>9</h3>
                    <p>Insights per analysis cycle</p>
                </div>
                <div class="highlight-item">
                    <h3>0</h3>
                    <p>Configuration required — works out of the box</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: How the ML works
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Under the Hood</span>
                    <h2>Machine learning that adapts to your business</h2>
                    <p>Our forecasting system uses multiple ML algorithms to predict future revenue, expenses, and customer growth. It continuously tests predictions against actual outcomes and adapts its methods based on what works best for your specific business.</p>
                    <p>The system doesn't just look at averages. It detects whether your business is growing, stable, or declining. It identifies seasonal cycles — like higher sales in December or slower months in summer — and factors these patterns into every forecast.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multiple ML algorithms for seasonal forecasting</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic trend detection — growing, stable, or declining</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Bi-monthly and seasonal cycle detection built in</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Confidence scores on every prediction so you know how reliable it is</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/analytics-ml-engine.svg" alt="How predictions work — seasonal pattern detection, trend analysis, adaptive model selection, and confidence scoring" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS — 3 Steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">From raw data to actionable forecasts</h2>
                <p class="section-desc">No setup, no configuration, no data science degree required. Just use Argo Books normally and the analytics engine works in the background.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Use Argo Books normally</h3>
                    <p>Record your data as you normally would. The more data you have, the better the forecasts become.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>ML analyzes your patterns</h3>
                    <p>The analytics engine processes your historical data, detects trends and seasonal patterns, and builds forecasting models tuned to your business.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Get forecasts and insights</h3>
                    <p>View next-month predictions for revenue, expenses, and profit. Get business insights, anomaly alerts, and growth opportunities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop guessing about next month</h3>
                <p>Let Argo Books forecast your revenue, flag anomalies, and surface opportunities you'd otherwise miss.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 3: Business Insights
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Smart Insights</span>
                    <h2>Insights that actually help</h2>
                    <p>Predictive analytics in Argo Books doesn't just show you charts. It generates plain-language business insights by analyzing your data across multiple dimensions — revenue trends, expense patterns, anomalies, and growth opportunities.</p>
                    <p>Every insight comes with a specific recommendation. If revenue is growing, it tells you to investigate which products drove it. If return rates spike, it flags the affected products. If a seasonal peak is approaching, it suggests preparing inventory and marketing in advance.</p>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/analytics-insights.svg" alt="Argo Books business insights panel showing revenue trends, anomaly detection, and opportunities" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 4: Accuracy Tracking
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Accuracy You Can Verify</span>
                    <h2>Every prediction is tested against reality</h2>
                    <p>Most forecasting tools give you a number and ask you to trust it. Argo Books shows you exactly how accurate its predictions have been — so you can decide how much weight to give each forecast.</p>
                    <p>The system tracks every past prediction and compares it to what actually happened. You can see the forecast vs. actual revenue and expenses for each month, along with the accuracy. Over time, the model improves as it learns from its own performance.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Backtesting validates predictions against historical data</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Confidence scores tell you how reliable each prediction is</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Model adapts over time — accuracy improves as more data comes in</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/analytics-accuracy.svg" alt="Past predictions accuracy table showing forecast vs actual results with 88% overall accuracy" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         BENEFITS GRID — 6 benefit cards
         ============================================= -->
    <section class="benefits-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Why It Matters</span>
                <h2 class="section-title">More than just forecasting</h2>
                <p class="section-desc">Predictive analytics transforms how you plan, budget, and grow your business.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Plan ahead with confidence</h3>
                    <p>Know what next month's revenue and expenses are likely to be before the month starts. Make hiring, inventory, and marketing decisions based on data, not gut feeling.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>Catch problems early</h3>
                    <p>Anomaly detection flags unusual patterns — like a spike in product returns or an unexpected expense increase — before they become bigger issues.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Understand your seasonality</h3>
                    <p>Automatically detect bi-monthly and seasonal patterns in your business. Know when your peak months are and prepare accordingly — stock up, hire temp staff, or boost marketing.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Zero setup required</h3>
                    <p>No configuration, no parameters to tune, no data science knowledge needed. The analytics engine works automatically in the background as you use Argo Books normally.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Transparent accuracy</h3>
                    <p>Every forecast includes a confidence score and historical accuracy data. You can see exactly how reliable the predictions are for your specific business — no black boxes.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('star', 22) ?>
                    </div>
                    <h3>Surface hidden opportunities</h3>
                    <p>Argo Books identifies high-margin products, customer retention opportunities, and approaching seasonal peaks — insights you'd miss from looking at raw numbers alone.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Make data-driven decisions</h3>
                <p>Join small business owners who replaced gut-feeling budgeting with Argo Books.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Download Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="../" class="btn-cta btn-cta-outline">
                        <span>View All Features</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         USE CASES SECTION
         ============================================= -->
    <section class="use-cases-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Who It's For</span>
                <h2 class="section-title">Built for business owners who want to plan smarter</h2>
                <p class="section-desc">Whether you're a solo freelancer or running a growing retail operation, predictive analytics helps you stay ahead.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Retail &amp; e-commerce
                    </h3>
                    <p>Forecast demand to plan inventory purchases. Know when seasonal peaks are coming so you can stock up in advance instead of scrambling. Identify which products have the highest margins and focus your marketing there.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Freelancers &amp; consultants
                    </h3>
                    <p>Predict income fluctuations so you can plan for lean months. See revenue trends over time to identify whether your business is growing or plateauing. Make informed decisions about raising rates or taking on new clients.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Service businesses
                    </h3>
                    <p>Understand your busy and slow seasons with data, not memory. Forecast expenses to budget for equipment, materials, and subcontractors. Catch expense anomalies before they eat into your profit.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('dollar', 22) ?>
                        Anyone planning a budget
                    </h3>
                    <p>Stop building budgets from scratch every month. Let ML project your expected revenue and expenses, then adjust based on what you know. The forecast gives you a starting point grounded in real data.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT MAKES IT SMART — 6 capability cards
         ============================================= -->
    <section class="benefits-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">What Makes It Smart</span>
                <h2 class="section-title">Six capabilities working together</h2>
                <p class="section-desc">Not a single algorithm — a system of interconnected capabilities that produce accurate, useful forecasts.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('calendar', 22) ?>
                    </div>
                    <h3>Seasonal pattern detection</h3>
                    <p>Detects recurring cycles like holiday spikes or summer slowdowns and adjusts forecasts automatically.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Trend analysis</h3>
                    <p>Factors in whether your business is growing, stable, or declining — so predictions aren't just based on averages.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('circle-check', 22) ?>
                    </div>
                    <h3>Accuracy tracking</h3>
                    <p>Compares past predictions to actual results so you can see how reliable the forecasts are over time.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Confidence scoring</h3>
                    <p>Every prediction includes a confidence percentage so you know when to trust the forecast and when to be cautious.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Adaptive learning</h3>
                    <p>Automatically selects the best forecasting model for your data and refines itself as more months come in.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Forecast range</h3>
                    <p>Gives you a predicted range (e.g., $12,554 – $18,831) so you can plan for both optimistic and conservative scenarios.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 6: Privacy & Security
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your financial data never leaves your computer</h2>
                    <p>Unlike cloud-based analytics platforms that upload your financial data to third-party servers, Argo Books runs everything on your machine — not on someone else's cloud.</p>
                    <p>The ML processes your data directly on your computer. No data is sent to external servers for analysis. You get enterprise-grade predictive analytics with the privacy of a desktop application.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>All analytics processing happens locally on your device</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No financial data uploaded to cloud servers for analysis</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Forecasts and insights stored alongside your existing data</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Works offline — generate forecasts without internet</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/privacy-local-storage.svg" alt="Your data stays local — encrypted, offline-capable, no cloud" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         RELATED FEATURES
         ============================================= -->
    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Related Features</span>
                <h2 class="section-title">Works great with</h2>
                <p class="section-desc">Predictive analytics is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense & Revenue Tracking</h3>
                    <p>The more expense and revenue data you track, the more accurate your forecasts become. Every transaction feeds the analytics engine.</p>
                </a>
                <a href="../receipt-scanning/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('receipt-scan-detail', 22) ?>
                    </div>
                    <h3>Receipt Scanning</h3>
                    <p>Scan receipts to capture detailed expense data. More granular data means better predictions and more useful anomaly detection.</p>
                </a>
                <a href="../spreadsheet-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>Spreadsheet Import</h3>
                    <p>Import your full transaction history to give the analytics engine a head start. More historical data means more accurate forecasts from day one.</p>
                </a>
            </div>
        </div>
    </section>

    </main>

    <!-- CTA + Footer Wrapper -->
    <div class="dark-section-wrapper">
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to predict your business future?</h2>
                    <p>Download now and let Argo Books forecast your revenue, flag anomalies, and surface growth opportunities.</p>
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