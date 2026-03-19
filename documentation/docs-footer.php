        </main>

        <!-- On This Page Sidebar -->
        <aside class="toc-sidebar" id="tocSidebar">
            <div class="toc-container">
                <h4 class="toc-heading">ON THIS PAGE</h4>
                <nav class="toc-nav" id="tocNav"></nav>
            </div>
        </aside>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        // Keyboard shortcut for search
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('docSearchInput');
                if (searchInput) searchInput.focus();
            }
        });
    </script>
</body>

</html>
