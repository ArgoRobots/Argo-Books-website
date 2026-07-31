// admin/preserve-scroll.js
//
// Keeps the scroll position across the full page reload that admin filter
// controls trigger, so changing a period or a range pill does not throw the
// user back to the top of a long dashboard.
//
// Six admin pages each carried their own copy of this. The restore half was
// identical everywhere; only the trigger selector differed, which is what the
// per-page ADMIN_PRESERVE_SCROLL list supplies.
//
// Usage, before this script:
//   <script>window.ADMIN_PRESERVE_SCROLL = ['a[href^="?range="]'];</script>
//   <script src="../preserve-scroll.js" defer></script>
//
// Any element also works without listing a selector by marking it directly:
//   <a href="?period=30d" data-preserve-scroll>30 days</a>
//   <form id="rangeForm" data-preserve-scroll>...</form>
//
// Listeners are delegated, so controls rendered after load are covered too.

(function () {
  var KEY = 'scrollPosition';

  function save() {
    try { sessionStorage.setItem(KEY, window.scrollY); } catch (e) { /* private mode */ }
  }

  function restore() {
    try {
      var y = sessionStorage.getItem(KEY);
      if (y === null) return;
      sessionStorage.removeItem(KEY);
      window.scrollTo(0, parseInt(y, 10) || 0);
    } catch (e) { /* private mode */ }
  }

  function selectors() {
    var list = window.ADMIN_PRESERVE_SCROLL;
    return Array.isArray(list) ? list.concat('[data-preserve-scroll]') : ['[data-preserve-scroll]'];
  }

  document.addEventListener('click', function (event) {
    if (!event.target.closest) return;
    var hit = selectors().some(function (sel) {
      try { return event.target.closest(sel); } catch (e) { return false; }
    });
    if (hit) save();
  });

  // Range pickers submit a form rather than following a link.
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (form && form.matches && form.matches('form[data-preserve-scroll]')) save();
  }, true);

  // The script is loaded with defer, so the document has parsed by now, but
  // guard anyway in case a caller drops the attribute.
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', restore);
  } else {
    restore();
  }
})();
