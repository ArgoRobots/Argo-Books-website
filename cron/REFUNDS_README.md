# Refund cron jobs

Server-side periodic jobs that drive the refund state machine forward and keep
audit / baseline / idempotency tables fresh.

| Script | Schedule | Purpose |
|---|---|---|
| `refund_cooling_off_promoter.php` | every 1 min | Promote `cooling_off` → `processing` after the timer elapses |
| `refund_stale_processing_reconcile.php` | every 5 min | Query Stripe for stuck `processing` rows older than 30 min |
| `refund_stale_request_cleanup.php` | hourly | Cancel `pending_code` rows older than 1h; vacuum idempotency cache |
| `refund_velocity_baseline_recompute.php` | nightly 02:00 | Refresh `refund_velocity_baselines` per company |

## Linux crontab (production)

```
* * * * *  cd /var/www/argo-books-website && php cron/refund_cooling_off_promoter.php       >> storage/logs/cron-refunds.log 2>&1
*/5 * * * * cd /var/www/argo-books-website && php cron/refund_stale_processing_reconcile.php >> storage/logs/cron-refunds.log 2>&1
0 * * * *  cd /var/www/argo-books-website && php cron/refund_stale_request_cleanup.php      >> storage/logs/cron-refunds.log 2>&1
0 2 * * *  cd /var/www/argo-books-website && php cron/refund_velocity_baseline_recompute.php >> storage/logs/cron-refunds.log 2>&1
```

## Windows / laragon (dev)

Use Windows Task Scheduler or run manually for testing:

```powershell
php C:\laragon\www\argo-books-website\cron\refund_cooling_off_promoter.php
```
