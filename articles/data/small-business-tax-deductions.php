<?php
// articles/data/small-business-tax-deductions.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'small-business-tax-deductions',

  'h1' => 'Small business tax deductions you might be missing (2026)',

  'meta_title' => 'Small Business Tax Deductions (2026) | Argo Books',

  'meta_description' => 'The business tax deductions small businesses most often miss, the difference between an expense and an asset, and how to keep records that hold up.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'receipts-expenses',
  'hub_weight' => 15,

  'published' => '2026-06-02',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Most small businesses overpay their tax, not because they cheat, but because they undercount their deductions. A deduction is a legitimate business cost you subtract from your income before tax is worked out, so every real cost you don't claim is money you hand the tax office for no reason. The ones that get missed are rarely the big obvious ones. They're the small, frequent, easy-to-forget costs that never made it into the books.</p>
<p>This guide covers the deductions small businesses most often leave on the table, the difference between a cost you claim all at once and one you claim over time, and the single habit that makes every deduction stick. None of this is tax advice, and the exact rules vary by country, so treat it as a checklist of things to ask your accountant about, not a ruling for your situation.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why deductions get missed',
      'anchor' => 'why-missed',
      'html' => <<<'HTML'
<p>The deductions you lose are almost never the rent or the big equipment purchase. Those are large, memorable, and easy to find on a bank statement. What slips away is the long tail of small costs: a tank of fuel, a box of supplies, a year of a software subscription, a parking fee at a client site. Individually they're trivial. Added up across a year, they're often thousands of dollars of deductions, and thousands of dollars is real tax.</p>
<p>They get missed for one reason: no record. The receipt was never captured, the cost never made it into the books, and at tax time nobody remembers a small purchase from nine months ago. You can't claim what you can't show, so the deduction quietly disappears. The fix isn't knowing more tax law; it's capturing the cost when it happens. The <a href="/how-to-track-business-expenses-without-spreadsheets/">guide on tracking expenses</a> covers the system; this guide covers what to make sure you're capturing.</p>
HTML,
    ],

    [
      'h2' => 'The deductions most often missed',
      'anchor' => 'most-missed',
      'html' => <<<'HTML'
<p>Run down this list and check that each one you actually incur is being captured. The exact treatment varies by country, so confirm the specifics with your accountant, but these are the costs small businesses most often forget to claim:</p>
<ul>
<li><strong>Vehicle and mileage.</strong> Driving for the business, to clients, suppliers, and jobs, is deductible, usually either as a per-kilometre rate or as the business share of your actual vehicle costs. It's one of the biggest missed deductions, because it needs a log kept through the year and nobody reconstructs a year of driving from memory.</li>
<li><strong>Home office.</strong> If you work from home, a share of your rent or mortgage interest, utilities, and internet may be deductible based on the space you use for work. The rules are specific, but the deduction is real and routinely skipped by people who assume it doesn't apply to them.</li>
<li><strong>Phone and internet.</strong> The business-use percentage of your mobile, home internet, and any business lines. Mixed-use, so you claim the business share, but that share over a year is a meaningful number.</li>
<li><strong>Software and subscriptions.</strong> Every tool you pay for to run the business: accounting software, design tools, cloud storage, your website hosting, industry apps. These auto-renew quietly and are easy to forget at tax time.</li>
<li><strong>Tools and equipment.</strong> Anything you buy to do the work. Smaller items are often claimed in full the year you buy them; larger ones may be claimed over several years (more on that below).</li>
<li><strong>Professional fees and insurance.</strong> Your accountant's fee, legal costs, trade or liability insurance, professional memberships, and licences. The accountant's own fee being deductible surprises people.</li>
<li><strong>Bank and payment fees.</strong> Business account fees, the card-processing fees taken out of customer payments, and currency-conversion costs. They're small per transaction and add up to a real deduction across a year.</li>
<li><strong>Education and training.</strong> Courses, certifications, books, and conferences that maintain or improve skills for your current business are often deductible.</li>
<li><strong>Materials and supplies.</strong> The consumables you buy to deliver the work. Easy to capture for big purchases, easy to lose for the small frequent ones bought across several suppliers.</li>
<li><strong>Startup costs.</strong> Money spent getting the business going before it opened, like registration, initial equipment, and early marketing, can often be claimed, sometimes spread over time. Many first-year owners don't realise these count.</li>
</ul>
<p>You won't have all of these, and you shouldn't claim ones you don't genuinely incur. But most businesses have several they're not capturing, and each one is tax you're paying that you don't owe.</p>
HTML,
    ],

    [
      'h2' => 'An expense or an asset? Claim now or over time',
      'anchor' => 'expense-vs-asset',
      'html' => <<<'HTML'
<p>Not every business purchase is claimed the same way, and getting this wrong is another way deductions go missing or get claimed incorrectly. The rough distinction:</p>
<ul>
<li><strong>An expense</strong> is a cost used up in the short term, like fuel, supplies, a monthly subscription, or insurance. You generally claim the whole amount in the year you spend it.</li>
<li><strong>An asset</strong> is something that lasts and keeps providing value, like a vehicle, major equipment, or a computer. Larger assets are often claimed gradually over their useful life rather than all at once, a process usually called depreciation or capital allowances.</li>
</ul>
<p>The line between the two, and the threshold where a purchase counts as an asset, varies by country, and many places let you write off smaller assets immediately under a set value. This is exactly the kind of thing to keep the receipt for and let your accountant categorise, rather than guessing. The point for you is simpler: keep the record of what you bought and when, so the right treatment can be applied. A purchase with no record can't be claimed either way.</p>
HTML,
    ],

    [
      'h2' => 'The habit that makes every deduction stick',
      'anchor' => 'keep-the-record',
      'html' => <<<'HTML'
<p>Everything above depends on one thing: a record of the cost, with the receipt, captured when it happened. This is the difference between a deduction you can claim and confidently defend, and one you lose because you can't prove it.</p>
<p>The practical version is short. Capture each receipt the moment you get it, before it fades or vanishes, ideally as a photo or a scan so the image is kept, not just a number. Tag it to the right category while you still remember what it was for. A <a href="/best-free-ai-receipt-scanner/">receipt-scanning app</a> does this in seconds by reading the receipt and filing it for you, which is why high-receipt businesses lean on one, but a phone photo into a labelled folder works too. The tool matters less than the habit.</p>
<p>Why it matters beyond the claim itself: tax authorities in most countries can ask you to back up a deduction, sometimes years later. A kept receipt is the proof. A remembered purchase with no receipt is a deduction you'll probably drop rather than risk, which means you keep records not just to claim more, but to claim with a clear conscience.</p>
HTML,
    ],

    [
      'h2' => 'What you cannot or should not claim',
      'anchor' => 'what-not-to-claim',
      'html' => <<<'HTML'
<p>Claiming more than you're owed is its own problem, and it's easy to do by accident. A few lines that get crossed without anyone meaning to:</p>
<ul>
<li><strong>Personal spending.</strong> A cost has to be for the business to be a business deduction. Your weekly groceries, personal clothing, and personal trips don't qualify, even if they ran through a business card by mistake. This is the strongest reason to keep separate accounts.</li>
<li><strong>The personal share of mixed-use costs.</strong> Your phone, vehicle, and home are used for both work and life, and only the business share is deductible. Claiming 100% of a phone you also use personally is a common, avoidable error.</li>
<li><strong>Things with special limits.</strong> Meals, entertainment, and client gifts often have partial limits or specific rules, rather than being fully deductible. Keep the receipts, but let your accountant apply the limit.</li>
</ul>
<p>The safe principle: capture everything with a receipt and an honest category, and let your accountant decide what's claimable and at what rate. Over-claiming to save tax now can cost far more later if it's ever questioned. Good records protect you in both directions, they help you claim everything you're owed, and nothing you're not.</p>
HTML,
    ],

    [
      'h2' => 'Getting ready for your accountant',
      'anchor' => 'for-your-accountant',
      'html' => <<<'HTML'
<p>If you've captured costs as they happened and categorized them, tax time is assembly, not archaeology. What your accountant wants is simple: your income, your expenses grouped by category, your mileage log, a note of any larger asset purchases, and the receipts available if a number is queried.</p>
<p>Handing that over clean does two things. It gets you every deduction, because nothing's been forgotten in a shoebox. And it cuts your bill, because accountants charge by the hour, and sorting a year of mixed receipts is hours you're paying for. The cleaner the records, the smaller the invoice and the bigger the refund.</p>
<p>The thread through this whole guide is the same: deductions aren't won by knowing obscure tax law, they're won by keeping a record of every real cost as it happens. Do that, ask your accountant about the items on the missed-deductions list above, and you'll stop paying tax you don't owe. For the system that keeps those records current with the least effort, see the <a href="/bookkeeping-for-contractors/">bookkeeping basics</a> for hands-on businesses.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 1,

  'tool_callout_text' => 'Argo Books captures expenses from a receipt photo and keeps them categorized, so your deductions are tracked and your proof is filed by tax time.',
  'tool_callout_cta' => 'See expense tracking in Argo Books',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'What is the most commonly missed small business tax deduction?',
      'a' => 'Vehicle and mileage costs are near the top, because they require a log kept through the year and most people never keep one, then cannot reconstruct a year of driving at tax time. Home office is another big one, often skipped by people who assume it does not apply to them. After those, it is the long tail of small frequent costs, fuel, supplies, software subscriptions, bank and payment fees, that individually feel too small to bother with but add up to thousands across a year. The pattern is the same for all of them: they get missed because there is no record, not because they are not claimable.',
    ],
    [
      'q' => 'Do I need to keep receipts, or is a bank statement enough?',
      'a' => 'Keep the receipts. A bank statement shows that money left your account, but not always what it was for or how the tax applies, and many tax authorities specifically want the receipt as proof of a deduction. The safe approach is to keep a clear image of every business receipt, which most authorities accept, so you are not relying on thermal paper that fades to blank. A statement line plus a kept receipt is a solid record; a statement line alone is weaker and, for some claims, not enough. The rules vary by country, so check your local guidance, but receipts kept as you go are never the wrong answer.',
    ],
    [
      'q' => 'What is the difference between an expense and an asset for tax?',
      'a' => 'An expense is a cost used up in the short term, like fuel, supplies, or a subscription, and you generally claim the full amount in the year you spend it. An asset is something durable that keeps providing value, like a vehicle or major equipment, and larger assets are often claimed gradually over their useful life through depreciation or capital allowances rather than all at once. The value threshold where a purchase becomes an asset, and the rules for writing off smaller ones immediately, vary by country. You don\'t have to get this right yourself; keep the receipt and the purchase date, and let your accountant apply the correct treatment.',
    ],
    [
      'q' => 'Can I claim my home office and car if I use them for personal life too?',
      'a' => 'Usually yes, but only the business share, not the whole cost. For a home office, that is typically the portion of your home and its running costs used for work. For a vehicle, it is either a per-kilometre rate for business driving or the business percentage of your total vehicle costs. The key is that you claim the work-related portion and keep a record that supports it, a mileage log for the car, a reasonable basis for the home-office share. Claiming 100% of a mixed-use cost is a common error that can cause problems if questioned. Check your country rules, since the methods and limits differ.',
    ],
    [
      'q' => 'Is this article tax advice?',
      'a' => 'No. It is a plain-language checklist of deductions small businesses commonly miss and the records that support them, written to help you ask your accountant better questions. The actual rules, rates, thresholds, and limits vary by country and by your specific situation, and only someone who knows your circumstances and your local tax law can tell you what you can claim and how. Use this to make sure you are capturing the right costs through the year; use a qualified accountant to turn those records into a correct return. This guide lives on the Argo Books site, and Argo Books sells expense-tracking software, so read the one product mention with that in mind.',
    ],
  ],

  'related_niche_slugs' => [
    'contractor',
    'freelance',
    'consultant',
  ],

  'related_article_slugs' => [
    'how-to-track-business-expenses-without-spreadsheets',
    'best-free-ai-receipt-scanner',
    'bookkeeping-for-contractors',
  ],
];
