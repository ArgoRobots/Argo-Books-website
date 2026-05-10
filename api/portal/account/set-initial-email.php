<?php
declare(strict_types=1);

/**
 * POST /api/portal/account/set-initial-email.php
 * Body: { email }
 *
 * Sets owner_email for a portal company that currently has none — the
 * "complete registration" path for accounts that registered with an empty
 * email. Trusts the API-key-authenticated caller (same trust model as
 * initial portal registration). Marks email_verified_at = NOW() so refunds
 * become enabled immediately.
 *
 * Refuses (409) if owner_email is already set: in that case, the caller must
 * use the 4-step email-change flow which verifies both old and new addresses.
 *
 * Why no verification on the new email? At this point the company has no
 * trustworthy contact channel; requiring a code on the new address before it's
 * stored creates a chicken-and-egg problem. The same trust model applies at
 * registration time. After this endpoint runs, the locked-email rules kick in
 * and any future change requires the full 4-step verification.
 */

require_once __DIR__ . '/../../portal-helper.php';
require_once __DIR__ . '/../../_audit.php';

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
if (!empty($company['owner_email'])) {
    send_error_response(409,
        'Owner email is already set on this portal account. Use the Change flow to update it.',
        'OWNER_EMAIL_ALREADY_SET');
}

// Reject if the chosen email is already used by another portal account.
$stmt = $pdo->prepare("SELECT id FROM portal_companies WHERE owner_email = ? AND id != ?");
$stmt->execute([$email, $company['id']]);
if ($stmt->fetch()) {
    send_error_response(409, 'That email is already used by another portal account.', 'EMAIL_IN_USE');
}

// Set owner_email + grandfather as verified (same as registration's behaviour
// when an email is supplied at signup time).
$pdo->prepare("UPDATE portal_companies SET owner_email = ?, email_verified_at = NOW() WHERE id = ?")
    ->execute([$email, $company['id']]);

audit_log($pdo, (int)$company['id'], 'email_changed', 'owner', null, null, null, [
    'reason' => 'set_initial_email',
    'old' => null,
    'new' => $email,
]);

send_json_response(200, [
    'success' => true,
    'ownerEmail' => $email,
]);
