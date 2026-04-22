<?php

/**
 * Read an environment variable with a fallback default.
 *
 * Always checks both $_ENV and getenv() because $_ENV is only populated when
 * php.ini's variables_order includes "E", which is not guaranteed on every host.
 *
 * @param string $key
 * @param mixed $default Returned when the variable is unset or empty
 * @return mixed
 */
function env(string $key, $default = '')
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
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
