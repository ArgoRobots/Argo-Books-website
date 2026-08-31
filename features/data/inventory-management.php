<?php
// features/data/inventory-management.php
//
// Content for /features/inventory-management/. Layout lives in
// features/feature-page.php.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

return [
    'breadcrumb' => 'Inventory Management',
    'title' => 'Inventory Management | Argo Books',
    'meta_description' => 'Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, stock adjustments, and multi-location support. Argo Books makes inventory simple for small businesses.',
    'meta_keywords' => 'inventory management software, stock tracking, product catalog management, small business inventory, inventory alerts, purchase orders, stock adjustments, warehouse management, reorder points, low stock alerts',
    'og_title' => 'Inventory Management | Argo Books',
    'og_description' => 'Manage your inventory with real-time stock tracking, low-stock alerts, purchase orders, and multi-location support. Argo Books makes inventory simple for small businesses.',
    'feature_list' => 'Stock level tracking, Low stock alerts, Purchase orders, Product cost tracking',

    'h1' => 'Stock counts that<br>stay correct.',
    'hero_sub' => 'Levels move as you sell and restock, so the number on screen is the number on the shelf, and you find out about a shortage before a customer does.',
    'hero_facts' => 'Free plan, no credit card, and your stock data stays on your own computer.',
    'demo' => 'inventory',

    'steps_h2' => 'Three steps, then it keeps up on its own',
    'steps_lede' => 'Stock counts go wrong when updating them is a separate task somebody has to remember. Here it is a side effect of selling.',
    'steps' => [
        ['h3' => 'List what you carry', 'p' => 'Product, cost, price and how many you have. Import a spreadsheet if you already keep one.'],
        ['h3' => 'Sell and restock as normal', 'p' => 'Every sale takes stock down and every purchase order puts it back, without a second entry.'],
        ['h3' => 'Get told before you run out', 'p' => 'Set a reorder point per product and the low-stock warning arrives while there is still time to order.'],
    ],

    'splits_before_cta' => [
        [
            'banner' => 'PRODUCT BLOCK',
            'bg' => true,
            'eyebrow' => 'Beyond the count',
            'h2' => 'What each product actually costs you',
            'lede' => 'Knowing you have eleven left is useful. Knowing what those eleven cost, what they sell for, and which supplier gave you the better price is what tells you whether the product is worth carrying at all.',
            'list' => [
                'Cost and margin held per product, not just quantity',
                'Purchase orders that update stock when they arrive',
                'Low stock alerts on the reorder point you choose',
            ],
            'img' => '../../resources/images/features/inventory-dashboard.svg',
            'img_alt' => 'The Argo Books inventory dashboard showing stock levels, low stock warnings and product costs',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'midcta_h2' => 'Get an accurate stock count today',
    'midcta_p' => 'No account, no credit card, and you can import the list you already have.',

    'benefits_h2' => 'What changes when the count is trustworthy',
    'benefits' => [
        ['icon' => 'package', 'h3' => 'You stop selling what you do not have', 'p' => 'A count that updates as you sell is a count you can quote from without checking the shelf first.'],
        ['icon' => 'bolt', 'h3' => 'Reordering happens on time', 'p' => 'Low stock warnings fire on your reorder point, not when a customer asks for something you cannot supply.'],
        ['icon' => 'trending-up', 'h3' => 'You can see which products earn', 'p' => 'Cost and price held together turn a stock list into a margin list, which is the one that matters.'],
        ['icon' => 'check', 'stroke' => 2.4, 'h3' => 'No more counting twice', 'p' => 'Sales and purchase orders both move stock automatically, so the spreadsheet reconciliation disappears.'],
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
        ['icon' => 'package', 'h3' => 'Retail and e-commerce', 'p' => 'Keep shelf and storefront counts in step without a nightly stocktake.'],
        ['icon' => 'wrench', 'h3' => 'Trades', 'p' => 'Track parts and materials so a job does not stop halfway through.'],
        ['icon' => 'truck', 'h3' => 'Wholesalers', 'p' => 'Manage larger quantities and supplier orders from the same list.'],
        ['icon' => 'users', 'h3' => 'Makers and small brands', 'p' => 'Watch component stock and finished goods without a warehouse system.'],
    ],

    'related_eyebrow' => 'Works with',
    'related_h2' => 'What inventory connects to',
    'related' => [
        ['href' => '../invoicing/', 'icon' => 'document', 'h3' => 'Invoicing', 'p' => 'Sell from your product list and stock adjusts as the invoice goes out.'],
        ['href' => '../expense-revenue-tracking/', 'icon' => 'dollar', 'h3' => 'Expense & revenue tracking', 'p' => 'Purchase orders become expenses without retyping them.'],
        ['href' => '../spreadsheet-import/', 'icon' => 'document-upload', 'h3' => 'Spreadsheet import', 'p' => 'Bring the stock list you already keep in Excel across in one go.'],
        ['href' => '../predictive-analytics/', 'icon' => 'analytics', 'h3' => 'Predictive analytics', 'p' => 'Sales history turns into a view of what you will need to reorder.'],
    ],

    // Drives both the visible accordion and the FAQPage JSON-LD.
    'faqs' => [
    [
        'q' => 'Can Argo Books track inventory across multiple locations?',
        'a' => 'Yes. You can add unlimited locations, such as warehouses, stores, offices, or any other facility, and track per-location stock levels, inventory value, and capacity. Everything is visible from a single dashboard, so you always know what you have and where it is.',
    ],
    [
        'q' => 'How do low-stock alerts work?',
        'a' => 'You can set a reorder point for each product. When stock drops to that level, Argo Books flags it with a color-coded status badge so you know it\'s time to restock. No more surprise stockouts. You\'ll see the warning before it becomes a problem.',
    ],
    [
        'q' => 'Can I create and manage purchase orders?',
        'a' => 'Yes. Create purchase orders with supplier details and itemized line items directly in Argo Books. When you mark an order as received, stock levels update automatically, with no manual adjustments needed. It keeps your inventory accurate without the extra work.',
    ],
    [
        'q' => 'Is inventory management included in the Free plan?',
        'a' => 'Yes. Inventory management is a core feature available on both the Free and Premium plans. You get unlimited products, multi-location tracking, low-stock alerts, and purchase orders at no cost. Premium adds predictive analytics to help you forecast demand and plan inventory purchases ahead of time.',
    ],
    ],

    'outro_h2' => 'Stop guessing what is on the shelf',
    'outro_p' => 'Download Argo Books and get your stock under control. Free plan, no credit card, and your data stays on your own machine.',
];
