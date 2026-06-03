<?php
// articles/data/bookkeeping-for-contractors.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-contractors',

  'h1' => 'Bookkeeping for contractors: a simple guide',

  'meta_title' => 'Bookkeeping for Contractors: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to contractor bookkeeping: tracking jobs, materials, mileage, and subcontractors, and getting ready for tax time without the stress.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 10,

  'published' => '2026-05-31',

  'updated' => '2026-05-31',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Bookkeeping for a contracting business is harder than for most small businesses, and not because contractors are bad at paperwork. It's because the work itself is messy to track: materials bought at three different stores for two different jobs, a truck used for work and the school run, subcontractors paid in cash, and money that arrives in big lumps with long gaps in between.</p>
<p>You don't need an accounting degree to handle it. You need a few habits that keep the numbers straight as you go, so tax time is a quiet afternoon instead of a two-week panic, and so you actually know which jobs make money. This guide walks through the parts of bookkeeping that matter most for a trades business, in plain language, with no jargon.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why contractor books are different',
      'anchor' => 'why-different',
      'html' => <<<'HTML'
<p>A shop sells the same things to walk-in customers all day. A contractor runs separate jobs, each with its own materials, labour, and timeline, and the money flows in a lumpy, unpredictable way. That creates a few problems a normal small business doesn't have:</p>
<ul>
<li><strong>Costs are spread across jobs.</strong> The lumber, the fittings, the rental on a tool, all of it belongs to a specific job. If you only track totals by month, you never learn which jobs actually made money and which quietly lost it.</li>
<li><strong>Cash flow is uneven.</strong> You spend on materials up front and get paid weeks later, sometimes in stages. A profitable business can still run out of cash mid-job if the timing isn't tracked.</li>
<li><strong>The truck and tools are mixed-use.</strong> Vehicles, phones, and tools get used for both work and life, and only the business share is deductible. That split has to be tracked or you lose the deduction.</li>
<li><strong>Subcontractors add paperwork.</strong> Paying other trades means tracking who you paid and reporting it correctly at tax time, which is its own job.</li>
</ul>
<p>The fixes for all of these are simple habits, not complicated accounting. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Separate business and personal money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>This is the single highest-value thing on the list, and it costs nothing. Open a separate bank account and, ideally, a separate card for the business. Run every job payment in and every material purchase out through them. Keep personal spending on personal accounts.</p>
<p>Why it matters so much: when business and personal money share one account, every tax season becomes a forensic exercise of going through hundreds of transactions deciding which were work. Mixed accounts also make it far harder to see whether the business is actually making money, because the truck payment and the grocery run sit in the same list. And if a tax authority ever looks closely, clean separation is the difference between a quick check and a deep one.</p>
<p>You don't need a fancy business account with monthly fees. A second basic account in the business name is enough. The discipline is the point: business money in the business account, personal money out of it. Do this one thing and half the year-end mess disappears before it starts.</p>
HTML,
    ],

    [
      'h2' => 'Track money by job, not just by month',
      'anchor' => 'track-by-job',
      'html' => <<<'HTML'
<p>This is the habit that turns bookkeeping from a tax chore into a tool that makes you money. For each job, track what came in and what went out: the deposit and final payment on the income side, and the materials, subcontractors, rentals, and any direct labour on the cost side. The gap between the two is what that job actually earned.</p>
<p>This is often called job costing, and it doesn't need special software to start. A simple approach: give every job a short name or number, and tag every expense and payment with it. At the end of the job, total the income and the costs and look at the difference. Do that for a few months and patterns appear fast. You'll find some kinds of work earn well and others barely break even once materials and time are counted, and you can quote and choose work accordingly.</p>
<p>The receipts are where job costing usually breaks down, because a pile of untagged receipts at month-end can't be split back to jobs from memory. The fix is to capture each receipt at the moment you get it and note the job then, while you still remember. A phone photo with the job name, or a <a href="/best-free-ai-receipt-scanner/">receipt-scanning app</a> that lets you tag the job, both work. The tool matters less than doing it on the spot.</p>
HTML,
    ],

    [
      'h2' => 'Materials, mileage, and tools',
      'anchor' => 'deductions',
      'html' => <<<'HTML'
<p>These are the deductions contractors most often leave on the table, which means paying more tax than you owe. Each one needs a record kept as you go, because none of them can be rebuilt accurately at year-end.</p>
<ul>
<li><strong>Materials.</strong> Every purchase for a job is a business cost: lumber, wire, fittings, fasteners, consumables. Keep the receipt and tag it to the job. The small runs add up: a year of fasteners and incidental supplies is a real number, and untracked it's a deduction you simply lose.</li>
<li><strong>Mileage or vehicle costs.</strong> Driving between jobs, to suppliers, and to quotes is deductible, but only the business share. Most tax systems let you either log business kilometres and claim a per-kilometre rate, or track all vehicle costs and claim the business percentage. Either way you need a record kept through the year. A mileage log or an app that tracks trips is the only way this number is accurate, because nobody remembers a year of driving in April.</li>
<li><strong>Tools and equipment.</strong> Tools bought for work are deductible, though larger purchases may have to be claimed over several years rather than all at once depending on your country's rules. Keep the receipts and note what each item was, so the split between an immediate write-off and a longer claim can be made correctly.</li>
<li><strong>Phone, software, and insurance.</strong> The business share of your phone, any software subscriptions, and trade or liability insurance are all costs. The business-use percentage applies to mixed-use items like the phone.</li>
</ul>
<p>The theme across all of these is the same: the deduction is only as good as the record. Capture it when it happens and the claim is solid. Try to reconstruct it at tax time and you'll undercount, and undercounting deductions is just paying extra tax for no reason.</p>
HTML,
    ],

    [
      'h2' => 'Paying subcontractors',
      'anchor' => 'subcontractors',
      'html' => <<<'HTML'
<p>If you bring in other trades, the money you pay them is a business cost, and most tax systems also want you to report those payments. The two things to get right:</p>
<ul>
<li><strong>Pay traceably and keep the record.</strong> Pay by transfer, cheque, or card rather than untracked cash where you can, and keep an invoice or record for every subcontractor payment. Cash payments with no paperwork are both a lost deduction for you and a problem if anyone ever asks.</li>
<li><strong>Collect their details up front.</strong> Most countries require you to report what you paid each subcontractor over the year, which means you need their business name, tax number, and address before you pay them, not chased down the following spring. Get the details with the first invoice and the year-end reporting is a quick export instead of a round of awkward calls.</li>
</ul>
<p>The exact forms and thresholds vary by country, so check your local rules or ask your accountant what you owe and when. But the habit is universal: a clean record of who you paid, how much, and their details, kept as you go.</p>
HTML,
    ],

    [
      'h2' => 'Getting ready for tax time',
      'anchor' => 'tax-time',
      'html' => <<<'HTML'
<p>If you've separated your accounts, tracked costs by job, and kept your receipts and mileage through the year, tax time is mostly assembly, not detective work. A simple month-end routine keeps it that way:</p>
<ol>
<li><strong>Once a month, check your records against the bank.</strong> Go through the month's business transactions and make sure every one is recorded and tagged. Catching a missing or miscategorized item in the month it happened takes seconds; catching it ten months later takes an afternoon.</li>
<li><strong>Chase anything missing while it's fresh.</strong> A receipt you can't find or a subcontractor detail you never collected is easy to fix this month and painful to fix in April.</li>
<li><strong>Set tax money aside as you get paid.</strong> Contractor income arrives in lumps and the tax on it is owed later. Moving a percentage of each payment into a separate tax-savings account as it lands means the bill isn't a shock. Your accountant can tell you a sensible percentage for your situation.</li>
<li><strong>Hand your accountant clean totals.</strong> Income, costs by category, mileage, and subcontractor payments, with the receipts available if asked. Accountants bill by the hour, so the cleaner your numbers, the smaller the bill.</li>
</ol>
<p>None of this is complicated. It's a handful of habits done consistently. The contractors who dread tax season are almost always the ones who left it all for the end. The ones who spend five minutes a week keeping it current barely notice it.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books can scan receipts, track costs against each job, and keep your contractor books current as you go.',
  'tool_callout_cta' => 'See Argo Books for contractors',
  'tool_callout_url' => '/for-contractors/',

  'faqs' => [
    [
      'q' => 'Do I need accounting software, or is a spreadsheet enough?',
      'a' => 'A spreadsheet is genuinely enough to start, especially if you have a small number of jobs and keep it current. Many contractors run for years on a separate bank account, a spreadsheet for income and costs by job, and a folder of receipts. Software earns its place as the volume grows: lots of jobs, lots of receipts, subcontractors to track, and mileage to log are where typing it all into a spreadsheet becomes the bottleneck and a tool that scans receipts and tags them to jobs saves real time. Start with whatever you will actually keep up with. A simple system used every week beats a powerful one you ignore.',
    ],
    [
      'q' => 'Is this article trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned, and yes, this is the Argo Books site, so read it knowing that. But the advice in this guide does not depend on the tool. Separating your accounts, tracking costs by job, keeping receipts and mileage as you go, and setting tax money aside are habits that work with a spreadsheet, a notebook, or any accounting app. If you take nothing but the habits and never look at Argo Books, the guide did its job. We mention the tool once, in a callout you can ignore.',
    ],
    [
      'q' => 'How do I handle a job that spans two tax years?',
      'a' => 'This is common in trades and the details depend on your country and your accounting method, so it is worth a quick question to your accountant. In general, the costs and income land in the periods they actually occur, so a job that starts in one tax year and finishes in the next has some of its costs in each. The thing that makes this manageable is the job-costing habit: if every cost and payment is already tagged to the job and dated, splitting it across two years is straightforward. If it is all in one undated pile, it is a mess. Track by job and dated, and your accountant can handle the year boundary easily.',
    ],
    [
      'q' => 'What is the most common bookkeeping mistake contractors make?',
      'a' => 'Mixing business and personal money in one account. It is the root of most year-end pain: it hides whether the business is profitable, turns tax prep into sorting hundreds of transactions by hand, and makes any tax-authority question harder to answer. The fix costs nothing but a second bank account and the discipline to keep business money in it. The second most common mistake is letting receipts pile up untagged, so the job-costing and the deductions both fall apart. Both mistakes are about habits, not knowledge, which is good news, because habits are fixable starting today.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'electrician',
    'plumber',
  ],

  'related_article_slugs' => [
    'best-free-ai-receipt-scanner',
    'best-quickbooks-alternatives',
    'how-to-invoice-clients',
  ],
];
