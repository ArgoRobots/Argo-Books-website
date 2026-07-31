// shared/scripts/craft-calculator.js
//
// Wires the surface rendered by partials/craft-calculator.php to the pure math
// in craft-engine.js. One module drives every craft product calculator, because
// they differ only in their material rows and their wording.

import { computeCraft, priceAtMarkup, marginForMarkup, num } from './craft-engine.js';
import { currencyFormatter, applyCurrencyAffixes } from './currency-format.js';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));
// Scoped to the results panel. Output cells also carry their own keys, so an
// input can never shadow one even if this scoping were lost.
const out = (key) => document.querySelector(`[data-cc-results] [data-cc="${key}"]`);

const el = {
  currency: $('[data-cc="currency"]'),
  batchYield: $('[data-cc="batchYield"]'),
  hourlyRate: $('[data-cc="hourlyRate"]'),
  minutesPerBatch: $('[data-cc="minutesPerBatch"]'),
  overheadPerUnit: $('[data-cc="overheadPerUnit"]'),
  markupPercent: $('[data-cc="markupPercent"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
const pct = (rate, digits = 0) => `${(rate * 100).toFixed(digits)}%`;

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  // The percent and minute suffixes are not currency affixes.
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function readInputs() {
  return {
    materials: $$('[data-cc-material]').map((i) => i.value),
    batchYield: el.batchYield ? el.batchYield.value : 1,
    hourlyRate: el.hourlyRate ? el.hourlyRate.value : 0,
    minutesPerBatch: el.minutesPerBatch ? el.minutesPerBatch.value : 0,
    overheadPerUnit: el.overheadPerUnit ? el.overheadPerUnit.value : 0,
    markupPercent: el.markupPercent ? el.markupPercent.value : 0,
  };
}

function render() {
  const input = readInputs();
  const r = computeCraft(input);

  out('price').textContent = money(r.price);
  out('priceSub').textContent = r.unitCost > 0
    ? `${money(r.unitCost)} to make, ${money(r.profit)} profit`
    : 'Enter your batch costs to see a price';

  out('materialsPerUnit').textContent = money(r.materialsPerUnit);
  out('labourPerUnit').textContent = money(r.labourPerUnit);
  out('unitCost').textContent = money(r.unitCost);

  // Show the division so the per-unit time is visible rather than implied.
  const perUnitTime = out('minutesPerUnit');
  if (perUnitTime) {
    perUnitTime.textContent = r.minutesPerUnit > 0
      ? `${r.minutesPerUnit < 1 ? r.minutesPerUnit.toFixed(1) : Math.round(r.minutesPerUnit)} min each`
      : '';
  }
  out('profit').textContent = money(r.profit);
  out('margin').textContent = r.price > 0 ? pct(r.margin) : '0%';

  // Selling costs only earn a row when there are any.
  const ovRow = document.querySelector('[data-cc-row="overhead"]');
  if (ovRow) ovRow.hidden = r.overheadPerUnit <= 0;
  out('overheadOut').textContent = money(r.overheadPerUnit);

  // The batch block is the reason this calculator exists, so it appears as soon
  // as a batch makes more than one thing.
  const batch = document.querySelector('[data-cc-batch]');
  if (batch) {
    const show = r.batchYield > 1 && r.unitCost > 0;
    batch.hidden = !show;
    if (show) {
      out('batchTitle').textContent = `Per batch of ${Math.round(r.batchYield)}`;
      out('batchCost').textContent = money(r.batchCost);
      out('batchRevenue').textContent = money(r.batchRevenue);
      out('batchProfit').textContent = money(r.batchProfit);
    }
  }

  $$('[data-cc-channel]').forEach((row) => {
    const markup = num(row.getAttribute('data-cc-channel'));
    row.querySelector('[data-cc-channel-price]').textContent = money(priceAtMarkup(r.unitCost, markup));
    row.querySelector('[data-cc-channel-margin]').textContent = pct(marginForMarkup(markup));
    row.classList.toggle('is-current', markup === num(input.markupPercent));
  });
}

function wirePresets() {
  const buttons = $$('[data-cc-preset]');
  buttons.forEach((btn) => {
    btn.addEventListener('click', () => {
      el.markupPercent.value = btn.getAttribute('data-cc-preset');
      syncPresetHighlight();
      render();
    });
  });
  if (el.markupPercent) el.markupPercent.addEventListener('input', syncPresetHighlight);
}

function syncPresetHighlight() {
  const current = el.markupPercent ? el.markupPercent.value : '';
  $$('[data-cc-preset]').forEach((b) => {
    b.classList.toggle('is-active', b.getAttribute('data-cc-preset') === String(current));
  });
}

function init() {
  if (!el.markupPercent) return;

  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }

  const form = el.markupPercent.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);

  wirePresets();
  syncPresetHighlight();
  render();
}

init();
