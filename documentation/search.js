// Documentation search: page index + init.
// The search engine is the shared resources/scripts/site-search.js (SiteSearch).
// NOTE: keep this list in sync when adding or removing documentation pages.

(function () {
    const pages = [
            // Getting Started
            { id: 'system-requirements', title: 'System Requirements', category: 'Getting Started', folder: 'getting-started', keywords: 'windows macos linux requirements specs hardware disk space ram memory' },
            { id: 'installation', title: 'Installation Guide', category: 'Getting Started', folder: 'getting-started', keywords: 'install download setup installer wizard run' },
            { id: 'quick-start', title: 'Quick Start Tutorial', category: 'Getting Started', folder: 'getting-started', keywords: 'tutorial getting started begin first steps currency company accountant category product' },
            { id: 'version-comparison', title: 'Free vs. Paid Version', category: 'Getting Started', folder: 'getting-started', keywords: 'free paid premium upgrade features comparison limited unlimited products biometric login touch id fingerprint ai search standard' },

            // Core Features
            { id: 'dashboard', title: 'Dashboard', category: 'Core Features', folder: 'features', keywords: 'dashboard home overview metrics expenses revenue invoices rentals quick actions charts' },
            { id: 'analytics', title: 'Analytics', category: 'Core Features', folder: 'features', keywords: 'analytics charts geographic map performance operational customers returns losses date range column line area scatter' },
            { id: 'predictive-analytics', title: 'Predictive Analytics', category: 'Core Features', folder: 'features', keywords: 'ai predictive analytics forecast revenue seasonal patterns inventory predictions premium' },
            { id: 'report-generator', title: 'Report Generator', category: 'Core Features', folder: 'features', keywords: 'report generate pdf png jpg chart analytics template layout designer' },
            { id: 'sales-tracking', title: 'Expense/Revenue Tracking', category: 'Core Features', folder: 'features', keywords: 'expense revenue transaction order tracking add quantity price shipping tax' },
            { id: 'invoicing', title: 'Invoicing & Payments', category: 'Core Features', folder: 'features', keywords: 'invoice payment billing stripe paypal square online payment processing credit card' },
            { id: 'bank-matching', title: 'Bank Matching', category: 'Core Features', folder: 'features', keywords: 'bank matching statement reconcile reconciliation import csv excel match expenses revenue invoices payments unmatched duplicate verify books deposit debit credit' },
            { id: 'rental', title: 'Rental Management', category: 'Core Features', folder: 'features', keywords: 'rental booking calendar availability equipment return deposit late fee reservation' },
            { id: 'customers', title: 'Customer Management', category: 'Core Features', folder: 'features', keywords: 'customer client profile contact expense history notes tags crm relationship' },
            { id: 'product-management', title: 'Product Management', category: 'Core Features', folder: 'features', keywords: 'products categories inventory add create manage organize' },
            { id: 'suppliers', title: 'Supplier Management', category: 'Core Features', folder: 'features', keywords: 'supplier vendor purchase source contact manage' },
            { id: 'inventory', title: 'Inventory Management', category: 'Core Features', folder: 'features', keywords: 'inventory stock tracking reorder point low stock alert quantity warehouse location batch' },
            { id: 'purchase-orders', title: 'Purchase Orders', category: 'Core Features', folder: 'features', keywords: 'purchase order supplier restock receive items inventory' },
            { id: 'returns', title: 'Returns', category: 'Core Features', folder: 'features', keywords: 'return refund exchange product damaged defective' },
            { id: 'receipts', title: 'Receipt Management', category: 'Core Features', folder: 'features', keywords: 'receipt digital scan microsoft lens export attach' },
            { id: 'receipt-scanning', title: 'AI Receipt Scanning', category: 'Core Features', folder: 'features', keywords: 'ai receipt scanning ocr photo image extract vendor date items totals premium' },
            { id: 'spreadsheet-import', title: 'Spreadsheet Import', category: 'Core Features', folder: 'features', keywords: 'import excel spreadsheet xlsx csv data suppliers products expenses revenue currency' },
            { id: 'spreadsheet-export', title: 'Spreadsheet Export', category: 'Core Features', folder: 'features', keywords: 'export excel spreadsheet xlsx backup data currency conversion chart' },

            // Reference
            { id: 'how-numbers-are-calculated', title: 'How Numbers Are Calculated', category: 'Reference', folder: 'reference', keywords: 'revenue profit net tax sales tax shipping refund refunds cash basis accrual currency calculations formula how computed gross subtotal total deduction discount fee invoice status overdue paid unpaid partially refunded' },
            { id: 'supported-currencies', title: 'Supported Currencies', category: 'Reference', folder: 'reference', keywords: 'currency currencies usd eur gbp cad jpy cny exchange rate convert' },
            { id: 'supported-languages', title: 'Supported Languages', category: 'Reference', folder: 'reference', keywords: 'language languages english spanish french german chinese arabic localization translation' },

            // Security
            { id: 'encryption', title: 'Encryption', category: 'Security', folder: 'security', keywords: 'encryption aes-256 security protect data' },
            { id: 'password', title: 'Password Protection', category: 'Security', folder: 'security', keywords: 'password protection biometric login fingerprint face touch id windows hello macos linux security' },
            { id: 'backups', title: 'Regular Backups', category: 'Security', folder: 'security', keywords: 'backup export save data loss protection cloud' },
            { id: 'anonymous-data', title: 'Anonymous Usage Data', category: 'Security', folder: 'security', keywords: 'anonymous usage data privacy statistics telemetry collection disable' }
    ];

    document.addEventListener("DOMContentLoaded", function () {
        if (typeof SiteSearch === "undefined") return;
        const input = document.getElementById("docSearchInput");
        const basePath = input ? (input.dataset.basePath || "") : "";
        const items = pages.map(function (p) {
            return {
                id: p.id,
                title: p.title,
                category: p.category,
                keywords: p.keywords,
                url: basePath + "pages/" + p.folder + "/" + p.id + ".php"
            };
        });
        new SiteSearch({ inputId: "docSearchInput", resultsId: "searchResults", items: items });
    });
})();
