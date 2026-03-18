<?php
/**
 * Exchange Rates Proxy Endpoint
 *
 * GET /api/exchange-rates - Proxy OpenExchangeRates API calls
 *
 * Fetches exchange rates from OpenExchangeRates using server-side API key.
 * Implements server-side file caching to reduce upstream API calls.
 *
 * Query parameters:
 *   - date: Optional date (YYYY-MM-DD). If omitted or today, fetches latest rates.
 */

require_once __DIR__ . '/portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['GET']);

// Authenticate using license key (optional — exchange rates are available to all users)
$license = authenticate_license_request();

// Rate limiting: use license hash if authenticated, otherwise use IP address
if ($license) {
    $rateLimitId = substr($license['license_key_hash'], 0, 16);
} else {
    $rateLimitId = 'ip_' . substr(hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown'), 0, 16);
}
$rateLimitKey = 'rates_' . $rateLimitId;
if (is_rate_limited($rateLimitId, 120, 900, $rateLimitKey)) {
    send_error_response(429, 'Rate limit exceeded. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($rateLimitId, $rateLimitKey);

// Validate server configuration
$apiKey = $_ENV['OPENEXCHANGERATES_API_KEY'] ?? '';
if (empty($apiKey)) {
    send_error_response(500, 'Exchange rate service not configured on server.', 'CONFIG_ERROR');
}

// Parse date parameter
$date = $_GET['date'] ?? '';
$isLatest = empty($date) || $date === date('Y-m-d');

if (!empty($date) && !$isLatest) {
    // Validate date format
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) {
        send_error_response(400, 'Invalid date format. Use YYYY-MM-DD.', 'INVALID_DATE');
    }
    // Don't allow future dates
    if ($parsed > new DateTime()) {
        send_error_response(400, 'Date cannot be in the future.', 'INVALID_DATE');
    }
}

// Check server-side cache
$cacheDir = sys_get_temp_dir() . '/argo_exchange_rates';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$cacheKey = $isLatest ? 'latest' : $date;
$cacheFile = $cacheDir . '/' . $cacheKey . '.json';
$cacheTtl = $isLatest ? 3600 : 86400; // 1 hour for latest, 24 hours for historical

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTtl) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    if ($cached !== null) {
        send_json_response(200, [
            'success' => true,
            'base' => 'USD',
            'date' => $cached['date'] ?? $date,
            'rates' => $cached['rates'] ?? [],
            'cached' => true,
            'timestamp' => date('c'),
        ]);
    }
}

// Fetch from OpenExchangeRates
if ($isLatest) {
    $url = "https://openexchangerates.org/api/latest.json?app_id={$apiKey}&base=USD";
} else {
    $url = "https://openexchangerates.org/api/historical/{$date}.json?app_id={$apiKey}&base=USD";
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 5,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    error_log('Exchange rates cURL error: ' . $curlError);
    send_error_response(502, 'Failed to connect to exchange rate service.', 'UPSTREAM_ERROR');
}

if ($httpCode !== 200) {
    error_log("Exchange rates error ({$httpCode}): " . substr($response, 0, 500));
    send_error_response(502, 'Exchange rate service returned an error.', 'UPSTREAM_ERROR');
}

$data = json_decode($response, true);
if ($data === null || !isset($data['rates'])) {
    send_error_response(502, 'Invalid response from exchange rate service.', 'UPSTREAM_ERROR');
}

// Cache the response
$cacheData = [
    'date' => $isLatest ? date('Y-m-d') : $date,
    'rates' => $data['rates'],
    'fetched_at' => time(),
];
file_put_contents($cacheFile, json_encode($cacheData), LOCK_EX);

send_json_response(200, [
    'success' => true,
    'base' => 'USD',
    'date' => $cacheData['date'],
    'rates' => $data['rates'],
    'cached' => false,
    'timestamp' => date('c'),
]);
