<?php
// articles/data/cash-basis-vs-accrual-accounting.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'cash-basis-vs-accrual-accounting',

  'h1' => 'Cash basis vs accrual accounting, explained simply',

  'meta_title' => 'Cash Basis vs Accrual Accounting Explained | Argo Books',

  'meta_description' => 'Cash basis vs accrual accounting in plain English: what each one means, a worked example, who uses which, and how to switch. No jargon, just the difference.',

  'schema_type' => 'Article',

  'category' => 'bookkeeping',
  'hub_weight' => 40,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>There are two ways to keep a set of books, and the only real difference between them is <em>timing</em>: when a sale or a cost lands in your records. Cash basis records money when it actually moves. Accrual records it when you earn it or owe it, even if no money has changed hands yet. That one difference decides which month your profit shows up in, how big your tax bill looks on paper, and whether your books say you're doing great or barely breaking even.</p>
<p>Most self-employed people and small businesses use cash basis because it's simpler and it matches what's in the bank. But there's a point where accrual takes over, either because you've grown, you carry stock, or a tax rule pushes you there. This guide explains both in plain language, walks the same invoice through each method so you can see the gap, and shows you who typically uses which. No accounting background needed.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What each method actually means',
      'anchor' => 'what-they-mean',
      'html' => <<<'HTML'
<p>Strip away the textbook language and it comes down to a single question: when does a transaction count?</p>
<p><strong>Cash basis</strong> counts money when it actually moves. You record revenue on the day the client's payment hits your account, and you record an expense on the day you actually pay for something. If you send an invoice in March and the client pays in May, cash basis says the sale happened in May, because that's when the money arrived. It's the way most people already think about their own finances: money in, money out, what's left is yours.</p>
<p><strong>Accrual basis</strong> counts money when it's <em>earned</em> or <em>owed</em>, not when it moves. You record revenue on the day you finish the work and send the invoice, even if the client won't pay for another two months. You record an expense on the day you receive the bill, even if you won't pay it until later. Accrual tries to match the income and the costs to the period when the actual work happened, regardless of when cash follows.</p>
{{illustration:compare-scale}}
<p>Here's the plain-English version of both. A designer finishes a logo on June 28, emails the invoice the same day, and the client pays on July 20:</p>
<ul>
<li><strong>Cash basis:</strong> the $2,000 is July revenue. That's the month the money landed.</li>
<li><strong>Accrual basis:</strong> the $2,000 is June revenue. That's the month the work was done and the invoice was raised.</li>
</ul>
<p>Neither answer is wrong. They're answering two different questions. Cash basis answers "how much money did I actually get this month?" Accrual answers "how much did I earn this month, whether or not I've been paid yet?" Almost every disagreement between two versions of the same books traces back to this one distinction.</p>
HTML,
    ],

    [
      'h2' => 'The same invoice, two different months',
      'anchor' => 'worked-example',
      'html' => <<<'HTML'
<p>The fastest way to feel the difference is to run one real transaction through both methods. Say you're a contractor. In March you finish a job, and here's what happens:</p>
<ul>
<li><strong>March 15:</strong> you finish the work and send an invoice for $6,000, due in 30 days.</li>
<li><strong>March 20:</strong> you buy $1,500 of materials for the job on a supplier account. The supplier's bill is due in 30 days too.</li>
<li><strong>April 18:</strong> the client pays the $6,000.</li>
<li><strong>April 25:</strong> you pay the supplier the $1,500.</li>
</ul>
<p>Now look at what your profit for March looks like under each method.</p>
<p><strong>Cash basis.</strong> Nothing moved in March. The client's money didn't arrive until April, and you didn't pay the supplier until April either. So on cash basis, March shows <strong>$0 revenue, $0 expenses, $0 profit</strong>. Then April shows <strong>$6,000 revenue minus $1,500 expenses, which is $4,500 profit</strong>. All of the activity lands in April, the month the cash actually moved.</p>
<p><strong>Accrual basis.</strong> The work happened in March and the materials were for a March job, so both get recorded in March. March shows <strong>$6,000 revenue minus $1,500 expenses, which is $4,500 profit</strong>. April shows <strong>$0</strong>, because the payments in April were just settling amounts already recorded in March.</p>
<p>Same job, same $4,500 profit in the end. But cash basis puts all of it in April, and accrual puts all of it in March. If March was your year-end, the two methods would report completely different results for the year, and a very different tax bill on paper. That's the whole game: the total is identical over time, but the <em>timing</em> moves, and timing is what tax periods and monthly reports are built on.</p>
<p>The gap gets wider the more you invoice on terms. If you have $40,000 sitting in unpaid invoices at year-end, accrual counts all $40,000 as this year's income. Cash basis counts none of it until it's paid. That can be the difference between a comfortable tax bill and a nasty one, which is exactly why the method you use isn't just an accounting detail.</p>
HTML,
    ],

    [
      'h2' => 'Cash basis: the pros and cons',
      'anchor' => 'cash-pros-cons',
      'html' => <<<'HTML'
<p>Cash basis is the default for a reason. It's simple, and it tells you the truth about your bank balance.</p>
<p><strong>What's good about it:</strong></p>
<ul>
<li><strong>It's simple.</strong> A sale is a sale on the day you get paid. There's nothing to track between the invoice and the payment. For a one-person business, that's a big deal.</li>
<li><strong>It matches your bank account.</strong> Your books and your bank balance move together, so it's easy to see what you can actually spend. You never look profitable on paper while your account is empty.</li>
<li><strong>You don't pay tax on money you haven't received.</strong> If a client is slow to pay, that income doesn't count until it lands, so you're never taxed on cash you're still waiting on.</li>
</ul>
<p><strong>Where it falls short:</strong></p>
<ul>
<li><strong>It hides what you're owed.</strong> A month where you did $20,000 of work but got paid $2,000 looks like a terrible month on cash basis, even though you'll be paid soon. The books don't show the work you've earned.</li>
<li><strong>It can mislead you on a big month.</strong> A month where three old invoices all land at once looks amazing, even if you barely worked. Cash basis is lumpy, and the lumps don't line up with your actual effort.</li>
<li><strong>It's easy to distort timing.</strong> You can push income into next year just by delaying a deposit, or pull expenses forward by prepaying. That flexibility can be handy, but it also means the numbers don't reflect real performance.</li>
</ul>
<p>For day-to-day decisions, though, cash basis is usually what you want. It answers the question that keeps most small-business owners up at night: is there money in the bank, and how much?</p>
HTML,
    ],

    [
      'h2' => 'Accrual basis: the pros and cons',
      'anchor' => 'accrual-pros-cons',
      'html' => <<<'HTML'
<p>Accrual is more work, but it paints a truer picture of how the business is actually performing month to month.</p>
<p><strong>What's good about it:</strong></p>
<ul>
<li><strong>It matches income to effort.</strong> The month you do the work is the month it shows up, so a busy month looks busy and a slow month looks slow, no matter when the cash arrives. That makes trends far easier to read.</li>
<li><strong>It shows the full picture.</strong> Money you're owed and bills you owe are both on the books, so you can see the real shape of the business, not just what's cleared the bank.</li>
<li><strong>It's what lenders and investors expect.</strong> Banks, serious buyers, and most formal reporting standards assume accrual. If you ever want financing or plan to sell, accrual books are the ones people trust.</li>
</ul>
<p><strong>Where it falls short:</strong></p>
<ul>
<li><strong>It's more work.</strong> You're tracking invoices and bills separately from payments, which means more moving parts to keep straight.</li>
<li><strong>It can show profit you can't spend.</strong> Accrual can say you made $4,500 this month while your bank account is still empty because nobody's paid yet. If you're not watching cash flow separately, that's a trap.</li>
<li><strong>You may owe tax before you're paid.</strong> Under accrual, a big unpaid invoice at year-end still counts as income, so you could owe tax on money that hasn't arrived. That's the flip side of the timing.</li>
</ul>
<p>The honest takeaway: accrual is better for understanding the business, and cash basis is better for understanding your bank account. This is exactly why a lot of owners end up wanting both, which we'll come back to at the end.</p>
HTML,
    ],

    [
      'h2' => 'Who typically uses which',
      'anchor' => 'who-uses-which',
      'html' => <<<'HTML'
<p>You don't have to guess where you fall. There are clear patterns.</p>
<p><strong>Cash basis is the norm for:</strong></p>
<ul>
<li>Freelancers, consultants, and contractors who sell their time.</li>
<li>Sole traders and self-employed people generally.</li>
<li>Service businesses that don't hold stock.</li>
<li>Anyone small enough that simplicity matters more than a perfectly matched month.</li>
</ul>
<p>If you sell your time or a service, get paid reasonably close to when you do the work, and don't carry inventory, cash basis is almost certainly the right call. It's simpler, and there's usually no rule forcing you off it.</p>
<p><strong>Accrual becomes the right choice when:</strong></p>
<ul>
<li><strong>You carry inventory.</strong> Once you're buying stock to resell, cash basis stops telling the truth, because a big stock purchase looks like a huge expense the month you buy it even though you'll sell it over many months. Accrual (and a balance sheet) handles this properly. See <a href="/inventory-tracking-for-small-businesses/">inventory tracking for small businesses</a> if this is you.</li>
<li><strong>You've grown past a certain size.</strong> Bigger businesses, especially ones with employees, staff on payroll, or serious credit terms, generally move to accrual for a clearer view.</li>
<li><strong>A tax rule requires it.</strong> Once your revenue passes the threshold in your country, or you hold inventory, the tax office may require accrual whether you like it or not. More on those thresholds next.</li>
<li><strong>You want financing or plan to sell.</strong> Lenders and buyers want accrual books, so growing businesses often switch ahead of time to be ready.</li>
</ul>
<p>A useful rule of thumb: start on cash basis, and switch to accrual when either the tax rules push you or the business gets complex enough that cash basis stops making sense. Most people don't need accrual on day one, and plenty never do.</p>
HTML,
    ],

    [
      'h2' => 'Switching methods and country thresholds',
      'anchor' => 'switching-and-thresholds',
      'html' => <<<'HTML'
<p>Two practical questions come up a lot: can I switch, and when am I forced to?</p>
<p><strong>Can you switch?</strong> Yes, but you generally can't flip back and forth whenever you feel like it. Tax offices treat your accounting method as a choice you commit to, and changing it usually means notifying them and, in some countries, getting approval or filing a specific form. You also have to handle the crossover carefully so income and expenses don't get counted twice or missed entirely when they straddle the switch. It's very doable, but it's the kind of change worth running past an accountant so it's done cleanly.</p>
{{illustration:coins}}
<p><strong>When are you forced onto accrual?</strong> Most countries let small businesses use cash basis up to a revenue threshold, then require accrual above it or once you hold inventory. The exact numbers change over time and by business type, so treat these as general signposts, not gospel:</p>
<ul>
<li><strong>United States:</strong> many small businesses can use cash basis, with the requirement to switch tied to an average annual gross receipts test (in the millions) and to whether you carry inventory. Above the threshold or with significant inventory, accrual is generally required.</li>
<li><strong>United Kingdom:</strong> sole traders and partnerships can use cash basis for self-assessment, and in recent years cash basis has become the default for eligible unincorporated businesses, with accrual as the alternative you opt into.</li>
<li><strong>Canada:</strong> most businesses are expected to use accrual for tax, with a notable cash-basis allowance for farming and fishing. Self-employed people should confirm what applies to their situation.</li>
<li><strong>Australia:</strong> smaller businesses under the turnover threshold can generally choose either method, including for GST reporting, with larger businesses moving to accrual.</li>
</ul>
<p>The thresholds and the fine print shift, and they depend on how your business is structured, so check with an accountant for your situation before you assume you're on the right method or that you're free to switch. The general shape holds everywhere though: small and simple starts on cash basis, and growth or inventory pushes you toward accrual.</p>
HTML,
    ],

    [
      'h2' => 'How Argo Books gives you both views',
      'anchor' => 'argo-both-views',
      'html' => <<<'HTML'
<p>Here's the part most people don't realize they need until they hit it: the best answer often isn't picking one method, it's seeing both. And in Argo Books you get both without doing anything extra.</p>
<p>The <strong>dashboard and analytics run on a cash basis.</strong> They count only invoices your customers have actually paid, so the numbers match what's in the bank. When you glance at your dashboard to decide whether you can afford a purchase this week, you're looking at real money you've collected, not money you're still owed. That's the cash-basis question, answered automatically.</p>
<p>The <strong>Reports run on an accrual basis.</strong> When you build an Income Statement (profit and loss) or a Balance Sheet in Argo Books, it counts all your invoiced revenue, paid or not. That's the view your accountant wants at tax time and the view that shows how the business actually performed, work done rather than cash collected. Same data, the accrual question answered.</p>
<p>So the $6,000 contractor invoice from earlier shows up on your dashboard the month it gets paid, and on your Income Statement the month you raised it. You don't choose a setting or keep two sets of books. The dashboard is your bank-balance reality, the reports are your accrual picture, and both are built from the same records you already entered.</p>
<p>One more thing worth knowing: sales tax you collect never counts as profit in either view. Argo Books treats it as money you owe the government, not income, so your profit figure stays honest no matter which basis you're looking at. If a number ever surprises you, the difference is almost always cash basis versus accrual, and now you know which surface is which.</p>
<p>The Report Builder that produces the accrual-basis Income Statement, Balance Sheet, and tax summaries is free, and it exports a clean branded PDF you can hand straight to your accountant. You get the day-to-day cash view and the formal accrual view from one place, which is exactly what most small businesses actually want.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 5,

  'tool_callout_text' => 'The free Report Builder turns your data into an accrual-basis Income Statement and Balance Sheet, then exports a clean PDF for your accountant.',
  'tool_callout_cta' => 'See the Report Builder',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'Which method is simpler?',
      'a' => 'Cash basis, by a wide margin. A sale counts on the day you get paid and an expense counts on the day you pay it, so there\'s nothing to track between an invoice and its payment. Accrual asks you to record income when it\'s earned and expenses when they\'re owed, which means keeping track of unpaid invoices and unpaid bills separately from the actual cash. For a one-person business that sells time or services, cash basis is far less to manage.',
    ],
    [
      'q' => 'Which method should a freelancer use?',
      'a' => 'Cash basis, almost always. Freelancers sell their time, usually get paid reasonably close to when they do the work, and don\'t carry inventory, which is exactly the situation cash basis handles best. It\'s simpler, it matches your bank account, and you don\'t pay tax on invoices that haven\'t been paid yet. Unless a tax rule in your country pushes you onto accrual, or you start selling physical stock, there\'s little reason for a freelancer to take on the extra work of accrual.',
    ],
    [
      'q' => 'Can I switch methods?',
      'a' => 'Yes, but usually not on a whim. Tax offices treat your accounting method as a commitment, so switching typically means notifying them and, in some countries, getting approval or filing a form. You also have to handle the crossover carefully so that income and expenses straddling the switch aren\'t counted twice or missed. It\'s a normal thing to do as a business grows, but it\'s worth running past an accountant so the change is done cleanly for that first year.',
    ],
    [
      'q' => 'Does the tax office care which method I use?',
      'a' => 'Yes. Most countries let small businesses choose cash basis up to a revenue threshold or until they hold inventory, then require accrual above that. The exact thresholds vary by country and business type and change over time, so they\'re signposts rather than fixed rules. Because your method affects which year income falls into, tax authorities want you to pick one and stick with it rather than switching to shift a tax bill around. Check with an accountant for your situation to confirm which method you\'re allowed or required to use.',
    ],
    [
      'q' => 'Which method does Argo Books use?',
      'a' => 'Both, and you don\'t have to choose. The dashboard and analytics run on a cash basis, counting only invoices your customers have actually paid, so those numbers match what\'s in the bank. The Reports, including the Income Statement and Balance Sheet, run on an accrual basis, counting all your invoiced revenue whether it\'s been paid or not. That gives you the day-to-day cash view for spending decisions and the formal accrual view for taxes and your accountant, all from the same records.',
    ],
    [
      'q' => 'Why is the profit on my dashboard different from the profit on my report?',
      'a' => 'It\'s the cash-versus-accrual difference, and it\'s expected. The dashboard is cash basis, so it only counts invoices that have actually been paid. A report like the Income Statement is accrual basis, so it counts everything you\'ve invoiced, paid or not. If you have unpaid invoices outstanding, the accrual report will show more revenue than the dashboard until those invoices are paid. Both numbers are correct; they\'re just answering different questions.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'contractor',
    'generic',
  ],

  'related_article_slugs' => [
    'what-is-a-profit-and-loss-statement',
    'small-business-bookkeeping-basics',
    'how-to-do-bookkeeping-without-an-accountant',
    'gross-profit-vs-net-profit',
  ],
];
