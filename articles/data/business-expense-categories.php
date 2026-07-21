<?php
// articles/data/business-expense-categories.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'business-expense-categories',

  'h1' => 'Business expense categories: a complete list',

  'meta_title' => 'Business Expense Categories: a Complete List | Argo Books',

  'meta_description' => 'Every business expense category explained, with what belongs in each, a real example, and a deductibility note. Here\'s how to sort your spending so tax time is easy.',

  'schema_type' => 'Article',

  'category' => 'receipts-expenses',
  'hub_weight' => 20,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Every dollar your business spends belongs in a bucket. A tin of paint, a Facebook ad, your accountant's bill, the coffee you bought a client: each one has a natural home, and putting it there is what turns a shoebox of receipts into numbers you can actually use. That sorting job is what expense categories do.</p>
<p>This guide is the complete list. You'll get every standard category a small business uses, what belongs in each one, a real example so there's no guessing, and a plain note on whether it's usually deductible. Get this right once and two things get easier at the same time: your tax return, because your deductions are already grouped, and your day-to-day sense of where the money actually goes. No accounting background needed.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why expense categories matter',
      'anchor' => 'why-categories-matter',
      'html' => <<<'HTML'
<p>Categories do two jobs, and both of them pay you back.</p>
<p>The first job is tax. When you file, you don't report one giant number called "money I spent". You report your spending grouped into types, because most tax forms ask for exactly that: how much on advertising, how much on supplies, how much on travel. If your spending is already sorted into those buckets, filling in the form is copying totals across. If it isn't, you're sitting at the kitchen table in April sorting a year of receipts by hand, and every receipt you can't place is a deduction you probably won't claim. Categories are how you make sure you actually get credit for what you spent.</p>
<p>The second job is seeing where the money goes. A single "expenses" total tells you nothing you can act on. The same money split into categories tells you a story: software crept from $40 a month to $180 without anyone deciding it should, or meals is quietly your third-biggest line, or the tools budget doubled the month you took on the bigger job. You can't cut a cost you can't see, and categories are how you see it.</p>
<p>One rule underpins all of this: a business expense has to be for the business. The plain test used across the US, Canada, the UK, and Australia is whether the cost was "ordinary and necessary" (or "wholly and exclusively", in UK wording) for earning your income. A laptop you use only for work is in. A family holiday is out. Anything used for both, like a phone or a car, gets split so you only claim the business share. For the full breakdown of that line, see <a href="/what-counts-as-a-business-expense/">what counts as a business expense</a>.</p>
{{illustration:checklist}}
HTML,
    ],

    [
      'h2' => 'What you sell, and how you get customers',
      'anchor' => 'cogs-and-marketing',
      'html' => <<<'HTML'
<p>These two categories sit at the front of the business: the cost of the thing you sell, and the cost of finding someone to sell it to.</p>
<h3>Cost of goods sold</h3>
<p>Cost of goods sold, usually shortened to COGS, is what it directly costs you to make or buy the things you sell. For a product business that's raw materials, stock you buy to resell, packaging, and the freight to get it to you. For a service business it's often small or zero, though a cleaner's supplies or a caterer's ingredients can sit here.</p>
<p><strong>Example:</strong> a candle maker spends $600 on wax, wicks, jars, and boxes to produce a batch. That $600 is COGS, not general supplies, because it went straight into products that got sold.</p>
<p><strong>Deductibility:</strong> COGS is deductible, but it's handled differently from other costs. It's subtracted from your sales to work out gross profit rather than listed with your operating expenses, and unsold stock at year end is carried as inventory instead of being deducted yet. This is one of the few categories where the timing genuinely matters, so it's worth getting right.</p>
<h3>Advertising and marketing</h3>
<p>Everything you spend to get noticed and win work. Online ads, printed flyers, business cards, your website hosting and domain, a logo design, sponsored posts, sign-writing on the van, listing fees on a directory.</p>
<p><strong>Example:</strong> a photographer pays $220 for Instagram ads promoting a mini-session weekend and $90 to renew the website domain and hosting. Both go in advertising and marketing.</p>
<p><strong>Deductibility:</strong> generally fully deductible in all four countries. The usual exception is client entertainment dressed up as marketing, which most tax authorities treat separately and limit. Straightforward promotion of your business is fine.</p>
HTML,
    ],

    [
      'h2' => 'Running your workspace',
      'anchor' => 'workspace',
      'html' => <<<'HTML'
<p>The costs of having somewhere to work and the kit to do it. Five categories live here, and the difference between them is mostly about size and how long the thing lasts.</p>
<h3>Office supplies</h3>
<p>Small, everyday consumables that get used up: paper, pens, ink, printer toner, folders, cleaning products for the office, stamps and postage. The rule of thumb is that a supply is something you'll replace within the year.</p>
<p><strong>Example:</strong> a bookkeeper spends $65 on printer ink and a box of file folders. That's office supplies.</p>
<p><strong>Deductibility:</strong> fully deductible in the year you buy them, everywhere.</p>
<h3>Software and subscriptions</h3>
<p>Any tool you pay for on a recurring basis or as a one-off licence: your accounting app, design software, cloud storage, a scheduling tool, email hosting, a password manager. If it's digital and you pay to use it for the business, it goes here.</p>
<p><strong>Example:</strong> a freelance designer pays $55 a month for a design suite and $12 a month for cloud file storage. Both land in software and subscriptions.</p>
<p><strong>Deductibility:</strong> fully deductible when the tool is used for work. Split the cost if a subscription is part personal, and claim only the business share.</p>
<h3>Rent and utilities</h3>
<p>Rent on a commercial space, plus the power, water, gas, and building costs that come with it. This is for a separate premises like a studio, shop, workshop, or leased office. Working from your home is a different category, covered further down.</p>
<p><strong>Example:</strong> a cleaning company rents a small unit to store equipment and pays $900 a month plus $110 for electricity. Both go under rent and utilities.</p>
<p><strong>Deductibility:</strong> fully deductible for a dedicated business premises.</p>
<h3>Phone and internet</h3>
<p>Your mobile plan, a business landline, and your internet connection. Most sole traders use one phone and one connection for both work and life, so this is a classic split-cost category.</p>
<p><strong>Example:</strong> a contractor's phone bill is $70 a month and he reckons it's used 70% for the business. He claims $49 a month and leaves the rest.</p>
<p><strong>Deductibility:</strong> deductible for the business-use portion. Keep a note of how you worked out the percentage; tax offices in all four countries expect a reasonable basis, not a round guess.</p>
<h3>Equipment and tools</h3>
<p>The bigger, longer-lasting items you buy to do the work: a laptop, a camera, a drill, a ladder, a printer, workshop machinery. The line between this and office supplies is durability. A tool you'll still be using in three years belongs here.</p>
<p><strong>Example:</strong> an electrician buys a $400 cordless drill kit and a $250 test meter. Both are equipment and tools.</p>
<p><strong>Deductibility:</strong> deductible, but often over time. Smaller items can usually be written off immediately (many countries have an instant write-off threshold), while pricier gear may need to be depreciated over several years. The thresholds differ by country, so this is a good one to check with an accountant.</p>
HTML,
    ],

    [
      'h2' => 'Getting around and feeding the work',
      'anchor' => 'travel-and-meals',
      'html' => <<<'HTML'
<p>Three categories that draw the closest attention from tax offices, because they're the easiest to blur with personal life. Keep them clean and they're perfectly claimable.</p>
<h3>Travel</h3>
<p>Costs of trips away for work: flights, trains, hotels, taxis, airport parking, baggage. This is for overnight or out-of-town business travel, not your daily drive around town, which sits under vehicle.</p>
<p><strong>Example:</strong> a consultant flies interstate for a two-day client project. The $310 flight and $240 hotel go under travel.</p>
<p><strong>Deductibility:</strong> business travel is deductible in all four countries. The trip has to be genuinely for work; if you tack on holiday days, only the business portion counts. Keep the itinerary and the reason for the trip.</p>
<h3>Meals</h3>
<p>Food and drink with a business purpose: a lunch with a client, a coffee meeting, meals while travelling for work. Everyday lunches you'd eat anyway are not a business expense.</p>
<p><strong>Example:</strong> a freelancer buys a $46 lunch discussing a new project with a prospective client. That's a meals expense.</p>
<p><strong>Deductibility:</strong> partial in most places. The US generally allows 50% of a qualifying business meal. Canada also caps most meals and entertainment at 50%. The UK and Australia are stricter and often disallow routine meals unless you're genuinely travelling for work. This is the category people most often get wrong, so keep the receipt and note who you met and why.</p>
<h3>Vehicle and fuel</h3>
<p>Running a car or van for the business: fuel, servicing, tyres, insurance, registration, and repairs, or a per-kilometre or per-mile rate instead. Your commute from home to a regular workplace usually doesn't count; driving between jobs does.</p>
<p><strong>Example:</strong> a plumber drives between three jobs in a day. The fuel and the wear on the van for those work trips go under vehicle and fuel.</p>
<p><strong>Deductibility:</strong> deductible for business use only. You either claim the business-use share of actual running costs or use the standard mileage rate your country sets. You have to track the business kilometres or miles either way, so keep a simple log of work trips. Argo Books doesn't track mileage automatically, so record the trips as you go.</p>
HTML,
    ],

    [
      'h2' => 'Expertise, protection, and paying people',
      'anchor' => 'services-and-people',
      'html' => <<<'HTML'
<p>The costs of being a proper business: covering your risk, paying for advice, moving money, and paying the people who help you.</p>
<h3>Insurance</h3>
<p>Business insurance premiums: public liability, professional indemnity, contents or equipment cover, commercial vehicle insurance, and product liability. Personal health or life cover generally isn't a business expense, though rules vary.</p>
<p><strong>Example:</strong> a cleaning company pays $540 a year for public liability cover. That's an insurance expense.</p>
<p><strong>Deductibility:</strong> business insurance is deductible in all four countries. Personal policies are not, so keep the two apart.</p>
<h3>Professional fees</h3>
<p>Fees paid to outside professionals for their expertise: your accountant, bookkeeper, tax agent, a lawyer, or a business consultant. If you pay someone qualified to advise or represent the business, it goes here.</p>
<p><strong>Example:</strong> a sole trader pays a $350 accountant's fee to prepare the year's tax return. That's a professional fee.</p>
<p><strong>Deductibility:</strong> generally fully deductible when the work relates to the business, including the cost of preparing your business tax return. Legal fees for buying a big asset can sometimes be treated differently, so flag anything large with your accountant.</p>
<h3>Bank and merchant fees</h3>
<p>The cost of moving money: business bank account fees, transaction charges, and the cut card processors take on payments. If you take card or online payments, those processing fees add up fast and are easy to overlook.</p>
<p><strong>Example:</strong> a photographer takes $4,000 in card payments through a processor charging around 2.9% plus a small per-transaction fee, so roughly $130 in fees for the month. That's a bank and merchant fee.</p>
<p><strong>Deductibility:</strong> fully deductible everywhere. These are pure cost of doing business, so make sure they're captured rather than netted silently out of your payouts.</p>
<h3>Wages and contractors</h3>
<p>What you pay other people to do work for the business: employee wages and payroll costs if you have staff, plus payments to subcontractors and freelancers you hire in. A landscaper who brings in a labourer for a big job records that payment here.</p>
<p><strong>Example:</strong> a contractor pays a subcontractor $1,200 to handle the tiling on a bathroom job. That $1,200 goes under wages and contractors.</p>
<p><strong>Deductibility:</strong> deductible, but this category comes with paperwork. Employee wages trigger payroll tax and withholding obligations, and contractor payments often need to be reported (a 1099 in the US, or similar records elsewhere). Keep the invoices and payment records; this is an area tax offices check.</p>
HTML,
    ],

    [
      'h2' => 'Growing the business and working from home',
      'anchor' => 'growth-and-home-office',
      'html' => <<<'HTML'
<p>Two last categories that a lot of small businesses forget to claim, which means leaving real money on the table.</p>
<h3>Training and education</h3>
<p>Courses, workshops, certifications, industry conferences, and books or online learning that keep your existing skills sharp or up to date. The key test is that it relates to the work you already do.</p>
<p><strong>Example:</strong> a bookkeeper pays $180 for an online course on the latest tax changes and $40 for a professional reference book. Both go under training and education.</p>
<p><strong>Deductibility:</strong> deductible when the training maintains or improves skills for your current business. Training to start a completely new career or qualify in a new field is usually not deductible, which is a distinction tax offices in all four countries draw.</p>
<h3>Home office</h3>
<p>If you work from home, a share of your home running costs can be a business expense: a portion of rent or mortgage interest, power, heating, and internet, based on how much of your home and time is given over to work. This is separate from renting a commercial space.</p>
<p><strong>Example:</strong> a freelancer uses one room as a dedicated office, about 12% of the floor area, and claims 12% of the household power and heating for the year.</p>
<p><strong>Deductibility:</strong> deductible, and every one of the four countries offers it, but the method differs. Several offer a simplified flat rate per square metre or per hour worked so you don't have to split every bill, alongside a detailed actual-cost method. Argo Books doesn't run a home-office wizard, so work out the figure (or ask your accountant), then record it as an expense. Because the rules and rates vary so much, this is the single best category to check with an accountant on.</p>
HTML,
    ],

    [
      'h2' => 'Categories change by country and by form',
      'anchor' => 'categories-vary',
      'html' => <<<'HTML'
<p>The categories above are the common ground that nearly every small business uses, and they map cleanly onto how most people think about their spending. What they don't do is match any single tax form line for line, because the forms differ.</p>
<p>A US Schedule C, a Canadian T2125, a UK Self Assessment return, and an Australian business schedule all group expenses slightly differently. One might have a dedicated line for "supplies" while another folds that into "other expenses". One caps meals at 50%, another disallows them almost entirely. Vehicle and home-office rules, in particular, vary a lot in both the method and the rate.</p>
<p>Here's the practical way to handle it. Track your spending in the everyday categories that make sense to you and that let you see where the money goes. When it's time to file, you (or your accountant) map those categories onto the specific lines your form asks for. That mapping is quick when your spending is already sorted, and painful when it isn't. And because the deductibility rules shift by country, and sometimes by the specifics of your situation, check with an accountant before you treat any borderline cost as deductible. A short conversation once a year is cheaper than a missed deduction or a claim that doesn't hold up.</p>
HTML,
    ],

    [
      'h2' => 'How Argo Books handles categories for you',
      'anchor' => 'argo-categories',
      'html' => <<<'HTML'
<p>Sorting spending into categories is exactly the kind of small, repetitive job that gets skipped when you're busy, and skipping it is what leaves you with the April shoebox. Argo Books is built to keep it from piling up.</p>
<p>Every expense you record goes into a category through a guided form, so the sorting happens as you enter the cost, not months later. Your spending stays grouped the whole year, which means the totals you'd hand to an accountant, or drop onto a tax form, are ready whenever you look. The free <a href="/features/expense-revenue-tracking/">Report Builder</a> turns those same categorised numbers into a clean Income Statement (profit and loss) and tax summary, exported as a branded PDF, so you can see your biggest cost lines at a glance and hand over something tidy at tax time.</p>
{{illustration:receipt-scan}}
<p>The part that saves the most time is AI receipt scanning. Import a photo of a receipt and the app reads the vendor, the date, the amount, and the tax, then helps drop it into the right category, so a paper receipt becomes a sorted, recorded expense in a few seconds. You get 10 scans a month free, and 500 a month on Premium, which is <span>${argo_premium_monthly}</span>/month. There's also a free receipt scanner on the site if you just want to try it. The same goes for a whole bank statement: drop in a CSV, Excel, or PDF and every line comes back categorised, no bank connection needed.</p>
<p>Categories are only worth the effort if keeping them up doesn't become a second job. Recording the expense and sorting it into the right bucket in one step, straight from a photo, is how you get the tax-time payoff and the where-the-money-goes picture without the shoebox. For more on keeping it painless, see <a href="/how-to-track-business-expenses-without-spreadsheets/">how to track business expenses without spreadsheets</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books sorts every expense into a category as you record it, then builds your profit and loss and tax summary from those numbers.',
  'tool_callout_cta' => 'See expense and revenue tracking',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'What are the main business expense categories?',
      'a' => 'The common ones nearly every small business uses are: cost of goods sold, advertising and marketing, office supplies, software and subscriptions, rent and utilities, travel, meals, vehicle and fuel, insurance, professional fees, bank and merchant fees, wages and contractors, equipment and tools, phone and internet, training and education, and home office. Most spending fits neatly into one of those. You don\'t have to use every category; use the ones that match how your business actually spends money.',
    ],
    [
      'q' => 'Does it matter which category I use?',
      'a' => 'For most costs, being consistent matters more than being perfect. If you always put your web hosting under software and subscriptions rather than advertising, your totals stay meaningful year to year, and moving a cost between two ordinary categories rarely changes your tax. A few categories are exceptions where the choice genuinely matters: cost of goods sold is treated differently from operating expenses, and equipment may need to be depreciated over time rather than deducted at once. When a cost could plausibly sit in one of those, it\'s worth getting it right or asking your accountant.',
    ],
    [
      'q' => 'Can I create my own categories?',
      'a' => 'Yes, and it\'s often useful. Beyond the standard list, many businesses add a category that reflects their own work: a photographer might track "props and styling", a food business might split "packaging" out from cost of goods sold. Custom categories help you see your specific costs clearly. Just keep them stable from year to year so your totals stay comparable, and when you file, map any custom category back onto the standard line your tax form expects.',
    ],
    [
      'q' => 'What is cost of goods sold?',
      'a' => 'Cost of goods sold, or COGS, is what it directly costs you to make or buy the things you sell: raw materials, stock you resell, packaging, and inbound freight. It\'s the cost tied to the actual products that left the door. It matters as its own category because it\'s treated differently from your other expenses: it\'s subtracted from sales to work out gross profit rather than listed with operating costs, and unsold stock at year end is carried as inventory instead of being deducted straight away. Service businesses often have little or no COGS.',
    ],
    [
      'q' => 'How do software subscriptions get categorized?',
      'a' => 'Put recurring software and app subscriptions under "software and subscriptions": your accounting app, design tools, cloud storage, scheduling apps, email hosting, and similar. They\'re generally fully deductible when used for the business. If a subscription is part personal, like a music service you also use for work, claim only the business share. Keeping all your recurring digital tools in one category also makes it easy to spot subscription creep, where small monthly charges quietly add up to a real number over a year.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'cleaning',
    'photographer',
  ],

  'related_article_slugs' => [
    'what-counts-as-a-business-expense',
    'small-business-tax-deductions',
    'how-to-track-business-expenses-without-spreadsheets',
    'how-to-scan-and-organize-receipts',
  ],
];
