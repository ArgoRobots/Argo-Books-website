// invoice-generator/scripts/tracker.js
// Tiny client-side helper that fires events to /api/invoice-generator/track.php.
// Wraps navigator.sendBeacon when available so download-then-navigate events
// don't get cancelled, with a fetch fallback for older browsers.
//
// Failures are swallowed: a missing endpoint or blocked request must never
// break the tool. Allowed event_type values are the source of truth in
// api/invoice-generator/track.php; this file does not validate them.

const BASE = (typeof window !== 'undefined' && window.INVGEN_BASE) || '';
const ENDPOINT = `${BASE}/api/invoice-generator/track.php`;

export function trackEvent(eventType, eventData = '') {
  try {
    const payload = JSON.stringify({
      event_type: String(eventType || ''),
      event_data: eventData == null ? '' : String(eventData),
    });
    if (typeof navigator !== 'undefined' && navigator.sendBeacon) {
      const blob = new Blob([payload], { type: 'application/json' });
      navigator.sendBeacon(ENDPOINT, blob);
      return;
    }
    if (typeof fetch === 'function') {
      fetch(ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: payload,
        keepalive: true,
      }).catch(() => { /* swallow */ });
    }
  } catch (_e) {
    /* swallow */
  }
}
