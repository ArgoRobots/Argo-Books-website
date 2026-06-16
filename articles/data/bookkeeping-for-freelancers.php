<?php
// articles/data/bookkeeping-for-freelancers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-freelancers',

  'h1' => 'Bookkeeping for freelancers: a practical guide',

  'meta_title' => 'Bookkeeping for Freelancers: a Practical Guide | Argo Books',

  'meta_description' => 'A plain guide to bookkeeping for freelancers: separate accounts, irregular income, chasing invoices, home-office deductions, and setting aside tax.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 12,

  'published' => '2026-06-15',

  'updated' => '2026-06-15',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>When you freelance, you are the whole business. You do the work, send the invoices, chase the payments, and somewhere in there you are supposed to keep books too. Nobody handed you a finance department, and most freelancers learn bookkeeping the hard way: by panicking the week before a tax deadline, staring at a year of bank transactions, trying to remember what half of them were.</p>
<p>It does not have to be like that, and the fix is simple. Freelance bookkeeping comes down to a few habits that fit the way solo work actually runs: income that arrives in uneven lumps, invoices that need chasing, deductions scattered across home and laptop and software subscriptions, and a tax bill that nobody withholds for you. This guide walks through each of those in plain language, so your books stay current with minutes of effort and tax time stops being a yearly scramble.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Separate your money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>This is the highest-value thing on the list and it costs nothing but a little setup. Open a separate bank account for your freelance work, and ideally a separate card. Every client payment comes into it, every business purchase goes out of it, and your personal spending stays on your personal accounts.</p>
<p>Why it matters so much when you are a one-person business: with everything mixed in one account, bookkeeping starts with the worst job there is, deciding which of hundreds of transactions were even work. Your grocery run, your Netflix, and your client payment all sit in the same list. Separate the accounts and that whole sorting step vanishes. The business account statement <em>is</em> your list of freelance income and expenses, so you are no longer untangling, just recording.</p>
<p>You don't need a fancy business account with monthly fees, especially when you are starting out. A second basic account in your name is enough. The discipline is the point: business money in the business account, personal money out of it. Do this one thing and the messiest part of freelance bookkeeping disappears before it starts.</p>
HTML,
    ],

    [
      'h2' => 'Track income when it\'s lumpy',
      'anchor' => 'track-income',
      'html' => <<<'HTML'
<p>Freelance income does not arrive in tidy monthly paycheques. It comes in lumps: a big project lands, then three quiet weeks, then two invoices pay at once. That unevenness makes two things easy to get wrong, so both need a habit.</p>
<ul>
<li><strong>Record income against the work, not just the deposit.</strong> When a payment lands, note which client and which invoice it was for. Months later, a row that just says "deposit $1,400" tells you nothing, but "Invoice 31, client John Doe" tells you everything. If you send invoices through a tool, this links up on its own. If you don't, jot it down when the money arrives, while you still know.</li>
<li><strong>Watch cash flow across the gaps.</strong> A profitable freelancer can still run short in a slow month, because the income is lumpy and the bills are not. Knowing what is actually in the business account, separate from the tax you owe and the personal money you have not drawn yet, is what keeps a quiet month from becoming a crisis. Keeping the books current is what makes that number real instead of a guess.</li>
</ul>
<p>You don't need anything elaborate to track lumpy income. A separate account plus a record of which payment came from which invoice covers it. The point is that when you look, you see clearly: this is what came in, this is who it came from, this is what is left after tax. That clarity is worth more to a freelancer than any chart.</p>
HTML,
    ],

    [
      'h2' => 'Chase invoices before they go cold',
      'anchor' => 'chase-invoices',
      'html' => <<<'HTML'
<p>An invoice you sent is not money you have. For freelancers, late payment is one of the biggest real-world problems, because you don't have a credit department and chasing feels awkward when it's your own name on the email. Bookkeeping is what makes chasing easy, because it tells you exactly who owes what and how late they are.</p>
<p>Keep a simple list of what you have invoiced and what has been paid, so the unpaid ones are visible at a glance instead of slipping your mind. The moment an invoice passes its due date, send a short, polite reminder. There is nothing rude about it; you did the work, payment is due, and a calm reminder the day after the due date is normal business, not nagging. Most late payments are not refusals, they are an invoice that got buried in someone's inbox, and a nudge fixes them.</p>
<p>A few things that get you paid faster, all of which start with good record-keeping: put clear payment terms and a due date on every invoice, number your invoices so both sides can refer to them, and send the invoice promptly rather than batching them up at month-end. The guide on <a href="/how-to-invoice-clients/">how to invoice clients</a> covers the details. The bookkeeping job here is small but vital: know what is outstanding, and act on it before it goes cold, because the older an unpaid invoice gets, the harder it's to collect.</p>
HTML,
    ],

    [
      'h2' => 'Claim the deductions freelancers miss',
      'anchor' => 'deductions',
      'html' => <<<'HTML'
<p>Freelancers routinely overpay tax, not because they cheat, but because they never recorded the costs they were entitled to deduct. Every business expense you fail to capture is a deduction you don't claim, which is money handed to the tax office for no reason. These are the ones solo workers miss most:</p>
<ul>
<li><strong>Home office.</strong> If you work from home, the business share of your home costs is usually deductible: a portion of rent or mortgage interest, utilities, and internet, based on the share of your home used for work. The exact method varies by country, so check your local rules, but the deduction is real and often sizeable. You need the household bills on record to claim it.</li>
<li><strong>Equipment.</strong> The laptop, monitor, phone, camera, desk, and chair you bought to do the work are business costs. Larger items may need to be claimed over several years rather than all at once depending on your country's rules, so keep the receipts and note what each was.</li>
<li><strong>Software and subscriptions.</strong> The design apps, the cloud storage, the website hosting, the professional memberships. These recurring digital costs add up to a real number over a year, and they are easy to forget because they leave the account quietly each month.</li>
<li><strong>Phone and internet.</strong> The business share of your phone and connection is deductible. For mixed-use items, you claim the work percentage, so a sensible estimate of how much you use them for work matters.</li>
<li><strong>Professional costs.</strong> Accountant or bookkeeper fees, business insurance, courses that improve your skills, and the fees the payment platforms take out of your client payments are all deductible costs.</li>
</ul>
<p>The theme is the same for all of them: the deduction is only as good as the record. Capture each cost when it happens, with the receipt, and the claim is solid. Try to rebuild a year of subscriptions and home-office bills the night before the deadline and you will undercount, which means paying extra tax you did not owe. A <a href="/best-free-ai-receipt-scanner/">receipt-scanning app</a> that reads and files receipts as you get them is the easiest way to make sure nothing slips through.</p>
HTML,
    ],

    [
      'h2' => 'Set aside tax as you get paid',
      'anchor' => 'set-aside-tax',
      'html' => <<<'HTML'
<p>This is the one that catches new freelancers hardest. When you had a job, your employer withheld tax from every paycheque, so the tax bill was handled before you ever saw the money. When you freelance, nobody does that. The full payment lands in your account, it feels like all yours, and then the tax bill arrives later for an amount you did not keep aside.</p>
<p>The fix is a habit, not a calculation: every time a client pays you, move a percentage straight into a separate tax-savings account, and don't touch it. Treat the tax money as never having been yours. When the bill comes, the money is already there, and what would have been a panic is a quiet transfer. Many freelancers also owe tax in instalments through the year rather than once at the end, so check whether your country expects quarterly or periodic payments, because missing those can mean penalties on top of the bill.</p>
<p>What percentage to set aside depends on your income, your country, and your other circumstances, including self-employment or social-security contributions that freelancers often owe on top of income tax. This is exactly the kind of thing worth a short conversation with an accountant early on, so you save the right amount instead of guessing. The principle, though, is universal and the single best money habit a freelancer can build: tax money goes into the tax account the moment you are paid, every time, no exceptions.</p>
HTML,
    ],

    [
      'h2' => 'Build a simple monthly habit',
      'anchor' => 'monthly-habit',
      'html' => <<<'HTML'
<p>Everything above works because it's kept current, and keeping it current is a small monthly routine, not a yearly marathon. If you separate your accounts, capture costs as they happen, and set tax aside on payment, your monthly check is short:</p>
<ol>
<li><strong>Check your records against the bank.</strong> Run down the month's business transactions and make sure each one is recorded and categorized. Catching a missing receipt or a wrong category in the month it happened takes seconds; finding it ten months later takes an evening you don't have.</li>
<li><strong>Look at what is outstanding.</strong> Glance at your unpaid invoices and send reminders on anything past due. This is when late payments turn back into paid ones.</li>
<li><strong>Confirm the tax money moved.</strong> Make sure the percentage from each payment actually went into the tax account. It's easy to skip in a busy week, and the whole point is that it never gets skipped.</li>
<li><strong>Note how the month went.</strong> A quick look at what came in versus what went out tells you whether the business is healthy, which for a freelancer is the difference between steering and hoping.</li>
</ol>
<p>That is the whole job: a few minutes once a month on top of capturing things as they happen. The freelancers who dread tax season are nearly always the ones who left it all for the end. The ones who spend a few minutes a month barely notice it, and they walk into tax time with clean numbers, every deduction claimed, and the tax money already saved. You don't need to like bookkeeping to get there. You just need the habit.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books scans receipts, tracks which invoices are unpaid, and keeps your freelance books current with minutes of effort a month.',
  'tool_callout_cta' => 'Download Argo Books free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'Do I need accounting software as a freelancer, or is a spreadsheet enough?',
      'a' => 'A spreadsheet is genuinely enough when you are starting out and your volume is low. A separate bank account, a simple sheet for income and expenses, and a folder of receipt photos is a complete, valid system, and plenty of freelancers run that way for years. Software earns its place as things grow: lots of invoices and receipts, several subscriptions, deductions to track, and a tax return getting complicated are where manual entry becomes the bottleneck and a tool that scans receipts and tracks unpaid invoices saves real time. Start with whatever you will actually keep up with. A simple system used every week beats a powerful one you ignore.',
    ],
    [
      'q' => 'How much should I set aside for tax?',
      'a' => 'It depends on your income level, your country, and whether you owe self-employment or social-security contributions on top of income tax, so there is no single right number and anyone who gives you one without knowing your situation is guessing. A common rough starting point that many freelancers use is somewhere between a quarter and a third of each payment, held in a separate account, but you should treat that as a placeholder until an accountant tells you the right figure for you. The habit matters more than the exact percentage: move tax money out of reach the moment you are paid, every time, and adjust the amount once you know your real rate. Saving slightly too much is far better than saving too little.',
    ],
    [
      'q' => 'A client has not paid and it\'s getting awkward to chase. What do I do?',
      'a' => 'Chase anyway, and chase early, because the awkwardness only grows with the delay. The day after an invoice passes its due date, send a short, polite reminder that simply restates the invoice number, the amount, and the due date. There is nothing rude about it; you did the work and payment is due. Most late payments are an invoice buried in an inbox, not a refusal, and a calm nudge resolves them. If reminders go unanswered, follow up with a firmer note referencing your payment terms, and for larger amounts that stay unpaid, your options escalate to a formal demand. The bookkeeping habit that makes this easy is keeping a clear list of what is outstanding, so nothing slips your mind until it\'s too cold to collect.',
    ],
    [
      'q' => 'Can I deduct my home office if I rent and only use a corner of my place?',
      'a' => 'Usually yes, though the method and limits depend on your country, so check your local rules or ask an accountant. In most systems you can claim the business share of your home costs based on the portion of your home used for work, whether you rent or own, and a dedicated corner of a room can count as long as it\'s genuinely used for the business. Renters typically claim a share of rent, utilities, and internet rather than mortgage interest. Some countries also offer a simplified flat-rate method that skips the detailed split. Either way you need your household bills on record to back the claim, which is one more reason to capture them as they come in.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned, and yes, this is the Argo Books site, so read it with that in mind. But the advice here does not depend on our tool. Separating your accounts, tracking lumpy income, chasing invoices, capturing deductions, and setting tax aside on payment are habits that work with a spreadsheet, a notebook, a free app, or a competitor\'s software. The guide says plainly that a spreadsheet is fine when you are starting out. If you take only the habits and never look at Argo Books, the guide did its job. We would rather you keep clean books and claim every deduction you are owed than buy software you don\'t need yet.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'designer',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'small-business-bookkeeping-basics',
    'how-to-track-business-expenses-without-spreadsheets',
  ],
];
