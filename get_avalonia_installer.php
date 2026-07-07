<?php
/**
 * Serves Argo Books Avalonia (cross-platform) installer files.
 *
 * Filesystem layout:
 *   resources/downloads/{version}/
 *     Argo Books Installer V.{version}.exe        (Windows)
 *     ArgoBooks-{version}-osx-arm64.zip            (macOS, future)
 *     ArgoBooks-{version}-linux-x64.AppImage       (Linux, future)
 *
 * URL examples:
 *   /download/avalonia/win           -> latest Windows installer
 *   /download/avalonia/mac           -> latest macOS zip
 *   /download/avalonia/linux         -> latest Linux AppImage
 *   /download/avalonia/2.1.0/win     -> specific version Windows installer
 */
session_start();
require_once __DIR__ . '/statistics.php';
require_once __DIR__ . '/track_referral_event.php';

// Platform file patterns: platform key => filename pattern
// {version} is replaced at runtime
$platformPatterns = [
    'win'   => 'Argo Books Installer V.{version}.exe',
    'mac'   => 'ArgoBooks-{version}-osx-arm64.zip',
    'linux' => 'ArgoBooks-{version}-linux-x64.AppImage',
];

// MIME types per extension
$mimeTypes = [
    'exe'      => 'application/octet-stream',
    'zip'      => 'application/zip',
    'AppImage' => 'application/octet-stream',
];

/**
 * Scans the avalonia downloads directory for available versions.
 * Returns an array sorted newest-first.
 */
function getAvaloniaVersions(): array
{
    $basePath = __DIR__ . '/resources/downloads/';
    $versions = [];

    if (!is_dir($basePath)) {
        return $versions;
    }

    foreach (scandir($basePath) as $folder) {
        if ($folder === '.' || $folder === '..') continue;
        if (!is_dir($basePath . $folder)) continue;

        // Validate it looks like a version number
        if (!preg_match('/^\d+\.\d+\.\d+/', $folder)) continue;

        $versions[] = $folder;
    }

    // Sort descending by version
    usort($versions, function ($a, $b) {
        return version_compare($b, $a);
    });

    return $versions;
}

/**
 * Finds the installer file for a given version and platform.
 */
function findInstaller(string $version, string $platform): ?array
{
    global $platformPatterns;

    if (!isset($platformPatterns[$platform])) {
        return null;
    }

    $filename = str_replace('{version}', $version, $platformPatterns[$platform]);
    $filepath = __DIR__ . "/resources/downloads/$version/$filename";

    if (!file_exists($filepath)) {
        return null;
    }

    return [
        'version'  => $version,
        'filename' => $filename,
        'filepath' => $filepath,
        'filesize' => filesize($filepath),
        'platform' => $platform,
    ];
}

/**
 * Compute an 8-char HMAC token for the given visitor_id. The token is
 * embedded in the served installer filename so the desktop app can pass
 * it back on first-run, letting us join "ad click -> install" without
 * sending PII through the filename.
 *
 * Token verification on the API side re-hashes recent visitor_ids and
 * compares, so this is one-way.
 */
function computeInstallerToken(string $visitor_id): string
{
    $secret = $_ENV['REFERRAL_TOKEN_SECRET'] ?? '';
    if ($secret === '') {
        return '';
    }
    return substr(hash_hmac('sha256', $visitor_id, $secret), 0, 8);
}

/**
 * Serves a file for download and exits.
 */
function serveFile(array $installer): void
{
    global $mimeTypes;

    // Fire the download_click funnel event BEFORE we start streaming headers/bytes,
    // so the visitor cookie can still be set if this is the visitor's first request.
    track_referral_event('download_click', [
        'event_data' => [
            'platform' => $installer['platform'],
            'version'  => $installer['version'],
        ],
    ]);

    // Embed the visitor token into the served filename so the installer can
    // extract it during install. Falls back to the plain filename if the
    // visitor has no cookie or no secret is configured.
    $served_filename = $installer['filename'];
    $visitor_id = $_COOKIE[ARGO_VISITOR_COOKIE] ?? null;
    if ($visitor_id && preg_match('/^[0-9a-f-]{36}$/i', $visitor_id)) {
        $token = computeInstallerToken($visitor_id);
        if ($token !== '') {
            $ext_pos = strrpos($served_filename, '.');
            if ($ext_pos !== false) {
                $served_filename = substr($served_filename, 0, $ext_pos)
                    . '_' . $token
                    . substr($served_filename, $ext_pos);
            }
        }
    }

    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    $ext = pathinfo($installer['filename'], PATHINFO_EXTENSION);
    $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $contentType);
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . $served_filename . '"');
    header('Content-Length: ' . $installer['filesize']);
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Track the download with platform-specific event type
    track_event('download_' . $installer['platform'], $installer['version']);

    readfile($installer['filepath']);
    exit;
}

// --- Request handling ---

$requestedVersion  = $_GET['version']  ?? null;
$requestedPlatform = $_GET['platform'] ?? null;

// Platform is required
if (!$requestedPlatform || !isset($platformPatterns[$requestedPlatform])) {
    http_response_code(400);
    die('Missing or invalid platform. Use: win, mac, or linux');
}

// Validate the version format before it is ever used to build a filesystem path.
// The /download/avalonia/<ver>/<platform> rewrite already constrains this, but
// the script is also reachable directly, so guard here too (blocks ../ traversal).
if ($requestedVersion !== null && !preg_match('/^\d+\.\d+\.\d+$/', $requestedVersion)) {
    http_response_code(400);
    die('Invalid version format.');
}

// If a specific version was requested, serve it
if ($requestedVersion) {
    $installer = findInstaller($requestedVersion, $requestedPlatform);
    if ($installer) {
        serveFile($installer);
    }
    http_response_code(404);
    die("Version $requestedVersion not found for platform $requestedPlatform");
}

// Otherwise, serve the latest version
$versions = getAvaloniaVersions();
foreach ($versions as $version) {
    $installer = findInstaller($version, $requestedPlatform);
    if ($installer) {
        serveFile($installer);
    }
}

http_response_code(404);
die("No Avalonia installer available for platform $requestedPlatform");
