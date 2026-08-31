<?php
declare(strict_types=1);

/**
 * POST /api/developer/keys/revoke
 *
 * Immediately stops a key working. Revocation is a stamp rather than a delete so
 * the key's history (its label, when it was last used) survives for the merchant
 * to look at afterwards, which is exactly what someone wants when they are
 * revoking a key because something looked wrong.
 *
 * Body: { "company_uid": "...", "key_id": "key_..." }
 */

require_once __DIR__ . '/../sync/sync-helper.php';

set_portal_headers();
require_method(['POST']);

$owner = resolve_owner_identity();
if (!$owner) {
    send_error_response(401, 'Unauthorized.', 'UNAUTHORIZED');
}

$body = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($body)) {
    send_error_response(400, 'Invalid JSON body.', 'INVALID_JSON');
}

$companyUid = trim((string) ($body['company_uid'] ?? ''));
$keyId = trim((string) ($body['key_id'] ?? ''));

if ($companyUid === '' || $keyId === '') {
    send_error_response(400, 'company_uid and key_id are required.', 'INVALID_INPUT');
}

global $pdo;

// The join through api_accounts is what stops one owner revoking another
// owner's key by guessing an id.
$stmt = $pdo->prepare(
    'UPDATE api_keys k
       JOIN api_accounts a ON a.id = k.account_id
        SET k.revoked_at = NOW()
      WHERE k.public_id = ?
        AND k.revoked_at IS NULL
        AND a.owner_identity_hash = ?
        AND a.company_uid = ?
       '
);
$stmt->execute([$keyId, $owner, $companyUid]);

if ($stmt->rowCount() === 0) {
    // Already revoked and never existed are reported the same way on purpose:
    // distinguishing them would let a caller probe for valid key ids.
    send_error_response(404, 'No active key with that id on this company.', 'KEY_NOT_FOUND');
}

send_json_response(200, ['success' => true, 'key_id' => $keyId, 'revoked' => true]);
