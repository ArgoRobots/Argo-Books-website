<?php

/**
 * Shared flat-file rate limiting.
 *
 * Rate limits are stored in /resources/rate_limits/rate_limits.json, keyed by
 * a caller-supplied prefix + sha256(ip). Callers pass their own prefix so
 * buckets don't collide between admin login, portal token lookups, payment
 * endpoints, etc.
 */

require_once __DIR__ . '/env_helper.php';

/**
 * Resolve the client IP, trusting X-Forwarded-For only when the request
 * arrives from an IP listed in the TRUSTED_PROXY_IPS env var.
 */
function get_client_ip(): string
{
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $trustedProxyConfig = env('TRUSTED_PROXY_IPS', '');
    if (!empty($trustedProxyConfig) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $trustedProxies = array_map('trim', explode(',', $trustedProxyConfig));
        if (in_array($remoteAddr, $trustedProxies, true)) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
    }

    return $remoteAddr;
}

/**
 * Read rate limits file with exclusive lock to prevent TOCTOU race conditions.
 * Returns the parsed array and keeps the file handle open for atomic updates.
 *
 * @param int $windowSeconds Time window for cleanup
 * @return array{rateLimits: array, handle: resource|null}
 */
function read_rate_limits_locked(int $windowSeconds = 900): array
{
    $rateDir = __DIR__ . '/resources/rate_limits';
    if (!is_dir($rateDir)) {
        // `&& !is_dir($rateDir)` handles the race where a concurrent request
        // created the directory between our is_dir() check and mkdir().
        if (!mkdir($rateDir, 0755, true) && !is_dir($rateDir)) {
            error_log('rate_limit_helper: failed to create rate-limit storage dir: ' . $rateDir);
            return ['rateLimits' => [], 'handle' => null];
        }
    }
    $rateFile = $rateDir . '/rate_limits.json';
    $handle = fopen($rateFile, 'c+');
    if (!$handle) {
        return ['rateLimits' => [], 'handle' => null];
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return ['rateLimits' => [], 'handle' => null];
    }

    $content = stream_get_contents($handle);
    $rateLimits = json_decode($content, true) ?: [];

    // Clean up expired entries
    $now = time();
    foreach ($rateLimits as $key => $data) {
        if ($now - ($data['first_attempt'] ?? 0) > $windowSeconds) {
            unset($rateLimits[$key]);
        }
    }

    return ['rateLimits' => $rateLimits, 'handle' => $handle];
}

/**
 * Write rate limits and release the file lock.
 *
 * @param resource $handle File handle from read_rate_limits_locked
 * @param array $rateLimits Updated rate limits data
 */
function write_rate_limits_unlock($handle, array $rateLimits): void
{
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($rateLimits));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

/**
 * Check rate limiting for an IP address and action type.
 * Uses file locking to prevent race conditions under concurrent requests.
 *
 * @param string $ip Client IP address
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $windowSeconds Time window in seconds
 * @param string $prefix Key prefix for different rate limit buckets
 * @return bool True if rate limit exceeded
 */
function is_rate_limited(string $ip, int $maxAttempts = 10, int $windowSeconds = 900, string $prefix = 'portal'): bool
{
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    $key = $prefix . '_' . hash('sha256', $ip);
    $isLimited = isset($rateLimits[$key]) && $rateLimits[$key]['count'] >= $maxAttempts;

    if ($handle) {
        write_rate_limits_unlock($handle, $rateLimits);
    }

    return $isLimited;
}

/**
 * Record a rate-limited action attempt for an IP address.
 * Uses file locking to prevent race conditions under concurrent requests.
 *
 * $windowSeconds must match the window passed to is_rate_limited() by the same
 * caller — it controls which stale entries get pruned during the read. Passing
 * a smaller window here than is_rate_limited uses will silently shorten the
 * effective rate-limit window.
 *
 * @param string $ip Client IP address
 * @param string $prefix Key prefix for different rate limit buckets
 * @param int $windowSeconds Time window in seconds (must match is_rate_limited window)
 */
function record_rate_limit_attempt(string $ip, string $prefix = 'portal', int $windowSeconds = 900): void
{
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    if (!$handle) {
        return;
    }

    $now = time();
    $key = $prefix . '_' . hash('sha256', $ip);

    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'count' => 1,
            'first_attempt' => $now
        ];
    } else {
        $rateLimits[$key]['count']++;
    }

    write_rate_limits_unlock($handle, $rateLimits);
}

/**
 * Atomically check the rate limit AND record the attempt under a single lock.
 * Use for endpoints where TOCTOU between the check and the record could let
 * concurrent requests slip past the cap (e.g. admin login). Every call counts
 * toward the limit; pair with clear_rate_limit_attempts() on success if you
 * want successful actions to reset the counter.
 *
 * @return bool True if the limit was already exceeded BEFORE this call (the
 *              attempt is NOT counted in that case), false if the attempt was
 *              recorded and processing should continue.
 */
function check_and_record_rate_limit(string $ip, int $maxAttempts, int $windowSeconds, string $prefix): bool
{
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    if (!$handle) {
        // Fail open if the lock/file isn't usable so we don't lock real users out.
        return false;
    }

    $key = $prefix . '_' . hash('sha256', $ip);

    if (isset($rateLimits[$key]) && $rateLimits[$key]['count'] >= $maxAttempts) {
        write_rate_limits_unlock($handle, $rateLimits);
        return true;
    }

    $now = time();
    if (!isset($rateLimits[$key])) {
        $rateLimits[$key] = [
            'count' => 1,
            'first_attempt' => $now
        ];
    } else {
        $rateLimits[$key]['count']++;
    }

    write_rate_limits_unlock($handle, $rateLimits);
    return false;
}

/**
 * Clear all recorded attempts for this IP/prefix bucket.
 * Call on a successful action so legitimate users don't accumulate counts.
 */
function clear_rate_limit_attempts(string $ip, string $prefix, int $windowSeconds = 900): void
{
    $result = read_rate_limits_locked($windowSeconds);
    $rateLimits = $result['rateLimits'];
    $handle = $result['handle'];

    if (!$handle) {
        return;
    }

    $key = $prefix . '_' . hash('sha256', $ip);
    unset($rateLimits[$key]);

    write_rate_limits_unlock($handle, $rateLimits);
}
