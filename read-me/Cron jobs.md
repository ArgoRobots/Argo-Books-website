# Cron Jobs

All cron scripts live in `/cron/` and must be run via CLI only. Each script writes daily logs to `/cron/logs/`.

---

## 1. Subscription Renewal

**Script:** `cron/subscription_renewal.php`
**Schedule:** Daily at 3:00 PM

```bash
0 15 * * * /usr/bin/php /home/argorobots/public_html/cron/subscription_renewal.php
```

### What It Does

1. Finds active Premium subscriptions due for renewal within 24 hours
2. Processes credit-based renewals first (no charge if credit covers it)
3. Charges payment methods (Stripe/Square) for remaining balance
4. Sends email receipts for successful renewals
5. Sends failure notifications for failed payments
6. Suspends subscriptions after 3 consecutive failures
7. Marks non-auto-renew subscriptions as expired

### Manual Execution

```bash
php /home/argorobots/public_html/cron/subscription_renewal.php
```

### Logs

`/cron/logs/subscription_renewal_YYYY-MM-DD.log`

---

## 2. Account Purge

**Script:** `cron/account_purge.php`
**Schedule:** Daily at 4:00 AM

```bash
0 4 * * * /usr/bin/php /home/argorobots/public_html/cron/account_purge.php
```

### What It Does

1. Finds user accounts whose 30-day deletion grace period has expired (users schedule deletion from their profile; logging back in cancels the request)
2. Cancels any active Premium subscriptions for the account
3. Deletes the user record (foreign key cascades handle related community data)
4. Logs each deletion or failure

### Manual Execution

```bash
php /home/argorobots/public_html/cron/account_purge.php
```

### Logs

`/cron/logs/account_purge_YYYY-MM-DD.log`

---

## 3. Outreach Pipeline

**Script:** `cron/outreach_pipeline.php`
**Schedule:** Daily at 8:00 AM

```bash
0 8 * * * /usr/bin/php /home/argorobots/public_html/cron/outreach_pipeline.php
```

### What It Does

1. Picks the next target city from the expansion list
2. Discovers businesses via Google Places API
3. Imports them as leads (skips duplicates)
4. Generates AI email drafts for leads that don't have one
5. Auto-approves drafts (configurable)
6. Sends approved emails up to the daily limit

### Environment Variables

| Variable | Default | Description |
|---|---|---|
| `GOOGLE_PLACES_API_KEY` | (required) | Required for business discovery. Must have **Places API (New)** enabled in Google Cloud Console (uses `places.googleapis.com/v1/places:searchText`, not the legacy `maps.googleapis.com/maps/api/place/...` endpoints). |
| `GEMINI_API_KEY` | (required) | Required for AI draft generation |
| `OUTREACH_DAILY_SEND_LIMIT` | 10 | Max first-touch emails sent per day (also controls discovery and draft batch sizes) |
| `OUTREACH_DAILY_FOLLOWUP_LIMIT` | 30 | Max follow-up emails sent per day (separate cap, oldest-due first) |

The Auto-send vs Review-before-send mode is controlled at runtime via the **Settings tab** in the admin (writes to `outreach_pipeline_state.auto_send_mode`); no env var is needed.

### CLI Flags

```bash
php outreach_pipeline.php                  # Run full pipeline
php outreach_pipeline.php --discover-only  # Only discover + import businesses (Google Places + Shopify)
php outreach_pipeline.php --shopify-only   # Only run Shopify discovery
php outreach_pipeline.php --draft-only     # Only generate AI drafts
php outreach_pipeline.php --send-only      # Only send approved emails
php outreach_pipeline.php --dry-run        # Log what would happen without doing it
```

### Logs

`/cron/logs/outreach_pipeline_YYYY-MM-DD.log`

A lock file (`/cron/logs/outreach_pipeline.lock`) prevents overlapping runs.

---

## 4. Outreach Reply Checker

**Script:** `cron/reply_checker.php`
**Schedule:** Hourly

```bash
0 * * * * /usr/bin/php /home/argorobots/public_html/cron/reply_checker.php
```

### What It Does

1. Connects to the contact mailbox via IMAP
2. Fetches emails received in the last 7 days
3. Skips auto-responders (out-of-office, auto-reply headers)
4. Matches sender addresses against outreach leads in `contacted` status
5. Auto-promotes matched leads to `replied` status (only if the email arrived after `sent_at`)
6. Logs a `reply_received` activity entry for each match

Admins can then manually classify replied leads as `interested` or `not_interested` in the outreach dashboard.

### Requirements

- PHP `imap` extension must be enabled. In cPanel: **Select PHP Version → Extensions → check `imap`**
- Mailbox credentials configured in `.env` (see below)

### Environment Variables

| Variable | Default | Description |
|---|---|---|
| `IMAP_HOST` | (required) | e.g. `mail.argorobots.com` |
| `IMAP_PORT` | 993 | IMAP SSL port |
| `IMAP_USERNAME` | (required) | Full email address (e.g. `contact@argorobots.com`) |
| `IMAP_PASSWORD` | (required) | Mailbox password or app password |
| `IMAP_MAILBOX` | INBOX | Folder to check |

### Manual Execution

```bash
php /home/argorobots/public_html/cron/reply_checker.php
```

### Logs

`/cron/logs/reply_checker_YYYY-MM-DD.log`

A lock file (`/cron/logs/reply_checker.lock`) prevents overlapping runs.

### Limitations

- Replies sent from a different address than the lead's email won't be detected
- Relies on the contact mailbox being the reply-to for outreach emails (it is, per `send_outreach_lead()`)

---

## 5. Refund Cooling-Off Promoter

**Script:** `cron/refund_cooling_off_promoter.php`
**Schedule:** Every 1 minute

```bash
* * * * * /usr/bin/php /home/argorobots/public_html/cron/refund_cooling_off_promoter.php
```

### What It Does

Promotes refund requests from `cooling_off` to `processing` once their cooling-off timer has elapsed. Without this cron, refunds flagged for soft review would sit in `cooling_off` forever.

---

## 6. Refund Stale Processing Reconcile

**Script:** `cron/refund_stale_processing_reconcile.php`
**Schedule:** Every 5 minutes

```bash
*/5 * * * * /usr/bin/php /home/argorobots/public_html/cron/refund_stale_processing_reconcile.php
```

### What It Does

Queries Stripe for refund requests stuck in `processing` for more than 30 minutes and updates their state based on the provider's response. Catches refunds where the provider succeeded but our callback never fired.

---

## 7. Refund Stale Request Cleanup

**Script:** `cron/refund_stale_request_cleanup.php`
**Schedule:** Hourly

```bash
0 * * * * /usr/bin/php /home/argorobots/public_html/cron/refund_stale_request_cleanup.php
```

### What It Does

1. Cancels refund requests stuck in `pending_code` for more than 1 hour (user never typed the verification code).
2. Vacuums old entries out of the refund idempotency cache.

---

## 8. Refund Velocity Baseline Recompute

**Script:** `cron/refund_velocity_baseline_recompute.php`
**Schedule:** Nightly at 2:00 AM

```bash
0 2 * * * /usr/bin/php /home/argorobots/public_html/cron/refund_velocity_baseline_recompute.php
```

### What It Does

Refreshes `refund_velocity_baselines` per company so the established-account hard-block tier (≥ 50% of trailing 30-day revenue) has accurate inputs. If baselines drift out of date, the refund safety check fires too often or not enough. See the [Hard-Block Response Procedure](procedures/Refund%20block%20response%20procedure.md) for symptoms.

---

## 9. Reddit Discovery

**Script:** `cron/reddit_monitor.php`
**Schedule:** Daily at 8:00 AM

```bash
0 8 * * * /usr/bin/php /home/argorobots/public_html/cron/reddit_monitor.php
```

### What It Does

1. Pulls fresh threads (last 24h) from each enabled watchlist subreddit
2. Runs global Reddit search for each enabled keyword
3. Deduplicates by thread ID and applies rules-based scoring
4. Runs a neutral AI relevance check (Gemini) on threads above the rules floor
5. Pre-generates a draft reply for threads scoring ≥ 8 on AI relevance
6. Marks borderline (6–7) threads as `drafted_pending` for on-demand drafting in the admin UI
7. Auto-expires drafted threads older than 3 days

Manual posting only. The cron does NOT post anything to Reddit. Drafts are reviewed and copy-pasted manually by the founder.

### Environment Variables

Default mode is **unauthenticated**: no app, no OAuth, no policy gate. Fill in the `REDDIT_*` vars later to upgrade to OAuth without code changes.

| Variable | Required | Description |
|---|---|---|
| `GEMINI_API_KEY` | yes | Reused from the email pipeline for AI relevance + draft generation |
| `REDDIT_CLIENT_ID` | optional | From a personal-use script app at https://www.reddit.com/prefs/apps/. Required only for OAuth mode. |
| `REDDIT_CLIENT_SECRET` | optional | App secret. Required only for OAuth mode. |
| `REDDIT_USERNAME` | optional | Reddit account that will post. If set without the OAuth pair, used purely as the User-Agent identifier and for the admin "your account stats" card. |
| `REDDIT_PASSWORD` | optional | Account password. Required only for OAuth mode. |

**Endpoint behaviour (unauth mode):** Reddit's JSON endpoints are aggressively IP-blocked from datacenter hosts (returns 403). To work around this, the helpers fetch discovery-shaped paths (`/r/X/new`, `/search`) via Reddit's RSS feed at the `.rss` suffix and parse the Atom XML back into the JSON listing shape; those endpoints are typically not IP-blocked. Endpoints with no RSS counterpart (`/api/info`, `/user/X/about`) still go to JSON and will silently 403 from blocked IPs, meaning the **reply-status check** and **account-info card** require OAuth to function. Discovery + drafting work either way.

When all four `REDDIT_*` vars are set, the helpers switch to OAuth (`oauth.reddit.com`) and all endpoints work normally. No code change required.

Per-channel tuning (scoring floors, post limits, auto-disable thresholds, subreddit / keyword lists) lives in the database and is editable in the admin UI under **Outreach → Reddit → Settings**.

### CLI Flags

```bash
php reddit_monitor.php             # Full pipeline
php reddit_monitor.php --dry-run   # Log what would happen; don't write threads/drafts
php reddit_monitor.php --verbose   # Stream progress to stdout
```

### Logs

A lock file (`/cron/logs/reddit_monitor.lock`) prevents overlapping runs.

### Manual Trigger

The admin Reddit → Threads tab has a **"Run discovery now"** button that triggers this cron in the background.

---

## 10. Reddit Status Check

**Script:** `cron/reddit_status_check.php`
**Schedule:** Every 2 hours

```bash
0 */2 * * * /usr/bin/php /home/argorobots/public_html/cron/reddit_status_check.php
```

### What It Does

For each thread marked `replied`, re-checks the posted Reddit comment via the Reddit API on a staggered schedule (30min / 2h / 6h / 24h / 72h after posting). Classifies the reply as `live`, `removed`, `removed_or_shadowbanned`, or `deleted_by_user`, captures upvote + reply engagement, and rolls up per-subreddit removal rates. Applies the auto-disable rule: any subreddit with ≥ N replies in the last 30 days and ≥ X% removal rate is automatically removed from the watchlist (N and X configurable in admin Settings).

Idempotent: each row tracks its own check count and last-checked timestamp; the cron never over-checks. After 5 checks, a reply's status is treated as final and dropped from the worklist.

### Logs

A lock file (`/cron/logs/reddit_status_check.lock`) prevents overlapping runs.

---

## 11. Marketing Broadcast Sender

**Script:** `cron/marketing_broadcast.php`
**Schedule:** Every 5 minutes

```bash
*/5 * * * * /usr/bin/php /home/argorobots/public_html/cron/marketing_broadcast.php
```

### What It Does

Drains queued broadcasts composed in the admin **Marketing** page (`marketing_broadcasts` / `marketing_broadcast_recipients`). Each run:

1. Picks broadcasts in `queued` or `sending` status, oldest first, and marks them `sending`.
2. Sends up to 100 emails per run total (the per-run cap keeps each run under Resend's rate limit and the time budget). A broadcast larger than the cap finishes over multiple runs.
3. Re-checks every recipient against the send-time gate (`should_send_marketing_email`), so anyone who unsubscribed between queue time and send time is marked `skipped`, not emailed.
4. Appends the one-click unsubscribe footer (subscriber token for the `newsletter` audience, community-user token for community contexts).
5. Marks the broadcast `sent` once no `pending` recipients remain.

Audience is one of: `newsletter` (no-account opt-in subscribers in `marketing_subscribers`) or a community context (`product_updates`, `tips_onboarding`, `promotions`) that maps to a `community_users.email_pref_*` column.

### Manual Execution

```bash
php /home/argorobots/public_html/cron/marketing_broadcast.php
```

### Logs

A lock file (`/cron/logs/marketing_broadcast.lock`) prevents overlapping runs.

---

## 12. IndexNow Submit

**Script:** `cron/indexnow_submit.php`
**Schedule:** Daily at 5:00 AM

```bash
0 5 * * * /usr/bin/php /home/argorobots/public_html/cron/indexnow_submit.php
```

### What It Does

Pings IndexNow with the pages whose source files changed since the last successful run, so freshly deployed or edited pages get recrawled quickly without manual submission. IndexNow notifies Bing, Yandex, DuckDuckGo, Seznam, and Naver in one call.

1. Builds the full URL list from `sitemap_build_urls()` in `sitemap_urls.php` (the same source the XML sitemap uses, so new pages are picked up automatically).
2. Selects URLs whose source file modification time is newer than the stored watermark (`/cron/logs/indexnow_last_submit`). On the server, file mtime reflects the last deploy that touched the file.
3. POSTs them to `https://api.indexnow.org/indexnow` via the helper in `indexnow.php`.
4. Advances the watermark only on full success, so a transient failure retries the same URLs next run.

The first run has no watermark, so it submits every URL once as a bootstrap. Run with `--baseline` first if you would rather start clean and only announce future changes.

Google does NOT participate in IndexNow, so this cron does nothing for Google. Keep using Search Console for Google.

### Setup (one time)

1. The ownership key file `77469e7877e34a30ab5fab27e275650e.txt` lives at the site root and deploys with the rest of the repo. Confirm it is reachable at `https://argorobots.com/77469e7877e34a30ab5fab27e275650e.txt` after the first deploy.
2. Add the crontab line above on the server.

The key can be rotated by generating a new one in Bing Webmaster Tools, replacing the root `.txt` file, and setting `INDEXNOW_KEY` in `.env`.

### CLI Flags

```bash
php indexnow_submit.php             # Submit URLs changed since last run
php indexnow_submit.php --all       # Force-submit every URL
php indexnow_submit.php --baseline  # Record the watermark without submitting
php indexnow_submit.php --dry-run   # Log what would be submitted; send nothing
```

### Logs

`/cron/logs/indexnow_submit_YYYY-MM-DD.log`

A lock file (`/cron/logs/indexnow_submit.lock`) prevents overlapping runs.

---

## 13. Reddit Run Dispatcher

**Script:** `cron/reddit_run_dispatcher.php`
**Schedule:** Every 2 minutes

```bash
*/2 * * * * /usr/bin/php /home/argorobots/public_html/cron/reddit_run_dispatcher.php
```

### What It Does

Bridges the admin **"Run discovery now"** button to a real CLI run of `reddit_monitor.php`.

The production host disables `exec` / `shell_exec` / `proc_open`, so the admin button cannot spawn a background process, and running discovery inline in the web request is killed by PHP-FPM's `request_terminate_timeout` (~30s) long before it finishes. So the button only records a request (`reddit_settings.manual_run_requested_at = NOW()`); this cron polls for that flag and runs discovery via CLI, which has no time limit.

1. Reads `reddit_settings.manual_run_requested_at`. If unset, exits immediately (one cheap SELECT).
2. Claims the request by clearing the flag, so a second tick can't double-fire it.
3. Ignores requests older than 15 minutes (a stale click can't trigger a surprise run later).
4. Runs `reddit_monitor.php` via CLI with `REDDIT_FORCE_RUN` defined, which makes discovery run even when the master enable toggle is off (a manual request is an explicit override).

This cron does not write to `cron_runs`; `reddit_monitor` records its own run when fired. Discovery starts within ~2 minutes of clicking the button; progress shows in the Reddit threads tab as usual.

### Setup (one time)

Add the column the button writes to (run in HeidiSQL):

```sql
ALTER TABLE reddit_settings
  ADD COLUMN manual_run_requested_at DATETIME DEFAULT NULL
  COMMENT 'Set by the admin "Run discovery now" button; reddit_run_dispatcher cron claims it and runs discovery via CLI.';
```

Then add the crontab line above on the server.
