<?php
session_start();
require_once '../db_connect.php';
require_once '../rate_limit_helper.php';
require_once 'settings/2fa.php';

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$show_2fa_form = false;

// Process 2FA verification
if (isset($_SESSION['awaiting_2fa']) && $_SESSION['awaiting_2fa'] === true) {
    $show_2fa_form = true;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
        $verification_code = $_POST['verification_code'] ?? '';

        if (empty($verification_code)) {
            $error = 'Please enter the verification code.';
        } else {
            $username = $_SESSION['temp_username'];
            $secret = get_2fa_secret($username);

            if (empty($secret)) {
                $error = "Authentication error: Unable to retrieve your 2FA secret.";
            } else if (verify_2fa_code($secret, $verification_code)) {
                // Code is valid, complete login
                session_regenerate_id(true);
                $_SESSION['awaiting_2fa'] = false;
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                unset($_SESSION['temp_username']);

                // Update last login time
                $stmt = $pdo->prepare('UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE username = ?');
                $stmt->execute([$username]);

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid verification code. Please try again.';
            }
        }
    }
}
// Process login form submission
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Rate limit login attempts (max 5 per 15 minutes, per IP, flat-file backed)
    $clientIp = get_client_ip();
    if (is_rate_limited($clientIp, 5, 900, 'admin_login')) {
        $error = 'Too many login attempts. Please wait 15 minutes before trying again.';
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($error) && (empty($username) || empty($password))) {
        $error = 'Please enter both username and password.';
    }

    if (empty($error)) {
        $stmt = $pdo->prepare('SELECT * FROM admin_users WHERE LOWER(username) = LOWER(?)');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $actual_username = $user['username']; // Get actual username with correct case

            if (is_2fa_enabled($actual_username)) {
                // 2FA is enabled, show the verification form
                $_SESSION['awaiting_2fa'] = true;
                $_SESSION['temp_username'] = $actual_username;
                $show_2fa_form = true;
            } else {
                // No 2FA, complete login
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $actual_username;

                // Update last login time
                $stmt = $pdo->prepare('UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE username = ?');
                $stmt->execute([$actual_username]);

                header('Location: index.php');
                exit;
            }
        } else {
            // Count this failed attempt only on authentication failure
            record_rate_limit_attempt($clientIp, 'admin_login');
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        // Apply saved theme immediately to prevent flash
        (function() {
            var theme = localStorage.getItem('admin-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
    <title>Admin Login - Argo Books</title>

    <link rel="stylesheet" href="common-style.css">
    <link rel="stylesheet" href="../resources/styles/button.css">
    <link rel="stylesheet" href="../resources/styles/custom-colors.css">
</head>

<body>
    <div class="login-container">
        <?php if ($show_2fa_form): ?>
            <div class="login-header">
                <h1>Two-Factor Authentication</h1>
                <p>Please enter the verification code from your authenticator app</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" id="verification-form">
                <div class="form-group">
                    <label for="verification_code">Verification Code</label>
                    <input type="number" id="verification_code" name="verification_code" class="verification-code" required autofocus placeholder="000000" min="0" max="999999">
                </div>

                <input type="hidden" name="verify_code" value="1">

                <div class="center">
                    <button type="button" onclick="submitVerificationForm()" id="submit-button" class="btn btn-blue">Verify</button>
                </div>

                <div class="back-to-login">
                    <a href="logout.php">Cancel and return to login</a>
                </div>
            </form>
        <?php else: ?>
            <div class="login-header">
                <h1>Admin Login</h1>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="center">
                    <button type="submit" name="login" class="btn btn-blue">Login</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($show_2fa_form): ?>
        <script>
            // Simple function to submit the verification form
            function submitVerificationForm() {
                document.getElementById('verification-form').submit();
            }

            document.addEventListener('DOMContentLoaded', function() {
                var codeInput = document.getElementById('verification_code');

                if (codeInput) {
                    codeInput.addEventListener('input', function() {
                        // Force numeric only
                        this.value = this.value.replace(/[^0-9]/g, '');

                        // Auto-submit on 6 digits
                        if (this.value.length === 6) {
                            // Add a small delay so user sees the 6th digit
                            setTimeout(function() {
                                submitVerificationForm();
                            }, 300);
                        }
                    });

                    // Prevent arrow keys
                    codeInput.addEventListener('keydown', function(e) {
                        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                            e.preventDefault();
                        }
                        // Also submit on Enter key when 6 digits entered
                        if (e.key === 'Enter' && this.value.length === 6) {
                            e.preventDefault();
                            submitVerificationForm();
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>