<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/verify-email/request.php
 *
 * Re-sends the registration verification code to the company's owner_email.
 * Limits: max 3 codes per company, no more often than once per 60s.
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

if (!empty($company['email_verified_at'])) {
    send_json_response(200, ['success' => true, 'message' => 'already_verified']);
}

global $pdo;

$stmt = $pdo->prepare("
    SELECT COUNT(*) AS c, MAX(created_at) AS latest
    FROM email_verifications
    WHERE company_id = ? AND purpose = 'registration'
");
$stmt->execute([$company['id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ((int)$row['c'] >= 3) {
    send_error_response(429, 'Maximum verification attempts reached. Contact support.', 'MAX_RESENDS');
}
if ($row['latest'] && (time() - strtotime($row['latest'])) < 60) {
    send_error_response(429, 'Please wait at least 60 seconds between resends.', 'TOO_SOON');
}

// Invalidate any prior unconsumed codes
$pdo->prepare("UPDATE email_verifications SET consumed_at = COALESCE(consumed_at, NOW()) WHERE company_id = ? AND purpose = 'registration' AND consumed_at IS NULL")
    ->execute([$company['id']]);

$code = refund_generate_code();
$hash = refund_hash_code($code, (string)$company['id']);
$pdo->prepare("INSERT INTO email_verifications (company_id, email, purpose, code_hash, expires_at) VALUES (?, ?, 'registration', ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))")
    ->execute([$company['id'], $company['owner_email'], $hash]);

audit_log($pdo, (int)$company['id'], 'code_sent', 'owner', null, null, null, [
    'purpose' => 'registration',
    'resend' => true,
]);
refund_email_send_registration_code($company['owner_email'], $code);

send_json_response(200, ['success' => true, 'maskedEmail' => refund_mask_email($company['owner_email'])]);
