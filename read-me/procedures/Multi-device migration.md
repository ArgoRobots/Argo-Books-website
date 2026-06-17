# Multi-device migration

Run BEFORE deploying the multi-device code (the new code reads/writes premium_subscription_devices).

1. Create the table (see mysql_schema.sql `premium_subscription_devices`):

```sql
CREATE TABLE IF NOT EXISTS premium_subscription_devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id VARCHAR(50) NOT NULL,
    device_id VARCHAR(255) NOT NULL,
    device_label VARCHAR(100) DEFAULT NULL,
    activated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_seen_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_sub_device (subscription_id, device_id),
    INDEX idx_subscription_id (subscription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

2. Backfill existing single-device bindings:

```sql
INSERT IGNORE INTO premium_subscription_devices
    (subscription_id, device_id, activated_at, last_seen_at, created_at)
SELECT subscription_id, device_id, redeemed_at, NOW(), NOW()
FROM premium_subscription_keys
WHERE device_id IS NOT NULL
  AND subscription_id IS NOT NULL;
```

3. Add `PREMIUM_MAX_DEVICES="2"` to the production `.env` (pricing section).

4. Deploy the code.
