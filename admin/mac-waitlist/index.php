<?php
require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../email_sender.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Waitlist rows for the current environment, newest first, with the referral
 * link's display name when the source is a known link. $idFilter re-scopes a
 * bulk-send POST to rows that actually exist in this environment (defence in
 * depth, same pattern as admin/email-customers).
 */
function get_waitlist_rows(?array $idFilter = null): array
{
    global $pdo;
    $sql = 'SELECT w.id, w.email, w.platform, w.source_code, w.notified_at, w.created_at,
                   rl.name AS source_name
              FROM platform_waitlist w
              LEFT JOIN referral_links rl ON rl.source_code = w.source_code
             WHERE w.environment = ?';
    $params = [current_environment()];

    if ($idFilter !== null && count($idFilter) > 0) {
        $placeholders = implode(',', array_fill(0, count($idFilter), '?'));
        $sql .= " AND w.id IN ($placeholders)";
        $params = array_merge($params, array_values(array_map('intval', $idFilter)));
    }

    $sql .= ' ORDER BY w.created_at DESC, w.id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Footer for waitlist emails. These recipients aren't marketing subscribers;
 * they asked for a launch notification on the downloads page, so the footer
 * says exactly that instead of the standard broadcast unsubscribe blurb.
 */
function waitlist_footer_html(): string
{
    return '<hr style="border:none;border-top:1px solid #e5e7eb;margin:32px 0 16px;">'
        . '<p style="font-size:12px;color:#6b7280;">'
        . "You're receiving this because you asked to be notified about Argo Books "
        . 'for Mac on <a href="https://argorobots.com/downloads/">argorobots.com</a>. '
        . 'Questions? Just reply to this email.'
        . '</p>';
}

// CSV export (before any output).
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $rows = get_waitlist_rows();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="mac-waitlist-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['email', 'platform', 'source', 'signed_up_at', 'notified_at']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['email'],
            $r['platform'],
            $r['source_name'] ?? $r['source_code'] ?? '',
            $r['created_at'],
            $r['notified_at'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

$flash = null;
$flash_type = null;

// ─── POST handlers: bulk send + delete ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Reload the page and try again.');
    }
    $action = $_POST['action'] ?? '';

    if ($action === 'send_email') {
        $selected = $_POST['waitlist_ids'] ?? [];
        $subject  = trim($_POST['subject'] ?? '');
        $body     = trim($_POST['html_body'] ?? '');

        if (!is_array($selected) || count($selected) === 0) {
            $flash = 'No signups selected.';
            $flash_type = 'error';
        } elseif ($subject === '' || $body === '') {
            $flash = 'Subject and body are both required.';
            $flash_type = 'error';
        } else {
            $recipients = get_waitlist_rows($selected);
            $sent = 0;
            $failed = 0;
            $html = $body . waitlist_footer_html();

            foreach ($recipients as $r) {
                $ok = send_styled_email(
                    $r['email'],
                    $subject,
                    $html,
                    'blue',
                    'noreply@argorobots.com',
                    'Argo Books',
                    'contact@argorobots.com'
                );
                if ($ok) {
                    $sent++;
                    // Keep the first notification date if this is a re-send.
                    $upd = $pdo->prepare(
                        'UPDATE platform_waitlist SET notified_at = COALESCE(notified_at, NOW())
                          WHERE id = ? AND environment = ?'
                    );
                    $upd->execute([(int)$r['id'], current_environment()]);
                } else {
                    $failed++;
                }
            }

            $flash = "Sent {$sent} email(s)." . ($failed > 0 ? " {$failed} failed, check the error log." : '');
            $flash_type = $failed > 0 ? 'error' : 'success';
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM platform_waitlist WHERE id = ? AND environment = ?');
        $stmt->execute([$id, current_environment()]);
        $_SESSION['message'] = 'Waitlist entry deleted.';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php');
        exit;
    }
}

$rows = get_waitlist_rows();
$total = count($rows);
$last_30d = 0;
$cutoff = strtotime('-30 days');
foreach ($rows as $r) {
    if (strtotime($r['created_at']) >= $cutoff) {
        $last_30d++;
    }
}

$page_title = 'Mac Waitlist';
$page_description = 'Emails collected from the "notify me when the Mac version ships" form on the downloads page.';

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<?php if ($flash): ?>
    <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?>"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total signups</h3>
        <div class="value"><?php echo number_format($total); ?></div>
        <div class="subtext">all time</div>
    </div>
    <div class="stat-card">
        <h3>Last 30 days</h3>
        <div class="value"><?php echo number_format($last_30d); ?></div>
        <div class="subtext">new signups</div>
    </div>
</div>

<div class="table-container">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h2 style="margin:0;">Signups</h2>
        <?php if ($total > 0): ?>
            <a href="index.php?export=csv" class="btn btn-small btn-blue">Export CSV</a>
        <?php endif; ?>
    </div>

    <?php if ($total === 0): ?>
        <div class="empty-state">
            <p>No signups yet.</p>
            <p>The form lives on the downloads page under the macOS card.</p>
        </div>
    <?php else: ?>
        <form method="POST" action="index.php" id="waitlistSendForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="action" value="send_email">

            <div class="table-responsive">
                <table data-paginate="25">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="select-all" id="waitlistSelectAll"></th>
                            <th>Email</th>
                            <th>Platform</th>
                            <th>Source</th>
                            <th>Signed up</th>
                            <th>Notified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $r): ?>
                            <tr>
                                <td>
                                    <!-- Rows already notified start unchecked so a launch
                                         send doesn't re-email them by accident. -->
                                    <input type="checkbox" class="row-check" name="waitlist_ids[]"
                                           value="<?php echo (int)$r['id']; ?>"
                                           <?php echo $r['notified_at'] === null ? 'checked' : ''; ?>>
                                </td>
                                <td><?php echo htmlspecialchars($r['email']); ?></td>
                                <td><?php echo htmlspecialchars($r['platform']); ?></td>
                                <td>
                                    <?php if ($r['source_name'] !== null): ?>
                                        <?php echo htmlspecialchars($r['source_name']); ?>
                                    <?php elseif ($r['source_code'] !== null): ?>
                                        <code><?php echo htmlspecialchars($r['source_code']); ?></code>
                                    <?php else: ?>
                                        Direct
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(date('M j, Y', strtotime($r['created_at']))); ?></td>
                                <td><?php echo $r['notified_at'] !== null ? htmlspecialchars(date('M j, Y', strtotime($r['notified_at']))) : '—'; ?></td>
                                <td class="action-buttons">
                                    <button type="button" class="btn-small btn-red waitlist-delete-btn"
                                            data-id="<?php echo (int)$r['id']; ?>"
                                            data-email="<?php echo htmlspecialchars($r['email'], ENT_QUOTES); ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <h2 style="margin-top:28px;">Email selected</h2>
            <p class="subtext" style="margin-top:0;">
                The body accepts HTML and is wrapped in the standard Argo email template, with a
                "you asked to be notified about Argo Books for Mac" footer appended automatically.
                Sending stamps each recipient's Notified date (first send only).
            </p>
            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" name="subject" id="subject" maxlength="300" required
                       placeholder="Argo Books for Mac is here"
                       style="width:100%; padding:10px 12px; border:1px solid var(--gray-input-border); border-radius:6px; font-size:14px;">
            </div>
            <div class="form-group">
                <label for="html_body">Body (HTML) *</label>
                <textarea name="html_body" id="html_body" rows="8" required
                          placeholder="<p>Hi,</p>&#10;<p>Argo Books for Mac is now available to download&hellip;</p>"
                          style="width:100%; padding:10px 12px; border:1px solid var(--gray-input-border); border-radius:6px; font-size:14px; font-family:inherit;"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-blue">Send to selected</button>
            </div>
        </form>

        <form method="POST" action="index.php" id="waitlistDeleteForm" style="display:none;">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="waitlistDeleteId" value="">
        </form>
    <?php endif; ?>
</div>

<script>
    // Select-all toggles every row checkbox.
    const selectAll = document.getElementById('waitlistSelectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            document.querySelectorAll('.row-check').forEach(function (cb) {
                cb.checked = selectAll.checked;
            });
        });
    }

    // Delete buttons live inside the send form, so they post through a separate
    // hidden form to avoid submitting the bulk-send by accident.
    document.querySelectorAll('.waitlist-delete-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Remove ' + btn.dataset.email + ' from the waitlist?')) return;
            document.getElementById('waitlistDeleteId').value = btn.dataset.id;
            document.getElementById('waitlistDeleteForm').submit();
        });
    });

    // Confirm before sending, with the selected count.
    const sendForm = document.getElementById('waitlistSendForm');
    if (sendForm) {
        sendForm.addEventListener('submit', function (e) {
            const checked = document.querySelectorAll('.row-check:checked').length;
            if (checked === 0) {
                e.preventDefault();
                alert('No signups selected.');
                return;
            }
            if (!confirm('Send this email to ' + checked + ' recipient(s)?')) {
                e.preventDefault();
            }
        });
    }
</script>

</main>
</div>
</body>
</html>
