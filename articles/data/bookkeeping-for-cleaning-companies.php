<?php
// articles/data/bookkeeping-for-cleaning-companies.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-cleaning-companies',

  'h1' => 'Bookkeeping for cleaning companies: a simple guide',

  'meta_title' => 'Bookkeeping for Cleaning Companies: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to cleaning business bookkeeping: recurring clients, supplies, mileage, paying cleaners, and getting ready for tax time without the stress.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 40,

  'published' => '2026-06-02',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A cleaning business looks simple to run the books for, and that's exactly why the bookkeeping often slips. The money comes in steadily from regular clients, the costs feel small, and it's easy to tell yourself the numbers will sort themselves out. Then tax time arrives, the supply receipts are gone, the mileage was never logged, and you're not sure which clients actually paid.</p>
<p>The good news is that cleaning-business books don't need to be complicated. They need a few habits suited to how a cleaning business actually works: lots of recurring clients, frequent small supply purchases, driving between sites, and paying cleaners. This guide walks through each one in plain language, so your books stay current and tax time is quiet.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why cleaning-business books are their own thing',
      'anchor' => 'why-different',
      'html' => <<<'HTML'
<p>A cleaning business isn't a trade business and isn't a shop, and its books reflect that. The shape of the money is specific:</p>
<ul>
<li><strong>Income is recurring and steady.</strong> Most of it comes from the same clients on a regular schedule, weekly, fortnightly, monthly. That's a strength, but it means the bookkeeping job is keeping track of many small repeating invoices, not a few big ones.</li>
<li><strong>Costs are low-value and high-frequency.</strong> Cleaning supplies, consumables, and small equipment, bought often, in small amounts, from supermarkets and suppliers. These are the easiest costs to lose, because each receipt feels too small to bother keeping.</li>
<li><strong>Labour is the biggest cost.</strong> Unlike a trade where materials dominate, a cleaning business mostly sells time. Paying cleaners, whether employees or subcontractors, is usually your largest expense and your biggest paperwork job.</li>
<li><strong>You drive between sites.</strong> Moving between clients all day means real, deductible mileage that has to be logged as it happens.</li>
</ul>
<p>None of this is hard. It just means the habits that matter for a cleaning business are recurring billing, capturing lots of small receipts, logging mileage, and tracking what you pay your cleaners. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Separate business and personal money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>This is the cheapest, highest-value thing on the list. Open a separate bank account and, ideally, a separate card for the business. Run every client payment in and every supply purchase out through them, and keep personal spending on personal accounts.</p>
<p>For a cleaning business this matters even more than usual, because so many of your costs are small purchases from ordinary shops. When those run through a personal card mixed with your groceries, separating the business cleaning supplies from the household ones at tax time is genuinely painful, and you'll undercount. With a separate business card, the card statement is your list of business costs. You're no longer sorting; you're just keeping receipts and categorizing.</p>
<p>You don't need a business account with monthly fees. A second basic account in the business name is enough. Do this one thing and most of the year-end mess never forms.</p>
HTML,
    ],

    [
      'h2' => 'Track recurring clients and invoices',
      'anchor' => 'recurring-clients',
      'html' => <<<'HTML'
<p>Because most of your income is the same clients on a schedule, the bookkeeping question is keeping clear which invoices went out and which were paid. A handful of regular clients is easy to hold in your head; twenty or thirty is not, and that's where money goes uncollected.</p>
<p>For a small client list, a simple record works: a row per client with what they're billed and when they pay. Once the list grows, billing the same clients every cycle by hand becomes a chore that's easy to forget, and a missed invoice is income you never collected. That's the point where <a href="/recurring-invoices-when-to-use-them/">recurring invoices</a> earn their place, sending the regular bill automatically so nothing is missed. Whatever you use, the rule is the same: every invoice has a number, you can see at a glance which are unpaid, and you follow up on the ones that go overdue.</p>
<p>The unpaid ones matter. A steady-looking cleaning business can still be short of cash if a few clients are quietly behind, so knowing who owes you, and chasing it, is part of the books, not separate from them.</p>
{{illustration:cashflow-cycle}}
HTML,
    ],

    [
      'h2' => 'Supplies, mileage, and equipment',
      'anchor' => 'supplies-mileage',
      'html' => <<<'HTML'
<p>These are the deductions cleaning businesses most often lose, because they're small and frequent. Each one needs a record kept as it happens, since none can be rebuilt accurately at year-end:</p>
<ul>
<li><strong>Cleaning supplies and consumables.</strong> Every product you buy to do the work, chemicals, cloths, bags, gloves, is a business cost. The trap is the small supermarket run picked up alongside personal items. Capture the receipt and tag it the moment you buy, or it's gone.</li>
<li><strong>Mileage.</strong> Driving between client sites is deductible, but only the business share, and only if you log it. Most tax systems let you claim a per-kilometre rate for business driving or the business percentage of your vehicle costs. For a cleaner moving between several jobs a day, this adds up to a large deduction, and it's almost always undercounted because nobody logs it. An app that tracks trips or a simple per-day log fixes it.</li>
<li><strong>Equipment.</strong> Vacuums, machines, and larger gear. Smaller items are often claimed in full the year you buy them; bigger ones may be claimed over several years depending on your country's rules. Keep the receipts so the right treatment can be applied.</li>
<li><strong>Other running costs.</strong> The business share of your phone, any scheduling or invoicing software, insurance, and uniforms or branded workwear. Small individually, real across a year.</li>
</ul>
<p>The theme is the one that runs through all bookkeeping: the deduction is only as good as the record. A receipt-scanning app helps here because the volume of small receipts is exactly what makes manual entry tedious, but a photo dropped into a labelled folder works too. Capture as you go and you claim everything; reconstruct at tax time and you'll undercount.</p>
HTML,
    ],

    [
      'h2' => 'Paying cleaners',
      'anchor' => 'paying-cleaners',
      'html' => <<<'HTML'
<p>Once you bring in help, labour becomes your biggest cost and your biggest paperwork job, and the rules depend heavily on whether your cleaners are employees or subcontractors. That distinction is set by law, not by what you call them, and getting it wrong has real consequences, so it's worth confirming with an accountant for your situation.</p>
<ul>
<li><strong>Employees.</strong> If your cleaners are employees, you generally have to run payroll: withholding tax, paying it across, and meeting employment obligations. This is region-specific and is the point where many cleaning businesses move from a simple spreadsheet to proper software or a payroll service, because doing it by hand and getting it wrong is costly.</li>
<li><strong>Subcontractors.</strong> If they're genuinely self-employed, you pay their invoices as a business cost, but you usually still have to keep records of who you paid and how much, and report it. Collect each subcontractor's business details up front, not chased down the following spring, and pay traceably with a record for every payment.</li>
</ul>
<p>Either way, the money you pay cleaners is a deductible cost, so it has to be captured like any other. The two things to get right are classifying them correctly and keeping a clean record of every payment. Both are easier set up properly at the start than untangled later.</p>
HTML,
    ],

    [
      'h2' => 'Getting ready for tax time',
      'anchor' => 'tax-time',
      'html' => <<<'HTML'
<p>If you've separated your accounts, kept your supply and mileage records as you go, tracked your recurring invoices, and recorded what you paid your cleaners, tax time is assembly, not a scramble. A short monthly routine keeps it that way:</p>
<ol>
<li><strong>Once a month, check your records against the bank.</strong> Run down the business account and confirm every payment in and out is recorded and categorized. Catching a missing supply receipt or an unpaid invoice in the month it happened takes seconds; finding it ten months later takes an afternoon.</li>
<li><strong>Chase anything overdue while it's fresh.</strong> A regular client who's a couple of invoices behind is easy to nudge now and awkward to raise much later.</li>
<li><strong>Set tax money aside as you're paid.</strong> Steady income makes this easy to skip, but the tax is still owed. Moving a percentage of each payment into a separate account as it lands means the bill isn't a shock.</li>
<li><strong>Hand your accountant clean totals.</strong> Income, costs by category, mileage, and what you paid your cleaners, with receipts available. Accountants bill by the hour, so clean records mean a smaller bill and every deduction claimed.</li>
</ol>
{{illustration:calendar-due}}
<p>It's a handful of habits, not a second job. The cleaning businesses that dread tax season are the ones who left it all for the end; the ones who spend a few minutes a week keeping it current barely notice it.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books can handle recurring client invoices, scan supply receipts, and keep your cleaning-business books current as you go.',
  'tool_callout_cta' => 'See Argo Books for cleaning companies',
  'tool_callout_url' => '/for-cleaning-companies/',

  'faqs' => [
    [
      'q' => 'Do I need accounting software for a cleaning business, or is a spreadsheet enough?',
      'a' => 'A spreadsheet is genuinely enough to start, especially with a small client list and a separate bank account. Plenty of cleaning businesses run for years on a spreadsheet for income and costs, a folder of receipt photos, and a mileage log. Software earns its place as the recurring client list grows and the small receipts pile up: sending the same invoices every cycle by hand becomes error-prone, and typing dozens of supply receipts a month becomes the bottleneck. A tool that sends recurring invoices and scans receipts saves real time at that point. Start with whatever you will actually keep up with, because a simple system used weekly beats a powerful one you ignore.',
    ],
    [
      'q' => 'Are my cleaners employees or subcontractors?',
      'a' => 'That is set by law and by the actual working relationship, not by what you call them or what is written on an invoice, and it varies by country. In general, the more control you have over how, when, and where someone works, the more likely they are an employee rather than a subcontractor. The distinction matters a lot: employees usually mean payroll, tax withholding, and employment obligations, while genuine subcontractors invoice you and handle their own tax. Getting it wrong can be expensive, with back-taxes and penalties, so this is the one area worth confirming with an accountant or your local labour authority before you bring people on, not after.',
    ],
    [
      'q' => 'How do I keep track of cleaning supply receipts?',
      'a' => 'Capture each one the moment you buy, before it is lost in a van or a pocket. The challenge for a cleaning business is volume and the fact that supplies are often bought from ordinary shops alongside personal items, so the single best move is a separate business card so the cost is already flagged as business. Then photograph or scan each receipt and tag it as a supply cost while you remember. A receipt-scanning app speeds this up by reading the receipt and filing it for you, which matters when you are buying supplies several times a week, but a labelled phone folder works for lower volumes. The goal is that no small purchase slips through, because across a year they are a large deduction.',
    ],
    [
      'q' => 'How do I handle mileage for driving between clients?',
      'a' => 'Log it as it happens, because it cannot be reconstructed accurately at year-end. Most tax systems let you either record your business kilometres and claim a set per-kilometre rate, or track your total vehicle costs and claim the business percentage. For a cleaning business moving between several sites a day, the business mileage is high and the deduction is significant, which is exactly why losing it hurts. A simple per-day note of where you drove, or an app that tracks trips automatically, both work. The one thing that does not work is trying to remember a year of driving in April, which is how this deduction usually gets undercounted or skipped entirely.',
    ],
    [
      'q' => 'Is this guide trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned once, in a callout you can ignore, and this is the Argo Books site, so read it knowing that. But the habits in this guide don\'t depend on our tool. Separating your accounts, capturing supply receipts as you go, logging mileage, tracking recurring invoices, and recording what you pay your cleaners all work with a spreadsheet, a notebook, and a phone camera. If you take only the habits and never look at Argo Books, the guide did its job. We would rather you keep clean books and claim every deduction than buy software you don\'t need.',
    ],
  ],

  'related_niche_slugs' => [
    'cleaning',
    'contractor',
    'freelance',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'bookkeeping-for-landscapers',
    'how-to-track-business-expenses-without-spreadsheets',
    'best-free-ai-receipt-scanner',
  ],
];
