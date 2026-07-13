<?php
// profit-analyzer/lib/import/schema.php
//
// Direct PHP port of the desktop app's ImportSchemaDefinition.cs. This is the
// target schema the analysis LLM maps source columns to, and the JsonName set
// the Tier 2 normalizer emits. Kept field-for-field identical to the C# so the
// two analyses produce the same shape.
//
// A column is: ['name','type','desc','required'(bool),'json'(string|null)].

/** Country-aware address labels (port of GetAddressLabels). */
function pa_address_labels(?string $country): array
{
    $n = strtoupper(trim($country ?? ''));
    switch ($n) {
        case 'UNITED STATES': case 'US': case 'USA':
            return ['State', 'State', 'ZIP Code', 'ZIP code'];
        case 'CANADA': case 'CA':
            return ['Province', 'Province', 'Postal Code', 'Postal code'];
        case 'UNITED KINGDOM': case 'UK': case 'GB': case 'GREAT BRITAIN':
            return ['County', 'County', 'Postcode', 'Postcode'];
        case 'AUSTRALIA': case 'AU':
            return ['State', 'State/territory', 'Postcode', 'Postcode'];
        case 'GERMANY': case 'DE': case 'DEUTSCHLAND':
            return ['State', 'Bundesland', 'Postal Code', 'Postleitzahl'];
        case 'FRANCE': case 'FR':
            return ['Region', 'Région', 'Postal Code', 'Code postal'];
        case 'JAPAN': case 'JP':
            return ['Prefecture', 'Prefecture', 'Postal Code', 'Postal code'];
        case 'CHINA': case 'CN':
            return ['Province', 'Province', 'Postal Code', 'Postal code'];
        case 'ITALY': case 'IT':
            return ['Province', 'Provincia', 'Postal Code', 'CAP'];
        case 'BRAZIL': case 'BR':
            return ['State', 'Estado', 'Postal Code', 'CEP'];
        case 'INDIA': case 'IN':
            return ['State', 'State', 'PIN Code', 'PIN code'];
        case 'MEXICO': case 'MX':
            return ['State', 'Estado', 'Postal Code', 'Código postal'];
        default:
            return ['State/Province', 'State or province', 'Postal Code', 'Postal code'];
    }
}

/** Helper to build a SchemaColumn record. */
function pa_col(string $name, string $type, string $desc, bool $required = false, ?string $json = null): array
{
    return ['name' => $name, 'type' => $type, 'desc' => $desc, 'required' => $required, 'json' => $json];
}

/**
 * The full import schema keyed by entity type (PascalCase, matching the C# enum).
 * Port of BuildSchema.
 */
function pa_import_schema(?string $country = null): array
{
    static $cache = [];
    $key = strtoupper(trim($country ?? ''));
    if (isset($cache[$key])) {
        return $cache[$key];
    }

    [$stateLabel, $stateDesc, $postalLabel, $postalDesc] = pa_address_labels($country);

    $schema = [
        'Customers' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., CUS-001)', true, 'id'),
            pa_col('Name', 'string', 'Customer name', true, 'name'),
            pa_col('Company', 'string', "Customer's company name", false, 'companyName'),
            pa_col('Email', 'string', 'Email address', false, 'email'),
            pa_col('Phone', 'string', 'Contact phone number', false, 'phone'),
            pa_col('Street', 'string', 'Mailing street address', false, 'address.street'),
            pa_col('City', 'string', 'City', false, 'address.city'),
            pa_col($stateLabel, 'string', $stateDesc, false, 'address.state'),
            pa_col($postalLabel, 'string', $postalDesc, false, 'address.zipCode'),
            pa_col('Country', 'string', 'Country', false, 'address.country'),
            pa_col('Notes', 'string', 'Additional notes', false, 'notes'),
            pa_col('Status', 'enum:Active,Inactive', 'Active or inactive customer', false, 'status'),
            pa_col('Total Purchases', 'decimal', 'Total purchase amount', false, 'totalPurchases'),
        ],
        'Suppliers' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., SUP-001)', true, 'id'),
            pa_col('Name', 'string', 'Name of the supplier', true, 'name'),
            pa_col('Email', 'string', 'Email address', false, 'email'),
            pa_col('Phone', 'string', 'Contact phone number', false, 'phone'),
            pa_col('Website', 'string', 'Website URL', false, 'website'),
            pa_col('Street', 'string', 'Mailing street address', false, 'address.street'),
            pa_col('City', 'string', 'City', false, 'address.city'),
            pa_col($stateLabel, 'string', $stateDesc, false, 'address.state'),
            pa_col($postalLabel, 'string', $postalDesc, false, 'address.zipCode'),
            pa_col('Country', 'string', 'Country', false, 'address.country'),
            pa_col('Notes', 'string', 'Additional notes', false, 'notes'),
        ],
        'Products' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., PRD-001)', true, 'id'),
            pa_col('Name', 'string', 'Name of the product or service', true, 'name'),
            pa_col('Type', 'enum:Revenue,Expenses,Rental', 'Product category type', false, 'type'),
            pa_col('Item Type', 'enum:Product,Service', 'Whether this is a product or service', false, 'itemType'),
            pa_col('SKU', 'string', 'Stock keeping unit code', false, 'sku'),
            pa_col('Description', 'string', 'Product description', false, 'description'),
            pa_col('Category ID', 'string', 'Category identifier', false, 'categoryId'),
            pa_col('Category Name', 'string', 'Name of the category - ALWAYS provide this, infer from product name/description if not in source data', false, 'categoryName'),
            pa_col('Supplier ID', 'string', 'Supplier identifier', false, 'supplierId'),
            pa_col('Supplier Name', 'string', 'Name of the supplier (alternative to ID)'),
            pa_col('Reorder Point', 'int', 'Stock level that triggers reorder', false, 'reorderPoint'),
            pa_col('Overstock Threshold', 'int', 'Stock level considered overstock', false, 'overstockThreshold'),
        ],
        'Categories' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., CAT-001)', true, 'id'),
            pa_col('Name', 'string', 'Name of the category', true, 'name'),
            pa_col('Type', 'enum:Revenue,Expenses,Rental', 'Category type', false, 'type'),
            pa_col('Parent ID', 'string', 'Parent category ID for subcategories', false, 'parentId'),
            pa_col('Description', 'string', 'Category description', false, 'description'),
            pa_col('Icon', 'string', 'Emoji icon for the category', false, 'icon'),
        ],
        'Invoices' => [
            pa_col('Invoice #', 'string', 'Invoice number (e.g., INV-2024-001)', true, 'id'),
            pa_col('Customer ID', 'string', 'Customer identifier', true, 'customerId'),
            pa_col('Issue Date', 'datetime', 'Date invoice was issued', false, 'issueDate'),
            pa_col('Due Date', 'datetime', 'Payment due date', false, 'dueDate'),
            pa_col('Subtotal', 'decimal', 'Amount before tax', false, 'subtotal'),
            pa_col('Tax', 'decimal', 'Tax amount', false, 'taxAmount'),
            pa_col('Total', 'decimal', 'Total amount due', false, 'total'),
            pa_col('Paid', 'decimal', 'Amount already paid', false, 'amountPaid'),
            pa_col('Balance', 'decimal', 'Remaining balance', false, 'balance'),
            pa_col('Status', 'enum:Draft,Sent,Paid,Overdue,Cancelled', 'Invoice status', false, 'status'),
        ],
        'Expenses' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., PUR-001)', true, 'id'),
            pa_col('Date', 'datetime', 'Transaction date', true, 'date'),
            pa_col('Supplier ID', 'string', 'Supplier identifier', false, 'supplierId'),
            pa_col('Product', 'string', 'Product or description of expense', false, 'description'),
            pa_col('Description', 'string', 'Description (alternative to Product)', false, 'description'),
            pa_col('Unit Price', 'decimal', 'Amount before tax', false, 'unitPrice'),
            pa_col('Tax', 'decimal', 'Tax amount', false, 'taxAmount'),
            pa_col('Total', 'decimal', 'Total amount including tax', false, 'total'),
            pa_col('Reference', 'string', 'External reference number', false, 'referenceNumber'),
            pa_col('Payment Method', 'enum:Cash,CreditCard,DebitCard,BankTransfer,Check,PayPal,Other', 'How payment was made', false, 'paymentMethod'),
            pa_col('Shipping', 'decimal', 'Cost of shipping', false, 'shippingCost'),
            pa_col('Currency', 'string', "ISO currency code the amounts are in (e.g., USD, EUR, GBP). Map when the sheet has a per-row currency column, OR when an amount cell itself contains a currency symbol or code (e.g. '£100', '$10 CAD'): output the ISO code, or the raw symbol if the code is unclear. Leave unmapped if all amounts are plainly in the company currency", false, 'originalCurrency'),
        ],
        'Revenue' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., SAL-001)', true, 'id'),
            pa_col('Date', 'datetime', 'Transaction date', true, 'date'),
            pa_col('Customer ID', 'string', 'Customer identifier', false, 'customerId'),
            pa_col('Product', 'string', 'Product or description of sale', false, 'description'),
            pa_col('Description', 'string', 'Description (alternative to Product)', false, 'description'),
            pa_col('Unit Price', 'decimal', 'Amount before tax', false, 'unitPrice'),
            pa_col('Tax', 'decimal', 'Tax amount', false, 'taxAmount'),
            pa_col('Total', 'decimal', 'Total amount including tax', false, 'total'),
            pa_col('Reference', 'string', 'External reference number', false, 'referenceNumber'),
            pa_col('Payment Status', 'enum:Paid,Unpaid,Partial,Pending,Overdue', 'Status of the payment', false, 'paymentStatus'),
            pa_col('Shipping', 'decimal', 'Cost of shipping', false, 'shippingCost'),
            pa_col('Currency', 'string', "ISO currency code the amounts are in (e.g., USD, EUR, GBP). Map when the sheet has a per-row currency column, OR when an amount cell itself contains a currency symbol or code (e.g. '£100', '$10 CAD'): output the ISO code, or the raw symbol if the code is unclear. Leave unmapped if all amounts are plainly in the company currency", false, 'originalCurrency'),
        ],
        'Inventory' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., INV-ITM-001)', true, 'id'),
            pa_col('Product ID', 'string', 'Associated product identifier', true, 'productId'),
            pa_col('Location ID', 'string', 'Storage location identifier', false, 'locationId'),
            pa_col('In Stock', 'int', 'Current stock quantity', false, 'inStock'),
            pa_col('Reserved', 'int', 'Reserved/allocated quantity', false, 'reserved'),
            pa_col('Reorder Point', 'int', 'Stock level that triggers reorder', false, 'reorderPoint'),
            pa_col('Unit Cost', 'decimal', 'Cost per unit', false, 'unitCost'),
            pa_col('Last Updated', 'datetime', 'When stock was last counted', false, 'lastUpdated'),
        ],
        'Payments' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., PAY-001)', true, 'id'),
            pa_col('Invoice ID', 'string', 'Associated invoice identifier', false, 'invoiceId'),
            pa_col('Customer ID', 'string', 'Customer identifier', false, 'customerId'),
            pa_col('Date', 'datetime', 'Payment date', false, 'date'),
            pa_col('Amount', 'decimal', 'Payment amount', false, 'amount'),
            pa_col('Payment Method', 'enum:Cash,CreditCard,DebitCard,BankTransfer,Check,PayPal,Other', 'How payment was made', false, 'paymentMethod'),
            pa_col('Reference', 'string', 'Payment reference number', false, 'referenceNumber'),
            pa_col('Notes', 'string', 'Additional notes', false, 'notes'),
            pa_col('Currency', 'string', "ISO currency code the amount is in (e.g., USD, EUR, GBP). Map when the sheet has a per-row currency column, OR when the amount cell itself contains a currency symbol or code (e.g. '£100', '$10 CAD'): output the ISO code, or the raw symbol if the code is unclear. Leave unmapped if all amounts are plainly in the company currency", false, 'originalCurrency'),
        ],
        'Locations' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., LOC-001)', true, 'id'),
            pa_col('Name', 'string', 'Name of the storage location', true, 'name'),
            pa_col('Contact Person', 'string', 'Contact person at location', false, 'contactPerson'),
            pa_col('Phone', 'string', 'Contact phone number', false, 'phone'),
            pa_col('Street', 'string', 'Mailing street address', false, 'address.street'),
            pa_col('City', 'string', 'City', false, 'address.city'),
            pa_col($stateLabel, 'string', $stateDesc, false, 'address.state'),
            pa_col($postalLabel, 'string', $postalDesc, false, 'address.zipCode'),
            pa_col('Country', 'string', 'Country', false, 'address.country'),
            pa_col('Capacity', 'int', 'Storage capacity', false, 'capacity'),
            pa_col('Utilization', 'int', 'Current utilization', false, 'currentUtilization'),
        ],
        'Departments' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., DEP-001)', true, 'id'),
            pa_col('Name', 'string', 'Name of the department', true, 'name'),
            pa_col('Description', 'string', 'Department description', false, 'description'),
        ],
        'Employees' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., EMP-001)', true, 'id'),
            pa_col('First Name', 'string', 'Employee first name', true, 'firstName'),
            pa_col('Last Name', 'string', 'Employee last name', true, 'lastName'),
            pa_col('Email', 'string', 'Email address', false, 'email'),
            pa_col('Phone', 'string', 'Contact phone number', false, 'phone'),
            pa_col('Date of Birth', 'datetime', "Employee's date of birth", false, 'dateOfBirth'),
            pa_col('Department ID', 'string', 'Department identifier', false, 'departmentId'),
            pa_col('Position', 'string', 'Job title or position', false, 'position'),
            pa_col('Hire Date', 'datetime', 'Date of hire', false, 'hireDate'),
            pa_col('Employment Type', 'enum:Full-time,Part-time,Contract,Intern', 'Type of employment', false, 'employmentType'),
            pa_col('Salary Type', 'enum:Annual,Hourly', 'Salary calculation basis', false, 'salaryType'),
            pa_col('Salary Amount', 'decimal', 'Salary amount per pay period', false, 'salaryAmount'),
            pa_col('Pay Frequency', 'enum:Weekly,Bi-weekly,Monthly', 'How often employee is paid', false, 'payFrequency'),
            pa_col('Status', 'enum:Active,Inactive,OnLeave,Terminated', 'Employment status', false, 'status'),
        ],
        'RentalInventory' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., RNT-ITM-001)', true, 'id'),
            pa_col('Inventory Item ID', 'string', 'Linked inventory item identifier', true, 'inventoryItemId'),
            pa_col('Daily Rate', 'decimal', 'Daily rental rate', false, 'dailyRate'),
            pa_col('Weekly Rate', 'decimal', 'Weekly rental rate', false, 'weeklyRate'),
            pa_col('Monthly Rate', 'decimal', 'Monthly rental rate', false, 'monthlyRate'),
            pa_col('Deposit', 'decimal', 'Security deposit required', false, 'securityDeposit'),
            pa_col('Status', 'enum:Active,Inactive', 'Item status', false, 'status'),
        ],
        'RentalRecords' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., RNT-001)', true, 'id'),
            pa_col('Customer ID', 'string', 'Customer identifier', true, 'customerId'),
            pa_col('Rental Item ID', 'string', 'Rental inventory item ID', false, 'rentalItemId'),
            pa_col('Start Date', 'datetime', 'Rental start date', false, 'startDate'),
            pa_col('Due Date', 'datetime', 'Expected return date', false, 'dueDate'),
            pa_col('Return Date', 'datetime', 'Actual return date (if returned)', false, 'returnDate'),
            pa_col('Quantity', 'int', 'Quantity rented', false, 'quantity'),
            pa_col('Rate Type', 'enum:Daily,Weekly,Monthly', 'Rental rate type', false, 'rateType'),
            pa_col('Rate Amount', 'decimal', 'Rate amount per period', false, 'rateAmount'),
            pa_col('Security Deposit', 'decimal', 'Security deposit amount', false, 'securityDeposit'),
            pa_col('Total Cost', 'decimal', 'Total cost of the rental', false, 'totalCost'),
            pa_col('Status', 'enum:Active,Returned,Overdue,Cancelled', 'Rental status', false, 'status'),
            pa_col('Paid', 'enum:Yes,No', 'Whether the rental has been paid', false, 'paid'),
        ],
        'RecurringInvoices' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., REC-INV-001)', true, 'id'),
            pa_col('Customer ID', 'string', 'Customer identifier', true, 'customerId'),
            pa_col('Amount', 'decimal', 'Invoice amount', false, 'amount'),
            pa_col('Description', 'string', 'Invoice description', false, 'description'),
            pa_col('Frequency', 'enum:Weekly,BiWeekly,Monthly,Quarterly,Annually', 'Billing frequency', false, 'frequency'),
            pa_col('Next Date', 'datetime', 'Next invoice date', false, 'nextInvoiceDate'),
            pa_col('Status', 'enum:Active,Paused,Cancelled', 'Recurring invoice status', false, 'status'),
        ],
        'StockAdjustments' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., ADJ-001)', true, 'id'),
            pa_col('Inventory Item ID', 'string', 'Inventory item identifier', true, 'inventoryItemId'),
            pa_col('Type', 'enum:Set,Add,Remove', 'Type of stock adjustment', false, 'adjustmentType'),
            pa_col('Quantity', 'int', 'Adjustment quantity', false, 'quantity'),
            pa_col('Previous Stock', 'int', 'Stock before adjustment', false, 'previousStock'),
            pa_col('New Stock', 'int', 'Stock after adjustment', false, 'newStock'),
            pa_col('Reason', 'string', 'Reason for adjustment', false, 'reason'),
            pa_col('Timestamp', 'datetime', 'When adjustment was made', false, 'timestamp'),
        ],
        'PurchaseOrders' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., PO-001)', true, 'id'),
            pa_col('Supplier ID', 'string', 'Supplier identifier', true, 'supplierId'),
            pa_col('Order Date', 'datetime', 'Date order was placed', false, 'orderDate'),
            pa_col('Expected Date', 'datetime', 'Expected delivery date', false, 'expectedDeliveryDate'),
            pa_col('Total', 'decimal', 'Order total', false, 'total'),
            pa_col('Status', 'enum:Draft,Submitted,Approved,Received,Cancelled', 'Order status', false, 'status'),
        ],
        'PurchaseOrderLineItems' => [
            pa_col('PO ID', 'string', 'Purchase order identifier', true),
            pa_col('Product ID', 'string', 'Product identifier', true, 'productId'),
            pa_col('Quantity', 'int', 'Ordered quantity', false, 'quantity'),
            pa_col('Unit Cost', 'decimal', 'Cost per unit', false, 'unitCost'),
            pa_col('Quantity Received', 'int', 'Quantity received so far', false, 'quantityReceived'),
        ],
        'Returns' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., RET-001)', true, 'id'),
            pa_col('Original Transaction ID', 'string', 'ID of the original transaction', false, 'originalTransactionId'),
            pa_col('Return Type', 'enum:Customer,Supplier', 'Type of return', false, 'returnType'),
            pa_col('Customer ID', 'string', 'Customer identifier (for customer returns)', false, 'customerId'),
            pa_col('Supplier ID', 'string', 'Supplier identifier (for supplier returns)', false, 'supplierId'),
            pa_col('Return Date', 'datetime', 'Date of return', false, 'returnDate'),
            pa_col('Product ID', 'string', 'Returned product ID'),
            pa_col('Product', 'string', 'Returned product name (alternative to ID)'),
            pa_col('Quantity', 'int', 'Quantity returned'),
            pa_col('Reason', 'string', 'Reason for return'),
            pa_col('Refund Amount', 'decimal', 'Amount being refunded', false, 'refundAmount'),
            pa_col('Restocking Fee', 'decimal', 'Restocking fee charged', false, 'restockingFee'),
            pa_col('Status', 'enum:Pending,Approved,Rejected,Completed', 'Return status', false, 'status'),
            pa_col('Notes', 'string', 'Additional notes', false, 'notes'),
            pa_col('Processed By', 'string', 'Employee who processed the return', false, 'processedBy'),
        ],
        'LostDamaged' => [
            pa_col('ID', 'string', 'Unique identifier (e.g., LOST-001)', true, 'id'),
            pa_col('Product ID', 'string', 'Product identifier', false, 'productId'),
            pa_col('Product', 'string', 'Product name (alternative to ID)'),
            pa_col('Inventory Item ID', 'string', 'Inventory item identifier', false, 'inventoryItemId'),
            pa_col('Quantity', 'int', 'Quantity lost or damaged', false, 'quantity'),
            pa_col('Reason', 'enum:Lost,Damaged,Stolen,Expired,Other', 'Reason for loss', false, 'reason'),
            pa_col('Date Discovered', 'datetime', 'Date loss was discovered', false, 'dateDiscovered'),
            pa_col('Date', 'datetime', 'Date (alternative to Date Discovered)', false, 'dateDiscovered'),
            pa_col('Value Lost', 'decimal', 'Monetary value of the loss', false, 'valueLost'),
            pa_col('Notes', 'string', 'Additional notes', false, 'notes'),
            pa_col('Insurance Claim', 'enum:Yes,No', 'Whether an insurance claim was filed', false, 'insuranceClaim'),
        ],
        'BankStatement' => [
            pa_col('Date', 'datetime', 'Date the bank posted the transaction', true, 'date'),
            pa_col('Description', 'string', 'Transaction description / memo from the bank', true, 'description'),
            pa_col('Amount', 'decimal', 'Signed amount: negative for money out, positive for money in. Map a single signed amount column here when present', false, 'amount'),
            pa_col('Debit', 'decimal', 'Money out of the account (use when the statement has separate debit/credit columns)', false, 'debit'),
            pa_col('Credit', 'decimal', 'Money into the account (use when the statement has separate debit/credit columns)', false, 'credit'),
            pa_col('Balance', 'decimal', 'Running account balance after the transaction', false, 'balance'),
            pa_col('Reference', 'string', 'Bank reference, transaction id, or check number', false, 'rawReference'),
        ],
    ];

    $cache[$key] = $schema;
    return $schema;
}

/** The schema columns for one entity type, or null. Port of GetSchemaForType. */
function pa_schema_for_type(string $type, ?string $country = null): ?array
{
    return pa_import_schema($country)[$type] ?? null;
}

/** The list of detectable entity types (for the response-format enum line). */
function pa_entity_type_names(): array
{
    return [
        'Customers', 'Suppliers', 'Products', 'Categories', 'Locations', 'Departments',
        'Invoices', 'Expenses', 'Inventory', 'Payments', 'Revenue', 'RentalInventory',
        'RentalRecords', 'Employees', 'RecurringInvoices', 'StockAdjustments',
        'PurchaseOrders', 'PurchaseOrderLineItems', 'Returns', 'LostDamaged', 'Unknown',
    ];
}

/** Formats the schema as markdown for the analysis prompt. Port of FormatSchemaForPrompt. */
function pa_format_schema_for_prompt(?string $country = null): string
{
    $out = '';
    foreach (pa_import_schema($country) as $type => $columns) {
        $out .= "### {$type}\n";
        $out .= "| Column | Type | Required | Description |\n";
        $out .= "|--------|------|----------|-------------|\n";
        foreach ($columns as $col) {
            $req = $col['required'] ? 'Yes' : 'No';
            $out .= "| {$col['name']} | {$col['type']} | {$req} | {$col['desc']} |\n";
        }
        $out .= "\n";
    }
    return $out;
}
