<?php
require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../community/affiliate/affiliate_functions.php';
require_once __DIR__ . '/../../community/affiliate/affiliate_emails.php';

// Admin auth guard (mirrors every other admin page).
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

$env = current_environment();
$admin_username = $_SESSION['admin_username'] ?? 'admin';

$page_title = 'Affiliates';
$page_description = 'Review affiliate applications, track commission owed, and record payouts';

// Per-session CSRF token for every form on this page.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Create the referral_links row that activates an affiliate's link. Idempotent,
 * like ensure_auto_referral_link() in track_referral.php: the dashboards join on
 * referral_links, so the row must exist for the affiliate's traffic to appear.
 */
function ensure_affiliate_referral_link(string $source_code, string $username): void
{
    global $pdo;
    $exists = $pdo->prepare('SELECT 1 FROM referral_links WHERE source_code = ? LIMIT 1');
    $exists->execute([$source_code]);
    if ($exists->fetchColumn()) {
        // Already exists (e.g. re-approval); just make sure it's active.
        $pdo->prepare('UPDATE referral_links SET is_active = 1 WHERE source_code = ?')->execute([$source_code]);
        return;
    }
    $stmt = $pdo->prepare('INSERT INTO referral_links (source_code, name, description, target_url, is_active) VALUES (?, ?, ?, ?, 1)');
    $stmt->execute([
        $source_code,
        'Affiliate: ' . $username,
        'Affiliate referral link',
        'https://argorobots.com/',
    ]);
}

// Handle state-mutating POSTs.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Reload the page and try again.');
    }

    $action = $_POST['action'] ?? '';
    $affiliate_id = (int) ($_POST['affiliate_id'] ?? 0);
    $affiliate = $affiliate_id ? get_affiliate_by_id($affiliate_id, $env) : null;

    if ($affiliate) {
        if ($action === 'approve' && $affiliate['status'] !== 'approved') {
            ensure_affiliate_referral_link($affiliate['source_code'], 'user#' . $affiliate['user_id']);
            $pdo->prepare("UPDATE affiliates SET status = 'approved', reviewed_at = NOW(), reviewed_by = ? WHERE id = ?")
                ->execute([$admin_username, $affiliate_id]);

            $u = $pdo->prepare('SELECT username, email FROM community_users WHERE id = ?');
            $u->execute([$affiliate['user_id']]);
            if ($cu = $u->fetch()) {
                $dash = 'https://argorobots.com/community/affiliate/';
                send_affiliate_approved_email($cu['email'], $cu['username'], affiliate_referral_url($affiliate['source_code']), $dash);
            }
            $_SESSION['success_message'] = 'Affiliate approved and link activated.';
        } elseif ($action === 'reject' && $affiliate['status'] === 'pending') {
            $notes = trim($_POST['review_notes'] ?? '');
            $pdo->prepare("UPDATE affiliates SET status = 'rejected', reviewed_at = NOW(), reviewed_by = ?, review_notes = ? WHERE id = ?")
                ->execute([$admin_username, $notes, $affiliate_id]);

            $u = $pdo->prepare('SELECT username, email FROM community_users WHERE id = ?');
            $u->execute([$affiliate['user_id']]);
            if ($cu = $u->fetch()) {
                send_affiliate_rejected_email($cu['email'], $cu['username'], $notes);
            }
            $_SESSION['success_message'] = 'Application rejected.';
        } elseif ($action === 'suspend' && $affiliate['status'] === 'approved') {
            $pdo->prepare("UPDATE affiliates SET status = 'suspended' WHERE id = ?")->execute([$affiliate_id]);
            $pdo->prepare('UPDATE referral_links SET is_active = 0 WHERE source_code = ?')->execute([$affiliate['source_code']]);
            $_SESSION['success_message'] = 'Affiliate suspended. Past commission is preserved.';
        } elseif ($action === 'reactivate' && $affiliate['status'] === 'suspended') {
            $pdo->prepare("UPDATE affiliates SET status = 'approved' WHERE id = ?")->execute([$affiliate_id]);
            $pdo->prepare('UPDATE referral_links SET is_active = 1 WHERE source_code = ?')->execute([$affiliate['source_code']]);
            $_SESSION['success_message'] = 'Affiliate reactivated.';
        } elseif ($action === 'update_code') {
            $new_code = affiliate_normalize_code($_POST['source_code'] ?? '');
            $old_code = $affiliate['source_code'];
            if ($new_code === '' || strlen($new_code) < 3 || strlen($new_code) > 50) {
                $_SESSION['error_message'] = 'Referral link must be 3 to 50 characters (letters, numbers, hyphens).';
            } elseif (affiliate_code_has_reserved_prefix($new_code)) {
                $_SESSION['error_message'] = 'That prefix is reserved for traffic sources. Pick a different link.';
            } elseif ($new_code !== $old_code && affiliate_source_code_taken($new_code, $affiliate_id)) {
                $_SESSION['error_message'] = 'That referral link is already taken.';
            } elseif ($new_code !== $old_code) {
                // Rename the code everywhere it is referenced so past clicks,
                // events, and attribution move with it instead of orphaning.
                $pdo->beginTransaction();
                $pdo->prepare('UPDATE affiliates SET source_code = ? WHERE id = ?')->execute([$new_code, $affiliate_id]);
                $pdo->prepare('UPDATE referral_links SET source_code = ? WHERE source_code = ?')->execute([$new_code, $old_code]);
                $pdo->prepare('UPDATE referral_events SET source_code = ? WHERE source_code = ?')->execute([$new_code, $old_code]);
                $pdo->prepare('UPDATE referral_visits SET source_code = ? WHERE source_code = ?')->execute([$new_code, $old_code]);
                $pdo->commit();
                $_SESSION['success_message'] = 'Referral link updated.';
            }
        } elseif ($action === 'delete') {
            if (affiliate_is_deletable($affiliate, $env)) {
                // Safe because there is zero activity: no visits/events reference
                // the code, so removing the link row can't orphan history.
                $pdo->beginTransaction();
                $pdo->prepare('DELETE FROM referral_links WHERE source_code = ?')->execute([$affiliate['source_code']]);
                $pdo->prepare('DELETE FROM affiliates WHERE id = ?')->execute([$affiliate_id]); // cascades payouts
                $pdo->commit();
                $_SESSION['success_message'] = 'Affiliate deleted.';
            } else {
                $_SESSION['error_message'] = 'Cannot delete an affiliate with clicks or earnings. Suspend it instead.';
            }
        } elseif ($action === 'record_payout') {
            $amount = round((float) ($_POST['amount'] ?? 0), 2);
            $paid_at = $_POST['paid_at'] ?? date('Y-m-d');
            $method = trim($_POST['method'] ?? 'paypal');
            $reference = trim($_POST['reference'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            if ($amount > 0 && strtotime($paid_at) !== false) {
                $stmt = $pdo->prepare('INSERT INTO affiliate_payouts (affiliate_id, amount, currency, paid_at, method, reference, notes, recorded_by, environment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$affiliate_id, $amount, 'CAD', date('Y-m-d', strtotime($paid_at)), $method, $reference, $notes, $admin_username, $env]);
                $_SESSION['success_message'] = 'Payout of $' . number_format($amount, 2) . ' recorded.';
            } else {
                $_SESSION['error_message'] = 'Enter a positive amount and a valid date.';
            }
        }
    }

    // Redirect back to the detail view (if we were on one) or the list.
    // approve/reject/delete land on the list; per-affiliate edits stay on detail.
    $back = $affiliate_id && !in_array($action, ['reject', 'approve', 'delete'], true)
        ? 'index.php?id=' . $affiliate_id
        : 'index.php';
    header('Location: ' . $back);
    exit;
}

$detail_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="../common-style.css">

<style>
    .money-positive { color: var(--success-color, #10b981); font-weight: 600; }
    .money-owed { color: var(--warning-color, #f59e0b); font-weight: 600; }
    .affiliate-link-box { font-family: monospace; word-break: break-all; }
    .payout-form .form-row { display: flex; flex-wrap: wrap; gap: 12px; }
    .payout-form .form-group { flex: 1; min-width: 140px; }
    .back-link { margin-bottom: 16px; }
    .code-edit-form { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin: 6px 0 4px; }
    .code-edit-form input { max-width: 320px; }

    /* common-style.css styles text/number/email inputs but not date inputs,
       so match them here (both themes) instead of leaving the native control. */
    .payout-form input[type="date"] {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--gray-input-border);
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
        font-family: inherit;
    }
    /* Native date inputs reserve extra height for the picker; pin all row
       inputs to one height (border-box) so they line up exactly. */
    .payout-form .form-row input { height: 38px; }
    [data-theme="dark"] .payout-form input[type="date"] {
        background: var(--gray-700);
        border-color: var(--gray-600);
        color: var(--white);
        /* render the native calendar picker + indicator in dark mode */
        color-scheme: dark;
    }
</style>

<div class="container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

<?php if ($detail_id && ($aff = get_affiliate_by_id($detail_id, $env))):
    // ---- Detail view for one affiliate ----
    $u = $pdo->prepare('SELECT username, email FROM community_users WHERE id = ?');
    $u->execute([$aff['user_id']]);
    $cu = $u->fetch() ?: ['username' => 'unknown', 'email' => ''];
    $money = affiliate_money_summary($aff, $env);
    $stats = get_affiliate_stats($aff['source_code'], $env);
    $payouts = $pdo->prepare('SELECT * FROM affiliate_payouts WHERE affiliate_id = ? AND environment = ? ORDER BY paid_at DESC, id DESC');
    $payouts->execute([$detail_id, $env]);
    $payout_rows = $payouts->fetchAll();
    $csrf = htmlspecialchars($_SESSION['csrf_token']);
?>
    <a href="index.php" class="link back-link">&larr; All affiliates</a>

    <h2><?php echo htmlspecialchars($cu['username']); ?>
        <span class="status-badge status-<?php echo $aff['status'] === 'approved' ? 'active' : 'inactive'; ?>"><?php echo htmlspecialchars(ucfirst($aff['status'])); ?></span>
    </h2>
    <p><?php echo htmlspecialchars($cu['email']); ?> &middot; applied <?php echo htmlspecialchars(date('M j, Y', strtotime($aff['applied_at']))); ?></p>

    <div class="stats-grid">
        <div class="stat-card"><h3>Clicks</h3><div class="value"><?php echo number_format($stats['clicks']); ?></div></div>
        <div class="stat-card"><h3>Signups</h3><div class="value"><?php echo number_format($stats['signups']); ?></div></div>
        <div class="stat-card"><h3>Paying customers</h3><div class="value"><?php echo number_format($stats['paying']); ?></div></div>
        <div class="stat-card"><h3>Commission earned</h3><div class="value money-positive">$<?php echo number_format($money['earned'], 2); ?></div><div class="subtext">CAD, 50% / 12 mo</div></div>
        <div class="stat-card"><h3>Paid out</h3><div class="value">$<?php echo number_format($money['paid'], 2); ?></div></div>
        <div class="stat-card"><h3>Owed</h3><div class="value money-owed">$<?php echo number_format($money['owed'], 2); ?></div></div>
    </div>

    <div class="table-container">
        <h3>Referral link &amp; payout details</h3>
        <p class="affiliate-link-box"><?php echo htmlspecialchars(affiliate_referral_url($aff['source_code'])); ?></p>
        <form method="POST" class="code-edit-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="update_code">
            <input type="hidden" name="affiliate_id" value="<?php echo $detail_id; ?>">
            <label for="source_code">Source code</label>
            <input type="text" name="source_code" id="source_code" value="<?php echo htmlspecialchars($aff['source_code']); ?>" pattern="[A-Za-z0-9\-]{3,50}" required>
            <button type="submit" class="btn btn-small btn-blue">Save link</button>
        </form>
        <p class="subtext">Changing this moves all past clicks and earnings to the new link.</p>
        <p>Payout: <?php echo htmlspecialchars($aff['payout_method']); ?>
            <?php echo $aff['payout_email'] ? '&rarr; ' . htmlspecialchars($aff['payout_email']) : '<em>(no payout email on file)</em>'; ?></p>
        <?php if (!empty($aff['promo_url'])): ?><p>Promotes at: <?php echo htmlspecialchars($aff['promo_url']); ?></p><?php endif; ?>
        <?php if (!empty($aff['application_reason'])): ?><p>Application note: <?php echo nl2br(htmlspecialchars($aff['application_reason'])); ?></p><?php endif; ?>

        <div class="action-buttons">
            <?php if ($aff['status'] === 'pending'): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="affiliate_id" value="<?php echo $detail_id; ?>">
                    <button type="submit" class="btn btn-green">Approve</button>
                </form>
            <?php elseif ($aff['status'] === 'approved'): ?>
                <form method="POST" style="display:inline" onsubmit="return confirm('Suspend this affiliate? Their link stops working but past commission is kept.');">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="suspend">
                    <input type="hidden" name="affiliate_id" value="<?php echo $detail_id; ?>">
                    <button type="submit" class="btn btn-red">Suspend</button>
                </form>
            <?php elseif ($aff['status'] === 'suspended'): ?>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="reactivate">
                    <input type="hidden" name="affiliate_id" value="<?php echo $detail_id; ?>">
                    <button type="submit" class="btn btn-green">Reactivate</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-container">
        <h3>Record a payout</h3>
        <p class="subtext">Owed right now: <strong class="money-owed">$<?php echo number_format($money['owed'], 2); ?></strong> CAD. Record what you actually paid externally.</p>
        <form method="POST" class="payout-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <input type="hidden" name="action" value="record_payout">
            <input type="hidden" name="affiliate_id" value="<?php echo $detail_id; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="amount">Amount (CAD)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" required value="<?php echo $money['owed'] > 0 ? number_format($money['owed'], 2, '.', '') : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="paid_at">Date paid</label>
                    <input type="date" name="paid_at" id="paid_at" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="method">Method</label>
                    <input type="text" name="method" id="method" value="<?php echo htmlspecialchars($aff['payout_method']); ?>">
                </div>
                <div class="form-group">
                    <label for="reference">Reference</label>
                    <input type="text" name="reference" id="reference" placeholder="PayPal txn id">
                </div>
            </div>
            <div class="form-group">
                <label for="notes">Notes</label>
                <input type="text" name="notes" id="notes">
            </div>
            <button type="submit" class="btn btn-blue">Record payout</button>
        </form>
    </div>

    <div class="table-container">
        <h3>Payout history</h3>
        <?php if (empty($payout_rows)): ?>
            <p class="subtext">No payouts recorded yet.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th><th>Notes</th><th>By</th></tr></thead>
                <tbody>
                    <?php foreach ($payout_rows as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('M j, Y', strtotime($p['paid_at']))); ?></td>
                            <td>$<?php echo number_format((float) $p['amount'], 2); ?> <?php echo htmlspecialchars($p['currency']); ?></td>
                            <td><?php echo htmlspecialchars($p['method']); ?></td>
                            <td><?php echo htmlspecialchars($p['reference'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['notes'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($p['recorded_by'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<?php else:
    // ---- List view ----
    $pending = $pdo->prepare("SELECT a.*, cu.username, cu.email FROM affiliates a JOIN community_users cu ON cu.id = a.user_id WHERE a.environment = ? AND a.status = 'pending' ORDER BY a.applied_at ASC");
    $pending->execute([$env]);
    $pending_rows = $pending->fetchAll();

    $active = $pdo->prepare("SELECT a.*, cu.username, cu.email FROM affiliates a JOIN community_users cu ON cu.id = a.user_id WHERE a.environment = ? AND a.status IN ('approved', 'suspended') ORDER BY a.reviewed_at DESC, a.id DESC");
    $active->execute([$env]);
    $active_rows = $active->fetchAll();
    $csrf = htmlspecialchars($_SESSION['csrf_token']);
?>
    <div class="table-container">
        <div class="table-header-actions"><h2>Pending applications</h2></div>
        <?php if (empty($pending_rows)): ?>
            <p class="subtext">No applications waiting for review.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>User</th><th>Email</th><th>Applied</th><th>Promotes at</th><th>Note</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($pending_rows as $a): ?>
                        <tr>
                            <td><a href="index.php?id=<?php echo (int) $a['id']; ?>" class="link"><?php echo htmlspecialchars($a['username']); ?></a></td>
                            <td><?php echo htmlspecialchars($a['email']); ?></td>
                            <td><?php echo htmlspecialchars(date('M j, Y', strtotime($a['applied_at']))); ?></td>
                            <td><?php echo htmlspecialchars($a['promo_url'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars(mb_strimwidth($a['application_reason'] ?? '', 0, 60, '…')); ?></td>
                            <td class="action-buttons">
                                <button type="button" class="btn btn-small btn-green" onclick="approveAffiliate(<?php echo (int) $a['id']; ?>)">Approve</button>
                                <button type="button" class="btn btn-small btn-red" onclick="rejectAffiliate(<?php echo (int) $a['id']; ?>)">Reject</button>
                                <button type="button" class="btn btn-small btn-gray" onclick="deleteAffiliate(<?php echo (int) $a['id']; ?>, <?php echo htmlspecialchars(json_encode($a['username'])); ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="table-container">
        <div class="table-header-actions"><h2>Affiliates</h2></div>
        <?php if (empty($active_rows)): ?>
            <p class="subtext">No approved affiliates yet.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>User</th><th>Code</th><th>Status</th><th>Clicks</th><th>Earned</th><th>Paid</th><th>Owed</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($active_rows as $a):
                        $money = affiliate_money_summary($a, $env);
                        $stats = get_affiliate_stats($a['source_code'], $env);
                        $deletable = $stats['clicks'] === 0 && $stats['signups'] === 0 && $stats['paying'] === 0
                            && (float) $money['earned'] === 0.0 && (float) $money['paid'] === 0.0;
                    ?>
                        <tr>
                            <td><a href="index.php?id=<?php echo (int) $a['id']; ?>" class="link"><?php echo htmlspecialchars($a['username']); ?></a></td>
                            <td><code><?php echo htmlspecialchars($a['source_code']); ?></code></td>
                            <td><span class="status-badge status-<?php echo $a['status'] === 'approved' ? 'active' : 'inactive'; ?>"><?php echo htmlspecialchars(ucfirst($a['status'])); ?></span></td>
                            <td><?php echo number_format($stats['clicks']); ?></td>
                            <td class="money-positive">$<?php echo number_format($money['earned'], 2); ?></td>
                            <td>$<?php echo number_format($money['paid'], 2); ?></td>
                            <td class="money-owed">$<?php echo number_format($money['owed'], 2); ?></td>
                            <td class="action-buttons">
                                <a href="index.php?id=<?php echo (int) $a['id']; ?>" class="btn btn-small btn-blue">View</a>
                                <?php if ($deletable): ?>
                                    <button type="button" class="btn btn-small btn-gray" onclick="deleteAffiliate(<?php echo (int) $a['id']; ?>, <?php echo htmlspecialchars(json_encode($a['username'])); ?>)">Delete</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Reject modal -->
    <div id="rejectModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="modal-close" onclick="closeRejectModal()">&times;</span>
            <h2>Reject application</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="affiliate_id" id="rejectAffiliateId">
                <div class="form-group">
                    <label for="review_notes">Reason (optional, included in the email)</label>
                    <textarea name="review_notes" id="review_notes" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-red">Reject &amp; email</button>
                    <button type="button" class="btn btn-gray" onclick="closeRejectModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const affiliateCsrf = <?php echo json_encode($csrf); ?>;

        function submitAffiliateAction(id, action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML =
                '<input type="hidden" name="csrf_token" value="' + affiliateCsrf + '">' +
                '<input type="hidden" name="action" value="' + action + '">' +
                '<input type="hidden" name="affiliate_id" value="' + id + '">';
            document.body.appendChild(form);
            form.submit();
        }

        function approveAffiliate(id) {
            submitAffiliateAction(id, 'approve');
        }

        function deleteAffiliate(id, name) {
            if (confirm('Delete the affiliate "' + name + '"? This can\'t be undone. Only allowed when there are no clicks or earnings.')) {
                submitAffiliateAction(id, 'delete');
            }
        }

        function rejectAffiliate(id) {
            document.getElementById('rejectAffiliateId').value = id;
            document.getElementById('rejectModal').style.display = 'block';
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
        window.addEventListener('click', function (e) {
            const m = document.getElementById('rejectModal');
            if (e.target === m) { closeRejectModal(); }
        });
    </script>
<?php endif; ?>
</div>
