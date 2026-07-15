<?php
/**
 * POST /api/sync/pair/key - the phone polls for the encrypted sync key.
 * Auth: phone device token, via X-Sync-Device-Token header (issued at claim time).
 * Returns: { pending: true } before delivery, or { success, encrypted_sync_key } once
 * delivered, after which the pairing row is deleted (the mobile_sync_devices row persists).
 */
require_once __DIR__ . '/../sync-helper.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../')->safeLoad();

set_portal_headers();
require_method(['POST']);

$token = $_SERVER['HTTP_X_SYNC_DEVICE_TOKEN'] ?? '';
if ($token === '') {
    send_error_response(401, 'Missing device token.', 'UNAUTHORIZED');
}

$result = fetch_and_consume_pairing_key($token);
if ($result === null) {
    send_error_response(404, 'Pairing not found.', 'NOT_FOUND');
}

if (isset($result['pending'])) {
    send_json_response(200, ['pending' => true]);
}

send_json_response(200, [
    'success' => true,
    'encrypted_sync_key' => $result['encrypted_sync_key'],
]);
