<?php
/**
 * Batch Exchange Rates Endpoint
 *
 * POST /api/exchange-rates-batch - Fetch exchange rates for multiple dates at once.
 *
 * Request body (JSON):
 *   { "dates": ["2025-01-15", "2025-03-20", ...] }
 *
 * Returns all rates from MySQL cache, fetching from OpenExchangeRates only for
 * dates not yet cached. Historical rates are stored permanently (they never change).
 * Today's rate is refreshed if older than 1 hour.
 */

require_once __DIR__ . '/portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Batch requests may fetch many dates from OER — allow up to 2 minutes
set_time_limit(120);

// Rate limiting (counts as 1 request regardless of how many dates)
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

// Parse request body
$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['dates']) || !is_array($body['dates'])) {
    send_error_response(400, 'Request body must contain a "dates" array.', 'INVALID_REQUEST');
}

$requestedDates = $body['dates'];
if (count($requestedDates) > 500) {
    send_error_response(400, 'Maximum 500 dates per request.', 'TOO_MANY_DATES');
}

// Validate all dates
$today = date('Y-m-d');
$validDates = [];
foreach ($requestedDates as $date) {
    if (!is_string($date)) continue;
    $parsed = DateTime::createFromFormat('Y-m-d', $date);
    if (!$parsed || $parsed->format('Y-m-d') !== $date) continue;
    if ($parsed > new DateTime()) continue; // skip future dates
    $validDates[] = $date;
}
$validDates = array_values(array_unique($validDates));

if (empty($validDates)) {
    send_json_response(200, [
        'success' => true,
        'base' => 'USD',
        'results' => new stdClass(),
        'failed' => [],
        'timestamp' => date('c'),
    ]);
}

// Connect to database
require_once __DIR__ . '/../db_connect.php';
if (!$pdo) {
    send_error_response(500, 'Database connection failed.', 'DB_ERROR');
}

// Ensure table exists
$pdo->exec("CREATE TABLE IF NOT EXISTS exchange_rates (
    rate_date DATE NOT NULL,
    rates JSON NOT NULL,
    fetched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (rate_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Fetch all cached rates from MySQL in one query
$placeholders = implode(',', array_fill(0, count($validDates), '?'));
$stmt = $pdo->prepare("SELECT rate_date, rates, fetched_at FROM exchange_rates WHERE rate_date IN ($placeholders)");
$stmt->execute($validDates);
$cachedRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$results = [];
$datesToFetch = [];
$oneHourAgo = date('Y-m-d H:i:s', time() - 3600);

foreach ($cachedRows as $row) {
    $dateStr = $row['rate_date'];
    $rates = json_decode($row['rates'], true);
    if ($rates === null) {
        $datesToFetch[] = $dateStr;
        continue;
    }
    // Today's rate: check freshness (1hr TTL)
    if ($dateStr === $today && $row['fetched_at'] < $oneHourAgo) {
        $datesToFetch[] = $dateStr;
        continue;
    }
    $results[$dateStr] = $rates;
}

// Find dates not in DB at all
$cachedDateSet = array_column($cachedRows, 'rate_date');
foreach ($validDates as $date) {
    if (!in_array($date, $cachedDateSet) && !isset($results[$date])) {
        $datesToFetch[] = $date;
    }
}
$datesToFetch = array_unique($datesToFetch);

// Cap OER calls per batch to protect API quota
$maxOerCalls = 30;
$failed = [];
if (count($datesToFetch) > $maxOerCalls) {
    // Fetch the first $maxOerCalls, mark the rest as failed
    $overflow = array_slice($datesToFetch, $maxOerCalls);
    $datesToFetch = array_slice($datesToFetch, 0, $maxOerCalls);
    $failed = $overflow;
}

// Fetch missing dates from OpenExchangeRates and store in MySQL
$insertStmt = $pdo->prepare(
    "INSERT INTO exchange_rates (rate_date, rates, fetched_at) VALUES (?, ?, NOW())
     ON DUPLICATE KEY UPDATE rates = VALUES(rates), fetched_at = NOW()"
);

foreach ($datesToFetch as $date) {
    $isLatest = ($date === $today);
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
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        $failed[] = $date;
        continue;
    }

    $data = json_decode($response, true);
    if ($data === null || !isset($data['rates'])) {
        $failed[] = $date;
        continue;
    }

    // Store in MySQL
    $insertStmt->execute([$date, json_encode($data['rates'])]);

    $results[$date] = $data['rates'];
}

send_json_response(200, [
    'success' => true,
    'base' => 'USD',
    'results' => empty($results) ? new stdClass() : $results,
    'failed' => array_values(array_unique($failed)),
    'timestamp' => date('c'),
]);
