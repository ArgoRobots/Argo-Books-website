<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../community_functions.php';
require_once __DIR__ . '/user_functions.php';
require_once __DIR__ . '/../../email_sender.php';

require_once __DIR__ . '/../../resources/icons.php';

// Ensure user is logged in
require_login();

$user_id = $_SESSION['user_id'];
$user = get_user($user_id);

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

if (isset($_SESSION['profile_success'])) {
    $success_message = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

if (isset($_SESSION['profile_error'])) {
    $error_message = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['profile_error'] = 'Invalid request. Please try again.';
        header('Location: edit_profile.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_profile':
            handle_profile_update();
            break;
        case 'change_avatar':
            handle_avatar_change();
            break;
        case 'remove_avatar':
            handle_avatar_removal();
            break;
    }
}

// Function to handle profile updates (username and bio)
function handle_profile_update()
{
    global $user_id, $user;

    $username = trim($_POST['username'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    // Validate username
    if (empty($username)) {
        $_SESSION['profile_error'] = 'Username is required';
        header('Location: edit_profile.php');
        exit;
    }

    if (strlen($username) < 3 || strlen($username) > 30) {
        $_SESSION['profile_error'] = 'Username must be between 3 and 30 characters';
        header('Location: edit_profile.php');
        exit;
    }

    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $_SESSION['profile_error'] = 'Username can only contain letters, numbers, underscores, and hyphens';
        header('Location: edit_profile.php');
        exit;
    }

    // Validate bio length
    if (strlen($bio) > 500) {
        $_SESSION['profile_error'] = 'Bio must be 500 characters or less';
        header('Location: edit_profile.php');
        exit;
    }

    global $pdo;

    // Check if username is taken by someone else
    if ($username !== $user['username']) {
        $stmt = $pdo->prepare('SELECT id FROM community_users WHERE username = ? AND id != ?');
        $stmt->execute([$username, $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['profile_error'] = 'Username is already taken';
            header('Location: edit_profile.php');
            exit;
        }
    }

    // Update user profile
    $stmt = $pdo->prepare('UPDATE community_users SET username = ?, bio = ? WHERE id = ?');

    if ($stmt->execute([$username, $bio, $user_id])) {

        // Update username across all posts and comments if changed
        if ($username !== $user['username']) {
            $stmt = $pdo->prepare('UPDATE community_posts SET user_name = ? WHERE user_id = ?');
            $stmt->execute([$username, $user_id]);

            $stmt = $pdo->prepare('UPDATE community_comments SET user_name = ? WHERE user_id = ?');
            $stmt->execute([$username, $user_id]);

            // Update session
            $_SESSION['username'] = $username;
        }

        $_SESSION['profile_success'] = 'Profile updated successfully!';
        header('Location: edit_profile.php');
        exit;
    } else {
        $_SESSION['profile_error'] = 'Failed to update profile. Please try again.';
        header('Location: edit_profile.php');
        exit;
    }
}

// Function to handle avatar changes
function handle_avatar_change()
{
    global $user_id, $user;

    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['profile_error'] = 'Please select an image to upload';
        header('Location: edit_profile.php');
        exit;
    }

    $file = $_FILES['avatar'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['profile_error'] = 'File upload failed. Please try again.';
        header('Location: edit_profile.php');
        exit;
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $file['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['profile_error'] = 'Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.';
        header('Location: edit_profile.php');
        exit;
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $_SESSION['profile_error'] = 'File is too large. Maximum size is 5MB.';
        header('Location: edit_profile.php');
        exit;
    }

    // Create base uploads directory first
    $base_dir = dirname(__DIR__) . '/uploads/';
    if (!file_exists($base_dir)) {
        if (!mkdir($base_dir, 0755)) {
            $_SESSION['profile_error'] = 'Failed to create uploads directory.';
            header('Location: edit_profile.php');
            exit;
        }
        chmod($base_dir, 0755);
    }

    // Then create avatars subdirectory
    $avatar_dir = $base_dir . 'avatars/';
    if (!file_exists($avatar_dir)) {
        if (!mkdir($avatar_dir, 0755)) {
            $_SESSION['profile_error'] = 'Failed to create avatars directory.';
            header('Location: edit_profile.php');
            exit;
        }
        chmod($avatar_dir, 0755);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $avatar_dir . $filename;

    // Delete old avatar if it exists (validate path to prevent traversal)
    if (!empty($user['avatar'])) {
        $old_avatar_name = basename($user['avatar']);
        $old_avatar_path = $avatar_dir . $old_avatar_name;
        if (file_exists($old_avatar_path) && realpath($old_avatar_path) && strpos(realpath($old_avatar_path), realpath($avatar_dir)) === 0) {
            unlink($old_avatar_path);
        }
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Set permissions for the file
        chmod($filepath, 0644);

        // Update database with consistent path format
        $relative_path = 'uploads/avatars/' . $filename;

        global $pdo;
        $stmt = $pdo->prepare('UPDATE community_users SET avatar = ? WHERE id = ?');

        if ($stmt->execute([$relative_path, $user_id])) {
            $_SESSION['profile_success'] = 'Avatar updated successfully!';
            header('Location: edit_profile.php');
            exit;
        } else {
            // Clean up uploaded file on database error
            unlink($filepath);
            $_SESSION['profile_error'] = 'Failed to update avatar in database.';
            header('Location: edit_profile.php');
            exit;
        }
    } else {
        $_SESSION['profile_error'] = 'Failed to upload avatar. Please try again.';
        header('Location: edit_profile.php');
        exit;
    }
}

// Function to handle avatar removal
function handle_avatar_removal()
{
    global $user_id, $user;

    if (!empty($user['avatar'])) {
        // Validate and delete old avatar (prevent path traversal)
        $avatar_name = basename($user['avatar']);
        $uploads_dir = realpath(dirname(__DIR__) . '/uploads/avatars/');
        if ($uploads_dir) {
            $avatar_path = $uploads_dir . '/' . $avatar_name;
            if (file_exists($avatar_path) && realpath($avatar_path) && strpos(realpath($avatar_path), $uploads_dir) === 0) {
                unlink($avatar_path);
            }
        }

        // Update database
        global $pdo;
        $stmt = $pdo->prepare('UPDATE community_users SET avatar = NULL WHERE id = ?');

        if ($stmt->execute([$user_id])) {
            $_SESSION['profile_success'] = 'Avatar removed successfully!';
            header('Location: edit_profile.php');
            exit;
        } else {
            $_SESSION['profile_error'] = 'Failed to remove avatar.';
            header('Location: edit_profile.php');
            exit;
        }
    } else {
        $_SESSION['profile_error'] = 'No avatar to remove.';
        header('Location: edit_profile.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account - Argo Community</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">

    <script src="delete-account.js" defer></script>
    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>
    <script src="../../resources/notifications/notifications.js" defer></script>

    <link rel="stylesheet" href="edit-profile.css">
    <link rel="stylesheet" href="account-subpage.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/link.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/header/dark.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="stylesheet" href="../../resources/notifications/notifications.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <?php if (!empty($success_message)): ?>
        <div class="success-message">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>

    <div class="edit-sections">
        <div class="title-container">
            <h1>Edit Account</h1>
        </div>

        <a href="profile.php" class="link-no-underline back-link">
            <?= svg_icon('arrow-back', 16) ?>
            Back to Profile
        </a>

        <!-- Avatar Section -->
        <div class="edit-section">
            <h2>Profile Picture</h2>
            <div class="avatar-section">
                <div class="current-avatar">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="../<?php echo htmlspecialchars($user['avatar']); ?>" alt="Current Avatar" class="avatar-preview" id="avatarPreview">
                    <?php else: ?>
                        <div class="avatar-preview" id="avatarPreview" style="background-color: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold;">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="avatar-controls">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="change_avatar">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="file-input-wrapper">
                            <input type="file" name="avatar" id="avatarFile" accept="image/*" onchange="previewAvatar(this)">
                            <label for="avatarFile" class="file-input-label">Choose New Avatar</label>
                        </div>
                        <div class="selected-file" id="selectedFile" style="display: none;"></div>
                        <p class="info-text">Upload a profile picture (JPEG, PNG, GIF, or WebP). Maximum size: 5MB.</p>
                        <div class="form-actions" style="margin-top: 15px; padding-top: 15px; justify-content: flex-start;">
                            <button type="submit" class="btn btn-blue" id="applyAvatarBtn" style="display: none;">Apply</button>
                        </div>
                    </form>

                    <?php if (!empty($user['avatar'])): ?>
                        <form method="post" style="margin-top: 15px;">
                            <input type="hidden" name="action" value="remove_avatar">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <button type="submit" class="btn btn-red" onclick="return confirm('Are you sure you want to remove your avatar?')">Remove Avatar</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="edit-section">
            <h2>Profile Information</h2>
            <form method="post">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    <p class="info-text">Your username will be displayed on all your posts and comments. Only letters, numbers, underscores, and hyphens allowed.</p>
                </div>

                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" placeholder="Tell us about yourself..." oninput="updateCharCount(this)"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                  
                    <div class="text-container">
                        <p class="info-text">Write a short bio that will be displayed on your profile page.</p>
                        <div class="char-counter">
                            <span id="bioCharCount"><?php echo strlen($user['bio'] ?? ''); ?></span>/500 characters
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-blue">Update Profile</button>
                </div>
            </form>
        </div>

        <!-- Account Security -->
        <div class="edit-section">
            <h2>Account Security</h2>
            <div class="account-link-cards">
                <a href="change_email.php" class="account-link-card">
                    <div class="card-icon">
                        <?= svg_icon('mail', 22, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                    </div>
                    <div class="card-content">
                        <h3>Change Email</h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="card-arrow">
                        <?= svg_icon('arrow-right', 18, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                    </div>
                </a>

                <a href="change_password.php" class="account-link-card">
                    <div class="card-icon">
                        <?= svg_icon('lock', 22, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                    </div>
                    <div class="card-content">
                        <h3>Change Password</h3>
                        <p>Update the password used to log in.</p>
                    </div>
                    <div class="card-arrow">
                        <?= svg_icon('arrow-right', 18, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                    </div>
                </a>
            </div>
        </div>

        <div class="delete-account-section">
            <button onclick="showDeleteModal()" class="btn btn-delete-outline">Delete Account</button>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Restore scroll position if it exists in sessionStorage
            if (sessionStorage.getItem('scrollPosition')) {
                window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
                sessionStorage.removeItem('scrollPosition');
            }

            // Save scroll position when submitting forms
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                });
            });

            // Also save position when clicking links
            const links = document.querySelectorAll('a[href^="edit_profile.php"], a[href^="profile.php"]');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    sessionStorage.setItem('scrollPosition', window.scrollY);
                });
            });
        });

        // Avatar preview functionality
        function previewAvatar(input) {
            const file = input.files[0];
            const preview = document.getElementById('avatarPreview');
            const selectedFile = document.getElementById('selectedFile');
            const applyBtn = document.getElementById('applyAvatarBtn');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Avatar Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                };
                reader.readAsDataURL(file);

                selectedFile.textContent = `Selected: ${file.name}`;
                selectedFile.style.display = 'block';
                applyBtn.style.display = 'inline-block';
            } else {
                selectedFile.style.display = 'none';
                applyBtn.style.display = 'none';
                // Reset preview to original avatar
                <?php if (!empty($user['avatar']) && file_exists('../../' . $user['avatar'])): ?>
                    preview.innerHTML = `<img src="../../<?php echo htmlspecialchars($user['avatar']); ?>" alt="Current Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">`;
                <?php else: ?>
                    preview.innerHTML = `<div style="background-color: #3b82f6; color: white; display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: bold; width: 100%; height: 100%; border-radius: 50%;"><?php echo strtoupper(substr($user['username'], 0, 1)); ?></div>`;
                <?php endif; ?>
            }
        }

        // Bio character counter
        function updateCharCount(textarea) {
            const count = textarea.value.length;
            const counter = document.getElementById('bioCharCount');
            counter.textContent = count;

            if (count > 500) {
                counter.style.color = '#dc2626';
            } else if (count > 400) {
                counter.style.color = '#f59e0b';
            } else {
                counter.style.color = '#6b7280';
            }
        }

        // Delete account fallback — available immediately before deferred delete-account.js loads
        function showDeleteModal() {
            document.getElementById('delete-account-modal').style.display = 'block';
            document.getElementById('delete-confirm-input').value = '';
            document.getElementById('confirm-delete').disabled = true;
        }

    </script>

    <div id="delete-account-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Confirm Account Deletion</h2>
            <p>Type <strong>DELETE</strong> to confirm. Your account will be scheduled for deletion in 30 days unless you log in again before then.</p>
            <input type="text" id="delete-confirm-input" placeholder="Type DELETE to confirm">
            <div class="modal-actions">
                <button type="button" id="cancel-delete" class="btn btn-gray">Cancel</button>
                <button type="button" id="confirm-delete" class="btn btn-red" disabled>Schedule Deletion</button>
            </div>
        </div>
    </div>
</body>

</html>