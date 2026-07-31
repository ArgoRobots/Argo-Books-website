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
        'q' => 'How does Argo Books track overdue rentals?',
        'a' => 'Argo Books automatically flags rentals as overdue when the return date passes. Color-coded status badges make it easy to spot late returns at a glance, so nothing slips through the cracks. You can see all overdue, active, and completed rentals from a single dashboard.',
    ],
    [
        'q' => 'Can I track deposits and payments for rentals?',
        'a' => 'Yes. You can set deposit amounts per rental item and track whether each deposit has been paid or is still outstanding. When the rental is complete, you can generate a professional invoice directly from the rental record with one click. Customer details and pricing auto-populate, so there\'s no double entry.',
    ],
    [
        'q' => 'Is rental management included in the Free plan?',
        'a' => 'Yes. Rental management is a core feature available on both the Free and Premium plans. You can create and manage rental bookings, track deposits, and monitor return dates at no cost. Premium users additionally benefit from unlimited invoicing directly from rental records.',
    ],
    [
        'q' => 'What types of businesses use rental management in Argo Books?',
        'a' => 'Rental management in Argo Books is designed for any business that lends or rents items: equipment rental companies, tool libraries, party supply rentals, AV equipment providers, and more. If you need to track who has what, when it\'s due back, and what they owe, Argo Books handles it.',
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
    <meta name="description" content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental, event companies, and any rental-based business.">
    <meta name="keywords" content="rental management software, booking management, equipment rental tracking, rental business software, rental inventory, rental returns, rental invoicing, equipment booking, rental deposits, overdue rentals">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Rental Management | Argo Books">
    <meta property="og:description" content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental and any rental-based business.">
    <meta property="og:url" content="https://argorobots.com/features/rental-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Rental Management | Argo Books">
    <meta name="twitter:description" content="Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental and any rental-based business.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/rental-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "Rental Management" => "/features/rental-management/"]) ?></script>

    <!-- FAQ Schema, built from the same array as the accordion further down -->
    <script type="application/ld+json"><?= argo_faq_schema($faqs) ?></script>

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
            "description": "Manage rental bookings, track rental periods, handle returns, and monitor revenue with Argo Books. Built for equipment rental, event companies, and any rental-based business.",
            "featureList": "Booking calendar, Availability tracking, Return tracking, Rental item management"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Rental Management | Argo Books</title>

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
                    <h1>Booked, out,<br>and back again.</h1>
                    <p class="fp-hero-sub">A calendar that shows what is reserved and what is free, so you can answer "is it available next Thursday?" without checking three places first.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Free plan, no credit card, and your booking data stays on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="rental">
                    <?= argo_feature_demo('rental') ?>
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
                <h2 class="fp-h2">Three steps from enquiry to return</h2>
                <p class="fp-lede">Double bookings happen when availability lives in somebody's head. This puts it on a calendar that the bookings themselves keep current.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>List what you rent out</h3>
                    <p>Each item with its rate and how many you have. One record covers the whole fleet of a given item.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Take the booking</h3>
                    <p>Pick the customer and the dates. The calendar blocks the item out and stops it being promised twice.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Mark it back in</h3>
                    <p>Returns free the item up immediately and close the rental off against the customer.</p>
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
                    <div class="fp-eyebrow">At a glance</div>
                    <h2 class="fp-h2">Availability you can trust when the phone rings</h2>
                    <p class="fp-lede">The booking calendar shows every item across every date, with what is out, what is reserved and what is free. Because bookings write to it directly there is no second diary to keep in step.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Every item and every date on one calendar</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Bookings tied to the customer who took the item</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Overdue returns visible without hunting for them</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/rental-records.svg"
                         alt="The Argo Books rental calendar showing booked, reserved and available dates across rental items"
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
                <h2>Get your bookings on one calendar</h2>
                <p>No account, no credit card, and nothing to set up before your first booking.</p>
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
                <h2 class="fp-h2">What changes when availability is visible</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('calendar', 20) ?></div>
                    <h3>No more double bookings</h3>
                    <p>An item that is out cannot be promised to somebody else, because the calendar and the booking are the same record.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Overdue returns surface themselves</h3>
                    <p>You see what should have come back yesterday without going looking for it.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Rentals become revenue automatically</h3>
                    <p>A completed rental lands in your books with the customer attached, rather than as a note to invoice later.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>One answer, not three</h3>
                    <p>Availability, price and history all come from the same record, so what you tell a customer is what the system knows.</p>
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
                    <h3><?= svg_icon('truck', 19) ?> Equipment hire</h3>
                    <p>Tools, machinery and vehicles booked out by the day or the week.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('calendar', 19) ?> Event businesses</h3>
                    <p>Furniture, staging and gear across overlapping event dates.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Kit and gear rental</h3>
                    <p>Cameras, instruments and sports equipment with fast turnaround.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('users', 19) ?> Anyone lending on terms</h3>
                    <p>Keep track of what is out, who has it, and when it is due back.</p>
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
                <h2 class="fp-h2">What rentals connect to</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../customer-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('users', 20) ?></div>
                    <h3>Customer management</h3>
                    <p>Bookings attach to the customer, along with their history and balance.</p>
                </a>
                <a href="../invoicing/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document', 20) ?></div>
                    <h3>Invoicing</h3>
                    <p>Bill a completed rental without re-entering the dates or the rate.</p>
                </a>
                <a href="../inventory-management/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('package', 20) ?></div>
                    <h3>Inventory management</h3>
                    <p>Track the items you rent alongside the stock you sell.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense & revenue tracking</h3>
                    <p>Rental income lands in your revenue records automatically.</p>
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
                <h2>Stop checking three places for one answer</h2>
                <p>Download Argo Books and put your bookings on one calendar. Free plan, no credit card, and your data stays on your own machine.</p>
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
