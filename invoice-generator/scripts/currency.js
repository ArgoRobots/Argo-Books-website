// invoice-generator/scripts/currency.js
// Currency formatting helpers used across the live tool, PDF, and DOCX generators.

export function formatMoney(amount, currencyCode, locale) {
  if (!Number.isFinite(amount)) amount = 0;
  try {
    return new Intl.NumberFormat(locale || 'en-US', {
      style: 'currency',
      currency: currencyCode || 'USD',
      currencyDisplay: 'narrowSymbol',
    }).format(amount);
  } catch (_e) {
    return `${currencyCode || ''} ${amount.toFixed(2)}`;
  }
}

export function parseMoney(input) {
  if (typeof input === 'number') return input;
  if (!input) return 0;
  const cleaned = String(input).replace(/[^\d.\-]/g, '');
  const n = parseFloat(cleaned);
  return Number.isFinite(n) ? n : 0;
}
