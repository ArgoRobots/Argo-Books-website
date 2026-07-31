<?php
// partials/craft-calculator.php
//
// Renders a batch-to-unit craft pricing calculator from a config array. The
// candle, soap, tumbler, and cake calculators are the same math (see
// shared/scripts/craft-engine.js) with different material rows and different
// wording, so they are configs rather than four copies of a tool.
//
// Config shape:
//   'unit'        => 'candle'            singular noun used throughout the UI
//   'unit_plural' => 'candles'
//   'materials'   => [['label' => 'Wax', 'hint' => '...', 'placeholder' => '0.00'], ...]
//   'yield'       => ['label' => 'Candles per batch', 'hint' => '...', 'default' => 12]
//   'time'        => ['label' => 'Time for the whole batch', 'hint' => '...', 'default' => 180]
//   'rate'        => ['default' => 20]
//   'overhead'    => ['label' => 'Packaging and selling costs', 'hint' => '...']
//   'channels'    => [['name' => 'Etsy', 'markup' => 150], ...]
//
// Markup presets drive both the quick-fill buttons and the comparison table, so
// the guidance a seller reads and the number they tap can never disagree.

require_once __DIR__ . '/../shared/_base.php';
require_once __DIR__ . '/../shared/currencies.php';

/** The calculator surface: inputs on the left, live results on the right. */
function craft_calculator_render(array $cfg): string
{
    $unit = $cfg['unit'] ?? 'item';
    $default_markup = $cfg['channels'][0]['markup'] ?? 150;

    ob_start();
    ?>
    <div class="calc-grid">
      <form class="calc-form" autocomplete="off" aria-label="<?= htmlspecialchars(ucfirst($unit)) ?> pricing inputs">

        <div class="craft-fieldset">
          <h2 class="craft-legend">What one batch costs you</h2>
          <?php foreach ($cfg['materials'] as $i => $m): ?>
            <div class="calc-field">
              <label for="cc-mat-<?= $i ?>"><?= htmlspecialchars($m['label']) ?></label>
              <div class="calc-money">
                <span class="calc-money-affix">$</span>
                <input id="cc-mat-<?= $i ?>" data-cc-material type="number" inputmode="decimal" min="0" step="0.01"
                       placeholder="<?= htmlspecialchars($m['placeholder'] ?? '0.00') ?>">
              </div>
              <?php if (!empty($m['hint'])): ?><p class="calc-hint"><?= htmlspecialchars($m['hint']) ?></p><?php endif; ?>
            </div>
          <?php endforeach; ?>

          <div class="calc-field">
            <label for="cc-yield"><?= htmlspecialchars($cfg['yield']['label']) ?></label>
            <input id="cc-yield" data-cc="batchYield" type="number" inputmode="numeric" min="1" step="1"
                   value="<?= (int)($cfg['yield']['default'] ?? 1) ?>">
            <p class="calc-hint"><?= htmlspecialchars($cfg['yield']['hint'] ?? '') ?></p>
          </div>
        </div>

        <div class="craft-fieldset">
          <h2 class="craft-legend">What your time is worth</h2>

          <div class="calc-field">
            <label for="cc-rate">Your hourly rate</label>
            <div class="calc-money">
              <span class="calc-money-affix">$</span>
              <input id="cc-rate" data-cc="hourlyRate" type="number" inputmode="decimal" min="0" step="0.5"
                     value="<?= htmlspecialchars((string)($cfg['rate']['default'] ?? 20)) ?>">
            </div>
            <p class="calc-hint">What you would want to earn per hour. Leave your time out and your profit is really unpaid wages.</p>
          </div>

          <div class="calc-field">
            <label for="cc-minutes"><?= htmlspecialchars($cfg['time']['label']) ?></label>
            <div class="calc-money calc-money-suffix">
              <input id="cc-minutes" data-cc="minutesPerBatch" type="number" inputmode="numeric" min="0" step="5"
                     value="<?= (int)($cfg['time']['default'] ?? 15) ?>">
              <span class="calc-money-affix calc-money-affix-right">min</span>
            </div>
            <p class="calc-hint"><?= htmlspecialchars($cfg['time']['hint'] ?? '') ?></p>
          </div>

          <div class="calc-field">
            <label for="cc-overhead"><?= htmlspecialchars($cfg['overhead']['label'] ?? 'Selling costs per ' . $unit) ?></label>
            <div class="calc-money">
              <span class="calc-money-affix">$</span>
              <input id="cc-overhead" data-cc="overheadPerUnit" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0.00">
            </div>
            <p class="calc-hint"><?= htmlspecialchars($cfg['overhead']['hint'] ?? 'Listing fees, stall fees, card fees, anything you pay to make the sale happen.') ?></p>
          </div>
        </div>

        <div class="craft-fieldset">
          <h2 class="craft-legend">Your markup</h2>
          <div class="calc-field">
            <label for="cc-markup">Markup on cost</label>
            <div class="calc-money calc-money-suffix">
              <input id="cc-markup" data-cc="markupPercent" type="number" inputmode="decimal" min="0" step="5"
                     value="<?= (int)$default_markup ?>">
              <span class="calc-money-affix calc-money-affix-right">%</span>
            </div>
            <div class="calc-presets" role="group" aria-label="Markup presets by where you sell">
              <?php foreach ($cfg['channels'] as $c): ?>
                <button type="button" class="calc-preset-btn" data-cc-preset="<?= (int)$c['markup'] ?>">
                  <?= htmlspecialchars($c['name']) ?> <span class="calc-preset-pct"><?= (int)$c['markup'] ?>%</span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="calc-field">
            <label for="cc-currency">Currency</label>
            <select id="cc-currency" data-cc="currency"><?= argo_currency_options() ?></select>
          </div>
        </div>
      </form>

      <div class="calc-results" data-cc-results aria-live="polite">
        <div class="calc-headline">
          <span class="calc-headline-label">Charge per <?= htmlspecialchars($unit) ?></span>
          <span class="calc-headline-amount" data-cc="price">$0.00</span>
          <span class="calc-headline-sub" data-cc="priceSub">Enter your batch costs to see a price</span>
        </div>

        <dl class="calc-breakdown">
          <div class="calc-group">What one <?= htmlspecialchars($unit) ?> costs you</div>
          <div class="calc-breakdown-row calc-row-cost">
            <dt>Materials</dt><dd data-cc="materialsPerUnit">$0.00</dd>
          </div>
          <div class="calc-breakdown-row calc-row-cost">
            <dt>Your time <span class="calc-band-rate" data-cc="minutesPerUnit"></span></dt>
            <dd data-cc="labourPerUnit">$0.00</dd>
          </div>
          <div class="calc-breakdown-row calc-row-cost" data-cc-row="overhead" hidden>
            <dt>Selling costs</dt><dd data-cc="overheadOut">$0.00</dd>
          </div>
          <div class="calc-breakdown-row calc-row-subtotal">
            <dt>Total cost</dt><dd data-cc="unitCost">$0.00</dd>
          </div>
          <div class="calc-breakdown-row calc-row-profit">
            <dt>Your profit</dt><dd data-cc="profit">$0.00</dd>
          </div>
          <div class="calc-breakdown-row calc-row-rate">
            <dt>Profit margin</dt><dd data-cc="margin">0%</dd>
          </div>
        </dl>

        <div class="craft-batch" data-cc-batch hidden>
          <h3 class="calc-subtitle" data-cc="batchTitle">Per batch</h3>
          <dl class="calc-breakdown">
            <div class="calc-breakdown-row"><dt>Batch cost</dt><dd data-cc="batchCost">$0.00</dd></div>
            <div class="calc-breakdown-row"><dt>Batch sells for</dt><dd data-cc="batchRevenue">$0.00</dd></div>
            <div class="calc-breakdown-row calc-row-profit"><dt>Batch profit</dt><dd data-cc="batchProfit">$0.00</dd></div>
          </dl>
        </div>

        <div class="craft-channel-table">
          <h3 class="calc-subtitle">Price by where you sell</h3>
          <table class="calc-table">
            <thead><tr><th scope="col">Channel</th><th scope="col">Markup</th><th scope="col">Price</th><th scope="col">Margin</th></tr></thead>
            <tbody>
              <?php foreach ($cfg['channels'] as $i => $c): ?>
                <tr data-cc-channel="<?= (int)$c['markup'] ?>">
                  <td><?= htmlspecialchars($c['name']) ?></td>
                  <td><?= (int)$c['markup'] ?>%</td>
                  <td data-cc-channel-price>$0.00</td>
                  <td data-cc-channel-margin>0%</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if (!empty($cfg['channel_note'])): ?>
            <p class="calc-hint"><?= htmlspecialchars($cfg['channel_note']) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
}

/** The <head> assets every craft calculator needs. */
function craft_calculator_head(): string
{
    return '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/calculator.css">'
        . '<link rel="stylesheet" href="' . INVGEN_BASE . '/shared/styles/craft-calculator.css">'
        . '<script>window.ARGO_CURRENCY_LOCALES = '
        . json_encode(argo_currency_locales(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
        . ';</script>';
}

/** The module that wires the surface above. */
function craft_calculator_scripts(): string
{
    return '<script type="module" src="' . INVGEN_BASE . '/shared/scripts/craft-calculator.js"></script>';
}
