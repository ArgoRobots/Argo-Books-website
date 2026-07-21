<?php
// articles/data/gross-profit-vs-net-profit.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'gross-profit-vs-net-profit',

  'h1' => 'Gross profit vs net profit: what\'s the difference?',

  'meta_title' => 'Gross Profit vs Net Profit: the Difference | Argo Books',

  'meta_description' => 'Gross profit vs net profit, explained with real numbers: the two formulas, how to work out each margin, and why the sales tax you collect isn\'t profit.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 70,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Two businesses can each bring in $150,000 a year and end up in completely different places. One keeps $40,000 of it. The other keeps $4,000, or loses money. The gap between those outcomes is the difference between gross profit and net profit, and if you only ever look at one of them, you're flying half-blind.</p>
<p>Gross profit tells you whether the work itself is priced right. Net profit tells you whether the whole business is actually making money after everything is paid. This guide covers both formulas, how to turn each one into a margin percentage, and a full worked example that carries one landscaping business from its top-line revenue all the way down to what it keeps. It also clears up the single most common mistake: counting the sales tax you collect as if it were yours.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The two profits in one line',
      'anchor' => 'the-two-profits',
      'html' => <<<'HTML'
<p>Start with the plain-English version, then the formulas.</p>
<p><strong>Gross profit</strong> is what's left after you subtract the direct cost of delivering the thing you sold. If you sell a $100 chair and the wood and hardware cost you $40, your gross profit on that chair is $60. It answers one question: is the job itself making money?</p>
<p><strong>Net profit</strong> is what's left after you subtract everything else too: rent, insurance, software, advertising, your accountant, the truck payment, the phone bill. It answers a bigger question: is the whole business making money?</p>
<p>Here are the two formulas. They're simpler than they look.</p>
<ul>
<li><strong>Gross profit = Revenue &minus; Cost of goods sold</strong></li>
<li><strong>Net profit = Gross profit &minus; all other expenses</strong></li>
</ul>
<p>"Cost of goods sold" (often shortened to COGS) is the direct cost of what you sold: materials, the parts that went into the product, and any labour you paid specifically to do that job. "All other expenses" is everything you'd still be paying even if you didn't land a single sale this month: rent, insurance, subscriptions, marketing.</p>
<p>One important note before we go further. In both formulas, "revenue" means revenue <em>excluding</em> any sales tax you collected. The sales tax isn't yours, so it never belongs in a profit calculation. There's a whole section on that below, because it trips up more people than any other part of this.</p>
{{illustration:profit-split}}
HTML,
    ],

    [
      'h2' => 'Gross profit: is the work priced right?',
      'anchor' => 'gross-profit',
      'html' => <<<'HTML'
<p>Gross profit zooms in on the jobs themselves and ignores the rest of the business for a moment. That focus is exactly what makes it useful. If your gross profit is thin, no amount of cutting the office phone bill will save you, because the problem is baked into the price of the work.</p>
<p>The tricky part is deciding what counts as cost of goods sold and what doesn't. A cost belongs in COGS if it goes up when you do more jobs and down when you do fewer. A few examples:</p>
<ul>
<li><strong>A furniture maker.</strong> COGS is the wood, the screws, the finish, and the delivery driver they hired for the day. The workshop rent is not COGS, because they pay it whether they build one table or ten.</li>
<li><strong>A landscaper.</strong> COGS is the plants, mulch, sod, and the fuel burned running the mower on a client's property. The general liability insurance is not COGS.</li>
<li><strong>A freelance developer.</strong> COGS is thin or close to zero, because the main cost of the work is their own time. If they subcontract part of a build to another developer, that subcontractor's invoice is COGS.</li>
</ul>
<p>That last example shows why gross profit matters more in some businesses than others. A product business or a trade with real materials lives and dies on its gross margin. A pure service business where the only input is your time has almost no COGS, so its gross profit is nearly all of its revenue, and the real story is entirely in the net profit further down.</p>
<p>If your gross profit is too low, you have three levers: raise your prices, cut what your materials cost you (buy smarter, waste less), or stop taking the low-margin jobs. Trimming overhead won't fix a gross-margin problem, so this is the number to look at first when a business feels busy but broke.</p>
HTML,
    ],

    [
      'h2' => 'Net profit: is the whole business profitable?',
      'anchor' => 'net-profit',
      'html' => <<<'HTML'
<p>Net profit is the number that actually pays you. It takes your gross profit and subtracts every other cost of being in business, the ones that keep showing up whether or not the phone rings:</p>
<ul>
<li>Rent or a workshop lease</li>
<li>Insurance (liability, professional, vehicle)</li>
<li>Software and subscriptions</li>
<li>Advertising and marketing</li>
<li>Your accountant or bookkeeper</li>
<li>Bank and payment-processing fees</li>
<li>Vehicle payments, phone, and utilities</li>
</ul>
<p>These are usually called overhead or operating expenses. The label matters less than the idea: they're the cost of keeping the doors open, not the cost of any one job.</p>
<p>A business can have a healthy gross profit and still end up with a net loss. That happens when the overhead quietly grows past what the jobs bring in: a bigger unit, another subscription, a second vehicle, a splashy ad campaign that didn't land. Gross profit looks fine on every individual job, but by the time all the fixed costs come out, there's nothing left. This is why you can't judge a business on gross profit alone, and why the owner who only watches the top line gets a nasty surprise at tax time.</p>
<p>One thing net profit is <em>not</em>: it isn't automatically your take-home pay. If you run a limited company or a corporation and pay yourself a salary, that salary is already one of the expenses subtracted above, so net profit is what's left <em>after</em> paying you. If you're a sole trader or run a partnership, you typically don't put your own pay through as a wage. In that case the net profit is broadly what's available for you to draw and to cover your income tax, but the tax hasn't been taken out yet. Rules differ by country and setup, so check with an accountant for your situation.</p>
HTML,
    ],

    [
      'h2' => 'Turning each one into a margin',
      'anchor' => 'margins',
      'html' => <<<'HTML'
<p>Raw dollar figures are hard to compare across months or against other businesses. A $60,000 gross profit sounds great until you learn it came from $600,000 of revenue. Margins fix that by turning the profit into a percentage of revenue, so you can compare a slow month to a busy one on equal footing.</p>
<p>There are two, and each uses the profit of the same name:</p>
<ul>
<li><strong>Gross margin % = (Gross profit &divide; Revenue) &times; 100</strong></li>
<li><strong>Net margin % = (Net profit &divide; Revenue) &times; 100</strong></li>
</ul>
<p>Say a business does $150,000 in revenue, with $60,000 of gross profit and $36,000 of net profit. Gross margin is 60,000 &divide; 150,000 = 40%. Net margin is 36,000 &divide; 150,000 = 24%. In plain terms: 40 cents of every dollar survives the direct costs of the work, and 24 cents of every dollar is still standing after everything is paid.</p>
<p>What counts as "good" swings hard by industry, so be careful comparing yourself to a business that does something different. A software or consulting business can run a gross margin above 80% because it has almost no materials. A grocery store might live on a gross margin under 25%. Net margins in the high single digits to the mid teens are common and healthy for a lot of small service businesses. The more useful comparison is against your own numbers over time: a net margin that's sliding month after month is a signal to look at, even if it's still positive.</p>
HTML,
    ],

    [
      'h2' => 'A worked example, top to bottom',
      'anchor' => 'worked-example',
      'html' => <<<'HTML'
<p>Numbers make this concrete. Meet Maria, who runs a two-person landscaping business. Over the year she invoices $150,000 of work, and on top of that she collects sales tax from her customers, which we'll come back to in a minute. Here's the full trip from revenue down to net profit.</p>
<p><strong>Start: Revenue (excluding sales tax) = $150,000.</strong> This is the value of the work she sold, before any sales tax. It's the top line, and every profit figure builds down from here.</p>
<p><strong>Subtract cost of goods sold.</strong> These are her direct job costs for the year:</p>
<ul>
<li>Plants, sod, and mulch: $34,000</li>
<li>Fuel for mowers and the trailer on job sites: $9,000</li>
<li>A day-labourer she hires for big installs: $17,000</li>
</ul>
<p>That's $60,000 of COGS. So <strong>Gross profit = $150,000 &minus; $60,000 = $90,000</strong>, and her <strong>gross margin is 90,000 &divide; 150,000 = 60%</strong>. The work itself is well priced: for every dollar of landscaping she sells, 60 cents is left after the materials and job labour.</p>
<p><strong>Now subtract everything else.</strong> Her overhead for the year:</p>
<ul>
<li>Truck payment and vehicle insurance: $14,000</li>
<li>General liability insurance: $4,000</li>
<li>Phone, software, and her accounting app: $2,500</li>
<li>Advertising (local ads and a van wrap): $6,000</li>
<li>Her own wage through the business: $25,000</li>
<li>Accountant, bank fees, and small odds and ends: $2,500</li>
</ul>
<p>That's $54,000 of other expenses. So <strong>Net profit = $90,000 &minus; $54,000 = $36,000</strong>, and her <strong>net margin is 36,000 &divide; 150,000 = 24%</strong>.</p>
<p>Read the two numbers together and you learn two different things. The 60% gross margin says her pricing and her job costs are in good shape. The 24% net margin says the whole business, after the truck and the insurance and paying herself, still keeps 24 cents on the dollar. If next year the gross margin held at 60% but the net margin fell to 12%, she'd know the jobs were still fine and the leak was somewhere in overhead, most likely that new van wrap and a second software subscription that didn't earn their keep.</p>
{{illustration:coins}}
HTML,
    ],

    [
      'h2' => 'The sales tax you collect isn\'t profit',
      'anchor' => 'sales-tax-is-not-profit',
      'html' => <<<'HTML'
<p>Back to the sales tax we set aside. This is where a lot of small businesses talk themselves into thinking they had a better year than they did.</p>
<p>When you charge a customer sales tax, GST, HST, or VAT, that money passes through your account, but it was never yours. You're holding it on behalf of the tax authority, and you'll hand it over when you file. Counting it as revenue is like counting a friend's cash as your own because it happened to be in your wallet for a night.</p>
<p>Here's the mechanics on a single sale. You sell $100 of work and add 10% sales tax, so the customer pays you $110. Your revenue for profit purposes is $100. The extra $10 is a liability: it sits on your books as "owed to the government" until you remit it. If you counted the full $110 as revenue, your profit would look $10 too high on every sale, and that gap adds up fast across a year.</p>
<p>This is exactly how Argo Books treats it. In the app, <strong>Net Profit = Revenue (excluding sales tax) &minus; Expenses &minus; Refunds</strong>. The sales tax you collect is recorded as a liability, never as profit. The app tracks it as two separate figures: <strong>Tax Collected</strong> (the tax you charged on your invoices) and <strong>Tax Paid</strong> (the tax you paid suppliers on your own expenses). Your net tax liability is simply Tax Collected minus Tax Paid. Argo Books shows you that number so you can hand it to your accountant or filing service, but it doesn't file or remit the tax for you.</p>
<p>One more detail worth knowing, because it surprises people. The "Total Revenue" figure on the dashboard <em>is</em> shown gross, meaning it does include the tax the customer paid, because that's the full amount that landed in your account. It's only the <em>profit</em> calculation that strips the tax back out. So if your net profit looks smaller than "revenue minus expenses," that missing slice is the sales tax that was never yours to keep. Rules on registration thresholds and rates vary by country, so check with an accountant for your situation.</p>
HTML,
    ],

    [
      'h2' => 'Where Argo Books shows the whole trip',
      'anchor' => 'in-argo-books',
      'html' => <<<'HTML'
<p>You can do all of this by hand, and plenty of people start in a spreadsheet. The catch is that gross and net profit are only as good as the bookkeeping underneath them. If half your expenses live in a shoebox and the sales tax is muddled into your revenue, the margins you calculate are guesses.</p>
<p>Argo Books does the arithmetic from your own records. The dashboard shows your net profit computed the honest way: Revenue (excluding sales tax), minus Expenses, minus Refunds. Because it draws on your recorded income and expenses, the number moves as your business does, and the sales tax is kept to one side where it belongs.</p>
<p>To see the full breakdown from gross down to net, the free <strong>Report Builder</strong> generates an Income Statement (also called a profit and loss statement) from your data. It lays out revenue at the top, subtracts cost of goods sold to show gross profit, then subtracts your other expenses to show net profit, the same trip Maria's numbers took above, but built from your actual figures and exported as a clean, branded PDF you can hand to an accountant or a lender.</p>
<p>The one job that's still on you is recording things in the right place: job materials and direct labour as expenses tied to the work, overhead as your operating costs, and the sales tax as tax rather than income. Argo Books' guided expense and revenue forms are built to make that sorting quick, and once it's in, the gross and net figures take care of themselves. For more on how each dashboard figure is worked out, the definitions in this guide line up with what you'll see in the app.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'Clean gross and net profit start with tidy income and expenses. See how Argo Books sorts them for you.',
  'tool_callout_cta' => 'See expense and revenue tracking',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'Which matters more, gross or net profit?',
      'a' => 'Neither one alone tells the full story, which is why you watch both. Gross profit tells you whether the work itself is priced right, so it\'s the first number to check when a business feels busy but broke. Net profit tells you whether the whole business is actually making money after every cost, so it\'s the number that decides whether you can pay yourself and stay open. A useful habit is to read them together: a strong gross margin with a weak net margin points at overhead, while a weak gross margin points at your pricing or your job costs.',
    ],
    [
      'q' => 'How do I calculate gross margin?',
      'a' => 'Take your gross profit, divide it by your revenue, and multiply by 100 to get a percentage. Gross profit itself is revenue (excluding sales tax) minus your cost of goods sold, which is the direct cost of delivering what you sold: materials, parts, and any labour paid specifically for that job. So if you did $150,000 of work with $60,000 of direct costs, your gross profit is $90,000 and your gross margin is 90,000 divided by 150,000, which is 60%. That means 60 cents of every dollar of sales is left after the direct cost of the work.',
    ],
    [
      'q' => 'Is net profit my take-home pay?',
      'a' => 'Not automatically, and it depends on how your business is set up. If you run a company and pay yourself a salary, that salary is already counted as an expense, so net profit is what\'s left after paying you. If you\'re a sole trader, you usually don\'t run your own pay through as a wage, so net profit is broadly what\'s available for you to draw, but your income tax hasn\'t been taken out of it yet. Either way, net profit is a business figure, not a paycheck, and you should set money aside from it for tax. Check with an accountant for your situation.',
    ],
    [
      'q' => 'Does sales tax count in profit?',
      'a' => 'No. The sales tax, GST, HST, or VAT you collect from customers was never yours. You\'re holding it for the tax authority and will hand it over when you file, so it doesn\'t belong in any profit calculation. If a customer pays $110 on a $100 sale with 10% tax, your revenue for profit purposes is $100 and the $10 is a liability. Argo Books works it exactly this way: net profit is revenue excluding sales tax, minus expenses, minus refunds, and the tax you collect is tracked as a liability rather than as income.',
    ],
    [
      'q' => 'What\'s a healthy profit margin?',
      'a' => 'It swings a lot by industry, so be careful comparing yourself to a business that does something different. A consulting or software business can run a gross margin above 80% because it has almost no materials, while a grocery store might live below 25%. For net margin, high single digits to the mid teens is common and healthy for many small service businesses. The most useful benchmark is your own history: a margin that holds steady or climbs is a good sign, and one that slides month after month is worth investigating even while it\'s still positive.',
    ],
    [
      'q' => 'What\'s the difference between cost of goods sold and overhead?',
      'a' => 'A cost is cost of goods sold if it rises when you do more jobs and falls when you do fewer: materials, parts, and job-specific labour. A cost is overhead if you\'d pay it anyway even with no sales this month: rent, insurance, software, advertising, and your accountant. The split matters because cost of goods sold is subtracted to get gross profit, while overhead is subtracted after that to get net profit. Sorting each expense into the right bucket is what makes your gross and net margins trustworthy rather than a guess.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'consultant',
    'generic',
  ],

  'related_article_slugs' => [
    'what-is-a-profit-and-loss-statement',
    'small-business-bookkeeping-basics',
    'how-to-price-your-services',
    'cash-basis-vs-accrual-accounting',
  ],
];
