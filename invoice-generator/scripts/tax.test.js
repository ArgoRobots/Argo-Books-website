import test from 'node:test';
import assert from 'node:assert/strict';
import { computeTax } from './tax.js';

test('computeTax exclusive mode adds tax on top', () => {
  assert.equal(computeTax(100, 10, 'exclusive'), 10);
  assert.equal(computeTax(200, 13, 'exclusive'), 26);
});

test('computeTax inclusive mode extracts tax from gross', () => {
  // 110 gross at 10% inclusive: net = 100, tax = 10
  assert.ok(Math.abs(computeTax(110, 10, 'inclusive') - 10) < 0.001);
  // 226 gross at 13% inclusive: net = 200, tax = 26
  assert.ok(Math.abs(computeTax(226, 13, 'inclusive') - 26) < 0.001);
});

test('computeTax handles 0 rate', () => {
  assert.equal(computeTax(100, 0, 'exclusive'), 0);
  assert.equal(computeTax(100, 0, 'inclusive'), 0);
});

test('computeTax handles missing/null rate as 0', () => {
  assert.equal(computeTax(100, null, 'exclusive'), 0);
  assert.equal(computeTax(100, undefined, 'exclusive'), 0);
});
