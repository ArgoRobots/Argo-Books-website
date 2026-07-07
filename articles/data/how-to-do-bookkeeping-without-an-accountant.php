<?php
// articles/data/how-to-do-bookkeeping-without-an-accountant.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-do-bookkeeping-without-an-accountant',

  'h1' => 'How to do bookkeeping without an accountant',

  'meta_title' => 'How to Do Bookkeeping Without an Accountant | Argo Books',

  'meta_description' => 'A practical playbook for doing your own bookkeeping: a simple system, the tools that help, what to still hand an accountant, and when to hire one.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 15,

  'published' => '2026-06-15',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>You can do your own bookkeeping. Most solo owners and small businesses do, and it's a lot less mysterious than the word "bookkeeping" makes it sound. At its heart it's just keeping an honest record of money coming in and money going out, with the proof attached, kept current enough that you're never guessing. You don't need an accounting degree, and you don't need to hand the whole thing to a professional from day one.</p>
<p>This guide is the practical playbook: a simple system you can run yourself, the tools that make it lighter, the parts you should still hand an accountant even while doing your own books, and the honest signs that it's time to hire one. Doing it yourself saves real money, but it has limits, and knowing where they are is part of doing it well.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Separate your business money first',
      'anchor' => 'separate-money',
      'html' => <<<'HTML'
<p>Before any system, do this one thing, because it makes everything after it easier. Open a separate bank account, and ideally a separate card, for the business. Run every business payment in and every business cost out through it, and keep personal spending on personal accounts.</p>
<p>Here's why it matters so much when you're the bookkeeper. With one mixed account, doing your books means first deciding which of hundreds of transactions were even business, every single month, before you can record anything. That sorting is the part people dread, and it's the part that makes mixed-account owners avoid their books until the night before tax time. With separate accounts, the business statement <em>is</em> your list of business activity. You're no longer sorting; you're just recording and categorizing, which is far less work and far less room for mistakes.</p>
<p>You don't need a business account with monthly fees. A second basic account in the business name is enough. This is the cheapest, highest-value move in the whole guide, and it costs you a form and an afternoon.</p>
HTML,
    ],

    [
      'h2' => 'The simple system: a five-part loop',
      'anchor' => 'the-system',
      'html' => <<<'HTML'
<p><a href="/small-business-bookkeeping-basics/">Doing your own books</a> is the same small loop repeated. Run it and you're a bookkeeper. The five parts:</p>
<ol>
<li><strong>Record income as it comes in.</strong> Every payment the business receives gets logged: the date, who paid, what for, and the amount. This is your record of what the business earned.</li>
<li><strong>Record expenses as they go out.</strong> Every business cost gets logged the same way, with a category on each one. This is the record that becomes your deductions, so it's the half that lowers your tax bill.</li>
<li><strong>Keep the receipt.</strong> Snap or save the proof for every expense, attached to or filed alongside the record. A cost with no receipt is a deduction you may not be able to back up.</li>
<li><strong>Categorize.</strong> Put each expense in a bucket: materials, vehicle and travel, tools, software, insurance and fees, meals separately. Categorized costs are what turn a pile of records into a tax return.</li>
<li><strong>Check against the bank monthly.</strong> Once a month, run down the business account and confirm every transaction is recorded and categorized. This is the safety net that catches what slipped through while it's still fresh.</li>
</ol>
{{illustration:checklist}}
<p>That's the whole job. Notice it's mostly capture-as-you-go, not a big month-end session. The owners who find bookkeeping painful are almost always the ones who skip the daily capture and try to rebuild a whole month from memory and a drawer of receipts. Five minutes a week beats a lost weekend every quarter.</p>
HTML,
    ],

    [
      'h2' => 'The tools that make it lighter',
      'anchor' => 'the-tools',
      'html' => <<<'HTML'
<p>You can run the loop above with a notebook, but a few tools take most of the typing out of it. Match the tool to your volume, not to what's most popular:</p>
<ul>
<li><strong>A spreadsheet.</strong> The free starting point. An income tab, an expense tab with categories, and a summary that totals them is a complete system at low volume. It works as long as you keep it current and don't mind the manual entry.</li>
<li><strong>A receipt-scanning app.</strong> The biggest single time-saver for the capture step. You <a href="/how-to-track-business-expenses-without-spreadsheets/">photograph a receipt and the app reads the supplier, date, total, and tax</a> and files it as an expense with the image attached, no typing. For more than a handful of receipts a week, this is the difference between minutes a month and an evening.</li>
<li><strong>Accounting software.</strong> Pulls the whole loop into one place: it records, categorizes, attaches receipts, and lines your records up against the bank for you. It's the step up when the spreadsheet's manual entry becomes the bottleneck. Plenty of options have real free tiers, so doing your own books doesn't have to mean paying for software.</li>
</ul>
<p>The honest order is: start with the spreadsheet, add receipt scanning when the typing annoys you, and move to software when the volume makes the spreadsheet the slow part. There's no prize for jumping straight to the most powerful tool. The best one is the one you'll actually keep up with.</p>
HTML,
    ],

    [
      'h2' => 'What to still hand an accountant',
      'anchor' => 'hand-to-accountant',
      'html' => <<<'HTML'
<p>Doing your own bookkeeping doesn't mean doing everything yourself. The smart split is to keep the books current all year, then bring a professional in for the parts where their expertise pays for itself. Even committed do-it-yourselfers usually hand over:</p>
<ul>
<li><strong>Year-end and tax filing.</strong> The actual tax return is where rules get specific and mistakes get expensive. An accountant who knows your country's rules will often <a href="/small-business-tax-deductions/">find deductions you missed</a> and structure things you wouldn't have thought of. Handing them clean, categorized totals means their bill is small, because you've done the time-consuming part.</li>
<li><strong>How larger purchases are claimed.</strong> Big equipment or vehicles may have to be written down over several years rather than claimed all at once, and the rules vary. This is worth a quick professional answer rather than a guess.</li>
<li><strong>Setting up correctly at the start.</strong> A one-time session to confirm your categories, your business structure, and how much tax to set aside can save a lot of grief later. You only need it once.</li>
<li><strong>Anything unusual.</strong> Selling the business, bringing on a partner, a tax authority asking questions, expanding to another country. These are not yearly events, and they are exactly when an hour of professional advice is worth it.</li>
</ul>
<p>Think of it as a division of labour. You do the daily bookkeeping, which is mostly habit and costs only your time. The accountant does the once-a-year and the unusual, which is mostly expertise. That split keeps your costs low without leaving the risky parts to a guess.</p>
HTML,
    ],

    [
      'h2' => 'When you should genuinely hire one',
      'anchor' => 'when-to-hire',
      'html' => <<<'HTML'
<p>There's a point where doing it all yourself stops being thrift and starts being a false economy. Be honest with yourself about these signs:</p>
<ul>
<li><strong>Your situation got complex.</strong> Payroll for staff, inventory, multiple revenue streams, sales tax across regions, or operating in more than one country. Each of these adds rules where a mistake costs more than an accountant would.</li>
<li><strong>You're spending hours you should spend earning.</strong> If the books eat a day a month that you could bill to a client, a bookkeeper costs less than the work you're not doing.</li>
<li><strong>You dread it and avoid it.</strong> Books you put off are books that fall behind, and behind is where mistakes and missed deductions live. If you genuinely won't keep up, paying someone who will is cheaper than the mess.</li>
<li><strong>The stakes went up.</strong> Bigger revenue, a loan application, investors, or a sale on the horizon all mean your numbers need to be right and defensible, not approximately right.</li>
</ul>
<p>Hiring help isn't an admission of failure; it's a normal step as a business grows. And it isn't all-or-nothing. Many owners keep doing the daily capture themselves and hire a bookkeeper for a few hours a month to check the work and keep it clean, which is far cheaper than handing over the whole thing. The goal is accurate books at the lowest sensible cost, and where that line falls changes as you grow.</p>
HTML,
    ],

    [
      'h2' => 'The limits of doing it yourself, honestly',
      'anchor' => 'the-limits',
      'html' => <<<'HTML'
<p>Doing your own bookkeeping saves money and gives you a real feel for your numbers, which is genuinely valuable. But it's worth naming the limits so you go in clear-eyed.</p>
<p>You won't catch what you don't know to look for. An accountant spots a deduction you've never heard of or a structure that saves tax; doing it alone, you only know what you've learned. You also carry the responsibility: if a number's wrong, it's on you, and tax authorities don't accept "I did my best with a spreadsheet" as a defence. And there's the time cost, which is easy to undercount. The hours on your books are hours not spent earning, and past a certain size that trade stops making sense.</p>
<p>None of that means don't do it. For a solo owner or small business with a straightforward setup, doing your own books with a simple system and the right tools is the right call, and the savings are real. It means do it with a clear system, keep it current, lean on cheap tools to cut the typing, and bring in a professional for the year-end and the unusual. That mix, mostly yourself with help where it counts, is how most small businesses keep their books accurate without paying for more help than they need.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books gives a solo owner a free way to record income and expenses, scan receipts, and keep the books current without an accountant.',
  'tool_callout_cta' => 'Download Argo Books free',
  'tool_callout_url' => '/downloads/',

  'faqs' => [
    [
      'q' => 'Can I really do my own bookkeeping with no training?',
      'a' => 'Yes, for a straightforward small business. Bookkeeping at this level is keeping an honest record of money in and money out, with the proof attached, kept current. The system is a small loop: record income, record expenses, keep receipts, categorize, and check against the bank monthly. None of that needs formal training, and a separate bank account plus a free spreadsheet or app is enough to start. Where training matters is the year-end tax filing and anything unusual, which is exactly why most do-it-yourselfers keep the daily books themselves and hand those specific parts to an accountant. You can absolutely do the bookkeeping; you just don\'t have to do every last piece alone.',
    ],
    [
      'q' => 'What is the difference between bookkeeping and accounting?',
      'a' => 'Bookkeeping is the day-to-day recording: logging each payment in and each cost out, keeping receipts, and categorizing. Accounting is the layer on top: interpreting those records, filing taxes, advising on structure, and making sure everything follows the rules. The useful way to think about it is that bookkeeping is the habit you can run yourself, and accounting is the expertise you bring in for the year-end and the unusual. Most solo owners do their own bookkeeping all year and pay an accountant for the accounting at tax time. Doing the bookkeeping well, keeping it clean and current, is what makes the accounting part fast and cheap.',
    ],
    [
      'q' => 'How often should I update my books?',
      'a' => 'Capture as you go and check monthly. The capture part, logging an expense or a payment, is best done at the moment it happens, because a receipt scanned in the car takes seconds and the same one rebuilt from memory in April takes ten minutes and is probably wrong. Then once a month, run down your business bank account and confirm every transaction is recorded and categorized. That monthly check is the safety net that catches anything missed while it\'s still fresh enough to fix in seconds. Owners who only touch their books quarterly or yearly are the ones who find it painful, because they are rebuilding months of activity at once instead of keeping a current record.',
    ],
    [
      'q' => 'How do I know when it\'s time to hire help?',
      'a' => 'Watch for four signs. First, your situation got complex: payroll, inventory, sales tax across regions, or operating in more than one country all add rules where mistakes are costly. Second, the books are eating hours you could spend earning, so a bookkeeper costs less than the work you are not doing. Third, you dread it and keep falling behind, which is where missed deductions and mistakes live. Fourth, the stakes went up, like a loan, investors, or a sale, where your numbers need to be defensible. It does not have to be all-or-nothing: many owners keep doing the daily capture and hire a bookkeeper for a few hours a month to check the work, which is far cheaper than handing over everything.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'No. The whole point of this guide is that you can do your own bookkeeping, and the system in it works with a notebook, a free spreadsheet, a competitor\'s app, or no software at all. Argo Books is mentioned once, in a callout you can ignore. Yes, this is the Argo Books site, so read it knowing that. But the advice does not depend on our tool, and the guide goes out of its way to say when you should hand work to an accountant or hire a bookkeeper instead, which is hardly a sales pitch. If you take the system and run it for free, the guide did its job. We would rather you keep accurate books cheaply than buy software you don\'t need.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'generic',
  ],

  'related_article_slugs' => [
    'small-business-bookkeeping-basics',
    'how-to-track-business-expenses-without-spreadsheets',
    'small-business-tax-deductions',
  ],
];
