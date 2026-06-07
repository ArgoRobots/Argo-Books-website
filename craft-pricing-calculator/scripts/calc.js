// craft-pricing-calculator/scripts/calc.js
// Pure pricing math for the craft pricing calculator. No DOM, no globals, so
// calc.test.js can verify it directly.
//
// Model (the standard handmade-pricing formula):
//   total cost    = material cost + labor cost
//   selling price = total cost * (1 + markup%)
//   profit        = selling price - total cost
//   margin        = profit / selling price

export function computeCraftPrice({ materialCost, laborCost, markupPercent }) {
  const material = Math.max(0, Number(materialCost) || 0);
  const labor = Math.max(0, Number(laborCost) || 0);
  const markup = Math.max(0, Number(markupPercent) || 0);

  const totalCost = material + labor;
  const sellingPrice = totalCost * (1 + markup / 100);
  const profit = sellingPrice - totalCost;
  const margin = sellingPrice > 0 ? profit / sellingPrice : 0;

  return { material, labor, markup, totalCost, sellingPrice, profit, margin };
}
