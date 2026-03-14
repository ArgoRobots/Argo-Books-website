<?php

/**
 * Searching users for @mentions
 * 
 * This endpoint searches for users based on a query string and returns results
 * formatted for the @mentions dropdown.
 */

// Start session and include necessary files
session_start();
require_once '../../db_connect.php';
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get query parameters
    $query = isset($_GET['query']) ? trim($_GET['query']) : '';
    $post_id = isset($_GET['postId']) ? intval($_GET['postId']) : 0;
    $current_user_id = $_SESSION['user_id']; // Get current user ID to exclude from results

    // Connect to the database
    $db = get_db_connection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Get users who have commented on the post (excluding current user)
    $commenters = [];
    if ($post_id > 0) {
        $sql_commenters = "
            SELECT DISTINCT u.id, u.username, u.avatar, u.role
            FROM community_users u
            JOIN community_comments c ON u.id = c.user_id
            WHERE c.post_id = ? AND u.id != ?
            ORDER BY c.created_at DESC
        ";

        $stmt = $db->prepare($sql_commenters);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param('ii', $post_id, $current_user_id);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $commenters[$row['id']] = $row;
        }
        $stmt->close();

        // Get the post author if not already in commenters (and not current user)
        $sql_author = "
            SELECT DISTINCT u.id, u.username, u.avatar, u.role
            FROM community_users u
            JOIN community_posts p ON u.id = p.user_id
            WHERE p.id = ? AND u.id != ?
        ";

        $stmt = $db->prepare($sql_author);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $db->error);
        }

        $stmt->bind_param('ii', $post_id, $current_user_id);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        if ($author = $result->fetch_assoc()) {
            if (!isset($commenters[$author['id']])) {
                $commenters[$author['id']] = $author;
            }
        }
        $stmt->close();
    }

    // If query is empty (just '@'), show only commenters and post author
    if (empty($query)) {
        // Return combined commenters and author (already collected earlier, excluding current user)
        echo json_encode(['users' => array_values($commenters)]);
        exit;
    }

    // For non-empty queries, proceed with the existing search logic
    $search_exact_start = $query . '%';
    $search_anywhere = '%' . $query . '%';

    $users = [];

    // First, get exact start matches (excluding current user)
    $sql_exact_start = "
        SELECT id, username, avatar, role
        FROM community_users
        WHERE username LIKE ? AND id != ?
        ORDER BY username ASC
        LIMIT 10
    ";

    $stmt = $db->prepare($sql_exact_start);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $db->error);
    }

    $stmt->bind_param('si', $search_exact_start, $current_user_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $users[$row['id']] = $row;
    }
    $stmt->close();

    // Then, if we have fewer than 10 results, get partial matches anywhere in the username (excluding current user)
    if (count($users) < 10) {
        $exclude_ids = !empty($users) ? array_keys($users) : [0];
        $placeholders = implode(',', array_fill(0, count($exclude_ids), '?'));
        $remaining = 10 - count($users);
        $sql_anywhere = "
            SELECT id, username, avatar, role
            FROM community_users
            WHERE username LIKE ? AND id NOT IN ($placeholders) AND id != ?
            ORDER BY username ASC
            LIMIT ?";

        $stmt = $db->prepare($sql_anywhere);
        if (!$stmt) {
            error_log('Mentions search prepare failed: ' . $db->error);
            throw new Exception('Prepare failed');
        }

        $types = 's' . str_repeat('i', count($exclude_ids)) . 'ii';
        $params = array_merge([$search_anywhere], $exclude_ids, [$current_user_id, $remaining]);
        // mysqli bind_param requires pass-by-reference; use call_user_func_array
        $bindParams = [];
        $bindParams[] = &$types;
        foreach ($params as $key => $value) {
            $bindParams[] = &$params[$key];
        }
        if (!call_user_func_array([$stmt, 'bind_param'], $bindParams)) {
            throw new Exception('Bind failed');
        }
        if (!$stmt->execute()) {
            throw new Exception('Execute failed');
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[$row['id']] = $row;
        }
        $stmt->close();
    }

    // Combine results, giving priority to commenters and the post author (all already exclude current user)
    $combined_users = [];

    // First add exact matches from commenters
    foreach ($commenters as $id => $user) {
        if (stripos($user['username'], $query) === 0) {
            $combined_users[$id] = $user;
            unset($commenters[$id]);
            unset($users[$id]);
        }
    }

    // Then add exact matches from general users
    foreach ($users as $id => $user) {
        if (stripos($user['username'], $query) === 0) {
            $combined_users[$id] = $user;
            unset($users[$id]);
        }
    }

    // Then add remaining commenters
    foreach ($commenters as $id => $user) {
        $combined_users[$id] = $user;
        unset($users[$id]);
    }

    // Finally add remaining users
    foreach ($users as $id => $user) {
        $combined_users[$id] = $user;
    }

    // Format the results for the @mentions dropdown
    $response = [
        'users' => array_values($combined_users)
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    error_log('Error in mentions/search.php: ' . $e->getMessage());
    echo json_encode(['error' => 'An internal error occurred']);
}
exit;
