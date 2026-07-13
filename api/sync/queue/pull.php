<?php
/**
 * POST /api/sync/queue/pull - desktop fetches pending scanned transactions for a company.
 * Auth: desktop owner. Body: { "company_uid": "..." }
 * Returns: { success, items: [ { id, ciphertext } ] }
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
if ($companyUid === '' || strlen($companyUid) > 64) {
    send_error_response(400, 'company_uid (max 64 chars) is required.', 'INVALID_COMPANY');
}

global $pdo;
$stmt = $pdo->prepare(
    'SELECT id, ciphertext FROM mobile_sync_queue WHERE company_uid = ? AND owner_identity_hash = ? ORDER BY id ASC LIMIT 200'
);
$stmt->execute([$companyUid, $owner]);
send_json_response(200, ['success' => true, 'items' => $stmt->fetchAll()]);
