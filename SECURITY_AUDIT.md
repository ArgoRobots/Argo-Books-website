# Payment System Security Audit

**Date:** 2026-03-12
**Scope:** All payment processing, subscription management, webhook handlers, OAuth flows, and supporting infrastructure

---

## Executive Summary

The Argo Books payment system integrates three payment providers (Stripe, PayPal, Square) across two payment flows: a B2B invoice portal and B2C premium subscriptions. The codebase demonstrates strong security fundamentals including AES-256-GCM encryption, webhook signature verification, prepared statements, and proper SSL/TLS enforcement. This audit identified and fixed **12 security issues** ranging from critical SSRF vectors to medium-severity information disclosure.

---

## Files Audited

### Portal Payment System (B2B)
- `api/portal/checkout.php` - Payment session creation
- `api/portal/process-payment.php` - Payment finalization
- `api/portal/portal-helper.php` - Core helper functions
- `api/portal/webhooks/stripe.php` - Stripe webhook handler
- `api/portal/webhooks/paypal.php` - PayPal webhook handler
- `api/portal/webhooks/square.php` - Square webhook handler
- `api/portal/connect.php` - OAuth initiation
- `api/portal/connect-callback.php` - OAuth callback handler
- `api/portal/register.php` - Company registration
- `api/portal/invoices.php` - Invoice publishing
- `api/portal/status.php` - Connection status
- `api/portal/payments-sync.php` - Payment sync

### Premium Subscriptions (B2C)
- `pricing/premium/checkout/process-subscription.php` - Subscription creation
- `webhooks/paypal-subscription.php` - PayPal subscription webhooks
- `webhooks/paypal-helper.php` - PayPal API helpers
- `cron/subscription_renewal.php` - Renewal processing

### Infrastructure
- `db_connect.php` - Database connection and encryption
- `.htaccess` - Security headers and access control

---

## Vulnerabilities Found & Fixed

### CRITICAL

#### 1. SSRF via PayPal Order ID Injection
- **File:** `api/portal/process-payment.php:216`
- **Issue:** PayPal `order_id` from client input was interpolated directly into a curl URL (`/v2/checkout/orders/$orderId`) without validation. An attacker could craft a malicious order ID containing path traversal characters to redirect the server-side request to arbitrary PayPal API endpoints.
- **Fix:** Added regex validation requiring alphanumeric/hyphen characters only: `preg_match('/^[A-Za-z0-9\-]+$/', $orderId)`

#### 2. SSRF via PayPal Subscription ID Injection
- **File:** `webhooks/paypal-helper.php` (multiple functions)
- **Issue:** `$subscriptionId` and `$captureId` parameters were interpolated into PayPal API URLs without format validation in `getPayPalSubscriptionDetails()`, `cancelPayPalSubscription()`, `suspendPayPalSubscription()`, `activatePayPalSubscription()`, and `getPayPalCaptureDetails()`.
- **Fix:** Added `isValidPayPalResourceId()` validation function and applied it to all affected functions.

#### 3. SSRF via Square Card ID Injection
- **File:** `cron/subscription_renewal.php:405`
- **Issue:** `$cardId` from the database was interpolated into a Square API URL without validation. While the value originates from the database rather than direct user input, defense-in-depth requires validation.
- **Fix:** Added regex validation requiring alphanumeric/hyphen/underscore/colon characters.

### HIGH

#### 4. Email Header Injection
- **File:** `api/portal/portal-helper.php` (send_invoice_notification, send_payment_confirmation)
- **Issue:** `$customerName` from user-controlled invoice data was used directly in the `To:` email header. CRLF characters in the name could inject additional headers (e.g., BCC to attacker's address).
- **Fix:** Added CRLF and control character stripping on `$customerName`, `$customerEmail`, and `$companyName` before use in headers.

#### 5. Missing Rate Limiting on Payment Endpoints
- **Files:** `api/portal/checkout.php`, `api/portal/process-payment.php`
- **Issue:** No rate limiting on payment creation endpoints. An attacker could create unlimited Stripe PaymentIntents (each costing API calls), abuse PayPal order creation, or probe for valid invoice tokens at high speed.
- **Fix:** Added `enforce_payment_rate_limit()` function (20 attempts per IP per 15 minutes) and applied to both endpoints.

#### 6. File-Based Rate Limiting Race Condition
- **File:** `api/portal/portal-helper.php`
- **Issue:** The `is_rate_limited()` and `record_failed_lookup()` functions had a TOCTOU (time-of-check-time-of-use) race condition. The file was read without a lock, so under concurrent requests rate limits could be bypassed.
- **Fix:** Rewrote using `flock(LOCK_EX)` for atomic read-modify-write operations via `read_rate_limits_locked()` and `write_rate_limits_unlock()` helpers. Also upgraded from `md5($ip)` to `hash('sha256', $ip)` for rate limit keys.

### MEDIUM

#### 7. Database Error Details Leaked to Clients
- **Files:** `api/portal/register.php`, `api/portal/invoices.php`, `api/portal/connect.php`
- **Issue:** Raw database error messages (`$stmt->error`, `$e->getMessage()`) were returned in API responses, potentially revealing table names, column names, or SQL structure.
- **Fix:** Changed all instances to log the detailed error server-side with `error_log()` and return a generic message to the client.

#### 8. Database Connection Error Leak
- **File:** `db_connect.php:86`
- **Issue:** `die("Database connection failed: " . $db->connect_error)` could expose database host, port, or error details to end users.
- **Fix:** Changed to `die("Database connection failed. Please try again later.")` (detailed error is already logged on the line above).

#### 9. PayPal Portal Webhook Accepted Without Verification
- **File:** `api/portal/webhooks/paypal.php:31-35`
- **Issue:** When `PORTAL_PAYPAL_WEBHOOK_ID` was not configured, the webhook handler returned HTTP 200 and logged a warning, but an attacker could send forged webhook events that would be silently acknowledged.
- **Fix:** Changed to return HTTP 500 and reject the request when webhook ID is not configured, consistent with the Stripe webhook handler's behavior.

#### 10. Weak Idempotency Keys
- **Files:** `pricing/premium/checkout/process-subscription.php`, `cron/subscription_renewal.php`
- **Issue:** Square API idempotency keys were generated with `uniqid('prefix_', true)` which is based on `microtime()` and is predictable. An attacker who can predict the key could replay or interfere with payment requests.
- **Fix:** Replaced all `uniqid()` calls with `bin2hex(random_bytes(16))` for cryptographically secure idempotency keys.

#### 11. Missing SSL Verification in Subscription Renewal
- **File:** `cron/subscription_renewal.php:406-413, 448-458`
- **Issue:** Square API calls in the renewal cron job did not explicitly set `CURLOPT_SSL_VERIFYPEER` and `CURLOPT_SSL_VERIFYHOST`. While PHP/curl defaults to verifying SSL, explicit setting is best practice and protects against misconfigured environments.
- **Fix:** Added `CURLOPT_SSL_VERIFYPEER => true` and `CURLOPT_SSL_VERIFYHOST => 2` to both curl calls.

#### 12. Missing `payment_environment` in Square Portal Payments
- **File:** `api/portal/checkout.php:273-284`
- **Issue:** The `record_portal_payment()` call for Square payments did not include `payment_environment`, unlike Stripe and PayPal handlers. This could cause confusion when distinguishing sandbox vs production payments.
- **Fix:** Added `'payment_environment' => $is_production ? 'production' : 'sandbox'` to the Square payment recording.

### LOW (Additional Hardening)

#### 13. Sensitive File Protection
- **File:** `.htaccess`
- **Issue:** `.env`, `composer.json`, and `composer.lock` files were not explicitly protected from direct web access. While server configuration typically prevents this, defense-in-depth is prudent.
- **Fix:** Added explicit `<Files>` deny rules for `.env`, `composer.json`, `composer.lock`, and broadened SQL file protection from `schema.sql` to all `*.sql` files.

#### 14. Stripe Payment Intent ID Validation
- **File:** `api/portal/process-payment.php`
- **Issue:** The `payment_intent_id` from client input was passed to Stripe's API without format validation. While Stripe would reject invalid IDs, validating the format prevents unnecessary API calls and provides defense-in-depth.
- **Fix:** Added regex validation `preg_match('/^pi_[A-Za-z0-9]+$/', $paymentIntentId)`.

---

## Existing Security Strengths

The following security measures were already well-implemented:

1. **Encryption:** AES-256-GCM with proper IV/tag handling for Square access tokens
2. **Webhook Verification:** All three providers (Stripe, PayPal, Square) have signature verification
3. **SQL Injection Prevention:** Consistent use of prepared statements (both mysqli and PDO)
4. **Token Security:** 192-bit and 256-bit cryptographic tokens using `random_bytes()`
5. **OAuth CSRF Protection:** State tokens with 10-minute expiry stored in database
6. **SSL/TLS:** Explicit `CURLOPT_SSL_VERIFYPEER`/`CURLOPT_SSL_VERIFYHOST` on most API calls
7. **Payment Verification:** Server-side amount validation against provider-confirmed amounts
8. **Idempotency:** Duplicate payment detection via `provider_payment_id` lookup
9. **Security Headers:** HSTS, X-Frame-Options, X-Content-Type-Options, CSP, Referrer-Policy
10. **Server-Side Price Calculation:** Subscription amounts computed server-side, never trusted from client
11. **Error Display:** `display_errors Off` in production via .htaccess
12. **CORS:** Single-origin CORS headers with configurable allowed origin

---

## Recommendations (Not Fixed - Require Architecture Decisions)

### 1. Database-Backed Rate Limiting
The current file-based rate limiting (even with the locking fix) is not ideal for multi-server deployments. Consider moving rate limits to the database or Redis for scalability.

### 2. IP Spoofing via X-Forwarded-For
`get_client_ip()` trusts the first IP in `X-Forwarded-For`. If the server is not behind a trusted reverse proxy, attackers can spoof their IP to bypass rate limiting. Consider configuring trusted proxy IPs and only accepting `X-Forwarded-For` from them.

### 3. Payment Webhook Logging
The `logPayPalWebhookEvent()` function writes full webhook payloads (including `json_encode($data)`) to log files. While these don't contain card numbers (PCI-compliant providers strip them), the logs could contain PII. Consider retention policies and ensure log directory permissions are restrictive (already `0700`).

### 4. Debug Logging in Production
`payments-sync.php` logs every request with `error_log()` at debug level. This should be gated behind an environment check or removed to reduce log noise in production.
