<?php
/**
 * AI Spreadsheet Import Usage Tracking API
 * Tracks and enforces monthly import limits for all users.
 * Every user gets 100 AI-powered imports per month.
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

// Load database connection and pricing config
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../config/pricing.php';

/**
 * Determine tier and validate license key
 * AI import is available to all users (free and premium) with 100 imports/month.
 * @param PDO $pdo
 * @param string $license_key
 * @return array|null Returns ['tier' => string, 'limit' => N] for valid keys,
 *                    or null if invalid
 */
function validateAndGetTier($pdo, $license_key) {
    $config = get_pricing_config();
    $limit = $config['ai_import_monthly_limit'];

    // Check if it's a Premium key (starts with PREM-)
    if (strpos($license_key, 'PREM-') === 0) {
        // Check premium_subscription_keys table (unredeemed promo keys)
        $stmt = $pdo->prepare("SELECT id FROM premium_subscription_keys WHERE subscription_key = ?");
        $stmt->execute([$license_key]);
        if ($stmt->fetch()) {
            return ['tier' => 'premium', 'limit' => $limit];
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
            return ['tier' => 'premium', 'limit' => $limit];
        }

        return null;
    }

    // Check if it's a valid free license key
    $stmt = $pdo->prepare("SELECT id FROM license_keys WHERE license_key = ?");
    $stmt->execute([$license_key]);
    if ($stmt->fetch()) {
        return ['tier' => 'free', 'limit' => $limit];
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
        FROM ai_import_usage
        WHERE license_key = ? AND usage_month = ?
    ");
    $stmt->execute([$license_key, $usage_month]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        return $record;
    }

    // Create new record for this month
    $stmt = $pdo->prepare("
        INSERT INTO ai_import_usage (license_key, usage_month, scan_count, monthly_limit)
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
 * @param int $import_count
 * @param int $monthly_limit
 * @param string $tier
 * @param bool $can_import
 * @return array
 */
function buildResponse($import_count, $monthly_limit, $tier, $can_import = null) {
    $remaining = max(0, $monthly_limit - $import_count);
    if ($can_import === null) {
        $can_import = $remaining > 0;
    }

    $usage_month = date('Y-m-01');
    $resets_at = date('Y-m-01', strtotime('first day of next month'));

    return [
        'success' => true,
        'can_import' => $can_import,
        'import_count' => (int)$import_count,
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

    // Get or create usage record
    $usage = getOrCreateUsageRecord($pdo, $license_key, $monthly_limit);
    $import_count = $usage['scan_count'];
    // Use the limit from the tier info (in case it changed)
    $monthly_limit = $tierInfo['limit'];

    if ($action === 'check') {
        // Just return current status
        echo json_encode(buildResponse($import_count, $monthly_limit, $tier));
        exit();
    }

    if ($action === 'increment') {
        // Check if limit reached before incrementing
        if ($import_count >= $monthly_limit) {
            $response = buildResponse($import_count, $monthly_limit, $tier, false);
            $response['success'] = false;
            $response['error'] = 'Monthly import limit reached';
            http_response_code(429);
            echo json_encode($response);
            exit();
        }

        // Atomic conditional update so two concurrent requests can't both pass
        // the read-then-check above and increment past the cap.
        $usage_month = date('Y-m-01');
        $stmt = $pdo->prepare("
            UPDATE ai_import_usage
            SET scan_count = scan_count + 1
            WHERE license_key = ? AND usage_month = ? AND scan_count < ?
        ");
        $stmt->execute([$license_key, $usage_month, $monthly_limit]);

        if ($stmt->rowCount() === 0) {
            $response = buildResponse($import_count, $monthly_limit, $tier, false);
            $response['success'] = false;
            $response['error'] = 'Monthly import limit reached';
            http_response_code(429);
            echo json_encode($response);
            exit();
        }

        // Return updated status
        $new_import_count = $import_count + 1;
        echo json_encode(buildResponse($new_import_count, $monthly_limit, $tier));
        exit();
    }

} catch (PDOException $e) {
    error_log("AI import usage API error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit();
}
