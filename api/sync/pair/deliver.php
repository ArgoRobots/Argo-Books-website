<?php
/**
 * POST /api/sync/pair/deliver - desktop uploads the RSA-encrypted sync key
 * once the phone has claimed the pairing.
 * Auth: desktop owner (license key or device id).
 * Body: { "pairing_token": "...", "encrypted_sync_key": "..." }
 * Returns: { success }
 */
require_once __DIR__ . '/../sync-helper.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../')->safeLoad();

set_portal_headers();
require_method(['POST']);

$owner = resolve_owner_identity();
if (!$owner) {
    send_error_response(401, 'Missing or invalid license key or device id.', 'UNAUTHORIZED');
}

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON body.', 'INVALID_JSON');
}

$token = trim((string) ($data['pairing_token'] ?? ''));
if ($token === '') {
    send_error_response(400, 'pairing_token is required.', 'INVALID_TOKEN');
}

$encryptedSyncKey = trim((string) ($data['encrypted_sync_key'] ?? ''));
if ($encryptedSyncKey === '' || strlen($encryptedSyncKey) > 4000 || !preg_match('/^[A-Za-z0-9+\/=]+$/', $encryptedSyncKey)) {
    send_error_response(400, 'encrypted_sync_key must be a non-empty base64 string.', 'INVALID_KEY');
}

$delivered = deliver_pairing_key($token, $owner, $encryptedSyncKey);
if (!$delivered) {
    send_error_response(400, 'That pairing is not ready to receive a key.', 'INVALID_PAIRING');
}

send_json_response(200, ['success' => true]);
