<?php
require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Waitlist rows for the current environment, newest first, with the referral
 * link's display name when the source is a known link.
 */
function get_waitlist_rows(): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT w.id, w.email, w.platform, w.source_code, w.notified_at, w.created_at,
                rl.name AS source_name
           FROM platform_waitlist w
           LEFT JOIN referral_links rl ON rl.source_code = w.source_code
          WHERE w.environment = ?
          ORDER BY w.created_at DESC, w.id DESC'
    );
    $stmt->execute([current_environment()]);
    return $stmt->fetchAll();
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

// Delete handler.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        http_response_code(403);
        die('Invalid CSRF token. Reload the page and try again.');
    }
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM platform_waitlist WHERE id = ? AND environment = ?');
    $stmt->execute([$id, current_environment()]);
    $_SESSION['message'] = 'Waitlist entry deleted.';
    $_SESSION['message_type'] = 'success';
    header('Location: index.php');
    exit;
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
        <div class="table-responsive">
            <table data-paginate="25">
                <thead>
                    <tr>
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
                                <form method="POST" action="index.php" style="display:inline;"
                                      onsubmit="return confirm('Remove <?php echo htmlspecialchars($r['email'], ENT_QUOTES); ?> from the waitlist?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <button type="submit" class="btn-small btn-red">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

</main>
</div>
</body>
</html>
