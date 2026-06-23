# Argo Books Website: Claude Code Guide

PHP/MySQL website for [Argo Books](https://argorobots.com/) accounting software. Handles marketing, software downloads, license sales, customer portal, community forum, admin dashboard, and API endpoints for the desktop app.

**Stack:** PHP 8.3+, MySQL (InnoDB, `utf8mb4`), Laragon locally, Composer. No build step, just refresh the browser.

**Companion repos:** The Avalonia/C# desktop app lives at `C:\Users\evand\Desktop\Argo-Books-Avalonia`. Many endpoints in `/api/` are called from there.

## Environment files

- `.env`: active environment file (gitignored)
- `.env - production` and `.env - sandbox`: checked-in templates
- `APP_ENV` (`sandbox` or `production`) drives all environment-conditional code (see `current_environment()` in `db_connect.php`)

## Key files

| Path | Why it matters |
|---|---|
| `db_connect.php` | Global `$pdo` + AES-256-GCM helpers + `current_environment()` |
| `email_sender.php` / `smtp_mailer.php` | All transactional email goes through here |
| `license_functions.php` | License key generation, validation, redemption |
| `statistics.php` | Page-view tracking + bot detection (`is_likely_bot()`) |
| `track_referral.php` / `track_referral_event.php` | Referral attribution + full-funnel event log |
| `mysql_schema.sql` | Source of truth for the schema. Update when adding tables |
| `config/pricing.php` | Pricing config (reads `.env`) |
| `config/plans.json` | Free vs Premium feature definitions |

## Database access

All queries go through the global `$pdo`. PDO is configured with `ATTR_ERRMODE => ERRMODE_EXCEPTION`, default fetch mode `FETCH_ASSOC`, `ATTR_EMULATE_PREPARES => false`. Inside functions, declare `global $pdo;` before use.

- Use prepared statements for anything touching user input. Pass params as an array to `execute([...])`; never concatenate into SQL.
- `$stmt->fetch()` returns `false` when there's no row. Check explicitly.
- Let `PDOException` bubble unless you have a specific user-facing error to return.

**Never create migration files.** Schema changes go in `mysql_schema.sql` plus a chat-message SQL block (CREATE/ALTER statements) for the user to run manually in HeidiSQL. Do not create a `migrations/` folder.

## Email sending

**All transactional email must go through Resend via the SMTP relay.** Never call `mail()` directly without first attempting `create_smtp_mailer()`. Raw `mail()` bypasses Resend, loses deliverability, and silently no-ops on servers without an MTA.

Pattern for new callers outside `email_sender.php`: try SMTP first, fall back to `mail()` only when `create_smtp_mailer()` returns `null`. Reference implementations: `api/invoice/invoice_email_sender.php`, `api/portal/portal-helper.php`.

For local dev, set up MailHog so the fallback path doesn't try to hit a real MTA. See `read-me/setup/Local email setup.md`.

## Payment gateways

**Portal Connect flow** (merchants accepting invoice payments through their own provider account): **Stripe** and **Square** only.

**PayPal portal Connect is disabled.** The "Log in with PayPal" OAuth endpoint refuses to return identity for Business-account tokens, and proper merchant onboarding requires the Partner Referrals API (gated behind Platforms & Marketplaces partner enrollment). All portal-side PayPal handlers (`api/portal/connect.php`, `connect-callback.php`, `checkout.php` `handle_paypal_checkout()`, `process-payment.php` `process_paypal_payment()`) return 503 `PROVIDER_UNSUPPORTED`, and `get_available_payment_methods()` in `api/portal/portal-helper.php` deliberately omits PayPal even when `paypal_merchant_id` is set. The desktop app hides the PayPal Connect button.

**PayPal IS still used for the SaaS subscription flow** (Argo Premium billing on argorobots.com): separate, working integration with its own webhook handler (`webhooks/paypal-subscription.php`), plan IDs, and checkout. Do not touch SaaS-subscription PayPal code when working on portal features.

## Header / footer loading

Pages include empty `<div id="includeHeader"></div>` and `<div id="includeFooter"></div>` containers that `resources/scripts/main.js` fills via jQuery `.load()` from `resources/header/index.html` and `resources/footer/index.html`.

Consequences:
- Any `<script>` a page depends on must load before `main.js`.
- New site-wide URL prefixes (e.g. `/unsubscribe/`) must be registered in `main.js`'s `fixLinks` logic.

## CSS / theming

All colors come from CSS variables in `resources/styles/custom-colors.css`. Don't hardcode hex values. Dark mode uses `[data-theme="dark"]` selectors. Shared admin styles in `admin/common-style.css` already cover `.stat-card`, `.table-container`, `.chart-container`, `.section-tabs`, `.modal`, etc. for both themes; only add `[data-theme="dark"]` overrides for custom components those defaults don't reach.

## Preserving scroll on filter reload

Admin pages with filter pills that reload the page (period selectors, source pills, range buttons) use a shared `sessionStorage.scrollPosition` pattern so the reload doesn't jump back to the top. When adding a new filter, extend the existing handler's selector on that page rather than writing a parallel script. URL anchors (`#section-id`) do NOT solve this; they only change where the jump lands. Reference implementations: `admin/referral-links/index.php`, `admin/website-stats/index.php`, `admin/users/index.php`, `admin/crons/index.php`.

## Tests

PHPUnit suite lives in `/tests/`. Run with `./vendor/bin/phpunit` from the project root. Requires a separate `argo_books_test` database (creds in `.env.testing`). The deploy workflow does not run tests; they're a local / pre-commit guardrail only.

## Git workflow

Commit directly to `main`. Don't branch-first or park changes on a feature branch unless asked.

## "What's New" page

For end users, plain language, no jargon. Include only user-visible changes; skip refactors, dependency updates, and other developer-facing work. Frame as benefits ("Faster invoice loading", not "Optimized SQL query").

**Never make bugs sound scary.** Accounting software has to feel rock-solid. Avoid words like *crash*, *bug*, *broken*, *error*, *lost*, *corrupted*, *vulnerability*. Either skip the entry or rephrase as a positive:

- "Fixed a bug where invoices could be lost" → skip, or "More reliable invoice saving"
- "Patched a security vulnerability" → skip entirely

## Documentation style

When writing markdown docs (in `read-me/` or anywhere else in the repo), don't use em dashes (the `—` character). Use a comma, a colon, or a period instead, to match the project's house style. Don't substitute a regular hyphen either: that reads as a compound-word marker, not a clause break.

## Security

- Admin requires TOTP 2FA. Secret stored in `admin_users.two_factor_secret`
- Sensitive portal data is AES-256-GCM encrypted via `portal_encrypt()` / `portal_decrypt()` in `db_connect.php`
- `.htaccess` blocks direct access to `.env`, `.sql`, log files

## Detailed docs

The `read-me/` directory has authoritative reference docs that are kept current. Don't duplicate their content here:

- `read-me/Cron jobs.md`: every scheduled cron and its frequency
- `read-me/Deployment.md`: how `.github/workflows/deploy.yml` ships code to the server
- `read-me/Email outreach.md`: outreach pipeline behavior
- `read-me/Admin guide.md`: admin dashboard tour
- `read-me/setup/Payment provider setup.md`: Stripe / PayPal / Square provider config
- `read-me/testing/Payment provider testing.md`: sandbox testing procedures
- `read-me/testing/First-run install tracking.md`: how the install funnel attributes back to the originating ad click, why your own machine deduplicates itself, and the end-to-end test procedure
- `read-me/setup/Local email setup.md`: MailHog setup
- `read-me/setup/Google Ads campaign setup.md`: Google Ads campaign + UET/gtag wiring
- `read-me/setup/Advanced Installer project setup.md` - recovery guide for the Windows installer project (tooling install, project recreation, signing, the WriteInstallToken custom action for funnel attribution)
- `read-me/procedures/Refund block response procedure.md`: what to do when the refund safety check fires
