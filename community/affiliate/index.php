<?php
session_start();
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../community_functions.php';
require_once __DIR__ . '/../users/user_functions.php';
require_once __DIR__ . '/affiliate_functions.php';
require_once __DIR__ . '/affiliate_emails.php';

// Affiliates are community members, so reuse community auth.
require_login();

$env = current_environment();
$user_id = (int) $_SESSION['user_id'];
$user = get_user($user_id);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$success_message = '';
if (isset($_SESSION['affiliate_success'])) {
    $success_message = $_SESSION['affiliate_success'];
    unset($_SESSION['affiliate_success']);
}

$affiliate = get_affiliate_for_user($user_id, $env);

// Handle the application submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!is_string($posted_token) || !hash_equals($_SESSION['csrf_token'] ?? '', $posted_token)) {
        $error_message = 'Invalid request. Please try again.';
    } elseif (($_POST['action'] ?? '') === 'apply') {
        if ($affiliate) {
            $error_message = 'You have already applied.';
        } else {
            $payout_email = trim($_POST['payout_email'] ?? '');
            $reason = trim($_POST['application_reason'] ?? '');
            $promo_url = trim($_POST['promo_url'] ?? '');

            if (!filter_var($payout_email, FILTER_VALIDATE_EMAIL)) {
                $error_message = 'Enter a valid PayPal email so we can pay your commission.';
            } elseif ($reason === '') {
                $error_message = 'Tell us a little about how you plan to promote Argo Books.';
            } elseif ($promo_url !== '' && !filter_var($promo_url, FILTER_VALIDATE_URL)) {
                $error_message = 'The promotion link does not look like a valid URL.';
            } else {
                // Reserve the source_code now; the referral_links row is created
                // on approval so the link stays dead until then.
                $source_code = generate_affiliate_source_code($user['username']);
                try {
                    $stmt = $pdo->prepare('INSERT INTO affiliates (user_id, source_code, status, payout_method, payout_email, application_reason, promo_url, environment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$user_id, $source_code, 'pending', 'paypal', $payout_email, $reason, $promo_url, $env]);
                    send_affiliate_application_received_email($user['email'], $user['username']);
                    $_SESSION['affiliate_success'] = 'Application submitted! We\'ll email you once it\'s reviewed.';
                    header('Location: index.php');
                    exit;
                } catch (PDOException $e) {
                    // UNIQUE(user_id, environment) race, or a code collision.
                    $error_message = 'You have already applied, or that link is taken. Please reload.';
                }
            }
        }
    }
}

// Compute dashboard figures only when approved.
$money = null;
$stats = null;
if ($affiliate && $affiliate['status'] === 'approved') {
    $money = affiliate_money_summary($affiliate, $env);
    $stats = get_affiliate_stats($affiliate['source_code'], $env);
    $referral_url = affiliate_referral_url($affiliate['source_code']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Program - Argo Books</title>
    <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">

    <script src="../../resources/scripts/main.js"></script>

    <link rel="stylesheet" href="affiliate.css">
    <link rel="stylesheet" href="../../resources/styles/button.css">
    <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
    <link rel="stylesheet" href="../../resources/styles/link.css">
    <link rel="stylesheet" href="../../resources/header/style.css">
    <link rel="stylesheet" href="../../resources/header/dark.css">
    <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body>
    <header>
        <?php include __DIR__ . '/../../resources/header/header.php'; ?>
    </header>

    <main class="affiliate-main">
        <?php if (!empty($success_message)): ?>
            <div class="affiliate-banner affiliate-banner-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="affiliate-banner affiliate-banner-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if (!$affiliate): ?>
            <!-- Application form -->
            <section class="affiliate-card">
                <h1>Become an Argo Books affiliate</h1>
                <p class="affiliate-lead">Earn <strong>50% of every payment</strong> from customers you refer, for the first 12 months of their subscription. Share your link, make videos, and get paid.</p>
                <ul class="affiliate-perks">
                    <li>50% commission on initial and renewal payments</li>
                    <li>Paid for the first 12 months of each subscription</li>
                    <li>Real-time dashboard for clicks, signups, and earnings</li>
                </ul>

                <form method="POST" class="affiliate-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action" value="apply">

                    <div class="affiliate-field">
                        <label for="payout_email">PayPal email for payouts *</label>
                        <input type="email" name="payout_email" id="payout_email" required value="<?php echo htmlspecialchars($_POST['payout_email'] ?? $user['email']); ?>">
                        <small>Where we'll send your commission.</small>
                    </div>

                    <div class="affiliate-field">
                        <label for="promo_url">Where will you promote? (optional)</label>
                        <input type="url" name="promo_url" id="promo_url" placeholder="https://youtube.com/@yourchannel" value="<?php echo htmlspecialchars($_POST['promo_url'] ?? ''); ?>">
                        <small>Your channel, page, or website.</small>
                    </div>

                    <div class="affiliate-field">
                        <label for="application_reason">How do you plan to promote Argo Books? *</label>
                        <textarea name="application_reason" id="application_reason" rows="4" required><?php echo htmlspecialchars($_POST['application_reason'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-blue affiliate-submit">Apply to the program</button>
                </form>
            </section>

        <?php elseif ($affiliate['status'] === 'pending'): ?>
            <section class="affiliate-card affiliate-status">
                <h1>Your application is under review</h1>
                <p>Thanks for applying! We review applications manually, usually within a day or two. We'll email <strong><?php echo htmlspecialchars($user['email']); ?></strong> the moment there's a decision.</p>
            </section>

        <?php elseif ($affiliate['status'] === 'rejected'): ?>
            <section class="affiliate-card affiliate-status">
                <h1>Application not approved</h1>
                <p>Thanks for your interest. We weren't able to approve your application at this time.</p>
                <?php if (!empty($affiliate['review_notes'])): ?>
                    <p><em><?php echo htmlspecialchars($affiliate['review_notes']); ?></em></p>
                <?php endif; ?>
            </section>

        <?php elseif ($affiliate['status'] === 'suspended'): ?>
            <section class="affiliate-card affiliate-status">
                <h1>Your affiliate account is paused</h1>
                <p>Your referral link is currently inactive. Please reach out to support if you think this is a mistake. Any commission you've already earned is safe.</p>
            </section>

        <?php else: // approved -> dashboard ?>
            <section class="affiliate-card">
                <h1>Your affiliate dashboard</h1>
                <p class="affiliate-lead">Share this link anywhere. You earn 50% of every payment your referrals make, for their first 12 months.</p>

                <div class="affiliate-link-row">
                    <input type="text" id="refLink" readonly value="<?php echo htmlspecialchars($referral_url); ?>">
                    <button type="button" class="btn btn-blue" id="copyBtn" onclick="copyRefLink()">Copy</button>
                </div>
            </section>

            <section class="affiliate-stats-grid">
                <div class="affiliate-stat"><span class="affiliate-stat-label">Clicks</span><span class="affiliate-stat-value"><?php echo number_format($stats['clicks']); ?></span></div>
                <div class="affiliate-stat"><span class="affiliate-stat-label">Signups</span><span class="affiliate-stat-value"><?php echo number_format($stats['signups']); ?></span></div>
                <div class="affiliate-stat"><span class="affiliate-stat-label">Paying customers</span><span class="affiliate-stat-value"><?php echo number_format($stats['paying']); ?></span></div>
                <div class="affiliate-stat affiliate-stat-highlight"><span class="affiliate-stat-label">Commission earned</span><span class="affiliate-stat-value">$<?php echo number_format($money['earned'], 2); ?></span></div>
                <div class="affiliate-stat"><span class="affiliate-stat-label">Paid out</span><span class="affiliate-stat-value">$<?php echo number_format($money['paid'], 2); ?></span></div>
                <div class="affiliate-stat affiliate-stat-owed"><span class="affiliate-stat-label">Owed to you</span><span class="affiliate-stat-value">$<?php echo number_format($money['owed'], 2); ?></span></div>
            </section>

            <section class="affiliate-card affiliate-fineprint">
                <p>Amounts are in CAD. Commission is 50% of completed payments within the first 12 months of each referred subscription. Payouts are sent to <strong><?php echo htmlspecialchars($affiliate['payout_email'] ?: $user['email']); ?></strong>. Questions? Contact support.</p>
            </section>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <?php include __DIR__ . '/../../resources/footer/footer.php'; ?>
    </footer>

    <script>
        function copyRefLink() {
            const input = document.getElementById('refLink');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(function () {
                const btn = document.getElementById('copyBtn');
                const original = btn.textContent;
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = original; }, 1500);
            });
        }
    </script>
</body>

</html>
