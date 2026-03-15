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

    // Ensure table exists
    $db->query(
        'CREATE TABLE IF NOT EXISTS google_oauth_tokens (
            id INT PRIMARY KEY AUTO_INCREMENT,
            device_id_hash VARCHAR(64) NOT NULL UNIQUE,
            google_refresh_token TEXT DEFAULT NULL,
            google_access_token TEXT DEFAULT NULL,
            google_token_expires DATETIME DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_device_id_hash (device_id_hash)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    // Try device-based tokens first
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
 * Store Google OAuth tokens for the given auth context.
 */
function store_google_tokens(array $authContext, string $encryptedAccessToken, string $encryptedRefreshToken, string $expiresAt): void
{
    $db = get_db_connection();

    $stmt = $db->prepare(
        'INSERT INTO google_oauth_tokens (device_id_hash, google_access_token, google_refresh_token, google_token_expires)
         VALUES (?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            google_access_token = VALUES(google_access_token),
            google_refresh_token = VALUES(google_refresh_token),
            google_token_expires = VALUES(google_token_expires)'
    );
    $stmt->bind_param('ssss', $authContext['device_id_hash'], $encryptedAccessToken, $encryptedRefreshToken, $expiresAt);

    $stmt->execute();
    $stmt->close();
    $db->close();
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

    // Ensure table exists
    $db->query(
        'CREATE TABLE IF NOT EXISTS google_oauth_states (
            id INT PRIMARY KEY AUTO_INCREMENT,
            state_token VARCHAR(255) NOT NULL UNIQUE,
            device_id_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_device_id_hash (device_id_hash),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

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
    $encrypted = portal_encrypt($newAccessToken);
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);
    update_google_access_token($authContext, $encrypted, $expiresAt);

    return $newAccessToken;
}
