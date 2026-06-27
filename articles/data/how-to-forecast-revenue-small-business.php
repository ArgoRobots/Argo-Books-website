<?php
// articles/data/how-to-forecast-revenue-small-business.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-to-forecast-revenue-small-business',

  'h1' => 'How to forecast your revenue and predict next month\'s sales',

  'meta_title' => 'How to Forecast Revenue for a Small Business | Argo Books',

  'meta_description' => 'How to forecast your small business revenue: three simple methods, how to use last year as a baseline, and how to tell if your forecast is any good.',

  'schema_type' => 'Article',

  'category' => 'bookkeeping',
  'hub_weight' => 36,

  'published' => '2026-06-27',

  'updated' => '2026-06-27',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<figure class="article-figure">
<img src="/resources/images/features/analytics-insights.svg" alt="Argo Books business insights showing revenue trends and opportunities">
</figure>
<p>Knowing roughly what next month's revenue will be is one of the most useful things you can do for a small business, because it changes real decisions. Whether you can afford to hire, how much stock to order, when to spend on marketing, whether this is the month to take a deposit on that bigger machine: all of those get easier when you have a number to plan against instead of a feeling.</p>
<p>The good news is that forecasting revenue does not need a data scientist or a finance degree. The basic methods are simple arithmetic you can do with the numbers you already have. This article walks through why gut-feel forecasts tend to be wrong, three plain ways to build a forecast yourself, how to use last year as your starting point, how to tell whether your forecast is any good, and where letting software handle it saves you the effort.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Why gut-feel forecasting fails',
      'anchor' => 'why-gut-fails',
      'html' => <<<'HTML'
<p>Most small business owners forecast in their head, and most of those forecasts are off. It is not because owners are bad at their business. It is because the human brain is wired in a few specific ways that work against an accurate guess.</p>
<ul>
<li><strong>Recency bias.</strong> You remember last week far more clearly than last spring. If the last two weeks were quiet, next month feels doomed. If they were busy, you feel unstoppable. A short stretch of good or bad luck colors the whole forecast, even though it tells you very little about a full month.</li>
<li><strong>Ignoring seasonality.</strong> Almost every business has a rhythm. A landscaper is slammed in summer and quiet in winter. A gift shop lives and dies by December. When you forecast from memory, it is easy to project this month forward as if next month will look the same, when the calendar says it will not.</li>
<li><strong>Optimism.</strong> Owners are optimists by nature, or they would not have started a business at all. That optimism is a strength most days, but it quietly inflates forecasts. The deals that are "basically closed" slip. The busy month you expected lands average. Hope leaks into the number.</li>
</ul>
<p>None of this means your instinct is worthless. It means your instinct needs a check. A simple forecast built from your actual past numbers gives you that check, and it is usually closer than the figure in your head.</p>
HTML,
    ],

    [
      'h2' => 'Three simple ways to forecast revenue',
      'anchor' => 'three-ways',
      'html' => <<<'HTML'
<p>You do not need one perfect method. You need a method that fits the shape of your business. Here are three, from simplest to most useful, each with a plain example.</p>
<p><strong>1. The recent average.</strong> Take the last three months of revenue, add them up, and divide by three. That average is your forecast for next month. If you brought in $8,000, $9,000, and $10,000 over the last three months, the total is $27,000, so your forecast for next month is $9,000. This is the fastest method and a fine starting point for a steady business with no strong seasonal pattern. Its weakness is that it cannot see a trend or a season coming.</p>
<p><strong>2. A trend line.</strong> If your revenue is climbing or falling month over month, an average hides that. Instead, look at the direction. Say the last four months were $7,000, $7,500, $8,000, and $8,500. That is a steady climb of about $500 a month. Carry the trend forward and next month forecasts at roughly $9,000. The same works in reverse if revenue is sliding: better to plan for the decline than to average it away and get surprised.</p>
<p><strong>3. The seasonal method.</strong> This is the most accurate for businesses with a yearly rhythm, and it is still just arithmetic. Take the same month from last year as your baseline, then adjust for how much you have grown since. If last July you made $10,000, and your revenue this year is running about 20 percent higher than last year overall, then your forecast for this July is $10,000 plus 20 percent, which is $12,000. You are letting last year tell you the shape of the season and using your growth rate to scale it up.</p>
{{illustration:forecast}}
<p>For most small businesses, the seasonal method beats the other two, because it is the only one that knows next month might be your busy season or your quiet one. The catch is that it needs a year of history to work, which brings us to the baseline.</p>
HTML,
    ],

    [
      'h2' => 'Use last year as your baseline',
      'anchor' => 'baseline',
      'html' => <<<'HTML'
<p>Last year is the single most useful forecasting tool you own, because it already contains your seasons. You do not have to guess when your busy months are. They are sitting in your records. The job is to read them and adjust for how your business has changed since.</p>
<p>The method is the seasonal one above. For any month you want to forecast, find that same month last year, then scale it by your year-over-year growth. If you are up 15 percent on the year, every month's baseline gets multiplied by 1.15. If you are down 10 percent, multiply by 0.90. The shape of the season stays; the size moves with your growth. This is far more reliable than averaging recent months, which treats a quiet January and a busy December as if they should produce the same forecast.</p>
<p>One step that makes this much easier is knowing your seasonal pattern in the first place. If you have never sat down and looked at which months are reliably strong and which are reliably soft, it is worth doing once. Our guide on <a href="/how-to-spot-seasonal-trends-in-your-business/">how to spot seasonal trends in your business</a> walks through reading your own history for the pattern, and once you have it, the seasonal forecast above falls out almost for free.</p>
{{illustration:calendar-due}}
<p><strong>What if you have under a year of data?</strong> Then you cannot do the seasonal method yet, and that is fine. Use the recent average or the trend line instead, and treat the forecast as rougher than it will be once you have a full year behind you. A few honest tips for the early days:</p>
<ul>
<li>Lean on whatever industry knowledge you have. If everyone in your trade goes quiet in winter, plan for it even without your own data to prove it.</li>
<li>Forecast a range, not a single number, while your history is thin. "Somewhere between $6,000 and $9,000" is more honest than a precise figure built on three data points.</li>
<li>Keep recording every month cleanly. The forecast you cannot make today gets steadily more reliable with each month you log, and a full year unlocks the seasonal method for good.</li>
</ul>
<p>The first year of any business is the hardest to forecast for exactly this reason. It gets easier on its own. Every month you trade, your baseline grows.</p>
HTML,
    ],

    [
      'h2' => 'How to tell if your forecast is any good',
      'anchor' => 'accuracy',
      'html' => <<<'HTML'
<p>A forecast you never check is just a wish. The way you find out whether your method works is to compare what you predicted against what actually happened, month after month, and watch the gap.</p>
<p>This is simpler than it sounds. Each month, before it starts, write down your forecast. When the month ends, write down the real number next to it. Over time you build a short list of predicted versus actual, and that list tells you everything. If your forecasts are landing within 10 percent most months, your method is working and you can plan against it with some confidence. If they are routinely off by 30 or 40 percent, the method does not fit your business and you should try a different one, usually the seasonal method if you have the history for it.</p>
{{illustration:checklist}}
<p>This habit of testing a forecast against what really happened has a name in the trade: backtesting. All it means is checking your predictions against reality so you know how much to trust the next one. You can do it on paper. The point is that a forecast earns trust by being right, not by sounding confident, and the only way to know is to keep score.</p>
<p>One thing worth saying plainly: a forecast does not have to be exactly right to be useful. A number that is wrong but close still beats a guess. If you forecast $9,000 and bring in $9,400, you planned your spending, your stock, and your hiring around very nearly the right figure, and the small miss cost you nothing. The goal is not perfection. The goal is to be close enough, often enough, that your decisions get better. A forecast that is usually within range is doing its job.</p>
HTML,
    ],

    [
      'h2' => 'Letting software do it for you',
      'anchor' => 'software',
      'html' => <<<'HTML'
<p>The methods above work, and doing them by hand is a good way to understand your own numbers. But once you have done it a few times, you will probably want the work done for you, updated automatically every month, without you exporting figures into a spreadsheet. That is what forecasting software is for.</p>
<p>Most accounting tools offer some version of this, and they vary a lot. Xero produces a rule-based short forecast, usually 7, 30, 60, or 90 days out, built from your open invoices and bills. QuickBooks has an AI forecast that looks roughly thirteen weeks ahead, but it wants around 18 to 24 months of history to work well and is strongest on the pricier Enterprise tier. Beyond that, the more capable forecasting features in the market are usually paid add-ons, tools like Float, Cash Flow Frog, and Fathom that bolt onto your accounting software for an extra monthly fee.</p>
<p>Argo Books takes a different approach. Its <a href="/features/predictive-analytics/">predictive analytics</a> forecast your revenue automatically, alongside expenses, profit, and customer growth, with no setup to configure. Under the hood it uses real time-series methods, Holt-Winters triple exponential smoothing combined with Singular Spectrum Analysis, which is a technical way of saying it models the underlying level of your business, its trend, and its seasonal pattern separately, then projects them forward. It auto-detects whether your business runs on a yearly, semi-annual, or quarterly rhythm and forecasts accordingly, so you are not telling it when your seasons are. It reads them from your data.</p>
<p>Two things make it practical rather than a black box. First, every forecast comes with a confidence score from 0 to 100, built from how much data you have, how stable your numbers are, how strong the seasonal signal is, and how accurate the method has been when tested against your own past. A high score means plan against it; a low one means treat it as a rough guide. Second, it does the backtesting for you, checking its own predictions against what actually happened and tracking its accuracy over time, which has averaged around 88 percent. The results come back as plain-language insights and simple charts, not a wall of statistics.</p>
{{illustration:cashflow-cycle}}
<p>To be straight about it: the forecasting, trend detection, and confidence scoring are part of Argo Premium, at ${argo_premium_monthly} a month or ${argo_premium_yearly} a year, though basic real-time analytics are in the free tier. Worth knowing if privacy matters to you: all of this runs locally on your own computer, because Argo Books is a desktop app. Your financial data is used to build the forecast on your machine and does not get shipped off to a server to do it.</p>
<p>If you would rather plan next month from your actual numbers than from gut feel, you can <a href="/downloads/">download Argo Books</a> and try it. Run your forecast by hand a few times first if you like; it will help you read what the software gives you, and it will show you why the seasonal pattern matters so much.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 4,

  'tool_callout_text' => 'Argo Books forecasts your revenue automatically and shows a confidence score, so you can plan next month from data instead of gut feel.',
  'tool_callout_cta' => 'See predictive analytics',
  'tool_callout_url' => '/features/predictive-analytics/',

  'faqs' => [
    [
      'q' => 'How much data do I need to forecast revenue?',
      'a' => 'For a simple recent-average or trend forecast, three to six months is enough to get started, though the result will be rough. For the seasonal method, which is the most accurate for most businesses, you want a full year of history, because that is what lets the forecast know which months are your busy ones and which are quiet. More history makes the forecast steadily more reliable. Software that uses real time-series methods generally wants at least a year too, and gives a higher confidence score the more clean history you feed it.',
    ],
    [
      'q' => 'Can I forecast with under a year of history?',
      'a' => 'Yes, but with lower accuracy and a wider margin. Without a full year you cannot use the seasonal method, so fall back on the recent average or a trend line, and treat the result as a rough guide rather than a firm number. Forecast a range instead of a single figure while your history is thin, lean on what you know about your industry\'s usual seasons, and keep recording every month cleanly. The forecast you cannot make confidently today gets meaningfully better with each month you log, and a full year unlocks the more reliable seasonal approach.',
    ],
    [
      'q' => 'What counts as a good forecast accuracy?',
      'a' => 'For a small business, landing within about 10 percent of actual revenue most months is a genuinely good forecast, and enough to plan hiring, stock, and spending against with confidence. Within 20 percent is still useful. If you are routinely off by 30 or 40 percent, the method does not fit your business and you should switch approaches, usually to the seasonal method if you have a year of history. Remember that a forecast does not need to be exact to be worth having. A number that is wrong but close still beats a guess.',
    ],
    [
      'q' => 'Should I forecast manually or let software do it?',
      'a' => 'Both have a place. Doing it manually a few times is worth it, because it teaches you how your own numbers behave and why seasonality matters. Once you understand that, software saves you the repeated effort and updates the forecast automatically as new months come in. Good forecasting tools also use methods that are hard to do by hand, like modelling level, trend, and seasonal pattern separately, and they can track their own accuracy over time. A reasonable path is to forecast by hand at first to build intuition, then hand the ongoing work to software.',
    ],
    [
      'q' => 'Does seasonality really matter for revenue?',
      'a' => 'For most businesses, yes, and ignoring it is the single biggest reason forecasts go wrong. Almost every business has a rhythm: a landscaper peaks in summer, a gift shop in December, a tax preparer in spring. If you forecast next month by averaging recent months, you treat a quiet stretch and a busy stretch as if they should produce the same number, which guarantees a miss when the season turns. Using the same month from last year as your baseline, scaled by your growth, is the simplest way to fold seasonality into the forecast and is usually the most accurate method a small business can use by hand.',
    ],
  ],

  'related_niche_slugs' => [
    'consultant',
    'contractor',
    'freelance',
  ],

  'related_article_slugs' => [
    'how-to-forecast-cash-flow-small-business',
    'how-to-spot-seasonal-trends-in-your-business',
    'cash-flow-forecasting-software',
  ],
];
