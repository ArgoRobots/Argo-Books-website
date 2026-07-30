<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_cfg = get_pricing_config();
$argo_monthly = (int) $argo_cfg['premium_monthly_price'];
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
        content="Connect your Stripe account to Argo Books with a read-only key. Every sale imports with the product, customer, tax, and discount already filled in, and Stripe fees are tracked automatically.">
    <meta name="keywords"
        content="Stripe integration, connect Stripe to accounting software, Stripe sales import, Stripe fees tracking, Stripe refunds accounting, Stripe bookkeeping software">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Stripe Integration | Argo Books">
    <meta property="og:description"
        content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
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
    <meta name="twitter:description"
        content="Your Stripe sales, in your books, automatically. Connect with a read-only key and every charge, fee, and refund is recorded for you.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/integrations/stripe/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Integrations", "item": "https://argorobots.com/integrations/"},
                {"@type": "ListItem", "position": 3, "name": "Stripe", "item": "https://argorobots.com/integrations/stripe/"}
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
                    "name": "How does the Argo Books Stripe integration work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Connect a restricted, read-only Stripe key under Settings > Integrations, then click Sync now. Argo Books reads your charges, refunds, fees, and customers and turns them into revenue entries, expense entries, and customer records ready to review and import."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can Argo Books move money through my Stripe account?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. The integration uses a restricted, read-only key, so Argo Books can read your payouts and charges but can never issue a charge, send a payout, or change anything in your Stripe account."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Will importing my bank statement double count my Stripe payouts?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books recognizes a Stripe payout deposit on your bank statement and skips it automatically, since the individual sales were already recorded through the Stripe integration."
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
                "description": "Free plan available. Premium for $<?= $argo_monthly ?>/month."
            },
            "description": "Connect Stripe to Argo Books with a restricted, read-only key. Every charge becomes a revenue entry with the product, customer, tax, and discount filled in, fees are tracked as linked expenses, and refunds and bank payouts are handled correctly.",
            "featureList": "Stripe sales import, Automatic fee tracking, Refund handling, Read-only key connection, Bank import duplicate detection"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Stripe Integration | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../../features/style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <!-- Brand typefaces (Fraunces display + IBM Plex Sans body), matched to the rest of the site -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../../resources/styles/typography.css">

    <!-- Page-local addition: a 4-column variant of the shared steps-grid for
         this page's 4-step "How It Works" list (features/style.css only
         defines the 3-column version). Collapses to one column below 900px,
         matching the shared .steps-grid breakpoint. -->
    <style>
        .steps-grid-4 {
            grid-template-columns: repeat(4, 1fr);
        }

        @media (max-width: 900px) {
            .steps-grid-4 {
                grid-template-columns: 1fr;
                max-width: 500px;
                margin: 0 auto;
            }
        }

        /* ---- Stripe flow showpiece (unique to this page) ---- */
        .stripe-flow {
            padding: 110px 0 120px;
            background: linear-gradient(180deg, #ffffff 0%, #f6f7fb 100%);
            overflow: hidden;
        }
        .stripe-flow .flow-scene {
            max-width: 960px;
            margin: 44px auto 0;
        }
        .stripe-flow .flow-scene svg {
            width: 100%;
            height: auto;
            display: block;
        }
        .stripe-flow .flow-caption {
            text-align: center;
            margin-top: 26px;
            font-size: 0.95rem;
            color: var(--gray-500);
        }
        .stripe-flow .flow-caption strong { color: var(--gray-900); font-weight: 600; }

        /* flowing "pipe" between Stripe and Argo */
        .sf-pipe {
            stroke-dasharray: 1 9;
            animation: sfPipe 0.9s linear infinite;
        }
        @keyframes sfPipe { to { stroke-dashoffset: -20; } }

        /* breathing glow behind the Stripe card */
        .sf-glow { animation: sfGlow 3.2s ease-in-out infinite; }
        @keyframes sfGlow { 0%, 100% { opacity: 0.28; } 50% { opacity: 0.55; } }

        /* pulsing ring on the "booked" check */
        .sf-ring {
            transform-box: fill-box;
            transform-origin: center;
            animation: sfRing 2.4s ease-out infinite;
        }
        @keyframes sfRing {
            0% { transform: scale(0.7); opacity: 0.55; }
            70%, 100% { transform: scale(2.9); opacity: 0; }
        }

        /* each booked row's accent lights up in sequence, in time with arrivals */
        .sf-accent {
            transform-box: fill-box;
            transform-origin: center;
            animation: sfAccent 2.4s ease-in-out infinite;
        }
        .sf-accent.d2 { animation-delay: 0.18s; }
        .sf-accent.d3 { animation-delay: 0.36s; }
        .sf-accent.d4 { animation-delay: 0.54s; }
        @keyframes sfAccent { 0%, 62%, 100% { opacity: 0.22; } 20% { opacity: 1; } }

        @media (prefers-reduced-motion: reduce) {
            .sf-pipe, .sf-glow, .sf-ring, .sf-accent { animation: none; }
        }

        @media (max-width: 600px) {
            .stripe-flow { padding: 70px 0 80px; }
        }
    </style>
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
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
            <h1 class="animate-fade-in">Your Stripe sales, in your books, automatically.</h1>
            <p class="hero-subtitle animate-fade-in">Connect your Stripe account with a read-only key and every sale flows in with the right product, customer, tax, and discount.</p>
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
         STRIPE FLOW SHOWPIECE (unique to this page)
         ============================================= -->
    <section class="stripe-flow">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">The magic part</span>
                <h2 class="section-title">One charge in, a full set of books out</h2>
                <p class="section-desc">A Stripe payment lands, and Argo Books turns it into a complete entry, the sale, the processing fee, the customer, and the tax, without you touching a thing.</p>
            </div>

            <div class="flow-scene animate-on-scroll">
                <svg viewBox="0 0 900 430" role="img" aria-label="A Stripe payment flowing into Argo Books and being recorded as revenue, fee, customer, and tax automatically" xmlns="http://www.w3.org/2000/svg" font-family="'IBM Plex Sans', sans-serif">
                    <defs>
                        <linearGradient id="sfStripe" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#8b85ff"/><stop offset="1" stop-color="#635bff"/>
                        </linearGradient>
                        <linearGradient id="sfCoin" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#9d97ff"/><stop offset="1" stop-color="#3f63e8"/>
                        </linearGradient>
                        <linearGradient id="sfBrand" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#3f63e8"/><stop offset="1" stop-color="#2740b5"/>
                        </linearGradient>
                        <radialGradient id="sfScene" cx="50%" cy="45%" r="60%">
                            <stop offset="0" stop-color="#635bff" stop-opacity="0.10"/>
                            <stop offset="1" stop-color="#635bff" stop-opacity="0"/>
                        </radialGradient>
                        <filter id="sfSoft" x="-40%" y="-40%" width="180%" height="180%"><feGaussianBlur stdDeviation="7"/></filter>
                        <filter id="sfShadow" x="-30%" y="-30%" width="160%" height="160%"><feGaussianBlur stdDeviation="9"/></filter>
                        <path id="sfFlow" d="M 316 210 C 404 210 474 150 560 150" fill="none"/>
                    </defs>

                    <ellipse cx="450" cy="212" rx="440" ry="190" fill="url(#sfScene)"/>

                    <!-- ============ Stripe charge card ============ -->
                    <rect class="sf-glow" x="44" y="122" width="276" height="188" rx="22" fill="#635bff" filter="url(#sfSoft)"/>
                    <rect x="48" y="120" width="268" height="188" rx="18" fill="#1a1f36"/>
                    <rect x="70" y="142" width="26" height="26" rx="7" fill="url(#sfStripe)"/>
                    <text x="83" y="161" text-anchor="middle" font-size="15" font-weight="700" fill="#ffffff">S</text>
                    <text x="106" y="161" font-size="14" font-weight="600" fill="#ffffff">Stripe</text>
                    <circle cx="292" cy="155" r="1.7" fill="#6b7394"/><circle cx="298" cy="155" r="1.7" fill="#6b7394"/><circle cx="304" cy="155" r="1.7" fill="#6b7394"/>
                    <text x="70" y="198" font-size="10" font-weight="600" letter-spacing="1" fill="#8b93b8">PAYMENT RECEIVED</text>
                    <text x="70" y="228" font-size="30" font-weight="700" fill="#ffffff">$1,240.00</text>
                    <text x="70" y="258" font-size="13" fill="#aab2d5">&#8226;&#8226;&#8226;&#8226; 4242</text>
                    <text x="294" y="258" text-anchor="end" font-size="12" font-weight="700" fill="#ffffff" letter-spacing="1">VISA</text>
                    <rect x="70" y="272" width="108" height="22" rx="11" fill="#10b981" fill-opacity="0.16"/>
                    <path d="M80 283 l3 3 l6 -7" fill="none" stroke="#34d399" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <text x="96" y="287" font-size="11" font-weight="600" fill="#34d399">Succeeded</text>

                    <!-- ============ flow pipe + coins ============ -->
                    <use href="#sfFlow" stroke="#c9c6ff" stroke-width="2.5" fill="none" opacity="0.7"/>
                    <use class="sf-pipe" href="#sfFlow" stroke="#635bff" stroke-width="2.5" stroke-linecap="round" fill="none"/>

                    <g opacity="0">
                        <circle r="16" fill="url(#sfCoin)" opacity="0.22" filter="url(#sfSoft)"/>
                        <circle r="12" fill="url(#sfCoin)"/>
                        <text x="0" y="4.5" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff">$</text>
                        <animateMotion dur="2.4s" begin="0s" repeatCount="indefinite"><mpath href="#sfFlow"/></animateMotion>
                        <animate attributeName="opacity" dur="2.4s" begin="0s" repeatCount="indefinite" values="0;1;1;1;0" keyTimes="0;0.12;0.5;0.85;1"/>
                    </g>
                    <g opacity="0">
                        <circle r="16" fill="url(#sfCoin)" opacity="0.22" filter="url(#sfSoft)"/>
                        <circle r="12" fill="url(#sfCoin)"/>
                        <text x="0" y="4.5" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff">$</text>
                        <animateMotion dur="2.4s" begin="0.8s" repeatCount="indefinite"><mpath href="#sfFlow"/></animateMotion>
                        <animate attributeName="opacity" dur="2.4s" begin="0.8s" repeatCount="indefinite" values="0;1;1;1;0" keyTimes="0;0.12;0.5;0.85;1"/>
                    </g>
                    <g opacity="0">
                        <circle r="16" fill="url(#sfCoin)" opacity="0.22" filter="url(#sfSoft)"/>
                        <circle r="12" fill="url(#sfCoin)"/>
                        <text x="0" y="4.5" text-anchor="middle" font-size="13" font-weight="700" fill="#ffffff">$</text>
                        <animateMotion dur="2.4s" begin="1.6s" repeatCount="indefinite"><mpath href="#sfFlow"/></animateMotion>
                        <animate attributeName="opacity" dur="2.4s" begin="1.6s" repeatCount="indefinite" values="0;1;1;1;0" keyTimes="0;0.12;0.5;0.85;1"/>
                    </g>

                    <!-- ============ Argo Books window ============ -->
                    <rect x="560" y="86" width="306" height="284" rx="16" fill="#0f172a" opacity="0.10" filter="url(#sfShadow)"/>
                    <rect x="556" y="78" width="306" height="284" rx="16" fill="#ffffff" stroke="#e8edf5"/>
                    <rect x="578" y="96" width="22" height="22" rx="6" fill="url(#sfBrand)"/>
                    <text x="608" y="112" font-family="Fraunces, Georgia, serif" font-size="13" font-weight="600" fill="#0f172a">Argo Books</text>
                    <rect x="772" y="98" width="72" height="18" rx="9" fill="#efeeff"/>
                    <text x="808" y="111" text-anchor="middle" font-size="9" font-weight="600" fill="#635bff">via Stripe</text>
                    <line x1="556" y1="128" x2="862" y2="128" stroke="#f1f5f9" stroke-width="1"/>

                    <!-- booked header -->
                    <circle class="sf-ring" cx="590" cy="152" r="11" fill="#10b981"/>
                    <circle cx="590" cy="152" r="11" fill="#10b981"/>
                    <path d="M585 152 l3.5 3.5 l6 -7" fill="none" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <text x="610" y="149" font-size="12.5" font-weight="600" fill="#0f172a">Recorded automatically</text>
                    <text x="610" y="163" font-size="9.5" fill="#94a3b8">from one Stripe charge</text>

                    <!-- rows -->
                    <g>
                        <rect class="sf-accent" x="574" y="182" width="3" height="26" rx="1.5" fill="#10b981"/>
                        <text x="590" y="192" font-size="12" font-weight="600" fill="#0f172a">Sale &#183; Design retainer</text>
                        <text x="590" y="206" font-size="9.5" fill="#94a3b8">Revenue</text>
                        <text x="846" y="197" text-anchor="end" font-size="13" font-weight="700" fill="#15803d">+$1,240.00</text>
                    </g>
                    <line x1="590" y1="220" x2="846" y2="220" stroke="#f5f7fa" stroke-width="1"/>
                    <g>
                        <rect class="sf-accent d2" x="574" y="228" width="3" height="26" rx="1.5" fill="#ef4444"/>
                        <text x="590" y="238" font-size="12" font-weight="600" fill="#0f172a">Stripe processing fee</text>
                        <text x="590" y="252" font-size="9.5" fill="#94a3b8">Expense &#183; linked to the sale</text>
                        <text x="846" y="243" text-anchor="end" font-size="13" font-weight="700" fill="#b91c1c">&#8722;$36.28</text>
                    </g>
                    <line x1="590" y1="266" x2="846" y2="266" stroke="#f5f7fa" stroke-width="1"/>
                    <g>
                        <rect class="sf-accent d3" x="574" y="274" width="3" height="26" rx="1.5" fill="#8b5cf6"/>
                        <circle cx="600" cy="288" r="11" fill="#ede9fe"/>
                        <text x="600" y="291" text-anchor="middle" font-size="9" font-weight="700" fill="#6d28d9">SL</text>
                        <text x="620" y="285" font-size="12" font-weight="600" fill="#0f172a">Sarah Lee</text>
                        <text x="620" y="298" font-size="9.5" fill="#94a3b8">Customer created</text>
                        <rect x="792" y="279" width="54" height="18" rx="9" fill="#f3e8ff"/>
                        <text x="819" y="291" text-anchor="middle" font-size="9" font-weight="600" fill="#7c3aed">New</text>
                    </g>
                    <line x1="590" y1="312" x2="846" y2="312" stroke="#f5f7fa" stroke-width="1"/>
                    <g>
                        <rect class="sf-accent d4" x="574" y="320" width="3" height="26" rx="1.5" fill="#3f63e8"/>
                        <text x="590" y="330" font-size="12" font-weight="600" fill="#0f172a">GST collected (5%)</text>
                        <text x="590" y="344" font-size="9.5" fill="#94a3b8">Tax tracked</text>
                        <text x="846" y="335" text-anchor="end" font-size="13" font-weight="700" fill="#0f172a">$59.05</text>
                    </g>
                </svg>
            </div>
            <p class="flow-caption"><strong>Zero manual entry.</strong> Every charge arrives fully sorted, fees and refunds included.</p>
        </div>
    </section>

    <!-- =============================================
         WHAT YOU GET
         ============================================= -->
    <section class="why-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">What You Get</span>
                <h2 class="section-title">Everything from Stripe, sorted for you</h2>
                <p class="section-desc">Connect once, and here is what you get.</p>
            </div>
            <div class="why-grid">
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('receipt', 28) ?>
                    </div>
                    <h3>Every sale, in full detail</h3>
                    <p>Each Stripe charge becomes a revenue entry with the product, the customer (auto-created), sales tax, and any discount.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('dollar', 28) ?>
                    </div>
                    <h3>Fees tracked automatically</h3>
                    <p>Stripe processing fees are recorded as expenses linked to each sale, so your profit is accurate.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('refresh', 28) ?>
                    </div>
                    <h3>Refunds handled correctly</h3>
                    <p>A Stripe refund marks the original sale as returned, not a mystery expense.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('check', 28, '', 2.5) ?>
                    </div>
                    <h3>No double-counting</h3>
                    <p>When you also import your bank statement, Argo recognizes the Stripe payout deposit and skips it, so revenue is never counted twice.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('key', 28) ?>
                    </div>
                    <h3>Read-only and safe</h3>
                    <p>You connect with a restricted, read-only key. Argo can read your payouts and charges but can never move money or change anything.</p>
                </div>
                <div class="why-card animate-on-scroll">
                    <div class="why-card-icon">
                        <?= svg_icon('eye', 28) ?>
                    </div>
                    <h3>You stay in control</h3>
                    <p>Sync when you want, review a summary before anything is imported, and undo a whole sync in one step.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>See it for yourself</h3>
                <p>Connect Stripe and watch your next batch of sales import automatically.</p>
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
         PRIVACY / READ-ONLY DETAIL (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your Stripe data stays under your control</h2>
                    <p>Argo Books connects to Stripe with a restricted key that you create and can revoke at any time. That key only ever grants read access, so Argo Books can pull in your charges, refunds, payouts, and customers, but it has no way to issue a charge, send a payout, or change a setting in your Stripe account.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Restricted, read-only key, created and controlled by you</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Argo Books can never move money or change your Stripe settings</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Revoke the key from Stripe at any time to disconnect</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/privacy-local-storage.svg" alt="Your data stays local: encrypted, offline-capable, no cloud" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         HOW IT WORKS, 4 Steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">How It Works</span>
                <h2 class="section-title">Connect once, sync whenever you like</h2>
                <p class="section-desc">Four steps from Stripe to fully recorded books.</p>
            </div>
            <div class="steps-grid steps-grid-4">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Create a restricted key</h3>
                    <p>In your Stripe dashboard, create a restricted key with read-only access to charges, refunds, payouts, and customers.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Paste it into Argo Books</h3>
                    <p>Under Settings > Integrations, paste the key to connect your Stripe account.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Click Sync now</h3>
                    <p>Argo Books reads your recent activity and prepares a summary of what's about to import.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">4</div>
                    <h3>Review and import</h3>
                    <p>Check the summary, adjust anything you like, and import. Nothing is saved until you confirm.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2: primary download CTA + setup guide -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Ready to connect Stripe?</h3>
                <p>Download Argo Books, add your Stripe key, and see your sales import in minutes.</p>
                <div class="inline-cta-buttons">
                    <a href="../../downloads/" class="btn-cta btn-cta-primary">
                        <span>Get Started Free</span>
                        <?= svg_icon('arrow-right', 18) ?>
                    </a>
                    <a href="<?= $base ?>documentation/pages/integrations/stripe-integration.php" class="btn-cta btn-cta-outline">
                        <span>Read the setup guide</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         RELATED
         ============================================= -->
    <section class="related-features">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Related</span>
                <h2 class="section-title">Works great with</h2>
                <p class="section-desc">Stripe pairs naturally with the rest of your books.</p>
            </div>
            <div class="related-grid">
                <a href="../../features/bank-statement-import/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('bank', 22) ?>
                    </div>
                    <h3>Bank Statement Import</h3>
                    <p>Import your bank statement too, and Argo Books skips the Stripe payout deposit automatically so revenue is never doubled up.</p>
                </a>
                <a href="../" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document-upload', 22) ?>
                    </div>
                    <h3>All Integrations</h3>
                    <p>See what's live now and what's coming next.</p>
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
                    <h2>Ready to connect your Stripe account?</h2>
                    <p>Download Argo Books for free, connect Stripe in a few minutes, and watch your sales import automatically.</p>
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
            <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
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
