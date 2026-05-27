// invoice-generator/scripts/render.js
// Pure totals math + DOM-updating renderers used by the live tool.

import { formatMoney } from './currency.js';
import { computeTax } from './tax.js';

export function computeSubtotal(lineItems) {
  return lineItems.reduce(
    (sum, li) => sum + (Number(li.quantity) || 0) * (Number(li.rate) || 0),
    0
  );
}

export function computeDiscount(subtotal, discount) {
  if (!discount) return 0;
  if (discount.mode === 'percent') return subtotal * ((Number(discount.value) || 0) / 100);
  return Number(discount.value) || 0;
}

export function computeTotals(state) {
  const subtotal = computeSubtotal(state.lineItems);
  const discount = computeDiscount(subtotal, state.discount);
  const shipping = state.shipping ? (Number(state.shipping.value) || 0) : 0;
  const taxBase = subtotal - discount + shipping;

  // taxRateMode 'fixed' treats the tax field as a flat dollar amount.
  // 'percent' (default) routes through computeTax, which still respects
  // the exclusive / inclusive taxMode semantics from earlier tasks.
  let tax;
  if (state.taxRateMode === 'fixed') {
    tax = Number(state.taxRatePercent) || 0;
  } else {
    tax = computeTax(taxBase, state.taxRatePercent, state.taxMode);
  }

  const total = state.taxMode === 'inclusive' ? taxBase : taxBase + tax;
  const balanceDue = total - (Number(state.amountPaid) || 0);
  return { subtotal, discount, shipping, tax, total, balanceDue };
}

export function renderTotals(state) {
  const t = computeTotals(state);
  const f = (n) => formatMoney(n, state.currency, state.locale);
  setText('subtotal', f(t.subtotal));
  setText('discount-amount', f(-t.discount));
  setText('shipping-amount', f(t.shipping));
  setText('tax-amount', f(t.tax));
  setText('total', f(t.total));
  setText('balance-due', f(t.balanceDue));
}

export function renderLineItemAmount(rowEl, lineItem, state) {
  const out = rowEl.querySelector('[data-field="lineItem-amount"]');
  if (!out) return;
  const amount = (Number(lineItem.quantity) || 0) * (Number(lineItem.rate) || 0);
  out.textContent = formatMoney(amount, state.currency, state.locale);
}

function setText(field, value) {
  const el = document.querySelector(`[data-field="${field}"]`);
  if (el) el.textContent = value;
}
