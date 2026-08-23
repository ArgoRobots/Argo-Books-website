<?php
// articles/data/bookkeeping-for-repair-shops.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-repair-shops',

  'h1' => 'Bookkeeping for repair shops',

  'meta_title' => 'Bookkeeping for Repair Shops: a Practical Guide | Argo Books',

  'meta_description' => 'Practical bookkeeping for repair shops: split parts from labor, track parts inventory and COGS, handle deposits, warranty redos, payments, and sales tax.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 54,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A repair shop sells two very different things at once, and the bookkeeping only makes sense when you keep them apart. There's the part you put in, a screen, a belt, a compressor, a chain, which you bought and marked up. And there's the labor, the time and skill it took to actually fix the thing. They earn money in different ways, they get taxed differently in a lot of places, and if your books lump them together you never really know where your profit comes from.</p>
<p>This guide covers the bookkeeping that matters for a fix-it business, whether you repair phones, computers, small engines, appliances, bikes, or a bit of everything. In plain language: how to split parts from labor on a job and on the invoice, how to track parts as inventory and cost of goods sold, how to see your true margin on each side, and how to handle deposits, warranty redos, cash and card, expenses, and sales tax. Get these straight and the shop stops being a busy blur and starts being a business you can steer.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why repair-shop books are different',
      'anchor' => 'why-different',
      'html' => <<<'HTML'
<p>Most small businesses sell one kind of thing. A shop sells products, a consultant sells time. A repair shop does both on the same ticket, and that's what makes the books trickier than they look. Every job is part product sale and part service, and the two behave differently in ways that matter:</p>
<ul>
<li><strong>Parts have a cost you paid; labor doesn't.</strong> When you fit a $40 screen and charge $90 for it, the $40 is a real cost that came out of your pocket. Labor has no material cost behind it, so a dollar of labor and a dollar of parts are not the same dollar of profit.</li>
<li><strong>Parts are inventory until they're used.</strong> The box of screens on your shelf is money sitting there, not an expense yet. It becomes a cost only when it goes into a repair that gets paid.</li>
<li><strong>Tax often treats them differently.</strong> In many places parts are taxable goods and labor may be taxed differently or not at all. If your invoice doesn't separate them, you can't charge or report the right tax.</li>
<li><strong>Deposits and other people's property show up.</strong> You take money before the work is done, and you hold customers' devices and machines while you work on them. Both need to be handled cleanly so your books and your customer trust both stay intact.</li>
</ul>
<p>None of this needs fancy accounting. It needs a few habits built around one idea: keep parts and labor as separate lines all the way through. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Split parts from labor on every job and invoice',
      'anchor' => 'split-parts-labor',
      'html' => <<<'HTML'
<p>This is the habit everything else sits on. On every job, and on every invoice you hand the customer, show parts and labor as separate lines. Not "phone repair, $120," but "replacement screen, $90" and "labor, $30." It takes a few extra seconds and it pays off in three directions at once.</p>
<p>For the customer, a split invoice looks honest and professional. They can see what they're paying for, which cuts down on "why so much?" arguments and makes the price feel fair. For your taxes, separating parts from labor is often the only way to apply sales tax correctly, because the two are frequently taxed at different rates or rules. And for you, splitting the lines is what lets your books show where the money actually comes from, which you'll need for the margin work later in this guide.</p>
{{illustration:invoice-doc}}
<p>The key is to record the split at the moment you build the ticket, while the job is in front of you and you know exactly which parts went in. Note the part, what it cost you, and what you're charging for it, then add the labor line for your time. If you invoice with a tool that has separate fields for parts and labor, even better, because the split flows straight into your books instead of being untangled later. <a href="/for-repair-shops/">Argo Books lets you build repair invoices that keep parts and labor on their own lines</a>, so the tax and the margins come out right without extra work.</p>
HTML,
    ],

    [
      'h2' => 'Track parts inventory and cost of goods sold',
      'anchor' => 'parts-inventory-cogs',
      'html' => <<<'HTML'
<p>The parts on your shelf are money you've already spent, sitting there waiting to earn. That's inventory, and the cost of a part doesn't count against your profit the day you buy it. It becomes a cost, cost of goods sold or COGS, only when the part goes into a repair that a customer pays for.</p>
<p>Here's why that timing matters. Say you stock up on $2,000 of common parts in one order to get a better price. If you treated the whole $2,000 as an expense that month, your books would show a bad month that wasn't real, because most of those parts are still on your shelf as an asset. As you use them on jobs, each part's cost moves from inventory to COGS, and your profit for each month reflects only the parts that actually went out the door. In most countries the tax rules expect you to account for stock this way, and it's also what makes your monthly profit honest instead of lurching around with your buying.</p>
{{illustration:inventory-boxes}}
<p>You don't need a warehouse system to do this. Track what you spent on parts, roughly what you still have on hand, and what got used in jobs. That's enough to work out COGS and see your real gross profit on parts. A careful spreadsheet with a shelf count at month-end works for a small shop; as your parts list grows, software that tracks stock and cost per part earns its keep and warns you before a common part runs out. Our guide on <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> goes deeper. The rule to hold onto: a part isn't an expense until it leaves as a finished, paid repair.</p>
HTML,
    ],

    [
      'h2' => 'Know your margin on parts versus labor',
      'anchor' => 'margins',
      'html' => <<<'HTML'
<p>Once parts and labor are separate lines and you know what your parts cost you, you can see something most repair shops only guess at: which side of the business actually makes the money. And the answer surprises a lot of owners.</p>
<p>Your margin on parts is simple: what you charged for the part minus what you paid for it. Fit a $40 screen, charge $90, and you made $50 of gross profit on that part before any of your time. Your labor, on the other hand, is close to pure margin because there's no material cost behind it, but it's capped by the hours in your day. You can only turn so many wrenches. Parts profit scales with volume; labor profit scales with your time. Knowing the split tells you where growth actually comes from.</p>
<p>Run the numbers across a few months and patterns appear. Maybe your quick jobs are almost all labor and barely worth the bench time once you count the customer chat. Maybe a whole category is only profitable because of the parts markup, and your labor rate is too low. Maybe you're marking parts up less than you think once shipping and the odd dead-on-arrival part are counted. You can't fix any of that by feel. You fix it by pricing your labor rate and your parts markup from the real numbers, which you only have once parts and labor live on separate lines in your books.</p>
HTML,
    ],

    [
      'h2' => 'Deposits, customer property, and warranty redos',
      'anchor' => 'deposits-warranty',
      'html' => <<<'HTML'
<p>Repair work has a few money situations that a plain product shop never deals with. Handle each one with a simple rule and it stays clean.</p>
<ul>
<li><strong>Deposits on repairs.</strong> When you take money up front, say to order a special part or to hold a slot, that deposit isn't income yet. It's money you're holding against work you haven't finished. Record it as a deposit, then turn it into income when the job is done and invoiced, applying it against the final bill. That way a pile of deposits doesn't make a slow month look great, then leave you short when the work actually lands.</li>
<li><strong>Holding customer property.</strong> The devices and machines on your bench belong to your customers, not to you, so they're never inventory and never an asset in your books. What you do need is a clear record of what came in, whose it is, and what was agreed, so nothing gets lost, mixed up, or argued over. Keep that on the job ticket, separate from your accounting.</li>
<li><strong>Warranty and redo jobs.</strong> Sometimes a repair comes back and you fix it again for free. It's still a real cost even though you don't charge for it, because you burned parts and time. Record the parts used on a redo as a cost (they came out of your inventory) and note the labor, even at zero charge, so you can see how often redos happen. A rising pile of free redos is telling you something about a part supplier or a process, and you'll only spot it if you track them.</li>
</ul>
<p>The theme is the same across all three: money and property that isn't really yours yet gets kept separate from money that is, so your profit number tells the truth.</p>
HTML,
    ],

    [
      'h2' => 'Payments, expenses, and sales tax',
      'anchor' => 'payments-expenses-tax',
      'html' => <<<'HTML'
<p>Day to day, a repair shop takes money a few ways and spends it on more than just parts. Keeping all of it recorded is what makes the month-end painless.</p>
<p><strong>Cash and card.</strong> A lot of repair shops still take cash, and cash is the easiest income to lose track of and the easiest to forget is taxable. Record every sale the day it happens, whether it came in as cash, card, or transfer, and match your recorded sales against what actually landed in the bank and the till. Card payments usually carry a processing fee, and that fee is a deductible business cost, so record the gross sale and the fee separately rather than just banking the net.</p>
<p><strong>Expenses beyond parts.</strong> Parts get the attention, but a repair shop spends on plenty else that's all deductible: tools and test gear, shop supplies like solder, cleaner, and adhesives, rent and utilities on the shop, software, and the business share of your phone and vehicle. These are easy deductions to miss because they arrive as small receipts through the year. Capture each one when it happens, a photo or a scan, so it isn't a shoebox in April. <a href="/best-free-ai-receipt-scanner/">Argo Books can scan a receipt straight into an expense</a>, which keeps the small stuff from slipping through.</p>
{{illustration:receipt-scan}}
<p><strong>Sales tax on parts versus labor.</strong> This is where the parts-and-labor split pays off again. In many places the part you sell is a taxable good, while labor may be taxed at a different rate or not taxed at all, and some regions tax the whole repair. Because the rules genuinely vary by country, state, and province, treat this as awareness, not advice: check with your local tax authority or an accountant for exactly what applies to you. The bookkeeping habit that works everywhere is to record the tax you collect separately from your sales. The tax you collect is money you're holding to pass on, not income, and treating it as income is a classic way to spend money you'll owe later. Argo Books tracks tax collected against tax paid and gives you a tax summary, though it doesn't file or send in the tax for you, so the filing is still yours or your accountant's to do.</p>
HTML,
    ],

    [
      'h2' => 'A simple monthly routine',
      'anchor' => 'monthly-routine',
      'html' => <<<'HTML'
<p>A repair shop throws off a steady stream of small transactions, part orders, card fees, deposits, cash tickets, so the difference between clean books and a year-end scramble is a short routine done regularly. Once a month:</p>
<ol>
<li><strong>Check your recorded sales against the bank and till.</strong> Make sure every job, cash and card, is recorded, and that the totals match what actually landed. Catching a missing ticket in the month it happened takes seconds; catching it ten months later takes an afternoon.</li>
<li><strong>Record every part order and expense.</strong> Enter your parts purchases, tools, shop supplies, rent, and card fees, and keep the receipts. This is where receipt scanning earns its keep.</li>
<li><strong>Update your parts inventory and COGS.</strong> Account for what you bought, roughly what's still on the shelf, and what got used in jobs, so your profit reflects the parts that actually went out.</li>
<li><strong>Clear out finished deposits.</strong> Turn deposits on completed jobs into income and make sure none are sitting recorded as income before the work is done.</li>
<li><strong>Set the tax money aside.</strong> Move the sales tax you collected, and a share for income tax, out of your spending money, treating collected tax as never yours to spend.</li>
</ol>
{{illustration:checklist}}
<p>Written out it looks like a lot, but for most shops it's an hour or two a month once the habit sticks, and a tool that scans receipts, tracks stock, and builds tax-ready reports shrinks it further. The payoff is real: you always know your margin on parts and on labor, you claim every deduction you're owed, and you can look at the shop and say with confidence which work is worth taking. That's the difference between a bench that's always busy and a business that's actually making money.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books keeps parts and labor on separate lines, tracks your parts inventory and cost of goods sold, and scans expense receipts, so you see your real margin on every repair.',
  'tool_callout_cta' => 'See inventory tracking in Argo Books',
  'tool_callout_url' => '/features/inventory-management/',

  'faqs' => [
    [
      'q' => 'Why should I separate parts from labor on a repair invoice?',
      'a' => 'Because they behave differently in every way that matters. For the customer, a split invoice shows exactly what they\'re paying for, which feels fair and cuts down on price arguments. For tax, parts and labor are often taxed differently, so separating them is usually the only way to charge and report the right sales tax. And for you, splitting the lines is what lets your books show where the profit actually comes from, since a dollar of parts (which had a cost behind it) and a dollar of labor (which didn\'t) are not the same profit. Record the split when you build the ticket, while the job is in front of you, so it flows straight into your books.',
    ],
    [
      'q' => 'Can I just deduct parts when I buy them?',
      'a' => 'Usually not all at once, because in most tax systems a part becomes a cost (cost of goods sold) only when it goes into a repair that a customer pays for. Until then, parts on your shelf are inventory, an asset you\'re holding rather than an expense. If you deducted a big parts order the moment you bought it, your books would show a bad month that isn\'t real and it would generally break the tax rules. So track what you spent on parts, roughly what\'s still on hand, and what got used in jobs, at least well enough to work out cost of goods sold at month-end. The specifics vary by country, so check your local rules or ask an accountant.',
    ],
    [
      'q' => 'How do I handle a deposit a customer pays before the repair is done?',
      'a' => 'Treat it as money you\'re holding, not income yet. When a customer pays up front, say to order a special part or hold a slot, record it as a deposit rather than a sale. Once the job is finished and invoiced, turn the deposit into income and apply it against the final bill. Handling it this way keeps a run of deposits from making a slow month look profitable and then leaving you short when the work actually lands. It also matches your income to when you earned it, which is what makes your monthly profit honest. If you take a lot of deposits, having them tracked separately is worth the small effort.',
    ],
    [
      'q' => 'How do I know if I make more money on parts or on labor?',
      'a' => 'You separate them in your books, then compare. Your margin on parts is what you charged minus what you paid: fit a $40 screen, charge $90, and you made $50 before any of your time. Your labor is close to pure margin because there\'s no material cost, but it\'s capped by the hours in your day. Parts profit grows with volume; labor profit grows only with your time. Run the numbers across a few months and you\'ll see which side carries the shop, and often it\'s not what you expected. That tells you whether to raise your labor rate, adjust your parts markup, or chase more of one kind of work. You can only do it once parts and labor live on separate lines.',
    ],
    [
      'q' => 'Do I charge sales tax on the labor part of a repair?',
      'a' => 'It depends entirely on where you are, so this is one to confirm with your local tax authority or an accountant rather than guess. In many places the part you sell is a taxable good while labor is taxed at a different rate or not taxed at all, and some regions tax the whole repair as one. That\'s exactly why keeping parts and labor on separate invoice lines matters, because you can\'t apply the right tax to each if they\'re lumped together. Whatever the rule turns out to be, record the tax you collect separately from your sales, since it\'s money you\'re holding to pass on, not income. Argo Books tracks tax collected against tax paid and gives you a summary, but it doesn\'t file or send in the tax for you.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'electrician',
    'plumber',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'inventory-tracking-for-small-businesses',
    'small-business-bookkeeping-basics',
  ],
];
