// Microsoft Advertising UET tag (ti: 187252936)
(function (w, d, t, u, o) {
  w[u] = w[u] || []; o.ts = (new Date).getTime();
  var n = d.createElement(t);
  n.src = "https://bat.bing.net/bat.js?ti=" + o.ti + ("uetq" != u ? "&q=" + u : "");
  n.async = 1; n.onload = n.onreadystatechange = function () {
    var s = this.readyState;
    s && "loaded" !== s && "complete" !== s ||
      (o.q = w[u], w[u] = new UET(o), w[u].push("pageLoad"),
        n.onload = n.onreadystatechange = null);
  };
  var i = d.getElementsByTagName(t)[0];
  i.parentNode.insertBefore(n, i);
})(window, document, "script", "uetq", { ti: "187252936", enableAutoSpaTracking: true });

// Google Ads gtag (AW-17210317271)
(function () {
  var s = document.createElement("script");
  s.async = true;
  s.src = "https://www.googletagmanager.com/gtag/js?id=AW-17210317271";
  var first = document.getElementsByTagName("script")[0];
  first.parentNode.insertBefore(s, first);
  window.dataLayer = window.dataLayer || [];
  window.gtag = function () { window.dataLayer.push(arguments); };
  window.gtag("js", new Date());
  window.gtag("config", "AW-17210317271");
})();

// Detect the base path for the application
// This handles both production (root) and local XAMPP (subfolder)
function getBasePath() {
  var path = window.location.pathname;

  // Check if we're in a subfolder (common XAMPP setup)
  // Look for common local folder names
  var match = path.match(/^(\/[\w-]+\/)/);

  // If the path doesn't start with common site paths, assume we're in a subfolder
  var sitePaths = ['/features/', '/pricing/', '/community/', '/documentation/', '/about-us/',
                   '/contact-us/', '/whats-new/', '/admin/', '/legal/', '/resources/',
                   '/error-pages/', '/older-versions/', '/downloads/', '/portal/',
                   '/invoice/', '/api/', '/compare/', '/unsubscribe/', '/review/',
                   '/for-landscapers/', '/for-contractors/', '/for-repair-shops/',
                   '/for-rental-businesses/', '/for-cleaning-companies/',
                   '/for-local-wholesalers/', '/for-resellers/',
                   '/for-auto-detailing/', '/for-solo-operators/'];

  var isRootPath = sitePaths.some(function(p) { return path.startsWith(p); }) || path === '/' || path === '/index.php';

  if (!isRootPath && match) {
    return match[1]; // Return the subfolder path (e.g., '/Argo-Books-website/')
  }

  return '/'; // Production or root-level local setup
}

var BASE_PATH = getBasePath();

function setDefaultAvatar() {
  const accountAvatar = document.querySelector(".account-avatar");
  if (accountAvatar) {
    accountAvatar.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
           viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>`;
  }
}

// Fix all root-relative links to work with BASE_PATH
function fixLinks(container) {
  $(container + " a").each(function () {
    var href = $(this).attr("href");
    // Only fix links that start with / but not // (protocol-relative)
    if (href && href.startsWith("/") && !href.startsWith("//") && BASE_PATH !== "/") {
      $(this).attr("href", BASE_PATH + href.substring(1));
    }
  });

  $(container + " img").each(function () {
    var src = $(this).attr("src");
    // Only fix images that start with / but not // (protocol-relative)
    if (src && src.startsWith("/") && !src.startsWith("//") && BASE_PATH !== "/") {
      $(this).attr("src", BASE_PATH + src.substring(1));
    }
  });
}

// Load header and footer with dynamic base path
$(document).ready(function () {
  $("#includeHeader").load(BASE_PATH + "resources/header/index.html", function () {
    fixLinks("#includeHeader");

    // Load the avatar after the header is loaded
    const accountAvatar = document.querySelector(".account-avatar");
    fetch(BASE_PATH + "community/get_avatar_info.php")
      .then((response) => response.json())
      .then((data) => {
        if (accountAvatar && data.logged_in && data.has_avatar) {
          // Apply BASE_PATH to avatar URL if it starts with /
          var avatarUrl = data.avatar_url;
          if (avatarUrl && avatarUrl.startsWith("/") && !avatarUrl.startsWith("//") && BASE_PATH !== "/") {
            avatarUrl = BASE_PATH + avatarUrl.substring(1);
          }
          accountAvatar.innerHTML = `<img src="${avatarUrl}" alt="Profile">`;
        }
      })
      .catch((error) => {
        console.error("Error fetching avatar info:", error);
        setDefaultAvatar();
      });
  });

  $("#includeFooter").load(BASE_PATH + "resources/footer/index.html", function () {
    fixLinks("#includeFooter");
  });

  // Load cursor orb script
  var cursorOrbScript = document.createElement('script');
  cursorOrbScript.src = BASE_PATH + "resources/scripts/cursor-orb.js";
  cursorOrbScript.defer = true;
  document.head.appendChild(cursorOrbScript);
});
