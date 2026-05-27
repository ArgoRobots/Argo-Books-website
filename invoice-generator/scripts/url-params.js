// invoice-generator/scripts/url-params.js
// Parses the ?template= query parameter from the URL, validates it
// against an allowlist of template ids, and returns the canonical id
// (lowercase) or null when the parameter is missing or invalid.

export function parseTemplateParam(search, allowed) {
  if (!search || typeof search !== 'string') return null;
  // URLSearchParams handles a leading '?' just fine.
  const params = new URLSearchParams(search.startsWith('?') ? search : '?' + search);
  const raw = params.get('template');
  if (!raw) return null;
  const lower = raw.toLowerCase();
  return Array.isArray(allowed) && allowed.includes(lower) ? lower : null;
}
