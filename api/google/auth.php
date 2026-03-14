<?php
/**
 * Google OAuth Proxy Endpoint
 *
 * POST /api/google/auth - Manage Google OAuth for Sheets/Drive access
 *
 * Actions:
 *   - { action: "initiate" } — Generate OAuth URL, return to app to open in browser
 *   - { action: "status" }   — Check if company has valid Google tokens
 *   - { action: "revoke" }   — Revoke Google tokens for this company
 */

require_once __DIR__ . '/../portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

// Validate server configuration
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
if (empty($clientId) || empty($clientSecret)) {
    send_error_response(500, 'Google integration not configured on server.', 'CONFIG_ERROR');
}

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

$action = $data['action'] ?? '';

switch ($action) {
    case 'initiate':
        handleInitiate($company, $clientId);
        break;

    case 'status':
        handleStatus($company);
        break;

    case 'revoke':
        handleRevoke($company);
        break;

    default:
        send_error_response(400, 'Invalid action. Supported: initiate, status, revoke.', 'INVALID_ACTION');
}

/**
 * Generate Google OAuth URL and store state token.
 */
function handleInitiate(array $company, string $clientId): void
{
    $state = bin2hex(random_bytes(16));
    $companyId = $company['id'];

    // Store state token in database for CSRF validation
    $db = get_db_connection();

    // Clean up expired states for this company
    $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE company_id = ? OR expires_at < NOW()');
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $stmt->close();

    // Store new state
    $stmt = $db->prepare(
        'INSERT INTO portal_oauth_states (state_token, company_id, provider, expires_at)
         VALUES (?, ?, "google", DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
    );
    $stmt->bind_param('si', $state, $companyId);
    $stmt->execute();
    $stmt->close();
    $db->close();

    $baseUrl = $_ENV['APP_URL'] ?? 'https://argorobots.com';
    $redirectUri = $baseUrl . '/api/google/callback';

    $scopes = implode(' ', [
        'https://www.googleapis.com/auth/spreadsheets',
        'https://www.googleapis.com/auth/drive.file',
    ]);

    $params = http_build_query([
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'response_type' => 'code',
        'scope' => $scopes,
        'access_type' => 'offline',
        'prompt' => 'consent',
        'state' => $state,
    ]);

    $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . $params;

    send_json_response(200, [
        'success' => true,
        'authUrl' => $authUrl,
        'timestamp' => date('c'),
    ]);
}

/**
 * Check if company has valid Google tokens.
 */
function handleStatus(array $company): void
{
    $db = get_db_connection();
    $stmt = $db->prepare(
        'SELECT google_refresh_token, google_token_expires
         FROM portal_companies WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $company['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();

    $hasTokens = !empty($row['google_refresh_token']);

    send_json_response(200, [
        'success' => true,
        'authenticated' => $hasTokens,
        'hasTokens' => $hasTokens,
        'timestamp' => date('c'),
    ]);
}

/**
 * Revoke Google tokens for this company.
 */
function handleRevoke(array $company): void
{
    $db = get_db_connection();

    // Get current access token to revoke at Google
    $stmt = $db->prepare(
        'SELECT google_access_token FROM portal_companies WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $company['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Try to revoke at Google (best effort)
    if (!empty($row['google_access_token'])) {
        $token = portal_decrypt($row['google_access_token']);
        if ($token) {
            $ch = curl_init('https://oauth2.googleapis.com/revoke?token=' . urlencode($token));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            curl_close($ch);
        }
    }

    // Clear tokens from database
    $stmt = $db->prepare(
        'UPDATE portal_companies
         SET google_refresh_token = NULL, google_access_token = NULL, google_token_expires = NULL
         WHERE id = ?'
    );
    $stmt->bind_param('i', $company['id']);
    $stmt->execute();
    $stmt->close();
    $db->close();

    send_json_response(200, [
        'success' => true,
        'message' => 'Google account disconnected.',
        'timestamp' => date('c'),
    ]);
}
