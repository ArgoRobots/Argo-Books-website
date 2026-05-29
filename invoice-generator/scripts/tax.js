// invoice-generator/scripts/tax.js
// Per-line tax computation. The country/subregion auto-fill and the
// exclusive/inclusive toggle from earlier Phase A drafts were both removed
// when the toolbar simplified to a Currency dropdown + manual tax entry,
// so this module is now a thin percent-of-subtotal helper.

export function computeTax(subtotal, ratePercent) {
  const r = (ratePercent || 0) / 100;
  return subtotal * r;
}
