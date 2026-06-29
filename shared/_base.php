<?php
// shared/_base.php
// Auto-detected URL prefix that maps to this project's root.
//
// In production at argorobots.com (DocumentRoot is the project root): ''.
// On Laragon at localhost/argo-books-website/ (DocumentRoot is c:/laragon/www):
// '/argo-books-website'.
//
// Use INVGEN_BASE to prefix every site-absolute asset path so the same code
// works in both layouts. JS modules read the same prefix from
// window.INVGEN_BASE, which layout.php injects as a tiny inline script.

if (!defined('INVGEN_BASE')) {
    $project_root = realpath(__DIR__ . '/..');
    $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';

    $base = '';
    if ($doc_root && $project_root && strpos($project_root, $doc_root) === 0) {
        $relative = substr($project_root, strlen($doc_root));
        $relative = str_replace('\\', '/', $relative);
        $relative = rtrim($relative, '/');
        if ($relative !== '' && $relative[0] !== '/') {
            $relative = '/' . $relative;
        }
        $base = $relative;
    }
    define('INVGEN_BASE', $base);
}

/**
 * Emit a minimal 404 page used by the niche / template / article routers
 * when a slug does not resolve to a data file. Intentionally bare: never
 * touches the layout helper or any data the caller may not have.
 *
 * $h1 is also used as the <title> (suffix " | Argo Books" appended).
 * $body_paragraph_html is interpolated after the H1, so the caller can pass
 * a link to the most useful fallback for that section.
 */
function invgen_render_404(string $h1, string $body_paragraph_html): void
{
    http_response_code(404);
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
    }
    $title = htmlspecialchars($h1) . ' | Argo Books';
    $h1_safe = htmlspecialchars($h1);
    ?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= $title ?></title>
<meta name="robots" content="noindex">
</head>
<body>
<h1><?= $h1_safe ?></h1>
<?= $body_paragraph_html ?>
</body>
</html>
<?php
}
