<?php
/**
 * Receipt Scan Usage Tracking API
 * Tracks and enforces monthly scan limits.
 * Free tier: 5 scans/month. Premium tier: 500 scans/month.
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

// Accept either license_key (premium) or device_id (free)
$license_key = trim($input['license_key'] ?? '');
$device_id = trim($input['device_id'] ?? '');

if (empty($license_key) && empty($device_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Either license_key or device_id is required']);
    exit();
}

if (!isset($input['action']) || !in_array($input['action'], ['check', 'increment'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Valid action (check or increment) is required']);
    exit();
}

$action = $input['action'];

// Load database connection and pricing config
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../config/pricing.php';

/**
 * Determine tier and validate identity.
 * Premium users (license key) get 500 scans/month.
 * Free users (device ID) get the configured free monthly limit.
 */
function validateAndGetTier($pdo, $license_key, $device_id) {
    $config = get_pricing_config();

    if (!empty($license_key)) {
        // Check if it's a Premium key (starts with PREM-)
        if (strpos($license_key, 'PREM-') === 0) {
            // Check premium_subscription_keys table (unredeemed promo keys)
            $stmt = $pdo->prepare("SELECT id FROM premium_subscription_keys WHERE subscription_key = ?");
            $stmt->execute([$license_key]);
            if ($stmt->fetch()) {
                return ['tier' => 'premium', 'limit' => $config['receipt_scan_monthly_limit'], 'identifier' => $license_key];
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
                return ['tier' => 'premium', 'limit' => $config['receipt_scan_monthly_limit'], 'identifier' => $license_key];
            }
        }

        // Check free license keys table
        $stmt = $pdo->prepare("SELECT id FROM license_keys WHERE license_key = ?");
        $stmt->execute([$license_key]);
        if ($stmt->fetch()) {
            return ['tier' => 'free', 'limit' => $config['free_receipt_scan_monthly_limit'], 'identifier' => $license_key];
        }
    }

    if (!empty($device_id)) {
        // Free tier via device ID
        $identifier = 'device_' . hash('sha256', $device_id);
        return ['tier' => 'free', 'limit' => $config['free_receipt_scan_monthly_limit'], 'identifier' => $identifier];
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
    // Validate identity and get tier
    $tierInfo = validateAndGetTier($pdo, $license_key, $device_id);

    if (!$tierInfo) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Invalid or expired license key']);
        exit();
    }

    $tier = $tierInfo['tier'];
    $monthly_limit = $tierInfo['limit'];
    $identifier = $tierInfo['identifier'];

    // Get or create usage record
    $usage = getOrCreateUsageRecord($pdo, $identifier, $monthly_limit);
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
        $stmt->execute([$identifier, $usage_month]);

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
