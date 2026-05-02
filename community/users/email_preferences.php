<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/user_functions.php';

require_login();

$user_id = (int) $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Load current prefs
$stmt = $pdo->prepare('SELECT email, email_pref_product_updates, email_pref_tips_onboarding,
                              email_pref_reviews, email_pref_promotions, email_pref_community_digest
                       FROM community_users WHERE id = ?');
$stmt->execute([$user_id]);
$prefs = $stmt->fetch();

if (!$prefs) {
    // Logged in but no row — shouldn't happen, but bail safely
    header('Location: login.php');
    exit;
}

// Check whether this user is also a license-key holder (drives the "reviews" notice)
$stmt = $pdo->prepare('SELECT 1 FROM license_keys WHERE email = ? LIMIT 1');
$stmt->execute([$prefs['email']]);
$is_license_holder = (bool) $stmt->fetchColumn();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_updates  = isset($_POST['email_pref_product_updates']) ? 1 : 0;
    $tips_onboarding  = isset($_POST['email_pref_tips_onboarding']) ? 1 : 0;
    $reviews          = isset($_POST['email_pref_reviews']) ? 1 : 0;
    $promotions       = isset($_POST['email_pref_promotions']) ? 1 : 0;
    $community_digest = isset($_POST['email_pref_community_digest']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare('UPDATE community_users
                               SET email_pref_product_updates = ?,
                                   email_pref_tips_onboarding = ?,
                                   email_pref_reviews = ?,
                                   email_pref_promotions = ?,
                                   email_pref_community_digest = ?
                               WHERE id = ?');
        $stmt->execute([$product_updates, $tips_onboarding, $reviews, $promotions, $community_digest, $user_id]);

        $prefs['email_pref_product_updates']  = $product_updates;
        $prefs['email_pref_tips_onboarding']  = $tips_onboarding;
        $prefs['email_pref_reviews']          = $reviews;
        $prefs['email_pref_promotions']       = $promotions;
        $prefs['email_pref_community_digest'] = $community_digest;

        $success_message = 'Email preferences updated successfully.';
    } catch (PDOException $e) {
        error_log('email_preferences update failed: ' . $e->getMessage());
        $error_message = 'Failed to update email preferences. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
    <title>Email Preferences - Argo Community</title>

    <script src="../../resources/scripts/jquery-3.6.0.js"></script>
    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="auth.css">
    <link rel="stylesheet" href="email_preferences-style.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/checkbox.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
    <link rel="stylesheet" href="../../resources/header/dark.css">
</head>

<body>
    <header>
        <div id="includeHeader"></div>
    </header>

    <div class="wrapper">
        <div class="prefs-container">
            <div class="prefs-header">
                <h1>Email Preferences</h1>
                <p class="subtitle">Choose which emails you'd like to receive from Argo Books.</p>
            </div>

            <?php if ($success_message): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="post" class="prefs-form">
                <div class="prefs-section">
                    <h2>Account &amp; transactional emails</h2>
                    <ul class="transactional-list">
                        <li><span class="always-on-tag">Always on</span><span class="label">Email verification</span></li>
                        <li><span class="always-on-tag">Always on</span><span class="label">Password reset</span></li>
                        <li><span class="always-on-tag">Always on</span><span class="label">Payment receipts</span></li>
                        <li><span class="always-on-tag">Always on</span><span class="label">License key delivery</span></li>
                        <li><span class="always-on-tag">Always on</span><span class="label">Subscription renewal &amp; payment-failed notices</span></li>
                        <li><span class="always-on-tag">Always on</span><span class="label">Account deletion warnings</span></li>
                    </ul>
                    <div class="info-note">
                        These keep your account, payments, and recovery working &mdash; turning them off would break things like password resets and receipt delivery.
                    </div>
                </div>

                <div class="prefs-section">
                    <h2>Marketing emails</h2>

                    <div class="checkbox">
                        <input type="checkbox" id="email_pref_product_updates" name="email_pref_product_updates"
                               <?php echo $prefs['email_pref_product_updates'] ? 'checked' : ''; ?>>
                        <label for="email_pref_product_updates">Product updates</label>
                    </div>
                    <p class="setting-description">New features, release notes, and what's new in Argo Books.</p>

                    <div class="checkbox">
                        <input type="checkbox" id="email_pref_tips_onboarding" name="email_pref_tips_onboarding"
                               <?php echo $prefs['email_pref_tips_onboarding'] ? 'checked' : ''; ?>>
                        <label for="email_pref_tips_onboarding">Tips &amp; onboarding</label>
                    </div>
                    <p class="setting-description">How-to guides and getting-started nudges, mostly useful when you're new.</p>

                    <div class="checkbox">
                        <input type="checkbox" id="email_pref_reviews" name="email_pref_reviews"
                               <?php echo $prefs['email_pref_reviews'] ? 'checked' : ''; ?>>
                        <label for="email_pref_reviews">Review requests</label>
                    </div>
                    <p class="setting-description">An occasional ask to leave a review on Capterra or share feedback directly.</p>

                    <?php if ($is_license_holder): ?>
                        <div class="review-note">
                            <strong>Heads up:</strong> if you've purchased Argo Books, we may still ask you once for a review even with this off, since you're a paying customer. You can unsubscribe from that email itself when you receive it.
                        </div>
                    <?php endif; ?>

                    <div class="checkbox">
                        <input type="checkbox" id="email_pref_promotions" name="email_pref_promotions"
                               <?php echo $prefs['email_pref_promotions'] ? 'checked' : ''; ?>>
                        <label for="email_pref_promotions">Promotions &amp; offers</label>
                    </div>
                    <p class="setting-description">Discount codes and Premium upsells. Sent rarely.</p>

                    <div class="checkbox">
                        <input type="checkbox" id="email_pref_community_digest" name="email_pref_community_digest"
                               <?php echo $prefs['email_pref_community_digest'] ? 'checked' : ''; ?>>
                        <label for="email_pref_community_digest">Community digest</label>
                    </div>
                    <p class="setting-description">Replies to your community posts and comments, plus interesting activity.</p>
                </div>

                <div class="form-actions">
                    <a href="profile.php" class="btn btn-black">Back to Profile</a>
                    <button type="submit" class="btn btn-blue">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div id="includeFooter"></div>
    </footer>
</body>

</html>
