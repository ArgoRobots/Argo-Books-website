<?php
declare(strict_types=1);

/**
 * Resource definitions.
 *
 * Seven near-identical CRUD families, so they are described as data and driven
 * by one engine (resource.php) rather than written out seven times. Adding a
 * field is a line here; adding a resource is a block here.
 *
 * Keys of the top-level array are the URL segments: /v1/customers, /v1/expenses.
 *
 * Field types are defined in validate.php. `ref` fields carry the target table
 * so a dangling reference is caught on the request that created it.
 */

/** Shared shape for a pointer at another resource. */
function api_ref(string $object): array
{
    $map = [
        'customer' => ['prefix' => 'cus', 'table' => 'api_customers'],
        'supplier' => ['prefix' => 'sup', 'table' => 'api_suppliers'],
        'category' => ['prefix' => 'cat', 'table' => 'api_categories'],
        'product'  => ['prefix' => 'prd', 'table' => 'api_products'],
        'revenue'  => ['prefix' => 'rev', 'table' => 'api_revenue'],
    ];
    return [
        'type'   => 'ref',
        'object' => $object,
        'prefix' => $map[$object]['prefix'],
        'table'  => $map[$object]['table'],
    ];
}

/** Postal address fields, shared by customers and suppliers. */
function api_address_fields(): array
{
    return [
        'address_line1' => ['type' => 'string', 'max' => 255],
        'address_line2' => ['type' => 'string', 'max' => 255],
        'city'          => ['type' => 'string', 'max' => 120],
        'state'         => ['type' => 'string', 'max' => 120],
        'postal_code'   => ['type' => 'string', 'max' => 30],
        'country'       => ['type' => 'country'],
    ];
}

function api_resource_definitions(): array
{
    static $definitions = null;
    if ($definitions !== null) {
        return $definitions;
    }

    $definitions = [
        'customers' => [
            'object'  => 'customer',
            'table'   => 'api_customers',
            'prefix'  => 'cus',
            'fields'  => array_merge([
                'name'       => ['type' => 'string', 'required' => true, 'max' => 255],
                'email'      => ['type' => 'email'],
                'phone'      => ['type' => 'string', 'max' => 50],
                'company'    => ['type' => 'string', 'max' => 255],
                'tax_number' => ['type' => 'string', 'max' => 60],
            ], api_address_fields(), [
                'notes'    => ['type' => 'text', 'max' => 5000],
                'metadata' => ['type' => 'metadata'],
            ]),
            'filters' => ['email' => 'exact', 'name' => 'exact'],
        ],

        'suppliers' => [
            'object'  => 'supplier',
            'table'   => 'api_suppliers',
            'prefix'  => 'sup',
            'fields'  => array_merge([
                'name'       => ['type' => 'string', 'required' => true, 'max' => 255],
                'email'      => ['type' => 'email'],
                'phone'      => ['type' => 'string', 'max' => 50],
                'website'    => ['type' => 'string', 'max' => 255],
                'tax_number' => ['type' => 'string', 'max' => 60],
            ], api_address_fields(), [
                'notes'    => ['type' => 'text', 'max' => 5000],
                'metadata' => ['type' => 'metadata'],
            ]),
            'filters' => ['email' => 'exact', 'name' => 'exact'],
        ],

        'categories' => [
            'object'  => 'category',
            'table'   => 'api_categories',
            'prefix'  => 'cat',
            'fields'  => [
                'name'        => ['type' => 'string', 'required' => true, 'max' => 255],
                'kind'        => ['type' => 'enum', 'values' => ['expense', 'revenue'], 'required' => true],
                'parent'      => api_ref('category'),
                'description' => ['type' => 'text', 'max' => 2000],
                'metadata'    => ['type' => 'metadata'],
            ],
            'filters' => ['kind' => 'exact', 'name' => 'exact', 'parent' => 'exact'],
        ],

        'products' => [
            'object'  => 'product',
            'table'   => 'api_products',
            'prefix'  => 'prd',
            'fields'  => [
                'name'        => ['type' => 'string', 'required' => true, 'max' => 255],
                'sku'         => ['type' => 'string', 'max' => 120],
                'description' => ['type' => 'text', 'max' => 5000],
                'unit'        => ['type' => 'string', 'max' => 40],
                'unit_amount' => ['type' => 'amount'],
                'currency'    => ['type' => 'currency'],
                'tax_rate'    => ['type' => 'decimal'],
                'category'    => api_ref('category'),
                'metadata'    => ['type' => 'metadata'],
            ],
            'filters' => ['sku' => 'exact', 'name' => 'exact', 'category' => 'exact'],
        ],

        'expenses' => [
            'object'    => 'expense',
            'table'     => 'api_expenses',
            'prefix'    => 'exp',
            'fields'    => [
                'description'    => ['type' => 'string', 'required' => true, 'max' => 500],
                'amount'         => ['type' => 'amount', 'required' => true],
                'currency'       => ['type' => 'currency', 'required' => true],
                'tax_amount'     => ['type' => 'amount', 'default' => 0],
                'occurred_on'    => ['type' => 'date', 'required' => true],
                'supplier'       => api_ref('supplier'),
                'category'       => api_ref('category'),
                'payment_method' => ['type' => 'string', 'max' => 40],
                'reference'      => ['type' => 'string', 'max' => 120],
                'notes'          => ['type' => 'text', 'max' => 5000],
                'metadata'       => ['type' => 'metadata'],
            ],
            'filters'   => [
                'supplier'    => 'exact',
                'category'    => 'exact',
                'currency'    => 'exact',
                'reference'   => 'exact',
                'occurred_on' => 'date_range',
            ],
            'line_items' => true,
        ],

        'revenue' => [
            'object'    => 'revenue',
            'table'     => 'api_revenue',
            'prefix'    => 'rev',
            'fields'    => [
                'description'     => ['type' => 'string', 'required' => true, 'max' => 500],
                'amount'          => ['type' => 'amount', 'required' => true],
                'currency'        => ['type' => 'currency', 'required' => true],
                'tax_amount'      => ['type' => 'amount', 'default' => 0],
                'discount_amount' => ['type' => 'amount', 'default' => 0],
                'fee_amount'      => ['type' => 'amount', 'default' => 0],
                'occurred_on'     => ['type' => 'date', 'required' => true],
                'customer'        => api_ref('customer'),
                'category'        => api_ref('category'),
                'payment_method'  => ['type' => 'string', 'max' => 40],
                'reference'       => ['type' => 'string', 'max' => 120],
                'notes'           => ['type' => 'text', 'max' => 5000],
                'metadata'        => ['type' => 'metadata'],
            ],
            'filters'   => [
                'customer'    => 'exact',
                'category'    => 'exact',
                'currency'    => 'exact',
                'reference'   => 'exact',
                'occurred_on' => 'date_range',
            ],
            'line_items' => true,
        ],

        'refunds' => [
            'object'  => 'refund',
            'table'   => 'api_refunds',
            'prefix'  => 're',
            'fields'  => [
                'revenue'     => array_merge(api_ref('revenue'), ['required' => true]),
                'amount'      => ['type' => 'amount', 'required' => true],
                'currency'    => ['type' => 'currency', 'required' => true],
                'reason'      => ['type' => 'string', 'max' => 255],
                'occurred_on' => ['type' => 'date', 'required' => true],
                'reference'   => ['type' => 'string', 'max' => 120],
                'metadata'    => ['type' => 'metadata'],
            ],
            'filters' => ['revenue' => 'exact', 'currency' => 'exact', 'occurred_on' => 'date_range'],
        ],
    ];

    return $definitions;
}

/** Line items are a sub-resource of expenses and revenue, with their own spec. */
function api_line_item_definition(): array
{
    return [
        'object' => 'line_item',
        'table'  => 'api_line_items',
        'prefix' => 'li',
        'fields' => [
            'product'         => api_ref('product'),
            'description'     => ['type' => 'string', 'required' => true, 'max' => 500],
            'quantity'        => ['type' => 'decimal', 'default' => '1.0000'],
            'unit_amount'     => ['type' => 'amount', 'required' => true],
            'tax_amount'      => ['type' => 'amount', 'default' => 0],
            'discount_amount' => ['type' => 'amount', 'default' => 0],
        ],
    ];
}
