<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/email-change/request.php
 * Body: { new_email, password_verified? }
 *
 * Begins a 4-step email change. Sends a verification code to the OLD email.
 * State: NEW row in 'pending'.
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
$new_email = filter_var(trim((string)($body['new_email'] ?? '')), FILTER_VALIDATE_EMAIL);
if (!$new_email) {
    send_error_response(400, 'Invalid email address.', 'INVALID_EMAIL');
}

global $pdo;

// Reject if already used by another company
$stmt = $pdo->prepare("SELECT id FROM portal_companies WHERE owner_email = ? AND id != ?");
$stmt->execute([$new_email, $company['id']]);
if ($stmt->fetch()) {
    send_error_response(409, 'That email is already used by another portal account.', 'EMAIL_IN_USE');
}

// 24h cooldown between completed changes
$stmt = $pdo->prepare("SELECT MAX(completed_at) FROM email_change_requests WHERE company_id = ? AND state = 'completed'");
$stmt->execute([$company['id']]);
$last = $stmt->fetchColumn();
if ($last && (time() - strtotime($last)) < 86400) {
    $retry = 86400 - (time() - strtotime($last));
    send_error_response(429, 'Email was changed less than 24h ago. Try again later.', 'COOLDOWN_ACTIVE');
}

// Cancel any other pending change for this company
$pdo->prepare("UPDATE email_change_requests SET state='cancelled' WHERE company_id = ? AND state IN ('pending','old_verified')")
    ->execute([$company['id']]);

$password_verified = !empty($body['password_verified']) ? 1 : 0;

$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO email_change_requests (company_id, old_email, new_email, password_verified) VALUES (?, ?, ?, ?)");
$stmt->execute([$company['id'], $company['owner_email'], $new_email, $password_verified]);
$change_id = (int)$pdo->lastInsertId();

$code = refund_generate_code();
$hash = refund_hash_code($code, 'echange-old-' . $change_id);
$pdo->prepare("UPDATE email_change_requests SET old_email_code_hash = ? WHERE id = ?")->execute([$hash, $change_id]);

audit_log($pdo, (int)$company['id'], 'email_change_requested', 'owner', null, null, $change_id, [
    'new_email' => $new_email,
    'password_verified' => (bool)$password_verified,
]);
audit_log($pdo, (int)$company['id'], 'code_sent', 'system', null, null, $change_id, ['target' => 'old']);
$pdo->commit();

refund_email_send_change_old_code($company['owner_email'], $code, $new_email);

send_json_response(200, [
    'success' => true,
    'changeId' => $change_id,
    'state' => 'pending',
    'maskedOldEmail' => refund_mask_email($company['owner_email']),
]);
