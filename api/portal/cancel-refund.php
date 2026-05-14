<?php
declare(strict_types=1);

/**
 * Public token-authenticated landing page for the "Cancel this refund" link
 * in the cooling-off email. The token is single-use and only valid while the
 * refund is still in pending_code / code_verified / cooling_off.
 *
 * Bound to: GET/POST /api/portal/cancel-refund.php?token=...
 */

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/_audit.php';

global $pdo;
$token = (string)($_GET['token'] ?? $_POST['token'] ?? '');

if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    http_response_code(400);
    echo cancel_refund_layout('Invalid link', '<p>This cancellation link is malformed or expired.</p>');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM refund_requests WHERE cancel_token = ?");
$stmt->execute([$token]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    http_response_code(404);
    echo cancel_refund_layout('Not found', '<p>This refund request was not found. The link may have already been used or expired.</p>');
    exit;
}

if (!in_array($req['state'], ['pending_code','code_verified','cooling_off'], true)) {
    echo cancel_refund_layout('Already finalized',
        '<p>This refund can no longer be cancelled. Current state: <code>' . htmlspecialchars($req['state']) . '</code></p>');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // State-guarded UPDATE: the cooling-off promoter cron can transition this
    // row to 'processing' or 'completed' between the SELECT above and this
    // UPDATE. Without the state predicate, this clobbers a finalized refund
    // back to 'cancelled' and the books diverge from the provider.
    $upd = $pdo->prepare("
        UPDATE refund_requests
        SET state='cancelled', state_reason='cancelled_by_email_link', cancel_token = NULL, updated_at = NOW()
        WHERE id = ? AND state IN ('pending_code','code_verified','cooling_off')
    ");
    $upd->execute([$req['id']]);
    if ($upd->rowCount() === 0) {
        http_response_code(409);
        echo cancel_refund_layout('Already finalized',
            '<p>This refund was finalized before the cancellation could complete. The page may have been left open while the cooling-off window expired.</p>');
        exit;
    }
    audit_log(
        $pdo, (int)$req['company_id'], 'cancelled_by_email_link', 'owner', null,
        (int)$req['id'], null,
        ['ip' => $_SERVER['REMOTE_ADDR'] ?? null]
    );

    $amount = number_format($req['amount_cents'] / 100, 2) . ' ' . htmlspecialchars($req['currency']);
    $invoice = htmlspecialchars($req['invoice_number']);
    echo cancel_refund_layout('Refund cancelled',
        "<p>The refund of <strong>$amount</strong> on invoice <strong>$invoice</strong> has been cancelled.</p>"
        . "<p>No money has moved.</p>");
    exit;
}

// GET: show confirmation
$amount = number_format($req['amount_cents'] / 100, 2) . ' ' . htmlspecialchars($req['currency']);
$invoice = htmlspecialchars($req['invoice_number']);
echo cancel_refund_layout('Cancel this refund?',
    "<p>You're about to cancel a refund of <strong>$amount</strong> on invoice <strong>$invoice</strong>.</p>"
    . "<form method='post' style='margin-top:1.5rem;'>"
    . "<input type='hidden' name='token' value='" . htmlspecialchars($token) . "'>"
    . "<button type='submit' style='background:#dc2626;color:#fff;padding:.7rem 1.4rem;border:none;border-radius:6px;font-size:1rem;cursor:pointer;'>Cancel this refund</button>"
    . "</form>");

function cancel_refund_layout(string $title, string $body): string {
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
                code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <h2>$titleSafe</h2>
            $body
        </body>
        </html>
HTML;
}
