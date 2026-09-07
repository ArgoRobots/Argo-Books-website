<?php
// compare/data/quickbooks-payroll-alternatives.php
//
// Content for /compare/quickbooks-payroll-alternatives/. Layout lives in
// compare/compare-page.php; the price chart in compare/mockups/.
//
// QuickBooks Payroll is an add-on, so every figure here is the Online plan plus
// the payroll base plus the per-employee fee. The payroll line alone
// understates it.

if (!defined('ARGO_TEMPLATE_RENDER')) {
    http_response_code(404);
    exit;
}

// Also read by compare/mockups/quickbooks-payroll-alternatives.php.
$qbo_base    = competitor_price('quickbooks', 'easystart');
$qbp_base    = competitor_price('quickbooks-payroll', 'unlimited');
$qbp_per     = get_competitors()['quickbooks-payroll']['plans']['unlimited']['per_employee'];
$qbp_solo    = competitor_price('quickbooks-payroll', 'solo');
$qbp_solo_pr = get_competitors()['quickbooks-payroll']['plans']['solo']['per_employee'];

// The whole stack at five people: books plus payroll plus headcount.
$qb_stack_five = $qbo_base + payroll_monthly_cost('quickbooks-payroll', 'unlimited', 5);
$qb_gap_year   = ($qb_stack_five - $argo_monthly) * 12;

return [
    'competitor' => 'QuickBooks Payroll',

    'breadcrumb' => 'QuickBooks Payroll alternatives',
    'title' => 'QuickBooks Payroll Alternatives for Canada (2026) | Argo Books',
    'meta_description' => 'Looking for a QuickBooks Payroll alternative in Canada? QuickBooks bills payroll on top of a QuickBooks Online subscription, plus a fee per employee. Argo Books is $' . $argo_monthly . ' CAD flat, books and payroll together.',
    'meta_keywords' => 'QuickBooks Payroll alternatives, QuickBooks Payroll alternative Canada, cheaper than QuickBooks Payroll, Canadian payroll software, T4 software, flat rate payroll, payroll without per employee fees, QuickBooks Online alternative',
    'og_title' => 'QuickBooks Payroll alternatives for Canadian small businesses',
    'og_description' => 'QuickBooks charges for the books, then for payroll, then per employee. Argo Books charges once. Compared on deductions, T4s, Quebec and what each one files for you.',

    'hero_eyebrow' => 'QuickBooks Payroll alternative',
    'hero_h1' => '<span class="text-gradient">QuickBooks Payroll</span><br>alternatives',
    'hero_subtitle' => 'QuickBooks Payroll is an add-on, so the real bill is the QuickBooks Online plan, plus the payroll base fee, plus a few dollars per employee every month. Argo Books does the books and Canadian payroll together for $' . $argo_monthly . ' CAD flat.',

    'differences_h2' => 'What does QuickBooks Payroll actually cost in Canada?',
    'differences_desc' => 'Three charges, not one. You need a QuickBooks Online subscription from about $' . $qbo_base . ' a month, the payroll add-on from about $' . $qbp_base . ', and then $' . $qbp_per . ' per employee per month on top. At five staff that stack comes to roughly $' . number_format($qb_stack_five) . ' a month. Argo Books includes Canadian payroll in Premium at $' . $argo_monthly . ', with no per-employee component.',
    'why_h3' => 'Why look at Argo Books instead?',
    'why_list' => [
        '<strong>One charge instead of three.</strong> Books, payroll and headcount are a single $' . $argo_monthly . ' CAD a month, rather than a subscription plus an add-on plus a per-person fee.',
        '<strong>Roughly $' . number_format($qb_gap_year) . ' a year less at five employees</strong>, on QuickBooks\' own published rates for the cheapest plans that do the job.',
        '<strong>Quebec handled as its own system.</strong> QPP, QPIP, Quebec income tax and the federal abatement, with RL-1 slips, rather than an approximation of the federal calculation.',
        '<strong>Your staff records stay on your computer.</strong> Social insurance numbers and salaries are written locally instead of held in a cloud account.',
        '<strong>No price creep.</strong> The pattern people leave QuickBooks over is the annual increase on a plan they are already deep into. A flat desktop price is a different arrangement.',
    ],
    'callout_title' => 'Three line items',
    'callout_sub' => 'QuickBooks Online + payroll add-on + $' . $qbp_per . ' per employee, against one flat $' . $argo_monthly,

    // Feature, Argo Free, Argo Premium, QuickBooks Payroll.
    'table_argo_sub' => '$' . $argo_monthly . ' CAD/month, books + payroll',
    'table_competitor_sub' => '~$' . number_format($qb_stack_five) . '/month at 5 staff',
    'table_rows' => [
        ['CPP, EI &amp; income tax from CRA tables', 'no', 'yes', 'yes'],
        ['Every province and territory', 'no', 'yes', 'yes'],
        ['Quebec (QPP, QPIP, RL-1)', 'no', 'yes', 'yes'],
        ['Pay stubs', 'no', 'yes', 'yes'],
        ['T4 slips &amp; summary (PDF)', 'no', 'yes', 'yes'],
        ['T4 XML for CRA filing', 'no', 'yes', 'yes'],
        ['Files the T4s for you', 'no', 'no', 'yes'],
        ['Remits source deductions for you', 'no', 'no', 'yes'],
        ['Direct deposit to staff', 'no', 'no', 'yes'],
        ['Accounting included, not a separate plan', 'yes', 'yes', 'no'],
        ['Flat price, unlimited employees', 'yes', 'yes', 'no'],
        ['Works offline', 'yes', 'yes', 'no'],
        ['Staff data stored locally', 'yes', 'yes', 'no'],
        ['Auto bank transaction import', 'no', 'no', 'yes'],
        ['Mobile app', 'no', 'no', 'yes'],
    ],

    'pros_cons_h2' => 'Argo Books vs QuickBooks Payroll: pros &amp; cons',
    'argo_pros' => [
        '<strong>One flat $' . $argo_monthly . ' CAD a month</strong> covering the books and Canadian payroll together',
        '<strong>No per-employee fee</strong>, so hiring does not change the bill',
        '<strong>No add-on structure</strong>, so there is no plan to upgrade when you take on staff',
        '<strong>Staff records held locally</strong> rather than in a cloud account',
        '<strong>Runs offline</strong> as a native desktop app for Windows and Linux',
    ],
    'argo_cons' => [
        'You upload the T4 XML and remit the source deductions yourself',
        'No direct deposit, so paying staff is still a bank transfer you make',
        'No automatic bank transaction import yet, which QuickBooks does well',
        'No mobile app, and no Mac build yet',
        'Payroll covers Canada only',
    ],
    'competitor_cons' => [
        '<strong>Three charges stacked</strong>: the QuickBooks Online plan, the payroll add-on, then a fee per employee',
        '<strong>Priced per head</strong>, so the monthly bill grows every time you hire',
        '<strong>Known for price increases</strong> on plans people have already committed their books to',
    ],
    'competitor_pros' => [
        'Files T4s and remits source deductions to the CRA for you',
        'Direct deposit built in, so staff are paid without a separate transfer',
        'Automatic bank feeds, which Argo Books does not have yet',
        'A large accountant network, so most Canadian bookkeepers already know it',
    ],

    'key_h2' => 'The cost is the add-on, not the payroll',
    'key_desc' => 'QuickBooks calculates Canadian deductions correctly and files for you, and for many businesses that is worth paying for. The problem people bring to a comparison page is rarely the payroll itself. It is that payroll arrives as a third line on a bill that already had two.',
    'key_cards' => [
        ['tone' => '', 'icon' => 'subscription', 'h3' => 'One line, not three', 'p' => 'Books, payroll and headcount are the same $' . $argo_monthly . ' a month in Argo Books. There is no add-on to enable and no plan tier tied to how many people you employ.'],
        ['tone' => 'purple', 'icon' => 'users', 'h3' => 'Hiring is free', 'p' => 'At $' . $qbp_per . ' per employee a month, taking on four people adds about $' . number_format($qbp_per * 4 * 12) . ' a year to a QuickBooks bill. In Argo Books it adds nothing.'],
        ['tone' => 'green', 'icon' => 'map-pin', 'h3' => 'Quebec done properly', 'p' => 'QPP, QPIP, Quebec income tax and the federal abatement calculated separately, with RL-1 slips and summary at year end.'],
    ],

    'honest' => [
        'QuickBooks Payroll does two things Argo Books does not: it files your T4s with the CRA and it remits your source deductions, and it pays your staff by direct deposit. If you want the deadline to be somebody else\'s responsibility, that service is what the per-employee fee buys, and QuickBooks is a reasonable place to buy it.',
        'It is also the software most Canadian bookkeepers already know, and automatic bank feeds are genuinely useful. If your bookkeeper works in QuickBooks and bank feeds are central to how you close a month, switching to save money is likely to cost you more than it saves.',
        'Argo Books is the better fit if you are comfortable uploading the file and making the payment yourself, and the growing monthly bill is what actually bothers you.',
    ],

    'related' => [
        'argo-books-vs-wagepoint',
        'payworks-alternatives',
        'argo-books-vs-quickbooks',
        'sage-50-alternatives',
        'argo-books-vs-wave',
    ],

    'faqs' => [
        ['q_html' => 'How much is QuickBooks Payroll in Canada?', 'a_html' => '<p>It is an add-on, so there are three parts: a QuickBooks Online subscription from about $' . $qbo_base . ' CAD a month, the payroll add-on from about $' . $qbp_solo . ' for one pay run a month or $' . $qbp_base . ' for unlimited runs, and then about $' . $qbp_per . ' per employee per month.</p>
                            <p>At five staff that stack is roughly <strong>$' . number_format($qb_stack_five) . ' CAD a month</strong>. Argo Books Premium is $' . $argo_monthly . ' CAD a month including the books and payroll together. Check current rates with Intuit before switching, since add-on pricing changes.</p>'],
        ['q_html' => 'Is there a free QuickBooks Payroll alternative?', 'a_html' => '<p>Not for payroll itself. Argo Books has a free tier covering invoicing, expenses, receipt scanning and reports, but Canadian payroll is a Premium feature at $' . $argo_monthly . ' CAD a month.</p>
                            <p>Be sceptical of anything advertising free Canadian payroll. Keeping up with two CRA rate editions a year, Quebec\'s separate system and the T4 filing specification is ongoing work, and software that is not funded to do it is the software that is quietly wrong in February.</p>'],
        ['q_html' => 'Will Argo Books file my T4s with the CRA?', 'a_html' => '<p>No. It prepares the T4 slips and summary as PDFs and builds the XML submission file the CRA accepts, including the T619 transmittal record, so the figures are worked out and the file is ready. You upload it through My Business Account and you make the remittance yourself.</p>
                            <p>QuickBooks does file and remit on your behalf. That is a real difference and it is the main reason to stay.</p>'],
        ['q_html' => 'Can I move my QuickBooks data into Argo Books?', 'a_html' => '<p>Partly. Argo Books imports spreadsheets with AI-assisted column matching, so customers, products, and transaction history exported from QuickBooks as CSV can be brought across. Payroll year-to-date figures are the part to be careful with: they have to carry over exactly or your T4s will be wrong at year end.</p>
                            <p>Because of that, the safest time to switch payroll is the start of a calendar year.</p>'],
        ['q_html' => 'Does Argo Books handle Quebec?', 'a_html' => '<p>Yes. Quebec runs its own income tax, pension plan and parental insurance, so Argo Books calculates QPP, QPIP, Quebec income tax and the federal abatement as a separate system, and produces RL-1 slips and summary at year end. Employees in different provinces can sit on the same pay run.</p>'],
        ['q_html' => 'What about bank feeds?', 'a_html' => '<p>QuickBooks has automatic bank transaction import and Argo Books does not yet. What Argo Books has is AI-assisted bank statement import, so you bring the statement in as a file rather than connecting the account.</p>
                            <p>If automatic feeds are central to how you work, that is a genuine reason to stay with QuickBooks.</p>'],
    ],

    'cta_h2' => 'Add up what payroll is really costing you',
    'cta_p' => 'Put your headcount into the calculator on our payroll page, including the QuickBooks Online plan underneath it, then download Argo Books free and run a period you have already run.',
];
