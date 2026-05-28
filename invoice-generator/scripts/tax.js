// invoice-generator/scripts/tax.js
// Per-line tax computation. The country/subregion auto-fill lookup from
// Phase A was removed when the toolbar switched to a Currency dropdown plus
// manual tax entry, so loadTaxData/lookupRate and tax-rates.json went with
// it. computeTax stays because render.js uses it for the math.

// taxMode: 'exclusive' (tax added on top) or 'inclusive' (tax is part of the line price)
export function computeTax(subtotal, ratePercent, taxMode) {
  const r = (ratePercent || 0) / 100;
  if (taxMode === 'inclusive') {
    return subtotal - (subtotal / (1 + r));
  }
  return subtotal * r;
}
