<?php
/**
 * Google OAuth Helper Functions
 *
 * Shared authentication and token management for Google API endpoints.
 * Supports both portal API key and license key authentication.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

/**
 * Authenticate a Google API request using either portal API key or license key.
 * Returns an auth context array with 'type' ('portal' or 'license') and relevant IDs.
 *
 * @return array|null Auth context or null if authentication fails
 */
function authenticate_google_request(): ?array
{
    // Try portal API key first
    $company = authenticate_portal_request();
    if ($company) {
        return [
            'type' => 'portal',
            'company_id' => $company['id'],
            'company' => $company,
        ];
    }

    // Try license key
    $licenseKey = '';
    if (!empty($_SERVER['HTTP_X_LICENSE_KEY'])) {
        $licenseKey = $_SERVER['HTTP_X_LICENSE_KEY'];
    }

    if (empty($licenseKey)) {
        return null;
    }

    // Validate the license key has an active premium subscription
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT subscription_key, subscription_id, redeemed_at
            FROM premium_subscription_keys
            WHERE subscription_key = ?
        ");
        $stmt->execute([$licenseKey]);
        $premiumKey = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$premiumKey || $premiumKey['redeemed_at'] === null) {
            return null;
        }

        // Check the linked subscription is active
        $stmt = $pdo->prepare("
            SELECT status, end_date
            FROM premium_subscriptions
            WHERE subscription_id = ?
        ");
        $stmt->execute([$premiumKey['subscription_id']]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$subscription) {
            return null;
        }

        $now = new DateTime();
        $endDate = new DateTime($subscription['end_date']);

        if (!in_array($subscription['status'], ['active', 'cancelled']) || $endDate <= $now) {
            return null;
        }

        return [
            'type' => 'license',
            'license_key_hash' => hash('sha256', $licenseKey),
        ];
    } catch (PDOException $e) {
        error_log("Google auth license key error: " . $e->getMessage());
        return null;
    }
}

/**
 * Get Google OAuth tokens for the given auth context.
 *
 * @param array $authContext Auth context from authenticate_google_request()
 * @return array|null Token row with google_refresh_token, google_access_token, google_token_expires
 */
function get_google_tokens(array $authContext): ?array
{
    $db = get_db_connection();

    if ($authContext['type'] === 'portal') {
        $stmt = $db->prepare(
            'SELECT google_access_token, google_refresh_token, google_token_expires
             FROM portal_companies WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $authContext['company_id']);
    } else {
        $stmt = $db->prepare(
            'SELECT google_access_token, google_refresh_token, google_token_expires
             FROM google_oauth_tokens WHERE license_key_hash = ? LIMIT 1'
        );
        $stmt->bind_param('s', $authContext['license_key_hash']);
    }

    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();

    return $row ?: null;
}

/**
 * Store Google OAuth tokens for the given auth context.
 *
 * @param array $authContext Auth context from authenticate_google_request()
 * @param string $encryptedAccessToken Encrypted access token
 * @param string $encryptedRefreshToken Encrypted refresh token
 * @param string $expiresAt Token expiry datetime string
 */
function store_google_tokens(array $authContext, string $encryptedAccessToken, string $encryptedRefreshToken, string $expiresAt): void
{
    $db = get_db_connection();

    if ($authContext['type'] === 'portal') {
        $stmt = $db->prepare(
            'UPDATE portal_companies
             SET google_access_token = ?, google_refresh_token = ?, google_token_expires = ?
             WHERE id = ?'
        );
        $stmt->bind_param('sssi', $encryptedAccessToken, $encryptedRefreshToken, $expiresAt, $authContext['company_id']);
    } else {
        $stmt = $db->prepare(
            'INSERT INTO google_oauth_tokens (license_key_hash, google_access_token, google_refresh_token, google_token_expires)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                google_access_token = VALUES(google_access_token),
                google_refresh_token = VALUES(google_refresh_token),
                google_token_expires = VALUES(google_token_expires)'
        );
        $stmt->bind_param('ssss', $authContext['license_key_hash'], $encryptedAccessToken, $encryptedRefreshToken, $expiresAt);
    }

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

    if ($authContext['type'] === 'portal') {
        $stmt = $db->prepare(
            'UPDATE portal_companies SET google_access_token = ?, google_token_expires = ? WHERE id = ?'
        );
        $stmt->bind_param('ssi', $encryptedAccessToken, $expiresAt, $authContext['company_id']);
    } else {
        $stmt = $db->prepare(
            'UPDATE google_oauth_tokens SET google_access_token = ?, google_token_expires = ? WHERE license_key_hash = ?'
        );
        $stmt->bind_param('sss', $encryptedAccessToken, $expiresAt, $authContext['license_key_hash']);
    }

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

    if ($authContext['type'] === 'portal') {
        $stmt = $db->prepare(
            'UPDATE portal_companies
             SET google_refresh_token = NULL, google_access_token = NULL, google_token_expires = NULL
             WHERE id = ?'
        );
        $stmt->bind_param('i', $authContext['company_id']);
    } else {
        $stmt = $db->prepare(
            'DELETE FROM google_oauth_tokens WHERE license_key_hash = ?'
        );
        $stmt->bind_param('s', $authContext['license_key_hash']);
    }

    $stmt->execute();
    $stmt->close();
    $db->close();
}

/**
 * Store an OAuth state token for Google auth flow.
 */
function store_google_oauth_state(array $authContext, string $state): void
{
    $db = get_db_connection();

    if ($authContext['type'] === 'portal') {
        // Clean up expired states
        $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE company_id = ? OR expires_at < NOW()');
        $stmt->bind_param('i', $authContext['company_id']);
        $stmt->execute();
        $stmt->close();

        $stmt = $db->prepare(
            'INSERT INTO portal_oauth_states (state_token, company_id, provider, expires_at)
             VALUES (?, ?, "google", DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
        );
        $stmt->bind_param('si', $state, $authContext['company_id']);
    } else {
        // For license key auth, store state with license_key_hash in the provider field
        $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE provider = ? OR expires_at < NOW()');
        $providerKey = 'google_license_' . $authContext['license_key_hash'];
        $stmt->bind_param('s', $providerKey);
        $stmt->execute();
        $stmt->close();

        // Use company_id = 0 for license-key-based states, store hash in provider
        $companyId = 0;
        $stmt = $db->prepare(
            'INSERT INTO portal_oauth_states (state_token, company_id, provider, expires_at)
             VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))'
        );
        $stmt->bind_param('sis', $state, $companyId, $providerKey);
    }

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
