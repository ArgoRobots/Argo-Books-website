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
 *
 * Free feature — authentication uses device ID.
 */

require_once __DIR__ . '/google-helper.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

set_portal_headers();
require_method(['POST']);

// Authenticate via device ID (free feature)
$authContext = authenticate_google_request();
if (!$authContext) {
    send_error_response(401, 'Missing device identifier.', 'UNAUTHORIZED');
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
        handleInitiate($authContext, $clientId);
        break;

    case 'status':
        handleStatus($authContext);
        break;

    case 'revoke':
        handleRevoke($authContext);
        break;

    default:
        send_error_response(400, 'Invalid action. Supported: initiate, status, revoke.', 'INVALID_ACTION');
}

/**
 * Generate Google OAuth URL and store state token.
 */
function handleInitiate(array $authContext, string $clientId): void
{
    $state = bin2hex(random_bytes(16));

    store_google_oauth_state($authContext, $state);

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
function handleStatus(array $authContext): void
{
    $tokenRow = get_google_tokens($authContext);
    $hasTokens = !empty($tokenRow['google_refresh_token']);

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
function handleRevoke(array $authContext): void
{
    $tokenRow = get_google_tokens($authContext);

    // Try to revoke at Google (best effort)
    if (!empty($tokenRow['google_access_token'])) {
        try {
            $token = google_decrypt($tokenRow['google_access_token']);
            $ch = curl_init('https://oauth2.googleapis.com/revoke?token=' . urlencode($token));
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (RuntimeException $e) {
            error_log('Failed to decrypt Google token for revocation: ' . $e->getMessage());
        }
    }

    clear_google_tokens($authContext);

    send_json_response(200, [
        'success' => true,
        'message' => 'Google account disconnected.',
        'timestamp' => date('c'),
    ]);
}
