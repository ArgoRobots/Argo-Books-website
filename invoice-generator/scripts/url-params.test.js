import test from 'node:test';
import assert from 'node:assert/strict';
import { parseTemplateParam, parseShareLink, serializeShareLink } from './url-params.js';

const VALID = ['classic', 'modern', 'formal', 'elegant', 'ribbon'];

test('parseTemplateParam returns null for empty string', () => {
  assert.equal(parseTemplateParam('', VALID), null);
});

test('parseTemplateParam returns null when param missing entirely', () => {
  assert.equal(parseTemplateParam(null, VALID), null);
  assert.equal(parseTemplateParam(undefined, VALID), null);
});

test('parseTemplateParam returns the value when it is in the allowlist', () => {
  assert.equal(parseTemplateParam('?template=elegant', VALID), 'elegant');
  assert.equal(parseTemplateParam('?template=ribbon', VALID), 'ribbon');
  assert.equal(parseTemplateParam('?foo=1&template=modern&bar=2', VALID), 'modern');
});

test('parseTemplateParam is case insensitive on the value', () => {
  assert.equal(parseTemplateParam('?template=ELEGANT', VALID), 'elegant');
  assert.equal(parseTemplateParam('?template=Ribbon', VALID), 'ribbon');
});

test('parseTemplateParam returns null when value is not in the allowlist', () => {
  assert.equal(parseTemplateParam('?template=fancy', VALID), null);
  assert.equal(parseTemplateParam('?template=<script>', VALID), null);
});

test('parseTemplateParam returns null when the param is the empty string', () => {
  assert.equal(parseTemplateParam('?template=', VALID), null);
});

// --- parseShareLink ---

test('parseShareLink ignores empty query string', () => {
  assert.deepEqual(parseShareLink('', VALID), {});
  assert.deepEqual(parseShareLink(null, VALID), {});
  assert.deepEqual(parseShareLink(undefined, VALID), {});
});

test('parseShareLink returns whitelisted keys only', () => {
  const out = parseShareLink('?template=elegant&from=Acme&unknown=ignored&logoDataUrl=evil', VALID);
  assert.deepEqual(out, { template: 'elegant', from: 'Acme' });
});

test('parseShareLink lowercases template and rejects unknown templates', () => {
  assert.deepEqual(parseShareLink('?template=ELEGANT', VALID), { template: 'elegant' });
  assert.deepEqual(parseShareLink('?template=does-not-exist', VALID), {});
});

test('parseShareLink uppercases currency and rejects malformed values', () => {
  assert.deepEqual(parseShareLink('?currency=cad', VALID), { currency: 'CAD' });
  assert.deepEqual(parseShareLink('?currency=USD', VALID), { currency: 'USD' });
  // Wrong length or non-alpha must be rejected.
  assert.deepEqual(parseShareLink('?currency=CADX', VALID), {});
  assert.deepEqual(parseShareLink('?currency=US', VALID), {});
  assert.deepEqual(parseShareLink('?currency=123', VALID), {});
});

test('parseShareLink parses taxRatePercent as float, clamps the range', () => {
  assert.deepEqual(parseShareLink('?taxRatePercent=13', VALID), { taxRatePercent: 13 });
  assert.deepEqual(parseShareLink('?taxRatePercent=13.5', VALID), { taxRatePercent: 13.5 });
  assert.deepEqual(parseShareLink('?taxRatePercent=-5', VALID), {});
  assert.deepEqual(parseShareLink('?taxRatePercent=999999', VALID), {});
  assert.deepEqual(parseShareLink('?taxRatePercent=abc', VALID), {});
});

test('parseShareLink accepts zero taxRatePercent', () => {
  assert.deepEqual(parseShareLink('?taxRatePercent=0', VALID), { taxRatePercent: 0 });
});

test('parseShareLink restricts taxRateMode to percent or fixed', () => {
  assert.deepEqual(parseShareLink('?taxRateMode=percent', VALID), { taxRateMode: 'percent' });
  assert.deepEqual(parseShareLink('?taxRateMode=fixed', VALID), { taxRateMode: 'fixed' });
  assert.deepEqual(parseShareLink('?taxRateMode=PERCENT', VALID), {}); // case-sensitive on enum values
  assert.deepEqual(parseShareLink('?taxRateMode=gross', VALID), {});
});

test('parseShareLink truncates over-long string fields', () => {
  const big = 'x'.repeat(500);
  const out = parseShareLink(`?from=${big}`, VALID);
  assert.equal(out.from.length, 200);
});

test('parseShareLink ignores empty values', () => {
  assert.deepEqual(parseShareLink('?from=&billTo=', VALID), {});
});

test('parseShareLink is case-insensitive on keys', () => {
  assert.deepEqual(
    parseShareLink('?Template=elegant&From=Acme&BILLTO=Client', VALID),
    { template: 'elegant', from: 'Acme', billTo: 'Client' }
  );
});

// --- serializeShareLink ---

test('serializeShareLink emits only the supported keys', () => {
  const state = {
    template: 'elegant', currency: 'CAD',
    from: 'Acme', billTo: 'Client', invoiceNumber: 'INV-001',
    paymentTerms: 'Net 30', taxRatePercent: 13, taxRateMode: 'percent',
    // Fields NOT in the whitelist must not appear:
    notes: 'private', logoDataUrl: 'data:image/png...', lineItems: [{}],
    date: '2026-05-28', dueDate: '2026-06-28', amountPaid: 50,
  };
  const url = serializeShareLink('https://argorobots.com/invoice-generator/', state);
  const u = new URL(url);
  assert.equal(u.searchParams.get('template'), 'elegant');
  assert.equal(u.searchParams.get('currency'), 'CAD');
  assert.equal(u.searchParams.get('from'), 'Acme');
  assert.equal(u.searchParams.get('billTo'), 'Client');
  assert.equal(u.searchParams.get('invoiceNumber'), 'INV-001');
  assert.equal(u.searchParams.get('paymentTerms'), 'Net 30');
  assert.equal(u.searchParams.get('taxRatePercent'), '13');
  assert.equal(u.searchParams.get('taxRateMode'), 'percent');
  assert.equal(u.searchParams.has('notes'), false);
  assert.equal(u.searchParams.has('logoDataUrl'), false);
  assert.equal(u.searchParams.has('lineItems'), false);
  assert.equal(u.searchParams.has('date'), false);
  assert.equal(u.searchParams.has('amountPaid'), false);
});

test('serializeShareLink omits empty string fields', () => {
  const state = {
    template: 'classic',
    currency: '', from: '', billTo: '',
    invoiceNumber: '1',
    taxRatePercent: 0, taxRateMode: 'percent',
  };
  const url = serializeShareLink('https://argorobots.com/invoice-generator/', state);
  const u = new URL(url);
  assert.equal(u.searchParams.has('currency'), false);
  assert.equal(u.searchParams.has('from'), false);
  assert.equal(u.searchParams.has('billTo'), false);
  // Numeric 0 still serializes (it's a legitimate value, distinct from empty).
  assert.equal(u.searchParams.get('taxRatePercent'), '0');
  assert.equal(u.searchParams.get('template'), 'classic');
  assert.equal(u.searchParams.get('invoiceNumber'), '1');
});

test('serializeShareLink round-trips through parseShareLink', () => {
  const state = {
    template: 'modern', currency: 'USD',
    from: 'Acme LLC', billTo: 'Client Inc', invoiceNumber: 'INV-2026-001',
    paymentTerms: 'Net 15', taxRatePercent: 7.25, taxRateMode: 'percent',
  };
  const url = serializeShareLink('https://argorobots.com/invoice-generator/', state);
  const query = url.slice(url.indexOf('?'));
  const parsed = parseShareLink(query, VALID);
  assert.equal(parsed.template, 'modern');
  assert.equal(parsed.currency, 'USD');
  assert.equal(parsed.from, 'Acme LLC');
  assert.equal(parsed.billTo, 'Client Inc');
  assert.equal(parsed.invoiceNumber, 'INV-2026-001');
  assert.equal(parsed.paymentTerms, 'Net 15');
  assert.equal(parsed.taxRatePercent, 7.25);
  assert.equal(parsed.taxRateMode, 'percent');
});
