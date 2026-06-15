<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../cron/lib/broadcast_helpers.php'; // pulls in email_marketing + env_helper

// Auth: admin session only (matches admin/reviews/index.php pattern)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = 'Marketing';
$page_description = 'Compose and send a broadcast to your opt-in lists. Sends are queued and delivered by the marketing_broadcast cron.';

$flash = null;
$flash_type = null;
$audiences = broadcast_audiences();

// ─── POST handlers ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $flash = 'Session expired or invalid request token. Please reload and try again.';
        $flash_type = 'error';
    } else {
        $action   = $_POST['action'] ?? '';
        $audience = $_POST['audience'] ?? '';
        $subject  = trim($_POST['subject'] ?? '');
        $body     = trim($_POST['html_body'] ?? '');

        $validAudience = isset($audiences[$audience]);

        if ($action === 'send_test') {
            $testEmail = trim($_POST['test_email'] ?? '');
            if (!$validAudience) {
                $flash = 'Pick a valid audience first.';
                $flash_type = 'error';
            } elseif (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                $flash = 'Enter a valid test email address.';
                $flash_type = 'error';
            } elseif ($subject === '' || $body === '') {
                $flash = 'Subject and body are both required to send a test.';
                $flash_type = 'error';
            } else {
                // A test send bypasses the gate (it goes only to the admin's own
                // address) and uses a placeholder unsubscribe link.
                $ok = send_broadcast_email($testEmail, '[TEST] ' . $subject, $body, site_url('/unsubscribe/marketing.php'));
                $flash = $ok ? "Test email sent to {$testEmail}." : 'Test email failed to send, check the error log.';
                $flash_type = $ok ? 'success' : 'error';
            }
        } elseif ($action === 'queue_broadcast') {
            if (!$validAudience) {
                $flash = 'Pick a valid audience.';
                $flash_type = 'error';
            } elseif ($subject === '' || $body === '') {
                $flash = 'Subject and body are both required.';
                $flash_type = 'error';
            } else {
                $emails = broadcast_audience_emails($pdo, $audience);
                if (!$emails) {
                    $flash = 'That audience has no deliverable recipients right now (everyone is unsubscribed, unconfirmed, or suppressed).';
                    $flash_type = 'error';
                } else {
                    try {
                        $pdo->beginTransaction();

                        $ins = $pdo->prepare(
                            "INSERT INTO marketing_broadcasts (subject, html_body, audience, status, total_recipients, created_by)
                             VALUES (?, ?, ?, 'queued', ?, ?)"
                        );
                        $ins->execute([$subject, $body, $audience, count($emails), $_SESSION['admin_username'] ?? 'admin']);
                        $broadcastId = (int) $pdo->lastInsertId();

                        // Snapshot recipients in chunks.
                        $recStmt = $pdo->prepare(
                            'INSERT IGNORE INTO marketing_broadcast_recipients (broadcast_id, email, status) VALUES (?, ?, ' . "'pending')"
                        );
                        foreach ($emails as $em) {
                            $recStmt->execute([$broadcastId, $em]);
                        }

                        $pdo->commit();
                        $flash = 'Queued a broadcast to ' . count($emails) . ' recipient(s). The sender cron will deliver them in batches.';
                        $flash_type = 'success';
                    } catch (Throwable $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        error_log('queue_broadcast failed: ' . $e->getMessage());
                        $flash = 'Could not queue the broadcast, check the error log.';
                        $flash_type = 'error';
                    }
                }
            }
        } elseif ($action === 'cancel_broadcast') {
            $bid = (int) ($_POST['broadcast_id'] ?? 0);
            if ($bid > 0) {
                // Only stop ones that haven't finished; leaves already-sent rows intact.
                $upd = $pdo->prepare("UPDATE marketing_broadcasts SET status = 'canceled' WHERE id = ? AND status IN ('queued','sending')");
                $upd->execute([$bid]);
                $pdo->prepare("UPDATE marketing_broadcast_recipients SET status = 'skipped', error = 'broadcast canceled' WHERE broadcast_id = ? AND status = 'pending'")->execute([$bid]);
                $flash = $upd->rowCount() ? 'Broadcast canceled. Pending recipients will not be emailed.' : 'That broadcast could not be canceled (already finished?).';
                $flash_type = $upd->rowCount() ? 'success' : 'error';
            }
        }
    }
}

// ─── Data for rendering ─────────────────────────────────────────────────────
// Newsletter subscriber breakdown.
$subCounts = ['pending' => 0, 'confirmed' => 0, 'unsubscribed' => 0];
$rows = $pdo->query("SELECT status, COUNT(*) c FROM marketing_subscribers WHERE context = 'newsletter' GROUP BY status")->fetchAll();
foreach ($rows as $r) {
    $subCounts[$r['status']] = (int) $r['c'];
}

// Deliverable count per audience (drives the select labels + queue confirm).
$audienceCounts = [];
foreach (array_keys($audiences) as $key) {
    $audienceCounts[$key] = count(broadcast_audience_emails($pdo, $key));
}

// Recent broadcasts.
$recent = $pdo->query(
    "SELECT id, subject, audience, status, total_recipients, sent_count, failed_count, skipped_count, created_at
     FROM marketing_broadcasts ORDER BY id DESC LIMIT 20"
)->fetchAll();

include __DIR__ . '/../admin_header.php';
?>

<style>
.mkt-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:24px}
.mkt-section-head{text-align:center}
.mkt-compose{display:flex;flex-direction:column;gap:14px;max-width:760px;margin:0 auto}
.mkt-field label{display:block;font-weight:600;margin-bottom:6px;font-size:14px}
.mkt-field input[type=text],.mkt-field input[type=email],.mkt-field select,.mkt-field textarea{
  width:100%;padding:10px 12px;border:1px solid var(--border-color,#d8e2f0);border-radius:8px;
  font:500 14px/1.5 inherit;background:var(--input-bg,#fff);color:inherit;box-sizing:border-box}
.mkt-field textarea{min-height:240px;font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px}
.mkt-hint{font-size:12.5px;color:var(--text-muted,#6b7280);margin-top:5px}
.mkt-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.mkt-test{display:flex;gap:8px;flex:1;min-width:240px}
.mkt-test input{flex:1}
.mkt-badge{display:inline-block;padding:2px 9px;border-radius:20px;font-size:12px;font-weight:600}
.mkt-badge.queued{background:#fef3c7;color:#92400e}
.mkt-badge.sending{background:#dbeafe;color:#1e40af}
.mkt-badge.sent{background:#dcfce7;color:#166534}
.mkt-badge.canceled{background:#f3f4f6;color:#6b7280}

/* Dark theme. The base input rule hardcodes a light background and the shared
   .btn-gray / .mkt-hint colors have no dark variant, so override them here to
   match the global dark input + label styling in common-style.css. */
[data-theme="dark"] .mkt-field input[type=text],
[data-theme="dark"] .mkt-field input[type=email],
[data-theme="dark"] .mkt-field select,
[data-theme="dark"] .mkt-field textarea{
  background:var(--gray-700);border-color:var(--black);color:var(--gray-200)}
[data-theme="dark"] .mkt-field input::placeholder,
[data-theme="dark"] .mkt-field textarea::placeholder{color:var(--gray-500)}
[data-theme="dark"] .mkt-hint{color:var(--gray-400)}
[data-theme="dark"] .btn-gray{background-color:var(--gray-700);color:var(--gray-200);border-color:var(--black)}
[data-theme="dark"] .btn-gray:hover{background-color:var(--gray-600);color:var(--gray-100)}
</style>

<?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash_type) ?>"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="mkt-grid">
    <div class="stat-card">
        <div class="stat-label">Confirmed subscribers</div>
        <div class="stat-value"><?= number_format($subCounts['confirmed']) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending confirmation</div>
        <div class="stat-value"><?= number_format($subCounts['pending']) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Unsubscribed</div>
        <div class="stat-value"><?= number_format($subCounts['unsubscribed']) ?></div>
    </div>
</div>

<h2 class="mkt-section-head">Compose a broadcast</h2>
<p class="mkt-hint" style="margin:0 auto 16px;max-width:760px;text-align:center">
    The body accepts HTML and is wrapped in the standard Argo email template. An unsubscribe
    footer is appended automatically. Always send yourself a test first. Queuing hands the send
    to the cron, which delivers in batches and skips anyone who has since unsubscribed.
</p>

<form method="post" class="mkt-compose" id="broadcast-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <div class="mkt-field">
        <label for="audience">Audience</label>
        <select name="audience" id="audience" required>
            <?php foreach ($audiences as $key => $label): ?>
                <option value="<?= htmlspecialchars($key) ?>" data-count="<?= (int) $audienceCounts[$key] ?>">
                    <?= htmlspecialchars($label) ?> — <?= (int) $audienceCounts[$key] ?> deliverable
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mkt-field">
        <label for="subject">Subject</label>
        <input type="text" name="subject" id="subject" maxlength="300" required
               value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
    </div>

    <div class="mkt-field">
        <label for="html_body">Body (HTML)</label>
        <textarea name="html_body" id="html_body" required placeholder="<p>Hi there,</p>&#10;<p>Here's what's new in Argo Books this month…</p>"><?= htmlspecialchars($_POST['html_body'] ?? '') ?></textarea>
        <div class="mkt-hint">Tip: keep it simple. Inline styles render most reliably across email clients.</div>
    </div>

    <div class="mkt-actions">
        <div class="mkt-test">
            <input type="email" name="test_email" placeholder="you@argorobots.com (send a test)">
            <button type="submit" name="action" value="send_test" class="btn btn-gray">Send test</button>
        </div>
        <button type="submit" name="action" value="queue_broadcast" class="btn btn-blue" id="queue-btn">Queue broadcast</button>
    </div>
</form>

<h2 class="mkt-section-head" style="margin-top:36px">Recent broadcasts</h2>
<?php if (!$recent): ?>
    <p class="empty-state">No broadcasts yet.</p>
<?php else: ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Audience</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['subject']) ?></td>
                        <td><?= htmlspecialchars($audiences[$b['audience']] ?? $b['audience']) ?></td>
                        <td><span class="mkt-badge <?= htmlspecialchars($b['status']) ?>"><?= htmlspecialchars($b['status']) ?></span></td>
                        <td>
                            <?= (int) $b['sent_count'] ?>/<?= (int) $b['total_recipients'] ?> sent
                            <?php if ((int) $b['failed_count'] > 0): ?>
                                · <?= (int) $b['failed_count'] ?> failed
                            <?php endif; ?>
                            <?php if ((int) $b['skipped_count'] > 0): ?>
                                · <?= (int) $b['skipped_count'] ?> skipped
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($b['created_at']))) ?></td>
                        <td>
                            <?php if (in_array($b['status'], ['queued', 'sending'], true)): ?>
                                <form method="post" style="display:inline" onsubmit="return confirm('Cancel this broadcast? Recipients not yet emailed will be skipped.');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="action" value="cancel_broadcast">
                                    <input type="hidden" name="broadcast_id" value="<?= (int) $b['id'] ?>">
                                    <button type="submit" class="btn btn-small btn-red">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
    document.getElementById('broadcast-form').addEventListener('submit', function (e) {
        // Only confirm on the queue action, not the test send.
        if (e.submitter && e.submitter.value === 'queue_broadcast') {
            var sel = document.getElementById('audience');
            var count = sel.options[sel.selectedIndex].getAttribute('data-count') || '0';
            if (count === '0') {
                e.preventDefault();
                alert('That audience has no deliverable recipients right now.');
                return;
            }
            if (!confirm('Queue this broadcast to ' + count + ' recipient(s)? This cannot be undone once the cron starts sending.')) {
                e.preventDefault();
            }
        }
    });
</script>
