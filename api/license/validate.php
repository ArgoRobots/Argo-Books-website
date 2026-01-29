<?php
// Set headers for API response
header('Content-Type: application/json');

require_once __DIR__ . '/../../license_functions.php';
require_once __DIR__ . '/../../db_connect.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request method'
];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the license key from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Check for Premium subscription key validation (active subscriptions)
    if (isset($data['subscription_id'])) {
        $subscription_id = trim($data['subscription_id']);
        $response = validate_premium_subscription_key($subscription_id);
    }
    // Check for free/promo premium key validation
    elseif (isset($data['premium_key'])) {
        $premium_key = trim($data['premium_key']);
        $response = validate_premium_key($premium_key);
    }
    // Check for license key validation (auto-detect type by prefix)
    elseif (isset($data['license_key'])) {
        $license_key = trim($data['license_key']);
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Validate key type based on prefix
        if (str_starts_with($license_key, 'PREM-')) {
            $response = validate_premium_key($license_key);
        } elseif (str_starts_with($license_key, 'STND-')) {
            $response = validate_standard_license_key($license_key, $ip_address);
        } else {
            $response = [
                'success' => false,
                'message' => 'Invalid license key format.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'License key, subscription ID, or premium key is required.'
        ];
    }
}

// Return the JSON response
echo json_encode($response);
