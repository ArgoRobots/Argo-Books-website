<?php
/**
 * Google OAuth Helper Functions
 *
 * Shared authentication and token management for Google API endpoints.
 * Google Sheets is a free feature — authentication uses device ID.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

/**
 * Authenticate a Google API request using device ID.
 * Google Sheets is a free feature, so only a device identifier is required.
 *
 * @return array|null Auth context with device_id_hash, or null if missing
 */
function authenticate_google_request(): ?array
{
    $deviceIdHash = authenticate_device_request();
    if ($deviceIdHash) {
        return [
            'type' => 'device',
            'device_id_hash' => $deviceIdHash,
        ];
    }

    return null;
}

/**
 * Get Google OAuth tokens for the given auth context.
 * Falls back to portal_companies table for users who connected via the old portal flow.
 *
 * @param array $authContext Auth context from authenticate_google_request()
 * @return array|null Token row with google_refresh_token, google_access_token, google_token_expires
 */
function get_google_tokens(array $authContext): ?array
{
    $db = get_db_connection();

    // Query device-based tokens
    $stmt = $db->prepare(
        'SELECT google_access_token, google_refresh_token, google_token_expires
         FROM google_oauth_tokens WHERE device_id_hash = ? LIMIT 1'
    );
    $stmt->bind_param('s', $authContext['device_id_hash']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $db->close();
    return $row ?: null;
}

/**
 * Update just the access token for the given auth context (after refresh).
 */
function update_google_access_token(array $authContext, string $encryptedAccessToken, string $expiresAt): void
{
    $db = get_db_connection();

    $stmt = $db->prepare(
        'UPDATE google_oauth_tokens SET google_access_token = ?, google_token_expires = ? WHERE device_id_hash = ?'
    );
    $stmt->bind_param('sss', $encryptedAccessToken, $expiresAt, $authContext['device_id_hash']);

    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Clear Google OAuth tokens for the given auth context.
 */
function clear_google_tokens(array $authContext): void
{
    $db = get_db_connection();

    $stmt = $db->prepare(
        'DELETE FROM google_oauth_tokens WHERE device_id_hash = ?'
    );
    $stmt->bind_param('s', $authContext['device_id_hash']);

    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Store an OAuth state token for Google auth flow.
 * Uses a dedicated table to avoid FK constraints on portal_oauth_states.
 */
function store_google_oauth_state(array $authContext, string $state): void
{
    $db = get_db_connection();

    // Clean up expired states for this device
    $stmt = $db->prepare('DELETE FROM google_oauth_states WHERE device_id_hash = ? OR expires_at < NOW()');
    $stmt->bind_param('s', $authContext['device_id_hash']);
    $stmt->execute();
    $stmt->close();

    $stmt = $db->prepare(
        'INSERT INTO google_oauth_states (state_token, device_id_hash, expires_at)
         VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
    );
    $stmt->bind_param('ss', $state, $authContext['device_id_hash']);

    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Encrypt a string using AES-256-GCM with a key derived from GOOGLE_CLIENT_SECRET.
 * This keeps Google OAuth token encryption independent of the payment portal.
 */
function google_encrypt(string $plaintext): string
{
    $secret = trim($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
    if (empty($secret)) {
        throw new RuntimeException('GOOGLE_CLIENT_SECRET is not configured.');
    }
    $key = hash('sha256', $secret, true); // 32 bytes
    $iv = random_bytes(12);
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ciphertext === false) {
        throw new RuntimeException('Encryption failed.');
    }
    return base64_encode($iv . $tag . $ciphertext);
}

/**
 * Decrypt a string encrypted with google_encrypt().
 */
function google_decrypt(string $encoded): string
{
    $secret = trim($_ENV['GOOGLE_CLIENT_SECRET'] ?? '');
    if (empty($secret)) {
        throw new RuntimeException('GOOGLE_CLIENT_SECRET is not configured.');
    }
    $key = hash('sha256', $secret, true);
    $data = base64_decode($encoded, true);
    if ($data === false || strlen($data) < 28) {
        throw new RuntimeException('Invalid encrypted data.');
    }
    $iv = substr($data, 0, 12);
    $tag = substr($data, 12, 16);
    $ciphertext = substr($data, 28);
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plaintext === false) {
        throw new RuntimeException('Decryption failed.');
    }
    return $plaintext;
}

/**
 * Refresh an expired Google access token.
 *
 * @param string $refreshToken Decrypted refresh token
 * @param array $authContext Auth context
 * @return string|null New access token or null on failure
 */
function refresh_google_token(string $refreshToken, array $authContext): ?string
{
    $clientId = $_ENV['GOOGLE_CLIENT_ID'] ?? '';
    $clientSecret = $_ENV['GOOGLE_CLIENT_SECRET'] ?? '';

    $payload = http_build_query([
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'refresh_token' => $refreshToken,
        'grant_type' => 'refresh_token',
    ]);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        error_log('Google token refresh failed: ' . ($response ?: 'curl error'));
        return null;
    }

    $tokenData = json_decode($response, true);
    $newAccessToken = $tokenData['access_token'] ?? '';
    $expiresIn = $tokenData['expires_in'] ?? 3600;

    if (empty($newAccessToken)) {
        return null;
    }

    // Update stored token
    $encrypted = google_encrypt($newAccessToken);
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);
    update_google_access_token($authContext, $encrypted, $expiresAt);

    return $newAccessToken;
}
