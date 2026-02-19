<?php

/**
 * This script handles resending subscription IDs to users
 */
session_start();
require_once '../../db_connect.php';
require_once 'user_functions.php';
require_once '../../email_sender.php';

// Initialize response variables
$subscription_success = '';
$subscription_error = '';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? '';

// Check if user has Premium subscription
$premium_subscription = get_user_premium_subscription($user_id);
$has_premium_subscription = ($premium_subscription !== null);

// Handle form submission for subscription ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_subscription']) && $has_premium_subscription) {
    $subscription_id = $premium_subscription['subscription_id'];
    $billing_cycle = $premium_subscription['billing_cycle'];
    $end_date = $premium_subscription['end_date'];
    $subscription_email = $premium_subscription['email'] ?? $email;

    // Send the subscription ID via email
    $send_to = !empty($subscription_email) ? $subscription_email : $email;
    $email_sent = resend_subscription_id_email($send_to, $subscription_id, $billing_cycle, $end_date);

    if ($email_sent) {
        $subscription_success = 'Your subscription ID has been sent to your email address.';
    } else {
        $subscription_error = 'Failed to send email. Please try again later or <a href="../../contact-us/">contact support</a>.';
    }
}

// If user has no subscription, redirect to profile
if (!$has_premium_subscription) {
    header('Location: profile.php');
    exit;
}

$page_title = 'Resend Subscription ID';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/A-logo.ico">
    <title><?php echo htmlspecialchars($page_title); ?> - Argo Community</title>

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/header/dark.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <style>
        .resend-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .resend-section.subscription {
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border-color: #c4b5fd;
        }
        .resend-section h3 {
            margin-top: 0;
            margin-bottom: 10px;
        }
        .resend-section.subscription h3 {
            color: #7c3aed;
        }
        .resend-section p {
            margin-bottom: 15px;
            color: #6b7280;
        }
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .error-message a {
            color: #721c24;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>

            <p class="auth-subtitle">We'll send your information to your registered email address: <strong><?php echo htmlspecialchars($email); ?></strong></p>

            <div class="resend-section subscription">
                <h3>Premium Subscription ID</h3>

                <?php if ($subscription_success): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($subscription_success); ?>
                    </div>
                <?php elseif ($subscription_error): ?>
                    <div class="error-message">
                        <?php echo $subscription_error; ?>
                    </div>
                <?php else: ?>
                    <p>Your Premium subscription ID is a unique identifier for your subscription. You may need it when contacting support.</p>
                    <form method="post">
                        <input type="hidden" name="resend_subscription" value="1">
                        <button type="submit" class="btn btn-purple">Send Subscription ID</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="centered" style="margin-top: 20px;">
                <a href="profile.php" class="btn btn-black">Back to Profile</a>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>
</body>

</html>
