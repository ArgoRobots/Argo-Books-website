<?php
// profit-analyzer/lib/export.php
//
// Builds the cleaned multi-sheet workbook structure from NormalizedData,
// one sheet per entity that has data, with the desktop export service's
// column sets. Hand off to pa_write_xlsx().

require_once __DIR__ . '/contract.php';
require_once __DIR__ . '/xlsx.php';

/** Resolve a field from an entity list by id. */
function pa_lookup(array $list, ?string $id, string $field): string
{
    if ($id === null || $id === '') { return ''; }
    foreach ($list as $x) {
        if (($x['id'] ?? '') === $id) { return (string) ($x[$field] ?? ''); }
    }
    return '';
}

/**
 * NormalizedData -> [ 'Sheet name' => ['headers'=>[...], 'rows'=>[[...]]] ].
 * Only entities present in the data become sheets.
 */
function pa_build_workbook(array $normalized): array
{
    $n = pa_normalize($normalized);
    $E = $n['entities'];
    $cust = $E['customers'];
    $sup = $E['suppliers'];
    $sheets = [];

    if ($E['revenue']) {
        $rows = [];
        foreach ($E['revenue'] as $r) {
            $rows[] = [
                (string) ($r['date'] ?? ''),
                pa_lookup($cust, $r['customerId'] ?? '', 'name'),
                (string) ($r['description'] ?? ''),
                (float) ($r['quantity'] ?? 0),
                (float) ($r['unitPrice'] ?? 0),
                (float) ($r['taxAmount'] ?? 0),
                (float) ($r['total'] ?? 0),
                (string) ($r['paymentStatus'] ?? ''),
            ];
        }
        $sheets['Sales'] = ['headers' => ['Date', 'Customer', 'Item', 'Quantity', 'Unit Price', 'Tax', 'Total', 'Status'], 'rows' => $rows];
    }

    if ($E['expenses']) {
        $rows = [];
        foreach ($E['expenses'] as $r) {
            $rows[] = [
                (string) ($r['date'] ?? ''),
                pa_lookup($sup, $r['supplierId'] ?? '', 'name'),
                (string) ($r['category'] ?? ''),
                (string) ($r['description'] ?? ''),
                (float) ($r['amount'] ?? 0),
                (float) ($r['taxAmount'] ?? 0),
                (float) ($r['total'] ?? 0),
                (string) ($r['paymentMethod'] ?? ''),
            ];
        }
        $sheets['Expenses'] = ['headers' => ['Date', 'Supplier', 'Category', 'Description', 'Amount', 'Tax', 'Total', 'Method'], 'rows' => $rows];
    }

    if ($E['invoices']) {
        $rows = [];
        foreach ($E['invoices'] as $r) {
            $rows[] = [
                (string) ($r['invoiceNumber'] ?? ''),
                pa_lookup($cust, $r['customerId'] ?? '', 'name'),
                (string) ($r['issueDate'] ?? ''),
                (string) ($r['dueDate'] ?? ''),
                (float) ($r['subtotal'] ?? 0),
                (float) ($r['taxAmount'] ?? 0),
                (float) ($r['total'] ?? 0),
                (float) ($r['amountPaid'] ?? 0),
                (float) ($r['balance'] ?? 0),
                (string) ($r['status'] ?? ''),
            ];
        }
        $sheets['Invoices'] = ['headers' => ['Invoice #', 'Customer', 'Issued', 'Due', 'Subtotal', 'Tax', 'Total', 'Paid', 'Balance', 'Status'], 'rows' => $rows];
    }

    if ($E['customers']) {
        $rows = [];
        foreach ($E['customers'] as $c) {
            $rows[] = [
                (string) ($c['name'] ?? ''), (string) ($c['company'] ?? ''), (string) ($c['email'] ?? ''),
                (string) ($c['city'] ?? ''), (string) ($c['country'] ?? ''), (string) ($c['status'] ?? ''),
            ];
        }
        $sheets['Customers'] = ['headers' => ['Name', 'Company', 'Email', 'City', 'Country', 'Status'], 'rows' => $rows];
    }

    if ($E['products']) {
        $rows = [];
        foreach ($E['products'] as $p) {
            $rows[] = [
                (string) ($p['name'] ?? ''), (string) ($p['sku'] ?? ''), (string) ($p['type'] ?? ''),
                (string) ($p['categoryName'] ?? ''), pa_lookup($sup, $p['supplierId'] ?? '', 'name'),
                (float) ($p['unitPrice'] ?? 0), (float) ($p['reorderPoint'] ?? 0),
            ];
        }
        $sheets['Products'] = ['headers' => ['Name', 'SKU', 'Type', 'Category', 'Supplier', 'Unit Price', 'Reorder Point'], 'rows' => $rows];
    }

    if ($E['suppliers']) {
        $rows = [];
        foreach ($E['suppliers'] as $s) {
            $rows[] = [(string) ($s['name'] ?? ''), (string) ($s['country'] ?? '')];
        }
        $sheets['Suppliers'] = ['headers' => ['Name', 'Country'], 'rows' => $rows];
    }

    return $sheets;
}
