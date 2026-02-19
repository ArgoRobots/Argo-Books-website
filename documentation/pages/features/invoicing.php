<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Invoicing & Payments';
$pageDescription = 'Create professional invoices, track payments, and accept online payments with Argo Books invoicing and payment features.';
$currentPage = 'invoicing';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> Invoicing & Payments is available with the Premium plan.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Create professional invoices in seconds, track payment status, and get paid faster with
            integrated payment processing. Argo Books makes invoicing simple and efficient.</p>

            <h2>Creating Invoices</h2>
            <p>Generate professional invoices with just a few clicks:</p>
            <ol class="steps-list">
                <li>Navigate to the Invoices section</li>
                <li>Click "New Invoice"</li>
                <li>Select a customer or create a new one</li>
                <li>Add line items from your product catalog</li>
                <li>Set payment terms and due date</li>
                <li>Preview and send</li>
            </ol>

            <h2>Sending Invoices</h2>
            <ul>
                <li><strong>Preview:</strong> Review the invoice before sending</li>
                <li><strong>Create & Send:</strong> Send the invoice to your customer</li>
                <li><strong>Save as Draft:</strong> Save and send later</li>
            </ul>

            <h2>Payment Tracking</h2>
            <p>Keep track of all your invoices and their payment status:</p>
            <ul>
                <li><strong>Draft:</strong> Invoice created but not yet sent</li>
                <li><strong>Sent:</strong> Invoice delivered to customer</li>
                <li><strong>Partial:</strong> Customer has made a partial payment</li>
                <li><strong>Paid:</strong> Invoice fully paid</li>
                <li><strong>Overdue:</strong> Payment is past the due date</li>
            </ul>

            <div class="page-navigation">
                <a href="customers.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Customer Management
                </a>
                <a href="receipt-scanning.php" class="nav-button next">
                    Next: AI Receipt Scanning
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
