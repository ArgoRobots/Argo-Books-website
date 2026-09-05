<?php
require_once __DIR__ . '/../../../resources/icons.php';
require_once __DIR__ . '/../../../partials/code-block.php';

// Render the field tables from the same definitions the API itself is driven by,
// so this page cannot drift out of date when a field is added or renamed.
require_once __DIR__ . '/../../../api/v1/lib/definitions.php';

$pageTitle = 'API Resources';
$pageDescription = 'Every object the Argo Books API accepts: customers, suppliers, categories, products, expenses, revenue, refunds and line items, with each field and its type.';
$currentPage = 'resources';
$pageCategory = 'api';

/** Turn an internal field spec into something a reader can act on. */
function api_doc_type(array $field): string
{
    switch ($field['type']) {
        case 'amount':
            return 'integer <span class="muted">(minor units)</span>';
        case 'currency':
            return 'string <span class="muted">(ISO 4217, 3 letters)</span>';
        case 'country':
            return 'string <span class="muted">(ISO 3166-1, 2 letters)</span>';
        case 'date':
            return 'string <span class="muted">(YYYY-MM-DD)</span>';
        case 'enum':
            return 'enum <span class="muted">(' . htmlspecialchars(implode(', ', $field['values'])) . ')</span>';
        case 'ref':
            return 'string <span class="muted">(id of a ' . htmlspecialchars($field['object']) . ', ' . htmlspecialchars($field['prefix']) . '_...)</span>';
        case 'metadata':
            return 'object <span class="muted">(string values)</span>';
        case 'decimal':
            return 'number';
        case 'email':
            return 'string <span class="muted">(email)</span>';
        case 'text':
            return 'string <span class="muted">(long)</span>';
        default:
            return 'string' . (isset($field['max']) ? ' <span class="muted">(max ' . (int) $field['max'] . ')</span>' : '');
    }
}

$definitions = api_resource_definitions();

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Seven object types, all with the same shape of endpoints. Replace <code>&lt;resource&gt;</code> below with any of <code>customers</code>, <code>suppliers</code>, <code>categories</code>, <code>products</code>, <code>expenses</code>, <code>revenue</code>, <code>refunds</code>.</p>

            <table>
                <thead>
                    <tr><th>Endpoint</th><th>Does</th><th>Scope</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>POST /v1/&lt;resource&gt;</code></td><td>Create. Requires <code>Idempotency-Key</code>.</td><td>write</td></tr>
                    <tr><td><code>GET /v1/&lt;resource&gt;</code></td><td>List, newest first.</td><td>read</td></tr>
                    <tr><td><code>GET /v1/&lt;resource&gt;/&lt;id&gt;</code></td><td>Retrieve one.</td><td>read</td></tr>
                    <tr><td><code>POST /v1/&lt;resource&gt;/&lt;id&gt;</code></td><td>Update. Only while pending.</td><td>write</td></tr>
                    <tr><td><code>DELETE /v1/&lt;resource&gt;/&lt;id&gt;</code></td><td>Withdraw your push. Only while pending.</td><td>write</td></tr>
                    <tr><td><code>POST /v1/&lt;resource&gt;/&lt;id&gt;/reject</code></td><td>Mark as declined by the merchant.</td><td>write</td></tr>
                </tbody>
            </table>

            <p>Expenses and revenue additionally have <code>GET</code> and <code>POST /v1/&lt;resource&gt;/&lt;id&gt;/line_items</code>.</p>

            <h2>Fields Every Object Has</h2>
            <table>
                <thead><tr><th>Field</th><th>Type</th><th>Notes</th></tr></thead>
                <tbody>
                    <tr><td><code>id</code></td><td>string</td><td>Prefixed and opaque, for example <code>cus_9f21c0b47ae35d18f2c4a7bb</code>.</td></tr>
                    <tr><td><code>object</code></td><td>string</td><td>The type name, so a mixed list is easy to switch on.</td></tr>
                    <tr><td><code>created</code>, <code>updated</code></td><td>integer</td><td>Unix timestamps.</td></tr>
                    <tr><td><code>import</code></td><td>object</td><td><code>status</code>, <code>batch</code>, <code>imported_at</code>, <code>local_ref</code>. See <a href="imports.php" class="link">Imports</a>.</td></tr>
                </tbody>
            </table>

            <p>Filter any list by <code>import_status=pending|imported|rejected</code>.</p>

<?php foreach ($definitions as $segment => $spec): ?>
            <h2 id="<?php echo htmlspecialchars($spec['object']); ?>"><?php echo htmlspecialchars(ucfirst($spec['object'])); ?></h2>
            <p><code>/v1/<?php echo htmlspecialchars($segment); ?></code>, ids start with <code><?php echo htmlspecialchars($spec['prefix']); ?>_</code>.</p>
            <table>
                <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                <tbody>
<?php foreach ($spec['fields'] as $name => $field): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($name); ?></code></td>
                        <td><?php echo api_doc_type($field); ?></td>
                        <td><?php echo !empty($field['required']) ? 'Yes' : ''; ?></td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
<?php if (!empty($spec['filters'])): ?>
            <p><strong>List filters:</strong>
<?php
    $filters = [];
    foreach ($spec['filters'] as $name => $kind) {
        $filters[] = '<code>' . htmlspecialchars($name) . '</code>'
            . ($kind === 'date_range' ? ' <span class="muted">(also [gte], [gt], [lte], [lt])</span>' : '');
    }
    echo implode(', ', $filters);
?>
            </p>
<?php endif; ?>
<?php endforeach; ?>

            <h2 id="line_item">Line item</h2>
            <p>A sub-object of an expense or revenue. Retrieved with <code>expand[]=line_items</code> or its own endpoint. Line items have no import status of their own; they follow their parent.</p>
            <table>
                <thead><tr><th>Field</th><th>Type</th><th>Required</th></tr></thead>
                <tbody>
<?php foreach (api_line_item_definition()['fields'] as $name => $field): ?>
                    <tr>
                        <td><code><?php echo htmlspecialchars($name); ?></code></td>
                        <td><?php echo api_doc_type($field); ?></td>
                        <td><?php echo !empty($field['required']) ? 'Yes' : ''; ?></td>
                    </tr>
<?php endforeach; ?>
                </tbody>
            </table>

            <h2>Rules Worth Knowing Before You Hit Them</h2>
            <ul>
                <li><code>tax_amount</code> cannot exceed <code>amount</code>. Amounts are gross, tax included.</li>
                <li>A refund must use the same currency as the revenue it refunds, and the total refunded can never exceed the original. The error tells you how much is still refundable.</li>
                <li>A reference to an object that does not exist is rejected on the request that made it, not silently at import time.</li>
                <li>Passing a field we do not recognise is a <code>400</code> naming the field. A typo in an optional parameter should not be something you discover in three weeks.</li>
                <li><code>fee_amount</code> on revenue is for a platform's withheld cut. It becomes a separate expense in the merchant's books, so the sale keeps its gross value.</li>
            </ul>

            <div class="page-navigation">
                <a href="authentication.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Authentication</span>
                </a>
                <a href="imports.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Imports &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
