<?php
require_once __DIR__ . '/../../../config/pricing.php';
require_once __DIR__ . '/../../../resources/icons.php';
require_once __DIR__ . '/../../../resources/components/feature-video.php';
$pricing = get_pricing_config();
$pageTitle = 'Invoicing & Payments';
$pageDescription = 'Create professional invoices, track payments, and accept online payments with Argo Books invoicing and payment features.';
$currentPage = 'invoicing';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Create professional invoices in seconds, track payment status, and get paid faster with
            integrated payment processing. Argo Books makes invoicing simple and efficient.</p>

            <div class="info-box">
                <p><strong>Note:</strong> The free version includes up to <?= (int) $pricing['free_invoice_monthly_limit'] ?> invoices per month. <a href="../getting-started/version-comparison.php" class="link">Upgrade to Premium</a> for unlimited invoices and online payment integration.</p>
            </div>

            <?php docs_video_embed('cTQaejF6Gh0', 'Argo Books Invoicing'); ?>

            <h2>Setting Up Payment Integration</h2>
            <p>This is a one-time setup and should be your first step: it's what enables customers to pay your invoices online. Argo Books supports <strong>Stripe</strong>, <strong>PayPal</strong>, and <strong>Square</strong>, covering credit/debit cards, Apple Pay, Google Pay, PayPal balance, and more.</p>
            <ol class="steps-list">
                <li>Go to Settings > Payment Portal</li>
                <li>Add your company name and logo. These appear on the payment portal your customers see when paying an invoice</li>
                <li>Under "Connected Payment Providers", select your preferred provider (Stripe, PayPal, or Square)</li>
                <li>Click "Connect" to link your existing account (or create a new one)</li>
                <li>Authorize Argo Books to process payments on your behalf</li>
            </ol>

            <div class="info-box">
                <p><strong>Note:</strong> A standard <strong>Stripe</strong>, <strong>PayPal Business</strong>, or <strong>Square seller</strong> account is all you need. If you don't already have one, you can create it during the "Connect" step. Argo Books connects to your account - we never store your payment credentials.</p>
            </div>

            <h2>Creating Invoices</h2>
            <p>Now that your payment integration is set up, you can now generate professional invoices with just a few clicks:</p>
            <ol class="steps-list">
                <li>Go to "Invoices" in the navigation menu (under Transactions)</li>
                <li>Click "Create Invoice"</li>
                <li>Select a customer or create a new one</li>
                <li>Add line items from your product catalog</li>
                <li>Set payment terms and due date</li>
                <li>Preview and send</li>
            </ol>

            <h2>Payment Portal</h2>
            <p>When you connect a payment provider, customers can pay invoices online:</p>
            <ul>
                <li>Customers receive an email with their invoice and a link to your payment portal</li>
                <li>They can pay securely using their preferred payment method</li>
                <li>Payments sync automatically with your Argo Books company</li>
            </ul>

            <h2>Payment Tracking</h2>
            <p>Keep track of all your invoices and their payment status:</p>
            <ul>
                <li><strong>Draft:</strong> Invoice created but not yet sent</li>
                <li><strong>Sent:</strong> Invoice delivered to customer</li>
                <li><strong>Partial:</strong> Customer has made a partial payment</li>
                <li><strong>Paid:</strong> Invoice fully paid</li>
                <li><strong>Overdue:</strong> Payment is past the due date</li>
                <li><strong>Cancelled:</strong> Invoice has been cancelled</li>
            </ul>

            <div class="info-box">
                <p><strong>Note:</strong> When you record a payment manually, link it to an invoice or to a revenue so it stays tied to your income. Payments made through the online portal are linked to their invoice automatically.</p>
            </div>

            <h2>Payment processing fees</h2>
            <p>Payment processing fees are charged by the payment provider, not Argo Books. Most providers typically charge around 2.9% + $0.30 per transaction, including Stripe, PayPal, and Square. Because the exact rate depends on many different factors, Argo Books adds the 2.9% + $0.30 for every transaction.</p>
            <p>You decide if you want to pass this fee onto your customer, or take the cost yourself. There is a "pass processing fee" toggle:</p>
            <ul>
                <li><strong>Toggle on</strong> → passes the cost on to the customer. Customer pays invoice total + 2.9% + $0.30.</li>
                <li><strong>Toggle off</strong> → customer pays just the invoice total. You absorb the fee.</li>
            </ul>
            <p>When your customers pay the invoices, the total amount and the fee go into your account. It's when you take the money out of your Stripe/PayPal/Square account and move it into your normal bank account that they charge you. The fee may be slightly different than the 2.9% + $0.30, especially if your customer is in a different country.</p>
            <p>Argo Books does not add any extra fee on top of the payment providers.</p>
            
            <div class="info-box">
                <p><strong>Tip:</strong> Payment processing fees are optionally passed on to the customer at checkout. To absorb the fees yourself instead, create an invoice template, go to the "Display" tab, and uncheck "Pass processing fees to customers".</p>
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
                <li>All payment processing happens on the payment provider's secure servers</li>
                <li>Argo Books never stores card numbers or bank details</li>
                <li>PCI DSS compliant through certified providers</li>
                <li>End-to-end encryption for all transactions</li>
            </ul>

            <div class="page-navigation">
                <a href="sales-tracking.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Expense/Revenue Tracking</span>
                </a>
                <a href="bank-matching.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Bank Matching &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
