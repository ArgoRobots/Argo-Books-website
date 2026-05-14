<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/set-initial-email.php
 * Body: { email }
 *
 * Sets owner_email for a portal company that currently has none — the
 * "complete registration" path for accounts that registered with an empty
 * email. Stores owner_email and emails a 6-digit verification code to it.
 * email_verified_at is NOT set here — the caller must enter the code via
 * /verify-email/confirm.php before refunds become available.
 *
 * Why this design: a typo in the typed email (or a malicious attacker
 * setting an email they don't actually own) shouldn't unlock refunds.
 * Requiring the code prevents both. The matching VerifyEmailModal on the
 * desktop pops automatically after this endpoint succeeds.
 *
 * Refuses (409) if owner_email is already set: in that case, the caller must
 * use the 4-step email-change flow which verifies both old and new addresses.
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
$email = filter_var(trim((string)($body['email'] ?? '')), FILTER_VALIDATE_EMAIL);
if (!$email) {
    send_error_response(400, 'Invalid email address.', 'INVALID_EMAIL');
}

global $pdo;

// Refuse if owner_email is already set — must go through Change flow.
// Include the current value so the client can reconcile its local state if
// the user is just trying to recover (e.g. local .argo lost the email).
if (!empty($company['owner_email'])) {
    http_response_code(409);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success'       => false,
        'errorCode'     => 'OWNER_EMAIL_ALREADY_SET',
        'message'       => 'Owner email is already set on this portal account. Use the Change flow to update it.',
        'ownerEmail'    => $company['owner_email'],
        'maskedEmail'   => refund_mask_email($company['owner_email']),
        'timestamp'     => date('c'),
    ]);
    exit;
}

// Reject if the chosen email is already used by another portal account.
$stmt = $pdo->prepare("SELECT id FROM portal_companies WHERE owner_email = ? AND id != ?");
$stmt->execute([$email, $company['id']]);
if ($stmt->fetch()) {
    send_error_response(409, 'That email is already used by another portal account.', 'EMAIL_IN_USE');
}

$pdo->beginTransaction();
try {
    // Set owner_email but DO NOT mark email_verified_at — the user has to
    // confirm the code we're about to email them first.
    $pdo->prepare("UPDATE portal_companies SET owner_email = ? WHERE id = ?")
        ->execute([$email, $company['id']]);

    // Issue verification code (purpose='registration' so the existing
    // /verify-email/confirm.php endpoint accepts it).
    $code = refund_generate_code();
    $hash = refund_hash_code($code, (string)$company['id']);
    $pdo->prepare("INSERT INTO email_verifications (company_id, email, purpose, code_hash, expires_at) VALUES (?, ?, 'registration', ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))")
        ->execute([$company['id'], $email, $hash]);

    audit_log($pdo, (int)$company['id'], 'email_changed', 'owner', null, null, null, [
        'reason' => 'set_initial_email',
        'old' => null,
        'new' => $email,
    ]);
    audit_log($pdo, (int)$company['id'], 'code_sent', 'system', null, null, null, [
        'purpose' => 'registration',
        'trigger' => 'set_initial_email',
    ]);

    $pdo->commit();
} catch (\Throwable $e) {
    $pdo->rollBack();
    error_log('set-initial-email DB error: ' . $e->getMessage());
    send_error_response(500, 'Could not set owner email.', 'SERVER_ERROR');
}

// Send the verification email AFTER commit (don't roll back if SMTP hiccups;
// user can resend via /verify-email/request.php).
refund_email_send_registration_code($email, $code);

send_json_response(200, [
    'success' => true,
    'ownerEmail' => $email,
    'maskedEmail' => refund_mask_email($email),
]);
