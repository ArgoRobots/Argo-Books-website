<?php
/**
 * Portal Company Name API Endpoint
 *
 * PUT /api/portal/company-name - Update the company display name on the portal
 *
 * Requires API key authentication (Argo Books -> Server).
 *
 * Expects JSON body: { "companyName": "New Company Name" }
 */

require_once __DIR__ . '/portal-helper.php';

set_portal_headers();
require_method(['PUT']);

// Authenticate the request
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

// Parse request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    send_error_response(400, 'Invalid JSON: ' . json_last_error_msg(), 'INVALID_JSON');
}

if (empty($data['companyName']) || !is_string($data['companyName'])) {
    send_error_response(400, 'Missing or invalid required field: companyName', 'MISSING_FIELDS');
}

$companyName = trim($data['companyName']);
if (mb_strlen($companyName) > 255) {
    send_error_response(400, 'Company name must be 255 characters or fewer.', 'NAME_TOO_LONG');
}

$companyId = $company['id'];

$db = get_db_connection();
$stmt = $db->prepare('UPDATE portal_companies SET company_name = ? WHERE id = ?');
$stmt->bind_param('si', $companyName, $companyId);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    $db->close();
    error_log('Portal company name update DB error: ' . $error);
    send_error_response(500, 'Failed to update company name. Please try again.', 'DB_ERROR');
}

$stmt->close();
$db->close();

send_json_response(200, [
    'success' => true,
    'companyName' => $companyName,
    'message' => 'Company name updated successfully',
    'timestamp' => date('c')
]);
