<?php
/**
 * Waitlist signup endpoint (today: the macOS "notify me" form on /downloads/).
 *
 * POST JSON: {"email": "...", "platform": "macos", "website": ""}
 * Responds JSON: {"success": bool, "error"?: string}
 *
 * No email is sent on signup; subscribers get exactly one email ever, the
 * launch announcement (sent manually later, tracked via notified_at).
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../track_referral_event.php';
require_once __DIR__ . '/waitlist_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    // Graceful fallback for form-encoded posts.
    $data = $_POST;
}

// Attribution: same approach as checkout. Visitor id from the cookie (when it
// looks like a UUID), source from the session or the visitor's recorded
// first-touch landing.
$visitor_id = $_COOKIE[ARGO_VISITOR_COOKIE] ?? null;
if (!is_string($visitor_id) || !preg_match('/^[0-9a-f-]{36}$/i', $visitor_id)) {
    $visitor_id = null;
}
$source_code = $_SESSION['referral_source'] ?? null;
if (empty($source_code) && $visitor_id !== null) {
    $source_code = get_referral_source_for_visitor($visitor_id);
}

$result = waitlist_subscribe($data, [
    'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
    'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? null,
    'visitor_id'  => $visitor_id,
    'source_code' => $source_code ?: null,
]);

http_response_code($result['status']);
echo json_encode($result['body']);
