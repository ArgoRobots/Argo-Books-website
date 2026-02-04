document.addEventListener("DOMContentLoaded", function () {
  const sidebarToggle = document.getElementById("sidebarToggle");
  const sidebar = document.querySelector(".sidebar");

  const isMobile = () => window.innerWidth <= 1024;

  const toggleSidebar = () => {
    sidebar.classList.toggle("active");
    sidebarToggle.classList.toggle("active");
  };

  const closeSidebar = () => {
    if (isMobile() && sidebar.classList.contains("active")) {
      toggleSidebar();
    }
  };

  // Add click event to toggle button
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", toggleSidebar);
  }

  // Close sidebar when clicking outside on mobile
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

  // Handle resize events
  let lastWidth = window.innerWidth;
  window.addEventListener("resize", () => {
    if (lastWidth <= 1024 && window.innerWidth > 1024) {
      // Switching to desktop
      if (sidebar) {
        sidebar.classList.remove("active");
      }
      if (sidebarToggle) {
        sidebarToggle.classList.remove("active");
      }
    }
    lastWidth = window.innerWidth;
  });

  // Close sidebar when clicking a nav link on mobile
  const navLinks = document.querySelectorAll(".nav-links a");
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      closeSidebar();
    });
  });
});
