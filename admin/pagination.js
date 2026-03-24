/**
 * Shared client-side table pagination for admin pages.
 *
 * Usage (auto-init):
 *   <table data-paginate="25"> ... </table>
 *
 * Usage (manual):
 *   const p = new TablePaginator(tableEl, { perPage: 25 });
 *   p.reset();   // after filtering / re-rendering
 */
class TablePaginator {
    constructor(tableEl, options = {}) {
        this.table = tableEl;
        this.tbody = tableEl.querySelector('tbody');
        if (!this.tbody) return;
        this.perPage = options.perPage || 25;
        this.currentPage = 1;
        this.controlsEl = document.createElement('div');
        this.controlsEl.className = 'pagination-controls';

        // Insert controls after the table's scroll wrapper or parent
        const wrapper = tableEl.closest('.table-responsive') || tableEl.closest('.leads-table-wrapper') || tableEl.closest('.discovery-table-wrapper') || tableEl.parentElement;
        wrapper.after(this.controlsEl);

        this.update();
    }

    /** Returns rows that are NOT hidden by external filters. */
    _rows() {
        return Array.from(this.tbody.querySelectorAll('tr')).filter(r => {
            // Skip rows hidden by an external filter (display:none without our class)
            if (r.style.display === 'none' && !r.classList.contains('pg-hidden')) return false;
            // Skip empty-state placeholder rows
            if (r.querySelector('.empty-state')) return false;
            return true;
        });
    }

    update() {
        const rows = this._rows();
        const totalPages = Math.max(1, Math.ceil(rows.length / this.perPage));
        if (this.currentPage > totalPages) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.perPage;
        const end = start + this.perPage;

        rows.forEach((row, i) => {
            if (i >= start && i < end) {
                row.style.display = '';
                row.classList.remove('pg-hidden');
            } else {
                row.style.display = 'none';
                row.classList.add('pg-hidden');
            }
        });

        this._renderControls(rows.length, totalPages);
    }

    _renderControls(total, totalPages) {
        if (total <= this.perPage) {
            this.controlsEl.style.display = 'none';
            return;
        }
        this.controlsEl.style.display = '';

        const start = (this.currentPage - 1) * this.perPage + 1;
        const end = Math.min(this.currentPage * this.perPage, total);

        this.controlsEl.innerHTML = `
            <span class="pagination-info">Showing ${start}\u2013${end} of ${total}</span>
            <div class="pagination-buttons">
                <button class="btn btn-small pg-btn" ${this.currentPage <= 1 ? 'disabled' : ''} data-pg="prev">\u2190 Prev</button>
                <span class="pagination-page">Page ${this.currentPage} of ${totalPages}</span>
                <button class="btn btn-small pg-btn" ${this.currentPage >= totalPages ? 'disabled' : ''} data-pg="next">Next \u2192</button>
            </div>
        `;

        this.controlsEl.querySelector('[data-pg="prev"]').addEventListener('click', () => this.goTo(this.currentPage - 1));
        this.controlsEl.querySelector('[data-pg="next"]').addEventListener('click', () => this.goTo(this.currentPage + 1));
    }

    goTo(page) {
        this.currentPage = page;
        this.update();
        // Scroll table into view
        this.table.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    reset() {
        this.currentPage = 1;
        this.update();
    }
}

// Auto-init tables with data-paginate attribute
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('table[data-paginate]').forEach(table => {
        const perPage = parseInt(table.dataset.paginate, 10) || 25;
        table._paginator = new TablePaginator(table, { perPage });
    });
});
