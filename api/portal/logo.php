<?php
/**
 * Portal Company Logo API Endpoint
 *
 * POST   /api/portal/logo - Upload or replace the company logo
 * DELETE /api/portal/logo - Remove the company logo
 *
 * Requires API key authentication (Argo Books -> Server).
 *
 * POST expects a multipart/form-data request with a 'logo' file field.
 * Accepted formats: PNG, JPEG, GIF, WebP, BMP, SVG.
 * Max file size: 2MB.
 *
 * On upload, any existing logo file is deleted from disk before saving the new one.
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['POST', 'DELETE']);

// Authenticate the request
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$companyId = $company['id'];
$logosDir = __DIR__ . '/../../resources/uploads/logos';

// Ensure the logos directory exists
if (!is_dir($logosDir)) {
    mkdir($logosDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // --- Delete the current logo ---
    delete_logo_file($company['company_logo_url'], $logosDir);

    $db = get_db_connection();
    $stmt = $db->prepare('UPDATE portal_companies SET company_logo_url = NULL WHERE id = ?');
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $stmt->close();
    $db->close();

    send_json_response(200, [
        'success' => true,
        'message' => 'Logo removed successfully',
        'timestamp' => date('c')
    ]);
}

// --- POST: Upload / replace logo ---

// Validate file presence
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = isset($_FILES['logo']) ? $_FILES['logo']['error'] : 'no_file';
    send_error_response(400, 'No logo file uploaded or upload error (code: ' . $errorCode . ').', 'UPLOAD_ERROR');
}

$file = $_FILES['logo'];

// Validate file size (2MB max)
$maxSize = 2 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    send_error_response(400, 'Logo file too large. Maximum size is 2MB.', 'FILE_TOO_LARGE');
}

// Validate MIME type
$allowedMimes = [
    'image/png'     => 'png',
    'image/jpeg'    => 'jpg',
    'image/gif'     => 'gif',
    'image/webp'    => 'webp',
    'image/bmp'     => 'bmp',
    'image/svg+xml' => 'svg',
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$detectedMime = $finfo->file($file['tmp_name']);

if (!isset($allowedMimes[$detectedMime])) {
    send_error_response(400, 'Invalid file type: ' . $detectedMime . '. Allowed: PNG, JPEG, GIF, WebP, BMP, SVG.', 'INVALID_FILE_TYPE');
}

$extension = $allowedMimes[$detectedMime];

// For SVG files, check for scripts, event handlers, and XXE entities
if ($extension === 'svg') {
    $svgContent = file_get_contents($file['tmp_name']);
    $unsafePatterns = [
        '/<script/i',                          // Script tags
        '/on\w+\s*=/i',                        // Event handlers
        '/<!DOCTYPE/i',                        // XXE DOCTYPE declarations
        '/<!ENTITY/i',                         // XXE entity definitions
        '/\bSYSTEM\s/i',                       // External entity references
        '/\bPUBLIC\s/i',                       // Public entity references
        '/<\?xml-stylesheet/i',                // XML stylesheet processing instructions
        '/href\s*=\s*["\']javascript:/i',      // JavaScript URIs in links
    ];
    foreach ($unsafePatterns as $pattern) {
        if (preg_match($pattern, $svgContent)) {
            send_error_response(400, 'SVG file contains potentially unsafe content.', 'UNSAFE_SVG');
        }
    }
}

// Delete the old logo file if one exists
delete_logo_file($company['company_logo_url'], $logosDir);

// Generate a unique filename: {companyId}_{random}.{ext}
$randomHash = bin2hex(random_bytes(8));
$filename = $companyId . '_' . $randomHash . '.' . $extension;
$destPath = $logosDir . '/' . $filename;

// Save the file
if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    send_error_response(500, 'Failed to save logo file.', 'SAVE_ERROR');
}

// Build the public URL path
$assetBaseUrl = rtrim(env('SITE_URL', 'https://argorobots.com'), '/');
$logoUrl = $assetBaseUrl . '/resources/uploads/logos/' . $filename;

// Update the database
$db = get_db_connection();
$stmt = $db->prepare('UPDATE portal_companies SET company_logo_url = ? WHERE id = ?');
$stmt->bind_param('si', $logoUrl, $companyId);
$stmt->execute();
$stmt->close();
$db->close();

send_json_response(200, [
    'success' => true,
    'message' => 'Logo uploaded successfully',
    'logo_url' => $logoUrl,
    'timestamp' => date('c')
]);

/**
 * Delete a logo file from disk given its URL.
 *
 * @param string|null $logoUrl The full URL or path stored in the database
 * @param string $logosDir Absolute path to the logos directory
 */
function delete_logo_file(?string $logoUrl, string $logosDir): void
{
    if (empty($logoUrl)) {
        return;
    }

    // Extract the filename from the URL
    // URL format: https://argorobots.com/resources/uploads/logos/5_a1b2c3d4.png
    $basename = basename(parse_url($logoUrl, PHP_URL_PATH));

    // Safety: only delete files that match our expected naming pattern
    if (!preg_match('/^\d+_[a-f0-9]+\.\w+$/', $basename)) {
        return;
    }

    $filePath = $logosDir . '/' . $basename;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}
