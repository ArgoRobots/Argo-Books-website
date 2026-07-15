<?php
/**
 * POST /api/sync/devices/revoke - desktop unpairs a phone.
 * Auth: desktop owner. Body: { "company_uid": "...", "device_id": 123 }
 * Deletes the device (scoped to owner+company). If none remain for the company,
 * purges the company's snapshot and queued items.
 */
require_once __DIR__ . '/../sync-helper.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../')->safeLoad();

set_portal_headers();
require_method(['POST']);

$owner = resolve_owner_identity();
if (!$owner) {
    send_error_response(401, 'Unauthorized.', 'UNAUTHORIZED');
}
$data = json_decode(file_get_contents('php://input'), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON body.', 'INVALID_JSON');
}
$companyUid = trim((string) ($data['company_uid'] ?? ''));
$deviceId = (int) ($data['device_id'] ?? 0);
if ($companyUid === '' || $deviceId <= 0) {
    send_error_response(400, 'company_uid and device_id are required.', 'INVALID_INPUT');
}

global $pdo;
$pdo->prepare('DELETE FROM mobile_sync_devices WHERE id = ? AND owner_identity_hash = ? AND company_uid = ?')
    ->execute([$deviceId, $owner, $companyUid]);

$stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM mobile_sync_devices WHERE company_uid = ? AND owner_identity_hash = ?');
$stmt->execute([$companyUid, $owner]);
$remaining = (int) $stmt->fetch()['c'];

if ($remaining === 0) {
    $pdo->prepare('DELETE FROM mobile_sync_snapshots WHERE company_uid = ? AND owner_identity_hash = ?')->execute([$companyUid, $owner]);
    $pdo->prepare('DELETE FROM mobile_sync_queue WHERE company_uid = ? AND owner_identity_hash = ?')->execute([$companyUid, $owner]);
}

send_json_response(200, ['success' => true, 'remaining_devices' => $remaining]);
