<?php

/**
 * Read an environment variable with a fallback default.
 *
 * Always checks both $_ENV and getenv() because $_ENV is only populated when
 * php.ini's variables_order includes "E", which is not guaranteed on every host.
 *
 * Returns the raw env value when the variable is set, even if it's "0" or "".
 * Only falls back to $default when the variable is truly unset — this matters
 * for boolean-style vars like INVOICE_LOG_ENABLED=0.
 *
 * @param string $key
 * @param mixed $default Returned when the variable is not set at all
 * @return mixed
 */
function env(string $key, $default = '')
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

/**
 * Build an absolute URL to a path on the site.
 *
 * Uses the SITE_URL env var (default: https://argorobots.com) so this works in
 * cron contexts where $_SERVER is empty. For web-request contexts where the
 * scheme/host must be detected from the request, use get_site_url() in
 * community/community_functions.php.
 */
function site_url(string $path = ''): string
{
    $base = rtrim(env('SITE_URL', 'https://argorobots.com'), '/');
    if ($path === '') {
        return $base;
    }
    return $base . '/' . ltrim($path, '/');
}
