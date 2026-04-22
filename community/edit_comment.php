<?php
session_start();
require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/community_functions.php';
require_once __DIR__ . '/mentions/mentions.php';
require_once __DIR__ . '/report/ban_check.php';

header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Unauthorized access'
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'You must be logged in to edit comments';
    echo json_encode($response);
    exit;
}

// Check if user is banned
$user_id = $_SESSION['user_id'];
$ban = is_user_banned($user_id);
if ($ban) {
    $response['success'] = false;
    $response['message'] = get_ban_message($ban);
    $response['banned'] = true;
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
    $comment_content = isset($_POST['comment_content']) ? trim($_POST['comment_content']) : '';

    if ($comment_id <= 0) {
        $response['message'] = 'Invalid comment ID';
        echo json_encode($response);
        exit;
    }

    if (empty($comment_content)) {
        $response['message'] = 'Comment content cannot be empty';
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

    // Check if user has permission to edit this comment
    // Admin can edit any comment, regular users can only edit their own comments
    if ($role === 'admin' || (int)$comment['user_id'] === (int)$user_id) {
        // Process @mentions in the comment content before saving
        $mentions = extract_mentions($comment_content);
        $has_mentions = !empty($mentions);

        // Create notifications for any new mentions
        if ($has_mentions) {
            // Get the post ID for this comment
            $post_id = $comment['post_id'];
            create_mention_notifications($mentions, $post_id, $comment_id, $user_id);
        }

        // Update the comment with new content
        $stmt = $pdo->prepare('UPDATE community_comments SET content = ? WHERE id = ?');

        try {
            $stmt->execute([$comment_content, $comment_id]);
            // Get the updated comment
            $stmt = $pdo->prepare('SELECT * FROM community_comments WHERE id = ?');
            $stmt->execute([$comment_id]);
            $updated_comment = $stmt->fetch();

            // Process the comment content using the proper mentions function
            $updated_comment['processed_content'] = process_mentions(htmlspecialchars($updated_comment['content']));

            $response = [
                'success' => true,
                'message' => 'Comment updated successfully',
                'comment' => $updated_comment
            ];
        } catch (PDOException $e) {
            error_log('Error updating comment ' . $comment_id . ': ' . $e->getMessage());
            $response['message'] = 'Error updating comment. Please try again.';
        }
    } else {
        $response['message'] = 'You do not have permission to edit this comment.';
    }
}

// Send the response
echo json_encode($response);
