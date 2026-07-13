<?php
/**
 * POST /api/sync/queue/push - phone uploads an encrypted scanned transaction.
 * Auth: phone device token. Body: { "ciphertext": "<opaque base64>" }
 */
require_once __DIR__ . '/../sync-helper.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../')->safeLoad();

set_portal_headers();
require_method(['POST']);

$device = authenticate_sync_device();
if (!$device) {
    send_error_response(401, 'Unpaired or invalid device.', 'UNAUTHORIZED');
}

$ip = get_client_ip();
if (is_rate_limited($ip, 120, 900, 'sync_push')) {
    send_error_response(429, 'Too many uploads. Try again later.', 'RATE_LIMITED');
}
record_rate_limit_attempt($ip, 'sync_push', 900);

$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON body.', 'INVALID_JSON');
}
$ciphertext = (string) ($data['ciphertext'] ?? '');
if ($ciphertext === '') {
    send_error_response(400, 'ciphertext is required.', 'INVALID_INPUT');
}

global $pdo;
$pdo->prepare(
    'INSERT INTO mobile_sync_queue (company_uid, owner_identity_hash, from_device_id, ciphertext) VALUES (?, ?, ?, ?)'
)->execute([$device['company_uid'], $device['owner_identity_hash'], $device['device_id'], $ciphertext]);

send_json_response(200, ['success' => true]);
