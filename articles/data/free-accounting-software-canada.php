<?php
// articles/data/free-accounting-software-canada.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'free-accounting-software-canada',

  'h1' => 'Free accounting software for Canadian small businesses',

  'meta_title' => 'Free Accounting Software Canada (2026) | Argo Books',

  'meta_description' => 'The free accounting software that actually works in Canada: GST, HST and PST handling, CAD reporting, and CRA-ready records, with honest trade-offs for each.',

  'schema_type' => 'Article',

  'category' => 'choosing-software',
  'hub_weight' => 7,

  'published' => '2026-06-26',
  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Canadian small business owners shopping for free accounting software hit a wrinkle the big American review sites gloss over. The tool has to handle GST, HST, and the patchwork of provincial sales taxes, report in Canadian dollars, and produce records the CRA will accept at tax time. A free invoicing tool built for a US sole proprietor can leave you doing the Canadian sales-tax math by hand.</p>
<p>The good news: several genuinely free options work well in Canada, including one of the best-known free accounting tools, which was actually built in Toronto. This guide covers what "free" really gets you, the Canada-specific things to check before you commit, the free tools worth a look with honest trade-offs, and how to keep your sales tax and year-end clean for the CRA. Where a paid tool is the smarter call, it says so.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What "free" actually means',
      'anchor' => 'what-free-means',
      'html' => <<<'HTML'
<p>"Free" covers three different things, and the difference matters for a Canadian business.</p>
<ul>
<li><strong>A free tier.</strong> A paid product the company gives away at a basic level. You get support and updates, in exchange for limits and an upgrade path. This is what most people mean by free accounting software.</li>
<li><strong>Open-source.</strong> Free because the software is community-built and you run it yourself. Genuinely free forever, with a steeper learning curve and no support line.</li>
<li><strong>A free trial.</strong> Not free at all, just delayed billing. Useful for testing a paid tool, but not a long-term answer.</li>
</ul>
<p>On a free tier, invoicing and basic income and expense tracking are almost always included. The features that tend to sit behind a paid plan are automatic bank feeds, payroll, and higher invoice volumes. None of that is Canada-specific, but the next part is: a free tool is only useful to you if it handles Canadian sales tax, and not all of them do it well.</p>
HTML,
    ],

    [
      'h2' => 'What to check before you pick (the Canada edition)',
      'anchor' => 'canada-checklist',
      'html' => <<<'HTML'
<p>Most "best free accounting software" lists are written for a US audience and skip the things that decide whether a tool works north of the border. Before you commit, check these:</p>
<ul>
<li><strong>GST, HST, and PST handling.</strong> The tool needs to let you set the right sales-tax rate and show it as its own line on an invoice. If you sell into more than one province, it should let you apply different rates. This is the single most important check.</li>
<li><strong>Canadian dollars.</strong> CAD as the default currency, formatted the way your clients and your accountant expect. Obvious, but some lighter tools assume US dollars.</li>
<li><strong>CRA-ready records.</strong> You need to be able to produce a clean income and expense summary, with receipts attached, that holds up if the CRA ever asks. Any tool worth using can export this.</li>
<li><strong>Bank connections.</strong> Automatic import of transactions from Canadian banks is common in paid tools and rarer or more limited on free tiers. If you bank with a smaller credit union, check it is supported before you rely on it.</li>
<li><strong>Where your data lives.</strong> Cloud tools keep your books on the provider's servers. A desktop tool keeps them on your own machine. Both are fine; just know which you are choosing.</li>
</ul>
<p>One thing worth knowing up front: you are only required to register for and charge GST/HST once your revenue passes the CRA's small-supplier threshold, currently $30,000 over four consecutive calendar quarters. Below that you can register voluntarily, but you are not obligated to. Either way, picking a tool that handles sales tax from day one saves you a migration later.</p>
HTML,
    ],

    [
      'h2' => 'The free options worth a look',
      'anchor' => 'free-options',
      'html' => <<<'HTML'
<p>Order roughly follows how broad the appeal is, not preference. Match these to the checklist above rather than picking the most familiar name.</p>
<ul>
<li><strong>Wave.</strong> The obvious free pick for Canadians, and notable because it was built in Toronto, so Canadian sales tax and CAD have been first-class from the start. Core invoicing and accounting are genuinely free with no time limit. You pay for card payments, payroll, and the Pro tier. Best for sole proprietors and small service businesses that want to stop paying for software. The trade-offs: thinner support and some features have moved from free to paid over time.</li>
<li><strong>Argo Books.</strong> Freemium, with a no-time-limit free tier covering invoicing, basic bookkeeping, and free receipt scanning, and Premium for higher volumes and advanced features. Prices are in CAD and it handles GST/HST. Built as a desktop app for Windows, Linux, and macOS, so your data lives on your own machine. The trade-offs: a smaller accountant ecosystem than the older players, and no built-in payroll, so it suits businesses that do not run payroll through their accounting tool.</li>
<li><strong>Zoho Books.</strong> A real free tier for the smallest businesses, with strong automation, especially if you already use other Zoho products. Handles Canadian tax. The trade-off: it works best inside the wider Zoho world, and the free tier has revenue limits you can outgrow.</li>
<li><strong>GnuCash.</strong> Free and open-source, full double-entry accounting that runs on your own computer with no subscription ever. You can configure it for Canadian sales tax. Best if you are comfortable with software and want total control at zero cost. The trade-offs: a dated interface, a real learning curve, and no support line.</li>
<li><strong>A spreadsheet.</strong> Free and flexible, and fine for the first few months. It stops scaling once you are chasing unpaid invoices, tracking GST/HST, and trying to produce a clean year-end. Most people move off it within a year.</li>
</ul>
<p>For most Canadian sole proprietors and small service businesses, the real shortlist is Wave or Argo Books for free and simple, Zoho Books if you live in that ecosystem, and GnuCash if you want open-source and do not mind the learning curve. Try one for a month before committing.</p>
HTML,
    ],

    [
      'h2' => 'Handling GST, HST, and PST',
      'anchor' => 'sales-tax',
      'html' => <<<'HTML'
<p>This is the part that trips up free tools built for the US market, so it is worth getting right.</p>
<ul>
<li><strong>Register when you cross the threshold.</strong> Once your revenue passes the CRA small-supplier threshold of $30,000 over four consecutive quarters, you must register for a GST/HST account and start charging it. Below that, registering is optional.</li>
<li><strong>Charge the rate for the right province.</strong> Sales tax in Canada is not one number. Some provinces use HST, which folds the federal GST and the provincial portion into a single rate. Others charge GST on its own, or GST plus a separate provincial sales tax. Rates vary by province, so set them up for where your customers are, not just where you are.</li>
<li><strong>Track the tax you pay, too.</strong> The GST/HST you pay on business purchases can usually be claimed back as input tax credits. That only works if you record it, which means keeping receipts and letting your software total the tax for you.</li>
<li><strong>Keep tax on its own line.</strong> Show the sales tax separately on every invoice and in your records. It makes your filing simpler and your invoices clearer for clients.</li>
</ul>
<p>Any tool on the shortlist above can do this once it is set up. The mistake to avoid is choosing a tool that cannot set provincial rates at all, then discovering it at filing time.</p>
HTML,
    ],

    [
      'h2' => 'Getting ready for tax time',
      'anchor' => 'tax-time',
      'html' => <<<'HTML'
<p>The point of bookkeeping all year is a calm tax season instead of a frantic one. Whichever free tool you pick, doing these as you go makes year-end routine:</p>
<ul>
<li><strong>Categorize as you go.</strong> Tag each expense to a category when it happens, not in a panic in the spring. A few seconds now saves hours later.</li>
<li><strong>Keep every receipt.</strong> Attach receipts to transactions so the paper trail is there if the CRA asks. Tools with receipt scanning make this painless.</li>
<li><strong>Work in CAD and keep totals clean.</strong> Income, expenses by category, and sales tax collected and paid, all in Canadian dollars, ready to hand off.</li>
<li><strong>Give your accountant clean records.</strong> Accountants bill by the hour, so clean books mean a smaller bill and every deduction claimed. Our guide to <a href="/small-business-tax-deductions/">small business tax deductions</a> covers what to track.</li>
</ul>
<p>Free software gets you all of this without a subscription. The discipline of doing it weekly, not annually, is what actually keeps tax time stress-free.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books has a no-time-limit free tier with CAD pricing and GST/HST handling, so you can try it before deciding.',
  'tool_callout_cta' => 'Try Argo Books for free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'Is free accounting software CRA compliant?',
      'a' => 'Compliance is about your records, not the price of the software. The CRA needs you to keep accurate, organized records of income and expenses, with supporting documents like receipts, and to charge and remit the right sales tax once you are registered. Any of the free tools above can produce records that meet that standard. What matters is that you keep them up to date and can export a clean summary, not whether you paid for the tool.',
    ],
    [
      'q' => 'Do I have to charge GST or HST as a small business?',
      'a' => 'Only once your revenue passes the CRA small-supplier threshold, currently $30,000 over four consecutive calendar quarters. Below that you can register and charge voluntarily, which lets you claim back the GST/HST you pay on purchases, but you are not required to. Once you cross the threshold, registering and charging is mandatory. Picking software that handles sales tax from the start means you are ready either way.',
    ],
    [
      'q' => 'Is Wave still free in Canada?',
      'a' => 'Wave still has a genuinely free tier for invoicing and basic accounting with no time limit, and it has always handled Canadian sales tax and CAD well since it was built in Toronto. Over the years some features have moved from free to its paid Pro tier, and you pay for card payments and payroll separately. For a Canadian sole proprietor who just needs invoicing and expense tracking, the free tier still covers the basics at no cost.',
    ],
    [
      'q' => 'Can free software handle both HST and PST provinces?',
      'a' => 'Yes, as long as you choose a tool that lets you set multiple sales-tax rates. Some provinces use a single HST rate, while others charge GST plus a separate provincial sales tax. A good tool lets you define the rates that apply and pick the right one per invoice or per customer. The free tools on the shortlist above all support this once configured. The ones to avoid are lightweight US-focused tools that only allow a single tax rate.',
    ],
    [
      'q' => 'Do I need accounting software if I earn under $30,000?',
      'a' => 'You are not required to register for GST/HST under the threshold, but you still have to report your business income on your tax return, which means you still need organized records. A free tool is worth using from day one because it builds the habit and keeps your income and expenses clean, so when you do cross the threshold or simply file your taxes, the work is already done. A spreadsheet can work at the very start, but most people outgrow it within a year.',
    ],
  ],

  'related_niche_slugs' => [
    'canada',
    'freelance',
    'contractor',
  ],

  'related_article_slugs' => [
    'best-free-accounting-software-for-small-business',
    'best-quickbooks-alternatives',
    'small-business-tax-deductions',
  ],
];
