<?php
/**
 * POST /api/sync/snapshot/get - phone downloads the latest snapshot for its company.
 * Auth: phone device token. Returns { success, ciphertext, updated_at } or 404 if none yet.
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

global $pdo;
$stmt = $pdo->prepare('SELECT ciphertext, updated_at FROM mobile_sync_snapshots WHERE company_uid = ? LIMIT 1');
$stmt->execute([$device['company_uid']]);
$row = $stmt->fetch();
if (!$row) {
    send_error_response(404, 'No snapshot yet. Open Argo Books on your computer to sync.', 'NO_SNAPSHOT');
}

send_json_response(200, [
    'success' => true,
    'ciphertext' => $row['ciphertext'],
    'updated_at' => $row['updated_at'],
]);
