<?php
// compare/compare-lib.php
//
// Small helpers shared by compare-page.php and the data files under
// compare/data/.

/**
 * Every comparison page, slug => the label other pages link to it by.
 *
 * The labels used to be repeated inside a $other_comparisons array on each of
 * the fifteen pages. They happened to agree, but nothing kept them agreeing:
 * renaming a page meant finding every other page that linked to it.
 */
function argo_compare_index(): array
{
    return [
        'argo-books-vs-quickbooks'     => 'Argo Books vs. QuickBooks',
        'argo-books-vs-wave'           => 'Argo Books vs. Wave',
        'argo-books-vs-freshbooks'     => 'Argo Books vs. FreshBooks',
        'argo-books-vs-xero'           => 'Argo Books vs. Xero',
        'argo-books-vs-spreadsheet'    => 'Argo Books vs. spreadsheets',
        'bonsai-alternatives'          => 'Bonsai alternatives',
        'gnucash-alternatives'         => 'GnuCash alternatives',
        'honeybook-alternatives'       => 'HoneyBook alternatives',
        'invoice2go-alternatives'      => 'Invoice2go alternatives',
        'manager-io-alternatives'      => 'Manager.io alternatives',
        'odoo-accounting-alternatives' => 'Odoo accounting alternatives',
        'sage-50-alternatives'         => 'Sage 50 alternatives',
        'square-invoices-alternatives' => 'Square Invoices alternatives',
        'zipbooks-alternatives'        => 'ZipBooks alternatives',
        'zoho-books-alternatives'      => 'Zoho Books alternatives',
    ];
}

/**
 * One cell of the side-by-side table.
 *
 * 'yes' and 'no' render the tick and the cross; any other string renders as the
 * grey "partial" pill carrying that text ("Limited", "Add-on", "Paid tiers").
 */
function argo_compare_cell(string $value): string
{
    if ($value === 'yes') {
        return '<span class="check-yes">' . svg_icon('check', 18) . '</span>';
    }
    if ($value === 'no') {
        return '<span class="check-no">' . svg_icon('x', 18) . '</span>';
    }
    return '<span class="check-partial">' . $value . '</span>';
}
