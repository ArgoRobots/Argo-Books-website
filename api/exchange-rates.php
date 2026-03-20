<?php
/**
 * Exchange Rates Proxy Endpoint
 *
 * GET /api/exchange-rates - Proxy OpenExchangeRates API calls
 *
 * Fetches exchange rates from OpenExchangeRates using server-side API key.
 * Implements persistent MySQL caching — historical rates are stored permanently
 * (they never change), today's rate is refreshed every hour.
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

// Exchange rates are a free feature available to all users — no license key required.
// Rate limiting uses device ID if provided, otherwise falls back to IP address.
$deviceId = authenticate_device_request();
if ($deviceId) {
    $rateLimitId = 'dev_' . substr($deviceId, 0, 16);
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

$lookupDate = $isLatest ? date('Y-m-d') : $date;

// Check MySQL cache
require_once __DIR__ . '/../db_connect.php';
if ($pdo) {
    // Ensure table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS exchange_rates (
        rate_date DATE NOT NULL,
        rates JSON NOT NULL,
        fetched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (rate_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $stmt = $pdo->prepare("SELECT rates, fetched_at FROM exchange_rates WHERE rate_date = ?");
    $stmt->execute([$lookupDate]);
    $cached = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cached) {
        $rates = json_decode($cached['rates'], true);
        // Historical rates never expire; today's rate has 1hr TTL
        $isStale = $isLatest && (strtotime($cached['fetched_at']) < time() - 3600);

        if ($rates !== null && !$isStale) {
            send_json_response(200, [
                'success' => true,
                'base' => 'USD',
                'date' => $lookupDate,
                'rates' => $rates,
                'cached' => true,
                'timestamp' => date('c'),
            ]);
        }
    }
}

// Fetch from OpenExchangeRates
if ($isLatest) {
    $url = "https://openexchangerates.org/api/latest.json?app_id={$apiKey}&base=USD";
} else {
    $url = "https://openexchangerates.org/api/historical/{$lookupDate}.json?app_id={$apiKey}&base=USD";
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

// Store in MySQL cache
if ($pdo) {
    $stmt = $pdo->prepare(
        "INSERT INTO exchange_rates (rate_date, rates, fetched_at) VALUES (?, ?, NOW())
         ON DUPLICATE KEY UPDATE rates = VALUES(rates), fetched_at = NOW()"
    );
    $stmt->execute([$lookupDate, json_encode($data['rates'])]);
}

send_json_response(200, [
    'success' => true,
    'base' => 'USD',
    'date' => $lookupDate,
    'rates' => $data['rates'],
    'cached' => false,
    'timestamp' => date('c'),
]);
