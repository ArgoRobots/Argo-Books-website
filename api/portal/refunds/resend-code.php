<?php
declare(strict_types=1);

/**
 * POST /api/portal/refunds/resend-code.php
 * Body: { request_id }
 *
 * Re-sends the 6-digit code for a refund request still in pending_code.
 * Limits: max 3 codes per request, no more often than once per 60s.
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
refund_assert_state($req['state'], ['pending_code'], 'resend code');

$stmt = $pdo->prepare("SELECT COUNT(*) AS c, MAX(created_at) AS latest FROM refund_email_codes WHERE refund_request_id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ((int)$row['c'] >= 3) {
    send_error_response(429, 'Maximum resends reached for this request.', 'MAX_RESENDS_REACHED');
}
if ($row['latest'] && (time() - strtotime($row['latest'])) < 60) {
    send_error_response(429, 'Please wait at least 60 seconds between resends.', 'TOO_SOON');
}

$pdo->prepare("UPDATE refund_email_codes SET consumed_at = COALESCE(consumed_at, NOW()) WHERE refund_request_id = ?")
    ->execute([$id]);

$code = refund_generate_code();
$hash = refund_hash_code($code, (string)$id);
$pdo->prepare("INSERT INTO refund_email_codes (refund_request_id, code_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))")
    ->execute([$id, $hash]);

audit_log($pdo, (int)$company['id'], 'code_sent', 'owner', null, $id, null, ['resend' => true]);
refund_email_send_code($company['owner_email'], $code, $req['invoice_number'], (int)$req['amount_cents'], $req['currency']);

send_json_response(200, [
    'success' => true,
    'maskedEmail' => refund_mask_email($company['owner_email']),
]);
