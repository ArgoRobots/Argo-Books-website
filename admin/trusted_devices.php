<?php
/**
 * Trusted device helpers — let an admin skip the TOTP step on devices they've
 * opted in on, for 30 days. The password step is always still required.
 *
 * Storage uses a split-token pattern (selector + validator), the same approach
 * Symfony and Spring Security use for "remember me":
 *
 *   cookie value  = "<selector>.<validator>"
 *   DB row        = (selector plaintext for O(1) lookup, validator_hash = sha256(validator))
 *
 * A database read therefore cannot grant authentication on its own — the
 * attacker would need the validator from the user's cookie. hash_equals() is
 * used on the validator compare to avoid timing leaks.
 */

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../env_helper.php';

const ADMIN_TRUST_COOKIE = 'admin_trust_token';
const ADMIN_TRUST_TTL_DAYS = 30;

/**
 * Issue a new trusted-device token for $user_id, persist the hashed row, and
 * set the cookie on the response.
 */
function issue_trusted_device_token($user_id, $user_agent, $ip)
{
    global $pdo;

    $selector  = bin2hex(random_bytes(8));   // 16 hex chars
    $validator = bin2hex(random_bytes(32));  // 64 hex chars (256 bits)
    $validator_hash = hash('sha256', $validator);

    $label = derive_device_label($user_agent);

    // Fail closed: if the INSERT throws (missing migration, transient DB
    // error, etc.) we don't set the cookie. The caller's login proceeds
    // normally; the user just doesn't gain trusted-device status this time.
    try {
        $stmt = $pdo->prepare(
            'INSERT INTO admin_trusted_devices
                (user_id, selector, validator_hash, label, user_agent, ip_address, expires_at)
             VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL ? DAY))'
        );
        $stmt->execute([
            $user_id,
            $selector,
            $validator_hash,
            $label,
            substr((string)$user_agent, 0, 255),
            substr((string)$ip, 0, 45),
            ADMIN_TRUST_TTL_DAYS,
        ]);
    } catch (Throwable $e) {
        error_log('issue_trusted_device_token failed: ' . $e->getMessage());
        return;
    }

    $secure = env('APP_ENV', 'sandbox') === 'production';
    setcookie(ADMIN_TRUST_COOKIE, $selector . '.' . $validator, [
        'expires'  => time() + (ADMIN_TRUST_TTL_DAYS * 86400),
        'path'     => '/admin/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}

/**
 * Returns the username (canonical case) the current cookie is valid for, or
 * null. On success, last_used_at + ip_address are bumped.
 *
 * Returns null silently for any malformed or expired cookie; the caller just
 * falls through to the normal TOTP flow.
 */
function verify_trusted_device_cookie($ip)
{
    global $pdo;

    $raw = $_COOKIE[ADMIN_TRUST_COOKIE] ?? '';
    if (!is_string($raw) || strpos($raw, '.') === false) {
        return null;
    }

    [$selector, $validator] = explode('.', $raw, 2);
    if (!preg_match('/^[a-f0-9]{16}$/', $selector) || !preg_match('/^[a-f0-9]{64}$/', $validator)) {
        return null;
    }

    // Trust-cookie verification must NEVER throw — that would block the admin
    // from logging in entirely if the table is missing or the DB hiccups. Any
    // error here falls through to the normal TOTP prompt.
    try {
        $stmt = $pdo->prepare(
            'SELECT d.id, d.validator_hash, d.expires_at, u.username
             FROM admin_trusted_devices d
             JOIN admin_users u ON u.id = d.user_id
             WHERE d.selector = ? AND d.expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute([$selector]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        if (!hash_equals($row['validator_hash'], hash('sha256', $validator))) {
            return null;
        }

        $upd = $pdo->prepare(
            'UPDATE admin_trusted_devices
             SET last_used_at = NOW(), ip_address = ?
             WHERE id = ?'
        );
        $upd->execute([substr((string)$ip, 0, 45), $row['id']]);

        return $row['username'];
    } catch (Throwable $e) {
        error_log('verify_trusted_device_cookie failed: ' . $e->getMessage());
        return null;
    }
}

/** Revoke a single device, scoped to the owning user. */
function revoke_trusted_device($user_id, $device_id)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM admin_trusted_devices WHERE id = ? AND user_id = ?');
    $stmt->execute([$device_id, $user_id]);
    return $stmt->rowCount() > 0;
}

/** Revoke every trusted device for a user. Returns the count removed. */
function revoke_all_trusted_devices($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM admin_trusted_devices WHERE user_id = ?');
    $stmt->execute([$user_id]);
    return $stmt->rowCount();
}

/** Expire the cookie on the client. Safe to call even if no cookie is set. */
function clear_trusted_device_cookie()
{
    $secure = env('APP_ENV', 'sandbox') === 'production';
    setcookie(ADMIN_TRUST_COOKIE, '', [
        'expires'  => time() - 3600,
        'path'     => '/admin/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}

/** List active trusted devices for a user, most recently used first. */
function list_trusted_devices($user_id)
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT id, label, user_agent, ip_address, created_at, last_used_at, expires_at
         FROM admin_trusted_devices
         WHERE user_id = ? AND expires_at > NOW()
         ORDER BY last_used_at DESC'
    );
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

/**
 * Best-effort human label from the User-Agent string. UA strings are unreliable
 * for security decisions, but they're fine as a display hint so an admin can
 * tell their devices apart when revoking.
 */
function derive_device_label($user_agent)
{
    $ua = (string)$user_agent;
    if ($ua === '') return 'Unknown device';

    if (stripos($ua, 'Edg/') !== false)         $browser = 'Edge';
    elseif (stripos($ua, 'Chrome/') !== false)  $browser = 'Chrome';
    elseif (stripos($ua, 'Firefox/') !== false) $browser = 'Firefox';
    elseif (stripos($ua, 'Safari/') !== false)  $browser = 'Safari';
    else                                        $browser = 'Browser';

    if (stripos($ua, 'Windows') !== false)   $os = 'Windows';
    elseif (stripos($ua, 'Mac OS') !== false) $os = 'macOS';
    elseif (stripos($ua, 'Android') !== false) $os = 'Android';
    elseif (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) $os = 'iOS';
    elseif (stripos($ua, 'Linux') !== false)  $os = 'Linux';
    else $os = 'device';

    return "$browser on $os";
}
