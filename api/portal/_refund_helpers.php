<?php
declare(strict_types=1);

require_once __DIR__ . '/_audit.php';
require_once __DIR__ . '/../../email_sender.php';

// ---------------------------------------------------------------
// Code generation, hashing, and email helpers
// ---------------------------------------------------------------

/** 6-digit zero-padded cryptographically-random verification code. */
function refund_generate_code(): string {
    return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * SHA-256 of (code || '|' || salt). The salt is the request_id (or change_id)
 * so a DB dump can't be precomputed.
 */
function refund_hash_code(string $code, string $salt): string {
    return hash('sha256', $code . '|' . $salt);
}

/** Mask an email for display (ev**@argobooks.app). */
function refund_mask_email(string $email): string {
    $at = strpos($email, '@');
    if ($at === false) return $email;
    $local = substr($email, 0, $at);
    $domain = substr($email, $at + 1);
    if (strlen($local) <= 2) return $local[0] . '***@' . $domain;
    return substr($local, 0, 2) . str_repeat('*', max(1, strlen($local) - 2)) . '@' . $domain;
}

// ---------------------------------------------------------------
// State-machine guards
// ---------------------------------------------------------------

/**
 * Reject the request with 409 if the current state isn't in $allowed.
 * Echoes JSON body and calls exit() — caller does not return.
 */
function refund_assert_state(string $current, array $allowed, string $action): void {
    if (!in_array($current, $allowed, true)) {
        http_response_code(409);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'ILLEGAL_STATE_TRANSITION',
            'message' => "Cannot $action while in state $current",
            'current_state' => $current,
            'allowed_states' => $allowed,
        ]);
        exit;
    }
}

/**
 * Gate /refunds/* endpoints: account must be unlocked AND email-verified.
 * Echoes JSON + exits on failure.
 */
function refund_ensure_company_active(array $company): void {
    if (!empty($company['locked'])) {
        http_response_code(423);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'ACCOUNT_LOCKED',
            'message' => $company['lock_reason'] ?: 'Refunds are temporarily disabled. Contact support.',
        ]);
        exit;
    }
    if (empty($company['email_verified_at'])) {
        http_response_code(412);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'EMAIL_NOT_VERIFIED',
            'message' => 'Verify your email before issuing refunds.',
        ]);
        exit;
    }
    // Refund verification codes are emailed to owner_email. Without it, the
    // code goes nowhere and the user is stranded on the verify-code step.
    // Catch this case explicitly with a clear message rather than silently
    // issuing a code that can't be delivered.
    if (empty($company['owner_email'])) {
        http_response_code(412);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'OWNER_EMAIL_NOT_SET',
            'message' => 'No owner email is on file for this portal account, so we can\'t send the refund verification code. Set your owner email in Settings → Payment Portal first.',
        ]);
        exit;
    }
}

/** Load a refund_request scoped to the company; 404 + exit if missing. */
function refund_load_request(PDO $pdo, int $company_id, int $request_id): array {
    $stmt = $pdo->prepare("SELECT * FROM refund_requests WHERE id = ? AND company_id = ?");
    $stmt->execute([$request_id, $company_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'REQUEST_NOT_FOUND']);
        exit;
    }
    return $row;
}

// ---------------------------------------------------------------
// Email senders for refund flow
// ---------------------------------------------------------------

function refund_email_send_code(string $to, string $code, string $invoice_number, int $amount_cents, string $currency): void {
    $amount_str = htmlspecialchars(number_format($amount_cents / 100, 2) . ' ' . $currency);
    $invoice_safe = htmlspecialchars($invoice_number);
    $subject = "Argo Books refund code: $code";
    $body = <<<HTML
        <p>Your refund verification code is:</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:6px;">$code</p>
        <p>You're refunding <strong>$amount_str</strong> on invoice <strong>$invoice_safe</strong>.</p>
        <p>The code expires in 10 minutes. If you didn't request this refund, ignore this email and the request will expire.</p>
HTML;
    send_styled_email($to, $subject, $body, 'blue');
}

function refund_email_send_issued(string $to, array $req): void {
    $amount_str = htmlspecialchars(number_format($req['amount_cents'] / 100, 2) . ' ' . $req['currency']);
    $invoice_safe = htmlspecialchars($req['invoice_number']);
    $subject = "Refund issued: $invoice_safe";
    $body = <<<HTML
        <p>A refund of <strong>$amount_str</strong> was issued on invoice <strong>$invoice_safe</strong>.</p>
        <p>The customer will see the money returned to their original payment method within 5–10 business days.</p>
HTML;
    send_styled_email($to, $subject, $body, 'blue');
}

/**
 * Notification sent to the *customer* when a refund completes. Distinct from
 * refund_email_send_issued (which goes to the business owner) — wording is
 * customer-facing and tells them what to expect in their bank/card account.
 */
function refund_email_send_customer_refunded(string $to, ?string $customer_name, string $invoice_number, int $amount_cents, string $currency, ?string $business_name = null, ?string $reason = null): void {
    $amount_str    = htmlspecialchars(number_format($amount_cents / 100, 2) . ' ' . $currency);
    $invoice_safe  = htmlspecialchars($invoice_number);
    $greeting      = !empty($customer_name) ? 'Hi ' . htmlspecialchars($customer_name) . ',' : 'Hi,';
    $business_safe = !empty($business_name) ? htmlspecialchars($business_name) : 'the merchant';
    $subject       = "Your refund for invoice $invoice_safe has been issued";

    // Optional reason from the merchant — surfaced in a quoted block so the
    // customer sees the explanation the business owner typed. The UI tells
    // the owner that this will be shown.
    $reason_html = '';
    if (!empty($reason)) {
        $reason_safe = nl2br(htmlspecialchars(trim($reason)));
        $reason_html = <<<HTML
        <p>Reason from $business_safe:</p>
        <blockquote style="border-left:3px solid #d1d5db;padding:8px 12px;margin:0 0 16px 0;color:#374151;background:#f9fafb;">
            $reason_safe
        </blockquote>
HTML;
    }

    $body = <<<HTML
        <p>$greeting</p>
        <p>A refund of <strong>$amount_str</strong> for invoice <strong>$invoice_safe</strong> has been issued by $business_safe.</p>
        $reason_html
        <p>The money will be returned to the same payment method you originally used. Most banks and card networks post the refund within 5–10 business days; some are faster.</p>
        <p>If you don't see the refund after 10 business days, please contact $business_safe directly.</p>
HTML;
    send_styled_email($to, $subject, $body, 'blue');
}

/**
 * Single point that fires *all* notifications when a refund_request transitions
 * to 'completed'. Always emails the business owner; additionally emails the
 * customer if portal_invoices has a customer_email on file. Safe to call
 * multiple times — the underlying mailer sends per call, so callers should
 * only invoke this once per actual state transition (i.e. inside the same
 * `if (state was not yet completed)` guard that issues the UPDATE).
 */
function refund_notify_completion(PDO $pdo, array $req): void {
    $stmt = $pdo->prepare("SELECT owner_email, company_name FROM portal_companies WHERE id = ?");
    $stmt->execute([$req['company_id']]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$company) return;

    if (!empty($company['owner_email'])) {
        refund_email_send_issued($company['owner_email'], $req);
    }

    // Look up the customer's email + name from the matching portal_invoices row.
    $istmt = $pdo->prepare("SELECT customer_email, customer_name FROM portal_invoices WHERE company_id = ? AND invoice_id = ? LIMIT 1");
    $istmt->execute([$req['company_id'], $req['invoice_id']]);
    $invoice = $istmt->fetch(PDO::FETCH_ASSOC);
    if ($invoice && !empty($invoice['customer_email'])) {
        try {
            refund_email_send_customer_refunded(
                $invoice['customer_email'],
                $invoice['customer_name'] ?? null,
                (string)$req['invoice_number'],
                (int)$req['amount_cents'],
                (string)$req['currency'],
                $company['company_name'] ?? null,
                $req['reason'] ?? null
            );
            audit_log($pdo, (int)$req['company_id'], 'customer_notified', 'system', null, (int)$req['id'], null, [
                'to' => $invoice['customer_email'],
            ]);
        } catch (\Throwable $e) {
            // Never let an SMTP hiccup roll back the refund. Log and move on;
            // the owner has already been notified, so the operator can manually
            // follow up with the customer if needed.
            error_log('refund_notify_completion customer email failed: ' . $e->getMessage());
        }
    }
}

// ---------------------------------------------------------------
// Email senders for email-verification + email-change flows
// ---------------------------------------------------------------

function refund_email_send_registration_code(string $to, string $code): void {
    $body = <<<HTML
        <p>Welcome to the Argo Books payment portal.</p>
        <p>Confirm your email with this code:</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:6px;">$code</p>
        <p>The code expires in 10 minutes. You'll need it to issue refunds and manage your account.</p>
HTML;
    send_styled_email($to, "Confirm your Argo Books email", $body, 'blue');
}

function refund_email_send_change_old_code(string $to, string $code, string $new_email): void {
    $new_safe = htmlspecialchars($new_email);
    $body = <<<HTML
        <p>Someone (hopefully you) requested an email change on your Argo Books account from this address to <strong>$new_safe</strong>.</p>
        <p>Confirm with this code:</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:6px;">$code</p>
        <p>If this wasn't you, ignore this email and the request will expire. The change cannot proceed without this code.</p>
HTML;
    send_styled_email($to, "Confirm email change to $new_safe", $body, 'purple');
}

function refund_email_send_change_new_code(string $to, string $code): void {
    $body = <<<HTML
        <p>You're being added as the new owner email for an Argo Books portal account.</p>
        <p>Confirm with this code:</p>
        <p style="font-size:28px;font-weight:bold;letter-spacing:6px;">$code</p>
        <p>If you didn't expect this, ignore this email — without confirmation the change cannot complete.</p>
HTML;
    send_styled_email($to, "Confirm this is your new Argo Books email", $body, 'blue');
}

function refund_email_send_change_completed_to_old(string $old, string $new, string $revert_token): void {
    $base = rtrim($_ENV['SITE_URL'] ?? 'https://argobooks.app', '/');
    $url = $base . '/api/portal/revert-email.php?token=' . urlencode($revert_token);
    $new_safe = htmlspecialchars($new);
    $body = <<<HTML
        <p>Your Argo Books portal email was changed from this address to <strong>$new_safe</strong>.</p>
        <p>If this wasn't you, click the button below to revert. You have 30 days.</p>
        <p><a href="$url" style="background:#dc2626;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Revert email change</a></p>
HTML;
    send_styled_email($old, "Your Argo Books email was changed", $body, 'purple');
}

function refund_email_send_cooling_off(string $to, array $req, string $token): void {
    $base = rtrim($_ENV['SITE_URL'] ?? 'https://argobooks.app', '/');
    $url = $base . '/api/portal/cancel-refund.php?token=' . urlencode($token);
    $amount_str = htmlspecialchars(number_format($req['amount_cents'] / 100, 2) . ' ' . $req['currency']);
    $invoice_safe = htmlspecialchars($req['invoice_number']);
    $subject = "Refund pending review: $amount_str on invoice $invoice_safe";
    $body = <<<HTML
        <p>We're holding this refund of <strong>$amount_str</strong> on invoice <strong>$invoice_safe</strong> for a brief review window because it triggered our anomaly checks.</p>
        <p>If you didn't initiate it or want to cancel, click the button below within 15 minutes:</p>
        <p><a href="$url" style="background:#dc2626;color:#fff;padding:10px 18px;border-radius:6px;text-decoration:none;">Cancel this refund</a></p>
        <p>Otherwise it will be sent to the customer automatically.</p>
HTML;
    send_styled_email($to, $subject, $body, 'purple');
}

// ---------------------------------------------------------------
// Provider execution dispatch
// ---------------------------------------------------------------

/**
 * Invoke the provider's refund API for a request currently in 'processing'.
 * Updates state to 'completed' (with provider_refund_id) on success or
 * 'failed' (with state_reason) on error.
 */
function refund_execute_against_provider(PDO $pdo, array $company, int $request_id): void {
    $stmt = $pdo->prepare("SELECT * FROM refund_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $req = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$req) return;

    try {
        switch ($req['provider']) {
            case 'stripe':
                require_once __DIR__ . '/refunds/_provider_stripe.php';
                $result = refund_stripe_issue($company, $req);
                $refund_id = $result['id'] ?? null;
                break;
            case 'paypal':
                require_once __DIR__ . '/refunds/_provider_paypal.php';
                $result = refund_paypal_issue($company, $req);
                $refund_id = $result['id'] ?? null;
                break;
            case 'square':
                require_once __DIR__ . '/refunds/_provider_square.php';
                $result = refund_square_issue($company, $req);
                $refund_id = $result['refund']['id'] ?? null;
                break;
            default:
                throw new RuntimeException("Unsupported provider: {$req['provider']}");
        }

        $pdo->prepare("UPDATE refund_requests SET state='completed', provider_refund_id = ?, completed_at = NOW(), updated_at = NOW() WHERE id = ?")
            ->execute([$refund_id, $request_id]);
        audit_log($pdo, (int)$company['id'], 'completed', 'system', null, $request_id, null, [
            'provider_refund_id' => $refund_id,
        ]);
        // Re-fetch with the now-completed state so the notification reflects
        // the final values (provider_refund_id, completed_at).
        $req['state'] = 'completed';
        $req['provider_refund_id'] = $refund_id;
        // Notification is best-effort — Stripe has the money back already.
        // If we let an SMTP/SQL hiccup propagate, the outer catch would mark
        // the refund as 'failed' and the books would diverge from Stripe.
        try { refund_notify_completion($pdo, $req); }
        catch (\Throwable $notifyEx) {
            error_log('refund_notify_completion threw after completed refund #' . $request_id . ': ' . $notifyEx->getMessage());
        }

        // The provider's webhook (charge.refunded for Stripe; PAYMENT.CAPTURE.REFUNDED
        // for PayPal; refund.created for Square) is the canonical source for the
        // negative-amount portal_payments row + invoice balance flip + original
        // payment status update — all already wired up in api/portal/webhooks/.
        // The webhook also reconciles refund_requests state to 'completed' (no-op
        // if we already set it here). Inserting from this path AND the webhook
        // would double-count the refund in books. Webhooks usually arrive within
        // seconds; the desktop sees the refund on its next payments-sync after that.

    } catch (\Throwable $e) {
        $msg = $e->getMessage();
        $pdo->prepare("UPDATE refund_requests SET state='failed', state_reason = ?, updated_at = NOW() WHERE id = ?")
            ->execute([substr($msg, 0, 1000), $request_id]);
        audit_log($pdo, (int)$company['id'], 'failed', 'system', null, $request_id, null, ['error' => $msg]);
    }
}

// Note: portal_payments insertion is handled exclusively by the provider
// webhook handlers (api/portal/webhooks/) to avoid double-counting refunds
// in books. See refund_execute_against_provider above for rationale.
