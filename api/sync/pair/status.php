<?php
/**
 * POST /api/sync/pair/status - desktop polls for the phone's public key.
 * Auth: desktop owner (license key or device id).
 * Body: { "pairing_token": "..." }
 * Returns: { success, status, phone_public_key?, device_label? }
 * phone_public_key/device_label are only present once status is 'claimed' or later.
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

$status = get_pairing_status($token, $owner);
if (!$status) {
    send_error_response(404, 'Pairing not found.', 'NOT_FOUND');
}

send_json_response(200, array_merge(['success' => true], $status));
