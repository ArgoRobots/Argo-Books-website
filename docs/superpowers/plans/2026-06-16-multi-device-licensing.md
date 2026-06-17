# Multi-Device Licensing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let a Premium subscription run on a small, configurable number of devices at once (default 2), with self-service device management in the logged-in web portal, replacing today's single-device "silent takeover" model.

**Architecture:** Device bindings move from the single `premium_subscription_keys.device_id` column into a new `premium_subscription_devices` table keyed by `subscription_id`. Redemption registers a device when under the limit and returns a `device_limit_reached` status (with the device list) when full, instead of overwriting whoever held the seat. Validation checks the device is in the active set. The logged-in web portal (`community/users/subscription.php`) and the admin license page let the owner/admin view and remove devices; removal is the account-authenticated action that frees a slot.

**Tech Stack:** PHP 8.3, MySQL (InnoDB, utf8mb4), PDO via global `$pdo`, PHPUnit (`./vendor/bin/phpunit`, `argo_books_test` DB via `.env.testing`). Desktop client is C#/Avalonia in a separate repo (`C:\Users\evand\Desktop\Argo-Books-Avalonia`).

## Global Constraints

- Never create migration files. Schema changes go in `mysql_schema.sql` plus a chat SQL block for manual run in HeidiSQL/phpMyAdmin. (CLAUDE.md)
- All queries through global `$pdo`; declare `global $pdo;` inside functions. Prepared statements for anything touching input; never concatenate SQL.
- `$stmt->fetch()` returns `false` when no row — check explicitly.
- PHP unit/integration tests: functions that manage their own transactions (e.g. `redeem_premium_key`) extend `Tests\Helpers\IntegrationTestCase`; functions that do not (e.g. `validate_license`) extend `Tests\Helpers\DatabaseTestCase`.
- Device limit is configurable via env `PREMIUM_MAX_DEVICES` (default 3), read through `get_pricing_config()`.
- Do not regress the existing security fixes already on `main` (rate limiting on redeem/validate, etc.).
- Security model (no email-code step, per product decision): the device limit bounds abuse; the **account-authenticated web portal** is what frees/removes seats. Residual risk: if a subscription has a free slot, someone holding the key could register their own device into it; the owner reclaims it by removing that device in the portal. Documented and accepted.

---

### Task 1: Schema — `premium_subscription_devices` table + `PREMIUM_MAX_DEVICES` config

**Files:**
- Modify: `mysql_schema.sql` (add table after `premium_subscription_keys`)
- Modify: `config/pricing.php` (add `max_devices` to `get_pricing_config()` and its docblock)
- Modify: `.env - production`, `.env - sandbox` (document the new var near the pricing section)

**Interfaces:**
- Produces: table `premium_subscription_devices(id, subscription_id, device_id, device_label, activated_at, last_seen_at, created_at)` with `UNIQUE KEY uq_sub_device (subscription_id, device_id)`; config key `max_devices` (int).

- [ ] **Step 1: Add the table to `mysql_schema.sql`** (immediately after the `premium_subscription_keys` table block)

```sql
-- Devices registered against a premium subscription. Replaces the single
-- premium_subscription_keys.device_id binding so a subscription can run on up
-- to PREMIUM_MAX_DEVICES machines at once. One row per (subscription, device).
CREATE TABLE IF NOT EXISTS premium_subscription_devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id VARCHAR(50) NOT NULL,
    device_id VARCHAR(255) NOT NULL COMMENT 'Hashed machine identifier from the desktop app',
    device_label VARCHAR(100) DEFAULT NULL COMMENT 'Optional human label (e.g. OS/platform) for the management UI',
    activated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_seen_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_sub_device (subscription_id, device_id),
    INDEX idx_subscription_id (subscription_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

- [ ] **Step 2: Add `max_devices` to `get_pricing_config()`** in `config/pricing.php`

In the `$config = [ ... ]` array (next to the other `_pricing_parse_int_env` limits), add:

```php
        'max_devices'                     => _pricing_parse_int_env('PREMIUM_MAX_DEVICES', 3),
```

And add to the env-vars docblock at the top of the file:

```php
 *   PREMIUM_MAX_DEVICES              - Max simultaneous devices per subscription (default: 3)
```

- [ ] **Step 3: Document the env var** — under `# Pricing Configuration (CAD)` in both `.env - production` and `.env - sandbox`, add:

```
# Max simultaneous devices per premium subscription (default 3)
PREMIUM_MAX_DEVICES="3"
```

- [ ] **Step 4: Commit**

```bash
git add mysql_schema.sql config/pricing.php ".env - production" ".env - sandbox"
git commit -m "Add premium_subscription_devices table and PREMIUM_MAX_DEVICES config"
```

> Note: `.env - *` template files may be gitignored; if `git add` skips them that's expected — the runtime `.env` is updated separately at deploy time.

---

### Task 2: Device helper functions

**Files:**
- Modify: `license_functions.php` (add functions near the top, after the `require_once` lines)
- Test: `tests/Integration/License/SubscriptionDevicesTest.php` (Create)

**Interfaces:**
- Consumes: `get_pricing_config()` from `config/pricing.php` (already required by callers; add `require_once __DIR__ . '/config/pricing.php';` at top of `license_functions.php` if not present).
- Produces:
  - `get_max_devices(): int`
  - `count_subscription_devices(string $subscriptionId): int`
  - `is_device_registered(string $subscriptionId, string $deviceId): bool`
  - `register_subscription_device(string $subscriptionId, string $deviceId, ?string $label = null): void` — inserts, or updates `last_seen_at`/`device_label` if the pair already exists.
  - `touch_subscription_device(string $subscriptionId, string $deviceId): void` — updates `last_seen_at` only.
  - `get_subscription_devices(string $subscriptionId): array` — rows ordered by `last_seen_at DESC`.
  - `remove_subscription_device(string $subscriptionId, string $deviceId): bool` — true if a row was deleted.

- [ ] **Step 1: Write the failing test** — `tests/Integration/License/SubscriptionDevicesTest.php`

```php
<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\IntegrationTestCase;

final class SubscriptionDevicesTest extends IntegrationTestCase
{
    private const SUB = 'PREM-DEVT-EST1-AAAA-BBBB';

    protected function tearDown(): void
    {
        $this->pdo->prepare('DELETE FROM premium_subscription_devices WHERE subscription_id = ?')
            ->execute([self::SUB]);
        parent::tearDown();
    }

    public function test_register_then_count_and_is_registered(): void
    {
        $this->assertSame(0, count_subscription_devices(self::SUB));
        $this->assertFalse(is_device_registered(self::SUB, 'dev-a'));

        register_subscription_device(self::SUB, 'dev-a', 'Windows');

        $this->assertSame(1, count_subscription_devices(self::SUB));
        $this->assertTrue(is_device_registered(self::SUB, 'dev-a'));
    }

    public function test_register_is_idempotent_per_device(): void
    {
        register_subscription_device(self::SUB, 'dev-a');
        register_subscription_device(self::SUB, 'dev-a');
        $this->assertSame(1, count_subscription_devices(self::SUB));
    }

    public function test_remove_device(): void
    {
        register_subscription_device(self::SUB, 'dev-a');
        register_subscription_device(self::SUB, 'dev-b');
        $this->assertTrue(remove_subscription_device(self::SUB, 'dev-a'));
        $this->assertFalse(is_device_registered(self::SUB, 'dev-a'));
        $this->assertSame(1, count_subscription_devices(self::SUB));
        $this->assertFalse(remove_subscription_device(self::SUB, 'dev-a')); // already gone
    }

    public function test_get_devices_returns_rows(): void
    {
        register_subscription_device(self::SUB, 'dev-a', 'Windows');
        $devices = get_subscription_devices(self::SUB);
        $this->assertCount(1, $devices);
        $this->assertSame('dev-a', $devices[0]['device_id']);
        $this->assertSame('Windows', $devices[0]['device_label']);
    }
}
```

- [ ] **Step 2: Run it to confirm it fails**

Run: `./vendor/bin/phpunit tests/Integration/License/SubscriptionDevicesTest.php`
Expected: FAIL — "Call to undefined function count_subscription_devices()".

- [ ] **Step 3: Implement the helpers** in `license_functions.php` (after the existing `require_once` lines; ensure `require_once __DIR__ . '/config/pricing.php';` is present)

```php
/**
 * Maximum number of devices a single subscription may run on simultaneously.
 */
function get_max_devices(): int
{
    return (int) get_pricing_config()['max_devices'];
}

/** Count devices currently registered to a subscription. */
function count_subscription_devices(string $subscriptionId): int
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM premium_subscription_devices WHERE subscription_id = ?'
    );
    $stmt->execute([$subscriptionId]);
    return (int) $stmt->fetchColumn();
}

/** True if this device is already registered to the subscription. */
function is_device_registered(string $subscriptionId, string $deviceId): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT 1 FROM premium_subscription_devices
         WHERE subscription_id = ? AND device_id = ? LIMIT 1'
    );
    $stmt->execute([$subscriptionId, $deviceId]);
    return $stmt->fetch() !== false;
}

/**
 * Register a device against a subscription, or refresh last_seen_at/label if it
 * already exists. Caller is responsible for enforcing the device limit BEFORE
 * calling this for a brand-new device.
 */
function register_subscription_device(string $subscriptionId, string $deviceId, ?string $label = null): void
{
    global $pdo;
    $stmt = $pdo->prepare(
        'INSERT INTO premium_subscription_devices
            (subscription_id, device_id, device_label, activated_at, last_seen_at, created_at)
         VALUES (?, ?, ?, NOW(), NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            last_seen_at = NOW(),
            device_label = COALESCE(VALUES(device_label), device_label)'
    );
    $stmt->execute([$subscriptionId, $deviceId, $label]);
}

/** Update only last_seen_at for an already-registered device. */
function touch_subscription_device(string $subscriptionId, string $deviceId): void
{
    global $pdo;
    $stmt = $pdo->prepare(
        'UPDATE premium_subscription_devices SET last_seen_at = NOW()
         WHERE subscription_id = ? AND device_id = ?'
    );
    $stmt->execute([$subscriptionId, $deviceId]);
}

/** All devices for a subscription, most-recently-seen first. */
function get_subscription_devices(string $subscriptionId): array
{
    global $pdo;
    $stmt = $pdo->prepare(
        'SELECT device_id, device_label, activated_at, last_seen_at
         FROM premium_subscription_devices
         WHERE subscription_id = ?
         ORDER BY last_seen_at DESC'
    );
    $stmt->execute([$subscriptionId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/** Remove one device. Returns true if a row was actually deleted. */
function remove_subscription_device(string $subscriptionId, string $deviceId): bool
{
    global $pdo;
    $stmt = $pdo->prepare(
        'DELETE FROM premium_subscription_devices
         WHERE subscription_id = ? AND device_id = ?'
    );
    $stmt->execute([$subscriptionId, $deviceId]);
    return $stmt->rowCount() > 0;
}
```

> Note: `VALUES(device_label)` inside `ON DUPLICATE KEY UPDATE` is deprecated in MySQL 8.0.20+ (works, emits a warning). It's fine here; if you prefer the modern form, alias the row (`INSERT ... AS new ... ON DUPLICATE KEY UPDATE device_label = COALESCE(new.device_label, device_label)`).

- [ ] **Step 4: Run the test to verify it passes**

Run: `./vendor/bin/phpunit tests/Integration/License/SubscriptionDevicesTest.php`
Expected: PASS (4 tests).

- [ ] **Step 5: Commit**

```bash
git add license_functions.php tests/Integration/License/SubscriptionDevicesTest.php
git commit -m "Add per-subscription device helper functions"
```

---

### Task 3: Redemption registers devices and enforces the limit

**Files:**
- Modify: `license_functions.php` — `redeem_premium_key()`, `_handle_re_redemption()`, `_recreate_subscription_for_key()`, and `redeem_premium_key`'s first-time block.
- Test: `tests/Integration/License/RedeemPremiumKeyTest.php` (Modify — add cases)

**Interfaces:**
- Consumes: helpers from Task 2.
- Produces: `redeem_premium_key($key, $device_id, $label = null)` — gains an optional `$label` (the device's friendly name, e.g. OS) threaded into `register_subscription_device()`. `_handle_re_redemption($key, $device_id, $subscription_id, $label = null)` and `_recreate_subscription_for_key($key, $device_id, $label = null)` gain the same trailing optional param. On success the return includes `'new_device' => bool`; when the limit is hit it returns the failure shape `['success' => false, 'status' => 'device_limit_reached', 'message' => string, 'devices' => array, 'max_devices' => int]`. On success the calling device is guaranteed present in `premium_subscription_devices`.

> **Signature threading (do this as you edit each function):**
> - `redeem_premium_key($key, $device_id, $label = null)` — pass `$label` to `_handle_re_redemption(..., $label)`, to `_recreate_subscription_for_key($key, $device_id, $label)` (only reached via `_handle_re_redemption`, so thread it through there too), and to `register_subscription_device()` in the first-time block.
> - `_handle_re_redemption($key, $device_id, $subscription_id, $label = null)`.
> - `_recreate_subscription_for_key($key, $device_id, $label = null)`.

- [ ] **Step 1: Write failing tests** — append to `tests/Integration/License/RedeemPremiumKeyTest.php`

```php
    public function test_first_redemption_registers_the_device(): void
    {
        $key = $this->seedPremiumKey(12);
        $result = redeem_premium_key($key, 'dev-1');

        $this->assertTrue($result['success']);
        $this->assertSame('active', $result['status']);
        $this->trackSubscription($result['subscription_id']);

        $this->assertTrue(is_device_registered($result['subscription_id'], 'dev-1'));
        $this->assertSame(1, count_subscription_devices($result['subscription_id']));
    }

    public function test_additional_device_registers_when_under_limit(): void
    {
        if (get_max_devices() < 2) {
            $this->markTestSkipped('Device limit < 2; multi-device add not testable.');
        }
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);

        $second = redeem_premium_key($key, 'dev-2');
        $this->assertTrue($second['success']);
        $this->assertSame(2, count_subscription_devices($first['subscription_id']));
    }

    public function test_device_over_limit_is_rejected_with_device_limit_reached(): void
    {
        // Limit-agnostic: fill exactly the limit, then one more must be rejected.
        $max = get_max_devices();
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        for ($i = 2; $i <= $max; $i++) {
            redeem_premium_key($key, 'dev-' . $i);
        }

        $over = redeem_premium_key($key, 'dev-' . ($max + 1));
        $this->assertFalse($over['success']);
        $this->assertSame('device_limit_reached', $over['status']);
        $this->assertCount($max, $over['devices']);
        $this->assertFalse(is_device_registered($first['subscription_id'], 'dev-' . ($max + 1)));
    }

    public function test_reusing_same_device_does_not_consume_a_new_slot(): void
    {
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        $again = redeem_premium_key($key, 'dev-1');

        $this->assertTrue($again['success']);
        $this->assertSame(1, count_subscription_devices($first['subscription_id']));
    }
```

Also add to that test file's `tearDown()` (or a new one) a cleanup of the devices table:

```php
    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM premium_subscription_devices WHERE subscription_id LIKE 'PREM-%'");
        parent::tearDown();
    }
```

> If `RedeemPremiumKeyTest` already defines `tearDown()`, add the `DELETE` line to it rather than redefining.

- [ ] **Step 2: Run to confirm failure**

Run: `./vendor/bin/phpunit tests/Integration/License/RedeemPremiumKeyTest.php`
Expected: FAIL (new cases error/asserts fail; `device_limit_reached` not produced).

- [ ] **Step 3: Implement — first-time redemption block** in `redeem_premium_key()`

After the subscription is created and the key is marked redeemed (right before the `$pdo->commit();`), register the device inside the transaction. Locate:

```php
        // Mark the key as redeemed with device_id
        $stmt = $pdo->prepare("
            UPDATE premium_subscription_keys
            SET redeemed_at = NOW(),
                device_id = ?,
                subscription_id = ?
            WHERE subscription_key = ?
        ");
        $stmt->execute([$device_id, $subscriptionId, $key]);

        $pdo->commit();
```

Replace with:

```php
        // Mark the key as redeemed with device_id (kept for backward compat /
        // "primary device"; the devices table is the source of truth now).
        $stmt = $pdo->prepare("
            UPDATE premium_subscription_keys
            SET redeemed_at = NOW(),
                device_id = ?,
                subscription_id = ?
            WHERE subscription_key = ?
        ");
        $stmt->execute([$device_id, $subscriptionId, $key]);

        // Register the redeeming device against the new subscription.
        register_subscription_device($subscriptionId, $device_id, $label);

        $pdo->commit();
```

- [ ] **Step 4: Implement — `_handle_re_redemption()` device limit** (replaces the free-transfer behavior)

Locate the block that begins after the expiry check, currently:

```php
        // Subscription is still active. Transfer to new device
        $stmt = $pdo->prepare("
            UPDATE premium_subscription_keys
            SET device_id = ?
            WHERE subscription_key = ?
        ");
        $stmt->execute([$device_id, $key]);

        return [
            'success' => true,
            'type' => 'premium',
            'status' => 'active',
            'message' => 'License activated successfully!',
            'subscription_id' => $subscription['subscription_id'],
            'end_date' => $subscription['end_date'],
        ];
```

Replace with:

```php
        // Subscription is active. Register THIS device if it's new and there's
        // room; never silently evict another device.
        $subId = $subscription['subscription_id'];
        $alreadyHasDevice = is_device_registered($subId, $device_id);

        if (!$alreadyHasDevice && count_subscription_devices($subId) >= get_max_devices()) {
            return [
                'success'     => false,
                'status'      => 'device_limit_reached',
                'message'     => 'This license is already active on the maximum number of devices. Remove a device from your account at ' . site_url() . '/community/users/subscription.php to free up a slot.',
                'devices'     => get_subscription_devices($subId),
                'max_devices' => get_max_devices(),
            ];
        }

        register_subscription_device($subId, $device_id, $label);

        // Keep premium_subscription_keys.device_id pointing at the most recent
        // device for backward compatibility with older app builds.
        $stmt = $pdo->prepare("
            UPDATE premium_subscription_keys SET device_id = ? WHERE subscription_key = ?
        ");
        $stmt->execute([$device_id, $key]);

        return [
            'success' => true,
            'type' => 'premium',
            'status' => 'active',
            'message' => 'License activated successfully!',
            'subscription_id' => $subId,
            'end_date' => $subscription['end_date'],
            // True only when a brand-new device was added (drives the "new device
            // activated" email in Task 5). Re-activating an existing device is false.
            'new_device' => !$alreadyHasDevice,
        ];
```

> `site_url()` is defined in `email_sender.php`/`db_connect.php` and is already available wherever `license_functions.php` is loaded for the API. If a unit test calls this without that include, hardcode the path string instead.

- [ ] **Step 4b: Also set `'new_device' => true` on the FIRST-TIME success return** in `redeem_premium_key()` (the `'status' => 'active'` array returned after the first-time commit). First-time redemption is always a new device. Add the key:

```php
            'duration_months' => $duration_months,
            'new_device' => true,
```

- [ ] **Step 5: Implement — `_recreate_subscription_for_key()` registers the device**

Before its `$pdo->commit();`, after the key UPDATE that sets the new `subscription_id`, add:

```php
        register_subscription_device($newSubscriptionId, $device_id, $label);
```

And add `'new_device' => true` to its success return array (so a recreate also triggers the new-device email, consistent with the other paths):

```php
            'duration_months' => $duration_months,
            'new_device' => true,
```

- [ ] **Step 6: Run the tests**

Run: `./vendor/bin/phpunit tests/Integration/License/RedeemPremiumKeyTest.php`
Expected: PASS (existing + new cases).

- [ ] **Step 7: Commit**

```bash
git add license_functions.php tests/Integration/License/RedeemPremiumKeyTest.php
git commit -m "Enforce per-subscription device limit on redemption"
```

---

### Task 4: Validation checks the device set

**Files:**
- Modify: `license_functions.php` — `validate_license()`
- Test: `tests/Integration/License/ValidateLicenseTest.php` (Modify — add multi-device cases; this file extends `DatabaseTestCase`)

**Interfaces:**
- Consumes: helpers from Task 2.
- Produces: `validate_license($key, $device_id)` returns `valid` when the device is in the subscription's device set (and active), `wrong_device` when it is not, `expired`/`invalid_key` unchanged. On `valid` it refreshes `last_seen_at`.

- [ ] **Step 1: Write failing tests** — append to `ValidateLicenseTest.php`

```php
    public function test_valid_when_device_in_set_even_if_not_primary(): void
    {
        $subId = 'PREM-TEST-MULT-DEV1-AAAA';
        $this->seedSubscription($subId, (new \DateTime('+90 days'))->format('Y-m-d H:i:s'));
        // Primary device on the key is 'device-primary', but 'device-second'
        // is also a registered device in the new table.
        $key = $this->seedRedeemedKey('device-primary', $subId);
        $this->pdo->prepare(
            'INSERT INTO premium_subscription_devices (subscription_id, device_id) VALUES (?, ?), (?, ?)'
        )->execute([$subId, 'device-primary', $subId, 'device-second']);

        $result = validate_license($key, 'device-second');
        $this->assertTrue($result['success']);
        $this->assertSame('valid', $result['status']);
    }

    public function test_wrong_device_when_not_in_set(): void
    {
        $subId = 'PREM-TEST-MULT-DEV2-BBBB';
        $this->seedSubscription($subId, (new \DateTime('+90 days'))->format('Y-m-d H:i:s'));
        $key = $this->seedRedeemedKey('device-primary', $subId);
        $this->pdo->prepare(
            'INSERT INTO premium_subscription_devices (subscription_id, device_id) VALUES (?, ?)'
        )->execute([$subId, 'device-primary']);

        $result = validate_license($key, 'device-unknown');
        $this->assertFalse($result['success']);
        $this->assertSame('wrong_device', $result['status']);
    }
```

> `DatabaseTestCase` wraps each test in a transaction and rolls back, so the inserted device rows are cleaned up automatically.

- [ ] **Step 2: Run to confirm failure**

Run: `./vendor/bin/phpunit tests/Integration/License/ValidateLicenseTest.php`
Expected: FAIL — `test_valid_when_device_in_set...` fails because current code only matches the single `premium_subscription_keys.device_id`.

- [ ] **Step 3: Implement** — in `validate_license()`, replace the device-match block. Locate:

```php
        // Check device_id matches
        if ($premium_key['device_id'] !== $device_id) {
            return [
                'success' => false,
                'status' => 'wrong_device',
                'message' => 'This license key is active on a different device.'
            ];
        }
```

Replace with:

```php
        // Device must be one of the subscription's registered devices.
        // (Fall back to the legacy single device_id for rows that predate the
        // devices table and weren't backfilled.)
        $subId = $premium_key['subscription_id'];
        $deviceOk = ($subId !== null && is_device_registered($subId, $device_id))
            || ($premium_key['device_id'] === $device_id);

        if (!$deviceOk) {
            return [
                'success' => false,
                'status' => 'wrong_device',
                'message' => 'This device is not activated for this license.'
            ];
        }
```

Then, just before the final `return ['success' => true, 'status' => 'valid', ...]`, refresh last-seen:

```php
        if ($subId !== null) {
            touch_subscription_device($subId, $device_id);
        }
```

- [ ] **Step 4: Run the tests**

Run: `./vendor/bin/phpunit tests/Integration/License/ValidateLicenseTest.php`
Expected: PASS (existing + 2 new).

- [ ] **Step 5: Commit**

```bash
git add license_functions.php tests/Integration/License/ValidateLicenseTest.php
git commit -m "Validate license against the device set, not a single device"
```

---

### Task 5: API surface — `device_limit_reached`, device list, and new-device email

**Files:**
- Modify: `email_sender.php` (add `send_new_device_activated_email()`)
- Modify: `api/license/redeem.php` (send the email on a new-device activation; the `device_limit_reached`/`devices`/`max_devices` keys already pass through unchanged)
- Test: `tests/Integration/License/RedeemEndpointShapeTest.php` (Create)

**Interfaces:**
- Consumes: `redeem_premium_key()` return including `success`, `new_device`, `subscription_id` (Task 3); `send_styled_email()` and `site_url()` from `email_sender.php`.
- Produces: redeem endpoint JSON includes `status: "device_limit_reached"`, `devices: [...]`, `max_devices: N` when the limit is hit; `send_new_device_activated_email(string $email, string $manageUrl): bool`.

- [ ] **Step 1: Add the email function** to `email_sender.php` (near the other premium emails)

```php
/**
 * Notify the subscriber that a new device was just activated on their Premium
 * subscription. Doubles as a security signal: if they don't recognize it, they
 * can remove it from the management page.
 *
 * @param string $email     Subscriber email
 * @param string $manageUrl Link to the device-management page
 * @return bool Success status
 */
function send_new_device_activated_email($email, $manageUrl)
{
    $site_url = site_url();
    $body = <<<HTML
        <h1>New device activated</h1>
        <p>A new device was just activated on your Argo Premium subscription.</p>
        <p>If this was you, you're all set — no action needed.</p>
        <div class="info-box info-box-warning">
            <p><strong>Don't recognize it?</strong> You can review and remove devices on your subscription page:</p>
            <p><a href="{$manageUrl}">Manage your devices</a></p>
        </div>
        <div class="receipt-footer">
            <p>Thank you for using Argo Books!</p>
            <p><a href="{$site_url}">argorobots.com</a></p>
        </div>
        HTML;

    return send_styled_email($email, 'New device activated - Argo Premium', $body, 'purple');
}
```

- [ ] **Step 2a: Read and pass the device label** — in `api/license/redeem.php`, read the optional label from the request and pass it to `redeem_premium_key`. Add near the existing `$device_id = trim($data['device_id'] ?? '');`:

```php
    $device_label = isset($data['device_label']) ? substr(trim((string) $data['device_label']), 0, 100) : null;
```

and change the redemption call from `redeem_premium_key($premium_key, $device_id)` to:

```php
        $response = redeem_premium_key($premium_key, $device_id, $device_label);
```

- [ ] **Step 2b: Wire the email into the endpoint** — in `api/license/redeem.php`, after `$response = redeem_premium_key(...)` and before `echo json_encode($response);`, add:

```php
    // Best-effort: notify the subscriber when a brand-new device is activated.
    // Never let email failure affect the activation result.
    if (!empty($response['success']) && !empty($response['new_device']) && !empty($response['subscription_id'])) {
        try {
            require_once __DIR__ . '/../../email_sender.php';
            $stmt = $pdo->prepare("SELECT email FROM premium_subscriptions WHERE subscription_id = ?");
            $stmt->execute([$response['subscription_id']]);
            $subEmail = $stmt->fetchColumn();
            if (!empty($subEmail)) {
                send_new_device_activated_email($subEmail, site_url() . '/community/users/subscription.php');
            }
        } catch (\Throwable $e) {
            error_log('New-device activation email failed: ' . $e->getMessage());
        }
    }
```

> The free-key redemption path may have a NULL subscription email; the `!empty($subEmail)` guard simply skips the email in that case.

- [ ] **Step 3: Add a thin assertion test** — `tests/Integration/License/RedeemEndpointShapeTest.php` (Create)

```php
<?php
declare(strict_types=1);

namespace Tests\Integration\License;

use Tests\Helpers\IntegrationTestCase;

final class RedeemEndpointShapeTest extends IntegrationTestCase
{
    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM premium_subscription_devices WHERE subscription_id LIKE 'PREM-%'");
        parent::tearDown();
    }

    public function test_device_limit_reached_payload_is_json_serializable(): void
    {
        $max = get_max_devices();
        $key = $this->seedPremiumKey(12);
        $first = redeem_premium_key($key, 'dev-1');
        $this->trackSubscription($first['subscription_id']);
        for ($i = 2; $i <= $max; $i++) {
            redeem_premium_key($key, 'dev-' . $i);
        }
        $limited = redeem_premium_key($key, 'dev-' . ($max + 1));

        $json = json_encode($limited);
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('device_limit_reached', $decoded['status']);
        $this->assertArrayHasKey('devices', $decoded);
        $this->assertArrayHasKey('max_devices', $decoded);
    }
}
```

- [ ] **Step 4: Run**

Run: `./vendor/bin/phpunit tests/Integration/License/RedeemEndpointShapeTest.php`
Expected: PASS.

- [ ] **Step 5: Manually verify the email** — point `.env.testing`/local at MailHog (see `read-me/setup/Local email setup.md`), redeem a key on a new device, and confirm a "New device activated" email lands with a working "Manage your devices" link.

- [ ] **Step 6: Commit**

```bash
git add email_sender.php api/license/redeem.php tests/Integration/License/RedeemEndpointShapeTest.php
git commit -m "Email subscriber on new-device activation; cover endpoint payload"
```

---

### Task 6: Self-service device management in the web portal

**Files:**
- Create: `community/users/remove-device.php` (account-authenticated POST endpoint)
- Modify: `community/users/subscription.php` (render the device list + remove buttons, when `$premium_subscription` exists)
- Modify: `community/users/subscription.css` (styles for the device list — follow existing classes)

**Interfaces:**
- Consumes: `require_login()`, `$_SESSION['user_id']`, `$_SESSION['csrf_token']` (all already used by `subscription.php`); `get_user_premium_subscription($user_id)`; `get_subscription_devices()`, `remove_subscription_device()` from Task 2.
- Produces: a POST endpoint `community/users/remove-device.php` accepting `csrf_token` + `device_id`, returning JSON `{success: bool, error?: string}`.

- [ ] **Step 1: Create the removal endpoint** — `community/users/remove-device.php`

```php
<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../community_functions.php';
require_once __DIR__ . '/user_functions.php';
require_once __DIR__ . '/../../license_functions.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$device_id = trim($_POST['device_id'] ?? '');
if ($device_id === '') {
    echo json_encode(['success' => false, 'error' => 'No device specified']);
    exit;
}

// Only let a user remove a device from THEIR OWN subscription.
$subscription = get_user_premium_subscription($user_id);
if (!$subscription) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'No active subscription']);
    exit;
}

$removed = remove_subscription_device($subscription['subscription_id'], $device_id);
echo json_encode(['success' => $removed, 'error' => $removed ? null : 'Device not found']);
```

- [ ] **Step 2: Render the device list** — in `subscription.php`, inside the block that runs when `$premium_subscription` is truthy (near the payment-history fetch), load devices:

```php
$subscription_devices = [];
if ($premium_subscription) {
    require_once __DIR__ . '/../../license_functions.php';
    $subscription_devices = get_subscription_devices($premium_subscription['subscription_id']);
}
$max_devices = (int) $pricing['max_devices'];
```

Then, in the HTML where subscription details render (after the plan/billing section), add a devices card:

```php
<?php if ($premium_subscription): ?>
<div class="devices-card">
    <h2>Your devices <span class="devices-count"><?php echo count($subscription_devices); ?> / <?php echo $max_devices; ?></span></h2>
    <p class="devices-help">Argo Premium can run on up to <?php echo $max_devices; ?> devices. Remove one to free a slot for a new computer.</p>
    <?php if (empty($subscription_devices)): ?>
        <p class="devices-empty">No devices activated yet.</p>
    <?php else: ?>
    <ul class="device-list">
        <?php foreach ($subscription_devices as $d): ?>
        <li class="device-row" data-device-id="<?php echo htmlspecialchars($d['device_id']); ?>">
            <span class="device-label"><?php echo htmlspecialchars($d['device_label'] ?: 'Device'); ?></span>
            <span class="device-seen">Last used <?php echo htmlspecialchars(date('M j, Y', strtotime($d['last_seen_at']))); ?></span>
            <button type="button" class="btn-remove-device">Remove</button>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.btn-remove-device').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var row = btn.closest('.device-row');
        var deviceId = row.getAttribute('data-device-id');
        if (!confirm('Remove this device? It will lose Premium access until reactivated.')) return;
        btn.disabled = true;
        fetch('remove-device.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'csrf_token=<?php echo $_SESSION['csrf_token']; ?>&device_id=' + encodeURIComponent(deviceId)
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) { row.remove(); }
            else { alert(res.error || 'Could not remove device.'); btn.disabled = false; }
        })
        .catch(function () { alert('Network error.'); btn.disabled = false; });
    });
});
</script>
<?php endif; ?>
```

- [ ] **Step 3: Style it** — add to `community/users/subscription.css`, reusing existing color variables (`var(--...)` from `custom-colors.css`):

```css
.devices-card { margin-top: 24px; padding: 20px; border-radius: 8px; background: var(--card-bg, #fff); }
.devices-count { font-weight: 600; color: var(--text-muted, #64748b); }
.devices-help { color: var(--text-muted, #64748b); font-size: 14px; }
.device-list { list-style: none; padding: 0; margin: 12px 0 0; }
.device-row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border, #e2e8f0); }
.device-row .device-label { font-weight: 600; }
.device-row .device-seen { color: var(--text-muted, #64748b); font-size: 13px; margin-left: auto; }
.btn-remove-device { padding: 4px 12px; border-radius: 4px; cursor: pointer; }
```

> Match the dark-theme approach used elsewhere: if `subscription.css` has `[data-theme="dark"]` overrides, add matching ones for `.devices-card`/`.device-row`.

- [ ] **Step 4: Manual test**

1. Log in as a user with a premium subscription (seed two devices via SQL: `INSERT INTO premium_subscription_devices (subscription_id, device_id, device_label) VALUES ('<sub>','d1','Windows'),('<sub>','d2','Mac');`).
2. Load `/community/users/subscription.php` — confirm "Your devices 2 / 2" and both rows show.
3. Click Remove on one → row disappears; reload → count is 1 / 2.
4. Confirm a logged-out request to `remove-device.php` is rejected (redirect/403), and a POST without a valid CSRF token returns `Invalid request`.

- [ ] **Step 5: Commit**

```bash
git add community/users/remove-device.php community/users/subscription.php community/users/subscription.css
git commit -m "Add self-service device management to the subscription portal"
```

---

### Task 7: Admin device view + reset

**Files:**
- Modify: `admin/license/index.php` (show device count per subscription; add a "Manage devices" action that lists/removes devices, mirroring the existing AJAX patterns like `reset_usage`)

**Interfaces:**
- Consumes: `get_subscription_devices()`, `remove_subscription_device()`, `count_subscription_devices()` from Task 2; existing `verify_csrf_token()` in the admin page.
- Produces: an admin AJAX action `?manage_devices` / `remove_device` (POST) returning JSON.

- [ ] **Step 1: Add a device-count column to the subscriptions table** — in the `get_premium_subscriptions()` query add a correlated subquery:

```sql
                (
                    SELECT COUNT(*) FROM premium_subscription_devices d
                    WHERE d.subscription_id = s.subscription_id
                ) AS device_count
```

and render it in the table body near the usage columns:

```php
<td><?php echo (int) ($sub['device_count'] ?? 0); ?> / <?php echo (int) get_pricing_config()['max_devices']; ?></td>
```

(Add a matching `<th>Devices</th>` header.)

- [ ] **Step 2: Add an admin remove-device AJAX handler** — near the existing `reset_usage` POST handler at the top of `admin/license/index.php`:

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_remove_device'])) {
    header('Content-Type: application/json');
    if (!verify_csrf_token()) {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
        exit;
    }
    $subId = trim($_POST['subscription_id'] ?? '');
    $deviceId = trim($_POST['device_id'] ?? '');
    if ($subId === '' || $deviceId === '') {
        echo json_encode(['success' => false, 'error' => 'Missing parameters']);
        exit;
    }
    require_once __DIR__ . '/../../license_functions.php';
    $ok = remove_subscription_device($subId, $deviceId);
    echo json_encode(['success' => $ok]);
    exit;
}
```

- [ ] **Step 3: Manual test**

1. Open `admin/license/` — confirm the new "Devices" column shows `N / max`.
2. Seed devices for a subscription via SQL, POST `admin_remove_device` with a valid CSRF token, confirm the row is deleted and the count drops.

- [ ] **Step 4: Commit**

```bash
git add admin/license/index.php
git commit -m "Show and manage subscription devices in admin"
```

---

### Task 8: Migration / backfill (manual SQL) + deploy ordering

**Files:**
- Create: `read-me/procedures/Multi-device migration.md` (record the steps; matches the repo's read-me convention)

**Interfaces:** none (operational).

- [ ] **Step 1: Write the migration doc** — `read-me/procedures/Multi-device migration.md` containing the exact SQL and order:

```markdown
# Multi-device migration

Run BEFORE deploying the multi-device code (the new code reads/writes
premium_subscription_devices).

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
```

- [ ] **Step 2: Run the migration on sandbox**, verify backfill row counts match `SELECT COUNT(*) FROM premium_subscription_keys WHERE device_id IS NOT NULL AND subscription_id IS NOT NULL;`.

- [ ] **Step 3: Commit**

```bash
git add read-me/procedures/Multi-device\ migration.md
git commit -m "Document multi-device migration steps"
```

---

### Task 9: Desktop app changes (separate repo: Argo-Books-Avalonia)

> This task is implemented in `C:\Users\evand\Desktop\Argo-Books-Avalonia`, not this repo. It is required for the full UX but the server changes (Tasks 1–8) are backward compatible with the current app: old builds keep working because `premium_subscription_keys.device_id` is still maintained and `validate_license` falls back to it.

**Files (app repo):**
- Modify: `ArgoBooks.Core/ViewModels/UpgradeModalViewModel.cs` (`VerifyKey()`)
- Modify: `ArgoBooks.Core/Services/LicenseService.cs`

**Interfaces:**
- Consumes: redeem endpoint's new `status: "device_limit_reached"`, `devices[]`, `max_devices`.

- [ ] **Step 1: Send a device label on redeem** — include the OS/platform name in the redeem POST so the portal/admin show something friendlier than a hash. Add `"device_label": "<Windows|macOS|Linux>"` to the request body; server: extend `redeem_premium_key()` signature to accept an optional label and pass it to `register_subscription_device()` (server-side change folded into Task 3 if desired, default null keeps it optional).

- [ ] **Step 2: Handle `device_limit_reached`** in `VerifyKey()` — when `status == "device_limit_reached"`, show a dialog: "This license is already active on {max_devices} devices. Remove one at argorobots.com → Premium Subscription, then try again." with a button that opens `https://argorobots.com/community/users/subscription.php`.

- [ ] **Step 3: Reframe `wrong_device`** in `LicenseService` startup validation — message becomes "This device isn't activated for your license. Open the Upgrade menu and re-enter your key to activate it (you can run on up to {N} devices)."

- [ ] **Step 4: Manual end-to-end test** against sandbox: activate on 3 devices (succeeds), 4th shows the limit dialog; remove one in the portal; 4th then activates.

---

### Task 10: Surface the device limit on plan/pricing cards (website + desktop app)

> Independent of the enforcement tasks (1–9) — pure plan-copy/config — so it can ship anytime. Because every website surface renders from `plans.json` via `get_plan_features()`, and `api/pricing/plans.php` serves that same data to the desktop app, adding the feature in one place propagates to the website cards and (if the app renders from the API) the app's cards too.

**Files:**
- Modify: `config/plans.json` (add the premium feature)
- Modify: `config/pricing.php` (`get_plan_features()` placeholder substitution; optionally `pricing_template_vars()`)
- Verify (no change expected): `partials/pricing-cards.php`, `pricing/index.php`, the `compare/*` and `for-*` landing pages, `api/pricing/plans.php`
- App repo (verify, maybe modify): the upgrade/payment-card feature source

**Interfaces:**
- Consumes: `get_pricing_config()['max_devices']` (Task 1).
- Produces: a new premium feature line "Use on up to N devices" rendered wherever plans are shown.

- [ ] **Step 1: Add the feature to `config/plans.json`** — add to the `premium.features` array (e.g. after "Predictive analytics"):

```json
            {"label": "Use on up to {premium_max_devices} devices"},
```

- [ ] **Step 2: Substitute the placeholder** in `get_plan_features()` (`config/pricing.php`) — add to the `strtr` map:

```php
        '{premium_max_devices}'             => (string) $cfg['max_devices'],
```

Optional: if any article/marketing copy needs the number inline, also add to `pricing_template_vars()`:

```php
        '{argo_max_devices}'              => (string) $cfg['max_devices'],
```

- [ ] **Step 3: Verify website propagation (no code change)** — load `/pricing/` and one `for-*` landing page; confirm the Premium card shows "Use on up to 3 devices". Then `curl https://<host>/api/pricing/plans.php` and confirm the feature string (already substituted) appears in `plans.premium.features`.

- [ ] **Step 4: Desktop app card source** — verify whether the upgrade/payment cards in the app are populated from `GET /api/pricing/plans.php` (the Explore notes point to `UpgradeModalViewModel`). 
  - If the app renders features from that endpoint → the new line appears automatically; just confirm it in-app. **No app release needed.**
  - If the app hardcodes the premium feature list, add "Use on up to 3 devices" to that hardcoded list (search the app for the other feature strings like "Predictive analytics" / "Priority support") and ship it with the app update from Task 9.

- [ ] **Step 5: Commit (website side)**

```bash
git add config/plans.json config/pricing.php
git commit -m "Show device limit on Premium plan cards"
```

---

## Self-Review

- **Spec coverage:** Phase 1 (device model) → Tasks 1–2. Phase 2 (activation/validation) → Tasks 3–5. New-device activation email → Task 5. Phase 3 (self-service portal) → Task 6. Phase 4 (admin + desktop) → Tasks 7 & 9. Phase 5 (migration & rollout) → Task 8. Device limit is env-driven (`PREMIUM_MAX_DEVICES`, default 3) → Task 1. Device limit shown on plan cards (website + app, via `plans.json`/`api/pricing/plans.php`) → Task 10. All covered.
- **Type consistency:** helper names (`register_subscription_device`, `count_subscription_devices`, `is_device_registered`, `get_subscription_devices`, `remove_subscription_device`, `touch_subscription_device`, `get_max_devices`) are used identically across Tasks 2–7. Redeem failure shape `device_limit_reached` with `devices`/`max_devices` is produced in Task 3 and consumed in Tasks 5, 6, 9. The `new_device` boolean is produced in Task 3 (both first-time and re-redemption success returns) and consumed in Task 5 to trigger the email.
- **Security note (carry into review):** with no email-code on activation (Phase 0 intentionally excluded), a free device slot can be claimed by anyone holding the key; the owner reclaims via the portal. If this proves abused, add an opt-in "require portal approval for new devices" toggle later.

## Decisions (confirmed)
- Device limit: **3** (`PREMIUM_MAX_DEVICES=3`).
- Management lives in the logged-in web portal; no email-code on activation (a "new device activated" email is the security signal instead).
- Device limit is advertised on the Premium plan cards everywhere (Task 10).
