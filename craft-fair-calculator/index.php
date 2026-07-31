<?php
// craft-fair-calculator/index.php
// Whether a market stall or craft fair actually paid. Tier 1 (see read-me/Tool
// page standards.md). Math in shared/scripts/business-calcs.js.

require_once __DIR__ . '/../shared/_base.php';
require_once __DIR__ . '/../shared/currencies.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/schema.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('craftfaircalc_tool');
}

$page_title = 'Craft Fair Calculator: Did Your Market Stall Actually Pay? | Argo Books';
$page_description = 'Free craft fair and market stall calculator. Work out how many sales cover your booth fee and travel, and whether the day paid you properly once your hours are counted.';
$canonical_url = 'https://argorobots.com/craft-fair-calculator/';

$tools_back = ['href' => INVGEN_BASE . '/tools/', 'label' => 'All tools'];
$ref_qs = '?source=craftfaircalc-tool&amp;utm_source=craft-fair-calculator&amp;utm_medium=tool&amp;utm_campaign=phase1';

$faqs = [
    [
        'q' => 'How do I know if a craft fair is worth doing?',
        'a' => 'Work out how many sales it takes to cover the booth fee and travel before the day, not after. Divide your total cash costs by what each average sale contributes after materials. If that number is more than you have ever sold at a similar event, the stall is unlikely to pay and you have found that out before handing over a deposit.',
    ],
    [
        'q' => 'What should I count as the cost of a market stall?',
        'a' => 'The booth fee, fuel or transport, parking, any table or gazebo hire, card reader fees, and the food you buy because you are out all day. Then your own hours, including setup, the whole trading day, packing down, and travel each way. Most people count the booth fee and stop, which makes every stall look better than it was.',
    ],
    [
        'q' => 'Should I count my own time at a craft fair?',
        'a' => 'Yes, in a separate line. A market day is often ten or twelve hours once travel and setup are counted. The calculator shows profit both ways: cash profit, which is what actually went in the tin, and profit after paying yourself, which tells you whether the day beat doing something else with your time.',
    ],
    [
        'q' => 'What is a good average sale at a craft fair?',
        'a' => 'It depends far more on your price points than on the event. What matters is your average transaction value, not your average product price, because bundles and multi-buys lift it. Tracking it across a few events is worth more than any benchmark, because it lets you predict whether a given booth fee is affordable.',
    ],
    [
        'q' => 'How can I make a market stall more profitable?',
        'a' => 'Raise the average sale rather than chasing more visitors. Bundles, a higher-priced anchor product, and an easy card payment all lift the number that break-even divides into. On the cost side, sharing a stall and a lift with another maker can halve your fixed costs for the day, and it is the single fastest way to make a marginal event work.',
    ],
    [
        'q' => 'Are craft fairs worth it if I barely break even?',
        'a' => 'Sometimes, but be honest about why. A stall that breaks even while producing repeat customers, a mailing list, and wholesale enquiries can be worth doing. A stall that breaks even and produces nothing else is an expensive day out. Decide which it is deliberately rather than by feel.',
    ],
    [
        'q' => 'Should I take card payments at a market?',
        'a' => 'Almost always. The fee on a card reader is a few percent; the cost of a customer walking away because you are cash only is the whole sale. Put the reader fees into the calculator as part of your material percentage or your other costs so the comparison is fair, and you will usually find they pay for themselves several times over.',
    ],
    [
        'q' => 'How do I track whether markets are working over a year?',
        'a' => 'Record each event as its own set of costs and takings, then compare them. Patterns show up quickly: certain organisers, certain months, and certain locations reliably outperform. Doing this from memory does not work, because a good conversation at a bad event distorts your recollection of how it went.',
    ],
];

$page_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'SoftwareApplication',
            'name' => 'Craft Fair Calculator',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
            'creator' => ['@id' => 'https://argorobots.com/#organization'],
            'url' => $canonical_url,
        ],
        argo_faq_schema_node($faqs),
    ],
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$breadcrumb_schema_json = argo_breadcrumb_schema([
    'Home' => '/',
    'Free Tools' => '/tools/',
    'Craft Fair Calculator' => $canonical_url,
]);

$extra_head = '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/calculator.css">'
    . '<script>window.ARGO_CURRENCY_LOCALES = ' . json_encode(argo_currency_locales(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
$extra_scripts = '<script type="module" src="' . INVGEN_BASE . '/craft-fair-calculator/scripts/main.js"></script>';

ob_start();
?>
<div class="calc-app">

  <section class="site-hero">
    <h1 class="site-hero-title">Craft Fair Calculator</h1>
    <p class="site-hero-tagline">How many sales it takes to cover the booth fee and the fuel, and whether the day actually paid you once your hours are counted.</p>
  </section>

  <aside class="page-banner" role="complementary">
    <span class="page-banner-text">Argo Books records each event's costs and takings, so you can see which markets are worth returning to.</span>
    <a class="page-banner-link" data-pitch-placement="banner" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=banner">See how <span aria-hidden="true">&rarr;</span></a>
  </aside>

  <div class="calc-grid">
    <form class="calc-form" autocomplete="off" aria-label="Craft fair inputs">

      <div class="craft-fieldset">
        <h2 class="craft-legend">What the day costs you</h2>

        <div class="calc-field">
          <label for="cf-booth">Booth or stall fee</label>
          <div class="calc-money">
            <span class="calc-money-affix">$</span>
            <input id="cf-booth" data-cf="boothFee" type="number" inputmode="decimal" min="0" step="1" placeholder="120">
          </div>
        </div>

        <div class="calc-field">
          <label for="cf-travel">Travel and parking</label>
          <div class="calc-money">
            <span class="calc-money-affix">$</span>
            <input id="cf-travel" data-cf="travel" type="number" inputmode="decimal" min="0" step="1" placeholder="45">
          </div>
          <p class="calc-hint">Fuel both ways, parking, and any tolls.</p>
        </div>

        <div class="calc-field">
          <label for="cf-other">Other costs on the day</label>
          <div class="calc-money">
            <span class="calc-money-affix">$</span>
            <input id="cf-other" data-cf="otherCosts" type="number" inputmode="decimal" min="0" step="1" placeholder="20">
          </div>
          <p class="calc-hint">Table or gazebo hire, display bits, lunch, a helper's pay.</p>
        </div>

        <div class="calc-field">
          <label for="cf-hours">Hours the day takes you</label>
          <input id="cf-hours" data-cf="hours" type="number" inputmode="decimal" min="0" step="0.5" value="9">
          <p class="calc-hint">Setup, trading, packing down, and travel each way. Usually more than the event's opening hours.</p>
        </div>

        <div class="calc-field">
          <label for="cf-rate">Your hourly rate</label>
          <div class="calc-money">
            <span class="calc-money-affix">$</span>
            <input id="cf-rate" data-cf="hourlyRate" type="number" inputmode="decimal" min="0" step="0.5" value="20">
          </div>
          <p class="calc-hint">Shown separately, so you can see cash profit and real profit side by side.</p>
        </div>
      </div>

      <div class="craft-fieldset">
        <h2 class="craft-legend">What you sell</h2>

        <div class="calc-field">
          <label for="cf-avg">Average sale</label>
          <div class="calc-money">
            <span class="calc-money-affix">$</span>
            <input id="cf-avg" data-cf="averageSale" type="number" inputmode="decimal" min="0" step="0.5" placeholder="28">
          </div>
          <p class="calc-hint">Your average transaction, not your average product price. Bundles lift this.</p>
        </div>

        <div class="calc-field">
          <label for="cf-material">Materials as a share of the sale</label>
          <div class="calc-money calc-money-suffix">
            <input id="cf-material" data-cf="materialPercent" type="number" inputmode="decimal" min="0" max="100" step="1" value="35">
            <span class="calc-money-affix calc-money-affix-right">%</span>
          </div>
          <p class="calc-hint">What the goods cost you to make, as a percentage of what they sell for. Include card fees here if you take payments.</p>
        </div>

        <div class="calc-field">
          <label for="cf-sales">Sales you made or expect</label>
          <input id="cf-sales" data-cf="salesMade" type="number" inputmode="numeric" min="0" step="1" placeholder="0">
          <p class="calc-hint">Leave blank to just see the break-even figures.</p>
        </div>

        <div class="calc-field">
          <label for="cf-currency">Currency</label>
          <select id="cf-currency" data-cf="currency"><?= argo_currency_options() ?></select>
        </div>
      </div>
    </form>

    <div class="calc-results" data-cf-results aria-live="polite">
      <div class="calc-headline">
        <span class="calc-headline-label">Sales to cover your costs</span>
        <span class="calc-headline-amount" data-cf="breakEven">0</span>
        <span class="calc-headline-sub" data-cf="breakEvenSub">Enter your costs and average sale</span>
      </div>

      <dl class="calc-breakdown">
        <div class="calc-group">The day costs you</div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Cash out before you sell anything</dt><dd data-cf="cashCosts">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Your time</dt><dd data-cf="timeCost">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-subtotal"><dt>Total cost of the day</dt><dd data-cf="totalCost">$0.00</dd></div>
        <div class="calc-group">Each sale</div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Contributes after materials</dt><dd data-cf="contribution">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Sales to also pay yourself</dt><dd data-cf="salesAll">0</dd></div>
      </dl>

      <div class="calc-callout" data-cf-actual hidden>
        <span class="calc-headline-label" data-cf="actualLabel">How the day went</span>
        <dl class="calc-breakdown">
          <div class="calc-breakdown-row"><dt>Takings</dt><dd data-cf="revenue">$0.00</dd></div>
          <div class="calc-breakdown-row calc-row-profit"><dt>Cash profit</dt><dd data-cf="profitCash">$0.00</dd></div>
          <div class="calc-breakdown-row"><dt>After paying yourself</dt><dd data-cf="profitTrue">$0.00</dd></div>
          <div class="calc-breakdown-row calc-row-rate"><dt>Worked out at</dt><dd data-cf="hourly">$0.00 an hour</dd></div>
        </dl>
        <p data-cf="verdict"></p>
      </div>
    </div>
  </div>

  <article class="calc-content">

    <section>
      <h2>Why stalls feel better than they were</h2>
      <p>A market day ends with cash in a tin, which feels like profit. It is not, and the gap is usually bigger than makers expect, because three costs get quietly dropped.</p>
      <p class="calc-formula">Sales needed = cash costs &divide; (average sale &minus; materials)</p>
      <p>The booth fee gets counted. The fuel usually does not. The eleven hours almost never do. A day that took $672 and felt like a triumph can be a <strong>$71</strong> day once everything is in, which is a fine result for a hobby and a poor one for a business.</p>
    </section>

    <section>
      <h2>The two break-even numbers</h2>
      <p>The calculator gives you both, because they answer different questions.</p>
      <ul>
        <li><strong>Sales to cover your cash.</strong> The point where you are no longer out of pocket on the booth fee, the fuel, and the lunch. This is the one to check before you book.</li>
        <li><strong>Sales to also pay yourself.</strong> The point where the day beat spending those hours on something else. This is the one that tells you whether markets are a good use of your time.</li>
      </ul>
      <p>Plenty of stalls clear the first and miss the second. That is not automatically a failure, but it should be a decision rather than a surprise.</p>
    </section>

    <section>
      <h2>A worked example</h2>
      <div class="calc-example">
        <p>A weekend market: booth <strong>$120</strong>, fuel and parking <strong>$45</strong>, table hire and lunch <strong>$20</strong>. Nine hours all in, valued at $20. Average sale <strong>$28</strong>, with materials at 35%.</p>
        <ul>
          <li>Cash out before opening: <strong>$185</strong>. Your time: <strong>$180</strong>. Total: <strong>$365</strong>.</li>
          <li>Each sale contributes <strong>$18.20</strong> after materials.</li>
          <li><strong>11 sales</strong> to get your cash back. <strong>21 sales</strong> to also pay yourself.</li>
        </ul>
        <p>Make 24 sales and you take <strong>$672</strong>, keep <strong>$251.80</strong> in cash profit, and after your own time are <strong>$71.80</strong> ahead. That is <strong>$27.98 an hour</strong> for the day, which is genuinely decent. Make 14 sales and the cash profit is $69.80, or <strong>$7.76 an hour</strong>, and the day cost you money against almost any alternative.</p>
      </div>
    </section>

    <section>
      <h2>The lever is the average sale, not the crowd</h2>
      <p>Makers instinctively blame footfall for a bad market. Footfall is not something you control, and the number that moves break-even fastest is the one you set yourself.</p>
      <p>Lifting the average sale from $28 to $35 drops break-even from 11 sales to 9, and turns a mediocre day into a decent one without a single extra customer. Three things do that reliably:</p>
      <ol class="calc-list">
        <li><strong>Bundles and multi-buys.</strong> Three for the price of two-and-a-half moves people up without discounting your whole table.</li>
        <li><strong>An anchor product.</strong> Something expensive on the stall makes the mid-range item look reasonable, even when the expensive one rarely sells.</li>
        <li><strong>Card payments.</strong> The fee is a few percent; a lost sale is a hundred. Put the fee into your materials percentage and it still wins comfortably.</li>
      </ol>
      <p>On the cost side, sharing a stall and the drive with another maker halves your fixed costs for the day. For a marginal event that is often the difference between worth doing and not.</p>
    </section>

    <section>
      <h2>Breaking even is not always failure</h2>
      <p>A stall that washes its face can still be worth doing if it produces something beyond the takings: repeat customers who then order online, a mailing list, a shop owner who asks about wholesale, or photographs of real people using your work.</p>
      <p>The trap is claiming those benefits without checking them. If markets are supposed to feed your online sales, look at whether online sales actually rise after one. If they do not, the day was a day out.</p>
    </section>

    <section>
      <h2>Where a calculator stops helping</h2>
      <p>This works out one event from figures you typed in. Over a season the useful question is which events are worth returning to, and that needs each one recorded rather than remembered, because a good conversation at a bad market distorts the memory of it.</p>
      <p><a class="calc-link" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=content">Argo Books</a> records each event's costs and takings so the pattern becomes obvious by the end of the year. It runs on your own computer and it is free to start.</p>
    </section>

  </article>

  <section class="calc-faqs">
    <h2>Frequently asked questions</h2>
    <?= argo_faq_grid($faqs) ?>
  </section>

</div>
<?php
$body_content = ob_get_clean();

include __DIR__ . '/../shared/layout.php';
