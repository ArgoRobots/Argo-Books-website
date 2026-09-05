// resources/scripts/theme-toggle.js
//
// Three-state theme control: device, light, dark.
//
// "device" is the default and is stored as the absence of a preference, so a
// reader who has never touched the control keeps following their OS even if
// they change it later.
//
// The theme is applied by a small inline script in the document head, not
// here. A deferred script runs after first paint, so the page would render
// light and then flip. This file only handles clicks and keeps the buttons in
// step with what the head script decided.

(function () {
  var KEY = 'argo-theme';
  var VALID = ['device', 'light', 'dark'];

  function stored() {
    try {
      var v = localStorage.getItem(KEY);
      return VALID.indexOf(v) === -1 ? 'device' : v;
    } catch (e) {
      return 'device';
    }
  }

  function prefersDark() {
    return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
  }

  /** The choice resolved against the OS: what actually gets painted. */
  function resolve(choice) {
    if (choice === 'dark') return 'dark';
    if (choice === 'light') return 'light';
    return prefersDark() ? 'dark' : 'light';
  }

  function paint(choice) {
    if (resolve(choice) === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
    } else {
      document.documentElement.removeAttribute('data-theme');
    }

    document.querySelectorAll('.theme-option').forEach(function (btn) {
      var on = btn.dataset.theme === choice;
      btn.classList.toggle('is-active', on);
      btn.setAttribute('aria-checked', on ? 'true' : 'false');
      btn.tabIndex = on ? 0 : -1;
    });
  }

  document.addEventListener('click', function (event) {
    var btn = event.target.closest ? event.target.closest('.theme-option') : null;
    if (!btn) return;

    var choice = btn.dataset.theme;
    if (VALID.indexOf(choice) === -1) return;

    paint(choice);

    try {
      if (choice === 'device') {
        // Absence of a value is what "follow the device" means, so the head
        // script does not need to understand a third stored state.
        localStorage.removeItem(KEY);
      } else {
        localStorage.setItem(KEY, choice);
      }
    } catch (e) {
      // Private browsing or storage disabled. The choice still applies to this
      // page view, it just is not remembered.
    }
  });

  // Left/right arrows move between the options, as a radio group should.
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
    var btn = event.target.closest ? event.target.closest('.theme-option') : null;
    if (!btn) return;

    var group = Array.prototype.slice.call(btn.parentNode.querySelectorAll('.theme-option'));
    var i = group.indexOf(btn);
    var next = group[(i + (event.key === 'ArrowRight' ? 1 : group.length - 1)) % group.length];
    if (next) {
      next.click();
      next.focus();
      event.preventDefault();
    }
  });

  paint(stored());

  // Follow the OS live, but only while the reader is on "device".
  if (window.matchMedia) {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    var onChange = function () {
      if (stored() === 'device') paint('device');
    };
    if (mq.addEventListener) {
      mq.addEventListener('change', onChange);
    } else if (mq.addListener) {
      mq.addListener(onChange);
    }
  }
})();
