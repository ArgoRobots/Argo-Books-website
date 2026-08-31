<?php
// features/data/expense-revenue-tracking.php
//
// Content for /features/expense-revenue-tracking/. Layout lives in
// features/feature-page.php.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

return [
    'breadcrumb' => 'Expense & Revenue Tracking',
    'title' => 'Expense &amp; Revenue Tracking | Argo Books',
    'meta_description' => 'Track business expenses and revenue with Argo Books. Guided forms, smart validation, receipt archiving, and real-time profit monitoring make bookkeeping simple for any small business.',
    'meta_keywords' => 'expense tracking software, revenue tracking, business expense tracker, income and expense tracking, small business bookkeeping, profit tracking software, transaction management, expense management app',
    'og_title' => 'Expense &amp; Revenue Tracking | Argo Books',
    'og_description' => 'Track every expense and revenue transaction in one place. Guided forms, smart validation, and real-time profit monitoring keep your books accurate.',
    'twitter_description' => 'Track every expense and revenue transaction in one place, with real-time profit monitoring.',
    'schema_description' => 'Track business expenses and revenue with guided forms, smart validation, receipt archiving, and real-time profit monitoring.',
    'feature_list' => 'Expense and revenue tracking, Guided transaction forms, Receipt archive with search, Real-time profit summary',

    'hero_banner' => 'HERO. Live demo beside the headline. The panel markup comes from
         partials/feature-demo.php and the loop from feature-tour.js, both
         shared with the landing and comparison pages.',
    'h1' => 'Every dollar in.<br>Every dollar out.',
    'hero_sub' => 'Record an expense or a sale in about ten seconds, and watch the monthly totals and net profit move as you type. No debits, no credits, no accounting course.',
    'hero_facts' => 'Free plan, no credit card, and your books stay on your own computer.',
    'demo' => 'expenses',

    'steps_h2' => 'Three steps, about ten seconds',
    'steps_lede' => 'A spreadsheet lets you type anything into any cell. A guided form does not, which is why the numbers still add up a year later.',
    'steps' => [
        ['h3' => 'Record the transaction', 'p' => 'Pick expense or revenue, then fill in the amount, the category and who it was with. Smart defaults fill most of it for you.'],
        ['h3' => 'It checks before it saves', 'p' => 'Required fields are enforced and amounts are verified, so nothing lands in your books half finished or in the wrong column.'],
        ['h3' => 'See the picture change', 'p' => 'Monthly revenue, expenses and net profit update the moment you save. No waiting for month end to find out where you stand.'],
    ],

    'splits_before_cta' => [
        [
            'banner' => 'PRODUCT BLOCK',
            'bg' => true,
            'eyebrow' => 'Both sides of the ledger',
            'h2' => 'Money in and money out, in one list',
            'lede' => 'Revenue and expenses live in the same place, with matching forms and the same search. Sort by supplier, customer, date, amount or status, and edit a row without opening a separate page. Every change is kept, so you can see what moved and when.',
            'list' => [
                'Summary cards for monthly revenue, expenses and net profit',
                'Search and filter by supplier, customer, date, amount or status',
                'Edit straight from the row, with full history and undo',
            ],
            'img' => '../../resources/images/features/expense-revenue-stats.svg',
            'img_alt' => 'Argo Books expense and revenue summary cards showing monthly totals, transaction counts and net profit',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'midcta_h2' => 'Get your finances organized in minutes',
    'midcta_p' => 'No account, no credit card, and no accounting experience needed.',

    'benefits_h2' => 'What changes when it is all in one place',
    'benefits' => [
        ['icon' => 'trending-up', 'h3' => 'You know if you are profitable today', 'p' => 'Summary cards move as you add transactions, so the answer is on screen instead of waiting until the end of the month.'],
        ['icon' => 'check', 'stroke' => 2.4, 'h3' => 'Guided forms catch the mistakes', 'p' => 'Smart defaults and validation mean nothing saves with a missing category, a blank date, or an amount in the wrong column.'],
        ['icon' => 'search', 'h3' => 'Any transaction, in seconds', 'p' => 'Everything is indexed. Search by supplier, customer, amount or date and find it years later without scrolling a spreadsheet.'],
        ['icon' => 'shield', 'h3' => 'Tax-ready all year', 'p' => 'Every entry is timestamped and categorized, with a receipt attached where it matters, so January is not a rebuilding job.'],
    ],

    'splits_after_benefits' => [
        [
            'banner' => 'PRIVACY',
            'bg' => true,
            'flip' => true,
            'eyebrow' => 'Privacy',
            'h2' => 'Your books stay on your computer',
            'lede' => 'Argo Books is a desktop application, not a cloud service holding your finances on someone else\'s server. Transactions, receipts and reports are written to your own machine, and you can back them up or move them like any other file.',
            'list' => [
                'Transactions and receipts stored locally',
                'No third-party cloud storage of your financial records',
                'Your data moves and backs up like any other file',
            ],
            'img' => '../../resources/images/privacy-local-storage.svg',
            'img_alt' => 'The Argo Books folder open on a local disk, showing receipts, invoices and the database file stored on this computer',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'who_h2' => 'Built for the way you actually work',
    'who' => [
        ['icon' => 'users', 'h3' => 'Freelancers', 'p' => 'Keep project income and business costs separate, and see profit per client when it is time to bill.'],
        ['icon' => 'package', 'h3' => 'Retail and e-commerce', 'p' => 'Record every sale and supplier purchase, track cost of goods, and see which suppliers cost you the most.'],
        ['icon' => 'wrench', 'h3' => 'Service businesses', 'p' => 'Log revenue by customer and costs by job, then compare profitability across service types and periods.'],
        ['icon' => 'document', 'h3' => 'Side hustles', 'p' => 'Start with a few transactions a week and scale to hundreds without the interface getting in the way.'],
    ],

    'related_eyebrow' => 'Works with',
    'related_h2' => 'Where your transactions come from',
    'related' => [
        ['href' => '../receipt-scanning/', 'icon' => 'receipt-scan', 'h3' => 'AI receipt scanning', 'p' => 'Photograph a receipt and the expense record writes itself, line items and all.'],
        ['href' => '../bank-statement-import/', 'icon' => 'bank', 'h3' => 'Bank statement import', 'p' => 'Bring a month of transactions in at once instead of typing them one by one.'],
        ['href' => '../spreadsheet-import/', 'icon' => 'document-upload', 'h3' => 'Spreadsheet import', 'p' => 'Move the history you already keep in Excel or CSV across in one go.'],
        ['href' => '../predictive-analytics/', 'icon' => 'analytics', 'h3' => 'Predictive analytics', 'p' => 'Enough history turns into a forecast of what next month is likely to cost.'],
    ],

    // Drives both the visible accordion and the FAQPage JSON-LD.
    'faqs' => [
    [
        'q' => 'Do I need accounting experience to track expenses?',
        'a' => "Not at all. Argo Books uses guided forms with smart defaults and built-in validation to make recording expenses and revenue simple for anyone. You don't need to know debits from credits. Just fill in the amount, category, and date, and Argo Books handles the rest.",
    ],
    [
        'q' => 'Can I track both expenses and revenue in one place?',
        'a' => 'Yes. Argo Books has dedicated expense and revenue pages with real-time summary cards showing monthly totals, transaction counts, and net profit. You get a complete picture of your business finances without switching between apps or spreadsheets.',
    ],
    [
        'q' => 'How does receipt management work?',
        'a' => "You can attach receipts to any expense record for your records. Even better, AI receipt scanning can automatically create expense entries from receipt photos. It extracts the supplier name, line items, taxes, and total with 99.9% accuracy. All receipts are stored in a searchable archive so you're always ready for tax time.",
    ],
    [
        'q' => 'Can I import existing expense data into Argo Books?',
        'a' => "Yes. If you have expense or revenue records in a spreadsheet, you can import them using the AI Spreadsheet Import feature. Just drop your Excel or CSV file and the AI maps your columns to the right fields automatically. It's the fastest way to get up and running with your existing financial data.",
    ],
    ],

    'outro_h2' => 'Stop guessing whether you are profitable',
    'outro_p' => 'Download Argo Books and record your first transaction today. Free plan, no credit card, and your data stays on your own machine.',
];
