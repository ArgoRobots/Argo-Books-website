<?php
/**
 * OAuth Callback Handler
 *
 * GET /api/portal/connect/callback/{provider} - Handle OAuth redirect from payment provider
 *
 * This endpoint is called by the browser after the user authorizes on the provider's site.
 * It verifies the CSRF state token, exchanges the authorization code for credentials,
 * stores them in portal_companies, and shows a result page.
 */

require_once __DIR__ . '/portal-helper.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// This is a browser redirect, not an API call — output HTML
header('Content-Type: text/html; charset=utf-8');

$provider = $_GET['provider'] ?? '';
if (!in_array($provider, ['stripe', 'paypal', 'square'])) {
    show_result_page(false, 'Invalid provider.');
    exit;
}

// PayPal uses direct email entry instead of OAuth
$mode = $_GET['mode'] ?? '';
if ($provider === 'paypal' && $mode === 'email_entry') {
    handle_paypal_email_entry();
    exit;
}

// Check for errors from the provider (user denied, etc.)
$error = $_GET['error'] ?? $_GET['error_description'] ?? '';
if (!empty($error)) {
    show_result_page(false, 'Authorization was denied: ' . $error);
    exit;
}

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';

// Stripe Account Links flow only returns state (no code). Other providers require both.
if ($provider !== 'stripe' && (empty($code) || empty($state))) {
    show_result_page(false, 'Missing authorization code or state parameter.');
    exit;
}
if (empty($state)) {
    show_result_page(false, 'Missing state parameter.');
    exit;
}

// Verify the CSRF state token and look up the company
$db = get_db_connection();
$stmt = $db->prepare(
    'SELECT os.id AS state_id, os.company_id, pc.company_name
     FROM portal_oauth_states os
     JOIN portal_companies pc ON os.company_id = pc.id
     WHERE os.state_token = ? AND os.provider = ? AND os.expires_at > NOW()
     LIMIT 1'
);
$stmt->bind_param('ss', $state, $provider);
$stmt->execute();
$oauthState = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$oauthState) {
    $db->close();
    show_result_page(false, 'Invalid or expired authorization state. Please try connecting again from Argo Books.');
    exit;
}

$companyId = $oauthState['company_id'];
$companyName = $oauthState['company_name'];
$stateId = $oauthState['state_id'];
$is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';
$callbackBase = rtrim($_ENV['PORTAL_BASE_URL'] ?? 'https://argorobots.com', '/');

$isRefresh = isset($_GET['refresh']);

try {
    switch ($provider) {
        case 'stripe':
            $callbackUrl = "$callbackBase/api/portal/connect/callback/stripe";
            $result = handle_stripe_callback($db, $companyId, $is_production, $isRefresh, $callbackUrl, $state);
            if ($result === 'redirect') {
                // Onboarding incomplete — keep the state token alive for the next callback
                $db->close();
                exit;
            }
            break;
        case 'paypal':
            handle_paypal_callback($db, $companyId, $code, $is_production);
            break;
        case 'square':
            handle_square_callback($db, $companyId, $code, $is_production);
            break;
    }

    // Delete the used state token (and clean up expired ones)
    $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE id = ?');
    $stmt->bind_param('i', $stateId);
    $stmt->execute();
    $stmt->close();
    $db->query('DELETE FROM portal_oauth_states WHERE expires_at <= NOW()');

    $db->close();
    show_result_page(true, ucfirst($provider) . ' has been connected successfully!', $companyName);
} catch (Exception $e) {
    // Clean up state token even on failure
    $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE id = ?');
    $stmt->bind_param('i', $stateId);
    $stmt->execute();
    $stmt->close();

    $db->close();
    error_log("OAuth callback error ($provider): " . $e->getMessage());
    show_result_page(false, 'Failed to complete connection: ' . $e->getMessage());
}

/**
 * Handle PayPal email entry flow.
 * Shows a form for the user to enter their PayPal email, validates the state token,
 * and saves the email directly (no OAuth needed).
 */
function handle_paypal_email_entry(): void
{
    $state = $_GET['state'] ?? '';
    if (empty($state)) {
        show_result_page(false, 'Missing state parameter.');
        return;
    }

    // On POST, process the submitted email
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['paypal_email'] ?? '');
        $submittedState = $_POST['state'] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            show_paypal_email_form($state, 'Please enter a valid email address.');
            return;
        }

        if ($submittedState !== $state) {
            show_result_page(false, 'Invalid state token. Please try connecting again from Argo Books.');
            return;
        }

        // Verify the CSRF state token and look up the company
        $db = get_db_connection();
        $stmt = $db->prepare(
            'SELECT os.id AS state_id, os.company_id, pc.company_name
             FROM portal_oauth_states os
             JOIN portal_companies pc ON os.company_id = pc.id
             WHERE os.state_token = ? AND os.provider = ? AND os.expires_at > NOW()
             LIMIT 1'
        );
        $provider = 'paypal';
        $stmt->bind_param('ss', $submittedState, $provider);
        $stmt->execute();
        $oauthState = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$oauthState) {
            $db->close();
            show_result_page(false, 'Invalid or expired state. Please try connecting again from Argo Books.');
            return;
        }

        $companyId = $oauthState['company_id'];
        $companyName = $oauthState['company_name'];
        $stateId = $oauthState['state_id'];

        // Save the PayPal email (use email as merchant_id since we don't have a payer_id)
        $stmt = $db->prepare(
            'UPDATE portal_companies
             SET paypal_merchant_id = ?, paypal_email = ?, updated_at = NOW()
             WHERE id = ?'
        );
        $stmt->bind_param('ssi', $email, $email, $companyId);
        $stmt->execute();
        $stmt->close();

        // Delete the used state token
        $stmt = $db->prepare('DELETE FROM portal_oauth_states WHERE id = ?');
        $stmt->bind_param('i', $stateId);
        $stmt->execute();
        $stmt->close();
        $db->query('DELETE FROM portal_oauth_states WHERE expires_at <= NOW()');
        $db->close();

        show_result_page(true, 'PayPal has been connected successfully!', $companyName);
        return;
    }

    // On GET, verify the state is valid before showing the form
    $db = get_db_connection();
    $stmt = $db->prepare(
        'SELECT os.id FROM portal_oauth_states os
         WHERE os.state_token = ? AND os.provider = ? AND os.expires_at > NOW()
         LIMIT 1'
    );
    $provider = 'paypal';
    $stmt->bind_param('ss', $state, $provider);
    $stmt->execute();
    $valid = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();

    if (!$valid) {
        show_result_page(false, 'Invalid or expired link. Please try connecting again from Argo Books.');
        return;
    }

    show_paypal_email_form($state);
}

/**
 * Show the PayPal email entry form.
 */
function show_paypal_email_form(string $state, string $error = ''): void
{
    $stateHtml = htmlspecialchars($state);
    $errorHtml = $error ? '<p class="form-error">' . htmlspecialchars($error) . '</p>' : '';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Connect PayPal - Argo Books</title>
    <link rel="shortcut icon" type="image/x-icon" href="/resources/images/argo-logo/A-logo.ico">
    <link rel="stylesheet" href="/resources/styles/custom-colors.css">
    <link rel="stylesheet" href="/portal/style.css">
    <style>
        .paypal-form-container { text-align: center; padding: 40px 20px; max-width: 480px; margin: 0 auto; }
        .paypal-form-container h2 { font-size: 22px; font-weight: 600; color: var(--gray-900, #111); margin: 0 0 8px; }
        .paypal-form-container p.subtitle { color: var(--gray-600, #555); font-size: 14px; margin: 0 0 24px; line-height: 1.5; }
        .paypal-form-container form { text-align: left; }
        .paypal-form-container label { display: block; font-size: 14px; font-weight: 500; color: var(--gray-700, #333); margin-bottom: 6px; }
        .paypal-form-container input[type="email"] {
            width: 100%; padding: 10px 12px; font-size: 15px; border: 1px solid var(--gray-300, #ccc);
            border-radius: 6px; box-sizing: border-box; outline: none; transition: border-color 0.2s;
        }
        .paypal-form-container input[type="email"]:focus { border-color: var(--primary, #003087); box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.1); }
        .paypal-form-container button {
            width: 100%; padding: 12px; font-size: 15px; font-weight: 600; color: #fff;
            background: #0070ba; border: none; border-radius: 6px; cursor: pointer;
            margin-top: 16px; transition: background 0.2s;
        }
        .paypal-form-container button:hover { background: #003087; }
        .form-error { color: #ef4444; font-size: 13px; margin: 8px 0 0; }
        .paypal-icon { margin-bottom: 16px; }
        .form-hint { color: var(--gray-400, #999); font-size: 12px; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="portal-page">
        <header class="portal-header">
            <div class="portal-header-inner">
                <div class="company-info">
                    <h1 class="company-name">Payment Portal</h1>
                    <span class="portal-subtitle">Powered by Argo Books</span>
                </div>
            </div>
        </header>
        <main class="portal-main">
            <div class="paypal-form-container">
                <div class="paypal-icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="#0070ba" opacity="0.1"/><circle cx="28" cy="28" r="20" fill="#0070ba" opacity="0.15"/><text x="28" y="34" text-anchor="middle" font-size="20" font-weight="bold" fill="#003087">P</text></svg>
                </div>
                <h2>Connect PayPal</h2>
                <p class="subtitle">Enter the PayPal email address where you'd like to receive payments.</p>
                <?= $errorHtml ?>
                <form method="POST">
                    <input type="hidden" name="state" value="<?= $stateHtml ?>">
                    <label for="paypal_email">PayPal Email Address</label>
                    <input type="email" id="paypal_email" name="paypal_email" required placeholder="you@example.com" autofocus>
                    <p class="form-hint">This should be the email associated with your PayPal Business or Personal account.</p>
                    <button type="submit">Connect PayPal</button>
                </form>
            </div>
        </main>
        <footer class="portal-footer">
            <p>Secure payments powered by <a href="https://argorobots.com" target="_blank" rel="noopener">Argo Books</a></p>
        </footer>
    </div>
</body>
</html>
    <?php
}

/**
 * Handle Stripe Account Links callback.
 * Retrieves the Express account, checks onboarding status, and saves email if complete.
 * Returns 'redirect' if onboarding is incomplete and the user was redirected back to Stripe.
 */
function handle_stripe_callback(mysqli $db, int $companyId, bool $is_production, bool $isRefresh, string $callbackUrl, string $state): ?string
{
    $secretKey = $is_production
        ? ($_ENV['STRIPE_LIVE_SECRET_KEY'] ?? '')
        : ($_ENV['STRIPE_SANDBOX_SECRET_KEY'] ?? '');
    \Stripe\Stripe::setApiKey($secretKey);

    // Look up the stored Stripe account ID for this company
    $stmt = $db->prepare('SELECT stripe_account_id FROM portal_companies WHERE id = ?');
    $stmt->bind_param('i', $companyId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $stripeAccountId = $row['stripe_account_id'] ?? '';
    if (empty($stripeAccountId)) {
        throw new Exception('No Stripe account found. Please initiate the connection again from Argo Books.');
    }

    // Retrieve the Express account to check onboarding status
    $account = \Stripe\Account::retrieve($stripeAccountId);

    if (!$account->details_submitted || $isRefresh) {
        // Onboarding incomplete or user requested refresh — send them back to Stripe
        $accountLink = \Stripe\AccountLink::create([
            'account' => $stripeAccountId,
            'return_url' => $callbackUrl . '?state=' . $state,
            'refresh_url' => $callbackUrl . '?state=' . $state . '&refresh=1',
            'type' => 'account_onboarding',
        ]);
        header('Location: ' . $accountLink->url);
        return 'redirect';
    }

    // Onboarding complete — save the account email
    $email = $account->email ?? null;
    $stmt = $db->prepare(
        'UPDATE portal_companies
         SET stripe_email = ?, updated_at = NOW()
         WHERE id = ?'
    );
    $stmt->bind_param('si', $email, $companyId);
    $stmt->execute();
    $stmt->close();

    return null;
}

/**
 * Handle PayPal OAuth callback.
 * Exchanges the authorization code for tokens, then retrieves the merchant/payer ID and email.
 */
function handle_paypal_callback(mysqli $db, int $companyId, string $code, bool $is_production): void
{
    $clientId = $is_production
        ? $_ENV['PAYPAL_LIVE_CLIENT_ID']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_ID'];
    $clientSecret = $is_production
        ? $_ENV['PAYPAL_LIVE_CLIENT_SECRET']
        : $_ENV['PAYPAL_SANDBOX_CLIENT_SECRET'];
    $apiBase = $is_production
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';

    // Exchange authorization code for access token
    $ch = curl_init("$apiBase/v1/oauth2/token");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_USERPWD => "$clientId:$clientSecret",
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $error = json_decode($response, true);
        throw new Exception($error['error_description'] ?? 'PayPal token exchange failed.');
    }

    $tokenData = json_decode($response, true);
    $accessToken = $tokenData['access_token'] ?? '';

    if (empty($accessToken)) {
        throw new Exception('No access token received from PayPal.');
    }

    // Get user info to extract merchant/payer ID and email
    $ch = curl_init("$apiBase/v1/identity/openidconnect/userinfo?schema=openIdConnect");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $accessToken",
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception('Failed to retrieve PayPal user info.');
    }

    $userInfo = json_decode($response, true);
    $payerId = $userInfo['payer_id'] ?? $userInfo['user_id'] ?? '';
    $email = $userInfo['email'] ?? null;

    if (empty($payerId)) {
        throw new Exception('No PayPal merchant/payer ID received.');
    }

    // Store credentials
    $stmt = $db->prepare(
        'UPDATE portal_companies
         SET paypal_merchant_id = ?, paypal_email = ?, updated_at = NOW()
         WHERE id = ?'
    );
    $stmt->bind_param('ssi', $payerId, $email, $companyId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Handle Square OAuth callback.
 * Exchanges the authorization code for access token, merchant ID, and location.
 */
function handle_square_callback(mysqli $db, int $companyId, string $code, bool $is_production): void
{
    $appId = $is_production
        ? ($_ENV['SQUARE_LIVE_APP_ID'] ?? '')
        : ($_ENV['SQUARE_SANDBOX_APP_ID'] ?? '');
    $appSecret = $is_production
        ? ($_ENV['SQUARE_LIVE_APP_SECRET'] ?? '')
        : ($_ENV['SQUARE_SANDBOX_APP_SECRET'] ?? '');
    $apiBase = $is_production
        ? 'https://connect.squareup.com'
        : 'https://connect.squareupsandbox.com';

    // Exchange authorization code for access token
    $ch = curl_init("$apiBase/oauth2/token");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]),
        CURLOPT_HTTPHEADER => [
            'Square-Version: 2025-10-16',
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $error = json_decode($response, true);
        $errorMsg = $error['errors'][0]['detail'] ?? ($error['message'] ?? 'Square token exchange failed.');
        throw new Exception($errorMsg);
    }

    $tokenData = json_decode($response, true);
    $accessToken = $tokenData['access_token'] ?? '';
    $merchantId = $tokenData['merchant_id'] ?? '';

    if (empty($accessToken) || empty($merchantId)) {
        throw new Exception('Missing access token or merchant ID from Square.');
    }

    // Retrieve primary location and business email
    $email = null;
    $locationId = null;

    $ch = curl_init("$apiBase/v2/locations");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Square-Version: 2025-10-16',
            "Authorization: Bearer $accessToken",
        ],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $locationsData = json_decode($response, true);
        $locations = $locationsData['locations'] ?? [];

        // Prefer the first active location
        foreach ($locations as $location) {
            if (($location['status'] ?? '') === 'ACTIVE') {
                $locationId = $location['id'];
                if (!empty($location['business_email'])) {
                    $email = $location['business_email'];
                }
                break;
            }
        }

        // Fallback to first location if none are active
        if (!$locationId && !empty($locations)) {
            $locationId = $locations[0]['id'];
            if (!$email && !empty($locations[0]['business_email'])) {
                $email = $locations[0]['business_email'];
            }
        }
    }

    // Store credentials
    $stmt = $db->prepare(
        'UPDATE portal_companies
         SET square_merchant_id = ?, square_access_token = ?, square_location_id = ?,
             square_email = ?, updated_at = NOW()
         WHERE id = ?'
    );
    $stmt->bind_param('ssssi', $merchantId, $accessToken, $locationId, $email, $companyId);
    $stmt->execute();
    $stmt->close();
}

/**
 * Show an HTML result page after the OAuth callback.
 * The user sees this in their browser and can close it to return to Argo Books.
 */
function show_result_page(bool $success, string $message, string $companyName = ''): void
{
    $statusClass = $success ? 'success' : 'error';
    $statusText = $success ? 'Connection Successful' : 'Connection Failed';
    $iconColor = $success ? '#22c55e' : '#ef4444';
    $iconSvg = $success
        ? '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="' . $iconColor . '" opacity="0.1"/><circle cx="28" cy="28" r="20" fill="' . $iconColor . '" opacity="0.15"/><path d="M20 28.5L25.5 34L36 22" stroke="' . $iconColor . '" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>'
        : '<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><circle cx="28" cy="28" r="28" fill="' . $iconColor . '" opacity="0.1"/><circle cx="28" cy="28" r="20" fill="' . $iconColor . '" opacity="0.15"/><path d="M22 22L34 34M34 22L22 34" stroke="' . $iconColor . '" stroke-width="3" stroke-linecap="round"/></svg>';

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($statusText) ?> - Argo Books</title>
    <link rel="shortcut icon" type="image/x-icon" href="/resources/images/argo-logo/A-logo.ico">
    <link rel="stylesheet" href="/resources/styles/custom-colors.css">
    <link rel="stylesheet" href="/portal/style.css">
    <style>
        .callback-result { text-align: center; padding: 60px 20px; max-width: 480px; margin: 0 auto; }
        .callback-icon { margin-bottom: 20px; }
        .callback-title { font-size: 22px; font-weight: 600; color: var(--gray-900, #111); margin: 0; }
        .callback-message { color: var(--gray-600, #555); font-size: 15px; margin-top: 12px; line-height: 1.5; }
        .callback-hint { color: var(--gray-400, #999); font-size: 13px; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="portal-page">
        <header class="portal-header">
            <div class="portal-header-inner">
                <div class="company-info">
                    <h1 class="company-name">Payment Portal</h1>
                    <span class="portal-subtitle">Powered by Argo Books</span>
                </div>
            </div>
        </header>
        <main class="portal-main">
            <div class="callback-result">
                <div class="callback-icon"><?= $iconSvg ?></div>
                <h2 class="callback-title"><?= htmlspecialchars($statusText) ?></h2>
                <p class="callback-message"><?= htmlspecialchars($message) ?></p>
                <p class="callback-hint">You can close this window and return to Argo Books.</p>
            </div>
        </main>
        <footer class="portal-footer">
            <p>Secure payments powered by <a href="https://argorobots.com" target="_blank" rel="noopener">Argo Books</a></p>
        </footer>
    </div>
</body>
</html>
    <?php
}
