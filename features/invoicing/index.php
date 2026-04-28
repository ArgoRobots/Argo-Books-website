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
        content="Create professional invoices with Argo Books. Customizable templates, automatic line-item calculations, online payment links, and payment tracking that help you get paid faster.">
    <meta name="keywords"
        content="invoice software, invoice generator, professional invoicing, small business invoicing, invoice templates, online invoice payments, invoice tracking, send invoices, payment reminders, invoice management">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Invoicing — Argo Books">
    <meta property="og:description"
        content="Create professional invoices with Argo Books. Customizable templates, payment tracking, and online payment links that help you get paid faster.">
    <meta property="og:url" content="https://argorobots.com/features/invoicing/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Invoicing&subtitle=Create+professional+invoices+and+get+paid+faster.+Templates%2C+payment+tracking%2C+and+online+payments.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Invoicing — Argo Books">
    <meta name="twitter:description"
        content="Create professional invoices with Argo Books. Customizable templates, payment tracking, and online payment links that help you get paid faster.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Invoicing&subtitle=Create+professional+invoices+and+get+paid+faster.+Templates%2C+payment+tracking%2C+and+online+payments.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/invoicing/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Invoicing", "item": "https://argorobots.com/features/invoicing/"}
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
                    "name": "Can customers pay invoices online through Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Every invoice includes a secure online payment link so your customers can pay by credit card with a single click. Argo Books supports Stripe, Square, and PayPal — you choose which payment gateway works best for your business. Payments are tracked automatically, so you always know which invoices are outstanding and which have been paid."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I customize how my invoices look?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. Invoices are sent via professional email templates that include your company logo, billing details, and itemized line items. You can choose from multiple templates and customize the content to match your brand. Every invoice looks polished and professional — no design skills required."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How does invoice tracking work?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books tracks every invoice from draft to paid with color-coded status badges so you can see where things stand at a glance. Summary cards on the invoicing dashboard show your outstanding, paid, and overdue totals in real time. You'll never have to wonder whether a client has paid — it's all right there."
                    }
                },
                {
                    "@type": "Question",
                    "name": "How many invoices can I send per month?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "The Free plan includes 25 invoices per month, which is plenty for most small businesses and freelancers getting started. If you need unlimited invoicing, the Premium plan removes all limits so you can send as many invoices as your business requires."
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
            "description": "Create professional invoices with Argo Books. Customizable templates, automatic line-item calculations, online payment links, and payment tracking.",
            "featureList": "Professional invoice templates with branding, Online payment links via Stripe and Square, Automatic tax and total calculations, Real-time payment status tracking"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Invoicing — Argo Books</title>

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
                <?= svg_icon('document', 16) ?>
                <span>Invoicing</span>
            </div>
            <h1 class="animate-fade-in">Create professional invoices and get paid faster</h1>
            <p class="hero-subtitle animate-fade-in">Build polished invoices in seconds with customizable templates, automatic calculations, and built-in online payments. Track every invoice from draft to paid — no chasing required.</p>
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
                    <h2>Unprofessional invoices cost you money and credibility</h2>
                    <p>Sending invoices as Word documents or plain emails doesn't just look bad — it slows down payments. Clients lose track of loose attachments, there's no easy way for them to pay online, and you end up chasing payments manually. Argo Books gives you a complete invoicing system with professional templates, automatic calculations, and built-in payment collection — so you look polished and get paid on time.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Professional invoice templates with your company branding</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic line-item calculations with tax, discounts, and totals</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Built-in online payments — customers pay in seconds</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Real-time status tracking from draft to sent to paid</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/invoice-dashboard.svg" alt="Argo Books invoice dashboard showing invoice list with status tracking, customer details, and financial summary cards" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Start sending professional invoices today</h3>
                <p>Download Argo Books and create your first invoice in under two minutes. No credit card required.</p>
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
                    <h3>1 minute</h3>
                    <p>To create and send a complete invoice</p>
                </div>
                <div class="highlight-item">
                    <h3>Online payments</h3>
                    <p>Customers pay online with any card</p>
                </div>
                <div class="highlight-item">
                    <h3>Real-time</h3>
                    <p>Invoice status tracking and payment alerts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Creating Invoices
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Create Invoices</span>
                    <h2>Build invoices in seconds, not hours</h2>
                    <p>Select a customer, add your products or services, set quantities and prices, and Argo Books handles the rest — subtotals, tax calculations, and totals update in real time as you type.</p>
                    <p>Customize professional email templates, set issue and due dates, and add multiple line items. Click send, and it's done. Every invoice is automatically linked to the customer's profile.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Real-time subtotal, tax, and total calculations as you add items</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multiple customizable professional templates</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Customers can pay online with their preferred payment method</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/invoice-create-form.svg" alt="Argo Books create invoice form showing customer selection, line items, automatic calculations, and template options" loading="lazy">
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
                <h2 class="section-title">Three steps to getting paid</h2>
                <p class="section-desc">From blank invoice to money in your account. No accounting knowledge needed — Argo Books handles the details.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Build your invoice</h3>
                    <p>Select a customer and your products or services, set quantities and prices. Argo Books calculates subtotals, tax, and totals automatically.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Preview and send</h3>
                    <p>Preview your invoice as the customer will see it. The invoice includes your branding, line-item details, and an online payment link. Send it by email with one click.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Get paid online</h3>
                    <p>Your customer receives the invoice with a secure payment link. They pay by credit card, and you see the status update to "Paid" in real time. No more chasing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop chasing payments</h3>
                <p>Built-in online payment links mean your customers can pay the moment they open the invoice. Get started with Argo Books in minutes.</p>
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
         DETAIL SECTION 3: Professional Preview
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Professional Templates</span>
                    <h2>Invoices your clients will take seriously</h2>
                    <p>Every invoice is rendered as a clean, professional document with your company name, customer billing details, itemized line items, and a clear total. The layout is designed to look great both on screen and when printed — with your branding front and center.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Branded invoice header with your company name and logo</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Full billing details — address, email, invoice number, and dates</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Itemized line-item table with quantities, prices, and amounts</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Clear due date badge so customers know exactly when to pay</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/invoice-preview.svg" alt="Argo Books invoice preview showing a professional invoice document with branded header, billing details, line items, and totals" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 4: Online Payments
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Online Payments</span>
                    <h2>Let customers pay with a single click</h2>
                    <p>Every invoice includes a secure online payment link powered by Stripe, Square, or PayPal. When your customer opens the invoice email, they click the payment link and see a clean, simple payment page with the amount due, invoice details, and a credit card form. No account creation, no extra steps — just enter card details and pay.</p>
                    <p>Payments are processed securely, and the invoice status updates to "Paid" automatically in Argo Books. You get notified when payment is received, and the transaction is recorded. No more back-and-forth about e-transfers or check deposits.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Multiple payment providers — choose Stripe, Square, or PayPal</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No account creation required for customers — just click and pay</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Automatic status updates when payment is received</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Payment receipts sent automatically to the customer</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/invoice-payment.svg" alt="Argo Books online invoice payment page showing a secure Stripe-powered credit card form with invoice details and pay button" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 5: Invoice Management
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Invoice Management</span>
                    <h2>Track every invoice from draft to paid</h2>
                    <p>The invoices dashboard gives you a complete view of every invoice you've ever sent. See all invoices in a sortable, searchable table. Summary cards at the top show your total outstanding, paid this month, overdue amounts, and invoices due this week — at a glance.</p>
                    <p>Filter by status to see only drafts, sent, paid, or overdue invoices. Click any invoice to view its details, resend the email, or record a manual payment.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Summary cards show outstanding, paid, overdue, and due-this-week totals</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Search and filter by customer, date, amount, or status</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Resend invoices or record manual payments from the dashboard</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/invoice-dashboard.svg" alt="Argo Books invoice management dashboard showing sortable invoice table with status badges and summary statistics" loading="lazy">
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
                <h2 class="section-title">More than just sending invoices</h2>
                <p class="section-desc">Professional invoicing in Argo Books isn't just about sending a bill — it's about getting paid faster and keeping your financial records accurate.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Get paid faster</h3>
                    <p>Built-in online payment links let customers pay the moment they open your invoice. No waiting for checks, no chasing e-transfers — payments go straight into your account.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('check', 22, '', 2.5) ?>
                    </div>
                    <h3>Look professional</h3>
                    <p>Clean, branded invoice templates show your clients you're a serious business. Professional invoices build trust and credibility.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('clock', 22) ?>
                    </div>
                    <h3>Save hours every month</h3>
                    <p>Stop building invoices in spreadsheets or Word. Auto-populated customers, products, and calculations mean you spend seconds per invoice instead of minutes.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Track payment status</h3>
                    <p>Know which invoices are paid, pending, or overdue at a glance. Summary cards and color-coded badges give you a real-time overview.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Secure payment processing</h3>
                    <p>Online payments are processed through Stripe, PayPal, and Square — the same payment platforms used by millions of businesses worldwide.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Automatic revenue recording</h3>
                    <p>When an invoice is paid, the transaction is automatically recorded. No double entry, no missed payments — your books stay accurate.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Get paid on time, every time</h3>
                <p>Join small business owners who stopped chasing payments and started getting paid automatically with Argo Books.</p>
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
                <h2 class="section-title">Built for every business that sends invoices</h2>
                <p class="section-desc">Whether you bill hourly, per project, or per product — Argo Books invoicing adapts to your workflow.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Freelancers &amp; consultants
                    </h3>
                    <p>Bill clients per project or per hour. Create invoices from your service catalog, add custom line items, and include payment links so clients pay instantly when the work is done.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Retail &amp; e-commerce
                    </h3>
                    <p>Invoice customers for the products they ordered. Pull items directly from your inventory and let Argo Books calculate quantities, prices, and tax.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Service businesses
                    </h3>
                    <p>Invoice for completed jobs, maintenance contracts, and recurring services. Track which invoices are paid and which are overdue to keep your cash flow healthy.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('document', 22) ?>
                        Property &amp; rental management
                    </h3>
                    <p>Send rent invoices and lease-related charges to tenants. Online payment links make it easy for tenants to pay on time, and you can track payment history per property.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 6: Privacy & Security
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Privacy First</span>
                    <h2>Your invoice data stays on your computer</h2>
                    <p>Unlike cloud-based invoicing tools that store your customer data and financial records on third-party servers, Argo Books is a desktop application. Your invoices, customer details, and payment records are stored locally on your device — not on someone else's cloud.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Desktop app — your invoice and customer data stays on your computer</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Online payments processed securely through Stripe, PayPal, and Square</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>No vendor lock-in — your data is yours, always</span>
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
                <p class="section-desc">Invoicing is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense &amp; Revenue Tracking</h3>
                    <p>Paid invoices automatically become revenue records. Your books stay accurate without manual double-entry — every payment flows into your financial picture.</p>
                </a>
                <a href="../customer-management/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('users', 22) ?>
                    </div>
                    <h3>Customer Management</h3>
                    <p>Invoice creation pulls customer details directly from your customer profiles. Everything stays up to date.</p>
                </a>
                <a href="../predictive-analytics/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('analytics', 22) ?>
                    </div>
                    <h3>Predictive Analytics</h3>
                    <p>Your invoice data feeds ML-powered forecasting. See predicted revenue, profits, and cash flow trends based on your invoicing history.</p>
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
                    <h2>Ready to send your first invoice?</h2>
                    <p>Download Argo Books and start invoicing in minutes. Free to get started — no credit card, no trial period.</p>
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
