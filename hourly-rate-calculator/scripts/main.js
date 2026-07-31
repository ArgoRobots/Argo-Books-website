// hourly-rate-calculator/scripts/main.js
// Wires the form to hourlyRate() in shared/scripts/business-calcs.js.

import { hourlyRate } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));
const out = (key) => document.querySelector(`[data-hr-results] [data-hr="${key}"]`);

const el = {
  currency: $('[data-hr="currency"]'),
  targetIncome: $('[data-hr="targetIncome"]'),
  businessExpenses: $('[data-hr="businessExpenses"]'),
  taxPercent: $('[data-hr="taxPercent"]'),
  hoursPerWeek: $('[data-hr="hoursPerWeek"]'),
  weeksOff: $('[data-hr="weeksOff"]'),
  billablePercent: $('[data-hr="billablePercent"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
let moneyRound = money;
const hours = (n) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(Math.round(n));

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  moneyRound = fmt.moneyRound;
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function render() {
  const r = hourlyRate({
    targetIncome: el.targetIncome.value,
    businessExpenses: el.businessExpenses.value,
    taxPercent: el.taxPercent.value,
    hoursPerWeek: el.hoursPerWeek.value,
    weeksOff: el.weeksOff.value,
    billablePercent: el.billablePercent.value,
  });

  out('rate').textContent = money(r.rate);
  out('rateSub').textContent = r.rate > 0
    ? `per billable hour, across ${hours(r.billableHours)} billable hours`
    : 'per billable hour';
  out('dayRate').textContent = money(r.dayRate);

  out('takeHome').textContent = moneyRound(Math.max(0, Number(el.targetIncome.value) || 0));
  out('preTax').textContent = moneyRound(r.preTaxIncome);
  out('expenses').textContent = moneyRound(Math.max(0, Number(el.businessExpenses.value) || 0));
  out('revenue').textContent = moneyRound(r.revenueNeeded);

  out('weeks').textContent = String(r.workingWeeks);
  out('hoursWorked').textContent = hours(r.hoursWorked);
  out('billableHours').textContent = hours(r.billableHours);
  out('unbillable').textContent = hours(r.unbillableHours);

  // The whole reason the tool exists. Shown as a short comparison rather than a
  // paragraph: the earlier wording made the reader hold four figures at once and
  // measured the gap against a revenue number they never see.
  const gap = document.querySelector('[data-hr-gap]');
  const show = r.rate > 0 && r.naiveRate > 0;
  gap.hidden = !show;
  if (show) {
    const target = Math.max(0, Number(el.targetIncome.value) || 0);
    out('naiveRate').textContent = `${money(r.naiveRate)} an hour`;
    out('naiveTakeHome').textContent = moneyRound(r.naiveTakeHome);
    out('targetEcho').textContent = moneyRound(target);
    out('gapText').textContent =
      `The quick sum forgets two things: ${hours(r.unbillableHours)} of your `
      + `${hours(r.hoursWorked)} hours cannot be billed to anyone, and costs and tax `
      + `come out of what is left. That is why the rate above is higher.`;
  }
}

function wirePresets() {
  $$('[data-hr-preset]').forEach((btn) => {
    btn.addEventListener('click', () => {
      el.billablePercent.value = btn.getAttribute('data-hr-preset');
      syncPresets();
      render();
    });
  });
  el.billablePercent.addEventListener('input', syncPresets);
}

function syncPresets() {
  $$('[data-hr-preset]').forEach((b) => {
    b.classList.toggle('is-active', b.getAttribute('data-hr-preset') === String(el.billablePercent.value));
  });
}

function init() {
  if (!el.targetIncome) return;
  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }
  const form = el.targetIncome.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);
  wirePresets();
  syncPresets();
  render();
}

init();
