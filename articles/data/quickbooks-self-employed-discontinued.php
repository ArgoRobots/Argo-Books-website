<?php
// articles/data/quickbooks-self-employed-discontinued.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'quickbooks-self-employed-discontinued',

  'h1' => 'QuickBooks Self-Employed is discontinued: what to do now',

  'meta_title' => 'QuickBooks Self-Employed Discontinued: What Now | Argo Books',

  'meta_description' => 'QuickBooks Self-Employed is discontinued. What actually happened, what it means for your account, and your three realistic paths forward, explained plainly.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'choosing-software',
  'hub_weight' => 22,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 12,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>If you've been using QuickBooks Self-Employed to run your freelance or solo business, you've probably seen the messages: the product is winding down, and Intuit wants you on its replacement, QuickBooks Solopreneur. Maybe you ignored the first banner. Maybe you're here because you finally clicked one and want to know what's actually going on before you agree to anything.</p>
<p>Here's the short version: QuickBooks Self-Employed stopped taking new customers in 2024, existing subscribers have mostly kept access so far, and Intuit is steadily steering everyone toward Solopreneur. Nothing breaks overnight. But a product on its way out is also a product nobody is improving, which makes this a genuinely good moment to choose your next tool on purpose instead of being defaulted into whatever Intuit picks for you. This guide covers what happened, what it means for you, and the three realistic paths forward, including one that gets you off the subscription treadmill entirely.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What actually happened to QuickBooks Self-Employed',
      'anchor' => 'what-happened',
      'html' => <<<'HTML'
<p>The timeline is simple. In February 2024, Intuit launched QuickBooks Solopreneur as the successor to QuickBooks Self-Employed. A few months later, in May 2024, Self-Employed closed to new signups. You can no longer buy it: anyone arriving fresh gets pointed at Solopreneur instead.</p>
<p>For people already subscribed, the change has been slower. At the time of writing, legacy Self-Employed subscribers have mostly kept access to their accounts, but the product is clearly winding down and Intuit is nudging users toward Solopreneur with increasing insistence. That's the normal life cycle for a retired software product: it keeps running for a while, gets no new features, and eventually the company sets a hard cutoff date.</p>
<p>Why did Intuit do it? Self-Employed was always a bit of an odd one out in the QuickBooks family. It was built for a specific job, helping US sole proprietors track income, expenses, and estimated taxes for a Schedule C, and it didn't upgrade cleanly into the main QuickBooks Online products. Solopreneur is built on the same platform as QuickBooks Online, which makes it easier for Intuit to maintain and easier to upsell from. That's a sensible business decision for Intuit. Whether it's the right outcome for you is a separate question, and it's the one this guide is actually about.</p>
HTML,
    ],

    [
      'h2' => 'What this means if you\'re still using it',
      'anchor' => 'what-it-means',
      'html' => <<<'HTML'
<p>First, the reassuring part: nothing disappears overnight. If you're a legacy subscriber, your account, your transaction history, and your reports are still there, and at the time of writing you can keep using them. There's no need to panic-migrate this weekend.</p>
<p>But "still works" and "worth staying on" are different things. A sunset product sits in a strange limbo:</p>
<ul>
<li><strong>No new investment.</strong> Discontinued products don't get new features or meaningful improvements. Anything that annoyed you about Self-Employed will annoy you forever.</li>
<li><strong>A clock you can't see.</strong> Intuit hasn't kept legacy access open out of generosity; it's a transition period. Companies in this position eventually set a final date, often with only a few months of notice. You don't want to be choosing new software in a rush against a deadline.</li>
<li><strong>The default path is chosen for you.</strong> If you do nothing, the most likely outcome is that you end up moved to Solopreneur, on Solopreneur's terms. That might be fine. But defaults serve the company that sets them, and the moment a product you pay for gets retired is exactly the moment you're entitled to shop around.</li>
</ul>
<p>So the practical takeaway isn't "leave immediately." It's "decide deliberately, on your own schedule, while you still have one." Export your data (transaction history, reports, mileage logs) sooner rather than later regardless of which path you pick, because exports are always easier while the old product is still fully alive.</p>
HTML,
    ],

    [
      'h2' => 'Your three realistic paths',
      'anchor' => 'three-paths',
      'html' => <<<'HTML'
<p>Almost everyone leaving Self-Employed lands in one of three places. Each is legitimate; they suit different people.</p>
{{illustration:compare-scale}}
<p><strong>Path 1: move to QuickBooks Solopreneur.</strong> This is the path of least resistance, and for some people it's genuinely the right call. Solopreneur is built for the same audience, it lives inside the QuickBooks ecosystem you already know, and Intuit has made the transition as frictionless as it can. The trade-offs: you stay on a subscription, you stay inside Intuit's pricing (and QuickBooks products have a history of price increases over time), and you're trusting that Solopreneur covers the specific features you relied on. Some Self-Employed users have found the successor handles certain things differently than they expected, so check the features you actually use before assuming it's a one-to-one swap.</p>
<p><strong>Path 2: move to a different cloud tool.</strong> If you like working in a browser with live bank feeds, there are other subscription tools aimed at freelancers and solo businesses. This path makes sense if automatic bank syncing is non-negotiable for you and you're happy paying monthly forever, but you'd rather not reward the company that just retired your product. Our guide to the <a href="/best-quickbooks-alternatives/">best QuickBooks alternatives</a> walks through the honest trade-offs between the main options.</p>
<p><strong>Path 3: step off the subscription treadmill with a desktop tool.</strong> This is the path most Self-Employed users never consider, because they've only ever known cloud subscriptions. A desktop accounting app runs on your own computer, keeps your data locally, works offline, and doesn't need a monthly fee just to let you look at your own numbers. For a solo business with simple books, this can be both cheaper and calmer: no price increases to absorb, no product retirements to migrate away from, no company between you and your records. The trade-off is that desktop tools typically import your bank activity rather than syncing it live, which we'll come back to below, because it matters more for some people than others.</p>
HTML,
    ],

    [
      'h2' => 'What you actually used Self-Employed for, and how to replace each piece',
      'anchor' => 'map-your-needs',
      'html' => <<<'HTML'
<p>The smartest way to choose a replacement isn't to compare feature lists. It's to write down what you personally used Self-Employed for, then check each need against the candidates. For most users, the list is short and looks something like this:</p>
{{illustration:checklist}}
<p><strong>Separating business from personal spending.</strong> This was Self-Employed's signature trick: swipe left for personal, swipe right for business. Any replacement needs a fast way to do the same job. In a cloud tool, that's usually categorizing a synced feed. In a desktop tool, it's importing your bank statement and categorizing the lines, which takes a few minutes per month rather than a few seconds per day. Same outcome, different rhythm.</p>
<p><strong>Tracking simple income and expenses.</strong> Every serious option handles this. The question is how much tool you want around it. Solopreneur and other cloud apps wrap it in a bigger platform; a simple desktop app keeps it closer to "a tidy ledger with reports." If your books are genuinely simple, more platform isn't more value.</p>
<p><strong>Quarterly estimated tax calculations.</strong> Self-Employed estimated your US federal quarterly taxes for you, and this is the feature with the fewest true replacements. Solopreneur carries the concept forward; most other tools, cloud or desktop, don't calculate estimates for you. The manual version is honestly not hard, it's a percentage of your profit set aside each quarter, and our guide on <a href="/how-to-pay-quarterly-estimated-taxes/">how to pay quarterly estimated taxes</a> walks through it step by step. But if you deeply valued having the number computed for you, weight that in your decision.</p>
<p><strong>Automatic mileage tracking.</strong> Self-Employed's phone app logged drives in the background and let you swipe them into business or personal. If mileage is a big deduction for you (drivers, mobile trades, consultants who travel to clients), this feature alone might steer your choice, because most general accounting tools don't do it. The alternatives are a dedicated mileage app feeding a log you record in your books, or a tool that still has tracking built in. Be honest with yourself about whether you'll keep a manual log, because an unlogged drive is a missed deduction.</p>
HTML,
    ],

    [
      'h2' => 'Where Argo Books fits, and where it honestly doesn\'t',
      'anchor' => 'argo-fit',
      'html' => <<<'HTML'
<p>Argo Books is our desktop accounting app for Windows, Mac, and Linux, and it's a natural landing spot for a chunk of the Self-Employed audience, so here's the honest fit check rather than a sales pitch.</p>
<p><strong>Where it fits well.</strong> If your Self-Employed usage was mostly "track my income and expenses, keep business separate from personal, scan receipts, send the occasional invoice, and hand my accountant clean numbers at tax time," Argo covers that comfortably. You get expense and revenue tracking, AI receipt scanning, invoicing, sales-tax tracking, and tax-ready reports like profit and loss. It's free to start, with no subscription required to begin, and your data lives on your own machine instead of on a server that can be retired out from under you. After watching a product you paid for get discontinued, that last point tends to resonate.</p>
<p><strong>Where it doesn't fit.</strong> Two gaps matter for this audience, and we'd rather you know now than find out after moving:</p>
<ul>
<li><strong>Argo does not auto-track mileage.</strong> There's no background GPS logging. If automatic mileage tracking was the feature you loved most, that's a serious gap: you'd need a separate mileage app or a manual log, and you should weigh whether you'll actually keep one.</li>
<li><strong>Argo does not estimate quarterly taxes for you.</strong> It gives you the profit numbers the estimate is based on, but you (or your accountant) do the calculation, using the approach in our <a href="/how-to-pay-quarterly-estimated-taxes/">quarterly taxes guide</a>.</li>
</ul>
<p>One more difference in kind rather than quality: Argo imports your bank statements (CSV or statement files, read with AI) rather than syncing a live feed. For a solo business that means a short monthly import-and-categorize session instead of a constantly updating feed. Some people prefer the batch rhythm; some miss the live feed. Know which one you are.</p>
HTML,
    ],

    [
      'h2' => 'How to actually make the move',
      'anchor' => 'making-the-move',
      'html' => <<<'HTML'
<p>Whichever path you choose, the move itself is less painful than it looks, because Self-Employed books are simple by design. The rough shape:</p>
<ol>
<li><strong>Export everything while you still can.</strong> Pull your transaction history, your reports for past years, and your mileage logs out of Self-Employed. Do this first, even before you've picked a destination. Past-year records matter if you're ever asked to back up a tax return.</li>
<li><strong>Pick a clean start date.</strong> The start of a quarter or a year is easiest. You don't need to rebuild your whole history in the new tool; you need your history saved somewhere safe, and your new books accurate from the start date forward.</li>
<li><strong>Bring in the current year.</strong> If you switch mid-year, import or enter the current year's income and expenses into the new tool so your tax-time totals live in one place. In Argo, that's a bank statement or spreadsheet import plus a categorization pass.</li>
<li><strong>Run both for one month if you're nervous.</strong> Keep the old account alive for a few weeks while you do a full month in the new tool. When the new tool's numbers match what you expect, close the old chapter.</li>
</ol>
<p>We've written a fuller walkthrough in <a href="/how-to-switch-from-quickbooks/">how to switch from QuickBooks</a>, covering exports, start dates, and the first month in more detail. And if cost is a main driver of your decision, our comparison of the <a href="/cheapest-accounting-software-for-self-employed/">cheapest accounting software for self-employed people</a> lines up the realistic options by what you'd actually pay over a year.</p>
HTML,
    ],

    [
      'h2' => 'The bigger lesson: own your books',
      'anchor' => 'own-your-books',
      'html' => <<<'HTML'
<p>Step back from the product names for a second, because there's a lesson in this episode that outlasts any single tool. Thousands of solo businesses built a routine around QuickBooks Self-Employed, and then the company retired it. Not because it stopped working, but because a different product suited the company better. That's not a scandal; it's just what happens when your bookkeeping lives inside someone else's subscription. The product roadmap belongs to them, and you're along for the ride.</p>
<p>You can't fully escape that in a cloud tool, whoever makes it. Any subscription product can be repriced, reshaped, or retired, and your only options are to accept the change or migrate. What you can do is reduce how exposed you are:</p>
<ul>
<li><strong>Keep exports.</strong> Whatever tool you use, download your transaction history and key reports at least yearly. Records you hold yourself can't be taken off the menu.</li>
<li><strong>Prefer tools you could leave.</strong> Before committing, check how easily data comes out, not just how easily it goes in.</li>
<li><strong>Consider owning the software outright.</strong> A desktop app with your data stored locally can't be discontinued out from under your files. The software you have keeps running, and the records on your disk stay yours.</li>
</ul>
<p>Self-Employed users are being handed a fork in the road they didn't ask for. That's annoying, but it's also a rare moment of clarity: for once, you're choosing accounting software with real experience of what you need, instead of guessing as a beginner. Use it. Pick the tool that fits the business you actually run, and set things up so the next product retirement, whenever it comes, is a shrug instead of a scramble.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books tracks your income and expenses, scans receipts, and builds tax-ready reports on your own computer. Free to start, no subscription required.',
  'tool_callout_cta' => 'See expense and income tracking in Argo Books',
  'tool_callout_url' => '/features/expense-revenue-tracking/',

  'faqs' => [
    [
      'q' => 'Is QuickBooks Self-Employed really discontinued?',
      'a' => 'Yes. Intuit launched QuickBooks Solopreneur in February 2024 as its replacement, and Self-Employed closed to new signups in May 2024, so it can no longer be purchased. At the time of writing, people who already had subscriptions have mostly kept access to their accounts, but the product is winding down and Intuit is steering existing users toward Solopreneur. Discontinued products typically run for a transition period and then get a final cutoff date, so if you\'re still on Self-Employed, it\'s worth exporting your data and choosing your next tool before a deadline chooses for you.',
    ],
    [
      'q' => 'Will I lose my QuickBooks Self-Employed data?',
      'a' => 'Not overnight, but don\'t leave it to chance. At the time of writing, legacy subscribers still have access to their accounts and history. The safe move is to export everything now, while the product is fully alive: your transaction history, your profit and loss reports for each past year, and your mileage logs. Past-year records matter because tax authorities can ask you to back up a return years after you filed it, and you don\'t want those records living only inside a product that\'s being retired. Once your exports are saved somewhere you control, the wind-down timeline stops being your problem.',
    ],
    [
      'q' => 'Should I just switch to QuickBooks Solopreneur?',
      'a' => 'It\'s the easiest path, and for some people it\'s the right one: it\'s built for the same audience, it keeps the quarterly tax estimate concept, and the migration is designed to be smooth. But easy isn\'t the same as best. You stay on a subscription inside Intuit\'s pricing, which has historically risen over time, and Solopreneur handles some things differently than Self-Employed did, so check the specific features you rely on before assuming it\'s a one-to-one swap. The retirement of a product you pay for is a fair moment to compare alternatives, including cheaper cloud tools and one-time desktop options, before defaulting to the successor.',
    ],
    [
      'q' => 'Does Argo Books track mileage and estimate quarterly taxes like Self-Employed did?',
      'a' => 'No, and it\'s better you know that upfront. Argo Books does not auto-track mileage: there\'s no background GPS logging, so if automatic drive detection was your favorite feature, you\'d need a dedicated mileage app or a manual log alongside it. Argo also doesn\'t calculate quarterly estimated taxes for you; it gives you accurate profit numbers, and you apply a set-aside percentage yourself, which our quarterly taxes guide explains step by step. What Argo does cover well is the core of what most Self-Employed users did daily: tracking income and expenses, separating business from personal, scanning receipts, invoicing, and producing tax-ready reports, free to start and without a subscription.',
    ],
    [
      'q' => 'What\'s the difference between importing bank statements and a live bank feed?',
      'a' => 'A live bank feed, which cloud tools like Self-Employed use, connects to your bank and pulls in transactions continuously, so new spending appears in your books within a day or so. Statement importing, which Argo Books uses, means you download a statement or CSV from your bank and bring it in yourself, usually once a month, and the AI import reads and categorizes the lines. The end result is the same set of categorized transactions; the difference is rhythm. A feed gives you a constantly current view with occasional connection hiccups to manage, while imports give you a predictable monthly session that works offline and keeps your bank credentials out of a third-party tool. Neither is wrong; pick the rhythm you\'ll actually stick to.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'generic',
  ],

  'related_article_slugs' => [
    'quickbooks-desktop-discontinued',
    'best-quickbooks-alternatives',
    'cheapest-accounting-software-for-self-employed',
    'how-to-switch-from-quickbooks',
  ],
];
