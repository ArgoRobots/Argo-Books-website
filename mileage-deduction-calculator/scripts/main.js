// mileage-deduction-calculator/scripts/main.js
// Wires the mileage form to the pure math in shared/scripts/business-calcs.js.
//
// The distance inputs are built at runtime, because the shape depends on the
// region: a split-rate year (the US in 2026) needs one box per period, while a
// tiered country needs a single total and the bands are worked out from it.

import { mileageTiered, mileageSplit, num } from '../../shared/scripts/business-calcs.js';
import { currencyFormatter } from '../../shared/scripts/currency-format.js';

const REGIONS = window.ARGO_MILEAGE || {};

const $ = (sel) => document.querySelector(sel);
const out = (key) => document.querySelector(`[data-mc="${key}"]`);
// regionNote lives in the form rather than the results panel, so this one
// stays unscoped. No mileage input shares a key with an output cell.

const el = {
  region: $('[data-mc="region"]'),
  taxRate: $('[data-mc="taxRate"]'),
  inputs: $('[data-mc-inputs]'),
};

let money = (n) => `$${(Number.isFinite(n) ? n : 0).toFixed(2)}`;
let currentCurrency = 'USD';

const fmtDistance = (n) => new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(Math.round(n));

/** Rate shown per unit: sub-dollar rates read better as cents or pence. */
function rateLabel(rate, region) {
  const minor = rate * 100;
  const symbol = currentCurrency === 'GBP' ? 'p' : '¢';
  return rate < 1
    ? `${minor % 1 === 0 ? minor.toFixed(0) : minor.toFixed(1)}${symbol}/${region.unit}`
    : `${money(rate)}/${region.unit}`;
}

/** Build the distance fields for the selected region. */
function buildInputs(region) {
  el.inputs.innerHTML = '';

  if (region.split) {
    region.periods.forEach((p, i) => {
      el.inputs.insertAdjacentHTML('beforeend', `
        <div class="calc-field">
          <label for="mc-d${i}">${escapeHtml(p.label)}</label>
          <div class="calc-money calc-money-suffix">
            <input id="mc-d${i}" data-mc-distance data-mc-rate="${p.rate}" type="number"
                   inputmode="decimal" min="0" step="1" placeholder="0">
            <span class="calc-money-affix calc-money-affix-right">${escapeHtml(region.unitPlural)}</span>
          </div>
          <p class="calc-hint">Claimed at ${rateLabel(p.rate, region)}.</p>
        </div>`);
    });
    return;
  }

  const cap = region.cap
    ? ` The method is capped at ${fmtDistance(region.cap)} ${escapeHtml(region.unitPlural)} a year.`
    : ' The calculator splits this across the rate bands for you.';

  el.inputs.insertAdjacentHTML('beforeend', `
    <div class="calc-field">
      <label for="mc-d0">Business ${escapeHtml(region.unitPlural)} this year</label>
      <div class="calc-money calc-money-suffix">
        <input id="mc-d0" data-mc-distance type="number" inputmode="decimal" min="0" step="1" placeholder="0">
        <span class="calc-money-affix calc-money-affix-right">${escapeHtml(region.unitPlural)}</span>
      </div>
      <p class="calc-hint">Your total for the year.${cap}</p>
    </div>`);
}

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));
}

function render() {
  const region = REGIONS[el.region.value];
  if (!region) return;

  const fields = Array.from(document.querySelectorAll('[data-mc-distance]'));
  const result = region.split
    ? mileageSplit(fields.map((f) => ({ distance: f.value, rate: parseFloat(f.getAttribute('data-mc-rate')) })))
    : mileageTiered(fields[0] ? fields[0].value : 0, region.tiers, region.cap);

  out('deduction').textContent = money(result.deduction);
  out('deductionSub').textContent = result.distance > 0
    ? `${fmtDistance(result.claimable)} ${region.unitPlural} claimed`
    : 'Enter your distance to see the claim';

  // One row per rate band, so the split is visible rather than assumed.
  const bands = document.querySelector('[data-mc-bands]');
  bands.innerHTML = result.bands.map((b) => `
    <div class="calc-breakdown-row">
      <dt>${fmtDistance(b.distance)} ${escapeHtml(region.unitPlural)}
        <span class="calc-band-rate">at ${rateLabel(b.rate, region)}</span></dt>
      <dd>${money(b.amount)}</dd>
    </div>`).join('');

  out('effectiveRate').textContent = result.claimable > 0 ? rateLabel(result.effectiveRate, region) : '0';

  // Australia's cap excludes distance rather than paying less for it, which is
  // worth saying out loud rather than silently dropping the kilometres.
  const excluded = document.querySelector('[data-mc-excluded]');
  const show = result.excluded > 0;
  excluded.hidden = !show;
  if (show) {
    out('excludedText').textContent =
      `${fmtDistance(result.excluded)} ${region.unitPlural} fall above the ${fmtDistance(region.cap)} ${region.unitPlural} cap and cannot be claimed this way. `
      + 'Past the cap you need the logbook method for the whole claim, which is usually worth more.';
  }

  const taxPct = Math.min(99, num(el.taxRate.value)) / 100;
  const worth = document.querySelector('[data-mc-worth]');
  const showWorth = taxPct > 0 && result.deduction > 0;
  worth.hidden = !showWorth;
  if (showWorth) out('taxSaved').textContent = money(result.deduction * taxPct);
}

function selectRegion() {
  const region = REGIONS[el.region.value];
  if (!region) return;
  currentCurrency = region.currency;
  money = currencyFormatter(region.currency, region.locale).money;
  out('regionNote').textContent = region.note || '';
  buildInputs(region);
  render();
}

function init() {
  if (!el.region || !REGIONS[el.region.value]) return;
  el.region.addEventListener('change', selectRegion);
  // Distance fields are rebuilt on every region change, so listen on the form.
  const form = el.region.closest('form') || document;
  form.addEventListener('input', render);
  selectRegion();
}

init();
