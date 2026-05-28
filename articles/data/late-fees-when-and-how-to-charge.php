<?php
// articles/data/late-fees-when-and-how-to-charge.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'late-fees-when-and-how-to-charge',

  'h1' => 'Late fees: when and how to charge them',

  'meta_title' => 'Late Fees on Invoices: When and How to Charge | Argo Books',

  'meta_description' => 'Late fees on invoices, in plain language: what is a reasonable rate, when to start the clock, what to put on the invoice, and when to waive the fee.',

  'schema_type' => 'Article',

  'published' => '2026-05-30',

  'updated' => '2026-05-30',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A late fee is a small charge added to an unpaid invoice once it passes its due date. The goal isn't to earn extra money from the fee itself. The point of the fee is to get the original invoice paid on time, and to signal that the deadline on the invoice is real. Done right, a late fee helps you get paid faster without creating unnecessary friction. Done badly, it surprises the client, lands in their inbox as an unexpected extra charge, and starts an argument.</p>
<p>This guide walks through what a late fee is allowed to be, how much is normal, when to start charging it, and how to write the clause so the client sees it in advance. It also covers the awkward parts: how to actually add the fee to an unpaid invoice, when to waive it, and what to do if the client refuses to pay it. None of this is legal advice. The exact rules depend on where you operate and what the contract says.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Are late fees actually legal?',
      'anchor' => 'are-late-fees-legal',
      'html' => <<<'HTML'
<p>In most places, you can only charge one if the client agreed to it in writing before the invoice went unpaid. That usually means it appears in a signed contract, a quote the client approved, or the Terms section of the invoice they received before the work was done. If the first time the client hears about a late fee is the day after the due date, the fee is on shaky ground.</p>
<p>Caps on the rate vary by country, state, and province. A few examples of what's commonly seen in practice:</p>
<ul>
<li>In the United States, several states cap late fees on consumer invoices at around 1.5% per month (18% per year). Some go lower, some allow higher rates for commercial-to-commercial deals.</li>
<li>In the United Kingdom, the Late Payment of Commercial Debts Act gives small businesses a statutory right to charge interest on overdue commercial invoices at 8% above the Bank of England base rate, plus a fixed compensation amount per invoice. This applies to business-to-business work, not consumer work.</li>
<li>In Canada, late fees are governed mostly by contract law and provincial consumer rules. As of January 1, 2025, the federal Criminal Code caps interest at 35% annual percentage rate (lowered from the old 60% effective annual rate). That puts the safe ceiling at roughly 2.5 to 3% per month simple interest. Canadian courts also require the rate to be expressed as an annualised percentage on the invoice, not just as a monthly rate.</li>
<li>In Australia, late fees are not capped by statute. They depend on what your contract says and whether the fee is a reasonable estimate of your costs, not a penalty.</li>
</ul>
<p>The pattern across all of these is the same: agree to it in writing, and keep the rate within a normal range. If you charge a rate that looks like a punishment, or a fee you never disclosed, a client who pushes back is likely to win. Check your local rules or ask an accountant if you aren't sure where the line is for your jurisdiction.</p>
HTML,
    ],

    [
      'h2' => 'How much to charge',
      'anchor' => 'how-much-to-charge',
      'html' => <<<'HTML'
<p>The most common late fee in small-business invoicing is 1 to 2% per month on the overdue balance. The middle of that range, 1.5% per month, is what shows up on most contractor, freelance, and consultant invoices in North America. Annualized, 1.5% per month is 18% per year, which lines up with the cap that several US states use for consumer debt.</p>
<p>For very small invoices, a percentage looks silly. 1.5% of a $200 invoice is $3, which isn't going to change anyone's behavior. A small fixed fee works better at the low end. A common pattern is a $25 flat fee on any overdue invoice under $500, and the percentage rate above that.</p>
<p>You also have to decide whether the fee compounds or stays simple:</p>
<ul>
<li><strong>Simple monthly.</strong> The fee is calculated once per month on the original overdue balance. A $1,000 invoice at 1.5% simple gives $15 the first month, $15 the second month, $15 the third month. Three months overdue: $45 in fees.</li>
<li><strong>Compounding.</strong> The fee is calculated each month on the new balance, including previous late fees. A $1,000 invoice at 1.5% compounded gives $15.00 the first month, $15.23 the second month, $15.45 the third month. Three months overdue: $45.68 in fees.</li>
</ul>
<p>For most small-business work, simple monthly is the right call. It's easier to explain, easier to calculate, and easier to defend if the client argues. Compounding makes a noticeable difference only on large balances that have been overdue for many months, and at that point the late fee is no longer the main issue. Whatever you pick, write it down in the contract or invoice terms in plain language. "1.5% simple interest per month on overdue balances" is clearer than a paragraph of formulas.</p>
HTML,
    ],

    [
      'h2' => 'When to start charging',
      'anchor' => 'when-to-start',
      'html' => <<<'HTML'
<p>The simplest rule is the day after the due date. If the invoice says Net 30 and the due date is June 29, the late fee starts on June 30. Anything later than that makes the deadline feel flexible, and clients learn quickly which due dates actually matter.</p>
<p>That said, a short grace period is normal and reasonable. Five to ten calendar days after the due date is common, and it covers the usual delays: a client on holiday, a payment that left their bank but hasn't landed in yours, an accounts-payable cycle that runs on Tuesdays. A grace period also gives you cover to send a polite reminder first, before any fee gets added. Most invoices that are late by a few days come in after one nudge, and you never need to charge the fee at all.</p>
<p>Whatever rule you pick, the rule has to be on the invoice in writing, in advance. The client should be able to read the Terms section the day they receive the invoice and know exactly when the fee starts. A few examples of clear wording:</p>
<ul>
<li>"A late fee of 1.5% per month will be applied to any balance unpaid after the due date."</li>
<li>"Invoices unpaid 10 days after the due date are subject to a 1.5% monthly late fee."</li>
<li>"Overdue invoices accrue a flat $25 late fee, plus 1.5% per month on balances over $500."</li>
</ul>
<p>Pick one rule, write it on every invoice, and apply it the same way to every client. Inconsistent fees, where you charge one client and waive it for another with no clear reason, are where disputes start. If you want the option to waive in a particular case, that's fine: the rule is the default, and waiving is a deliberate choice you make for a specific reason. More on when to waive below.</p>
HTML,
    ],

    [
      'h2' => 'How to write the late-fee clause',
      'anchor' => 'late-fee-clause',
      'html' => <<<'HTML'
<p>The clause doesn't need to be long. Two or three sentences in the Terms section of your invoice is enough. The goal is for a non-lawyer to read it once and understand exactly what happens if they pay late. Skip the legalese.</p>
<p>Here's a sample clause you can paste into the Terms section of your invoices and adjust to fit your situation:</p>
<p><em>Payment is due by the date shown on this invoice. Invoices unpaid more than 10 days after the due date are subject to a late fee of 1.5% per month (equivalent to an annualised rate of 18% per year) on the outstanding balance, calculated as simple interest. A flat fee of $25 applies in place of the percentage on overdue balances under $500.</em></p>
<p>If you're in Canada, the annualised rate must be stated on the invoice for the clause to be enforceable (Interest Act, section 4).</p>
<p>That covers four things in three sentences: when payment is due, when the fee starts, the rate, and the small-invoice exception. If you don't want the flat-fee exception, drop the third sentence. If you want the fee to start the day after the due date with no grace period, change "more than 10 days after" to "after". If your jurisdiction caps the rate lower than 1.5%, adjust the number.</p>
<p>A few small things that make the clause hold up better:</p>
<ul>
<li>Use "late fee" or "interest on overdue balances" rather than "penalty". In some jurisdictions, courts are less willing to enforce a clause that reads as a penalty than one that reads as a reasonable charge for the cost of delayed payment.</li>
<li>Put the clause on the invoice itself, in the Terms section, not buried in a contract the client signed six months ago. The invoice is what the client looks at when the bill arrives. If the clause is on the invoice, they can't say they didn't know.</li>
<li>Use the same wording on every invoice you send to that client. If the clause changes between invoices, a client arguing the fee can point at the inconsistency.</li>
</ul>
<p>If you have a written contract with the client, the clause should appear there too, in the same words. The contract is the binding agreement, and the invoice is the reminder.</p>
HTML,
    ],

    [
      'h2' => 'How to add the fee to an unpaid invoice',
      'anchor' => 'add-fee-to-invoice',
      'html' => <<<'HTML'
<p>This is the part most small-business owners get wrong. The fee doesn't get added to the original invoice. The original invoice stays exactly as it was sent, with the original amount, the original date, and the original due date. Changing a sent invoice after the fact, by editing the line items or bumping the total, makes your records confusing and gives the client a fair reason to argue the new number.</p>
<p>The clean way is to send a fresh invoice with a new invoice number, dated the day you're sending it, with a single line item: "Late fee on invoice 2026-0042, overdue since June 30". The amount is the calculated fee. The new invoice references the old one in the description and in any cover email, so the client can see exactly what it relates to.</p>
<p>An example. The original invoice was 2026-0042 for $1,000, due June 29. It's now July 10, eleven days late. Your terms say a 1.5% monthly late fee with a 10-day grace period. The fee is $15, and you send a new invoice 2026-0058 with one line: "Late fee on invoice 2026-0042 (overdue since June 30), 1.5% on $1,000". Total: $15. Payment terms: Due on Receipt.</p>
<p>The new invoice gets its own number from your normal sequence. Don't reuse 2026-0042 or tack a suffix on it. Each invoice in your books is one document with one number, and the late-fee charge is a separate document for a separate event. See <a href="/invoice-numbering-best-practices/">invoice numbering best practices</a> for the full rules on how to handle sequences cleanly.</p>
<p>The cover email is short. Something like: "Hi [name], invoice 2026-0042 was due June 29 and is still outstanding. Per the terms on the original invoice, I have attached a separate invoice for the late fee. Please settle both at your earliest convenience." Two attachments: the original PDF (for reference) and the new late-fee PDF (for payment). If the client pays both, the situation closes. If they pay only the original and ask you to drop the fee, you have a decision to make. More on that below.</p>
HTML,
    ],

    [
      'h2' => 'When to waive the late fee',
      'anchor' => 'when-to-waive',
      'html' => <<<'HTML'
<p>Having a late-fee policy doesn't mean you charge it every time. The policy gives you the right to charge it. Whether you actually do is a judgment call, and the answer is often no.</p>
<p>Three situations where waiving the fee is usually the right call:</p>
<ul>
<li><strong>First-time slip with a good client.</strong> A client who has paid you on time for a year, who runs a real business, and who missed one due date by a few days. Sending a late-fee invoice over $15 will sour a relationship that's worth thousands of dollars per year. Send a polite reminder, accept the late payment when it arrives, and skip the fee.</li>
<li><strong>Clear miscommunication.</strong> The invoice went to the wrong email. The client thought the deposit covered the final balance. The PO number was missing and the invoice sat in the accounts-payable queue waiting for more information. Anywhere the late payment is partly on your end, or partly on a mix-up that no one really caused, charging the fee feels punitive. Fix the mix-up, accept the payment, move on.</li>
<li><strong>Long-term relationships where the fee will burn the relationship.</strong> A repeat client who occasionally runs a week or two late but always pays. A friend's business that you took on as a favor. A client referring you new work every quarter. The math on the fee is small, and the math on the relationship is large. Skip the fee, keep the client.</li>
</ul>
<p>Three situations where charging the fee is the right call:</p>
<ul>
<li><strong>Repeat offenders.</strong> A client who pays late every single invoice has learned that your deadline is soft. Charging the fee reinforces that your payment terms are real. Some clients will adjust their payment cycle after the first time it costs them money.</li>
<li><strong>Clients who go silent.</strong> Two reminders, no reply, the invoice is now a month past due. The fee partly offsets the cost of carrying the unpaid balance and spending time following up.</li>
<li><strong>Large balances.</strong> If the invoice is $20,000 and it's three months late, the fee is no longer about teaching a lesson. It's about partly covering the cost of you not having that money for three months.</li>
</ul>
<p>When you waive the fee, say so in writing: "I am waiving the late fee on this one as a one-time courtesy. The next invoice will be subject to the standard terms." That keeps the policy alive for next time without making the current situation harder.</p>
HTML,
    ],

    [
      'h2' => 'What happens if the client refuses to pay the late fee',
      'anchor' => 'client-refuses',
      'html' => <<<'HTML'
<p>A real possibility: you send the late-fee invoice, the client pays the original balance, and the late-fee invoice sits unpaid. The client may not say anything. They may send an email saying they don't believe the fee applies. Either way, the fee is now its own outstanding invoice, and you have to decide what to do.</p>
<p>Three options, in order of how much energy they cost you:</p>
<ul>
<li><strong>Drop it.</strong> If the relationship is worth more than the fee, write it off. Send a short note: "Thanks for settling invoice 2026-0042. I am writing off the late fee on this one. Please make sure invoice 2026-0058 is paid on time so we do not run into the same situation again." You keep the client, the lesson is on record, and you're out the cost of the fee, which on most invoices is a small number.</li>
<li><strong>Pursue it through follow-up.</strong> Treat the late-fee invoice the same way you would treat any other overdue invoice. Send reminders, follow up, escalate to phone calls if needed. Most clients who initially balk at the fee will pay it after one or two firm reminders if the clause is clearly on the original invoice. See <a href="/how-to-follow-up-on-unpaid-invoices/">how to follow up on unpaid invoices</a> for the full sequence.</li>
<li><strong>Take it to small claims court.</strong> An option for unpaid invoices that genuinely matter to your business. The threshold for small claims varies by jurisdiction: in most US states it's between $5,000 and $15,000, with the full range running from about $2,500 in Kentucky up to $25,000 in Tennessee and Delaware, in England and Wales the small claims limit is £10,000, in Australia, state tribunals handle minor civil disputes up to between $10,000 and $100,000 AUD depending on the state. NCAT in New South Wales handles consumer claims up to $100,000 since 2022. VCAT in Victoria has a civil claims jurisdiction up to $100,000 with a $15,000 small-claims sub-tier. QCAT and ACAT sit at $25,000, and WA's Minor Cases division is at the floor of $10,000. Small claims is set up so you don't need a lawyer, filing fees are modest, and judges are used to invoice disputes. For a late fee of $15 or $50, it isn't worth your time. For an unpaid balance of $5,000 plus three months of late fees, it can be.</li>
</ul>
<p>Set expectations honestly. Most disputes over small late fees don't end up in court. They end with the client paying, the small-business owner writing it off, or both sides agreeing to a partial settlement. The point of the fee is to get the original invoice paid on time, and on that score it works most of the time even if the fee itself doesn't always get collected. For deeper guidance on the worst-case path, see <a href="/what-to-do-when-a-client-does-not-pay/">what to do when a client does not pay</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'You can add a late-fee line item in the free invoice generator and resend the invoice in a minute.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'What is a reasonable late fee?',
      'a' => 'The most common range on small-business invoices is 1 to 2% per month on the overdue balance, with 1.5% per month being the typical middle. Annualized, that works out to 18% per year, which lines up with the cap several US states use on consumer debt. For very small invoices, a flat $25 fee is more common than a percentage, since 1.5% of $200 is too small to matter. Whatever rate you pick, check that your jurisdiction doesn\'t cap it lower, and make sure the rate is on the invoice in writing before the bill goes unpaid.',
    ],
    [
      'q' => 'Can I add a late fee retroactively?',
      'a' => 'Not really. If the client never agreed to a late fee in writing, either in the contract or on the original invoice they received before the work was done, adding one after the fact is hard to enforce and feels unfair to most clients. The clean path is to send the next batch of invoices with a clear late-fee clause in the Terms section, so it applies going forward. For the current overdue invoice, you can ask politely for a settlement amount that includes a small fee, but you can\'t demand it as a right if it was never disclosed.',
    ],
    [
      'q' => 'Do I need to tell the client about the late fee in advance?',
      'a' => 'Yes. The late-fee clause has to be visible to the client before the invoice goes unpaid, either in a signed contract or in the Terms section of the original invoice. The day-after-due-date late fee that appears for the first time on a follow-up invoice is on shaky ground in most places, because the client never had the chance to agree to it or pay before the rate started accruing. Put the clause on every invoice from the start, in plain language, and the fee holds up.',
    ],
    [
      'q' => 'Is a flat fee or a percentage better?',
      'a' => 'It depends on the size of the invoice. A flat fee, around $25, works better on small invoices where a percentage rate would be too small to change behavior. A percentage rate, around 1.5% per month, works better on larger invoices where a flat fee would feel trivial against a $5,000 balance. The cleanest answer is to use both: a flat fee on invoices under $500, and a percentage on anything above. Write the rule into the Terms section of every invoice so the client sees exactly what applies to their bill.',
    ],
    [
      'q' => 'Do I charge late fees on overdue late fees?',
      'a' => 'Most small-business owners do not, and the math does not really justify it on small balances. If your late-fee clause uses simple monthly interest, the fee is calculated once per month on the original overdue invoice amount, and the previous fees do not get re-charged. Compounding, where each month the fee is calculated on the new total including previous fees, is allowed in some jurisdictions but adds very little money and makes the calculation harder to explain. Pick simple monthly unless you have a specific reason not to, and keep the clause easy to read.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'plumber',
    'electrician',
  ],

  'related_article_slugs' => [
    'what-to-do-when-a-client-does-not-pay',
    'how-to-follow-up-on-unpaid-invoices',
    'net-30-vs-due-on-receipt',
  ],
];
