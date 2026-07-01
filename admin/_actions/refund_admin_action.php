<?php
declare(strict_types=1);

/**
 * Admin actions for refund requests.
 * POST body: { action: 'cancel' | 'force_fail', request_id, reason? }
 *
 * Bound to: POST /admin/_actions/refund_admin_action.php
 * Session-authenticated (admin_logged_in).
 */

require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../api/portal/_audit.php';

if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// CSRF protection (token is generated on /admin/payments/index.php load)
if (empty($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], (string)$_POST['csrf_token'])) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

$action = $_POST['action'] ?? '';
$request_id = (int)($_POST['request_id'] ?? 0);
$reason = trim((string)($_POST['reason'] ?? ''));
$admin_id = (string)($_SESSION['admin_user_id'] ?? $_SESSION['admin_username'] ?? 'unknown_admin');

if ($request_id <= 0 || !in_array($action, ['cancel','force_fail'], true)) {
    http_response_code(400);
    exit('Bad request');
}

global $pdo;
$stmt = $pdo->prepare("SELECT id, company_id, state FROM refund_requests WHERE id = ?");
$stmt->execute([$request_id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$r) {
    header('Location: /admin/payments/index.php?msg=not_found');
    exit;
}

if ($action === 'cancel') {
    if (!in_array($r['state'], ['pending_code','code_verified','cooling_off'], true)) {
        header('Location: /admin/payments/index.php?msg=cannot_cancel');
        exit;
    }
    // State-guarded UPDATE: the cooling-off promoter cron can flip this row
    // to 'processing' (and fire the gateway) between the SELECT above and
    // this UPDATE. Without the predicate, the admin's cancel would clobber
    // an already-finalized refund.
    $upd = $pdo->prepare("
        UPDATE refund_requests
        SET state='cancelled', state_reason = ?, cancel_token = NULL, updated_at = NOW()
        WHERE id = ? AND state IN ('pending_code','code_verified','cooling_off')
    ");
    $upd->execute(['admin_cancelled' . ($reason ? ': ' . $reason : ''), $request_id]);
    if ($upd->rowCount() === 0) {
        header('Location: /admin/payments/index.php?msg=state_conflict');
        exit;
    }
    audit_log($pdo, (int)$r['company_id'], 'cancelled_by_admin', 'admin', $admin_id, $request_id, null, [
        'reason' => $reason ?: 'admin_cancelled',
    ]);
    header('Location: /admin/payments/index.php?msg=cancelled');
    exit;
}

if ($action === 'force_fail') {
    if ($r['state'] !== 'processing') {
        header('Location: /admin/payments/index.php?msg=invalid_state');
        exit;
    }
    // Same state-guard pattern as the cancel branch above. cancel_token = NULL
    // closes a narrow race: the cooling-off promoter cron can flip cooling_off
    // → processing between our SELECT and this UPDATE, carrying the token
    // forward. Clearing it here keeps the public cancel link from leaking
    // terminal state after force_fail.
    $upd = $pdo->prepare("
        UPDATE refund_requests
        SET state='failed', state_reason = ?, cancel_token = NULL, updated_at = NOW()
        WHERE id = ? AND state = 'processing'
    ");
    $upd->execute(['admin_force_failed' . ($reason ? ': ' . $reason : ''), $request_id]);
    if ($upd->rowCount() === 0) {
        header('Location: /admin/payments/index.php?msg=state_conflict');
        exit;
    }
    audit_log($pdo, (int)$r['company_id'], 'failed', 'admin', $admin_id, $request_id, null, [
        'reason' => $reason ?: 'admin_force_failed',
    ]);
    header('Location: /admin/payments/index.php?msg=force_failed');
    exit;
}
