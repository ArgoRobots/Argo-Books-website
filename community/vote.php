<?php
session_start();
require_once '../db_connect.php';
require_once 'community_functions.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'Invalid request'
];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['success'] = false;
    $response['message'] = 'You must be logged in to vote';
    $response['show_message'] = true;
    $response['message_class'] = 'error-message';
    echo json_encode($response);
    exit;
}

// Get user information from session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? '';

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

    // Validate and sanitize inputs
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    $vote_type = isset($_POST['vote_type']) ? intval($_POST['vote_type']) : 0;

    // Basic validation
    if (empty($post_id) && empty($comment_id)) {
        $response['message'] = 'Missing required parameters';
    } elseif ($vote_type !== 1 && $vote_type !== -1) {
        $response['message'] = 'Invalid vote type';
    } else {
        if ($post_id > 0) {
            // Voting on a post
            // Verify post exists
            $post = get_post($post_id);

            if (!$post) {
                $response['message'] = 'Post not found';
            } else {
                // Check if user is the author of the post
                if ((int)$post['user_id'] === (int)$user_id) {
                    $response['success'] = false;
                    $response['message'] = 'You cannot vote on your own post';
                    $response['show_message'] = true;
                    $response['message_class'] = 'error-message';
                } else {
                    // Process the vote
                    $result = vote_post($post_id, $email, $vote_type);

                    if ($result !== false) {
                        // Connect vote to user account
                        if ($user_id > 0) {
                            // Update the vote record with user_id
                            $stmt = $pdo->prepare('UPDATE community_votes SET user_id = ? WHERE post_id = ? AND user_email = ?');
                            $stmt->execute([$user_id, $post_id, $email]);
                        }

                        $response = [
                            'success' => true,
                            'message' => 'Vote recorded successfully',
                            'new_vote_count' => $result['new_vote_count'],
                            'user_vote' => $result['user_vote']
                        ];
                    } else {
                        $response['message'] = 'Error recording vote';
                    }
                }
            }
        } elseif ($comment_id > 0) {
            // Voting on a comment
            // Verify comment exists and check if user is the author
            $stmt = $pdo->prepare('SELECT id, user_id FROM community_comments WHERE id = ?');
            $stmt->execute([$comment_id]);
            $comment = $stmt->fetch();

            if (!$comment) {
                $response['message'] = 'Comment not found';
            } elseif ((int)$comment['user_id'] === (int)$user_id) {
                $response['success'] = false;
                $response['message'] = 'You cannot vote on your own comment';
                $response['show_message'] = true;
                $response['message_class'] = 'error-message';
            } else {
                // Process the comment vote
                $result = vote_comment($comment_id, $email, $vote_type);

                if ($result !== false) {
                    // Connect vote to user account
                    if ($user_id > 0) {
                        // Update the vote record with user_id
                        $stmt = $pdo->prepare('UPDATE comment_votes SET user_id = ? WHERE comment_id = ? AND user_email = ?');
                        $stmt->execute([$user_id, $comment_id, $email]);
                    }

                    $response = [
                        'success' => true,
                        'message' => 'Comment vote recorded successfully',
                        'new_vote_count' => $result['new_vote_count'],
                        'user_vote' => $result['user_vote']
                    ];
                } else {
                    $response['message'] = 'Error recording comment vote';
                }
            }
        }
    }
}

// Send the response
echo json_encode($response);
