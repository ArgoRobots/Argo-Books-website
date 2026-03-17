<?php require_once __DIR__ . '/../../../resources/icons.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Subscription Confirmed - Argo Books">
    <meta name="author" content="Argo">
    <link rel="shortcut icon" type="image/x-icon" href="../../../resources/images/argo-logo/argo-icon.ico">
    <title>Premium Subscription Confirmed - Argo Books</title>

    <script src="../../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="../../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../../resources/styles/button.css">
    <link rel="stylesheet" href="../../../resources/styles/link.css">
    <link rel="stylesheet" href="../../../resources/header/style.css">
    <link rel="stylesheet" href="../../../resources/header/dark.css">
    <link rel="stylesheet" href="../../../resources/footer/style.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <?php
    $subscriptionId = isset($_GET['subscription_id']) ? htmlspecialchars($_GET['subscription_id'], ENT_QUOTES, 'UTF-8') : 'N/A';
    $email = isset($_GET['email']) ? htmlspecialchars($_GET['email'], ENT_QUOTES, 'UTF-8') : '';
    ?>

    <div class="thank-you-container">
        <div class="success-icon">
            <?= svg_icon('check-pricing') ?>
        </div>

        <h1>You're All Set!</h1>
        <p class="subtitle">Your Premium subscription is now active. Welcome to the future of finance tracking!</p>

        <div class="subscription-details">
            <h2>Subscription Details</h2>

            <div class="subscription-id-box">
                <label>Your Subscription ID</label>
                <div class="subscription-id" id="subscription-id"><?php echo $subscriptionId; ?></div>
                <button class="copy-btn" onclick="copySubscriptionId()">Copy to Clipboard</button>
            </div>

            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span class="detail-value"><?php echo $email; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value" style="color: #059669;">Active</span>
            </div>
        </div>

        <div class="features-list">
            <div class="feature-item">
                <?= svg_icon('document') ?>
                <span>Unlimited Invoices & Payments</span>
            </div>
            <div class="feature-item">
                <?= svg_icon('calendar') ?>
                <span>AI Receipt Scanning <span>(500/month)</span></span>
            </div>
            <div class="feature-item">
                <?= svg_icon('package') ?>
                <span>predictive analytics</span>
            </div>
        </div>

        <div class="activation-steps">
            <h3>How to Activate Premium Features</h3>
            <ol>
                <li>Open <strong>Argo Books</strong> on your computer</li>
                <li>Click the blue <strong>Upgrade</strong> button in the top right corner</li>
                <li>Enter your <strong>Subscription ID</strong> shown above</li>
                <li>Click <strong>Activate</strong> and enjoy all premium features!</li>
            </ol>
        </div>

        <div class="cta-buttons">
            <a href="../../../downloads" class="btn btn-purple">Download Argo Books</a>
            <a href="../../../documentation/" class="btn btn-outline-purple">View Documentation</a>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        function copySubscriptionId() {
            const subscriptionId = document.getElementById('subscription-id').textContent;
            navigator.clipboard.writeText(subscriptionId).then(() => {
                const btn = document.querySelector('.copy-btn');
                btn.textContent = 'Copied!';
                btn.classList.add('copied');
                setTimeout(() => {
                    btn.textContent = 'Copy to Clipboard';
                    btn.classList.remove('copied');
                }, 2000);
            });
        }
    </script>
</body>

</html>
