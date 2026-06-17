<?php
// Set headers for API response
header('Content-Type: application/json');

require_once __DIR__ . '/../../license_functions.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../portal/portal-helper.php';

// Initialize response array
$response = [
    'success' => false,
    'status' => 'error',
    'message' => 'Invalid request method.'
];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limit per IP to slow brute-force / repeated takeover attempts.
    $client_ip = get_client_ip();
    if (is_rate_limited($client_ip, 20, 600, 'license_redeem')) {
        echo json_encode(['success' => false, 'status' => 'rate_limited', 'message' => 'Too many attempts. Please try again in a few minutes.']);
        exit;
    }
    record_rate_limit_attempt($client_ip, 'license_redeem');

    // Get the request data
    $data = json_decode(file_get_contents('php://input'), true);

    $premium_key = trim($data['premium_key'] ?? '');
    $device_id = trim($data['device_id'] ?? '');
    $device_label = isset($data['device_label']) ? substr(trim((string) $data['device_label']), 0, 100) : null;

    if (empty($premium_key)) {
        $response = [
            'success' => false,
            'status' => 'error',
            'message' => 'Premium key is required.'
        ];
    } elseif (empty($device_id)) {
        $response = [
            'success' => false,
            'status' => 'error',
            'message' => 'Device ID is required.'
        ];
    } else {
        $response = redeem_premium_key($premium_key, $device_id, $device_label);
    }
}

// Best-effort: notify the subscriber when a brand-new device is activated.
// Never let email failure affect the activation result.
if (!empty($response['success']) && !empty($response['new_device']) && !empty($response['subscription_id'])) {
    try {
        require_once __DIR__ . '/../../email_sender.php';
        $stmt = $pdo->prepare("SELECT email FROM premium_subscriptions WHERE subscription_id = ?");
        $stmt->execute([$response['subscription_id']]);
        $subEmail = $stmt->fetchColumn();
        if (!empty($subEmail)) {
            send_new_device_activated_email($subEmail, site_url() . '/community/users/subscription.php');
        }
    } catch (\Throwable $e) {
        error_log('New-device activation email failed: ' . $e->getMessage());
    }
}

// Return the JSON response
echo json_encode($response);
