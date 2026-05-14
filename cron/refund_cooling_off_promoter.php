<?php
declare(strict_types=1);

/**
 * refund_cooling_off_promoter.php
 *
 * Promotes refund_requests from cooling_off → processing once their
 * cooling_off_until elapses, then invokes the provider API.
 *
 * Schedule: every 1 minute.
 *   * * * * * php /var/www/argo-books-website/cron/refund_cooling_off_promoter.php
 */

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request).
// Without this, /cron/refund_cooling_off_promoter.php is web-reachable and
// anyone could trigger refund promotion (and provider charges) over HTTP.
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../api/portal/_audit.php';
require_once __DIR__ . '/../api/portal/_refund_helpers.php';

global $pdo;

$stmt = $pdo->query("
    SELECT r.*, c.id AS cid, c.locked, c.lock_reason, c.email_verified_at,
           c.owner_email, c.stripe_account_id, c.environment, c.created_at AS company_created_at,
           c.paypal_merchant_id, c.square_merchant_id, c.square_access_token, c.square_location_id
    FROM refund_requests r
    INNER JOIN portal_companies c ON c.id = r.company_id
    WHERE r.state = 'cooling_off' AND r.cooling_off_until <= NOW()
    LIMIT 100
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$promoted = 0;
$auto_cancelled = 0;

foreach ($rows as $row) {
    if ($row['locked']) {
        // Stay in cooling_off until unlocked, OR auto-cancel after 24h.
        // CAS guard: only flip if still cooling_off so a concurrent
        // user-cancel or webhook completion isn't overwritten.
        if (strtotime($row['updated_at']) < time() - 86400) {
            $upd = $pdo->prepare("UPDATE refund_requests SET state='cancelled', state_reason='locked_account_auto_cancel', cancel_token = NULL, updated_at=NOW() WHERE id = ? AND state = 'cooling_off'");
            $upd->execute([$row['id']]);
            if ($upd->rowCount() > 0) {
                audit_log($pdo, (int)$row['company_id'], 'cancelled_by_system', 'system', null, (int)$row['id'], null, [
                    'reason' => 'locked_account_auto_cancel',
                ]);
                $auto_cancelled++;
            }
        }
        continue;
    }

    // Build a "company" array shape compatible with refund_execute_against_provider
    $company = [
        'id' => (int)$row['cid'],
        'environment' => $row['environment'],
        'owner_email' => $row['owner_email'],
        'stripe_account_id' => $row['stripe_account_id'],
        'paypal_merchant_id' => $row['paypal_merchant_id'],
        'square_merchant_id' => $row['square_merchant_id'],
        'square_access_token' => $row['square_access_token'],
        'square_location_id' => $row['square_location_id'],
        'created_at' => $row['company_created_at'],
        'customer_name' => $row['customer_name'] ?? null,
    ];

    // CAS guard: only promote if the row is still cooling_off. Without
    // this, a user-cancel between SELECT and UPDATE would be overwritten
    // and we'd still execute the refund against the provider.
    $upd = $pdo->prepare("UPDATE refund_requests SET state='processing', updated_at=NOW() WHERE id = ? AND state = 'cooling_off'");
    $upd->execute([$row['id']]);
    if ($upd->rowCount() === 0) {
        // State changed under us (cancelled, completed, etc.) — skip.
        continue;
    }
    audit_log($pdo, (int)$row['company_id'], 'processing', 'system', null, (int)$row['id'], null, [
        'promoted_from' => 'cooling_off',
    ]);

    refund_execute_against_provider($pdo, $company, (int)$row['id']);
    $promoted++;
}

echo "Promoted: $promoted, Auto-cancelled (locked >24h): $auto_cancelled\n";
