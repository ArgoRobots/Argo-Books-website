<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/email-change/confirm-new.php
 * Body: { change_id, code }
 *
 * Verifies the NEW-email code. Transitions state old_verified → completed,
 * flips portal_companies.owner_email to the new address, and emails the
 * OLD address with a 30-day revert link.
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
if ($row['state'] !== 'old_verified') {
    send_error_response(409, 'Change request is in state ' . $row['state'], 'WRONG_STATE');
}

$expected = refund_hash_code($code, 'echange-new-' . $change_id);
if (!hash_equals($row['new_email_code_hash'], $expected)) {
    audit_log($pdo, (int)$company['id'], 'code_failed', 'owner', null, null, $change_id, ['target' => 'new']);
    send_error_response(401, 'Wrong code.', 'WRONG_CODE');
}

$revert_token = bin2hex(random_bytes(32));

$pdo->beginTransaction();
$pdo->prepare("
    UPDATE email_change_requests
    SET state='completed', new_email_verified_at = NOW(), completed_at = NOW(),
        cancel_token = ?, revert_until = DATE_ADD(NOW(), INTERVAL 30 DAY)
    WHERE id = ?
")->execute([$revert_token, $change_id]);
$pdo->prepare("UPDATE portal_companies SET owner_email = ? WHERE id = ?")
    ->execute([$row['new_email'], $company['id']]);

audit_log($pdo, (int)$company['id'], 'email_change_new_verified', 'owner', null, null, $change_id, []);
audit_log($pdo, (int)$company['id'], 'email_changed', 'owner', null, null, $change_id, [
    'old' => $row['old_email'],
    'new' => $row['new_email'],
]);
$pdo->commit();

refund_email_send_change_completed_to_old($row['old_email'], $row['new_email'], $revert_token);

send_json_response(200, [
    'success' => true,
    'state' => 'completed',
    'newEmail' => $row['new_email'],
]);
