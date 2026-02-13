<?php
/**
 * Portal Company Registration API Endpoint
 *
 * POST /api/portal/register - Register a new company for the payment portal
 *
 * Called from Argo Books when a user sets up the payment portal feature.
 * Generates an API key and returns connection details.
 *
 * Requires the master portal registration key (set in .env).
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['POST']);

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// Verify master registration key
$masterKey = $_ENV['PORTAL_REGISTRATION_KEY'] ?? '';
if (empty($masterKey)) {
    send_error_response(500, 'Portal registration not configured.', 'CONFIG_ERROR');
}

$providedKey = '';
if (!empty($_SERVER['HTTP_X_API_KEY'])) {
    $providedKey = $_SERVER['HTTP_X_API_KEY'];
} elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    if (preg_match('/Bearer\s+(.+)/i', $authHeader, $matches)) {
        $providedKey = $matches[1];
    }
}

if (empty($providedKey) || !hash_equals($masterKey, $providedKey)) {
    send_error_response(401, 'Invalid or missing registration key.', 'UNAUTHORIZED');
}

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

// Validate required fields
if (empty($data['companyName'])) {
    send_error_response(400, 'Missing required field: companyName', 'MISSING_FIELDS');
}

$db = get_db_connection();

// Check if company already exists by owner_email
$ownerEmail = $data['ownerEmail'] ?? '';
if (!empty($ownerEmail)) {
    $stmt = $db->prepare('SELECT id, api_key FROM portal_companies WHERE owner_email = ? LIMIT 1');
    $stmt->bind_param('s', $ownerEmail);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $db->close();
        send_json_response(200, [
            'success' => true,
            'api_key' => $existing['api_key'],
            'company_id' => $existing['id'],
            'message' => 'Company already registered',
            'timestamp' => date('c')
        ]);
    }
}

// Generate API key (64-char hex = 256 bits of entropy)
$apiKey = bin2hex(random_bytes(32));

$companyName = $data['companyName'];
$companyLogoUrl = $data['companyLogoUrl'] ?? null;
$stripeAccountId = $data['stripeAccountId'] ?? null;
$paypalMerchantId = $data['paypalMerchantId'] ?? null;
$squareMerchantId = $data['squareMerchantId'] ?? null;
$squareAccessToken = $data['squareAccessToken'] ?? null;
$squareLocationId = $data['squareLocationId'] ?? null;

$stmt = $db->prepare(
    'INSERT INTO portal_companies
     (api_key, company_name, company_logo_url, stripe_account_id,
      paypal_merchant_id, square_merchant_id, square_access_token,
      square_location_id, owner_email, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->bind_param(
    'sssssssss',
    $apiKey, $companyName, $companyLogoUrl, $stripeAccountId,
    $paypalMerchantId, $squareMerchantId, $squareAccessToken,
    $squareLocationId, $ownerEmail
);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    $db->close();
    send_error_response(500, 'Failed to register company: ' . $error, 'DB_ERROR');
}

$companyId = $stmt->insert_id;
$stmt->close();
$db->close();

send_json_response(201, [
    'success' => true,
    'api_key' => $apiKey,
    'company_id' => $companyId,
    'message' => 'Company registered successfully',
    'timestamp' => date('c')
]);
