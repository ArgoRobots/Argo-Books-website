document.addEventListener("DOMContentLoaded", function () {
  const STORAGE_KEY_SECTIONS = "docs-sidebar-sections";
  const STORAGE_KEY_SCROLL = "docs-sidebar-scroll";

  // ===== Sidebar Mobile Toggle =====
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.getElementById("docsSidebar");

  const isMobile = () => window.innerWidth <= 1024;

  const toggleSidebar = () => {
    if (sidebar) sidebar.classList.toggle("active");
    if (sidebarToggle) sidebarToggle.classList.toggle("active");
  };

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", toggleSidebar);
  }

  document.addEventListener("click", (e) => {
    if (
      isMobile() &&
      sidebar &&
      !sidebar.contains(e.target) &&
      sidebarToggle &&
      !sidebarToggle.contains(e.target) &&
      sidebar.classList.contains("active")
    ) {
      toggleSidebar();
    }
  });

  let lastWidth = window.innerWidth;
  window.addEventListener("resize", () => {
    if (lastWidth <= 1024 && window.innerWidth > 1024) {
      if (sidebar) sidebar.classList.remove("active");
      if (sidebarToggle) sidebarToggle.classList.remove("active");
    }
    lastWidth = window.innerWidth;
  });

  // Close sidebar when clicking a nav link on mobile
  const navLinks = document.querySelectorAll(".nav-links a");
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (isMobile() && sidebar && sidebar.classList.contains("active")) {
        toggleSidebar();
      }
    });
  });

  // ===== Sidebar State Persistence =====
  const sections = document.querySelectorAll(".nav-section");

  function getSectionName(section) {
    const toggle = section.querySelector(".nav-section-toggle span");
    return toggle ? toggle.textContent.trim() : "";
  }

  function saveSectionState() {
    const expanded = [];
    sections.forEach((section) => {
      if (section.classList.contains("expanded")) {
        expanded.push(getSectionName(section));
      }
    });
    try {
      localStorage.setItem(STORAGE_KEY_SECTIONS, JSON.stringify(expanded));
    } catch (e) {}
  }

  function saveScrollPosition() {
    if (sidebar) {
      try {
        sessionStorage.setItem(STORAGE_KEY_SCROLL, String(sidebar.scrollTop));
      } catch (e) {}
    }
  }

  // Restore section expanded/collapsed state from localStorage
  function restoreSectionState() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY_SECTIONS);
      if (stored) {
        const expanded = JSON.parse(stored);
        sections.forEach((section) => {
          const name = getSectionName(section);
          const toggle = section.querySelector(".nav-section-toggle");
          const hasActivePage = section.querySelector(".nav-links a.active");

          if (expanded.includes(name) || hasActivePage) {
            section.classList.add("expanded");
            if (toggle) toggle.setAttribute("aria-expanded", "true");
          } else {
            section.classList.remove("expanded");
            if (toggle) toggle.setAttribute("aria-expanded", "false");
          }
        });
      }
    } catch (e) {}
  }

  // Restore sidebar scroll position from sessionStorage
  function restoreScrollPosition() {
    if (sidebar) {
      try {
        const scrollPos = sessionStorage.getItem(STORAGE_KEY_SCROLL);
        if (scrollPos !== null) {
          sidebar.scrollTop = parseInt(scrollPos, 10);
        }
      } catch (e) {}
    }
  }

  // Disable transitions, restore state, then re-enable transitions
  if (sidebar) {
    sidebar.classList.add("no-transition");
  }
  restoreSectionState();

  // Force layout recalc so expanded sections have full height before restoring scroll
  if (sidebar) {
    void sidebar.offsetHeight;
  }
  restoreScrollPosition();

  // Re-enable transitions after a frame
  requestAnimationFrame(() => {
    if (sidebar) {
      sidebar.classList.remove("no-transition");
    }
  });

  // Save scroll position before navigating away
  window.addEventListener("beforeunload", saveScrollPosition);

  // Also save scroll on every nav link click (backup for beforeunload)
  navLinks.forEach((link) => {
    link.addEventListener("click", saveScrollPosition);
  });

  // ===== Sidebar Section Toggle =====
  const sectionToggles = document.querySelectorAll(".nav-section-toggle");
  sectionToggles.forEach((toggle) => {
    toggle.addEventListener("click", () => {
      const section = toggle.closest(".nav-section");
      section.classList.toggle("expanded");
      toggle.setAttribute(
        "aria-expanded",
        section.classList.contains("expanded")
      );
      saveSectionState();
    });
  });

  // ===== Table of Contents Generation =====
  const tocNav = document.getElementById("tocNav");
  const content = document.querySelector(".docs-content");

  if (tocNav && content) {
    const headings = content.querySelectorAll("h2, h3");

    if (headings.length > 0) {
      const tocList = document.createElement("ul");
      tocList.className = "toc-list";

      headings.forEach((heading) => {
        if (!heading.id) {
          heading.id = heading.textContent
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-|-$/g, "");
        }

        const li = document.createElement("li");
        li.className =
          heading.tagName === "H3" ? "toc-item toc-h3" : "toc-item";

        const a = document.createElement("a");
        a.href = "#" + heading.id;
        a.textContent = heading.textContent;
        a.className = "toc-link";

        a.addEventListener("click", (e) => {
          e.preventDefault();
          heading.scrollIntoView({ behavior: "smooth", block: "start" });
          history.replaceState(null, null, "#" + heading.id);
        });

        li.appendChild(a);
        tocList.appendChild(li);
      });

      tocNav.appendChild(tocList);

      // ===== Scroll Spy =====
      const tocLinks = tocNav.querySelectorAll(".toc-link");

      const updateActiveLink = () => {
        const scrollPos = window.scrollY + 70;
        let activeIndex = 0;

        headings.forEach((heading, index) => {
          if (heading.offsetTop <= scrollPos) {
            activeIndex = index;
          }
        });

        tocLinks.forEach((link, index) => {
          link.classList.toggle("active", index === activeIndex);
        });
      };

      window.addEventListener("scroll", updateActiveLink);
      updateActiveLink();
    } else {
      const tocSidebar = document.getElementById("tocSidebar");
      if (tocSidebar) tocSidebar.style.display = "none";
    }
  }
});

/**
 * Copy button on documentation code blocks.
 *
 * The source is read from the block's data-code attribute rather than from the
 * rendered text, so the syntax-highlighting markup can never end up in what the
 * reader pastes. Delegated from the document so it also covers blocks that are
 * added after load.
 */
document.addEventListener("click", async (event) => {
  const button = event.target.closest(".code-copy");
  if (!button) return;

  const block = button.closest(".code-block");
  if (!block) return;

  const label = button.querySelector(".code-copy-text");

  // A tabbed block keeps the source on each variant, so copy whichever one the
  // reader is actually looking at rather than the block as a whole.
  const active = block.querySelector(".code-variant.is-active");
  const source = (active || block).getAttribute("data-code") || "";

  const show = (text) => {
    if (label) label.textContent = text;
    button.classList.add("is-copied");
    setTimeout(() => {
      if (label) label.textContent = "Copy";
      button.classList.remove("is-copied");
    }, 1600);
  };

  try {
    await navigator.clipboard.writeText(source);
    show("Copied");
  } catch {
    // Clipboard access is refused on insecure origins and in some browsers, so
    // fall back to the old selection trick rather than leaving the button dead.
    const scratch = document.createElement("textarea");
    scratch.value = source;
    scratch.setAttribute("readonly", "");
    scratch.style.position = "fixed";
    scratch.style.opacity = "0";
    document.body.appendChild(scratch);
    scratch.select();
    try {
      document.execCommand("copy");
      show("Copied");
    } catch {
      show("Press Ctrl+C");
    }
    document.body.removeChild(scratch);
  }
});

/**
 * Language tabs on documentation code blocks.
 *
 * The choice is remembered and applied to every block on every page, because a
 * C# developer should pick C# once rather than on each example. Blocks that do
 * not offer the remembered language keep their own first tab rather than
 * showing nothing.
 */
(() => {
  const STORAGE_KEY = "argoDocsCodeLang";

  const selectVariant = (block, variant) => {
    const panes = block.querySelectorAll(".code-variant");
    const tabs = block.querySelectorAll(".code-tab");
    let matched = false;

    panes.forEach((pane) => {
      const isMatch = pane.dataset.variant === variant;
      pane.classList.toggle("is-active", isMatch);
      if (isMatch) matched = true;
    });

    tabs.forEach((tab) => {
      const isMatch = tab.dataset.variant === variant;
      tab.classList.toggle("is-active", isMatch);
      tab.setAttribute("aria-selected", isMatch ? "true" : "false");
    });

    return matched;
  };

  const applyStoredLanguage = () => {
    let stored = null;
    try {
      stored = localStorage.getItem(STORAGE_KEY);
    } catch {
      return; // storage blocked; the server-rendered default stands
    }
    if (!stored) return;

    document.querySelectorAll(".code-block-tabbed").forEach((block) => {
      // Leave the block on its own default when it has no such variant.
      if (!block.querySelector(`.code-variant[data-variant="${CSS.escape(stored)}"]`)) return;
      selectVariant(block, stored);
    });
  };

  document.addEventListener("click", (event) => {
    const tab = event.target.closest(".code-tab");
    if (!tab) return;

    const block = tab.closest(".code-block-tabbed");
    if (!block) return;

    const variant = tab.dataset.variant;
    selectVariant(block, variant);

    // Every other block follows along, so scrolling the page does not mean
    // re-picking the language over and over.
    document.querySelectorAll(".code-block-tabbed").forEach((other) => {
      if (other !== block) selectVariant(other, variant);
    });

    try {
      localStorage.setItem(STORAGE_KEY, variant);
    } catch {
      // Not being able to remember the choice is not worth surfacing.
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", applyStoredLanguage);
  } else {
    applyStoredLanguage();
  }
})();
