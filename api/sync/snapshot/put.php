<?php
/**
 * POST /api/sync/snapshot/put - desktop uploads the encrypted read-model snapshot.
 * Auth: desktop owner. Body: { "company_uid": "...", "ciphertext": "<opaque base64>" }
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
$ciphertext = (string) ($data['ciphertext'] ?? '');
if ($companyUid === '' || strlen($companyUid) > 64 || $ciphertext === '') {
    send_error_response(400, 'company_uid (max 64 chars) and ciphertext are required.', 'INVALID_INPUT');
}

global $pdo;
$pdo->prepare(
    'INSERT INTO mobile_sync_snapshots (company_uid, owner_identity_hash, ciphertext)
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE ciphertext = VALUES(ciphertext), owner_identity_hash = VALUES(owner_identity_hash)'
)->execute([$companyUid, $owner, $ciphertext]);

send_json_response(200, ['success' => true]);
