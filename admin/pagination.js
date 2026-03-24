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

        // Build page number buttons with ellipsis for large page counts
        let pages = '';
        const cur = this.currentPage;
        const range = [];

        for (let i = 1; i <= totalPages; i++) {
            if (totalPages <= 7 || i === 1 || i === totalPages || (i >= cur - 1 && i <= cur + 1)) {
                range.push(i);
            } else if (range[range.length - 1] !== '...') {
                range.push('...');
            }
        }

        for (const p of range) {
            if (p === '...') {
                pages += '<span class="pg-ellipsis">\u2026</span>';
            } else {
                pages += `<button class="pg-num${p === cur ? ' pg-active' : ''}" data-pg="${p}">${p}</button>`;
            }
        }

        this.controlsEl.innerHTML = `
            <button class="pg-arrow" ${cur <= 1 ? 'disabled' : ''} data-pg="prev">\u2039</button>
            ${pages}
            <button class="pg-arrow" ${cur >= totalPages ? 'disabled' : ''} data-pg="next">\u203A</button>
        `;

        this.controlsEl.querySelector('[data-pg="prev"]').addEventListener('click', () => this.goTo(cur - 1));
        this.controlsEl.querySelector('[data-pg="next"]').addEventListener('click', () => this.goTo(cur + 1));
        this.controlsEl.querySelectorAll('.pg-num').forEach(btn => {
            btn.addEventListener('click', () => this.goTo(parseInt(btn.dataset.pg, 10)));
        });
    }

    goTo(page) {
        this.currentPage = page;
        this.update();
        window.scrollTo(0, document.documentElement.scrollHeight);
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
