<?php

/**
 * Founder self-exclusion — the single source of truth for "which app installs are
 * the founder's own", so the founder's own testing never pollutes app telemetry,
 * crash reports, or the app-stats dashboards.
 *
 * Identities are the telemetry/crash authIds the desktop endpoints authenticate
 * as: 'subscription:PREM-...' for premium installs, 'device:<hash>' for free ones.
 * They are configured ONCE in .env as EXCLUDED_AUTH_IDS (comma-separated) and read
 * only through this helper, so there is one place to manage them.
 *
 * Scope: this covers the app-data pipelines (telemetry + crashes) only. Website
 * page-view / referral exclusion is a separate, IP-based mechanism configured via
 * EXCLUDED_TRACKING_IPS and enforced by is_nontracked_ip() in statistics.php.
 *
 * Enforced at BOTH ends:
 *   - write time: api/data/upload.php and api/data/crash.php drop the data before
 *     it is ever written to disk.
 *   - read time: the admin/app-stats dashboards skip any matching files that are
 *     already on disk (e.g. uploaded before an id was added here), so historical
 *     founder installs disappear from the counts without hand-deleting each one.
 *
 * Requires .env to already be loaded (callers load it via Dotenv or db_connect).
 */

if (!function_exists('excluded_auth_ids')) {
    /**
     * Parsed list of excluded authIds from the EXCLUDED_AUTH_IDS env var
     * (comma-separated; surrounding whitespace and empty entries are ignored).
     *
     * @return string[]
     */
    function excluded_auth_ids(): array
    {
        static $ids = null;
        if ($ids !== null) {
            return $ids;
        }

        $raw = $_ENV['EXCLUDED_AUTH_IDS'] ?? getenv('EXCLUDED_AUTH_IDS');
        if (!is_string($raw) || trim($raw) === '') {
            return $ids = [];
        }

        $ids = [];
        foreach (explode(',', $raw) as $part) {
            $part = trim($part);
            if ($part !== '') {
                $ids[] = $part;
            }
        }
        return $ids;
    }
}

if (!function_exists('is_excluded_auth_id')) {
    /**
     * True if the given telemetry/crash authId belongs to the founder and should
     * be dropped (write time) or hidden (read time). A missing/empty authId is
     * never treated as excluded.
     */
    function is_excluded_auth_id(?string $authId): bool
    {
        if ($authId === null || $authId === '') {
            return false;
        }
        return in_array($authId, excluded_auth_ids(), true);
    }
}
