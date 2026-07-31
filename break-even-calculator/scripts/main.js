// break-even-calculator/scripts/main.js
// Wires the form to breakEven() in shared/scripts/business-calcs.js.

import { breakEven, num } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const $ = (sel) => document.querySelector(sel);
const out = (key) => document.querySelector(`[data-be-results] [data-be="${key}"]`);

const el = {
  currency: $('[data-be="currency"]'),
  fixedCosts: $('[data-be="fixedCosts"]'),
  pricePerUnit: $('[data-be="pricePerUnit"]'),
  variableCostPerUnit: $('[data-be="variableCostPerUnit"]'),
  expectedUnits: $('[data-be="expectedUnits"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
const units = (n) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(n);
const pct = (n) => `${(n * 100).toFixed(0)}%`;

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function render() {
  const r = breakEven({
    fixedCosts: el.fixedCosts.value,
    pricePerUnit: el.pricePerUnit.value,
    variableCostPerUnit: el.variableCostPerUnit.value,
  });

  out('price').textContent = money(r.price);
  out('variable').textContent = money(r.variable);
  out('contribution').textContent = money(r.contribution);
  out('contributionMargin').textContent = r.price > 0 ? pct(r.contributionMargin) : '0%';

  const warn = document.querySelector('[data-be-warn]');

  if (!r.viable) {
    out('units').textContent = '—';
    out('unitsSub').textContent = r.price > 0
      ? 'No volume covers your costs at this price'
      : 'Enter your costs and price';
    out('revenue').textContent = money(0);

    // Saying "impossible" is more useful than showing an enormous number.
    const show = r.price > 0 && r.variable > 0;
    warn.hidden = !show;
    if (show) {
      out('warnText').textContent =
        `Each sale loses ${money(Math.abs(r.contribution))} before fixed costs are even considered, `
        + 'so selling more increases the loss. Raise the price or reduce what each unit costs to make. '
        + 'No amount of volume fixes a negative contribution.';
    }
    document.querySelector('[data-be-expected]').hidden = true;
    return;
  }

  warn.hidden = true;
  out('units').textContent = units(r.unitsRounded);
  out('unitsSub').textContent = `sales, each contributing ${money(r.contribution)}`;
  out('revenue').textContent = money(r.revenue);

  paintExpected(r);
}

function paintExpected(r) {
  const box = document.querySelector('[data-be-expected]');
  const expected = num(el.expectedUnits.value);
  const show = expected > 0;
  box.hidden = !show;
  if (!show) return;

  const outcome = r.profitAt(expected);
  const profitable = outcome >= 0;
  const safety = expected > 0 ? (expected - r.unitsRounded) / expected : 0;

  out('expectedLabel').textContent = `At ${units(expected)} sales`;
  out('outcomeLabel').textContent = profitable ? 'Profit' : 'Loss';
  out('outcome').textContent = money(Math.abs(outcome));
  out('safety').textContent = profitable ? pct(Math.max(0, safety)) : 'None';

  out('expectedText').textContent = profitable
    ? `Sales could fall ${pct(Math.max(0, safety))} before you stopped covering costs.`
    : `You are ${units(r.unitsRounded - expected)} sales short of break-even.`;
}

function init() {
  if (!el.fixedCosts) return;
  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }
  const form = el.fixedCosts.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);
  render();
}

init();
