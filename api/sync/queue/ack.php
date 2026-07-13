<?php
/**
 * POST /api/sync/queue/ack - desktop confirms it ingested items; delete them.
 * Auth: desktop owner. Body: { "company_uid": "...", "ids": [1,2,3] }
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
$ids = $data['ids'] ?? [];
if ($companyUid === '' || strlen($companyUid) > 64 || !is_array($ids) || $ids === []) {
    send_error_response(400, 'company_uid (max 64 chars) and non-empty ids are required.', 'INVALID_INPUT');
}
$ids = array_values(array_filter(array_map('intval', $ids), fn ($n) => $n > 0));
$ids = array_slice($ids, 0, 500);
if ($ids === []) {
    send_json_response(200, ['success' => true, 'deleted' => 0]);
}

global $pdo;
$in = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare(
    "DELETE FROM mobile_sync_queue WHERE company_uid = ? AND owner_identity_hash = ? AND id IN ($in)"
);
$stmt->execute(array_merge([$companyUid, $owner], $ids));
send_json_response(200, ['success' => true, 'deleted' => $stmt->rowCount()]);
