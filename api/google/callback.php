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
$stmt = $pdo->prepare(
    'SELECT device_id_hash FROM google_oauth_states
     WHERE state_token = ? AND expires_at > NOW()
     LIMIT 1'
);
$stmt->execute([$state]);
$row = $stmt->fetch();

if (!$row) {
    showResult(false, 'Invalid or expired authorization state. Please try again from the app.');
}

$deviceIdHash = $row['device_id_hash'];

// Clean up used state token
$stmt = $pdo->prepare('DELETE FROM google_oauth_states WHERE state_token = ?');
$stmt->execute([$state]);

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
    error_log('Google token exchange failed (HTTP ' . $httpCode . '): ' . ($response ?: $curlError));
    showResult(false, 'Failed to complete Google authorization. Please try again.');
}

$tokenData = json_decode($response, true);
$accessToken = $tokenData['access_token'] ?? '';
$refreshToken = $tokenData['refresh_token'] ?? '';
$expiresIn = $tokenData['expires_in'] ?? 3600;

if (empty($accessToken)) {
    showResult(false, 'Invalid token response from Google.');
}

// Encrypt tokens before storing
$encryptedAccess = google_encrypt($accessToken);
$encryptedRefresh = !empty($refreshToken) ? google_encrypt($refreshToken) : null;
$expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);

// Store tokens in database
$stmt = $pdo->prepare(
    'INSERT INTO google_oauth_tokens (device_id_hash, google_access_token, google_refresh_token, google_token_expires)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        google_access_token = VALUES(google_access_token),
        google_refresh_token = COALESCE(VALUES(google_refresh_token), google_refresh_token),
        google_token_expires = VALUES(google_token_expires)'
);
$stmt->execute([$deviceIdHash, $encryptedAccess, $encryptedRefresh, $expiresAt]);

showResult(true, 'Google Sheets connected successfully!');

/**
 * Show a result page that auto-closes on success.
 */
function showResult(bool $success, string $message): void
{
    $iconColor = $success ? '#22c55e' : '#ef4444';
    $title = $success ? 'Connected' : 'Connection Failed';
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $iconSvg = $success
        ? '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="' . $iconColor . '" opacity="0.1"/><circle cx="28" cy="28" r="20" fill="' . $iconColor . '" opacity="0.15"/><path d="M20 28.5L25.5 34L36 22" stroke="' . $iconColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        : '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="' . $iconColor . '" opacity="0.1"/><circle cx="28" cy="28" r="20" fill="' . $iconColor . '" opacity="0.15"/><path d="M22 22L34 34M34 22L22 34" stroke="' . $iconColor . '" stroke-width="3" stroke-linecap="round"/></svg>';
    $autoCloseScript = $success ? '<script>setTimeout(function(){window.close()},1500);</script>' : '';
    $closingNote = $success
        ? '<p class="callback-hint">This window will close automatically...</p>'
        : '<p class="callback-hint">You can close this window and try again from Argo Books.</p>';

    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Google Sheets - {$title}</title>
    <link rel="shortcut icon" type="image/x-icon" href="/resources/images/argo-logo/argo-icon.ico">
    <link rel="stylesheet" href="/resources/styles/custom-colors.css">
    <link rel="stylesheet" href="/portal/style.css">
    {$autoCloseScript}
    <style>
        .callback-result { text-align: center; padding: 60px 20px; max-width: 480px; margin: 0 auto; }
        .callback-icon { margin-bottom: 20px; }
        .callback-title { font-size: 22px; font-weight: 600; color: var(--gray-900, #111); margin: 0; }
        .callback-message { color: var(--gray-900, #111); font-size: 15px; margin-top: 12px; line-height: 1.5; }
        .callback-hint { color: var(--gray-900, #111); font-size: 13px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="portal-page">
        <header class="portal-header">
            <div class="portal-header-inner">
                <div class="company-info">
                    <h1 class="company-name">Argo Books</h1>
                    <span class="portal-subtitle">Google Sheets integration</span>
                </div>
            </div>
        </header>
        <main class="portal-main">
            <div class="callback-result">
                <div class="callback-icon">{$iconSvg}</div>
                <h2 class="callback-title">{$title}</h2>
                <p class="callback-message">{$safeMessage}</p>
                {$closingNote}
            </div>
        </main>
        <footer class="portal-footer">
            <p>Powered by <a href="https://argorobots.com" target="_blank" rel="noopener">Argo Books</a></p>
        </footer>
    </div>
</body>
</html>
HTML;
    exit;
}
