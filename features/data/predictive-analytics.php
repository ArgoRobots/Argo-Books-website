<?php
// features/data/predictive-analytics.php
//
// Content for /features/predictive-analytics/. Layout lives in
// features/feature-page.php.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

return [
    'breadcrumb' => 'Predictive Analytics',
    'title' => 'Predictive Analytics | Argo Books',
    'meta_description' => 'Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.',
    'meta_keywords' => 'predictive analytics, financial forecasting, business analytics, sales trend forecasting, ML business analytics, revenue forecasting software, expense prediction, seasonal pattern detection, machine learning forecasting, small business analytics',
    'og_title' => 'Predictive Analytics | Argo Books',
    'og_description' => 'Predict revenue, expenses, and growth with AI-powered analytics. Forecast trends and detect seasonal patterns automatically.',
    'feature_list' => 'Cash flow forecasting, Revenue and expense projections, Confidence ranges, Trend analysis',

    'h1' => 'See next month<br>before it arrives.',
    'hero_sub' => 'Argo Books reads the history already in your books and projects where revenue, expenses and cash are heading, with an honest range around the estimate.',
    'hero_facts' => 'Free plan, no credit card, and the analysis runs on your own computer.',
    'demo' => 'predictive',

    'steps_h2' => 'Three steps, and it keeps itself current',
    'steps_lede' => 'A forecast is only useful if it is built from what actually happened. This one is, and it updates as the month goes on.',
    'steps' => [
        ['h3' => 'Keep recording as normal', 'p' => 'Invoices, expenses and payments are all the input the forecast needs. There is nothing extra to fill in.'],
        ['h3' => 'It finds the pattern', 'p' => 'Seasonality, growth and recurring costs are picked out of your own history rather than an industry average.'],
        ['h3' => 'Read the range, not just the line', 'p' => 'Every projection comes with a confidence band, so you can see how sure it is before you spend against it.'],
    ],

    'splits_before_cta' => [
        [
            'banner' => 'PRODUCT BLOCK',
            'bg' => true,
            'eyebrow' => 'Honest numbers',
            'h2' => 'A forecast that admits what it does not know',
            'lede' => 'A single confident line is easy to draw and easy to be wrong about. Argo Books shows the projection inside a band that widens the further out it looks, so a quiet month reads as a risk rather than a surprise.',
            'list' => [
                'Projections built from your own transaction history',
                'Confidence range that widens with distance',
                'Revenue, expenses and net cash flow each projected',
            ],
            'img' => '../../resources/images/features/analytics-dashboard.svg',
            'img_alt' => 'The Argo Books analytics dashboard showing a cash flow forecast with a confidence band around the projection',
            'img_w' => 600, 'img_h' => 500,
        ],
    ],

    'midcta_h2' => 'Find out where the next quarter is heading',
    'midcta_p' => 'No account, no credit card, and no data science required.',

    'benefits_h2' => 'What changes when you can see ahead',
    'benefits' => [
        ['icon' => 'trending-up', 'h3' => 'Slow months stop being surprises', 'p' => 'A dip you can see coming six weeks out is a planning problem. The same dip discovered on the day is a cash problem.'],
        ['icon' => 'dollar', 'h3' => 'You can time the big spends', 'p' => 'Knowing what cash looks like in eight weeks is the difference between buying the equipment now and buying it after the quiet season.'],
        ['icon' => 'bolt', 'h3' => 'The picture updates as you work', 'p' => 'Every transaction you record sharpens the projection, so it is never based on last quarter alone.'],
        ['icon' => 'check', 'stroke' => 2.4, 'h3' => 'No guessing at the inputs', 'p' => 'It reads your real invoices and expenses, so the forecast reflects your business rather than a generic template.'],
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
        ['icon' => 'calendar', 'h3' => 'Seasonal businesses', 'p' => 'See the quiet stretch coming while there is still time to do something about it.'],
        ['icon' => 'users', 'h3' => 'Freelancers', 'p' => 'Spot the gap between projects before it becomes an empty month.'],
        ['icon' => 'package', 'h3' => 'Retail and e-commerce', 'p' => 'Plan stock and cash around what demand has actually done, not what you hope it does.'],
        ['icon' => 'wrench', 'h3' => 'Growing businesses', 'p' => 'Decide whether the next hire or the next van is affordable before committing.'],
    ],

    'related_eyebrow' => 'Works with',
    'related_h2' => 'What the forecast is built from',
    'related' => [
        ['href' => '../expense-revenue-tracking/', 'icon' => 'dollar', 'h3' => 'Expense & revenue tracking', 'p' => 'The transaction history every projection is calculated from.'],
        ['href' => '../invoicing/', 'icon' => 'document', 'h3' => 'Invoicing', 'p' => 'Outstanding invoices feed expected income into the forecast.'],
        ['href' => '../report-builder/', 'icon' => 'report', 'h3' => 'Report builder', 'p' => 'Turn the projection into something you can show a lender or an accountant.'],
        ['href' => '../bank-statement-import/', 'icon' => 'bank', 'h3' => 'Bank statement import', 'p' => 'More history in means a sharper forecast out.'],
    ],

    // Drives both the visible accordion and the FAQPage JSON-LD.
    'faqs' => [
    [
        'q' => 'How accurate are the revenue forecasts?',
        'a' => 'Argo Books achieves an average of 88% forecast accuracy in backtesting. Every prediction includes a confidence score so you know exactly how reliable it is. The more data Argo Books has to work with, the more accurate forecasts become over time.',
    ],
    [
        'q' => 'Do I need technical skills to use predictive analytics?',
        'a' => 'Not at all. The analytics engine runs automatically in the background with zero configuration. No formulas, no spreadsheets, no data science degree required. Just use Argo Books normally and forecasts are generated from your real business data. Results are presented in clear, visual charts that anyone can understand.',
    ],
    [
        'q' => 'Can Argo Books detect seasonal patterns in my business?',
        'a' => 'Yes. Argo Books automatically detects bi-monthly and seasonal cycles in your revenue and expenses, and factors these patterns into every forecast. This means your projections account for predictable fluctuations like holiday rushes or slow summer months, giving you a more realistic picture of what\'s ahead.',
    ],
    [
        'q' => 'Is predictive analytics included in the Free plan?',
        'a' => 'Basic real-time analytics are included in the Free plan. Predictive analytics, including revenue forecasting, trend detection, and confidence scoring, is a Premium feature. It\'s one of the most powerful reasons to upgrade, especially for businesses that want to plan ahead with data-driven insights.',
    ],
    ],

    'outro_h2' => 'Stop running your business on last month',
    'outro_p' => 'Download Argo Books and see where your numbers are heading. Free plan, no credit card, and your data stays on your own machine.',
];
