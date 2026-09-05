// resources/scripts/toc-active.js
//
// Highlights the contents entry for the section currently on screen.
//
// Works from the links already in the markup rather than building a list from
// headings, so the server stays the single source of truth for what is in the
// contents. Positions come from getBoundingClientRect rather than offsetTop:
// the article body sits in a grid column, so offsetTop is measured against that
// column rather than the page and gives the wrong answer.

(function () {
  var nav = document.querySelector('.article-toc');
  if (!nav) return;

  var links = Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]'));
  if (links.length < 2) return;

  var targets = links
    .map(function (link) {
      var el = document.getElementById(decodeURIComponent(link.hash.slice(1)));
      return el ? { link: link, el: el } : null;
    })
    .filter(Boolean);

  if (targets.length < 2) return;

  // Roughly where the reader's eye sits. A section counts as current once its
  // heading passes this line, not once it reaches the very top of the window.
  var TRIGGER = 120;
  var current = null;

  function update() {
    var active = targets[0];

    for (var i = 0; i < targets.length; i++) {
      if (targets[i].el.getBoundingClientRect().top <= TRIGGER) {
        active = targets[i];
      }
    }

    // At the bottom of the page the last section may never cross the trigger,
    // so the final entry would never light up. Force it.
    if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 2) {
      active = targets[targets.length - 1];
    }

    if (active === current) return;
    current = active;

    targets.forEach(function (t) {
      t.link.classList.toggle('is-active', t === active);
      if (t === active) {
        t.link.setAttribute('aria-current', 'true');
      } else {
        t.link.removeAttribute('aria-current');
      }
    });
  }

  var queued = false;
  function onScroll() {
    if (queued) return;
    queued = true;
    window.requestAnimationFrame(function () {
      queued = false;
      update();
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  update();
})();
