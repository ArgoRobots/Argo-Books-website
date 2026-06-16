<?php
// articles/data/bookkeeping-for-food-trucks.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-food-trucks',

  'h1' => 'Bookkeeping for food trucks: a simple guide',

  'meta_title' => 'Bookkeeping for Food Trucks: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to food truck bookkeeping: tracking daily cash sales, food cost and spoilage, fuel and permits, staff pay, and protecting thin margins.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 45,

  'published' => '2026-06-15',

  'updated' => '2026-06-15',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A food truck looks simple from the outside: cook good food, take the money, drive home. The books behind it are anything but. Cash comes in fast all day, ingredients spoil if you over-buy, fuel and the generator burn money before you sell a thing, and the commissary, the permits, and the staff all want paying on a margin that's thin to begin with.</p>
<p>That thin margin is exactly why the bookkeeping matters more for a food truck than for a lot of other small businesses. A few dollars of waste per service, untracked, is the difference between a good month and a break-even one. You don't need an accountant's brain to stay on top of it. You need a short daily habit and a short monthly one. This guide walks through both, in plain language, with no jargon.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why food truck books are their own thing',
      'anchor' => 'why-different',
      'html' => <<<'HTML'
<p>Most small businesses can do their books weekly and be fine. A food truck can't, because too much happens in cash, too fast, and too much of what you buy can go off before it sells. A few things make it harder than a typical shop:</p>
<ul>
<li><strong>Heavy cash handling.</strong> A big share of sales is still cash, and cash with no record simply disappears from the books. Cash you don't record causes two problems: you lose an accurate picture of how the business is actually doing, and you have nothing to back up your numbers if a tax authority ever asks how they add up.</li>
<li><strong>Food cost and spoilage.</strong> You buy ingredients hoping to sell them. What you don't sell, or what goes off, is money gone. Tracking food cost against sales is how you find out if your portions and prices actually work.</li>
<li><strong>Costs before you open the window.</strong> Fuel to get to the pitch, the generator, the commissary rent, permits and licences. These are spent whether you have a good day or a bad one, so they have to be counted to know your real break-even.</li>
<li><strong>Thin margins, so small leaks matter.</strong> When the gap between cost and price is small, a little waste or a little untracked spending eats the profit fast. Tracking is what lets you spot the leak.</li>
</ul>
<p>The fixes are all simple habits done on a schedule, not complicated accounting. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Separate business and personal money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>This is the highest-value thing on the list and it costs nothing. Open a separate bank account, and ideally a separate card, for the truck. Bank your takings into it, and run every supply run, fuel fill, and permit fee out of it. Keep personal spending on personal accounts.</p>
<p>Why it matters so much: when business and personal money share one account, every tax season turns into going through hundreds of transactions deciding which were the truck and which were your own life. Mixed accounts also hide whether the truck actually makes money, because the propane refill sits in the same list as a family dinner. And with all the cash a truck handles, a tax authority is more likely to look closely, so clean separation is worth a lot.</p>
<p>You don't need a fancy business account with monthly fees. A second basic account in the business name is enough. The discipline is the point: takings in, business costs out, personal money kept well clear. Do this one thing and half the year-end mess never starts.</p>
HTML,
    ],

    [
      'h2' => 'Record your sales every single day',
      'anchor' => 'daily-sales',
      'html' => <<<'HTML'
<p>This is the daily habit, and it's the one that makes or breaks a food truck's books. At the end of each service, before you drive off or while you wait to, record the day's sales: the card total from your reader, and the cash counted from the till. Write down both, dated, every day.</p>
<p>Cash is the part that needs the discipline. A card payment leaves a record automatically; cash only exists in the books if you count it and write it down. The clean routine is to start each day with a known float, count what's in the till at the end, subtract the float, and that's your cash sales. Bank the cash regularly rather than letting it pile up, so the deposit in your account lines up with the sales you recorded.</p>
<p>Doing this daily, not weekly, is the whole point. By the next day you won't remember whether Tuesday was busy or quiet, and a week of guessed totals is a week of books you can't trust. A till reading and a cash count take two minutes at close. That two minutes is what gives you a sales figure you can actually rely on, and a clear answer if anyone ever asks where the cash came from.</p>
HTML,
    ],

    [
      'h2' => 'Track food cost, inventory, and spoilage',
      'anchor' => 'food-cost',
      'html' => <<<'HTML'
<p>Food cost is the number that decides whether a food truck makes money, and it's the one most owners track by gut feel. The idea is simple: compare what you spent on ingredients against what you sold, and you learn your food cost as a share of sales. If that share creeps up, your profit is quietly leaking, and you want to know why.</p>
<p>You don't need a full warehouse inventory system to do this. A simple approach works for most trucks: keep every ingredient and supply receipt, and do a rough stock check at sensible intervals, weekly is fine for many. Note what you're throwing away, too, because spoilage is real money and it's the first thing to fix. If you're over-buying a perishable and binning the rest each week, that's a portion size or an ordering habit you can change once you can see it.</p>
<p>The bigger your menu and the more you carry, the more a proper system helps, and the guide on <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> covers when it's worth stepping up. But the starting habit is just this: keep the supply receipts, watch what you waste, and compare ingredient spend to sales each month. That comparison is where a thin-margin business finds its money.</p>
HTML,
    ],

    [
      'h2' => 'Supplies, fuel, the generator, and the truck',
      'anchor' => 'running-costs',
      'html' => <<<'HTML'
<p>Beyond ingredients, a truck has a stack of running costs, and each one is a deduction only if you keep the record. Capture each as it happens, because none of them rebuild accurately at year-end.</p>
<ul>
<li><strong>Ingredients and supplies.</strong> Every food and packaging purchase, napkins, containers, cups, cleaning supplies. Keep the receipt. The small runs add up fast across a year of service.</li>
<li><strong>Fuel and the generator.</strong> Fuel to drive to pitches and to run the generator is a business cost. If the same vehicle does personal driving too, only the business share counts, so keep a mileage record or track all vehicle costs and claim the business percentage. Propane and generator fuel for cooking are straight business costs, kept by receipt.</li>
<li><strong>The truck and equipment.</strong> Repairs, tyres, insurance, and servicing are deductible. The truck itself and big equipment like a new griddle or fridge are often large enough to claim over several years rather than all at once, so keep each purchase with its date and cost.</li>
<li><strong>Commissary, permits, and licences.</strong> Commissary or kitchen rent, health permits, vending licences, and pitch fees are all business costs. They're easy to forget because they're occasional or annual, so record them when you pay so they don't drop out of the books.</li>
</ul>
<p>The theme is the same across all of these: the deduction is only as good as the record. Capture it when it happens and the claim is solid. Try to reconstruct a year of fuel and supply runs in April and you'll undercount, which is just paying extra tax for nothing.</p>
HTML,
    ],

    [
      'h2' => 'Paying staff and casual help',
      'anchor' => 'staff',
      'html' => <<<'HTML'
<p>Most trucks need a hand at busy services, and how you pay matters for both the books and the law. The money you pay staff is a business cost, but paying people brings rules that vary a lot by region, so this is one to get right.</p>
<ul>
<li><strong>Know whether they're an employee or a contractor.</strong> This isn't your choice to make freely; it depends on how the work is structured, and your country sets the test. Employees usually mean payroll, tax withholding, and possibly other obligations; a genuine contractor invoices you. Getting the classification wrong can be costly, so check your local rules or ask an accountant if you're unsure.</li>
<li><strong>Pay traceably and keep records.</strong> Pay by transfer or through proper payroll rather than untracked cash where you can, and keep a record of every payment. Cash wages with no paperwork are both a lost deduction and a problem if anyone asks.</li>
<li><strong>Collect details up front.</strong> Whether it's payroll details for an employee or a contractor's business name and tax number, get them before the first payday, not chased down later.</li>
</ul>
<p>Argo Books, for the record, doesn't include built-in payroll, so a truck with employees will run payroll through a separate payroll service or an accountant and bring the totals into the books. That's a normal setup, and it's worth knowing up front when you're choosing tools.</p>
HTML,
    ],

    [
      'h2' => 'A daily habit and a monthly habit',
      'anchor' => 'habits',
      'html' => <<<'HTML'
<p>Everything above comes down to two routines. Keep these and a food truck's books stay trustworthy and tax time stays calm:</p>
<ol>
<li><strong>Daily, at close:</strong> record card and cash sales, count the till against the float, and bank cash regularly. Capture the day's receipts, fuel, supplies, anything, with a quick photo so they don't get lost in the truck.</li>
<li><strong>Monthly, check your records against the bank.</strong> Run down the month's business transactions and make sure each is recorded and categorized, and that your banked cash matches your recorded cash sales. A gap caught this month takes seconds; the same gap found in April takes an afternoon.</li>
<li><strong>Monthly, look at food cost.</strong> Compare ingredient spend to sales and note your waste. This is where a thin-margin business finds the leak and fixes it before it eats a whole month.</li>
<li><strong>Set tax money aside as you bank takings.</strong> Move a slice into a separate account so the bill isn't a shock. Your accountant can suggest a sensible percentage for your situation.</li>
</ol>
<p>None of this is complicated. It's a two-minute habit at close and a short session once a month. The truck owners who dread tax season are the ones who never counted the cash and let the receipts blow around the cab. The ones who keep the daily and monthly habits know exactly where they stand, and on a thin margin, knowing where you stand is what keeps the truck on the road.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books can scan supply receipts, track your costs against sales, and keep your food truck books current as you go.',
  'tool_callout_cta' => 'Try Argo Books for free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'How do I track cash sales properly?',
      'a' => 'Count it and write it down every single day, because cash only exists in your books if you record it. The clean routine is to start each service with a known float, count the till at close, subtract the float, and that figure is your cash sales for the day. Record it dated alongside the card total from your reader. Bank the cash regularly so the deposits in your account line up with the sales you recorded, which makes your books easy to back up if a tax authority ever asks. The key is doing it daily, not weekly. By the next day you will not remember whether a service was busy or quiet, so a week of guessed cash totals is a week of books you cannot trust.',
    ],
    [
      'q' => 'How do I work out my food cost?',
      'a' => 'Compare what you spent on ingredients against what you sold over the same period, which gives your food cost as a share of sales. Keep every ingredient and supply receipt, do a rough stock check at sensible intervals, weekly works for many trucks, and note what you throw away, because spoilage is real money leaving the business. If your food cost share creeps up month to month, your profit is leaking and the stock check and waste notes tell you where. You don\'t need a full warehouse system to start. The light-touch version, receipts plus a weekly look at stock and waste, is enough to find the leaks on a thin margin, and you can step up to proper inventory tracking as your menu grows.',
    ],
    [
      'q' => 'Does Argo Books handle payroll for my staff?',
      'a' => 'No, Argo Books does not include built-in payroll, and that is worth knowing up front if your truck has employees. Payroll is region-specific and involves tax withholding and other obligations that vary by country, so a truck with staff typically runs payroll through a dedicated payroll service or an accountant, then brings the wage totals into the books as a cost. That is a normal and common setup. If built-in payroll is a must-have for you, factor that into your choice of accounting tool. Either way, get the employee-versus-contractor classification right first, since that is set by your local rules and getting it wrong can be expensive.',
    ],
    [
      'q' => 'Do I need accounting software, or is a spreadsheet enough?',
      'a' => 'A spreadsheet can work for a brand-new, simple truck if you keep it current daily, but food trucks hit the limits of a spreadsheet faster than most businesses. The daily cash counts, the constant supply receipts, the fuel and permit costs, and the food-cost comparison add up to a lot of entry, and the volume is where manual typing becomes the bottleneck and the errors start. Software that scans receipts and tracks costs against sales saves real time once you are running regular services. Start with whatever you will actually keep up with every day, because on a thin margin the books only help if they are current. A simple system used daily beats a powerful one you fall behind on.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned in a callout you can ignore, and yes, this is the Argo Books site, so read it with that in mind. We have also said plainly that Argo Books has no built-in payroll, which matters if you have staff. The advice in this guide does not depend on our tool. Recording cash daily, tracking food cost and waste, keeping fuel and permit receipts, and setting tax money aside are habits that work with a spreadsheet, a notebook, or any accounting app. If you take only the habits and never look at Argo Books, the guide did its job. On a thin margin, what matters is that you can see your numbers, not which software shows them to you.',
    ],
  ],

  'related_niche_slugs' => [
    'generic',
    'contractor',
    'freelance',
  ],

  'related_article_slugs' => [
    'small-business-bookkeeping-basics',
    'inventory-tracking-for-small-businesses',
    'how-to-track-business-expenses-without-spreadsheets',
  ],
];
