<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/community_functions.php';
require_once __DIR__ . '/users/user_functions.php';

header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Unauthorized access'
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to delete comments';
    echo json_encode($response);
    exit;
}

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $response['message'] = 'Invalid request. Please refresh and try again.';
        echo json_encode($response);
        exit;
    }

    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

    if ($comment_id <= 0) {
        $response['message'] = 'Invalid comment ID';
        echo json_encode($response);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'] ?? 'user';

    // Get the comment to verify ownership
    $stmt = $pdo->prepare('SELECT * FROM community_comments WHERE id = ?');
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();

    if (!$comment) {
        $response['message'] = 'Comment not found';
        echo json_encode($response);
        exit;
    }

    // Check permission: admin or comment owner can delete
    $can_delete = ($role === 'admin') ||
        (isset($comment['user_id']) && (int)$comment['user_id'] === (int)$user_id);

    if (!$can_delete) {
        $response['message'] = 'You do not have permission to delete this comment';
        echo json_encode($response);
        exit;
    }

    // Delete the comment
    $stmt = $pdo->prepare('DELETE FROM community_comments WHERE id = ?');

    try {
        $stmt->execute([$comment_id]);
        $response = [
            'success' => true,
            'message' => 'Comment deleted successfully',
            'post_id' => $comment['post_id']
        ];
    } catch (PDOException $e) {
        error_log('Error deleting comment ' . $comment_id . ': ' . $e->getMessage());
        $response['message'] = 'Error deleting comment. Please try again.';
    }
} else {
    $response['message'] = 'Invalid request method';
}

// Send the response
echo json_encode($response);
