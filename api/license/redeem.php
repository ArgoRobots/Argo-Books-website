<?php
// Set headers for API response
header('Content-Type: application/json');

require_once __DIR__ . '/../../license_functions.php';
require_once __DIR__ . '/../../db_connect.php';

// Initialize response array
$response = [
    'success' => false,
    'status' => 'error',
    'message' => 'Invalid request method.'
];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request data
    $data = json_decode(file_get_contents('php://input'), true);

    $premium_key = trim($data['premium_key'] ?? '');
    $device_id = trim($data['device_id'] ?? '');

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
        $response = redeem_premium_key($premium_key, $device_id);
    }
}

// Return the JSON response
echo json_encode($response);
