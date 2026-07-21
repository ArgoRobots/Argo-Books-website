<?php
// articles/data/invoice-vs-receipt.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'invoice-vs-receipt',

  'h1' => 'Invoice vs receipt: what\'s the difference?',

  'meta_title' => 'Invoice vs Receipt: What\'s the Difference? | Argo Books',

  'meta_description' => 'Invoice vs receipt made simple: an invoice asks for payment before you\'re paid, a receipt proves payment after. Here\'s what goes on each and why you keep both.',

  'schema_type' => 'Article',

  'category' => 'invoicing',
  'hub_weight' => 70,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>An invoice and a receipt look almost the same on paper. Both list what was sold, both show a total, both carry your business name at the top. But they do opposite jobs, and mixing them up is one of the most common ways small businesses confuse a client or lose track of who has actually paid.</p>
<p>The short version: an invoice is a request for money you're owed, sent before you get paid. A receipt is proof that money changed hands, given after. One asks, the other confirms. This guide walks through the difference in plain terms, shows you the timeline they live on, compares what goes on each, and explains why you keep both sides for your records and your taxes.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The plain difference',
      'anchor' => 'plain-difference',
      'html' => <<<'HTML'
<p>Here are the two definitions with nothing extra bolted on:</p>
<ul>
<li><strong>An invoice is a request for payment.</strong> You send it before you've been paid. It says "here's what you owe me, here's how to pay, and here's when it's due." Until the client pays, an invoice is an open bill. It's a promise on their side, not proof of anything on yours.</li>
<li><strong>A receipt is proof of payment.</strong> You give it after the money has arrived. It says "you paid this amount, on this date, and we're square." A receipt closes the loop. Nobody owes anybody once a real receipt exists.</li>
</ul>
<p>Picture a plumber finishing a Saturday job for a homeowner. When the plumber hands over a document that says "Please pay $240 within 7 days," that's an invoice: the work is done but the money hasn't moved. When the homeowner pays and the plumber hands back a slip that says "Paid $240, thank you," that's a receipt: it's proof the homeowner settled up. Same job, same $240, two different documents doing two different jobs at two different moments.</p>
{{illustration:compare-scale}}
<p>The easiest way to remember it: an invoice points forward to a payment that hasn't happened yet. A receipt points backward at a payment that already did. If you're still waiting on the money, you're holding an invoice. If the money's in, you're holding a receipt.</p>
HTML,
    ],

    [
      'h2' => 'The timeline: invoice, payment, receipt',
      'anchor' => 'timeline',
      'html' => <<<'HTML'
<p>The clearest way to see the difference is to line the three events up in the order they actually happen:</p>
<ol>
<li><strong>Invoice.</strong> You finish the work or deliver the goods, then send the invoice. This is the request. The clock on your payment terms starts here.</li>
<li><strong>Payment.</strong> The client pays. Bank transfer, card, cash, an online payment link, it doesn't matter. This is the moment money moves from their account to yours.</li>
<li><strong>Receipt.</strong> You confirm you got the money by issuing a receipt. This is the proof, handed over after the payment lands.</li>
</ol>
<p>So the natural flow is invoice, then payment, then receipt. The invoice comes first because it's the ask. The receipt comes last because it's the confirmation, and you can't confirm a payment that hasn't happened.</p>
<p>There's one common exception. In shops, restaurants, and any over-the-counter sale, payment and receipt happen at the same instant and there's often no invoice at all. You buy a coffee, you pay, you get a receipt. No invoice is needed because there was never a gap between the sale and the payment. Invoices exist to bridge that gap. When you're selling to a business, doing project work, or extending any kind of credit ("pay me next week"), the gap is real, so the invoice fills it. When the sale and the payment are the same event, the invoice disappears and you're left with just a receipt.</p>
HTML,
    ],

    [
      'h2' => 'What goes on each',
      'anchor' => 'what-goes-on-each',
      'html' => <<<'HTML'
<p>Because they do different jobs, they carry slightly different information. A lot overlaps, but the key fields differ. Here's the side-by-side.</p>
{{illustration:invoice-doc}}
<p><strong>An invoice needs the fields that let the client pay you:</strong></p>
<ul>
<li>The word "Invoice" clearly at the top, plus a unique invoice number</li>
<li>Your business name, address, and contact details</li>
<li>The client's name and address</li>
<li>The invoice date and, most importantly, a <strong>due date</strong></li>
<li>A line-by-line list of what you're billing for, with quantities and rates</li>
<li>The subtotal, any tax, and the total <strong>amount due</strong></li>
<li>Payment terms and how to pay (bank details, a payment link, accepted methods)</li>
</ul>
<p>The invoice's job is to get money moving, so it's built around the amount due, the due date, and the instructions for paying. For the full breakdown, see <a href="/what-to-include-on-an-invoice/">what to include on an invoice</a>.</p>
<p><strong>A receipt needs the fields that prove the payment happened:</strong></p>
<ul>
<li>The word "Receipt" at the top</li>
<li>Your business name and contact details</li>
<li>The date the payment was received (not the date of the sale, if they differ)</li>
<li>What was paid for</li>
<li>The <strong>amount paid</strong> and the payment method (card, cash, transfer)</li>
<li>A zero balance, or the remaining balance if it was only a partial payment</li>
<li>Ideally a reference back to the original invoice number</li>
</ul>
<p>The one field that flips is the money line. An invoice shows an <strong>amount due</strong>: money you're still waiting for. A receipt shows an <strong>amount paid</strong>: money that already arrived. If a document says "Balance due: $500," it's an invoice. If it says "Paid in full," it's a receipt. The due date matters on an invoice and means nothing on a receipt, because on a receipt the payment is already done. Learn the receipt side in detail in <a href="/how-to-write-a-receipt/">how to write a receipt</a>.</p>
HTML,
    ],

    [
      'h2' => 'What each one is used for',
      'anchor' => 'what-each-is-for',
      'html' => <<<'HTML'
<p>The two documents serve two different people, and that's the cleanest way to understand why both exist.</p>
<p><strong>Invoices are for chasing the money you're owed.</strong> An invoice is a working document for you, the seller. It's the record that says "this client owes me $500 and it's due on the 30th." That's what lets you see, at a glance, who still hasn't paid. On the client's side, your invoice lands in their accounts payable pile: the list of bills a business has to pay. A company processes your invoice, schedules the payment, and pays it. Without a proper invoice, especially without an invoice number and your address, a bigger client's accounts-payable team will often bounce it straight back and you wait even longer. So the invoice does double duty: it's your tool for tracking what's outstanding, and it's the client's trigger to actually pay. When a payment runs late, the invoice is the thing you point to when you follow up. There's a whole method to that in <a href="/how-to-follow-up-on-unpaid-invoices/">how to follow up on unpaid invoices</a>.</p>
<p><strong>Receipts are proof for the person who paid.</strong> A receipt is mostly for the buyer, not the seller. It's their evidence that they paid you, which they need for three things: their own bookkeeping, claiming the cost back as a business expense on their taxes, and settling any dispute about whether they paid. If a client ever says "I already paid that invoice," the receipt is what proves it. When you're the buyer instead, the receipts you collect from your own suppliers are exactly what your accountant wants at tax time. Every business expense you want to deduct usually needs a receipt behind it. That's why the humble receipt matters so much: it's the paper trail that turns "I think I spent this on the business" into a deduction you can actually back up. More on that in <a href="/how-long-to-keep-business-receipts/">how long to keep business receipts</a>.</p>
HTML,
    ],

    [
      'h2' => 'The record-keeping and tax role',
      'anchor' => 'record-keeping-and-tax',
      'html' => <<<'HTML'
<p>Both documents are part of your books, and both matter at tax time, but for opposite reasons.</p>
<p><strong>Invoices are your record of income.</strong> The invoices you send are the evidence of what you earned. Your total invoiced revenue over the year is the starting point for the income you report. That's true whether or not every invoice was paid by the time your tax year closed, depending on which accounting method you use (see <a href="/net-30-vs-due-on-receipt/">how timing works</a> for a sense of why paid dates matter). Tax authorities across the US (IRS), Canada (CRA), the UK (HMRC), and Australia (ATO) all expect you to keep copies of the invoices you issue. If you're registered for sales tax, GST, HST, or VAT, your invoices are also the record of the tax you charged and now owe.</p>
<p><strong>Receipts are your record of expenses.</strong> The receipts you receive prove what you spent, and every deduction you claim needs one behind it. If you can't produce a receipt for a business cost, you generally can't safely deduct it, and if you're ever audited, missing receipts are the first thing that unravels a claim. The usual rule of thumb is to keep receipts and invoices for several years (broadly five to seven, though it varies by country and situation, so check with an accountant for yours).</p>
<p>Here's why you keep both sides of every transaction. For a sale you make, you keep the invoice you sent (proof of income) and, once it's paid, the receipt closes it out. For a purchase you make, you keep the supplier's invoice or receipt (proof of expense). Together, those two piles are your whole story: money in on one side, money out on the other. When the two piles are complete, filling in a tax return or handing everything to an accountant is a short job instead of a weekend of digging through email.</p>
HTML,
    ],

    [
      'h2' => 'Common points of confusion',
      'anchor' => 'common-confusion',
      'html' => <<<'HTML'
<p>A few things trip people up constantly. Here's each one, cleared up.</p>
<p><strong>"Bill" versus "invoice."</strong> These are the same document seen from two sides. When you send a request for payment, you call it an invoice. When you receive one and have to pay it, you often call it a bill. Your electricity bill is really the utility company's invoice to you. So "bill" and "invoice" describe the same piece of paper: it's an invoice to the person sending it and a bill to the person paying it. Neither is a receipt, because neither one proves the money has been paid yet.</p>
<p><strong>Can one document be both an invoice and a receipt?</strong> Yes, and this is the part that confuses people most. A single document can act as both, just at different moments. When you first send it with a balance due, it's an invoice. Once the client pays and you stamp it "Paid in full" with the payment date, that very same document now proves payment, so it doubles as a receipt. It didn't become a new document; it changed roles when the money arrived. This is why a paid invoice is often all a small business needs. You don't always have to produce a separate receipt if the paid-and-marked invoice already shows the amount, the date paid, and a zero balance.</p>
<p><strong>Do I still give a receipt if I already sent an invoice?</strong> Not necessarily a separate one. If your invoice, once paid, clearly shows it's settled, that satisfies the client's need for proof. But if the client specifically asks for a receipt, or if you took the payment in cash with no other paper trail, hand one over. For over-the-counter sales where there was never an invoice, the receipt is the only document, so it's not optional.</p>
<p><strong>Is a proforma invoice a receipt?</strong> No. A proforma invoice is a preliminary estimate sent before the real invoice, more like a detailed quote. It isn't a demand for payment and it's definitely not proof of one. It sits even earlier on the timeline than a normal invoice.</p>
HTML,
    ],

    [
      'h2' => 'How Argo Books handles both',
      'anchor' => 'how-argo-handles-both',
      'html' => <<<'HTML'
<p>The invoice-then-receipt flow is built into Argo Books so you don't have to juggle two separate documents.</p>
<p><strong>For the money coming in,</strong> you create an invoice with your line items, tax, and payment terms, then send it with a payment link so the client can pay online. The app tracks it through its real statuses: Draft, Sent, Viewed, Partial, Paid, Overdue. The moment you mark an invoice Paid, that same invoice records the payment date and a zero balance, which means <strong>it doubles as the receipt</strong>. There's no second document to make. The paid invoice is the proof of payment, exactly like the paid-invoice-as-receipt idea above. It handles partial payments and deposits too, so if a client pays half now, the invoice shows what's been paid and what's still due.</p>
{{illustration:receipt-scan}}
<p><strong>For the money going out,</strong> the receipts you collect from your suppliers are where the AI receipt scanning comes in. Import a photo or PDF of a supplier receipt and the app pulls out the vendor, date, amount, and tax, then files it as an expense. The free tier scans 10 receipts a month and Premium scans 500, and there's a free web receipt scanner on the site if you just want to try it. That's the buyer side of the story: those scanned receipts become your proof-of-expense pile, ready for tax time.</p>
<p>So the two piles this whole guide is about, the invoices you send and the receipts you receive, both live in one place. Your income and your expenses build up as you go, and when the Report Builder puts together your Income Statement or tax summary, both sides are already there. No weekend of digging.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books turns a paid invoice into a receipt automatically, no second document to make. See how invoicing works.',
  'tool_callout_cta' => 'See invoicing in Argo Books',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'Is an invoice proof of payment?',
      'a' => 'No. An invoice is a request for payment, sent before the money arrives, so on its own it proves the opposite: that a payment is still owed. Only once an invoice has been paid and marked as paid does it become proof of payment. What proves payment is a receipt, or a paid invoice showing the amount paid, the date, and a zero balance.',
    ],
    [
      'q' => 'Can one document be both an invoice and a receipt?',
      'a' => 'Yes. A single document can play both roles at different times. When you first send it with a balance due, it\'s an invoice. Once the client pays and you mark it "Paid in full" with the payment date, the same document now proves the payment happened, so it doubles as a receipt. This is why a paid invoice is often all a small business needs, rather than a separate receipt.',
    ],
    [
      'q' => 'Do I still give a receipt if I sent an invoice?',
      'a' => 'Not always a separate one. If your paid invoice clearly shows it\'s settled, with the amount paid and a zero balance, that already serves as proof of payment for the client. Send a distinct receipt when the client specifically asks for one, or when you took the payment in cash with no other paper trail. For counter sales where there was never an invoice, the receipt is the only document, so it isn\'t optional.',
    ],
    [
      'q' => 'Does an invoice count for taxes?',
      'a' => 'Yes. The invoices you send are your record of income and the starting point for the revenue you report, whether or not every one was paid by year-end depending on your accounting method. If you\'re registered for sales tax, GST, HST, or VAT, invoices are also the record of the tax you charged. Tax authorities in the US, Canada, the UK, and Australia all expect you to keep copies. Check with an accountant for how the timing rules apply to your situation.',
    ],
    [
      'q' => 'What\'s the difference between a bill and an invoice?',
      'a' => 'They\'re the same document seen from two sides. When you send a request for payment, you call it an invoice. When you receive one and have to pay it, you tend to call it a bill. Your electricity bill is really the utility company\'s invoice to you. Neither one is a receipt, because neither proves the money has actually been paid yet.',
    ],
    [
      'q' => 'Which comes first, the invoice or the receipt?',
      'a' => 'The invoice comes first. The natural order is invoice, then payment, then receipt: you send the invoice to request payment, the client pays, and then you issue a receipt to confirm the money arrived. The only common exception is an over-the-counter sale, where payment and receipt happen at the same instant and there\'s usually no invoice at all.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'photographer',
    'consultant',
  ],

  'related_article_slugs' => [
    'what-to-include-on-an-invoice',
    'how-to-write-a-receipt',
    'how-to-invoice-clients',
    'how-long-to-keep-business-receipts',
  ],
];
