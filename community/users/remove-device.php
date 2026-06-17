<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../community_functions.php';
require_once __DIR__ . '/user_functions.php';
require_once __DIR__ . '/../../license_functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$device_id = trim($_POST['device_id'] ?? '');
if ($device_id === '') {
    echo json_encode(['success' => false, 'error' => 'No device specified']);
    exit;
}

// Only let a user remove a device from THEIR OWN subscription.
$subscription = get_user_premium_subscription($user_id);
if (!$subscription) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No active subscription']);
    exit;
}

$removed = remove_subscription_device($subscription['subscription_id'], $device_id);
echo json_encode(['success' => $removed, 'error' => $removed ? null : 'Device not found']);
