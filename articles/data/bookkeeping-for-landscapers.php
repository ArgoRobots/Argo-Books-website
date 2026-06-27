<?php
// articles/data/bookkeeping-for-landscapers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-landscapers',

  'h1' => 'Bookkeeping for landscapers: a simple guide',

  'meta_title' => 'Bookkeeping for Landscapers: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to landscaping bookkeeping: job costing, the seasonal cash-flow swing, equipment and mileage, paying crew, and getting ready for tax.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 50,

  'published' => '2026-06-02',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Landscaping income swings hard with the seasons, and that's what makes the bookkeeping its own challenge. The money pours in through spring and summer, slows to a trickle in the off-season, and in between you're buying materials for one job while billing for another and paying a crew the whole time. Get the books right and you ride the seasons comfortably. Get them wrong and a profitable summer turns into a broke February.</p>
<p>You don't need an accounting background to handle it. You need a few habits suited to how a landscaping business actually runs: tracking each job, managing the seasonal cash swing, keeping records of equipment and mileage, and paying your crew cleanly. This guide walks through each in plain language.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why landscaping books are their own thing',
      'anchor' => 'why-different',
      'html' => <<<'HTML'
<p>A landscaping business has a shape most small businesses don't, and the books have to match it:</p>
<ul>
<li><strong>Income is seasonal and lumpy.</strong> A huge share of the year's revenue arrives in a few warm months, then drops off. The bookkeeping job isn't just recording it; it's managing the gap so the quiet months are covered.</li>
<li><strong>Jobs mix materials and labour.</strong> Plants, mulch, stone, and sod for one property; design and installation labour on another. Each job has its own costs, and without tracking them by job you never learn which kinds of work actually make money.</li>
<li><strong>Equipment is a major cost.</strong> Mowers, trimmers, trucks, and trailers are real money, and they're assets that wear out and get claimed over time, not simple expenses.</li>
<li><strong>You run a crew and a fleet.</strong> Paying workers and driving between sites are two of your biggest costs, and both need clean records.</li>
</ul>
<p>So the habits that matter for a landscaper are job costing, handling the seasonal cash swing, tracking equipment and mileage, and paying crew properly. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Separate business and personal money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>Start here, because it costs nothing and removes most of the year-end mess. Open a separate bank account, and ideally a separate card, for the business. Run every job payment in and every material and fuel purchase out through it, and keep personal spending separate.</p>
<p>For a landscaper this matters doubly, because your costs are a constant stream of small purchases, fuel, materials, parts, from all over town. Mixed into a personal account with everything else, separating them at tax time is a slog and you'll undercount. With a business card, the statement is your list of business costs, and the job becomes keeping receipts and categorizing rather than detective work.</p>
<p>A basic second account in the business name is enough; you don't need a fee-charging business account. This one habit is the foundation everything else sits on.</p>
HTML,
    ],

    [
      'h2' => 'Track money by job',
      'anchor' => 'job-costing',
      'html' => <<<'HTML'
<p>This is the habit that turns bookkeeping from a tax chore into a tool that makes you money. For each job, track what came in against what it cost: the materials, the crew hours, equipment time, and fuel on the cost side, and the payment on the income side. The gap is what that job actually earned.</p>
<p>This is called job costing, and you can start it without special software. Give each job a name or number and tag every expense and payment to it. Total them when the job's done and look at the margin. Do that across a season and patterns appear fast: maybe maintenance contracts earn steadily while big installs look impressive but barely clear once materials and labour are counted, or the reverse. Either way, you start quoting and choosing work based on what actually pays, not what feels busy.</p>
<p>The receipts are where job costing breaks down, because a pile of untagged fuel and material receipts can't be split back to jobs from memory. Capture each one at the moment you buy and note the job then. A phone photo with the job name, or a <a href="/best-free-ai-receipt-scanner/">receipt-scanning app</a> that lets you tag the job, both work.</p>
HTML,
    ],

    [
      'h2' => 'The seasonal cash-flow swing',
      'anchor' => 'seasonal-cash',
      'html' => <<<'HTML'
<p>This is the one that catches landscapers out, and it's a cash-flow problem, not a profit problem. You can have a genuinely profitable year and still be short of money in winter, because the profit arrived in summer and got spent, while the bills, and the tax, keep coming when the work doesn't.</p>
<p>The fix is to treat the busy season as funding the whole year, not as spending money. A few practical habits:</p>
<ul>
<li><strong>Set aside off-season money in season.</strong> Move a percentage of every summer payment into a separate account to cover the lean months. Treat it as already spent. This is the difference between a calm winter and scrambling.</li>
<li><strong>Set tax money aside as you earn it.</strong> The tax on a profitable summer is owed later, often when cash is tight. Park a percentage of income as it comes in so the bill isn't a crisis.</li>
<li><strong>Watch what you're owed.</strong> Unpaid invoices hurt most going into the slow season. Chase overdue payments while the client is still thinking about the work, not in November.</li>
</ul>
{{illustration:cashflow-cycle}}
<p>Bookkeeping is what makes this possible, because you can only set aside the right amount if you know your real numbers. A landscaper who tracks income and costs through the season knows what winter needs; one who doesn't is guessing, and usually guesses short.</p>
HTML,
    ],

    [
      'h2' => 'Equipment, mileage, and materials',
      'anchor' => 'deductions',
      'html' => <<<'HTML'
<p>These are the deductions landscapers most often lose or mishandle. Each needs a record kept as you go:</p>
<ul>
<li><strong>Equipment.</strong> Mowers, trimmers, trucks, trailers, and machines are usually assets, not simple expenses, which means larger ones are often claimed over several years rather than all at once, depending on your country's rules. Smaller tools may be written off immediately. Keep every receipt and the purchase date so the right treatment can be applied; don't guess.</li>
<li><strong>Materials.</strong> Plants, mulch, stone, soil, sod, fertiliser, every input for a job is a business cost. Tag it to the job and keep the receipt. The frequent small material runs are where deductions quietly disappear.</li>
<li><strong>Mileage and fuel.</strong> Driving between sites, to suppliers, and to quotes is deductible, but only the business share and only if logged. Most tax systems let you claim a per-kilometre rate or the business percentage of vehicle costs. For a landscaper with a truck running all day, this is a big number that's almost always undercounted without a log.</li>
<li><strong>Other running costs.</strong> Equipment repairs and servicing, blades and parts, insurance, licences, and the business share of your phone. Small individually, real across a season.</li>
</ul>
<p>The theme is the usual one: the deduction is only as good as the record. Capture as you go and you claim everything; reconstruct at tax time and you'll undercount, which means paying more tax than you owe.</p>
HTML,
    ],

    [
      'h2' => 'Paying crew and getting ready for tax',
      'anchor' => 'crew-and-tax',
      'html' => <<<'HTML'
<p>Once you take on help, labour becomes your biggest cost and your biggest paperwork job. Whether your workers are employees or subcontractors is set by law and the working relationship, not by what you call them, and it changes your obligations significantly, so it's worth confirming with an accountant. Employees usually mean running payroll, with tax withheld and paid across; genuine subcontractors invoice you, but you still keep records of who you paid and report it. Either way, collect details up front and keep a clean record of every payment, because it's all deductible cost that has to be captured.</p>
{{illustration:calendar-due}}
<p>Pull it together with a short monthly routine and tax time is calm:</p>
<ol>
<li><strong>Once a month, check your records against the bank</strong> and confirm everything's recorded and tagged, ideally to the right job.</li>
<li><strong>Chase overdue invoices</strong> while they're fresh, especially heading into the slow season.</li>
<li><strong>Confirm your set-aside</strong> for both off-season costs and tax is keeping pace with what you've earned.</li>
<li><strong>Hand your accountant clean totals</strong>, income, costs by category and by job, mileage, equipment purchases, and crew payments, with receipts available. Clean records mean a smaller bill and every deduction claimed.</li>
</ol>
<p>It's a handful of habits, not a second job. The landscapers who dread tax season and dread February are usually the same ones who left the books for the end. A few minutes a week through the season fixes both.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books can track costs against each job, scan fuel and material receipts, and keep your landscaping books current through the busy season.',
  'tool_callout_cta' => 'See Argo Books for landscapers',
  'tool_callout_url' => '/for-landscapers/',

  'faqs' => [
    [
      'q' => 'How do I manage cash flow with such a seasonal income?',
      'a' => 'Treat the busy season as funding the whole year, not as money to spend. The core habit is to move a percentage of every in-season payment into a separate account for the off-season, and treat it as already gone, so the quiet months are covered before they arrive. Do the same for tax, setting aside a percentage as you earn so the bill is not a shock when cash is tight. The thing that makes this work is knowing your real numbers: bookkeeping tells you what the lean months will cost and how much to set aside. Without it you are guessing, and seasonal businesses that guess usually guess short and get caught out in winter.',
    ],
    [
      'q' => 'Is my equipment an expense or an asset for tax?',
      'a' => 'Larger equipment like mowers, trucks, and trailers is usually an asset, which means it is often claimed gradually over its useful life through depreciation or capital allowances rather than written off all at once. Smaller tools may often be claimed in full the year you buy them, and many countries let you immediately write off assets under a set value. The threshold and the rules vary by country, so this is exactly the kind of thing to keep the receipt and purchase date for and let your accountant categorise. What matters for you is the record: keep proof of what you bought and when, and the correct treatment can be applied. A purchase with no record cannot be claimed either way.',
    ],
    [
      'q' => 'Do I need to track each job separately?',
      'a' => 'It is worth it, because landscaping jobs vary so much in margin. Job costing means tagging each cost, materials, crew hours, fuel, equipment, and each payment to a specific job, so you can see what that job actually earned after everything. Do it across a season and you learn which kinds of work genuinely pay and which just feel busy, which changes how you quote and what work you chase. You don\'t need special software to start; a job name on every receipt and a simple per-job tally works. The hard part is capturing material and fuel receipts at the moment of purchase and tagging the job then, because they cannot be split back from memory later.',
    ],
    [
      'q' => 'Are my crew employees or subcontractors?',
      'a' => 'That is determined by law and the actual working relationship, not by what you call them, and it varies by country. Broadly, the more control you have over how, when, and where someone works, the more likely they are an employee rather than a subcontractor. It matters a great deal: employees usually mean payroll, tax withholding, and employment obligations, while genuine subcontractors handle their own tax and invoice you. Getting the classification wrong can be expensive in back-taxes and penalties, so it is worth confirming with an accountant or your local labour authority before you take crew on, especially as a seasonal business that may scale up and down each year.',
    ],
    [
      'q' => 'Is this guide trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned once, in a callout you can ignore, and this is the Argo Books site, so read it knowing that. But the habits here don\'t depend on our tool. Separating your accounts, tracking costs by job, setting aside money for the off-season and for tax, logging mileage, and keeping crew records all work with a spreadsheet, a notebook, and a phone camera. If you take only the habits and never look at Argo Books, the guide did its job. We would rather you ride the seasons with clean books and claim every deduction than buy software you don\'t need.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'consultant',
    'freelance',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'bookkeeping-for-cleaning-companies',
    'how-to-track-business-expenses-without-spreadsheets',
    'small-business-tax-deductions',
  ],
];
