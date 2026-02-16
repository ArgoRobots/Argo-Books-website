<?php
/**
 * Invoice View Page (Customer-Facing)
 *
 * Displays a single invoice with payment options.
 * URL: /invoice/{token} (rewritten by .htaccess)
 *
 * No login required - token-based access.
 */

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../api/portal/portal-helper.php';

$token = $_GET['token'] ?? '';

// Validate token format
if (empty($token) || !preg_match('/^[a-fA-F0-9]{48}$/', $token)) {
    http_response_code(404);
    include __DIR__ . '/../error-pages/404.html';
    exit;
}

// Rate limiting
$clientIp = get_client_ip();
if (is_rate_limited($clientIp)) {
    http_response_code(429);
    echo '<!DOCTYPE html><html><head><title>Too Many Requests</title></head><body><h1>Too many requests. Please try again later.</h1></body></html>';
    exit;
}

// Get invoice data
$invoice = get_invoice_by_token($token);

if (!$invoice) {
    record_failed_lookup($clientIp);
    http_response_code(404);
    include __DIR__ . '/../error-pages/404.html';
    exit;
}

// Available payment methods
$paymentMethods = get_available_payment_methods($invoice);

// Get environment-based keys for payment SDKs
$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';

$stripe_publishable_key = '';
if (in_array('stripe', $paymentMethods)) {
    $stripe_publishable_key = $is_production
        ? $_ENV['STRIPE_LIVE_PUBLISHABLE_KEY']
        : $_ENV['STRIPE_SANDBOX_PUBLISHABLE_KEY'];
}

$paypal_client_id = '';
if (in_array('paypal', $paymentMethods)) {
    $paypal_client_id = $is_production
        ? $_ENV['PAYPAL_LIVE_CLIENT_ID']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
}

$square_app_id = '';
if (in_array('square', $paymentMethods)) {
    $square_app_id = $is_production
        ? $_ENV['SQUARE_LIVE_APP_ID']
        : $_ENV['SQUARE_SANDBOX_APP_ID'];
}

// Parse invoice data
$invoiceData = $invoice['invoice_data'] ?? [];
$lineItems = $invoiceData['lineItems'] ?? $invoiceData['LineItems'] ?? [];
$companyName = $invoice['company_name'] ?? '';
$companyLogo = $invoice['company_logo_url'] ?? '';
$customerName = $invoice['customer_name'] ?? '';
$invoiceId = $invoice['invoice_id'] ?? '';
$totalAmount = floatval($invoice['total_amount']);
$balanceDue = floatval($invoice['balance_due']);
$currency = $invoice['currency'] ?: 'USD';
$currencySymbol = $currency === 'CAD' ? 'CA$' : '$';
$dueDate = $invoice['due_date'] ?? '';
$status = $invoice['status'] ?? 'sent';
$issueDate = $invoiceData['issueDate'] ?? $invoiceData['IssueDate'] ?? $invoice['created_at'] ?? '';
$notes = $invoiceData['notes'] ?? $invoiceData['Notes'] ?? '';
$subtotal = $invoiceData['subtotal'] ?? $invoiceData['Subtotal'] ?? $totalAmount;
$taxAmount = $invoiceData['taxAmount'] ?? $invoiceData['TaxAmount'] ?? 0;
$taxRate = $invoiceData['taxRate'] ?? $invoiceData['TaxRate'] ?? '';
$amountPaid = $totalAmount - $balanceDue;

// Custom invoice HTML (rendered by Argo Books desktop app)
$customInvoiceHtml = $invoiceData['customInvoiceHtml'] ?? '';

// Company address info
$companyAddress = $invoiceData['companyAddress'] ?? $invoiceData['CompanyAddress'] ?? '';
$companyEmail = $invoiceData['companyEmail'] ?? $invoiceData['CompanyEmail'] ?? '';
$companyPhone = $invoiceData['companyPhone'] ?? $invoiceData['CompanyPhone'] ?? '';
$customerAddress = $invoiceData['customerAddress'] ?? $invoiceData['CustomerAddress'] ?? '';
$customerEmail = $invoice['customer_email'] ?? '';

// Determine if overdue
$isOverdue = false;
if ($dueDate && $status !== 'paid' && $status !== 'cancelled') {
    $isOverdue = strtotime($dueDate) < time();
    if ($isOverdue && $status !== 'partial') {
        $status = 'overdue';
    }
}

$isPaid = $status === 'paid' || $balanceDue <= 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Invoice <?php echo htmlspecialchars($invoiceId); ?> - <?php echo htmlspecialchars($companyName); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="/resources/images/argo-logo/A-logo.ico">

    <link rel="stylesheet" href="/resources/styles/custom-colors.css">
    <link rel="stylesheet" href="/portal/style.css">

    <?php if (!$isPaid && !empty($paymentMethods)): ?>
    <script>
        window.PORTAL_CONFIG = {
            invoiceToken: '<?php echo htmlspecialchars($token); ?>',
            invoiceId: '<?php echo htmlspecialchars($invoiceId); ?>',
            balanceDue: <?php echo $balanceDue; ?>,
            currency: '<?php echo htmlspecialchars($currency); ?>',
            currencySymbol: '<?php echo $currencySymbol; ?>',
            paymentMethods: <?php echo json_encode($paymentMethods); ?>,
            stripe: {
                publishableKey: '<?php echo $stripe_publishable_key; ?>',
                accountId: '<?php echo htmlspecialchars($invoice['stripe_account_id'] ?? ''); ?>'
            },
            paypal: {
                clientId: '<?php echo $paypal_client_id; ?>'
            },
            square: {
                appId: '<?php echo $square_app_id; ?>'
            },
            apiBase: '/api/portal'
        };
    </script>
    <script src="/portal/main.js" defer></script>
    <?php endif; ?>
</head>
<body>
    <div class="portal-page">
        <!-- Header -->
        <header class="portal-header">
            <div class="portal-header-inner">
                <?php if (!empty($companyLogo)): ?>
                    <img src="<?php echo htmlspecialchars($companyLogo); ?>" alt="<?php echo htmlspecialchars($companyName); ?>" class="company-logo">
                <?php endif; ?>
                <div class="company-info">
                    <h1 class="company-name"><?php echo htmlspecialchars($companyName); ?></h1>
                    <span class="portal-subtitle">Invoice Portal</span>
                </div>
                <?php if (!empty($invoice['customer_token'])): ?>
                    <a href="/portal/<?php echo htmlspecialchars($invoice['customer_token']); ?>" class="portal-all-invoices-link">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        View all invoices
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <main class="portal-main">
            <?php if (!empty($customInvoiceHtml)): ?>
                <!-- Custom Invoice rendered by Argo Books -->
                <?php if ($amountPaid > 0 || $isPaid): ?>
                    <div class="invoice-status-bar">
                        <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                            <?php echo ucfirst(htmlspecialchars($status)); ?>
                        </span>
                        <?php if ($amountPaid > 0 && !$isPaid): ?>
                            <span class="status-bar-detail">
                                Paid: <?php echo $currencySymbol . number_format($amountPaid, 2); ?>
                            </span>
                            <span class="status-bar-detail">
                                Balance Due: <strong><?php echo $currencySymbol . number_format($balanceDue, 2); ?> <?php echo $currency; ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div class="custom-invoice-container">
                    <iframe
                        id="custom-invoice-frame"
                        srcdoc="<?php echo htmlspecialchars($customInvoiceHtml); ?>"
                        sandbox="allow-same-origin"
                        class="custom-invoice-iframe"
                        scrolling="no"
                        frameborder="0">
                    </iframe>
                </div>
                <script>
                (function() {
                    var iframe = document.getElementById('custom-invoice-frame');
                    iframe.addEventListener('load', function() {
                        try {
                            var doc = iframe.contentDocument;

                            // Strip email wrapper styling so the invoice fills the container
                            var style = doc.createElement('style');
                            style.textContent =
                                'html, body { margin: 0 !important; padding: 0 !important; background: transparent !important; overflow: hidden !important; }' +
                                'body > table { background: transparent !important; }' +
                                'body > table > tbody > tr > td { padding: 0 !important; }' +
                                'body > table > tbody > tr > td > table { max-width: 100% !important; width: 100% !important; box-shadow: none !important; border-radius: 0 !important; }';
                            doc.head.appendChild(style);

                            // Auto-resize height to content
                            iframe.style.height = doc.documentElement.scrollHeight + 'px';
                        } catch(e) {}
                    });
                    window.addEventListener('resize', function() {
                        setTimeout(function() {
                            try {
                                iframe.style.height = iframe.contentDocument.documentElement.scrollHeight + 'px';
                            } catch(e) {}
                        }, 100);
                    });
                })();
                </script>
            <?php else: ?>
                <!-- Standard invoice template -->
                <div class="invoice-header-section">
                    <div class="invoice-title-row">
                        <h2 class="invoice-title">Invoice <?php echo htmlspecialchars($invoiceId); ?></h2>
                        <span class="status-badge status-<?php echo htmlspecialchars($status); ?>">
                            <?php echo ucfirst(htmlspecialchars($status)); ?>
                        </span>
                    </div>

                    <div class="invoice-parties">
                        <div class="party-info">
                            <span class="party-label">From</span>
                            <strong><?php echo htmlspecialchars($companyName); ?></strong>
                            <?php if ($companyAddress): ?>
                                <span class="party-detail"><?php echo nl2br(htmlspecialchars($companyAddress)); ?></span>
                            <?php endif; ?>
                            <?php if ($companyEmail): ?>
                                <span class="party-detail"><?php echo htmlspecialchars($companyEmail); ?></span>
                            <?php endif; ?>
                            <?php if ($companyPhone): ?>
                                <span class="party-detail"><?php echo htmlspecialchars($companyPhone); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="party-info">
                            <span class="party-label">To</span>
                            <strong><?php echo htmlspecialchars($customerName); ?></strong>
                            <?php if ($customerAddress): ?>
                                <span class="party-detail"><?php echo nl2br(htmlspecialchars($customerAddress)); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="invoice-dates">
                        <?php if ($issueDate): ?>
                            <div class="date-item">
                                <span class="date-label">Issue Date</span>
                                <span class="date-value"><?php echo date('M j, Y', strtotime($issueDate)); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($dueDate): ?>
                            <div class="date-item <?php echo $isOverdue ? 'overdue' : ''; ?>">
                                <span class="date-label">Due Date</span>
                                <span class="date-value"><?php echo date('M j, Y', strtotime($dueDate)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="invoice-items-section">
                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th class="col-description">Description</th>
                                <th class="col-qty">Qty</th>
                                <th class="col-price">Price</th>
                                <th class="col-total">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($lineItems)): ?>
                                <?php foreach ($lineItems as $item): ?>
                                    <tr>
                                        <td class="col-description">
                                            <?php echo htmlspecialchars($item['description'] ?? $item['Description'] ?? ''); ?>
                                        </td>
                                        <td class="col-qty">
                                            <?php echo htmlspecialchars($item['quantity'] ?? $item['Quantity'] ?? 1); ?>
                                        </td>
                                        <td class="col-price">
                                            <?php echo $currencySymbol . number_format(floatval($item['unitPrice'] ?? $item['UnitPrice'] ?? $item['price'] ?? $item['Price'] ?? 0), 2); ?>
                                        </td>
                                        <td class="col-total">
                                            <?php echo $currencySymbol . number_format(floatval($item['total'] ?? $item['Total'] ?? $item['amount'] ?? $item['Amount'] ?? 0), 2); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="no-items">Invoice details</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <div class="invoice-totals">
                        <?php if ($subtotal != $totalAmount): ?>
                            <div class="total-row">
                                <span>Subtotal</span>
                                <span><?php echo $currencySymbol . number_format(floatval($subtotal), 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($taxAmount > 0): ?>
                            <div class="total-row">
                                <span>Tax<?php echo $taxRate ? " ({$taxRate}%)" : ''; ?></span>
                                <span><?php echo $currencySymbol . number_format(floatval($taxAmount), 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="total-row total-row-main">
                            <span>Total</span>
                            <span><?php echo $currencySymbol . number_format($totalAmount, 2); ?> <?php echo $currency; ?></span>
                        </div>
                        <?php if ($amountPaid > 0): ?>
                            <div class="total-row total-row-paid">
                                <span>Paid</span>
                                <span>-<?php echo $currencySymbol . number_format($amountPaid, 2); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="total-row total-row-balance <?php echo $isPaid ? 'paid' : ($isOverdue ? 'overdue' : ''); ?>">
                            <span>Balance Due</span>
                            <span><?php echo $currencySymbol . number_format($balanceDue, 2); ?> <?php echo $currency; ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($notes): ?>
                    <div class="invoice-notes">
                        <h3>Notes</h3>
                        <p><?php echo nl2br(htmlspecialchars($notes)); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Payment Section -->
            <?php if (!$isPaid && !empty($paymentMethods)): ?>
                <div class="payment-section" id="payment-section">
                    <h3>Pay This Invoice</h3>

                    <!-- Payment Amount -->
                    <div class="payment-amount-section">
                        <div class="amount-options">
                            <label class="amount-option">
                                <input type="radio" name="payment_type" value="full" checked>
                                <span>Pay full balance: <strong><?php echo $currencySymbol . number_format($balanceDue, 2); ?> <?php echo $currency; ?></strong></span>
                            </label>
                            <label class="amount-option">
                                <input type="radio" name="payment_type" value="partial">
                                <span>Pay a different amount:</span>
                            </label>
                        </div>
                        <div class="partial-amount-input" id="partial-amount-wrapper" style="display: none;">
                            <span class="currency-prefix"><?php echo $currencySymbol; ?></span>
                            <input type="number" id="partial-amount" min="0.01" max="<?php echo $balanceDue; ?>"
                                   step="0.01" placeholder="0.00" class="form-control">
                            <span class="currency-suffix"><?php echo $currency; ?></span>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="payment-methods" id="payment-methods">
                        <p class="method-label">Pay with:</p>
                        <div class="method-buttons">
                            <?php if (in_array('stripe', $paymentMethods)): ?>
                                <button type="button" class="method-btn" data-method="stripe">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                    Credit Card
                                </button>
                            <?php endif; ?>
                            <?php if (in_array('paypal', $paymentMethods)): ?>
                                <button type="button" class="method-btn" data-method="paypal">
                                    <svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/></svg>
                                    PayPal
                                </button>
                            <?php endif; ?>
                            <?php if (in_array('square', $paymentMethods)): ?>
                                <button type="button" class="method-btn" data-method="square">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg>
                                    Square
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Payment Form Container (dynamically populated by JS) -->
                    <div id="payment-form-container" style="display: none;"></div>
                </div>
            <?php elseif ($isPaid): ?>
                <div class="invoice-paid-banner">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <h3>This invoice has been paid</h3>
                    <p>Thank you for your payment.</p>
                </div>
            <?php else: ?>
                <div class="invoice-no-methods">
                    <p>Online payments are not available for this invoice. Please contact <?php echo htmlspecialchars($companyName); ?> for payment instructions.</p>
                </div>
            <?php endif; ?>

            <!-- Confirmation Section (shown after payment) -->
            <div id="payment-confirmation" class="payment-confirmation" style="display: none;">
                <svg class="confirmation-icon" viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <h3>Payment Successful</h3>
                <div class="confirmation-details">
                    <div class="detail-row">
                        <span>Amount Paid</span>
                        <span id="conf-amount"></span>
                    </div>
                    <div class="detail-row">
                        <span>Reference</span>
                        <span id="conf-reference"></span>
                    </div>
                    <div class="detail-row">
                        <span>Payment Method</span>
                        <span id="conf-method"></span>
                    </div>
                    <div class="detail-row">
                        <span>Date</span>
                        <span id="conf-date"></span>
                    </div>
                    <div class="detail-row">
                        <span>Invoice</span>
                        <span id="conf-invoice"><?php echo htmlspecialchars($invoiceId); ?></span>
                    </div>
                </div>
                <p class="confirmation-note">A confirmation has been sent. You can close this page.</p>
            </div>
        </main>

        <!-- Footer -->
        <footer class="portal-footer">
            <p>Powered by <a href="https://argorobots.com" target="_blank" rel="noopener">Argo Books</a></p>
        </footer>
    </div>
</body>
</html>
