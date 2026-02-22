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

    $license_key = trim($data['license_key'] ?? '');
    $device_id = trim($data['device_id'] ?? '');

    if (empty($license_key) || empty($device_id)) {
        $response = [
            'success' => false,
            'status' => 'error',
            'message' => 'License key and device ID are required.'
        ];
    } else {
        $response = validate_license($license_key, $device_id);
    }
}

// Return the JSON response
echo json_encode($response);
