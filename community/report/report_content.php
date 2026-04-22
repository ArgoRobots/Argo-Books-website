<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../users/user_functions.php';
require_once __DIR__ . '/../../email_sender.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to report content.']);
    exit;
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}

// Get and validate input
$content_type = $_POST['content_type'] ?? '';
$content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
$violation_type = $_POST['violation_type'] ?? '';
$additional_info = $_POST['additional_info'] ?? '';

// Validation
if (!in_array($content_type, ['post', 'comment', 'user'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid content type.']);
    exit;
}

if ($content_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid content ID.']);
    exit;
}

if (empty($violation_type)) {
    echo json_encode(['success' => false, 'message' => 'Please select a violation type.']);
    exit;
}

$valid_violations = ['spam', 'harassment', 'hateful', 'inappropriate', 'misinformation', 'off-topic', 'inappropriate_username', 'inappropriate_bio', 'impersonation', 'other'];
if (!in_array($violation_type, $valid_violations)) {
    echo json_encode(['success' => false, 'message' => 'Invalid violation type.']);
    exit;
}

try {
    // Get reporter info
    $reporter_user_id = $_SESSION['user_id'];
    $reporter_email = $_SESSION['email'];

    // Verify content exists
    if ($content_type === 'post') {
        $stmt = $pdo->prepare('SELECT id, user_id FROM community_posts WHERE id = ?');
        $stmt->execute([$content_id]);
        $content = $stmt->fetch();

        if (!$content) {
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            exit;
        }

        // Don't allow users to report their own posts
        if ($content['user_id'] == $reporter_user_id) {
            echo json_encode(['success' => false, 'message' => 'You cannot report your own content.']);
            exit;
        }
    } elseif ($content_type === 'comment') {
        $stmt = $pdo->prepare('SELECT id, user_id FROM community_comments WHERE id = ?');
        $stmt->execute([$content_id]);
        $content = $stmt->fetch();

        if (!$content) {
            echo json_encode(['success' => false, 'message' => 'Comment not found.']);
            exit;
        }

        // Don't allow users to report their own comments
        if ($content['user_id'] == $reporter_user_id) {
            echo json_encode(['success' => false, 'message' => 'You cannot report your own content.']);
            exit;
        }
    } else {
        // User report
        $stmt = $pdo->prepare('SELECT id, username FROM community_users WHERE id = ?');
        $stmt->execute([$content_id]);
        $content = $stmt->fetch();

        if (!$content) {
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Don't allow users to report themselves
        if ($content_id == $reporter_user_id) {
            echo json_encode(['success' => false, 'message' => 'You cannot report yourself.']);
            exit;
        }
    }

    // Check if user has already reported this content
    $stmt = $pdo->prepare('SELECT id FROM content_reports WHERE reporter_user_id = ? AND content_type = ? AND content_id = ? AND status = "pending"');
    $stmt->execute([$reporter_user_id, $content_type, $content_id]);
    $existing_report = $stmt->fetch();

    if ($existing_report) {
        echo json_encode(['success' => false, 'message' => 'You have already reported this content.']);
        exit;
    }

    // Insert report
    $stmt = $pdo->prepare('INSERT INTO content_reports (reporter_user_id, reporter_email, content_type, content_id, violation_type, additional_info) VALUES (?, ?, ?, ?, ?, ?)');

    if ($stmt->execute([$reporter_user_id, $reporter_email, $content_type, $content_id, $violation_type, $additional_info])) {
        $report_id = $pdo->lastInsertId();

        error_log("Report created with ID: " . $report_id);

        // Send notification to admins who have opted in
        $stmt = $pdo->prepare('
            SELECT ans.notification_email, cu.username as admin_username
            FROM admin_notification_settings ans
            JOIN community_users cu ON ans.user_id = cu.id
            WHERE cu.role = "admin" AND ans.notify_new_reports = 1
        ');
        $stmt->execute();
        $admins = $stmt->fetchAll();

        error_log("Found " . count($admins) . " admins with notifications enabled");

        // Get reporter username
        $stmt2 = $pdo->prepare('SELECT username FROM community_users WHERE id = ?');
        $stmt2->execute([$reporter_user_id]);
        $reporter = $stmt2->fetch();
        $reporter_username = $reporter ? $reporter['username'] : $reporter_email;

        error_log("Reporter username: " . $reporter_username);

        // Get reported username if applicable
        $reported_username = 'N/A';
        if ($content_type === 'user') {
            $reported_username = $content['username'] ?? 'Unknown';
        } elseif ($content_type === 'post' && isset($content['user_id'])) {
            $stmt3 = $pdo->prepare('SELECT username FROM community_users WHERE id = ?');
            $stmt3->execute([$content['user_id']]);
            $user_data = $stmt3->fetch();
            $reported_username = $user_data ? $user_data['username'] : 'Unknown';
        } elseif ($content_type === 'comment' && isset($content['user_id'])) {
            $stmt3 = $pdo->prepare('SELECT username FROM community_users WHERE id = ?');
            $stmt3->execute([$content['user_id']]);
            $user_data = $stmt3->fetch();
            $reported_username = $user_data ? $user_data['username'] : 'Unknown';
        }

        error_log("Reported username: " . $reported_username);

        foreach ($admins as $admin) {
            error_log("Sending notification to: " . $admin['notification_email'] . " (admin: " . $admin['admin_username'] . ")");
            $mail_result = send_new_report_notification(
                $admin['notification_email'],
                $report_id,
                $content_type,
                $violation_type,
                $reporter_username,
                $reported_username
            );
            error_log("Mail result for " . $admin['notification_email'] . ": " . ($mail_result ? "success" : "failure"));
        }

        echo json_encode(['success' => true, 'message' => 'Report submitted successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit report. Please try again.']);
    }

} catch (Exception $e) {
    error_log('Error submitting report: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}
