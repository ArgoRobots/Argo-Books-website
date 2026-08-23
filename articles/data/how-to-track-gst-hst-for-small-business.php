<?php
// articles/data/how-to-track-gst-hst-for-small-business.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-track-gst-hst-for-small-business',

  'h1' => 'How to track GST/HST for your small business (Canada)',

  'meta_title' => 'How to Track GST/HST for Small Business | Argo Books',

  'meta_description' => 'A plain-language guide to GST/HST for Canadian small businesses: the $30,000 rule, charging the right rate, input tax credits, and a simple tracking routine.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'bookkeeping',
  'hub_weight' => 47,

  'published' => '2026-07-22',

  'updated' => '2026-07-22',

  'reading_time_min' => 12,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>GST/HST is the tax most Canadian business owners meet before they feel ready for it. One month you're happily invoicing clients, the next you learn you crossed a sales threshold two quarters ago and were supposed to be charging tax the whole time. The rules aren't actually complicated, but nobody sits you down and explains them, so most people piece it together from forum posts and panic.</p>
<p>This guide covers the whole picture in plain language: how GST/HST works, the $30,000 small-supplier rule and the classic way people trip over it, how to charge the right rate when your customer lives in another province, the one bookkeeping habit that makes filing painless, and a short monthly routine that keeps you ahead of it. One note before we start: tax rules and rates change, so treat this as a map, not the law. Confirm current figures with the CRA, and check anything about your own filing obligations with the CRA or an accountant.</p>
HTML,

  'sections' => [

    [
      'h2' => 'How GST/HST works, in one breath',
      'anchor' => 'how-gst-hst-works',
      'html' => <<<'HTML'
<p>Here's the whole system in one sentence: you collect GST/HST on your sales, you pay GST/HST on your business purchases, and you send the CRA the difference. That's it. Everything else is detail.</p>
<p>Say you invoice a client $1,000 plus $130 of HST, and that same period you spend $400 plus $52 of HST on business supplies. You collected $130 for the CRA and you paid $52 of the same tax yourself. When you file, you report both numbers and remit the difference: $78. The tax you paid on purchases comes back to you as a credit, called an input tax credit (or ITC), which is the system's way of making sure businesses aren't taxed on the tax.</p>
<p>The mental shift that makes all of this easier: the GST/HST you collect is never your money. It lands in your bank account alongside your real revenue, which is exactly why so many owners accidentally spend it. From the moment a customer pays you, that tax portion is money you're holding for the CRA on its way through. Treat it that way in your books and in your head, and filing becomes an errand instead of an emergency.</p>
{{illustration:coins}}
<p>One scope note: this guide covers GST and HST, the federal system. Some provinces also charge their own separate provincial sales tax on top of the 5% GST (PST in British Columbia, Saskatchewan, and Manitoba; QST in Quebec). Those provincial taxes have their own rules and registrations, so if you're in one of those provinces, check your provincial tax separately.</p>
HTML,
    ],

    [
      'h2' => 'The $30,000 rule, and how people cross it without noticing',
      'anchor' => 'small-supplier-rule',
      'html' => <<<'HTML'
<p>You don't have to charge GST/HST from day one. Until your business passes the small-supplier threshold, registration is optional. The rule: you must register for a GST/HST account once your total taxable sales exceed $30,000 over four consecutive calendar quarters, or in a single calendar quarter. Below that line, you're what the CRA calls a small supplier, and you can stay unregistered if you want.</p>
<p>The trap is in the phrase "four consecutive calendar quarters." It's not a calendar-year test that resets every January, it's a rolling window. A freelancer who bills $8,000 a quarter is fine at $24,000 over a year, but the moment a good quarter pushes the trailing four-quarter total past $30,000, the clock starts. Plenty of owners only discover this when an accountant looks back through their invoices and finds the crossing point months in the rear-view mirror, which can mean owing tax they never collected from customers. The fix is simple and boring: watch your rolling twelve-month sales total, not just your year-to-date. If you're anywhere near $30,000, start checking it monthly. The exact registration timing rules have some wrinkles, so once you're close, confirm the details with the CRA or an accountant.</p>
<p>Here's the counterintuitive part: registering before you have to can actually pay. Once registered, you can claim back the GST/HST you pay on business purchases as input tax credits. If you're spending on equipment, software, or supplies while revenue is still ramping up, voluntary registration means the tax on those purchases comes back to you instead of being a cost you eat. The trade-off is real too: you have to charge your customers tax, file returns, and keep the records. For businesses selling to other businesses, adding tax to the invoice barely matters because your customers claim it back themselves. For businesses selling to the public, it effectively raises your prices. Whether early registration is worth it depends on your situation, so it's a genuinely good question to put to an accountant.</p>
HTML,
    ],

    [
      'h2' => 'Charging the right rate: it depends where your customer is',
      'anchor' => 'right-rate',
      'html' => <<<'HTML'
<p>Canada doesn't have one sales tax rate, it has a patchwork. The federal GST is 5% everywhere. Five provinces have harmonized their provincial tax with the GST into a single HST: Ontario at 13%, Nova Scotia at 14%, and New Brunswick, Newfoundland and Labrador, and Prince Edward Island at 15%. The rest of the country charges the 5% GST, with some provinces layering their own separate PST or QST on top, which as mentioned above is outside this guide's scope.</p>
<p>The rule that surprises people is called place of supply: as a general idea, you charge based on where your customer is, not where you are. An Ontario consultant billing a client in Nova Scotia generally charges 14% HST, not Ontario's 13%. A Nova Scotia shop shipping an order to Alberta generally charges 5% GST. This is why selling across provinces means your invoices can carry different rates depending on who you're billing. The place-of-supply rules have specific tests for goods versus services versus digital products, so treat "charge the customer's province's rate" as the general shape and confirm the details for your situation with the CRA.</p>
<p>And the rates themselves move. Nova Scotia's HST dropped from 15% to 14% on April 1, 2025. Anyone who had 15% hard-coded in a spreadsheet formula or saved in an invoice template started overcharging Nova Scotia customers that day without anything looking wrong. That's the quiet cost of managing tax rates by hand: nothing warns you when the number goes stale. Whatever you use to invoice, know where your rates live and check them against the CRA's current published rates once in a while, especially any time a federal or provincial budget makes news.</p>
HTML,
    ],

    [
      'h2' => 'The habit that makes filing painless: track tax separately',
      'anchor' => 'track-separately',
      'html' => <<<'HTML'
<p>Here's the single bookkeeping habit that decides whether filing takes twenty minutes or a lost weekend: keep GST/HST out of your income and expense numbers entirely, on both sides.</p>
<p>On the sales side, when a customer pays you $1,130 on a $1,000 invoice with 13% HST, record $1,000 of income and $130 of tax collected. Not $1,130 of income. The $130 was never yours, and folding it into revenue inflates your income, distorts your profit, and buries the number you'll need at filing time inside every transaction.</p>
<p>On the purchase side, do the mirror image. When you spend $565 on supplies, that's $500 of expense and $65 of tax paid. Track that tax-paid amount separately, because it's the running total of your input tax credits, the money you'll claim back. Lump it into expenses and you'll either forget to claim it or spend hours at filing time digging the tax portion back out of a year of receipts.</p>
{{illustration:report-statement}}
<p>Do both consistently and your GST/HST return becomes nearly automatic: tax collected minus tax paid equals what you owe, and both totals are sitting there waiting. Skip it and filing means re-opening every invoice and receipt to split the tax out by hand. This is the same principle behind sales tax tracking anywhere in the world, and our guide on <a href="/how-to-track-sales-tax-for-small-business/">how to track sales tax for your small business</a> goes deeper on setting it up. A related habit worth stealing: some owners move the collected tax into a separate savings account as it comes in, so the CRA's money physically can't get spent by accident.</p>
HTML,
    ],

    [
      'h2' => 'What filing actually involves',
      'anchor' => 'filing',
      'html' => <<<'HTML'
<p>If you've been tracking tax collected and tax paid separately, filing is mostly arithmetic. At a high level, a GST/HST return asks: how much did you sell this period, how much tax did you collect, how much tax did you pay on business purchases, and what's the difference. If you collected more than you paid, you remit the difference to the CRA. If you paid more than you collected, which happens in heavy-spending stretches like a startup year or a big equipment purchase, the CRA refunds you the difference.</p>
<p>How often you file depends on your revenue. Smaller businesses generally file annually, and larger ones file quarterly or monthly, with some ability to choose a more frequent schedule than required. Filing more often means smaller amounts each time and less chance of a scary lump sum; filing annually means less paperwork but requires the discipline not to spend the tax you've been collecting all year. Your filing frequency, deadlines, and payment due dates depend on your specific numbers and registration, so confirm yours with the CRA or an accountant rather than assuming.</p>
<p>Two things catch first-time filers. First, the return covers a defined reporting period, so your books need to be able to answer "how much tax did I collect between these two dates," which is trivial if you've tracked it separately and painful if you haven't. Second, you file even for periods where you owe nothing. Registration comes with the obligation to file on schedule, not just when there's money to send.</p>
HTML,
    ],

    [
      'h2' => 'A simple monthly GST/HST routine',
      'anchor' => 'monthly-routine',
      'html' => <<<'HTML'
<p>None of this needs to eat your life. A short monthly routine keeps you current and makes filing day a non-event:</p>
<ol>
<li><strong>Record the month's sales with tax split out.</strong> Every invoice and sale goes into your books as income plus tax collected, as two separate numbers. If your invoicing tool does this automatically, you're just confirming it happened.</li>
<li><strong>Record the month's purchases with tax split out.</strong> Same on the expense side: each business purchase as expense plus tax paid, so your input tax credit total stays current. Keep the receipts, since they're your backup for every credit you claim.</li>
<li><strong>Check your net position.</strong> Tax collected minus tax paid, year to date for your reporting period. That's roughly what you'd owe if you filed today. No surprises at filing time because you watched the number grow.</li>
<li><strong>Set the money aside.</strong> Move the net amount somewhere you won't touch it. The single most common GST/HST disaster is a business that collected the tax, spent it, and met a filing deadline with an empty account.</li>
<li><strong>Glance at your rolling sales total.</strong> If you're not yet registered, check your trailing four quarters against the $30,000 threshold. If you're near it, talk to an accountant before you cross it, not after.</li>
</ol>
<p>Fifteen to thirty minutes a month, most of it recording things you'd record anyway. The routine works in a spreadsheet, and it works better in software that splits the tax for you. What matters is that it happens monthly, because GST/HST problems are cheap to fix when they're four weeks old and expensive when they're four quarters old.</p>
HTML,
    ],

    [
      'h2' => 'Where software helps, and where it can\'t',
      'anchor' => 'where-software-helps',
      'html' => <<<'HTML'
<p>The honest division of labor: software is good at the tracking, and only you (or your accountant) can do the filing.</p>
<p>The tracking side is exactly the kind of repetitive splitting that tools do well. Accounting software applies the right tax rate on each invoice, records tax collected separately from income and tax paid separately from expenses without you thinking about it, and keeps a running summary of both totals. Argo Books does this: it tracks the tax you've collected against the tax you've paid and shows your net position and a tax summary, so the numbers your return asks for are already sitting in a report. Argo is also Canadian-built, so GST/HST isn't an afterthought bolted onto an American product. If you're comparing options, our guide to <a href="/free-accounting-software-canada/">free accounting software for Canadian businesses</a> covers the field honestly, including where Argo fits and where it doesn't.</p>
<p>What no software should claim, and Argo doesn't: it does not file your GST/HST return or remit the money. You or your accountant do that with the CRA. The software's job is to make sure that when you sit down to file, the two numbers you need are accurate and one click away, instead of scattered across a year of invoices. That's a real difference in effort, but the filing itself, and the responsibility for getting it right, stays with you. If your situation has any wrinkles, cross-province sales, mixed taxable and exempt products, a provincial tax on top, that's your cue to spend an hour with an accountant. It's one of the cheapest hours a small business ever buys.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Argo Books tracks the GST/HST you collect against the tax you pay and shows your net position in a tax summary, so filing time starts with the numbers already done.',
  'tool_callout_cta' => 'See the report builder in Argo Books',
  'tool_callout_url' => '/features/report-builder/',

  'faqs' => [
    [
      'q' => 'Do I have to charge GST/HST if I make less than $30,000?',
      'a' => 'Generally no. Below the small-supplier threshold, registering for a GST/HST account is optional, and if you\'re not registered you don\'t charge the tax. The threshold is $30,000 of total taxable sales over four consecutive calendar quarters, or in a single quarter, and it\'s a rolling window rather than a calendar-year test, so watch your trailing twelve months. Some businesses register voluntarily anyway, because registration lets you claim back the GST/HST you pay on business purchases as input tax credits. Whether that trade is worth it depends on what you spend and who you sell to, so confirm your situation with the CRA or an accountant.',
    ],
    [
      'q' => 'What are input tax credits?',
      'a' => 'Input tax credits, or ITCs, are the mechanism that gives you back the GST/HST you pay on business purchases. If you\'re registered and you buy supplies, equipment, or services for the business, the tax you paid on those purchases generally comes off what you owe the CRA. So instead of remitting everything you collected, you remit tax collected minus tax paid. That\'s why tracking the tax portion of your expenses separately matters: your ITC total is money you\'re owed, and you can only claim it if you can show it. The rules about which purchases qualify have details worth checking with the CRA or an accountant.',
    ],
    [
      'q' => 'What rate do I charge a customer in another province?',
      'a' => 'As a general rule, you charge based on where your customer is, not where your business sits. That\'s the place-of-supply idea: an Ontario business billing a client in New Brunswick generally charges New Brunswick\'s 15% HST, and one shipping to a GST-only province generally charges 5%. The specific rules differ for goods, services, and digital products, so the safe approach is to confirm the treatment of what you actually sell with the CRA. Also remember that rates change over time, so check current rates against the CRA\'s published figures rather than trusting a number saved in an old template.',
    ],
    [
      'q' => 'Is the GST/HST I collect counted as income?',
      'a' => 'No, and treating it as income is one of the most common bookkeeping mistakes in Canada. The tax you add to an invoice is money you collect on the CRA\'s behalf and pass along when you file. It should be recorded separately from your revenue: a $1,000 invoice with 13% HST is $1,000 of income and $130 of tax collected, not $1,130 of income. Mixing them inflates your revenue, distorts your profit, and makes filing miserable because you have to dig the tax back out of every transaction. Track it separately from day one and your return practically writes itself.',
    ],
    [
      'q' => 'How often do I have to file a GST/HST return?',
      'a' => 'It depends on your revenue. Broadly, smaller businesses file annually and larger ones file quarterly or monthly, and in some cases you can choose to file more often than required, which some owners prefer because it keeps each payment small. Your assigned frequency, deadlines, and payment due dates depend on your specific registration and numbers, so confirm yours with the CRA or an accountant. Two things worth knowing either way: you must file for every period once registered, even a period where you owe nothing, and if you paid more GST/HST than you collected in a period, filing is how you get that difference refunded.',
    ],
  ],

  'related_niche_slugs' => [
    'canada',
    'generic',
    'contractor',
  ],

  'related_article_slugs' => [
    'how-to-track-sales-tax-for-small-business',
    'free-accounting-software-canada',
    'how-to-pay-quarterly-estimated-taxes',
  ],
];
