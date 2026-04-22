<?php
/**
 * Portal Company Registration API Endpoint
 *
 * POST /api/portal/register - Register a new company for the payment portal
 *
 * Called from Argo Books when a user sets up the payment portal feature.
 * Validates the user's license key and device ID, then generates
 * a portal API key and returns connection details.
 */

require_once __DIR__ . '/portal-helper.php';
require_once __DIR__ . '/../../license_functions.php';

set_portal_headers();
require_method(['POST']);

// Rate limiting: 10 registration attempts per 15 minutes per IP
$ip = get_client_ip();
if (is_rate_limited($ip, 10, 900, 'register')) {
    send_error_response(429, 'Too many registration attempts. Please try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($ip, 'register');

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

// Validate required fields
if (empty($data['deviceId'])) {
    send_error_response(400, 'Missing required field: deviceId', 'MISSING_FIELDS');
}
if (empty($data['companyName'])) {
    send_error_response(400, 'Missing required field: companyName', 'MISSING_FIELDS');
}

$licenseKey = trim($data['licenseKey'] ?? '');
$deviceId = trim($data['deviceId']);

// Validate identity: premium users use license key, free users use device ID
global $pdo;
if ($pdo === null) {
    error_log('Portal registration failed: database connection unavailable');
    send_error_response(500, 'Service temporarily unavailable. Please try again later.', 'DB_UNAVAILABLE');
}

if (!empty($licenseKey)) {
    // Premium path: validate license key
    $licenseResult = validate_license($licenseKey, $deviceId);
    if (!($licenseResult['success'] ?? false)) {
        $message = $licenseResult['message'] ?? 'Invalid license key.';
        $status = $licenseResult['status'] ?? 'invalid_key';
        send_error_response(401, $message, strtoupper($status));
    }
}
// Free path: no license key, device ID is the sole identifier (already validated as non-empty)

// Check if company already exists by owner_email
$ownerEmail = $data['ownerEmail'] ?? '';
if (!empty($ownerEmail)) {
    $stmt = $pdo->prepare('SELECT id FROM portal_companies WHERE owner_email = ? LIMIT 1');
    $stmt->execute([$ownerEmail]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Rotate API key: generate new key and store its hash
        $rotatedKey = bin2hex(random_bytes(32));
        $rotatedHash = hash('sha256', $rotatedKey);
        try {
            $rotateStmt = $pdo->prepare('UPDATE portal_companies SET api_key_hash = ? WHERE id = ?');
            $rotateStmt->execute([$rotatedHash, $existing['id']]);
            if ($rotateStmt->rowCount() <= 0) {
                error_log('Portal registration failed: API key rotation UPDATE affected 0 rows for company ID ' . $existing['id']);
                send_error_response(500, 'Service temporarily unavailable. Please try again later.', 'DB_ERROR');
            }
        } catch (PDOException $e) {
            error_log('Portal registration failed: API key rotation error: ' . $e->getMessage());
            send_error_response(500, 'Service temporarily unavailable. Please try again later.', 'DB_ERROR');
        }
        send_json_response(200, [
            'success' => true,
            'api_key' => $rotatedKey,
            'company_id' => $existing['id'],
            'message' => 'Company already registered',
            'timestamp' => date('c')
        ]);
    }
}

// Generate API key (64-char hex = 256 bits of entropy)
$apiKey = bin2hex(random_bytes(32));
$apiKeyHash = hash('sha256', $apiKey);

$companyName = $data['companyName'];
$companyLogoUrl = $data['companyLogoUrl'] ?? null;
$stripeAccountId = $data['stripeAccountId'] ?? null;
$paypalMerchantId = $data['paypalMerchantId'] ?? null;
$squareMerchantId = $data['squareMerchantId'] ?? null;
$squareAccessToken = $data['squareAccessToken'] ?? null;
if ($squareAccessToken !== null && $squareAccessToken !== '') {
    $squareAccessToken = portal_encrypt($squareAccessToken);
}
$squareLocationId = $data['squareLocationId'] ?? null;
$environment = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';

try {
    $stmt = $pdo->prepare(
        'INSERT INTO portal_companies
         (api_key_hash, company_name, company_logo_url, stripe_account_id,
          paypal_merchant_id, square_merchant_id, square_access_token,
          square_location_id, owner_email, environment, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([
        $apiKeyHash, $companyName, $companyLogoUrl, $stripeAccountId,
        $paypalMerchantId, $squareMerchantId, $squareAccessToken,
        $squareLocationId, $ownerEmail, $environment
    ]);
} catch (PDOException $e) {
    error_log('Portal registration DB error: ' . $e->getMessage());
    send_error_response(500, 'Failed to register company. Please try again.', 'DB_ERROR');
}

$companyId = $pdo->lastInsertId();

send_json_response(201, [
    'success' => true,
    'api_key' => $apiKey,
    'company_id' => $companyId,
    'message' => 'Company registered successfully',
    'timestamp' => date('c')
]);
