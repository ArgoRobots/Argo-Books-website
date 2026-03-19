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
