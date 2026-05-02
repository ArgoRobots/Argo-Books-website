<?php require_once __DIR__ . '/../resources/icons.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Argo">

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Leave a review for Argo Books on Capterra. Learn what to expect: how Capterra verifies reviewers, what gets published, and what stays private.">
    <meta name="keywords"
        content="argo books review, capterra review, leave a review, software review, accounting software review">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Leave a Review - Argo Books">
    <meta property="og:description"
        content="Help others discover Argo Books. Leave a review on Capterra and see what to expect from their verification process.">
    <meta property="og:url" content="https://argorobots.com/review/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Leave+a+Review&subtitle=Help+others+discover+Argo+Books+by+sharing+your+experience+on+Capterra&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Leave a Review - Argo Books">
    <meta name="twitter:description"
        content="Help others discover Argo Books. Leave a review on Capterra and see what to expect from their verification process.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Leave+a+Review&subtitle=Help+others+discover+Argo+Books+by+sharing+your+experience+on+Capterra&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/review/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Leave a Review", "item": "https://argorobots.com/review/"}
            ]
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Leave a Review - Argo Books</title>

    <script src="../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../features/style.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/header/style.css">
    <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>
    <main>

    <?php
        $capterra_url = 'https://reviews.capterra.com/products/new/b879ef0f-634f-414f-bd7d-e8e818d409c1/?utm_source=vp&utm_campaign=vendor_request';
    ?>

    <!-- =============================================
         HERO
         ============================================= -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-gradient-orb hero-orb-1"></div>
            <div class="hero-gradient-orb hero-orb-2"></div>
        </div>
        <div class="container">
            <div class="hero-badge animate-fade-in">
                <?= svg_icon('star', 16) ?>
                <span>Share Your Experience</span>
            </div>
            <h1 class="animate-fade-in">Help others discover Argo Books</h1>
            <p class="hero-subtitle animate-fade-in">
                If Argo Books has saved you time or simplified your books, a review on Capterra is one of the most useful ways to give back. It helps small businesses find software that fits the way they actually work.
            </p>
            <div class="review-prefer-contact animate-fade-in">
                <?= svg_icon('message-circle', 18) ?>
                <span>
                    Having an issue? <a href="../contact-us/">Tell us first &mdash; we'd love to fix it.</a>
                </span>
            </div>
            <div class="hero-ctas animate-fade-in">
                <a href="<?= htmlspecialchars($capterra_url) ?>" target="_blank" rel="noopener" class="btn-cta btn-cta-primary">
                    <span>Leave a Review on Capterra</span>
                    <?= svg_icon('arrow-top-right', 18) ?>
                </a>
                <a href="#what-to-expect" class="btn-cta btn-cta-outline">
                    <span>See What to Expect</span>
                </a>
            </div>
        </div>
    </section>

    <!-- =============================================
         STATS BANNER — quick reassurance
         ============================================= -->
    <section class="highlight-banner">
        <div class="container">
            <div class="highlight-grid animate-on-scroll">
                <div class="highlight-item">
                    <h3>~5 min</h3>
                    <p>Average time to leave a review</p>
                </div>
                <div class="highlight-item">
                    <h3>Verified</h3>
                    <p>Every review is manually checked by Capterra</p>
                </div>
                <div class="highlight-item">
                    <h3>Independent</h3>
                    <p>Capterra is owned by Gartner, not by us</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT IS CAPTERRA?
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">About Capterra</span>
                    <h2>An independent review site small businesses trust</h2>
                    <p>
                        Capterra is one of the largest independent software review sites on the internet, owned by Gartner. It lets real users compare business tools side by side using verified reviews &mdash; not paid ads. People researching accounting software regularly check Capterra before they pick a product, so a thoughtful review from an actual user has real weight.
                    </p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Reviews are read by thousands of small business owners every day</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Capterra is independent &mdash; we don't pay for placement or curate reviews</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Honest, balanced reviews help us improve and tell us what's working</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <div class="review-info-card">
                        <div class="review-info-card-header">
                            <div class="review-info-card-stars">
                                <?= svg_icon('star', 22) ?>
                                <?= svg_icon('star', 22) ?>
                                <?= svg_icon('star', 22) ?>
                                <?= svg_icon('star', 22) ?>
                                <?= svg_icon('star', 22) ?>
                            </div>
                            <span class="review-info-card-label">Verified review</span>
                        </div>
                        <p class="review-info-card-quote">
                            &ldquo;Argo Books replaced three different tools we were paying for. Setup took an afternoon and the receipt scanning alone has saved us hours every week.&rdquo;
                        </p>
                        <div class="review-info-card-meta">
                            <div class="review-info-card-avatar">
                                <?= svg_icon('user', 20) ?>
                            </div>
                            <div>
                                <div class="review-info-card-name">Sarah K.</div>
                                <div class="review-info-card-role">Owner, Retail &mdash; 2-10 employees</div>
                            </div>
                        </div>
                        <p class="review-info-card-disclaimer">Illustrative example. Reviews on Capterra display first name, role, industry, and company size.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT YOU'LL SEE
         ============================================= -->
    <section id="what-to-expect" class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">What to Expect</span>
                    <h2>Verifying you're a real person</h2>
                    <p>
                        Before publishing your review, Capterra needs to confirm you're genuinely a user of Argo Books and not a bot, a competitor, or someone connected to us. You'll see this verification screen near the end of the form &mdash; you can choose either path.
                    </p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span><strong>Sign in with LinkedIn</strong> &mdash; the fastest path. Capterra uses your LinkedIn profile to confirm you're real.</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span><strong>Continue without LinkedIn</strong> &mdash; enter your name and a contact email manually. Capterra may follow up by email if they have questions.</span>
                        </li>
                    </ul>
                    <div class="review-callout">
                        <?= svg_icon('info', 18) ?>
                        <span>The most common reason a review doesn't get published is that Capterra couldn't verify the reviewer. LinkedIn or accurate contact info helps.</span>
                    </div>
                </div>
                <div class="feature-detail-visual">
                    <div class="review-screenshot-frame">
                        <img src="../resources/images/review/capterra-publish-screen.png"
                             alt="Capterra's 'Improve your chances of getting published' screen, showing a Continue with LinkedIn button and three tips for writing a great review: be specific and relevant, be authentic, and be balanced."
                             loading="lazy"
                             onerror="this.parentElement.classList.add('review-screenshot-frame-missing')">
                        <div class="review-screenshot-fallback">
                            <?= svg_icon('shield-check', 56) ?>
                            <h3>Identity verification</h3>
                            <p>Sign in with LinkedIn or continue with manual entry &mdash; both work, both are private.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         TIPS FOR A GREAT REVIEW
         ============================================= -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Capterra's Guidelines</span>
                <h2 class="section-title">Tips for a great review</h2>
                <p class="section-desc">Capterra surfaces these three tips on the verification screen. They're also what make a review most useful to other small business owners.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('search', 22) ?>
                    </div>
                    <h3>Be specific &amp; relevant</h3>
                    <p>Share concrete examples of features you liked or disliked. Real details from your day-to-day use are far more useful to readers than general impressions.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('user', 22) ?>
                    </div>
                    <h3>Be authentic</h3>
                    <p>Write in your own words about your genuine experience. Capterra asks people not to use AI tools to generate review content.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Be balanced</h3>
                    <p>Highlight what's working and where things could improve. Balanced reviews are the most helpful &mdash; and the ones other small business owners actually trust.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT'S PUBLISHED VS PRIVATE
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">Privacy</span>
                <h2 class="section-title">What's shared, and what stays private</h2>
                <p class="section-desc">Capterra publishes enough information to make a review credible &mdash; without exposing your full identity or contact details.</p>
            </div>
            <div class="review-privacy-grid">
                <div class="review-privacy-card review-privacy-public animate-on-scroll">
                    <div class="review-privacy-card-icon green">
                        <?= svg_icon('eye', 22) ?>
                    </div>
                    <h3>Shown publicly</h3>
                    <ul>
                        <li><?= svg_icon('check', 18) ?><span>Your first name</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Your job role or function</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Your industry</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Your company size</span></li>
                        <li><?= svg_icon('check', 18) ?><span>How long you've used Argo Books</span></li>
                        <li><?= svg_icon('check', 18) ?><span>A profile photo (only in some cases)</span></li>
                    </ul>
                </div>
                <div class="review-privacy-card review-privacy-private animate-on-scroll">
                    <div class="review-privacy-card-icon purple">
                        <?= svg_icon('lock', 22) ?>
                    </div>
                    <h3>Kept private</h3>
                    <ul>
                        <li><?= svg_icon('check', 18) ?><span>Your last name</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Your email address</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Your company name (unless you choose to share it)</span></li>
                        <li><?= svg_icon('check', 18) ?><span>Anything else you don't explicitly enter into the form</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         WHAT HAPPENS AFTER YOU SUBMIT — 3 steps
         ============================================= -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <span class="section-label">After You Submit</span>
                <h2 class="section-title">What happens next</h2>
                <p class="section-desc">From submit to live on Capterra, here's the full path your review takes.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>You submit your review</h3>
                    <p>Fill in the rating, what you like, what could be better, and how you use Argo Books. Choose LinkedIn or manual verification at the end.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Capterra verifies you</h3>
                    <p>Their quality assurance team manually confirms you're a real person and that the review fits their community guidelines. They may email you with a quick follow-up question.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Your review goes live</h3>
                    <p>Once approved &mdash; usually within a few business days &mdash; your review appears on the Argo Books listing on Capterra and starts helping other small businesses make a confident decision.</p>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- =============================================
         FINAL CTA + Footer (dark wrapper)
         ============================================= -->
    <div class="dark-section-wrapper">
        <section class="cta-section">
            <div class="container">
                <div class="cta-card animate-on-scroll">
                    <h2>Ready to share your experience?</h2>
                    <p>It takes about five minutes, and your review genuinely helps other small business owners pick the right tool. Thank you for taking the time.</p>
                    <div class="cta-buttons">
                        <a href="<?= htmlspecialchars($capterra_url) ?>" target="_blank" rel="noopener" class="btn-cta btn-cta-primary">
                            <span>Leave a Review on Capterra</span>
                            <?= svg_icon('arrow-top-right', 18) ?>
                        </a>
                        <a href="../contact-us/" class="btn-cta btn-cta-ghost">
                            <span>Or Send Us Feedback Directly</span>
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
