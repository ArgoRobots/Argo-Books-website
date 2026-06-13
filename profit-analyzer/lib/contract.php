<?php
// profit-analyzer/lib/contract.php
//
// The NormalizedData contract: the single, stable interface between the
// (deferred) analysis step and everything downstream (analytics, export, UI).
// The analysis step — whoever produces it, a future PHP port or the desktop
// importer's logic — must emit this shape. The field set mirrors the desktop
// app's ImportSchemaDefinition + SpreadsheetExportService column lists, which
// the importer roadmap treats as fixed.
//
// Shape:
//   {
//     "meta":     { filename, currency, taxRate?, period?{start,end}, flagged? },
//     "entities": {
//        "revenue":   [ {id,date,customerId,productId,description,quantity,unitPrice,amount,taxAmount,total,paymentStatus} ],
//        "expenses":  [ {id,date,supplierId,category,description,amount,taxAmount,total,paymentMethod} ],
//        "products":  [ {id,name,sku,type,categoryName,supplierId,unitPrice,reorderPoint} ],
//        "customers": [ {id,name,company,email,city,country,status} ],
//        "suppliers": [ {id,name,country} ],
//        "invoices":  [ {invoiceNumber,customerId,issueDate,dueDate,subtotal,taxAmount,total,amountPaid,balance,status} ],
//        "payments":  [...], "inventory": [...], "rentals": [...],
//        "returns":   [...], "losses": [...]   // ... any of the ~18 entity types
//     }
//   }
//
// Every entity array is OPTIONAL. The analytics layer renders only the tabs the
// present entities support, so a sales-only export yields a few tabs, not all.

/** The full set of entity types the contract may carry (from the app schema). */
function pa_entity_types(): array
{
    return [
        'revenue', 'expenses', 'products', 'customers', 'suppliers', 'categories',
        'invoices', 'payments', 'inventory', 'locations', 'departments', 'employees',
        'recurringInvoices', 'stockAdjustments', 'purchaseOrders', 'rentals',
        'rentalInventory', 'returns', 'losses',
    ];
}

/** Load a bundled sample fixture by name (no extension). Returns NormalizedData. */
function pa_load_fixture(string $name): ?array
{
    $name = preg_replace('/[^a-z0-9_-]/i', '', $name);
    $path = __DIR__ . '/../fixtures/' . $name . '.json';
    if (!is_file($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? pa_normalize($data) : null;
}

/** Coerce arbitrary input into the contract shape (fills missing keys safely). */
function pa_normalize(array $data): array
{
    $entities = $data['entities'] ?? [];
    $out = ['meta' => $data['meta'] ?? [], 'entities' => []];
    foreach (pa_entity_types() as $t) {
        $out['entities'][$t] = isset($entities[$t]) && is_array($entities[$t]) ? array_values($entities[$t]) : [];
    }
    return $out;
}
