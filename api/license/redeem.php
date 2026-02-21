<?php
// Set headers for API response
header('Content-Type: application/json');

require_once __DIR__ . '/../../license_functions.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../email_sender.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request method'
];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request data
    $data = json_decode(file_get_contents('php://input'), true);

    $premium_key = trim($data['premium_key'] ?? '');
    $user_id = intval($data['user_id'] ?? 0);
    $email = trim($data['email'] ?? '');

    if (empty($premium_key)) {
        $response = [
            'success' => false,
            'message' => 'Premium key is required.'
        ];
    } elseif ($user_id <= 0) {
        $response = [
            'success' => false,
            'message' => 'Valid user ID is required.'
        ];
    } elseif (empty($email)) {
        $response = [
            'success' => false,
            'message' => 'Email is required.'
        ];
    } else {
        // Verify user exists
        try {
            $stmt = $pdo->prepare("SELECT id, email, username FROM community_users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid user.'
                ];
            } else {
                $response = redeem_premium_key($premium_key, $user_id, $email);

                // Send redemption confirmation email on success
                if ($response['success']) {
                    send_premium_key_redeemed_email(
                        $email,
                        $user['username'],
                        $premium_key,
                        $response['subscription_id'],
                        $response['duration_months'],
                        date('Y-m-d H:i:s'),
                        $response['end_date']
                    );
                }
            }
        } catch (PDOException $e) {
            error_log("Redeem endpoint error: " . $e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Error processing request. Please try again.'
            ];
        }
    }
}

// Return the JSON response
echo json_encode($response);
