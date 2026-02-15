<?php
/**
 * Provider Connect/Disconnect API Endpoint
 *
 * POST /api/portal/connect/{provider} - Initiate OAuth flow for a payment provider
 * DELETE /api/portal/connect/{provider} - Disconnect a payment provider
 *
 * Requires API key authentication (Argo Books -> Server).
 * Provider must be one of: stripe, paypal, square.
 */

require_once __DIR__ . '/portal-helper.php';
require_once __DIR__ . '/../../vendor/autoload.php';

set_portal_headers();
require_method(['POST', 'DELETE']);

// Authenticate the request
$company = authenticate_portal_request();
if (!$company) {
    send_error_response(401, 'Invalid or missing API key.', 'UNAUTHORIZED');
}

// Get provider from URL (set by .htaccess rewrite rule)
$provider = $_GET['provider'] ?? '';
if (!in_array($provider, ['stripe', 'paypal', 'square'])) {
    send_error_response(400, 'Invalid provider. Must be one of: stripe, paypal, square.', 'INVALID_PROVIDER');
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    disconnect_provider($company, $provider);
} else {
    initiate_connect($company, $provider);
}

/**
 * Initiate OAuth flow for a payment provider.
 * Generates a CSRF state token, stores it, and returns the authorization URL.
 */
function initiate_connect(array $company, string $provider): void
{
    $is_production = ($_ENV['APP_ENV'] ?? 'sandbox') === 'production';
    $callbackBase = rtrim($_ENV['PORTAL_BASE_URL'] ?? 'https://argorobots.com', '/');
    $callbackUrl = "$callbackBase/api/portal/connect/callback/$provider";

    // Generate CSRF state token
    $state = bin2hex(random_bytes(32));

    // Store state in DB with 10-minute expiry
    $db = get_db_connection();
    $expiresAt = date('Y-m-d H:i:s', time() + 600);
    $stmt = $db->prepare(
        'INSERT INTO portal_oauth_states (company_id, provider, state_token, expires_at)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('isss', $company['id'], $provider, $state, $expiresAt);
    $stmt->execute();
    $stmt->close();

    // Clean up expired states
    $db->query('DELETE FROM portal_oauth_states WHERE expires_at <= NOW()');
    $db->close();

    $authUrl = '';

    switch ($provider) {
        case 'stripe':
            $secretKey = $is_production
                ? ($_ENV['STRIPE_LIVE_SECRET_KEY'] ?? '')
                : ($_ENV['STRIPE_SANDBOX_SECRET_KEY'] ?? '');
            if (empty($secretKey)) {
                send_error_response(500, 'Stripe Connect is not configured on the server.', 'PROVIDER_NOT_CONFIGURED');
            }

            try {
                \Stripe\Stripe::setApiKey($secretKey);

                // Check if this company already has a Stripe Express account
                $stripeAccountId = null;
                $dbCheck = get_db_connection();
                $stmtCheck = $dbCheck->prepare(
                    'SELECT stripe_account_id FROM portal_companies WHERE id = ?'
                );
                $stmtCheck->bind_param('i', $company['id']);
                $stmtCheck->execute();
                $row = $stmtCheck->get_result()->fetch_assoc();
                $stmtCheck->close();
                $dbCheck->close();

                if (!empty($row['stripe_account_id'])) {
                    $stripeAccountId = $row['stripe_account_id'];
                } else {
                    // Create a new Express connected account
                    $account = \Stripe\Account::create([
                        'type' => 'express',
                        'capabilities' => [
                            'card_payments' => ['requested' => true],
                            'transfers' => ['requested' => true],
                        ],
                    ]);
                    $stripeAccountId = $account->id;

                    // Store the account ID immediately
                    $dbStore = get_db_connection();
                    $stmtStore = $dbStore->prepare(
                        'UPDATE portal_companies SET stripe_account_id = ?, updated_at = NOW() WHERE id = ?'
                    );
                    $stmtStore->bind_param('si', $stripeAccountId, $company['id']);
                    $stmtStore->execute();
                    $stmtStore->close();
                    $dbStore->close();
                }

                // Create an Account Link for Express onboarding
                $accountLink = \Stripe\AccountLink::create([
                    'account' => $stripeAccountId,
                    'return_url' => $callbackUrl . '?state=' . $state,
                    'refresh_url' => $callbackUrl . '?state=' . $state . '&refresh=1',
                    'type' => 'account_onboarding',
                ]);
                $authUrl = $accountLink->url;
            } catch (\Stripe\Exception\ApiErrorException $e) {
                error_log('Stripe Connect error: ' . $e->getMessage());
                send_error_response(500, 'Stripe error: ' . $e->getMessage(), 'STRIPE_API_ERROR');
            }
            break;

        case 'paypal':
            $clientId = $is_production
                ? ($_ENV['PAYPAL_LIVE_CLIENT_ID'] ?? '')
                : ($_ENV['PAYPAL_SANDBOX_CLIENT_ID'] ?? '');
            if (empty($clientId)) {
                send_error_response(500, 'PayPal is not configured on the server.', 'PROVIDER_NOT_CONFIGURED');
            }
            $paypalBase = $is_production
                ? 'https://www.paypal.com'
                : 'https://www.sandbox.paypal.com';
            $authUrl = "$paypalBase/signin/authorize?" . http_build_query([
                'flowEntry' => 'static',
                'client_id' => $clientId,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'redirect_uri' => $callbackUrl,
                'state' => $state,
            ]);
            break;

        case 'square':
            $appId = $is_production
                ? ($_ENV['SQUARE_LIVE_APP_ID'] ?? '')
                : ($_ENV['SQUARE_SANDBOX_APP_ID'] ?? '');
            if (empty($appId)) {
                send_error_response(500, 'Square is not configured on the server.', 'PROVIDER_NOT_CONFIGURED');
            }
            $squareBase = $is_production
                ? 'https://connect.squareup.com'
                : 'https://connect.squareupsandbox.com';
            $authUrl = "$squareBase/oauth2/authorize?" . http_build_query([
                'client_id' => $appId,
                'scope' => 'PAYMENTS_WRITE PAYMENTS_READ MERCHANT_PROFILE_READ',
                'session' => 'false',
                'state' => $state,
            ]);
            break;
    }

    send_json_response(200, [
        'success' => true,
        'authUrl' => $authUrl,
        'message' => 'Redirect to ' . ucfirst($provider) . ' to authorize.',
        'timestamp' => date('c')
    ]);
}

/**
 * Disconnect a payment provider by clearing its credentials from the database.
 */
function disconnect_provider(array $company, string $provider): void
{
    $db = get_db_connection();

    switch ($provider) {
        case 'stripe':
            $stmt = $db->prepare(
                'UPDATE portal_companies SET stripe_account_id = NULL, stripe_email = NULL, updated_at = NOW() WHERE id = ?'
            );
            break;
        case 'paypal':
            $stmt = $db->prepare(
                'UPDATE portal_companies SET paypal_merchant_id = NULL, paypal_email = NULL, updated_at = NOW() WHERE id = ?'
            );
            break;
        case 'square':
            $stmt = $db->prepare(
                'UPDATE portal_companies SET square_merchant_id = NULL, square_access_token = NULL, square_location_id = NULL, square_email = NULL, updated_at = NOW() WHERE id = ?'
            );
            break;
    }

    $stmt->bind_param('i', $company['id']);
    $stmt->execute();
    $stmt->close();
    $db->close();

    send_json_response(200, [
        'success' => true,
        'message' => ucfirst($provider) . ' has been disconnected.',
        'timestamp' => date('c')
    ]);
}
