<?php
// articles/data/how-to-send-an-invoice-for-free.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-send-an-invoice-for-free',

  'h1' => 'How to send an invoice for free',

  'meta_title' => 'How to Send an Invoice for Free: Step by Step | Argo Books',

  'meta_description' => 'How to send a professional invoice for free, step by step: what to include, how to get paid faster, and which free tools actually work.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 15,

  'published' => '2026-06-01',

  'updated' => '2026-06-26',

  'reading_time_min' => 8,

  'total_time_iso8601' => 'PT10M',

  'intro_html' => <<<'HTML'
<p>You don't need to pay for software to send a professional invoice. A free tool plus the right details gets you a clean PDF that looks every bit as professional as one from a paid app, and it gets you paid just as fast. The trap is doing it sloppily: a missing due date, no payment instructions, or a vague description, any one of which gives a slow-paying client an excuse to sit on it.</p>
<p>This guide walks through sending an invoice for free, properly, from the details you pull together before you start <a href="/how-to-follow-up-on-unpaid-invoices/">following up</a> if the money is late. It works whether you're a freelancer sending your first invoice or a small business that just wants to stop paying for software you barely use.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Gather your details first',
      'anchor' => 'gather-details',
      'step_name' => 'Gather your details',
      'step_text' => 'Collect your business name and contact details, the client name and billing email, the next invoice number, the work and amounts, your payment terms, and how you want to be paid.',
      'html' => <<<'HTML'
<p>Two minutes of gathering details now saves you redoing the invoice later. Before you open any tool, have these ready:</p>
<ul>
<li><strong>Your details.</strong> Business name (or your own name if you're not registered), an address, an email, and a phone number. If you're registered for tax, your tax number too.</li>
<li><strong>The client's details.</strong> Their business name, the contact person, and the email the invoice should go to. For larger clients, that's often an accounts-payable address, not your day-to-day contact.</li>
<li><strong>An invoice number.</strong> If this is your first, pick a starting point like 1001 and go up by one each time. <a href="/invoice-numbering-best-practices/">Sequential numbers</a> look established and make invoices easy to find later.</li>
<li><strong>What you're billing for.</strong> A clear description of the work or items, the quantity, and the rate or price for each.</li>
<li><strong>Your <a href="/net-30-vs-due-on-receipt/">payment terms</a>.</strong> When it's due (on receipt, or in 14 or 30 days) and which payment methods you accept.</li>
</ul>
<p>Having this in front of you means the invoice itself takes a couple of minutes, not twenty.</p>
HTML,
    ],

    [
      'h2' => 'Pick a free way to make the invoice',
      'anchor' => 'pick-a-tool',
      'html' => <<<'HTML'
<p>There are three honest free routes. The right one depends on whether you value speed, control, or saved history.</p>
<ul>
<li><strong>A free invoice generator.</strong> The fastest path. You fill in a form in your browser and download a clean PDF, no signup and no watermark on the good ones. Best for one-off invoices and for people who don't need their invoices stored. The trade-off: a pure generator doesn't keep a history, so you save the downloaded PDFs yourself.</li>
<li><strong>A template.</strong> A Word, Excel, or Google Docs invoice template gives you full control over the layout, and you keep the files in your own folders. Best if you want to brand it heavily or you already live in a spreadsheet. The trade-off: more manual, and it's easy to make a calculation or formatting mistake if the template isn't built carefully.</li>
<li><strong>Free-tier invoicing software.</strong> Tools like Wave and Zoho Invoice have genuinely free tiers that save your invoices, track which are paid, and sometimes add a pay-online button. Best if you send invoices regularly and want a record kept for you. The trade-off: you create an account, and free tiers have limits you can outgrow. Argo Books fits here too, with one difference: it's a desktop app you download, so there's no account to create, and its free tier covers up to {argo_free_invoice_limit} invoices a month.</li>
</ul>
<p>For most people sending the occasional invoice, a free generator is the quickest way to a professional result. If you're billing the same clients every month, free-tier software that remembers them will save you time. Either way, none of these lock you in, because an invoice is just a PDF at the end of the day.</p>
HTML,
    ],

    [
      'h2' => 'Fill in the invoice',
      'anchor' => 'fill-it-in',
      'step_name' => 'Fill in the invoice',
      'step_text' => 'Add your details and the client details, the invoice number and dates, a line for each item with quantity and rate, and let the tool total the subtotal, tax, and amount due.',
      'html' => <<<'HTML'
<p>Whatever tool you picked, the invoice needs the same parts. Fill them in carefully, because a clear invoice gets paid faster:</p>
<ul>
<li><strong>The word "Invoice" and the number.</strong> So it's unmistakably a bill and easy to reference later.</li>
<li><strong>Your details and the client's details.</strong> Both clearly labelled, so there's no question who is billing whom.</li>
<li><strong>Issue date and due date.</strong> Put an actual due date, not just "Net 30". A date is harder to ignore than a term.</li>
<li><strong>A line for each item.</strong> Description, quantity, rate, and the line total. Be specific: "Website homepage redesign, 6 hours" beats "design work". Specific descriptions head off "what was this for?" emails that delay payment.</li>
<li><strong>Subtotal, tax, and total due.</strong> If you charge tax, show it as its own line so the client can see the breakdown. The total due should be big and obvious.</li>
</ul>
{{illustration:invoice-doc}}
<p>If you used a generator or software, the totals and tax are calculated for you, which removes the most common source of invoice errors. If you used a template, double-check the calculations before you send.</p>
HTML,
    ],

    [
      'h2' => 'Add how you want to be paid',
      'anchor' => 'payment-details',
      'step_name' => 'Add your payment details',
      'step_text' => 'Put clear payment instructions on the invoice: bank or e-transfer details, or a payment link, plus the due date and accepted methods, so the client can pay without asking how.',
      'html' => <<<'HTML'
<p>This is the step people skip, and it's the one that most directly affects whether you get paid on time. The invoice has to tell the client exactly how to pay, with no extra step of emailing you to ask.</p>
<ul>
<li><strong>Bank transfer or e-transfer.</strong> The free options. Put your bank details or your e-transfer email right on the invoice. In Canada, an Interac e-transfer to your email is the simplest no-fee method for small amounts.</li>
<li><strong>A payment link, if you want card payments.</strong> If you'd rather offer a "Pay now" button, a free-tier tool or a payment processor can generate a link. Card payments cost a processing fee, but they often get you paid days sooner, which is usually worth it.</li>
<li><strong>The due date and accepted methods, stated plainly.</strong> "Payable by e-transfer to name@email.com by June 15" leaves nothing to guess.</li>
</ul>
<p>The rule is simple: the easier you make it to pay, the faster the money arrives. Every extra step between the client reading the invoice and being able to pay it is a delay.</p>
HTML,
    ],

    [
      'h2' => 'Send the invoice',
      'anchor' => 'send-it',
      'step_name' => 'Send the invoice',
      'step_text' => 'Export the invoice as a PDF, attach it to an email with a clear subject line and a short polite message, send it to the right person, and copy yourself for your records.',
      'html' => <<<'HTML'
<p>How you send it matters almost as much as the invoice itself. A few things that get invoices paid rather than buried:</p>
<ul>
<li><strong>Attach a PDF, don't paste it in the email body.</strong> A PDF is what an accounts team needs to file and pay. Pasted text gets lost.</li>
<li><strong>Write a clear subject line.</strong> "Invoice #1042 from [Your Business], due June 15" tells the reader everything before they open it and makes the email findable later.</li>
<li><strong>Keep the message short and polite.</strong> Thank them, note what it's for, state the total and due date, and point to the payment details. Three or four sentences.</li>
<li><strong>Send it to the right person.</strong> For a small client, that's your contact. For a larger one, ask early who handles invoices, because sending it to the wrong inbox is a common cause of "we never got it" delays.</li>
<li><strong>Copy yourself.</strong> CC or BCC your own email so you have a timestamped record that it went out.</li>
</ul>
<p>Send it promptly, too. The best time to invoice is as soon as the work is done or on the schedule you agreed. An invoice sent the day you finish gets paid sooner than one you get around to next week.</p>
HTML,
    ],

    [
      'h2' => 'Track it and follow up',
      'anchor' => 'follow-up',
      'step_name' => 'Track it and follow up',
      'step_text' => 'Note the due date, mark the invoice paid when the money arrives, and send one polite reminder if it goes past due. Keep the PDF as your record.',
      'html' => <<<'HTML'
<p>Sending the invoice isn't the last step, getting paid is. A bit of tracking turns "I think they paid that one" into a clear picture:</p>
<ul>
<li><strong>Note the due date somewhere you'll see it.</strong> A calendar reminder, a spreadsheet column, or the status field in free-tier software. The point is to know when something has gone past due without having to remember.</li>
<li><strong>Mark it paid when the money lands.</strong> Check it off so you're not chasing an invoice that's already settled, which can lead to an awkward situation.</li>
<li><strong>Send one polite reminder if it's late.</strong> A short, friendly note a few days after the due date unsticks most late payments. People forget; they're rarely refusing. You don't have to be apologetic about it, it's owed.</li>
<li><strong>Keep the PDF.</strong> File the sent invoice in a folder by client or by year. It's your record at tax time and your proof if a client ever queries what they paid for.</li>
</ul>
<p>None of this needs software. A folder and a calendar reminder do the job for a handful of invoices a month. The tools just automate the remembering once the volume grows.</p>
HTML,
    ],

    [
      'h2' => 'Is free actually enough?',
      'anchor' => 'is-free-enough',
      'html' => <<<'HTML'
<p>For a lot of people, free isn't a starter option, it's the permanent answer. If you send a handful of invoices a month to a short list of clients, take payment by bank or e-transfer, and only see your accountant once a year, a free generator or template will carry you for years with no downside.</p>
<p>Paid invoicing software earns its monthly fee in specific situations: billing the same clients on a recurring schedule, wanting card payments built into the invoice, sending a high volume where manual entry costs real time, or needing expense tracking and reports that flow through to tax time. If two or more of those describe you, paid software will probably pay for itself. If none do, stay free and put the money somewhere it matters more.</p>
<p>For a fuller breakdown of where that line sits, see the guide on <a href="/free-vs-paid-invoicing-tools/">free versus paid invoicing tools</a>. But the short version: sending a professional invoice and getting paid is free, and always has been, but looking professional is a clean PDF with the right details, sent on time, with clear payment instructions. None of that costs anything.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 1,

  'tool_callout_text' => 'This free invoice generator makes a clean PDF with no signup.',
  'tool_callout_cta' => 'Open the free invoice generator',

  'faqs' => [
    [
      'q' => 'Do free invoices look unprofessional?',
      'a' => 'No, as long as you use a tool that doesn\'t stamp itself on the page. A clean PDF with your details, clear line items, and a total looks exactly as professional as one from a paid app, because to the client it is just a PDF. The thing that looks unprofessional is an older free generator that prints "Made with [tool name]" in the footer, or a template with broken formatting. Generate one test invoice and look at it in PDF preview before you send your first real one. If it carries someone else\'s branding, switch to a different free tool, because the good ones don\'t do that.',
    ],
    [
      'q' => 'How do I number invoices if I am just starting out?',
      'a' => 'Pick a sensible starting number and go up by one each time. Many people start at 1001 rather than 1, because a first invoice numbered 0001 quietly tells a client they are your very first customer. After that, keep them sequential and never reuse a number. The point of numbering is that every invoice has a unique reference you and the client can both point to, and that your accountant can see none are missing. Whatever scheme you choose, keep it consistent, because gaps and duplicates are what cause confusion later.',
    ],
    [
      'q' => 'Can I get paid online with a free invoice?',
      'a' => 'It depends on the method. Bank transfer and e-transfer are free and need nothing more than your details printed on the invoice. A "Pay now" card button needs a payment processor, and card payments carry a processing fee whether the invoice tool is free or not. Some free-tier software lets you add a pay-online link to a free invoice and takes the fee out of the payment. So yes, you can offer online payment on a free invoice, you just decide whether the speed of getting paid is worth the card fee, or whether a free bank transfer is fine for your clients.',
    ],
    [
      'q' => 'How soon should I send an invoice after finishing the work?',
      'a' => 'As soon as the work is done, or on whatever schedule you agreed up front. The longer you wait to invoice, the longer you wait to be paid, and a late invoice can read as if the payment wasn\'t important to you. Sending it the same day or the next morning sets a professional tone and starts the payment clock sooner. If you bill on a monthly cycle, pick a consistent day each month and stick to it, so clients come to expect it.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'The free invoice generator mentioned here is made by Argo Books, and this is the Argo Books site, so read it knowing that. But the generator genuinely needs no signup and adds no watermark, and every step in this guide works just as well with a template or a competitor\'s free tool. The advice does not depend on using our tool. If you follow the steps with a Word template and an e-transfer, you have sent a professional invoice for free, and that is a complete answer. We would rather you do that than pay for something you don\'t need.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'contractor',
  ],

  'related_article_slugs' => [
    'what-to-include-on-an-invoice',
    'how-to-invoice-clients',
    'free-vs-paid-invoicing-tools',
  ],
];
