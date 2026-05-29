import test from 'node:test';
import assert from 'node:assert/strict';
import { computeTax } from './tax.js';

test('computeTax returns percent of subtotal', () => {
  assert.equal(computeTax(100, 10), 10);
  assert.equal(computeTax(200, 13), 26);
});

test('computeTax handles 0 rate', () => {
  assert.equal(computeTax(100, 0), 0);
});

test('computeTax handles missing/null rate as 0', () => {
  assert.equal(computeTax(100, null), 0);
  assert.equal(computeTax(100, undefined), 0);
});
