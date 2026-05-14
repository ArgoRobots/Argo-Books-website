<?php
declare(strict_types=1);

/**
 * Public token-authenticated landing page for the "revert" link in the
 * email-change-completed notification sent to the OLD address.
 *
 * Bound to: GET/POST /api/portal/revert-email.php?token=...
 */

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/_audit.php';
require_once __DIR__ . '/../../email_sender.php';

global $pdo;
$token = (string)($_GET['token'] ?? $_POST['token'] ?? '');

if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    http_response_code(400);
    echo revert_layout('Invalid link', '<p>This revert link is malformed or expired.</p>');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM email_change_requests WHERE cancel_token = ? AND state = 'completed'");
$stmt->execute([$token]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo revert_layout('Not found', '<p>This revert link is no longer valid (it may have been used or the 30-day window passed).</p>');
    exit;
}

if ($row['revert_until'] && strtotime($row['revert_until']) < time()) {
    http_response_code(410);
    echo revert_layout('Window expired', '<p>The 30-day revert window has expired.</p>');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    // Stale-token guard: only revert if the company is currently on the
    // new_email from THIS change request. Without this predicate, a 30-day-old
    // revert link can silently undo a later legitimate change
    // (A->B then B->C; the old A->B link stomps owner back to A).
    $upd = $pdo->prepare(
        "UPDATE portal_companies SET owner_email = ?
         WHERE id = ? AND owner_email = ?"
    );
    $upd->execute([$row['old_email'], $row['company_id'], $row['new_email']]);
    if ($upd->rowCount() !== 1) {
        $pdo->rollBack();
        http_response_code(409);
        echo revert_layout(
            'Cannot revert',
            '<p>This email change has been superseded by a newer one. The portal owner email is no longer ' . htmlspecialchars($row['new_email']) . ', so this revert link can no longer be used.</p>'
        );
        exit;
    }
    $pdo->prepare("UPDATE email_change_requests SET state='reverted', reverted_at = NOW(), cancel_token = NULL WHERE id = ?")
        ->execute([$row['id']]);
    audit_log($pdo, (int)$row['company_id'], 'email_reverted', 'owner', null, null, (int)$row['id'], [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
    $pdo->commit();

    // Notify both addresses
    $old_safe = htmlspecialchars($row['old_email']);
    $new_safe = htmlspecialchars($row['new_email']);
    send_styled_email($row['old_email'], 'Email change reverted',
        "<p>Your Argo Books portal email has been restored to <strong>$old_safe</strong>.</p>", 'blue');
    send_styled_email($row['new_email'], 'Email change reverted',
        "<p>The change to <strong>$new_safe</strong> was reverted by the original owner. The portal owner email is now back to the previous value.</p>", 'purple');

    echo revert_layout('Email reverted',
        "<p>Your portal owner email has been restored to <strong>$old_safe</strong>.</p>"
        . "<p>Both addresses have been notified.</p>");
    exit;
}

// GET: show confirmation
$old_safe = htmlspecialchars($row['old_email']);
$new_safe = htmlspecialchars($row['new_email']);
echo revert_layout('Revert email change?',
    "<p>Your portal owner email was changed to <strong>$new_safe</strong>.</p>"
    . "<p>Click below to revert it back to <strong>$old_safe</strong>.</p>"
    . "<form method='post' style='margin-top:1.5rem;'>"
    . "<input type='hidden' name='token' value='" . htmlspecialchars($token) . "'>"
    . "<button type='submit' style='background:#dc2626;color:#fff;padding:.7rem 1.4rem;border:none;border-radius:6px;font-size:1rem;cursor:pointer;'>Revert email</button>"
    . "</form>");

function revert_layout(string $title, string $body): string {
    $titleSafe = htmlspecialchars($title);
    return <<<HTML
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>$titleSafe — Argo Books</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif; max-width: 540px; margin: 4rem auto; padding: 2rem; color: #111; }
                h2 { margin-top: 0; }
            </style>
        </head>
        <body>
            <h2>$titleSafe</h2>
            $body
        </body>
        </html>
HTML;
}
