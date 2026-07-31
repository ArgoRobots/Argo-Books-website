<?php
// mileage-deduction-calculator/index.php
// Business mileage deduction calculator. Tier 2 (see read-me/Tool page
// standards.md): the rates are set by tax authorities, so only countries with
// verified rates are offered and the currency follows the country.
//
// Two things this handles that most mileage calculators do not: the US rate
// changed mid-year in 2026, and Canada and the UK are tiered. All rates come
// from shared/data/mileage-rates.php.

require_once __DIR__ . '/../shared/_base.php';
require_once __DIR__ . '/../partials/faq.php';
require_once __DIR__ . '/../partials/schema.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('mileagecalc_tool');
}

$data = require __DIR__ . '/../shared/data/mileage-rates.php';
$regions = $data['regions'];
$verified = date('F Y', strtotime($data['verified']));

$page_title = 'Mileage Deduction Calculator: US, Canada, UK, Australia | Argo Books';
$page_description = 'Free business mileage deduction calculator. Handles the 2026 US mid-year rate change and the tiered rates in Canada and the UK, so your claim uses the right rate for every mile.';
$canonical_url = 'https://argorobots.com/mileage-deduction-calculator/';

$tools_back = ['href' => INVGEN_BASE . '/tools/', 'label' => 'All tools'];
$ref_qs = '?source=mileagecalc-tool&amp;utm_source=mileage-deduction-calculator&amp;utm_medium=tool&amp;utm_campaign=phase1';

// Rates handed to the client, shaped for shared/scripts/business-calcs.js.
$client = [];
foreach ($regions as $code => $r) {
    $client[$code] = [
        'name' => $r['name'],
        'currency' => $r['currency'],
        'locale' => $r['locale'],
        'unit' => $r['unit'],
        'unitPlural' => $r['unit_plural'],
        'split' => (bool)$r['split'],
        'periods' => $r['periods'] ?? [],
        'tiers' => $r['tiers'] ?? [],
        'cap' => $r['cap'] ?? null,
        'note' => $r['note'] ?? '',
        'periodLabel' => $r['period_label'],
    ];
}

$faqs = [
    [
        'q' => 'What is the 2026 IRS mileage rate?',
        'a' => 'It changed part way through the year. Business miles driven from 1 January to 30 June 2026 are claimed at 72.5 cents a mile, and miles driven from 1 July to 31 December 2026 at 76 cents. A full-year claim therefore needs both figures, which is why this calculator asks you to split your log at the end of June.',
    ],
    [
        'q' => 'What is the CRA mileage rate for 2026?',
        'a' => 'For the provinces it is 73 cents a kilometre for the first 5,000 kilometres in the year and 67 cents for every kilometre after that. The territories are higher, at 77 cents then 71 cents. The lower rate applies only to the distance above the threshold, not retroactively to everything.',
    ],
    [
        'q' => 'What is the HMRC mileage rate?',
        'a' => 'For cars and vans it is 55 pence a mile for the first 10,000 business miles in the tax year and 25 pence a mile after that. The first-band rate rose from 45 pence on 6 April 2026, its first change since 2011, so travel before that date uses 45 pence.',
    ],
    [
        'q' => 'How much can I claim per kilometre in Australia?',
        'a' => 'The cents-per-kilometre method pays 88 cents a kilometre for the 2025-26 income year, capped at 5,000 business kilometres per car. Above that ceiling the method simply stops; you cannot claim the excess at a lower rate, you have to switch to the logbook method for the whole claim. The rate rises to 91 cents for 2026-27.',
    ],
    [
        'q' => 'Does the lower rate apply to all my miles once I cross the threshold?',
        'a' => 'No, and this is the most common mistake. The bands are cumulative. If you drive 12,000 kilometres in Canada, the first 5,000 are claimed at the higher rate and only the remaining 7,000 at the lower one. Applying the lower rate to everything would cost you roughly $300 on that example.',
    ],
    [
        'q' => 'What counts as a business mile?',
        'a' => 'Travel between work locations, to clients, to suppliers, to the bank, and to pick up materials. What does not count almost anywhere is ordinary commuting between your home and a regular place of work. If you work from home and your home is your business base, trips out to clients generally do count, which is worth checking for your situation.',
    ],
    [
        'q' => 'Do I need a mileage log?',
        'a' => 'Yes. Every one of these authorities expects a record showing the date, the destination, the business purpose, and the distance. A calculator gives you the number; it is the log that survives an audit. Record trips as they happen, because reconstructing a year from memory is both painful and unconvincing.',
    ],
    [
        'q' => 'Should I use the mileage rate or claim actual costs?',
        'a' => 'The flat rate is simpler and needs no receipts for fuel, servicing, or insurance, and it usually wins for cheaper, efficient, high-mileage cars. Claiming actual running costs can beat it for expensive vehicles or low business mileage, but it means keeping every receipt and apportioning by business use. Some countries also restrict switching between methods, so choose deliberately.',
    ],
];

$page_schema_json = json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'SoftwareApplication',
            'name' => 'Mileage Deduction Calculator',
            'applicationCategory' => 'FinanceApplication',
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
    'Mileage Deduction Calculator' => $canonical_url,
]);

$extra_head = '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/calculator.css">'
    . '<script>window.ARGO_MILEAGE = ' . json_encode($client, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script>';
$extra_scripts = '<script type="module" src="' . INVGEN_BASE . '/mileage-deduction-calculator/scripts/main.js"></script>';

ob_start();
?>
<div class="calc-app">

  <section class="site-hero">
    <h1 class="site-hero-title">Mileage Deduction Calculator</h1>
    <p class="site-hero-tagline">Work out what your business driving is worth at tax time, with the right rate for every mile. Handles the 2026 US mid-year change and the tiered rates in Canada and the UK.</p>
  </section>

  <aside class="page-banner" role="complementary">
    <span class="page-banner-text">Argo Books keeps your mileage log and your receipts in the same place, ready for tax time.</span>
    <a class="page-banner-link" data-pitch-placement="banner" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=banner">See how <span aria-hidden="true">&rarr;</span></a>
  </aside>

  <div class="calc-grid">
    <form class="calc-form" autocomplete="off" aria-label="Mileage inputs">
      <div class="calc-field">
        <label for="mc-region">Where you file</label>
        <select id="mc-region" data-mc="region">
          <?php foreach ($regions as $code => $r): ?>
            <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['authority']) ?>)</option>
          <?php endforeach; ?>
        </select>
        <p class="calc-hint" data-mc="regionNote"></p>
      </div>

      <div data-mc-inputs></div>

      <div class="calc-field">
        <label for="mc-taxrate">Your marginal tax rate (optional)</label>
        <div class="calc-money calc-money-suffix">
          <input id="mc-taxrate" data-mc="taxRate" type="number" inputmode="decimal" min="0" max="99" step="1" placeholder="0">
          <span class="calc-money-affix calc-money-affix-right">%</span>
        </div>
        <p class="calc-hint">A deduction lowers your taxable income, not your tax bill directly. Enter your rate to see what the claim is actually worth to you.</p>
      </div>
    </form>

    <div class="calc-results" data-mc-results aria-live="polite">
      <div class="calc-headline">
        <span class="calc-headline-label">Your deduction</span>
        <span class="calc-headline-amount" data-mc="deduction">$0.00</span>
        <span class="calc-headline-sub" data-mc="deductionSub">Enter your distance to see the claim</span>
      </div>

      <dl class="calc-breakdown" data-mc-bands></dl>

      <div class="calc-breakdown-row calc-row-rate">
        <dt>Effective rate</dt><dd data-mc="effectiveRate">0.00</dd>
      </div>

      <div class="calc-note" data-mc-excluded hidden>
        <p data-mc="excludedText"></p>
      </div>

      <div class="calc-callout" data-mc-worth hidden>
        <h3 class="calc-headline-label">What it saves you</h3>
        <div class="calc-breakdown-row calc-row-profit">
          <dt>Tax saved</dt><dd data-mc="taxSaved">$0.00</dd>
        </div>
        <p class="calc-hint">Your deduction times your marginal rate. The claim reduces the income you are taxed on; this is the cash it puts back.</p>
      </div>

      <p class="calc-verified">Rates checked <?= htmlspecialchars($verified) ?>.</p>
    </div>
  </div>

  <article class="calc-content">

    <section>
      <h2>Which rate applies to your miles</h2>
      <p>Mileage rates are rarely one flat number, and the two ways they vary catch people out in different ways.</p>
      <h3>Rates that change during the year</h3>
      <p>The IRS raised the 2026 business rate part way through the year, from <strong>72.5 cents</strong> to <strong>76 cents</strong> a mile on 1 July. Which rate applies depends on <em>when</em> you drove, not how far you have driven, so a full-year claim needs your log split at the end of June.</p>
      <h3>Rates that step down with distance</h3>
      <p>Canada and the UK pay a higher rate on the first slice of your annual distance and a lower one after. The bands are cumulative, and this is where most manual claims go wrong: crossing the threshold does not reprice everything you drove earlier.</p>
      <p class="calc-hint">A UK driver doing 14,000 business miles claims 10,000 at 55p and 4,000 at 25p, which is &pound;6,500. Applying 25p to the lot would give &pound;3,500 and quietly hand HMRC &pound;3,000 of your deduction.</p>
    </section>

    <section>
      <h2>Current rates</h2>
      <div class="calc-table-wrap">
        <table class="calc-table">
          <thead>
            <tr><th scope="col">Where you file</th><th scope="col">Period</th><th scope="col">Rate</th><th scope="col">Applies to</th></tr>
          </thead>
          <tbody>
            <?php foreach ($regions as $r): ?>
              <?php if ($r['split']): ?>
                <?php foreach ($r['periods'] as $p): ?>
                  <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['period_label']) ?></td>
                    <td><?= number_format($p['rate'] * 100, 1) ?>&cent; / <?= htmlspecialchars($r['unit']) ?></td>
                    <td><?= htmlspecialchars($p['label']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <?php foreach ($r['tiers'] as $i => $t): ?>
                  <?php if ($t['rate'] <= 0) continue; ?>
                  <tr>
                    <td><?= htmlspecialchars($r['name']) ?></td>
                    <td><?= htmlspecialchars($r['period_label']) ?></td>
                    <td><?= number_format($t['rate'] * 100, 0) ?>&cent; / <?= htmlspecialchars($r['unit']) ?></td>
                    <td><?php
                        if (!empty($r['cap'])) {
                            echo 'Up to ' . number_format($r['cap']) . ' ' . htmlspecialchars($r['unit_plural']) . ', then the method stops';
                        } elseif ($t['upTo']) {
                            echo 'First ' . number_format($t['upTo']) . ' ' . htmlspecialchars($r['unit_plural']);
                        } else {
                            echo 'Every ' . htmlspecialchars($r['unit']) . ' after that';
                        }
                    ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="calc-hint">Rates checked <?= htmlspecialchars($verified) ?>. Authorities change these, sometimes mid-year, so check yours before filing.</p>
    </section>

    <section>
      <h2>What a deduction is actually worth</h2>
      <p>A mileage deduction does not come off your tax bill. It comes off the income you are taxed on, so what it saves you depends on your marginal rate.</p>
      <p>Claim <strong>$7,460</strong> of mileage at a 24% marginal rate and you keep about <strong>$1,790</strong> more. That is real money, and it is also why the claim is worth doing properly rather than rounding to a guess.</p>
      <p>Enter your marginal rate above and the calculator shows both the deduction and what it puts back in your pocket.</p>
    </section>

    <section>
      <h2>What counts, and what does not</h2>
      <ul>
        <li><strong>Usually claimable:</strong> travel to clients and customers, trips between work sites, runs to suppliers or the wholesaler, the bank, the post office with your orders, and to a market or trade show.</li>
        <li><strong>Usually not:</strong> ordinary commuting between home and a regular workplace. This is the single biggest source of rejected claims.</li>
        <li><strong>Depends:</strong> if your home is genuinely your business base, trips out from it are often business travel. Worth confirming for your circumstances rather than assuming either way.</li>
        <li><strong>Mixed trips:</strong> claim the business portion only. A detour to collect stock on a personal journey is the extra distance, not the whole trip.</li>
      </ul>
    </section>

    <section>
      <h2>Keep the log, not just the number</h2>
      <p>Every one of these authorities wants a record, not a total. A defensible log has the date, where you went, why it was business, and the distance. Odometer readings at the start and end of the year help.</p>
      <p>The practical problem is that nobody wants to write this down, so it gets reconstructed in April from calendar entries and guesswork. That reconstruction is exactly what an auditor is trained to spot, and it is also how people end up under-claiming, because forgotten trips are lost trips.</p>
      <p>Record it as you go. Whatever you use, the habit matters more than the tool.</p>
    </section>

    <section>
      <h2>Flat rate or actual costs?</h2>
      <p>The flat rate is meant to cover everything: fuel, servicing, insurance, depreciation, tyres. You claim distance and keep no fuel receipts.</p>
      <p>Claiming actual running costs instead can win if you drive an expensive vehicle a short business distance, but it means keeping every receipt and apportioning by business use percentage. It is more work and more audit surface. Several countries also restrict switching methods once you have chosen for a vehicle, so decide deliberately rather than year to year.</p>
      <p>For most self-employed people driving an ordinary car a fair distance, the flat rate wins on both money and effort.</p>
    </section>

    <section>
      <h2>Where a calculator stops helping</h2>
      <p>This works out one claim. It cannot keep your log through the year, hold the receipts for the trips it does not cover, or tell you what your vehicle actually costs the business.</p>
      <p><a class="calc-link" href="<?= INVGEN_BASE ?>/features/expense-revenue-tracking/<?= $ref_qs ?>&amp;placement=content">Argo Books</a> keeps your mileage and your expenses together, categorised and ready when the tax return is due, rather than scattered across a notebook and a shoebox. It runs on your own computer and it is free to start.</p>
    </section>

  </article>

  <section class="calc-faqs">
    <h2>Frequently asked questions</h2>
    <?= argo_faq_grid($faqs) ?>
  </section>

  <p class="calc-disclaimer">General information, not tax advice. Rates and rules change and depend on your circumstances. Confirm with your tax authority or an accountant before filing.</p>

</div>
<?php
$body_content = ob_get_clean();

include __DIR__ . '/../shared/layout.php';
