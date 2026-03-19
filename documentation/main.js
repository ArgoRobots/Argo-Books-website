document.addEventListener("DOMContentLoaded", function () {
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
        // Generate ID from heading text
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
        const scrollPos = window.scrollY + 120;
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
      // Hide TOC sidebar if no headings
      const tocSidebar = document.getElementById("tocSidebar");
      if (tocSidebar) tocSidebar.style.display = "none";
    }
  }
});
