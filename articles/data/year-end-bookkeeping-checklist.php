<?php
// articles/data/year-end-bookkeeping-checklist.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'year-end-bookkeeping-checklist',

  'h1' => 'Year-end bookkeeping checklist for small businesses',

  'meta_title' => 'Year-End Bookkeeping Checklist (Step by Step) | Argo Books',

  'meta_description' => 'A step-by-step year-end bookkeeping checklist for small businesses: gather transactions, check against your bank, chase invoices, count stock, and run reports.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 45,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 11,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Closing out the books at year-end has a reputation for being miserable, and there's a reason: most of the misery comes from doing a whole year of bookkeeping in one sitting. If your records are twelve months behind, this checklist is the honest, slightly painful version of the fix. If they're roughly up to date, it's a calm afternoon or two of tidying. Either way, the steps are the same, and none of them need an accounting degree.</p>
<p>Here's the plan: get every transaction into your books, check them against your bank so nothing is missing or counted twice, deal with unpaid invoices, count your stock if you sell products, round up your deductions, run the year-end reports, set money aside for tax, and archive the lot. Work through the steps in order, because each one depends on the ones before it. By the end you'll know exactly what your business made this year, and you'll be ready for whatever your tax filing asks of you.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Step 1: get every transaction into your books',
      'anchor' => 'gather-transactions',
      'step_name' => 'Get every transaction into your books',
      'step_text' => 'Collect twelve months of bank and card statements, cash sales, and paper receipts, and enter anything that is not already recorded so the year is complete before you start checking it.',
      'html' => <<<'HTML'
<p>Nothing else on this checklist works until the raw material is in. Before you check, categorize, or report on anything, every transaction from the year needs to be recorded somewhere: every sale, every expense, every fee, every transfer.</p>
{{illustration:checklist}}
<p>Round up your sources first:</p>
<ul>
<li><strong>Bank and credit card statements</strong> for all twelve months, for every account the business touches. Download them now while they're easy to get; some banks make older statements annoying to reach.</li>
<li><strong>Cash sales</strong> from markets, in-person jobs, or anywhere else money changed hands without a paper trail. Notebooks, texts to yourself, and memory all count as sources, just get the amounts written down.</li>
<li><strong>The shoebox of receipts.</strong> Every business has one, whether it's a literal shoebox, a glovebox, or a folder of crumpled paper. Empty it.</li>
<li><strong>Platform reports</strong> if you sell through a marketplace or take card payments: the payout that landed in your bank is smaller than your real sales, and the fees in between are deductible expenses you want recorded.</li>
</ul>
<p>Then enter whatever isn't already in your books. This is the slow part if you've been putting it off all year, but modern tools shrink it a lot. Argo Books, for example, can scan a photo of a receipt into an expense and import a bank statement or spreadsheet with AI doing the reading, so a pile of paper and a stack of PDFs becomes entries instead of an evening of typing. However you do it, the finish line for this step is simple: every dollar in and every dollar out for the year exists as a record.</p>
HTML,
    ],

    [
      'h2' => 'Step 2: check your records against your bank',
      'anchor' => 'check-against-bank',
      'step_name' => 'Check your records against your bank',
      'step_text' => 'Compare your books to each bank and card statement, month by month, and fix anything missing, doubled, or entered with the wrong amount until the balances agree.',
      'html' => <<<'HTML'
<p>Now prove the records are right. The way to do that is to match up your books against the one source that doesn't forget or exaggerate: your bank.</p>
<p>Work through each account, month by month. For every month, compare the transactions in your books to the transactions on the statement, and check that the closing balance in your books matches the closing balance the bank shows. You're hunting for three kinds of problems:</p>
<ul>
<li><strong>Missing entries.</strong> A charge on the statement that never made it into your books. Usually a subscription, a bank fee, or an expense you forgot.</li>
<li><strong>Doubled entries.</strong> The same sale or expense recorded twice, often once from a receipt and again from the statement import.</li>
<li><strong>Wrong amounts.</strong> A typo, a transposed digit, or a payment recorded at the invoice amount when the client actually paid something different.</li>
</ul>
<p>When a month's closing balance in your books matches the bank, you can trust that month and move on. When it doesn't, the difference tells you how big the problem is, and it's almost always one of the three above. Don't skip this step to save time. Every number you produce later, profit, tax owed, all of it, inherits its accuracy from here. An hour of matching up now beats discovering in April that your income was overstated by a doubled deposit.</p>
<p>One habit worth stealing from bookkeepers: work oldest month first. Fixes in January ripple forward, so starting at the back means you never fix the same discrepancy twice.</p>
HTML,
    ],

    [
      'h2' => 'Step 3: chase and clean up unpaid invoices',
      'anchor' => 'unpaid-invoices',
      'step_name' => 'Chase and clean up unpaid invoices',
      'step_text' => 'List every invoice still unpaid, send reminders on the collectable ones, and write off the ones that are genuinely dead so your books show income you will actually receive.',
      'html' => <<<'HTML'
<p>Pull up a list of every invoice that's still unpaid, and be honest about each one. They fall into two piles, and each pile gets different treatment.</p>
<p><strong>The collectable pile.</strong> Invoices that are late but alive: the client is responsive, the work isn't disputed, the money is coming. Year-end is a natural excuse to nudge these, and "I'm closing my books for the year" is a genuinely effective line in a payment reminder, because it signals you're organized and paying attention. Send the reminders now so the money has a chance to land before you file. If a client has gone quiet or is actively dodging, our guide on <a href="/what-to-do-when-a-client-does-not-pay/">what to do when a client doesn't pay</a> walks through the escalation steps, from friendly reminder to final demand.</p>
<p><strong>The dead pile.</strong> Some invoices aren't late, they're gone: the client vanished, the company folded, or the amount is too small to be worth pursuing. Leaving these on your books forever quietly inflates what your business appears to be owed, and in some tax systems, if you report income when you invoice rather than when you're paid, writing off a genuinely uncollectable invoice can reduce your taxable income. The rules on that vary by country, so check with your local tax authority or an accountant before claiming it. Either way, mark dead invoices as written off so your books reflect money you'll actually receive, not money you're owed in theory.</p>
<p>The end state for this step: every open invoice is either freshly chased or deliberately written off, and nothing is sitting in limbo.</p>
HTML,
    ],

    [
      'h2' => 'Step 4: count your inventory and work out cost of goods sold',
      'anchor' => 'inventory-cogs',
      'step_name' => 'Count inventory and work out cost of goods sold',
      'step_text' => 'If you sell products, physically count what stock you hold at year-end, value it at what it cost you, and use it to calculate your cost of goods sold for the year.',
      'html' => <<<'HTML'
<p>If you sell services only, skip to the next step. If you sell physical products, this step is where your real profit number comes from, and it needs an actual count, not a guess.</p>
<p>Pick a day as close to year-end as you can and count what you're holding: finished products, and raw materials if you make things. Value the count at what the stock cost you, not what you'd sell it for. A shelf of items you'd sell for $2,000 that cost you $800 to buy or make is $800 of inventory.</p>
<p>The reason this matters is cost of goods sold, usually shortened to COGS: the cost of the specific items you sold this year, as opposed to everything you bought this year. The classic year-end calculation is:</p>
<p><strong>Opening inventory + purchases during the year − closing inventory = cost of goods sold.</strong></p>
<p>Say you started the year holding $1,000 of stock, bought $5,000 more during the year, and your count shows $1,500 still on the shelf. Your COGS is $4,500, and that's the number that comes off your sales to give your gross profit. The stock still on the shelf isn't an expense yet; it becomes one when it sells, which is exactly why the count matters. Get the count wrong and your profit is wrong in the same breath. If the concept is new, our plain-language guide to <a href="/what-is-cost-of-goods-sold/">cost of goods sold</a> covers it properly, including what belongs in COGS and what doesn't.</p>
<p>While you're counting, note anything damaged, expired, or realistically unsellable. Stock like that can usually be written down, and it's better to face it now than to carry imaginary value into next year.</p>
HTML,
    ],

    [
      'h2' => 'Step 5: gather and categorize your deductions',
      'anchor' => 'deductions',
      'step_name' => 'Gather and categorize your deductions',
      'step_text' => 'Sweep the year for deductible expenses, including easily missed ones like home office costs, mileage, and subscriptions, and make sure every expense sits in a sensible category with its receipt kept.',
      'html' => <<<'HTML'
<p>Every legitimate business expense you fail to record is tax you'll pay that you didn't owe. Most of your deductions are already in your books from step 1, so this step is a sweep for the ones that slip through, plus a tidy-up of categories.</p>
<p>The commonly missed ones are worth a deliberate look:</p>
<ul>
<li><strong>Home office costs</strong>, if you work from home: a portion of rent or mortgage interest, utilities, and internet, worked out under your country's rules.</li>
<li><strong>Vehicle and mileage</strong> for business trips, which needs a log or a reasonable record, not a vibe.</li>
<li><strong>Software and subscriptions</strong> that renew quietly on a card and never get thought of as expenses.</li>
<li><strong>Phone and internet</strong>, in whatever business proportion your rules allow.</li>
<li><strong>Professional fees</strong>: your accountant, legal advice, that course you took.</li>
<li><strong>Bank and payment processing fees</strong>, small individually, real in total.</li>
</ul>
<p>Then check your categories. Tax filings ask for expenses grouped by type, so "Miscellaneous: $9,400" is a problem, both because it looks careless to a tax authority and because you can't learn anything from it. Recategorize the big lumps into honest groups now, while you still remember what things were.</p>
<p>Two guides go deeper here: <a href="/small-business-tax-deductions/">small business tax deductions</a> for a fuller list of what's typically claimable, and <a href="/how-long-to-keep-business-receipts/">how long to keep business receipts</a> for the proof side, because a deduction without a record is a deduction you may have to give back in an audit. What counts as deductible varies by country, so treat any list, including ours, as a starting point and confirm the specifics with your local tax authority or an accountant.</p>
HTML,
    ],

    [
      'h2' => 'Step 6: run the year-end reports and actually read them',
      'anchor' => 'year-end-reports',
      'step_name' => 'Run the year-end reports and read them',
      'step_text' => 'Produce a profit and loss statement and a balance sheet for the full year, then read them for the story: what you earned, what it cost, and what looks off.',
      'html' => <<<'HTML'
<p>With clean, complete records, the reports are the payoff. Two matter for almost every small business:</p>
{{illustration:report-statement}}
<p><strong>The profit and loss statement</strong> (also called an income statement) shows the whole year in one page: revenue at the top, cost of goods sold if you have it, expenses by category, and the profit or loss at the bottom. This is the report your tax filing is largely built from, and it's also the report that answers the question you've probably been half-avoiding: did the business actually make money this year?</p>
<p><strong>The balance sheet</strong> is a snapshot of the last day of the year: what the business owns (cash, inventory, money owed to you) and what it owes (cards, loans, tax you've collected but not yet passed on). It's the report that catches things a profit number hides, like healthy profit sitting entirely in unpaid invoices instead of the bank.</p>
<p>Don't just generate them, read them. Compare revenue to last year if you have it. Look at which expense categories grew and whether that growth bought anything. Check that gross profit on your products is where you thought it was. And treat anything surprising as a question to answer, because a surprise in a report is either a lesson about the business or a slip in the books, and both are worth finding now rather than in a tax review.</p>
<p>If you're assembling these by hand from a spreadsheet, this is the step where software earns its keep: Argo Books builds the profit and loss, balance sheet, and tax-ready reports from the records you've already entered, so the reports are a few clicks rather than a weekend of formulas.</p>
HTML,
    ],

    [
      'h2' => 'Step 7: set aside tax, note your deadlines, and archive the year',
      'anchor' => 'tax-archive-reset',
      'step_name' => 'Set aside tax, note deadlines, and archive the year',
      'step_text' => 'Estimate the tax on your profit and move that money aside, write down your filing deadlines, then archive the year\'s records and set up a monthly routine so next year-end takes an hour.',
      'html' => <<<'HTML'
<p>Your profit and loss now tells you roughly what you made, which means you can estimate what the tax bill looks like before it arrives. Move that money into a separate account now, while it still exists, because tax money left in the everyday account has a way of becoming inventory, ads, or a quiet December. If you're not sure what fraction to hold back, our guide on <a href="/how-much-to-set-aside-for-taxes-self-employed/">how much to set aside for taxes</a> gives you a working method.</p>
<p>Then write down your actual deadlines: when the return is due, when payment is due, and whether you're expected to pay in installments through the year rather than in one lump. Deadlines and rules vary a lot by country, and in many places growing businesses are required to make <a href="/how-to-pay-quarterly-estimated-taxes/">quarterly estimated payments</a>, so check the dates with your tax authority rather than assuming last year's calendar still applies.</p>
<p>Finally, close the loop:</p>
<ul>
<li><strong>Archive everything.</strong> Export or save the year's reports, statements, receipts, and invoice records together, with a backup somewhere other than the machine they live on. Most tax systems expect you to keep records for several years, so make the bundle you'd hand an auditor and then forget about it.</li>
<li><strong>Set up next year to be easy.</strong> The difference between the painful version of this checklist and the pleasant one is entirely about when the work happens. A monthly hour, entering the month's transactions, matching them against the bank, filing the receipts, means next year-end is a review, not an excavation. Book that hour now, first week of every month, while the memory of the shoebox is fresh.</li>
</ul>
<p>That's the whole close. Books complete, checked against the bank, invoices resolved, stock counted, deductions claimed, reports read, tax money parked, records archived. Done properly once, it's mostly a system that maintains itself.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books scans the receipt shoebox, imports your bank statements with AI, and turns the year into a profit and loss, balance sheet, and tax-ready reports.',
  'tool_callout_cta' => 'See the report builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'How long does year-end bookkeeping take for a small business?',
      'a' => 'It depends almost entirely on the state of your records going in. If you\'ve kept things roughly current through the year, closing the books is an afternoon or two: a final check against the bank, a stock count if you sell products, the reports, and the archive. If you\'re starting from twelve months of statements and a shoebox of receipts, budget several sessions, because step one, getting every transaction entered, is the slow part. Receipt scanning and statement import tools cut that time substantially. Whatever your starting point, work the steps in order rather than jumping to the reports, since every later number depends on the earlier steps being complete.',
    ],
    [
      'q' => 'Do I need an accountant to close my books at year-end?',
      'a' => 'For most small, simple businesses, no: the closing itself, gathering transactions, checking against the bank, counting stock, and running reports, is well within reach of a careful owner, and this checklist covers it. Where an accountant earns their fee is at the filing stage and on judgment calls: whether a write-off is claimable in your country, how home office rules apply to you, or how to handle something unusual like selling equipment or taking on a loan. A common middle path is doing the bookkeeping yourself all year, then paying an accountant a small amount to review the year and file. Clean books make that review cheap; a shoebox makes it expensive.',
    ],
    [
      'q' => 'What if I am missing receipts for some of my expenses?',
      'a' => 'Record the expense anyway if it genuinely happened and you can support it another way: a bank or card statement line, an emailed confirmation, or a supplier who can reissue the invoice, which most will do on request. A statement line is weaker evidence than a receipt in an audit, but it\'s far better than nothing, and dropping real expenses just because the paper is gone means paying tax you don\'t owe. Rules on acceptable proof vary by country, so check what your tax authority accepts. Then fix the habit going forward: photograph receipts the day you get them, because a receipt captured in ten seconds never goes missing.',
    ],
    [
      'q' => 'What should I do with invoices that are still unpaid at year-end?',
      'a' => 'Split them into two piles. Invoices that are late but collectable get a reminder now, and mentioning that you\'re closing your books for the year is a genuinely effective nudge. Invoices that are dead, because the client vanished or folded or the amount isn\'t worth pursuing, should be written off so your books show income you\'ll actually receive. Whether a write-off reduces your taxable income depends on your country and on whether you report income when you invoice or when you\'re paid, so check that detail with your tax authority or an accountant. The goal either way is that nothing is left in limbo: every open invoice is either freshly chased or deliberately closed.',
    ],
    [
      'q' => 'How much money should I set aside for taxes at year-end?',
      'a' => 'A common rough rule for self-employed people is to hold back somewhere in the region of a quarter to a third of profit, but the honest answer is that it depends on your country, your other income, and whether you owe self-employment or payroll-style contributions on top of income tax. That\'s why the checklist has you run the profit and loss first: once you know the year\'s profit, you can apply your local rates to a real number instead of guessing from revenue. Move the money to a separate account as soon as you\'ve estimated it, and check whether your tax system expects installment payments through the year rather than one payment at filing time.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'freelance',
    'contractor',
  ],

  'related_article_slugs' => [
    'how-to-pay-quarterly-estimated-taxes',
    'how-much-to-set-aside-for-taxes-self-employed',
    'small-business-tax-deductions',
    'how-long-to-keep-business-receipts',
  ],
];
