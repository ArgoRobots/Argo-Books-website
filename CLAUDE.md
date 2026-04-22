# Argo Books Website — Claude Code Guide

## Project Overview

PHP/MySQL website for [Argo Books](https://argorobots.com/), accounting software. The site handles marketing, software downloads, license key sales, customer portal, community forum, admin dashboard, and API endpoints for the desktop app.

## Local Development Setup

**Requirements:** Laragon (Apache + MySQL), Composer, PHP 8.3+

```bash
# Install dependencies
composer install

# Database: create 'argo_books' in MySQL, then import schema
# Import mysql_schema.sql via HeidiSQL or MySQL CLI

# Copy and configure environment
cp ".env - sandbox" .env
# Edit .env with local credentials

# Run locally
# http://localhost/argo-books-website
```

**No build step** — PHP is interpreted directly. Refresh browser to see changes.

## Environment

- `.env` — active environment file (gitignored)
- `.env - production` — production credentials template
- `.env - sandbox` — sandbox/testing credentials template
- `APP_ENV="sandbox"` for local development, `"production"` for production

## Key Files & Entry Points

| Path | Purpose |
|------|---------|
| `index.php` | Homepage / main entry point |
| `db_connect.php` | Database connection, AES-256-GCM encryption helpers |
| `email_sender.php` | All email sending logic (SMTP via Resend) |
| `license_functions.php` | License key generation and validation |
| `statistics.php` | Page view tracking (include in each page) |
| `mysql_schema.sql` | Full database schema |
| `config/pricing.php` | Centralized pricing config (reads from .env) |
| `config/plans.json` | Free vs Premium feature definitions |
| `style.css` | Global stylesheet |

## Directory Structure

```
/admin          Admin dashboard (2FA-protected)
/api            RESTful endpoints for the desktop app
/community      Bug/feature request forum
/config         Shared config (pricing, plans)
/cron           Background jobs (subscriptions, email outreach, purges)
/documentation  User-facing docs with search
/downloads      Software version management
/portal         Customer self-service (invoices, subscriptions)
/resources      Shared assets (images, JS, CSS, uploads)
/webhooks       Payment webhook handlers (Stripe, PayPal, Square)
/whats-new      Release notes and version history
/read-me        Developer guides (cron setup, payment testing, email setup)
```

## Database

- MySQL with InnoDB, `utf8mb4`
- Database name: `argo_books`
- Connection via PDO in `db_connect.php` (global `$pdo`; `PDO::ATTR_ERRMODE` is `ERRMODE_EXCEPTION`, default fetch mode is `FETCH_ASSOC`)
- Schema file: `mysql_schema.sql` — update this when adding/modifying tables

Key table groups:
- **Licensing:** `license_keys`, `premium_subscriptions`, `premium_subscription_payments`
- **Community:** `community_users`, `community_posts`, `community_comments`, `community_votes`
- **Portal:** `portal_companies`, `portal_invoices`, `portal_payments`
- **Analytics:** `statistics`, `receipt_scan_usage`, `invoice_send_usage`
- **Email:** `outreach_leads`, `outreach_activity_log`

## Payment Gateways

Three gateways are supported — Stripe, PayPal, Square. Each has:
- A checkout/payment page
- A webhook handler in `/webhooks/`
- Sandbox credentials in `.env - sandbox` (see `/read-me/` for testing guides)

Processing fees are configurable via `.env` (`PROCESSING_FEE_PERCENT`, `PROCESSING_FEE_FIXED`).

## Email Sending

**All transactional email must go through Resend via the SMTP relay.** The entry points are `email_sender.php` (general-purpose, `send_styled_email()`) and `smtp_mailer.php` (`create_smtp_mailer()` — returns a configured PHPMailer instance, or `null` if SMTP is not configured so the caller can fall back to PHP `mail()`).

Rules for any code that sends email:

- **Never call `mail()` directly without first attempting `create_smtp_mailer()`.** Raw `mail()` bypasses Resend, loses deliverability, and silently no-ops on servers without an MTA.
- Prefer reusing existing helpers in `email_sender.php` (e.g., `send_premium_subscription_receipt`, `send_payment_failed_email`, `resend_subscription_id_email`) rather than duplicating HTML templates elsewhere.
- The accepted pattern for new callers outside `email_sender.php`: try SMTP first, fall back to `mail()` only when `create_smtp_mailer()` returns `null`. See `api/invoice/invoice_email_sender.php` and `api/portal/portal-helper.php` for reference implementations.
- SMTP config lives in `.env` under `SMTP_*` (see `smtp_mailer.php` docblock). In production and sandbox, `SMTP_HOST=smtp.resend.com`, `SMTP_USERNAME=resend`, `SMTP_PASSWORD` is the Resend API key.
- For local development, set up MailHog (see `/read-me/Local email setup.md`) so mail() fallback works without hitting real inboxes.

## Database Access

All DB queries go through the global `$pdo` (PDO with `ATTR_ERRMODE => ERRMODE_EXCEPTION`, default fetch mode `FETCH_ASSOC`, `ATTR_EMULATE_PREPARES => false`). At script top-level, `$pdo` is already in scope once `db_connect.php` is required. Inside functions, declare `global $pdo;` before use.

Conventions:

- Always use prepared statements for any query touching user input. Pass params as an array to `execute([...])`; don't concatenate into SQL.
- `$stmt->fetch()` returns `false` when there's no row — check that explicitly instead of treating it as an existence test.
- PDO throws `PDOException` on error. Wrap in try/catch only at boundaries where you want a specific user-facing error response; otherwise let it bubble to the global handler.
- For INSERT/UPDATE/DELETE, `$stmt->rowCount()` gives affected rows and `$pdo->lastInsertId()` gives the new id.
- Transactions: `$pdo->beginTransaction()` / `$pdo->commit()` / `$pdo->rollBack()` (note the capital B).

The old `mysqli` interface (`get_db_connection()`) was removed in the PDO migration — don't reintroduce it.

## Layout (Header / Footer)

Pages include empty containers — `<div id="includeHeader"></div>` and `<div id="includeFooter"></div>` — which `resources/scripts/main.js` fills via jQuery `.load()` from `resources/header/index.html` and `resources/footer/index.html`.

Consequences:
- There is **no PHP header file**. `<?php include 'header.php'; ?>` will produce nothing.
- Any `<script>` a page depends on must load before `main.js`.
- New site-wide URL prefixes (e.g., `/unsubscribe/`) must be registered in `main.js`'s `fixLinks` logic (see commit `77eb2a1`).

## License Key Terminology

The user-facing "License Key" is stored in the `premium_subscriptions.subscription_id` column and generated by `generate_license_key('premium')` in `license_functions.php`. Labels shown to users must say "License Key"; code/columns/URL params/webhook payloads keep `subscription_id`.

`paypal_subscription_id` on the same table is a **different thing** — PayPal's own subscription identifier. Don't conflate.

`license_functions.php::generate_license_key()` is the single source of truth — never write a parallel key generator.

## Env + URL Helpers

Always read env vars via `env('KEY', 'default')` from `env_helper.php`, which wraps `$_ENV['KEY'] ?? getenv('KEY') ?: $default`. Both fallbacks are load-bearing because `$_ENV` can be empty depending on `variables_order` in `php.ini`.

For site URLs in emails, crons, or any non-request context, use `site_url($path)` from the same helper (reads `SITE_URL` env var, default `https://argorobots.com`). For in-request URLs where the scheme/host should come from the actual request, `community/community_functions.php::get_site_url()` is the right choice — it derives from `$_SERVER`, but **returns an empty or wrong result when called from cron or CLI**.

Shared flat-file rate limiting helpers (`is_rate_limited`, `record_rate_limit_attempt`, `get_client_ip`, …) live in `rate_limit_helper.php` at the repo root. Callers pass a bucket prefix (`'admin_login'`, `'portal'`, `'payment'`, etc.) so buckets don't collide. Rate-limit state lives in `/resources/rate_limits/rate_limits.json`.

## Cron Jobs

Located in `/cron/`. Must be scheduled on the server (see `/read-me/Cron jobs.md`):

| File | Frequency | Purpose |
|------|-----------|---------|
| `subscription_renewal.php` | Daily | Process recurring subscription renewals |
| `outreach_pipeline.php` | Hourly | Send bulk email campaigns |
| `account_purge.php` | Daily | Delete accounts scheduled for deletion |

Cron requests are authenticated via `CRON_SECRET` in `.env`.

## Git Workflow

- There is an active version branch (e.g., `V.2.0.5`) for the next release. **Branch off this version branch, not `main`.**
- Target PRs to the current version branch, not `main`.
- `main` only receives merges when a version is released to production.

## Production Deployment

GitHub Actions (`.github/workflows/deploy.yml`) deploys on push to `main`:
1. Runs `composer install --no-dev --optimize-autoloader`
2. Diffs changed files vs last deploy
3. Uploads via SFTP to production/dev servers

Files excluded from deployment: `.git`, `.github`, `README.md`, `composer.json`, `mysql_schema.sql`

**Never commit `.env` files** — they are gitignored. Configure the server's `.env` directly.

## What's New Page

- Write for non-technical users — plain language, no jargon
- Only include changes users will notice (new features, UI changes, important fixes)
- Skip internal refactors, code cleanup, dependency updates, and developer-facing changes
- Use short, benefit-oriented descriptions (e.g., "Faster invoice loading" not "Optimized SQL query for invoice retrieval")

## Security Notes

- Admin requires TOTP 2FA — secret stored in `admin_users` table
- Sensitive portal data encrypted with AES-256-GCM (`db_connect.php`)
- `.htaccess` blocks direct access to `.env`, `.sql`, log files
- Always sanitize user input; use PDO prepared statements (`$pdo->prepare(...)->execute([...])`) for every SQL query touching user input — never concatenate into SQL
- Rate limiting via flat files in `/resources/rate_limits/` (gitignored)

## Third-Party Services

| Service | Purpose | Config Key Prefix |
|---------|---------|-------------------|
| Stripe | Payments | `STRIPE_` |
| PayPal | Payments | `PAYPAL_` |
| Square | Payments | `SQUARE_` |
| Resend | Email relay (SMTP) | `SMTP_` |
| Google OAuth | Social login | `GOOGLE_` |
| OpenAI | AI features | `OPENAI_` |
| Azure Document Intelligence | Receipt scanning | `AZURE_` |
| Open Exchange Rates | Currency conversion | `EXCHANGE_RATES_` |
