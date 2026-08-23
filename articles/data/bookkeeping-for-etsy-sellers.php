<?php
// articles/data/bookkeeping-for-etsy-sellers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-etsy-sellers',

  'h1' => 'Bookkeeping for Etsy sellers and handmade businesses',

  'meta_title' => 'Bookkeeping for Etsy Sellers & Handmade Shops | Argo Books',

  'meta_description' => 'Bookkeeping for Etsy sellers and handmade businesses: your true cost per item, Etsy fees, materials inventory, pricing, craft-fair cash, and sales tax.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 26,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Making the thing is the fun part. The money side of a handmade business is where a lot of makers quietly lose the plot, because selling a craft you made yourself hides a cost that a reseller never has to think about: your materials. When you buy a mug and resell it, your cost is obvious. When you throw the mug from a bag of clay, dip it in glaze you mixed, and fire it in a kiln that runs on electricity, the cost of that one mug is spread across a dozen purchases and a power bill, and most sellers never add it up.</p>
<p>That is why so many handmade shops feel busy but never seem to get ahead. This guide covers the bookkeeping that actually matters for Etsy sellers and other makers, in plain language: how to find your true cost per item, how to handle the stack of Etsy fees that shrink every payout, how to track materials as inventory, what your numbers should tell you about pricing, and how craft-fair cash and sales tax fit in. Get these straight and you stop guessing whether your shop makes money.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Your true cost per item is the number that matters',
      'anchor' => 'true-cost-per-item',
      'html' => <<<'HTML'
<p>Everything else in handmade bookkeeping sits on top of this one number, and it's the number most makers never calculate: what it actually cost you to make the item you just sold. Not the bag of clay, not the spool of thread, but the share of those things that went into this one piece.</p>
<p>Work it out for a single product and it clicks. Say a candle uses $2.10 of wax, $0.40 of wick, $1.20 of fragrance, $0.90 for the jar, and $0.60 for the label and box. That's $5.20 in materials before you've spent a minute of your own time or paid a cent of Etsy's fees. If you sell it for $18 and feel good about the "margin," you're forgetting that Etsy's cut, payment fees, and shipping still come out of that $18, and so does the cost of every candle that didn't sell. The maker who knows their $5.20 can price with confidence. The one who doesn't is guessing, and usually guessing low.</p>
{{illustration:coins}}
<p>You don't need to cost every item to the penny every time. Cost each product once, keep the recipe (how much of each material it uses), and update it when your supply prices jump. That single habit, knowing the material cost of each thing you sell, is what separates a handmade business from an expensive hobby with a shop attached.</p>
HTML,
    ],

    [
      'h2' => 'The Etsy fee stack: why your payout is smaller than you think',
      'anchor' => 'etsy-fees',
      'html' => <<<'HTML'
<p>The deposit Etsy sends you is never what your customers paid, because a stack of fees comes out first, and each one is small enough to ignore and frequent enough to matter. Recording only the payout as your income hides all of it, and hidden fees mean both inaccurate books and lost tax deductions, because every one of these is a deductible business cost:</p>
<ul>
<li><strong>Listing fees</strong> charged per item you list, and again each time a listing renews.</li>
<li><strong>Transaction fees</strong> taken as a percentage of each sale, including the shipping you charged.</li>
<li><strong>Payment processing fees</strong> on top, a percentage plus a flat amount per order.</li>
<li><strong>Offsite Ads fees</strong> when a sale comes through Etsy's advertising, which can be a chunky percentage.</li>
<li><strong>Etsy Ads</strong> spend if you run your own on-platform ads.</li>
<li><strong>Shipping labels</strong> you buy through Etsy, netted out of the same payout.</li>
</ul>
<p>The right way to handle it: record the gross sale as income, then record each fee as its own expense, using Etsy's payment account or monthly statement rather than the bank deposit as your source of truth. Do that and two things happen. You capture every deductible fee, which lowers your tax. And you finally see your real margin per sale, which is often a shock: a maker who thought they cleared $13 on that $18 candle may be closer to $9 once Etsy's cut and the $5.20 of materials are both counted. That's not bad news, it's the information you need to price and to decide which products are worth making.</p>
HTML,
    ],

    [
      'h2' => 'Materials are inventory until they become a product',
      'anchor' => 'materials-inventory',
      'html' => <<<'HTML'
<p>This is the part that trips up makers more than resellers, because your inventory isn't finished goods sitting on a shelf, it's raw materials waiting to become something. The money you spend on clay, fabric, beads, wax, or wood isn't an expense the moment you buy it. It becomes a cost (cost of goods sold, or COGS) only when a product made from it actually sells.</p>
<p>Here's why it matters at tax time. Suppose you spend $3,000 stocking up on supplies in December to get ready for a busy season, but only work through half of it by year-end. If you deducted the whole $3,000 as a December expense, your books would show a loss that isn't real, because you still hold $1,500 of materials that are an asset, not a cost yet. The unused supplies stay as inventory until they go into products that sell. In most countries the tax rules require you to account for stock this way, and getting it right is what makes your profit number honest instead of lumpy.</p>
{{illustration:inventory-boxes}}
<p>You don't need a warehouse system for this. Track three things: what you spent on materials, roughly what you still have on hand, and what has sold. Combined with your per-item recipes from earlier, that lets you calculate COGS and see your true gross profit. For a small maker a careful spreadsheet and a stock count at period-end is plenty; as your volume and material list grow, software that tracks stock and cost per item earns its keep. Our guide on <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> goes deeper. The rule to remember: supplies you bought aren't an expense until they leave as a finished, sold product.</p>
HTML,
    ],

    [
      'h2' => 'What your numbers should tell you about pricing',
      'anchor' => 'pricing',
      'html' => <<<'HTML'
<p>Good books aren't just for the tax office, they're the tool that tells you whether your prices work, and pricing is where handmade sellers most often undercharge. Once you know your material cost per item and your real Etsy fees, you can see something most makers never do: which products actually make money and which just keep you busy.</p>
<p>Run the full stack for each product. Take the sale price, subtract the materials, subtract the Etsy and payment fees, subtract the packaging, and subtract what shipping actually cost you beyond what the customer paid. What's left has to cover your time and still leave a profit, or the product isn't really earning. Do this across your shop and patterns jump out: a "best seller" that barely clears its costs, a slow item that's actually your most profitable, a whole category that only looks good because you never counted the materials.</p>
<p>The one cost makers forget hardest is their own labor. If your numbers leave nothing for your time, you've bought yourself a job that pays in exposure. You don't have to solve pricing in your bookkeeping, but honest books are what let you see the problem instead of feeling vaguely broke while the shop looks active. Price from the real numbers, not from what feels polite to charge.</p>
HTML,
    ],

    [
      'h2' => 'Craft fairs, cash, and selling in more than one place',
      'anchor' => 'craft-fairs-cash',
      'html' => <<<'HTML'
<p>Most handmade sellers don't sell only on Etsy. There are craft fairs and markets, maybe a Shopify or Square shop, maybe direct sales to friends and repeat buyers, and each channel has its own quirks that need to land in one set of books, not five dashboards you glance at separately.</p>
<ul>
<li><strong>Cash and market sales.</strong> Cash is the easiest income to lose track of and the easiest to forget is taxable. Record what you sell at each fair, ideally the same evening, and log the booth fee, table rental, and travel as the real business expenses they are. A market that felt like a good day can turn out to be a wash once the $75 booth and the gas are counted, and you only know if you write it down.</li>
<li><strong>Multiple online shops.</strong> If you sell on Etsy and Shopify and at markets, each has a different fee structure and a different report, so the totals have to come together in one place to show your whole business. That consolidation is exactly where keeping proper books beats staring at each platform on its own.</li>
<li><strong>Samples, seconds, and gifts.</strong> The mug you gave away, the scarf you kept, the seconds you sold cheap at a fair: these use up materials that came out of your inventory. You don't need to agonize over each one, but a rough accounting keeps your material costs and your profit honest.</li>
</ul>
<p>The goal isn't a perfect ledger of every keychain. It's that all your income and all your costs, from every channel, end up in one place so the profit number reflects the whole business rather than just your busiest storefront.</p>
HTML,
    ],

    [
      'h2' => 'Sales tax, and the hobby-versus-business line',
      'anchor' => 'sales-tax-hobby',
      'html' => <<<'HTML'
<p>Two tax topics catch handmade sellers off guard. Both are genuinely regional, so treat this as awareness, not advice, and confirm the specifics with your local tax authority or an accountant.</p>
<p><strong>Sales tax.</strong> Selling online means selling into places you don't live, and many tax systems require you to collect and remit sales tax, VAT, or GST once your sales into a region pass a threshold. The relief for Etsy sellers is that large marketplaces increasingly collect and remit sales tax on your behalf in many regions, but it isn't universal and it may not cover your craft-fair or Shopify sales, so you still need to know which sales were handled for you and which are yours. The key bookkeeping habit is simple: record sales tax separately from your actual sales. The tax you collect is money you're holding to pass on, not income, and treating it as income is a classic way to spend money you owe.</p>
<p><strong>Hobby versus business.</strong> Many makers start selling before they think of it as a business, and tax authorities have their own line for when a hobby becomes a business you report income and claim expenses on. The threshold and rules vary by country, but the practical takeaway is the same either way: keep records from the start. If it stays a hobby, you've got tidy records. If it grows into a business, you already have the material costs, fees, and income you need to file properly and claim every deduction, instead of reconstructing a year from memory and a shoebox.</p>
HTML,
    ],

    [
      'h2' => 'A simple monthly routine',
      'anchor' => 'monthly-routine',
      'html' => <<<'HTML'
<p>Handmade selling generates a steady trickle of small transactions, supply runs, fee deductions, market days, so the difference between clean books and a year-end nightmare is a short routine done regularly. Once a month:</p>
<ol>
<li><strong>Pull your Etsy payment statement.</strong> Download the report that breaks payouts into sales, fees, refunds, and shipping, plus any Shopify or Square reports. This, not the bank deposit, is your source of truth.</li>
<li><strong>Record gross sales and each fee separately.</strong> Enter real sales as income and each fee type as its own expense, so your margin is honest and every deductible fee is captured.</li>
<li><strong>Log your supply purchases and market costs.</strong> Enter materials, packaging, booth fees, and travel, and keep the receipts. Argo Books can scan a receipt into an expense so a supply run doesn't become a shoebox.</li>
<li><strong>Update materials and COGS.</strong> Account for what you bought, roughly what you still hold, and what sold, so your profit reflects the cost of the items that actually went out.</li>
<li><strong>Set aside sales tax and income tax.</strong> Move the tax you owe out of your spending money, treating collected sales tax as never yours.</li>
</ol>
<p>Written out it sounds like a lot, but for most makers it's an hour or so a month once the habit sticks, and a tool that scans receipts and tracks stock shrinks it further. The payoff is real: you always know your true cost per item, you claim every deduction you're owed, and you can look at your shop and say with confidence which products are worth making. That's the difference between a hobby that drains money and a handmade business you can actually steer.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books tracks your materials, cost of goods sold, and the fees behind every sale, and scans supply receipts in seconds, so you see your true cost per item.',
  'tool_callout_cta' => 'See inventory tracking in Argo Books',
  'tool_callout_url' => '/features/inventory-management/',

  'faqs' => [
    [
      'q' => 'How do I figure out what a handmade item actually costs me to make?',
      'a' => 'Add up the share of each material that goes into one finished piece. For a candle that might be the wax, wick, fragrance, jar, and label; for a knitted item it\'s the yarn and any notions. That total is your material cost per item, and it\'s the foundation for pricing and for cost of goods sold. Cost each product once, write down the recipe of how much of each material it uses, and update it when supply prices change so you\'re not recosting from scratch every time. Note that this material cost is only part of the picture: your Etsy and payment fees, packaging, and shipping still come out of the sale price on top of it, and your own time has to be covered too. Knowing the material cost is what lets you see whether a product is genuinely profitable or just keeping you busy.',
    ],
    [
      'q' => 'Why is my Etsy payout so much less than my sales total?',
      'a' => 'Because Etsy takes a stack of fees out before the money reaches you: listing fees per item, a transaction fee on each sale including the shipping you charged, payment processing fees, Offsite Ads fees when a sale comes through Etsy advertising, any Etsy Ads spend you chose, and the cost of shipping labels you bought through the platform. The deposit in your bank is what\'s left after all of that. The right way to handle it in your books is to record the gross sale as income and each fee as its own expense, using your Etsy payment account or monthly statement rather than the bank deposit. That keeps your income accurate and captures every fee as a deductible cost, which lowers your tax and shows your true margin per sale.',
    ],
    [
      'q' => 'Can I just deduct my craft supplies when I buy them?',
      'a' => 'Usually not all at once, because in most tax systems supplies become a cost (cost of goods sold) only when a product made from them actually sells. Until then, unused materials are inventory, an asset you\'re holding rather than an expense. If you deducted a big supply haul the moment you bought it, your books would show a loss that isn\'t real and it would generally break the tax rules. So you need to track what you spent on materials, roughly what you still have on hand, and what has sold, at least well enough to calculate cost of goods sold at period-end. For a small maker a careful spreadsheet and a stock count works fine; as your material list grows, inventory software earns its place. The specifics vary by country, so check your local rules.',
    ],
    [
      'q' => 'Do I have to report income from a hobby craft shop?',
      'a' => 'Possibly, and where the line falls between a hobby and a business depends on your country and how you sell, so confirm it with your local tax authority or an accountant. Many tax systems treat regular selling with the intent to make money as a business you must report, even if it started as a hobby, while occasional casual sales may be treated differently. The practical advice is the same whichever side you fall on: keep records from the start. Track your income, your material costs, and your Etsy and market fees from day one. If it stays a hobby you simply have tidy records, and if it grows into a business you already have everything you need to file properly and claim your deductions, instead of trying to rebuild a whole year from memory.',
    ],
    [
      'q' => 'How do I handle sales I make at craft fairs and markets in cash?',
      'a' => 'Treat them exactly like any other income, and record them promptly, because cash is the easiest income to lose track of and it\'s still taxable. Log what you sold at each market, ideally the same evening while you remember, and record the booth or table fee, any rental, and your travel as business expenses, since a market that felt busy can turn out to be a wash once those costs are counted. If you also sell on Etsy or a Shopify or Square shop, the aim is to get every channel into one set of books so your profit reflects the whole business, not just your busiest storefront. Keep any receipts from the day, and remember that stock you sold at the fair came out of your materials inventory just like an online sale.',
    ],
  ],

  'related_niche_slugs' => [
    'designer',
    'generic',
    'freelance',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-online-sellers',
    'inventory-tracking-for-small-businesses',
    'small-business-bookkeeping-basics',
  ],
];
