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

            <h2>Online Payment Providers</h2>
            <p>Connect your existing payment processor accounts to accept payments online:</p>
            <ul>
                <li><strong>Stripe:</strong> Credit/debit cards, Apple Pay, Google Pay</li>
                <li><strong>PayPal:</strong> PayPal balance, cards, Pay Later options</li>
                <li><strong>Square:</strong> Cards, Square Cash, afterpay</li>
            </ul>

            <h2>Setting Up Payment Integration</h2>
            <ol class="steps-list">
                <li>Go to Settings > Payment Providers</li>
                <li>Select your preferred payment provider</li>
                <li>Click "Connect Account" to link your existing account (or create a new one)</li>
                <li>Authorize Argo Books to process payments on your behalf</li>
                <li>Configure your payment preferences</li>
            </ol>

            <div class="info-box">
                <p><strong>Note:</strong> You'll need an existing account with Stripe, PayPal, or Square. Argo Books connects to your account - we never store your payment credentials.</p>
            </div>

            <h2>Payment Portal</h2>
            <p>When you connect a payment provider, customers can pay invoices online:</p>
            <ul>
                <li>Customers receive invoices with a link to your payment portal</li>
                <li>They can pay securely using their preferred payment method</li>
                <li>Payments sync automatically with your Argo Books account</li>
            </ul>

            <h2>Accepting a Payment</h2>
            <p>When a customer clicks your payment link:</p>
            <ol class="steps-list">
                <li>They're taken to a secure payment page</li>
                <li>Customer enters their payment details</li>
                <li>Payment is processed through your connected provider</li>
                <li>You receive confirmation and funds are deposited to your account</li>
                <li>The invoice is automatically marked as paid in Argo Books</li>
            </ol>

            <h2>Automatic Reconciliation</h2>
            <p>Payments are automatically matched to invoices:</p>
            <ul>
                <li><strong>Auto-matching:</strong> Payments linked to invoices update automatically</li>
                <li><strong>Partial Payments:</strong> Track partial payments and remaining balances</li>
                <li><strong>Overpayments:</strong> Handle credits and refunds</li>
                <li><strong>Manual Matching:</strong> Match unlinked payments to invoices manually</li>
            </ul>

            <h2>Transaction Fees</h2>
            <p>Payment processing fees are charged by the payment provider, not Argo Books:</p>
            <ul>
                <li><strong>Stripe:</strong> Typically 2.9% + $0.30 per transaction</li>
                <li><strong>PayPal:</strong> Typically 2.9% + fixed fee (varies by currency)</li>
                <li><strong>Square:</strong> Typically 2.9% + $0.30 per transaction</li>
            </ul>

            <div class="info-box">
                <p><strong>Tip:</strong> Fee rates may vary based on your account type, location, and transaction volume. Check with your payment provider for exact rates.</p>
            </div>

            <h2>Payment Dashboard</h2>
            <p>Monitor your payments at a glance:</p>
            <ul>
                <li><strong>Received This Month:</strong> Total payments collected</li>
                <li><strong>Transactions:</strong> Number of payment transactions</li>
                <li><strong>Pending:</strong> Payments awaiting processing</li>
                <li><strong>Refunds:</strong> Refunded payments</li>
            </ul>

            <h2>Payment Security</h2>
            <p>Your payment data is protected:</p>
            <ul>
                <li>All payment processing happens on provider's secure servers</li>
                <li>Argo Books never stores card numbers or bank details</li>
                <li>PCI DSS compliant through certified providers</li>
                <li>End-to-end encryption for all transactions</li>
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
