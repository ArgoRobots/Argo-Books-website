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
- Connection via PDO in `db_connect.php`
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
- Always sanitize user input; use PDO prepared statements (already the convention)
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
