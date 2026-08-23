<?php
// articles/data/what-is-a-balance-sheet.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-is-a-balance-sheet',

  'h1' => 'What is a balance sheet? A plain-English guide',

  'meta_title' => 'What Is a Balance Sheet? A Plain-English Guide | Argo Books',

  'meta_description' => "A balance sheet is a snapshot of what your business owns and owes. Here's the equation, a worked example, and why it always balances.",

  'schema_type' => 'Article',

  'category' => 'bookkeeping',
  'hub_weight' => 60,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A balance sheet is a snapshot of your business on one particular day. It answers two plain questions: what does the business own, and what does it owe? Everything the business owns is on one side, everything it owes is on the other, and the gap between them is your stake in it. That's the whole idea. No accounting degree needed to read one.</p>
<p>The word "balance" isn't a suggestion. The two sides always match, down to the penny, and once you see why, the rest of the document stops feeling like a wall of jargon. This guide walks through the equation behind it, breaks down each part with real small-business examples, builds a worked mini balance sheet you can copy, and shows how it's different from the profit and loss statement people usually mix it up with.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The one equation behind every balance sheet',
      'anchor' => 'the-equation',
      'html' => <<<'HTML'
<p>Every balance sheet ever written rests on a single line:</p>
<p><strong>Assets = Liabilities + Equity</strong></p>
<p>In plain words: what you own equals what you owe plus what's actually yours. Assets are the things the business has. Liabilities are the claims other people have on those things, the money you owe to banks, suppliers, and the tax office. Equity is what's left for you once those claims are settled.</p>
<p>Here's why it can never be out of balance. Every dollar of stuff the business owns had to come from somewhere. It was either borrowed (a liability) or it came from you and the profits you left in the business (equity). There's no third source. If you buy a $2,000 laptop, either you paid cash that was already yours (equity funded it) or you put it on a card (a liability funded it), or some mix of the two. The asset going up is always matched, dollar for dollar, by a liability going up or equity going up. That's not a coincidence you have to maintain by hand. It's baked into how the two sides are defined.</p>
{{illustration:balance-sheet}}
<p>A quick way to feel it: rearrange the equation to <strong>Equity = Assets &minus; Liabilities</strong>. Equity isn't a number you choose. It's whatever is left after you subtract what you owe from what you own. So the moment you know your assets and your liabilities, equity is fixed, and the sheet balances by definition. If you ever add it up and it doesn't balance, a number is missing or double-counted somewhere, not the math failing.</p>
HTML,
    ],

    [
      'h2' => 'Assets: the things your business owns',
      'anchor' => 'assets',
      'html' => <<<'HTML'
<p>Assets are anything the business owns that has value. For most small businesses they fall into a handful of everyday buckets:</p>
<ul>
<li><strong>Cash.</strong> The money in your business bank account, plus any petty cash. This is the most liquid asset there is, cash is already cash.</li>
<li><strong>Money customers owe you.</strong> If you've sent invoices that haven't been paid yet, that money is still yours to collect, so it's an asset. Bookkeepers call this "accounts receivable," but it just means unpaid invoices in your favour.</li>
<li><strong>Stock or inventory.</strong> If you sell physical products, the goods sitting on your shelves waiting to be sold are an asset. A shop with $5,000 of unsold candles owns $5,000 of stock.</li>
<li><strong>Equipment.</strong> Your laptop, your van, your tools, your camera, your espresso machine. Anything durable you use to run the business. These are longer-term assets, since you won't turn them back into cash any time soon.</li>
</ul>
<p>Assets usually get split into two groups. <strong>Current assets</strong> are things you expect to turn into cash within about a year: your bank balance, unpaid invoices, and stock. <strong>Fixed assets</strong> (also called non-current assets) are the longer-term things like equipment and vehicles that stick around for years. The split matters because it tells a reader how much of what you own could actually be spent soon, versus how much is tied up in gear.</p>
<p>A plumber's balance sheet might list cash in the bank, a few unpaid invoices from last month's jobs, a stock of fittings and copper pipe in the van, and the van itself. A freelance designer's might be almost all cash and unpaid invoices, with a laptop as the only real fixed asset. Same document, very different shapes, and both are normal.</p>
HTML,
    ],

    [
      'h2' => 'Liabilities: what your business owes',
      'anchor' => 'liabilities',
      'html' => <<<'HTML'
<p>Liabilities are the flip side: money the business owes to someone else. If a person, a bank, or a government has a claim on your money, it's a liability. The common ones:</p>
<ul>
<li><strong>Loans.</strong> A bank loan, a line of credit, equipment financing, or a chunk of your credit-card balance you haven't cleared. Whatever the principal you still owe is, that's the liability.</li>
<li><strong>Unpaid bills.</strong> Suppliers you've bought from on terms but haven't paid yet. If your fittings supplier lets you pay at the end of the month, the amount you've run up so far is a liability. Bookkeepers call this "accounts payable," the mirror image of the unpaid invoices on the asset side.</li>
<li><strong>Tax you owe.</strong> Sales tax you've collected from customers but not yet handed to the government, or income tax you've set aside but not yet paid. That money passed through your account, but it was never yours to keep, so it sits as a liability until you remit it.</li>
</ul>
<p>Like assets, liabilities split into <strong>current</strong> (due within a year, such as this month's supplier bills and the tax you owe next quarter) and <strong>long-term</strong> (due further out, such as the remaining years on a van loan). Grouping them this way shows a reader how much is coming due soon versus how much you have years to pay off.</p>
<p>One point that trips people up: the sales tax you collect on invoices is a liability, not income. When a customer pays you $110 on a $100 sale with 10% tax, only $100 is yours. The $10 belongs to the tax office and is a claim against you until you pay it over. Treating that $10 as if it were profit is one of the fastest ways to end up short at tax time.</p>
HTML,
    ],

    [
      'h2' => "Equity: what's left over, your stake",
      'anchor' => 'equity',
      'html' => <<<'HTML'
<p>Equity is the part that's genuinely yours. Take everything the business owns, subtract everything it owes, and whatever remains is equity. If you sold every asset at book value and paid off every debt tomorrow, equity is the pile of money you'd walk away with.</p>
<p>For a small business, equity usually comes from two places. First, the money you put in to get started, sometimes called owner's capital or contributed capital. Second, the profits you've earned over time and chosen to leave in the business instead of taking out, often called retained earnings. Money you draw out for yourself reduces equity; profit you leave in grows it.</p>
<p>Because equity is a leftover, it moves on its own as the other two sides shift. Pay down a loan using cash, and both an asset (cash) and a liability (the loan) shrink by the same amount, so equity doesn't change. Earn a profit and keep it in the bank, and cash goes up with no matching debt, so equity goes up. Equity going up over time is a plain sign the business is building value. Equity that's negative, meaning you owe more than you own, is a warning worth taking seriously.</p>
HTML,
    ],

    [
      'h2' => 'A worked mini balance sheet',
      'anchor' => 'worked-example',
      'html' => <<<'HTML'
<p>Numbers make this click faster than definitions. Meet Sam, who runs a small candle shop as a sole trader. Here's Sam's balance sheet as of 30 June:</p>
<table>
<thead>
<tr><th>Item</th><th>Amount</th></tr>
</thead>
<tbody>
<tr><td><strong>Assets</strong></td><td></td></tr>
<tr><td>Cash in the business account</td><td>$8,000</td></tr>
<tr><td>Unpaid invoices from wholesale customers</td><td>$2,500</td></tr>
<tr><td>Stock (unsold candles and raw wax)</td><td>$5,000</td></tr>
<tr><td>Equipment (pouring gear, laptop)</td><td>$3,500</td></tr>
<tr><td><strong>Total assets</strong></td><td><strong>$19,000</strong></td></tr>
<tr><td></td><td></td></tr>
<tr><td><strong>Liabilities</strong></td><td></td></tr>
<tr><td>Small business loan (remaining)</td><td>$6,000</td></tr>
<tr><td>Unpaid supplier bills</td><td>$1,200</td></tr>
<tr><td>Sales tax collected, not yet remitted</td><td>$800</td></tr>
<tr><td><strong>Total liabilities</strong></td><td><strong>$8,000</strong></td></tr>
<tr><td></td><td></td></tr>
<tr><td><strong>Equity</strong></td><td></td></tr>
<tr><td>Owner's stake (Total assets &minus; Total liabilities)</td><td><strong>$11,000</strong></td></tr>
<tr><td></td><td></td></tr>
<tr><td><strong>Liabilities + Equity</strong></td><td><strong>$19,000</strong></td></tr>
</tbody>
</table>
<p>Read it top to bottom. Sam owns $19,000 of stuff. Of that, $8,000 is spoken for by the bank, suppliers, and the tax office. The remaining $11,000 is Sam's actual stake in the business. And notice the last line: liabilities plus equity comes to $19,000, exactly matching total assets. That's the equation holding. It isn't a lucky round number, it's equity being calculated as the leftover, which forces the two sides to agree.</p>
<p>If Sam used $6,000 of that cash to pay off the loan tomorrow, cash would drop to $2,000 and the loan would vanish. Total assets fall to $13,000, total liabilities fall to $2,000, and equity stays at $11,000. The sheet still balances. That's the whole trick: any real transaction touches at least two lines, and the two sides move together.</p>
HTML,
    ],

    [
      'h2' => 'What a balance sheet tells you, and how it differs from a P&L',
      'anchor' => 'what-it-tells-you',
      'html' => <<<'HTML'
<p>A balance sheet answers questions a bank statement never can:</p>
<ul>
<li><strong>What's the business actually worth?</strong> Equity is your net worth in the business, in one number. Watch it climb quarter over quarter and you know you're building something real.</li>
<li><strong>Can you cover what you owe?</strong> Compare current assets to current liabilities. If Sam's cash, unpaid invoices, and stock ($15,500 of current assets) comfortably beat the bills and tax due within the year ($2,000), the business can pay its way. If current liabilities were larger than current assets, that's a cash-flow squeeze coming, and worth spotting early.</li>
<li><strong>How much of the business is borrowed?</strong> Stack total liabilities against equity. A business funded mostly by debt is riskier than one funded mostly by owner's stake, and the balance sheet shows the split at a glance.</li>
</ul>
<p>The document people constantly confuse it with is the profit and loss statement (the P&L, also called the income statement). Here's the clean line between them: <strong>a balance sheet is a snapshot at one moment; a P&L covers a stretch of time.</strong></p>
{{illustration:report-statement}}
<p>A P&L says "between 1 January and 31 December, you brought in $60,000, spent $40,000, and kept $20,000 in profit." It's a video of a period. A balance sheet says "as of 31 December, here's exactly what you own and owe." It's a photo of a day. The two connect: the profit a P&L reports for the year flows into equity on the balance sheet as retained earnings. Earn $20,000 and leave it in the business, and equity rises by $20,000. You want both. The P&L tells you whether you're making money, and the balance sheet tells you what you've built up as a result. For a deeper walk through the P&L, see <a href="/what-is-a-profit-and-loss-statement/">what is a profit and loss statement</a>.</p>
HTML,
    ],

    [
      'h2' => 'Getting a balance sheet without doing it by hand',
      'anchor' => 'argo-books',
      'html' => <<<'HTML'
<p>Building a balance sheet by hand means gathering your bank balance, tallying unpaid invoices, valuing your stock, listing your loans and bills, and working out the tax you still owe, all as of one specific date. Do that in a spreadsheet and it's doable, but it's fiddly, and it goes out of date the moment you make another sale.</p>
<p>If you already track your income and expenses in <a href="/">Argo Books</a>, you don't have to build it at all. The free Report Builder assembles a Balance Sheet straight from the data you've already entered, alongside an income statement and tax summaries, and exports the lot as a clean branded PDF. Pick a date, and it works out what you owned and owed as of that day from your records.</p>
<p>Two things worth knowing about how it treats stock. Unsold inventory shows up as a <strong>current asset</strong> on the Balance Sheet, valued at each item's current unit cost, worked out from the stock movements you've recorded up to the report date. And because buying stock is already recorded as an expense when you purchase it, having it appear here as an asset doesn't double-count or inflate your profit. It just reflects that unsold stock is still something you own. If you sell physical products, that's the piece a plain spreadsheet almost always gets wrong, and it's handled for you.</p>
<p>One more detail so the numbers make sense: Argo Books runs its reports, including the Balance Sheet, on an accrual basis, meaning all your invoiced revenue counts whether or not it's been paid yet. That's the right basis for a statement you'd hand to an accountant or a bank, and it's why unpaid invoices show up as an asset. The report is free on every plan, so there's no reason to hand-build one in a spreadsheet.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'Argo Books builds a Balance Sheet, income statement, and tax summaries from your data and exports them as a clean PDF, free on every plan.',
  'tool_callout_cta' => 'See the Report Builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'What are the three parts of a balance sheet?',
      'a' => 'Assets, liabilities, and equity. Assets are everything the business owns (cash, unpaid invoices, stock, equipment). Liabilities are everything it owes (loans, unpaid supplier bills, tax due). Equity is what is left for the owner once the liabilities are subtracted from the assets. They always fit the equation Assets = Liabilities + Equity.',
    ],
    [
      'q' => 'Why does a balance sheet always balance?',
      'a' => 'Because every dollar of stuff the business owns had to be funded somehow, either by borrowing (a liability) or by the owner and retained profits (equity). There is no third source. So total assets always equal total liabilities plus equity by definition. Equity itself is calculated as assets minus liabilities, which forces the two sides to match. If yours does not balance, a figure has been missed or double-counted, not the math failing.',
    ],
    [
      'q' => "What's the difference between a balance sheet and a P&L?",
      'a' => 'A balance sheet is a snapshot at a single moment: what you own and owe as of one date. A profit and loss statement (P&L, or income statement) covers a period of time and shows revenue minus expenses to arrive at profit for that stretch. The two connect, because the profit a P&L reports flows into equity on the balance sheet as retained earnings. You want both: the P&L tells you if you are making money, the balance sheet tells you what you have built up.',
    ],
    [
      'q' => 'Do I need a balance sheet as a freelancer?',
      'a' => 'Often not for day-to-day work, since a freelancer with little more than a bank balance and a laptop can run on income and expense tracking alone. But a balance sheet still helps, and you may need one when applying for a loan or a mortgage, bringing on an accountant, or checking whether unpaid invoices are piling up faster than you can collect. Because it is quick to generate from data you already keep, there is little downside to having one on hand.',
    ],
    [
      'q' => 'Where does inventory show up on a balance sheet?',
      'a' => 'Unsold inventory appears as a current asset, because it is something you own that you expect to turn into cash within about a year by selling it. It is usually valued at what the stock cost you. In Argo Books, the Balance Sheet lists stock on hand as a current asset valued at each item current unit cost, and because buying stock was already recorded as an expense at purchase, showing it here does not inflate your profit.',
    ],
    [
      'q' => 'How often should I look at my balance sheet?',
      'a' => 'For most small businesses, once a quarter is plenty, plus year-end for taxes and any time a bank or investor asks. Because a balance sheet is a snapshot of a single date, you generate a fresh one whenever you want a current picture rather than keeping one constantly up to date. If you are watching cash carefully or growing fast, checking monthly helps you spot a squeeze between what you owe soon and what you can cover.',
    ],
    [
      'q' => 'Is equity the same as the cash in my bank account?',
      'a' => 'No, and mixing them up is common. Cash is one asset among several. Equity is what is left after you subtract every liability from every asset, so it includes the value of your stock, equipment, and unpaid invoices too, minus your loans and bills. You can have plenty of equity while holding very little cash, for example if most of your worth is tied up in stock, or little equity despite a healthy bank balance if that cash is largely borrowed.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'generic',
    'consultant',
    'freelance',
  ],

  'related_article_slugs' => [
    'what-is-a-profit-and-loss-statement',
    'small-business-bookkeeping-basics',
    'inventory-tracking-for-small-businesses',
    'cash-basis-vs-accrual-accounting',
  ],
];
