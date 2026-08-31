<?php
// features/data/customer-management.php
//
// Content for /features/customer-management/. Layout lives in
// features/feature-page.php.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

return [
    'breadcrumb' => 'Customer Management',
    'title' => 'Customer Management | Argo Books',
    'meta_description' => 'Track customer information, purchase history, and contact details with Argo Books. A simple customer database built for small businesses, organizing contacts, addresses, and notes without a full CRM.',
    'meta_keywords' => 'customer management, CRM, customer tracking, customer database, small business CRM, customer profiles, contact management, customer address book, customer notes, customer purchase history',
    'og_title' => 'Customer Management | Argo Books',
    'og_description' => 'Track customer information, purchase history, and contact details with Argo Books. Simple customer management built for small businesses.',
    'feature_list' => 'Customer directory, Purchase history, Outstanding balances, Contact management',

    'h1' => 'Every customer.<br>Every balance.',
    'hero_sub' => 'One record per customer holding their contact details, everything they have ever bought, and exactly what they still owe you.',
    'hero_facts' => 'Free plan, no credit card, and your customer list stays on your own computer.',
    'demo' => 'customers',

    'steps_h2' => 'Three steps, and the record fills itself',
    'steps_lede' => 'Most customer lists rot because keeping them current is a separate job. This one updates as a side effect of billing.',
    'steps' => [
        ['h3' => 'Add them once', 'p' => 'Name, contact details, and any terms you have agreed. That is the last time you type any of it.'],
        ['h3' => 'Sell and invoice as normal', 'p' => 'Every invoice, payment and sale attaches itself to the customer it belongs to, with no filing on your part.'],
        ['h3' => 'Open the record when you need it', 'p' => 'Their full history, their balance, and when they last paid you, on one screen.'],
    ],

    'splits_before_cta' => [
        [
            'banner' => 'PRODUCT BLOCK',
            'bg' => true,
            'eyebrow' => 'The whole picture',
            'h2' => 'What they bought, and what they owe',
            'lede' => 'A customer record is not just an address book entry. It carries every invoice raised, every payment received, and the balance between the two, so you can answer "are we square?" without opening three other screens.',
            'list' => [
                'Outstanding balance per customer, kept current automatically',
                'Full purchase and payment history on one record',
                'Search and filter by name, balance or last activity',
            ],
            'img' => '../../resources/images/features/customer-dashboard.svg',
            'img_alt' => 'The Argo Books customer directory showing contact details, outstanding balances and recent activity',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'midcta_h2' => 'Get your customer list in order',
    'midcta_p' => 'No account, no credit card, and no import needed to start.',

    'benefits_h2' => 'What changes when the history is in one place',
    'benefits' => [
        ['icon' => 'search', 'h3' => 'You can answer questions immediately', 'p' => 'When a customer calls about an invoice from March, the answer is one search away instead of a scroll through email.'],
        ['icon' => 'dollar', 'h3' => 'You know who owes you', 'p' => 'Balances are calculated from real invoices and payments, so the number is current rather than remembered.'],
        ['icon' => 'check', 'stroke' => 2.4, 'h3' => 'Invoices start half written', 'p' => 'Billing an existing customer pulls their details in automatically, which is where most invoicing time actually goes.'],
        ['icon' => 'trending-up', 'h3' => 'You can see who is worth keeping', 'p' => 'Purchase history over time shows which customers grow, which shrink, and which quietly cost you money.'],
    ],

    'splits_after_benefits' => [
        [
            'banner' => 'PRIVACY',
            'bg' => true,
            'flip' => true,
            'eyebrow' => 'Privacy',
            'h2' => 'Your books stay on your computer',
            'lede' => 'Argo Books is a desktop application, not a cloud service holding your finances on someone else\'s server. Your records are written to your own machine, and you can back them up or move them like any other file.',
            'list' => [
                'Records and documents stored locally',
                'No third-party cloud storage of your financial data',
                'Your data moves and backs up like any other file',
            ],
            'img' => '../../resources/images/privacy-local-storage.svg',
            'img_alt' => 'The Argo Books folder open on a local disk, showing receipts, invoices and the database file stored on this computer',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'who_h2' => 'Built for the way you actually work',
    'who' => [
        ['icon' => 'users', 'h3' => 'Freelancers', 'p' => 'Keep repeat clients and their rates straight without a spreadsheet on the side.'],
        ['icon' => 'package', 'h3' => 'Retail and wholesale', 'p' => 'Trade accounts with running balances alongside one-off retail sales.'],
        ['icon' => 'wrench', 'h3' => 'Trades and services', 'p' => 'Job history per customer, so a return visit starts from what happened last time.'],
        ['icon' => 'calendar', 'h3' => 'Anyone on repeat business', 'p' => 'See at a glance who has not bought in a while and is worth a call.'],
    ],

    'related_eyebrow' => 'Works with',
    'related_h2' => 'What customer records connect to',
    'related' => [
        ['href' => '../invoicing/', 'icon' => 'document', 'h3' => 'Invoicing', 'p' => 'Bill a saved customer in a couple of clicks with their details already filled in.'],
        ['href' => '../expense-revenue-tracking/', 'icon' => 'dollar', 'h3' => 'Expense & revenue tracking', 'p' => 'Payments received land against both the customer and your revenue.'],
        ['href' => '../rental-management/', 'icon' => 'calendar', 'h3' => 'Rental management', 'p' => 'Bookings and returns tracked against the customer who took the item.'],
        ['href' => '../report-builder/', 'icon' => 'report', 'h3' => 'Report builder', 'p' => 'Turn customer activity into statements and summaries.'],
    ],

    // Drives both the visible accordion and the FAQPage JSON-LD.
    'faqs' => [
    [
        'q' => 'Does Argo Books include a built-in customer database?',
        'a' => 'Yes. Argo Books includes a built-in customer database where you can store names, emails, phone numbers, addresses, and notes for every client. It integrates directly with invoicing, revenue tracking, and rental management, so when you create an invoice or rental, your customer details auto-populate without re-entering anything.',
    ],
    [
        'q' => 'How do I find customers in Argo Books?',
        'a' => 'You can instantly search customers by name, email, or ID, and filter by country, status, or date added. Whether you have 10 customers or 10,000, finding the right record takes seconds.',
    ],
    [
        'q' => 'Is my customer data private and secure?',
        'a' => 'Absolutely. Argo Books is a desktop application, so all customer data is stored locally on your computer. Nothing is uploaded to cloud servers. Your data is encrypted with AES-256-GCM, the same standard used by banks and government agencies. You have full control over your customer information at all times.',
    ],
    [
        'q' => 'Can I import my existing customer list?',
        'a' => 'Yes. You can import customers from Excel or CSV files using the AI Spreadsheet Import feature. The AI automatically detects your columns and maps them to the right fields, so you can migrate your existing customer data into Argo Books in minutes, with no manual data entry required.',
    ],
    ],

    'outro_h2' => 'Know your customers, not just their names',
    'outro_p' => 'Download Argo Books and build your customer list today. Free plan, no credit card, and your data stays on your own machine.',
];
