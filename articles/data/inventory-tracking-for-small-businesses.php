<?php
// articles/data/inventory-tracking-for-small-businesses.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'inventory-tracking-for-small-businesses',

  'h1' => 'Inventory tracking for small businesses: a simple guide',

  'meta_title' => 'Inventory Tracking for Small Businesses: a Guide | Argo Books',

  'meta_description' => 'A plain guide to inventory for small businesses: cost of goods sold, stock value, reorder points, shrinkage, and what it all means for your taxes.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 30,

  'published' => '2026-06-02',

  'updated' => '2026-06-02',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>If you sell physical products, your inventory is probably the biggest number in your business and the one most likely to be wrong in your books. The stock on your shelves is money sitting still: cash you spent that hasn't turned back into cash yet. Track it loosely and you end up with a profit figure that isn't real, a tax bill built on guesswork, and no idea when you're about to run out of your best seller.</p>
<p>This guide explains inventory bookkeeping in plain language, for resellers, makers, local wholesalers, and any small product business doing its own books. It covers why inventory matters to your profit and your taxes, how to track stock and know when to reorder, what cost of goods sold actually means, where stock quietly disappears, and the honest line between when a spreadsheet is fine and when software starts to pay for itself. No jargon, no accounting degree required.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why inventory matters to your books',
      'anchor' => 'why-it-matters',
      'html' => <<<'HTML'
<p>Here is the idea that surprises people new to selling products: the stock you buy is not an expense the moment you buy it. When you spend $1,000 on goods to resell, you haven't lost $1,000, you have swapped $1,000 of cash for $1,000 of inventory. It only becomes an expense when you actually sell it. Until then it sits on your books as something of value that you own.</p>
<p>This matters because getting it wrong distorts your profit in both directions. If you treat a big stock purchase as an immediate expense, a month where you stocked up looks like a loss even though you simply converted cash into goods. Then the month you sell that stock looks wildly profitable because the cost seems to have vanished. Neither figure is real, and you cannot run a business or estimate a tax bill on numbers that swing around for reasons that have nothing to do with how well you are actually selling.</p>
<p>Two numbers fix this and they run through the rest of this guide. The first is the cost of the goods you actually sold in a period, which is the real expense against your sales. The second is the value of the stock you still hold at the end of a period, which is the asset still sitting on your shelves. Get those two right and your profit becomes honest and your taxes become straightforward. Get them wrong and everything downstream is guesswork.</p>
HTML,
    ],

    [
      'h2' => 'Cost of goods sold, explained simply',
      'anchor' => 'cogs',
      'html' => <<<'HTML'
<p>Cost of goods sold, usually shortened to COGS, is just the cost of the stock you actually sold during a period. It is the single most important inventory number in your books, because it is the real expense you subtract from your sales to find your true profit on products.</p>
<p>The plain-language way to work it out over a period is: take the value of the stock you started with, add what you bought during the period, then subtract the value of the stock you have left at the end. What's missing is what you sold, and its cost is your COGS. So if you began the month with $2,000 of stock, bought another $1,500, and ended with $1,800 still on the shelf, then $1,700 of goods left as sales, and that $1,700 is your cost of goods sold for the month.</p>
<p>Why this is worth understanding rather than ignoring: your real profit on products is your sales minus COGS, not your sales minus everything you spent on stock. A business can have a great sales month and a fat bank balance simply because it didn't restock, or a scary-looking month simply because it bought ahead. COGS strips that noise out and shows what you actually earned on the things you sold. It is also the figure tax authorities expect, because you are taxed on profit, and profit needs the real cost of what sold, not the cash you happened to spend on stock that month.</p>
HTML,
    ],

    [
      'h2' => 'Track stock levels and reorder points',
      'anchor' => 'stock-levels',
      'html' => <<<'HTML'
<p>Beyond the accounting, there is the day-to-day question every product business has to answer: how much of each thing do I have, and when do I buy more? Getting this wrong costs money in two directions. Run out of a popular item and you lose sales you were ready to make. Overstock a slow item and you tie up cash in goods that just sit there, sometimes until they expire or go out of style.</p>
<p>The core habit is knowing your count for each product and updating it as stock comes in and goes out. From there, the tool that prevents most stockouts is a reorder point: a stock level for each item that, when you hit it, tells you it's time to buy more. You set it based on how fast the item sells and how long a new order takes to arrive. A fast seller that takes two weeks to restock needs a high reorder point so you don't run dry while waiting. A slow item that arrives next day can sit near zero before you reorder.</p>
<p>This is also where you learn which products carry your business. Just as a service business benefits from tracking earnings per job, a product business benefits from seeing which items sell quickly and earn well, and which gather dust. That knowledge tells you what to reorder aggressively, what to discount to clear, and what to stop buying altogether. You cannot see it from a single lump inventory total. It only appears when you track stock item by item.</p>
HTML,
    ],

    [
      'h2' => 'Stock value at year-end',
      'anchor' => 'year-end-value',
      'html' => <<<'HTML'
<p>At the end of your tax year, most product businesses have to report the value of the stock still on hand. This is not bureaucratic box-ticking, it is the other half of getting your profit right. Remember that stock only becomes an expense when it sells. So the goods still sitting on your shelves at year-end are not yet an expense, they are an asset, and that value has to be counted and carried forward.</p>
<p>The practical job is a stock count, often called taking inventory: physically counting what you have at year-end and valuing it, usually at what you paid for it. This count is what lets you finish the COGS calculation from earlier, because the closing stock value is one of its three inputs. It is also a moment of truth, because the count almost never matches what you thought you had, and the gap tells you about loss, miscounting, or sales you didn't record, which leads straight into the next section.</p>
<p>The way to make year-end painless is to keep stock records current through the year so the count is a confirmation rather than a panicked reconstruction. A business that has tracked stock in and out all year does a quick count, fixes small differences, and hands the closing value to its accountant. A business that tracked nothing spends the holidays counting boxes and guessing at what things cost. The habit, as everywhere in bookkeeping, is to keep it current rather than rebuild it at the end.</p>
HTML,
    ],

    [
      'h2' => 'Shrinkage: where stock quietly disappears',
      'anchor' => 'shrinkage',
      'html' => <<<'HTML'
<p>Shrinkage is the industry word for stock that you paid for but can no longer sell, and it shows up as the gap between what your records say you should have and what your count actually finds. Every product business has some. The point of tracking it is that unexplained shrinkage is both a real cost and a warning sign worth understanding rather than shrugging off.</p>
<ul>
<li><strong>Damage and spoilage.</strong> Goods that break, expire, or pass their sell-by date. Real losses, and in most tax systems deductible, but only if you have a record of what was lost.</li>
<li><strong>Theft and loss.</strong> Items that walk out the door or simply go missing. Painful, normal at some level, and worth measuring so you know if it is creeping up.</li>
<li><strong>Counting and recording slips.</strong> Sometimes the stock is fine and the records are wrong: a sale not entered, a delivery counted twice, a return never logged. This kind of shrinkage is fixable at the source once you notice the gap.</li>
</ul>
<p>The reason to measure shrinkage rather than ignore it is that the number tells you a story. A small, steady amount is the normal cost of doing business. A number that suddenly jumps points at a new problem: a supplier shorting deliveries, a process that keeps missing sales, or genuine theft. You can only spot the change if you were tracking the baseline. And the losses themselves, once recorded, are generally deductible, so good shrinkage records turn an unavoidable cost into a legitimate deduction instead of a silent hole in your profit.</p>
HTML,
    ],

    [
      'h2' => 'What inventory means for your taxes',
      'anchor' => 'taxes',
      'html' => <<<'HTML'
<p>Pulling the threads together, here is how inventory touches your tax bill, in plain terms. The exact rules and methods vary by country, so treat this as the shape of it and confirm specifics with your accountant or local tax authority.</p>
<ul>
<li><strong>You are taxed on profit, and profit needs COGS.</strong> Your taxable profit on products is your sales minus the cost of the goods that actually sold, not minus everything you spent on stock. This is why the COGS calculation matters: get it wrong and you either overpay tax on profit you didn't make, or understate profit in a way that catches up with you later.</li>
<li><strong>Closing stock is an asset, not an expense.</strong> The value of stock on hand at year-end stays on your books as something you own and carries into the next year. It has not been deducted yet because it has not sold yet. Counting it accurately is what keeps your profit honest across the year boundary.</li>
<li><strong>Recorded losses are deductible.</strong> Damaged, spoiled, and stolen stock is generally a deductible loss, but only with a record. Shrinkage you tracked is a deduction. Shrinkage you didn't is just missing money.</li>
</ul>
<p>The encouraging part is that none of this requires you to be a tax expert. It requires the records: stock in, stock out, what you paid, what you have left, and what was lost. Keep those through the year and your accountant has everything needed to get the tax right. The businesses that struggle at tax time are the ones reconstructing a year of stock movements from memory and bank statements in April.</p>
HTML,
    ],

    [
      'h2' => 'Spreadsheet or software: an honest answer',
      'anchor' => 'spreadsheet-vs-software',
      'html' => <<<'HTML'
<p>You do not need inventory software to keep good books, and it would be dishonest to pretend otherwise. Many small product businesses run perfectly well on a spreadsheet for years. Here is a straight read on where the spreadsheet works and where it starts to cost you more than it saves.</p>
<p>A spreadsheet is genuinely enough when your product range is manageable and your sales volume is steady rather than frantic. A sheet listing each product, its cost, its current count, and a reorder point, updated as stock comes in and goes out, gives you almost everything in this guide: per-item stock levels, reorder warnings if you flag low counts, the inputs for COGS, and a year-end value to count against. Plenty of resellers and makers run exactly this and never need more. It costs nothing but the discipline to keep it current.</p>
<p>The spreadsheet starts to strain when the moving parts multiply: hundreds of products, fast turnover, stock counts that drift out of step with reality between updates, and the manual work of recalculating COGS and chasing reorder points by hand every month. The bigger risk is that the spreadsheet and your actual accounting are two separate things you have to keep in agreement, and as volume grows they drift apart. That is the point where software earns its keep, by holding the stock counts and the accounting in one place so a sale lowers the stock and records the income and the cost of goods in a single step.</p>
<p>Argo Books is one option there. It keeps inventory and accounting together in one app, so your stock levels, your cost of goods sold, and your books stay in step without you bridging two systems by hand, and it can scan supplier receipts so what you paid for stock lands in the right place automatically. It is worth a look once you have outgrown the spreadsheet, and you can weigh it honestly against the bigger names in our <a href="/best-quickbooks-alternatives/">guide to QuickBooks alternatives</a>, since inventory is exactly where the cheap tools tend to fall short and the expensive ones tend to overcharge. As with everything here, start with whatever you will actually keep up with. A simple system used weekly beats a powerful one you abandon.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 6,

  'tool_callout_text' => 'Argo Books keeps inventory and accounting in one place, so your stock counts, cost of goods sold, and books stay in step automatically.',
  'tool_callout_cta' => 'See Argo Books inventory tracking',
  'tool_callout_url' => '/features/inventory-management/',

  'faqs' => [
    [
      'q' => 'What is cost of goods sold (COGS) in simple terms?',
      'a' => 'COGS is the cost of the stock you actually sold during a period. It is the real expense you subtract from your sales to find your true profit on products. The plain way to work it out is to take the value of the stock you started with, add what you bought during the period, then subtract the value of the stock you have left at the end. What is missing is what you sold, and its cost is your COGS. For example, starting with $2,000 of stock, buying $1,500 more, and ending with $1,800 means $1,700 of goods left as sales, so COGS is $1,700. It matters because you are taxed on profit, and real profit needs the cost of what sold, not just the cash you spent on stock.',
    ],
    [
      'q' => 'Why isn\'t buying stock counted as an expense right away?',
      'a' => 'Because when you buy stock you have not spent the money so much as converted it: you swapped cash for goods of equal value that you now own. Nothing has been used up yet, so there is nothing to expense yet. The cost becomes an expense at the moment the item sells, which is when it leaves your business and turns back into cash through a sale. If you expensed stock the day you bought it, your profit would swing wildly for reasons unrelated to how well you sell, with stock-up months looking like losses and selling months looking artificially profitable. Treating stock as an asset until it sells is what keeps your profit figure honest and your taxes correct.',
    ],
    [
      'q' => 'How do I know when to reorder stock?',
      'a' => 'Set a reorder point for each product: a stock level that, when you reach it, tells you it is time to buy more. You base it on how fast the item sells and how long a new order takes to arrive. A fast seller with a two-week restock time needs a high reorder point so you do not run out while waiting for the delivery. A slow item that arrives next day can run close to zero before you reorder. The habit underneath it is keeping an accurate count of each product as stock comes in and goes out, because a reorder point only works if your counts are current. This is also where you learn which items sell fast and earn well, which tells you what to reorder aggressively and what to stop buying.',
    ],
    [
      'q' => 'Do I need inventory software, or is a spreadsheet enough?',
      'a' => 'A spreadsheet is genuinely enough to start, especially with a manageable product range and steady sales. A sheet with each product, its cost, its current count, and a reorder point, kept current as stock moves, gives you per-item levels, reorder warnings, the inputs for cost of goods sold, and a year-end value. Many resellers and makers run this way for years at no cost. Software earns its place as the volume grows: hundreds of products, fast turnover, and the work of keeping a separate spreadsheet in step with your actual accounting are where it starts to pay off. A tool that keeps stock and books together so one sale updates both at once then saves real time and prevents the two systems from drifting apart. Start with whatever you will keep up with weekly.',
    ],
    [
      'q' => 'Is this article trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned, and yes, this is the Argo Books site, so read it knowing that. But the advice in this guide does not depend on the tool. Understanding cost of goods sold, treating stock as an asset until it sells, setting reorder points, counting stock at year-end, and tracking shrinkage are ideas that work with a spreadsheet, a notebook, or any accounting app. If you take nothing but those ideas and never look at Argo Books, the guide did its job. We mention the tool once, in a callout you can ignore, because keeping inventory and accounting in one place is genuinely useful once you outgrow a spreadsheet, and inventory is exactly where cheaper tools tend to fall short, so pointing that out honestly is fair.',
    ],
  ],

  'related_niche_slugs' => [
    'designer',
    'photographer',
    'consultant',
  ],

  'related_article_slugs' => [
    'best-quickbooks-alternatives',
    'bookkeeping-for-contractors',
    'best-free-ai-receipt-scanner',
  ],
];
