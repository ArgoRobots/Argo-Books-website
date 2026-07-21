<?php
// articles/data/what-counts-as-a-business-expense.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-counts-as-a-business-expense',

  'h1' => 'What counts as a business expense (and what doesn\'t)',

  'meta_title' => 'What Counts as a Business Expense | Argo Books',

  'meta_description' => 'What counts as a business expense and what doesn\'t: the ordinary-and-necessary test, a clear yes and no list, and how to split mixed personal and business use.',

  'schema_type' => 'Article',

  'category' => 'receipts-expenses',
  'hub_weight' => 30,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Every dollar you spend running your business is a dollar you don't pay tax on. That's the whole reason "what counts as a business expense" matters: a legitimate expense lowers your taxable profit, so getting this right puts real money back in your pocket at tax time. Get it wrong in the other direction, by claiming things that don't qualify, and you're exposed if anyone ever looks closely.</p>
<p>The good news is that the line is clearer than most people think. There's one core test, a short list of things that plainly qualify, a short list of things that plainly don't, and a grey zone in the middle that you handle by splitting the cost. This guide walks through all of it in plain language, with real numbers, so you can look at a receipt and know which pile it goes in.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The core test: ordinary and necessary',
      'anchor' => 'the-core-test',
      'html' => <<<'HTML'
<p>In the United States, the rule is that an expense has to be <strong>ordinary and necessary</strong> for your trade or business. "Ordinary" means it's a common, accepted cost in your line of work. "Necessary" means it's helpful and appropriate for running the business. It does not have to be unavoidable. A graphic designer buying a font license, a plumber buying pipe fittings, a tutor buying workbooks: all ordinary and necessary for those trades.</p>
<p>Most countries use almost the same idea with different words:</p>
<ul>
<li><strong>United Kingdom:</strong> the cost has to be <strong>wholly and exclusively</strong> for the purposes of the trade. If something is partly personal, only the business part can be claimed, and some mixed costs can't be split at all.</li>
<li><strong>Canada:</strong> the expense must be incurred to earn business income, and it has to be reasonable in the circumstances.</li>
<li><strong>Australia:</strong> the expense must be incurred in gaining or producing your assessable income, and it can't be private or domestic in nature.</li>
</ul>
<p>Different wording, same instinct. Ask yourself one question about any purchase: <em>did I buy this to run or grow the business, or did I buy it for me?</em> If the honest answer is "for the business", you're usually looking at a deductible expense. If it's "for me", it isn't. The rest of this guide is really just that question applied to harder cases.</p>
HTML,
    ],

    [
      'h2' => 'The clear yes list',
      'anchor' => 'the-yes-list',
      'html' => <<<'HTML'
<p>Start with the easy pile. These are costs that plainly pass the test for most small businesses and freelancers. You'll rarely get a second look on any of them, as long as the money actually went to the business.</p>
<ul>
<li><strong>Tools, equipment, and supplies</strong> you use for the work: a laptop, a camera, hand tools, a ladder, printer paper, packaging materials.</li>
<li><strong>Software and subscriptions</strong> the business runs on: your design apps, accounting software, a website host, stock photo credits, a project-management tool.</li>
<li><strong>Materials and stock</strong> you buy to sell or to complete a job: lumber for a build, beans for a coffee cart, parts for a repair.</li>
<li><strong>Contractors and help:</strong> a subcontractor, a virtual assistant, a bookkeeper, a designer you hired for one project.</li>
<li><strong>Advertising and marketing:</strong> Google or Facebook ads, business cards, a booth at a trade show, printing flyers.</li>
<li><strong>Professional services:</strong> your accountant, a lawyer for a business contract, industry association dues.</li>
<li><strong>Business insurance</strong> and any licenses or permits your trade requires.</li>
<li><strong>Bank and payment fees:</strong> the cut a card processor takes, monthly fees on a business account, invoice payment fees.</li>
<li><strong>Travel for work</strong> that isn't your normal commute: a flight to meet a client, a hotel for an out-of-town job, parking at a job site.</li>
<li><strong>Education</strong> that keeps your existing skills sharp: a course, a trade certification renewal, a book on your craft.</li>
</ul>
<p>Notice the pattern. Every item on this list is something you'd have no reason to buy if the business didn't exist. That's what makes them clean.</p>
HTML,
    ],

    [
      'h2' => 'The clear no list',
      'anchor' => 'the-no-list',
      'html' => <<<'HTML'
<p>Now the other easy pile. These don't count, no matter how you feel about them, because they fail the core test. Trying to claim them anyway is the fastest way to turn a routine tax return into a problem.</p>
<ul>
<li><strong>Personal spending.</strong> Groceries for your family, your own clothes, a weekend trip, a haircut. If you'd buy it whether or not you ran a business, it isn't a business expense.</li>
<li><strong>Your commute.</strong> Driving from home to a regular workplace is personal travel in the US, UK, Canada, and Australia alike. Travel <em>between</em> job sites or to a temporary work location can count, but the daily trip to the same place you always go does not.</li>
<li><strong>Fines and penalties.</strong> A parking ticket, a speeding fine, a late-filing penalty from the tax office. Governments generally don't let you deduct the cost of breaking a rule, even if it happened during work.</li>
<li><strong>Most client entertainment.</strong> Taking a client to a concert, a ball game, or a round of golf is largely non-deductible in the US since the 2018 rules, and treated strictly elsewhere. A meal can sometimes qualify (more on that below), but the entertainment part usually doesn't.</li>
<li><strong>The full cost of anything you also use personally.</strong> Your home internet, your personal phone, your family car. These aren't a flat no, but you can't claim 100% of them. They belong in the grey zone, split by how much you use them for work.</li>
<li><strong>Money you draw for yourself.</strong> Paying yourself out of the business (an owner's draw) isn't an expense. It's you taking your own profit, and that profit is what gets taxed.</li>
<li><strong>Clothing you can wear anywhere.</strong> A nice suit or a plain pair of boots doesn't count, even if you only wear it to work. Protective gear and branded uniforms are different, and covered below.</li>
</ul>
<p>If a purchase makes you a little nervous about which list it's on, that's usually a sign it belongs in the grey zone, not that you should quietly file it under yes.</p>
HTML,
    ],

    [
      'h2' => 'The grey zone: how to split mixed use',
      'anchor' => 'the-grey-zone',
      'html' => <<<'HTML'
<p>Most real-world confusion lives here, in costs that are part business and part personal. The rule is simple to say: you claim the business share and only the business share. You work out that share as a percentage, apply it to the cost, and keep a note of how you got the number. This is called apportioning, and it's how you handle the five things people ask about most.</p>
{{illustration:checklist}}
<h3>Meals</h3>
<p>A meal can count when there's a real business reason: you're travelling for work, or you're eating with a client or a supplier to talk shop. In the US, business meals are typically 50% deductible, so a $80 client lunch gives you a $40 deduction. Rules differ elsewhere and are often tighter, so meals are a classic "check with an accountant for your situation" item. Your everyday lunch at your desk, with no business guest, is personal.</p>
<h3>Home office</h3>
<p>If you work from home, you can claim the share of your home costs that the workspace uses. The common method is by floor area: if your office is 150 square feet of a 1,500 square foot home, that's 10%, so 10% of your rent, heat, electricity, and internet becomes a business expense. Many countries also offer a simplified flat-rate method so you don't have to add up every bill. The space usually has to be used regularly for work to qualify.</p>
<h3>Phone and internet</h3>
<p>Pick an honest business-use percentage and apply it to the bill. If roughly 60% of your phone use is work calls and messages, claim 60% of the plan. A $70 monthly phone bill at 60% is $42 a month of business expense. Don't claim 100% of a phone you also text your friends on.</p>
<h3>Vehicle</h3>
<p>You have two ways to handle a car used for both. You can track business miles or kilometres and claim the standard per-mile rate your country sets, or you can claim the business-use percentage of your actual running costs (fuel, insurance, repairs, depreciation). Either way it comes down to the same thing: what share of the driving was for the business? Remember the commute doesn't count as business miles. Argo Books doesn't track your mileage automatically, so you'll log the trips yourself, then record the total as an expense.</p>
<h3>Clothing</h3>
<p>Everyday clothes are out, even the ones you only wear to work. What counts is clothing you couldn't reasonably wear in daily life: steel-toe boots, a hard hat, hi-vis gear, chef whites, a uniform with your logo on it. The test is whether the item is genuinely protective or specific to the job, not whether it's expensive.</p>
<p>For any of these, the safe move is to write down how you reached the percentage. "Home office is 10% by floor area" or "phone is 60% business" is a one-line note that turns a guess into a defensible figure.</p>
HTML,
    ],

    [
      'h2' => 'The two rules that make an expense stick',
      'anchor' => 'two-rules',
      'html' => <<<'HTML'
<p>Strip everything above down and two rules decide whether an expense holds up. Miss either one and the deduction is shaky, no matter how obviously business-related the purchase feels.</p>
<p><strong>Rule one: there's a genuine business purpose.</strong> The money went toward running or growing the business, and you can say in one plain sentence why. "I bought the drill to finish the deck job." "I paid for the ads to bring in customers." If you can't finish the sentence without stretching, the expense probably doesn't qualify.</p>
<p><strong>Rule two: you have a record to back it up.</strong> A deduction you can't prove is a deduction you might lose. The record is the receipt or invoice showing what you bought, when, from whom, and for how much. A line on a bank statement says $60 left your account; it doesn't say the $60 was drill bits rather than dinner. The receipt is what makes the claim real.</p>
{{illustration:receipt-scan}}
<p>This is exactly where a receipt goes missing in real life. It's a slip of thermal paper that fades in a month, or it's an email buried in an inbox, or it never existed because you tapped your card and walked out. Come tax time, you remember the expense but you can't find the proof, so you either leave the deduction on the table or claim it and hope.</p>
<p>The fix is to capture the receipt the moment you get it, while it's still in your hand. In Argo Books you snap a photo and the AI reads the vendor, date, amount, and tax straight off it, then files it as an expense in the category you choose. The image stays attached to that expense record, so the proof and the claim live together instead of drifting apart. The free tier scans 10 receipts a month and Premium handles 500, and there's a free receipt scanner on this site if you just want to try it.</p>
<p>Do this consistently and the year-end question changes. Instead of "did I spend anything I can claim?", it becomes "here's every expense, with a receipt on each one, sorted by category". That's the difference between guessing at your deductions and knowing them.</p>
HTML,
    ],

    [
      'h2' => 'Country notes, and when to ask an accountant',
      'anchor' => 'country-notes',
      'html' => <<<'HTML'
<p>The core test travels well, but the fine print is local. A few things worth knowing:</p>
<ul>
<li><strong>United States:</strong> ordinary-and-necessary is the standard. Business meals are generally 50% deductible, most entertainment is not, and the home-office deduction has a simplified square-footage option. Keep receipts for anything you claim.</li>
<li><strong>Canada:</strong> expenses must be reasonable and incurred to earn income. Meals and entertainment are usually limited to 50%. Home-office and vehicle claims are apportioned by business use.</li>
<li><strong>United Kingdom:</strong> the wholly-and-exclusively rule is strict, so mixed personal costs are handled carefully. There are simplified flat rates for working from home and for vehicle mileage that many sole traders use to keep things simple.</li>
<li><strong>Australia:</strong> the expense must relate to earning your income and can't be private or domestic. There are set methods for home-office and car claims, and you generally need written records once claims pass a threshold.</li>
</ul>
<p>Thresholds, percentages, and the exact rules for edge cases change, and they change more often than anyone would like. This guide gets you to the right pile for the vast majority of everyday costs, but it isn't tax advice for your specific situation. When a purchase is large, unusual, or genuinely on the fence, check with an accountant for your situation. A ten-minute question is cheaper than a wrong claim.</p>
<p>What you can do on your own is the part that makes their job easy and your return accurate: track every expense, put it in a sensible category, and keep the receipt attached. If you want a head start on the categories themselves, see <a href="/business-expense-categories/">business expense categories</a>.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Track and categorize every expense, and keep the scanned receipt attached as proof, all in one place.',
  'tool_callout_cta' => 'See expense and revenue tracking',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'Can I deduct meals?',
      'a' => 'Sometimes, when there\'s a real business reason. A meal while travelling for work, or a meal with a client or supplier where you\'re talking business, can qualify. In the United States, business meals are generally 50% deductible, so an $80 client lunch gives you a $40 deduction. Canada also commonly limits meals to 50%, and other countries treat them strictly, so this is a good one to check with an accountant. Your everyday solo lunch with no business purpose is personal and doesn\'t count. Keep the receipt and jot down who you ate with and why.',
    ],
    [
      'q' => 'Is my phone a business expense?',
      'a' => 'The business share of it is. If you use one phone for both work and personal life, pick an honest business-use percentage and claim that portion of the bill. If about 60% of your use is work, claim 60% of the plan, so a $70 monthly bill gives you roughly $42 a month of business expense. Don\'t claim the whole bill on a phone you also use to text friends and scroll at night. A second phone used only for the business can be claimed in full.',
    ],
    [
      'q' => 'Can I claim a home office?',
      'a' => 'Yes, if you regularly use part of your home for work. The common method is by area: if your workspace is 10% of your home\'s floor space, you can claim 10% of costs like rent, heat, electricity, and internet. Many countries also offer a simplified flat-rate method so you don\'t have to total up every bill. The space generally needs to be used regularly for the business, though it doesn\'t always have to be a separate room. Rules vary by country, so confirm the method that applies to you.',
    ],
    [
      'q' => 'What records do I need to keep?',
      'a' => 'For each expense, keep a receipt or invoice showing what you bought, the date, who you bought it from, and the amount. A bank or card statement alone usually isn\'t enough, because it shows money left your account but not what it was for. For mixed-use costs like a home office, phone, or car, also keep a short note of how you worked out the business-use percentage. Most tax offices expect you to keep these records for several years after you file, so store them somewhere they won\'t fade or go missing.',
    ],
    [
      'q' => 'Are work clothes deductible?',
      'a' => 'Only clothing you couldn\'t reasonably wear in everyday life. Steel-toe boots, a hard hat, hi-vis gear, chef whites, and a uniform with your business logo all count. A regular suit, plain trousers, or ordinary shoes don\'t, even if you bought them purely for work and never wear them elsewhere. The test is whether the item is genuinely protective or specific to the job, not how much it cost or how you personally feel about wearing it.',
    ],
    [
      'q' => 'Does the commute to work count?',
      'a' => 'No. Driving from home to a regular workplace is personal travel in the US, UK, Canada, and Australia. It doesn\'t matter how far it is or that you only make the trip for work. What can count is travel between job sites during the day, or a trip to a temporary or one-off work location, such as a client\'s office in another city. The everyday trip to the same place you always go is not a business expense.',
    ],
    [
      'q' => 'What if a purchase is part business and part personal?',
      'a' => 'You claim the business share and leave the personal share out. Work out what percentage of the item\'s use is for the business, apply that percentage to the cost, and keep a note of how you reached the figure. For example, a $70 phone bill at 60% business use gives a $42 monthly expense, and a home office that\'s 10% of your floor space lets you claim 10% of your home running costs. Splitting an expense honestly is normal and expected. Claiming the whole thing when part of it is personal is the mistake to avoid.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'contractor',
    'developer',
  ],

  'related_article_slugs' => [
    'business-expense-categories',
    'small-business-tax-deductions',
    'how-long-to-keep-business-receipts',
    'how-to-separate-business-and-personal-finances',
  ],
];
