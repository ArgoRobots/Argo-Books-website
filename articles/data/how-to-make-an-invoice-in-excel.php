<?php
// articles/data/how-to-make-an-invoice-in-excel.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-make-an-invoice-in-excel',

  'h1' => 'How to make an invoice in Excel (with a faster way)',

  'meta_title' => 'How to Make an Invoice in Excel | Argo Books',

  'meta_description' => 'How to build an invoice in Excel or Google Sheets from scratch, with the real formulas, then an honest look at when a spreadsheet stops keeping up.',

  'schema_type' => 'HowTo',

  'category' => 'invoicing',
  'hub_weight' => 60,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => 'PT20M',

  'intro_html' => <<<'HTML'
<p>You can build a clean, professional invoice in Excel or Google Sheets in about twenty minutes, and it won't cost you a cent. A spreadsheet does the math for you, it looks tidy once it's formatted, and you almost certainly already have it open. For your first few invoices, it's a perfectly reasonable tool.</p>
<p>This guide builds one from scratch, step by step, with the actual formulas typed out so you can copy them straight in. You'll set up the header and client block, build a line-item table that totals itself, format it so it looks like a real business document, and save it as a PDF the right way. Then comes the honest part: a spreadsheet is great until you're sending more than a couple of invoices a month, at which point it quietly stops telling you who has actually paid. We'll cover exactly where that line is and what to do when you cross it.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Step 1: Set up the header and client block',
      'anchor' => 'header-and-client-block',
      'step_name' => 'Set up the header and client block',
      'step_text' => 'Put your business name, address, and logo in a block at the top, the client details below it, and the invoice number, invoice date, and due date in their own labelled cells.',
      'html' => <<<'HTML'
<p>Open a blank sheet. The top third of the page is all identity: who's billing, who's being billed, and the three dates and numbers that make the invoice official. Get this part right and the rest is just typing.</p>
<p>Start in the top-left. Merge a few cells across the top (highlight them, then Merge and Center) to make room for your business name in a large, bold font. Underneath it, in smaller text, put your address, phone number, and email, one per line. If you have a logo, use Insert then Picture and drop it into the top-right corner so it sits opposite your name. If you have a tax ID (an ABN in Australia, an EIN in the United States, a GST/HST number in Canada, a VAT number in the UK), add a line for it here too.</p>
<p>Drop down a few rows and build the client block on the left side:</p>
<ul>
<li><strong>Bill To.</strong> The legal name of the business you're invoicing, not a contact person or a trading name. Add their full address underneath. That address is what gets your invoice through the other side's accounts payable.</li>
<li><strong>Attention.</strong> Optional, but useful. The name of the person expecting the invoice, so it lands on the right desk.</li>
</ul>
<p>On the right side, opposite the client block, put three labelled cells. Label in one column, value in the next:</p>
<ul>
<li><strong>Invoice number.</strong> A unique number that never repeats. More on this in Step 4.</li>
<li><strong>Invoice date.</strong> The day you're sending it, not the day the work happened.</li>
<li><strong>Due date.</strong> The invoice date plus your terms. For Net 30, that's the invoice date plus 30 days. You can even let the sheet do it: if your invoice date is in cell G2, put <strong>=G2+30</strong> in the due date cell and it updates itself.</li>
</ul>
{{illustration:checklist}}
<p>Two minutes on this block saves you three rounds of email later asking the client for a missing address or PO number. Set it up once, then save the file as a template you copy for every future invoice.</p>
HTML,
    ],

    [
      'h2' => 'Step 2: Build the line-item table with real formulas',
      'anchor' => 'line-item-formulas',
      'step_name' => 'Build the line-item table with real formulas',
      'step_text' => 'Create columns for description, quantity, rate, and line total. Use =qty*rate for each line, =SUM() for the subtotal, subtotal times your rate for tax, and subtotal plus tax for the grand total.',
      'html' => <<<'HTML'
<p>This is the part a spreadsheet is genuinely good at: the math. Build the table once with formulas and it totals itself every time you change a number.</p>
<p>Set up four column headers in a row, say row 12. Column A: <strong>Description</strong>. Column B: <strong>Quantity</strong>. Column C: <strong>Rate</strong>. Column D: <strong>Line total</strong>. Bold that header row and give it a background colour so it reads as the top of a table.</p>
<p>Now the first formula. In the line-total cell on the first item row (D13), the amount is quantity times rate:</p>
<ul>
<li><strong>=B13*C13</strong></li>
</ul>
<p>Grab the little square at the bottom-right corner of that cell and drag it down as many rows as you need. Excel fills in <strong>=B14*C14</strong>, <strong>=B15*C15</strong>, and so on automatically. Now type your line items in plain language, one per distinct thing you're billing for:</p>
<ul>
<li>Description: "Website redesign, fixed fee". Quantity: 1. Rate: 4500. The line total cell shows 4500.</li>
<li>Description: "Consulting (per hour)". Quantity: 12. Rate: 150. The line total cell shows 1800.</li>
</ul>
<p>Under the last item row, build the totals stack in the line-total column. Say your items run from row 13 to row 22. The subtotal adds them all up:</p>
<ul>
<li><strong>Subtotal:</strong> =SUM(D13:D22)</li>
</ul>
<p>The tax row multiplies the subtotal by your rate. If the subtotal is in D23 and you charge 10% tax:</p>
<ul>
<li><strong>Tax (10%):</strong> =D23*0.1</li>
</ul>
<p>And the grand total adds the subtotal and the tax together. With tax in D24:</p>
<ul>
<li><strong>Total due:</strong> =D23+D24</li>
</ul>
<p>If you've already taken a deposit, add one more row under the total: <strong>Less deposit paid</strong> as a negative number, then a final <strong>Balance due</strong> cell of <strong>=D25-D26</strong> so the client sees exactly what's left to pay. Format every money cell as currency (highlight them, then Format Cells, then Currency) so the sheet shows $4,500.00 instead of 4500.</p>
<p>Change any quantity or rate now and watch the whole stack recalculate. That's the payoff for setting the formulas up once.</p>
HTML,
    ],

    [
      'h2' => 'Step 3: Format it and save as a PDF',
      'anchor' => 'format-and-save-pdf',
      'step_name' => 'Format it and save as a PDF',
      'step_text' => 'Clean up the borders, fonts, and spacing so it looks professional, then export to PDF. Never send the raw spreadsheet file.',
      'html' => <<<'HTML'
<p>A working invoice and a professional-looking invoice are two different things, and the gap is about five minutes of formatting. Do this once on your template and every future invoice inherits it.</p>
<ul>
<li><strong>Pick one font and two sizes.</strong> A large bold size for your business name and the word "Invoice", a normal size for everything else. Mixing three fonts makes it look like a ransom note.</li>
<li><strong>Add borders to the line-item table only.</strong> Highlight the table, then apply "All Borders". Leave the header and client blocks border-free so they don't fight the table for attention.</li>
<li><strong>Right-align the money columns.</strong> Numbers line up on the decimal point and are far easier to scan.</li>
<li><strong>Turn off the gridlines.</strong> On the View tab, untick Gridlines. Without them the page looks like a document instead of a spreadsheet.</li>
<li><strong>Set the print area.</strong> Highlight everything from your business name down to the total, then Page Layout, Print Area, Set Print Area. This stops half-empty extra columns from spilling onto a second page.</li>
</ul>
{{illustration:invoice-doc}}
<p>Now the single most important step: <strong>save it as a PDF, and send the PDF, never the spreadsheet.</strong> In Excel it's File, then Export, then Create PDF/XPS. In Google Sheets it's File, then Download, then PDF Document. There are three good reasons the raw .xlsx or Google Sheets link should never leave your computer:</p>
<ul>
<li><strong>The layout stays put.</strong> A spreadsheet reflows depending on the reader's screen, their column widths, and their version of Excel. A PDF looks identical everywhere.</li>
<li><strong>Nobody can change your numbers.</strong> An editable spreadsheet invites a client to "adjust" a quantity or a rate. A PDF is final.</li>
<li><strong>Your formulas stay hidden.</strong> Send the live file and the client can click any total and see the formula behind it, along with any other tab or note you left in the workbook. A PDF shows only the finished page.</li>
</ul>
<p>Name the file something the client can find later, like <strong>Invoice-2026-0042-YourBusiness.pdf</strong>, and that's the file you attach to the email.</p>
HTML,
    ],

    [
      'h2' => 'Step 4: Number your invoices properly',
      'anchor' => 'number-your-invoices',
      'step_name' => 'Number your invoices properly',
      'step_text' => 'Give every invoice a unique, sequential number that never repeats, and keep a master list so you always know the next number to use.',
      'html' => <<<'HTML'
<p>Every invoice needs its own number, and no two invoices should ever share one. This isn't red tape. The number is how you, your client, and both of your tax filings refer to a specific bill months later. "Did you pay invoice 2026-0042?" is a clear question. "Did you pay that invoice from March?" is not.</p>
<p>The simplest scheme is plain sequential: 0001, 0002, 0003. Many people prefix the year so the count resets cleanly each January and you can see at a glance when an invoice was raised: 2026-0001, 2026-0002. Whatever you pick, follow two rules:</p>
<ul>
<li><strong>Never reuse a number,</strong> even for a client who cancelled or an invoice you voided. Skip it and move on. Gaps are fine. Duplicates are the thing that makes your records unclear at tax time.</li>
<li><strong>Keep a master list</strong> of every number you've used so you always know the next one. In a spreadsheet workflow this usually means a separate tab, which is exactly the kind of manual tracking that starts to slip once the volume climbs.</li>
</ul>
<p>Pick a format on your very first invoice and stick with it, because changing schemes midway is where numbering gets messy. For the full set of options, including per-client and date-based formats, see <a href="/invoice-numbering-best-practices/">invoice numbering best practices</a>.</p>
HTML,
    ],

    [
      'h2' => 'Common spreadsheet invoice mistakes',
      'anchor' => 'common-mistakes',
      'step_name' => 'Avoid the common spreadsheet mistakes',
      'step_text' => 'Save each invoice as its own file, protect your formula cells, back the files up, and keep a separate paid or unpaid record so nothing slips.',
      'html' => <<<'HTML'
<p>A spreadsheet gives you all the rope you need. These are the four ways invoices go wrong in Excel, and how to head each one off.</p>
<ul>
<li><strong>Overwriting last month's invoice.</strong> The most common one by far. You open last month's file, change the client and the amounts, and save. Now last month's invoice is gone, and if that client ever queries what you billed, you have nothing to show them. Always start from a blank copy of your template and <strong>Save As</strong> a new, uniquely named file. One invoice, one file, forever.</li>
<li><strong>Broken formulas.</strong> Insert a row in the wrong place, or paste over a cell, and your <strong>=SUM()</strong> range quietly stops including a line. The total still shows a number, so nothing looks off, but it's undercounting. Before you export the PDF, glance at the subtotal and sanity-check it against the line items by hand. Locking the formula cells (Format Cells, Protection, then protect the sheet) stops you from typing over them by accident.</li>
<li><strong>No backup.</strong> If your invoices live in one folder on one laptop, they're one spilled coffee away from being gone, and with them your record of who owes you what. Keep the folder in something that syncs to the cloud, or copy it somewhere safe on a schedule you'll actually keep.</li>
<li><strong>No record of what's paid.</strong> This is the big one, and it's the reason the next section exists. The invoice file tells you what you billed. It says nothing about whether the money arrived. Unless you keep a separate master list and update the status every time a payment lands, you're relying on memory to know who still owes you, and memory is where invoices go to be forgotten.</li>
</ul>
<p>Every one of these is avoidable with discipline. The catch is that the discipline is entirely on you, every single invoice, and it's the paid or unpaid tracking that gets skipped first when you're busy.</p>
HTML,
    ],

    [
      'h2' => 'When a spreadsheet stops keeping up',
      'anchor' => 'when-to-switch',
      'step_name' => 'Know when to switch',
      'step_text' => 'A spreadsheet works for one or two invoices a month. Past that, use a tool that tracks payment status, deposits, and outstanding balances for you.',
      'html' => <<<'HTML'
<p>Here's the honest turn. A spreadsheet invoice is genuinely fine if you send one or two a month. You can hold "the Henderson job is still unpaid" in your head when there's only one of them. The tool does the math, you send the PDF, and it works.</p>
<p>The wall you hit isn't making the invoices. It's tracking them. Once you're sending five or ten a month, the question that matters stops being "can I make an invoice?" and becomes "who hasn't paid me yet?" A spreadsheet has no answer to that unless you build and maintain a whole separate tracking sheet by hand, marking each one paid the day the money lands, sorting by due date, and never forgetting a single update. Miss a few updates during a busy week and you've lost the one thing the system was supposed to give you: a reliable picture of what you're owed.</p>
{{illustration:spreadsheet-to-books}}
<p>That's the point where a proper invoicing tool earns its keep, and it's worth knowing exactly what you get for switching. If you'd rather skip the formula-building entirely even for a one-off, the <a href="/invoice-generator/">free invoice generator</a> on this site fills in the same fields and hands you a clean PDF, no spreadsheet required.</p>
<p>For the tracking side, <a href="/features/invoicing/">Argo Books</a> is a desktop app that does the part the spreadsheet can't. It keeps every invoice with a live status badge (Draft, Sent, Viewed, Partial, Paid, Overdue), so "who hasn't paid me?" is a glance, not a memory test. It handles the things that get fiddly in a spreadsheet:</p>
<ul>
<li><strong>Payment tracking.</strong> Mark an invoice paid, or record a partial payment, and the outstanding balance updates itself. An Outstanding Invoices figure shows exactly what you're still owed across every client.</li>
<li><strong>Deposits.</strong> Take a deposit up front, and the balance due reflects it automatically, no manual "less deposit" row to maintain.</li>
<li><strong>Payment links.</strong> Send an invoice with an online payment link so the client can pay it directly, and it marks itself paid when they do.</li>
<li><strong>Paid and unpaid at a glance.</strong> Statuses run all the way through to Overdue, so an invoice that's slipped past its due date flags itself instead of waiting for you to notice.</li>
</ul>
<p>The app runs on your own computer, your data stays local, and there's a free tier that covers up to {argo_free_invoice_limit} invoices. Premium is ${argo_premium_monthly}/month if you outgrow it. The way to think about it: a spreadsheet is a fine place to make an invoice, but a poor place to keep track of one. Once the tracking is what's costing you time, that's the signal to switch.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 1,

  'tool_callout_text' => 'Skip the formulas. Open the free invoice generator, fill in the fields, and download a clean PDF.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'Is there a free invoice template?',
      'a' => 'Yes, in a few places. Both Excel and Google Sheets ship with built-in invoice templates: in Excel, open File then New and search "invoice"; in Google Sheets, check the template gallery at the top of the start page. They give you a formatted header and a line-item table you can type straight into. If you\'d rather not touch a spreadsheet at all, the free invoice generator on this site lets you fill in the fields in your browser and download a finished PDF, with the totals and tax calculated for you.',
    ],
    [
      'q' => 'Can Excel total the invoice automatically?',
      'a' => 'Yes, that\'s the main reason to use a spreadsheet. Put =B13*C13 in each line-total cell to multiply quantity by rate, then =SUM(D13:D22) to add the line totals into a subtotal. For tax, multiply the subtotal by your rate, for example =D23*0.1 for 10%. The grand total is just =D23+D24. Once those formulas are in, change any quantity or rate and every total recalculates on its own. Format the money cells as currency so they show $4,500.00 instead of a bare 4500.',
    ],
    [
      'q' => 'Should I send the Excel file or a PDF?',
      'a' => 'Always send a PDF, never the spreadsheet. A PDF looks identical on every device, the client can\'t change your numbers, and your formulas and any other tabs in the workbook stay hidden. Send the live spreadsheet and the layout can shift on the reader\'s screen, the numbers become editable, and clicking any total reveals the formula behind it. In Excel, use File then Export then Create PDF. In Google Sheets, use File then Download then PDF Document. Keep the spreadsheet in your own records as the editable master, and send only the PDF.',
    ],
    [
      'q' => 'How do I track which invoices are paid?',
      'a' => 'In a spreadsheet, you keep a separate master list with a status column and update it by hand every time a payment lands: invoice number, client, amount, due date, and paid or unpaid. Sort by due date so the next thing to chase sits at the top. This works for a handful of invoices a month. Once the volume climbs, the manual updating is what slips first, and that\'s usually the point people move to an invoicing tool like Argo Books, which tracks the paid, partial, and overdue status of every invoice for you and shows a running total of what you\'re still owed.',
    ],
    [
      'q' => 'Can I make an invoice on my phone?',
      'a' => 'Yes. Google Sheets has free mobile apps for iPhone and Android, so you can open your invoice template, fill it in, and share a PDF from your phone. The Excel mobile app does the same. Editing a detailed line-item table on a small screen is fiddly, though. If you\'re on your phone, the free invoice generator on this site is usually quicker: it\'s a simple web form, it works in your phone\'s browser, and it hands you a finished PDF to email on the spot.',
    ],
    [
      'q' => 'Do I need to add tax to my invoice?',
      'a' => 'It depends on where you are and whether you\'re registered for the relevant tax. If you\'re below the registration threshold for your country, you usually don\'t charge tax at all. Above it, you do: GST is 10% in Australia (registration required past $75,000 AUD in turnover), GST/HST runs 5 to 15% by province in Canada (mandatory past $30,000 CAD), VAT is 20% standard in the UK (threshold GBP 90,000), and US sales tax varies by state and often doesn\'t apply to services. Rules vary, so check with an accountant for your situation before deciding.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'contractor',
    'designer',
  ],

  'related_article_slugs' => [
    'what-to-include-on-an-invoice',
    'how-to-invoice-clients',
    'invoice-numbering-best-practices',
    'invoice-vs-receipt',
  ],
];
