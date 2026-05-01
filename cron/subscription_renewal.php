<?php
/**
 * Premium Subscription Renewal Cron Job
 *
 * This script should be run daily via cron to check for and process subscription renewals.
 *
 * RECOMMENDED SCHEDULE: Daily at midnight (00:00)
 *
 * Example cron entry:
 *
 *   0 0 * * * /usr/bin/php /path/to/subscription_renewal.php
 *
 * The script will:
 *   1. Find active subscriptions due for renewal within 24 hours
 *   2. Process credit-based renewals first (no charge)
 *   3. Charge payment methods (Stripe/Square) for remaining balance
 *   4. Send email receipts for successful renewals
 *   5. Send failure notifications for failed payments
 *   6. Suspend subscriptions after 3 consecutive failures
 *   7. Mark non-auto-renew subscriptions as expired
 *
 * Manual execution:
 *   php subscription_renewal.php
 *
 * Logs are stored in: /cron/logs/subscription_renewal_YYYY-MM-DD.log
 */

// Prevent timeout for long-running process
set_time_limit(300);

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request)
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
require_once __DIR__ . '/../config/pricing.php';

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../email_sender.php';

// Configure logging
function logMessage($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";

    // Log to file
    $logFile = __DIR__ . '/logs/subscription_renewal_' . date('Y-m-d') . '.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    // Also output to CLI
    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

logMessage('Starting subscription renewal check...');

// Get environment configuration
$isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production';

// Initialize Stripe
$stripeSecretKey = $isProduction
    ? $_ENV['STRIPE_LIVE_SECRET_KEY']
    : $_ENV['STRIPE_SANDBOX_SECRET_KEY'];
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Square configuration
$squareAccessToken = $isProduction
    ? $_ENV['SQUARE_LIVE_ACCESS_TOKEN']
    : $_ENV['SQUARE_SANDBOX_ACCESS_TOKEN'];
$squareEnvironment = $isProduction ? 'production' : 'sandbox';

// Find subscriptions due for renewal (within next 24 hours or already past due)
try {
    $stmt = $pdo->prepare("
        SELECT
            s.*,
            u.username,
            u.email as user_email
        FROM premium_subscriptions s
        JOIN community_users u ON s.user_id = u.id
        WHERE s.status = 'active'
        AND s.end_date <= DATE_ADD(NOW(), INTERVAL 1 DAY)
        AND s.auto_renew = 1
        ORDER BY s.end_date ASC
    ");
    $stmt->execute();
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    logMessage("Found " . count($subscriptions) . " subscriptions due for renewal");

} catch (PDOException $e) {
    logMessage("Database error fetching subscriptions: " . $e->getMessage(), 'ERROR');
    exit(1);
}

$successCount = 0;
$failedCount = 0;
$skippedCount = 0;

foreach ($subscriptions as $subscription) {
    $subscriptionId = $subscription['subscription_id'];
    $userId = $subscription['user_id'];
    $email = $subscription['email'];
    $billing = $subscription['billing_cycle'];
    $paymentMethod = $subscription['payment_method'];
    $paymentToken = $subscription['payment_token'];
    $creditBalance = floatval($subscription['credit_balance'] ?? 0);

    logMessage("Processing renewal for subscription: $subscriptionId (User: $userId, Method: $paymentMethod, Credit: $$creditBalance)");

    // Calculate renewal amount from centralized config
    $pricingConfig = get_pricing_config();
    $baseMonthly = $pricingConfig['premium_monthly_price'];
    $baseYearly = $pricingConfig['premium_yearly_price'];
    $amount = ($billing === 'yearly') ? $baseYearly : $baseMonthly;

    // Check if renewal can be covered by credit
    $useCredit = false;
    $creditUsed = 0;
    $amountToCharge = $amount;

    if ($creditBalance > 0) {
        if ($creditBalance >= $amount) {
            // Full renewal covered by credit - no charge needed
            $useCredit = true;
            $creditUsed = $amount;
            $amountToCharge = 0;
            logMessage("Renewal for $subscriptionId covered by credit balance ($$creditBalance)");
        } else {
            // Partial credit - charge the difference
            $creditUsed = $creditBalance;
            $amountToCharge = $amount - $creditBalance;
            logMessage("Partial credit ($$creditBalance) applied for $subscriptionId, charging $$amountToCharge");
        }
    }

    // Add processing fee on the amount actually charged to the card
    if ($amountToCharge > 0) {
        $processingFee = calculate_processing_fee($amountToCharge);
        $amountToCharge += $processingFee;
    }

    // Skip payment processing if fully covered by credit
    if ($amountToCharge <= 0 && $useCredit) {
        try {
            // Update subscription dates
            $newEndDate = calculateNewEndDate($subscription['end_date'], $billing);
            $newCreditBalance = $creditBalance - $creditUsed;

            $stmt = $pdo->prepare("
                UPDATE premium_subscriptions
                SET end_date = ?,
                    credit_balance = ?,
                    updated_at = NOW()
                WHERE subscription_id = ?
            ");
            $stmt->execute([$newEndDate, $newCreditBalance, $subscriptionId]);

            // Log the credit-based payment (no actual charge)
            $stmt = $pdo->prepare("
                INSERT INTO premium_subscription_payments (
                    subscription_id, amount, currency, payment_method,
                    transaction_id, status, payment_type, created_at
                ) VALUES (?, 0, 'CAD', ?, ?, 'completed', 'credit', NOW())
            ");
            $creditTransactionId = 'CREDIT_RENEWAL_' . strtoupper(bin2hex(random_bytes(8)));
            $stmt->execute([$subscriptionId, $paymentMethod, $creditTransactionId]);

            // DO NOT send receipt email for $0 credit-based renewals
            logMessage("Successfully renewed $subscriptionId using credit - new end date: $newEndDate, remaining credit: $$newCreditBalance");
            $successCount++;
            continue;
        } catch (Exception $e) {
            logMessage("Failed to process credit-based renewal for $subscriptionId: " . $e->getMessage(), 'ERROR');
            $failedCount++;
            continue;
        }
    }

    // Skip if no payment token stored and we need to charge
    if (empty($paymentToken) && $amountToCharge > 0) {
        logMessage("No payment token stored for $subscriptionId - skipping", 'WARNING');
        $skippedCount++;
        continue;
    }

    // Process payment based on method
    $paymentResult = null;
    $transactionId = null;

    try {
        // Idempotency guard: if a successful renewal payment already exists for
        // this subscription within the last 23 hours, skip — this prevents a
        // double-charge if the cron is fired twice (overlapping runs, retry,
        // accidental re-invoke).
        $stmt = $pdo->prepare("
            SELECT 1 FROM premium_subscription_payments
            WHERE subscription_id = ?
              AND status = 'completed'
              AND payment_type = 'renewal'
              AND created_at > DATE_SUB(NOW(), INTERVAL 23 HOUR)
            LIMIT 1
        ");
        $stmt->execute([$subscriptionId]);
        if ($stmt->fetch()) {
            logMessage("Skipping $subscriptionId - already renewed within the last 23 hours", 'INFO');
            $skippedCount++;
            continue;
        }

        switch ($paymentMethod) {
            case 'stripe':
                $stripeCustomerId = $subscription['stripe_customer_id'] ?? null;
                $paymentResult = processStripeRenewal($paymentToken, $amountToCharge, $subscriptionId, $email, $stripeCustomerId);
                break;
            case 'square':
                $paymentResult = processSquareRenewal($paymentToken, $amountToCharge, $subscriptionId, $email, $squareAccessToken, $squareEnvironment);
                break;
            case 'paypal':
                // Check if this is a PayPal Subscription (managed by PayPal)
                if (!empty($subscription['paypal_subscription_id'])) {
                    // PayPal Subscriptions are automatically renewed by PayPal
                    // Renewals are handled via PayPal webhooks
                    logMessage("PayPal subscription {$subscription['paypal_subscription_id']} - managed by PayPal webhooks", 'INFO');
                    $skippedCount++;
                    continue 2;
                }
                // One-time PayPal payment - no recurring billing available
                logMessage("PayPal one-time payment - no recurring billing token available", 'WARNING');
                $skippedCount++;
                continue 2;
            default:
                logMessage("Unknown payment method: $paymentMethod", 'WARNING');
                $skippedCount++;
                continue 2;
        }

        if ($paymentResult['success']) {
            $transactionId = $paymentResult['transaction_id'];

            // Update subscription dates and credit balance
            $newEndDate = calculateNewEndDate($subscription['end_date'], $billing);
            $newCreditBalance = $creditBalance - $creditUsed; // Deduct any used credit

            // Wrap the two writes in a transaction so we can never end up with
            // an extended subscription but no payment record (or vice versa).
            // The charge has already been made by this point — if the DB writes
            // fail we log loudly so an operator can fix the DB state manually.
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("
                    UPDATE premium_subscriptions
                    SET end_date = ?,
                        credit_balance = ?,
                        updated_at = NOW()
                    WHERE subscription_id = ?
                ");
                $stmt->execute([$newEndDate, $newCreditBalance, $subscriptionId]);

                // Log payment (log the actual amount charged, not the full renewal amount)
                $stmt = $pdo->prepare("
                    INSERT INTO premium_subscription_payments (
                        subscription_id, amount, currency, payment_method,
                        transaction_id, status, payment_type, created_at
                    ) VALUES (?, ?, 'CAD', ?, ?, 'completed', 'renewal', NOW())
                ");
                $stmt->execute([$subscriptionId, $amountToCharge, $paymentMethod, $transactionId]);

                $pdo->commit();
            } catch (Exception $dbEx) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                logMessage(
                    "CRITICAL: charged $subscriptionId (txn $transactionId, $$amountToCharge) but DB write failed: "
                    . $dbEx->getMessage() . " — manual fix required",
                    'ERROR'
                );
                throw $dbEx;
            }

            // Send receipt email (only for actual charges, not credit-covered renewals)
            if ($amountToCharge > 0) {
                send_premium_subscription_receipt(
                    $email,
                    $subscriptionId,
                    $billing,
                    $amountToCharge,
                    $newEndDate,
                    $transactionId,
                    $paymentMethod
                );
            }

            $creditMessage = $creditUsed > 0 ? " ($$creditUsed credit applied)" : "";
            logMessage("Successfully renewed $subscriptionId - new end date: $newEndDate, charged: $$amountToCharge$creditMessage");
            $successCount++;

        } else {
            throw new Exception($paymentResult['error'] ?? 'Payment failed');
        }

    } catch (Exception $e) {
        logMessage("Failed to renew $subscriptionId: " . $e->getMessage(), 'ERROR');

        // Log failed attempt
        $stmt = $pdo->prepare("
            INSERT INTO premium_subscription_payments (
                subscription_id, amount, currency, payment_method,
                transaction_id, status, payment_type, error_message, created_at
            ) VALUES (?, ?, 'CAD', ?, NULL, 'failed', 'renewal', ?, NOW())
        ");
        $stmt->execute([$subscriptionId, $amount, $paymentMethod, $e->getMessage()]);

        // Send payment failed notification
        send_payment_failed_email($email, $subscriptionId, $e->getMessage());

        // If multiple failures, consider suspending
        $failureCount = getRecentFailureCount($pdo, $subscriptionId);
        if ($failureCount >= 3) {
            $stmt = $pdo->prepare("
                UPDATE premium_subscriptions
                SET status = 'payment_failed',
                    updated_at = NOW()
                WHERE subscription_id = ?
            ");
            $stmt->execute([$subscriptionId]);
            logMessage("Subscription $subscriptionId suspended after $failureCount failures", 'WARNING');
        }

        $failedCount++;
    }
}

logMessage("Renewal processing complete. Success: $successCount, Failed: $failedCount, Skipped: $skippedCount");

// Also check for subscriptions that should be marked as expired
try {
    $stmt = $pdo->prepare("
        UPDATE premium_subscriptions
        SET status = 'expired',
            updated_at = NOW()
        WHERE status = 'active'
        AND auto_renew = 0
        AND end_date < NOW()
    ");
    $stmt->execute();
    $expiredCount = $stmt->rowCount();

    if ($expiredCount > 0) {
        logMessage("Marked $expiredCount subscriptions as expired (auto-renew disabled)");
    }
} catch (PDOException $e) {
    logMessage("Error marking expired subscriptions: " . $e->getMessage(), 'ERROR');
}

// Cleanup: clear stale previous_paypal_subscription_id values from PayPal
// cycle switches that happened more than 7 days ago. The column exists only
// to let the cancel webhook recognize an expected cancel event for the
// pre-switch subscription; once that event has had a week to arrive, we no
// longer need the back-reference.
//
// Cutoff is keyed on last_cycle_change_at, NOT updated_at — updated_at gets
// auto-bumped by every renewal/admin edit (ON UPDATE CURRENT_TIMESTAMP), so
// for monthly subs it would never reach the 7-day threshold.
try {
    $stmt = $pdo->prepare("
        UPDATE premium_subscriptions
        SET previous_paypal_subscription_id = NULL
        WHERE previous_paypal_subscription_id IS NOT NULL
          AND last_cycle_change_at IS NOT NULL
          AND last_cycle_change_at < NOW() - INTERVAL 7 DAY
    ");
    $stmt->execute();
    $cleared = $stmt->rowCount();
    if ($cleared > 0) {
        logMessage("Cleared previous_paypal_subscription_id on $cleared row(s)");
    }
} catch (PDOException $e) {
    logMessage("Error clearing previous_paypal_subscription_id: " . $e->getMessage(), 'ERROR');
}

/**
 * Process Stripe renewal payment
 */
function processStripeRenewal($paymentMethodId, $amount, $subscriptionId, $email, $customerId = null) {
    try {
        // Build payment intent params
        $params = [
            'amount' => intval($amount * 100), // Stripe uses cents
            'currency' => 'cad',
            'payment_method' => $paymentMethodId,
            'confirm' => true,
            'off_session' => true,
            'description' => "Premium Subscription Renewal - $subscriptionId",
            'receipt_email' => $email,
            'metadata' => [
                'subscription_id' => $subscriptionId,
                'type' => 'renewal'
            ]
        ];

        // Include customer ID if available (required for saved payment methods)
        if ($customerId) {
            $params['customer'] = $customerId;
        }

        // Create payment intent
        $paymentIntent = \Stripe\PaymentIntent::create($params);

        if ($paymentIntent->status === 'succeeded') {
            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Payment not completed: ' . $paymentIntent->status
            ];
        }
    } catch (\Stripe\Exception\CardException $e) {
        return [
            'success' => false,
            'error' => 'Card declined: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Process Square renewal payment
 */
function processSquareRenewal($cardId, $amount, $subscriptionId, $email, $accessToken, $environment) {
    try {
        $locationId = ($environment === 'production')
            ? $_ENV['SQUARE_LIVE_LOCATION_ID']
            : $_ENV['SQUARE_SANDBOX_LOCATION_ID'];

        $apiBaseUrl = ($environment === 'production')
            ? 'https://connect.squareup.com/v2'
            : 'https://connect.squareupsandbox.com/v2';

        // Validate card ID format to prevent SSRF via URL path injection
        if (!preg_match('/^[A-Za-z0-9\-_:]+$/', $cardId)) {
            return ['success' => false, 'error' => 'Invalid card ID format'];
        }

        // First, retrieve the card to get the customer_id (required for card-on-file payments)
        $ch = curl_init("$apiBaseUrl/cards/$cardId");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Square-Version: 2025-10-16",
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $cardResponse = curl_exec($ch);
        $cardHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $cardResult = json_decode($cardResponse, true);
        if ($cardHttpCode < 200 || $cardHttpCode >= 300 || !isset($cardResult['card'])) {
            $errorMessage = $cardResult['errors'][0]['detail'] ?? 'Failed to retrieve card details';
            return ['success' => false, 'error' => $errorMessage];
        }

        $customerId = $cardResult['card']['customer_id'];

        // Create payment request with customer_id
        $paymentData = [
            'idempotency_key' => bin2hex(random_bytes(16)),
            'source_id' => $cardId,
            'customer_id' => $customerId,
            'amount_money' => [
                'amount' => intval($amount * 100), // Square uses cents
                'currency' => 'CAD'
            ],
            'location_id' => $locationId,
            'note' => "Premium Subscription Renewal - $subscriptionId",
            'autocomplete' => true
        ];

        // Make API call
        $ch = curl_init("$apiBaseUrl/payments");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($paymentData),
            CURLOPT_HTTPHEADER => [
                "Square-Version: 2025-10-16",
                "Authorization: Bearer $accessToken",
                "Content-Type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && isset($result['payment'])) {
            return [
                'success' => true,
                'transaction_id' => $result['payment']['id']
            ];
        } else {
            $errorMessage = $result['errors'][0]['detail'] ?? 'Unknown error';
            return [
                'success' => false,
                'error' => $errorMessage
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Calculate new subscription end date.
 *
 * Bases the new period on the LATER of the existing end_date or NOW(). When a
 * cron run is delayed and end_date is already in the past, extending from the
 * stale end_date can leave the new end_date still in the past — causing the
 * subscription to be picked up again and re-charged on the next run.
 */
function calculateNewEndDate($currentEndDate, $billing) {
    $endDateTime = new DateTime($currentEndDate);
    $now = new DateTime('now');
    if ($endDateTime < $now) {
        $endDateTime = $now;
    }

    if ($billing === 'yearly') {
        $endDateTime->add(new DateInterval('P1Y'));
    } else {
        $endDateTime->add(new DateInterval('P1M'));
    }

    return $endDateTime->format('Y-m-d H:i:s');
}

/**
 * Get count of recent payment failures
 */
function getRecentFailureCount($pdo, $subscriptionId) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM premium_subscription_payments
        WHERE subscription_id = ?
        AND status = 'failed'
        AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stmt->execute([$subscriptionId]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

