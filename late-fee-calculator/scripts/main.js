// late-fee-calculator/scripts/main.js
// Wires the form to lateFee() in shared/scripts/business-calcs.js.

import { lateFee, num } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));
// Scoped to the results panel. Output cells also carry their own keys, so an
// input can never shadow one even if this scoping were lost.
const out = (key) => document.querySelector(`[data-lf-results] [data-lf="${key}"]`);

const el = {
  currency: $('[data-lf="currency"]'),
  amount: $('[data-lf="amount"]'),
  daysOverdue: $('[data-lf="daysOverdue"]'),
  annualRate: $('[data-lf="annualRate"]'),
  flatFee: $('[data-lf="flatFee"]'),
  compound: $('[data-lf="compound"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function render() {
  const r = lateFee({
    amount: el.amount.value,
    daysOverdue: el.daysOverdue.value,
    annualRate: el.annualRate.value,
    flatFee: el.flatFee.value,
    compound: el.compound.value,
  });

  out('totalDue').textContent = money(r.totalDue);
  out('totalSub').textContent = r.amount > 0
    ? `${money(r.amount)} invoice, ${r.days} ${r.days === 1 ? 'day' : 'days'} overdue`
    : 'Enter the invoice amount and how late it is';

  out('amountOut').textContent = money(r.amount);

  out('interest').textContent = money(r.interest);
  out('rateNote').textContent = num(el.annualRate.value) > 0
    ? `${num(el.annualRate.value)}% a year${el.compound.value === 'monthly' ? ', compounding' : ''}`
    : '';

  const flatRow = document.querySelector('[data-lf-row="flat"]');
  if (flatRow) flatRow.hidden = r.flatFee <= 0;
  out('flatFeeOut').textContent = money(r.flatFee);

  out('totalFees').textContent = money(r.totalFees);
  out('daily').textContent = `${money(r.dailyInterest)} a day`;

  // A percentage of the invoice reads more usefully than the raw fee.
  const ctx = document.querySelector('[data-lf-context]');
  const show = r.amount > 0 && r.totalFees > 0;
  ctx.hidden = !show;
  if (show) {
    const pct = (r.effectivePercent * 100).toFixed(1);
    out('contextText').textContent =
      `The charges come to ${pct}% of the invoice. At this rate another 30 days adds about `
      + `${money(r.dailyInterest * 30)}, and a full year of lateness would add `
      + `${money(r.dailyInterest * 365)}.`;
  }
}

function wirePresets() {
  $$('[data-lf-preset]').forEach((btn) => {
    btn.addEventListener('click', () => {
      el.annualRate.value = btn.getAttribute('data-lf-preset');
      syncPresets();
      render();
    });
  });
  el.annualRate.addEventListener('input', syncPresets);
}

function syncPresets() {
  $$('[data-lf-preset]').forEach((b) => {
    b.classList.toggle('is-active', b.getAttribute('data-lf-preset') === String(el.annualRate.value));
  });
}

function init() {
  if (!el.amount) return;
  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }
  const form = el.amount.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);
  wirePresets();
  syncPresets();
  render();
}

init();
