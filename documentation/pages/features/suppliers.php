<?php
$pageTitle = 'Supplier Management';
$pageDescription = 'Learn how to manage suppliers in Argo Books. Add vendor details, track purchases, and link suppliers to products and transactions.';
$currentPage = 'suppliers';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Keep track of your vendors and where your products come from. The Suppliers page lets you store contact details, link suppliers to products, and see a complete purchase history for each vendor.</p>

            <h2>Adding a Supplier</h2>
            <ol class="steps-list">
                <li>Go to "Suppliers" in the navigation menu (under Management)</li>
                <li>Click "Add Supplier"</li>
                <li>Enter the supplier's name and contact information</li>
                <li>Add address details if applicable</li>
                <li>Save the supplier</li>
            </ol>

            <h2>Supplier Details</h2>
            <p>Each supplier profile can include:</p>
            <ul>
                <li><strong>Name:</strong> The business or vendor name</li>
                <li><strong>Contact Information:</strong> Phone number, email address</li>
                <li><strong>Address:</strong> Street address, city, state/province, country</li>
                <li><strong>Notes:</strong> Any additional information about the supplier</li>
            </ul>

            <h2>Linking Suppliers to Products</h2>
            <p>When adding or editing a product, you can assign a supplier to it. This helps you track which vendor supplies each product and makes it easier to manage reordering.</p>

            <h2>Linking Suppliers to Transactions</h2>
            <p>When recording an expense, you can select a supplier to associate with the transaction. This builds a purchase history for each supplier, which you can review from their profile.</p>

            <h2>Managing Existing Suppliers</h2>
            <p>From the Suppliers page, you can edit or delete any supplier using the action buttons in each row. Editing a supplier updates their information across all linked products and future transactions.</p>

            <div class="page-navigation">
                <a href="customers.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Customer Management
                </a>
                <a href="invoicing.php" class="nav-button next">
                    Next: Invoicing & Payments
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
