<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Premium Subscription Checkout - Argo Books">
    <meta name="author" content="Argo">
    <link rel="shortcut icon" type="image/x-icon" href="../../../resources/images/argo-logo/argo-icon.ico">
    <title>Premium Subscription Checkout - Argo Books</title>

    <?php
    session_start();
    require_once '../../../db_connect.php';
    require_once '../../../community/users/user_functions.php';
    require_once '../../../config/pricing.php';

    $pricing = get_pricing_config();

    // Require login to checkout
    require_login('pricing/premium/');

    $user_id = $_SESSION['user_id'];
    $user_email = $_SESSION['email'] ?? '';

    // Check if user already has an active subscription
    $existing_subscription = get_user_premium_subscription($user_id);
    $is_changing_method = isset($_GET['change_method']) && $_GET['change_method'] === '1';

    if ($existing_subscription && in_array($existing_subscription['status'], ['active', 'cancelled', 'payment_failed'])) {
        // User already has a subscription
        $has_valid_subscription = $existing_subscription['status'] === 'active' ||
            (in_array($existing_subscription['status'], ['cancelled', 'payment_failed']) && strtotime($existing_subscription['end_date']) > time());

        if ($has_valid_subscription && !$is_changing_method) {
            // Redirect to subscription page unless they're changing payment method
            header('Location: ../../../community/users/subscription.php');
            exit;
        }
    }

    // Get environment-based keys
    $is_production = $_ENV['APP_ENV'] === 'production';

    $stripe_publishable_key = $is_production
        ? $_ENV['STRIPE_LIVE_PUBLISHABLE_KEY']
        : $_ENV['STRIPE_SANDBOX_PUBLISHABLE_KEY'];

    $paypal_client_id = $is_production
        ? $_ENV['PAYPAL_LIVE_CLIENT_ID']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];

    // PayPal subscription plan IDs (create these in PayPal dashboard)
    $paypal_monthly_plan_id = $is_production
        ? ($_ENV['PAYPAL_LIVE_MONTHLY_PLAN_ID'] ?? '')
        : ($_ENV['PAYPAL_SANDBOX_MONTHLY_PLAN_ID'] ?? '');

    $paypal_yearly_plan_id = $is_production
        ? ($_ENV['PAYPAL_LIVE_YEARLY_PLAN_ID'] ?? '')
        : ($_ENV['PAYPAL_SANDBOX_YEARLY_PLAN_ID'] ?? '');

    $square_app_id = $is_production
        ? $_ENV['SQUARE_LIVE_APP_ID']
        : $_ENV['SQUARE_SANDBOX_APP_ID'];

    $square_location_id = $is_production
        ? $_ENV['SQUARE_LIVE_LOCATION_ID']
        : $_ENV['SQUARE_SANDBOX_LOCATION_ID'];

    // Get URL parameters - whitelist to allowed values to prevent injection
    $billing = isset($_GET['billing']) && in_array($_GET['billing'], ['monthly', 'yearly'], true)
        ? $_GET['billing']
        : 'monthly';

    // Calculate prices from centralized config
    $monthlyPrice = $pricing['premium_monthly_price'];
    $yearlyPrice = $pricing['premium_yearly_price'];

    if ($billing === 'yearly') {
        $basePrice = $yearlyPrice;
        $finalPrice = $yearlyPrice;
        $billingPeriod = 'year';
    } else {
        $basePrice = $monthlyPrice;
        $finalPrice = $monthlyPrice;
        $billingPeriod = 'month';
    }

    // Processing fee
    $feeToday = calculate_processing_fee($finalPrice);
    $totalToday = $finalPrice + $feeToday;

    // Renewal amounts (same as initial since no discount)
    $renewalBase = ($billing === 'yearly') ? $yearlyPrice : $monthlyPrice;
    $renewalFee = calculate_processing_fee($renewalBase);
    $renewalTotal = $renewalBase + $renewalFee;
    ?>

    <!-- Payment processor keys -->
    <?php $je = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT; ?>
    <script>
        window.PAYMENT_CONFIG = {
            stripe: {
                publishableKey: <?php echo json_encode($stripe_publishable_key, $je); ?>
            },
            paypal: {
                clientId: <?php echo json_encode($paypal_client_id, $je); ?>,
                monthlyPlanId: <?php echo json_encode($paypal_monthly_plan_id, $je); ?>,
                yearlyPlanId: <?php echo json_encode($paypal_yearly_plan_id, $je); ?>
            },
            square: {
                appId: <?php echo json_encode($square_app_id, $je); ?>,
                locationId: <?php echo json_encode($square_location_id, $je); ?>
            }
        };

        window.AI_SUBSCRIPTION = {
            billing: <?php echo json_encode($billing, $je); ?>,
            basePrice: <?php echo $basePrice; ?>,
            finalPrice: <?php echo $finalPrice; ?>,
            processingFee: <?php echo $feeToday; ?>,
            totalCharge: <?php echo $totalToday; ?>,
            userId: <?php echo $user_id; ?>,
            userEmail: <?php echo json_encode($user_email, $je); ?>,
            isUpdatingPaymentMethod: <?php echo $is_changing_method ? 'true' : 'false'; ?>
        };
    </script>

    <script src="main.js"></script>
    <script src="../../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../../resources/styles/link.css">
    <link rel="stylesheet" href="../../../resources/header/style.css">
    <link rel="stylesheet" href="../../../resources/header/dark.css">
    <link rel="stylesheet" href="../../../resources/footer/style.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <section class="checkout-container">
        <h1>Complete Your Premium Subscription</h1>

        <div class="checkout-form">
            <h2>Payment Details</h2>

            <div class="order-summary ai-order-summary">
                <h3>Order Summary</h3>
                <div class="order-item">
                    <span>Argo Premium (<?php echo ucfirst($billing); ?>)</span>
                    <span>$<?php echo number_format($basePrice, 2); ?> CAD</span>
                </div>
                <?php if ($feeToday > 0): ?>
                <div class="order-item">
                    <span>Processing Fee</span>
                    <span>$<?php echo number_format($feeToday, 2); ?> CAD</span>
                </div>
                <?php endif; ?>
                <div class="order-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($totalToday, 2); ?> CAD/<?php echo $billingPeriod; ?></span>
                </div>
            </div>

            <div class="subscription-notice">
                <p>You will be charged $<?php echo number_format($totalToday, 2); ?> CAD today, then $<?php echo number_format($renewalTotal, 2); ?> CAD/<?php echo $billingPeriod; ?> on each renewal.</p>
                <p>Cancel anytime from your account settings.</p>
            </div>

            <div id="stripe-container" style="display: none;">
                <form id="stripe-payment-form">
                    <div class="form-group">
                        <label for="card-holder">Cardholder Name</label>
                        <input type="text" id="card-holder" name="card-holder" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="card-element">Card Details</label>
                        <div id="card-element" class="form-control">
                            <!-- Stripe Elements Placeholder -->
                        </div>
                        <div id="card-errors" role="alert" class="stripe-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <button type="submit" id="stripe-submit-btn" class="checkout-btn ai-checkout-btn">
                        Subscribe - $<?php echo number_format($totalToday, 2); ?> CAD/<?php echo $billingPeriod; ?>
                    </button>
                </form>
            </div>

            <div id="square-container" style="display: none;">
                <!-- Square payment form will be inserted here by JavaScript -->
            </div>
        </div>
    </section>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>
</body>

</html>
