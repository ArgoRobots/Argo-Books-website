<?php
// articles/data/how-much-to-set-aside-for-taxes-self-employed.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-much-to-set-aside-for-taxes-self-employed',

  'h1' => 'How much to set aside for taxes when self-employed',

  'meta_title' => 'How Much to Set Aside for Taxes Self-Employed | Argo Books',

  'meta_description' => 'How much to set aside for taxes when you work for yourself: why 25 to 30% of profit is the safe rule, and the buckets you can\'t forget.',

  'schema_type' => 'Article',

  'category' => 'receipts-expenses',
  'hub_weight' => 60,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>When you work for yourself, nobody takes tax out of your pay before it lands in your account. Every dollar a client sends shows up whole, which feels great right up until the tax bill arrives and a chunk of it was never actually yours to spend. The people who get caught out aren't careless. They just spent money that had a tax claim on it the whole time.</p>
<p>The fix is boring and it works: set a slice of every payment aside the moment it arrives, before you touch the rest. This guide covers how big that slice should be, why it's a range rather than one magic number, the three separate things your set-aside has to cover, and a simple habit that makes it automatic. Real numbers throughout, plus short notes for the US, Canada, the UK, and Australia.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why self-employed people get caught out',
      'anchor' => 'why-caught-out',
      'html' => <<<'HTML'
<p>When you had a regular job, your employer did something quietly helpful on every payday: they took income tax and social contributions out of your wage and sent them to the government before you ever saw the money. The number on your payslip was already the after-tax number. You could spend all of it because the tax was gone.</p>
<p>Working for yourself, that machinery disappears. A client pays you $3,000 and all $3,000 hits your account. Nothing was withheld. It looks like you earned $3,000, but if roughly a quarter of it is owed in tax, you really earned about $2,250 and you're holding $750 of the government's money by accident. Spend it on rent and stock and a new laptop, and when the tax deadline comes you have to find that $750 out of next month's income, which had its own tax claim sitting on top of it. That's how a first tax bill turns into a hole that takes a year to climb out of.</p>
<p>The trap isn't the size of the bill. It's the timing. The money arrives months before the bill does, so it never feels like tax money. Setting a percentage aside on the day each payment lands is the entire trick. Do that and the tax bill is just moving money from one account you own to the government. Skip it and the tax bill is a shock.</p>
HTML,
    ],

    [
      'h2' => 'The rule of thumb: 25 to 30% of profit',
      'anchor' => 'rule-of-thumb',
      'html' => <<<'HTML'
<p>Here's the short answer most self-employed people can run with: set aside <strong>25 to 30% of your profit</strong> for tax. Not 25 to 30% of what clients pay you, 25 to 30% of your profit, which is what's left after your business costs. At higher income levels, or in higher-tax places, push that toward <strong>35%</strong>.</p>
{{illustration:tax-jar}}
<p>Two things about that sentence matter a lot.</p>
<p><strong>It's a percentage of profit, not revenue.</strong> If clients pay you $80,000 in a year but you spent $20,000 running the business (software, equipment, materials, a home-office share, mileage, fees), your profit is $60,000. Tax is worked out on the $60,000, not the $80,000. This is why tracking expenses matters so much: every legitimate cost you record lowers the profit you're taxed on, which lowers the amount you need to set aside. Miss expenses and you'll over-set-aside, or worse, over-report and pay tax you didn't owe.</p>
<p><strong>It's a range because tax isn't one flat number.</strong> Two forces stack on top of each other. First, income tax, which is usually banded: the first slice of profit is taxed low (sometimes at zero, thanks to a tax-free allowance), and each higher slice is taxed at a higher rate. Second, a separate self-employment or social contribution that funds pensions and public health, which applies on top of income tax. Because both move with how much you earn, the honest answer to "what percentage?" is a range. Lower earners land near the bottom of it; higher earners land near the top. If you want a figure for your own numbers rather than a range, that's exactly what a tax calculator is for.</p>
HTML,
    ],

    [
      'h2' => 'The three buckets your set-aside covers',
      'anchor' => 'three-buckets',
      'html' => <<<'HTML'
<p>"Tax" isn't one thing. When you set money aside, you're really covering up to three separate obligations. Keeping them straight is the difference between guessing and knowing.</p>
<p><strong>Bucket 1: Income tax.</strong> The tax on your profit, banded as described above. This is usually the biggest of the three and the one people already expect.</p>
<p><strong>Bucket 2: Self-employment or social contributions.</strong> This is the one that surprises people. When you were employed, you paid part of this and your employer quietly paid the other part. Self-employed, you're on the hook for both halves. In the US it's called self-employment tax and runs about <strong>15.3%</strong> of net profit (Social Security plus Medicare). In Canada it's CPP, where you pay both the employee and employer share. In the UK it's National Insurance. In Australia there's no separate self-employment tax, but with no employer paying into your superannuation, many sole traders choose to fund it themselves. Different names, same idea: a chunk on top of income tax that a salaried worker never sees because it was handled for them.</p>
<p><strong>Bucket 3: Sales tax, and this one is never yours.</strong> If you're registered to charge sales tax, VAT, GST, or HST, the tax you add to a client's invoice was never your income for even a second. You're collecting it on the government's behalf and passing it along. It's the clearest case of money that lands in your account but isn't yours. Treat it as a separate liability, not as part of your set-aside percentage, because it works differently: you owe the tax you collected, minus the sales tax you paid on your own business purchases.</p>
{{illustration:coins}}
<p>Buckets 1 and 2 are what the 25 to 30% rule is really about. Bucket 3 sits alongside, held completely separately. If you mix sales tax into your general float and spend it, you'll be short at filing time by exactly the amount you collected, which is a painful and entirely avoidable surprise.</p>
HTML,
    ],

    [
      'h2' => 'A worked example',
      'anchor' => 'worked-example',
      'html' => <<<'HTML'
<p>Numbers make this concrete. Meet a freelance web designer, sole trader, one person, no staff.</p>
<ul>
<li>Clients paid her <strong>$90,000</strong> over the year (before any sales tax she charged on top).</li>
<li>She spent <strong>$18,000</strong> running the business: software subscriptions, a new laptop, a co-working desk, accountant fees, and her home-office share.</li>
<li>Her <strong>profit</strong> is therefore $90,000 minus $18,000 = <strong>$72,000</strong>. This is the number tax is worked out on.</li>
</ul>
<p>Apply the rule of thumb at the middle of the range, 28%:</p>
<ul>
<li>Set-aside for income tax plus self-employment contributions: 28% of $72,000 = <strong>$20,160</strong>.</li>
<li>That's roughly <strong>$1,680 a month</strong>, or about <strong>$5,040 a quarter</strong> if she pays estimated tax four times a year.</li>
</ul>
<p>Now watch what expenses did for her. If she'd never tracked that $18,000 of costs and reported the full $90,000 as profit, 28% would be $25,200. Tracking her expenses saved her over <strong>$5,000</strong> in set-aside on paper, and a real chunk of that is genuine tax she doesn't owe. That's not a loophole. It's just claiming the costs of doing business, which the tax rules expect you to do.</p>
<p>Separately, say she's registered for sales tax and charged clients 10% on top, collecting <strong>$9,000</strong> across the year. That $9,000 lives in its own account and never counted toward the $72,000 profit or the 28%. She'll hand most of it over at her sales-tax filing, reduced by the sales tax she paid on her own purchases. Three buckets, kept apart, no surprises.</p>
<p>One caveat: 28% is a planning figure, not a filing figure. Your real rate depends on your total income, your country, your region, and the deductions you qualify for. The point of the set-aside is to have <em>more</em> than enough parked when the real bill is calculated, so any leftover is yours to keep.</p>
HTML,
    ],

    [
      'h2' => 'The method: a separate account and a per-invoice skim',
      'anchor' => 'the-method',
      'html' => <<<'HTML'
<p>Knowing the percentage is useless if the money stays in your spending account, because you'll spend it. The habit that makes this work has two parts.</p>
<p><strong>Open a separate savings account just for tax.</strong> A plain savings account at your bank, named "Tax" so you never mistake it for spending money. Nothing you buy for the business or yourself ever comes out of it. The only two things that touch this account are money going in (your set-aside) and money going out (paying the tax bill). If your bank pays a little interest on savings, that interest is a small bonus for being organized. Some people open a second one labelled "Sales Tax" so bucket 3 is physically separate too.</p>
<p><strong>Skim a percentage off every single payment, the day it lands.</strong> Don't wait until month-end and try to do it in one lump, because by month-end the money's already been spent. The moment a client payment clears, move your percentage into the Tax account. Client pays $3,000, you move roughly $840 (at 28%) into Tax, and you treat the remaining $2,160 as the real payment. Do it per payment and the set-aside is never a big scary transfer. It's a small reflex you barely notice.</p>
{{illustration:forecast}}
<p>A rule you can set and forget: <em>every payment gets skimmed before it gets spent.</em> If you're registered for sales tax, skim that off too and send it to the sales-tax account. What's left after both skims is genuinely yours, and you can budget against it without any nagging feeling that some of it belongs to someone else. When the tax deadline arrives, the money's already sitting there. You're not scrambling, you're just paying.</p>
<p>The one thing this habit needs to be accurate is a clear view of your profit, because your set-aside is a percentage of profit, not of raw payments. That means keeping your expenses recorded as you go, so you always know the real number you're skimming against.</p>
HTML,
    ],

    [
      'h2' => 'Country notes: US, Canada, UK, Australia',
      'anchor' => 'country-notes',
      'html' => <<<'HTML'
<p>The 25 to 30% rule holds up surprisingly well across countries, but the pieces underneath it have different names and thresholds. Here's the shape of it. Treat these as orientation, not as filing advice, and check with an accountant for your situation, because rates, bands, and thresholds change.</p>
<p><strong>United States.</strong> Two layers. Federal income tax is banded, plus <strong>self-employment tax of about 15.3%</strong> on net profit (that's the Social Security and Medicare you'd normally split with an employer). Add state income tax on top in most states. Because self-employment tax alone is over 15%, many US freelancers aim closer to 30% and pay <a href="/how-to-pay-quarterly-estimated-taxes/">quarterly estimated taxes</a> to avoid an underpayment penalty at year-end.</p>
<p><strong>Canada.</strong> Federal and provincial income tax, both banded, plus <strong>CPP</strong> (Canada Pension Plan) where a self-employed person pays both the employee and the employer share. There's no separate employer to cover half, so budget for the full contribution. Quarterly instalments kick in once your tax owing passes a threshold. GST/HST is your bucket 3 once you're registered.</p>
<p><strong>United Kingdom.</strong> Income tax with a tax-free personal allowance at the bottom, then banded rates above it, plus <strong>National Insurance</strong>. The self-employed pay Class 4 NI, worked out through Self Assessment (the old flat-rate Class 2 was scrapped in April 2024, though low earners can still pay it voluntarily to protect their benefit entitlements). VAT is bucket 3 once you pass the registration threshold. Payments on account mean you can end up pre-paying part of next year's bill, so keep the set-aside going even after you file.</p>
<p><strong>Australia.</strong> Income tax with a tax-free threshold, then banded rates. There's no separate self-employment tax the way the US has one, and super isn't compulsory for sole traders, but with no employer funding your <strong>superannuation</strong> many choose to set money aside for it themselves. GST is bucket 3 once your turnover passes the registration threshold, and the ATO's PAYG instalment system spreads the income-tax bill across the year.</p>
<p>Across all four, the pattern is identical: income tax that steps up in bands, a social or pension contribution on top, and a separate sales tax you hold in trust. That's why one simple range covers most people. For a fuller breakdown of the sales-tax side, see <a href="/how-to-track-sales-tax-for-small-business/">how to track sales tax for small business</a>.</p>
HTML,
    ],

    [
      'h2' => 'Let the app do the number for you',
      'anchor' => 'let-the-app-help',
      'html' => <<<'HTML'
<p>Two things make the set-aside accurate instead of a guess: knowing your real profit, and getting a tax estimate for your actual situation. Argo Books helps with both.</p>
<p><strong>Track expenses so your profit is right.</strong> Since your set-aside is a percentage of profit, every business cost you record lowers the profit you're taxed on and the amount you need to park. In Argo Books you can import a photo of a receipt and the AI pulls out the vendor, date, amount, and tax for you, so recording an expense takes seconds instead of feeling like homework. Drop in a bank statement as a CSV, Excel, or PDF and every line comes back categorized. The more of your real costs you capture, the closer your profit figure is to the truth, and the less you either over-set-aside or accidentally over-report. See <a href="/how-to-track-business-expenses-without-spreadsheets/">how to track business expenses without spreadsheets</a>.</p>
<p><strong>See your tax numbers as you go.</strong> Argo Books tracks the sales tax you collected on invoices and the sales tax you paid on expenses, and shows the net (your bucket 3) as a running number, so you always know roughly what you're holding for the government. The free Report Builder turns your data into an income statement and a tax summary you can hand straight to your accountant, exported as a clean PDF. The app shows you the numbers; it doesn't file or remit tax for you, so you or your advisor still submit the actual return.</p>
<p>And to turn the rule of thumb into a real figure for your income, the free self-employed tax calculator on this site (for the US and Canada) estimates what to set aside each quarter based on your profit. Start with a percentage from this guide, then let the calculator sharpen it to your situation.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Want a number for your situation? The free self-employed tax calculator estimates what to set aside each quarter.',
  'tool_callout_cta' => 'Open the tax calculator',
  'tool_callout_url' => '/self-employed-tax-calculator/',

  'faqs' => [
    [
      'q' => 'What percentage should I set aside for taxes?',
      'a' => 'A safe rule of thumb is 25 to 30% of your profit, not of what clients pay you. Profit is what\'s left after your business costs, so track those costs carefully because they lower the amount you owe. If you\'re a higher earner or you live somewhere with higher tax rates, lean toward 30% or even 35%. Lower earners often land closer to 20 to 25% because the first slice of income is usually taxed at a low rate or not at all. The percentage is a planning cushion, not an exact bill, so it\'s fine to have a little more parked than you end up needing.',
    ],
    [
      'q' => 'Where should I keep the money I set aside?',
      'a' => 'In a separate savings account you only use for tax, named something obvious like "Tax" so you never mistake it for spending money. The only things that go in are your set-aside transfers, and the only thing that comes out is paying the tax bill. Keeping it separate from your everyday account is the whole point: if it\'s sitting with your spending money, you\'ll spend it without meaning to. A savings account also usually earns a little interest, which is a small reward for staying organized. If you collect sales tax, consider a second account just for that, since it\'s money you\'re holding for the government rather than income.',
    ],
    [
      'q' => 'Does my set-aside percentage include sales tax?',
      'a' => 'No, keep sales tax completely separate. The 25 to 30% rule covers income tax and your self-employment or social contributions, which are worked out on your profit. Sales tax (VAT, GST, or HST) is different: it was never your income, you\'re just collecting it from clients on the government\'s behalf and passing it along. Hold it in its own account and don\'t count it toward your income-tax set-aside. What you actually owe on the sales-tax side is the tax you collected on invoices minus the sales tax you paid on your own business purchases, so keep both figures recorded.',
    ],
    [
      'q' => 'What if I set aside too much?',
      'a' => 'That\'s a good problem to have, and it happens often because the rule of thumb is deliberately a bit cautious. If your tax bill comes in lower than what you parked, the leftover is simply yours to keep. You can move it back into your spending account, roll it toward next year\'s tax so you\'re ahead, or treat it as a small buffer for a quieter month. Over-setting-aside costs you nothing except a little cash sitting in savings for a while. Under-setting-aside costs you a scramble to find money you already spent, so if you\'re going to be off, being off on the high side is by far the safer direction.',
    ],
    [
      'q' => 'It\'s my first year and I have no estimate. What do I do?',
      'a' => 'Start conservative and adjust. With no prior year to base a figure on, set aside 30% of your profit from day one, which for most new self-employed people is more than enough to cover the first bill. Skim that 30% off every payment the moment it lands, before you spend anything. A few months in, run your numbers through a self-employed tax calculator to get a figure closer to your real situation, then dial the percentage up or down. The mistake to avoid is setting aside nothing "until you know," because by the time you know, the money has usually been spent. Park too much early, relax it later once you have real data.',
    ],
    [
      'q' => 'Do I still need to set money aside if my income is small?',
      'a' => 'Usually yes, though the percentage may be lower. Even at modest income, self-employment or social contributions often start at a lower threshold than income tax does, so you can owe something even when little or no income tax is due. Many countries have a tax-free allowance at the bottom, and if your profit sits entirely within it your income-tax bill may be zero, but the social-contribution piece can still apply. The safe move is to keep setting a smaller slice aside, check your country\'s thresholds, and confirm with an accountant. It\'s much easier to relax a habit you already have than to start one after a surprise bill.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'usa',
    'canada',
  ],

  'related_article_slugs' => [
    'how-to-pay-quarterly-estimated-taxes',
    'small-business-tax-deductions',
    'how-to-separate-business-and-personal-finances',
    'how-to-track-sales-tax-for-small-business',
  ],
];
