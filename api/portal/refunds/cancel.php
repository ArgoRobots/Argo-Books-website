<?php
declare(strict_types=1);

/**
 * POST /api/portal/refunds/cancel.php
 * Body: { request_id }
 *
 * User-initiated cancel from the desktop's Back button.
 * Allowed only in pending_code, code_verified, cooling_off.
 */

require_once __DIR__ . '/../portal-helper.php';
require_once __DIR__ . '/../_audit.php';
require_once __DIR__ . '/../_refund_helpers.php';

set_portal_headers();
require_method(['POST']);

$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$body = json_decode(file_get_contents('php://input') ?: '', true) ?? [];
$id = (int)($body['request_id'] ?? 0);
if ($id <= 0) {
    send_error_response(400, 'Missing request_id.', 'MISSING_ID');
}

global $pdo;
$req = refund_load_request($pdo, (int)$company['id'], $id);
refund_assert_state($req['state'], ['pending_code', 'code_verified', 'cooling_off'], 'cancel');

// State-guarded UPDATE. The state check above and this UPDATE form a
// classic TOCTOU window: the cooling-off promoter cron can promote the
// request between them. Without the predicate, this UPDATE would clobber
// a finalized refund back to 'cancelled' and the books would diverge
// from the provider. rowCount = 0 means the cron beat us — surface as
// 409 so the desktop can re-fetch the current state.
$upd = $pdo->prepare("
    UPDATE refund_requests
    SET state='cancelled', state_reason='cancelled_by_user', cancel_token = NULL, updated_at=NOW()
    WHERE id = ? AND state IN ('pending_code','code_verified','cooling_off')
");
$upd->execute([$id]);
if ($upd->rowCount() === 0) {
    send_error_response(409, 'Refund was finalized before the cancellation could complete.', 'STATE_CONFLICT');
}
audit_log($pdo, (int)$company['id'], 'cancelled_by_user', 'owner', null, $id, null, []);

send_json_response(200, ['success' => true, 'state' => 'cancelled']);
