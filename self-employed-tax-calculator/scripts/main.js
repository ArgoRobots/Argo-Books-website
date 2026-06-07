// self-employed-tax-calculator/scripts/main.js
// Wires the calculator form to the pure calc functions and renders a live
// estimate. Vanilla ES module, no build step. Reuses formatMoney from the
// invoice generator so currency formatting stays consistent across tools.

import { computeTax } from './calc.js';
import { PROVINCES, PROVINCE_ORDER } from '../data/tax-rates-2026.js';
import { formatMoney } from '../../invoice-generator/scripts/currency.js';

const $ = (sel) => document.querySelector(sel);
const el = {
  country: $('[data-tc="country"]'),
  provinceField: $('[data-tc-province-field]'),
  province: $('[data-tc="province"]'),
  income: $('[data-tc="income"]'),
  expenses: $('[data-tc="expenses"]'),
  regionNote: $('[data-tc="region-note"]'),
  setaside: $('[data-tc="setaside"]'),
  setasidePct: $('[data-tc="setaside-pct"]'),
  quarterly: $('[data-tc="quarterly"]'),
  netProfit: $('[data-tc="netprofit"]'),
  contributionLabel: $('[data-tc="contribution-label"]'),
  contribution: $('[data-tc="contribution"]'),
  incomeTax: $('[data-tc="incometax"]'),
  total: $('[data-tc="total"]'),
  effRate: $('[data-tc="effrate"]'),
};

const num = (v) => {
  const n = parseFloat(v);
  return Number.isFinite(n) && n > 0 ? n : 0;
};

const pct = (rate) => `${(rate * 100).toFixed(1)}%`;

// Whole-dollar formatting for the headline (a "set aside roughly this much"
// number reads better without cents). The detailed breakdown keeps cents via
// the shared formatMoney.
function formatWhole(amount, currency, locale) {
  try {
    return new Intl.NumberFormat(locale, {
      style: 'currency', currency, maximumFractionDigits: 0,
    }).format(Math.round(amount));
  } catch (_e) {
    return `$${Math.round(amount)}`;
  }
}

function populateProvinces() {
  if (!el.province) return;
  el.province.innerHTML = '';
  for (const code of PROVINCE_ORDER) {
    const opt = document.createElement('option');
    opt.value = code;
    opt.textContent = PROVINCES[code].name;
    el.province.appendChild(opt);
  }
  el.province.value = 'ON'; // sensible default
}

function syncCountry() {
  const isCA = el.country.value === 'CA';
  if (el.provinceField) el.provinceField.hidden = !isCA;
  if (el.regionNote) {
    el.regionNote.textContent = isCA
      ? 'Includes federal and provincial income tax plus CPP/QPP. Tax year 2026.'
      : 'Federal estimate only — does not include state income tax. Tax year 2026.';
  }
}

function render() {
  const country = el.country.value;
  const profit = Math.max(0, num(el.income.value) - num(el.expenses.value));
  const province = el.province ? el.province.value : 'ON';

  const r = computeTax({ country, profit, province });
  const currency = r.currency;
  const locale = country === 'CA' ? 'en-CA' : 'en-US';
  const money = (n) => formatMoney(n, currency, locale);

  el.setaside.textContent = formatWhole(r.total, currency, locale);
  el.setasidePct.textContent = pct(r.effectiveRate);
  el.quarterly.textContent = formatWhole(r.quarterly, currency, locale);

  el.netProfit.textContent = money(r.netProfit);
  el.contributionLabel.textContent = r.contributionLabel;
  el.contribution.textContent = money(r.contribution);
  el.incomeTax.textContent = money(r.incomeTax);
  el.total.textContent = money(r.total);
  el.effRate.textContent = pct(r.effectiveRate);
}

function handle(ev) {
  if (ev.target === el.country) syncCountry();
  render();
}

function init() {
  if (!el.country) return;
  populateProvinces();
  syncCountry();
  render();
  const form = el.country.closest('form') || document;
  form.addEventListener('input', handle);
  form.addEventListener('change', handle);
}

init();
