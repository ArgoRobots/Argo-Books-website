import test from 'node:test';
import assert from 'node:assert/strict';
import { computeSubtotal, computeDiscount, computeTotals } from './render.js';

test('computeSubtotal sums quantity * rate', () => {
  assert.equal(computeSubtotal([{quantity: 2, rate: 50}, {quantity: 1, rate: 100}]), 200);
});

test('computeSubtotal handles strings and missing values', () => {
  assert.equal(computeSubtotal([{quantity: '2', rate: '50'}, {quantity: 0, rate: 999}, {}]), 100);
});

test('computeDiscount percent mode', () => {
  assert.equal(computeDiscount(200, {mode: 'percent', value: 10}), 20);
});

test('computeDiscount fixed mode', () => {
  assert.equal(computeDiscount(200, {mode: 'fixed', value: 25}), 25);
});

test('computeDiscount handles null discount as 0', () => {
  assert.equal(computeDiscount(200, null), 0);
});

test('computeTotals percent tax with no discount or shipping', () => {
  const state = {
    lineItems: [{quantity: 1, rate: 100}],
    discount: null, shipping: null,
    taxRatePercent: 10,
    amountPaid: 0,
  };
  const t = computeTotals(state);
  assert.equal(t.subtotal, 100);
  assert.equal(t.tax, 10);
  assert.equal(t.total, 110);
  assert.equal(t.balanceDue, 110);
});

test('computeTotals discount reduces the tax base', () => {
  const state = {
    lineItems: [{quantity: 1, rate: 100}],
    discount: {mode: 'percent', value: 10},
    shipping: null,
    taxRatePercent: 10,
    amountPaid: 0,
  };
  const t = computeTotals(state);
  assert.equal(t.subtotal, 100);
  assert.equal(t.discount, 10);
  assert.equal(t.tax, 9);
  assert.equal(t.total, 99);
});

test('computeTotals shipping adds to the tax base', () => {
  const state = {
    lineItems: [{quantity: 1, rate: 100}],
    discount: null,
    shipping: {value: 25},
    taxRatePercent: 10,
    amountPaid: 0,
  };
  const t = computeTotals(state);
  assert.equal(t.subtotal, 100);
  assert.equal(t.shipping, 25);
  assert.equal(t.tax, 12.5);
  assert.equal(t.total, 137.5);
});

test('computeTotals taxRateMode "fixed" treats taxRatePercent as a flat dollar amount', () => {
  const state = {
    lineItems: [{quantity: 1, rate: 100}],
    discount: null, shipping: null,
    taxRatePercent: 7, taxRateMode: 'fixed',
    amountPaid: 0,
  };
  const t = computeTotals(state);
  assert.equal(t.tax, 7);
  assert.equal(t.total, 107);
});

test('computeTotals balanceDue subtracts amountPaid', () => {
  const state = {
    lineItems: [{quantity: 1, rate: 100}],
    discount: null, shipping: null,
    taxRatePercent: 0,
    amountPaid: 40,
  };
  const t = computeTotals(state);
  assert.equal(t.total, 100);
  assert.equal(t.balanceDue, 60);
});
