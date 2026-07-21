<?php
// articles/data/how-to-write-a-receipt.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-write-a-receipt',

  'h1' => 'How to write a receipt for a small business',

  'meta_title' => 'How to Write a Receipt for a Small Business | Argo Books',

  'meta_description' => 'How to write a receipt that holds up: what it must include, which format to use, and why cash jobs still need one. Plus a copy-and-paste layout.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 90,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 7,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A receipt is proof that a payment happened. The customer walks away knowing they've paid in full, and you walk away with a record you can point to if there's ever a question. It's a small document, but a sloppy one causes real headaches: a customer who thinks they still owe you, a tax return you can't back up, a dispute you can't win because there's no paper.</p>
<p>The good news is that a receipt only has to do one job, and doing it well takes about a minute. This guide covers exactly what a receipt has to include, the three formats worth using and when each fits, why even a small cash job deserves one, and how to hand out a copy while keeping your own. There's a copy-and-paste layout at the end you can start using today.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What a receipt has to include',
      'anchor' => 'what-to-include',
      'step_name' => 'Put the required details on the receipt',
      'step_text' => 'Include your business name and contact, the date, a unique receipt number, a description of what was bought, the amount, the payment method, any tax shown separately, and the word Paid.',
      'html' => <<<'HTML'
<p>A receipt and an invoice look similar, but they answer opposite questions. An invoice says "here's what you owe." A receipt says "you've paid, and here's the proof." That difference is why every receipt has to make one thing obvious: the money changed hands. Get these details on the page and the receipt does its job.</p>
<ul>
<li><strong>Your business name and contact.</strong> The registered business name if you have one, or your own name if you're a sole trader working under your name. Add a phone number or email so the customer can reach you about it later.</li>
<li><strong>The date.</strong> The day the payment was made, not the day the work was done. For tax records, the payment date is the one that matters.</li>
<li><strong>A unique receipt number.</strong> A number that doesn't repeat, like 0001, 0002, 0003. It lets both sides refer to the exact transaction without confusion, and it keeps your own records in order.</li>
<li><strong>A description of the items or services.</strong> What the customer actually paid for. "Bathroom tap replacement, parts and labour" beats a bare "plumbing." A clear description is what makes the receipt usable as a tax record months later.</li>
<li><strong>The amount paid.</strong> The total the customer handed over. If they paid part of a larger bill, show the amount paid and the balance still owing so nobody's guessing.</li>
<li><strong>The payment method.</strong> Cash, card, bank transfer, cheque. This matters most for cash, where the receipt is often the only trace the payment ever existed.</li>
<li><strong>Tax shown separately.</strong> If you charge sales tax, GST, HST, or VAT, list it as its own line rather than burying it in the total. The customer may need that figure to claim the tax back, and you need it to track what you owe the government.</li>
<li><strong>The word "Paid."</strong> Small detail, big effect. Writing "Paid in full" (or "Paid" with a zero balance) is what turns the document from a bill into a receipt. Without it, a receipt can read like an invoice the customer still has to act on.</li>
</ul>
{{illustration:checklist}}
<p>That's the whole list. If you're ever unsure whether you're writing an invoice or a receipt, the tie-breaker is the word "Paid" and a zero balance. For the full difference between the two documents, see <a href="/invoice-vs-receipt/">invoice vs receipt</a>.</p>
HTML,
    ],

    [
      'h2' => 'Pick a format: book, PDF, or software',
      'anchor' => 'formats',
      'step_name' => 'Choose the right format',
      'step_text' => 'Use a carbon receipt book for on-the-spot cash jobs, a PDF for anything you email, or accounting software when you want the receipt to track itself.',
      'html' => <<<'HTML'
<p>There are three practical ways to produce a receipt. None is wrong. The right one depends on where you are when the money changes hands.</p>
<ul>
<li><strong>A carbon receipt book (handwritten).</strong> The classic two-part pad from any stationery shop. You write once, the carbon copy underneath captures it, the customer takes the top sheet and you keep the copy. It fits on-the-spot work: a market stall, a mobile trade taking cash at the door, a job where there's no printer and no time. The built-in copy is the whole point, and the pre-printed numbers keep your sequence tidy.</li>
<li><strong>A PDF.</strong> Best for anything you send by email. The layout is locked, it opens the same on any phone or laptop, and it looks like a finished document rather than a draft. If the customer paid remotely, or paid a business that expects a clean file for its records, a PDF is the format they want.</li>
<li><strong>Generated from software.</strong> Best once you're issuing more than a handful a month, or once you want the receipt tied to the rest of your books. The software fills in the number, the date, and the totals for you, produces the PDF, and keeps its own copy without you filing anything. It's the least effort per receipt once you're past the occasional one.</li>
</ul>
<p>A rough rule: if the payment happens in person and in cash, reach for the book. If it happens over email or online, reach for a PDF. If you're doing enough of them that filing copies has become a chore, let software handle it.</p>
HTML,
    ],

    [
      'h2' => 'Receipts for cash jobs',
      'anchor' => 'cash-jobs',
      'step_name' => 'Give a receipt for cash payments',
      'step_text' => 'Write a receipt for every cash job, even small ones, because cash leaves no bank record and the receipt becomes the only proof the payment happened.',
      'html' => <<<'HTML'
<p>Cash is exactly where a receipt earns its keep, and exactly where people skip it. A card payment leaves a trail in two banks. A cash payment leaves nothing unless somebody writes it down. So the receipt isn't a nicety on a cash job. It's the only record that the money moved at all.</p>
<p>Say a cleaner finishes a one-off deep clean and the homeowner pays $180 in cash at the door. If no receipt is written, three problems show up later:</p>
<ul>
<li><strong>The customer has no proof they paid.</strong> If a question comes up next month, it's one person's memory against another's.</li>
<li><strong>You can't back up the income.</strong> That $180 is still income you have to declare. When it's tax time, a stack of receipt copies is what turns "I think I earned about this much" into a number you can stand behind.</li>
<li><strong>Your totals drift.</strong> Cash that never gets recorded quietly vanishes from your books, and by year-end your records and your actual earnings don't match.</li>
</ul>
<p>Declaring cash income isn't optional in the US, Canada, the UK, or Australia. Tax authorities treat cash the same as any other payment, and cash jobs are a common thing they look at closely. A written receipt for every cash payment, with your own copy kept, is the simplest way to keep that side of the business clean. It protects the customer and it protects you.</p>
HTML,
    ],

    [
      'h2' => 'Digital or paper: give one copy, keep the other',
      'anchor' => 'copies',
      'step_name' => 'Give a copy and keep your own',
      'step_text' => 'Always hand the customer a copy and keep a matching copy for yourself. Digital copies are easier to store and search; paper works when you have no device on hand.',
      'html' => <<<'HTML'
<p>Every receipt has two readers: the customer, who needs proof they paid, and you, who needs proof you were paid. So every receipt gets made twice. The customer takes one copy, you keep the other. This is the step people forget, and it's the one that matters most when a dispute or a tax question comes up.</p>
<p>Digital or paper, both work, and they have different strengths:</p>
<ul>
<li><strong>Digital.</strong> A PDF emailed to the customer, with a copy saved in a folder or held in your accounting software. Nothing to lose, nothing to fade, and you can search for any receipt in seconds. This is the better long-term option for almost everyone.</li>
<li><strong>Paper.</strong> A handwritten book with its carbon copy. Fits when you're standing at a door with cash and no device. The weakness is storage: ink fades, boxes get bulky, and finding one receipt from eight months ago means flipping pages. Many people snap a photo of each paper copy so there's a digital backup too.</li>
</ul>
<p>Whichever you use, keep your copies for as long as your country's tax rules require, which is often several years. The exact number varies by country and situation, so check with an accountant for yours. For the full breakdown, see <a href="/how-long-to-keep-business-receipts/">how long to keep business receipts</a>. The habit to build is simple: no payment leaves without a copy staying behind.</p>
HTML,
    ],

    [
      'h2' => 'A simple receipt layout you can copy',
      'anchor' => 'layout',
      'step_name' => 'Use a ready-made layout',
      'step_text' => 'Start from a plain layout that already has every required field, then fill in the details for each sale.',
      'html' => <<<'HTML'
<p>Here's a plain layout with every required field already in place. Copy it into a notebook, a document, or the top of your receipt pad, and fill in the blanks for each sale.</p>
<p><strong>RECEIPT</strong></p>
<ul>
<li>[Your business name]</li>
<li>[Phone / email]</li>
<li>Receipt #: [0001]</li>
<li>Date paid: [21 July 2026]</li>
<li>Received from: [Customer name]</li>
<li>For: [Description of items or services]</li>
<li>Subtotal: [$180.00]</li>
<li>Tax: [$0.00]</li>
<li>Total paid: [$180.00]</li>
<li>Payment method: [Cash]</li>
<li><strong>Paid in full. Balance owing: $0.00</strong></li>
</ul>
<p>A filled-in version for the cleaner's cash job above:</p>
<ul>
<li>Bright Spark Cleaning</li>
<li>0400 123 456</li>
<li>Receipt #: 0042</li>
<li>Date paid: 21 July 2026</li>
<li>Received from: J. Okafor</li>
<li>For: One-off deep clean, 3-bedroom house</li>
<li>Subtotal: $180.00</li>
<li>Tax: $0.00</li>
<li>Total paid: $180.00</li>
<li>Payment method: Cash</li>
<li><strong>Paid in full. Balance owing: $0.00</strong></li>
</ul>
<p>If you charge tax, put the real figure on the Tax line and add it into the total. If a customer paid only part of what they owe, show the amount paid on the Total paid line and the real number on the Balance owing line so it's clear more is due. That's the entire document. It doesn't need a logo or fancy design to count.</p>
HTML,
    ],

    [
      'h2' => 'Let Argo Books handle both sides of the receipt',
      'anchor' => 'argo-books',
      'step_name' => 'Automate receipts with Argo Books',
      'step_text' => 'Mark an invoice paid in Argo Books to produce a receipt for the customer, and scan supplier receipts with your phone so the ones you receive get recorded too.',
      'html' => <<<'HTML'
<p>Once you're doing more than the occasional sale, writing each receipt by hand adds up, and that's where software saves the time. Argo Books deals with both sides of a receipt: the ones you give out and the ones you get.</p>
<p><strong>The receipts you issue.</strong> When you mark an invoice as paid in Argo Books, it produces a receipt for the customer. The invoice status changes to Paid, the balance shows zero, and the document becomes the proof of payment you can send. Because the app tracks partial payments and deposits, a receipt reflects exactly what's been paid and what, if anything, is still owed, without you doing the math. Your copy stays in the app, so there's no separate folder to keep.</p>
{{illustration:invoice-doc}}
<p><strong>The receipts you receive.</strong> Every receipt a supplier hands you is a business expense worth recording, and Argo Books lets you snap a photo of it. The built-in AI receipt scanner reads the vendor, date, amount, and tax off the photo and turns it into a recorded expense, so the tax you paid on purchases gets tracked alongside the tax you collected. The free tier scans 10 receipts a month and Premium raises that to 500, and there's a free receipt scanner on the website too. See <a href="/how-to-scan-and-organize-receipts/">how to scan and organize receipts</a> for the full workflow.</p>
<p>The result is one place where the receipts you write and the receipts you collect both live, backing up your income on one side and your deductible expenses on the other. That's the record you'll be glad to have at tax time.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 1,

  'tool_callout_text' => 'Argo Books turns a paid invoice into a receipt for your customer automatically. See how invoicing works.',
  'tool_callout_cta' => 'See invoicing in Argo Books',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'What has to be on a receipt?',
      'a' => 'At a minimum: your business name and a way to contact you, the date the payment was made, a unique receipt number, a description of what was bought, the amount paid, the payment method, any tax shown as its own line, and the word "Paid" with a zero balance. Those details are what make it proof of payment rather than a bill. If you charge sales tax, GST, HST, or VAT, showing it separately matters because the customer may need that figure to claim it back and you need it for your own tax records.',
    ],
    [
      'q' => 'Can I handwrite a receipt?',
      'a' => 'Yes. A handwritten receipt is perfectly valid as long as it has all the required details. A carbon receipt book is the easiest way to do it, because it captures a copy for you automatically and the receipts are already numbered. Handwriting fits on-the-spot cash work where you have no printer. The one habit to keep is legibility and a saved copy: if the ink fades or the page goes missing, so does your record, so many people photograph each handwritten copy as a backup.',
    ],
    [
      'q' => 'Do I need to give receipts for cash payments?',
      'a' => 'You should, and arguably you need to more than for any other payment type. Cash leaves no bank trail, so the receipt is often the only record that the payment happened at all. It protects the customer, who otherwise has no proof they paid, and it protects you, because that cash is still income you have to declare. In the US, Canada, the UK, and Australia, cash income is taxed the same as any other, and unrecorded cash is a common thing tax authorities look at. Write a receipt for every cash job and keep your copy.',
    ],
    [
      'q' => 'Receipt versus invoice, which do I give?',
      'a' => 'Give an invoice before payment and a receipt after. An invoice tells the customer what they owe and how to pay. A receipt confirms they have paid and shows a zero balance. Many small jobs use both: you send the invoice, and once it is paid you send the same document marked "Paid" as the receipt. If a customer pays you on the spot, you can skip straight to a receipt. The deciding detail is whether money has already changed hands.',
    ],
    [
      'q' => 'How long should I keep copies of receipts I issue?',
      'a' => 'Keep them for as long as your country requires you to hold tax records, which is usually several years. The exact number varies: it is commonly around six years in the UK and Canada and around five in Australia, while in the US it is often three years but can be longer in some situations. Because the rule depends on your country and circumstances, check with an accountant for yours. Storing copies digitally makes the long retention period painless, since nothing fades and any receipt is searchable in seconds.',
    ],
    [
      'q' => 'Does a receipt need to show tax?',
      'a' => 'If you charge sales tax, GST, HST, or VAT, show it as its own line rather than folding it into one total. The customer may need that figure to claim the tax back, and separating it keeps your own records clean, since the tax you collect is money owed to the government rather than income you keep. If you are not registered for a sales tax and do not charge one, your receipt simply shows a tax line of zero or no tax line at all.',
    ],
  ],

  'related_niche_slugs' => [
    'cleaning',
    'plumber',
    'contractor',
    'freelance',
  ],

  'related_article_slugs' => [
    'invoice-vs-receipt',
    'what-to-include-on-an-invoice',
    'how-long-to-keep-business-receipts',
    'how-to-scan-and-organize-receipts',
  ],
];
