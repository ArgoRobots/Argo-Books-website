<?php
// articles/data/how-to-price-your-services.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-price-your-services',

  'h1' => 'How to price your services as a freelancer',

  'meta_title' => 'How to Price Your Services as a Freelancer | Argo Books',

  'meta_description' => 'How to price your services as a freelancer: the three pricing approaches, your true hourly cost, billing models, and when to raise your rates.',

  'schema_type' => 'HowTo',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 120,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Most people set their first rate by copying a number they heard somewhere, then quietly wondering if it's too high or too low. That's a coin flip, and it usually lands on too low. Pricing isn't a personality test or a confidence trick. It's arithmetic with a strategy on top, and both parts are learnable in an afternoon.</p>
<p>This guide walks through the whole decision in order. First the three ways to think about a price, then the number you can't skip: what an hour of your time actually costs you to sell. From there you'll pick a billing model, learn the warning signs that you're too cheap, and get a script for raising rates without losing the clients you already have. By the end you'll have a rate you can defend, and a way to check whether it's actually leaving you money.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The three ways to price anything',
      'anchor' => 'three-approaches',
      'step_name' => 'Understand the three pricing approaches',
      'step_text' => 'Learn cost-plus, market rate, and value-based pricing, and when each one fits. Most freelancers use a blend, with a cost-plus floor underneath.',
      'html' => <<<'HTML'
<p>Every price sits on one of three foundations. You'll usually blend them, but it helps to know which one you're leaning on.</p>
<ul>
<li><strong>Cost-plus.</strong> Work out what the job costs you to deliver, then add a margin on top. This is the floor, not the ceiling. It answers "what's the least I can charge and still come out ahead?" It's the one number no other method lets you skip, because a price below your cost loses money no matter how the client feels about it.</li>
<li><strong>Market rate.</strong> Charge roughly what comparable people charge for comparable work. It's fast, it's easy to justify to a client, and it keeps you from being wildly out of step. The weakness is that it anchors you to whatever the average is, including the people who are underpricing themselves. Use it as a sanity check, not as the whole answer.</li>
<li><strong>Value-based.</strong> Price against what the result is worth to the client, not the hours it took you. A logo that took you six hours might be worth $8,000 to a company launching a product line, and $400 to a hobbyist selling at a weekend market. Same work, different value, and a fair price reflects that. Value-based pricing pays the most, but it needs you to understand the client's business and it doesn't fit every job.</li>
</ul>
{{illustration:price-tag}}
<p>Here's how they fit together. A new freelancer photographing local real estate listings leans on <strong>market rate</strong>, because the work is a known quantity and clients compare quotes. A bookkeeper who saves a small business owner ten hours a month leans toward <strong>value-based</strong>, because the value is easy to point at. But every single one of them needs the <strong>cost-plus</strong> number underneath, because it's the line they can't drop below without paying for the privilege of working. Get your cost first, then decide how far above it the market and the value let you go.</p>
HTML,
    ],

    [
      'h2' => 'Calculate your true hourly cost',
      'anchor' => 'true-hourly-cost',
      'step_name' => 'Calculate your true hourly cost',
      'step_text' => 'Add your target take-home income, business expenses, and tax, then divide by your realistic billable hours, not all 2,080 hours in a work year. That is the rate that just breaks even.',
      'html' => <<<'HTML'
<p>This is the step nobody wants to do, and it's the one that changes everything. The mistake almost everyone makes is dividing the salary they want by 2,080 hours (40 hours a week times 52 weeks) and calling that their rate. That number is a trap, and it will keep you broke. Here's why, and how to do it properly.</p>
<p><strong>Start with three things you actually need to cover:</strong></p>
<ul>
<li><strong>Your target take-home income.</strong> The money you want to keep and live on. Say $60,000 a year.</li>
<li><strong>Your business expenses.</strong> Software, equipment, insurance, your phone and internet share, accounting tools, professional memberships, marketing. Add them up for the year. Say $9,000.</li>
<li><strong>Your tax.</strong> As a freelancer you pay income tax plus self-employment or national insurance style contributions, and nobody withholds it for you. A rough planning figure of 25% to 30% of profit is common, but it varies a lot by country and income. Say you set aside 28%.</li>
</ul>
<p>Now the part everyone skips: <strong>not all your hours are billable.</strong> Out of a 2,080-hour work year, a big chunk goes to things you can't put on an invoice: finding clients, writing proposals, sending invoices, doing your own books, admin, holidays, sick days, and learning. For most freelancers, realistically billable hours land somewhere between <strong>1,000 and 1,400 a year</strong>, not 2,080. A useful planning number is around <strong>25 billable hours a week</strong>, which is about 1,200 hours a year once you take a few weeks off.</p>
<p><strong>Now the math, with real numbers:</strong></p>
<ul>
<li>Target take-home income: <strong>$60,000</strong></li>
<li>Business expenses: <strong>+$9,000</strong></li>
<li>Subtotal you need before tax: <strong>$69,000</strong></li>
<li>Gross up for tax at 28% (so that after tax you're left with what you need). $69,000 divided by (1 minus 0.28) = <strong>$95,833</strong></li>
<li>Divide by realistic billable hours: $95,833 divided by <strong>1,200 hours</strong> = <strong>$80 per hour</strong></li>
</ul>
<p>So your break-even rate is about <strong>$80 an hour</strong>. That is not your selling price. That's the floor, the point where you've covered your pay, your costs, and your tax and made zero profit on top. If you had naively divided $60,000 by 2,080, you'd have landed on $29 an hour and been losing money on every job while feeling busy. The gap between $29 and $80 is why so many freelancers work constantly and still can't get ahead.</p>
{{illustration:coins}}
<p>Run this with your own numbers. Change the income you want, your real expenses, and an honest count of billable hours, and you get a floor you can trust. Then market rate and value tell you how far above $80 you can actually charge. If comparable work in your area goes for $110 an hour, you have real room. If it goes for $70, you have a problem to solve: cut costs, work faster, move upmarket, or find better-paying clients.</p>
HTML,
    ],

    [
      'h2' => 'Choose a billing model',
      'anchor' => 'billing-model',
      'step_name' => 'Choose a billing model',
      'step_text' => 'Pick how you package the price: hourly, fixed-fee, or value-based. Each trades predictability against upside differently.',
      'html' => <<<'HTML'
<p>Your hourly cost tells you what you need to earn. How you package that into a price the client agrees to is a separate choice, and it's worth getting right because it changes how much you make on the same work.</p>
<ul>
<li><strong>Hourly.</strong> You bill for time spent. Best when the scope is genuinely unknown: ongoing support, open-ended consulting, or maintenance work. The upside is you never work for free, because every hour is paid. The downside is that your income is capped by hours in the day, and getting faster and better literally earns you less per project, which is backwards. Clients also feel every hour, so hourly invites the client to watch the clock.</li>
<li><strong>Fixed-fee.</strong> One agreed price for a defined deliverable: a website, a logo, a set of edited photos, a month of bookkeeping. Best when both sides understand exactly what's included. The upside is huge: if you get faster, you keep the difference, so skill pays off. The client also knows the total up front, which they usually prefer. The risk is scope creep, so you have to write down exactly what's included and what counts as a change order (two revision rounds is a common ceiling, and anything past that is billed extra).</li>
<li><strong>Value-based.</strong> The price is tied to the outcome, and it's usually the highest of the three. A consultant who helps a client win a $200,000 contract can charge a fee that reflects a slice of that, far more than the hours would suggest. Best for work with a clear, valuable result you can point at. It needs trust, a real conversation about the client's goals, and the confidence to name a big number. It doesn't fit commodity work where the client is comparing three near-identical quotes.</li>
</ul>
<p>A practical path for most freelancers: start hourly while you learn how long things actually take, then move to fixed-fee as soon as you can estimate a job confidently, because fixed-fee is where your income stops being tied to the clock. Keep hourly on the shelf for the genuinely open-ended work. Layer in value-based pricing for the clients and projects where the result is worth real money. For more on packaging the invoice itself, see <a href="/how-to-invoice-clients/">how to invoice clients</a>.</p>
HTML,
    ],

    [
      'h2' => 'The underpricing trap',
      'anchor' => 'underpricing-trap',
      'step_name' => 'Check whether you are too cheap',
      'step_text' => 'Watch for the signs of underpricing: every prospect says yes, you are fully booked but broke, and low-price clients demand the most. If you see them, your rate is too low.',
      'html' => <<<'HTML'
<p>Underpricing is the default failure mode for freelancers, and it's sneaky because being cheap feels safe. It isn't. A rate that's too low doesn't just earn you less per hour, it fills your schedule with the wrong work and leaves no room to raise it later. Here are the signs your price is too low:</p>
<ul>
<li><strong>Almost everyone says yes.</strong> If nobody ever pushes back on your price and you close nearly every quote, your rate is too low. A healthy win rate involves losing some jobs on price. If you're winning them all, you're leaving money on the table on every single one.</li>
<li><strong>You're fully booked and still broke.</strong> This is the clearest sign. If you're working constantly and the bank account doesn't reflect it, the problem isn't effort, it's the rate. You can't fix a pricing problem by working more hours, because the hours are the thing that's underpriced.</li>
<li><strong>Your cheapest clients are the most demanding.</strong> It's a well-worn pattern: the clients who pay the least ask for the most, question every hour, and take the longest to pay. Clients who pay a professional rate tend to treat you like a professional. Bargain-hunters treat you like a bargain.</li>
<li><strong>The math doesn't clear your floor.</strong> If your effective rate (total earned divided by all the hours you actually worked, including the unbilled admin) comes in under the break-even number you calculated earlier, you're subsidising your clients out of your own pocket. That's not a business, it's an expensive hobby.</li>
</ul>
<p>The fix is uncomfortable but simple: raise your rate. New freelancers routinely undercharge by 30% to 50% and don't find out until they finally test a higher number and clients keep saying yes. The only way to know where the ceiling is, is to walk toward it. Raise the rate on your next new quote and watch what happens. Losing the occasional price-sensitive prospect is the goal, not a failure.</p>
HTML,
    ],

    [
      'h2' => 'How and when to raise your rates',
      'anchor' => 'raise-rates',
      'step_name' => 'Raise your rates',
      'step_text' => 'Raise the price on new clients first, then existing clients on a clear schedule. Give notice, keep the message short, and do not over-explain or apologize.',
      'html' => <<<'HTML'
<p>Raising rates is two different jobs, and people jam them together and freeze. Keep them separate.</p>
<p><strong>New clients: just quote the higher number.</strong> The easiest place to raise your rate is the next quote you send to someone who has never worked with you. They have no old price to compare against. Nudge it up, send it, and see if it lands. There's no conversation to have and no relationship at risk. Do this regularly and your average rate climbs on its own. Good triggers to bump the new-client rate: you're booked solid, you've added a skill or a result you can point to, or it's simply been a year.</p>
<p><strong>Existing clients: give notice and keep it short.</strong> This is the one that makes people sweat, and it's more routine than it feels. Existing clients expect prices to rise over time, the same way every service they buy does. The rules that make it painless:</p>
<ul>
<li><strong>Give real notice.</strong> Tell ongoing clients 30 to 60 days ahead of the new rate, so it doesn't land as a surprise on the next invoice. A clean point to do it is the new year or a contract renewal.</li>
<li><strong>Keep the message to three sentences.</strong> State the new rate, state when it takes effect, and thank them. Do not write a paragraph of justification. The more you explain and apologize, the more you signal that the increase is negotiable. Here's a script you can copy: <em>"Hi [name], a quick heads-up that my rate will move to $[new rate] starting [date]. I've really valued working with you and wanted to give you plenty of notice. Happy to answer any questions."</em></li>
<li><strong>Modest and regular beats rare and huge.</strong> A 5% to 15% bump that clients see coming is far easier to accept than a 50% jump after three years of holding the price flat. If you've frozen your rate for years, you may need one larger correction, but after that, small and predictable is the way.</li>
<li><strong>Be ready to lose one.</strong> Occasionally a client leaves over a price rise. That's usually the client who was paying you the least and demanding the most, which frees up room for a better one. A rate that no client ever objects to is a rate that's too low.</li>
</ul>
<p>Set a standing reminder to review your rates once a year. It stops the price from drifting for so long that the eventual correction feels dramatic to everyone.</p>
HTML,
    ],

    [
      'h2' => 'Let your real numbers set the price',
      'anchor' => 'real-numbers',
      'step_name' => 'Base your price on your actual numbers',
      'step_text' => 'Track your expenses and net profit so your pricing runs on real figures, not guesses. Your true costs and what a rate actually leaves you come straight from your own books.',
      'html' => <<<'HTML'
<p>Every step so far runs on numbers: your real expenses, your real billable hours, your real tax set-aside, and whether a given rate actually leaves you money once everything is paid. Guess at those and your carefully calculated rate is built on sand. The whole point of pricing from cost is that the cost has to be true.</p>
<p>This is where your books do the work. When you track your expenses in <a href="/features/expense-revenue-tracking/">Argo Books</a>, the business-expenses figure in your hourly-cost math stops being a guess and becomes the real total: software, insurance, equipment, your phone and internet share, all categorized in one place. Snap a photo of a receipt and the app pulls out the vendor, date, and amount, so the costs actually get recorded instead of piling up in a drawer. A year of clean expense records is exactly the input the cost-plus calculation needs.</p>
{{illustration:forecast}}
<p>The other half is net profit. Argo Books calculates it as revenue (not counting the sales tax you collect, since that's owed to the government and was never yours) minus your expenses minus any refunds. That's the number that tells you the truth about a rate. You can be charging what looks like a healthy hourly price and still watch net profit come out thin once costs and tax are in the picture. Seeing that early lets you fix the rate before a year goes by. And because the Report Builder turns your data into an income statement showing revenue against expenses, you can look back over a quarter and see plainly whether the price you set is actually leaving money in the business. Pricing is a decision you make once and then check against reality. Argo Books is where you check it.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 1,

  'tool_callout_text' => 'Track your real expenses and net profit in Argo Books, so your pricing runs on actual numbers instead of guesses.',
  'tool_callout_cta' => 'See expense and revenue tracking',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'How do I set my hourly rate?',
      'a' => 'Start from your break-even cost, not from a number you heard. Add up the take-home income you want, your yearly business expenses, and the tax you have to set aside, then divide the total by your realistically billable hours, which for most freelancers is around 1,000 to 1,400 a year, not the full 2,080. That gives you the floor: the rate where you cover everything and make zero profit. Your real selling price sits above that floor, as high as the market rate and the value of your work let you go. If comparable work in your area pays well above your floor, you have room to charge more.',
    ],
    [
      'q' => 'Should I charge hourly or a fixed price?',
      'a' => 'Charge hourly when the scope is genuinely unknown, like ongoing support or open-ended consulting, because it means you never work for free. Charge a fixed price once you can estimate a job with confidence, because fixed-fee lets you keep the upside when you work faster, and clients usually prefer knowing the total in advance. Most freelancers start hourly to learn how long things really take, then shift to fixed-fee for defined deliverables. The one rule for fixed-fee work is to write down exactly what is included and what counts as a change order, so extra requests get billed instead of eating your margin.',
    ],
    [
      'q' => 'How do I know if I am charging too little?',
      'a' => 'The clearest sign is being fully booked and still broke: if you are working constantly and the money does not reflect it, the rate is the problem, not your effort. Other signs are that almost every prospect says yes with no pushback, and that your cheapest clients are the ones who demand the most and pay the slowest. Do the math too: divide everything you earned by every hour you actually worked, including unbilled admin, and if that effective rate is under your break-even cost, you are paying to work. New freelancers commonly undercharge by 30% to 50% without realizing it until they test a higher number.',
    ],
    [
      'q' => 'How do I raise rates with existing clients?',
      'a' => 'Give 30 to 60 days of notice and keep the message to three sentences: the new rate, the date it starts, and a thank-you. Do not write a long justification or apologize, because the more you explain, the more the increase sounds negotiable. A new year or a contract renewal is a natural point to do it. Keep increases modest and regular, in the 5% to 15% range, so clients see them coming, which is far easier to accept than a large jump after years of holding the price flat. Be prepared to lose the occasional client over it, usually the lowest-paying and most demanding one, which frees up room for a better fit.',
    ],
    [
      'q' => 'Should I factor tax into my pricing?',
      'a' => 'Yes, always. As a freelancer nobody withholds tax for you, so a rate that ignores tax quietly overstates what you actually keep. Build it in by grossing up: work out the pre-tax income you need, then divide by one minus your tax rate so the after-tax amount lands where you want it. A rough planning figure of 25% to 30% of profit is common, but it varies a lot by country and income level, so check with an accountant for your situation. One separate but related point: sales tax, GST, HST, or VAT that you charge on top of your price is never yours to keep and should not be treated as income when you judge whether a rate is working.',
    ],
    [
      'q' => 'What is the difference between cost-plus and value-based pricing?',
      'a' => 'Cost-plus starts from what the job costs you to deliver and adds a margin, which sets the floor you must not drop below. Value-based starts from what the result is worth to the client and prices against that, which usually earns the most. The same six hours of design work might be worth $400 to a hobbyist and $8,000 to a company launching a product line, and value-based pricing captures that difference while cost-plus would charge both the same. In practice you use both: cost-plus is the line you never go under, and value-based tells you how high above that line you can reasonably go for a given client.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'designer',
    'developer',
  ],

  'related_article_slugs' => [
    'gross-profit-vs-net-profit',
    'how-to-invoice-clients',
    'how-much-to-set-aside-for-taxes-self-employed',
    'what-counts-as-a-business-expense',
  ],
];
