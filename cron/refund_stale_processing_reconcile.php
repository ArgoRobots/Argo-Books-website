<?php
declare(strict_types=1);

/**
 * refund_stale_processing_reconcile.php
 *
 * For refund_requests stuck in 'processing' for over 30 minutes (no webhook
 * received, no provider response stored), query the provider API to look up
 * the actual refund status and reconcile.
 *
 * Schedule: every 5 minutes.
 *   *\/5 * * * * php /var/www/argo-books-website/cron/refund_stale_processing_reconcile.php
 */

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request).
// Without this, the provider-reconciliation loop and completion-notification
// path is exposed over HTTP.
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../api/portal/_audit.php';
require_once __DIR__ . '/../api/portal/_refund_helpers.php';
require_once __DIR__ . '/../api/portal/refunds/_provider_stripe.php';
require_once __DIR__ . '/lib/run_tracker.php';

global $pdo;

$runId = cron_run_start($pdo, 'refund_stale_processing_reconcile');

try {

$stmt = $pdo->query("
    SELECT r.*, c.environment, c.stripe_account_id
    FROM refund_requests r
    INNER JOIN portal_companies c ON c.id = r.company_id
    WHERE r.state = 'processing'
      AND r.updated_at < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
    LIMIT 100
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reconciled = 0;
foreach ($rows as $r) {
    if ($r['provider'] !== 'stripe') {
        // PayPal/Square reconciliation is wired into their webhook handlers
        // and provider adapters; skip here for now.
        continue;
    }

    try {
        $company = [
            'environment' => $r['environment'],
            'stripe_account_id' => $r['stripe_account_id'],
        ];
        refund_stripe_init($company);

        $list = \Stripe\Refund::all(
            ['payment_intent' => $r['provider_payment_id'], 'limit' => 20],
            $r['stripe_account_id'] ? ['stripe_account' => $r['stripe_account_id']] : []
        );
        $found = null;
        foreach ($list->data as $rf) {
            $argoId = $rf->metadata['argo_request_id'] ?? null;
            if ($argoId !== null && (string)$argoId === (string)$r['id']) {
                $found = $rf;
                break;
            }
        }

        if ($found) {
            if ($found->status === 'succeeded') {
                // CAS guard so a webhook arriving in the same window doesn't
                // produce a second completion notification.
                $upd = $pdo->prepare("UPDATE refund_requests SET state='completed', provider_refund_id = ?, completed_at = NOW(), cancel_token = NULL, updated_at = NOW() WHERE id = ? AND state IN ('processing','cooling_off')");
                $upd->execute([$found->id, $r['id']]);
                if ($upd->rowCount() > 0) {
                    audit_log($pdo, (int)$r['company_id'], 'completed', 'system', null, (int)$r['id'], null, [
                        'reconciled_via_stale_cron' => true,
                        'provider_refund_id' => $found->id,
                    ]);
                    $r['state'] = 'completed';
                    $r['provider_refund_id'] = $found->id;
                    refund_notify_completion($pdo, $r);
                    $reconciled++;
                }
            } elseif (in_array($found->status, ['failed','canceled'], true)) {
                // CAS guard: same rationale as the success branch above,
                // a webhook (or another reconciler) may have flipped this
                // row to 'completed' between our SELECT and this UPDATE,
                // and we must not overwrite that with 'failed'.
                $upd = $pdo->prepare("UPDATE refund_requests SET state='failed', state_reason = ?, cancel_token = NULL, updated_at = NOW() WHERE id = ? AND state IN ('processing','cooling_off')");
                $upd->execute([$found->failure_reason ?? $found->status, $r['id']]);
                if ($upd->rowCount() > 0) {
                    audit_log($pdo, (int)$r['company_id'], 'failed', 'system', null, (int)$r['id'], null, [
                        'reconciled_via_stale_cron' => true,
                        'provider_status' => $found->status,
                    ]);
                    $reconciled++;
                }
            }
        }
    } catch (\Throwable $e) {
        error_log("stale_processing_reconcile (request {$r['id']}): " . $e->getMessage());
    }
}

echo "Reconciled $reconciled of " . count($rows) . " stuck-processing rows\n";
cron_metric_incr('refunds_reconciled', $reconciled);
cron_run_finish($pdo, $runId, 'ok');

} catch (Throwable $e) {
    cron_run_finish($pdo, $runId, 'error', $e->getMessage());
    throw $e;
}
