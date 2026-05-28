<?php
// articles/data/net-30-vs-due-on-receipt.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'net-30-vs-due-on-receipt',

  'h1' => 'Net 30 vs Due on Receipt: which to use',

  'meta_title' => 'Net 30 vs Due on Receipt: Which to Use | Argo Books',

  'meta_description' => 'Net 30 vs Due on Receipt explained in plain language. When to use each, how to write the term on the invoice, and what to set as your default.',

  'schema_type' => 'Article',

  'published' => '2026-05-30',
  'updated'   => '2026-05-30',

  'reading_time_min' => 7,

  'intro_html' => <<<'HTML'
<p>Payment terms are the two or three words at the bottom of an invoice that decide when you actually get paid. Net 30 and Due on Receipt are the two most common, and the choice between them matters more than people think. Pick the wrong one and you either annoy a good client or wait a month for money you needed last week.</p>
<p>This guide walks through what each term actually means, when to use which, and how to set a default you don't have to think about again. The goal is simple: get paid on time without making the client feel chased. You'll see real numbers, sample wording you can copy into your own invoice, and the small variations like Net 30 EOM and 2/10 Net 30 that show up on bigger invoices.</p>
HTML,

  'sections' => [

    // 0
    [
      'h2' => 'What Net 30 actually means',
      'anchor' => 'what-net-30-means',
      'html' => <<<'HTML'
<p>Net 30 means the invoice is due 30 days after the invoice date. If you issue an invoice on June 1, the payment is due on July 1. The word "net" just means the full amount with nothing taken off, so Net 30 is the full balance, no discount, due in 30 days.</p>
<p>Most US and UK businesses count from the invoice date, not the date the client opened the email or signed for the package. That's the standard, and it's what accounts payable teams assume unless your invoice says otherwise. If you want to count from a different starting point, say so in writing. Something like "Payment due 30 days from receipt of invoice" makes the rule explicit and removes the back-and-forth.</p>
<p>There's one common variation worth knowing: Net 30 EOM, which stands for end of month. That means the 30-day clock doesn't start the day you send the invoice, it starts on the last day of the month the invoice was issued. So an invoice sent on June 5 with Net 30 EOM is due 30 days after June 30, which is July 30. Bigger clients with monthly accounts payable runs sometimes ask for this. It buys them up to a full extra month, so don't agree to it lightly on a small invoice.</p>
<p>A few practical notes. Net 30 is calendar days, not business days, unless the contract says otherwise. Weekends and bank holidays count. If the due date lands on a Sunday, most clients will pay the Monday after and that's fine. And Net 30 doesn't require the client to wait 30 days, it just means they have up to 30 days. Plenty of well-run finance teams pay on day 7 or day 14 if the invoice is clean and the bank details are clear.</p>
HTML,
    ],

    // 1
    [
      'h2' => 'What Due on Receipt actually means',
      'anchor' => 'what-due-on-receipt-means',
      'html' => <<<'HTML'
<p>Due on Receipt means the invoice is payable as soon as the client gets it. In theory, that's the moment the email lands. In practice, almost nobody pays inside the hour. A reasonable client reads Due on Receipt as "pay this within a few business days", and that's usually how it shakes out.</p>
<p>The term has a real use case: it signals urgency without committing to a specific number. It tells the client this isn't something to file under "review at month end". It's meant to be paid now. For a one-off job, a small invoice from a new client, or a final bill on a job that already had a deposit, Due on Receipt is the polite way of saying don't sit on this.</p>
<p>The honesty piece matters. If you genuinely need the money within 24 hours, say that. Write "Payment due within 24 hours of receipt" or "Payment due on or before [specific date]". Due on Receipt without a date can read as vague, and vague terms get paid last. Clients pay invoices with hard dates faster than invoices with soft language, every time.</p>
<p>Where Due on Receipt can irritate clients is on bigger invoices to established companies. A $12,000 invoice sent Due on Receipt to a company with a normal twice-monthly check run is going to bounce internally. The finance person will look at the term, look at their schedule, and pay it on the next normal run anyway. So all you've done is mark yourself as a vendor who doesn't understand how their payment cycle works. For invoices over a few thousand dollars to companies that have actual accounts payable, Net 30 reads as professional and Due on Receipt reads as pushy.</p>
HTML,
    ],

    // 2
    [
      'h2' => 'When to use Net 30',
      'anchor' => 'when-to-use-net-30',
      'html' => <<<'HTML'
<p>Net 30 is the right call when you're billing a bigger client, an agency, a company with an accounts payable team, or anyone whose finance process isn't a single person paying bills from their phone. These clients run on cycles. They have a weekly or bi-weekly check run, a payment approval queue, and a system that pays invoices based on the date written on them.</p>
<p>If you put Due on Receipt on an invoice to a company like that, the invoice still gets paid on their next cycle. You don't get paid any faster. All you do is signal that you don't know how they work. Net 30 lines up with their process, gets your invoice into their queue without friction, and gets you paid on a predictable schedule.</p>
<p>There's also the relationship piece. Bigger clients value vendors who feel easy to work with. A vendor who insists on Due on Receipt on a $20,000 project, then chases the invoice on day 3, comes across as anxious. A vendor who sends Net 30, gets paid on day 28, and just moves on, comes across as someone with their act together. The second vendor gets called again. The first one doesn't.</p>
<p>Use Net 30 when:</p>
<ul>
  <li>The client is a company with a finance team, not a single owner-operator.</li>
  <li>The invoice is over a couple of thousand dollars.</li>
  <li>You have a written agreement or recurring relationship in place.</li>
  <li>The client specifically asked for Net 30 (more common than you'd think).</li>
  <li>You're happy to wait up to 30 days for the money without it hurting cash flow.</li>
</ul>
<p>If a 30-day wait would put you in a tight spot, that's a cash-flow problem to solve another way: a deposit, milestone billing, or a smaller buffer in your account. Trying to fight Net 30 on a client whose whole company runs on it usually ends with you switching the term to Net 30 anyway, just two emails later.</p>
HTML,
    ],

    // 3
    [
      'h2' => 'When to use Due on Receipt',
      'anchor' => 'when-to-use-due-on-receipt',
      'html' => <<<'HTML'
<p>Due on Receipt is the right call when the client is new, the job is small, and the trust between you hasn't been earned yet. It's also the right call for one-off work that doesn't have a recurring relationship behind it, where waiting a month for the money is a real risk and not a process step.</p>
<p>Think about a first job for a brand-new client. You have no payment history, no contract you've already invoiced against, no idea whether they pay on time or chase you for a month. Due on Receipt protects you. It says the work is done, the bill is due, and we close this out before either of us moves on. If they don't pay, you learned something important before doing more work.</p>
<p>Same logic applies to small invoices. A $250 invoice for a quick design tweak doesn't need a 30-day window. Most individual customers and small business owners will pay it the day they see it, because it's small enough to clear without a process. Putting Net 30 on a $250 invoice tells them they have a month to think about it, and a month later they've forgotten.</p>
<p>Use Due on Receipt when:</p>
<ul>
  <li>It's the first invoice with a new client and you have no payment history.</li>
  <li>The invoice is small enough that the client can pay it from their phone in a minute.</li>
  <li>The job is a one-off with no future work depending on it.</li>
  <li>The client is an individual or sole owner, not a finance department.</li>
  <li>You've already collected a deposit and this is the final small balance.</li>
</ul>
<p>One more thing. Due on Receipt doesn't give you the right to chase the client on day 2. A polite reminder is fair on day 5 or day 7. Anything sooner reads as pushy, even when the term technically supports it. The goal is to get paid and keep the door open for next time.</p>
HTML,
    ],

    // 4
    [
      'h2' => 'Set your default terms once',
      'anchor' => 'set-your-default-terms-once',
      'html' => <<<'HTML'
<p>Most small businesses bounce between Net 30 and Due on Receipt invoice by invoice, and the result is messy. Some clients see one term, some see another, and you spend time picking the wording every single time you send a bill. Pick a default. Stick to it. Only vary when a specific client asks.</p>
<p>For most freelancers, consultants, and small service businesses, Net 14 or Net 15 is a sensible default. It's long enough to feel professional to a finance team, short enough that you don't wait a full month for cash, and it's right in the middle of what clients expect. If most of your work is for bigger established companies, default to Net 30 instead, because that's what they'll pay on anyway.</p>
<p>If you mostly do small jobs for individuals and small business owners, Due on Receipt as a default makes sense. The invoice gets paid quickly and you don't have to remember which client got what term.</p>
<p>The key is consistency. A client should never get one invoice from you with Net 30 and the next with Due on Receipt unless there's a real reason. Pick a number, write it into your standard invoice template, and only deviate when the client asks. Saves you mental load and keeps your billing predictable.</p>
<p>A practical note: writing "Payment due in 14 days" or "Payment due in 30 days" on every invoice gets old quickly when you're sending them by hand. The fix is to set the wording once in a template, then let the tool fill it in. Examples of clean default wording you can copy:</p>
<ul>
  <li><strong>Net 14:</strong> "Payment due within 14 days of invoice date."</li>
  <li><strong>Net 30:</strong> "Payment due within 30 days of invoice date."</li>
  <li><strong>Due on Receipt:</strong> "Payment due on receipt of invoice."</li>
  <li><strong>Net 30 with late fee:</strong> "Payment due within 30 days of invoice date. A 1.5 percent monthly late fee applies to overdue balances."</li>
</ul>
HTML,
    ],

    // 5
    [
      'h2' => 'Other terms worth knowing',
      'anchor' => 'other-terms-worth-knowing',
      'html' => <<<'HTML'
<p>Net 30 and Due on Receipt are the two you'll use most, but there's a small family of related terms that show up often enough to be worth recognising. Quick tour.</p>
<p><strong>Net 7.</strong> Payment due 7 days from the invoice date. Used when you want fast payment but want to look more professional than Due on Receipt. Common on small ongoing service work, like a weekly cleaner or a freelance editor billing on a tight cycle.</p>
<p><strong>Net 14 (or Net 15).</strong> Payment due 14 or 15 days from the invoice date. The middle ground that a lot of freelancers and small agencies live on. Feels professional, gets the money in within two weeks, and works for most client types.</p>
<p><strong>Net 45.</strong> Payment due 45 days from the invoice date. Less common, usually requested by larger companies with slow internal cycles. Push back if you can, because 45 days is a long time to wait, and most clients will accept Net 30 if you ask.</p>
<p><strong>Net 60.</strong> Payment due 60 days from the invoice date. The standard for some big enterprise buyers, government contracts, and certain industries. If a client asks for Net 60, you either build it into the price, ask for a deposit up front, or both. Two full months of waiting is real money in your cash flow.</p>
<p><strong>2/10 Net 30.</strong> An early-payment discount term that reads "2 percent off if you pay within 10 days, otherwise the full amount is due in 30". On a $5,000 invoice, paying inside 10 days saves the client $100. Some clients chase the discount, some don't. The math works out to roughly a 36 percent annualised return for paying early, which is why bigger finance teams take it. Only offer it if the cash speed-up is worth the 2 percent to you.</p>
<p><strong>EOM (end of month).</strong> Adds onto another term, like Net 30 EOM. The clock starts at the end of the month rather than the invoice date. Common on monthly billing cycles. Be careful with it, because invoicing on the 2nd of the month with Net 30 EOM means you wait almost two months for the money.</p>
HTML,
    ],

    // 6
    [
      'h2' => 'How to write the term on the invoice',
      'anchor' => 'how-to-write-the-term',
      'html' => <<<'HTML'
<p>The cleanest payment term is the one the client can't misread. That means a specific date, in plain English, in a spot on the invoice they can't miss. Most invoices have a Terms section near the bottom or a Payment Terms field near the top, and that's where this line belongs.</p>
<p>Beat jargon with a date. "Payment due by July 1, 2026" reads cleaner than "Net 30" to most people. The number Net 30 is fine to also include for the finance team, but a written date is the version that gets paid first. Some clients will read "Net 30" and have to do the math, and a slow finance person will park your invoice until they get to it.</p>
<p>Good wording you can copy into your Terms section, depending on which term you settled on:</p>
<ul>
  <li>"Payment due by [date]. Net 30 from invoice date."</li>
  <li>"Payment due on receipt of invoice. Please settle within 5 business days."</li>
  <li>"Payment due within 14 days of invoice date. Late payments are subject to a 1.5 percent monthly fee."</li>
  <li>"50 percent deposit on signing, balance due within 15 days of project completion."</li>
</ul>
<p>One thing to avoid: the bare phrase "ASAP". It tells the client nothing, sets no deadline, and gives you no ground to stand on if they pay 60 days later. If you need it fast, set a date.</p>
<p>If you charge late fees, write the rate into the Terms section on every invoice, not just the first one or the ones where the client is already late. Something like "A late fee of 1.5 percent per month applies to overdue balances" is enough. The fee isn't enforceable if it was never disclosed, and putting it on every invoice means you can apply it without a separate conversation.</p>
<p>Last note: include your preferred payment methods near the terms. Bank transfer details, a payment link, a check mailing address. The faster and easier it is for the client to pay, the more often you get paid on time. For more on the full invoice structure, see <a href="/how-to-invoice-clients/">How to invoice clients</a>, and for the late-fee specifics see <a href="/late-fees-when-and-how-to-charge/">Late fees: when and how to charge</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'You can set the payment terms field once in the free invoice generator and it carries to every download.',
  'tool_callout_cta'  => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'Is Net 30 calendar days or business days?',
      'a' => 'Calendar days, unless the contract says otherwise. Weekends and bank holidays count, so an invoice issued on June 1 with Net 30 is due on July 1 even if July 1 lands on a Saturday. If you want a different count, write it into the invoice, for example "Payment due within 30 business days of invoice date". Most US and UK businesses default to calendar days, and accounts payable teams will assume that unless you say otherwise. If the due date falls on a weekend, most clients pay on the next business day and that\'s normal.',
    ],
    [
      'q' => 'Can I switch terms between invoices for the same client?',
      'a' => 'You can, but it\'s usually a bad idea unless something has changed. Inconsistent terms confuse the client and make your billing look less organised. If you\'ve always sent Net 30 and you switch to Due on Receipt on a single invoice, expect questions. The cleaner move is to set a default for that client and stick with it, then renegotiate the standing terms once in writing if you need to change them. The exception is a one-off final invoice on a specific project, where Due on Receipt for that final balance is reasonable and most clients accept it.',
    ],
    [
      'q' => 'Do I need a late fee written down even with Net 30?',
      'a' => 'Yes. The late fee isn\'t enforceable if it was never disclosed. Put the rate in the Terms section on every invoice you send, something like "A 1.5 percent monthly late fee applies to overdue balances". That way, when an invoice goes past due, you can apply the fee without a separate conversation or argument. The fee doesn\'t have to be large to work. The point is that the client knows there\'s a cost to ignoring the invoice, which gets the bill paid faster than no fee at all. Check your local rules, because some places cap how much you can charge.',
    ],
    [
      'q' => 'What does Net 30 EOM mean?',
      'a' => 'Net 30 EOM means payment is due 30 days after the end of the month the invoice was issued. So an invoice dated June 5 with Net 30 EOM is due July 30, not July 5. EOM stands for end of month. Bigger clients with monthly accounts payable cycles sometimes ask for it because it lines up with how they batch payments. Be careful with EOM on small invoices, because you can end up waiting almost two months for the money if you invoice early in the month. If a client asks for Net 30 EOM, consider a deposit or partial up-front payment to cover the gap.',
    ],
    [
      'q' => 'Is Due on Receipt rude?',
      'a' => 'It depends on the client and the size of the invoice. For a small invoice to a new client or an individual customer, Due on Receipt is fine and reads as normal. For a bigger invoice to an established company with a finance team, it can come across as pushy, because their process isn\'t built around paying instantly. A better alternative for those clients is Net 14 with a specific due date written out. That keeps the urgency without fighting their internal cycle. Match the term to the client, not the other way round, and you avoid friction either way.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'designer',
  ],

  'related_article_slugs' => [
    'how-to-invoice-clients',
    'late-fees-when-and-how-to-charge',
    'how-to-follow-up-on-unpaid-invoices',
  ],
];
