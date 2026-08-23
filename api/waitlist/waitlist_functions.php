<?php
/**
 * Platform-waitlist logic, separated from the HTTP endpoint
 * (api/waitlist/subscribe.php) so it can be unit-tested directly.
 */

const WAITLIST_PLATFORMS = ['macos', 'linux'];
const WAITLIST_MAX_PER_IP_PER_HOUR = 5;

/**
 * Handle a waitlist signup.
 *
 * @param array $input Request fields:
 *                       - email    (string) required
 *                       - platform (string) one of WAITLIST_PLATFORMS, default 'macos'
 *                       - website  (string) honeypot; non-empty means bot
 * @param array $ctx   Request context resolved by the endpoint:
 *                       - ip_address  (?string)
 *                       - user_agent  (?string)
 *                       - visitor_id  (?string) validated argo_visitor_id cookie
 *                       - source_code (?string) first-touch referral source
 *
 * @return array{status: int, body: array} HTTP status + JSON-ready body
 */
function waitlist_subscribe(array $input, array $ctx = []): array
{
    global $pdo;

    // Honeypot filled: a bot autofilled the hidden field. Pretend success and
    // write nothing, so the bot learns nothing from the response.
    if (!empty($input['website'])) {
        return ['status' => 200, 'body' => ['success' => true]];
    }

    $email = strtolower(trim((string)($input['email'] ?? '')));
    if ($email === '' || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['status' => 400, 'body' => ['success' => false, 'error' => 'Please enter a valid email address.']];
    }

    $platform = (string)($input['platform'] ?? 'macos');
    if (!in_array($platform, WAITLIST_PLATFORMS, true)) {
        return ['status' => 400, 'body' => ['success' => false, 'error' => 'Invalid platform.']];
    }

    if (!$pdo) {
        return ['status' => 500, 'body' => ['success' => false, 'error' => 'Something went wrong. Please try again.']];
    }

    $ip = $ctx['ip_address'] ?? null;
    if ($ip !== null && $ip !== '') {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM platform_waitlist
              WHERE ip_address = ? AND created_at >= NOW() - INTERVAL 1 HOUR'
        );
        $stmt->execute([$ip]);
        if ((int)$stmt->fetchColumn() >= WAITLIST_MAX_PER_IP_PER_HOUR) {
            return ['status' => 429, 'body' => ['success' => false, 'error' => 'Too many signups from this connection. Please try again later.']];
        }
    }

    try {
        // Duplicate email for the same platform + environment is a silent no-op
        // (unique key), and the response stays success so the form never leaks
        // whether an address was already subscribed.
        $stmt = $pdo->prepare(
            'INSERT INTO platform_waitlist
                (email, platform, visitor_id, source_code, ip_address, user_agent, environment)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE id = id'
        );
        $stmt->execute([
            $email,
            $platform,
            $ctx['visitor_id'] ?? null,
            $ctx['source_code'] ?? null,
            $ip,
            isset($ctx['user_agent']) ? substr((string)$ctx['user_agent'], 0, 255) : null,
            current_environment(),
        ]);
    } catch (PDOException $e) {
        error_log('waitlist_subscribe failed: ' . $e->getMessage());
        return ['status' => 500, 'body' => ['success' => false, 'error' => 'Something went wrong. Please try again.']];
    }

    return ['status' => 200, 'body' => ['success' => true]];
}
