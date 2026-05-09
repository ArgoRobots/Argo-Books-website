<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/email-change/confirm-old.php
 * Body: { change_id, code }
 *
 * Verifies the OLD-email code. Transitions state pending → old_verified.
 * Immediately issues a NEW-email code so the desktop can move to step 4.
 */

require_once __DIR__ . '/../../portal-helper.php';
require_once __DIR__ . '/../../_audit.php';
require_once __DIR__ . '/../../_refund_helpers.php';

set_portal_headers();
require_method(['POST']);

$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$body = json_decode(file_get_contents('php://input') ?: '', true) ?? [];
$change_id = (int)($body['change_id'] ?? 0);
$code = (string)($body['code'] ?? '');
if ($change_id <= 0 || !preg_match('/^\d{6}$/', $code)) {
    send_error_response(400, 'Invalid input.', 'INVALID_INPUT');
}

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM email_change_requests WHERE id = ? AND company_id = ?");
$stmt->execute([$change_id, $company['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    send_error_response(404, 'Change request not found.', 'NOT_FOUND');
}
if ($row['state'] !== 'pending') {
    send_error_response(409, 'Change request is in state ' . $row['state'], 'WRONG_STATE');
}

$expected = refund_hash_code($code, 'echange-old-' . $change_id);
if (!hash_equals($row['old_email_code_hash'], $expected)) {
    audit_log($pdo, (int)$company['id'], 'code_failed', 'owner', null, null, $change_id, ['target' => 'old']);
    send_error_response(401, 'Wrong code.', 'WRONG_CODE');
}

$pdo->beginTransaction();
$pdo->prepare("UPDATE email_change_requests SET state='old_verified', old_email_verified_at = NOW() WHERE id = ?")
    ->execute([$change_id]);

// Issue NEW-email code
$new_code = refund_generate_code();
$new_hash = refund_hash_code($new_code, 'echange-new-' . $change_id);
$pdo->prepare("UPDATE email_change_requests SET new_email_code_hash = ? WHERE id = ?")
    ->execute([$new_hash, $change_id]);

audit_log($pdo, (int)$company['id'], 'email_change_old_verified', 'owner', null, null, $change_id, []);
audit_log($pdo, (int)$company['id'], 'code_sent', 'system', null, null, $change_id, ['target' => 'new']);
$pdo->commit();

refund_email_send_change_new_code($row['new_email'], $new_code);

send_json_response(200, [
    'success' => true,
    'state' => 'old_verified',
    'maskedNewEmail' => refund_mask_email($row['new_email']),
]);
