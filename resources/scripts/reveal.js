// resources/scripts/reveal.js
//
// Scroll-triggered reveal for the marketing pages. Load it with `defer`; it
// runs once the document has parsed and needs no wrapper.
//
// Two class conventions grew up separately and both are still in the markup:
//
//   .animate-on-scroll -> .animate-visible   (home, compare, for-*, tools, ...)
//   .fp-reveal         -> .is-in             (features/*, integrations/*)
//
// Each kept its own threshold, so the trigger points below match what the
// pages did before this file existed. Forty-eight pages carried their own copy
// of the observer; only the two class names and the thresholds ever differed.
//
// The .animate-on-scroll copies had no reduced-motion branch and never
// unobserved. Both behaviours are applied to each convention here, so a
// visitor who asks for reduced motion gets the content with no animation
// rather than a page that fades things in as they scroll.

(function () {
  var GROUPS = [
    { selector: '.animate-on-scroll', shown: 'animate-visible', threshold: 0.1, rootMargin: '0px 0px -50px 0px' },
    { selector: '.fp-reveal', shown: 'is-in', threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
  ];

  // Without an observer the elements would sit at opacity 0 forever, so the
  // fallback is to show everything rather than to animate nothing.
  var canObserve = 'IntersectionObserver' in window &&
    !(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);

  GROUPS.forEach(function (group) {
    var targets = document.querySelectorAll(group.selector);
    if (!targets.length) return;

    if (!canObserve) {
      targets.forEach(function (el) { el.classList.add(group.shown); });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add(group.shown);
        observer.unobserve(entry.target);
      });
    }, { threshold: group.threshold, rootMargin: group.rootMargin });

    targets.forEach(function (el) { observer.observe(el); });
  });
})();
