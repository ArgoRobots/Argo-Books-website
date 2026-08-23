// markup-margin-calculator/scripts/main.js
// Wires the form to pricing() in shared/scripts/business-calcs.js.

import { pricing, num } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));
const out = (key) => document.querySelector(`[data-mm-results] [data-mm="${key}"]`);

const el = {
  currency: $('[data-mm="currency"]'),
  known: $('[data-mm="known"]'),
  cost: $('[data-mm="cost"]'),
  price: $('[data-mm="price"]'),
  markupPercent: $('[data-mm="markupPercent"]'),
  marginPercent: $('[data-mm="marginPercent"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
const pct = (n) => `${Number(n).toFixed(1).replace(/\.0$/, '')}%`;

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function render() {
  const mode = el.known.value;
  const r = pricing({
    known: mode,
    cost: el.cost.value,
    price: el.price.value,
    markupPercent: el.markupPercent.value,
    marginPercent: el.marginPercent.value,
  });

  // The headline shows whichever figure the mode solves for.
  if (mode === 'cost+price') {
    out('headlineLabel').textContent = 'Markup on cost';
    out('headline').textContent = r.cost > 0 ? pct(r.markupPercent) : '0%';
    out('headlineSub').textContent = r.price > 0 ? `which is a ${pct(r.marginPercent)} margin` : 'Enter a cost and a price';
  } else {
    out('headlineLabel').textContent = 'Selling price';
    out('headline').textContent = money(r.price);
    out('headlineSub').textContent = r.cost > 0
      ? `${money(r.profit)} profit on ${money(r.cost)} of cost`
      : 'Enter a cost to begin';
  }

  out('costOut').textContent = money(r.cost);
  out('priceOut').textContent = money(r.price);
  out('profit').textContent = money(r.profit);
  out('markupOut').textContent = r.cost > 0 ? pct(r.markupPercent) : '0%';
  out('marginOut').textContent = r.price > 0 ? pct(r.marginPercent) : '0%';

  paintWarning(mode, r);
}

// The whole point of the tool: name the gap between the two percentages.
function paintWarning(mode, r) {
  const box = document.querySelector('[data-mm-warn]');
  const show = r.cost > 0 && r.price > 0 && r.profit > 0;
  box.hidden = !show;
  if (!show) return;

  if (mode === 'cost+margin') {
    const wrong = r.cost * (1 + num(el.marginPercent.value) / 100);
    const lost = r.price - wrong;
    out('warnText').textContent =
      `Adding ${pct(num(el.marginPercent.value))} to your cost instead of solving for the margin `
      + `would price this at ${money(wrong)}, which is only a ${pct((wrong - r.cost) / wrong * 100)} margin. `
      + `That mistake costs ${money(lost)} on every unit.`;
  } else {
    out('warnText').textContent =
      `A ${pct(r.markupPercent)} markup is a ${pct(r.marginPercent)} margin. `
      + `If someone told you to hit a ${pct(r.markupPercent)} margin, you would need to price this at `
      + `${money(r.cost / (1 - Math.min(0.999, r.markupPercent / 100)))} instead.`;
  }
}

function syncMode() {
  const mode = el.known.value;
  $$('[data-mm-when]').forEach((f) => { f.hidden = f.getAttribute('data-mm-when') !== mode; });
  render();
}

function wirePresets() {
  $$('[data-mm-preset]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const [field, value] = btn.getAttribute('data-mm-preset').split(':');
      (field === 'markup' ? el.markupPercent : el.marginPercent).value = value;
      syncPresets();
      render();
    });
  });
  [el.markupPercent, el.marginPercent].forEach((i) => i.addEventListener('input', syncPresets));
}

function syncPresets() {
  $$('[data-mm-preset]').forEach((b) => {
    const [field, value] = b.getAttribute('data-mm-preset').split(':');
    const current = field === 'markup' ? el.markupPercent.value : el.marginPercent.value;
    b.classList.toggle('is-active', value === String(current));
  });
}

function init() {
  if (!el.cost) return;
  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }
  el.known.addEventListener('change', syncMode);
  const form = el.cost.closest('form') || document;
  form.addEventListener('input', render);
  wirePresets();
  syncPresets();
  syncMode();
}

init();
