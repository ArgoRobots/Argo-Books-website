<?php
// articles/data/free-cash-flow-forecast-template.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'free-cash-flow-forecast-template',

  'h1' => 'Free cash flow forecast template (and when to outgrow it)',

  'meta_title' => 'Free Cash Flow Forecast Template for Small Business | Argo Books',

  'meta_description' => 'Build a free cash flow forecast template in Excel or Google Sheets today: exactly what to include, how to fill it in, how to keep it current, and when to switch.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 38,

  'published' => '2026-06-27',

  'updated' => '2026-06-27',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A cash flow forecast is just a simple picture of the money you expect to come in and go out over the next few weeks or months, and whether your bank balance stays above zero the whole way. You do not need software to start. A plain spreadsheet gets you forecasting for free today, and for a lot of small businesses that is genuinely all you need.</p>
<p>This guide shows you exactly how to build that template in Excel or Google Sheets, how to fill it in so the numbers mean something, and the one habit that decides whether the forecast is useful or just decoration. It also covers the honest part most templates skip: the point where a spreadsheet stops keeping up and starts costing you more time than it saves.</p>
<p>To save you the setup, there is a free template just below that you can download and start using straight away. It's laid out exactly the way this guide explains, so filling it in still teaches you what the forecast is telling you.</p>
<div class="article-download">
<a class="article-download-btn" href="/resources/templates/cash-flow-forecast-template.xlsx" download>Download the free cash flow forecast template (.xlsx)</a>
<span class="article-download-note">12 months, with the formulas already built in. Works in Excel, Google Sheets, and LibreOffice. No signup.</span>
</div>
HTML,

  'sections' => [

    [
      'h2' => 'What a good cash flow template includes',
      'anchor' => 'what-it-includes',
      'html' => <<<'HTML'
<p>A cash flow forecast is a grid. Time runs across the top, and the kinds of money run down the side. Each column is one period: a week or a month. Each row is one type of cash movement. Where they meet, you write the amount you expect.</p>
<p>The structure barely changes from business to business. Here is the row layout, top to bottom, in the order the money actually flows through a period:</p>
<table>
<thead>
<tr><th>Row</th><th>What it holds</th></tr>
</thead>
<tbody>
<tr><td><strong>Opening balance</strong></td><td>The cash you start the period with. In the first column this is your real bank balance today. After that it is just last period's closing balance.</td></tr>
<tr><td><strong>Cash in: sales</strong></td><td>Money from sales you expect to actually receive in this period.</td></tr>
<tr><td><strong>Cash in: paid invoices</strong></td><td>Invoices you expect customers to pay in this period, dated by when they pay, not when you sent the invoice.</td></tr>
<tr><td><strong>Cash in: other</strong></td><td>Anything else: a tax refund, a loan drawdown, interest, an owner contribution.</td></tr>
<tr><td><strong>Cash out: rent</strong></td><td>Premises, storage, anything you pay to occupy space.</td></tr>
<tr><td><strong>Cash out: wages</strong></td><td>Staff pay, contractor payments, and your own draw if you take one.</td></tr>
<tr><td><strong>Cash out: supplies</strong></td><td>Stock, materials, tools, software, the things the business consumes.</td></tr>
<tr><td><strong>Cash out: tax</strong></td><td>Sales tax set aside, income tax instalments, payroll tax.</td></tr>
<tr><td><strong>Cash out: loan</strong></td><td>Loan repayments, finance, lease payments.</td></tr>
<tr><td><strong>Net movement</strong></td><td>Total cash in minus total cash out for the period. This can be negative, and that is fine as long as the closing balance below it stays positive.</td></tr>
<tr><td><strong>Closing balance</strong></td><td>Opening balance plus net movement. This number carries across to become next period's opening balance.</td></tr>
</tbody>
</table>
<p>That last link is the whole engine. Closing balance becomes the next opening balance, period after period, so a shortfall in week two pushes its effect into week three and beyond. In a spreadsheet the closing balance cell is just a formula: opening plus all the cash-in rows, minus all the cash-out rows. The first opening balance is the only number you type by hand; every opening balance after it points at the previous column's closing balance.</p>
<p>Keep the categories to ones you actually use. If you do not have a loan, drop that row. If half your money goes out on subcontractors, give them their own row. The goal is a layout you can read at a glance and update in a couple of minutes, not a perfect chart of accounts.</p>
HTML,
    ],

    [
      'h2' => 'How to fill it in, step by step',
      'anchor' => 'how-to-fill',
      'html' => <<<'HTML'
<p>Building the grid is the easy part. Filling it in honestly is where the value lives. Work through it in this order:</p>
<ol>
<li><strong>Set your period length.</strong> If cash is tight or lumpy, use weeks. If money moves slowly and predictably, months are fine. Most small businesses are best served by weekly columns for the next eight to thirteen weeks, because that is the window where a cash gap can actually hurt you and where you can still do something about it.</li>
<li><strong>Enter the real opening balance.</strong> Open your bank account and type in the actual cash you have right now, to the dollar. Not your invoiced total, not your profit, the real bank balance. A forecast built on a guessed starting number is wrong from the first cell.</li>
<li><strong>List the money coming in, dated by when it arrives.</strong> Go through expected sales and unpaid invoices and put each one in the period you will actually receive it. This is the step everyone gets wrong. If you invoice a client today on 30-day terms, that money belongs in the column four to five weeks out, not this one. Forecast the payment date, not the invoice date.</li>
<li><strong>List the money going out, dated by when it leaves.</strong> Rent on the first, payroll on its run date, supplier bills on their due dates, tax instalments on their deadlines. Use the date the cash actually leaves your account. A bill you owe but will not pay until next month sits in next month's column.</li>
<li><strong>Let the closing balance roll forward.</strong> With your formulas in place, each closing balance feeds the next opening balance automatically. Now read down the closing balance row. Every period should stay above zero. If a column dips negative, that is the forecast doing its job: it has spotted a cash gap before it happened, while you still have time to chase an invoice, delay a purchase, or arrange cover.</li>
</ol>
<p>The timing assumption in steps three and four is the single most important idea in cash flow forecasting, so it's worth saying plainly. Cash flow is not about what you earn or what you owe. It's about when the money actually moves. A profitable business can still run out of cash if the money it's owed arrives later than the bills it has to pay. Your receivables (money customers owe you) land on their payment dates, and your payables (money you owe others) leave on their due dates. Date every row by when the cash crosses your bank account, and the forecast tells the truth. Date them by when you invoiced or when the bill arrived, and it lies to you.</p>
HTML,
    ],

    [
      'h2' => 'Keeping it updated is the part everyone skips',
      'anchor' => 'keep-updated',
      'html' => <<<'HTML'
<p>Here is the uncomfortable truth about a spreadsheet forecast: it is only as good as the last time you touched it. A forecast you built in January and have not opened since is not a forecast anymore, it is a souvenir. And a stale forecast is genuinely worse than no forecast, because it gives you false confidence in numbers that drifted away from reality weeks ago.</p>
<p>The fix is small and boring, which is exactly why it works. Once a week, block five minutes and do one thing: replace the past period's forecast figures with what actually happened. The invoice you expected paid on Tuesday either arrived or it did not. The supplier bill came in higher or lower than you guessed. Swap your estimates for the real numbers, and the closing balance recalculates down the whole sheet.</p>
<p>This does two things at once. It keeps the rest of the forecast accurate, because every future opening balance now sits on a true foundation. And it quietly teaches you how good your guesses are. After a month of swapping forecast for actual, you start to see your own patterns: that you always assume customers pay faster than they do, or that supply costs run ten percent over your estimate. That feedback makes next month's forecast sharper.</p>
{{illustration:calendar-due}}
<p>Pick a fixed time so it becomes a habit rather than a decision. Monday morning with coffee, or Friday before you close down for the week. The businesses that get value out of a cash flow template are not the ones with the prettiest spreadsheet. They are the ones that actually open it every week.</p>
HTML,
    ],

    [
      'h2' => 'When a template stops being enough',
      'anchor' => 'outgrow-it',
      'html' => <<<'HTML'
<p>A spreadsheet is the right tool right up until it isn't, and the change is usually gradual rather than sudden. Watch for these signs that you have outgrown it:</p>
<ul>
<li><strong>Multiple income streams.</strong> One revenue line is easy to forecast. Five, with different timing and reliability, turns the spreadsheet into a juggling act where one mistyped date throws the whole sheet off.</li>
<li><strong>Real seasonality.</strong> If your business has busy and quiet seasons, a flat spreadsheet cannot see them coming. You end up eyeballing last year's figures and hoping, instead of having the pattern built into the forecast.</li>
<li><strong>Chasing unpaid invoices by hand.</strong> When forecasting the right payment date means cross-checking a separate list of who owes you what and how late they usually are, the spreadsheet is no longer the source of truth, it is a copy you have to keep in sync.</li>
<li><strong>Copy-and-paste creep.</strong> Every formula you drag across, every row you insert, is a chance for a small mistake to hide in a cell you never look at. The bigger the sheet, the more places a wrong number can sit unnoticed.</li>
<li><strong>The time cost.</strong> Five minutes a week is cheap. Forty-five minutes a week reconstructing a forecast from three other documents is not. At some point the spreadsheet costs more of your time than the answer is worth.</li>
</ul>
<p>When you hit two or three of those, the natural next step is software that does the updating for you. Your accounting tool already knows your real income and expenses, who has paid and who has not, and what last year looked like. Forecasting from that data directly, instead of retyping it into a separate sheet, removes the whole class of copy-paste mistakes and the weekly maintenance along with them. If you are weighing that move, our guide on <a href="/how-to-move-from-spreadsheets-to-bookkeeping-software/">how to move from spreadsheets to bookkeeping software</a> walks through doing it without losing your history.</p>
{{illustration:spreadsheet-to-books}}
<p>This is the gap Argo Books is built to close. Its <a href="/features/predictive-analytics/">predictive analytics</a> (a Premium feature) forecasts your revenue, expenses, and profit straight from your own books, so there is no separate spreadsheet to keep current. It detects your busy and quiet seasons automatically rather than asking you to spot them, using established forecasting methods (Holt-Winters and Singular Spectrum Analysis, if you want the names) under a plain-English summary. Each forecast comes with a confidence score that has been tested against your real past data, with backtested accuracy around 88 percent, so you can see how much to trust the number rather than guessing. Because Argo Books runs locally as a desktop app, all of that happens on your own machine, not in someone else's cloud.</p>
<p>None of that replaces the value of working through a forecast yourself first. Start with the spreadsheet, learn what the forecast is telling you, and switch to automatic forecasting only when the manual upkeep starts costing more than it returns. When you reach that point, you can <a href="/downloads/">download Argo Books</a> and try the free tier for everyday bookkeeping and invoicing before deciding whether the Premium forecasting is worth it for you.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'When the template gets heavy, Argo Books forecasts cash flow automatically, detects your seasonality, and keeps itself up to date.',
  'tool_callout_cta' => 'See predictive analytics',
  'tool_callout_url' => '/features/predictive-analytics/',

  'faqs' => [
    [
      'q' => 'Is a spreadsheet enough for cash flow forecasting?',
      'a' => 'For a lot of small businesses, yes. If you have one or two income streams, predictable costs, and you open the sheet every week to keep it current, a spreadsheet forecast does the job and costs nothing. It stops being enough when the upkeep gets heavy: multiple income streams with different timing, real seasonality you have to eyeball, or so many rows that a mistyped figure can hide. At that point the maintenance starts costing more time than the forecast saves, and software that forecasts from your real books becomes the cheaper option overall.',
    ],
    [
      'q' => 'Should I build this in Excel or Google Sheets?',
      'a' => 'Either works, and the layout is identical in both. Use Google Sheets if you want to open the forecast from any device, share it with a partner or bookkeeper, or have it back itself up automatically. Use Excel if you already live in it, prefer working offline, or want its more advanced formula and charting features. The forecast only needs basic adding and subtracting, so pick whichever one you will actually open every week. The habit matters far more than the program.',
    ],
    [
      'q' => 'How often should I update the template?',
      'a' => 'Once a week is the sweet spot for most small businesses, and it only takes about five minutes. The job is simple: replace the past period\'s forecast figures with what actually happened, then let the closing balances recalculate down the sheet. Weekly keeps the forecast accurate without becoming a chore. If your cash is very tight or moves fast, you might glance at it more often. If it moves slowly, every two weeks can be fine. The one thing that does not work is building it once and never reopening it, because a stale forecast gives you false confidence in numbers that have already drifted.',
    ],
    [
      'q' => 'When should I switch from a spreadsheet to software?',
      'a' => 'When two or three warning signs show up at once: multiple income streams with different timing, real seasonality your flat sheet cannot anticipate, chasing unpaid invoices across separate lists, copy-paste formula creep, or the weekly update growing from five minutes into the better part of an hour. Any one of those on its own is usually still manageable in a spreadsheet. When several pile up together, software that forecasts straight from your own books removes the manual retyping and the mistakes that come with it, and the time you get back is worth more than the spreadsheet ever saved.',
    ],
    [
      'q' => 'Can I move my spreadsheet into accounting software later?',
      'a' => 'Yes, and starting in a spreadsheet does not lock you out of anything. Your customer list, invoice history, and category totals are the parts that carry across, and most accounting tools can import them. The clean approach is to keep the spreadsheet as a reference for a while, start fresh transactions in the new tool from a clear date, and check the imported figures against your old sheet before you trust them. Our guide on how to move from spreadsheets to bookkeeping software covers the whole process step by step so the switch does not lose your history.',
    ],
  ],

  'related_niche_slugs' => [
    'consultant',
    'contractor',
    'freelance',
  ],

  'related_article_slugs' => [
    'how-to-forecast-cash-flow-small-business',
    'free-bookkeeping-spreadsheet-templates',
    'how-to-move-from-spreadsheets-to-bookkeeping-software',
    'cash-flow-forecasting-software',
  ],
];
