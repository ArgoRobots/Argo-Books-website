<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/verify-email/confirm.php
 * Body: { code }
 *
 * Confirms the registration verification code. Until this succeeds, the
 * company's email_verified_at is NULL and refund endpoints return 412.
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
$code = (string)($body['code'] ?? '');
if (!preg_match('/^\d{6}$/', $code)) {
    send_error_response(400, 'Invalid code format.', 'INVALID_CODE_FORMAT');
}

global $pdo;

$stmt = $pdo->prepare("
    SELECT * FROM email_verifications
    WHERE company_id = ? AND purpose = 'registration' AND consumed_at IS NULL
    ORDER BY id DESC LIMIT 1
");
$stmt->execute([$company['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    send_error_response(409, 'No active verification code. Request a new one.', 'NO_ACTIVE_CODE');
}
if (strtotime($row['expires_at']) < time()) {
    send_error_response(410, 'Verification code expired.', 'EXPIRED');
}
if ((int)$row['attempts'] >= 5) {
    send_error_response(429, 'Too many attempts. Request a new code.', 'TOO_MANY_ATTEMPTS');
}

$expected = refund_hash_code($code, (string)$company['id']);
if (!hash_equals($row['code_hash'], $expected)) {
    $pdo->prepare("UPDATE email_verifications SET attempts = attempts + 1 WHERE id = ?")
        ->execute([$row['id']]);
    audit_log($pdo, (int)$company['id'], 'code_failed', 'owner', null, null, null, [
        'purpose' => 'registration',
    ]);
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'WRONG_CODE',
        'attemptsRemaining' => max(0, 5 - ((int)$row['attempts'] + 1)),
    ]);
    exit;
}

$pdo->beginTransaction();
$pdo->prepare("UPDATE email_verifications SET consumed_at = NOW() WHERE id = ?")->execute([$row['id']]);
$pdo->prepare("UPDATE portal_companies SET email_verified_at = NOW() WHERE id = ?")->execute([$company['id']]);
audit_log($pdo, (int)$company['id'], 'email_registration_verified', 'owner', null, null, null, []);
$pdo->commit();

send_json_response(200, [
    'success' => true,
    'verifiedAt' => date('c'),
]);
