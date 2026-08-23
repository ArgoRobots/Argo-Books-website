<?php
// articles/data/how-to-set-up-bookkeeping-for-a-new-business.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-set-up-bookkeeping-for-a-new-business',

  'h1' => 'How to set up bookkeeping for a new business',

  'meta_title' => 'How to Set Up Bookkeeping for a New Business | Argo Books',

  'meta_description' => 'A from-scratch checklist for setting up bookkeeping for a new business: bank account, method, system, categories, receipts, and a weekly rhythm you can keep.',

  'schema_type' => 'HowTo',

  'category' => 'bookkeeping',
  'hub_weight' => 110,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => 'PT30M',

  'intro_html' => <<<'HTML'
<p>Bookkeeping for a brand-new business isn't complicated, but almost nobody sets it up on day one, and that's the mistake that costs money later. The problem is never the math. It's that six months in, you're staring at a bank account where the grocery run, the client payment, and the software subscription all sit in one long list, and tax season is three weeks away. Setting up properly at the start takes an afternoon and saves you that pile-up entirely.</p>
<p>This guide is a from-scratch checklist, in the order you actually do it. Open a business bank account, pick a method, choose a system, set up your categories, decide how you'll get paid, build a receipt habit, set a simple rhythm, know your tax dates, and run your first report at month end. No accounting background needed, and most of it is free.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Step 1: Open a separate business bank account',
      'anchor' => 'business-bank-account',
      'step_name' => 'Open a separate business bank account',
      'step_text' => 'Open a bank account used only for the business. Route every sale into it and pay every business cost out of it, so business money and personal money never mix.',
      'html' => <<<'HTML'
<p>This is the single most useful thing you can do before you record a single number. One account for the business, nothing personal touching it. Every sale lands here, every business cost gets paid from here, and you pay yourself by transferring money out to your personal account in a clean, visible move.</p>
<p>The reason is simple. When business and personal money share an account, your bookkeeping becomes a sorting job every single month: was that $60 charge groceries or a client lunch? A separate account does the sorting for you. The statement is already your bookkeeping, more or less. It also matters if you ever get asked to prove your numbers, because a mixed account is the first thing that makes a tax reviewer suspicious.</p>
<p>You don't need a fancy business account with monthly fees on day one. A second free checking account in the business name is enough for a sole trader or freelancer starting out. Add a business debit card, and if you can, a separate card for online subscriptions so those don't scatter across your personal statement. If you've registered a company (an LLC, a Ltd, a Pty Ltd), a proper business account is usually required, not optional, because the company's money legally isn't yours.</p>
<p>For the full walkthrough on why this matters and how to untangle things if you've already been mixing, see <a href="/how-to-separate-business-and-personal-finances/">how to separate business and personal finances</a>.</p>
{{illustration:storefront}}
HTML,
    ],

    [
      'h2' => 'Step 2: Pick your method, cash or accrual',
      'anchor' => 'cash-vs-accrual',
      'step_name' => 'Pick your method: cash or accrual',
      'step_text' => 'Decide whether you record money when it actually moves (cash basis) or when it is earned (accrual basis). Most new small businesses start on cash basis.',
      'html' => <<<'HTML'
<p>There are two ways to record money, and you pick one now so every entry is consistent.</p>
<ul>
<li><strong>Cash basis.</strong> You record income when the money actually lands in your account, and expenses when the money actually leaves. If you invoice a client in March but they pay in April, it's April income. Simple, matches your bank statement, and it's what most freelancers and small businesses start on.</li>
<li><strong>Accrual basis.</strong> You record income when you earn it (the day you send the invoice) and expenses when you incur them, regardless of when cash moves. That March invoice is March income even if it's paid in April. This gives a truer picture of a busy month, but it's more work and usually only required once you're bigger or holding inventory.</li>
</ul>
<p>For a new one-person or small business, cash basis is almost always the right starting point. It's easier to keep, it lines up with what's really in the bank, and in the US and Australia you can generally use it under common turnover thresholds. Some businesses are required to use accrual once they cross a size limit or carry stock, and the rules differ by country, so check with an accountant for your situation before you lock it in.</p>
<p>One useful thing to know: you don't always have to choose between the two forever. Good tools can show you both views. In Argo Books, the dashboard and analytics run on a cash basis (only invoices that have actually been paid), while the Reports run on an accrual basis (all invoiced revenue, paid or not). That means you get the day-to-day "what's in the bank" number and the "what did I actually earn" number without keeping two sets of books. For the deeper comparison, see <a href="/cash-basis-vs-accrual-accounting/">cash basis vs accrual accounting</a>.</p>
HTML,
    ],

    [
      'h2' => 'Step 3: Choose a system to record it in',
      'anchor' => 'choose-a-system',
      'step_name' => 'Choose a system',
      'step_text' => 'Start with a simple spreadsheet if you have only a handful of transactions, and move to a bookkeeping app once you are invoicing clients and tracking real expenses.',
      'html' => <<<'HTML'
<p>Now you need somewhere to write the numbers down. You have two honest choices, and the right one depends on how much is actually happening.</p>
<p><strong>A spreadsheet</strong> is a fine place to start if you have a handful of transactions a month and no invoices to send yet. One tab for income, one for expenses, columns for date, description, category, and amount. It costs nothing and you already know how it works. The catch is that it stays manual: you type every row, you build your own totals, and there's no one chasing an unpaid invoice for you.</p>
<p><strong>A bookkeeping app</strong> earns its place the moment you start sending invoices and tracking real expenses. The tipping point is usually around five invoices a month, or the first time a spreadsheet drops a payment you were sure you'd recorded. An app tracks what's paid and what's outstanding, categorizes expenses, handles sales tax, and builds your reports without you summing a single column.</p>
<p>Here's where Argo Books fits, and it fits this step cleanly. It's free to start, so a brand-new business with no revenue yet pays nothing to set up. It runs offline as a desktop app, so your books and your customer data stay on your own computer rather than on someone else's server. And it needs no accounting background: you fill in guided forms, not a ledger. That combination means you can install it, set up your categories, and send your first invoice the same day you start, without a subscription and without a training course. If you outgrow a spreadsheet later, moving over is straightforward too, since the app maps your columns for you. See <a href="/how-to-move-from-spreadsheets-to-bookkeeping-software/">how to move from spreadsheets to bookkeeping software</a> for that path.</p>
<p>Whatever you pick, pick one and put everything in it. The worst system is three half-finished ones.</p>
HTML,
    ],

    [
      'h2' => 'Step 4: Set up your expense categories',
      'anchor' => 'expense-categories',
      'step_name' => 'Set up your expense categories',
      'step_text' => 'Create a short list of expense categories that match how you actually spend and how your tax form groups deductions, then sort every cost into one of them.',
      'html' => <<<'HTML'
<p>Categories are just buckets for your spending. Their whole job is to answer two questions at year end: how much did I spend, and on what? Get them right and your tax return mostly fills itself in, because the categories line up with the boxes on the form.</p>
<p>Don't overthink it. Start with a short list that matches how you actually spend, and add to it only when a real expense doesn't fit anywhere. A common starting set for a service business:</p>
<ul>
<li><strong>Software and subscriptions</strong> (your app tools, hosting, cloud storage)</li>
<li><strong>Equipment and supplies</strong> (a laptop, tools, materials)</li>
<li><strong>Advertising and marketing</strong> (ads, a website, business cards)</li>
<li><strong>Vehicle and travel</strong> (fuel, parking, fares, a hotel for a job)</li>
<li><strong>Fees and charges</strong> (bank fees, payment processing, professional memberships)</li>
<li><strong>Contractors and subcontractors</strong> (anyone you pay to help)</li>
<li><strong>Office and admin</strong> (phone, internet, postage, stationery)</li>
</ul>
<p>The mistake to avoid is going too fine. Twenty-five categories with one transaction each tells you nothing and makes every entry a decision. Aim for eight to twelve that you'll actually use. You can always split a busy bucket later. For a fuller breakdown of which costs go where, see <a href="/business-expense-categories/">business expense categories</a>, and if you're unsure whether something even counts, <a href="/what-counts-as-a-business-expense/">what counts as a business expense</a>.</p>
HTML,
    ],

    [
      'h2' => 'Step 5: Decide how you will invoice and get paid',
      'anchor' => 'invoicing-and-getting-paid',
      'step_name' => 'Decide how you will invoice and get paid',
      'step_text' => 'Settle on an invoice format, a numbering scheme, payment terms, and how customers pay you, before you send the first bill.',
      'html' => <<<'HTML'
<p>If you sell to clients rather than over a counter, invoicing is your cash flow, so decide the mechanics before the first job wraps up, not while you're staring at a blank template afterwards.</p>
<p>Four things to settle:</p>
<ul>
<li><strong>The format.</strong> A clean invoice with your business name, the client's details, a unique invoice number, line items, and a total. Send it as a PDF so the layout is locked and it opens on any device.</li>
<li><strong>The numbering.</strong> Pick a scheme and never reuse a number. Sequential (0001, 0002) is fine; many people prefix the year (2026-0001) so the books reset cleanly each January.</li>
<li><strong>The terms.</strong> Due on Receipt gets you paid fastest for small one-off jobs. Net 15 or Net 30 is normal for business clients. Write the terms and any late fee on the invoice itself so nothing is a surprise.</li>
<li><strong>How they pay.</strong> Bank transfer is cheap but slower. A pay-by-card link on the invoice gets you paid faster but costs a processing fee. Many new businesses offer both and let the client choose.</li>
</ul>
<p>This is where a bookkeeping app quietly pays for itself. Argo Books lets you build a branded invoice, add an online payment link, and then tracks the whole life of it through statuses like Sent, Viewed, Partial, Paid, and Overdue, so you always know what's still owed. It handles partial payments and deposits too, which matters if you take money up front on bigger jobs. If you just want to get a single invoice out the door right now without installing anything, the free <a href="/invoice-generator/">invoice generator</a> on this site does the job. For the full walkthrough, see <a href="/how-to-invoice-clients/">how to invoice clients</a>.</p>
HTML,
    ],

    [
      'h2' => 'Step 6: Build a receipt habit from day one',
      'anchor' => 'receipt-habit',
      'step_name' => 'Build a receipt habit from day one',
      'step_text' => 'Capture every business receipt the moment you get it, digitally, so you never lose a deduction to a faded slip or a forgotten purchase.',
      'html' => <<<'HTML'
<p>Every receipt you keep is a deduction that lowers your tax bill. Every one you lose is money you paid tax on for no reason. The businesses that struggle at tax time aren't the ones with complicated books, they're the ones with a shoebox of faded paper and no idea what half of it was for.</p>
<p>The habit is simple: capture the receipt the moment it lands, not at month end. A paper receipt from a hardware store gets photographed in the parking lot before it goes in your pocket. An emailed receipt gets filed the day it arrives. The rule that works is "capture it now," because "I'll sort it later" is how the shoebox happens.</p>
{{illustration:checklist}}
<p>Snap it digitally and you solve two problems at once: the paper can fade or go missing, and you'd otherwise have to type the details in by hand. This is exactly what receipt scanning is for. In Argo Books you import that photo and the AI pulls out the vendor, date, amount, and tax, then drops it into the right expense category. The free tier covers 10 receipts a month, which is plenty for a new business finding its feet, and Premium raises that to 500. There's also a free web receipt scanner on this site if you just want to try it. Most tax authorities want you to keep receipts for several years (commonly five to seven, though it varies by country), so a digital copy that never fades is worth having regardless. See <a href="/how-to-scan-and-organize-receipts/">how to scan and organize receipts</a> for a system that scales.</p>
HTML,
    ],

    [
      'h2' => 'Step 7: Set a simple weekly and monthly rhythm',
      'anchor' => 'bookkeeping-rhythm',
      'step_name' => 'Set a simple weekly and monthly rhythm',
      'step_text' => 'Put a short weekly bookkeeping session and a slightly longer monthly one on the calendar, so the work never piles up into a year-end scramble.',
      'html' => <<<'HTML'
<p>Bookkeeping stays easy only if it stays small, and it stays small only if you do a little of it often. The businesses that hate their books are the ones that touch them once a year. Put two recurring blocks on your calendar and defend them.</p>
<p><strong>Weekly, about fifteen minutes.</strong> Capture any receipts you missed, record new expenses, mark paid invoices as paid, and send a nudge on anything overdue. That's it. Fifteen minutes on a Friday keeps the whole thing current.</p>
<p><strong>Monthly, about an hour.</strong> Check your records against your bank statement so nothing is missing or double-counted, categorize anything you left as "uncategorized," follow up seriously on overdue invoices, and glance at how the month went. This monthly check is where you catch the small stuff before it becomes a year-end mountain.</p>
<p>If you dread it, shrink it. A weekly session you actually do beats a monthly one you keep skipping. The point isn't to be an accountant, it's to never again face a full year of untouched transactions three weeks before a deadline. If you want to run the whole thing yourself without hiring help, <a href="/how-to-do-bookkeeping-without-an-accountant/">how to do bookkeeping without an accountant</a> lays out the full routine.</p>
HTML,
    ],

    [
      'h2' => 'Step 8: Know your tax dates and register if you need to',
      'anchor' => 'tax-dates',
      'step_name' => 'Know your tax dates and register if you need to',
      'step_text' => 'Find out which taxes apply to you, when they are due, and whether you need to register for a sales tax or business number, then set aside money as you earn.',
      'html' => <<<'HTML'
<p>You don't need to become a tax expert, but you do need to know three things: which taxes apply to you, when they're due, and whether you have to register for anything. Miss a registration threshold or a filing date and the penalty is real money, so this goes on the list early.</p>
<p>The picture varies by country:</p>
<ul>
<li><strong>United States.</strong> Self-employed income is reported on your annual return, and many people also pay quarterly estimated taxes through the year. Sales tax is set at the state (and sometimes city) level, with rules that differ everywhere. If you'll owe a meaningful amount, see <a href="/how-to-pay-quarterly-estimated-taxes/">how to pay quarterly estimated taxes</a>.</li>
<li><strong>Canada.</strong> You must register for GST/HST once revenue passes $30,000 CAD over four quarters. Income tax is annual, with quarterly instalments once your tax owing gets large enough.</li>
<li><strong>United Kingdom.</strong> Self-employment is reported through Self Assessment. You must register for VAT once turnover passes GBP 90,000 over a rolling 12 months.</li>
<li><strong>Australia.</strong> You'll generally want an ABN, and you must register for GST once turnover passes $75,000 AUD, with a Business Activity Statement to lodge after that.</li>
</ul>
<p>Thresholds and dates change and vary by situation, so confirm yours with an accountant or your tax authority rather than treating this as the final word. The habit that saves you either way is setting money aside as you earn it. A common rule of thumb is to park 25 to 30% of profit in a separate account so the tax bill is already covered when it lands. Argo Books helps here by tracking Tax Collected on your invoices and Tax Paid on your expenses, so the net figure is ready when you need it. It shows you the number; it doesn't file or remit the tax for you. For the setting-aside math, see <a href="/how-much-to-set-aside-for-taxes-self-employed/">how much to set aside for taxes when self-employed</a>.</p>
HTML,
    ],

    [
      'h2' => 'Step 9: Run your first report at month end',
      'anchor' => 'first-report',
      'step_name' => 'Run your first report at month end',
      'step_text' => 'At the end of your first month, produce an income statement so you can see revenue, expenses, and whether you actually made money.',
      'html' => <<<'HTML'
<p>All of this setup exists to answer one question: is the business making money? At the end of your first month, run an income statement (also called a profit and loss, or P&L) and find out. It's the report that lists your revenue at the top, your expenses below, and the difference at the bottom. That bottom number is your profit, and it's the first honest read on how the business is doing.</p>
<p>Reading it is straightforward. Revenue is what you earned. Expenses are what it cost you to earn it, grouped by the categories you set up in Step 4. Net profit is revenue minus expenses, with one detail worth knowing: sales tax you collected isn't yours, so it doesn't count as profit. It's money you're holding for the government. A good report leaves it out of the profit line automatically.</p>
<p>Doing this by hand in a spreadsheet means summing every category and building the layout yourself. This is the other place Argo Books saves real time. Its Report Builder is free and turns your recorded data into a clean income statement, a balance sheet, and tax summaries, then exports a branded PDF you can keep or hand to an accountant. Remember the method detail from Step 2: the reports run on an accrual basis, counting all invoiced revenue whether or not it's been paid, which is the view an accountant and a tax return want. Run it once at your first month end and the setup you did over the last afternoon pays off in a single click. To understand the report itself, see <a href="/what-is-a-profit-and-loss-statement/">what is a profit and loss statement</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books is the free, offline app these steps are built around. Download it and set your books up today.',
  'tool_callout_cta' => 'Download Argo Books free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'When should I start bookkeeping for a new business?',
      'a' => 'On day one, before the first sale or the first expense. The moment you spend money on the business or take money in, that transaction should be recorded somewhere. Starting from the very first entry is far easier than trying to rebuild months of history later from a mixed bank statement and a pile of receipts. Even if the business is tiny and pre-revenue, open the bank account and start capturing costs now. Startup costs you record before you earn a cent are often deductible, so catching them early can lower your first tax bill.',
    ],
    [
      'q' => 'Should I use a spreadsheet or software to start?',
      'a' => 'Start with a spreadsheet if you have only a handful of transactions a month and you are not sending invoices yet. It is free and you already know how it works. Switch to a bookkeeping app once you are invoicing clients or your expenses get busy, usually around five invoices a month or the first time a spreadsheet drops something. An app tracks what is paid and what is outstanding, categorizes expenses, and builds your reports for you. Argo Books is free to start and runs offline, so you can move over without a subscription whenever the spreadsheet stops keeping up.',
    ],
    [
      'q' => 'What do I need to set up on day one?',
      'a' => 'Four things. A separate business bank account so business and personal money never mix. A decision on your method, which is almost always cash basis to start. A system to record in, either a simple spreadsheet or a free app. And a receipt habit, meaning you capture every business receipt the moment you get it rather than at month end. With those four in place, everything else, categories, invoicing, tax dates, and reports, slots in on top without a scramble.',
    ],
    [
      'q' => 'How often should I update my books?',
      'a' => 'A little and often beats a lot at year end. Aim for a short weekly session of about fifteen minutes to capture receipts, record expenses, and mark paid invoices, plus a longer monthly session of about an hour to check your records against your bank statement and tidy up any uncategorized items. That rhythm keeps the work small and stops it piling up into a stressful year-end catch-up. If you can only manage one, the weekly touch is the one to keep, because it is the one that stops things slipping through the cracks.',
    ],
    [
      'q' => 'Do I need an accountant for a new business?',
      'a' => 'Not usually for the day-to-day bookkeeping. A one-person or small service business can record income, track expenses, send invoices, and run a monthly income statement on its own with a simple system. Where an accountant earns their fee is on the judgment calls: which structure to register, whether cash or accrual applies to you, what you can and cannot deduct, and getting the first tax return right. A common approach is to do your own books through the year and pay an accountant for a review at tax time. Keep clean records and that review is cheaper, because they spend less time sorting out a mess.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'cleaning',
    'generic',
  ],

  'related_article_slugs' => [
    'small-business-bookkeeping-basics',
    'how-to-do-bookkeeping-without-an-accountant',
    'how-to-separate-business-and-personal-finances',
    'cash-basis-vs-accrual-accounting',
  ],
];
