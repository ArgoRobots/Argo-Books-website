<?php
// articles/data/how-to-take-a-deposit-on-an-invoice.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-take-a-deposit-on-an-invoice',

  'h1' => 'How to take a deposit on an invoice',

  'meta_title' => 'How to Take a Deposit on an Invoice | Argo Books',

  'meta_description' => 'How to take a deposit on an invoice: how much to ask for, how to word it, two ways to invoice it, and when tax applies. You will get paid before you start.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 80,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 8,

  'total_time_iso8601' => 'PT20M',

  'intro_html' => <<<'HTML'
<p>A deposit is money the client pays before you start, so you're not funding their job out of your own pocket. It's the most reliable way to get paid on time, because the hardest payment to collect is the one where the work is already done and the client has what they wanted. Ask for the money up front and the whole relationship starts on a firmer footing.</p>
<p>This guide covers the full flow: why a deposit is worth asking for, how much to charge, how to word it so there's no argument later, and the two ways to actually put it on an invoice. It also covers the receipt you owe the client, and the tricky question of when tax applies. None of it needs an accounting background, and by the end you'll have a clear plan for taking a deposit on your next job.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why take a deposit at all',
      'anchor' => 'why-take-a-deposit',
      'step_name' => 'Decide whether to take a deposit',
      'step_text' => 'Take a deposit when the job is large, runs over time, or needs you to buy materials up front. It protects your cash flow and confirms the client is serious.',
      'html' => <<<'HTML'
<p>A deposit does three jobs at once, and each one is worth money to you.</p>
<ul>
<li><strong>It protects your cash flow.</strong> If you buy $2,000 of tile before you set foot on a job, that $2,000 is out of your account until the client pays. A deposit means the client funds the materials, not you. You're never lending money to a customer without meaning to.</li>
<li><strong>It confirms the client is serious.</strong> A client who won't pay a deposit is telling you something. People who put money down show up for the appointment, answer the phone, and pay the balance. A deposit filters out the tyre-kickers before you've spent a day on them.</li>
<li><strong>It covers materials and slots you book.</strong> A photographer who blocks a Saturday for a wedding has turned down every other booking for that day. The deposit pays for the slot whether the couple shows up or not. A contractor who orders custom cabinets can't return them if the client walks away.</li>
</ul>
<p>The rule of thumb: the bigger the job, the longer it runs, or the more you have to spend before you get paid, the stronger the case for a deposit. A quick one-hour call rarely needs one. A three-week renovation almost always does.</p>
{{illustration:coins}}
HTML,
    ],

    [
      'h2' => 'How much to ask for',
      'anchor' => 'how-much',
      'step_name' => 'Set the deposit amount',
      'step_text' => 'A deposit of 25 to 50 percent is standard. For bigger jobs, split the total into milestone payments such as 50/25/25 or 30/30/30/10.',
      'html' => <<<'HTML'
<p>For most small jobs, a deposit of <strong>25% to 50%</strong> of the total is normal and nobody blinks at it. Where you land inside that range depends on how much you have to spend before you start:</p>
<ul>
<li><strong>25% to 33%</strong> when the job is mostly your labour and you buy little up front. A consultant, a designer, or a tutor booking a block of sessions.</li>
<li><strong>50%</strong> when materials are a big part of the cost, or the slot is hard to re-book. A plumber replacing a hot water system, a photographer holding a wedding date, an electrician wiring a new extension.</li>
</ul>
<p>For a bigger project, one deposit isn't the right shape. Split the total into <strong>milestone payments</strong> so money comes in as the work moves, and neither side is ever carrying too much risk:</p>
<ul>
<li><strong>50 / 25 / 25.</strong> Half up front, a quarter at the midpoint, the last quarter on completion. A clean default for a job of a few thousand dollars.</li>
<li><strong>30 / 30 / 30 / 10.</strong> Common in construction and renovation. Three even payments as the work progresses, with the final 10% held until the client signs off on the finished job. That last slice gives them a reason to do the final walk-through and gives you a reason to finish clean.</li>
</ul>
<p>A worked example. Say a kitchen job is $8,000. On a 50/25/25 split, the client pays $4,000 before you order anything, $2,000 when the units are installed, and $2,000 when the last handle is on and they're happy. You're never more than a milestone out of pocket, and they never pay for work they haven't seen.</p>
HTML,
    ],

    [
      'h2' => 'Agree it and word it in writing first',
      'anchor' => 'put-it-in-writing',
      'step_name' => 'Agree the terms in writing',
      'step_text' => 'Before any work starts, write down the deposit amount, what it covers, and whether it is refundable. Get the client to confirm in writing, even a short email.',
      'html' => <<<'HTML'
<p>The deposit has to be agreed in writing before work starts, not sprung on the client after. It doesn't need a lawyer or a fancy contract. A short paragraph in an email, confirmed with a "yes, that works" reply, is enough to save you a painful conversation later.</p>
<p>Three things go in writing:</p>
<ul>
<li><strong>The amount and when it's due.</strong> "A 50% deposit of $4,000 is due before we order materials. The balance is due on completion." Now there's no confusion about what starts the clock.</li>
<li><strong>What the deposit covers.</strong> Spell out that the deposit goes toward the total, not on top of it. Clients sometimes fear a deposit is an extra fee. It isn't, and saying so removes the friction.</li>
<li><strong>Whether it's refundable.</strong> This is the line that prevents the worst argument. Decide up front and write it down.</li>
</ul>
<p>On that last point, be clear with yourself about which kind of deposit you're taking:</p>
<ul>
<li><strong>Refundable.</strong> The client gets it back if they cancel before you've spent anything or booked out time. Fair when you haven't yet committed money or a slot.</li>
<li><strong>Non-refundable.</strong> The client doesn't get it back, because the moment they paid it you turned down other work or bought materials you can't return. If you're going to make a deposit non-refundable, say so plainly in the written agreement. A deposit the client didn't know was non-refundable is the kind of dispute that ends up as a bad review.</li>
</ul>
<p>A common middle path: the deposit is refundable up to a cut-off (say, seven days before the booked date) and non-refundable after that, because past that point you can no longer re-book the slot. Whatever you choose, the client should have read it and agreed to it before they pay a cent.</p>
HTML,
    ],

    [
      'h2' => 'Two ways to put it on an invoice',
      'anchor' => 'how-to-invoice-it',
      'step_name' => 'Invoice the deposit',
      'step_text' => 'Either send a deposit invoice now and a balance invoice later, or send one invoice and enter the deposit as an amount paid so the balance due updates.',
      'html' => <<<'HTML'
<p>There are two clean ways to handle the paperwork. Both are correct. Pick the one that matches how you like to work.</p>
{{illustration:invoice-doc}}
<h3>Option A: a deposit invoice now, a balance invoice later</h3>
<p>Send one invoice for the deposit only, then a second invoice for the balance when the work is done. On the $8,000 kitchen with a 50% deposit:</p>
<ul>
<li><strong>Invoice 1</strong> goes out today. One line: "Deposit, kitchen installation (50% of $8,000)". Total: $4,000. Due before materials are ordered.</li>
<li><strong>Invoice 2</strong> goes out on completion. It shows the full $8,000 job, then the $4,000 deposit already paid, leaving a balance due of $4,000.</li>
</ul>
<p>This works well when the deposit and the final bill are weeks or months apart, or when the client's accounts-payable system wants a separate invoice for each payment. Each invoice is a clean record on its own.</p>
<h3>Option B: one invoice, deposit entered as an amount paid</h3>
<p>Raise the full invoice for the whole job, then record the deposit against it as a payment. The invoice shows the total, the amount paid, and a balance due that drops as money comes in. On the same job:</p>
<ul>
<li>Invoice total: <strong>$8,000</strong>.</li>
<li>Deposit received: <strong>$4,000</strong> entered as an amount paid.</li>
<li>Balance due: <strong>$4,000</strong>, updated automatically.</li>
</ul>
<p>This keeps everything on a single document, which is easier to track and easier for the client to follow. It's the natural fit for milestone billing, because each milestone payment lands against the same invoice and the balance due walks down to zero. It's the approach most accounting apps are built around, and it's the one I'd reach for on most jobs.</p>
HTML,
    ],

    [
      'h2' => 'Give a receipt for the deposit',
      'anchor' => 'give-a-receipt',
      'step_name' => 'Send a receipt for the deposit',
      'step_text' => 'When the deposit lands, send the client a receipt showing the amount paid, the date, and the balance still owing. It confirms the money arrived and keeps your records clean.',
      'html' => <<<'HTML'
<p>The moment the deposit hits your account, send the client a receipt. It's a small courtesy that heads off two problems at once: the client wondering whether the money arrived, and either side losing track of what's still owed.</p>
<p>A deposit receipt shows:</p>
<ul>
<li>The amount paid and the date it was received.</li>
<li>What it was for, tied to the invoice or job (for example, "Deposit against invoice 2026-0042").</li>
<li>The <strong>balance still owing</strong>, so both sides are looking at the same number.</li>
</ul>
<p>If you're using Option B above, the receipt more or less writes itself: it's the invoice reprinted with the deposit now showing as paid and the balance due updated. If you're using Option A, the receipt is your confirmation that the deposit invoice is settled, ahead of the balance invoice you'll send later. Either way, keep a copy for your own records. A deposit is income the day it arrives, and you'll want the proof at tax time.</p>
HTML,
    ],

    [
      'h2' => 'When tax applies to a deposit',
      'anchor' => 'tax-on-deposits',
      'step_name' => 'Work out whether tax applies',
      'step_text' => 'Whether sales tax, GST, VAT, or HST applies to a deposit depends on your country and the type of deposit. The rules vary, so check with an accountant for your situation.',
      'html' => <<<'HTML'
<p>This is the part where the honest answer is "it depends, and check with an accountant for your situation." Whether tax is due on a deposit, and exactly when, varies by country and by the kind of deposit you're taking. Here's the general shape in four places:</p>
<ul>
<li><strong>United States.</strong> Sales tax rules are set state by state and vary a lot. In many states a deposit toward a taxable sale becomes taxable when the sale is taxable, but the timing and the treatment of forfeited deposits differ. Check your state revenue site.</li>
<li><strong>Canada.</strong> A true deposit, one that's refundable and just secures the deal, generally isn't subject to GST/HST until it's applied to the sale. Once it counts as a payment toward the goods or services, tax usually follows. A forfeited deposit can be treated differently again.</li>
<li><strong>United Kingdom.</strong> For VAT, the tax point is often the earlier of the invoice date or the date you receive payment, so taking a deposit can trigger VAT on that amount at that moment. Refundable security deposits are usually outside the scope of VAT unless they're kept.</li>
<li><strong>Australia.</strong> A security deposit generally isn't subject to GST when you take it, but it can become taxable when it's forfeited or applied as part of the payment for the supply. The timing depends on the type of deposit.</li>
</ul>
<div class="info-box"><strong>The safe habit:</strong> keep the deposit tied to the invoice it belongs to, so when tax does become due you can see exactly what was paid and when. Don't guess on the tax treatment of a deposit. The rules move around, and an accountant will sort out your specific case in ten minutes.</div>
HTML,
    ],

    [
      'h2' => 'Track the deposit and the balance in one place',
      'anchor' => 'track-the-balance',
      'step_name' => 'Track the balance to paid',
      'step_text' => 'Record the deposit and any milestone payments against the invoice so the balance due and the invoice status always reflect what is actually owed.',
      'html' => <<<'HTML'
<p>A deposit only helps if you can see, at a glance, what's been paid and what's still owed. On one job that's easy. Across ten jobs with different deposits and milestones, it's the kind of thing a spreadsheet quietly starts dropping.</p>
{{illustration:calendar-due}}
<p>This is where <a href="/features/invoicing/">Argo Books</a> earns its place. The app is built around exactly this flow:</p>
<ul>
<li><strong>Deposits and partial payments.</strong> Record the deposit against the invoice and every milestone payment after it. The balance due updates itself, so the number on screen is always what the client actually owes.</li>
<li><strong>Security-deposit line rows.</strong> When a deposit is a distinct item rather than a payment toward the job, you can add it as its own line on the invoice, and it's tracked separately right through to any refund.</li>
<li><strong>Invoice status that keeps up.</strong> An invoice moves to <strong>Partial</strong> once a deposit is paid, then to <strong>Paid</strong> when the balance clears. You can see which jobs are half-paid and which are done without opening each one.</li>
</ul>
<p>Because the app records what's actually been paid, the outstanding balance across every open invoice is right there without you adding it up. That's the whole point of taking a deposit: to be paid as you go, and to always know where you stand. When the last milestone lands, the invoice reads Paid and the job is closed out with a clean record behind it.</p>
<p>New to invoicing in general? Start with <a href="/how-to-invoice-clients/">how to invoice clients</a>, then come back and layer deposits on top.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books tracks deposits, partial payments, and the balance due on every invoice, so the status always matches what is really owed.',
  'tool_callout_cta' => 'See how invoicing works',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'How much deposit should I ask for?',
      'a' => 'For most small jobs, 25% to 50% of the total is normal and clients expect it. Lean toward the lower end when the work is mostly your time and you spend little up front, and toward 50% when materials are a big part of the cost or you\'re booking out a slot you can\'t easily re-book. For bigger projects, don\'t take one large deposit. Split the total into milestone payments such as 50/25/25, or 30/30/30/10 for construction and renovation work, so money comes in as the job moves and neither side carries too much risk.',
    ],
    [
      'q' => 'Is a deposit refundable?',
      'a' => 'That\'s entirely up to what you agree in writing before the work starts. A refundable deposit is returned if the client cancels before you\'ve spent money or booked out time. A non-refundable deposit is kept, because taking it meant you turned down other work or bought materials you can\'t return. Many people use a middle path: refundable up to a cut-off date, then non-refundable after that, because past that point the slot can\'t be re-booked. Whatever you choose, put it in writing and get the client to confirm it before they pay. A deposit the client didn\'t know was non-refundable is the kind of dispute that ends up as a bad review.',
    ],
    [
      'q' => 'How do I invoice the remaining balance?',
      'a' => 'Two ways, both correct. You can send a separate balance invoice when the work is done, showing the full job total, the deposit already paid, and the balance still owing. Or you can raise the full invoice up front and record the deposit against it as an amount paid, so the balance due drops automatically as each payment lands. The second approach keeps everything on one document and is the natural fit for milestone billing, because each payment lands against the same invoice and the balance walks down to zero.',
    ],
    [
      'q' => 'Do I pay tax on a deposit?',
      'a' => 'It depends on your country and the type of deposit, so check with an accountant for your situation. In general, a true refundable security deposit often isn\'t taxed until it\'s applied to the sale or forfeited, while a deposit that counts as a payment toward the goods or services can trigger sales tax, GST, VAT, or HST at the moment you receive it. In the UK, for example, taking a deposit can create a VAT tax point on that amount straight away. The rules vary a lot, so the safe habit is to keep the deposit tied to the invoice it belongs to and confirm the treatment with your accountant.',
    ],
    [
      'q' => 'What is the difference between a deposit and a retainer?',
      'a' => 'A deposit is a payment toward one specific job, and it\'s applied to the total for that job. A retainer is money paid to reserve your availability over a period of time, often on a recurring basis, and it\'s common for ongoing work like consulting, legal advice, or a monthly design arrangement. A retainer can be paid in advance and drawn down as you do the work, or it can simply hold a spot in your schedule. In short: a deposit secures a single job, a retainer secures your time. If you bill the same client on a repeating basis, see the guide on recurring invoices for how to set that up.',
    ],
    [
      'q' => 'When should I take a deposit and when should I skip it?',
      'a' => 'Take a deposit when the job is large, runs over days or weeks, or needs you to buy materials or book out time before you get paid. That covers most contracting, renovation, photography, and custom work. Skip it for quick, low-cost jobs where you\'re not out of pocket before payment, such as a one-hour consultation or a small repair you can invoice on the spot. The core question is simple: am I spending my own money or turning down other work before this client pays me? If yes, take a deposit.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'photographer',
    'plumber',
    'electrician',
  ],

  'related_article_slugs' => [
    'how-to-invoice-clients',
    'recurring-invoices-when-to-use-them',
    'net-30-vs-due-on-receipt',
    'what-to-do-when-a-client-does-not-pay',
  ],
];
