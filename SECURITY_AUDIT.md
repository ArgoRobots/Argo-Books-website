# Security Audit Report

**Date:** 2026-03-14
**Scope:** Complete codebase security audit of the Argo Books website

---

## Executive Summary

A comprehensive security audit was performed covering SQL injection, XSS, CSRF, authentication/session management, API security, access controls, and server configuration. The codebase demonstrates strong security fundamentals (prepared statements, password hashing, HSTS, CSP headers), but several critical and high-severity vulnerabilities were identified and fixed.

**Total findings: 41** | Fixed: 25 | Remaining (low risk/informational): 16

---

## CRITICAL Findings (Fixed)

### 1. Password Leaked in JavaScript Source Code
- **File:** `community/users/register.php:238-239`
- **Issue:** Plaintext passwords echoed into JavaScript via `addslashes()` on validation errors, enabling XSS via `</script>` breakout
- **Fix:** Removed password restoration; users re-enter passwords on validation failure

### 2. TOTP Secret Generated with Insecure `rand()`
- **File:** `admin/settings/totp.php:129`
- **Issue:** `rand()` is a non-cryptographic PRNG; TOTP secrets were predictable
- **Fix:** Replaced with `random_int()`

### 3. Reflected XSS via `$billing` in JavaScript Context
- **File:** `pricing/premium/checkout/index.php:116`
- **Issue:** `$_GET['billing']` echoed unencoded into JavaScript string literal
- **Fix:** Used `json_encode()` for all JavaScript-embedded values

### 4. Missing CSRF on Admin Post Status Update
- **File:** `community/update_status.php`
- **Fix:** Added CSRF token verification

### 5. Missing CSRF on Content Reporting
- **File:** `community/report/report_content.php`
- **Fix:** Added CSRF token verification

---

## HIGH Findings (Fixed)

### 6. 2FA Secret Stored in Plain Text
- **File:** `admin/settings/2fa.php:37`
- **Fix:** Secrets now encrypted with `portal_encrypt()` before storage, decrypted on retrieval

### 7. Remember-Me Token Stored in Plain Text
- **File:** `community/users/user_functions.php:246`
- **Fix:** Now stores `hash('sha256', $token)` in DB; validates by hashing cookie value

### 8. Missing CSRF on Delete Account
- **File:** `community/users/delete_account.php`
- **Fix:** Added CSRF token verification

### 9. Session Fixation on Email Verification Login
- **File:** `community/users/verify_code.php:106`
- **Fix:** Added `session_regenerate_id(true)` before setting session variables

### 10. Session Fixation on Remember-Me Auto-Login
- **File:** `community/users/user_functions.php:214`
- **Fix:** Added `session_regenerate_id(true)` before setting session variables

### 11. `SELECT *` Exposes Sensitive Data in Profile
- **File:** `community/users/profile.php:36`
- **Fix:** Replaced with explicit column list excluding `password_hash`, `reset_token`, etc.

### 12. IP Spoofing via X-Forwarded-For
- **File:** `api/portal/portal-helper.php:581`
- **Fix:** Now uses `REMOTE_ADDR` only; removed trust of spoofable `X-Forwarded-For` header

### 13. Unauthenticated Preview Handler
- **File:** `community/preview_handler.php`
- **Fix:** Added session authentication and CSRF verification requirements

### 14. XSS via Payment Config in JavaScript Context
- **Files:** `pricing/premium/checkout/index.php`, `portal/invoice.php`
- **Fix:** All values embedded in JavaScript now use `json_encode()` instead of string interpolation

### 15. Wildcard CORS on Email Endpoint
- **File:** `api/invoice/send-email.php:17`
- **Fix:** Replaced `*` with configurable allowed origin from environment variable

### 16. User-Controlled Attachment Filename
- **File:** `api/invoice/invoice_email_sender.php:129,162`
- **Fix:** Filename sanitized with strict allowlist pattern `[a-zA-Z0-9._-]`

---

## MEDIUM Findings (Fixed)

### 17. Account Enumeration via Forgot Password
- **File:** `community/users/forgot_password.php:29-33`
- **Fix:** Now returns identical message regardless of whether email exists

### 18. Weak Password Validation on Reset
- **File:** `community/users/reset_password.php:33`
- **Fix:** Added uppercase, number, and special character requirements matching registration

### 19. Non-Parameterized SQL in Mentions Search
- **File:** `community/mentions/search.php:128-134`
- **Fix:** Replaced interpolated IDs with proper parameterized placeholders

### 20. Error Details Exposed in API Response
- **File:** `community/mentions/search.php:193`
- **Fix:** Replaced exception details with generic error message

### 21. PHP Version Disclosure in Email Headers
- **File:** `cron/subscription_renewal.php:602`
- **Fix:** Replaced `PHP/phpversion()` with generic `ArgoBooks` mailer string

### 22. TOTP Window Too Large (9 valid codes)
- **File:** `admin/settings/totp.php:67`
- **Fix:** Reduced window from 4 to 1 (3 valid codes instead of 9)

### 23. Password Reset Token 24-Hour Expiry
- **File:** `community/users/user_functions.php:367`
- **Fix:** Reduced to 1 hour per OWASP recommendations

### 24. XSS via Thank-You Page Missing ENT_QUOTES
- **File:** `pricing/premium/thank-you/index.php:33-34`
- **Fix:** Added `ENT_QUOTES, 'UTF-8'` to `htmlspecialchars()` calls

---

## Remaining Low-Risk / Informational Items (Not Fixed - Require Manual Review)

### Session Management
25. **No global `session_set_cookie_params()`** - Session cookie security flags depend on php.ini defaults. Recommend adding `Secure`, `HttpOnly`, `SameSite=Lax` in a bootstrap file.
26. **Session-based login rate limiting** - Bypassable by clearing session cookie. Recommend server-side rate limiting keyed by IP/username.
27. **No rate limiting on 2FA verification** - TOTP codes could be brute-forced. Recommend adding attempt limits.
28. **No rate limiting on forgot password** - Could be used to flood inboxes. Recommend per-email/IP rate limiting.
29. **Account enumeration via registration** - "Username/email already exists" messages reveal valid accounts.
30. **Verification code has no expiry** - 6-digit code remains valid indefinitely until used.

### API / Webhooks
31. **No rate limiting on license redeem/validate** - License keys could be brute-forced.
32. **Wildcard CORS on AI import and receipt endpoints** - Recommend restricting to specific domains.
33. **No rate limiting on AI import and receipt endpoints**
34. **Webhook signature verification skipped in dev** - Acceptable if dev is not internet-accessible.
35. **Rate limit data file in web directory** - Already protected by .htaccess rules.
36. **No rate limiting on invoice email endpoint**
37. **SVG upload allowed** - Has pattern checks but SVG XSS bypasses are evolving.

### Other
38. **Admin header doesn't verify auth** - Individual pages check; recommend defense-in-depth.
39. **MIME type disclosed in upload error** - Minor information leakage.
40. **No rate limiting on avatar info endpoint**
41. **`htmlspecialchars()` without `ENT_QUOTES` used systemically** - Not exploitable in current contexts but fragile.

---

## Positive Security Practices Found

- **Password hashing:** Consistent use of `password_hash(PASSWORD_DEFAULT)` with `password_verify()`
- **Prepared statements:** All user-facing SQL queries use parameterized queries
- **Security headers:** HSTS, X-Frame-Options, X-Content-Type-Options, CSP, Referrer-Policy
- **CSRF protection:** Properly implemented on most forms with `hash_equals()`
- **Sensitive file protection:** `.htaccess` blocks access to `.env`, `.sql`, config files
- **Directory listing disabled:** `Options -Indexes`
- **Error display disabled:** `php_flag display_errors Off`
- **Encryption:** AES-256-GCM with proper IV handling for portal data
- **Environment variables:** Credentials stored in `.env`, not in code
- **Webhook verification:** Stripe, PayPal, and Square webhooks verify signatures
- **Open redirect prevention:** Post-login redirects validate paths are relative/local
- **File upload validation:** Server-side MIME checking with `finfo`, path traversal prevention
