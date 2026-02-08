<?php
/**
 * Serves Argo Books Avalonia (cross-platform) installer files.
 *
 * Filesystem layout:
 *   resources/downloads/versions/{version}/
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
    $basePath = __DIR__ . '/resources/downloads/versions/';
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
    $filepath = __DIR__ . "/resources/downloads/versions/$version/$filename";

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
 * Serves a file for download and exits.
 */
function serveFile(array $installer): void
{
    global $mimeTypes;

    // Clean output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }

    $ext = pathinfo($installer['filename'], PATHINFO_EXTENSION);
    $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $contentType);
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . $installer['filename'] . '"');
    header('Content-Length: ' . $installer['filesize']);
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Track the download
    track_event('download_avalonia', $installer['version'] . '_' . $installer['platform']);

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
