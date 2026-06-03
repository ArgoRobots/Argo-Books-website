// Generic client-side search, shared by the documentation and the guides hub.
//
// Each page instantiates it with its own items and element ids:
//   new SiteSearch({
//     inputId: 'docSearchInput',
//     resultsId: 'searchResults',
//     items: [ { title, category, keywords, url, id? }, ... ]
//   });
//
// Every item carries its own destination `url`, so this engine stays free of
// any page-specific URL scheme. Fuzzy matching uses getSimilarity() from
// resources/scripts/levenshtein.js when present.
class SiteSearch {
    constructor(options = {}) {
        this.searchInput = document.getElementById(options.inputId || 'docSearchInput');
        this.searchResults = document.getElementById(options.resultsId || 'searchResults');
        this.pages = Array.isArray(options.items) ? options.items : [];
        this.selectedIndex = -1;
        this.currentResults = [];

        this.init();
    }

    init() {
        if (!this.searchInput || !this.searchResults) return;
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Keyboard navigation for search input
        this.searchInput.addEventListener('keydown', (e) => {
            const resultItems = this.searchResults.querySelectorAll('.search-result-item');
            const isResultsVisible = this.searchResults.classList.contains('active');

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
            }, 200);
        });

        // Show results on focus if there's a query
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value.length >= 2) {
                this.performSearch();
            }
        });

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            const searchContainer = this.searchInput.closest('.hero-search')
                || this.searchInput.closest('.subpage-search')
                || this.searchInput.closest('.search-container')
                || this.searchInput.closest('.guides-search');
            if (searchContainer && !searchContainer.contains(e.target)) {
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
        const similarityThreshold = 0.5;
        const queryWords = query.split(/\s+/).filter(w => w.length > 0);

        return this.pages
            .map(page => {
                const titleLower = (page.title || '').toLowerCase();
                const keywordsLower = (page.keywords || '').toLowerCase();
                const categoryLower = (page.category || '').toLowerCase();
                let score = 0;

                // Exact substring match (highest priority)
                if (titleLower.includes(query)) {
                    score += 100;
                }
                if (keywordsLower.includes(query)) {
                    score += 50;
                }
                if (categoryLower.includes(query)) {
                    score += 30;
                }

                // Check each query word
                const titleWords = titleLower.split(/\s+/);
                const keywordWords = keywordsLower.split(/\s+/);

                for (const qWord of queryWords) {
                    // Prefix matching (e.g., "instal" matches "installation")
                    if (titleWords.some(w => w.startsWith(qWord))) {
                        score += 40;
                    }
                    if (keywordWords.some(w => w.startsWith(qWord))) {
                        score += 20;
                    }

                    // Fuzzy matching using Levenshtein
                    if (typeof getSimilarity === 'function') {
                        const titleMatch = titleWords.some(w => getSimilarity(w, qWord) >= similarityThreshold);
                        const keywordMatch = keywordWords.some(w => getSimilarity(w, qWord) >= similarityThreshold);

                        if (titleMatch) score += 15;
                        if (keywordMatch) score += 10;
                    }
                }

                return { page, score };
            })
            .filter(item => item.score > 0)
            .sort((a, b) => b.score - a.score)
            .map(item => item.page);
    }

    getCategorySlug(category) {
        return (category || '').toLowerCase().replace(/\s+/g, '-');
    }

    displayResults(results, query) {
        this.currentResults = results;
        this.selectedIndex = -1;

        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="no-results">
                    <svg class="no-results-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                    <div class="no-results-title">No results found</div>
                    <div class="no-results-text">Try searching for something else</div>
                </div>
            `;
            this.showResults();
            return;
        }

        const resultsHtml = results.map(page => this.createResultItem(page, query)).join('');
        this.searchResults.innerHTML = resultsHtml;
        this.showResults();

        // Add click handlers to result items
        this.searchResults.querySelectorAll('.search-result-item').forEach((item, index) => {
            item.addEventListener('click', () => {
                this.navigateToPage(results[index]);
            });
        });
    }

    createResultItem(page, query) {
        const titleHighlighted = this.highlightText(page.title || '', query);
        const categorySlug = this.getCategorySlug(page.category);

        return `
            <div class="search-result-item" data-page="${page.id || ''}" data-category="${categorySlug}">
                <span class="search-result-section">${page.category || ''}</span>
                <span class="search-result-title">
                    <svg class="search-result-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>${titleHighlighted}</span>
                </span>
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
        window.location.href = page.url;
    }

    showResults() {
        this.searchResults.classList.add('active');
    }

    hideResults() {
        this.searchResults.classList.remove('active');
        this.selectedIndex = -1;
    }

    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
}

window.SiteSearch = SiteSearch;
