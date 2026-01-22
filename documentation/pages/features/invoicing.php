<?php
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
                <li>Add line items from your product catalog or enter custom items</li>
                <li>Set payment terms and due date</li>
                <li>Preview and send</li>
            </ol>

            <h2>Invoice Features</h2>
            <ul>
                <li><strong>Automatic Numbering:</strong> Sequential invoice numbers are generated automatically</li>
                <li><strong>Tax Calculations:</strong> Automatic tax calculations based on your settings</li>
                <li><strong>Multiple Currencies:</strong> Create invoices in any supported currency</li>
                <li><strong>Notes & Terms:</strong> Add custom notes and payment terms</li>
                <li><strong>Recurring Invoices:</strong> Set up invoices that repeat on a schedule</li>
            </ul>

            <h2>Sending Invoices</h2>
            <p>After creating an invoice, you can:</p>
            <ul>
                <li><strong>Preview:</strong> Review the invoice before sending</li>
                <li><strong>Create & Send:</strong> Send the invoice to your customer</li>
                <li><strong>Save as Draft:</strong> Save and send later</li>
            </ul>

            <h2>Payment Processing</h2>
            <p>Accept payments online with integrated payment providers:</p>
            <ul>
                <li><strong>Stripe:</strong> Accept credit cards, debit cards, and bank transfers</li>
                <li><strong>PayPal:</strong> Let customers pay with their PayPal account</li>
                <li><strong>Square:</strong> Process payments through Square</li>
            </ul>

            <div class="info-box">
                <p><strong>Note:</strong> You'll need to connect your payment provider account in Settings
                before customers can pay online. Each provider has their own fees and processing times.</p>
            </div>

            <h2>Payment Tracking</h2>
            <p>Keep track of all your invoices and their payment status:</p>
            <ul>
                <li><strong>Draft:</strong> Invoice created but not yet sent</li>
                <li><strong>Sent:</strong> Invoice delivered to customer</li>
                <li><strong>Viewed:</strong> Customer has opened the invoice</li>
                <li><strong>Partial:</strong> Customer has made a partial payment</li>
                <li><strong>Paid:</strong> Invoice fully paid</li>
                <li><strong>Overdue:</strong> Payment is past the due date</li>
            </ul>

            <h2>Automatic Reconciliation</h2>
            <p>When customers pay online, payments are automatically:</p>
            <ul>
                <li>Matched to the correct invoice</li>
                <li>Recorded in your transaction history</li>
                <li>Reflected in your reports and analytics</li>
            </ul>

            <h2>Invoice Dashboard</h2>
            <p>Monitor your invoicing at a glance with key metrics:</p>
            <ul>
                <li><strong>Total Outstanding:</strong> Total amount owed across all unpaid invoices</li>
                <li><strong>Paid This Month:</strong> Payments received in the current month</li>
                <li><strong>Overdue:</strong> Amount past due date</li>
                <li><strong>Due This Week:</strong> Invoices coming due soon</li>
            </ul>

            <div class="page-navigation">
                <a href="customers.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Customer Management
                </a>
                <a href="receipt-scanning.php" class="nav-button next">
                    Next: AI Receipt Scanning
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
