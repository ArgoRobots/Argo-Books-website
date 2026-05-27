// invoice-generator/scripts/tax.js
// Tax data loader, rate lookup, and per-line tax computation.

const BASE = (typeof window !== 'undefined' && window.INVGEN_BASE) || '';

let taxData = null;

export async function loadTaxData() {
  if (taxData) return taxData;
  const res = await fetch(`${BASE}/invoice-generator/data/tax-rates.json`);
  taxData = await res.json();
  return taxData;
}

export function lookupRate(data, countryCode, subregionCode) {
  const country = data[countryCode];
  if (!country) return null;
  if (typeof country.rate === 'number') return { rate: country.rate, note: country.notes || null };
  if (country.subregions && subregionCode) {
    const sub = country.subregions.find(s => s.code === subregionCode);
    if (sub) return { rate: sub.rate, note: sub.notes || null, subregionName: sub.name, type: sub.type || null };
  }
  return null;
}

// taxMode: 'exclusive' (tax added on top) or 'inclusive' (tax is part of the line price)
export function computeTax(subtotal, ratePercent, taxMode) {
  const r = (ratePercent || 0) / 100;
  if (taxMode === 'inclusive') {
    return subtotal - (subtotal / (1 + r));
  }
  return subtotal * r;
}
