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

## 9. Marketing Broadcast Sender

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

## 10. IndexNow Submit

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
