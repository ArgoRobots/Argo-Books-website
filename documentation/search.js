class DocumentationSearch {
    constructor() {
        this.searchInput = document.getElementById('docSearchInput');
        this.searchButton = document.getElementById('searchButton');
        this.searchResults = document.getElementById('searchResults');
        this.selectedIndex = -1;
        this.currentResults = [];

        // Static index of all documentation pages
        this.pages = [
            // Getting Started
            { id: 'system-requirements', title: 'System Requirements', category: 'Getting Started', folder: 'getting-started', keywords: 'windows macos linux requirements specs hardware disk space ram memory' },
            { id: 'installation', title: 'Installation Guide', category: 'Getting Started', folder: 'getting-started', keywords: 'install download setup installer wizard run' },
            { id: 'quick-start', title: 'Quick Start Tutorial', category: 'Getting Started', folder: 'getting-started', keywords: 'tutorial getting started begin first steps currency company accountant category product' },
            { id: 'version-comparison', title: 'Free vs. Paid Version', category: 'Getting Started', folder: 'getting-started', keywords: 'free paid premium upgrade features comparison limited unlimited products windows hello ai search' },

            // Core Features
            { id: 'product-management', title: 'Product Management', category: 'Core Features', folder: 'features', keywords: 'products categories inventory add create manage organize' },
            { id: 'sales-tracking', title: 'Purchase/Sales Tracking', category: 'Core Features', folder: 'features', keywords: 'purchase sale transaction order tracking add quantity price shipping tax' },
            { id: 'receipts', title: 'Receipt Management', category: 'Core Features', folder: 'features', keywords: 'receipt digital scan microsoft lens export attach' },
            { id: 'spreadsheet-import', title: 'Spreadsheet Import', category: 'Core Features', folder: 'features', keywords: 'import excel spreadsheet xlsx csv data accountants companies products purchases sales currency' },
            { id: 'spreadsheet-export', title: 'Spreadsheet Export', category: 'Core Features', folder: 'features', keywords: 'export excel spreadsheet xlsx backup data currency conversion chart' },
            { id: 'report-generator', title: 'Report Generator', category: 'Core Features', folder: 'features', keywords: 'report generate pdf png jpg chart analytics template layout designer' },
            { id: 'advanced-search', title: 'Advanced Search', category: 'Core Features', folder: 'features', keywords: 'search find filter operators quotes exact phrase plus minus exclude ai natural language' },

            // Reference
            { id: 'accepted-countries', title: 'Accepted Countries', category: 'Reference', folder: 'reference', keywords: 'country countries iso code variant name import us usa uk germany' },
            { id: 'supported-currencies', title: 'Supported Currencies', category: 'Reference', folder: 'reference', keywords: 'currency currencies usd eur gbp cad jpy cny exchange rate convert' },
            { id: 'supported-languages', title: 'Supported Languages', category: 'Reference', folder: 'reference', keywords: 'language languages english spanish french german chinese arabic localization translation' },

            // Security
            { id: 'encryption', title: 'Encryption', category: 'Security', folder: 'security', keywords: 'encryption aes-256 security protect data' },
            { id: 'password', title: 'Password Protection', category: 'Security', folder: 'security', keywords: 'password protection windows hello fingerprint face login security' },
            { id: 'backups', title: 'Regular Backups', category: 'Security', folder: 'security', keywords: 'backup export save data loss protection cloud' },
            { id: 'anonymous-data', title: 'Anonymous Usage Data', category: 'Security', folder: 'security', keywords: 'anonymous usage data privacy statistics telemetry collection disable' }
        ];

        this.init();
    }

    init() {
        if (!this.searchInput || !this.searchResults) return;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Search on button click
        this.searchButton.addEventListener('click', () => this.performSearch());

        // Keyboard navigation for search input
        this.searchInput.addEventListener('keydown', (e) => {
            const resultItems = this.searchResults.querySelectorAll('.search-result-item');
            const isResultsVisible = this.searchResults.style.display === 'block';

            if (!isResultsVisible || resultItems.length === 0) {
                if (e.key === 'Enter') {
                    this.performSearch();
                }
                return;
            }

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this.navigateResults(1);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.navigateResults(-1);
                    break;
                case 'Tab':
                    e.preventDefault();
                    this.navigateResults(e.shiftKey ? -1 : 1);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (this.selectedIndex >= 0 && this.selectedIndex < this.currentResults.length) {
                        this.navigateToPage(this.currentResults[this.selectedIndex]);
                    } else if (this.currentResults.length > 0) {
                        this.navigateToPage(this.currentResults[0]);
                    }
                    break;
                case 'Escape':
                    this.hideResults();
                    this.searchInput.blur();
                    break;
            }
        });

        // Real-time search as user types (with debounce)
        let timeout;
        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (e.target.value.length >= 2) {
                    this.performSearch();
                } else {
                    this.hideResults();
                }
            }, 300);
        });

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.searchInput.contains(e.target) && !this.searchResults.contains(e.target)) {
                this.hideResults();
            }
        });
    }

    performSearch() {
        const query = this.searchInput.value.trim().toLowerCase();

        if (query.length < 2) {
            this.hideResults();
            return;
        }

        const results = this.searchPages(query);
        this.displayResults(results, query);
    }

    searchPages(query) {
        const similarityThreshold = 0.6;

        return this.pages.filter(page => {
            const titleLower = page.title.toLowerCase();
            const keywordsLower = page.keywords.toLowerCase();
            const categoryLower = page.category.toLowerCase();

            // Exact match check first
            if (titleLower.includes(query) || keywordsLower.includes(query) || categoryLower.includes(query)) {
                return true;
            }

            // Fuzzy matching for title words
            const titleWords = titleLower.split(/\s+/);
            const titleMatch = titleWords.some(word =>
                typeof getSimilarity === 'function' && getSimilarity(word, query) >= similarityThreshold
            );

            if (titleMatch) return true;

            // Fuzzy matching for keywords
            const keywordWords = keywordsLower.split(/\s+/);
            const keywordMatch = keywordWords.some(word =>
                typeof getSimilarity === 'function' && getSimilarity(word, query) >= similarityThreshold
            );

            return keywordMatch;
        });
    }

    displayResults(results, query) {
        this.currentResults = results;
        this.selectedIndex = -1;

        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="no-results">
                    <p>No results found for "<strong>${this.escapeHtml(query)}</strong>"</p>
                    <p style="margin-top: 0.5rem; font-size: 0.875rem;">Try different keywords or browse the documentation menu.</p>
                </div>
            `;
            this.searchResults.style.display = 'block';
            return;
        }

        const resultsHtml = results.map(page => this.createResultItem(page, query)).join('');
        this.searchResults.innerHTML = resultsHtml;
        this.searchResults.style.display = 'block';

        // Add click handlers to result items
        this.searchResults.querySelectorAll('.search-result-item').forEach((item, index) => {
            item.addEventListener('click', () => {
                this.navigateToPage(results[index]);
            });
        });
    }

    createResultItem(page, query) {
        const titleHighlighted = this.highlightText(page.title, query);

        return `
            <div class="search-result-item" data-page="${page.id}">
                <div class="search-result-title">
                    <svg class="search-result-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    ${titleHighlighted}
                </div>
                <div class="search-result-section">${page.category}</div>
            </div>
        `;
    }

    highlightText(text, query) {
        const regex = new RegExp(`(${this.escapeRegex(query)})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    navigateResults(direction) {
        const resultItems = this.searchResults.querySelectorAll('.search-result-item');
        if (resultItems.length === 0) return;

        this.selectedIndex += direction;

        if (this.selectedIndex < 0) {
            this.selectedIndex = resultItems.length - 1;
        } else if (this.selectedIndex >= resultItems.length) {
            this.selectedIndex = 0;
        }

        this.updateSelection();
    }

    updateSelection() {
        const resultItems = this.searchResults.querySelectorAll('.search-result-item');

        resultItems.forEach(item => item.classList.remove('selected'));

        if (this.selectedIndex >= 0 && this.selectedIndex < resultItems.length) {
            const selectedItem = resultItems[this.selectedIndex];
            selectedItem.classList.add('selected');

            selectedItem.scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }

    navigateToPage(page) {
        window.location.href = `pages/${page.folder}/${page.id}.php`;
    }

    hideResults() {
        this.searchResults.style.display = 'none';
        this.selectedIndex = -1;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}

// Initialize search when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new DocumentationSearch();
});
