<?php
/**
 * Google OAuth Callback Endpoint
 *
 * GET /api/google/callback - Handle Google OAuth redirect after user authorization
 *
 * Exchanges the authorization code for access/refresh tokens and stores them
 * (encrypted) in the portal_companies table.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

// Load environment variables
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

// Get state and code from callback
$state = $_GET['state'] ?? '';
$code = $_GET['code'] ?? '';
$error = $_GET['error'] ?? '';

// Handle OAuth errors
if (!empty($error)) {
    showResult(false, 'Google authorization was denied: ' . htmlspecialchars($error));
}

if (empty($state) || empty($code)) {
    showResult(false, 'Missing authorization parameters.');
}

// Validate state token against database
$db = get_db_connection();
$stmt = $db->prepare(
    'SELECT company_id FROM portal_oauth_states
     WHERE state_token = ? AND provider = "google" AND expires_at > NOW()
     LIMIT 1'
);
$stmt->bind_param('s', $state);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    $db->close();
    showResult(false, 'Invalid or expired authorization state. Please try again from the app.');
}

$companyId = $row['company_id'];

// Clean up used state token
$stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE state_token = ?');
$stmt->bind_param('s', $state);
$stmt->execute();
$stmt->close();

// Exchange authorization code for tokens
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
$baseUrl = $_ENV['APP_URL'] ?? 'https://argorobots.com';
$redirectUri = $baseUrl . '/api/google/callback';

$tokenPayload = http_build_query([
    'code' => $code,
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri,
    'grant_type' => 'authorization_code',
]);

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $tokenPayload,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT => 15,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    $db->close();
    error_log('Google token exchange failed (HTTP ' . $httpCode . '): ' . ($response ?: $curlError));
    showResult(false, 'Failed to complete Google authorization. Please try again.');
}

$tokenData = json_decode($response, true);
$accessToken = $tokenData['access_token'] ?? '';
$refreshToken = $tokenData['refresh_token'] ?? '';
$expiresIn = $tokenData['expires_in'] ?? 3600;

if (empty($accessToken)) {
    $db->close();
    showResult(false, 'Invalid token response from Google.');
}

// Encrypt tokens before storing
$encryptedAccess = portal_encrypt($accessToken);
$encryptedRefresh = !empty($refreshToken) ? portal_encrypt($refreshToken) : null;
$expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

// Store tokens in database
$stmt = $db->prepare(
    'UPDATE portal_companies
     SET google_access_token = ?, google_refresh_token = COALESCE(?, google_refresh_token), google_token_expires = ?
     WHERE id = ?'
);
$stmt->bind_param('sssi', $encryptedAccess, $encryptedRefresh, $expiresAt, $companyId);
$stmt->execute();
$stmt->close();
$db->close();

showResult(true, 'Google Sheets connected successfully! You can close this window.');

/**
 * Show a simple HTML result page that auto-closes.
 */
function showResult(bool $success, string $message): void
{
    $color = $success ? '#059669' : '#dc2626';
    $icon = $success ? '&#10003;' : '&#10007;';
    $title = $success ? 'Connected' : 'Error';

    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets - {$title}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .card { background: white; border-radius: 12px; padding: 48px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; }
        .icon { font-size: 48px; color: {$color}; margin-bottom: 16px; }
        h1 { font-size: 24px; color: #111827; margin: 0 0 12px; }
        p { font-size: 16px; color: #6b7280; margin: 0; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">{$icon}</div>
        <h1>{$title}</h1>
        <p>{$message}</p>
    </div>
</body>
</html>
HTML;
    exit;
}
