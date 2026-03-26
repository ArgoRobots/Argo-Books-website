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
        content="Track customer information, purchase history, and contact details with Argo Books. A simple customer database built for small businesses — organize contacts, addresses, and notes without a full CRM.">
    <meta name="keywords"
        content="customer management, CRM, customer tracking, customer database, small business CRM, customer profiles, contact management, customer address book, customer notes, customer purchase history">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="Customer Management — Argo Books">
    <meta property="og:description"
        content="Track customer information, purchase history, and contact details with Argo Books. Simple customer management built for small businesses.">
    <meta property="og:url" content="https://argorobots.com/features/customer-management/">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Argo Books">
    <meta property="og:locale" content="en_CA">
    <meta property="og:image" content="https://ogimage.io/templates/brand?title=Customer+Management&subtitle=Keep+a+complete+record+of+every+customer.+Contacts%2C+addresses%2C+purchase+history%2C+and+notes+in+one+place.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Customer Management — Argo Books">
    <meta name="twitter:description"
        content="Track customer information, purchase history, and contact details with Argo Books. Simple customer management built for small businesses.">
    <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Customer+Management&subtitle=Keep+a+complete+record+of+every+customer.+Contacts%2C+addresses%2C+purchase+history%2C+and+notes+in+one+place.&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="CA-SK">
    <meta name="geo.placename" content="Canada">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://argorobots.com/features/customer-management/">

    <!-- Breadcrumb Schema -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://argorobots.com/"},
                {"@type": "ListItem", "position": 2, "name": "Features", "item": "https://argorobots.com/features/"},
                {"@type": "ListItem", "position": 3, "name": "Customer Management", "item": "https://argorobots.com/features/customer-management/"}
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
                    "name": "Does Argo Books include a CRM for managing customers?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Argo Books includes a built-in customer database where you can store names, emails, phone numbers, addresses, and notes. It integrates with invoicing, revenue tracking, and rentals."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Can I search and filter my customer list in Argo Books?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "Yes. You can instantly search customers by name, email, or ID, and filter by country, status, or date added."
                    }
                },
                {
                    "@type": "Question",
                    "name": "Is customer data stored in the cloud?",
                    "acceptedAnswer": {
                        "@type": "Answer",
                        "text": "No. Argo Books is a desktop app and all customer data is stored locally on your computer. Nothing is uploaded to cloud servers."
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
            "description": "Track customer information, purchase history, and contact details with Argo Books. A simple customer database built for small businesses.",
            "featureList": "Customer profiles with contact details, Searchable and sortable customer table, Integration with invoicing and rentals, Local data storage with no cloud dependency"
        }
    </script>

    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Customer Management — Argo Books</title>

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
                <?= svg_icon('users', 16) ?>
                <span>Customer Management</span>
            </div>
            <h1 class="animate-fade-in">Know your customers, grow your business</h1>
            <p class="hero-subtitle animate-fade-in">Keep a complete record of every customer — names, contact details, addresses, and notes — in one organized place. Build stronger relationships with the people who matter most to your business.</p>
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
                    <h2>Scattered contacts cost you customers and credibility</h2>
                    <p>Most small businesses track customers across spreadsheets, email inboxes, sticky notes, and phone contacts. When a customer calls, you scramble to find their details. When it's time to send an invoice, you're guessing at addresses. And when a customer hasn't ordered in months, you don't even notice. Argo Books gives you a single, searchable customer database with every detail you need — so you always know who your customers are and how to reach them.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Complete customer profiles with name, email, phone, and address</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Searchable, sortable customer table with instant filtering</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Summary cards show total, active, and new customers at a glance</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Customer data flows into invoices, revenue tracking, and rentals automatically</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/customer-dashboard.svg" alt="Argo Books customer management dashboard showing a sortable customer table with names, emails, phone numbers, addresses, and action buttons" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 1 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Start organizing your customers today</h3>
                <p>Download Argo Books and add your first customer in under a minute. No credit card required.</p>
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
                    <h3>10 seconds</h3>
                    <p>To add a new customer profile</p>
                </div>
                <div class="highlight-item">
                    <h3>Instant</h3>
                    <p>Search across all customer records</p>
                </div>
                <div class="highlight-item">
                    <h3>Zero</h3>
                    <p>Duplicate entries or lost contacts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =============================================
         DETAIL SECTION 2: Adding Customers
         Image left, text right (reversed)
         ============================================= -->
    <section class="feature-detail-section" style="background: var(--gray-50);">
        <div class="container">
            <div class="feature-detail reversed animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Add Customers</span>
                    <h2>Build complete customer profiles in seconds</h2>
                    <p>Click "Add Customer" and fill in a clean, guided form. Enter a first name, last name, company, email, phone number with country code, and a full mailing address — street, city, state, postal code, and country. Each customer is uniquely identifiable from the start.</p>
                    <p>Add notes to any customer profile for details that don't fit neatly into a form field — preferred contact method, special pricing agreements, or anything else you want to remember. Every field is optional except the name, so you can start with just a name and fill in details later as the relationship grows.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Full address fields — street, city, state, postal code, and country</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>International phone numbers with country code selector</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Free-text notes field for anything extra you need to remember</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/customer-add-form.svg" alt="Argo Books add customer form showing fields for name, company, email, phone with country code, full address, and notes" loading="lazy">
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
                <h2 class="section-title">Three steps to an organized customer base</h2>
                <p class="section-desc">From scattered contacts to a complete customer database. No CRM experience needed — Argo Books keeps it simple.</p>
            </div>
            <div class="steps-grid">
                <div class="step-card animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>Add your customers</h3>
                    <p>Click "Add Customer" and fill in names, contact details, and addresses. Start with just a name and add details over time.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>Search, filter, and manage</h3>
                    <p>Find any customer instantly with the search bar. Sort by name, email, or country. Edit details, add notes, or view a customer's complete profile.</p>
                </div>
                <div class="step-card animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>Use everywhere in Argo Books</h3>
                    <p>Customer profiles flow into invoicing, revenue tracking, and rental management. Select a customer when creating an invoice and their details auto-populate — no retyping.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 2 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Stop losing track of your customers</h3>
                <p>A complete customer database means faster invoicing, better follow-ups, and stronger relationships. Get started in minutes.</p>
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
         DETAIL SECTION 3: Customer Dashboard
         Text left, image right
         ============================================= -->
    <section class="feature-detail-section">
        <div class="container">
            <div class="feature-detail animate-on-scroll">
                <div class="feature-detail-text">
                    <span class="section-label">Customer Dashboard</span>
                    <h2>See your entire customer base at a glance</h2>
                    <p>The customers page shows every customer in a clean, sortable table. Each row displays the customer's name and ID with a color-coded avatar, email address, phone number, street address, country, and last rental date. Summary cards at the top show your total customer count, active customers, banned customers, and how many were added this month.</p>
                    <p>Use the search bar to find any customer by name, email, or ID. Click the filter button to narrow results by country, status, or date added. Inline action buttons let you view a customer's full profile, edit their details, or remove them — all without leaving the page. Pagination keeps things fast even with hundreds of customers.</p>
                    <ul class="feature-checklist">
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Summary cards — total customers, active, banned, and new this month</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Color-coded avatars with customer initials for quick visual scanning</span>
                        </li>
                        <li>
                            <?= svg_icon('check', 20) ?>
                            <span>Edit, view, and delete actions on every row</span>
                        </li>
                    </ul>
                </div>
                <div class="feature-detail-visual">
                    <img src="../../resources/images/features/customer-dashboard.svg" alt="Argo Books customer dashboard showing summary cards, search bar, filter button, and a paginated customer table with action buttons" loading="lazy">
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
                <h2 class="section-title">More than just a contact list</h2>
                <p class="section-desc">Customer management in Argo Books isn't a full CRM — it's the organized, searchable customer database your business actually needs.</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon">
                        <?= svg_icon('bolt', 22) ?>
                    </div>
                    <h3>Faster invoicing</h3>
                    <p>Select a customer when creating an invoice and their name, email, and billing address auto-populate. No retyping, no copy-pasting from a spreadsheet.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon green">
                        <?= svg_icon('search', 22) ?>
                    </div>
                    <h3>Find anyone instantly</h3>
                    <p>Search across names, emails, phone numbers, and addresses. Every customer is indexed and findable in seconds — even if you have hundreds of records.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon purple">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>One source of truth</h3>
                    <p>No more outdated spreadsheets or duplicate contacts. Every feature in Argo Books pulls from the same customer database, so details are always consistent.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon amber">
                        <?= svg_icon('globe', 22) ?>
                    </div>
                    <h3>International support</h3>
                    <p>Full mailing addresses with country fields, international phone numbers with country codes, and multi-currency support. Built for businesses that serve customers anywhere.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon cyan">
                        <?= svg_icon('shield', 22) ?>
                    </div>
                    <h3>Data stays on your computer</h3>
                    <p>Customer names, emails, phone numbers, and addresses are stored locally on your device. No cloud uploads, no third-party access — your customer data is yours.</p>
                </div>
                <div class="benefit-card animate-on-scroll">
                    <div class="benefit-card-icon red">
                        <?= svg_icon('trending-up', 22) ?>
                    </div>
                    <h3>Grow without complexity</h3>
                    <p>Start with 5 customers, scale to 500. The interface stays clean and fast. No CRM training, no complex pipelines — just the customer info your business needs.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inline CTA 3 -->
    <section class="inline-cta">
        <div class="container">
            <div class="inline-cta-inner animate-on-scroll">
                <h3>Build your customer database today</h3>
                <p>Join small business owners who stopped losing track of customers and started building real relationships with Argo Books.</p>
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
                <h2 class="section-title">Built for every business that has customers</h2>
                <p class="section-desc">Whether you have 10 regulars or 1,000 clients — customer management scales with your business.</p>
            </div>
            <div class="use-cases-grid">
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('users', 22) ?>
                        Freelancers &amp; consultants
                    </h3>
                    <p>Keep track of every client's contact details, billing address, and project notes. When it's time to invoice, their profile is one click away — no digging through old emails.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('package', 22) ?>
                        Retail &amp; e-commerce
                    </h3>
                    <p>Maintain a database of wholesale buyers, repeat customers, and suppliers. Track shipping addresses, company names, and purchase patterns across every transaction.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('calendar', 22) ?>
                        Service businesses
                    </h3>
                    <p>Store customer addresses for on-site work, phone numbers for appointment reminders, and notes about service preferences. Everything your team needs in one place.</p>
                </div>
                <div class="use-case-card animate-on-scroll">
                    <h3>
                        <?= svg_icon('house', 22) ?>
                        Property &amp; rental management
                    </h3>
                    <p>Keep tenant and renter profiles with full contact details and mailing addresses. Link customers to rental records and invoices for a complete history per person.</p>
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
                <p class="section-desc">Customer management is even more powerful when combined with these features.</p>
            </div>
            <div class="related-grid">
                <a href="../invoicing/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('document', 22) ?>
                    </div>
                    <h3>Invoicing</h3>
                    <p>Create an invoice and select a customer — their name, email, and billing address auto-populate. No retyping, no errors, no wasted time.</p>
                </a>
                <a href="../expense-revenue-tracking/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('dollar', 22) ?>
                    </div>
                    <h3>Expense &amp; Revenue Tracking</h3>
                    <p>Revenue transactions are linked to customer profiles. See which customers generate the most revenue and track purchase patterns over time.</p>
                </a>
                <a href="../rental-management/" class="related-card animate-on-scroll">
                    <div class="related-card-icon">
                        <?= svg_icon('calendar', 22) ?>
                    </div>
                    <h3>Rental Management</h3>
                    <p>Assign rentals to customer profiles. Track who rented what, when it's due back, and see a customer's complete rental history from their profile.</p>
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
                    <h2>Ready to organize your customers?</h2>
                    <p>Download Argo Books and build your customer database in minutes. Free to get started — no credit card, no trial period.</p>
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
