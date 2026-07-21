<?php
// articles/data/what-is-a-profit-and-loss-statement.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-is-a-profit-and-loss-statement',

  'h1' => 'What is a profit and loss statement?',

  'meta_title' => 'What Is a Profit and Loss Statement? | Argo Books',

  'meta_description' => 'A profit and loss statement shows what you earned, what it cost, and what you kept. Here\'s how to read one, build one, and run it every month.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 50,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A profit and loss statement answers the one question every business owner actually cares about: after everything, did I make money? It's a single page that takes what came in over a stretch of time, subtracts what went out, and shows you the number at the bottom. Accountants also call it an income statement, and the two names mean exactly the same thing.</p>
<p>You don't need a bookkeeping background to read one. Once you know the five lines it's built from, you can look at any P&L, yours or someone else's, and understand it in about thirty seconds. This guide walks through each line in plain words, shows a worked example with real numbers, explains how a P&L is different from the other two reports people confuse it with, and covers how often you should run one.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What a profit and loss statement is',
      'anchor' => 'what-it-is',
      'html' => <<<'HTML'
<p>A profit and loss statement (P&L for short, income statement if you want the formal name) is a summary of your revenue and your costs over a set period. That period is the important part. A P&L always covers a span of time, not a single moment. You run one for a month, a quarter, or a full year, and it tells you what happened across that whole window.</p>
<p>Think of it as a story with a beginning, a middle, and an end. The top line is everything you earned. Each line below it strips out another kind of cost. By the time you reach the bottom, you're left with the money the business actually kept. Every line in between exists to tell you where the rest of it went.</p>
<p>That's the whole idea. Money in at the top, costs peeled off one layer at a time, profit at the bottom. Now let's name the layers.</p>
HTML,
    ],

    [
      'h2' => 'The five parts, top to bottom',
      'anchor' => 'the-parts',
      'html' => <<<'HTML'
<p>Almost every P&L you'll ever see is built from the same five lines, in the same order. Learn these once and you can read any of them.</p>
<ul>
<li><strong>Revenue.</strong> The money your customers paid you for your work, before any costs come out. Also called sales or turnover. A house painter's revenue is the total of every job invoiced. A freelancer's revenue is the total of every client invoice. One thing that does not belong here: sales tax you collected. That money was never yours to keep, so a clean P&L leaves it out of revenue entirely.</li>
<li><strong>Cost of goods sold (COGS).</strong> The direct cost of delivering what you sold. For a product business, that's what the stock cost you to buy or make. For a trade, it's materials and subcontractor labour on the actual jobs. If a cost only exists because you did the work, it's COGS. A service business with no materials, like a consultant, often has little or no COGS at all.</li>
<li><strong>Gross profit.</strong> Revenue minus COGS. This is what's left after the direct cost of the work, but before the cost of running the business. Gross profit tells you whether the work itself is priced well. If it's thin, you're either charging too little or your materials cost too much.</li>
<li><strong>Operating expenses.</strong> The costs of keeping the business open, whether or not you land a single job. Software subscriptions, phone and internet, insurance, advertising, accounting fees, rent, fuel. These don't rise and fall with each sale the way COGS does. They're the overhead.</li>
<li><strong>Net profit.</strong> Gross profit minus operating expenses. The bottom line, literally. This is the money the business earned and got to keep after everything. When someone asks "are you profitable?", this is the number they mean.</li>
</ul>
<p>Read top to bottom, the logic is: here's what I earned, here's what the work cost, here's the margin on the work, here's what the office cost, and here's what I actually kept.</p>
{{illustration:profit-split}}
HTML,
    ],

    [
      'h2' => 'A worked example',
      'anchor' => 'worked-example',
      'html' => <<<'HTML'
<p>Numbers make this concrete. Here's a one-month P&L for a small landscaping business. They invoiced $12,000 of work in June, spent money on plants and a subcontractor to deliver it, and paid the usual bills to keep the truck running.</p>
<table>
<thead>
<tr><th>Line</th><th>Amount</th></tr>
</thead>
<tbody>
<tr><td><strong>Revenue</strong> (jobs invoiced, tax excluded)</td><td>$12,000</td></tr>
<tr><td>Plants and materials</td><td>$2,600</td></tr>
<tr><td>Subcontractor labour</td><td>$1,900</td></tr>
<tr><td><strong>Cost of goods sold</strong></td><td>$4,500</td></tr>
<tr><td><strong>Gross profit</strong> (revenue minus COGS)</td><td>$7,500</td></tr>
<tr><td>Fuel</td><td>$650</td></tr>
<tr><td>Equipment and tool upkeep</td><td>$400</td></tr>
<tr><td>Insurance</td><td>$300</td></tr>
<tr><td>Phone, software, advertising</td><td>$350</td></tr>
<tr><td><strong>Operating expenses</strong></td><td>$1,700</td></tr>
<tr><td><strong>Net profit</strong> (gross profit minus operating expenses)</td><td><strong>$5,800</strong></td></tr>
</tbody>
</table>
<p>Read it top to bottom. Twelve thousand came in. Four thousand five hundred went straight back out on the plants and the helper needed to do the jobs, leaving $7,500 of gross profit. Then $1,700 of running costs came off, and the business kept $5,800 for the month.</p>
<p>That last number is the point of the whole exercise. Revenue of $12,000 sounds like a good month, but the owner didn't keep $12,000. They kept $5,800. A P&L is the difference between feeling busy and knowing what you earned.</p>
HTML,
    ],

    [
      'h2' => 'What a P&L actually tells you',
      'anchor' => 'what-it-tells-you',
      'html' => <<<'HTML'
<p>Two things, mainly. First, whether you're making money at all. Plenty of businesses have strong revenue and almost no profit, because the costs quietly eat everything. The bottom line is the only place that shows up. If net profit is small or negative while revenue looks healthy, something in the middle is out of line.</p>
<p>Second, where the money is going. Because the costs are split into COGS and operating expenses, a P&L shows you which layer is heavy. If your gross profit is thin, the problem is in the work itself: your prices are too low or your materials cost too much. If your gross profit is fat but net profit is thin, the problem is overhead: the running costs of the business are swallowing the margin. Those are two completely different fixes, and the P&L points straight at which one you've got.</p>
<p>It also gives you a margin you can track over time. Net profit margin is net profit divided by revenue. In the landscaping example, that's $5,800 divided by $12,000, or about 48%. On its own a single month's margin doesn't say much. Watched month after month, it tells you whether the business is getting healthier or leaner. A margin that drifts down over a few months is an early warning worth acting on before it shows up in your bank balance. For a deeper look at these two profit figures, see <a href="/gross-profit-vs-net-profit/">gross profit vs net profit</a>.</p>
HTML,
    ],

    [
      'h2' => 'How a P&L differs from the other two reports',
      'anchor' => 'vs-other-reports',
      'html' => <<<'HTML'
<p>People mix up three reports constantly. Here's how the P&L is different from the two it gets confused with.</p>
<p><strong>A balance sheet</strong> is a snapshot, not a period. Where a P&L covers a span of time (June, or all of 2026), a balance sheet freezes a single day and lists what you own, what you owe, and the difference between them. The P&L asks "did I make money over this stretch?" The balance sheet asks "what is the business worth right now?" You need both, and they answer different questions. If you want the full walkthrough, see <a href="/what-is-a-balance-sheet/">what is a balance sheet</a>.</p>
<p><strong>A cash flow statement</strong> tracks the actual movement of cash in and out, which isn't the same as profit. Here's the catch that surprises people: a P&L can show a profit in a month where your bank account went down, and the other way around. If you invoiced $10,000 of work in June but the client pays in July, your June P&L can show that revenue while no cash has landed. The cash flow statement is what shows you the money actually moving. This gap is the difference between <a href="/cash-basis-vs-accrual-accounting/">cash basis and accrual accounting</a>, and it's worth understanding, because a profitable business can still run short of cash if the timing is off.</p>
<p>The short version: the P&L is about earnings over a period, the balance sheet is about worth on a single day, and the cash flow statement is about cash actually moving. Three lenses on the same business.</p>
HTML,
    ],

    [
      'h2' => 'How often to run one',
      'anchor' => 'how-often',
      'html' => <<<'HTML'
<p>Monthly is the habit worth building. A yearly P&L at tax time is required, but it's a rear-view mirror: by the time you see a problem, it's twelve months old. A monthly P&L is close enough to the action that you can still do something about what it shows you.</p>
<p>Pick a day, the first of the month works well, and run last month's P&L. It takes a few minutes once your records are in order, and the payoff is that you're never guessing. You'll spot a cost creeping up, or a good month, or a lean one, while it's still recent enough to matter. Compare each month to the ones before it and the pattern tells you more than any single month can.</p>
{{illustration:report-statement}}
<p>Quarterly is the minimum if monthly feels like too much, and you'll want a full-year P&L regardless for tax filing and for anyone who lends to you or invests in you. But the owners who stay ahead of their numbers are almost always the ones looking monthly. It's the difference between steering and reacting.</p>
HTML,
    ],

    [
      'h2' => 'Let Argo Books build it for you',
      'anchor' => 'argo-books',
      'html' => <<<'HTML'
<p>The slow part of a P&L was never the maths. It's gathering the records: pulling every invoice and every expense into one place, sorting them into the right buckets, and adding them up without missing anything. Once that's done, the report almost writes itself.</p>
<p>Argo Books handles the gathering, because your invoices and expenses are already in it. The <a href="/features/report-builder/">Report Builder</a> takes that data and generates an Income Statement (the same thing as a profit and loss statement) on demand, then exports it as a clean, branded PDF you can hand to an accountant or a lender. The Report Builder is free. There's no add-on to buy for it.</p>
<p>The number it lands on is built the honest way. Net profit is your revenue with sales tax excluded, minus your expenses, minus any refunds you issued. Sales tax is never counted as profit, because it was never yours: it's money you're holding for the government, and Argo Books treats it as a liability, not as earnings. That means the bottom line on your statement is the number you actually kept, not an inflated figure with tax quietly padding it.</p>
<p>One detail worth knowing: the Report Builder runs on an accrual basis, so its Income Statement counts all the revenue you invoiced in the period, whether the client has paid yet or not. That's the right basis for a P&L you'll share with an accountant or use at tax time. Your day-to-day dashboard counts only money actually collected, so the two views can differ, and both are correct. They're just answering slightly different questions.</p>
<p>So the whole job comes down to keeping your invoices and expenses recorded as you go, which the app is built to make quick. When you want to know what you earned last month, you open the Report Builder and it's there.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books builds your Income Statement from your own invoices and expenses and exports it as a branded PDF, for free.',
  'tool_callout_cta' => 'See the Report Builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'What\'s on a profit and loss statement?',
      'a' => 'Five lines, top to bottom: revenue (what customers paid you, tax excluded), cost of goods sold (the direct cost of delivering the work, like materials and subcontractors), gross profit (revenue minus COGS), operating expenses (the overhead of running the business, like rent, insurance, software, and fuel), and net profit (gross profit minus operating expenses, which is the money you actually kept). Some statements add a bit more detail inside each section, but those five lines are the backbone of every P&L.',
    ],
    [
      'q' => 'How is a P&L different from a balance sheet?',
      'a' => 'A P&L covers a period of time and shows whether you made money over that stretch. A balance sheet is a snapshot of a single day and shows what the business is worth at that moment: what you own, what you owe, and the difference. The P&L answers "did I earn a profit this month or year?" The balance sheet answers "what is my business worth right now?" You use both, but they measure different things over different timeframes.',
    ],
    [
      'q' => 'How often should I run a P&L?',
      'a' => 'Monthly is the habit worth building. A yearly statement is required for tax filing, but by the time you see it the information is up to a year old. Running one each month keeps you close enough to the numbers to act on what they show, like a cost creeping up or a margin slipping. If monthly feels like too much, quarterly is a reasonable minimum, and you\'ll still want a full-year statement for taxes and for any lender or investor.',
    ],
    [
      'q' => 'Is a P&L the same as an income statement?',
      'a' => 'Yes. Profit and loss statement, P&L, and income statement are three names for the exact same report. Accountants tend to say income statement, business owners tend to say P&L, and the two are interchangeable. Argo Books labels its version the Income Statement, but it\'s the same document this guide describes: revenue at the top, costs peeled off in layers, net profit at the bottom.',
    ],
    [
      'q' => 'What\'s a healthy net profit margin?',
      'a' => 'It depends heavily on your industry, so there\'s no single right number. Service businesses with low material costs often run margins of 20% or higher, while product and trade businesses that buy a lot of stock or materials tend to run lower because their cost of goods sold is bigger. More useful than any benchmark is your own trend: track your net profit margin month over month and watch the direction. A margin holding steady or rising is a good sign, and one drifting down is worth investigating early. Check with an accountant for what\'s normal in your line of work.',
    ],
    [
      'q' => 'Does sales tax count as revenue or profit on a P&L?',
      'a' => 'Neither, on a clean statement. The sales tax you collect from customers was never your money. You\'re holding it on behalf of the government until you remit it, so it belongs on the books as a liability, not as income. A well-built P&L excludes it from revenue and from profit. That\'s exactly how Argo Books handles it: net profit is revenue with sales tax excluded, minus expenses, minus refunds, so the bottom line reflects only what you actually kept.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'consultant',
    'generic',
  ],

  'related_article_slugs' => [
    'what-is-a-balance-sheet',
    'gross-profit-vs-net-profit',
    'how-to-turn-a-spreadsheet-into-a-profit-and-loss-statement',
    'cash-basis-vs-accrual-accounting',
  ],
];
