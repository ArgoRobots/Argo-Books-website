<?php
// articles/data/bookkeeping-for-online-sellers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-online-sellers',

  'h1' => 'Bookkeeping for online sellers: a practical guide',

  'meta_title' => 'Bookkeeping for Online Sellers: a Practical Guide | Argo Books',

  'meta_description' => 'Bookkeeping for online sellers on Etsy, eBay, Shopify, and Amazon: fees, payouts that don\'t match sales, inventory, sales tax, shipping, and returns.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 25,

  'published' => '2026-06-15',

  'updated' => '2026-06-15',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Selling online looks simple from the outside: list a product, someone buys it, money lands in your account. The bookkeeping behind it is anything but. The payout that hits your bank is never the same as what your customer paid, fees come out of every sale in ways that are hard to see, you are buying and holding stock, and depending on what you sell and where, you may owe sales tax.</p>
<p>This is the part of an online business that quietly eats profit, because a seller who only watches payouts has no idea how much each sale actually earned after fees, shipping, and the cost of the item. This guide walks through the parts of bookkeeping that are genuinely tricky for sellers on Etsy, eBay, Shopify, Amazon, and the rest, in plain language, with the habits and tools that keep it straight so you know your real numbers instead of guessing.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why your payout never matches your sales',
      'anchor' => 'payouts-vs-sales',
      'html' => <<<'HTML'
<p>This is the single biggest source of confusion for online sellers, and getting it right is the foundation for everything else. When a marketplace pays you, the deposit is a net figure: your sales total, minus a stack of deductions, sometimes for a period covering many orders at once. If you record only the deposit as your income, your books are wrong from the start, because you have hidden all the costs that came out before the money reached you.</p>
<p>A single payout might already have these taken out of it:</p>
<ul>
<li><strong>Marketplace and listing fees</strong> that the platform charges per sale or per listing.</li>
<li><strong>Payment processing fees</strong> on the card or wallet the customer used.</li>
<li><strong>Refunds and returns</strong> from the same period, netted against new sales.</li>
<li><strong>Shipping labels</strong> you bought through the platform.</li>
<li><strong>Advertising or promotion costs</strong> if you run on-platform ads.</li>
<li><strong>Sales tax</strong> the platform collected and is holding or remitting.</li>
</ul>
<p>The right way to handle this: record the gross sale as income, then record each fee as its own expense, so your books show the full picture instead of a mystery net number. The fees are deductible costs, so capturing them is not just accuracy, it lowers your tax. Every marketplace lets you download a transaction or settlement report that breaks the payout into its parts. That report, not the bank deposit, is your source of truth, and pulling it regularly is the habit the rest of this guide is built on.</p>
HTML,
    ],

    [
      'h2' => 'Track the fees that eat your margin',
      'anchor' => 'track-fees',
      'html' => <<<'HTML'
<p>Fees are where online-selling profit goes to die, precisely because they are small, automatic, and hidden inside the payout. A 6% marketplace fee plus a 3% payment fee plus a listing fee does not feel like much on one order, but across a year it's a serious chunk of revenue, and a seller who never records it has no idea how thin their actual margin is.</p>
<p>The fix is to treat fees as real expenses you track, not background noise. Pull each platform's fee breakdown from its settlement report and record the totals by type: marketplace fees, payment processing, ad spend, label costs. Two payoffs come from this. First, every fee is a deductible business expense, so recording them lowers your tax bill, and unrecorded fees are deductions you simply lose. Second, and just as important, once you can see the fees clearly you can see your true margin per sale, which tells you whether a product is actually making money or just moving money. Plenty of sellers discover a "best seller" barely breaks even once all the fees are counted, and that is information you can only act on if you track it.</p>
<p>If you sell across several platforms, each one has its own fee structure and its own report, so the totals need to come together in one place to give you the whole picture. That consolidation is exactly where keeping proper books, rather than glancing at each platform's dashboard separately, starts to pay for itself.</p>
HTML,
    ],

    [
      'h2' => 'Inventory and cost of goods sold',
      'anchor' => 'inventory-cogs',
      'html' => <<<'HTML'
<p>If you sell physical products, this is the part that separates real bookkeeping from just tracking cash, and it's where a lot of online sellers go wrong. The money you spend buying or making stock is not an ordinary expense you deduct the moment you pay it. It becomes a cost only when the item actually sells, and that timing is what cost of goods sold (COGS) is about.</p>
<p>Here is why it matters. Suppose you spend $5,000 stocking up in December but only sell half of it by year-end. If you treated the whole $5,000 as a December expense, your books would show a loss that is not real, because you still hold $2,500 of stock that is an asset, not a cost yet. The unsold half stays as inventory until it sells, then becomes COGS. Getting this right is what makes your profit number honest, and in most countries it's also how the tax rules require you to account for stock.</p>
<p>To handle it without it taking over your life, you need to track three things: what you paid for stock, what you still hold, and what has sold. That lets you calculate COGS and see your true gross profit (sales minus what those specific items cost you). For a small catalogue, a careful spreadsheet or a count at period-end can do it. As the catalogue and volume grow, manual tracking becomes the bottleneck, and software that tracks stock levels and cost per item earns its place. The guide on <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> goes deeper on the methods. The key point for your books: stock you bought is not an expense until it sells, and treating it that way is what keeps your profit honest.</p>
HTML,
    ],

    [
      'h2' => 'Sales tax across regions',
      'anchor' => 'sales-tax',
      'html' => <<<'HTML'
<p>Sales tax is the part most likely to surprise an online seller, because selling online means selling everywhere, and tax rules are tied to where your customers are, not just where you are. This is genuinely complicated and the rules differ sharply by country, so this section is about awareness and habits, not specific advice, and the real answer for your situation comes from your local tax authority or an accountant.</p>
<p>The big things to understand:</p>
<ul>
<li><strong>You may owe tax in places you don't live.</strong> Many tax systems require you to collect and remit sales tax (or VAT, or GST) once your sales into a region pass a threshold, even if your business is somewhere else entirely. Crossing one of those thresholds without noticing is a common and expensive mistake.</li>
<li><strong>Marketplaces often collect it for you, but not always.</strong> Big platforms increasingly collect and remit sales tax on your behalf in many regions, which is a relief, but it's not universal and it does not always cover every place you sell. You still need to know which sales the platform handled and which are your responsibility.</li>
<li><strong>The tax you collect is not your money.</strong> Sales tax you collect is money you are holding to pass on to the government, not revenue. Treating it as income is a classic way to spend money you owe. Keep it separate in your books, the same way a freelancer keeps income tax aside.</li>
</ul>
<p>The bookkeeping habit that keeps this manageable is recording sales tax separately from your actual sales, and keeping a clear record of what each platform collected versus what you collected yourself. That way, when it's time to file, you know exactly what was handled for you and what you owe. Because the stakes and rules are high and regional, this is the area of online-seller bookkeeping where talking to an accountant early is most worth the money.</p>
HTML,
    ],

    [
      'h2' => 'Shipping, returns, and the rest',
      'anchor' => 'shipping-returns',
      'html' => <<<'HTML'
<p>A few more parts of online selling have their own bookkeeping wrinkles, and ignoring them leaves your profit picture fuzzy:</p>
<ul>
<li><strong>Shipping costs.</strong> What you pay to ship is a business expense, and what you charge the customer for shipping is income. They rarely match. If you offer free shipping, you are absorbing the cost, which quietly cuts your margin on every order, so it needs to be tracked as the real cost it is. Record both sides rather than netting them in your head, so you can see whether your shipping is helping or hurting.</li>
<li><strong>Returns and refunds.</strong> A refund reverses a sale, so it has to come back out of your income, not sit as an ignored line. If the item comes back sellable, it returns to inventory; if it does not, the cost of that item is a loss. Returns are normal in online selling, but unrecorded they make your sales look bigger and your profit look healthier than they are.</li>
<li><strong>Packaging and supplies.</strong> Boxes, mailers, tape, labels, and tissue paper are real recurring costs that sellers often forget because they are small. Over a year of orders they add up, and they are deductible, so capture them like any other expense.</li>
<li><strong>Software and subscriptions.</strong> Your shop subscription, listing tools, design apps, and any inventory or accounting software are deductible costs. They leave the account quietly each month, which is exactly why they get missed.</li>
</ul>
<p>None of these is hard on its own. The trouble comes from leaving them out, because each one you skip makes your real profit a little blurrier, and a blurry profit number is how sellers keep pushing products that are not actually making money. Capture them as they happen and the picture stays sharp.</p>
HTML,
    ],

    [
      'h2' => 'A monthly routine that keeps it straight',
      'anchor' => 'monthly-routine',
      'html' => <<<'HTML'
<p>Online selling generates a lot of small transactions fast, so the difference between clean books and chaos is a regular routine rather than a year-end pile-up. Once a month, or more often if your volume is high:</p>
<ol>
<li><strong>Download each platform's settlement report.</strong> Pull the report that breaks payouts into sales, fees, refunds, shipping, and tax, from every platform you sell on. This is your source of truth, not the bank deposit.</li>
<li><strong>Record gross sales and each fee separately.</strong> Enter the real sales as income and each fee type as its own expense, so your margin is honest and every deductible fee is captured.</li>
<li><strong>Update inventory and COGS.</strong> Account for what you bought, what sold, and what you still hold, so your profit reflects the cost of the items that actually left.</li>
<li><strong>Check the platform reports against your bank.</strong> Make sure the payouts on the reports match what actually landed in your account, so nothing is missing or double-counted.</li>
<li><strong>Set aside sales tax and income tax.</strong> Move the tax you owe out of your spending money, treating collected sales tax as never yours and saving for income tax on your real profit.</li>
</ol>
<p>That routine sounds like a lot written out, but for most sellers it's an hour or so a month once the habit is set, and software that pulls and categorizes the data shrinks it further. The alternative, a year of un-untangled payouts faced the week before a deadline, is the genuine nightmare. Sellers who keep their books current month by month always know their real margin, claim every fee and cost they are owed, and never get a tax surprise. That clarity is what turns an online shop from a hopeful side hustle into a business you can actually steer.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books can track your stock, cost of goods sold, and the fees and costs behind every sale, so you see your real margin.',
  'tool_callout_cta' => 'See inventory tracking in Argo Books',
  'tool_callout_url' => '/features/inventory-management/',

  'faqs' => [
    [
      'q' => 'Why is my marketplace payout less than my total sales?',
      'a' => 'Because the payout is a net figure with a stack of deductions already taken out before the money reaches you: marketplace and listing fees, payment processing fees, shipping labels you bought through the platform, on-platform advertising, refunds netted against new sales, and sometimes sales tax the platform is holding. The deposit you see in your bank is what is left after all of that. The right way to handle it in your books is to record the gross sale as income and each deduction as its own expense, using the platform\'s settlement or transaction report rather than the bank deposit. That keeps your income accurate and captures every fee as a deductible cost, which both lowers your tax and shows your true margin.',
    ],
    [
      'q' => 'Do I have to track inventory, or can I just deduct stock when I buy it?',
      'a' => 'If you sell physical products, you generally cannot simply deduct stock when you buy it, because in most tax systems stock becomes a cost (cost of goods sold) only when the item actually sells. Until then it\'s an asset you are holding, not an expense. Deducting a big stock purchase all at once would make your books show a loss that is not real and usually breaks the tax rules. So yes, you need to track what you bought, what sold, and what you still hold, at least well enough to calculate cost of goods sold at period-end. For a small catalogue a careful spreadsheet or a count works; as volume grows, inventory software earns its place. Check your country\'s specific rules, since the details vary.',
    ],
    [
      'q' => 'Do I owe sales tax in other regions if I sell online?',
      'a' => 'Possibly, and this is genuinely complicated, so treat this as a heads-up rather than advice and confirm with your local tax authority or an accountant. Many tax systems require you to collect and remit sales tax, VAT, or GST once your sales into a region cross a threshold, even if your business is based elsewhere, so selling online can create obligations in places you don\'t live. Large marketplaces increasingly collect and remit this for you in many regions, which helps, but it\'s not universal and may not cover everywhere you sell, so you still need to know which sales the platform handled. The key bookkeeping habit is to record sales tax separately from your real sales and keep clear records of what each platform collected versus what you collected yourself.',
    ],
    [
      'q' => 'How often should I do my bookkeeping as an online seller?',
      'a' => 'At least monthly, and more often if your volume is high, because online selling produces a lot of small transactions quickly and they are far easier to handle in batches than as a year-end pile. A monthly routine of downloading each platform\'s settlement report, recording gross sales and fees separately, updating inventory and cost of goods sold, checking the reports against your bank, and setting aside tax keeps everything current. For most sellers that is around an hour a month once the habit is set, and software that pulls and categorizes the data shrinks it further. The thing to avoid is letting a year of untangled payouts build up, which turns a manageable routine into a deadline-week nightmare and almost guarantees missed deductions and tax surprises.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned, and yes, this is the Argo Books site, so read it knowing that. But the advice does not depend on our tool. Recording gross sales and fees separately, tracking inventory and cost of goods sold, keeping sales tax apart, and a monthly routine are habits that work with a spreadsheet, a free app, or a competitor\'s software, and the guide says plainly that a careful spreadsheet is fine for a small catalogue. We also point you to your accountant for the genuinely tricky sales-tax questions, because that is where the real risk is, not in which software you pick. If you take only the habits and never look at Argo Books, the guide did its job.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'designer',
    'freelance',
  ],

  'related_article_slugs' => [
    'inventory-tracking-for-small-businesses',
    'bookkeeping-for-freelancers',
    'small-business-bookkeeping-basics',
  ],
];
