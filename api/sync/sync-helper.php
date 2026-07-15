<?php
/**
 * Shared helpers for the mobile sync endpoints (api/sync/*).
 *
 * Auth model:
 *  - Desktop ("owner") authenticates with its existing license key (premium) or
 *    device id (free), via portal-helper's authenticate_license_request /
 *    authenticate_device_request. resolve_owner_identity() returns the owner hash.
 *  - Phone authenticates with a long-lived device token (X-Sync-Device-Token),
 *    issued at pairing, via authenticate_sync_device().
 *
 * The AES key and all plaintext live only on the paired devices; this server
 * only moves opaque ciphertext.
 */

require_once __DIR__ . '/../portal/portal-helper.php';

/**
 * Resolve the desktop "owner" identity hash from the request, or null.
 * Premium: sha256 license-key hash. Free: sha256 device-id hash.
 */
function resolve_owner_identity(): ?string
{
    $license = authenticate_license_request();
    if ($license) {
        return $license['license_key_hash'];
    }
    $deviceHash = authenticate_device_request();
    return $deviceHash ?: null;
}

/**
 * Authenticate a phone by its X-Sync-Device-Token header.
 * Returns ['device_id','company_uid','owner_identity_hash'] and bumps last_seen_at, or null.
 */
function authenticate_sync_device(): ?array
{
    global $pdo;
    $token = $_SERVER['HTTP_X_SYNC_DEVICE_TOKEN'] ?? '';
    if ($token === '') {
        return null;
    }
    $tokenHash = hash('sha256', $token);
    $stmt = $pdo->prepare(
        'SELECT id, company_uid, owner_identity_hash FROM mobile_sync_devices WHERE device_token_hash = ? LIMIT 1'
    );
    $stmt->execute([$tokenHash]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $pdo->prepare('UPDATE mobile_sync_devices SET last_seen_at = NOW() WHERE id = ?')->execute([$row['id']]);
    return [
        'device_id' => (int) $row['id'],
        'company_uid' => $row['company_uid'],
        'owner_identity_hash' => $row['owner_identity_hash'],
    ];
}

/**
 * Generate an 8-char short code from an alphabet that excludes ambiguous
 * characters (no 0 1 I L O U), for the phone to type in manually.
 */
function generate_pairing_short_code(): string
{
    $alphabet = '23456789ABCDEFGHJKMNPQRSTVWXYZ';
    $max = strlen($alphabet) - 1;
    $code = '';
    for ($i = 0; $i < 8; $i++) {
        $code .= $alphabet[random_int(0, $max)];
    }
    return $code;
}

/**
 * Create a single-use, 10-minute pairing token for (owner, company). Opportunistically
 * GCs expired tokens. Also mints a unique short code for the manual-entry pairing flow.
 * Returns ['token' => bin2hex(random_bytes(16)), 'short_code' => string].
 */
function create_pairing_token(string $ownerHash, string $companyUid, string $companyLabel): array
{
    global $pdo;
    $pdo->prepare('DELETE FROM mobile_sync_pairings WHERE expires_at < NOW()')->execute();

    $token = bin2hex(random_bytes(16));
    $insert = $pdo->prepare(
        "INSERT INTO mobile_sync_pairings
            (pairing_token, short_code, owner_identity_hash, company_uid, company_label, status, expires_at)
         VALUES (?, ?, ?, ?, ?, 'pending', DATE_ADD(NOW(), INTERVAL 10 MINUTE))"
    );

    $attempts = 0;
    while (true) {
        $code = generate_pairing_short_code();
        try {
            $insert->execute([$token, $code, $ownerHash, $companyUid, $companyLabel]);
            break;
        } catch (\PDOException $e) {
            $attempts++;
            // MySQL duplicate-entry error code is 23000; retry with a fresh code
            // a few times in case of a short_code collision, then give up.
            if ($e->getCode() !== '23000' || $attempts >= 5) {
                throw $e;
            }
        }
    }

    return ['token' => $token, 'short_code' => $code];
}

/**
 * Validate + consume (single use) a pairing token. Returns the binding or null.
 */
function consume_pairing_token(string $token): ?array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT owner_identity_hash, company_uid, company_label
         FROM mobile_sync_pairings WHERE pairing_token = ? AND expires_at > NOW() LIMIT 1'
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $del = $pdo->prepare('DELETE FROM mobile_sync_pairings WHERE pairing_token = ? AND expires_at > NOW()');
    $del->execute([$token]);
    if ($del->rowCount() !== 1) {
        return null;
    }
    return $row;
}
