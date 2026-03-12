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
    curl_close($ch);

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
    curl_close($ch);

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
 * Get PayPal subscription details
 *
 * @param string $subscriptionId The PayPal subscription ID
 * @return array|false The subscription details or false on failure
 */
function getPayPalSubscriptionDetails($subscriptionId) {
    if (!isValidPayPalResourceId($subscriptionId)) {
        error_log("Invalid PayPal subscription ID format: " . substr($subscriptionId, 0, 50));
        return false;
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v1/billing/subscriptions/$subscriptionId");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Failed to get PayPal subscription details. HTTP Code: $httpCode");
        return false;
    }

    return json_decode($response, true);
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
    curl_close($ch);

    // PayPal returns 204 No Content on successful cancellation
    if ($httpCode !== 204) {
        error_log("Failed to cancel PayPal subscription. HTTP Code: $httpCode, Response: $response");
        return false;
    }

    return true;
}

/**
 * Suspend a PayPal subscription
 *
 * @param string $subscriptionId The PayPal subscription ID
 * @param string $reason The suspension reason
 * @return bool True if suspension was successful
 */
function suspendPayPalSubscription($subscriptionId, $reason = 'Payment failed') {
    if (!isValidPayPalResourceId($subscriptionId)) {
        error_log("Invalid PayPal subscription ID format for suspension");
        return false;
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v1/billing/subscriptions/$subscriptionId/suspend");
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
    curl_close($ch);

    return $httpCode === 204;
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
    curl_close($ch);

    return $httpCode === 204;
}

/**
 * Get PayPal transaction details from a sale/payment
 *
 * @param string $captureId The capture/sale ID
 * @return array|false The transaction details or false on failure
 */
function getPayPalCaptureDetails($captureId) {
    if (!isValidPayPalResourceId($captureId)) {
        error_log("Invalid PayPal capture ID format");
        return false;
    }

    $accessToken = getPayPalAccessToken();
    if (!$accessToken) {
        return false;
    }

    $baseUrl = getPayPalApiBaseUrl();

    $ch = curl_init("$baseUrl/v2/payments/captures/$captureId");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Bearer $accessToken"
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return false;
    }

    return json_decode($response, true);
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
