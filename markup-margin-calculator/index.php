<?php
// markup-margin-calculator/index.php
// Converts between cost, price, markup, and margin. Tier 1 (see read-me/Tool
// page standards.md). Math in shared/scripts/business-calcs.js.

require_once __DIR__ . '/../shared/_base.php';
require_once __DIR__ . '/../shared/currencies.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/schema.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('markupcalc_tool');
}

$page_title = 'Markup vs Margin Calculator: Convert Between Them | Argo Books';
$page_description = 'Free markup and margin calculator. Enter any two of cost, price, markup, or margin and get the rest, with a conversion table showing why the two are not the same number.';
$canonical_url = 'https://argorobots.com/markup-margin-calculator/';

$tools_back = ['href' => INVGEN_BASE . '/tools/', 'label' => 'All tools'];
$ref_qs = '?source=markupcalc-tool&amp;utm_source=markup-margin-calculator&amp;utm_medium=tool&amp;utm_campaign=phase1';

// Conversion table, computed rather than typed so it cannot drift from the math.
$conversions = [10, 20, 25, 30, 40, 50, 60, 66.67, 75];

$faqs = [
    [
        'q' => 'What is the difference between markup and margin?',
        'a' => 'Markup is measured against your cost; margin is measured against your selling price. Buy for $50 and sell for $100 and you have a 100% markup but a 50% margin, from exactly the same transaction. The confusion is expensive: someone told to hit a 40% margin who adds 40% to cost actually achieves a 28.6% margin and quietly loses a chunk of every sale.',
    ],
    [
        'q' => 'How do I convert margin to markup?',
        'a' => 'Divide the margin by one minus the margin. A 50% margin is 0.5 / 0.5 = 100% markup. A 60% margin is 0.6 / 0.4 = 150% markup. Markup is always the larger number, and the gap widens as margins rise.',
    ],
    [
        'q' => 'How do I convert markup to margin?',
        'a' => 'Divide the markup by one plus the markup. A 100% markup is 1 / 2 = 50% margin. A 25% markup is 0.25 / 1.25 = 20% margin. Margin can never reach 100%, no matter how large the markup gets.',
    ],
    [
        'q' => 'Which should I actually use?',
        'a' => 'Use markup when setting a price from a known cost, because it is the calculation you perform. Use margin when judging profitability, because it tells you what share of your revenue you keep and is comparable across products with different costs. Most businesses need both, which is why mixing them up is so common.',
    ],
    [
        'q' => 'Why can margin never be 100%?',
        'a' => 'Because margin is profit as a share of the price, and profit can never exceed the price unless your cost is zero. As markup rises, margin approaches 100% but never gets there: a 400% markup is an 80% margin, a 900% markup is 90%. Anyone quoting a margin above 100% has confused the two terms.',
    ],
    [
        'q' => 'What is keystone pricing?',
        'a' => 'Doubling the wholesale cost to set the retail price, a long-standing retail convention. That is a 100% markup and a 50% margin. It exists because the retail margin has to cover rent, staff, shrinkage, and unsold stock, not because doubling is inherently fair.',
    ],
    [
        'q' => 'What is a good margin for a small business?',
        'a' => 'It depends entirely on the trade. Grocery retail survives on single digits because of volume; handmade goods and consultancy commonly run at 50% to 70% because volume is low and the labour is yours. The useful comparison is against others in your own field, and against your own figure last year.',
    ],
    [
        'q' => 'Does the margin here account for my overheads?',
        'a' => 'No. This is gross margin: price minus the direct cost of the thing you sold. Rent, insurance, software, and your own wages come out of that. A healthy gross margin can still leave a business losing money overall, which is why gross margin is a pricing tool rather than a verdict on the business.',
    ],
];

$page_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'SoftwareApplication',
            'name' => 'Markup and Margin Calculator',
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
    'Markup vs Margin Calculator' => $canonical_url,
]);

$extra_head = '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/calculator.css">'
    . '<script>window.ARGO_CURRENCY_LOCALES = ' . json_encode(argo_currency_locales(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
$extra_scripts = '<script type="module" src="' . INVGEN_BASE . '/markup-margin-calculator/scripts/main.js"></script>';

ob_start();
?>
<div class="calc-app">

  <section class="site-hero">
    <h1 class="site-hero-title">Markup vs Margin Calculator</h1>
    <p class="site-hero-tagline">Enter what you know and get the rest. Markup and margin are different numbers from the same sale, and mixing them up costs money on every transaction.</p>
  </section>

  <aside class="page-banner" role="complementary">
    <span class="page-banner-text">Argo Books shows your real margin per product from your actual sales, not an assumed one.</span>
    <a class="page-banner-link" data-pitch-placement="banner" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=banner">See how <span aria-hidden="true">&rarr;</span></a>
  </aside>

  <div class="calc-grid">
    <form class="calc-form" autocomplete="off" aria-label="Markup and margin inputs">

      <div class="calc-field">
        <label for="mm-known">What do you know?</label>
        <select id="mm-known" data-mm="known">
          <option value="cost+markup">Cost and markup</option>
          <option value="cost+margin">Cost and target margin</option>
          <option value="cost+price">Cost and price</option>
        </select>
      </div>

      <div class="calc-field">
        <label for="mm-cost">Your cost</label>
        <div class="calc-money">
          <span class="calc-money-affix">$</span>
          <input id="mm-cost" data-mm="cost" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0.00">
        </div>
        <p class="calc-hint">What the item costs you: materials, wholesale price, or direct production cost.</p>
      </div>

      <div class="calc-field" data-mm-when="cost+markup">
        <label for="mm-markup">Markup</label>
        <div class="calc-money calc-money-suffix">
          <input id="mm-markup" data-mm="markupPercent" type="number" inputmode="decimal" min="0" step="5" value="100">
          <span class="calc-money-affix calc-money-affix-right">%</span>
        </div>
        <div class="calc-presets" role="group" aria-label="Common markups">
          <button type="button" class="calc-preset-btn" data-mm-preset="markup:50">50%</button>
          <button type="button" class="calc-preset-btn" data-mm-preset="markup:100">Keystone <span class="calc-preset-pct">100%</span></button>
          <button type="button" class="calc-preset-btn" data-mm-preset="markup:150">150%</button>
          <button type="button" class="calc-preset-btn" data-mm-preset="markup:200">200%</button>
        </div>
      </div>

      <div class="calc-field" data-mm-when="cost+margin" hidden>
        <label for="mm-margin">Target margin</label>
        <div class="calc-money calc-money-suffix">
          <input id="mm-margin" data-mm="marginPercent" type="number" inputmode="decimal" min="0" max="99.9" step="1" value="50">
          <span class="calc-money-affix calc-money-affix-right">%</span>
        </div>
        <div class="calc-presets" role="group" aria-label="Common margins">
          <button type="button" class="calc-preset-btn" data-mm-preset="margin:30">30%</button>
          <button type="button" class="calc-preset-btn" data-mm-preset="margin:50">50%</button>
          <button type="button" class="calc-preset-btn" data-mm-preset="margin:60">60%</button>
          <button type="button" class="calc-preset-btn" data-mm-preset="margin:70">70%</button>
        </div>
        <p class="calc-hint">Margin can never reach 100%, so the box stops just short of it.</p>
      </div>

      <div class="calc-field" data-mm-when="cost+price" hidden>
        <label for="mm-price">Selling price</label>
        <div class="calc-money">
          <span class="calc-money-affix">$</span>
          <input id="mm-price" data-mm="price" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0.00">
        </div>
      </div>

      <div class="calc-field">
        <label for="mm-currency">Currency</label>
        <select id="mm-currency" data-mm="currency"><?= argo_currency_options() ?></select>
      </div>
    </form>

    <div class="calc-results" data-mm-results aria-live="polite">
      <div class="calc-headline">
        <span class="calc-headline-label" data-mm="headlineLabel">Selling price</span>
        <span class="calc-headline-amount" data-mm="headline">$0.00</span>
        <span class="calc-headline-sub" data-mm="headlineSub">Enter a cost to begin</span>
      </div>

      <dl class="calc-breakdown">
        <div class="calc-breakdown-row"><dt>Cost</dt><dd data-mm="costOut">$0.00</dd></div>
        <div class="calc-breakdown-row"><dt>Price</dt><dd data-mm="priceOut">$0.00</dd></div>
        <div class="calc-breakdown-row calc-row-profit"><dt>Profit per unit</dt><dd data-mm="profit">$0.00</dd></div>
        <div class="calc-group">The two percentages</div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Markup on cost</dt><dd data-mm="markupOut">0%</dd></div>
        <div class="calc-breakdown-row calc-row-cost"><dt>Margin on price</dt><dd data-mm="marginOut">0%</dd></div>
      </dl>

      <div class="calc-callout" data-mm-warn hidden>
        <span class="calc-headline-label">Worth noticing</span>
        <p data-mm="warnText"></p>
      </div>
    </div>
  </div>

  <article class="calc-content">

    <section>
      <h2>Markup and margin are not the same number</h2>
      <p>They describe the same sale from two different angles, and the difference is money.</p>
      <p class="calc-formula">Markup = profit &divide; cost&nbsp;&nbsp;&nbsp;&nbsp;Margin = profit &divide; price</p>
      <p>Buy something for <strong>$50</strong>, sell it for <strong>$100</strong>. Your profit is $50. Against your cost that is a <strong>100% markup</strong>. Against your price it is a <strong>50% margin</strong>. Same transaction, same $50, two very different-looking percentages.</p>
      <p>Markup is always the bigger number, and the gap grows as you go up. That is why the confusion is so costly: someone aiming for a 40% margin who adds 40% to their cost lands on a 28.6% margin instead, and gives away nearly a third of the profit they thought they were making.</p>
    </section>

    <section>
      <h2>Conversion table</h2>
      <p>The same relationship in both directions. Find your target margin on the left and the markup you actually have to apply is on the right.</p>
      <div class="calc-table-wrap">
        <table class="calc-table">
          <thead><tr><th scope="col">If you want this margin</th><th scope="col">Apply this markup</th><th scope="col">Sell $100 of cost at</th></tr></thead>
          <tbody>
            <?php foreach ($conversions as $margin):
                $m = $margin / 100;
                $markup = ($m / (1 - $m)) * 100;
            ?>
              <tr>
                <td><?= rtrim(rtrim(number_format($margin, 2), '0'), '.') ?>%</td>
                <td><?= rtrim(rtrim(number_format($markup, 1), '0'), '.') ?>%</td>
                <td>$<?= number_format(100 * (1 + $markup / 100), 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="calc-hint">Notice how quickly markup runs away at the top. A 75% margin needs a 300% markup, which is why very high margins are rare outside software and services.</p>
    </section>

    <section>
      <h2>Which one should you use?</h2>
      <ul>
        <li><strong>Markup for setting prices.</strong> You start from a known cost and add to it, so markup is the calculation you actually perform.</li>
        <li><strong>Margin for judging the business.</strong> It tells you what share of every dollar of revenue you keep, and it compares fairly across products whose costs are wildly different.</li>
      </ul>
      <p>Trouble arrives when advice given in one is applied in the other. "Aim for 50%" is meaningless without saying which. If you take one thing from this page, take the habit of asking.</p>
    </section>

    <section>
      <h2>This is gross margin, not profit</h2>
      <p>Everything here is price minus the direct cost of the thing you sold. Rent, insurance, software, equipment, and your own wages all come out of what is left.</p>
      <p>A business can run a healthy 60% gross margin and still lose money, if the overheads are bigger than the gross profit. Gross margin is a pricing instrument. Whether the business works is a separate question, and it needs the whole year's figures rather than one product.</p>
    </section>

    <section>
      <h2>Where a calculator stops helping</h2>
      <p>This works out one product at an assumed cost. It cannot tell you what your costs really were, which products actually carry the margin you think, or what is left after overheads.</p>
      <p><a class="calc-link" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=content">Argo Books</a> tracks your real costs and sales so the margin you see is measured rather than assumed. It runs on your own computer and it is free to start.</p>
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
