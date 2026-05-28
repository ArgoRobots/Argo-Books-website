<?php
// articles/data/how-to-invoice-clients.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-invoice-clients',

  'h1' => 'How to invoice clients: a step-by-step guide',

  'meta_title' => 'How to Invoice Clients: a Step-by-Step Guide | Argo Books',

  'meta_description' => 'How to invoice clients without missing anything: what to charge, what to put on the invoice, when to send it, and how to get paid on time.',

  'schema_type' => 'HowTo',

  'published' => '2026-05-30',

  'updated' => '2026-05-30',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Sending an invoice should take five minutes, not an afternoon. If it does take an afternoon, the problem is almost always one of two things: the price was never settled in writing, or the invoice itself is missing a detail the client needs before they can pay. Both are fixable, and both happen before you ever open a template.</p>
<p>This guide walks through the full flow in six steps, in the order you actually do them. Decide what to charge, gather the details, fill out the invoice, pick payment terms, send it, then track it. You'll end up with a clean PDF in the client's inbox, a payment date on your calendar, and a clear plan for the day after that date if the money hasn't arrived. No accounting background needed.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Step 1: Decide what to charge for',
      'anchor' => 'decide-what-to-charge',
      'step_name' => 'Decide what to charge for',
      'step_text' => 'Agree the price and the billing model with the client in writing before any work starts. Hourly, fixed-fee, and milestone billing each work, but the number has to be locked in.',
      'html' => <<<'HTML'
<p>The single biggest reason invoices get paid late isn't the invoice. It's the conversation that never happened before the work started. If the client and you have different numbers in your heads, the invoice is going to feel like a surprise, and a surprise invoice gets parked on someone's desk.</p>
<p>Pick one of three billing models and write it down somewhere both sides can see, even if that's just a paragraph in an email reply:</p>
<ul>
<li><strong>Hourly.</strong> Best when the scope is genuinely unclear, for example IT support, ongoing consulting, or quick patch jobs. Agree the hourly rate, the rounding rule (most people round to the nearest 15 minutes), and a not-to-exceed ceiling so the client isn't staring down an open invoice.</li>
<li><strong>Fixed-fee.</strong> Best when both sides understand the deliverable, for example a logo design, a one-page website, or a kitchen tile job. Write down exactly what's included, and what counts as a change order. Two revision rounds is a common ceiling. Anything past that is billed at your hourly rate.</li>
<li><strong>Milestone or progress billing.</strong> Best for projects over a few thousand dollars or longer than a couple of weeks. A common split is 50 percent up front, 25 percent at the midpoint, and 25 percent on completion. For construction and renovation work, a 30/30/30/10 split is also normal, with the final 10 percent held until snagging is signed off.</li>
</ul>
<p>Three numbers go in the agreement: the price, the billing model, and any deposit. A plumber finishing a Saturday job for a homeowner will usually have all three settled before the van gets unloaded. A consultant taking on a six-month engagement will have them in a one-page scope document. The format doesn't matter, but the writing-it-down does. If the only place the price exists is a verbal call, the invoice is going to feel like a quote, and quotes get negotiated.</p>
HTML,
    ],

    [
      'h2' => 'Step 2: Gather your details',
      'anchor' => 'gather-details',
      'step_name' => 'Gather your details',
      'step_text' => 'Pull together your business name and address, the client details, an invoice number, the date, and a due date. Have them ready before you open the invoice form.',
      'html' => <<<'HTML'
<p>Every invoice needs the same six pieces of information. Get them on the screen before you start filling in line items, and the rest of the process takes about three minutes.</p>
<ul>
<li><strong>Your business name and address.</strong> The legal name if you have a registered business, or your own name if you're a sole trader operating under your name. Include a phone number and an email. If you have a tax ID (ABN in Australia, EIN in the United States, GST/HST number in Canada, VAT number in the UK and EU), that goes here too.</li>
<li><strong>Client name and address.</strong> Use the legal name of the business you're billing, not a trading name or a contact person. If the client is an individual, their full name and address. The address is what gets your invoice through accounts payable on the other side. Without it, an AP clerk will sometimes bounce the invoice back.</li>
<li><strong>Invoice number.</strong> A unique number that doesn't repeat. The simplest scheme is sequential: 0001, 0002, 0003. Many businesses prefix the year (2026-0001) so the books reset cleanly each year. Whatever scheme you pick, never reuse a number. See <a href="/invoice-numbering-best-practices/">invoice numbering best practices</a> for the full set of options.</li>
<li><strong>Invoice date.</strong> The day you're sending it, not the day the work happened. The due date is calculated from this.</li>
<li><strong>Due date.</strong> Calculated from the invoice date plus your payment terms. Net 30 means 30 days from the invoice date.</li>
<li><strong>Purchase order or job reference.</strong> If the client gave you a PO number, put it on the invoice. Larger clients won't pay without it. Small clients usually don't have one, and that's fine.</li>
</ul>
<p>Spending two minutes on this list before you open the invoice form is the difference between a finished invoice and three rounds of email back and forth asking for a missing address.</p>
HTML,
    ],

    [
      'h2' => 'Step 3: Fill out the invoice',
      'anchor' => 'fill-out-invoice',
      'step_name' => 'Fill out the invoice',
      'step_text' => 'Open an invoice generator or template, drop in your line items, set the tax rate, and check the totals match what was agreed.',
      'html' => <<<'HTML'
<p>This is the quick part. Open whatever tool you're using, paste in the details from Step 2, and add the line items.</p>
<p>A line item has three fields: a description, a quantity, and a rate. Two examples:</p>
<ul>
<li>Description: "Website redesign, fixed fee". Quantity: 1. Rate: 4500. Line total: 4500.</li>
<li>Description: "Consulting (per hour)". Quantity: 12. Rate: 150. Line total: 1800.</li>
</ul>
<p>Use one line per distinct thing you're billing for, not one line per task. Three lines of "Email", "Phone call", and "Notes" reads as nickel-and-diming. One line of "Project setup and discovery, 4 hours" reads as a professional billing for their time.</p>
<p>Set the tax rate next. The rate depends on where you are and what you sell:</p>
<ul>
<li>Sales tax in the United States: varies by state and sometimes by city. Most states tax goods but not most services. Some states tax labor on new construction but not on repair. Check your state revenue site.</li>
<li>GST in Australia: a flat 10 percent if you're registered. You must register once turnover passes $75,000 AUD.</li>
<li>GST/HST in Canada: 5 to 15 percent depending on province. Registration is mandatory once you pass $30,000 CAD in revenue over four quarters.</li>
<li>VAT in the UK: standard rate is 20 percent, with a registration threshold of GBP 90,000 over a rolling 12 months.</li>
</ul>
<p>If you're below the registration threshold for your country, you usually don't add tax. If you're above it, you do. Rules vary, so check with an accountant for your situation before making a final call.</p>
<p>Check the totals at the bottom one more time. The subtotal is the sum of the line items. Tax is calculated on the subtotal (or on each taxable line, if you have a mix). The total is what the client owes. If you've already taken a deposit, enter it in the amount paid field so the balance due reflects what's actually outstanding.</p>
HTML,
    ],

    [
      'h2' => 'Step 4: Pick your payment terms',
      'anchor' => 'payment-terms',
      'step_name' => 'Pick your payment terms',
      'step_text' => 'Choose Net 30, Net 15, or Due on Receipt, write the terms on the invoice, and note any late fee.',
      'html' => <<<'HTML'
<p>Payment terms are the rule that turns the invoice date into a due date. Three common choices, in order of how fast they get you paid:</p>
<ul>
<li><strong>Due on Receipt.</strong> Payment is expected as soon as the client gets the invoice. Best for one-off jobs with new clients, small consumer work (a plumber finishing a Saturday job, a photographer delivering wedding photos), and any situation where you don't want to extend credit.</li>
<li><strong>Net 15.</strong> Payment due 15 days after the invoice date. Common for established small-business clients and most freelance and contracting work with homeowners.</li>
<li><strong>Net 30.</strong> Payment due 30 days after the invoice date. The standard for commercial clients, agencies, and anyone running a formal accounts-payable cycle. If you're billing a corporate client, expect Net 30 by default and Net 45 or Net 60 in some industries.</li>
</ul>
<p>Shorter terms get paid faster on average than longer ones, and clients tend to push toward the back of any window you give them. A Net 30 invoice typically lands in the bank around five to six weeks after it goes out. A Due on Receipt invoice tends to clear in one to two weeks. The trade-off is whether the speed-up is worth the relationship cost on bigger clients who run on monthly cycles.</p>
<p>Write a late fee into the Terms section so it isn't a surprise later. The standard is 1.5 percent per month on overdue balances, which works out to 18 percent per year. Some states cap late fees, so check your local rules before going higher.</p>
<p>For the full breakdown of when to use each, see <a href="/net-30-vs-due-on-receipt/">Net 30 vs Due on Receipt</a>.</p>
HTML,
    ],

    [
      'h2' => 'Step 5: Send the invoice',
      'anchor' => 'send-invoice',
      'step_name' => 'Send the invoice',
      'step_text' => 'Email a PDF of the invoice to the client. Use a clear subject line that includes the invoice number, and keep the body short.',
      'html' => <<<'HTML'
<p>Send it by email, as a PDF attachment. PDF over Word for the formal send: the layout is locked, the file opens on any device, and accounts payable systems are built to handle PDFs. A Word file can shift when the client opens it in a different version, and it looks like a draft rather than a final document.</p>
<p>The subject line decides whether the email gets opened today or sits in the inbox. Include your business name, the word "invoice", and the invoice number. A subject like this works well:</p>
<p><strong>Subject: Invoice 2026-0042 from [Your Business Name], due June 29</strong></p>
<p>The body of the email should be three sentences. Long emails don't get read, and they don't need to be. Here's a copy-and-paste template:</p>
<p><em>Hi [Client first name],</em></p>
<p><em>Please find attached invoice 2026-0042 for [project or work description], totaling $[amount]. Payment is due by [due date], and you can pay by [payment method]. Let me know if you need anything else from me to get this processed.</em></p>
<p><em>Thanks,<br>[Your name]</em></p>
<p>That's it. Attach the PDF and send. Two extra notes:</p>
<ul>
<li>CC accounts payable if the client has given you their AP email. At larger companies, the person who hired you is rarely the person who pays you, and sending only to the hiring contact adds days to the cycle.</li>
<li>Keep your sent folder. The email is the timestamp on when the invoice was delivered, and it's what you reach for if the client later claims they never got it.</li>
</ul>
HTML,
    ],

    [
      'h2' => 'Step 6: Track it and follow up',
      'anchor' => 'track-and-follow-up',
      'step_name' => 'Track it and follow up',
      'step_text' => 'Record the invoice in a spreadsheet or accounting app, mark it paid when the money lands, and follow up the day after it is due if it has not.',
      'html' => <<<'HTML'
<p>Sending the invoice isn't the finish line. Tracking it is. Until the money is in your account, the invoice is an open task.</p>
<p>For one or two invoices a month, a spreadsheet is fine. Five columns: invoice number, client, amount, due date, status. Mark the row paid as soon as the money lands. Sort by due date so the next thing to chase is always at the top.</p>
<p>Once you're sending five or more invoices a month, a spreadsheet starts dropping things. Switch to an accounting app like Argo Books. The app tracks the sent date, the due date, the paid date, and partial payments automatically, and it shows you what's outstanding without you having to type it in. The cost of a missed invoice is more than the cost of the app.</p>
<p>On the day after the due date, check the inbox and the bank. If the payment hasn't arrived, send a short reminder that same day. Waiting a week to send the first nudge teaches the client that your deadline is soft. The reminder should be friendly: "Hi [name], just a quick heads-up that invoice 2026-0042 was due yesterday and I haven't seen the payment land yet. Let me know if there's anything blocking it from your end."</p>
<p>Most late payments come unstuck after one polite reminder. The rest usually need a second nudge a week later, and only a small fraction require any formal escalation beyond that. For the full sequence, including what to say in the second and third reminders, see <a href="/how-to-follow-up-on-unpaid-invoices/">how to follow up on unpaid invoices</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'You can fill in your details right now in the free invoice generator and download the result.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'Do I need a tax ID on every invoice?',
      'a' => 'It depends on where you are and whether you\'re registered for the relevant tax. In Australia, your ABN has to appear on every invoice, and without it the buyer is required to withhold PAYG at 47 percent. In Canada, if you\'re registered for GST/HST you have to show the registration number on invoices of $100 or more (tax-inclusive), and add the buyer\'s name once an invoice reaches $500 or more. In the United States, an EIN isn\'t required on most invoices, though larger clients often ask for a W-9. In the UK and EU, VAT-registered businesses must show the VAT number. If you\'re not registered for any tax and you\'re below the threshold for your country, you usually don\'t need a tax ID on the invoice at all.',
    ],
    [
      'q' => 'How soon should I send the invoice after finishing work?',
      'a' => 'Same day if you can, and within 48 hours at the outside. The faster the invoice goes out, the faster it gets paid, and the more recent the work is in the client\'s head. Invoices that arrive two weeks after the job feel disconnected from what was delivered, and they end up at the back of the AP queue. For ongoing work, set a regular cadence: weekly for hourly work, monthly for retainers, or per milestone for project work. Pick the cadence in advance and stick to it, so the client knows when to expect the next bill.',
    ],
    [
      'q' => 'Can I invoice without a registered business?',
      'a' => 'Yes, in most countries. As a sole trader you can invoice under your own name with your own address and your personal tax identifier if you have one. In the United States, that means using your Social Security Number on the W-9 if you don\'t have an EIN. In Australia, you can apply for an ABN as a sole trader at no cost. In the UK, you can operate as a sole trader without registering a limited company. Once your income grows past a certain threshold, registering as a formal business often becomes useful for tax and liability reasons. Check with an accountant for your situation.',
    ],
    [
      'q' => 'Should I send a PDF or a Word file?',
      'a' => 'PDF for the formal send. The layout is locked, the file looks the same on any device, and accounts payable systems are built around PDFs. A Word file can shift when the client opens it in a different version of Word, and it reads as editable rather than final, which sometimes invites the client to "tweak" the numbers. Save the Word file in your records as a backup in case you need to edit and resend, but the version that goes to the client should be PDF. Most invoice generators, including the free one on this site, give you both formats with one click.',
    ],
    [
      'q' => 'Do I need to send an invoice if I already got paid?',
      'a' => 'Yes, in most cases. The client still needs the invoice for their records, especially if they\'re running their own books, claiming a tax deduction, or processing the payment through accounts payable. Mark the invoice as paid, show the amount paid and the zero balance due, and send it as a receipt. For very small cash jobs with a homeowner, a simple receipt may be all that\'s needed, but for any business client an invoice is the standard. Always keep a copy in your own records as well, for your own tax filing and as proof of income.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'consultant',
  ],

  'related_article_slugs' => [
    'net-30-vs-due-on-receipt',
    'invoice-numbering-best-practices',
    'how-to-follow-up-on-unpaid-invoices',
  ],
];
