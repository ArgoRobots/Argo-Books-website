<?php
// Local dev router for `php -S`. The built-in server ignores .htaccess entirely
// and skips a couple of things Apache does for free, so this puts them back.
// Only ever loaded by `php -S`; in production Apache reads .htaccess directly
// and this file is never executed.

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// 1. Deny rules from .htaccess. Without these, .env, the schema dump, vendor/
//    and secrets/ are all served as plain text over localhost.
$blocked = '#^/(vendor|secrets|database|cron/logs|admin/data-logs)/'
         . '|/\.env'
         . '|\.(sql|log|key|pem|ppk|p12|pfx|bak|old|orig|save|swp|dist)$'
         . '|^/composer\.(json|lock)$#i';
if (preg_match($blocked, $path)) {
    http_response_code(403);
    exit('Forbidden (blocked by router.php, mirroring .htaccess)');
}

// 2. Apache's mod_dir redirects /downloads to /downloads/ (DirectorySlash, on by
//    default; production answers 301). The built-in server instead serves the
//    directory's index.php in place, at the unslashed URL. The browser then
//    resolves that page's relative asset URLs one level too high: href="style.css"
//    becomes /style.css rather than /downloads/style.css, so every page-level
//    stylesheet 404s while the ../resources/ ones still happen to resolve. The
//    result is a half-styled page that looks like a CSS build failure. Send the
//    same 301 Apache would.
$decoded = rawurldecode($path);
if ($path !== '/'
    && substr($path, -1) !== '/'
    && strpos($decoded, '..') === false
    && is_dir(__DIR__ . $decoded)
) {
    $qs = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: ' . $path . '/' . ($qs !== '' ? '?' . $qs : ''), true, 301);
    exit;
}

// 3. When a path matches no file, the built-in server walks UP the tree looking for
//    an index.php and serves whatever it finds: a typo'd URL renders the homepage
//    with a 200. Apache 404s and serves ErrorDocument 404 /error-pages/404.html.
//    Without this, a broken link or bad redirect is invisible during local testing.
$target = __DIR__ . $decoded;
if ($path !== '/' && strpos($decoded, '..') === false) {
    $dir = rtrim($target, '/');
    $resolves = is_file($target)
        || (is_dir($target) && (is_file($dir . '/index.php') || is_file($dir . '/index.html')));
    if (!$resolves) {
        http_response_code(404);
        $errorPage = __DIR__ . '/error-pages/404.html';
        exit(is_file($errorPage) ? file_get_contents($errorPage) : 'Not Found');
    }
}

return false;
