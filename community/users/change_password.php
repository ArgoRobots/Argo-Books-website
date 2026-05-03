<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../community_functions.php';
require_once __DIR__ . '/user_functions.php';
require_once __DIR__ . '/../../resources/icons.php';

require_login();

$user_id = $_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

if (isset($_SESSION['change_password_success'])) {
    $success_message = $_SESSION['change_password_success'];
    unset($_SESSION['change_password_success']);
}

if (isset($_SESSION['change_password_error'])) {
    $error_message = $_SESSION['change_password_error'];
    unset($_SESSION['change_password_error']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['change_password_error'] = 'Invalid request. Please try again.';
        header('Location: change_password.php');
        exit;
    }

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['change_password_error'] = 'All password fields are required';
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['change_password_error'] = 'New passwords do not match';
    } elseif (strlen($new_password) < 8) {
        $_SESSION['change_password_error'] = 'Password must be at least 8 characters long';
    } elseif ($new_password === $current_password) {
        $_SESSION['change_password_error'] = 'New password must be different from your current password';
    } else {
        $stmt = $pdo->prepare('SELECT password_hash FROM community_users WHERE id = ?');
        $stmt->execute([$user_id]);
        $password_data = $stmt->fetch();

        if (!$password_data || !password_verify($current_password, $password_data['password_hash'])) {
            $_SESSION['change_password_error'] = 'Current password is incorrect';
        } else {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE community_users SET password_hash = ? WHERE id = ?');
            if ($stmt->execute([$new_password_hash, $user_id])) {
                $_SESSION['change_password_success'] = 'Password changed successfully!';
            } else {
                $_SESSION['change_password_error'] = 'Failed to change password. Please try again.';
            }
        }
    }

    header('Location: change_password.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Argo Community</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="edit-profile.css">
    <link rel="stylesheet" href="account-subpage.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/link.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/header/dark.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="stylesheet" href="../../resources/styles/password-toggle.css">
    <script src="../../resources/scripts/password-toggle.js" defer></script>
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <div class="account-subpage">
        <a href="edit_profile.php" class="link-no-underline back-link">
            <?= svg_icon('arrow-back', 16) ?>
            Back to Edit Account
        </a>

        <div class="subpage-card">
            <div class="subpage-header">
                <div class="subpage-icon">
                    <?= svg_icon('lock', 28, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
                </div>
                <div>
                    <h1>Change Password</h1>
                    <p class="subpage-subtitle">Update the password used to log in to your account.</p>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="banner banner-success"><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="banner banner-error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="form-group">
                    <label for="current_password">Current password</label>
                    <input type="password" id="current_password" name="current_password" required autofocus>
                </div>

                <div class="form-group">
                    <label for="new_password">New password</label>
                    <input type="password" id="new_password" name="new_password" required oninput="checkPasswordStrength(this.value)">
                    <div id="passwordStrength" class="password-strength"></div>
                    <p class="info-text">At least 8 characters.</p>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm new password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required oninput="checkPasswordMatch()">
                    <div id="passwordMatch" class="password-strength"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-blue">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>

    <script>
        function checkPasswordStrength(password) {
            const strengthDiv = document.getElementById('passwordStrength');
            let strength = 0;

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            let feedback = '';
            switch (strength) {
                case 0:
                case 1:
                    feedback = 'Very weak password';
                    strengthDiv.className = 'password-strength strength-weak';
                    break;
                case 2:
                    feedback = 'Weak password';
                    strengthDiv.className = 'password-strength strength-weak';
                    break;
                case 3:
                    feedback = 'Medium password';
                    strengthDiv.className = 'password-strength strength-medium';
                    break;
                case 4:
                    feedback = 'Strong password';
                    strengthDiv.className = 'password-strength strength-strong';
                    break;
                case 5:
                    feedback = 'Very strong password';
                    strengthDiv.className = 'password-strength strength-strong';
                    break;
            }
            strengthDiv.textContent = password ? feedback : '';
        }

        function checkPasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('passwordMatch');

            if (confirmPassword === '') {
                matchDiv.textContent = '';
                return;
            }

            if (newPassword === confirmPassword) {
                matchDiv.textContent = 'Passwords match';
                matchDiv.className = 'password-strength strength-strong';
            } else {
                matchDiv.textContent = 'Passwords do not match';
                matchDiv.className = 'password-strength strength-weak';
            }
        }
    </script>
</body>

</html>
