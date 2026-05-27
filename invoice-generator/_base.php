<?php
// invoice-generator/_base.php
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
