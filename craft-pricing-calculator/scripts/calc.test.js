// craft-pricing-calculator/scripts/calc.test.js
// Run with: node --test craft-pricing-calculator/scripts/*.test.js

import { test } from 'node:test';
import assert from 'node:assert';
import { computeCraftPrice } from './calc.js';

const near = (a, b, tol = 0.001, msg = '') =>
  assert.ok(Math.abs(a - b) <= tol, `${msg} expected ~${b}, got ${a}`);

test('materials + labor, 150% markup', () => {
  const r = computeCraftPrice({ materialCost: 5, laborCost: 10, markupPercent: 150 });
  near(r.totalCost, 15, 0.001, 'total cost');
  near(r.sellingPrice, 37.5, 0.001, 'selling price'); // 15 * 2.5
  near(r.profit, 22.5, 0.001, 'profit');
  near(r.margin, 0.6, 0.0001, 'margin'); // 22.5 / 37.5
});

test('100% markup doubles cost and gives 50% margin', () => {
  const r = computeCraftPrice({ materialCost: 8, laborCost: 12, markupPercent: 100 });
  near(r.sellingPrice, 40, 0.001);
  near(r.margin, 0.5, 0.0001);
});

test('zero markup means price equals cost, zero profit', () => {
  const r = computeCraftPrice({ materialCost: 3, laborCost: 7, markupPercent: 0 });
  near(r.sellingPrice, 10, 0.001);
  near(r.profit, 0, 0.001);
  near(r.margin, 0, 0.001);
});

test('empty / invalid inputs are treated as zero', () => {
  const r = computeCraftPrice({ materialCost: '', laborCost: undefined, markupPercent: 'abc' });
  assert.strictEqual(r.totalCost, 0);
  assert.strictEqual(r.sellingPrice, 0);
  assert.strictEqual(r.margin, 0);
});

test('negative inputs are floored at zero', () => {
  const r = computeCraftPrice({ materialCost: -5, laborCost: -10, markupPercent: -50 });
  assert.strictEqual(r.totalCost, 0);
  assert.strictEqual(r.sellingPrice, 0);
});
