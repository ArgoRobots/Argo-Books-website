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
| `GOOGLE_PLACES_API_KEY` | — | Required for business discovery |
| `OPENAI_API_KEY` | — | Required for AI draft generation |
| `OUTREACH_DAILY_SEND_LIMIT` | 10 | Max emails sent per day (also controls discovery and draft batch sizes) |
| `OUTREACH_AUTO_APPROVE` | true | Auto-approve generated drafts |

### CLI Flags

```bash
php outreach_pipeline.php                  # Run full pipeline
php outreach_pipeline.php --discover-only  # Only discover + import businesses
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
| `IMAP_HOST` | — | e.g. `mail.argorobots.com` |
| `IMAP_PORT` | 993 | IMAP SSL port |
| `IMAP_USERNAME` | — | Full email address (e.g. `contact@argorobots.com`) |
| `IMAP_PASSWORD` | — | Mailbox password or app password |
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
