<?php
session_start();
require_once '../db_connect.php';
require_once 'community_functions.php';

header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Unauthorized access'
];

// Check if user is an admin
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF token
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $response['message'] = 'Invalid CSRF token';
            echo json_encode($response);
            exit;
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        // Validate status
        if (!in_array($status, ['open', 'in_progress', 'completed', 'declined'])) {
            $response['message'] = 'Invalid status value';
        } elseif ($post_id <= 0) {
            $response['message'] = 'Invalid post ID';
        } else {
            // Update the post status
            if (update_post_status($post_id, $status)) {
                $response = [
                    'success' => true,
                    'message' => 'Status updated successfully'
                ];
            } else {
                $response['message'] = 'Error updating status';
            }
        }
    } else {
        $response['message'] = 'Invalid request method';
    }
}

// Send the response
echo json_encode($response);
