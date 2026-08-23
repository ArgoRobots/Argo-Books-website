<?php
// articles/data/bookkeeping-for-plumbers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-plumbers',

  'h1' => 'Bookkeeping for plumbers',

  'meta_title' => 'Bookkeeping for Plumbers: Parts, Jobs & Taxes | Argo Books',

  'meta_description' => 'Bookkeeping for plumbers in plain language: parts vs labor, call-out fees, van stock, deposits on installs, job costing, and a simple monthly routine.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 58,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 14,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Plumbing money is messy in a way most trades aren't. In the same week you might snake a drain for a flat fee, answer a burst-pipe call at 2 a.m. at your emergency rate, collect a deposit on a water heater install, and invoice a general contractor for rough-in work on a reno. Each of those jobs pays differently, costs differently, and hits your books differently. Add a van full of fittings you bought weeks ago for jobs you hadn't met yet, and it gets genuinely hard to answer the simple question: am I actually making money?</p>
<p>This guide walks through the bookkeeping that matters for a solo plumber or a small outfit, in plain language: separating parts from labor, recording call-out fees, handling van stock and cost of goods sold, taking deposits on installs, costing jobs across your mix of work, and keeping the van, tools, and paperwork costs from quietly eating your margin. None of it needs an accounting degree. It needs a few habits done consistently.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Separate parts from labor on every invoice',
      'anchor' => 'parts-vs-labor',
      'html' => <<<'HTML'
<p>Start here, because almost everything else in plumbing bookkeeping depends on it. Every job you do is really two businesses in one: you sell your time, and you resell parts. A water heater install might be $600 of labor and $1,400 of tank, valves, venting, and fittings. If your invoice just says "water heater install: $2,000," your books can't tell you which half of the business is earning.</p>
{{illustration:invoice-doc}}
<p>Listing parts and labor as separate lines does three useful things at once:</p>
<ul>
<li><strong>It shows your real markup.</strong> Most plumbers mark up parts over the supplier price, and that markup is a legitimate part of your income: you sourced the part, hauled it, stand behind it, and eat the trip back to the supply house when the box has the wrong fitting. But you can only see whether your markup is doing its job if the parts revenue and the parts cost are both visible in your books, not blended into one job total.</li>
<li><strong>It makes disputes shorter.</strong> When a customer questions a bill, "the tank itself was $1,400 and the install was $600" is a conversation. A single $2,000 line is an argument.</li>
<li><strong>It can matter for tax.</strong> In some regions, parts and labor are taxed differently, or need to be shown separately for sales tax purposes. Rules vary a lot by country and province or state, so check what applies where you work, but separated invoices keep every option open.</li>
</ul>
<p>On the cost side, keep the habit symmetrical: when you buy parts, record what they cost you, and when you invoice them out, record what you charged. The gap between those two numbers, across all your jobs, is your parts margin. Plenty of plumbers discover it's thinner than they assumed once they actually measure it, usually because of restock runs, returns, and the odd fitting that gets absorbed into a job for free.</p>
HTML,
    ],

    [
      'h2' => 'Emergency call-outs and after-hours rates',
      'anchor' => 'call-outs',
      'html' => <<<'HTML'
<p>Emergency work is where plumbing differs most from other trades' books. A burst pipe on a Sunday night pays a call-out fee plus an after-hours rate, and those premiums exist for a reason: the job interrupts your life, wrecks the next morning, and often happens in the worst conditions. Your bookkeeping should let you see whether that premium is actually covering what emergency work costs you.</p>
<p>Record the call-out fee as its own line on the invoice, not folded into the hourly total. Do the same with the after-hours premium if you charge one. When those amounts are visible lines in your books, you can pull a simple report at year-end and answer questions that are otherwise pure guesswork: how much of my income is emergency work? Is my call-out fee even covering the drive and the first half hour? Did the after-hours jobs that felt heroic actually pay better than a boring Tuesday of service calls?</p>
<p>The honest answer is sometimes no. An emergency call can involve a long drive, a part you don't carry, a temporary fix, and a return visit at normal rates to finish properly. If the follow-up visit is invoiced as a fresh job, the emergency looks profitable and the follow-up looks ordinary, when really they're one job whose true margin is split across two invoices. Tag or name both invoices for the same customer and problem so you can see them together. If your emergency work keeps coming out thin, that's not a bookkeeping failure, it's bookkeeping doing its job: telling you to raise the call-out fee or tighten what it includes.</p>
HTML,
    ],

    [
      'h2' => 'Van stock, per-job parts, and cost of goods sold',
      'anchor' => 'van-stock-cogs',
      'html' => <<<'HTML'
<p>Your van is a rolling warehouse: fittings, valves, supply lines, wax rings, solder, PEX, a spare fill valve or three. That stock creates the accounting wrinkle most plumbers get wrong. The parts you buy fall into two groups, and they behave differently in your books:</p>
<ul>
<li><strong>Per-job parts.</strong> The water heater, the sump pump, the shower valve you picked up this morning for this afternoon's install. These are easy: the cost belongs to that job, and it becomes cost of goods sold when you invoice the job.</li>
<li><strong>Van stock.</strong> The box of fittings and consumables you bought to have on hand. When you spend $400 restocking the van, you haven't really "spent" $400 yet in the profit sense. You've converted cash into inventory, and each fitting becomes a cost only when it goes into a job. If you expense every supply run the day it happens, your profit lurches around with your restock schedule instead of tracking your actual work: a heavy restock month looks like a loss, and the month you coast on van stock looks weirdly profitable.</li>
</ul>
{{illustration:inventory-boxes}}
<p>You don't need to log every elbow. A workable middle ground for a small outfit: treat big-ticket and per-job parts precisely (assign them to the job), and handle van consumables with a periodic count. Every month or quarter, eyeball what's on the shelves, estimate its value, and let your books reflect stock on hand versus what's been used. Inventory software makes this less painful: Argo Books tracks inventory and cost of goods sold, so you can log what you stock, watch what each job consumes, and see a parts margin that's real rather than hopeful. However you do it, the principle is the same: parts are an asset until they're on an invoice, and your profit is only honest when costs land on the jobs that used them.</p>
HTML,
    ],

    [
      'h2' => 'Deposits on installs, pay-on-completion on service calls',
      'anchor' => 'deposits',
      'html' => <<<'HTML'
<p>Service calls are simple: do the work, invoice, get paid. Installs and renovation work are different animals. A bathroom repipe or a tankless water heater install means real money in parts before you've earned a dollar, so taking a deposit isn't greedy, it's how you avoid financing your customer's renovation out of your own pocket.</p>
<p>The bookkeeping catch: a deposit isn't income yet. When a customer hands you 40% up front for an install, you're holding their money against work you haven't done. If they cancel, some or all of it may go back. Record deposits so they're distinguishable from earned revenue, then apply the deposit against the final invoice when the job's done. If you book deposits as income the day they arrive, your revenue shows up before the work does, your parts costs land in a different period than the income they belong to, and your monthly profit becomes fiction. Argo Books handles this cleanly on the invoicing side: you can put a deposit on an invoice and the balance owing updates as payments come in, so the paper trail matches the money.</p>
<p>Renovation subcontract work adds one more wrinkle. When you're the plumbing sub on a general contractor's project, you often bill in stages, rough-in and then finish, and in some regions the contractor holds back a percentage of each payment until the project wraps. Whether holdbacks apply, and how they're treated for tax, varies by region, so confirm the local rules. In your books, invoice each stage as you complete it and track any held-back portion as money you're owed. It's easy to forget a holdback from a job that ended months ago, and forgotten holdbacks are the most polite way to lose money in the trades.</p>
HTML,
    ],

    [
      'h2' => 'Job costing across service calls, installs, and reno work',
      'anchor' => 'job-costing',
      'html' => <<<'HTML'
<p>Most plumbing businesses run a mix: quick service calls, day-long installs, and multi-week reno subcontracts. Averaged together, your books can look fine while one slice of the business quietly subsidizes another. Job costing is just the habit of asking, per job and per type of work, "what did this actually cost, and what did it actually pay?"</p>
<p>For each job, the cost side is: parts (from your per-job and van-stock tracking), your hours, any helper's hours, and a fair share of the driving. The revenue side is the invoice. You don't need software ceremony for this; even a consistent naming pattern like "Smith, ensuite repipe" on invoices and expenses lets you pull everything for one job together. Do it for a couple of months and patterns emerge that gut feel misses. Service calls often look small but carry great margins because the parts are cheap and the hourly rate does the work. Installs move big invoice numbers, but after the equipment cost, the margin percentage can be modest. Reno subcontracts bring steady weeks but at negotiated rates that are usually your thinnest, and they pay slowest.</p>
<p>Then there are warranty callbacks, the margin-eater nobody invoices. The fitting that weeps a week later, the water heater that needed a second visit to adjust: you fix these for free because your reputation is the business. That's the right call, but record the callback as a job with hours and parts and zero revenue, attached to the original work. Callbacks are a real cost of the job that spawned them. If a certain fixture brand or job type keeps generating them, your books will show it long before your memory does, and that's what tells you which products to stop installing and where your quotes need padding.</p>
HTML,
    ],

    [
      'h2' => 'The van, the tools, the license, the insurance',
      'anchor' => 'overheads',
      'html' => <<<'HTML'
<p>Between jobs sits a layer of costs that don't belong to any single customer but absolutely belong in your books, because they're the difference between your hourly rate feeling high and your bank account agreeing.</p>
<ul>
<li><strong>The van.</strong> Fuel, insurance, repairs, tires, plates, and payments or lease costs. Log each of these as expenses as they happen. Argo Books records vehicle costs as expenses like any other; it doesn't automatically track your mileage, so if your tax authority lets you claim per-kilometer or per-mile driving, keep a simple mileage log (a notebook or a phone app) alongside your books.</li>
<li><strong>Tools and equipment.</strong> A drain camera, a press tool, a jetter: these are serious money. In many tax systems, big equipment purchases are deducted gradually over years rather than all at once, while small tools are expensed immediately, and the threshold varies by country. Record the purchase either way and let your accountant sort the treatment at year-end. What you can't deduct is the receipt you never kept.</li>
<li><strong>Licensing, certifications, and insurance.</strong> Your trade license, backflow certification, liability insurance, bonding: generally deductible business costs in most places. They renew on their own schedules, so they're easy to pay and forget. Put them in your books when you pay them and note the renewal dates.</li>
</ul>
<p>The reason to be diligent here isn't only deductions. These overheads are what your labor rate has to cover before you earn anything. A plumber charging $120 an hour who never totals the van, insurance, and license costs doesn't actually know what an hour is worth. Total them once a year, divide by your billable hours, and you'll know the floor under your rate.</p>
HTML,
    ],

    [
      'h2' => 'Getting paid, sales tax, and a monthly routine',
      'anchor' => 'getting-paid-routine',
      'html' => <<<'HTML'
<p><strong>Unpaid invoices.</strong> Homeowners on service calls mostly pay on the spot. Installs and contractor work are where invoices age. Send the invoice the day the job finishes, with a clear due date, and follow up the day after it passes: a short, friendly reminder, then a firmer one, then a phone call. Most late payment is disorganization, not refusal, and the plumber who reminds politely and promptly gets paid ahead of the one who stews for two months. Your books should always be able to show who owes you what and how old each balance is. If a customer keeps not paying, we've got a full walkthrough of the escalation steps in <a href="/what-to-do-when-a-client-does-not-pay/">what to do when a client doesn't pay</a>.</p>
<p><strong>Sales tax.</strong> Depending on your country and region, you may need to register for and charge sales tax, GST, or VAT on your work once your revenue passes a threshold, and the rules on labor versus parts can differ. Treat the tax you collect as money you're holding for the government, not income, and keep it separate in your head and your books. The specifics genuinely vary by region, so confirm the thresholds and rules with your local tax authority or an accountant rather than a guide on the internet.</p>
<p><strong>A monthly routine that actually fits a plumber's life:</strong></p>
<ol>
<li><strong>Clear the receipt pile.</strong> Supplier counter receipts, fuel, the hardware-store run. Scanning them as you get them beats sorting a dashboard's worth at month-end; Argo Books can scan a receipt into an expense from your phone-camera shot at the counter.</li>
<li><strong>Invoice everything finished.</strong> Any completed job not yet invoiced is free work until you send the bill.</li>
<li><strong>Chase what's overdue.</strong> Five minutes of reminders, every month, no exceptions.</li>
<li><strong>Check your books against your bank.</strong> Make sure what your books say arrived and left matches the bank statement, so nothing slips through.</li>
<li><strong>Count the van, roughly.</strong> Update your stock estimate so your parts costs stay honest.</li>
<li><strong>Set aside tax.</strong> Move your sales tax and a slice for income tax somewhere you won't touch it.</li>
</ol>
<p>An hour a month, maybe two in a busy season. If you're starting from zero, our plain-language guide to <a href="/small-business-bookkeeping-basics/">small business bookkeeping basics</a> covers the foundations, and the <a href="/bookkeeping-for-contractors/">contractor bookkeeping guide</a> goes deeper on progress billing and working under a general contractor.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books lets you invoice with parts and labor as separate lines, take deposits on installs, and scan supplier receipts at the counter, with tax-ready reports at year-end.',
  'tool_callout_cta' => 'See invoicing in Argo Books',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'Should I show parts and labor separately on my plumbing invoices?',
      'a' => 'Yes, in almost every case. Separate lines show your parts markup clearly in your books, make customer disputes shorter because each charge is explained, and in some regions parts and labor are taxed differently, so separated invoices keep you compliant either way. It also lets you measure your parts margin: what you paid the supplier versus what you charged the customer, across all jobs. Many plumbers who start separating the two discover their real markup is thinner than they thought once returns, restock runs, and absorbed fittings are counted. The only common exception is flat-rate pricing where you deliberately quote one number; even then, track the parts cost internally so you know what each flat-rate job really earned.',
    ],
    [
      'q' => 'How do I handle the parts stocked in my van for bookkeeping?',
      'a' => 'Treat van stock as inventory, not an instant expense. When you spend $400 restocking fittings and consumables, that money becomes an asset sitting on your shelves, and each part becomes a cost only when it goes into a job. If you expense every supply run immediately, your profit jumps around with your restock schedule instead of reflecting your actual work. A practical approach for a small outfit: assign big-ticket and per-job parts directly to their jobs, and handle small consumables with a rough monthly or quarterly count of what\'s on the van. Software that tracks inventory and cost of goods sold makes this easier, but even a consistent estimate beats ignoring it.',
    ],
    [
      'q' => 'Is a deposit on an install counted as income when I receive it?',
      'a' => 'Not yet. A deposit is money you\'re holding against work you haven\'t done, and if the job cancels, some or all of it may need to go back. Record deposits so they\'re distinguishable from earned revenue, then apply them against the final invoice when the job is complete. Booking deposits as income on arrival inflates your revenue before the work happens and splits the job\'s income and its parts costs across different months, which makes your monthly profit unreliable. The exact tax treatment of deposits varies by country, so confirm the details with your accountant, but the bookkeeping habit is the same everywhere: deposit in, tracked separately, applied at completion.',
    ],
    [
      'q' => 'How should I account for warranty callbacks and free return visits?',
      'a' => 'Record them, even though there\'s no invoice. A callback has real costs: your hours, a replacement part, the drive. Log it as a zero-revenue job linked to the original work so the true margin of that job includes the return trip. Done consistently, this shows you patterns that memory misses: a fixture brand that keeps failing, a type of job that generates repeat visits, a rushed install habit that\'s costing you afternoons. That\'s information you can act on, by switching suppliers, adjusting quotes for callback-prone work, or building a small buffer into install pricing. Callbacks handled well protect your reputation; callbacks tracked well protect your margin too.',
    ],
    [
      'q' => 'Can I deduct my van, tools, and license costs as a plumber?',
      'a' => 'Generally yes, business costs like the van\'s fuel, insurance, and repairs, your tools and equipment, trade licensing, certifications, and liability insurance are deductible in most tax systems, but the mechanics vary. Large equipment purchases often have to be deducted gradually over several years rather than all at once, and vehicle deductions may work per-kilometer or as a share of actual costs depending on your country. Keep every receipt and record every cost as it happens, and keep a simple mileage log if your tax authority uses distance-based claims, since accounting software records vehicle costs as expenses but won\'t track your driving for you. Then let a local accountant apply the right treatment at year-end. Rules differ enough by region that the receipts matter more than memorizing the rules.',
    ],
  ],

  'related_niche_slugs' => [
    'plumber',
    'contractor',
    'electrician',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'bookkeeping-for-electricians',
    'small-business-bookkeeping-basics',
  ],
];
