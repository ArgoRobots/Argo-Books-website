<?php
// articles/data/what-is-cost-of-goods-sold.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-is-cost-of-goods-sold',

  'h1' => 'What is cost of goods sold (COGS)? A plain-English guide',

  'meta_title' => 'What Is Cost of Goods Sold (COGS)? Plain Guide | Argo Books',

  'meta_description' => 'Cost of goods sold explained in plain English: what counts, what doesn\'t, the formula with worked examples, and simple ways to track COGS in a small business.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 40,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 12,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Cost of goods sold, usually shortened to COGS, is one of those accounting terms that sounds harder than it is. Here's the whole idea in one sentence: COGS is what the things you sold cost you, counted only when they sell. Not what you spent on stock this month. Not your rent or your ads. Just the cost, to you, of the actual items that went out the door to customers.</p>
<p>That one number does a lot of work. It's what turns your sales total into a profit number you can trust, it's what most tax systems expect you to report, and it's the difference between knowing your products earn money and hoping they do. This guide explains COGS in plain language: what goes in it and what stays out, why the timing rule matters more than people expect, the standard formula with worked examples, and how it plays out for resellers, makers, and service businesses.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The plain definition: what the things you sold cost you',
      'anchor' => 'plain-definition',
      'html' => <<<'HTML'
<p>Say you buy a phone case for $4 and sell it for $15. The $4 is your cost of goods sold for that sale. That's it. That's the concept. COGS is the direct cost of the specific items your customers actually bought.</p>
<p>Two words in the name carry all the meaning, and both are easy to skate past:</p>
<ul>
<li><strong>"Goods"</strong> means the products themselves and what it directly took to have them ready to sell: the item you bought for resale, or the materials that went into the thing you made. It does not mean everything your business spends money on.</li>
<li><strong>"Sold"</strong> means the cost counts only when the item sells. Buying stock doesn't create COGS. Selling it does. Stock sitting on your shelf hasn't cost you anything yet in profit terms; it's just money in a different shape, waiting.</li>
</ul>
<p>So if you bought 100 phone cases at $4 each ($400 total) and sold 30 of them this month, your COGS for the month is $120, not $400. The other 70 cases are inventory, which is an asset you own, like cash you've temporarily turned into plastic. Their $280 of cost will become COGS in whatever month they actually sell.</p>
<p>If you hold onto that one rule, cost follows the sale, everything else about COGS is just bookkeeping mechanics.</p>
HTML,
    ],

    [
      'h2' => 'What goes in COGS, and what stays out',
      'anchor' => 'whats-in-whats-out',
      'html' => <<<'HTML'
<p>COGS covers <strong>direct costs</strong>: spending you can trace to the products you sell. For most small businesses that means:</p>
<ul>
<li><strong>Stock bought for resale.</strong> What you paid your supplier for the items, including shipping to get them to you and any import duties.</li>
<li><strong>Materials, if you make things.</strong> The wood, fabric, clay, beads, wax, or ingredients that end up inside the finished product.</li>
<li><strong>Packaging that's part of the product.</strong> The jar the candle comes in, the box a gift set is sold in. (Generic mailing supplies are usually treated as an ordinary expense instead; either way, be consistent.)</li>
<li><strong>Direct labor, in some businesses.</strong> If you pay someone specifically to produce the goods, their wages for that work can belong in COGS. For a solo owner, your own time usually doesn't go in; it gets paid out of profit instead.</li>
</ul>
<p>Everything else your business spends is an <strong>operating expense</strong>, not COGS. That includes rent, software subscriptions, advertising, your phone bill, accounting fees, insurance, and the fees platforms charge you to sell. Those are real costs and they absolutely reduce your profit, but they don't belong in COGS, because they don't rise and fall with each individual item sold. You'd pay the rent whether you sold two units or two hundred.</p>
<p>A quick test that resolves most edge cases: <em>if you sold twice as many units, would this cost roughly double?</em> Materials and stock, yes, so they're COGS. Rent and your website subscription, no, so they're operating expenses. Keeping the two apart is what makes your gross profit number mean something, which we'll get to below.</p>
HTML,
    ],

    [
      'h2' => 'Why timing matters: bought in December, sold whenever',
      'anchor' => 'timing',
      'html' => <<<'HTML'
<p>The timing rule is where COGS stops being obvious, so here's a concrete example.</p>
<p>Suppose you spend $3,000 on stock in December, getting ready for a busy season. By December 31 you've sold half of it. What's your COGS for December?</p>
<p>It's $1,500, not $3,000. Only the half that sold counts. The other $1,500 of stock is still yours: it sits on your books as <strong>inventory, an asset</strong>, the same way the cash it used to be was an asset. When those items sell in January and February, their cost becomes COGS in January and February.</p>
{{illustration:inventory-boxes}}
<p>This feels like a technicality until you see what it protects you from. If you counted the whole $3,000 as a December cost, December would look like a terrible month, maybe even a loss, when actually you just moved money from your bank account onto your shelves. Then January would look artificially amazing, with sales coming in and apparently no product costs at all. Your books would swing between fake bad months and fake good months, and you'd never know which products or seasons actually earn.</p>
<p>Matching each sale with the cost of the thing sold keeps every month honest. It's also not optional in most places: most tax systems require businesses that sell goods to account for stock this way rather than deducting purchases the moment they're made. The exact rules vary by country, and some have simplified schemes for very small businesses, so check with your local tax authority or an accountant for the specifics where you are.</p>
HTML,
    ],

    [
      'h2' => 'The formula: opening inventory + purchases - closing inventory',
      'anchor' => 'the-formula',
      'html' => <<<'HTML'
<p>You might wonder how anyone works out COGS without tracking every single item out the door. The standard formula gets there from just three numbers:</p>
<p><strong>COGS = opening inventory + purchases during the period - closing inventory</strong></p>
<p>In words: start with the value of the stock you had at the beginning of the period, add everything you bought during the period, then subtract the value of what's still on the shelf at the end. Whatever's unaccounted for must have gone out the door, and its cost is your COGS.</p>
<p>Worked example for one year:</p>
<ul>
<li>Stock on hand January 1: <strong>$2,000</strong> (at what it cost you, not its selling price)</li>
<li>Stock bought during the year: <strong>$10,000</strong></li>
<li>Stock on hand December 31, from a count: <strong>$3,500</strong></li>
</ul>
<p>COGS = $2,000 + $10,000 - $3,500 = <strong>$8,500</strong>.</p>
<p>Notice what the formula quietly handles for you. You didn't need a running log of every sale's cost; you needed a count at each end and a total of your purchases, which your receipts already give you. Notice also that this year's closing inventory ($3,500) becomes next year's opening inventory, so the numbers chain together from period to period.</p>
<p>One honest caveat: because the formula works by subtraction, anything that leaves your stock without being sold, items you gave away, kept for yourself, or that were damaged, ends up inside the COGS number unless you adjust for it. For a small business that's usually fine at a rough level, but it's worth knowing the number includes shrinkage, not just sales.</p>
HTML,
    ],

    [
      'h2' => 'Gross profit: the number COGS unlocks',
      'anchor' => 'gross-profit',
      'html' => <<<'HTML'
<p>The reason to care about COGS isn't the number itself, it's what you can do with it:</p>
<p><strong>Gross profit = sales - COGS</strong></p>
<p>Gross profit answers a question your sales total can't: do your products actually earn money before the rest of the business takes its share? Two shops can both sell $100,000 a year and be in completely different situations. One buys its stock for $40,000 (gross profit $60,000, a 60% margin) and has plenty left to cover rent, ads, and the owner's pay. The other pays $85,000 for its stock (gross profit $15,000, a 15% margin) and will struggle to cover anything, no matter how busy it looks.</p>
{{illustration:coins}}
<p>Without a real COGS number, you can't see which shop you are. With it, you can go further and check gross margin per product, which is where the useful surprises live: the popular item that barely clears its cost, the quiet one that's actually your best earner, the product line whose supplier price has crept up until the margin is gone. Those are pricing and stocking decisions you can only make when the cost side is tracked.</p>
<p>Gross profit is the first checkpoint, not the finish line. Operating expenses still come out of it before you get to the profit you keep. The difference between the two numbers, and why both matter, is covered in our guide to <a href="/gross-profit-vs-net-profit/">gross profit vs net profit</a>.</p>
HTML,
    ],

    [
      'h2' => 'COGS for resellers, makers, and service businesses',
      'anchor' => 'business-types',
      'html' => <<<'HTML'
<p>The concept is the same everywhere, but what goes into the number depends on what you sell.</p>
<p><strong>If you resell products</strong> (an online shop, a retail store, a flipper): your COGS is what you paid for each item, plus the cost of getting it to you, shipping, duties, and similar. This is the simplest case. Buy for $4, sell for $15, COGS is $4. The main work is keeping good records of your purchases and doing a stock count now and then. Selling across several platforms adds its own wrinkles, which our guide to <a href="/bookkeeping-for-online-sellers/">bookkeeping for online sellers</a> walks through.</p>
<p><strong>If you make what you sell</strong> (handmade goods, food, furniture): your COGS is the materials that went into each finished item, and that takes one extra step, because a bag of clay or a bolt of fabric spreads across many products. The fix is to cost each product once: work out how much wax, wick, jar, and label goes into one candle, and you have a per-item cost you can reuse. Unused materials stay as inventory until they become something that sells, exactly like a reseller's unsold stock. There's a fuller walkthrough of the maker version, recipes, material costs, and marketplace fees included, in our guide to <a href="/bookkeeping-for-etsy-sellers/">bookkeeping for Etsy sellers</a>.</p>
<p><strong>If you sell services</strong> (design, consulting, trades, freelancing): you usually have little or no COGS, because there's no stock. Your costs are mostly operating expenses, and your gross margin is naturally high, with your time as the real constraint. The exception is when a job includes goods: a contractor who buys materials for a project, or a designer who prints and ships a product, has direct costs that behave like COGS for that job. If that's occasional, tracking those costs against the specific job gets you the same insight without a full inventory system.</p>
HTML,
    ],

    [
      'h2' => 'Two simple ways to track it',
      'anchor' => 'how-to-track',
      'html' => <<<'HTML'
<p>You don't need warehouse software to get COGS right. Small businesses generally land on one of two approaches.</p>
<p><strong>The periodic count.</strong> Keep receipts for everything you buy for resale or production, then count and value your stock at the end of each period (year-end at minimum, quarterly or monthly if you want sharper numbers). Feed the three numbers into the formula from earlier and out comes COGS. This is low-effort and completely fine for a small, simple product range. Its weakness is that you only learn your margin after the fact, in bulk, and you can't see which individual products are earning.</p>
<p><strong>Per-item tracking.</strong> Record the cost of each product when it comes in, and let each sale carry its cost out with it. This gives you a running COGS figure and, more usefully, gross margin per product, so you can spot the strong and weak earners while there's still time to act. It's more setup, but software does the heavy lifting: this is exactly what an inventory feature in an accounting app is for. Our guide to <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> covers how to choose between these approaches and when a spreadsheet stops being enough.</p>
<p>Argo Books takes the per-item route: you record what each product costs you, it tracks your stock, and each sale is matched with its cost automatically, so your gross margin per item and overall is just there in your reports instead of being a year-end math project. Whichever method you pick, the habit that matters is the same: keep every purchase receipt, know roughly what stock you're holding, and check the numbers on a schedule instead of once a year in a panic.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books tracks your inventory and cost of goods sold per item, so every sale carries its real cost and you can see your gross margin without a spreadsheet.',
  'tool_callout_cta' => 'See inventory and COGS in Argo Books',
  'tool_callout_url' => '/features/inventory-management/',

  'faqs' => [
    [
      'q' => 'Is cost of goods sold the same as expenses?',
      'a' => 'No, and keeping them apart is the whole point. COGS covers only direct costs: what you paid for the stock or materials that went into the items you actually sold. Operating expenses cover everything else the business spends, like rent, software, advertising, and insurance. Both reduce your final profit, but they answer different questions. Sales minus COGS gives you gross profit, which tells you whether your products earn money at all. Subtracting operating expenses from that gives net profit, what you actually keep. If you lump everything together you can\'t tell whether a weak month came from thin product margins or from overhead creeping up, and those two problems have completely different fixes.',
    ],
    [
      'q' => 'Do I count stock as a cost when I buy it or when I sell it?',
      'a' => 'When it sells. Buying stock moves money from your bank account into inventory, which is an asset you own, not a cost you\'ve incurred. The cost lands in your books, as COGS, in the period when each item actually sells. So if you buy $3,000 of stock in December and sell half by year-end, your December COGS is $1,500 and the remaining $1,500 stays on your books as inventory until it sells. Most tax systems require goods businesses to account for stock this way rather than deducting purchases immediately, though some countries have simplified rules for very small businesses, so it\'s worth confirming the specifics with your local tax authority or an accountant.',
    ],
    [
      'q' => 'How do I calculate COGS if I don\'t track every item?',
      'a' => 'Use the standard formula: opening inventory plus purchases during the period, minus closing inventory. You need three numbers: the value of your stock at the start of the period (at what it cost you), the total you spent on stock or materials during the period (from your receipts), and the value of what\'s left at the end (from a physical count). Whatever\'s missing must have gone out the door, and that\'s your COGS. For example, $2,000 opening stock plus $10,000 of purchases minus $3,500 counted at the end gives $8,500 of COGS. One thing to know: items given away, kept, or damaged also end up in that number unless you adjust for them separately.',
    ],
    [
      'q' => 'Does a service business have cost of goods sold?',
      'a' => 'Usually little or none, because there\'s no stock: a consultant or designer sells time and skill, and their costs are mostly operating expenses like software and insurance. That\'s why service businesses tend to show high gross margins. The exception is when a job includes physical goods or job-specific costs, like a contractor buying lumber and fixtures for a renovation, or a photographer printing albums for a client. Those direct costs behave like COGS for that job, and tracking them against the specific project tells you whether the job itself was profitable. If goods are only an occasional part of your work, per-job cost tracking gives you the insight without needing a full inventory system.',
    ],
    [
      'q' => 'Does my own time count in cost of goods sold?',
      'a' => 'For a solo owner, generally no. If you make products yourself, the materials go into COGS but your own hours don\'t; your pay comes out of the profit the business makes. That can make margins look better than they feel, which is why it\'s smart to check that your gross profit leaves enough to actually pay you, not just cover the bills. It\'s different when you pay someone else to produce the goods: wages for direct production work can belong in COGS, since they\'re a direct cost of making the items. Where exactly that line sits for tax purposes varies by country and business structure, so if payroll is involved, confirm the treatment with an accountant.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'designer',
    'contractor',
  ],

  'related_article_slugs' => [
    'inventory-tracking-for-small-businesses',
    'gross-profit-vs-net-profit',
    'bookkeeping-for-online-sellers',
    'bookkeeping-for-etsy-sellers',
  ],
];
