<?php

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/email_sender.php';
require_once __DIR__ . '/env_helper.php';

/**
 * Marketing email contexts that users can independently subscribe to.
 * Maps context -> community_users.email_pref_<column>.
 */
function marketing_contexts(): array
{
    return [
        'product_updates'   => 'email_pref_product_updates',
        'tips_onboarding'   => 'email_pref_tips_onboarding',
        'reviews'           => 'email_pref_reviews',
        'promotions'        => 'email_pref_promotions',
        'community_digest'  => 'email_pref_community_digest',
    ];
}

/**
 * Decide whether a marketing email may be sent to $email for $context.
 *
 * Rules (see plan: Send-time gate):
 * - 'all_marketing' suppression always blocks.
 * - For 'reviews' to a license-key holder: allow unless explicitly suppressed
 *   for 'reviews' or 'all_marketing'. Community-user prefs are NOT consulted —
 *   purchase establishes an existing business relationship for one ask.
 * - For all other community-driven contexts (and reviews to non-license
 *   holders): require an opted-in community_users row.
 */
function should_send_marketing_email(string $email, string $context): bool
{
    global $pdo;

    $email = strtolower(trim($email));
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $contexts = marketing_contexts();
    if (!isset($contexts[$context])) {
        return false;
    }

    // Hard block: blanket suppression
    $stmt = $pdo->prepare("SELECT 1 FROM email_suppressions WHERE email = ? AND context = 'all_marketing' LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn()) {
        return false;
    }

    // Per-context suppression
    $stmt = $pdo->prepare('SELECT 1 FROM email_suppressions WHERE email = ? AND context = ? LIMIT 1');
    $stmt->execute([$email, $context]);
    if ($stmt->fetchColumn()) {
        return false;
    }

    // Reviews path: license-key holder gets the ask by default
    if ($context === 'reviews') {
        $stmt = $pdo->prepare('SELECT 1 FROM license_keys WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn()) {
            return true;
        }
    }

    // Community-driven path: must have opted in via community account
    $column = $contexts[$context];
    $stmt = $pdo->prepare("SELECT $column FROM community_users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $optedIn = $stmt->fetchColumn();

    return $optedIn === 1 || $optedIn === '1';
}

/**
 * Record that a marketing email was sent. Informational only — used for
 * debugging "did this email get sent?" later.
 */
function mark_marketing_sent(string $email, string $context, ?int $relatedId = null): void
{
    global $pdo;
    $stmt = $pdo->prepare('INSERT INTO email_marketing_log (email, context, related_id) VALUES (?, ?, ?)');
    $stmt->execute([strtolower(trim($email)), $context, $relatedId]);
}

/**
 * Lazily generate and persist a community-user unsubscribe token. Reused
 * across all marketing emails to that user so unsubscribe links remain stable.
 */
function ensure_unsubscribe_token(int $userId): ?string
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT email_pref_unsubscribe_token FROM community_users WHERE id = ?');
    $stmt->execute([$userId]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        return $existing;
    }

    $token = bin2hex(random_bytes(24));
    $stmt = $pdo->prepare('UPDATE community_users SET email_pref_unsubscribe_token = ? WHERE id = ?');
    $stmt->execute([$token, $userId]);
    return $token;
}

/**
 * Build the unsubscribe URL for a community-user-scoped marketing email.
 */
function community_user_unsubscribe_url(int $userId, string $context): ?string
{
    $token = ensure_unsubscribe_token($userId);
    if (!$token) {
        return null;
    }
    return site_url('/unsubscribe/marketing.php?u=' . urlencode($token) . '&c=' . urlencode($context));
}

/**
 * Build the unsubscribe URL for a license-only marketing email (review/feedback).
 * Generates and persists a per-license token if none exists yet.
 */
function license_unsubscribe_url(int $licenseId): ?string
{
    global $pdo;

    $stmt = $pdo->prepare('SELECT review_email_token FROM license_keys WHERE id = ?');
    $stmt->execute([$licenseId]);
    $token = $stmt->fetchColumn();

    if (!$token) {
        $token = bin2hex(random_bytes(24));
        $stmt = $pdo->prepare('UPDATE license_keys SET review_email_token = ? WHERE id = ?');
        $stmt->execute([$token, $licenseId]);
    }

    return site_url('/unsubscribe/marketing.php?l=' . urlencode($token));
}

/**
 * Send a review-request email to an active license-key holder.
 * Returns true on success. Caller is responsible for marking the license row.
 */
function send_review_request_email(int $licenseId, string $email): bool
{
    if (!should_send_marketing_email($email, 'reviews')) {
        return false;
    }

    $unsubscribe_url = license_unsubscribe_url($licenseId);
    $review_url = site_url('/review/');
    $unsubscribe_safe = htmlspecialchars($unsubscribe_url ?? '', ENT_QUOTES, 'UTF-8');
    $review_safe = htmlspecialchars($review_url, ENT_QUOTES, 'UTF-8');

    $body = <<<HTML
        <h2>How is Argo Books working for you?</h2>
        <p>Hey,</p>
        <p>Thanks for being an Argo Books customer. If the app has helped your bookkeeping, I'd really appreciate a short review on Capterra — it's the single biggest thing that helps other small businesses find Argo.</p>
        <p style="margin: 24px 0;">
            <a href="{$review_safe}" class="btn-primary" style="background:#2563eb;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;display:inline-block;">Leave a review</a>
        </p>
        <p>If something's getting in the way or you have feedback, just hit reply — I read every email.</p>
        <p>&mdash; Evan, Argo Books</p>
        <hr style="border:none;border-top:1px solid #e5e7eb;margin:32px 0 16px;">
        <p style="font-size:12px;color:#6b7280;">
            You're receiving this because you purchased an Argo Books license.
            <a href="{$unsubscribe_safe}">Unsubscribe</a>.
        </p>
        HTML;

    $sent = send_styled_email(
        $email,
        'How is Argo Books working for you?',
        $body,
        'blue',
        'noreply@argorobots.com',
        'Evan at Argo Books',
        'contact@argorobots.com'
    );

    if ($sent) {
        mark_marketing_sent($email, 'reviews', $licenseId);
    }
    return $sent;
}

/**
 * Send a feedback-request email to an inactive license-key holder.
 * Asks them to reply with what's getting in the way.
 */
function send_feedback_request_email(int $licenseId, string $email): bool
{
    if (!should_send_marketing_email($email, 'reviews')) {
        return false;
    }

    $unsubscribe_url = license_unsubscribe_url($licenseId);
    $unsubscribe_safe = htmlspecialchars($unsubscribe_url ?? '', ENT_QUOTES, 'UTF-8');

    $body = <<<HTML
        <h2>Quick check-in on Argo Books</h2>
        <p>Hey,</p>
        <p>I noticed it's been a little while since you used Argo Books. I wanted to check in — was there something that didn't work for you, or that's missing?</p>
        <p>Just reply to this email and let me know. Anything you tell me goes straight to me, and it genuinely helps me decide what to build next.</p>
        <p>You can also email me directly at <a href="mailto:contact@argorobots.com">contact@argorobots.com</a>.</p>
        <p>&mdash; Evan, Argo Books</p>
        <hr style="border:none;border-top:1px solid #e5e7eb;margin:32px 0 16px;">
        <p style="font-size:12px;color:#6b7280;">
            You're receiving this because you purchased an Argo Books license.
            <a href="{$unsubscribe_safe}">Unsubscribe</a>.
        </p>
        HTML;

    $sent = send_styled_email(
        $email,
        'Quick check-in on Argo Books',
        $body,
        'blue',
        'noreply@argorobots.com',
        'Evan at Argo Books',
        'contact@argorobots.com'
    );

    if ($sent) {
        mark_marketing_sent($email, 'reviews', $licenseId);
    }
    return $sent;
}
