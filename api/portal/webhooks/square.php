<?php
/**
 * Square Webhook for Portal Payments
 *
 * POST /api/portal/webhooks/square
 *
 * Handles Square webhook events for invoice payments.
 * Backup confirmation for payments processed through the portal.
 */

require_once __DIR__ . '/../portal-helper.php';
require_once __DIR__ . '/_square_helpers.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$payload = file_get_contents('php://input');

// Verify webhook signature (mandatory)
$signature = $_SERVER['HTTP_X_SQUARE_HMACSHA256_SIGNATURE'] ?? '';
$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';
$webhookSignatureKey = $is_production
    ? ($_ENV['SQUARE_LIVE_PORTAL_WEBHOOK_SIGNATURE_KEY'] ?? '')
    : ($_ENV['SQUARE_SANDBOX_PORTAL_WEBHOOK_SIGNATURE_KEY'] ?? '');

if (empty($webhookSignatureKey)) {
    error_log('Portal Square webhook: No webhook signature key configured (env: ' . ($is_production ? 'production' : 'sandbox') . ')');
    http_response_code(500);
    exit;
}

if (empty($signature)) {
    error_log('Portal Square webhook: Missing signature header');
    http_response_code(401);
    exit;
}

$notificationUrl = site_url('/api/portal/webhooks/square');
if (!verify_square_webhook_signature($notificationUrl, $payload, $webhookSignatureKey, $signature)) {
    error_log('Portal Square webhook: Invalid signature');
    http_response_code(400);
    exit;
}

$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    exit;
}

$eventType = $data['type'] ?? '';

switch ($eventType) {
    case 'payment.created':
    case 'payment.updated':
        // Square fires payment.created/updated for any status transition.
        // We only act on COMPLETED — this is the backup confirmation for
        // payments whose synchronous handling in checkout.php was interrupted.
        $payment = $data['data']['object']['payment'] ?? [];
        $paymentStatus = $payment['status'] ?? '';
        if ($paymentStatus !== 'COMPLETED') break;
        $paymentId = $payment['id'] ?? 'unknown';
        error_log("Portal Square webhook: Payment completed - $paymentId");

        $squareAmount = floatval($payment['amount_money']['amount'] ?? 0) / 100;
        $squareCurrency = strtoupper($payment['amount_money']['currency'] ?? 'USD');
        $squareOrderId = $payment['order_id'] ?? '';
        $squareReferenceId = $payment['reference_id'] ?? '';
        $squareNote = $payment['note'] ?? '';

        // Try to extract invoice reference from reference_id or note
        $invoiceRef = $squareReferenceId ?: $squareNote;

        if (!empty($paymentId) && $paymentId !== 'unknown' && $squareAmount > 0 && !empty($invoiceRef)) {
            $stmt = $pdo->prepare(
                'SELECT company_id, invoice_id, customer_name FROM portal_invoices WHERE invoice_id = ? LIMIT 1'
            );
            $stmt->execute([$invoiceRef]);
            $invoiceRecord = $stmt->fetch();

            if ($invoiceRecord) {
                record_portal_payment([
                    'company_id' => (int) $invoiceRecord['company_id'],
                    'invoice_id' => $invoiceRecord['invoice_id'],
                    'customer_name' => $invoiceRecord['customer_name'] ?? '',
                    'amount' => $squareAmount,
                    'currency' => $squareCurrency,
                    'payment_method' => 'square',
                    'provider_payment_id' => $paymentId,
                    'provider_transaction_id' => $squareOrderId ?: $paymentId,
                    'reference_number' => generate_reference_number(),
                    'status' => 'completed',
                    'payment_environment' => ($_ENV['APP_ENV'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox',
                ]);
            }
        }
        break;

    case 'refund.created':
    case 'refund.updated':
        try {
            $refund = $data['data']['object']['refund'] ?? [];
            $refundId = $refund['id'] ?? 'unknown';
            $status = $refund['status'] ?? '';
            error_log("Portal Square webhook: Refund event $refundId status=$status");

            // We only act on COMPLETED. Square fires updated events for every
            // status transition (PENDING → COMPLETED → etc.).
            if ($status !== 'COMPLETED') break;

            $paymentId = $refund['payment_id'] ?? '';
            $idempotencyKey = $refund['order_id'] ?? '';   // not always present
            $amount = (int)($refund['amount_money']['amount'] ?? 0);
            $currency = $refund['amount_money']['currency'] ?? 'USD';
            // Argo set idempotency_key = 'argo_request_<id>' on issue; surfaced via reason note
            $note = $refund['reason'] ?? '';

            if (empty($paymentId)) break;

            // Find original payment + insert negative-amount refund row (mirroring Stripe webhook)
            $stmt = $pdo->prepare("SELECT * FROM portal_payments WHERE provider_payment_id = ? AND status = 'completed' AND amount > 0 LIMIT 1");
            $stmt->execute([$paymentId]);
            $original = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($original) {
                // Key the refund row by Square's own refund id so multiple
                // partial refunds against the same payment produce distinct
                // rows (paymentId-keying collides on the second refund and
                // silently drops it).
                $refundRow = 'refund_' . $refundId;
                $refundAmount = $amount / 100.0;
                $recordResult = record_portal_payment([
                    'company_id' => $original['company_id'],
                    'invoice_id' => $original['invoice_id'],
                    'customer_name' => $original['customer_name'],
                    'amount' => -$refundAmount,
                    'currency' => strtoupper($currency),
                    'payment_method' => 'square',
                    'provider_payment_id' => $refundRow,
                    'provider_transaction_id' => $paymentId,
                    'reference_number' => generate_reference_number(),
                    'status' => 'refunded',
                    // Inherit the original payment's environment so a misconfigured
                    // APP_ENV can't tag a refund with a different env than the
                    // payment it offsets.
                    'payment_environment' => $original['payment_environment'] ?? (($_ENV['APP_ENV'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox'),
                ]);
                if (!empty($recordResult['inserted'])) {
                    // Cumulative-refund check: only flip the original payment
                    // to 'refunded' once refunds cover the original amount.
                    // Without this, even a partial refund flipped the original
                    // to refunded and books would show it as fully refunded.
                    $sumStmt = $pdo->prepare(
                        "SELECT COALESCE(SUM(amount), 0) AS refunded_total
                         FROM portal_payments
                         WHERE amount < 0 AND payment_method = 'square'
                           AND provider_transaction_id = ?"
                    );
                    $sumStmt->execute([$paymentId]);
                    // Compare in integer cents so a chain of partial refunds
                    // can't drift past the threshold via repeated float rounding.
                    $refundedCents = (int)round(abs((float)$sumStmt->fetch()['refunded_total']) * 100);
                    $originalCents = (int)round((float)$original['amount'] * 100);
                    if ($refundedCents >= $originalCents) {
                        $pdo->prepare("UPDATE portal_payments SET status='refunded' WHERE id = ?")
                            ->execute([$original['id']]);
                    }

                    // Update invoice balance/status (mirrors Stripe path).
                    // SET-clause order matters: status CASE must see the
                    // pre-update balance_due.
                    $pdo->prepare(
                        'UPDATE portal_invoices
                         SET status = CASE
                                 WHEN balance_due + ? >= total_amount THEN "sent"
                                 ELSE "partial"
                             END,
                             balance_due = LEAST(total_amount, balance_due + ?),
                             updated_at = NOW()
                         WHERE company_id = ? AND invoice_id = ?'
                    )->execute([$refundAmount, $refundAmount, $original['company_id'], $original['invoice_id']]);
                }

                // Reconcile refund_requests by argo_request_id (we set this in the
                // SDK's idempotency_key on issue; Square echoes it back as well
                // as in some payload shapes).
                $combined = ($refund['idempotency_key'] ?? '') . '|' . $note . '|' . ($refund['order_id'] ?? '');
                if (preg_match('/argo_request_(\d+)/', $combined, $m)) {
                    require_once __DIR__ . '/../_audit.php';
                    require_once __DIR__ . '/../_refund_helpers.php';
                    $argoId = (int)$m[1];
                    $rstmt = $pdo->prepare("SELECT * FROM refund_requests WHERE id = ?");
                    $rstmt->execute([$argoId]);
                    $rr = $rstmt->fetch(PDO::FETCH_ASSOC);
                    if ($rr && $rr['state'] !== 'completed' && $rr['state'] !== 'cancelled') {
                        // CAS guard: only the UPDATE that actually flips the
                        // state notifies, so a race with the synchronous
                        // execute path can't fire two completion emails.
                        $upd = $pdo->prepare("UPDATE refund_requests SET state='completed', provider_refund_id = ?, completed_at = NOW(), cancel_token = NULL, updated_at = NOW() WHERE id = ? AND state IN ('processing','cooling_off')");
                        $upd->execute([$refundId, $argoId]);
                        if ($upd->rowCount() > 0) {
                            audit_log($pdo, (int)$rr['company_id'], 'completed', 'webhook', null, $argoId, null, [
                                'provider_refund_id' => $refundId,
                                'reconciled_via_webhook' => true,
                            ]);
                            $rr['state'] = 'completed';
                            $rr['provider_refund_id'] = $refundId;
                            refund_notify_completion($pdo, $rr);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log('SQUARE refund handler: ' . $e->getMessage());
        }
        break;

    default:
        break;
}

http_response_code(200);
echo json_encode(['received' => true]);
