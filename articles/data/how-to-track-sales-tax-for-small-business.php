<?php
// articles/data/how-to-track-sales-tax-for-small-business.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-track-sales-tax-for-small-business',

  'h1' => 'How to track sales tax for your small business',

  'meta_title' => 'How to Track Sales Tax for Your Small Business | Argo Books',

  'meta_description' => 'Sales tax isn\'t your money, it\'s the government\'s. Here\'s how to charge it, track what you collect against what you pay, set it aside, and file on time.',

  'schema_type' => 'HowTo',

  'category' => 'bookkeeping',
  'hub_weight' => 80,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Sales tax trips up more small businesses than almost any other part of the books, and it's usually for one reason: people treat the tax they collect like income. It isn't. When a customer pays you $110 on a $100 job, that extra $10 was never yours. You're holding it for the government, and one day you have to hand it over. If you've already spent it, that day gets painful.</p>
<p>This guide walks through the whole cycle in plain terms. You'll see the mental model that makes the rest click, how to tell whether you even need to register, how to charge the right rate and record what you collect, how to track the tax you collect against the tax you pay on your own purchases, and how to set the money aside so it's there at filing time. No accounting background needed.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The mental model: it was never your money',
      'anchor' => 'the-mental-model',
      'html' => <<<'HTML'
<p>Get this one idea straight and everything else falls into place. Sales tax (also called GST, HST, or VAT depending on where you are) is money you collect <em>on behalf of</em> the government. You are a middleman. The chain looks like this: the customer pays it to you, you hold it for a while, then you hand it to the tax authority. It passes through your bank account, but it's never yours to keep.</p>
<p>Here's a worked example. A plumber charges a homeowner $500 for a Saturday repair, plus 10% sales tax. The invoice total is $550. The homeowner pays $550. It's tempting to look at the bank balance and think "I made $550 today." You didn't. You earned $500, and you're holding $50 that belongs to the tax office. Your real income from that job is $500.</p>
<p>This is exactly why good accounting software keeps sales tax out of your profit number. Your net profit is worked out from revenue <strong>excluding</strong> the tax you collected, because that tax was always a liability, never earnings. If you ever see a profit figure that looks suspiciously high, the first thing to check is whether collected tax got mixed into it.</p>
<p>Treat the tax like a jar on the shelf that isn't yours. Money goes in every time you make a sale, and it only comes out when you pay the government. The rest of this guide is really just about keeping that jar honest.</p>
HTML,
    ],

    [
      'h2' => 'Step 1: Work out if and when you must register',
      'anchor' => 'do-you-need-to-register',
      'step_name' => 'Work out if you need to register',
      'step_text' => 'Check whether your sales and your location cross the registration threshold for your tax authority. Below it you usually don\'t charge tax; above it you must register and start charging.',
      'html' => <<<'HTML'
<p>Before you charge a cent of tax, you need to know whether you're supposed to. In most places there's a turnover threshold. Below it, small sellers usually don't have to register or charge tax. Above it, you must register, start charging, and file returns. The exact number and the fine print vary a lot, so treat these as rough signposts, not gospel.</p>
<ul>
<li><strong>United States.</strong> There's no national sales tax. Each state sets its own rules, and whether you owe tax in a state depends on "nexus," meaning a connection to that state such as a physical location, employees, or enough sales into it. Most states tax goods but treat services differently, and some tax labour on new construction but not on repairs. If you sell into several states, you can have tax duties in more than one. Check the revenue site for each state you operate in.</li>
<li><strong>Canada.</strong> You generally must register for GST/HST once your revenue passes about $30,000 CAD over four rolling quarters. Below that you're a "small supplier" and registration is optional. The rate runs roughly 5% to 15% depending on the province.</li>
<li><strong>United Kingdom.</strong> VAT registration becomes mandatory once your taxable turnover passes around GBP 90,000 over a rolling 12 months. The standard VAT rate is 20%, with reduced and zero rates for some goods.</li>
<li><strong>Australia.</strong> You must register for GST once your turnover reaches about $75,000 AUD (lower for taxi and rideshare drivers, who register from day one). GST is a flat 10%.</li>
</ul>
<p>Two things matter here. First, thresholds move and the details are full of exceptions, so check your local rule or ask an accountant before you decide. Second, watch your running total as you grow. Crossing the threshold mid-year means you have to register and start charging, and the responsibility is on you to notice. Once you're registered, your tax number usually has to appear on every invoice you send.</p>
HTML,
    ],

    [
      'h2' => 'Step 2: Charge the right rate and record what you collect',
      'anchor' => 'charge-the-right-rate',
      'step_name' => 'Charge the right rate and record it',
      'step_text' => 'Put the correct tax rate on each invoice, show it as its own line, and record the tax collected as a separate figure from your income.',
      'html' => <<<'HTML'
<p>Once you're registered, tax goes on your invoices. The rate depends on where you are, what you sell, and sometimes where the customer is. Show it as its own line, separate from the subtotal, so the customer can see exactly what they're paying and why.</p>
<p>A clean invoice line-up looks like this:</p>
<ul>
<li>Subtotal (the sum of your line items): <strong>$500.00</strong></li>
<li>Sales tax at 10%: <strong>$50.00</strong></li>
<li>Total due: <strong>$550.00</strong></li>
</ul>
<p>Getting the rate right is the part people get wrong most often. A few traps to watch for:</p>
<ul>
<li><strong>Mixed invoices.</strong> If some line items are taxable and others aren't, tax is calculated per line, not on the whole subtotal. A designer billing $400 for taxable printed materials and $600 for exempt consulting shouldn't charge tax on the full $1,000.</li>
<li><strong>Location-based rates.</strong> In the US especially, the right rate can depend on where the customer is, not where you are, and can combine state, county, and city portions. Two clients in the same state can owe different totals.</li>
<li><strong>Rate changes.</strong> Governments adjust rates. Charging last year's rate leaves you short when you file, and the difference comes out of your own pocket.</li>
</ul>
<p>Whatever rate you land on, record the tax collected as a number in its own right, kept apart from your income. This is the single most useful habit in the whole process. If your $50 of collected tax is quietly folded into a $550 "revenue" figure, you have no clean way to know what you owe at filing time. Keep it separate from the moment it hits your books and the rest is easy. If you're unsure which rate applies to what you sell, our <a href="/tax-on-invoices-country-guide/">country-by-country guide to tax on invoices</a> goes deeper by region.</p>
HTML,
    ],

    [
      'h2' => 'Step 3: Track tax collected against tax you paid',
      'anchor' => 'collected-vs-paid',
      'step_name' => 'Track collected against paid',
      'step_text' => 'Record the tax you collect on sales and the tax you pay on your own business purchases. In many systems you can subtract the tax you paid from the tax you owe.',
      'html' => <<<'HTML'
<p>Here's the part beginners miss, and it often means paying more tax than you had to. In a lot of tax systems (VAT in the UK, GST/HST in Canada, GST in Australia), you don't just hand over everything you collected. You get to subtract the tax you <em>paid</em> on your own business purchases first. What you actually owe is the difference.</p>
<p>So there are two numbers to track, not one:</p>
<ul>
<li><strong>Tax collected.</strong> The sales tax you charged customers on your invoices.</li>
<li><strong>Tax paid.</strong> The sales tax you paid suppliers when you bought things for the business: tools, materials, software, a laptop.</li>
</ul>
<p>Your net position is <strong>Tax Collected minus Tax Paid</strong>. Walk through a quarter for that plumber:</p>
<ul>
<li>Collected $2,000 of tax across all the jobs invoiced.</li>
<li>Paid $600 of tax on parts, fittings, and a new set of tools.</li>
<li>Net owed to the tax office: <strong>$2,000 minus $600 = $1,400.</strong></li>
</ul>
<p>If you'd ignored the tax you paid on purchases, you might have sent the government the full $2,000 and handed over $600 you didn't owe. Over a year that adds up fast. (Rules on what you can claim back differ by country and by what the purchase was for, so check your local rule. In the US retail sales-tax model this "claim back" mostly doesn't apply, but tracking tax paid is still worth it for your expense records and income tax.)</p>
<p>The catch is that this only works if you captured the tax on your purchases when they happened. That means keeping the receipt and recording the tax portion, not just the total. A receipt sitting in a shoebox with the tax line unread is a deduction you'll probably miss. This is exactly the kind of detail that's easy to lose when you're busy, which is why recording expenses as they happen beats a frantic sort-through at filing time. See <a href="/how-to-track-business-expenses-without-spreadsheets/">how to track business expenses without spreadsheets</a> for a simpler way to stay on top of it.</p>
HTML,
    ],

    [
      'h2' => 'Step 4: Set the money aside',
      'anchor' => 'set-the-money-aside',
      'step_name' => 'Set the money aside',
      'step_text' => 'Move the tax you collect into a separate place, ideally a second bank account, so it\'s untouched and ready when the filing deadline arrives.',
      'html' => <<<'HTML'
<p>You know the number you'll owe. Now make sure the cash is actually there when the bill comes due. The safest way is boring and effective: keep the tax somewhere you won't touch it.</p>
{{illustration:tax-jar}}
<p>The simplest version is a second bank account. Every time a customer pays you, move the tax portion of that payment straight into it. When you get paid $550 on that $500 job, sweep $50 across. The account becomes your tax jar. You never see it as spendable money, so you never accidentally spend it, and when the filing deadline lands the money is sitting right there waiting.</p>
<p>If a second account feels like too much, at least know your running tax balance at all times and never let your main account dip below it. The danger with keeping it all in one account is simple: a $10,000 balance feels like $10,000 you can use, even when $1,400 of it belongs to the tax office. Spend against that feeling for a few months and the filing deadline turns into a scramble.</p>
<p>A quick rule of thumb: if you're registered and charging tax, treat your true spendable balance as your bank balance minus the tax you're holding. That one subtraction, done honestly, prevents the most common and most stressful sales-tax mistake there is.</p>
HTML,
    ],

    [
      'h2' => 'Step 5: File and pay on your authority\'s schedule',
      'anchor' => 'file-and-pay',
      'step_name' => 'File and pay on schedule',
      'step_text' => 'File your return and pay the net tax owed by the deadline your tax authority sets, whether that\'s monthly, quarterly, or annually. Software tracks the number; it does not file for you.',
      'html' => <<<'HTML'
<p>Filing is when you tell the tax authority what you collected, what you're claiming back, and pay the difference. How often you do this depends on your authority and your size. Common schedules are monthly, quarterly, and annually, and bigger businesses tend to file more often. When you register, you'll be told your schedule and your deadlines. Write them on the calendar the day you find out.</p>
{{illustration:calendar-due}}
<p>A typical filing goes like this:</p>
<ol>
<li>Add up the tax you collected over the period.</li>
<li>Add up the tax you paid on business purchases (where your system lets you claim it back).</li>
<li>Subtract the second from the first to get the net amount owed.</li>
<li>Submit the return to your tax authority and pay by the deadline.</li>
</ol>
<p>One thing to be crystal clear about: <strong>accounting software does not file or pay the tax for you.</strong> Argo Books, and any honest tool, tracks the numbers and shows you exactly what you owe, but the actual filing happens on your government's website or through your accountant or filing service. Anyone claiming their app "handles your sales tax" end to end is stretching the truth. What good software does is make the number trustworthy and instant, so filing is a five-minute job of copying figures across instead of an afternoon of adding up invoices by hand.</p>
<p>Missing a deadline usually means penalties and interest, and those stack up whether or not you had the money set aside. If you followed Step 4, the cash is ready; the only job left is to not miss the date.</p>
HTML,
    ],

    [
      'h2' => 'Common mistakes to avoid',
      'anchor' => 'common-mistakes',
      'html' => <<<'HTML'
<p>Almost every sales-tax problem comes down to one of three habits. Knowing them in advance is half the battle.</p>
<ul>
<li><strong>Spending the tax.</strong> The big one. You treat collected tax as income, spend against it, and come up short at filing time. The fix is Step 4: keep it separate so it's never in the pile you think of as yours.</li>
<li><strong>Charging the wrong rate.</strong> Using an out-of-date rate, applying tax to exempt items, or charging one flat rate when the customer's location calls for another. You either short yourself and pay the gap out of pocket, or overcharge and have to make it right with the customer. Check your rate against your authority's current published rate, and re-check when you hear about a change.</li>
<li><strong>Forgetting tax paid on expenses.</strong> If your system lets you claim back the tax you paid on purchases and you don't track it, you hand over more than you owe. Every unrecorded receipt with a tax line is money left on the table. Capture the tax portion of every business purchase as it happens.</li>
</ul>
<p>None of these are complicated. They're just easy to let slide when you're busy running the actual business, which is the whole argument for having a system that tracks it quietly in the background.</p>
HTML,
    ],

    [
      'h2' => 'How Argo Books keeps sales tax straight',
      'anchor' => 'how-argo-books-helps',
      'html' => <<<'HTML'
<p>This is exactly the kind of tracking Argo Books is built to do without you thinking about it. When you add tax to an invoice, it's recorded as <strong>Tax Collected</strong>. When you record an expense with tax on it, that goes in as <strong>Tax Paid</strong>. The app keeps the two separate from your income and from each other, and shows your <strong>net position: Collected minus Paid</strong>. That's the number you need at filing time, ready the moment you want it, instead of something you build by hand from a stack of invoices.</p>
{{illustration:report-statement}}
<p>Because tax collected is treated as a liability and kept out of your profit, your net profit figure stays honest. You never have to wonder whether the tax you're holding is quietly inflating how well the business looks. And when it's time to file, the free <a href="/features/report-builder/">Report Builder</a> produces a clean tax summary you can hand straight to your accountant or filing service. It won't file for you (nothing honest will), but it turns filing day into copying a few numbers across rather than a long evening of adding up.</p>
<p>If you snap a photo of a receipt, the AI receipt scanner pulls out the vendor, date, amount, and tax for you, so the tax-paid side of the ledger fills itself in without you keying anything. The whole point is that the jar stays honest on its own, and you get to spend your time on the work that actually earns the money.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'See how the Report Builder turns your tracked tax into a filing-ready summary in one click.',
  'tool_callout_cta' => 'Explore the Report Builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'Do I need to charge sales tax?',
      'a' => 'Only if you\'re registered, and you generally only have to register once your sales cross a threshold or you have a tax connection ("nexus") to a place that taxes what you sell. Below the threshold, most small sellers don\'t charge tax at all. Above it, you must register and start charging. The threshold and the rules vary a lot by country and, in the US, by state, so check your local rule or ask an accountant before you decide either way.',
    ],
    [
      'q' => 'When do I have to register?',
      'a' => 'It\'s tied to your turnover in most places, and the rough signposts are about $30,000 CAD in Canada, around GBP 90,000 in the UK, and about $75,000 AUD in Australia. The US works differently: there\'s no single national threshold, and your duty to register in a given state depends on nexus, such as a location, staff, or enough sales into that state. These numbers move and have exceptions, so treat them as a prompt to check the current rule for your situation rather than a firm cutoff.',
    ],
    [
      'q' => 'How do I set the tax rate on an invoice?',
      'a' => 'Add the tax as its own line, calculated on the taxable subtotal, and show it separately from the total so the customer can see what they\'re paying. Use the current published rate for your area, and remember that if some line items are exempt, tax applies per line rather than to the whole invoice. In Argo Books you set the rate on the invoice and the tax is recorded as Tax Collected automatically, kept apart from your income.',
    ],
    [
      'q' => 'Where should I keep the tax I collect?',
      'a' => 'Somewhere separate from your spending money, ideally a second bank account. Every time a customer pays, move the tax portion of that payment across so you never treat it as spendable. If you\'d rather not open another account, at minimum know your running tax balance and never let your main balance drop below it. The tax you collect isn\'t yours, and the safest way to remember that is to physically keep it apart until you file.',
    ],
    [
      'q' => 'Does Argo Books file the tax for me?',
      'a' => 'No, and no honest accounting tool does. Argo Books tracks your Tax Collected and Tax Paid, shows your net position (Collected minus Paid), and the Report Builder produces a tax summary you can hand to your accountant or filing service. The actual filing and payment happen on your tax authority\'s website or through your accountant. What the app removes is the manual adding-up, so filing becomes copying a ready number across instead of building it from scratch.',
    ],
    [
      'q' => 'What\'s the difference between tax collected and tax paid?',
      'a' => 'Tax collected is the sales tax you charge customers on your invoices. Tax paid is the sales tax you pay suppliers when you buy things for the business. In many systems (VAT, GST, HST) you subtract the tax you paid from the tax you collected and only hand over the difference, so tracking both means you don\'t overpay. In the US retail model you usually can\'t claim purchase tax back the same way, but tracking it still helps your expense and income-tax records.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'plumber',
    'electrician',
    'usa',
  ],

  'related_article_slugs' => [
    'tax-on-invoices-country-guide',
    'how-much-to-set-aside-for-taxes-self-employed',
    'small-business-tax-deductions',
    'how-to-separate-business-and-personal-finances',
  ],
];
