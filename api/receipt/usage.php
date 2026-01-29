<?php
/**
 * Receipt Scan Usage Tracking API
 * Tracks and enforces monthly scan limits for Premium tier subscribers.
 * AI Receipt Scanning is only available on the Premium plan ($5/month) with 500 scans/month.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['license_key']) || empty($input['license_key'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'License key is required']);
    exit();
}

if (!isset($input['action']) || !in_array($input['action'], ['check', 'increment'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Valid action (check or increment) is required']);
    exit();
}

$license_key = trim($input['license_key']);
$action = $input['action'];

// Load database connection
require_once __DIR__ . '/../../db_connect.php';

/**
 * Determine tier and validate license key
 * @param PDO $pdo
 * @param string $license_key
 * @return array|null Returns ['tier' => 'premium', 'limit' => 500] for valid premium keys,
 *                    ['tier' => 'standard', 'limit' => 0] for standard keys (feature not available),
 *                    or null if invalid
 */
function validateAndGetTier($pdo, $license_key) {
    // Check if it's a Premium key (starts with PREM-)
    if (strpos($license_key, 'PREM-') === 0) {
        // Check premium_subscription_keys table (unredeemed promo keys)
        $stmt = $pdo->prepare("SELECT id FROM premium_subscription_keys WHERE subscription_key = ?");
        $stmt->execute([$license_key]);
        if ($stmt->fetch()) {
            return ['tier' => 'premium', 'limit' => 500];
        }

        // Check premium_subscriptions table for active subscriptions
        $stmt = $pdo->prepare("
            SELECT id FROM premium_subscriptions
            WHERE subscription_id = ?
            AND status IN ('active', 'cancelled')
            AND end_date > NOW()
        ");
        $stmt->execute([$license_key]);
        if ($stmt->fetch()) {
            return ['tier' => 'premium', 'limit' => 500];
        }

        return null;
    }

    // Check if it's a Standard key (starts with STND-)
    // AI Receipt Scanning is NOT available on Standard plan
    if (strpos($license_key, 'STND-') === 0) {
        $stmt = $pdo->prepare("SELECT id FROM license_keys WHERE license_key = ?");
        $stmt->execute([$license_key]);
        if ($stmt->fetch()) {
            return ['tier' => 'standard', 'limit' => 0];
        }
    }

    return null;
}

/**
 * Get or create usage record for current month
 * @param PDO $pdo
 * @param string $license_key
 * @param int $monthly_limit
 * @return array
 */
function getOrCreateUsageRecord($pdo, $license_key, $monthly_limit) {
    $usage_month = date('Y-m-01');

    // Try to get existing record
    $stmt = $pdo->prepare("
        SELECT id, scan_count, monthly_limit
        FROM receipt_scan_usage
        WHERE license_key = ? AND usage_month = ?
    ");
    $stmt->execute([$license_key, $usage_month]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        return $record;
    }

    // Create new record for this month
    $stmt = $pdo->prepare("
        INSERT INTO receipt_scan_usage (license_key, usage_month, scan_count, monthly_limit)
        VALUES (?, ?, 0, ?)
    ");
    $stmt->execute([$license_key, $usage_month, $monthly_limit]);

    return [
        'id' => $pdo->lastInsertId(),
        'scan_count' => 0,
        'monthly_limit' => $monthly_limit
    ];
}

/**
 * Build response array
 * @param int $scan_count
 * @param int $monthly_limit
 * @param string $tier
 * @param bool $can_scan
 * @return array
 */
function buildResponse($scan_count, $monthly_limit, $tier, $can_scan = null) {
    $remaining = max(0, $monthly_limit - $scan_count);
    if ($can_scan === null) {
        $can_scan = $remaining > 0;
    }

    $usage_month = date('Y-m-01');
    $resets_at = date('Y-m-01', strtotime('first day of next month'));

    return [
        'success' => true,
        'can_scan' => $can_scan,
        'scan_count' => (int)$scan_count,
        'monthly_limit' => (int)$monthly_limit,
        'remaining' => (int)$remaining,
        'tier' => $tier,
        'usage_month' => $usage_month,
        'resets_at' => $resets_at
    ];
}

try {
    // Validate license key and get tier
    $tierInfo = validateAndGetTier($pdo, $license_key);

    if (!$tierInfo) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid or expired license key']);
        exit();
    }

    $tier = $tierInfo['tier'];
    $monthly_limit = $tierInfo['limit'];

    // Standard tier does not have access to AI Receipt Scanning
    if ($tier === 'standard') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'AI Receipt Scanning is only available on the Premium plan',
            'tier' => 'standard',
            'can_scan' => false,
            'upgrade_required' => true
        ]);
        exit();
    }

    // Get or create usage record
    $usage = getOrCreateUsageRecord($pdo, $license_key, $monthly_limit);
    $scan_count = $usage['scan_count'];
    // Use the limit from the tier info (in case it changed)
    $monthly_limit = $tierInfo['limit'];

    if ($action === 'check') {
        // Just return current status
        echo json_encode(buildResponse($scan_count, $monthly_limit, $tier));
        exit();
    }

    if ($action === 'increment') {
        // Check if limit reached before incrementing
        if ($scan_count >= $monthly_limit) {
            $response = buildResponse($scan_count, $monthly_limit, $tier, false);
            $response['success'] = false;
            $response['error'] = 'Monthly scan limit reached';
            http_response_code(429);
            echo json_encode($response);
            exit();
        }

        // Increment the scan count
        $usage_month = date('Y-m-01');
        $stmt = $pdo->prepare("
            UPDATE receipt_scan_usage
            SET scan_count = scan_count + 1
            WHERE license_key = ? AND usage_month = ?
        ");
        $stmt->execute([$license_key, $usage_month]);

        // Return updated status
        $new_scan_count = $scan_count + 1;
        echo json_encode(buildResponse($new_scan_count, $monthly_limit, $tier));
        exit();
    }

} catch (PDOException $e) {
    error_log("Receipt usage API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit();
}
