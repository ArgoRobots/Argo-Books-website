<?php
declare(strict_types=1);

/**
 * GET /api/portal/refunds/status.php?id={request_id}
 * Returns the current state of a refund request. Desktop polls this every 2s
 * while the modal is showing cooling_off / processing.
 */

require_once __DIR__ . '/../portal-helper.php';
require_once __DIR__ . '/../_refund_helpers.php';

set_portal_headers();
require_method(['GET']);

$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    send_error_response(400, 'Missing id parameter.', 'MISSING_ID');
}

global $pdo;
$req = refund_load_request($pdo, (int)$company['id'], $id);

send_json_response(200, [
    'success' => true,
    'requestId' => (int)$req['id'],
    'state' => $req['state'],
    'stateReason' => $req['state_reason'],
    'velocityTier' => $req['velocity_tier'],
    'coolingOffUntil' => $req['cooling_off_until'],
    'providerRefundId' => $req['provider_refund_id'],
    'completedAt' => $req['completed_at'],
]);
