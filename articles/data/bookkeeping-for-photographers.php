<?php
// articles/data/bookkeeping-for-photographers.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'bookkeeping-for-photographers',

  'h1' => 'Bookkeeping for photographers: a simple guide',

  'meta_title' => 'Bookkeeping for Photographers: a Simple Guide | Argo Books',

  'meta_description' => 'A plain guide to bookkeeping for photographers: handling deposits, pricing per shoot, gear deductions, travel costs, and setting tax money aside.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 35,

  'published' => '2026-06-15',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Photography is a lovely business to run and an awkward one to keep books for. The money doesn't arrive in tidy monthly chunks. A wedding pays a deposit in spring and the balance in autumn, a corporate shoot pays net-30, and three weeks go by with nothing coming in at all. Meanwhile you've bought a new lens, paid an editing subscription, driven two hours to a venue, and handed a second shooter some cash on the day.</p>
<p>None of that needs an accountant's brain to handle. It needs a few habits that keep the numbers straight while you remember them, so tax time is calm and so you actually know whether your prices cover your costs. This guide walks through the parts of bookkeeping that trip photographers up most, in plain language, with no jargon.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why a photographer\'s books are tricky',
      'anchor' => 'why-tricky',
      'html' => <<<'HTML'
<p>A shop takes money over the counter and that's the end of it. A photographer's income and costs are spread out, mixed up, and easy to mistime. A few things make it harder than it looks:</p>
<ul>
<li><strong>Income is lumpy and slow.</strong> You might book a wedding a year out, take a deposit now, and not see the balance until the shoot. A good year can still feel like a cash crunch if the timing isn't tracked.</li>
<li><strong>Deposits aren't quite income yet.</strong> A retainer for a shoot six months away has landed in your account, but it isn't really earned until you've done the work. Treating every deposit as spendable income is how photographers get caught short.</li>
<li><strong>Gear is expensive and long-lived.</strong> A camera body or a set of lenses is a big purchase that often can't be written off all in one year. How you record it changes your tax bill.</li>
<li><strong>Everything is mixed-use.</strong> The laptop edits client work and streams films. The car drives to shoots and to the shops. Only the business share counts, and that split has to be tracked.</li>
</ul>
<p>The fixes are all simple habits, not accounting theory. The rest of this guide is those habits.</p>
HTML,
    ],

    [
      'h2' => 'Separate business and personal money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>This is the highest-value thing on the list and it costs nothing. Open a separate bank account, and ideally a separate card, for the photography business. Run every client payment in and every gear purchase, subscription, and travel cost out through them. Keep personal spending on personal accounts.</p>
<p>Why it matters so much: when business and personal money share one account, every tax season turns into going through hundreds of transactions deciding which were work, the lens or the lunch. Mixed accounts also hide whether you're actually making money, because the camera payment sits in the same list as the grocery run. And if a tax authority ever looks closely, clean separation is the difference between a quick check and a deep one.</p>
<p>You don't need a fancy business account with monthly fees. A second basic account in the business name is enough. The discipline is the point: business money in the business account, personal money out of it. Do this one thing and half the year-end mess never starts.</p>
HTML,
    ],

    [
      'h2' => 'Deposits, retainers, and when money counts',
      'anchor' => 'deposits',
      'html' => <<<'HTML'
<p>This is the part most photographers get wrong, and it's worth a few minutes to get right. A deposit or retainer is money a client pays up front to hold a date. It's in your account, but you haven't done the work yet, so in accounting terms it isn't fully earned income until the shoot happens.</p>
<p>Two practical points come out of that. First, don't spend a deposit as if it's profit. If a couple cancels, or the shoot moves, that money may have to go back, and a deposit you've already spent on a new lens is a problem. Treat retainers as money you're holding until you've earned it.</p>
{{illustration:invoice-doc}}
<p>Second, the timing affects which tax year the income lands in, and that depends on your accounting method and your country's rules. On a simple cash basis, you often count the money when it arrives; on an accrual basis, you count it when you've earned it. This is exactly the kind of thing worth one short question to an accountant, because getting it right keeps a January deposit from landing in the wrong year's return. Whatever the answer, the habit is the same: record every deposit, note which shoot it's for and which part is still owed, so the full picture of a booking, deposit plus balance, is always clear.</p>
HTML,
    ],

    [
      'h2' => 'Track money per shoot, not just per month',
      'anchor' => 'per-shoot',
      'html' => <<<'HTML'
<p>This is the habit that turns bookkeeping from a chore into a tool that tells you whether your prices work. For each shoot, track what came in and what went out: the deposit and balance on the income side, and the direct costs on the other, travel to the venue, a second shooter, prints or album costs, props, parking, any gear rented for the day. The gap is what that shoot actually earned.</p>
<p>Do that for a few months and patterns appear fast. You might find weddings pay well but eat a whole weekend once editing is counted, while smaller portrait sessions earn more per hour than you thought. That's the information that lets you price properly and choose the work that's actually worth it, instead of guessing.</p>
<p>It doesn't need special software to start. Give every shoot a short name, the client and date, and tag every cost and payment with it. Receipts are where this usually falls apart, because a pile of untagged receipts at month-end can't be split back to shoots from memory. The fix is to capture each receipt when you get it and note the shoot then. A phone photo, or a <a href="/best-free-ai-receipt-scanner/">receipt-scanning app</a> that lets you tag the job, both work. Doing it on the spot matters more than which tool you use.</p>
HTML,
    ],

    [
      'h2' => 'Gear: deduction now or depreciation over years',
      'anchor' => 'gear',
      'html' => <<<'HTML'
<p>Gear is the biggest expense most photographers have, and how you record it makes a real difference to your tax. The key idea: a small purchase is usually a straight deduction in the year you buy it, while a large one often has to be claimed gradually over several years. That gradual claim is called depreciation, and the threshold for where it kicks in depends on your country's rules.</p>
<ul>
<li><strong>Smaller kit, claimed now.</strong> Memory cards, filters, a cheap reflector, cables, bags. These are usually a simple business cost in the year you buy them. Keep the receipt and record it.</li>
<li><strong>Big-ticket gear, often spread out.</strong> Camera bodies, pro lenses, lighting rigs, a high-end editing computer. These can be large enough that the tax system makes you claim them over their useful life instead of all at once. You still get the full deduction, just spread across years.</li>
<li><strong>Keep the detail for each item.</strong> Note what it was, what it cost, and when you bought it. That record is what lets you, or your accountant, decide correctly between an immediate write-off and a multi-year claim, and what proves the deduction if you're ever asked.</li>
</ul>
<p>The exact thresholds and rules vary a lot by country and change over time, so this is a good place to check your local guidance or ask an accountant. But the bookkeeping habit is universal: keep a clean record of every gear purchase with its date and cost, and the tax treatment can be sorted out from there.</p>
HTML,
    ],

    [
      'h2' => 'Subscriptions, travel, and people you pay',
      'anchor' => 'other-costs',
      'html' => <<<'HTML'
<p>Beyond gear, these are the costs photographers most often forget to claim, which means paying more tax than you owe. Each needs a record kept as you go.</p>
<ul>
<li><strong>Software and subscriptions.</strong> Editing software, cloud storage, your gallery and client-proofing service, your website, a booking system. These recurring costs add up to a real number over a year, and the business share is deductible. The catch is they're easy to forget because they leave the account quietly each month.</li>
<li><strong>Travel and mileage.</strong> Driving to shoots, scouting locations, and trips to suppliers are deductible, but only the business share. Most tax systems let you either log business kilometres and claim a per-kilometre rate, or track all vehicle costs and claim the business percentage. Either way you need a record kept through the year, because nobody remembers a year of driving in April.</li>
<li><strong>Second shooters and assistants.</strong> When you pay another photographer or an assistant for a day, that's a business cost. Pay traceably where you can and keep a record or invoice for each payment. Many countries also want you to report what you paid contractors over the year, so collect their business name and details up front rather than chasing them the following spring.</li>
<li><strong>Mixed personal and business use.</strong> The laptop, the phone, the car, sometimes a camera you also use for personal photos. Only the business percentage is deductible, so estimate a fair split and keep a note of how you worked it out.</li>
</ul>
<p>The theme across all of these is the same as gear: the deduction is only as good as the record. Capture it when it happens and the claim is solid. Try to rebuild it at tax time and you'll undercount, which is just paying extra tax for nothing.</p>
HTML,
    ],

    [
      'h2' => 'Set tax money aside and keep it current',
      'anchor' => 'tax-time',
      'html' => <<<'HTML'
<p>Because photography income arrives in lumps and the tax on it is owed later, the bill can be a shock if you've spent everything as it came in. The fix is a habit, not a calculation:</p>
<ol>
<li><strong>Move a slice of every payment into a tax-savings account as it lands.</strong> A separate account you don't touch. Your accountant can suggest a sensible percentage for your income and region, but the act of setting it aside as you get paid is what keeps the bill from hurting.</li>
<li><strong>Once a month, check your records against the bank.</strong> Run down the month's business transactions and make sure each one is recorded and tagged to a shoot. Catching a missing receipt or a miscategorized cost in the month it happened takes seconds; catching it ten months later takes an afternoon.</li>
<li><strong>Chase anything missing while it's fresh.</strong> A receipt you can't find or a second shooter's details you never collected is easy to fix this month and painful in April.</li>
<li><strong>Hand your accountant clean totals.</strong> Income by shoot, costs by category, gear purchases with dates, mileage, and contractor payments, with receipts available if asked. Accountants bill by the hour, so the cleaner your numbers, the smaller the bill.</li>
</ol>
{{illustration:checklist}}
<p>None of this is hard. It's a handful of habits done consistently. The photographers who dread tax season are almost always the ones who left it all to the end. The ones who spend a few minutes a week keeping it current barely notice it, and they get the bonus prize: real numbers that tell them which work is actually worth booking.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books can scan receipts, track costs against each shoot, and keep your photography books current as you go.',
  'tool_callout_cta' => 'Try Argo Books for free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'How should I record a deposit for a shoot months away?',
      'a' => 'Record the money the moment it lands, note which shoot it\'s for, and note how much of the booking is still owed, so the full picture of deposit plus balance is always clear. The trickier question is when it counts as income for tax, which depends on your accounting method and your country. On a simple cash basis you often count it when it arrives; on an accrual basis you count it when the shoot happens and you have earned it. This is worth one short question to an accountant, because it decides which tax year a January deposit belongs in. Whatever the answer, don\'t spend a deposit as profit until the work is done, since cancellations and date changes can mean handing it back.',
    ],
    [
      'q' => 'Can I deduct a new camera all at once, or do I spread it out?',
      'a' => 'It depends on the cost and your country\'s rules. Smaller gear like cards, filters, and cables is usually a straight deduction in the year you buy it. A big purchase like a pro camera body, a set of lenses, or a high-end editing computer is often large enough that the tax system makes you claim it gradually over several years, which is called depreciation. You still get the full deduction, just spread out. The thresholds change by country and over time, so check your local guidance or ask an accountant. The bookkeeping habit is the same either way: keep a record of each gear purchase with its date and exact cost, and the tax treatment can be worked out from there.',
    ],
    [
      'q' => 'How do I handle gear I use for both work and personal photos?',
      'a' => 'Only the business share of a mixed-use item is deductible, so the job is to set a fair split and keep a note of how you reached it. For a camera that shoots paid jobs and family holidays, estimate roughly what percentage of its use is business and apply that to the cost. The same logic covers a laptop that edits client work and streams films, a phone used for bookings and personal calls, and a car driven to shoots and to the shops. Tax authorities accept a reasonable, honest estimate far more readily than a guess made up at year-end, so jot down your reasoning when you buy the item, while the split is fresh.',
    ],
    [
      'q' => 'Do I need accounting software, or is a spreadsheet enough?',
      'a' => 'A spreadsheet is genuinely enough to start, especially if you shoot a small number of jobs and keep it current. Plenty of photographers run for years on a separate bank account, a spreadsheet tracking income and costs per shoot, and a folder of receipt photos. Software earns its place as volume grows: lots of shoots, lots of gear and subscription receipts, second shooters to track, and travel to log are where typing it all in becomes the bottleneck, and a tool that scans receipts and tags them to shoots saves real time. Start with whatever you will actually keep up with. A simple system used every week beats a powerful one you ignore.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'Argo Books is mentioned once, in a callout you can ignore, and yes, this is the Argo Books site, so read it with that in mind. But the advice here does not depend on our tool. Separating your accounts, handling deposits properly, tracking costs per shoot, recording gear with its date and cost, and setting tax money aside are habits that work with a spreadsheet, a notebook, or any accounting app. If you take only the habits and never look at Argo Books, the guide did its job. We would rather you price your work properly and claim every deduction you are owed than buy software you don\'t need.',
    ],
  ],

  'related_niche_slugs' => [
    'photographer',
    'freelance',
    'designer',
  ],

  'related_article_slugs' => [
    'bookkeeping-for-freelancers',
    'small-business-tax-deductions',
    'how-to-track-business-expenses-without-spreadsheets',
  ],
];
