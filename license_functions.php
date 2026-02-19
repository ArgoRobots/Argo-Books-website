<?php
require_once 'db_connect.php';

/**
 * Generate a random license key
 *
 * @param string $type The type of license (always 'premium')
 * @return string A 20-character alphanumeric license key in format PREM-XXXX-XXXX-XXXX-XXXX
 */
function generate_license_key($type = 'premium')
{
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    $prefix = 'PREM';

    // Generate remaining 16 random characters
    $key = $prefix;
    for ($i = 0; $i < 16; $i++) {
        if ($i % 4 == 0) {
            $key .= '-';
        }
        $key .= $chars[random_int(0, strlen($chars) - 1)];
    }

    // Format: PREM-XXXX-XXXX-XXXX-XXXX
    return $key;
}

/**
 * Verify if a premium subscription is valid
 * @param string $subscription_id The subscription ID to validate
 * @return array Response array with validation result
 */
function validate_premium_subscription_key($subscription_id) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT subscription_id, user_id, email, billing_cycle, status,
                start_date, end_date, created_at
            FROM premium_subscriptions
            WHERE subscription_id = ?
        ");
        $stmt->execute([$subscription_id]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$subscription) {
            return [
                'success' => false,
                'message' => 'Invalid subscription ID.'
            ];
        }

        // Check subscription status
        $now = new DateTime();
        $end_date = new DateTime($subscription['end_date']);

        if ($subscription['status'] === 'active' && $end_date > $now) {
            return [
                'success' => true,
                'type' => 'premium_subscription',
                'status' => 'active',
                'message' => 'Premium subscription is valid and active.',
                'subscription_id' => $subscription['subscription_id'],
                'billing_cycle' => $subscription['billing_cycle'],
                'end_date' => $subscription['end_date'],
                'days_remaining' => $now->diff($end_date)->days
            ];
        } elseif ($subscription['status'] === 'cancelled' && $end_date > $now) {
            return [
                'success' => true,
                'type' => 'premium_subscription',
                'status' => 'cancelled',
                'message' => 'Premium subscription is cancelled but still active until end of billing period.',
                'subscription_id' => $subscription['subscription_id'],
                'billing_cycle' => $subscription['billing_cycle'],
                'end_date' => $subscription['end_date'],
                'days_remaining' => $now->diff($end_date)->days
            ];
        } else {
            // Subscription expired - update status if needed
            if ($subscription['status'] !== 'expired') {
                $stmt = $pdo->prepare("UPDATE premium_subscriptions SET status = 'expired' WHERE subscription_id = ?");
                $stmt->execute([$subscription_id]);
            }

            return [
                'success' => false,
                'type' => 'premium_subscription',
                'status' => 'expired',
                'message' => 'Premium subscription has expired.',
                'subscription_id' => $subscription['subscription_id'],
                'end_date' => $subscription['end_date']
            ];
        }
    } catch (PDOException $e) {
        error_log("Premium subscription validation error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error validating subscription. Please try again.'
        ];
    }
}

/**
 * Validate a free/promo premium subscription key
 * @param string $key The premium subscription key to validate
 * @return array Response array with validation result
 */
function validate_premium_key($key) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            SELECT subscription_key, email, duration_months, created_at,
                redeemed_at, redeemed_by_user_id, subscription_id, notes
            FROM premium_subscription_keys
            WHERE subscription_key = ?
        ");
        $stmt->execute([$key]);
        $premium_key = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$premium_key) {
            return [
                'success' => false,
                'message' => 'Invalid premium key.'
            ];
        }

        // Check if already redeemed
        if ($premium_key['redeemed_at'] !== null) {
            return [
                'success' => true,
                'type' => 'premium_key',
                'status' => 'redeemed',
                'message' => 'Premium key has already been redeemed.',
                'key' => $premium_key['subscription_key'],
                'redeemed_at' => $premium_key['redeemed_at'],
                'subscription_id' => $premium_key['subscription_id']
            ];
        }

        // Key is valid and not yet redeemed
        return [
            'success' => true,
            'type' => 'premium_key',
            'status' => 'valid',
            'message' => 'Premium key is valid and can be redeemed.',
            'key' => $premium_key['subscription_key'],
            'duration_months' => $premium_key['duration_months'],
            'restricted_email' => $premium_key['email']
        ];
    } catch (PDOException $e) {
        error_log("Premium key validation error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error validating premium key. Please try again.'
        ];
    }
}
