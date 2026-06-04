<?php
/**
 * dev-gate.php - password wall for the staging site.
 *
 * Loaded via auto_prepend_file on dev.argorobots.com ONLY (set in cPanel's
 * MultiPHP INI Editor for that domain). It must never affect production:
 *   1. production's PHP config does not prepend this file, and
 *   2. the host guard below makes it a no-op on any host except dev.
 *
 * It uses HTTP Basic Auth, so the browser shows a username/password popup and
 * remembers the credentials for the rest of the browser session.
 */

if (($_SERVER['HTTP_HOST'] ?? '') === 'dev.argorobots.com') {

    $expected_user = 'Admin';
    // bcrypt hash of the staging password (verified, never stored as plaintext).
    $expected_hash = '$2y$10$QneDZeVbh8CuP.HLX2OxiuilopIPwoJNdaQQ91f4KkfapL83khUqm';

    $user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $pass = $_SERVER['PHP_AUTH_PW'] ?? '';

    // Under PHP-FPM/CGI, PHP_AUTH_* is often not populated. Fall back to the
    // raw Authorization header (forwarded to PHP by the rule in .htaccess).
    if ($user === '' && $pass === '') {
        $auth = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        if (stripos($auth, 'Basic ') === 0) {
            $decoded = base64_decode(substr($auth, 6), true);
            if ($decoded !== false && strpos($decoded, ':') !== false) {
                [$user, $pass] = explode(':', $decoded, 2);
            }
        }
    }

    $ok = hash_equals($expected_user, $user) && password_verify($pass, $expected_hash);

    if (!$ok) {
        header('WWW-Authenticate: Basic realm="Argo Books Staging"');
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Staging area. Authentication required.';
        exit;
    }
}
