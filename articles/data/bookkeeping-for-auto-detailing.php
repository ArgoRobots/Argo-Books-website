<?php
// articles/data/bookkeeping-for-auto-detailing.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-auto-detailing',

  'h1' => 'Bookkeeping for auto detailing businesses',

  'meta_title' => 'Bookkeeping for Auto Detailing Businesses | Argo Books',

  'meta_description' => 'Bookkeeping for auto detailing: track product usage and supplies, price packages for real margin, handle cash and card at the car, mobile costs, and sales tax.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 52,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 10,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Detailing looks like a simple business from the outside. A car comes in dirty, it leaves clean, and someone hands you cash or taps a card. But the money side has a quiet trap in it: the soaps, waxes, pads, and coatings you use disappear a little at a time on every job, and most detailers never add up what a single wash or full correction actually costs them in product. That's how a shop can stay busy all summer and still wonder where the money went.</p>
<p>This guide walks through the bookkeeping that actually matters for a detailing business, whether you're mobile with a van and a water tank or working out of a bay. We'll cover tracking supplies and product usage, pricing your packages so you know the margin on each one, handling cash and card payments at the vehicle, mobile and vehicle costs, deposits, the seasonal swings, sales tax, and a short monthly routine that keeps it all straight.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Track your supplies and product usage as real costs',
      'anchor' => 'supplies-and-product-usage',
      'html' => <<<'HTML'
<p>Every job burns through product. A wash uses shampoo, a bit of tire dressing, some glass cleaner, and a couple of towels through the laundry. A paint correction eats compound, polish, and a stack of pads. A ceramic coating uses a bottle that might cost you $60 to $120 and treats only a handful of cars. None of that shows up on a receipt when the customer pays, so it's the easiest cost in the whole business to forget, and forgetting it is how you end up pricing below what the job really costs.</p>
<p>You don't need to weigh every drop. What you need is a rough cost of product per job for each type of service you sell. Work it out once: a basic wash might use $4 of product, a full interior and exterior detail maybe $18, a ceramic coating $70 or more once you count the coating, prep, and pads. Write those numbers down and update them when your supplier prices jump. That single habit turns "I think I made good money today" into a number you can actually trust.</p>
{{illustration:coins}}
<p>The other half is buying. When you make a supply run for soaps, waxes, coatings, microfiber towels, and buffing pads, log it as a business expense and keep the receipt. Argo Books can scan a receipt into an expense in a few seconds, so a trip to the detailing supplier doesn't turn into a glovebox full of crumpled paper. If you carry a lot of retail-style product, like coating bottles or bulk chemicals you resell or use in packages, Argo's inventory feature can track what you hold and what it cost, which feeds straight into knowing your margin.</p>
HTML,
    ],

    [
      'h2' => 'Price your packages so you know the margin on each one',
      'anchor' => 'package-margins',
      'html' => <<<'HTML'
<p>Most detailers sell packages: a maintenance wash, a full detail, a paint correction, a ceramic coating, maybe add-ons like pet hair removal or engine bay cleaning. Each one has a price on your menu, but the price only tells half the story. The number that matters is what's left after the product, the labor time, and the card fees come out.</p>
<p>Run the math on each package the same way. Take the price, subtract your product cost for that job, subtract the value of the hours it takes (yours or an employee's), and subtract the card processing fee if they paid by card. What's left is your real margin. Do this across your menu and things jump out. A ceramic coating that sounds premium might clear less per hour than a quick maintenance wash once you count the coating and the prep time. A cheap add-on might be nearly pure profit. You can't see any of that from the menu price alone.</p>
<p>This is also where honest books change how you sell. If you find that full corrections tie up a whole day for a thin margin, you can raise the price, tighten the package, or push the services that actually pay. You're not guessing anymore. Our guide on <a href="/bookkeeping-for-cleaning-companies/">bookkeeping for cleaning companies</a> covers the same idea for recurring service work, since the pricing trap is nearly identical.</p>
HTML,
    ],

    [
      'h2' => 'Cash and card payments at the vehicle',
      'anchor' => 'cash-and-card',
      'html' => <<<'HTML'
<p>Detailing gets paid at the car, and that means a messy mix of payment types landing in different places. Someone taps a card on your phone reader, someone hands you cash, someone sends an e-transfer or a payment-app transfer while you're rinsing the wheels. Each one has to end up in your books, and cash is the one that quietly slips away.</p>
<ul>
<li><strong>Cash.</strong> It's the easiest income to lose track of and it's still taxable. Record every cash job the same day, before the notes get spent on gas or lunch and vanish from memory. A market wash that felt like a good afternoon isn't in your books until you write it down.</li>
<li><strong>Card and app payments.</strong> Card readers and payment apps take a small cut of every transaction. That fee is a real, deductible business cost, so record the full price the customer paid as income and the processing fee as its own expense. Skip that and both your income and your deductions come out wrong.</li>
<li><strong>Invoices for bigger jobs.</strong> For corrections, coatings, fleet work, or dealership accounts, send a proper invoice instead of scribbling a total. It gives the customer a record, makes card or bank payment easy, and drops a clean line straight into your books. Argo Books can create and send the invoice and track whether it's been paid.</li>
</ul>
<p>The goal is simple: whether the money came as cash, card, or transfer, it all lands in one set of books so your income total is the whole picture, not just the part that happened to hit your bank account.</p>
HTML,
    ],

    [
      'h2' => 'Mobile costs and vehicle expenses',
      'anchor' => 'mobile-and-vehicle',
      'html' => <<<'HTML'
<p>If you detail on the road, your van or truck is part of the business, and so is a real list of costs that shop-based detailers don't carry. These are legitimate expenses, and tracking them is what keeps your true profit honest instead of flattering.</p>
<ul>
<li><strong>Fuel and travel</strong> to and from each job.</li>
<li><strong>Vehicle upkeep,</strong> oil changes, tires, repairs, and insurance on your work vehicle.</li>
<li><strong>Water and power</strong> if you run a tank, a generator, or a pressure washer.</li>
<li><strong>Equipment,</strong> the polishers, extractors, vacuums, and hoses that wear out and get replaced.</li>
</ul>
<p>Log each of these as a business expense and keep the receipts. Argo Books tracks these as expenses, which covers the fuel, upkeep, equipment, and supply side well. One honest note, though: Argo does not automatically track your mileage. If your tax authority lets you claim a per-kilometer or per-mile rate for business driving, you'll need a separate log of the distance you drive, whether that's a small mileage app or a notebook in the glovebox, and then you enter the deduction as an expense. Don't skip it, because for a busy mobile detailer the driving can add up to a meaningful deduction.</p>
{{illustration:receipt-scan}}
<p>Shop-based detailers have their own version of this list: rent on the bay, utilities, and the water bill can be a bigger line than people expect once you're running wands and extractors all day. Either way, the rule holds. If a cost exists because the business exists, it belongs in your books.</p>
HTML,
    ],

    [
      'h2' => 'Deposits and seasonality',
      'anchor' => 'deposits-and-seasonality',
      'html' => <<<'HTML'
<p>Two things about a detailing calendar make the money bumpier than it looks: deposits on booked work, and the way demand rises and falls through the year.</p>
<p><strong>Booking deposits.</strong> For bigger jobs, corrections and coatings especially, plenty of detailers take a deposit to hold the slot. A deposit is money in your pocket, but it isn't fully earned income until you've done the work. If a customer cancels and you refund it, it was never yours. The clean way to handle it is to record the deposit when it comes in and the balance when the job is finished, so your income reflects work actually done rather than promises on the calendar. Keep it simple, but keep it honest, because a stack of deposits can make a slow month look busier than it really was.</p>
<p><strong>Seasonality.</strong> Detailing swings hard with the weather in most places. Spring cleanups and summer shine jobs pile in, then winter or a rainy stretch can go quiet. If you spend at July's pace in a January lull, you'll feel it. The fix is boring and it works: watch your monthly income and expenses across the whole year, not week to week, and set aside a cushion from the busy months to carry the slow ones. Books that show the pattern let you plan for the dip instead of being surprised by it every single year.</p>
HTML,
    ],

    [
      'h2' => 'Keep personal and business money apart, and mind sales tax',
      'anchor' => 'separate-money-and-tax',
      'html' => <<<'HTML'
<p>Two habits save detailers the most pain at tax time, and both are about keeping things separate.</p>
<p><strong>Separate your personal and business money.</strong> When you're a one-person operation it's tempting to run everything through one account and sort it out later. Later never comes, and you end up guessing which fuel fill-up was a job and which was groceries. Open a dedicated business account and a business card, run every job and every supply run through them, and pay yourself by transferring money out. It costs nothing and it turns tax time from a shoebox archaeology dig into a quick export. Our <a href="/small-business-bookkeeping-basics/">small business bookkeeping basics</a> guide covers this and the other foundations in more depth.</p>
<p><strong>Sales tax awareness.</strong> Depending on where you operate, you may need to charge sales tax, GST, HST, or VAT on detailing services, and the rules genuinely vary by region, so treat this as a heads-up rather than advice and confirm the details with your local tax authority or an accountant. The key bookkeeping habit is to record the tax you collect separately from your actual sales. Tax you collect is money you're holding to pass on, not income, and treating it as income is a classic way to accidentally spend money you owe. Argo Books tracks tax collected against tax paid and gives you a tax summary to work from, but to be clear, it does not file or remit the tax for you. That part is still on you or your accountant.</p>
HTML,
    ],

    [
      'h2' => 'A simple monthly routine',
      'anchor' => 'monthly-routine',
      'html' => <<<'HTML'
<p>Detailing throws off a steady trickle of small transactions: cash jobs, card taps, supply runs, fuel stops. The difference between clean books and a year-end scramble is a short routine done regularly. Once a month, block out an hour and work through this:</p>
<ol>
<li><strong>Record every job's income.</strong> Go through the month and enter each job, cash, card, transfer, and invoice alike, so your income is the whole picture and not just what hit the bank.</li>
<li><strong>Log supply and vehicle costs.</strong> Enter your product buys, fuel, equipment, and any bay rent or utilities, and keep the receipts. Argo Books can scan a supply receipt straight into an expense.</li>
<li><strong>Record card and app fees separately.</strong> Enter the processing fees as their own expense so your margin is honest and every deductible fee is captured.</li>
<li><strong>Update your product-cost-per-job figures.</strong> If supplier prices moved, adjust your cost estimates for each package so your margins stay accurate.</li>
<li><strong>Set aside tax.</strong> Move any sales tax you collected and a slice for income tax out of your spending money, so it's there when it's due.</li>
</ol>
<p>Written out it looks like a lot, but once the habit sticks it's an hour or so a month, less if you scan receipts as you go. The payoff is real. You know the margin on every package, you claim every deduction you're owed, you can see the seasonal pattern coming, and when tax time arrives you export a tax-ready report instead of rebuilding the year from memory. That's the difference between a detailing hustle that just feels busy and a business you can actually steer.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books lets you invoice bigger jobs, take card payments, and track whether each one is paid, so corrections and coatings don\'t slip through the cracks.',
  'tool_callout_cta' => 'See invoicing in Argo Books',
  'tool_callout_url' => '/features/invoicing/',

  'faqs' => [
    [
      'q' => 'How do I figure out what a detailing job actually costs me in product?',
      'a' => 'Work it out once for each type of service you sell. Add up the rough cost of the soaps, dressings, glass cleaner, towels, and any wax or coating that a single job uses. A basic wash might run a few dollars of product, a full detail closer to twenty, and a ceramic coating much more once you count the bottle, the prep materials, and the pads. Write those figures down and revisit them when your supplier prices change. That per-job product cost is the foundation for pricing, because it tells you the real margin on each package instead of leaving you to guess whether a busy day actually made money.',
    ],
    [
      'q' => 'Does Argo Books track my mileage for mobile detailing?',
      'a' => 'No, Argo does not automatically track mileage. It logs your fuel, vehicle upkeep, equipment, and supply costs as expenses, which covers most of what a mobile detailer spends. But if your tax authority lets you claim a per-kilometer or per-mile rate for business driving, you need to keep a separate record of the distance you drive, either with a small mileage app or a notebook in your vehicle. You then enter that deduction as an expense in your books. It is worth the effort, because for a busy mobile operation the driving can add up to a meaningful deduction over a year. Check your local rules for how the rate works.',
    ],
    [
      'q' => 'How should I handle cash payments at the car?',
      'a' => 'Treat every cash job as taxable income and record it the same day, before the notes get spent and forgotten. Cash is the easiest income to lose track of, and unrecorded cash is both inaccurate books and a tax problem waiting to happen. Log the job right after you finish it, or at the end of the day at the latest. If you also take card and app payments, get all of it into one set of books so your income total reflects every job, not just the ones that landed in your bank. Keeping a business account separate from your personal money makes this much easier to keep straight.',
    ],
    [
      'q' => 'Do I need to charge sales tax on detailing services?',
      'a' => 'It depends on where you operate, and the rules genuinely vary by region, so confirm the specifics with your local tax authority or an accountant rather than guessing. Many places do apply sales tax, GST, HST, or VAT to services like detailing once you pass certain thresholds. The important bookkeeping habit, wherever you are, is to record the tax you collect separately from your actual sales. That tax is money you are holding to pass on, not income, so treating it as earnings is a common way to spend money you owe. Argo Books tracks tax collected against tax paid and gives you a summary, but it does not file or remit the tax for you.',
    ],
    [
      'q' => 'How do I record a booking deposit for a coating or correction?',
      'a' => 'Record the deposit as money received when it comes in, but treat the job as fully earned income only once the work is actually done. A deposit holds the slot, yet it is not truly yours until you have detailed the car, and if the customer cancels and you refund it, it was never income at all. The clean approach is to log the deposit when it arrives and the remaining balance when you finish, so your income reflects completed work rather than promises on the calendar. This matters because a run of deposits can make a slow month look busier than it really was, which throws off your sense of how the business is doing.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'cleaning',
    'generic',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-contractors',
    'bookkeeping-for-cleaning-companies',
    'small-business-bookkeeping-basics',
  ],
];
