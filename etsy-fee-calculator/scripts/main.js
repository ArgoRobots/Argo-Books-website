// etsy-fee-calculator/scripts/main.js
// Wires the form to the pure calc and renders live. Vanilla ES module.
//
// Rates arrive as window.ETSY_FEES, which index.php renders from data/fees.php.
// Switching country swaps the rate set, the currency formatting, and the symbol
// shown inside every money input.
//
// Reverse mode does not have its own display path: it solves for a price, then
// feeds that price back through the same forward calculation. Whatever the two
// modes disagree about, it will not be the breakdown.

import { computeSale, priceForProfit, projectYear, num } from './calc.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const FEES = window.ETSY_FEES || {};

const $ = (sel) => document.querySelector(sel);
const $$ = (sel) => Array.from(document.querySelectorAll(sel));
const out = (key) => document.querySelector(`[data-ec="${key}"]`);

const el = {
  country: $('[data-ec="country"]'),
  itemPrice: $('[data-ec="itemPrice"]'),
  targetProfit: $('[data-ec="targetProfit"]'),
  shippingCharged: $('[data-ec="shippingCharged"]'),
  materials: $('[data-ec="materials"]'),
  labour: $('[data-ec="labour"]'),
  shippingCost: $('[data-ec="shippingCost"]'),
  offsiteAdsRate: $('[data-ec="offsiteAdsRate"]'),
  currencyConversion: $('[data-ec="currencyConversion"]'),
  listingFee: $('[data-ec="listingFee"]'),
  salesPerMonth: $('[data-ec="salesPerMonth"]'),
};

let mode = 'forward';

/* ---------- formatting ---------- */

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
let moneyRound = money;
let symbol = '$';

// Rebuild the formatters for the selected country. The rates object carries its
// own locale (from data/fees.php) rather than looking it up by currency code.
function setCurrency(rates) {
  const fmt = currencyFormatter(rates.currency, rates.locale);
  money = fmt.money;
  moneyRound = fmt.moneyRound;
  symbol = fmt.symbol;
  applyCurrencyAffixes(symbol, '.calc-money', '[data-ec-symbol]');
}

const pct = (rate, decimals = 1) => `${(rate * 100).toFixed(decimals)}%`;

// Trims a trailing ".0" so rates read as "3%" and "1.15%" rather than "3.0%".
const ratePct = (rate) => `${parseFloat((rate * 100).toFixed(2))}%`;

/* ---------- reading the form ---------- */

function readInputs() {
  return {
    itemPrice: el.itemPrice.value,
    targetProfit: el.targetProfit.value,
    shippingCharged: el.shippingCharged.value,
    materials: el.materials.value,
    labour: el.labour.value,
    shippingCost: el.shippingCost.value,
    otherCosts: 0,
    listingFee: el.listingFee.value,
    offsiteAdsRate: el.offsiteAdsRate.value,
    currencyConversion: el.currencyConversion.checked,
  };
}

/* ---------- rendering ---------- */

function paintHeadline(sale, input, solved) {
  if (mode === 'reverse') {
    const target = num(input.targetProfit);
    out('headlineLabel').textContent = 'List it at';
    out('headline').textContent = money(sale.itemPrice);

    if (target === 0 && sale.costs === 0) {
      out('headlineSub').textContent = 'Enter the profit you want from one sale';
    } else if (solved && solved.itemPrice < 0) {
      // The shipping charge alone already covers the target, so the solver
      // wanted a negative price. Saying so beats silently showing zero.
      out('headlineSub').textContent = 'The shipping you charge already covers this on its own';
    } else {
      out('headlineSub').textContent = `to keep ${money(target)} after every fee and cost`;
    }
    return;
  }

  out('headlineLabel').textContent = 'You keep, per sale';
  out('headline').textContent = money(sale.profit);
  out('headlineSub').textContent = sale.orderTotal > 0
    ? `${money(sale.orderTotal)} order, ${money(sale.totalFees)} to Etsy, ${money(sale.costs)} of costs`
    : 'Enter a price to see the breakdown';
}

function paintRow(key, value, visible) {
  const row = document.querySelector(`[data-ec-row="${key}"]`);
  if (row) row.hidden = !visible;
  const cell = out(`fee-${key}`);
  if (cell) cell.textContent = money(value);
}

function render() {
  const rates = FEES[el.country.value];
  if (!rates) return;

  const input = readInputs();

  // Reverse mode solves for a price, then runs the ordinary forward
  // calculation on it so the breakdown below is produced by one code path.
  let solved = null;
  if (mode === 'reverse') {
    solved = priceForProfit(input, rates);
    input.itemPrice = solved ? Math.max(0, solved.itemPrice) : 0;
  }

  const sale = computeSale(input, rates);

  paintHeadline(sale, input, solved);

  out('orderTotal').textContent = money(sale.orderTotal);
  out('fee-listing').textContent = money(sale.fees.listing);
  out('fee-transaction').textContent = money(sale.fees.transaction);
  out('fee-processing').textContent = money(sale.fees.processing);

  out('note-processing').textContent =
    `${ratePct(rates.processingPct)} + ${symbol}${rates.processingFlat.toFixed(2)}`;
  out('note-regulatory').textContent = ratePct(rates.regulatoryPct);

  paintRow('regulatory', sale.fees.regulatory, rates.regulatoryPct > 0);
  paintRow('offsiteAds', sale.fees.offsiteAds, num(input.offsiteAdsRate) > 0);
  paintRow('currencyConversion', sale.fees.currencyConversion, input.currencyConversion);

  out('totalFees').textContent = money(sale.totalFees);
  out('feePct').textContent = sale.orderTotal > 0 ? `${pct(sale.feePct)} of the order` : '';
  out('costs').textContent = money(sale.costs);
  out('profit').textContent = money(sale.profit);
  out('margin').textContent = sale.orderTotal > 0 ? pct(sale.margin, 0) : '0%';

  // Losing money on a sale is worth flagging visually, not just numerically.
  const results = document.querySelector('[data-ec-results]');
  if (results) results.classList.toggle('is-loss', sale.orderTotal > 0 && sale.profit < 0);

  paintYear(sale);
}

function paintYear(sale) {
  const block = document.querySelector('[data-ec-year]');
  if (!block) return;

  const perMonth = num(el.salesPerMonth.value);
  const show = perMonth > 0 && sale.orderTotal > 0;
  block.hidden = !show;
  if (!show) return;

  const year = projectYear(sale, perMonth);
  out('yearTitle').textContent = `At ${perMonth} ${perMonth === 1 ? 'sale' : 'sales'} a month`;
  out('year-sales').textContent = String(Math.round(year.sales));
  out('year-revenue').textContent = moneyRound(year.revenue);
  out('year-fees').textContent = moneyRound(year.fees);
  out('year-profit').textContent = moneyRound(year.profit);
}

/* ---------- wiring ---------- */

function setMode(next) {
  mode = next;
  $$('[data-ec-mode]').forEach((btn) => {
    const active = btn.getAttribute('data-ec-mode') === next;
    btn.classList.toggle('is-active', active);
    btn.setAttribute('aria-selected', active ? 'true' : 'false');
  });
  $$('[data-ec-when]').forEach((field) => {
    field.hidden = field.getAttribute('data-ec-when') !== next;
  });
  render();
}

function wireModes() {
  $$('[data-ec-mode]').forEach((btn) => {
    btn.addEventListener('click', () => setMode(btn.getAttribute('data-ec-mode')));
  });
}

function wireCountry() {
  el.country.addEventListener('change', () => {
    setCurrency(FEES[el.country.value]);
    render();
  });
}

function init() {
  if (!el.country || !FEES[el.country.value]) return;

  setCurrency(FEES[el.country.value]);
  wireModes();
  wireCountry();

  const form = el.country.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);

  render();
}

init();
