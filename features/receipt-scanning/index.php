<?php
// Referral tracking: capture ?source so article/ad clicks landing here attribute.
require_once __DIR__ . '/../../partials/schema.php';
require_once __DIR__ . '/../../partials/faq.php';
require_once __DIR__ . '/../../partials/feature-demo.php';
require_once __DIR__ . '/../../track_referral.php';
require_once __DIR__ . '/../../resources/icons.php';
require_once __DIR__ . '/../../config/pricing.php';
$argo_monthly = (int) get_pricing_config()['premium_monthly_price'];
$argo_free_scan_limit = (int) get_pricing_config()['free_receipt_scan_monthly_limit'];
$argo_scan_limit = (int) get_pricing_config()['receipt_scan_monthly_limit'];

// One array drives both the visible accordion and the FAQPage schema, so the
// two cannot drift apart.
$faqs = [
    [
        'q' => 'How does AI receipt scanning work?',
        'a' => 'Take a photo of any receipt, or upload an image, and Argo Books uses AI to extract the store name, individual line items, totals, taxes, and date automatically. The extracted data is used to create an expense record with no manual typing. It achieves 99.9% accuracy, so you spend less time on data entry and more time running your business.',
    ],
    [
        'q' => 'What types of receipts can Argo Books scan?',
        'a' => 'Argo Books can scan printed receipts, handwritten receipts, and digital receipt images. It supports photos taken with your phone camera, screenshots, and uploaded image files in common formats like JPG and PNG. Whether it is a crumpled gas station receipt or a clean digital invoice, the AI handles it.',
    ],
    [
        'q' => 'Do I need an internet connection to scan?',
        'a' => 'Yes, for the scan itself. Reading the receipt happens through a secure API call, so that step needs a connection. The receipt image and the expense record it creates are written to your own computer either way.',
    ],
    [
        'q' => 'Is my receipt data private?',
        'a' => 'Yes. AI processing uses a secure API call to extract the data, but your receipt images and all extracted information are stored locally on your computer. No receipt data is kept on third-party servers after processing. Your financial records remain fully under your control.',
    ],
    [
        'q' => 'How many receipts can I scan per month?',
        'a' => "The Free plan includes {$argo_free_scan_limit} AI receipt scans per month: enough to get started and see how it works. Premium users get {$argo_scan_limit} scans per month, which is more than enough for even the busiest small businesses. If you regularly collect receipts, Premium pays for itself in time saved.",
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
    <meta name="description"
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Argo Books' AI receipt scanner eliminates manual data entry and keeps your books accurate.">
    <meta name="keywords"
        content="AI receipt scanner, OCR receipt, automatic receipt scanning, receipt data extraction, receipt management software, scan receipts app, receipt OCR software, digital receipt organizer, receipt tracker, expense receipt app">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="AI Receipt Scanning | Argo Books">
    <meta property="og:description"
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry and keep your books accurate.">
    <meta property="og:url" content="https://argorobots.com/features/receipt-scanning/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://argorobots.com/resources/images/og/og-home.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AI Receipt Scanning | Argo Books">
    <meta name="twitter:description"
        content="Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry.">
    <meta name="twitter:image" content="https://argorobots.com/resources/images/og/og-home.png">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/receipt-scanning/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json"><?= argo_breadcrumb_schema(["Home" => "/", "Features" => "/features/", "AI Receipt Scanning" => "/features/receipt-scanning/"]) ?></script>

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
            "description": "Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry and keep your books accurate.",
            "featureList": "AI-powered receipt data extraction, Automatic expense record creation, Receipt archive with search, Support for printed and handwritten receipts"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>AI Receipt Scanning | Argo Books</title>

    <script src="../../resources/scripts/main.js"></script>
    <!-- Mockup animations, shared with the landing and comparison pages. -->
    <script src="../../resources/scripts/feature-tour.js" defer></script>

    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/faq.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <!-- Brand typefaces. IBM Plex Mono joins the pair on this page because the
         layout borrows the receipt's own monospaced grammar. -->
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
         HERO. The scan-and-extract demo from the landing page, sitting
         beside the headline the way Wave puts its product visual in a
         feature-page hero. Markup matches #receiptScan on the home page
         so the two stay in step; the loop that drives it is at the foot
         of this file.
         ============================================= -->
    <?php /* The `hero` class and the .hero-bg child are what resources/scripts/
             cursor-orb.js looks for. Without the child the orb is appended to
             the section itself and paints over the headline; inside .hero-bg it
             sits behind the content the way it does on every other page. */ ?>
    <section class="fp-hero hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="fp-wrap">
            <div class="fp-hero-grid">
                <div>
                    <h1>The whole receipt.<br>Not just the total.</h1>
                    <p class="fp-hero-sub">Argo Books reads the vendor, the date, every line item, the tax and the total off a photo, then files it as an expense you can check before you save.</p>
                    <div class="fp-hero-act">
                        <a href="../../downloads/" class="fp-btn fp-btn-primary">
                            <span>Download free</span>
                            <?= svg_icon('arrow-right', 17) ?>
                        </a>
                        <a href="../../pricing/" class="fp-textlink">See pricing</a>
                    </div>
                    <p class="fp-hero-facts">Reads a receipt in under ten seconds. <?= $argo_free_scan_limit ?> free scans a month, no card, and everything stays on your own computer.</p>
                </div>

                <div class="fp-hero-demo" data-feature-demo="ai-receipts">
                    <?= argo_feature_demo('ai-receipts') ?>
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
                <h2 class="fp-h2">Three steps, about ten seconds</h2>
                <p class="fp-lede">Most scanners find the total and stop. Argo Books works down the receipt line by line, so the detail you need at tax time is already there.</p>
            </div>
            <div class="fp-steps fp-reveal">
                <div class="fp-step">
                    <div class="fp-step-n">Step 1</div>
                    <h3>Take the photo</h3>
                    <p>Snap the receipt with your phone, or drop in a screenshot, a scan, or a PDF. Printed and handwritten both work.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 2</div>
                    <h3>Let it read</h3>
                    <p>Character recognition lifts the text, then a language model works out what each part means: which line is the vendor, which is tax, which is the total.</p>
                </div>
                <div class="fp-step">
                    <div class="fp-step-n">Step 3</div>
                    <h3>Check and save</h3>
                    <p>The fields come back filled in and categorized. Correct anything that needs it, then save it as an expense.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         AFTER THE SCAN
         ============================================= -->
    <section class="fp-section" style="background: var(--gray-50)">
        <div class="fp-wrap">
            <div class="fp-split fp-reveal">
                <div class="fp-split-text">
                    <div class="fp-eyebrow">After the scan</div>
                    <h2 class="fp-h2">Every receipt filed, and findable</h2>
                    <p class="fp-lede">A scanned receipt is not a photo in a folder. It becomes an expense record with the image attached, categorized and dated, sitting alongside the rest of your books. Finding that $342 equipment purchase from last July takes one search.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Receipts attach to their expense record automatically</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Original images kept next to the extracted data</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Export expense reports for your accountant or your return</span></li>
                    </ul>
                </div>
                <div class="fp-split-media">
                    <img src="../../resources/images/features/receipt-archive.svg"
                         alt="The Argo Books receipt archive: searchable receipt cards showing amounts, dates, suppliers, and expense or revenue tags"
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
                <h2>Scan your first receipt in about a minute</h2>
                <p>No account, no credit card, and <?= $argo_free_scan_limit ?> scans a month on the free plan.</p>
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
                <h2 class="fp-h2">What changes when you stop typing</h2>
            </div>
            <div class="fp-benefits fp-reveal">
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('clock', 20) ?></div>
                    <h3>Hours back every week</h3>
                    <p>Receipt entry is the chore that gets put off until it becomes a weekend. Scanning turns it into something you do at the till.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('check', 20, '', 2.4) ?></div>
                    <h3>Numbers that match the paper</h3>
                    <p>Typing by hand is where transposed digits and skipped line items come from. The scanner reads what is printed, and shows you before it saves.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('shield', 20) ?></div>
                    <h3>Tax-ready all year</h3>
                    <p>Each scan is categorized and stored with its original image, so the records your accountant asks for already exist in January.</p>
                </div>
                <div class="fp-benefit">
                    <div class="fp-benefit-ic"><?= svg_icon('trending-up', 20) ?></div>
                    <h3>You can finally see the spending</h3>
                    <p>Line-item detail across every receipt shows which suppliers cost you the most, which a shoebox of paper never will.</p>
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
                    <h2 class="fp-h2">Your receipts stay on your computer</h2>
                    <p class="fp-lede">Argo Books is a desktop application, not a cloud service holding your books on someone else's server. The receipt image and the expense record it creates are written to your machine. Reading the receipt happens through a secure API call, and nothing is kept there afterwards.</p>
                    <ul class="fp-list">
                        <li><?= svg_icon('check', 17) ?><span>Receipts and expense records stored locally</span></li>
                        <li><?= svg_icon('check', 17) ?><span>No third-party cloud storage of your financial documents</span></li>
                        <li><?= svg_icon('check', 17) ?><span>Nothing retained after the scan returns</span></li>
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
                    <p>Scan client-related expenses on the spot, so billing starts from a clean record instead of a pile.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('package', 19) ?> Retail and e-commerce</h3>
                    <p>Read supplier invoices line by line and know exactly what you paid for every product on the shelf.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('wrench', 19) ?> Trades and services</h3>
                    <p>Fuel, materials, equipment. Categorize by job or project and see profit on every engagement.</p>
                </div>
                <div class="fp-who-item">
                    <h3><?= svg_icon('document', 19) ?> Anyone at tax time</h3>
                    <p>No January scramble. Every receipt is already scanned, categorized, and ready to hand over.</p>
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
                <h2 class="fp-h2">Where scanned receipts go next</h2>
            </div>
            <div class="fp-related fp-reveal">
                <a href="../expense-revenue-tracking/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('dollar', 20) ?></div>
                    <h3>Expense &amp; revenue tracking</h3>
                    <p>Scanned receipts land straight in your records, categorized and ready for reports.</p>
                </a>
                <a href="../predictive-analytics/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('analytics', 20) ?></div>
                    <h3>Predictive analytics</h3>
                    <p>More expense detail means better forecasts of what the next quarter costs you.</p>
                </a>
                <a href="../spreadsheet-import/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('document-upload', 20) ?></div>
                    <h3>Spreadsheet import</h3>
                    <p>Bring the history you already keep in spreadsheets, then scan everything from here on.</p>
                </a>
                <a href="../../free-receipt-scanner/" class="fp-rel-card">
                    <div class="fp-rel-ic"><?= svg_icon('receipt-scan', 20) ?></div>
                    <h3>Try it in your browser</h3>
                    <p>The free web scanner reads a receipt without installing anything. No signup.</p>
                </a>
            </div>
<?php /* No "read next" guide links here. Both guides are already linked from
         16+ other pages, so this page adds nothing for crawlers, and sending
         someone to a long read directly above the download CTA costs more
         than it returns. */ ?>
        </div>
    </section>

    </main>

    <!-- Final CTA and footer share one dark block. The dark-section-wrapper
         class is what lets the footer's orbs bleed up past the footer's own
         box (see resources/footer/style.css), which is what removes the seam
         between the CTA and the footer. Same arrangement as /about-us/. -->
    <div class="dark-section-wrapper fp-outro">
        <section class="fp-outro-cta cta-section">
            <div class="fp-wrap">
                <h2>Put the shoebox down</h2>
                <p>Download Argo Books and scan your first receipt today. Free plan, no credit card, and your data stays on your own machine.</p>
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
