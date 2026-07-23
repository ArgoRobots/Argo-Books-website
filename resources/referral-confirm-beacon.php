<?php
/**
 * JS-confirmation beacon for the referral funnel, shared by every layout that
 * renders public pages (resources/footer/footer.php and shared/layout.php).
 *
 * Confirms the current page view as coming from a real browser: the beacon
 * POSTs to api/referral/confirm.php, which flips js_confirmed = 1 on this
 * visitor's recent landing / downloads_page rows. Bots that never run JS stay
 * unconfirmed and are excluded from the marketing funnel, so keep this partial
 * as the single copy; a drifted or broken duplicate would silently undercount
 * real visitors in one layout.
 *
 * Expects $confirm_url_js: a JavaScript EXPRESSION (already quoted/encoded by
 * the caller) that evaluates to the confirm endpoint URL.
 */
if (!isset($confirm_url_js) || !is_string($confirm_url_js) || $confirm_url_js === '') {
    return; // Mis-included without a URL: render nothing rather than broken JS.
}
?>
<script>
// Confirms this page view as coming from a real browser (see
// api/referral/confirm.php). Bots that never run JS stay unconfirmed and are
// excluded from the marketing funnel. Fire-and-forget; never blocks the page.
(function () {
  try {
    var url = <?= $confirm_url_js ?>;
    if (navigator.sendBeacon) {
      navigator.sendBeacon(url, new Blob(['{}'], { type: 'application/json' }));
    } else {
      fetch(url, { method: 'POST', body: '{}', keepalive: true, credentials: 'same-origin' }).catch(function () {});
    }
  } catch (e) { /* analytics must never break the page */ }
})();
</script>
