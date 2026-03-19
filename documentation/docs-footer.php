        </main>

        <!-- On This Page Sidebar -->
        <aside class="toc-sidebar" id="tocSidebar">
            <div class="toc-container">
                <h4 class="toc-heading">ON THIS PAGE</h4>
                <nav class="toc-nav" id="tocNav"></nav>
            </div>
        </aside>
    </div>

    <!-- Search overlay (triggered by Ctrl+K) -->
    <div class="search-overlay" id="searchOverlay">
        <div class="search-overlay-container search-container">
            <div class="search-input-wrapper">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="text" id="docSearchInput" placeholder="Search documentation..." aria-label="Search documentation" data-base-path="<?php echo $docsPath; ?>">
                <kbd class="search-shortcut">Esc</kbd>
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        // Keyboard shortcut for search overlay
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const overlay = document.getElementById('searchOverlay');
                const input = document.getElementById('docSearchInput');
                if (overlay && input) {
                    overlay.classList.add('active');
                    input.focus();
                }
            }
            if (e.key === 'Escape') {
                const overlay = document.getElementById('searchOverlay');
                if (overlay && overlay.classList.contains('active')) {
                    overlay.classList.remove('active');
                }
            }
        });

        // Close overlay on click outside
        document.getElementById('searchOverlay')?.addEventListener('click', (e) => {
            if (e.target === e.currentTarget) {
                e.currentTarget.classList.remove('active');
            }
        });
    </script>
</body>

</html>
