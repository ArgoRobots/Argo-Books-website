<?php
declare(strict_types=1);

/**
 * refund_stale_request_cleanup.php
 *
 * Cancels refund_requests stuck in 'pending_code' for over 1 hour.
 * Without this, abandoned codes pile up and clutter audit logs.
 *
 * Schedule: hourly.
 *   0 * * * * php /var/www/argo-books-website/cron/refund_stale_request_cleanup.php
 */

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../api/portal/_audit.php';

global $pdo;

$stmt = $pdo->query("
    SELECT id, company_id FROM refund_requests
    WHERE state = 'pending_code'
      AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)
    LIMIT 500
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $r) {
    $pdo->prepare("UPDATE refund_requests SET state='cancelled', state_reason='code_window_expired', updated_at = NOW() WHERE id = ?")
        ->execute([$r['id']]);
    audit_log($pdo, (int)$r['company_id'], 'cancelled_by_user', 'system', null, (int)$r['id'], null, [
        'reason' => 'code_window_expired',
    ]);
}

echo "Cleaned " . count($rows) . " stale pending_code rows\n";

// Also vacuum the idempotency cache (older than 24h is useless).
$pdo->exec("DELETE FROM refund_idempotency_cache WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)");
