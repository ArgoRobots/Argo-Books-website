<?php
// articles/data/best-accounting-software-for-contractors.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'best-accounting-software-for-contractors',

  'h1' => 'The best accounting software for contractors',

  'meta_title' => 'Best Accounting Software for Contractors | Argo Books',

  'meta_description' => 'What contractors really need from accounting software: deposits, progress billing, on-site receipts, and profit per job. Here\'s how to choose the right tool.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'choosing-software',
  'hub_weight' => 40,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Most accounting software is built for a business that sends one invoice, gets paid once, and moves on. Contracting doesn't work that way. You take a deposit before the first tool comes off the truck, you bill again at the halfway point, you buy materials from three different suppliers in one afternoon, and you only find out whether the job made money after the last cheque clears. A tool that can't handle that shape of work will fight you on every job.</p>
<p>This guide is about picking software that fits how contractors actually get paid. We'll go through the five things the work demands, a plain checklist you can hold any tool up against, an honest look at the free tools, desktop apps, and subscription suites on the market, and where Argo Books fits if you want something that handles deposits, reads your receipts from a photo, and keeps working when the signal doesn't.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What contractors need that a generic app misses',
      'anchor' => 'what-contractors-need',
      'html' => <<<'HTML'
<p>Before you compare tools, it helps to name the specific jobs the software has to do. Five come up on almost every contracting job, and a general-purpose app usually handles two or three of them well and the rest badly.</p>
<ul>
<li><strong>Deposits and progress billing.</strong> You rarely get paid once at the end. A kitchen remodel might be 30% up front, 30% at rough-in, 30% at finish, and 10% held until the final walk-through. The tool has to take a deposit, bill in stages, and always show the real balance still owed.</li>
<li><strong>Capturing materials and expenses on site.</strong> A plumber buys fittings at the supply house, a painter grabs another two gallons at the hardware store, an electrician picks up a breaker on the way to the job. Those receipts pile up on the dashboard of the truck. If logging them means sitting down at a desk that night, most of them never get logged.</li>
<li><strong>Knowing the profit on each job.</strong> Total revenue tells you the business is busy. It doesn't tell you the bathroom remodel actually lost money because materials ran 40% over the estimate. You need to see costs against income job by job, not just for the whole year.</li>
<li><strong>Sales tax on labour and materials.</strong> Tax rules for trades are their own maze. Some places tax materials but not labour, some tax new construction differently from a repair, and some want you to collect tax on the full job. The tool has to let you set tax the right way and total it up so you know what you owe.</li>
<li><strong>Turning estimates into invoices.</strong> You quote a price, the customer says yes, and the last thing you want is to retype the whole thing as an invoice. The quote and the invoice should be the same numbers, moved forward without the double entry.</li>
</ul>
<p>Hold any tool up against those five. If it only really does the first and the last, you'll be doing the middle three in a spreadsheet on Sunday night, which is exactly the situation you're trying to get out of.</p>
HTML,
    ],

    [
      'h2' => 'Deposits and progress billing',
      'anchor' => 'deposits-progress-billing',
      'html' => <<<'HTML'
<p>This is the part most software gets wrong, and it's the part contractors need most. A deposit isn't a discount and it isn't a separate sale. It's money paid against a job before the work is done, and the invoice has to keep track of it so the balance due is always right.</p>
<p>Say you quote a deck build at $8,000 plus tax. You take a $2,400 deposit (30%) to lock the dates and cover the lumber. Three weeks later the deck is done. The final invoice can't say "$8,000 due", because the customer already paid $2,400. It has to show the full job, the deposit already paid, and the $5,600 balance still owed. Get that wrong and you either look like you're double-charging, or you quietly lose track of the deposit and undercharge yourself.</p>
<p>Progress billing is the same idea stretched over a longer job. On a $40,000 renovation you might bill in four stages so you're never floating the customer's materials out of your own pocket. A common split for bigger construction work is 30% up front, 30% at rough-in, 30% at finish, and 10% held back until the final inspection is signed off. Each stage is its own invoice, and each one needs to reference the same job so the running total makes sense.</p>
<p>What to look for: the tool should let you take a deposit or partial payment against an invoice, record more than one payment on the same invoice, and show a clear balance due at every step. If a tool treats every payment as full-or-nothing, it can't do contracting. Argo Books handles the deposit side of this directly: deposits, security deposits, and partial payments recorded against a single invoice, with an invoice status (Draft, Sent, Partial, Paid, Overdue) that reflects exactly how much has landed. For staged progress billing on a bigger job you raise a separate invoice for each stage yourself; Argo doesn't link several invoices back to one job and roll them into a single running total, so you track the job's overall total by hand. For the mechanics of taking that first payment, see <a href="/how-to-take-a-deposit-on-an-invoice/">how to take a deposit on an invoice</a>.</p>
HTML,
    ],

    [
      'h2' => 'Capturing materials and expenses on the job site',
      'anchor' => 'capturing-expenses',
      'html' => <<<'HTML'
<p>Every trade runs on materials, and materials come with a pile of paper receipts. The supply house, the hardware store, the fuel stop, the tool rental. Each one is a business expense that lowers your tax bill, but only if it actually gets recorded. The receipts that get lost are the deductions you paid for and never claimed.</p>
<p>The problem is timing. The best moment to log a receipt is the second you have it in your hand, standing at the counter. The worst moment is three weeks later when it's a faded curl of thermal paper in the cupholder and you can't remember which job it was for. Any tool that only lets you enter expenses by typing them at a desk is going to lose most of your receipts, because the desk is where you never sit.</p>
<p>What to look for: receipt scanning that reads a photo for you instead of making you type. With a desktop app the honest workflow is to photograph receipts on the spot so none get lost, then import those photos into Argo Books, where the AI receipt scanner pulls out the vendor, date, amount, and tax, so you're checking numbers instead of typing them. The free tier covers 10 scans a month and Premium covers 500, which is enough for a busy trade buying materials most days. There's also a free web receipt scanner on the site if you want to try it before installing anything. On top of that, when the bank statement comes in you can drop the CSV, Excel, or PDF straight into Argo Books and every line comes back categorized, so anything you paid by card is caught even if the paper receipt went missing.</p>
HTML,
    ],

    [
      'h2' => 'Knowing what each job actually made',
      'anchor' => 'profit-per-job',
      'html' => <<<'HTML'
<p>Ask a lot of contractors which of their jobs made the most money and you'll get a shrug. The bank balance goes up and down, the phone keeps ringing, and it all blurs together. But two jobs at the same price can have completely different outcomes. A $6,000 bathroom where materials came in at $1,500 is a good job. A $6,000 bathroom where materials ballooned to $3,400 because of a tile you underquoted is barely worth the trouble, and you want to know that before you quote the next one the same way.</p>
<p>Tracking profit per job means putting two numbers side by side for each job: what you invoiced, and what it cost you in materials, subcontractors, and other expenses. The gap is your gross profit on that job. Do it for a handful of recent jobs and patterns jump out fast: the type of work you're good at pricing, and the type where you keep leaving money on the table.</p>
<p>What to look for: at minimum, a tool that lets you label or categorize each expense so you can total up the costs for one job and set them against what you billed. In Argo Books you record expenses with categories and clear descriptions, so you can tie a supplier receipt to a specific job in the description and add up the costs for it. The free Report Builder then produces an Income Statement (a profit and loss) for the whole business, which shows your overall margin and where the money is going. For a walk-through of setting this up for trades work, see <a href="/bookkeeping-for-contractors/">bookkeeping for contractors</a>.</p>
HTML,
    ],

    [
      'h2' => 'Sales tax on labour and materials',
      'anchor' => 'sales-tax',
      'html' => <<<'HTML'
<p>Tax on contracting work is one of the trickier corners of the trade, because the rules split labour and materials in ways that change from place to place. This is a spot where a quick word with an accountant for your own situation pays for itself, but here's the shape of it so you know what your software has to handle.</p>
<ul>
<li><strong>United States.</strong> Sales tax is set state by state and sometimes city by city. Many states tax materials but not most labour, and several treat new construction differently from a repair to something that already exists. A few tax the whole contract. Your state revenue site is the source of truth.</li>
<li><strong>Canada.</strong> GST/HST runs 5% to 15% depending on the province, and it generally applies to the full job, labour included. You have to register once your revenue passes $30,000 CAD over four quarters.</li>
<li><strong>United Kingdom.</strong> Standard VAT is 20%, but some construction work qualifies for a reduced or zero rate, and the domestic reverse charge changes who accounts for the VAT on certain building services between VAT-registered businesses. The registration threshold is GBP 90,000 over a rolling 12 months.</li>
<li><strong>Australia.</strong> GST is a flat 10% once you're registered, which you must be once turnover passes $75,000 AUD.</li>
</ul>
<p>What to look for: a tool that lets you set the tax rate the way your work is actually taxed, and that keeps a running total of what you've collected so you're not guessing at filing time. Argo Books tracks Tax Collected on your invoices and Tax Paid on your expenses, and shows the net (Collected minus Paid) so you can see the number at a glance. It's important to know that it treats the tax you collect as money owed to the government, not as your profit, so your profit figure stays honest. Argo Books shows you the number to hand to your filing service; it doesn't file or remit the tax for you. Rules vary a lot in the trades, so check with an accountant before you lock in how you charge it. For a country-by-country rundown, see <a href="/tax-on-invoices-country-guide/">tax on invoices</a>.</p>
HTML,
    ],

    [
      'h2' => 'A checklist for choosing a tool',
      'anchor' => 'checklist',
      'html' => <<<'HTML'
<p>Here's the whole thing as a checklist. Run any tool you're considering down this list. It doesn't have to score a perfect ten, but the more of these it misses, the more of your evenings it's going to cost you.</p>
<ul>
<li><strong>Deposits and partial payments.</strong> Can it take a deposit and record more than one payment against a single invoice, with a correct running balance?</li>
<li><strong>Progress or milestone billing.</strong> Can you bill a job in stages that all tie back to the same job?</li>
<li><strong>Photo receipt capture.</strong> Can you photograph a materials receipt and have it read automatically, instead of typing it in by hand?</li>
<li><strong>Expense categories.</strong> Can you sort expenses so you can total the cost of one job and see your margin?</li>
<li><strong>Estimates that become invoices.</strong> Can a quote turn into an invoice without you retyping the line items?</li>
<li><strong>Flexible sales tax.</strong> Can you set tax to match how your work is taxed, and does it total up what you owe?</li>
<li><strong>Profit and loss you can read.</strong> Does it produce a clean Income Statement without an accounting degree?</li>
<li><strong>Works where you work.</strong> Does it function in a basement or a new build with no signal, or does it need a live connection to do anything?</li>
<li><strong>Price that fits a small crew.</strong> Is the monthly cost sane for a one-person or small operation, and is there a free tier to start on?</li>
</ul>
<p>The last two get overlooked and shouldn't. A cloud-only tool that spins forever on a site with no bars is worse than useless when you're standing there with a customer waiting on a number. And a subscription that made sense at $15 a month has a way of climbing, so it's worth knowing what you're signing up for.</p>
HTML,
    ],

    [
      'h2' => 'The landscape: free tools, desktop apps, and subscription suites',
      'anchor' => 'the-landscape',
      'html' => <<<'HTML'
<p>Broadly, what's on the market falls into three buckets, and each one trades something away.</p>
{{illustration:compare-scale}}
<p><strong>Free tools and spreadsheets.</strong> A spreadsheet costs nothing and does whatever you tell it to, which is exactly the problem. It'll happily let you fat-finger a total, forget which deposits you've taken, and give you no warning when a job is bleeding money. It works for the first handful of invoices, then quietly stops keeping up once you're juggling deposits, progress bills, and a stack of receipts. There are free web tools too, like an <a href="/invoice-generator/">invoice generator</a> for a one-off invoice, that are genuinely handy for a quick job but aren't built to run your whole book of work. If you want to see what a free app can do, start with <a href="/best-free-accounting-software-for-small-business/">the best free accounting software for small business</a>.</p>
<p><strong>Subscription suites.</strong> The big names run on a monthly fee, usually somewhere in the range of $15 to $30 a month for a small business, often more once you add the tier that unlocks the features you actually needed. They're powerful and they cover the full ground, but they can be heavy for a solo trade, they're built around a full double-entry ledger you may never touch, and most of them stop working the moment your signal drops. The price also tends to climb over time. If you're weighing one of these, <a href="/best-quickbooks-alternatives/">the best QuickBooks alternatives</a> lays out the field.</p>
<p><strong>Desktop apps.</strong> A desktop app installs on your machine and keeps your data local. The trade there is that it lives on one computer rather than syncing to a phone everywhere, but for a contractor that's often a fair swap: it keeps working in a basement or a half-built house with no connection, and your numbers aren't sitting on someone else's server. The right one still gives you receipt scanning from a photo you import and the reports you need. This is the bucket Argo Books sits in.</p>
<p>There's no single right answer for every trade. But if your work happens away from a desk and away from a reliable signal, the trade-offs push you toward something that runs on your own machine and doesn't fall over when the bars drop.</p>
HTML,
    ],

    [
      'h2' => 'Where Argo Books fits for contractors',
      'anchor' => 'where-argo-fits',
      'html' => <<<'HTML'
<p>Argo Books is a desktop accounting app built for people who do their own books, and it lines up well with the five things contracting demands. Here's the honest version, feature by feature, without knocking the other tools, which are fine choices for a business shaped differently from yours.</p>
{{illustration:app-check}}
<ul>
<li><strong>Deposits and partial payments.</strong> You can take a deposit or a security deposit against an invoice, record several payments as the job progresses, and the balance due stays correct the whole way through. The invoice status moves through Partial and on to Paid as the money lands.</li>
<li><strong>Receipt scanning.</strong> Photograph a materials receipt, import it into the app, and the AI pulls the vendor, date, amount, and tax. Ten scans a month on the free tier, 500 on Premium. When the card statement arrives, drop the CSV or PDF in and every line comes back categorized, so nothing slips through.</li>
<li><strong>Expenses and profit.</strong> Record costs with categories and clear descriptions, then use the free Report Builder to produce an Income Statement, a Balance Sheet, and tax summaries as a clean branded PDF, the kind of thing your accountant actually wants to see.</li>
<li><strong>Sales tax.</strong> It tracks Tax Collected and Tax Paid and shows the net, and it treats collected tax as owed to the government rather than as profit, so your margin figure is real.</li>
<li><strong>Works offline.</strong> Because it's a desktop app with your data stored locally, it keeps working in a basement, a new build, or anywhere the signal drops. Nothing spins waiting for a connection.</li>
</ul>
<p>On price, Argo Books has a free tier that's genuinely free forever, and Premium runs ${argo_premium_monthly}/month for the higher receipt limit and the extra tools like predictive analytics. For a one-person trade or a small crew, that's a sane number that isn't going to creep up on you. If you're deciding between doing your books yourself and keeping paying for a heavier suite you half-use, it's a fair place to start. Download it, load a couple of recent jobs, and see whether the deposits and receipts feel easier. That's the real test.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books runs on your own machine, handles deposits and partial payments, and reads the vendor, date, and tax off a receipt photo you import. There\'s a free tier to start on.',
  'tool_callout_cta' => 'Download Argo Books free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'What do contractors need from accounting software?',
      'a' => 'Five things above all. It has to take deposits and bill a job in stages, so the balance due is always right. It has to turn a receipt photo into a logged expense automatically, because the ones that wait to be typed up at a desk get lost. It has to let you total the cost of a job against what you invoiced, so you can see the profit. It has to handle sales tax the way your work is actually taxed. And it should keep working where you work, including sites with no signal. A general-purpose app usually nails invoicing and misses the middle three.',
    ],
    [
      'q' => 'Can I track profit per job?',
      'a' => 'You can get most of the way there without a fancy setup. The idea is to put two numbers side by side for each job: what you invoiced, and what it cost you in materials, subcontractors, and other expenses. In Argo Books you record each expense with a category and a description, so you can tie a supplier receipt to a specific job and add up the costs for it, then set that against what you billed. The free Report Builder gives you an overall Income Statement for the business. It doesn\'t have a separate per-job dashboard, but for a handful of jobs at a time, tagging costs by job in the description works well enough to spot which work actually makes money.',
    ],
    [
      'q' => 'What works on a job site with no signal?',
      'a' => 'A desktop app that stores your data locally, rather than a cloud-only tool that needs a live connection to load. Most subscription suites run in the browser and stall when the signal drops, which is exactly when you\'re standing in a basement or a half-built house needing a number. Argo Books is a desktop app, so it keeps working offline, and your data lives on your own machine. When you\'re back on a connection you can still send invoices and payment links as normal.',
    ],
    [
      'q' => 'Do I need QuickBooks?',
      'a' => 'Not necessarily. QuickBooks is capable and a lot of accountants know it, but it\'s built around a full double-entry ledger that a solo trade or small crew often never touches, it runs on a monthly subscription that tends to climb, and the cloud version leans on a live connection. If you mainly need to invoice with deposits, capture receipts, track expenses, and produce a clean profit and loss, a lighter tool can do all of that for less money and less overhead. See the best QuickBooks alternatives guide for a fuller comparison.',
    ],
    [
      'q' => 'How do I handle deposits and progress billing?',
      'a' => 'A deposit is money paid against a job before it\'s finished, so the invoice has to show the full job, the amount already paid, and the balance still owed. If you quote $8,000 and take a $2,400 deposit, the final invoice should show $5,600 due, not $8,000. Progress billing is the same thing spread over stages on a bigger job, for example 30% up front, 30% at rough-in, 30% at finish, and 10% held until inspection. Your tool needs to let you record more than one payment against a single invoice and keep the running balance correct. Argo Books supports deposits and partial payments directly, with the invoice status moving from Partial to Paid as the money comes in. For progress billing across stages, you raise a separate invoice for each stage yourself, since Argo doesn\'t tie multiple invoices back to one job automatically.',
    ],
    [
      'q' => 'Is a free tool enough, or should I pay?',
      'a' => 'A free tool or a spreadsheet is fine for your first handful of invoices, and free web tools like an invoice generator are genuinely useful for a one-off. The trouble starts once you\'re juggling deposits, progress bills, and a steady stream of receipts, which is where a spreadsheet quietly starts dropping things. A free app tier that handles invoicing and some receipt scanning is a good middle step. Argo Books has a free tier that stays free, and Premium at the monthly price above raises the receipt limit and adds forecasting, so you can start free and only pay once the volume makes it worth it.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'plumber',
    'electrician',
    'cleaning',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'best-free-accounting-software-for-small-business',
    'how-to-take-a-deposit-on-an-invoice',
    'best-quickbooks-alternatives',
  ],
];
