<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../email_marketing.php';

// Auth: admin session only (matches admin/outreach/index.php pattern)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$page_title = "Review Emails";
$page_description = "Send review or feedback emails to license-key customers (purchased 30+ days ago).";

$flash = null;
$flash_type = null;

/**
 * Eligibility query — license keys we can send to.
 * Re-used both for rendering and for the POST handler (defence in depth).
 */
function fetch_eligible_licenses(PDO $pdo, ?array $idFilter = null): array
{
    $sql = "
        SELECT
          lk.id, lk.email, lk.license_key,
          lk.created_at AS purchased_at,
          GREATEST(
            COALESCE((SELECT MAX(updated_at) FROM receipt_scan_usage WHERE license_key = lk.license_key), '1970-01-01'),
            COALESCE((SELECT MAX(updated_at) FROM invoice_send_usage  WHERE license_key = lk.license_key), '1970-01-01')
          ) AS last_active_at
        FROM license_keys lk
        WHERE lk.activated = 1
          AND lk.created_at <= NOW() - INTERVAL 30 DAY
          AND lk.review_email_sent_at IS NULL
          AND NOT EXISTS (
            SELECT 1 FROM email_suppressions s
            WHERE s.email = lk.email AND s.context IN ('reviews','all_marketing')
          )
    ";

    $params = [];
    if ($idFilter !== null && count($idFilter) > 0) {
        $placeholders = implode(',', array_fill(0, count($idFilter), '?'));
        $sql .= " AND lk.id IN ($placeholders)";
        $params = array_values(array_map('intval', $idFilter));
    }

    $sql .= " ORDER BY lk.created_at ASC LIMIT 500";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * "Active" = used the desktop app in the last 14 days.
 */
function is_active_user(string $lastActiveAt): bool
{
    $threshold = strtotime('-14 days');
    return strtotime($lastActiveAt) >= $threshold;
}

// POST handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = $_POST['csrf_token'] ?? '';
    if (!$postedToken || !hash_equals($_SESSION['csrf_token'], $postedToken)) {
        $flash = 'Session expired or invalid request token. Please try again.';
        $flash_type = 'error';
    } else {
        $action = $_POST['action'] ?? 'send';

        if ($action === 'skip') {
            $skipId = (int) ($_POST['license_id'] ?? 0);
            if ($skipId > 0) {
                $stmt = $pdo->prepare("UPDATE license_keys
                                       SET review_email_sent_at = NOW(),
                                           review_email_variant = 'manually_skipped'
                                       WHERE id = ? AND review_email_sent_at IS NULL");
                $stmt->execute([$skipId]);
                $flash = 'License skipped. It will no longer appear in this list.';
                $flash_type = 'success';
            }
        } elseif ($action === 'send') {
            $selected = $_POST['license_ids'] ?? [];
            if (!is_array($selected) || count($selected) === 0) {
                $flash = 'No licenses selected.';
                $flash_type = 'error';
            } else {
                $eligible = fetch_eligible_licenses($pdo, $selected);
                $sent = 0;
                $failed = 0;

                foreach ($eligible as $row) {
                    $licenseId = (int) $row['id'];
                    $email = $row['email'];
                    $variant = is_active_user($row['last_active_at']) ? 'active' : 'inactive';

                    // Atomic claim: only proceed if no other request beat us to this row.
                    // The IS NULL guard makes the operation idempotent under concurrent
                    // admin sessions and protects against double-emailing the customer.
                    $claim = $pdo->prepare("UPDATE license_keys
                                            SET review_email_sent_at = NOW(),
                                                review_email_variant = ?
                                            WHERE id = ? AND review_email_sent_at IS NULL");
                    $claim->execute([$variant, $licenseId]);
                    if ($claim->rowCount() === 0) {
                        // Already claimed by another request — skip silently.
                        continue;
                    }

                    $ok = ($variant === 'active')
                        ? send_review_request_email($licenseId, $email)
                        : send_feedback_request_email($licenseId, $email);

                    if ($ok) {
                        $sent++;
                    } else {
                        // Best-effort revert so the admin can retry. Scoped by variant
                        // so we only un-claim the row WE just claimed.
                        $revert = $pdo->prepare("UPDATE license_keys
                                                 SET review_email_sent_at = NULL,
                                                     review_email_variant = NULL
                                                 WHERE id = ? AND review_email_variant = ?");
                        $revert->execute([$licenseId, $variant]);
                        $failed++;
                    }
                }

                $flash = "Sent {$sent} email(s)." . ($failed > 0 ? " {$failed} failed — check error logs." : '');
                $flash_type = $failed > 0 ? 'error' : 'success';
            }
        }
    }
}

// Render
$eligible = fetch_eligible_licenses($pdo);
$active = [];
$inactive = [];
foreach ($eligible as $row) {
    if (is_active_user($row['last_active_at'])) {
        $active[] = $row;
    } else {
        $inactive[] = $row;
    }
}

include __DIR__ . '/../admin_header.php';
?>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../../resources/styles/checkbox.css">

<?php if ($flash): ?>
    <div class="alert alert-<?= htmlspecialchars($flash_type) ?>"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="reviews-intro">
    <p>
        These are license-key customers who purchased 30+ days ago and haven't been emailed yet. Active customers (used the app in the last 14 days) get a review request linking to Capterra. Inactive customers get a feedback request asking them to reply with what's getting in the way. Each customer is asked at most once.
    </p>
    <p>
        Use <strong>Skip</strong> to permanently exclude someone (for example, customers who already left a review or who you've already spoken to in person).
    </p>
</div>

<form method="post" id="reviews-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="send">

    <?php
    /**
     * @param array $rows
     * @param string $variantLabel
     */
    $renderSection = function (array $rows, string $sectionTitle, string $sectionDescription, string $variantClass) {
        ?>
        <div class="review-section <?= $variantClass ?>">
            <h2><?= htmlspecialchars($sectionTitle) ?> <span class="count">(<?= count($rows) ?>)</span></h2>
            <p class="section-description"><?= htmlspecialchars($sectionDescription) ?></p>

            <?php if (count($rows) === 0): ?>
                <p class="empty-state">No eligible customers in this group.</p>
            <?php else: ?>
                <table class="reviews-table">
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="select-all" data-section="<?= $variantClass ?>" checked></th>
                            <th>Email</th>
                            <th>License key</th>
                            <th>Purchased</th>
                            <th>Last active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row):
                            $purchased = date('Y-m-d', strtotime($row['purchased_at']));
                            $lastActive = strtotime($row['last_active_at']);
                            $lastActiveDisplay = ($lastActive > strtotime('1970-01-02')) ? date('Y-m-d', $lastActive) : 'Never';
                            $licenseShort = substr($row['license_key'], 0, 12) . '…';
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="row-check <?= $variantClass ?>-row"
                                           name="license_ids[]" value="<?= (int) $row['id'] ?>" checked>
                                </td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><code title="<?= htmlspecialchars($row['license_key']) ?>"><?= htmlspecialchars($licenseShort) ?></code></td>
                                <td><?= htmlspecialchars($purchased) ?></td>
                                <td><?= htmlspecialchars($lastActiveDisplay) ?></td>
                                <td>
                                    <button type="button" class="btn btn-small btn-gray skip-btn"
                                            data-license-id="<?= (int) $row['id'] ?>">Skip</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    };

    $renderSection(
        $active,
        'Active customers (review request)',
        'These customers used the app in the last 14 days. They will receive a Capterra review ask.',
        'active'
    );

    $renderSection(
        $inactive,
        'Inactive customers (feedback request)',
        'These customers have not used the app in the last 14 days. They will receive a "what got in the way?" email asking them to reply.',
        'inactive'
    );
    ?>

    <?php if (count($eligible) > 0): ?>
        <div class="form-actions">
            <button type="submit" class="btn btn-blue">Send to selected</button>
        </div>
    <?php endif; ?>
</form>

<form method="post" id="skip-form" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="action" value="skip">
    <input type="hidden" name="license_id" id="skip-license-id" value="">
</form>

<script>
    document.querySelectorAll('.select-all').forEach(function (master) {
        master.addEventListener('change', function () {
            var section = master.dataset.section;
            document.querySelectorAll('.row-check.' + section + '-row').forEach(function (cb) {
                cb.checked = master.checked;
            });
        });
    });

    document.querySelectorAll('.skip-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Skip this customer permanently? They will no longer appear in this list, even if they remain eligible.')) {
                return;
            }
            document.getElementById('skip-license-id').value = btn.dataset.licenseId;
            document.getElementById('skip-form').submit();
        });
    });

    document.getElementById('reviews-form').addEventListener('submit', function (e) {
        var checked = document.querySelectorAll('.row-check:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('No customers selected.');
            return;
        }
        if (!confirm('Send emails to ' + checked + ' customer(s)?')) {
            e.preventDefault();
        }
    });
</script>
