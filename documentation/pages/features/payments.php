<?php
$pageTitle = 'Payment System';
$pageDescription = 'Learn how to accept online payments with Stripe, PayPal, and Square integration in Argo Books.';
$currentPage = 'payments';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> Online payment processing is available with the Premium subscription.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Accept payments anywhere with integrated payment processing. Let customers pay invoices online with credit cards, debit cards, or bank transfers through Stripe, PayPal, or Square.</p>

            <h2>Supported Payment Providers</h2>
            <p>Connect your existing payment processor accounts:</p>
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

            <h2>Payment Links</h2>
            <p>Generate payment links for easy online collection:</p>
            <ul>
                <li><strong>Invoice Links:</strong> Attach payment links directly to invoices</li>
                <li><strong>Standalone Links:</strong> Create payment links for any amount</li>
                <li><strong>QR Codes:</strong> Generate scannable codes for in-person payments</li>
                <li><strong>Email Integration:</strong> Send payment requests via email</li>
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

            <h2>Payment Reports</h2>
            <p>Track your payment activity:</p>
            <ul>
                <li><strong>Transaction History:</strong> All payments received with details</li>
                <li><strong>Outstanding Payments:</strong> Invoices awaiting payment</li>
                <li><strong>Fee Summary:</strong> Total processing fees paid</li>
                <li><strong>Provider Breakdown:</strong> Payments by provider</li>
            </ul>

            <h2>Security</h2>
            <p>Your payment data is protected:</p>
            <ul>
                <li>All payment processing happens on provider's secure servers</li>
                <li>Argo Books never stores card numbers or bank details</li>
                <li>PCI DSS compliant through certified providers</li>
                <li>End-to-end encryption for all transactions</li>
            </ul>

            <div class="page-navigation">
                <a href="rental.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Rental Management
                </a>
                <a href="../reference/accepted-countries.php" class="nav-button next">
                    Next: Accepted Countries
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
