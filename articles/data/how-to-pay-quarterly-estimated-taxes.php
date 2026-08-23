<?php
// articles/data/how-to-pay-quarterly-estimated-taxes.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-pay-quarterly-estimated-taxes',

  'h1' => 'How to pay quarterly estimated taxes',

  'meta_title' => 'How to Pay Quarterly Estimated Taxes | Argo Books',

  'meta_description' => 'If no one withholds tax from your income, you probably owe it four times a year. Here\'s who pays, the due dates, how to estimate, and how to avoid a penalty.',

  'schema_type' => 'HowTo',

  'category' => 'bookkeeping',
  'hub_weight' => 90,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>When you work a regular job, your employer takes tax out of every paycheck and sends it to the government for you. You never see that money, so you never have to think about it. When you work for yourself, nobody does that. The full payment lands in your account, tax and all, and it's on you to hand the tax part back later. To stop people owing one giant bill at the end of the year, the tax office asks self-employed people to pay in installments as they earn. In the US these are called quarterly estimated taxes.</p>
<p>This guide is US-focused, because that's where the four-times-a-year system is strictest, but it covers the Canada, UK, and Australia versions too so you're not lost if you're outside the States. You'll learn who actually owes these payments, the due dates, how to work out the amount, the "safe harbor" trick that keeps you penalty-free, how to pay online in a few minutes, and what to do in your very first year when you have no prior figure to lean on.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Who has to pay quarterly estimated taxes',
      'anchor' => 'who-owes-them',
      'step_name' => 'Check whether you owe estimated taxes',
      'step_text' => 'You generally owe quarterly estimated taxes if you expect to owe $1,000 or more at year-end and nobody is withholding tax for you. That covers most self-employed people, freelancers, and small business owners.',
      'html' => <<<'HTML'
<p>The rule is simpler than it sounds. If tax isn't being taken out of your income for you, and you expect to owe at least $1,000 when you file, the US expects you to pay as you go, four times a year. That sweeps in a lot of people:</p>
<ul>
<li><strong>Freelancers and gig workers.</strong> A graphic designer paid the full invoice amount, a rideshare driver, a virtual assistant. No employer, no withholding.</li>
<li><strong>Sole proprietors and single-member LLCs.</strong> If your business income flows onto your personal return, you're on the hook for estimated payments.</li>
<li><strong>Independent contractors.</strong> A plumber doing 1099 work, a consultant on a retainer. The client pays you gross and reports it, but takes nothing out.</li>
<li><strong>Anyone with big untaxed side income.</strong> Rental income, investment gains, or a profitable side hustle on top of a day job can push you over the line even if your main paycheck is taxed.</li>
</ul>
<p>Remember that for self-employed people this covers two taxes, not one. There's income tax, and there's self-employment tax (Social Security and Medicare, roughly 15.3% on your net profit). An employee splits that second one with their employer. When you work for yourself, you pay both halves, which is why the bill catches so many first-timers off guard.</p>
<p>If your only income is a normal salaried job where tax is already withheld, you almost certainly don't need to do any of this. This guide is for the people the withholding system skips.</p>
HTML,
    ],

    [
      'h2' => 'The four payment periods and due dates',
      'anchor' => 'due-dates',
      'step_name' => 'Mark the four due dates',
      'step_text' => 'US estimated taxes are due four times a year, roughly in April, June, September, and January. Put all four dates on your calendar at the start of the year so none of them sneak up on you.',
      'html' => <<<'HTML'
<p>The word "quarterly" is a little misleading, because the four periods aren't evenly spaced. Each payment covers the income you earned in the months just before it. The usual dates are:</p>
<ul>
<li><strong>Q1:</strong> due mid-April, covering income from January through March.</li>
<li><strong>Q2:</strong> due mid-June, covering April and May.</li>
<li><strong>Q3:</strong> due mid-September, covering June through August.</li>
<li><strong>Q4:</strong> due mid-January of the following year, covering September through December.</li>
</ul>
<p>Notice that the second period is only two months long and the third is three. That trips people up: it's easy to spend the Q2 money thinking you have until Q3, then get surprised in June. When a due date lands on a weekend or a holiday, it rolls to the next business day. The exact dates shift a day or two each year, so confirm them on the IRS site rather than trusting a date from memory.</p>
{{illustration:calendar-due}}
<p>The single best habit here is to put all four dates in your calendar in January with a reminder a week ahead of each one. A late payment isn't the end of the world, but the penalty is calculated per period, so a payment that's a month late costs you a small amount of interest for that month. Paying on time simply avoids the whole question.</p>
HTML,
    ],

    [
      'h2' => 'How to estimate what to pay',
      'anchor' => 'estimate-the-amount',
      'step_name' => 'Estimate the amount for each period',
      'step_text' => 'Take your expected profit for the year, work out the income tax plus self-employment tax on it, and divide by four. Or use the safe-harbor shortcut based on last year\'s tax bill.',
      'html' => <<<'HTML'
<p>There are two honest ways to land on a number. Most people use one for their first year and switch to the other once they have a full return behind them.</p>
<p><strong>Method 1: build it up from this year's profit.</strong> Start with what you expect to keep, not what you expect to bill. That's revenue minus your business expenses, which is your taxable profit. Say a freelancer expects $60,000 in revenue and about $10,000 in expenses (software, a laptop, mileage, home office costs). That's $50,000 in profit. On that, they'd owe self-employment tax of roughly 15.3% (about $7,000 after the small adjustment the IRS allows) plus federal income tax based on their bracket, plus any state income tax. Add it up, and divide by four. That's the per-period payment.</p>
<p><strong>Method 2: use last year's tax as your guide.</strong> If you filed last year, take the total tax you owed on that return and pay that same amount across the four periods this year. This is the basis of the safe harbor rule in the next section, and it's the easier path because it's a known number instead of a forecast.</p>
<p>A rough shortcut many self-employed people use is to set aside 25% to 30% of every payment that comes in, park it in a separate savings account, and pay the estimated tax out of that pot. It won't be exact, but it keeps you from spending money that was never really yours. We go deeper on that habit in <a href="/how-much-to-set-aside-for-taxes-self-employed/">how much to set aside for taxes when you're self-employed</a>.</p>
{{illustration:tax-jar}}
<p>The accuracy of any of this depends entirely on knowing your real profit, which means your expenses have to be current. If you've forgotten $4,000 of deductible expenses, your estimate will be too high and you'll hand the government an interest-free loan all year. Keep your books up to date and the estimate takes care of itself.</p>
HTML,
    ],

    [
      'h2' => 'The safe harbor rule and avoiding the penalty',
      'anchor' => 'safe-harbor',
      'step_name' => 'Hit a safe-harbor target to avoid the penalty',
      'step_text' => 'Pay at least 90% of this year\'s total tax, or 100% of last year\'s tax (110% if your income is high), and the IRS won\'t charge an underpayment penalty even if you still owe a little at filing.',
      'html' => <<<'HTML'
<p>Here's the part that takes the fear out of estimating: you don't have to be exactly right. You only have to clear one of two lines, called safe harbors. Hit either one and the IRS won't charge an underpayment penalty, no matter what your final number turns out to be.</p>
<ul>
<li><strong>Pay 90% of this year's tax.</strong> If your estimated payments add up to at least 90% of what you actually end up owing for the year, you're safe.</li>
<li><strong>Pay 100% of last year's tax.</strong> If you simply pay what you owed last year, split across the four periods, you're safe even if you earn a lot more this year. The threshold rises to 110% if your income last year was above a certain level (around $150,000 of adjusted gross income), so higher earners aim for 110%.</li>
</ul>
<p>The second one is the quiet superpower of the whole system. Say you owed $8,000 last year and your business booms this year to where you'll owe $14,000. As long as you pay that old $8,000 across the four periods (or $8,800 if you're a higher earner), you owe no penalty. You'll still have to pay the remaining balance when you file in April, but there's no penalty on it, and you got to hold that money and earn interest on it in the meantime.</p>
<p>The penalty itself, if you miss both harbors, isn't a scary flat fine. It's interest, charged per period on the shortfall, at a rate the IRS resets each quarter. Being a bit short for one period costs a few dollars of interest, not a catastrophe. But hitting a safe harbor means you never think about it at all. State rules vary, and some states have their own safe-harbor percentages, so check with an accountant for your situation if you also owe state estimated tax.</p>
HTML,
    ],

    [
      'h2' => 'How to actually pay it online',
      'anchor' => 'how-to-pay',
      'step_name' => 'Make the payment online',
      'step_text' => 'Pay directly on the IRS website using IRS Direct Pay or an EFTPS account, choose "estimated tax" and the right year, and keep the confirmation. Paper Form 1040-ES vouchers are the mail-in alternative.',
      'html' => <<<'HTML'
<p>The paperwork name for this is <strong>Form 1040-ES</strong>. The form comes with a worksheet to calculate your payment and four paper vouchers you can mail with a check. Almost nobody mails checks anymore, though. Paying online is faster, free, and gives you an instant receipt. Two ways to do it:</p>
<ul>
<li><strong>IRS Direct Pay.</strong> The simplest option for most people. Go to the IRS website, choose Direct Pay, select "Estimated Tax" as the reason and "1040-ES" as the form, pick the correct tax year, and pay straight from your bank account with no fee. No account signup needed.</li>
<li><strong>EFTPS (Electronic Federal Tax Payment System).</strong> A free government system you enroll in once. It's a little more setup up front, but it keeps a full history of every payment you've made, which is handy if you pay estimated tax every quarter for years.</li>
</ul>
<p>Whichever you use, the two things that matter most are picking <em>estimated tax</em> as the payment type and picking the <em>right year</em>. Applying a payment to the wrong year is the most common mixup, and it creates a headache to sort out later. After you pay, save the confirmation number or screenshot. That's your proof the payment was made on time.</p>
<p>Don't forget your state. Most US states with an income tax run their own separate estimated-tax system with its own website and its own vouchers. A payment to the IRS does nothing for your state bill, so if your state taxes income, budget for both.</p>
HTML,
    ],

    [
      'h2' => 'Your first year, with no prior return',
      'anchor' => 'first-year',
      'step_name' => 'Handle your first self-employed year',
      'step_text' => 'With no prior-year tax to copy, estimate from your expected profit and lean toward setting aside a little extra. You can also skip early payments and catch up once you see real income, as long as you cover the year in total.',
      'html' => <<<'HTML'
<p>The safe-harbor shortcut of "just pay last year's tax" only works if you have a last year. In your first year of self-employment, or your first year with a return at all, you don't. So you fall back to estimating from your expected profit, using Method 1 above.</p>
<p>A few things make the first year easier:</p>
<ul>
<li><strong>Set aside a little extra.</strong> Since your estimate is a genuine guess, err on the high side. Putting away 30% instead of 25% cushions you against a surprise, and any overpayment comes back as a refund. Overshooting is annoying; undershooting can cost a penalty.</li>
<li><strong>You can start when the income does.</strong> If you launch mid-year, you don't owe estimated payments for periods before you had any income. Begin with the period in which you actually started earning.</li>
<li><strong>Recalculate as you go.</strong> You're not locked into your January guess. If Q1 and Q2 come in stronger than expected, bump up your Q3 and Q4 payments to match. The payments are checkpoints, not a fixed contract.</li>
<li><strong>Next year gets much easier.</strong> Once you've filed one full return, you have a real number to base the safe harbor on, and the guessing mostly stops.</li>
</ul>
<p>If your first-year profit is small enough that you'll owe under $1,000 for the year, you may not need to make estimated payments at all, and can just settle up when you file. When you're genuinely unsure, a one-hour session with an accountant in your first year is money well spent.</p>
HTML,
    ],

    [
      'h2' => 'If you\'re in Canada, the UK, or Australia',
      'anchor' => 'outside-the-us',
      'html' => <<<'HTML'
<p>The idea of paying tax in advance instead of one lump sum exists in most countries, just under different names and schedules. If you're outside the US, here's the short version so you know what to search for.</p>
<ul>
<li><strong>Canada: instalment payments.</strong> The CRA asks for tax instalments, usually four times a year (March, June, September, December), once your net tax owing passes a threshold (around $3,000, or $1,800 in Quebec) in the current year and one of the two prior years. The CRA even mails you instalment reminders with suggested amounts based on your past returns.</li>
<li><strong>UK: payments on account.</strong> Under Self Assessment, HMRC collects tax in advance through two "payments on account," due in January and July. Each is typically half of your previous year's tax bill, with a balancing payment the following January to square up the difference. It kicks in once your bill passes a set amount.</li>
<li><strong>Australia: PAYG instalments.</strong> The ATO puts self-employed people and investors into Pay As You Go instalments, usually reported and paid quarterly through your activity statement, once your business or investment income is above a threshold. The ATO generally works out an instalment amount for you based on your last return.</li>
</ul>
<p>The details, thresholds, and exact dates differ in every country and change over time, so treat the above as a map, not the final word. Confirm the current rules for where you live, and check with an accountant before you rely on any specific figure.</p>
HTML,
    ],

    [
      'h2' => 'How Argo Books keeps your estimate realistic',
      'anchor' => 'argo-books',
      'html' => <<<'HTML'
<p>Every part of this comes back to one number: your real taxable profit. If that number is accurate, your estimate is easy and honest. If it's stale, you're either overpaying and starving your own cash flow, or underpaying and risking a penalty. So the practical job is keeping your income and expenses current all year, not scrambling to rebuild them the week a payment is due.</p>
<p>That's the everyday work Argo Books is built for. You log income as invoices get paid and log expenses as they happen, snapping a photo of a receipt so the AI pulls the vendor, date, amount, and tax for you. Because your profit is Revenue minus Expenses (with sales tax kept separate as a liability, never counted as your profit), the profit figure stays right as long as your expenses stay current. When a quarter comes due, the number you need is already sitting there instead of buried in a shoebox.</p>
<p>To turn that profit into an actual quarterly figure, the free <a href="/self-employed-tax-calculator/">self-employed tax calculator</a> on this site estimates your income and self-employment tax for the US and Canada, so you can see the ballpark payment in a couple of minutes. The Report Builder in the app can also produce a tax summary and an income statement you can hand to an accountant at year-end.</p>
<p>One honest limit worth stating plainly: Argo Books shows you the numbers, it does not file or send the payment for you. There's no button that pays the IRS. What it does is keep your books accurate enough that the estimate you make, and the payment you send through Direct Pay, is based on real figures rather than a guess. For more on the wider habit, see <a href="/how-to-separate-business-and-personal-finances/">how to separate business and personal finances</a>, which makes every one of these numbers cleaner.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'The free self-employed tax calculator turns your expected profit into a quarterly estimate for the US and Canada in a couple of minutes.',
  'tool_callout_cta' => 'Open the tax calculator',
  'tool_callout_url' => '/self-employed-tax-calculator/',

  'faqs' => [
    [
      'q' => 'Who has to pay quarterly estimated taxes?',
      'a' => 'In the US, you generally owe estimated taxes if nobody withholds tax from your income and you expect to owe $1,000 or more when you file. That covers most freelancers, independent contractors, sole proprietors, single-member LLCs, and anyone with significant untaxed side income like rentals or investments. If your only income is a salaried job where tax is already taken out of your paycheck, you usually don\'t need to make estimated payments at all.',
    ],
    [
      'q' => 'What are the due dates?',
      'a' => 'US estimated taxes are due four times a year, roughly mid-April, mid-June, mid-September, and mid-January of the following year. The periods aren\'t evenly spaced: the June payment covers only April and May, while the September one covers three months. Dates shift by a day or two each year and roll forward when they land on a weekend or holiday, so confirm the exact dates on the IRS site and put all four on your calendar in January.',
    ],
    [
      'q' => 'How do I estimate what to pay?',
      'a' => 'Two common ways. First, take your expected profit for the year (revenue minus expenses), work out the income tax plus self-employment tax of about 15.3% on it, and divide by four. Second, and easier once you have a prior return, just pay what you owed last year split across the four periods. Many self-employed people also set aside 25% to 30% of every payment they receive into a separate account and pay the estimate out of that pot.',
    ],
    [
      'q' => 'What happens if I underpay?',
      'a' => 'The IRS charges an underpayment penalty, but it\'s really interest calculated per period on the amount you were short, at a rate the IRS resets each quarter. Being a little short for one period costs a few dollars, not a huge fine. You avoid it entirely by hitting a safe harbor: pay at least 90% of this year\'s tax, or 100% of last year\'s tax (110% if you\'re a higher earner). Clear either line and there\'s no penalty even if you still owe a balance at filing.',
    ],
    [
      'q' => 'It\'s my first year with no prior return, what do I pay?',
      'a' => 'With no prior-year tax to copy, estimate from your expected profit and lean toward setting aside a little extra, say 30% instead of 25%, since your guess has more uncertainty in it. You only owe estimated payments for periods after you actually started earning, so a mid-year start means you begin with that period. You can also raise later payments if income comes in stronger than expected. If you\'ll owe under $1,000 for the whole year, you may not need estimated payments and can settle up when you file.',
    ],
    [
      'q' => 'Does self-employment tax count on top of income tax?',
      'a' => 'Yes, and it catches a lot of first-timers. Self-employed people pay income tax plus self-employment tax, which is Social Security and Medicare at roughly 15.3% of net profit. An employee splits that second tax with their employer, but when you work for yourself you pay both halves. Your quarterly estimate needs to cover both, which is why setting aside 25% to 30% of income is a common rule of thumb rather than just your income-tax rate.',
    ],
    [
      'q' => 'Do quarterly payments cover my state taxes too?',
      'a' => 'No. A payment to the IRS only covers your federal tax. Most US states that have an income tax run a separate estimated-tax system with its own website, forms, and due dates, so you have to pay them separately. Some states set their own safe-harbor percentages that differ from the federal ones. If your state taxes income, budget for both federal and state, and check your state revenue site or an accountant for the specifics.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'usa',
    'canada',
  ],

  'related_article_slugs' => [
    'how-much-to-set-aside-for-taxes-self-employed',
    'small-business-tax-deductions',
    'how-to-separate-business-and-personal-finances',
    'how-to-track-sales-tax-for-small-business',
  ],
];
