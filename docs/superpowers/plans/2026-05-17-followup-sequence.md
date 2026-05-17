# Multi-touch Follow-up Sequence Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the single-template follow-up system with a configurable multi-touch sequence that uses Gemini-personalized drafts, queues for admin review, and integrates with the existing A/B framework.

**Architecture:** New `outreach_followups` table holds one row per scheduled touch. Lifecycle: scheduled → drafted (Gemini, ~1 day before send) → approved (admin in Review mode, or auto in Auto-send mode) → sent. Halts on reply/unsubscribe/bounce. A/B tested as "whole sequence strategy" via a new `followup_sequence` variant_type. Configured per-touch (count + days_after_prev + default intent) in admin Settings.

**Tech Stack:** PHP 8.3, MySQL/PDO, vanilla JS for admin UI, Gemini API for personalization, Resend SMTP for send.

**Spec:** [docs/superpowers/specs/2026-05-17-followup-sequence-design.md](../specs/2026-05-17-followup-sequence-design.md)

**Operational notes:**
- This project has no PHPUnit suite. Tests are CLI smoke scripts run via `php cron/test_followups_smoke.php`.
- Schema changes go in `mysql_schema.sql` AND get output as a chat-message SQL block for the user to run manually on production (no migration files — see CLAUDE.md).
- The user does NOT want any `git commit` commands auto-run. Where a task ends with "Stage and commit", the bash block is for the user to run when ready — leave the staging up to them.

---

## Task 1: Schema — new table + ENUM expansion

**Files:**
- Modify: `mysql_schema.sql` (append after the existing `outreach_ab_variants` table block, around line 887)

- [ ] **Step 1: Append `outreach_followups` table to mysql_schema.sql**

Add this block immediately after the existing `outreach_ab_variants` table definition (before `outreach_scrape_cache` at line 895):

```sql
-- ─────────────────────────────────────────────────────────────────────
-- outreach_followups
-- One row per scheduled follow-up touch (touch 1 = original first-touch
-- email, lives in outreach_leads). Created in bulk when first-touch send
-- succeeds (one row per configured touch in followup_sequence_config).
--
-- State machine:
--   scheduled  →  drafted  →  approved  →  sent
--      └─→ halted (replied/unsubscribed/bounced/manual/max_reached)
--      └─→ skipped (admin clicked skip on the drafted row)
--      └─→ failed  (Gemini call failed 3 times)
--
-- ab_test_id / ab_variant_id are copied from the lead's assignment at
-- creation time so the whole sequence shares one variant (we test
-- strategies, not arbitrary mixes).
-- ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS outreach_followups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    touch_number TINYINT UNSIGNED NOT NULL,
    scheduled_for DATETIME NOT NULL,
    draft_subject VARCHAR(500) DEFAULT NULL,
    draft_body TEXT DEFAULT NULL,
    drafted_at DATETIME DEFAULT NULL,
    draft_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('scheduled','drafted','approved','sent','halted','skipped','failed') NOT NULL DEFAULT 'scheduled',
    halt_reason VARCHAR(100) DEFAULT NULL,
    ab_test_id INT DEFAULT NULL,
    ab_variant_id INT DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    message_id VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_lead_touch (lead_id, touch_number),
    INDEX idx_status_scheduled (status, scheduled_for),
    INDEX idx_lead (lead_id, touch_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- For existing installs (run alongside the ALTER below):
--   (the CREATE TABLE IF NOT EXISTS above is safe to re-run.)
```

- [ ] **Step 2: Expand the `outreach_ab_tests.variant_type` ENUM**

In `mysql_schema.sql`, find the `outreach_ab_tests` table definition (around line 863). Change this line:

```sql
variant_type ENUM('subject','body','sender','cta','preheader','format','personalization') NOT NULL DEFAULT 'subject',
```

To:

```sql
variant_type ENUM('subject','body','sender','cta','preheader','format','personalization','followup_sequence') NOT NULL DEFAULT 'subject',
```

Also add an ALTER-comment note directly under the table for existing-install operators:

```sql
-- For existing installs, expand the variant_type ENUM:
--   ALTER TABLE outreach_ab_tests
--     MODIFY COLUMN variant_type ENUM('subject','body','sender','cta','preheader','format','personalization','followup_sequence') NOT NULL DEFAULT 'subject';
```

- [ ] **Step 3: Verify by running the schema file against a local fresh DB**

Run:

```bash
# Drop & recreate locally to make sure the schema is valid (DESTRUCTIVE on local only — NOT prod)
mysql -u root -e "DROP DATABASE IF EXISTS argo_books_test; CREATE DATABASE argo_books_test;"
mysql -u root argo_books_test < mysql_schema.sql
mysql -u root argo_books_test -e "DESCRIBE outreach_followups;"
mysql -u root argo_books_test -e "SHOW COLUMNS FROM outreach_ab_tests LIKE 'variant_type';"
```

Expected: `outreach_followups` shows all columns from the spec. `variant_type` Type column ends with `'followup_sequence')`.

- [ ] **Step 4: Stage and commit (user runs when ready)**

```bash
git add mysql_schema.sql
git commit -m "feat(outreach): add outreach_followups table and followup_sequence variant_type"
```

---

## Task 2: Helper — `schedule_followups_for_lead()`

**Files:**
- Modify: `cron/lib/outreach_helpers.php` (add new function near the existing `send_outreach_lead` function)

**Purpose:** Called immediately after `send_outreach_lead()` succeeds. Reads the active sequence config and inserts N `outreach_followups` rows for the lead. Copies the lead's `ab_test_id` / `ab_variant_id` onto each row so the whole sequence shares one A/B variant assignment.

- [ ] **Step 1: Add the helper function**

Append this function to `cron/lib/outreach_helpers.php` (after `send_outreach_lead`, before `send_outreach_followup`):

```php
/**
 * After a first-touch email is sent, create one outreach_followups row per
 * touch in the active sequence config. scheduled_for is computed cumulatively
 * from days_after_prev. Copies the lead's existing A/B test/variant assignment
 * (set during first-touch drafting) onto every follow-up row so all touches
 * in the sequence use the same followup_sequence variant.
 *
 * Idempotent at the row level via UNIQUE KEY (lead_id, touch_number) —
 * a re-call (e.g. after a manual resend) will silently no-op on already-
 * existing rows.
 *
 * Returns the number of rows actually inserted.
 */
function schedule_followups_for_lead($pdo, int $leadId, ?int $abTestId = null, ?int $abVariantId = null): int
{
    // Read the active sequence config from outreach_pipeline_state. If unset
    // or empty, no follow-ups are scheduled (sequence disabled).
    $stmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'");
    $stmt->execute();
    $row = $stmt->fetch();
    $configJson = $row ? $row['state_value'] : null;
    if (!$configJson) return 0;

    $config = json_decode($configJson, true);
    if (!is_array($config) || empty($config)) return 0;

    // The lead's first-touch sent_at — needed as the anchor for scheduled_for.
    $leadStmt = $pdo->prepare("SELECT sent_at FROM outreach_leads WHERE id = ?");
    $leadStmt->execute([$leadId]);
    $leadRow = $leadStmt->fetch();
    if (!$leadRow || !$leadRow['sent_at']) return 0;

    $anchor = strtotime($leadRow['sent_at']);
    if ($anchor === false) return 0;

    $cumulativeDays = 0;
    $inserted = 0;

    $insertStmt = $pdo->prepare(
        "INSERT IGNORE INTO outreach_followups
            (lead_id, touch_number, scheduled_for, status, ab_test_id, ab_variant_id)
         VALUES (?, ?, ?, 'scheduled', ?, ?)"
    );

    foreach ($config as $entry) {
        if (!is_array($entry) || !isset($entry['touch'], $entry['days_after_prev'])) continue;
        $touchNumber = (int) $entry['touch'];
        $daysAfterPrev = max(1, (int) $entry['days_after_prev']);
        $cumulativeDays += $daysAfterPrev;
        $scheduledFor = date('Y-m-d H:i:s', $anchor + $cumulativeDays * 86400);

        $insertStmt->execute([$leadId, $touchNumber, $scheduledFor, $abTestId, $abVariantId]);
        if ($insertStmt->rowCount() > 0) {
            $inserted++;
        }
    }

    return $inserted;
}
```

- [ ] **Step 2: Smoke check from CLI**

Run:

```bash
php -r "
require __DIR__.'/vendor/autoload.php';
\$dotenv = Dotenv\\Dotenv::createImmutable(__DIR__);
\$dotenv->load();
require __DIR__.'/db_connect.php';
require __DIR__.'/cron/lib/outreach_helpers.php';

// Seed minimal config
\$pdo->exec(\"INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('followup_sequence_config', '[{\\\"touch\\\":2,\\\"days_after_prev\\\":3,\\\"default_intent\\\":\\\"bump\\\"},{\\\"touch\\\":3,\\\"days_after_prev\\\":7,\\\"default_intent\\\":\\\"angle\\\"}]') ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)\");

// Insert a fake lead with sent_at
\$pdo->exec(\"INSERT INTO outreach_leads (business_name, email, sent_at) VALUES ('SmokeTest Co', 'smoke@example.com', NOW())\");
\$id = (int) \$pdo->lastInsertId();

\$n = schedule_followups_for_lead(\$pdo, \$id);
echo \"Inserted rows: \$n\\n\";

\$rows = \$pdo->query(\"SELECT touch_number, scheduled_for, status FROM outreach_followups WHERE lead_id = \$id ORDER BY touch_number\")->fetchAll(PDO::FETCH_ASSOC);
foreach (\$rows as \$r) { print_r(\$r); }

// Cleanup
\$pdo->exec(\"DELETE FROM outreach_leads WHERE id = \$id\");
"
```

Expected output: `Inserted rows: 2`, two rows printed with `touch_number=2` and `3`, `status='scheduled'`, `scheduled_for` ~3 days and ~10 days from now.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/lib/outreach_helpers.php
git commit -m "feat(outreach): add schedule_followups_for_lead helper"
```

---

## Task 3: Helper — `draft_followup_via_gemini()`

**Files:**
- Modify: `cron/lib/outreach_helpers.php` (add after `schedule_followups_for_lead`)

**Purpose:** Generates a single follow-up draft for one `outreach_followups` row. Calls Gemini with the touch's intent (from active A/B variant or default), original first-touch context, and lead info. Updates the row's `draft_subject` / `draft_body` / `drafted_at` / `status` fields. Returns true on success, false on failure (which the caller increments `draft_attempts` for).

- [ ] **Step 1: Add the helper function**

Append after `schedule_followups_for_lead`:

```php
/**
 * Generates a follow-up draft for a single outreach_followups row using Gemini.
 *
 * Determines the per-touch intent by:
 *  1. If the row has an ab_variant_id, parsing that variant's JSON content
 *     and extracting the intent for this touch_number.
 *  2. Otherwise, reading the default_intent from followup_sequence_config.
 *
 * Prompts Gemini with the original first-touch subject + body for context so
 * the follow-up reads as a coherent continuation rather than a fresh pitch.
 *
 * On success: writes draft_subject/draft_body/drafted_at, flips status to
 * 'drafted', and returns true.
 * On failure: increments draft_attempts, may flip status to 'failed' if
 * attempts >= 3, and returns false. Does NOT throw.
 */
function draft_followup_via_gemini($pdo, array $followupRow): bool
{
    $followupId = (int) $followupRow['id'];
    $leadId = (int) $followupRow['lead_id'];
    $touchNumber = (int) $followupRow['touch_number'];
    $abVariantId = isset($followupRow['ab_variant_id']) ? (int) $followupRow['ab_variant_id'] : 0;

    // Fetch lead context (business name, summary, original first-touch subject/body, category)
    $leadStmt = $pdo->prepare("SELECT business_name, business_summary, category, city, draft_subject, draft_body FROM outreach_leads WHERE id = ?");
    $leadStmt->execute([$leadId]);
    $lead = $leadStmt->fetch();
    if (!$lead) {
        // Lead deleted — mark followup failed permanently
        $pdo->prepare("UPDATE outreach_followups SET status = 'failed', draft_attempts = draft_attempts + 1 WHERE id = ?")
            ->execute([$followupId]);
        return false;
    }

    // Determine intent: A/B variant override OR default_intent from config
    $intent = null;
    if ($abVariantId > 0) {
        $vStmt = $pdo->prepare("SELECT content FROM outreach_ab_variants WHERE id = ?");
        $vStmt->execute([$abVariantId]);
        $vRow = $vStmt->fetch();
        if ($vRow) {
            $parsed = json_decode((string) $vRow['content'], true);
            if (is_array($parsed)) {
                foreach ($parsed as $entry) {
                    if (is_array($entry) && isset($entry['touch'], $entry['intent']) && (int) $entry['touch'] === $touchNumber) {
                        $intent = (string) $entry['intent'];
                        break;
                    }
                }
            }
        }
    }
    if ($intent === null) {
        // Fall back to config default
        $cfgStmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'");
        $cfgStmt->execute();
        $cfgRow = $cfgStmt->fetch();
        if ($cfgRow) {
            $cfg = json_decode((string) $cfgRow['state_value'], true);
            if (is_array($cfg)) {
                foreach ($cfg as $entry) {
                    if (is_array($entry) && isset($entry['touch'], $entry['default_intent']) && (int) $entry['touch'] === $touchNumber) {
                        $intent = (string) $entry['default_intent'];
                        break;
                    }
                }
            }
        }
    }
    if ($intent === null || trim($intent) === '') {
        $intent = 'gentle bump'; // last-resort fallback
    }

    // Total touches in the configured sequence (for "touch N of M" context to Gemini)
    $totalStmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'");
    $totalStmt->execute();
    $totalRow = $totalStmt->fetch();
    $totalTouches = 1;
    if ($totalRow) {
        $cfg = json_decode((string) $totalRow['state_value'], true);
        if (is_array($cfg)) {
            $totalTouches = count($cfg) + 1; // +1 because touch 1 isn't in the config
        }
    }

    $bizName = trim((string) ($lead['business_name'] ?? ''));
    $bizSummary = trim((string) ($lead['business_summary'] ?? ''));
    $category = trim((string) ($lead['category'] ?? ''));
    $origSubject = trim((string) ($lead['draft_subject'] ?? ''));
    $origBody = trim((string) ($lead['draft_body'] ?? ''));

    $prompt = "You are writing a follow-up email in an ongoing cold-outreach sequence.\n\n"
        . "Context:\n"
        . "- This is touch $touchNumber of " . ($totalTouches) . " in the sequence.\n"
        . "- The recipient has not replied to any prior touch.\n"
        . "- Recipient business: " . ($bizName !== '' ? $bizName : '(unknown)') . "\n"
        . ($category !== '' ? "- Category: $category\n" : '')
        . ($bizSummary !== '' ? "- About them: $bizSummary\n" : '')
        . "\n"
        . "Original first-touch subject: $origSubject\n"
        . "Original first-touch body:\n---\n$origBody\n---\n"
        . "\n"
        . "Intent for THIS follow-up: $intent\n"
        . "\n"
        . "Write a brief follow-up email (max ~120 words) that:\n"
        . "- Sounds like a continuation of the original, not a fresh pitch.\n"
        . "- Reflects the intent above (do not literally quote the intent text).\n"
        . "- Is from Evan at Argo Books (free accounting software for small Canadian businesses).\n"
        . "- Includes 'argorobots.com' once, bare URL (not formatted).\n"
        . "- Mentions the unsubscribe option once with the placeholder {UNSUBSCRIBE_URL} (no other text around the placeholder).\n"
        . "- Signs off 'All the best,\\nEvan\\nArgo Books'.\n"
        . "\n"
        . "Output as JSON exactly: {\"subject\": \"...\", \"body\": \"...\"}\n"
        . "The subject should NOT include 'Re:' — that prefix is added automatically.";

    $geminiResult = call_gemini($prompt);
    if (!is_string($geminiResult) || trim($geminiResult) === '') {
        $pdo->prepare("UPDATE outreach_followups SET draft_attempts = draft_attempts + 1, status = CASE WHEN draft_attempts + 1 >= 3 THEN 'failed' ELSE status END WHERE id = ?")
            ->execute([$followupId]);
        return false;
    }

    // Strip any markdown fences Gemini sometimes wraps JSON in
    $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($geminiResult));
    $parsed = json_decode($cleaned, true);
    if (!is_array($parsed) || !isset($parsed['subject'], $parsed['body'])) {
        $pdo->prepare("UPDATE outreach_followups SET draft_attempts = draft_attempts + 1, status = CASE WHEN draft_attempts + 1 >= 3 THEN 'failed' ELSE status END WHERE id = ?")
            ->execute([$followupId]);
        return false;
    }

    $subject = trim((string) $parsed['subject']);
    $body = trim((string) $parsed['body']);

    // Always Re:-prefix the subject (idempotent)
    if (stripos($subject, 're:') !== 0) {
        $subject = 'Re: ' . ($origSubject !== '' ? $origSubject : $subject);
    }

    $pdo->prepare(
        "UPDATE outreach_followups
         SET draft_subject = ?, draft_body = ?, drafted_at = NOW(), status = 'drafted', draft_attempts = draft_attempts + 1
         WHERE id = ?"
    )->execute([$subject, $body, $followupId]);

    return true;
}
```

- [ ] **Step 2: Sanity check the function loads without syntax errors**

Run:

```bash
php -l cron/lib/outreach_helpers.php
```

Expected: `No syntax errors detected`.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/lib/outreach_helpers.php
git commit -m "feat(outreach): add Gemini follow-up draft generator"
```

---

## Task 4: Helper — bulk halt logic

**Files:**
- Modify: `cron/lib/outreach_helpers.php` (add after `draft_followup_via_gemini`)

**Purpose:** Two helpers — `halt_followups_for_lead()` (used by Skip/Halt admin actions) and `halt_followups_bulk()` (called by `stepHaltFollowups` to mark rows halted for leads whose status flipped to a stop-condition or whose email is suppressed).

- [ ] **Step 1: Add the helpers**

Append:

```php
/**
 * Halt all pre-sent follow-ups for a single lead. Used by admin actions
 * (manual halt from the UI). Returns count of rows updated.
 */
function halt_followups_for_lead($pdo, int $leadId, string $haltReason = 'manual'): int
{
    $stmt = $pdo->prepare(
        "UPDATE outreach_followups
         SET status = 'halted', halt_reason = ?
         WHERE lead_id = ?
           AND status IN ('scheduled', 'drafted', 'approved')"
    );
    $stmt->execute([$haltReason, $leadId]);
    return $stmt->rowCount();
}

/**
 * Bulk halt: mark pre-sent follow-up rows as halted for any lead in a
 * stop-condition status, or whose email is in email_suppressions (context='outreach').
 *
 * Called from stepHaltFollowups on each cron tick. Idempotent — only touches
 * rows still in scheduled/drafted/approved state.
 *
 * Returns an associative array of halt_reason => count for logging.
 */
function halt_followups_bulk($pdo): array
{
    $counts = [
        'replied' => 0,
        'unsubscribed' => 0,
        'bounced' => 0,
    ];

    // 1) Leads in stop-condition status
    $stopStatuses = ['replied', 'interested', 'not_interested', 'onboarded', 'email_bounced'];
    $placeholders = implode(',', array_fill(0, count($stopStatuses), '?'));
    $stmt = $pdo->prepare(
        "UPDATE outreach_followups f
         JOIN outreach_leads l ON l.id = f.lead_id
         SET f.status = 'halted',
             f.halt_reason = CASE l.status
                 WHEN 'email_bounced' THEN 'bounced'
                 WHEN 'replied' THEN 'replied'
                 WHEN 'interested' THEN 'replied'
                 WHEN 'onboarded' THEN 'replied'
                 ELSE 'replied'
             END
         WHERE f.status IN ('scheduled', 'drafted', 'approved')
           AND l.status IN ($placeholders)"
    );
    $stmt->execute($stopStatuses);
    $statusHalted = $stmt->rowCount();

    // 2) Leads whose email is in the outreach suppression list (unsubscribed)
    $suppStmt = $pdo->prepare(
        "UPDATE outreach_followups f
         JOIN outreach_leads l ON l.id = f.lead_id
         JOIN email_suppressions s ON LOWER(s.email) = LOWER(l.email) AND s.context = 'outreach'
         SET f.status = 'halted', f.halt_reason = 'unsubscribed'
         WHERE f.status IN ('scheduled', 'drafted', 'approved')"
    );
    $suppStmt->execute();
    $unsubHalted = $suppStmt->rowCount();

    $counts['replied'] = max(0, $statusHalted - $unsubHalted); // rough split for logging
    $counts['unsubscribed'] = $unsubHalted;

    return $counts;
}
```

- [ ] **Step 2: Sanity-check syntax**

Run:

```bash
php -l cron/lib/outreach_helpers.php
```

Expected: `No syntax errors detected`.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/lib/outreach_helpers.php
git commit -m "feat(outreach): add halt helpers for follow-up sequences"
```

---

## Task 5: Helper — `send_followup_row()` (replaces `send_outreach_followup`)

**Files:**
- Modify: `cron/lib/outreach_helpers.php` — delete old `send_outreach_followup` (current lines 431-520), add new `send_followup_row`

**Purpose:** Sends a single approved follow-up row. Atomic-claims by transitioning `approved → sent`, builds threading headers using the previous touch's `message_id` (so each touch threads under the previous, not just the original), renders the body identically to first-touch HTML output, calls `send_styled_email`.

- [ ] **Step 1: Delete the old `send_outreach_followup` function**

Open `cron/lib/outreach_helpers.php`, find the function `send_outreach_followup` (starts at line 431, ends ~line 520). Delete the entire function including its docblock. Don't leave a stub.

- [ ] **Step 2: Add the new `send_followup_row` function**

In the same location:

```php
/**
 * Send a single approved follow-up row. Atomic-claims the row by flipping
 * status=approved → status=sent. Threading headers point at the PREVIOUS
 * touch's message_id (or the original first-touch's message_id if this is
 * touch 2) so the whole sequence stays in one inbox thread.
 *
 * Returns true on send success. On failure, releases the atomic claim
 * (status returns to 'approved') so the next cron tick retries.
 *
 * &$reason out-param: 'sent' | 'not_eligible' | 'invalid_email' | 'smtp_failed' | 'lead_missing'
 */
function send_followup_row($pdo, array $followupRow, ?string &$reason = null): bool
{
    $followupId = (int) $followupRow['id'];
    $leadId = (int) $followupRow['lead_id'];
    $touchNumber = (int) $followupRow['touch_number'];

    // Fetch lead for email + unsubscribe token + first-touch context
    $leadStmt = $pdo->prepare("SELECT email, business_name, unsubscribe_token, original_message_id FROM outreach_leads WHERE id = ?");
    $leadStmt->execute([$leadId]);
    $lead = $leadStmt->fetch();
    if (!$lead) {
        $reason = 'lead_missing';
        return false;
    }

    $email = trim((string) $lead['email']);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pdo->prepare("UPDATE outreach_followups SET status = 'failed', halt_reason = 'invalid_email' WHERE id = ?")
            ->execute([$followupId]);
        $reason = 'invalid_email';
        return false;
    }

    // Atomic claim — only one process can flip approved → sent
    $claim = $pdo->prepare(
        "UPDATE outreach_followups
         SET status = 'sent', sent_at = NOW()
         WHERE id = ? AND status = 'approved'"
    );
    $claim->execute([$followupId]);
    if ($claim->rowCount() === 0) {
        $reason = 'not_eligible';
        return false; // race-lost, already sent, or halted between fetch and claim
    }

    // Ensure unsubscribe token exists
    $unsubToken = $lead['unsubscribe_token'] ?? null;
    if (empty($unsubToken)) {
        $unsubToken = bin2hex(random_bytes(32));
        $pdo->prepare("UPDATE outreach_leads SET unsubscribe_token = ? WHERE id = ?")
            ->execute([$unsubToken, $leadId]);
    }
    $unsubUrl = 'https://argorobots.com/unsubscribe?t=' . $unsubToken;

    // Threading: previous touch's message_id, or first-touch's if this is touch 2
    $prevMessageId = null;
    if ($touchNumber > 2) {
        $prevStmt = $pdo->prepare("SELECT message_id FROM outreach_followups WHERE lead_id = ? AND touch_number = ? AND status = 'sent'");
        $prevStmt->execute([$leadId, $touchNumber - 1]);
        $prevRow = $prevStmt->fetch();
        if ($prevRow && !empty($prevRow['message_id'])) {
            $prevMessageId = (string) $prevRow['message_id'];
        }
    }
    if ($prevMessageId === null) {
        // Fall back to first-touch's message_id
        $prevMessageId = trim((string) ($lead['original_message_id'] ?? ''));
        if ($prevMessageId === '') $prevMessageId = null;
    }
    $threadingHeaders = [];
    if ($prevMessageId !== null) {
        $threadingHeaders['In-Reply-To'] = $prevMessageId;
        $threadingHeaders['References']  = $prevMessageId;
    }

    // Build tracked URL + render body HTML (same pattern as send_outreach_lead)
    $trackedUrl = 'https://argorobots.com/?source=outreach-' . $leadId . '-fu' . $touchNumber;
    $anchorHtml = '<a href="' . htmlspecialchars($trackedUrl) . '" style="color:#3b82f6;text-decoration:underline">argorobots.com</a>';
    $unsubAnchor = '<a href="' . htmlspecialchars($unsubUrl) . '" style="color:#6b7280;text-decoration:underline">unsubscribe</a>';

    $rawBody = (string) ($followupRow['draft_body'] ?? '');
    $rawBody = str_replace('{UNSUBSCRIBE_URL}', $unsubUrl, $rawBody);

    $escaped = htmlspecialchars($rawBody);
    $escaped = preg_replace('#https?://argorobots\.com/?(?![\w?/])#', $anchorHtml, $escaped);
    $escaped = preg_replace('#https?://argorobots\.com/unsubscribe\?t=[a-f0-9]+#i', $unsubAnchor, $escaped);

    if (strpos($escaped, 'unsubscribe?t=') === false) {
        $unsubLine = "\n\n<span style=\"color:#9ca3af;font-size:13px\">Not interested? " . $unsubAnchor . " and I'll stop emailing you.</span>";
        $escaped .= $unsubLine;
    }

    $finalBody = '<p>' . nl2br($escaped) . '</p>';

    $messageId = null;
    $result = send_styled_email(
        $email,
        (string) ($followupRow['draft_subject'] ?? 'Following up'),
        $finalBody,
        '',
        'contact@argorobots.com',
        'Evan',
        'contact@argorobots.com',
        $threadingHeaders,
        'Quick bump on my last note',
        'html',
        $messageId
    );

    if ($result) {
        // Record the message_id so the NEXT touch can thread off it
        $pdo->prepare("UPDATE outreach_followups SET message_id = ? WHERE id = ?")
            ->execute([$messageId, $followupId]);

        // Update lead-level activity timestamp + counters for backward-compat
        $pdo->prepare(
            "UPDATE outreach_leads
             SET last_contact_date = NOW(),
                 last_followup_at = NOW(),
                 followup_count = followup_count + 1
             WHERE id = ?"
        )->execute([$leadId]);

        log_activity($pdo, $leadId, 'followup_sent', "Follow-up #$touchNumber delivered");
        $reason = 'sent';
        return true;
    }

    // SMTP failed — release the claim
    $pdo->prepare("UPDATE outreach_followups SET status = 'approved', sent_at = NULL WHERE id = ?")
        ->execute([$followupId]);
    $reason = 'smtp_failed';
    return false;
}
```

- [ ] **Step 3: Verify the old function is gone and the new one is present**

Run:

```bash
grep -n "function send_outreach_followup\b" cron/lib/outreach_helpers.php
grep -n "function send_followup_row\b" cron/lib/outreach_helpers.php
php -l cron/lib/outreach_helpers.php
```

Expected: First grep returns nothing. Second grep returns the line of the new function. `php -l` reports no syntax errors.

- [ ] **Step 4: Stage and commit (user runs when ready)**

```bash
git add cron/lib/outreach_helpers.php
git commit -m "feat(outreach): replace send_outreach_followup with send_followup_row"
```

---

## Task 6: AB helpers — add `followup_sequence` to types, rotation, stats

**Files:**
- Modify: `cron/lib/ab_helpers.php`

**Purpose:** Wire the new variant_type into the existing A/B framework so it appears in rotation, stats loading branches correctly, and validation knows the JSON shape.

- [ ] **Step 1: Find `ab_known_variant_types()` and add `followup_sequence`**

Locate the function `ab_known_variant_types()` in `cron/lib/ab_helpers.php`. Add `'followup_sequence'` to its return array. Show the current line first:

```bash
grep -n "function ab_known_variant_types" cron/lib/ab_helpers.php
```

Edit the returned array to include `'followup_sequence'`. Example diff (your exact current array may differ):

Before:
```php
return ['subject', 'body', 'sender', 'cta', 'preheader', 'format', 'personalization'];
```

After:
```php
return ['subject', 'body', 'sender', 'cta', 'preheader', 'format', 'personalization', 'followup_sequence'];
```

- [ ] **Step 2: Find `ab_auto_rotation_order()` and append `followup_sequence`**

Locate `ab_auto_rotation_order()`. Append `'followup_sequence'` after `'personalization'` so the rotation cycles to it last. Example:

Before:
```php
return ['subject', 'sender', 'format', 'personalization'];
```

After:
```php
return ['subject', 'sender', 'format', 'personalization', 'followup_sequence'];
```

- [ ] **Step 3: Update `load_variants_with_stats()` to handle followup_sequence sent counts**

In `load_variants_with_stats()`, the current logic queries `outreach_leads.ab_variant_id` for `sent_count`. For `followup_sequence` variants we want the same — the count of *leads* assigned to the variant whose first-touch went out — which is exactly `outreach_leads WHERE ab_variant_id = v.id AND sent_at IS NOT NULL`. The existing query already does this.

The reply_count and clicked_count queries also work as-is because they're lead-level. **No change needed** — but add a comment after the existing queries:

```php
        // (followup_sequence variants use the same lead-level counts: the unit
        // of randomization is the lead, and reply/click attribution is at the
        // lead level regardless of which touch produced the response.)
```

- [ ] **Step 4: Add a JSON content validator for `followup_sequence` variants**

Append this new function to `cron/lib/ab_helpers.php`:

```php
/**
 * Validate that a followup_sequence variant's JSON content has the right
 * shape: an array of {touch, intent} entries, one per configured touch,
 * with touch numbers matching the active followup_sequence_config.
 *
 * Returns ['valid' => bool, 'reason' => string|null].
 */
function validate_followup_sequence_content(string $contentJson, array $configTouchNumbers): array
{
    $parsed = json_decode($contentJson, true);
    if (!is_array($parsed)) {
        return ['valid' => false, 'reason' => 'content is not valid JSON array'];
    }

    $variantTouches = [];
    foreach ($parsed as $entry) {
        if (!is_array($entry) || !isset($entry['touch'], $entry['intent'])) {
            return ['valid' => false, 'reason' => 'each entry must have "touch" and "intent" keys'];
        }
        $touch = (int) $entry['touch'];
        $intent = trim((string) $entry['intent']);
        if ($intent === '') {
            return ['valid' => false, 'reason' => "touch $touch has empty intent"];
        }
        if (strlen($intent) > 200) {
            return ['valid' => false, 'reason' => "touch $touch intent exceeds 200 chars"];
        }
        $variantTouches[] = $touch;
    }

    sort($variantTouches);
    sort($configTouchNumbers);
    if ($variantTouches !== $configTouchNumbers) {
        return [
            'valid' => false,
            'reason' => 'variant touch list [' . implode(',', $variantTouches) . '] does not match config [' . implode(',', $configTouchNumbers) . ']',
        ];
    }

    return ['valid' => true, 'reason' => null];
}
```

- [ ] **Step 5: Sanity-check syntax**

Run:

```bash
php -l cron/lib/ab_helpers.php
```

Expected: no syntax errors.

- [ ] **Step 6: Stage and commit (user runs when ready)**

```bash
git add cron/lib/ab_helpers.php
git commit -m "feat(outreach): wire followup_sequence into A/B framework"
```

---

## Task 7: AB — seed starter variants + auto-cycle uses seed pool

**Files:**
- Modify: `cron/lib/ab_helpers.php`

**Purpose:** Define the three starter sequence variants (Bump-Reframe-Close, Value-Question-Close, Persistent Bump) as a constant, add an idempotent seeding function `ensure_followup_starter_test()`, and update `ab_start_new_cycle()` to reuse the seed pool when creating a new `followup_sequence` cycle.

- [ ] **Step 1: Add the seed variants constant**

Near the top of `cron/lib/ab_helpers.php` (after the existing constants/loader guard):

```php
/**
 * Starter variants for the followup_sequence A/B type. Each variant's
 * intent array assumes the default 3-touch sequence config (touches 2, 3, 4).
 * If the admin has changed the touch count, the seeding function will
 * project these to match — for now just keep them aligned with the
 * shipped default config.
 */
const FOLLOWUP_SEQUENCE_SEED_VARIANTS = [
    [
        'label' => 'Bump-Reframe-Close',
        'intents' => [
            ['touch' => 2, 'intent' => 'gentle bump'],
            ['touch' => 3, 'intent' => 'different angle, offer concrete example'],
            ['touch' => 4, 'intent' => 'final note before closing'],
        ],
    ],
    [
        'label' => 'Value-Question-Close',
        'intents' => [
            ['touch' => 2, 'intent' => 'helpful tip relevant to their business'],
            ['touch' => 3, 'intent' => 'open-ended question about their pain point'],
            ['touch' => 4, 'intent' => 'final note before closing'],
        ],
    ],
    [
        'label' => 'Persistent Bump',
        'intents' => [
            ['touch' => 2, 'intent' => 'gentle bump'],
            ['touch' => 3, 'intent' => 'gentle bump, slightly different wording'],
            ['touch' => 4, 'intent' => 'gentle bump, one more time'],
        ],
    ],
];
```

- [ ] **Step 2: Add `ensure_followup_starter_test()` (idempotent seeder)**

Append:

```php
/**
 * Idempotent: creates the followup_sequence starter A/B test in 'draft'
 * status with the three seed variants attached, if no followup_sequence
 * test of any status exists yet. Admin reviews and activates from the
 * A/B Tests tab.
 *
 * Variant intents are filtered to only the touches present in the current
 * followup_sequence_config — so if admin has 2 touches configured instead
 * of the default 3, only those touches' intents make it onto the variants.
 *
 * Returns the test_id if created, or null if a test already exists.
 */
function ensure_followup_starter_test($pdo): ?int
{
    // Skip if any followup_sequence test exists already
    $existing = $pdo->query("SELECT id FROM outreach_ab_tests WHERE variant_type = 'followup_sequence' LIMIT 1")->fetchColumn();
    if ($existing) return null;

    // Read current config to know which touches are active
    $cfgRow = $pdo->query("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'")->fetch();
    if (!$cfgRow) return null;
    $cfg = json_decode((string) $cfgRow['state_value'], true);
    if (!is_array($cfg) || empty($cfg)) return null;

    $configTouches = array_map(fn($e) => (int) $e['touch'], array_filter($cfg, fn($e) => is_array($e) && isset($e['touch'])));
    if (empty($configTouches)) return null;

    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO outreach_ab_tests (name, variant_type, status, notes) VALUES (?, 'followup_sequence', 'draft', ?)")
            ->execute([
                'Starter follow-up sequences',
                'Auto-seeded on first deploy. Three baseline strategies to test against each other. Activate from this tab when ready.',
            ]);
        $testId = (int) $pdo->lastInsertId();

        $varStmt = $pdo->prepare("INSERT INTO outreach_ab_variants (test_id, label, content, is_default) VALUES (?, ?, ?, ?)");
        foreach (FOLLOWUP_SEQUENCE_SEED_VARIANTS as $i => $variant) {
            // Filter intents to only touches in the current config
            $filteredIntents = array_values(array_filter($variant['intents'], fn($it) => in_array((int) $it['touch'], $configTouches, true)));
            // If the config has touches the seed doesn't cover (e.g. admin added touch 5), fill with the variant's last intent
            $coveredTouches = array_map(fn($it) => (int) $it['touch'], $filteredIntents);
            foreach ($configTouches as $t) {
                if (!in_array($t, $coveredTouches, true) && !empty($filteredIntents)) {
                    $filteredIntents[] = ['touch' => $t, 'intent' => $filteredIntents[count($filteredIntents) - 1]['intent']];
                }
            }
            usort($filteredIntents, fn($a, $b) => $a['touch'] - $b['touch']);
            $varStmt->execute([
                $testId,
                $variant['label'],
                json_encode($filteredIntents, JSON_UNESCAPED_SLASHES),
                $i === 0 ? 1 : 0,
            ]);
        }

        $pdo->commit();
        return $testId;
    } catch (Throwable $e) {
        $pdo->rollBack();
        return null;
    }
}
```

- [ ] **Step 3: Update `ab_start_new_cycle()` to reuse seed variants for `followup_sequence`**

Find `ab_start_new_cycle()` in `cron/lib/ab_helpers.php`. Locate the branch where it dispatches by `variant_type` to generate variants. Add a new branch (before the catch-all error) that handles `followup_sequence` by creating a new test from the seed variants (same pattern as `ensure_followup_starter_test` but with `status='active'` and a rotating subset of 2-3 seeds):

```php
    if ($cycleType === 'followup_sequence') {
        $cfgRow = $pdo->query("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'")->fetch();
        if (!$cfgRow) {
            return ['action' => 'error', 'error' => 'followup_sequence_config not set — cannot start cycle'];
        }
        $cfg = json_decode((string) $cfgRow['state_value'], true);
        if (!is_array($cfg) || empty($cfg)) {
            return ['action' => 'error', 'error' => 'followup_sequence_config is empty'];
        }
        $configTouches = array_map(fn($e) => (int) $e['touch'], array_filter($cfg, fn($e) => is_array($e) && isset($e['touch'])));

        $pdo->beginTransaction();
        try {
            $testName = 'Auto-cycle followup_sequence ' . date('Y-m-d H:i');
            $pdo->prepare("INSERT INTO outreach_ab_tests (name, variant_type, status, started_at) VALUES (?, 'followup_sequence', 'active', NOW())")
                ->execute([$testName]);
            $testId = (int) $pdo->lastInsertId();

            $varStmt = $pdo->prepare("INSERT INTO outreach_ab_variants (test_id, label, content, is_default) VALUES (?, ?, ?, ?)");
            foreach (FOLLOWUP_SEQUENCE_SEED_VARIANTS as $i => $variant) {
                $filtered = array_values(array_filter($variant['intents'], fn($it) => in_array((int) $it['touch'], $configTouches, true)));
                $coveredTouches = array_map(fn($it) => (int) $it['touch'], $filtered);
                foreach ($configTouches as $t) {
                    if (!in_array($t, $coveredTouches, true) && !empty($filtered)) {
                        $filtered[] = ['touch' => $t, 'intent' => $filtered[count($filtered) - 1]['intent']];
                    }
                }
                usort($filtered, fn($a, $b) => $a['touch'] - $b['touch']);
                $varStmt->execute([$testId, $variant['label'], json_encode($filtered, JSON_UNESCAPED_SLASHES), $i === 0 ? 1 : 0]);
            }

            $pdo->commit();
            return [
                'action' => 'created',
                'test_id' => $testId,
                'test_name' => $testName,
                'variant_count' => count(FOLLOWUP_SEQUENCE_SEED_VARIANTS),
                'source' => 'seed pool',
                'carried_winner' => false,
            ];
        } catch (Throwable $e) {
            $pdo->rollBack();
            return ['action' => 'error', 'error' => 'DB error: ' . $e->getMessage()];
        }
    }
```

If the existing function returns its dispatched result differently than this, adapt the return shape to match. Read the function fully before editing.

- [ ] **Step 4: Sanity check syntax**

```bash
php -l cron/lib/ab_helpers.php
```

Expected: no syntax errors.

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add cron/lib/ab_helpers.php
git commit -m "feat(outreach): seed and auto-cycle followup_sequence A/B variants"
```

---

## Task 8: AB — auto-pause on sequence shape mismatch

**Files:**
- Modify: `cron/lib/ab_helpers.php`

**Purpose:** Add a function that checks whether the currently-active `followup_sequence` test's variant intents still match the current sequence config. If not, auto-pause the test and persist the reason. Called from the pipeline's `stepManageAbTests`.

- [ ] **Step 1: Add the check function**

Append to `cron/lib/ab_helpers.php`:

```php
/**
 * Checks the active followup_sequence A/B test (if any) against the current
 * followup_sequence_config. If any variant's touch list doesn't match the
 * config's touch list, pauses the test and writes ab_auto_last_pause_reason
 * to state for surfacing in the admin UI.
 *
 * Returns ['action' => 'ok'|'paused'|'no_active'|'no_config', 'reason' => ?string].
 */
function check_followup_sequence_shape_match($pdo): array
{
    $test = $pdo->query("SELECT id, name FROM outreach_ab_tests WHERE variant_type = 'followup_sequence' AND status = 'active' LIMIT 1")->fetch();
    if (!$test) {
        return ['action' => 'no_active', 'reason' => null];
    }

    $cfgRow = $pdo->query("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config'")->fetch();
    if (!$cfgRow) {
        return ['action' => 'no_config', 'reason' => null];
    }
    $cfg = json_decode((string) $cfgRow['state_value'], true);
    if (!is_array($cfg)) {
        return ['action' => 'no_config', 'reason' => null];
    }
    $configTouches = array_map(fn($e) => (int) $e['touch'], array_filter($cfg, fn($e) => is_array($e) && isset($e['touch'])));
    sort($configTouches);

    $variants = $pdo->prepare("SELECT id, label, content FROM outreach_ab_variants WHERE test_id = ?");
    $variants->execute([(int) $test['id']]);
    foreach ($variants->fetchAll() as $v) {
        $validation = validate_followup_sequence_content((string) $v['content'], $configTouches);
        if (!$validation['valid']) {
            // Pause the test
            $pdo->prepare("UPDATE outreach_ab_tests SET status = 'paused' WHERE id = ?")
                ->execute([(int) $test['id']]);
            $reason = sprintf(
                "followup_sequence test #%d '%s' auto-paused — variant '%s' shape mismatch: %s",
                (int) $test['id'],
                (string) $test['name'],
                (string) $v['label'],
                (string) $validation['reason']
            );
            $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('ab_auto_last_pause_reason', ?) ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
                ->execute([$reason]);
            return ['action' => 'paused', 'reason' => $reason];
        }
    }

    return ['action' => 'ok', 'reason' => null];
}
```

- [ ] **Step 2: Sanity-check syntax**

```bash
php -l cron/lib/ab_helpers.php
```

Expected: no syntax errors.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/lib/ab_helpers.php
git commit -m "feat(outreach): auto-pause followup_sequence tests on shape mismatch"
```

---

## Task 9: Pipeline — modify `stepSendEmails` to schedule follow-ups after each first-touch send

**Files:**
- Modify: `cron/outreach_pipeline.php` (in `stepSendEmails`, around the `send_outreach_lead` success branch at line 747)

**Purpose:** After each successful first-touch send, call `schedule_followups_for_lead()` so the follow-up rows exist as soon as the first touch goes out.

- [ ] **Step 1: Locate the success branch**

Find this block in `cron/outreach_pipeline.php` (around line 747):

```php
            $reason = null;
            if (send_outreach_lead($pdo, $lead, $reason)) {
                $variantTag = !empty($lead['ab_variant_id'])
                    ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
                    : '';
                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email . $variantTag);
                logPipeline("Sent email to $businessName <$email> (lead #$id)" . $variantTag);
                $successCount++;
```

- [ ] **Step 2: Add the schedule call after the existing log line**

Replace the block above with:

```php
            $reason = null;
            if (send_outreach_lead($pdo, $lead, $reason)) {
                $variantTag = !empty($lead['ab_variant_id'])
                    ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
                    : '';
                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email . $variantTag);
                logPipeline("Sent email to $businessName <$email> (lead #$id)" . $variantTag);

                // Schedule the multi-touch follow-up sequence (if any configured).
                // For the followup_sequence A/B assignment, we look up the active
                // followup_sequence test and pick a variant for this lead now —
                // separate from any A/B variant the first-touch is on (which is
                // usually subject/sender/format/etc., not followup_sequence).
                $fuVariantId = null;
                $fuTestId = null;
                $fuActive = $pdo->query("SELECT id FROM outreach_ab_tests WHERE variant_type = 'followup_sequence' AND status = 'active' LIMIT 1")->fetch();
                if ($fuActive) {
                    $fuTestId = (int) $fuActive['id'];
                    $fuVariants = $pdo->prepare("SELECT * FROM outreach_ab_variants WHERE test_id = ?");
                    $fuVariants->execute([$fuTestId]);
                    $fuVariantList = $fuVariants->fetchAll();
                    if (count($fuVariantList) >= 2) {
                        $picked = pick_ab_variant($pdo, ['id' => $fuTestId], $fuVariantList, $lead);
                        if ($picked) {
                            $fuVariantId = (int) $picked['id'];
                        }
                    }
                }
                $scheduled = schedule_followups_for_lead($pdo, $id, $fuTestId, $fuVariantId);
                if ($scheduled > 0) {
                    logPipeline("Scheduled $scheduled follow-up(s) for lead #$id" . ($fuVariantId ? " [followup A/B variant #$fuVariantId]" : ''));
                }

                $successCount++;
```

- [ ] **Step 3: Sanity check syntax**

```bash
php -l cron/outreach_pipeline.php
```

Expected: no syntax errors.

- [ ] **Step 4: Stage and commit (user runs when ready)**

```bash
git add cron/outreach_pipeline.php
git commit -m "feat(outreach): schedule follow-ups after first-touch send"
```

---

## Task 10: Pipeline — new `stepHaltFollowups`

**Files:**
- Modify: `cron/outreach_pipeline.php`

**Purpose:** Add a new pipeline step that bulk-halts pre-sent follow-ups for leads in a stop-condition state or with suppressed emails. Runs between `stepSendEmails` (step 5) and the new draft step.

- [ ] **Step 1: Add the function near the other `stepXxx` functions**

Append after the existing `stepSendFollowups` function (or near it — placement is logical, not strict):

```php
function stepHaltFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 5.5: Halt Follow-ups ---');

    if ($dryRun) {
        // Count how many WOULD be halted, but don't write
        $countStmt = $pdo->query(
            "SELECT COUNT(*) FROM outreach_followups f
             JOIN outreach_leads l ON l.id = f.lead_id
             WHERE f.status IN ('scheduled','drafted','approved')
               AND (
                   l.status IN ('replied','interested','not_interested','onboarded','email_bounced')
                   OR EXISTS (SELECT 1 FROM email_suppressions s WHERE LOWER(s.email) = LOWER(l.email) AND s.context = 'outreach')
               )"
        );
        $count = (int) $countStmt->fetchColumn();
        logPipeline("[DRY RUN] Would halt $count follow-up row(s).");
        return;
    }

    $counts = halt_followups_bulk($pdo);
    $total = array_sum($counts);
    if ($total === 0) {
        logPipeline('No follow-ups halted.');
    } else {
        logPipeline("Halted $total follow-up(s): " . json_encode($counts));
    }
}
```

- [ ] **Step 2: Wire `stepHaltFollowups` into the main pipeline flow**

In the main try block of the pipeline (around line 220), insert this call AFTER `stepSendEmails` and BEFORE the existing follow-up step. Find:

```php
    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    // ─── STEP 6: Send Follow-ups ───
```

Change to:

```php
    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    // ─── STEP 5.5: Halt Follow-ups (replies / unsubscribes / bounces) ───
    if ($runAll || $sendOnly) {
        stepHaltFollowups($pdo, $dryRun);
    }

    // ─── STEP 6: Send Follow-ups ───
```

- [ ] **Step 3: Sanity-check syntax**

```bash
php -l cron/outreach_pipeline.php
```

Expected: no syntax errors.

- [ ] **Step 4: Stage and commit (user runs when ready)**

```bash
git add cron/outreach_pipeline.php
git commit -m "feat(outreach): add stepHaltFollowups pipeline step"
```

---

## Task 11: Pipeline — new `stepDraftFollowups`

**Files:**
- Modify: `cron/outreach_pipeline.php`

**Purpose:** New step that picks up `outreach_followups WHERE status='scheduled' AND scheduled_for <= NOW() + 1 day`, calls `draft_followup_via_gemini` for each, respects a daily Gemini cap, and in Auto-send mode immediately advances drafted rows to `approved`.

- [ ] **Step 1: Add the constant for the daily draft cap**

Near the top of `cron/outreach_pipeline.php` (with the existing `define('DAILY_SEND_LIMIT', ...)` block around line 54):

```php
define('DAILY_DRAFT_LIMIT', (int) ($_ENV['OUTREACH_DAILY_DRAFT_LIMIT'] ?? 100));
```

- [ ] **Step 2: Add the new function**

Append near the other `stepXxx` functions:

```php
function stepDraftFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 5.6: Draft Follow-ups ---');

    $geminiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    if (empty($geminiKey)) {
        logPipeline('Gemini API key not configured. Skipping follow-up draft generation.', 'WARN');
        return;
    }

    // Find rows whose draft window has opened (scheduled_for within next 24h)
    $stmt = $pdo->prepare(
        "SELECT * FROM outreach_followups
         WHERE status = 'scheduled'
           AND scheduled_for <= DATE_ADD(NOW(), INTERVAL 1 DAY)
         ORDER BY scheduled_for ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, DAILY_DRAFT_LIMIT, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        logPipeline('No follow-ups need drafts. Skipping.');
        return;
    }

    logPipeline('Found ' . count($rows) . ' follow-up(s) needing AI drafts (cap: ' . DAILY_DRAFT_LIMIT . ').');

    if ($dryRun) {
        foreach ($rows as $r) {
            logPipeline("[DRY RUN] Would draft followup #{$r['id']} (lead #{$r['lead_id']}, touch {$r['touch_number']})");
        }
        return;
    }

    $autoSendMode = (function() use ($pdo) {
        $r = $pdo->query("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'auto_send_mode'")->fetch();
        return $r ? $r['state_value'] : 'auto';
    })();

    $success = 0;
    $failed = 0;
    foreach ($rows as $row) {
        try {
            $ok = draft_followup_via_gemini($pdo, $row);
            if ($ok) {
                $success++;
                // In auto-send mode, advance drafted → approved immediately
                if ($autoSendMode === 'auto') {
                    $pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE id = ? AND status = 'drafted'")
                        ->execute([(int) $row['id']]);
                }
            } else {
                $failed++;
            }
            sleep(1); // Rate-limit Gemini calls
        } catch (Throwable $e) {
            logPipeline("Draft followup error (followup #{$row['id']}): " . $e->getMessage(), 'ERROR');
            $failed++;
        }
    }

    logPipeline("Follow-up drafts: $success generated, $failed failed.");
}
```

- [ ] **Step 3: Wire `stepDraftFollowups` into the main pipeline**

In the main try block, insert AFTER `stepHaltFollowups` (which was added in Task 10) and BEFORE the existing `stepSendFollowups` call. The block should now read:

```php
    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    // ─── STEP 5.5: Halt Follow-ups ───
    if ($runAll || $sendOnly) {
        stepHaltFollowups($pdo, $dryRun);
    }

    // ─── STEP 5.6: Draft Follow-ups (Gemini, lazy ~1 day before send) ───
    // Always runs regardless of send mode — Drafting itself is harmless.
    // The review-vs-auto gating happens INSIDE stepDraftFollowups (which
    // advances drafted → approved only when auto_send_mode = 'auto').
    if ($runAll || $sendOnly || $draftOnly) {
        stepDraftFollowups($pdo, $dryRun);
    }

    // ─── STEP 6: Send Follow-ups ───
```

- [ ] **Step 4: Sanity-check syntax**

```bash
php -l cron/outreach_pipeline.php
```

Expected: no syntax errors.

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add cron/outreach_pipeline.php
git commit -m "feat(outreach): add stepDraftFollowups using Gemini personalization"
```

---

## Task 12: Pipeline — rewrite `stepSendFollowups` against new table

**Files:**
- Modify: `cron/outreach_pipeline.php` — replace the existing `stepSendFollowups` (lines 780-856) body

**Purpose:** Replace the old query (which read from `outreach_leads`) with one against `outreach_followups WHERE status='approved' AND scheduled_for <= NOW()`. Call `send_followup_row()` for each. Keep the daily cap (raised to 75 by default per spec).

- [ ] **Step 1: Raise the default for `DAILY_FOLLOWUP_LIMIT`**

Find this line in `cron/outreach_pipeline.php` (around line 59):

```php
define('DAILY_FOLLOWUP_LIMIT', (int) ($_ENV['OUTREACH_DAILY_FOLLOWUP_LIMIT'] ?? 30));
```

Change to:

```php
define('DAILY_FOLLOWUP_LIMIT', (int) ($_ENV['OUTREACH_DAILY_FOLLOWUP_LIMIT'] ?? 75));
```

Also update the inline comment block above it to reflect the multi-touch semantics:

```php
// Follow-ups have their own daily cap, separate from first-touch sends.
// With the multi-touch sequence (touches 2 through N), this cap applies
// across ALL touch positions combined. Default 75 — raise via env var
// (OUTREACH_DAILY_FOLLOWUP_LIMIT) once domain reputation supports more.
define('DAILY_FOLLOWUP_LIMIT', (int) ($_ENV['OUTREACH_DAILY_FOLLOWUP_LIMIT'] ?? 75));
```

- [ ] **Step 2: Replace the `stepSendFollowups` body**

Find the function `stepSendFollowups($pdo, $dryRun)` (starts around line 780). Replace the ENTIRE function body with:

```php
function stepSendFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 6: Send Follow-ups ---');

    // Count how many follow-up sends have happened today (across all touch positions)
    $sentToday = (int) $pdo->query(
        "SELECT COUNT(*) FROM outreach_followups WHERE DATE(sent_at) = CURDATE()"
    )->fetchColumn();
    $remaining = DAILY_FOLLOWUP_LIMIT - $sentToday;

    if ($remaining <= 0) {
        logPipeline("Follow-up daily limit of " . DAILY_FOLLOWUP_LIMIT . " reached ($sentToday sent today). Skipping.");
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM outreach_followups
         WHERE status = 'approved'
           AND scheduled_for <= NOW()
         ORDER BY scheduled_for ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, $remaining, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        logPipeline('No follow-ups ready to send.');
        return;
    }

    logPipeline('Found ' . count($rows) . ' follow-up(s) ready to send (cap remaining: ' . $remaining . ').');

    if ($dryRun) {
        foreach ($rows as $r) {
            logPipeline("[DRY RUN] Would send followup #{$r['id']} (lead #{$r['lead_id']}, touch {$r['touch_number']})");
        }
        return;
    }

    $successCount = 0;
    $failCount = 0;
    $skipCount = 0;

    foreach ($rows as $row) {
        try {
            $reason = null;
            if (send_followup_row($pdo, $row, $reason)) {
                logPipeline("Sent followup #{$row['id']} (lead #{$row['lead_id']}, touch {$row['touch_number']})");
                $successCount++;
            } elseif ($reason === 'not_eligible') {
                $skipCount++;
            } else {
                logPipeline("Failed to send followup #{$row['id']}: " . ($reason ?? 'unknown'), 'WARN');
                $failCount++;
            }

            if ($successCount + $failCount + $skipCount < count($rows)) {
                sleep(2);
            }
        } catch (Throwable $e) {
            logPipeline("Error sending followup #{$row['id']}: " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logPipeline("Follow-ups complete. Sent: $successCount, Failed: $failCount, Skipped: $skipCount");
}
```

- [ ] **Step 3: Remove the auto_send_mode gating around `stepSendFollowups`**

Now that drafts auto-advance to `approved` in auto mode (via `stepDraftFollowups`) and drafted rows in review mode stay `drafted` (so they won't be picked up here), the gate at lines 226-230 is no longer needed. Find:

```php
    if (($runAll || $sendOnly) && $autoSendMode === 'auto') {
        stepSendFollowups($pdo, $dryRun);
    } elseif (($runAll || $sendOnly) && $autoSendMode === 'review') {
        logPipeline('Send mode: review — follow-ups skipped (no manual-trigger UI exists yet; flip to auto-send to resume).');
    }
```

Replace with:

```php
    // Step 6 always runs — review-vs-auto gating is implicit in row statuses.
    // (Review mode: rows stay 'drafted' awaiting admin approval — not picked
    // up by the WHERE status='approved' query.)
    if ($runAll || $sendOnly) {
        stepSendFollowups($pdo, $dryRun);
    }
```

- [ ] **Step 4: Sanity-check syntax**

```bash
php -l cron/outreach_pipeline.php
```

Expected: no syntax errors.

- [ ] **Step 5: Run --dry-run end-to-end to verify all steps wire together**

```bash
php cron/outreach_pipeline.php --dry-run
```

Expected: no fatal errors. Output should show Steps 1, 2.5, 3, 4, 5, 5.5, 5.6, 6 in order with sensible messages (or "API not configured" warnings if env vars are missing locally).

- [ ] **Step 6: Stage and commit (user runs when ready)**

```bash
git add cron/outreach_pipeline.php
git commit -m "feat(outreach): rewrite stepSendFollowups against new outreach_followups table"
```

---

## Task 13: Pipeline — integrate shape-mismatch check + AB rotation hook for followup_sequence

**Files:**
- Modify: `cron/outreach_pipeline.php` — wire `check_followup_sequence_shape_match` into `stepManageAbTests`

**Purpose:** The pipeline's existing `stepManageAbTests` already loops over `ab_known_variant_types()` for promotion. We need to ALSO call the new shape-mismatch check on each tick so admins get warned/test gets paused when config diverges from variant intents.

- [ ] **Step 1: Find `stepManageAbTests` and add the shape-mismatch check**

Locate the function `stepManageAbTests($pdo, $dryRun)` (around line 426). Right after the `$enabled` check at the top:

```php
    $enabled = getState($pdo, 'ab_auto_enabled', '1');
    if ($enabled !== '1') {
        logPipeline('A/B automation is OFF (ab_auto_enabled != 1). Skipping.');
        return;
    }
```

Add this block immediately after (still inside the function, before the `$allTypes = ab_known_variant_types();` line):

```php
    // followup_sequence-specific shape check: if the active followup_sequence
    // test's variant intents no longer match the current followup_sequence_config
    // touch list (e.g. admin added a touch), auto-pause it.
    if (!$dryRun) {
        $shapeResult = check_followup_sequence_shape_match($pdo);
        if ($shapeResult['action'] === 'paused') {
            logPipeline($shapeResult['reason'] ?? 'followup_sequence test auto-paused for shape mismatch', 'WARN');
        }
    }
```

- [ ] **Step 2: Sanity-check syntax**

```bash
php -l cron/outreach_pipeline.php
```

Expected: no syntax errors.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/outreach_pipeline.php
git commit -m "feat(outreach): auto-pause followup_sequence tests on config shape mismatch in pipeline"
```

---

## Task 14: Delete old static template file

**Files:**
- Delete: `cron/lib/followup_template.php`

- [ ] **Step 1: Confirm no remaining references**

```bash
grep -rn "followup_template" cron/ admin/ webhooks/ api/
grep -rn "build_followup_email" cron/ admin/ webhooks/ api/
```

Expected: only matches inside the file itself, and the `require_once __DIR__ . '/followup_template.php';` line inside the old `send_outreach_followup` function (which was already removed in Task 5). If any non-removed references remain, fix them before deleting.

- [ ] **Step 2: Delete the file**

```bash
git rm cron/lib/followup_template.php
```

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git commit -m "chore(outreach): remove obsolete static follow-up template"
```

---

## Task 15: Settings tab — sequence config panel (render + POST handler)

**Files:**
- Modify: `admin/outreach/tabs/settings.php`

**Purpose:** Add the "Follow-up sequence" panel below the existing "A/B automation" panel. Provides per-touch row editing (days_after_prev + default_intent), with validation. On save, writes JSON to `outreach_pipeline_state.followup_sequence_config`.

- [ ] **Step 1: Add the POST handler**

In `admin/outreach/tabs/settings.php`, find `settings_tab_handle_post($pdo)`. Add this branch BEFORE the final `header('Location: ...'); exit;` fallback:

```php
    if ($action === 'set_followup_sequence') {
        $touches = $_POST['touches'] ?? [];
        if (!is_array($touches)) $touches = [];
        if (count($touches) > 6) {
            $_SESSION['message'] = 'Maximum 6 follow-up touches allowed.';
            $_SESSION['message_type'] = 'error';
            header('Location: index.php?tab=settings'); exit;
        }

        $cfg = [];
        $touchNum = 2;
        foreach ($touches as $touch) {
            if (!is_array($touch)) continue;
            $days = (int) ($touch['days_after_prev'] ?? 0);
            $intent = trim((string) ($touch['default_intent'] ?? ''));
            if ($days < 1 || $days > 90) {
                $_SESSION['message'] = "Touch $touchNum: days_after_prev must be 1-90.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?tab=settings'); exit;
            }
            if (strlen($intent) > 200) {
                $_SESSION['message'] = "Touch $touchNum: intent exceeds 200 chars.";
                $_SESSION['message_type'] = 'error';
                header('Location: index.php?tab=settings'); exit;
            }
            // Strip HTML tags defensively
            $intent = strip_tags($intent);
            if ($intent === '') $intent = 'gentle bump';
            $cfg[] = [
                'touch' => $touchNum,
                'days_after_prev' => $days,
                'default_intent' => $intent,
            ];
            $touchNum++;
        }

        settings_tab_set_state($pdo, 'followup_sequence_config', json_encode($cfg, JSON_UNESCAPED_SLASHES));
        $_SESSION['message'] = 'Follow-up sequence saved (' . count($cfg) . ' touch' . (count($cfg) === 1 ? '' : 'es') . ').';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php?tab=settings'); exit;
    }
```

- [ ] **Step 2: Add the render function for the panel**

In `settings_tab_render($pdo)`, add this code BEFORE the closing of the function (after the existing "A/B automation" panel render, before the "Current A/B status" panel):

```php
    // ─── Follow-up sequence panel ───

    $fuConfigJson = settings_tab_get_state($pdo, 'followup_sequence_config', '[]');
    $fuConfig = json_decode((string) $fuConfigJson, true);
    if (!is_array($fuConfig)) $fuConfig = [];
    ?>

    <div class="panel">
        <div class="panel-header">
            <h2>Follow-up sequence</h2>
        </div>
        <div class="panel-content">
            <p class="hint" style="margin-top:0;">
                The pipeline drafts each follow-up ~1 day before its scheduled send. Drafts queue in the
                <a href="?tab=followups">Follow-ups tab</a> when Review-before-send is on; otherwise they auto-send.
                Touch 1 is the original first-touch email; this list configures touches 2 onward.
            </p>
            <form method="POST">
                <input type="hidden" name="tab" value="settings">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <input type="hidden" name="action" value="set_followup_sequence">

                <table class="data-table" style="margin-bottom:12px;">
                    <thead>
                        <tr>
                            <th>Touch #</th>
                            <th>Days after previous touch (1-90)</th>
                            <th>Default intent (used if no active A/B test)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="followupTouchesBody">
                        <?php if (empty($fuConfig)): ?>
                            <tr><td colspan="4" class="empty-state">No follow-up touches configured. Click "Add touch" to add one.</td></tr>
                        <?php else: ?>
                            <?php foreach ($fuConfig as $i => $touch): ?>
                                <?php $touchNum = (int) ($touch['touch'] ?? ($i + 2)); ?>
                                <tr>
                                    <td>Touch <?php echo $touchNum; ?></td>
                                    <td><input type="number" name="touches[<?php echo $i; ?>][days_after_prev]" min="1" max="90" value="<?php echo (int) ($touch['days_after_prev'] ?? 5); ?>" required></td>
                                    <td><input type="text" name="touches[<?php echo $i; ?>][default_intent]" maxlength="200" value="<?php echo htmlspecialchars((string) ($touch['default_intent'] ?? '')); ?>" style="width:100%;" required></td>
                                    <td><!-- removed via JS only on the last row --></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-small btn-blue" onclick="addFollowupTouch()">+ Add touch</button>
                    <button type="button" class="btn btn-small btn-neutral" onclick="removeLastFollowupTouch()">Remove last touch</button>
                    <button type="submit" class="btn btn-blue" style="margin-left:auto;">Save sequence</button>
                </div>
            </form>

            <script>
                (function() {
                    var nextIndex = <?php echo count($fuConfig); ?>;
                    var defaultGaps = [3, 7, 14, 21, 30, 60]; // suggested defaults when adding rows

                    window.addFollowupTouch = function() {
                        if (nextIndex >= 6) { alert('Maximum 6 follow-up touches.'); return; }
                        var tbody = document.getElementById('followupTouchesBody');
                        // Clear empty-state row if present
                        if (tbody.querySelector('.empty-state')) tbody.innerHTML = '';
                        var i = nextIndex;
                        var touchNum = i + 2;
                        var gap = defaultGaps[i] || 14;
                        var row = document.createElement('tr');
                        row.innerHTML = '<td>Touch ' + touchNum + '</td>' +
                            '<td><input type="number" name="touches[' + i + '][days_after_prev]" min="1" max="90" value="' + gap + '" required></td>' +
                            '<td><input type="text" name="touches[' + i + '][default_intent]" maxlength="200" value="" style="width:100%;" required placeholder="e.g. gentle bump"></td>' +
                            '<td></td>';
                        tbody.appendChild(row);
                        nextIndex++;
                    };

                    window.removeLastFollowupTouch = function() {
                        var tbody = document.getElementById('followupTouchesBody');
                        var rows = tbody.querySelectorAll('tr');
                        if (rows.length === 0) return;
                        // Don't allow removing if it's the empty-state placeholder
                        if (rows[0].querySelector('.empty-state')) return;
                        rows[rows.length - 1].remove();
                        nextIndex = Math.max(0, nextIndex - 1);
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4" class="empty-state">No follow-up touches configured. Click "Add touch" to add one.</td></tr>';
                        }
                    };
                })();
            </script>
        </div>
    </div>

    <?php
```

- [ ] **Step 3: Sanity-check syntax**

```bash
php -l admin/outreach/tabs/settings.php
```

Expected: no syntax errors.

- [ ] **Step 4: Visual check in browser**

Load `http://localhost/argo-books-website/admin/outreach/?tab=settings` (after admin login). Confirm:
- "Follow-up sequence" panel appears
- "Add touch" / "Remove last touch" buttons work
- Saving updates the values and shows the success flash message

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/tabs/settings.php
git commit -m "feat(outreach): add follow-up sequence config panel in Settings"
```

---

## Task 16: Settings + AB — first-deploy defaults seeding

**Files:**
- Modify: `admin/outreach/tabs/settings.php` (call seeder from render)

**Purpose:** When `settings_tab_render` runs and there's no `followup_sequence_config` in state yet, seed the defaults (3 touches at +3, +7, +14) AND call `ensure_followup_starter_test` to create the draft A/B test.

- [ ] **Step 1: Add the seeding logic at the top of `settings_tab_render`**

In `admin/outreach/tabs/settings.php`, find the start of `settings_tab_render($pdo)`. Right after the function opens and the require_once line that loads `outreach_helpers.php`, add:

```php
    // ─── First-deploy seed: followup sequence config + A/B starter test ───
    $existingFuCfg = settings_tab_get_state($pdo, 'followup_sequence_config', null);
    if ($existingFuCfg === null) {
        $defaultCfg = [
            ['touch' => 2, 'days_after_prev' => 3,  'default_intent' => 'gentle bump'],
            ['touch' => 3, 'days_after_prev' => 7,  'default_intent' => 'different angle'],
            ['touch' => 4, 'days_after_prev' => 14, 'default_intent' => 'final note before closing'],
        ];
        settings_tab_set_state($pdo, 'followup_sequence_config', json_encode($defaultCfg, JSON_UNESCAPED_SLASHES));

        // Seed the A/B starter test (requires ab_helpers)
        require_once __DIR__ . '/../../../cron/lib/ab_helpers.php';
        ensure_followup_starter_test($pdo);
    }
```

- [ ] **Step 2: Verify by clearing the state and reloading the settings tab**

```bash
mysql -u root argo_books -e "DELETE FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config';"
mysql -u root argo_books -e "DELETE FROM outreach_ab_variants WHERE test_id IN (SELECT id FROM outreach_ab_tests WHERE variant_type = 'followup_sequence');"
mysql -u root argo_books -e "DELETE FROM outreach_ab_tests WHERE variant_type = 'followup_sequence';"
```

Then reload `http://localhost/argo-books-website/admin/outreach/?tab=settings`. Confirm the Follow-up sequence panel shows 3 default rows. Then:

```bash
mysql -u root argo_books -e "SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'followup_sequence_config';"
mysql -u root argo_books -e "SELECT id, name, variant_type, status FROM outreach_ab_tests WHERE variant_type = 'followup_sequence';"
mysql -u root argo_books -e "SELECT label, content FROM outreach_ab_variants WHERE test_id IN (SELECT id FROM outreach_ab_tests WHERE variant_type = 'followup_sequence');"
```

Expected: state row exists with the 3-touch default JSON. One test exists with `status='draft'`. Three variants with the seed labels.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/tabs/settings.php
git commit -m "feat(outreach): seed default sequence config and A/B starter test on first deploy"
```

---

## Task 17: API — new endpoints for follow-ups

**Files:**
- Modify: `admin/outreach/api.php`

**Purpose:** Add API endpoints that the new Follow-ups tab JS will call: `get_followups` (list with sub-view filter), `approve_followup`, `regenerate_followup`, `skip_followup`, `halt_followup_sequence`, plus bulk variants (`bulk_approve_followups`, `bulk_skip_followups`, `bulk_halt_followups`).

- [ ] **Step 1: Add the new cases to the switch in api.php**

In `admin/outreach/api.php`, add these cases inside the `switch ($action)` block (after the existing `case 'export_csv':` block, before `default:`):

```php
    // Follow-ups
    case 'get_followups':
        get_followups($pdo);
        break;
    case 'approve_followup':
        approve_followup($pdo);
        break;
    case 'regenerate_followup':
        regenerate_followup($pdo);
        break;
    case 'skip_followup':
        skip_followup($pdo);
        break;
    case 'halt_followup_sequence':
        halt_followup_sequence($pdo);
        break;
    case 'bulk_approve_followups':
        bulk_approve_followups($pdo);
        break;
    case 'bulk_skip_followups':
        bulk_skip_followups($pdo);
        break;
    case 'bulk_halt_followups':
        bulk_halt_followups($pdo);
        break;
    case 'get_followups_for_lead':
        get_followups_for_lead($pdo);
        break;
```

Also add the relevant actions to the `$pipelineActions` blocking list near the top:

```php
$pipelineActions = ['send_email', 'generate_draft', 'import_leads', 'search_businesses', 'regenerate_followup'];
```

- [ ] **Step 2: Append the function implementations at the bottom of api.php**

```php
// ─── Follow-up endpoints ───

function get_followups($pdo)
{
    $view = $_GET['view'] ?? 'pending_review';
    $validViews = ['pending_review', 'approved', 'upcoming', 'sent', 'halted'];
    if (!in_array($view, $validViews, true)) {
        $view = 'pending_review';
    }

    $sql = "SELECT f.*, l.business_name, l.email AS lead_email, l.city, l.draft_subject AS original_subject,
                   v.label AS ab_variant_label
            FROM outreach_followups f
            JOIN outreach_leads l ON l.id = f.lead_id
            LEFT JOIN outreach_ab_variants v ON v.id = f.ab_variant_id
            WHERE ";

    switch ($view) {
        case 'pending_review':
            $sql .= "f.status = 'drafted' AND f.scheduled_for <= DATE_ADD(NOW(), INTERVAL 2 DAY)";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'approved':
            $sql .= "f.status = 'approved'";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'upcoming':
            $sql .= "f.status = 'scheduled'";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'sent':
            $sql .= "f.status = 'sent' AND f.sent_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $sql .= " ORDER BY f.sent_at DESC";
            break;
        case 'halted':
            $sql .= "f.status IN ('halted','failed','skipped') AND f.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $sql .= " ORDER BY f.updated_at DESC";
            break;
    }
    $sql .= " LIMIT 200";

    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'view' => $view, 'rows' => $rows]);
}

function approve_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE id = ? AND status = 'drafted'");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Row not in drafted state']); return;
    }
    echo json_encode(['success' => true]);
}

function regenerate_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("SELECT * FROM outreach_followups WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Not found']); return;
    }
    if (!in_array($row['status'], ['drafted', 'failed'], true)) {
        echo json_encode(['success' => false, 'message' => 'Can only regenerate drafted or failed rows']); return;
    }

    // Reset attempts so regen has a fresh budget
    $pdo->prepare("UPDATE outreach_followups SET draft_attempts = 0, status = 'scheduled' WHERE id = ?")
        ->execute([$id]);
    // Re-fetch with the updated state
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $ok = draft_followup_via_gemini($pdo, $row);
    if ($ok) {
        // Return the new draft for the UI
        $newRow = $pdo->prepare("SELECT draft_subject, draft_body FROM outreach_followups WHERE id = ?");
        $newRow->execute([$id]);
        $r = $newRow->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'draft_subject' => $r['draft_subject'], 'draft_body' => $r['draft_body']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gemini draft failed']);
    }
}

function skip_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'skipped', halt_reason = 'manual' WHERE id = ? AND status IN ('drafted','approved','scheduled')");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Row already sent or halted']); return;
    }
    echo json_encode(['success' => true]);
}

function halt_followup_sequence($pdo)
{
    $leadId = (int) ($_POST['lead_id'] ?? 0);
    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid lead_id']); return;
    }
    $count = halt_followups_for_lead($pdo, $leadId, 'manual');
    echo json_encode(['success' => true, 'halted_count' => $count]);
}

function bulk_approve_followups($pdo)
{
    $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
    $ids = array_filter($ids, fn($i) => $i > 0);
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No ids']); return;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE status = 'drafted' AND id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    echo json_encode(['success' => true, 'approved_count' => $stmt->rowCount()]);
}

function bulk_skip_followups($pdo)
{
    $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
    $ids = array_filter($ids, fn($i) => $i > 0);
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No ids']); return;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'skipped', halt_reason = 'manual' WHERE status IN ('drafted','approved','scheduled') AND id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    echo json_encode(['success' => true, 'skipped_count' => $stmt->rowCount()]);
}

function bulk_halt_followups($pdo)
{
    $leadIds = array_map('intval', (array) ($_POST['lead_ids'] ?? []));
    $leadIds = array_filter($leadIds, fn($i) => $i > 0);
    if (empty($leadIds)) {
        echo json_encode(['success' => false, 'message' => 'No lead_ids']); return;
    }
    $total = 0;
    foreach ($leadIds as $lid) {
        $total += halt_followups_for_lead($pdo, $lid, 'manual');
    }
    echo json_encode(['success' => true, 'halted_count' => $total]);
}

function get_followups_for_lead($pdo)
{
    $leadId = (int) ($_GET['lead_id'] ?? 0);
    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid lead_id']); return;
    }
    $stmt = $pdo->prepare("SELECT f.*, v.label AS ab_variant_label FROM outreach_followups f LEFT JOIN outreach_ab_variants v ON v.id = f.ab_variant_id WHERE f.lead_id = ? ORDER BY f.touch_number ASC");
    $stmt->execute([$leadId]);
    echo json_encode(['success' => true, 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}
```

- [ ] **Step 3: Update `get_stats` to include `followups_pending`**

Find `function get_stats($pdo)` in api.php. Add `followups_pending` to the existing stats query:

Before:
```php
    $rows = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(status = 'new') as new_leads,
        SUM(status = 'draft_generated') as drafts_pending,
        SUM(status = 'contacted') as contacted,
        SUM(status = 'replied') as replied,
        SUM(status = 'interested') as interested
    FROM outreach_leads")->fetch();
```

After:
```php
    $rows = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(status = 'new') as new_leads,
        SUM(status = 'draft_generated') as drafts_pending,
        SUM(status = 'contacted') as contacted,
        SUM(status = 'replied') as replied,
        SUM(status = 'interested') as interested
    FROM outreach_leads")->fetch();

    // Follow-ups pending review (drafted, awaiting admin approval)
    $rows['followups_pending'] = (int) $pdo->query("SELECT COUNT(*) FROM outreach_followups WHERE status = 'drafted'")->fetchColumn();
```

- [ ] **Step 4: Sanity-check syntax**

```bash
php -l admin/outreach/api.php
```

Expected: no syntax errors.

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/api.php
git commit -m "feat(outreach): add API endpoints for follow-up review tab"
```

---

## Task 18: Follow-ups tab — render partial

**Files:**
- Create: `admin/outreach/tabs/followups.php`

**Purpose:** New tab partial with sub-view pills + table that JS will populate. Matches the patterns used by the existing Leads tab.

- [ ] **Step 1: Create the file**

```php
<?php
/**
 * Follow-ups tab partial.
 *
 * Renders the static structure of the Follow-ups tab — sub-view pills,
 * filters, empty bulk actions bar, and the table tbody that outreach.js
 * populates via `get_followups` API calls.
 */

function followups_tab_render($pdo)
{
    ?>
    <div class="panel">
        <div class="panel-header">
            <h2>Follow-ups</h2>
            <div class="panel-actions">
                <div class="section-tabs" style="display:inline-flex; gap:0; border-radius:6px; overflow:hidden;">
                    <button type="button" class="section-tab active" data-fu-view="pending_review" onclick="switchFollowupsView(this, 'pending_review')">Pending review <span class="fu-count" id="fuCountPending">0</span></button>
                    <button type="button" class="section-tab" data-fu-view="approved" onclick="switchFollowupsView(this, 'approved')">Approved & queued</button>
                    <button type="button" class="section-tab" data-fu-view="upcoming" onclick="switchFollowupsView(this, 'upcoming')">Upcoming</button>
                    <button type="button" class="section-tab" data-fu-view="sent" onclick="switchFollowupsView(this, 'sent')">Sent</button>
                    <button type="button" class="section-tab" data-fu-view="halted" onclick="switchFollowupsView(this, 'halted')">Halted / failed</button>
                </div>
            </div>
        </div>

        <div class="bulk-actions-bar" id="fuBulkActionsBar" style="display:none;">
            <span><strong id="fuSelectedCount">0</strong> selected</span>
            <button class="btn btn-small btn-blue" onclick="bulkApproveFollowups()">Approve selected</button>
            <button class="btn btn-small btn-blue" onclick="bulkSkipFollowups()">Skip selected</button>
            <button class="btn btn-small btn-red" onclick="bulkHaltFollowupSequences()">Halt sequences for selected leads</button>
        </div>

        <div id="followupsContainer">
            <p class="empty-state">Loading follow-ups...</p>
        </div>
    </div>
    <?php
}
```

- [ ] **Step 2: Sanity-check syntax**

```bash
php -l admin/outreach/tabs/followups.php
```

Expected: no syntax errors.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/tabs/followups.php
git commit -m "feat(outreach): add follow-ups tab render partial"
```

---

## Task 19: Wire the Follow-ups tab into the main outreach page

**Files:**
- Modify: `admin/outreach/index.php`

**Purpose:** Require the new tab partial, add it to the tab nav, dispatch POSTs for it (none in v1 — actions are AJAX), and render the new `<div id="followups">` container.

- [ ] **Step 1: Require the new partial**

In `admin/outreach/index.php`, find the existing `require_once` block (around line 17-18):

```php
require_once __DIR__ . '/tabs/ab-tests.php';
require_once __DIR__ . '/tabs/settings.php';
```

Add after:

```php
require_once __DIR__ . '/tabs/followups.php';
```

- [ ] **Step 2: Add `followups` to the allowed-tabs whitelist**

Find this block (around line 41):

```php
$activeTab = $_GET['tab'] ?? 'leads';
if (!in_array($activeTab, ['leads', 'ab-tests', 'settings'], true)) {
    $activeTab = 'leads';
}
```

Change to:

```php
$activeTab = $_GET['tab'] ?? 'leads';
if (!in_array($activeTab, ['leads', 'ab-tests', 'followups', 'settings'], true)) {
    $activeTab = 'leads';
}
```

- [ ] **Step 3: Add the tab nav button**

Find the existing tab nav (around line 58):

```php
<div class="section-tabs">
    <button class="section-tab <?php echo $activeTab === 'leads' ? 'active' : ''; ?>" data-tab="leads">Leads</button>
    <button class="section-tab <?php echo $activeTab === 'ab-tests' ? 'active' : ''; ?>" data-tab="ab-tests">A/B Tests</button>
    <button class="section-tab <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" data-tab="settings">Settings</button>
</div>
```

Change to:

```php
<div class="section-tabs">
    <button class="section-tab <?php echo $activeTab === 'leads' ? 'active' : ''; ?>" data-tab="leads">Leads</button>
    <button class="section-tab <?php echo $activeTab === 'ab-tests' ? 'active' : ''; ?>" data-tab="ab-tests">A/B Tests</button>
    <button class="section-tab <?php echo $activeTab === 'followups' ? 'active' : ''; ?>" data-tab="followups">Follow-ups</button>
    <button class="section-tab <?php echo $activeTab === 'settings' ? 'active' : ''; ?>" data-tab="settings">Settings</button>
</div>
```

- [ ] **Step 4: Add the followups tab content div**

Find the existing `<div id="ab-tests">` and `<div id="settings">` blocks (around lines 285-291). Insert a new block between them:

```php
<div id="followups" class="tab-content <?php echo $activeTab === 'followups' ? 'active' : ''; ?>">
    <?php followups_tab_render($pdo); ?>
</div>
```

So the order ends up:

```php
<div id="ab-tests" class="tab-content <?php echo $activeTab === 'ab-tests' ? 'active' : ''; ?>">
    <?php ab_tests_tab_render($pdo, (int) ($_GET['test_id'] ?? 0)); ?>
</div>

<div id="followups" class="tab-content <?php echo $activeTab === 'followups' ? 'active' : ''; ?>">
    <?php followups_tab_render($pdo); ?>
</div>

<div id="settings" class="tab-content <?php echo $activeTab === 'settings' ? 'active' : ''; ?>">
    <?php settings_tab_render($pdo); ?>
</div>
```

- [ ] **Step 5: Add the "Follow-ups pending review" stat card**

Find the existing stats row (around line 72-101). Add a new stat card after the "Drafts Pending" card:

```php
    <div class="stat-card">
        <div class="stat-label">Follow-ups pending review</div>
        <div class="stat-value stat-pending" id="statFollowupsPending">0</div>
    </div>
```

- [ ] **Step 6: Sanity-check syntax**

```bash
php -l admin/outreach/index.php
```

Expected: no syntax errors.

- [ ] **Step 7: Browser check**

Load `http://localhost/argo-books-website/admin/outreach/?tab=followups`. Confirm:
- Tab appears in the nav and is active
- Sub-view pills render
- "Loading follow-ups..." placeholder appears
- Stat card "Follow-ups pending review" shows on the Leads tab

- [ ] **Step 8: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/index.php
git commit -m "feat(outreach): wire Follow-ups tab into admin page"
```

---

## Task 20: Follow-ups tab — JavaScript (sub-view loading, row actions, bulk actions)

**Files:**
- Modify: `admin/outreach/outreach.js`

**Purpose:** Add JS functions that the new tab uses: switching sub-views, loading rows via `get_followups`, rendering rows (with editable body + per-row actions), handling row + bulk actions. Also update the dashboard stats loader to populate the new "Follow-ups pending review" card.

- [ ] **Step 1: Update the stats loader to include the new stat**

Find the existing stats loader in `admin/outreach/outreach.js` (around line 136-141):

```js
        const data = await api('get_stats');
```

Find the block that populates the stat cards (around lines 140-141). Add:

```js
            document.getElementById('statFollowupsPending').textContent = s.followups_pending || 0;
```

- [ ] **Step 2: Append the follow-ups tab JS to the bottom of outreach.js**

```js
// ─── Follow-ups tab ───

let currentFollowupView = 'pending_review';

window.switchFollowupsView = function(button, view) {
    document.querySelectorAll('[data-fu-view]').forEach(b => b.classList.remove('active'));
    button.classList.add('active');
    currentFollowupView = view;
    loadFollowups();
};

async function loadFollowups() {
    const container = document.getElementById('followupsContainer');
    container.innerHTML = '<p class="empty-state">Loading...</p>';
    try {
        const data = await api('get_followups&view=' + encodeURIComponent(currentFollowupView));
        if (!data.success) {
            container.innerHTML = '<p class="empty-state">Error loading: ' + (data.message || 'unknown') + '</p>';
            return;
        }
        if (!data.rows.length) {
            const emptyMessages = {
                'pending_review': 'No follow-ups awaiting review. Drafts appear here ~1 day before each scheduled send.',
                'approved': 'No approved follow-ups waiting in the send queue.',
                'upcoming': 'No follow-ups scheduled. New ones are created as leads receive their first email.',
                'sent': 'No follow-ups sent in the last 30 days.',
                'halted': 'No halted or failed follow-ups in the last 30 days.',
            };
            container.innerHTML = '<p class="empty-state">' + (emptyMessages[currentFollowupView] || 'No rows.') + '</p>';
            updateFollowupsBulkBar();
            return;
        }
        container.innerHTML = data.rows.map(r => renderFollowupRow(r, currentFollowupView)).join('');
        updateFollowupsBulkBar();
    } catch (e) {
        container.innerHTML = '<p class="empty-state">Error loading follow-ups: ' + e.message + '</p>';
    }
}

function renderFollowupRow(r, view) {
    const cityLabel = r.city ? ' (' + escapeHtml(r.city) + ')' : '';
    const scheduledStr = r.scheduled_for ? formatScheduled(r.scheduled_for) : '';
    const sentStr = r.sent_at ? formatScheduled(r.sent_at) : '';
    const haltReason = r.halt_reason ? ' · Reason: ' + escapeHtml(r.halt_reason) : '';
    const abLabel = r.ab_variant_label ? ' · A/B: ' + escapeHtml(r.ab_variant_label) : '';

    let actions = '';
    let bodyEditor = '';
    let checkboxCell = '';

    if (view === 'pending_review') {
        checkboxCell = '<input type="checkbox" class="fu-row-check" data-fu-id="' + r.id + '" data-lead-id="' + r.lead_id + '" onchange="updateFollowupsBulkBar()">';
        bodyEditor = '<input type="text" class="fu-subject" data-id="' + r.id + '" value="' + escapeHtml(r.draft_subject || '') + '" style="width:100%; margin-bottom:6px;">' +
            '<textarea class="fu-body" data-id="' + r.id + '" rows="6" style="width:100%; font-family:inherit;">' + escapeHtml(r.draft_body || '') + '</textarea>';
        actions = '<button class="btn btn-small btn-blue" onclick="approveFollowup(' + r.id + ')">Approve & queue</button>' +
            ' <button class="btn btn-small btn-blue" onclick="regenerateFollowup(' + r.id + ')">Regenerate draft</button>' +
            ' <button class="btn btn-small btn-neutral" onclick="skipFollowup(' + r.id + ')">Skip this touch</button>' +
            ' <button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    } else if (view === 'approved') {
        actions = '<button class="btn btn-small btn-neutral" onclick="skipFollowup(' + r.id + ')">Skip</button>' +
            ' <button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    } else if (view === 'upcoming') {
        actions = '<button class="btn btn-small btn-red" onclick="haltFollowupSequence(' + r.lead_id + ')">Halt sequence</button>';
    }

    return '<div class="panel" style="margin-bottom:12px;">' +
        '<div class="panel-content">' +
            '<div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">' +
                (checkboxCell ? '<div>' + checkboxCell + '</div>' : '') +
                '<div style="flex:1;">' +
                    '<strong>' + escapeHtml(r.business_name || 'Unknown') + '</strong>' + cityLabel +
                    ' &nbsp;·&nbsp; Touch ' + r.touch_number +
                    (scheduledStr ? ' &nbsp;·&nbsp; Scheduled ' + scheduledStr : '') +
                    (sentStr ? ' &nbsp;·&nbsp; Sent ' + sentStr : '') +
                    haltReason +
                '</div>' +
            '</div>' +
            (bodyEditor ? '<div style="margin-bottom:8px;">' + bodyEditor + '</div>' :
                (r.draft_subject ? '<div style="font-size:13px; color:#666;">Subject: ' + escapeHtml(r.draft_subject) + '</div>' : '')) +
            '<div style="font-size:12px; color:#999; margin-top:6px;">' + abLabel + '</div>' +
            (actions ? '<div style="margin-top:10px;">' + actions + '</div>' : '') +
        '</div>' +
    '</div>';
}

function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' })[c]);
}

function formatScheduled(dt) {
    try {
        const d = new Date(dt.replace(' ', 'T'));
        const now = new Date();
        const diffMs = d.getTime() - now.getTime();
        const diffHours = diffMs / 3600000;
        if (Math.abs(diffHours) < 48) {
            const h = Math.round(diffHours);
            if (h === 0) return 'now';
            if (h > 0) return 'in ' + h + 'h';
            return Math.abs(h) + 'h ago';
        }
        return d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return dt;
    }
}

window.approveFollowup = async function(id) {
    // Save any inline subject/body edits first
    const subjectInput = document.querySelector('.fu-subject[data-id="' + id + '"]');
    const bodyInput = document.querySelector('.fu-body[data-id="' + id + '"]');
    if (subjectInput && bodyInput) {
        await api('save_followup_draft', { id: id, subject: subjectInput.value, body: bodyInput.value });
    }
    const data = await api('approve_followup', { id: id });
    if (data.success) loadFollowups();
    else alert('Approve failed: ' + (data.message || 'unknown'));
};

window.regenerateFollowup = async function(id) {
    if (!confirm('Regenerate this follow-up via Gemini? Current draft will be replaced.')) return;
    const data = await api('regenerate_followup', { id: id });
    if (data.success) loadFollowups();
    else alert('Regenerate failed: ' + (data.message || 'unknown'));
};

window.skipFollowup = async function(id) {
    if (!confirm('Skip this follow-up touch? The next touch in the sequence will still be sent on schedule.')) return;
    const data = await api('skip_followup', { id: id });
    if (data.success) loadFollowups();
    else alert('Skip failed: ' + (data.message || 'unknown'));
};

window.haltFollowupSequence = async function(leadId) {
    if (!confirm('Halt the entire follow-up sequence for this lead? No more follow-ups will be sent.')) return;
    const data = await api('halt_followup_sequence', { lead_id: leadId });
    if (data.success) loadFollowups();
    else alert('Halt failed: ' + (data.message || 'unknown'));
};

window.updateFollowupsBulkBar = function() {
    const checks = document.querySelectorAll('.fu-row-check:checked');
    const bar = document.getElementById('fuBulkActionsBar');
    document.getElementById('fuSelectedCount').textContent = checks.length;
    bar.style.display = checks.length > 0 ? 'flex' : 'none';
};

window.bulkApproveFollowups = async function() {
    const ids = Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.fuId));
    if (!ids.length) return;
    const data = await api('bulk_approve_followups', { ids: ids });
    if (data.success) { alert('Approved ' + data.approved_count + ' follow-up(s).'); loadFollowups(); }
    else alert('Bulk approve failed: ' + (data.message || 'unknown'));
};

window.bulkSkipFollowups = async function() {
    const ids = Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.fuId));
    if (!ids.length) return;
    if (!confirm('Skip ' + ids.length + ' follow-up(s)?')) return;
    const data = await api('bulk_skip_followups', { ids: ids });
    if (data.success) { alert('Skipped ' + data.skipped_count + ' follow-up(s).'); loadFollowups(); }
    else alert('Bulk skip failed: ' + (data.message || 'unknown'));
};

window.bulkHaltFollowupSequences = async function() {
    const leadIds = Array.from(new Set(Array.from(document.querySelectorAll('.fu-row-check:checked')).map(c => parseInt(c.dataset.leadId))));
    if (!leadIds.length) return;
    if (!confirm('Halt the follow-up sequence for ' + leadIds.length + ' lead(s)? This stops ALL remaining follow-ups for these leads.')) return;
    const data = await api('bulk_halt_followups', { lead_ids: leadIds });
    if (data.success) { alert('Halted ' + data.halted_count + ' follow-up row(s).'); loadFollowups(); }
    else alert('Bulk halt failed: ' + (data.message || 'unknown'));
};

// Auto-load when the followups tab activates
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.section-tab[data-tab="followups"]').forEach(btn => {
        btn.addEventListener('click', () => setTimeout(loadFollowups, 0));
    });
    // Also auto-load on page load if we're already on the tab (e.g. ?tab=followups)
    if (window.location.search.indexOf('tab=followups') !== -1) {
        loadFollowups();
    }
});
```

- [ ] **Step 3: Add the `save_followup_draft` endpoint to api.php (small follow-on)**

In `admin/outreach/api.php`, add to the switch:

```php
    case 'save_followup_draft':
        save_followup_draft($pdo);
        break;
```

And append the function:

```php
function save_followup_draft($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));
    if ($id <= 0 || $subject === '' || $body === '') {
        echo json_encode(['success' => false, 'message' => 'Missing fields']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET draft_subject = ?, draft_body = ? WHERE id = ? AND status = 'drafted'");
    $stmt->execute([$subject, $body, $id]);
    echo json_encode(['success' => true]);
}
```

- [ ] **Step 4: Browser check**

Load `http://localhost/argo-books-website/admin/outreach/?tab=followups`. Confirm sub-view pills switch, table loads (empty state OK), checkboxes show bulk bar.

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/outreach.js admin/outreach/api.php
git commit -m "feat(outreach): follow-ups tab UI with sub-views, row actions, bulk actions"
```

---

## Task 21: Lead detail modal — Follow-ups sub-tab

**Files:**
- Modify: `admin/outreach/index.php` (add sub-tab + content div in the lead detail modal)
- Modify: `admin/outreach/outreach.js` (load sub-tab data when modal opens)

**Purpose:** Inside the existing lead detail modal, add a fourth sub-tab "Follow-ups" that shows the per-lead sequence with statuses and scheduled dates.

- [ ] **Step 1: Add the new sub-tab button to the modal tabs**

In `admin/outreach/index.php`, find the modal tabs (around line 302):

```html
<div class="tabs">
    <button class="tab active" onclick="switchTab('tabInfo', this)">Info</button>
    <button class="tab" onclick="switchTab('tabDraft', this)">Email Draft</button>
    <button class="tab" onclick="switchTab('tabActivity', this)">Activity</button>
</div>
```

Change to:

```html
<div class="tabs">
    <button class="tab active" onclick="switchTab('tabInfo', this)">Info</button>
    <button class="tab" onclick="switchTab('tabDraft', this)">Email Draft</button>
    <button class="tab" onclick="switchTab('tabActivity', this)">Activity</button>
    <button class="tab" onclick="switchTab('tabFollowups', this); loadLeadFollowups();">Follow-ups</button>
</div>
```

- [ ] **Step 2: Add the new tab content div**

In `admin/outreach/index.php`, find the existing Activity tab content (around line 432-436):

```html
<!-- Activity Tab -->
<div id="tabActivity" class="tab-content">
    <div id="activityTimeline" class="activity-timeline">
        <p class="empty-state-text">Loading activity...</p>
    </div>
</div>
```

Add directly after:

```html
<!-- Follow-ups Tab -->
<div id="tabFollowups" class="tab-content">
    <div id="leadFollowupsList">
        <p class="empty-state-text">Loading follow-ups...</p>
    </div>
</div>
```

- [ ] **Step 3: Add `loadLeadFollowups` to outreach.js**

Append:

```js
window.loadLeadFollowups = async function() {
    const list = document.getElementById('leadFollowupsList');
    list.innerHTML = '<p class="empty-state-text">Loading...</p>';
    // currentLeadId is the global declared at outreach.js:2 and set by
    // openLeadDetail() at outreach.js:527 whenever a lead modal opens.
    if (!currentLeadId) {
        list.innerHTML = '<p class="empty-state-text">No lead selected.</p>';
        return;
    }
    const data = await api('get_followups_for_lead&lead_id=' + currentLeadId);
    if (!data.success) {
        list.innerHTML = '<p class="empty-state-text">Error: ' + (data.message || 'unknown') + '</p>';
        return;
    }
    if (!data.rows.length) {
        list.innerHTML = '<p class="empty-state-text">No follow-ups scheduled for this lead. Sequence is configured in Settings; rows are created when the first-touch email sends.</p>';
        return;
    }
    list.innerHTML = '<table class="data-table"><thead><tr><th>Touch</th><th>Status</th><th>Scheduled</th><th>Sent</th><th>Halt reason</th><th>A/B variant</th></tr></thead><tbody>' +
        data.rows.map(r =>
            '<tr>' +
                '<td>' + r.touch_number + '</td>' +
                '<td>' + escapeHtml(r.status) + '</td>' +
                '<td>' + (r.scheduled_for || '—') + '</td>' +
                '<td>' + (r.sent_at || '—') + '</td>' +
                '<td>' + (r.halt_reason ? escapeHtml(r.halt_reason) : '—') + '</td>' +
                '<td>' + (r.ab_variant_label ? escapeHtml(r.ab_variant_label) : '—') + '</td>' +
            '</tr>'
        ).join('') +
    '</tbody></table>';
};
```

No changes needed to `openLeadDetail` — it already sets `currentLeadId` at outreach.js:527.

- [ ] **Step 4: Browser check**

Open any lead's detail modal, click the new "Follow-ups" sub-tab, confirm it loads (showing the empty-state message for leads without scheduled follow-ups, or a table for leads who've received first-touch under the new system).

- [ ] **Step 5: Stage and commit (user runs when ready)**

```bash
git add admin/outreach/index.php admin/outreach/outreach.js
git commit -m "feat(outreach): add Follow-ups sub-tab in lead detail modal"
```

---

## Task 22: Smoke tests

**Files:**
- Create: `cron/test_followups_smoke.php`

**Purpose:** CLI script that runs the 4 spec-listed smoke tests against a local DB. Exits non-zero on assertion failure.

- [ ] **Step 1: Create the smoke test file**

```php
<?php
/**
 * Follow-up smoke tests.
 *
 * Run via: php cron/test_followups_smoke.php
 *
 * Inserts fake data into the real local DB, runs assertions, then cleans up.
 * DO NOT RUN AGAINST PRODUCTION. Aborts immediately if APP_ENV='production'.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (($_ENV['APP_ENV'] ?? '') === 'production') {
    fwrite(STDERR, "REFUSING TO RUN: APP_ENV='production'. Smoke tests touch live data.\n");
    exit(2);
}

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/lib/outreach_helpers.php';
require_once __DIR__ . '/lib/ab_helpers.php';

global $pdo;

$pass = 0;
$fail = 0;
$createdLeadIds = [];

function assert_true($cond, $msg) {
    global $pass, $fail;
    if ($cond) { echo "  ✓ $msg\n"; $pass++; }
    else       { echo "  ✗ $msg\n"; $fail++; }
}

function cleanup($pdo, &$createdLeadIds) {
    foreach ($createdLeadIds as $id) {
        $pdo->prepare("DELETE FROM outreach_leads WHERE id = ?")->execute([$id]);
    }
    $createdLeadIds = [];
}

// ─── Ensure config exists ───
$pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('followup_sequence_config', ?) ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
    ->execute([json_encode([
        ['touch' => 2, 'days_after_prev' => 3,  'default_intent' => 'gentle bump'],
        ['touch' => 3, 'days_after_prev' => 7,  'default_intent' => 'different angle'],
        ['touch' => 4, 'days_after_prev' => 14, 'default_intent' => 'final note before closing'],
    ])]);
$pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('auto_send_mode', 'review') ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
    ->execute();

// ─── Test 1: scheduling creates N rows ───
echo "Test 1: schedule_followups_for_lead creates 3 rows for default config\n";
$pdo->prepare("INSERT INTO outreach_leads (business_name, email, status, sent_at) VALUES ('Smoke1', 'smoke1@example.com', 'contacted', NOW())")->execute();
$leadId = (int) $pdo->lastInsertId();
$createdLeadIds[] = $leadId;

$n = schedule_followups_for_lead($pdo, $leadId);
assert_true($n === 3, "schedule_followups_for_lead returned 3 (got $n)");

$rows = $pdo->prepare("SELECT touch_number, scheduled_for, status FROM outreach_followups WHERE lead_id = ? ORDER BY touch_number");
$rows->execute([$leadId]);
$rowList = $rows->fetchAll(PDO::FETCH_ASSOC);
assert_true(count($rowList) === 3, "3 outreach_followups rows exist for lead");
assert_true($rowList[0]['touch_number'] == 2 && $rowList[0]['status'] === 'scheduled', "Touch 2 is scheduled");

// ─── Test 2: draft step picks up scheduled rows in the window ───
echo "Test 2: draft step picks up scheduled rows whose window has opened\n";
// Set touch 2 scheduled_for to NOW so window is open
$pdo->prepare("UPDATE outreach_followups SET scheduled_for = NOW() WHERE lead_id = ? AND touch_number = 2")->execute([$leadId]);

$inWindowStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_followups WHERE status = 'scheduled' AND scheduled_for <= DATE_ADD(NOW(), INTERVAL 1 DAY) AND lead_id = ?");
$inWindowStmt->execute([$leadId]);
$inWindowCount = (int) $inWindowStmt->fetchColumn();
assert_true($inWindowCount === 1, "Touch 2 (scheduled now) is in the draft window (count=$inWindowCount)");

// (Actual Gemini call not mocked here — the test just verifies the eligibility query.
// To smoke-test the full Gemini path, set GEMINI_API_KEY and run --dry-run separately.)

// ─── Test 3: halt step halts follow-ups when lead replies ───
echo "Test 3: halt step halts follow-ups when lead is in stop-condition status\n";
$pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE lead_id = ?")->execute([$leadId]);
$pdo->prepare("UPDATE outreach_leads SET status = 'replied' WHERE id = ?")->execute([$leadId]);

$counts = halt_followups_bulk($pdo);
$haltedStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_followups WHERE lead_id = ? AND status = 'halted'");
$haltedStmt->execute([$leadId]);
$haltedCount = (int) $haltedStmt->fetchColumn();
assert_true($haltedCount === 3, "All 3 follow-ups halted after lead replied (got $haltedCount)");
$reasonsStmt = $pdo->prepare("SELECT DISTINCT halt_reason FROM outreach_followups WHERE lead_id = ?");
$reasonsStmt->execute([$leadId]);
$reasons = $reasonsStmt->fetchAll(PDO::FETCH_COLUMN);
assert_true(in_array('replied', $reasons, true), "halt_reason 'replied' present");

// ─── Test 4: atomic send claim — second concurrent claim loses ───
echo "Test 4: atomic claim — only one process can flip approved → sent\n";
$pdo->prepare("INSERT INTO outreach_leads (business_name, email, status, sent_at, unsubscribe_token) VALUES ('Smoke4', 'smoke4@example.com', 'contacted', NOW(), 'tok4')")->execute();
$leadId4 = (int) $pdo->lastInsertId();
$createdLeadIds[] = $leadId4;
$pdo->prepare("INSERT INTO outreach_followups (lead_id, touch_number, scheduled_for, status, draft_subject, draft_body) VALUES (?, 2, NOW(), 'approved', 'Re: Smoke4', 'body')")
    ->execute([$leadId4]);
$fuId = (int) $pdo->lastInsertId();

// First claim — should succeed
$claim1 = $pdo->prepare("UPDATE outreach_followups SET status = 'sent', sent_at = NOW() WHERE id = ? AND status = 'approved'");
$claim1->execute([$fuId]);
assert_true($claim1->rowCount() === 1, "First claim wins (rowCount=1)");

// Second claim — should lose (status is now 'sent', not 'approved')
$claim2 = $pdo->prepare("UPDATE outreach_followups SET status = 'sent', sent_at = NOW() WHERE id = ? AND status = 'approved'");
$claim2->execute([$fuId]);
assert_true($claim2->rowCount() === 0, "Second claim loses (rowCount=0)");

// ─── Cleanup ───
cleanup($pdo, $createdLeadIds);

echo "\n========================================\n";
echo "Result: $pass passed, $fail failed\n";
echo "========================================\n";

exit($fail === 0 ? 0 : 1);
```

- [ ] **Step 2: Run it**

```bash
php cron/test_followups_smoke.php
```

Expected: All assertions pass, exit code 0. If any fail, the test prints which assertion failed and which test it belonged to — debug from there.

- [ ] **Step 3: Stage and commit (user runs when ready)**

```bash
git add cron/test_followups_smoke.php
git commit -m "test(outreach): add follow-up smoke test script"
```

---

## Task 23: Production SQL output for user to run manually

**Files:**
- None to modify in repo. This task produces a SQL block to share with the user.

**Purpose:** Per CLAUDE.md, schema changes go in `mysql_schema.sql` AND get output as a chat-message SQL block for the user to run on production via HeidiSQL / phpMyAdmin / MySQL CLI. This task is the handoff step at the end of implementation.

- [ ] **Step 1: Final end-to-end smoke**

Run the full smoke test one more time after all prior tasks are complete:

```bash
php cron/test_followups_smoke.php
php -l cron/outreach_pipeline.php
php -l cron/lib/outreach_helpers.php
php -l cron/lib/ab_helpers.php
php -l admin/outreach/api.php
php -l admin/outreach/index.php
php -l admin/outreach/tabs/settings.php
php -l admin/outreach/tabs/followups.php
php cron/outreach_pipeline.php --dry-run
```

Expected: All `php -l` checks report no errors. `--dry-run` runs to completion with sensible step logs.

- [ ] **Step 2: Output the production SQL block in the chat**

When implementation is complete and the user has confirmed local testing is happy, share this SQL with them as a copy-paste block to run on production:

```sql
-- ────────────────────────────────────────────────────────────────────
-- Production migration for multi-touch follow-up sequence feature
-- Run via HeidiSQL or `mysql -u <user> -p argo_books < this.sql`
-- Safe to re-run — all statements are idempotent.
-- ────────────────────────────────────────────────────────────────────

-- 1. Expand outreach_ab_tests.variant_type ENUM to include followup_sequence
ALTER TABLE outreach_ab_tests
  MODIFY COLUMN variant_type ENUM('subject','body','sender','cta','preheader','format','personalization','followup_sequence') NOT NULL DEFAULT 'subject';

-- 2. Create the outreach_followups table (one row per scheduled follow-up touch)
CREATE TABLE IF NOT EXISTS outreach_followups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    touch_number TINYINT UNSIGNED NOT NULL,
    scheduled_for DATETIME NOT NULL,
    draft_subject VARCHAR(500) DEFAULT NULL,
    draft_body TEXT DEFAULT NULL,
    drafted_at DATETIME DEFAULT NULL,
    draft_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('scheduled','drafted','approved','sent','halted','skipped','failed') NOT NULL DEFAULT 'scheduled',
    halt_reason VARCHAR(100) DEFAULT NULL,
    ab_test_id INT DEFAULT NULL,
    ab_variant_id INT DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    message_id VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_lead_touch (lead_id, touch_number),
    INDEX idx_status_scheduled (status, scheduled_for),
    INDEX idx_lead (lead_id, touch_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Verify
SELECT 'outreach_followups created' AS status WHERE EXISTS (
    SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'outreach_followups'
);
SELECT 'followup_sequence in ENUM' AS status WHERE EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = DATABASE() AND table_name = 'outreach_ab_tests' AND column_name = 'variant_type'
      AND column_type LIKE '%followup_sequence%'
);
```

After running, the user should then visit `https://argorobots.com/admin/outreach/?tab=settings` once — that page load triggers the idempotent seeding of `followup_sequence_config` defaults and the A/B starter test in draft status (per Task 16).

- [ ] **Step 3: Final stage and commit (user runs when ready)**

```bash
git add docs/superpowers/specs/2026-05-17-followup-sequence-design.md docs/superpowers/plans/2026-05-17-followup-sequence.md
git commit -m "docs: add follow-up sequence spec and implementation plan"
```

---

## Spec Coverage Self-Check

Reviewing each spec section against this plan:

| Spec section | Covered by tasks |
|---|---|
| §1 Context / goals / non-goals | (informational, no code) |
| §2 Decisions table | (informational, embedded throughout) |
| §3 Data model — outreach_followups table | Task 1 |
| §3 Data model — outreach_ab_tests ENUM expansion | Task 1 |
| §3 Data model — new state keys | Task 16 (seeded) + Task 15 (POST handler writes) |
| §4 Lifecycle — schedule on first-touch send | Task 9 |
| §4 Lifecycle — lazy draft, ~1 day window | Task 11 |
| §4 Lifecycle — review gating matches send mode | Task 11 (auto → approved), Task 12 (review keeps drafted) |
| §4 Lifecycle — bulk halt | Tasks 4, 10 |
| §4 Lifecycle — threading | Task 5 (send_followup_row) |
| §4 Lifecycle — race conditions | Task 5 (atomic claim), Task 22 (smoke test 4) |
| §4 Lifecycle — Gemini failure handling | Task 3 (draft_attempts++ logic) |
| §4 Lifecycle — frozen-cadence on config change | Task 9 (rows created with current config; no retroactive code path) |
| §5 Settings tab — sequence panel | Task 15 |
| §5 Settings tab — defaults seeded | Task 16 |
| §5 Settings tab — A/B starter seeded | Task 16 (calls ensure_followup_starter_test from Task 7) |
| §6 Follow-ups tab — sub-views, rows, actions | Tasks 18, 19, 20 |
| §6 Follow-ups tab — dashboard stat | Tasks 17 (API), 19 (HTML), 20 (JS) |
| §6 Lead modal sub-tab | Task 21 |
| §7 A/B framework — new variant_type | Task 6 |
| §7 A/B framework — JSON validator | Task 6 |
| §7 A/B framework — assignment at first-touch | Task 9 |
| §7 A/B framework — per-touch intent extraction | Task 3 |
| §7 A/B framework — promotion scoring | Task 6 (comment-only, existing query already handles) |
| §7 A/B framework — rotation order | Task 6 |
| §7 A/B framework — auto-cycle from seed | Task 7 |
| §7 A/B framework — shape mismatch auto-pause | Tasks 8, 13 |
| §8 Cron pipeline step list | Tasks 9, 10, 11, 12, 13 |
| §9 Daily caps | Task 12 (limit raised), Task 11 (new DRAFT_LIMIT) |
| §10 Error handling table | Distributed across Tasks 3, 5, 9, 10, 11, 12 |
| §11 Testing — smoke script | Task 22 |
| §12 Removed code | Task 14 |
| §13 Migration / deployment notes | Tasks 1, 23 |
| §14 Future work (out of scope) | N/A |

No gaps identified.

---

## Execution Handoff

Plan complete and saved to `docs/superpowers/plans/2026-05-17-followup-sequence.md`. Two execution options:

**1. Subagent-Driven (recommended)** — I dispatch a fresh subagent per task, review the diff between tasks, and iterate. Good for plans this size because each task ends up reviewed before the next starts.

**2. Inline Execution** — Execute tasks in this session using executing-plans, batching with checkpoints for your review.

Which approach?
