<?php
/**
 * Google OAuth Callback Endpoint
 *
 * GET /api/google/callback - Handle Google OAuth redirect after user authorization
 *
 * Exchanges the authorization code for access/refresh tokens and stores them
 * (encrypted) in the google_oauth_tokens table.
 */

require_once __DIR__ . '/google-helper.php';

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

// Validate state token against google_oauth_states table
$db = get_db_connection();
$stmt = $db->prepare(
    'SELECT device_id_hash FROM google_oauth_states
     WHERE state_token = ? AND expires_at > NOW()
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

$deviceIdHash = $row['device_id_hash'];

// Clean up used state token
$stmt = $db->prepare('DELETE FROM google_oauth_states WHERE state_token = ?');
$stmt->bind_param('s', $state);
$stmt->execute();
$stmt->close();

// Exchange authorization code for tokens
$clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
$clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';
$redirectUri = site_url('/api/google/callback');

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
$encryptedAccess = google_encrypt($accessToken);
$encryptedRefresh = !empty($refreshToken) ? google_encrypt($refreshToken) : null;
$expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

// Store tokens in database
$stmt = $db->prepare(
    'INSERT INTO google_oauth_tokens (device_id_hash, google_access_token, google_refresh_token, google_token_expires)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        google_access_token = VALUES(google_access_token),
        google_refresh_token = COALESCE(VALUES(google_refresh_token), google_refresh_token),
        google_token_expires = VALUES(google_token_expires)'
);
$stmt->bind_param('ssss', $deviceIdHash, $encryptedAccess, $encryptedRefresh, $expiresAt);
$stmt->execute();
$stmt->close();
$db->close();

showResult(true, 'Google Sheets connected successfully!');

/**
 * Show a result page that auto-closes on success.
 */
function showResult(bool $success, string $message): void
{
    $color = $success ? '#059669' : '#dc2626';
    $icon = $success ? '&#10003;' : '&#10007;';
    $title = $success ? 'Connected' : 'Error';
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $autoCloseScript = $success ? '<script>setTimeout(function(){window.close()},1500);</script>' : '';
    $closingNote = $success ? '<p style="font-size:13px;color:#9ca3af;margin-top:12px;">This window will close automatically...</p>' : '';

    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets - {$title}</title>
    {$autoCloseScript}
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
        <p>{$safeMessage}</p>
        {$closingNote}
    </div>
</body>
</html>
HTML;
    exit;
}
