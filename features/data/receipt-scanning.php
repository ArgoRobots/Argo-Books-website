<?php
// features/data/receipt-scanning.php
//
// Content for /features/receipt-scanning/. Layout lives in
// features/feature-page.php.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

$argo_free_scan_limit = (int) get_pricing_config()['free_receipt_scan_monthly_limit'];
$argo_scan_limit = (int) get_pricing_config()['receipt_scan_monthly_limit'];

return [
    'breadcrumb' => 'AI Receipt Scanning',
    'title' => 'AI Receipt Scanning | Argo Books',
    'meta_description' => 'Scan receipts with AI and automatically extract store names, items, totals, and taxes. Argo Books\' AI receipt scanner eliminates manual data entry and keeps your books accurate.',
    'meta_keywords' => 'AI receipt scanner, OCR receipt, automatic receipt scanning, receipt data extraction, receipt management software, scan receipts app, receipt OCR software, digital receipt organizer, receipt tracker, expense receipt app',
    'og_title' => 'AI Receipt Scanning | Argo Books',
    'og_description' => 'Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry and keep your books accurate.',
    'twitter_description' => 'Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry.',
    'schema_description' => 'Scan receipts with AI and automatically extract store names, items, totals, and taxes. Eliminate manual data entry and keep your books accurate.',
    'feature_list' => 'AI-powered receipt data extraction, Automatic expense record creation, Receipt archive with search, Support for printed and handwritten receipts',

    'hero_banner' => 'HERO. The scan-and-extract demo from the landing page, sitting
         beside the headline the way Wave puts its product visual in a
         feature-page hero. Markup matches #receiptScan on the home page
         so the two stay in step; the loop that drives it is at the foot
         of this file.',
    'h1' => 'The whole receipt.<br>Not just the total.',
    'hero_sub' => 'Argo Books reads the vendor, the date, every line item, the tax and the total off a photo, then files it as an expense you can check before you save.',
    'hero_facts' => 'Reads a receipt in under ten seconds. ' . $argo_free_scan_limit . ' free scans a month, no card, and everything stays on your own computer.',
    'demo' => 'ai-receipts',

    'steps_h2' => 'Three steps, about ten seconds',
    'steps_lede' => 'Most scanners find the total and stop. Argo Books works down the receipt line by line, so the detail you need at tax time is already there.',
    'steps' => [
        ['h3' => 'Take the photo', 'p' => 'Snap the receipt with your phone, or drop in a screenshot, a scan, or a PDF. Printed and handwritten both work.'],
        ['h3' => 'Let it read', 'p' => 'Character recognition lifts the text, then a language model works out what each part means: which line is the vendor, which is tax, which is the total.'],
        ['h3' => 'Check and save', 'p' => 'The fields come back filled in and categorized. Correct anything that needs it, then save it as an expense.'],
    ],

    'splits_before_cta' => [
        [
            'banner' => 'AFTER THE SCAN',
            'bg' => true,
            'eyebrow' => 'After the scan',
            'h2' => 'Every receipt filed, and findable',
            'lede' => 'A scanned receipt is not a photo in a folder. It becomes an expense record with the image attached, categorized and dated, sitting alongside the rest of your books. Finding that $342 equipment purchase from last July takes one search.',
            'list' => [
                'Receipts attach to their expense record automatically',
                'Original images kept next to the extracted data',
                'Export expense reports for your accountant or your return',
            ],
            'img' => '../../resources/images/features/receipt-archive.svg',
            'img_alt' => 'The Argo Books receipt archive: searchable receipt cards showing amounts, dates, suppliers, and expense or revenue tags',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'midcta_h2' => 'Scan your first receipt in about a minute',
    'midcta_p' => 'No account, no credit card, and ' . $argo_free_scan_limit . ' scans a month on the free plan.',

    'benefits_h2' => 'What changes when you stop typing',
    'benefits' => [
        ['icon' => 'clock', 'h3' => 'Hours back every week', 'p' => 'Receipt entry is the chore that gets put off until it becomes a weekend. Scanning turns it into something you do at the till.'],
        ['icon' => 'check', 'stroke' => 2.4, 'h3' => 'Numbers that match the paper', 'p' => 'Typing by hand is where transposed digits and skipped line items come from. The scanner reads what is printed, and shows you before it saves.'],
        ['icon' => 'shield', 'h3' => 'Tax-ready all year', 'p' => 'Each scan is categorized and stored with its original image, so the records your accountant asks for already exist in January.'],
        ['icon' => 'trending-up', 'h3' => 'You can finally see the spending', 'p' => 'Line-item detail across every receipt shows which suppliers cost you the most, which a shoebox of paper never will.'],
    ],

    'splits_after_benefits' => [
        [
            'banner' => 'PRIVACY',
            'bg' => true,
            'flip' => true,
            'eyebrow' => 'Privacy',
            'h2' => 'Your receipts stay on your computer',
            'lede' => 'Argo Books is a desktop application, not a cloud service holding your books on someone else\'s server. The receipt image and the expense record it creates are written to your machine. Reading the receipt happens through a secure API call, and nothing is kept there afterwards.',
            'list' => [
                'Receipts and expense records stored locally',
                'No third-party cloud storage of your financial documents',
                'Nothing retained after the scan returns',
            ],
            'img' => '../../resources/images/privacy-local-storage.svg',
            'img_alt' => 'The Argo Books folder open on a local disk, showing receipts, invoices and the database file stored on this computer',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'who_h2' => 'Built for the way you actually work',
    'who' => [
        ['icon' => 'users', 'h3' => 'Freelancers', 'p' => 'Scan client-related expenses on the spot, so billing starts from a clean record instead of a pile.'],
        ['icon' => 'package', 'h3' => 'Retail and e-commerce', 'p' => 'Read supplier invoices line by line and know exactly what you paid for every product on the shelf.'],
        ['icon' => 'wrench', 'h3' => 'Trades and services', 'p' => 'Fuel, materials, equipment. Categorize by job or project and see profit on every engagement.'],
        ['icon' => 'document', 'h3' => 'Anyone at tax time', 'p' => 'No January scramble. Every receipt is already scanned, categorized, and ready to hand over.'],
    ],

    'related_eyebrow' => 'Works with',
    'related_h2' => 'Where scanned receipts go next',
    'related' => [
        ['href' => '../expense-revenue-tracking/', 'icon' => 'dollar', 'h3' => 'Expense &amp; revenue tracking', 'p' => 'Scanned receipts land straight in your records, categorized and ready for reports.'],
        ['href' => '../predictive-analytics/', 'icon' => 'analytics', 'h3' => 'Predictive analytics', 'p' => 'More expense detail means better forecasts of what the next quarter costs you.'],
        ['href' => '../spreadsheet-import/', 'icon' => 'document-upload', 'h3' => 'Spreadsheet import', 'p' => 'Bring the history you already keep in spreadsheets, then scan everything from here on.'],
        ['href' => '../../free-receipt-scanner/', 'icon' => 'receipt-scan', 'h3' => 'Try it in your browser', 'p' => 'The free web scanner reads a receipt without installing anything. No signup.'],
    ],

    // Drives both the visible accordion and the FAQPage JSON-LD.
    'faqs' => [
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
    ],

    'outro_h2' => 'Put the shoebox down',
    'outro_p' => 'Download Argo Books and scan your first receipt today. Free plan, no credit card, and your data stays on your own machine.',
];
