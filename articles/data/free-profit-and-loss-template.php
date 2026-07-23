<?php
// articles/data/free-profit-and-loss-template.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'free-profit-and-loss-template',

  'h1' => 'Free profit and loss template (Excel and Google Sheets)',

  'meta_title' => 'Free Profit and Loss Statement Template | Argo Books',

  'meta_description' => 'Download a free profit and loss template for Excel or Google Sheets, then learn how to fill in each section, avoid the two big mistakes, and read the result.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'spreadsheets',
  'hub_weight' => 30,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 12,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A profit and loss statement answers one question: did your business actually make money? Not "did sales feel good this month", but the real number, income minus costs, in black and white. You don't need accounting software to get that answer. A well-built spreadsheet does the job, and you can have one open in the next thirty seconds.</p>
<p>The template below is free, has no signup, and the totals calculate themselves. Download it first, then read on: this guide walks through what goes in each section, the two mistakes that quietly wreck most homemade P&amp;Ls, and how to actually read the result once the numbers are in.</p>
<div class="article-download">
<a class="article-download-btn" href="/resources/templates/profit-and-loss-template.xlsx" download>Download the free profit and loss template (.xlsx)</a>
<span class="article-download-note">Two sheets: a full statement plus a month-by-month view. Totals calculate automatically. Opens in Excel, uploads straight into Google Sheets. No signup.</span>
</div>
HTML,

  'sections' => [

    [
      'h2' => 'What\'s inside the template',
      'anchor' => 'whats-inside',
      'html' => <<<'HTML'
<p>The file has two sheets, and they do two different jobs.</p>
<p><strong>The "Profit and Loss" sheet</strong> is a full statement for a single period, usually a month, a quarter, or a year. It follows the standard layout accountants use, top to bottom:</p>
<ul>
<li><strong>Income</strong>: rows for Sales, Services, Shipping charged to customers, and Other income.</li>
<li><strong>Cost of goods sold</strong>: rows for Materials and stock purchases, Packaging, and Other direct costs.</li>
<li><strong>Gross profit</strong>: calculated for you, income minus cost of goods sold.</li>
<li><strong>Operating expenses</strong>: rows for Rent, Software and subscriptions, Advertising, Insurance, Office and shop supplies, Phone and internet, Travel and vehicle, Professional fees, Bank and payment fees, and Other expenses.</li>
<li><strong>Net profit</strong>: also calculated for you, gross profit minus operating expenses.</li>
</ul>
<p>You type numbers into the category rows and the totals, gross profit, and net profit update on their own. There are no formulas to build and nothing to wire up.</p>
<p><strong>The "Monthly" sheet</strong> is the year at a glance. It runs Income, Cost of goods sold, Gross profit, Operating expenses, and Net profit across columns for January through December, with a Year total column at the end. The gross profit and net profit rows calculate automatically here too. You fill in three numbers per month and the sheet shows you the shape of your whole year.</p>
<p>The file opens directly in Excel. If you'd rather work in Google Sheets, upload it to Google Drive and open it there; the formulas carry across fine. It also works in LibreOffice if that's what you use.</p>
HTML,
    ],

    [
      'h2' => 'What a profit and loss statement tells you',
      'anchor' => 'what-it-tells-you',
      'html' => <<<'HTML'
<p>In one breath: a profit and loss statement (also called an income statement, or just a P&amp;L) lists everything you earned in a period, subtracts everything it cost you to earn it, and shows what's left. That leftover number is your profit, and it's the single most honest measure of whether the business works.</p>
<p>It's worth being clear about what it is not. It's not your bank balance. Money can sit in your account from a loan, a tax refund, or last month's sales, none of which say anything about whether this month was profitable. And a P&amp;L is not a cash flow forecast either: it looks backward at what happened, not forward at what's coming. If you want the forward-looking view too, grab the <a href="/free-cash-flow-forecast-template/">free cash flow forecast template</a> as a companion; the two together cover both questions.</p>
<p>If you want the full picture of how the statement works, what each line means, and how businesses use it to make decisions, read our plain-English guide to <a href="/what-is-a-profit-and-loss-statement/">what a profit and loss statement is</a>. For this article, the short version above is enough to fill in the template properly.</p>
HTML,
    ],

    [
      'h2' => 'How to fill in each section',
      'anchor' => 'fill-it-in',
      'html' => <<<'HTML'
<p>Open the "Profit and Loss" sheet, pick your period (last month is the natural place to start), and work top to bottom.</p>
<h3>Income</h3>
<p>Enter what you earned during the period, split across the four rows. <strong>Sales</strong> is money from products you sold. <strong>Services</strong> is money from work you did: freelance projects, consulting hours, repairs, commissions. <strong>Shipping charged to customers</strong> is any delivery cost you passed on to buyers; it counts as income because customers paid it to you, even though most of it goes back out as postage. <strong>Other income</strong> catches the rest: interest, a one-off equipment sale, an affiliate payout.</p>
<p>Use what you earned before any fees came off. If a customer paid $100 and the payment processor kept $3, your income is $100 and the $3 belongs down in Bank and payment fees. Recording the $97 hides a real cost and understates both numbers.</p>
<h3>Cost of goods sold</h3>
<p>This section is only for costs tied directly to the things you sold. <strong>Materials and stock purchases</strong> covers the products or raw materials behind the sales in this period. <strong>Packaging</strong> is boxes, mailers, tape, labels. <strong>Other direct costs</strong> is anything else that scales with each sale, like a per-unit production fee. If you sell services only and buy no materials, it's fine for this whole section to be zero.</p>
<h3>Operating expenses</h3>
<p>Everything it costs to keep the business running, whether you sold anything or not. Rent, software subscriptions, advertising, insurance, your phone and internet (the business share of it), travel, your accountant, bank fees. Go through your business bank and card statements for the period and put each charge in the closest row. Don't agonize over categories: whether a charge lands in Office and shop supplies or Other expenses barely matters. What matters is that every business cost lands somewhere, because a missed expense inflates your profit and gives you a rosier picture than reality.</p>
<p>That's it. Gross profit and net profit calculate themselves as you type. If you're starting from a pile of receipts and bank rows rather than tidy totals, our guide on <a href="/how-to-turn-a-spreadsheet-into-a-profit-and-loss-statement/">turning a spreadsheet into a profit and loss statement</a> covers that messier first step.</p>
HTML,
    ],

    [
      'h2' => 'The two mistakes that wreck a DIY P&L',
      'anchor' => 'two-mistakes',
      'html' => <<<'HTML'
<p>Most homemade profit and loss statements go wrong in one of two specific ways. Both produce a statement that looks finished but tells you the wrong number.</p>
<h3>Mistake 1: personal spending mixed in</h3>
<p>If your groceries, your streaming subscriptions, or your family phone plan flow through the same account as the business and get typed into the expense rows, your P&amp;L stops measuring the business. Profit looks worse than it is, and you can't tell whether a bad month was the business or just a big personal week. The fix is a boundary: only costs the business would still have if you weren't in the picture personally go on the statement. A separate business bank account makes this nearly automatic, because everything on that statement belongs in the P&amp;L and nothing else does. For shared costs like a phone or a home office, put in a consistent business-use share and keep it the same every period.</p>
<h3>Mistake 2: treating stock purchases as a straight expense</h3>
<p>This one is sneakier. Say you spend $2,000 on stock in March and sell half of it. If you write the full $2,000 into March as an expense, March looks terrible and April, when you sell the rest with no purchase against it, looks amazing. Neither month is telling the truth. The cost of stock belongs against the period in which you <em>sell</em> it, not the period in which you buy it. That's the whole idea behind cost of goods sold, and it's why the template gives COGS its own section above gross profit instead of burying it in expenses. A simple way to get close: count what the stock you actually sold this period cost you, and put that in Materials and stock purchases. Our guide to <a href="/what-is-cost-of-goods-sold/">cost of goods sold</a> explains the idea properly, with the standard formula for working it out from opening stock, purchases, and closing stock.</p>
<p>Get these two things right and a homemade P&amp;L is genuinely trustworthy. Get either wrong and the bottom line is decoration.</p>
<div class="article-download">
<a class="article-download-btn" href="/resources/templates/profit-and-loss-template.xlsx" download>Download the template and follow along (.xlsx)</a>
<span class="article-download-note">The COGS section is already split out for you, so mistake 2 is hard to make.</span>
</div>
HTML,
    ],

    [
      'h2' => 'How to read the result: gross profit vs net profit',
      'anchor' => 'read-the-result',
      'html' => <<<'HTML'
<p>Once the numbers are in, the template hands you two profit figures, and they answer different questions.</p>
<p><strong>Gross profit</strong> is income minus cost of goods sold. It asks: is the thing I sell worth selling? If your gross profit is thin, no amount of trimming subscriptions or renegotiating rent will save you, because the product itself isn't earning enough over what it costs you. Thin gross profit points at pricing, supplier costs, or shipping you're eating.</p>
<p><strong>Net profit</strong> is gross profit minus operating expenses. It asks: does the business as a whole make money? Healthy gross profit with weak net profit means the products are fine but the overhead is eating the margin: too much rent, too many subscriptions, advertising that isn't paying for itself.</p>
{{illustration:report-statement}}
<p>Reading the two together is the diagnostic. Weak gross profit means fix the selling side. Strong gross but weak net means fix the running-costs side. Both weak means both, and both strong means you can stop guessing and start deciding what to do with the profit. For a deeper walkthrough with examples, see <a href="/gross-profit-vs-net-profit/">gross profit vs net profit</a>.</p>
<p>One more habit worth building: look at the percentages, not just the dollars. Gross profit divided by income is your gross margin. If income grows but that percentage shrinks, you're working harder for less on every sale, and that trend is easy to miss when the raw dollar figures are all going up.</p>
HTML,
    ],

    [
      'h2' => 'Use the Monthly sheet to spot trends',
      'anchor' => 'monthly-sheet',
      'html' => <<<'HTML'
<p>A single statement is a snapshot. The "Monthly" sheet turns twelve snapshots into a film, and that's where the interesting answers live.</p>
<p>At the end of each month, copy your totals across: income, cost of goods sold, and operating expenses into that month's column. The gross profit and net profit rows fill in on their own, and the Year total column keeps a running picture of the whole year.</p>
<p>Once you have three or four months in a row, read across the rows instead of down the columns:</p>
<ul>
<li><strong>Income climbing while net profit stays flat</strong> means costs are growing exactly as fast as sales. You're getting busier, not richer.</li>
<li><strong>Gross profit shrinking as a share of income</strong> usually means supplier prices crept up or discounting crept in, and neither made it into your prices.</li>
<li><strong>An expense row that only moves one direction</strong> is worth a look. Subscriptions in particular tend to stack up quietly until the row is double what you'd guess.</li>
<li><strong>Repeating strong and weak months</strong> reveal your seasonality, which changes how you read any single month. A slow February isn't a problem if February is always slow; it's a problem if last February was strong.</li>
</ul>
<p>This monthly habit takes maybe ten minutes once the single-period statement is filled in, and it's the difference between a P&amp;L you made once and a P&amp;L that actually steers the business.</p>
HTML,
    ],

    [
      'h2' => 'When the spreadsheet stops being enough',
      'anchor' => 'outgrow-it',
      'html' => <<<'HTML'
<p>An honest note to end on: this template has a natural lifespan. The statement itself never stops being useful, but the way you produce it does.</p>
<p>The template works beautifully while your months are small enough to total up by hand. The strain shows as volume grows: every month you're re-reading bank statements, re-sorting charges into categories, and re-typing totals, and every one of those manual steps is a chance for a number to go astray. When the monthly fill-in stretches past an hour, or you catch a figure that doesn't match your bank, or you skip a month because you're busy (and then two), the spreadsheet has quietly turned from a tool into a chore. We've written up the full set of warning signs in <a href="/why-your-bookkeeping-spreadsheet-stops-working/">why your bookkeeping spreadsheet stops working</a>.</p>
{{illustration:spreadsheet-to-books}}
<p>The step up isn't a fancier spreadsheet, it's keeping your records in one place so the statement builds itself. That's how Argo Books handles it: you record income and expenses (or import them from a bank statement or CSV), and the report builder puts together your profit and loss statement from those records automatically, with cost of goods sold handled properly instead of by hand. No monthly re-typing, and the gross-vs-net reading you've learned here works exactly the same on the generated report. It's free to start and runs as a desktop app on your own machine, so your books stay local.</p>
<p>But don't rush there. Filling this template in by hand for a few months is the best accounting education a small business owner can get for free, because you see exactly where every number comes from. Start with the spreadsheet, learn what your P&amp;L is telling you, and move up when the upkeep costs more than it teaches.</p>
<div class="article-download">
<a class="article-download-btn" href="/resources/templates/profit-and-loss-template.xlsx" download>Download the free profit and loss template (.xlsx)</a>
<span class="article-download-note">Single-period statement plus a Jan-to-Dec monthly view. Totals calculate automatically. No signup.</span>
</div>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'When the monthly fill-in gets old, Argo Books builds your profit and loss statement automatically from the income and expenses you\'ve recorded.',
  'tool_callout_cta' => 'See the report builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'Does this template work in Google Sheets?',
      'a' => 'Yes. The file is a standard .xlsx, so it opens directly in Excel, and you can upload it to Google Drive and open it in Google Sheets, where the formulas carry across and keep calculating. It also opens in LibreOffice Calc. Pick whichever you\'ll actually open every month: Google Sheets if you want it available on any device and backed up automatically, Excel if you prefer working offline. The template only uses simple adding and subtracting, so nothing exotic breaks between programs.',
    ],
    [
      'q' => 'Do I have to fill in every row?',
      'a' => 'No. The rows are there so common costs have an obvious home, not as a checklist. A service business with no physical products can leave the whole cost of goods sold section at zero, and plenty of businesses will have nothing in rows like Insurance or Travel and vehicle some months. Empty rows simply add nothing to the totals. What matters is the opposite direction: every real business cost should land in some row, because a cost you leave out entirely makes your profit look better than it is.',
    ],
    [
      'q' => 'How often should I update the template?',
      'a' => 'Monthly is the sweet spot for most small businesses. Fill in the single-period statement from your bank and card statements at the start of each month for the month just ended, then copy the totals into that month\'s column on the Monthly sheet. Done regularly it takes well under an hour. Quarterly works if your business is small and steady, but you lose the early warning a monthly view gives you: a cost creeping up for three months is much cheaper to catch in month one than at the quarter\'s end.',
    ],
    [
      'q' => 'Why doesn\'t my net profit match the money in my bank account?',
      'a' => 'Because profit and cash measure different things. Your bank balance moves with every transfer: loan money arriving, tax set aside, equipment purchases, your own drawings, and customers paying late or early. None of those change whether the period was profitable. A profitable month can leave the bank empty if customers haven\'t paid yet, and a losing month can look flush if a loan landed. The P&L answers "did the business make money?" while the bank answers "what can I spend right now?" You need both, which is why a cash flow forecast makes a good companion to this template.',
    ],
    [
      'q' => 'Can I use this profit and loss statement for my taxes?',
      'a' => 'It\'s a strong starting point, but treat it as preparation rather than the final word. A well-kept P&L gives you or your accountant the income and expense totals a tax return is built from, and keeping it current all year makes tax season far calmer. That said, tax rules about what counts as deductible, how stock is valued, and how home or vehicle costs are split vary by country and change over time. Check the specifics with your local tax authority or an accountant before filing anything based on your own spreadsheet.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'freelance',
    'consultant',
  ],

  'related_article_slugs' => [
    'what-is-a-profit-and-loss-statement',
    'how-to-turn-a-spreadsheet-into-a-profit-and-loss-statement',
    'free-bookkeeping-spreadsheet-templates',
    'free-cash-flow-forecast-template',
  ],
];
