// invoice-template/scripts/template-page-tracker.js
// Tiny client-side hook that fires invgen_template_cta_clicked and
// invgen_template_download events when a template-page CTA is clicked.
// Mirrors invoice-generator/scripts/tracker.js sendBeacon pattern but
// kept separate so the live tool's main.js bundle is not loaded on every
// template landing page.

const BASE = (typeof window !== 'undefined' && window.INVGEN_BASE) || '';

function postEvent(eventType, eventData) {
  const url = `${BASE}/api/invoice-generator/track.php`;
  const payload = JSON.stringify({ event_type: eventType, event_data: eventData });
  try {
    if (navigator.sendBeacon) {
      const blob = new Blob([payload], { type: 'application/json' });
      navigator.sendBeacon(url, blob);
      return;
    }
  } catch (_e) {
    // Fall through to fetch fallback.
  }
  try {
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: payload,
      keepalive: true,
    });
  } catch (_e) {
    // Best-effort; swallow.
  }
}

document.addEventListener('click', (ev) => {
  const a = ev.target && ev.target.closest && ev.target.closest('[data-template-cta]');
  if (!a) return;
  const eventType = a.getAttribute('data-template-cta') || '';
  const style = a.getAttribute('data-template-style') || '';
  const format = a.getAttribute('data-template-format') || '';
  const data = `${style}|${format}`.slice(0, 200);
  if (eventType === 'invgen_template_cta_clicked' || eventType === 'invgen_template_download') {
    postEvent(eventType, data);
  }
});
