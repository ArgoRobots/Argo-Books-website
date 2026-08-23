// craft-fair-calculator/scripts/main.js
// Wires the form to craftFair() in shared/scripts/business-calcs.js.

import { craftFair, num } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter, applyCurrencyAffixes } from '../../shared/scripts/currency-format.js';

const $ = (sel) => document.querySelector(sel);
const out = (key) => document.querySelector(`[data-cf-results] [data-cf="${key}"]`);

const el = {
  currency: $('[data-cf="currency"]'),
  boothFee: $('[data-cf="boothFee"]'),
  travel: $('[data-cf="travel"]'),
  otherCosts: $('[data-cf="otherCosts"]'),
  hours: $('[data-cf="hours"]'),
  hourlyRate: $('[data-cf="hourlyRate"]'),
  averageSale: $('[data-cf="averageSale"]'),
  materialPercent: $('[data-cf="materialPercent"]'),
  salesMade: $('[data-cf="salesMade"]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
const count = (n) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(n);

function setCurrency(code) {
  const fmt = currencyFormatter(code);
  money = fmt.money;
  applyCurrencyAffixes(fmt.symbol, '.calc-money', '.calc-money-affix:not(.calc-money-affix-right)');
}

function render() {
  const r = craftFair({
    boothFee: el.boothFee.value,
    travel: el.travel.value,
    otherCosts: el.otherCosts.value,
    hours: el.hours.value,
    hourlyRate: el.hourlyRate.value,
    averageSale: el.averageSale.value,
    materialPercent: el.materialPercent.value,
    salesMade: el.salesMade.value,
  });

  out('breakEven').textContent = r.viable ? count(r.salesToCoverCash) : '—';
  out('breakEvenSub').textContent = r.viable
    ? `sales just to get your ${money(r.cashCosts)} back`
    : 'Enter your costs and average sale';

  out('cashCosts').textContent = money(r.cashCosts);
  out('timeCost').textContent = money(r.timeCost);
  out('totalCost').textContent = money(r.totalCost);
  out('contribution').textContent = money(r.contributionPerSale);
  out('salesAll').textContent = r.viable ? count(r.salesToCoverAll) : '—';

  paintActual(r);
}

function paintActual(r) {
  const box = document.querySelector('[data-cf-actual]');
  const sales = num(el.salesMade.value);
  const show = sales > 0 && r.viable;
  box.hidden = !show;
  if (!show) return;

  out('actualLabel').textContent = `How ${count(sales)} sales worked out`;
  out('revenue').textContent = money(r.revenue);
  out('profitCash').textContent = money(r.profitCash);
  out('profitTrue').textContent = money(r.profitTrue);
  out('hourly').textContent = `${money(r.effectiveHourly)} an hour`;

  // Three genuinely different outcomes, so say which one this was.
  const rate = num(el.hourlyRate.value);
  if (r.profitCash < 0) {
    out('verdict').textContent =
      `The day cost you ${money(Math.abs(r.profitCash))} in cash, before your time. `
      + `You needed ${count(r.salesToCoverCash)} sales just to break even on the booth fee and travel.`;
  } else if (r.profitTrue < 0) {
    out('verdict').textContent =
      `You got your cash back and kept ${money(r.profitCash)}, but that works out at `
      + `${money(r.effectiveHourly)} an hour against the ${money(rate)} you set. `
      + `Covering your time too would have needed ${count(r.salesToCoverAll)} sales.`;
  } else {
    out('verdict').textContent =
      `The day cleared its costs and paid you ${money(r.effectiveHourly)} an hour, `
      + `${money(r.profitTrue)} ahead of your ${money(rate)} target. Worth repeating.`;
  }
}

function init() {
  if (!el.boothFee) return;
  if (el.currency) {
    setCurrency(el.currency.value);
    el.currency.addEventListener('change', () => { setCurrency(el.currency.value); render(); });
  }
  const form = el.boothFee.closest('form') || document;
  form.addEventListener('input', render);
  form.addEventListener('change', render);
  render();
}

init();
