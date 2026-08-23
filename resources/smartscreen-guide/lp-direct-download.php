<?php
/**
 * Direct-download upgrade for the paid landing pages.
 *
 * Progressive enhancement: every ".js-direct-download" CTA links to the
 * downloads page in the HTML, so Mac, Linux, mobile, and no-JS visitors keep
 * today's behavior. On Windows desktops this script rewrites those CTAs into
 * one-click "Download for Windows" buttons that hit the installer directly
 * (skipping the downloads-page hop) and then reveals the SmartScreen
 * walkthrough rendered by guide.php.
 *
 * Expects before include:
 *   $cta_source (string) the page's referral source code, e.g. 'paid-lp-contractors'
 *
 * The ?source= is honored by get_avalonia_installer.php as a session fallback
 * so the download_click funnel event stays attributed even without a prior
 * tracked landing.
 */
if (!isset($cta_source) || !is_string($cta_source) || $cta_source === '') {
    return; // Mis-included without a source: render nothing.
}
?>
<script>
(function () {
    var ua = navigator.userAgent || '';
    // Windows desktops only; everyone else keeps the downloads-page link.
    if (ua.indexOf('Windows') === -1 || /Mobile|Android/i.test(ua)) return;

    var directUrl = '../download/avalonia/win?source=' + encodeURIComponent(<?= json_encode($cta_source) ?>);
    var guides = document.getElementById('downloadGuides');

    document.querySelectorAll('a.js-direct-download').forEach(function (btn) {
        btn.setAttribute('href', directUrl);
        var label = btn.querySelector('span');
        if (label) label.textContent = 'Download for Windows';

        btn.addEventListener('click', function () {
            // Same events the downloads page fires on a Windows download click.
            if (typeof gtag !== 'undefined') {
                gtag('event', 'download_click', {
                    'event_category': 'software',
                    'event_label': 'argo_books_windows',
                    'platform': 'windows'
                });
                gtag('event', 'conversion', {'send_to': 'AW-17210317271/niGZCJv2vbkbENezwo5A'});
            }

            if (guides) {
                guides.hidden = false;
                requestAnimationFrame(function () {
                    guides.querySelectorAll('.smartscreen-guide')
                        .forEach(function (g) { g.classList.add('is-visible'); });
                    setTimeout(function () {
                        var targetY = guides.getBoundingClientRect().top
                            + window.pageYOffset - 130;
                        window.scrollTo({ top: targetY, behavior: 'smooth' });
                    }, 120);
                });
            }
        });
    });
})();
</script>
