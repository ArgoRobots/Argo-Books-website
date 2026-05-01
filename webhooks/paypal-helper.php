<?php
/**
 * PayPal API Helper Functions
 *
 * This file contains helper functions for interacting with the PayPal API
 * for subscription management, webhook verification, and API calls.
 */

/**
 * Get PayPal API base URL based on environment
 *
 * @return string The PayPal API base URL
 */
function getPayPalApiBaseUrl() {
    $isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production';
    return $isProduction
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';
}

/**
 * Get PayPal OAuth access token
 *
 * @return string|false The access token or false on failure
 */
function getPayPalAccessToken() {
    $isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production';

    $clientId = $isProduction
        ? $_ENV['PAYPAL_LIVE_CLIENT_ID']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];

    $clientSecret = $isProduction
        ? $_ENV['PAYPAL_LIVE_CLIENT_SECRET']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'];

    if (empty($clientId) || empty($clientSecret)) {
        error_log("PayPal credentials not configured");
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v1/oauth2/token");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Accept-Language: en_US',
            'Content-Type: application/x-www-form-urlencoded'
        ],
        CURLOPT_USERPWD => "$clientId:$clientSecret",
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        error_log("Failed to get PayPal access token. HTTP Code: $httpCode, Response: $response");
        return false;
    }

    $data = json_decode($response, true);
    return $data['access_token'] ?? false;
}

/**
 * Verify PayPal webhook signature
 *
 * @param array $headers The request headers
 * @param string $body The raw request body
 * @param string $webhookId The webhook ID from PayPal dashboard
 * @return bool True if signature is valid
 */
function verifyPayPalWebhookSignature($headers, $body, $webhookId) {
    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        error_log("Failed to get access token for webhook verification");
        return false;
    }

    // Normalize header keys to lowercase
    $normalizedHeaders = [];
    foreach ($headers as $key => $value) {
        $normalizedHeaders[strtolower($key)] = is_array($value) ? $value[0] : $value;
    }

    // Required headers for verification
    $transmissionId = $normalizedHeaders['paypal-transmission-id'] ?? '';
    $transmissionTime = $normalizedHeaders['paypal-transmission-time'] ?? '';
    $certUrl = $normalizedHeaders['paypal-cert-url'] ?? '';
    $transmissionSig = $normalizedHeaders['paypal-transmission-sig'] ?? '';
    $authAlgo = $normalizedHeaders['paypal-auth-algo'] ?? '';

    if (empty($transmissionId) || empty($transmissionTime) || empty($certUrl) ||
        empty($transmissionSig) || empty($authAlgo)) {
        error_log("Missing required PayPal webhook headers");
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $verificationData = [
        'transmission_id' => $transmissionId,
        'transmission_time' => $transmissionTime,
        'cert_url' => $certUrl,
        'auth_algo' => $authAlgo,
        'transmission_sig' => $transmissionSig,
        'webhook_id' => $webhookId,
        'webhook_event' => json_decode($body, true)
    ];

    $ch = curl_init("$baseUrl/v1/notifications/verify-webhook-signature");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($verificationData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode !== 200) {
        error_log("PayPal webhook verification failed. HTTP Code: $httpCode, Response: $response");
        return false;
    }

    $result = json_decode($response, true);
    $verificationStatus = $result['verification_status'] ?? '';

    return $verificationStatus === 'SUCCESS';
}

/**
 * Validate a PayPal resource ID to prevent SSRF via URL path injection.
 * PayPal IDs contain only alphanumeric characters and hyphens.
 *
 * @param string $id The PayPal resource ID to validate
 * @return bool True if the ID format is valid
 */
function isValidPayPalResourceId(string $id): bool
{
    return (bool) preg_match('/^[A-Za-z0-9\-]+$/', $id);
}

/**
 * Cancel a PayPal subscription
 *
 * @param string $subscriptionId The PayPal subscription ID
 * @param string $reason The cancellation reason
 * @return bool True if cancellation was successful
 */
function cancelPayPalSubscription($subscriptionId, $reason = 'Cancelled by user') {
    if (!isValidPayPalResourceId($subscriptionId)) {
        error_log("Invalid PayPal subscription ID format for cancellation");
        return false;
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        error_log("Failed to get access token for subscription cancellation");
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v1/billing/subscriptions/$subscriptionId/cancel");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['reason' => $reason]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // PayPal returns 204 No Content on successful cancellation
    if ($httpCode !== 204) {
        error_log("Failed to cancel PayPal subscription. HTTP Code: $httpCode, Response: $response");
        return false;
    }

    return true;
}

/**
 * Activate a PayPal subscription
 *
 * @param string $subscriptionId The PayPal subscription ID
 * @param string $reason The activation reason
 * @return bool True if activation was successful
 */
function activatePayPalSubscription($subscriptionId, $reason = 'Reactivated by user') {
    if (!isValidPayPalResourceId($subscriptionId)) {
        error_log("Invalid PayPal subscription ID format for activation");
        return false;
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v1/billing/subscriptions/$subscriptionId/activate");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['reason' => $reason]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $httpCode === 204;
}

/**
 * Issue a refund against a PayPal v1 sale.
 *
 * Used by the cycle-switch flow to return the prorated value of an
 * unused billing period (and any pre-existing account credit) to the
 * user's PayPal account when they switch monthly <-> yearly.
 *
 * @param string $saleId       PayPal sale ID (from premium_subscription_payments.transaction_id)
 * @param float  $amount       Refund amount
 * @param string $description  Buyer-facing description (PayPal v1 sale-refund field name)
 * @param string $currency     ISO currency code; defaults to CAD but should match the original sale's currency
 * @return array { success: bool, refund_id?: string|null, http_code?: int, error?: string }
 */
function refundPayPalSale($saleId, $amount, $description = 'Cycle switch proration', $currency = 'CAD') {
    if (!isValidPayPalResourceId($saleId)) {
        return ['success' => false, 'error' => 'Invalid sale id format'];
    }
    if (!is_numeric($amount) || $amount <= 0) {
        return ['success' => false, 'error' => 'Refund amount must be positive'];
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return ['success' => false, 'error' => 'Failed to obtain PayPal access token'];
    }

    $baseUrl = getPayPalApiBaseUrl();
    $url = "$baseUrl/v1/payments/sale/" . urlencode($saleId) . "/refund";

    // PayPal v1 sale-refund field is `description`, not `reason`. The
    // cancel-subscription endpoint uses `reason`; sale refund does not.
    $body = json_encode([
        'amount' => [
            'total'    => number_format((float) $amount, 2, '.', ''),
            'currency' => strtoupper($currency),
        ],
        'description' => $description,
    ]);

    // PayPal-Request-Id: deterministic per (sale, day) so an accidental
    // retry inside a 24h window reuses the key and PayPal dedups.
    $requestId = hash('sha256', 'refund_' . $saleId . '_' . date('Y-m-d'));

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'PayPal-Request-Id: ' . $requestId,
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 201) {
        error_log("PayPal refund failed for sale $saleId: HTTP $httpCode response=$response");
        return [
            'success'   => false,
            'http_code' => $httpCode,
            'error'     => 'Refund API returned HTTP ' . $httpCode,
        ];
    }

    $decoded = json_decode($response, true);
    return [
        'success'   => true,
        'refund_id' => $decoded['id'] ?? null,
        'http_code' => $httpCode,
    ];
}

/**
 * Find the most recent successful PayPal sale for a subscription.
 * Returns the row from premium_subscription_payments suitable for
 * refundPayPalSale, or null if no completed sale exists yet (block
 * the cycle switch in that case).
 *
 * @param string $subscriptionId Argo subscription_id
 * @return array|null { transaction_id, amount, currency, created_at }
 */
function getMostRecentPayPalSale($subscriptionId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT transaction_id, amount, currency, created_at
        FROM premium_subscription_payments
        WHERE subscription_id = ?
          AND payment_method = 'paypal'
          AND status = 'completed'
          AND payment_type IN ('initial', 'renewal')
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$subscriptionId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Log webhook event for debugging
 *
 * @param string $eventType The event type
 * @param array $data The event data
 * @param string $result The processing result
 */
function logPayPalWebhookEvent($eventType, $data, $result = 'processed') {
    $logDir = __DIR__ . '/../cron/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0700, true);
    }

    $logFile = $logDir . '/paypal_webhooks_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $dataJson = json_encode($data, JSON_PRETTY_PRINT);

    $logEntry = "[$timestamp] [$result] $eventType\n$dataJson\n\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
