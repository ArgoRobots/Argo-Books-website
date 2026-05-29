// invoice-generator/scripts/url-params.js
// Parses URL query parameters for the invoice generator.
//
// parseShareLink + serializeShareLink: share-link helpers. Parse a whitelist
// of pre-fill fields (template, currency, from, billTo, invoiceNumber,
// paymentTerms, taxRatePercent, taxRateMode) so a user can copy a permalink
// that restores a sensible starting invoice. The whitelist deliberately
// excludes line items, notes, terms, addresses, and the logo so URLs stay
// short and never carry accidental PII from one user to another.

const MAX_STR = 200;
const MAX_SHORT_STR = 40;
const MAX_TAX_VALUE = 100000;

// The whitelist is the contract for share links. Adding a key here makes it
// shareable; removing one stops emitting it without breaking older links
// (parseShareLink ignores unknown keys).
const KEYS = {
  template:       { type: 'enum' },
  currency:       { type: 'currency' },
  from:           { type: 'string', max: MAX_STR },
  billTo:         { type: 'string', max: MAX_STR },
  invoiceNumber:  { type: 'string', max: MAX_SHORT_STR },
  paymentTerms:   { type: 'string', max: MAX_SHORT_STR },
  taxRatePercent: { type: 'float', min: 0, max: MAX_TAX_VALUE },
  taxRateMode:    { type: 'oneof', values: ['percent', 'fixed'] },
};

const KEYS_LOWER = Object.fromEntries(Object.keys(KEYS).map(k => [k.toLowerCase(), k]));

function coerce(key, raw, templates) {
  const spec = KEYS[key];
  if (raw === '' || raw == null) return undefined;
  switch (spec.type) {
    case 'enum': {
      const v = String(raw).toLowerCase();
      return Array.isArray(templates) && templates.includes(v) ? v : undefined;
    }
    case 'currency': {
      const v = String(raw).toUpperCase();
      return /^[A-Z]{3}$/.test(v) ? v : undefined;
    }
    case 'string': {
      // Truncate by codepoints (Array.from splits on full code points) so
      // we never leave a lone surrogate when the cut falls inside a
      // non-BMP character like an emoji.
      const cps = Array.from(String(raw));
      const v = cps.length > spec.max ? cps.slice(0, spec.max).join('') : cps.join('');
      return v || undefined;
    }
    case 'float': {
      const n = parseFloat(String(raw));
      if (!Number.isFinite(n)) return undefined;
      if (n < spec.min || n > spec.max) return undefined;
      return n;
    }
    case 'oneof': {
      const v = String(raw);
      return spec.values.includes(v) ? v : undefined;
    }
  }
  return undefined;
}

export function parseShareLink(queryString, templates) {
  if (!queryString) return {};
  const stripped = queryString.startsWith('?') ? queryString.slice(1) : queryString;
  const params = new URLSearchParams(stripped);
  const out = {};
  for (const [rawKey, rawValue] of params) {
    const key = KEYS_LOWER[rawKey.toLowerCase()];
    if (!key) continue;
    const coerced = coerce(key, rawValue, templates);
    if (coerced !== undefined) out[key] = coerced;
  }
  return out;
}

export function serializeShareLink(baseUrl, state) {
  const url = new URL(baseUrl);
  // Reset any existing query so the function is deterministic.
  url.search = '';
  for (const key of Object.keys(KEYS)) {
    const value = state[key];
    if (value === undefined || value === null) continue;
    if (typeof value === 'string' && value === '') continue;
    if (typeof value === 'number' && !Number.isFinite(value)) continue;
    url.searchParams.set(key, String(value));
  }
  return url.toString();
}
