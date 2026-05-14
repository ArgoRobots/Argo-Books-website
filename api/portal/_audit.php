<?php
declare(strict_types=1);

/**
 * Append-only audit log for refund and email-change events.
 * Single entry point — every state-mutating code path MUST call this.
 *
 * @param string $event_type one of: request_created, code_sent, code_failed,
 *   code_verified, cooling_off_started, cancelled_by_user, cancelled_by_admin,
 *   cancelled_by_system, cancelled_by_email_link, processing, completed,
 *   failed, provider_pending, velocity_tier_assigned, email_change_requested,
 *   email_change_old_verified, email_change_new_verified, email_changed,
 *   email_reverted, email_registration_verified, account_locked, account_unlocked
 * @param string $actor_type owner|admin|system|webhook
 *   actor_type and event_type must agree: 'cancelled_by_user' implies actor
 *   'owner', 'cancelled_by_admin' implies 'admin', 'cancelled_by_system'
 *   implies 'system' (cron auto-cancel, brute-force cancel).
 */
function audit_log(
    PDO $pdo,
    int $company_id,
    string $event_type,
    string $actor_type,
    ?string $actor_id = null,
    ?int $refund_request_id = null,
    ?int $email_change_request_id = null,
    array $payload = [],
    ?string $ip = null,
    ?string $ua = null
): int {
    $stmt = $pdo->prepare("
        INSERT INTO refund_audit_log
            (company_id, refund_request_id, email_change_request_id,
             event_type, payload_json, actor_type, actor_id, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $company_id,
        $refund_request_id,
        $email_change_request_id,
        $event_type,
        $payload ? json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
        $actor_type,
        $actor_id,
        $ip,
        $ua,
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Pull request context (IP + user-agent) for audit entries.
 */
function audit_request_context(): array {
    return [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255) ?: null,
    ];
}
