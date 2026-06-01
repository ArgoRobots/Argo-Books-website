<?php
// Resolves the URL base path for the current environment so server-rendered
// links work both in production (served at the domain root) and in local
// subfolder setups (e.g. http://localhost/argo-books-website/).
//
// It compares the application's filesystem root to the web server's document
// root: when the app IS the document root the base is "/", otherwise it's the
// subfolder prefix (with a trailing slash). This mirrors the old getBasePath()
// logic that main.js used, but resolved on the server so the markup is correct
// in the initial HTML.

if (!function_exists('site_base_path')) {
    function site_base_path(): string
    {
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
        // This file lives in resources/includes/, so two levels up is the app root.
        $appRoot = rtrim(str_replace('\\', '/', dirname(__DIR__, 2)), '/');

        if ($docRoot !== '' && strncmp($appRoot, $docRoot, strlen($docRoot)) === 0) {
            $sub = substr($appRoot, strlen($docRoot));
            return $sub === '' ? '/' : $sub . '/';
        }

        return '/';
    }
}
