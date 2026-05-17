# Multi-touch Follow-up Sequence — Design Spec

**Date:** 2026-05-17
**Status:** Approved for implementation planning
**Author:** Brainstormed with Evan (argorobots)

## 1. Context

### Current state

The outreach pipeline (`cron/outreach_pipeline.php`) sends a single follow-up email 5 days after the first-touch cold email. The follow-up uses a static template (`cron/lib/followup_template.php`) — no AI personalization, identical body for every lead. It is gated by the existing Auto-send vs Review-before-send toggle in admin Settings, but in Review mode follow-ups are **skipped entirely** with no UI surface to approve them. The cap is hard-coded to 1 follow-up per lead via the eligibility query `followup_count = 0`.

### Why we are changing it

- Reply-rate analysis on the existing 950 cold emails (1 mild interest, ~0.1%) suggests the single-touch model is a major contributor to low conversion. Industry data shows touches 2-5 collectively produce ~50-70% of all replies a cold-email sequence will ever generate.
- Review-before-send mode is the operator's preferred setting but currently disables follow-ups entirely — operationally broken.
- The static template means every follow-up reads identically, hurting deliverability over time and giving no per-lead relevance signal.

### Goals

1. Multi-touch follow-up sequences (configurable count and per-touch gaps).
2. Each follow-up draft personalized by Gemini.
3. Reviewable in a new admin tab when in Review-before-send mode.
4. Sequence strategy A/B-testable using the existing A/B framework, with starter variants shipped out of the box.

### Non-goals (v1)

- Per-touch fully-custom Gemini prompt templates (we settled on shared base prompt + per-touch one-line intent).
- Mid-sequence config retroactive application to in-flight leads (frozen-cadence policy).
- A "regenerate full sequence" UI action on existing leads.
- PHPUnit test suite (project does not currently use one; we match existing manual-smoke conventions).
- Predictive engagement-based scheduling (e.g. "delay next touch if recipient opened today").

## 2. Decisions captured during brainstorming

| Question | Decision |
|---|---|
| Cadence model | Per-touch gaps configurable in Settings (count + days_after_prev per touch). |
| Draft timing | Lazy — Gemini drafts each touch ~1 day before its scheduled send. |
| Review gating | Matches the existing Auto-send vs Review-before-send toggle (no separate follow-up-specific toggle). |
| Halt conditions | Replied, unsubscribed, bounce/spam complaint. Open-without-reply does NOT halt. |
| A/B unit | Whole sequence strategy. New `variant_type='followup_sequence'`, each variant defines per-touch intents covering all configured touches. Lead assigned at first-touch and locked through the sequence. |
| Storage | New `outreach_followups` table (one row per scheduled touch). |
| Existing follow-up code | Fully replaced — `cron/lib/followup_template.php` deleted, `send_outreach_followup()` rewritten. |
| Migration | Forward-only — existing leads with `followup_count > 0` (sent under old system) are not retroactively scheduled for additional touches. |

## 3. Data model

### New table: `outreach_followups`

```sql
CREATE TABLE IF NOT EXISTS outreach_followups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lead_id INT NOT NULL,
    touch_number TINYINT UNSIGNED NOT NULL,          -- 2, 3, 4, ... (touch 1 lives in outreach_leads)
    scheduled_for DATETIME NOT NULL,
    draft_subject VARCHAR(500) DEFAULT NULL,
    draft_body TEXT DEFAULT NULL,
    drafted_at DATETIME DEFAULT NULL,
    draft_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('scheduled','drafted','approved','sent','halted','skipped','failed') NOT NULL DEFAULT 'scheduled',
    halt_reason VARCHAR(100) DEFAULT NULL,           -- 'replied','unsubscribed','bounced','manual','max_reached',NULL
    ab_test_id INT DEFAULT NULL,
    ab_variant_id INT DEFAULT NULL,
    sent_at DATETIME DEFAULT NULL,
    message_id VARCHAR(255) DEFAULT NULL,            -- used to thread the NEXT touch
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES outreach_leads(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_lead_touch (lead_id, touch_number),
    INDEX idx_status_scheduled (status, scheduled_for),
    INDEX idx_lead (lead_id, touch_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Existing schema changes

- **`outreach_ab_tests.variant_type`** ENUM: add `'followup_sequence'`.
  - Existing-install ALTER: documented in `mysql_schema.sql` comment block alongside the table.
- **`outreach_leads`**: no schema change. Existing columns `followup_count`, `last_followup_at`, `next_followup_due_at` are kept for backward-compatibility with legacy queries and dashboard stats but are no longer the source of truth (`outreach_followups` rows are).

### New `outreach_pipeline_state` keys

| Key | Value format | Default seeded on first deploy |
|---|---|---|
| `followup_sequence_config` | JSON array of `{touch, days_after_prev, default_intent}` | `[{"touch":2,"days_after_prev":3,"default_intent":"gentle bump"},{"touch":3,"days_after_prev":7,"default_intent":"different angle"},{"touch":4,"days_after_prev":14,"default_intent":"final note before closing"}]` |

## 4. Lifecycle

### State machine per `outreach_followups` row

```
scheduled  ──draft window opens──►  drafted   ──admin approves──►  approved  ──cron sends──►  sent
    │                                  │                                                        │
    │                                  └──auto-send mode: skip review, go straight to approved──┘
    │
    └── (any pre-sent state) ──halt condition fires──► halted (halt_reason set)
    │
    └── admin clicks "Skip" ──► skipped
    │
    └── Gemini fails 3 times ──► failed
```

### Flow

1. **First-touch send completes** (`send_outreach_lead` returns success). New code path inserts N rows into `outreach_followups` for the lead — one per touch position from the active `followup_sequence_config`. Each row's `scheduled_for` is computed cumulatively from `first_send_time + sum(days_after_prev_up_to_and_including_this_touch)`. The lead's assigned `ab_variant_id` (if any, from the active `followup_sequence` test) is copied onto each row.

2. **Draft window opens** (`scheduled_for - 1 day <= NOW()`). `stepDraftFollowups` calls Gemini for each scheduled row, sets `draft_subject` / `draft_body` / `drafted_at`, flips to `drafted`. In Auto-send mode, immediately advances to `approved` in the same pass.

3. **Review** (Review-before-send mode only). Admin sees the row in the new Follow-ups tab, clicks Approve. Status flips to `approved`.

4. **Send** (`scheduled_for <= NOW()` AND `status='approved'`). `stepSendFollowups` claims atomically, calls SMTP, sets `sent_at` and `message_id`, flips to `sent`.

5. **Halt** (any time before send). `stepHaltFollowups` runs on each cron tick and bulk-updates pre-sent rows to `halted` for leads whose status flipped to a halt-condition value, or whose email is in `email_suppressions`.

### Threading

Each touch's `In-Reply-To` and `References` headers point to the **previous touch's** `message_id` (or the first-touch `message_id` from `outreach_leads.original_message_id` if this is touch 2). Keeps the entire follow-up sequence in a single inbox thread.

### Race conditions

- Cron-level: `flock` lock file `cron/logs/outreach_pipeline.lock` prevents concurrent pipeline runs (already in place). This is the primary defence — the draft step doesn't need row-level claim because re-drafting the same row is idempotent (latest Gemini result overwrites; `draft_attempts++` happens once per attempt regardless).
- Row-level (send step): atomic claim `UPDATE outreach_followups SET status='sent', sent_at=NOW() WHERE id=? AND status='approved'`. If `rowCount() == 0`, another process won — skip silently. The send step needs row-level protection because the cost of double-sending an email is real (recipient receives the same follow-up twice).

### Gemini failure handling

- Errors during draft: leave row at `scheduled`, increment `draft_attempts`. Next cron tick retries.
- After 3 consecutive failed attempts: flip to `failed`. Surfaces in the Halted/failed sub-view of the Follow-ups tab for admin attention. Does not affect remaining touches in the sequence (touch N+1 still drafts independently when its window opens).

### Mid-sequence config changes

- Sequence config (touch count, gap days, default intents) edits in Settings apply to **new first-touch sends only**.
- Already-scheduled `outreach_followups` rows keep their original `scheduled_for` and existing position in the sequence.
- A/B test shape change (admin adds touch 4 to the sequence config but the active `followup_sequence` test has only 3 touches' worth of intents): the test is auto-paused. Admin sees a warning in the A/B Tests tab. Either recreate the test against the new shape, or revert the config change.

## 5. Settings tab additions

New panel in `admin/outreach/tabs/settings.php`, below the existing "A/B automation" panel:

```
┌─ Follow-up sequence ──────────────────────────────────────┐
│ The pipeline drafts each follow-up ~1 day before its       │
│ scheduled send. Drafts queue in the Follow-ups tab when    │
│ Review-before-send is on; otherwise they auto-send.        │
│                                                            │
│ Touch │ Days after previous touch │ Default intent (used   │
│       │                           │  if A/B inactive)      │
│  2    │ [ 3 ]                     │ [ gentle bump        ] │
│  3    │ [ 7 ]                     │ [ different angle    ] │
│  4    │ [ 14 ]                    │ [ final note before  ] │
│                                   │   closing             │
│                                                            │
│ [ + Add touch ]   [ Remove last touch ]                    │
│                                                            │
│ [ Save sequence ]                                          │
└────────────────────────────────────────────────────────────┘
```

### Validation

- `days_after_prev`: integer 1-90.
- Touch count: 0-6 inclusive. Zero = follow-ups disabled.
- `default_intent`: plain text, max 200 chars, no HTML, fed verbatim to Gemini.

### Saved as

`outreach_pipeline_state.followup_sequence_config` (JSON, see §3).

### Defaults seeded on first deploy

- 3 follow-up touches at +3, +7, +14 days.
- Default intents: "gentle bump", "different angle", "final note before closing".

### A/B starter variants seeded on first deploy

A `followup_sequence` test is auto-created in **`status='draft'`** (NOT auto-activated — admin reviews and activates from the A/B Tests tab):

| Variant label | Touch 2 intent | Touch 3 intent | Touch 4 intent |
|---|---|---|---|
| Bump-Reframe-Close | gentle bump | different angle, offer concrete example | final note before closing |
| Value-Question-Close | helpful tip relevant to their business | open-ended question about their pain point | final note before closing |
| Persistent Bump | gentle bump | gentle bump (slightly different wording) | gentle bump (slightly different wording) |

## 6. Follow-ups tab UI

New tab in `admin/outreach/index.php` between **A/B Tests** and **Settings**:

```
[ Leads ]  [ A/B Tests ]  [ Follow-ups ]  [ Settings ]
```

### Sub-views

| Pill | Filter | Default view |
|---|---|---|
| Pending review | `status='drafted'` AND `scheduled_for` within next 2 days | ✓ |
| Approved & queued | `status='approved'` | |
| Upcoming | `status='scheduled'` | |
| Sent | `status='sent'` ORDER BY `sent_at DESC` LIMIT 30 days | |
| Halted / failed | `status IN ('halted','failed','skipped')` LIMIT 30 days | |

Pending-review pill carries a count badge (the actionable queue).

### Row shape (Pending review)

```
┌──────────────────────────────────────────────────────────────────────────┐
│ Acme Coffee (Saskatoon)        Touch 2/3 · Scheduled in 18 hours         │
│ Subject: Re: Quick question about Acme Coffee's bookkeeping              │
│ ┌────────────────────────────────────────────────────────────────────┐   │
│ │ Hi Acme Coffee team, [editable body]                                │   │
│ └────────────────────────────────────────────────────────────────────┘   │
│                                                                          │
│ A/B: Bump-Reframe-Close · Intent: "different angle, offer concrete example" │
│ Original first-touch: [ view ]    Activity: [ view lead ]                │
│                                                                          │
│ [ Approve & queue ]   [ Regenerate draft ]   [ Skip this touch ]   [ Halt sequence ] │
└──────────────────────────────────────────────────────────────────────────┘
```

### Row actions

| Action | Behavior |
|---|---|
| Approve & queue | `status='approved'`. Cron sends at next eligible tick. |
| Regenerate draft | Re-calls Gemini, replaces subject/body. Status stays `drafted`. |
| Skip this touch | `status='skipped'`, `halt_reason='manual'`. Next touch's `scheduled_for` is unchanged (gap measured from previous *attempted* touch, not previous *sent* touch). |
| Halt sequence | Bulk-halts this and all later pre-sent touches for this lead. `halt_reason='manual'`. |

### Bulk actions (checkbox selection)

- Approve selected
- Skip selected
- Halt sequences for selected leads

### Lead detail modal integration

The existing lead detail modal gets a new sub-tab **"Follow-ups"** showing the per-lead sequence: each touch with status, scheduled date, sent date, halt reason. Enables drill-in from either direction.

### Dashboard stats row

One new stat card in `admin/outreach/index.php`: **"Follow-ups pending review"** (count of `outreach_followups WHERE status='drafted'`).

## 7. A/B framework integration

### Variant content format

A `followup_sequence` variant's `outreach_ab_variants.content` field holds JSON:

```json
[
  {"touch": 2, "intent": "gentle bump"},
  {"touch": 3, "intent": "different angle, offer concrete example"},
  {"touch": 4, "intent": "final note before closing"}
]
```

The intent count must match the current `followup_sequence_config` touch count. Mismatch auto-pauses the test (see §4 Mid-sequence config changes). Each per-touch `intent` field is plain text, max 200 chars, fed verbatim to Gemini (same constraint as `default_intent` in §5).

### Assignment

Happens at first-touch send time. The lead's `ab_variant_id` from the active `followup_sequence` test is copied onto each of the N `outreach_followups` rows when they're created. All follow-ups for one lead use the same variant — we're testing strategies, not arbitrary mixes.

### Per-touch intent extraction at draft time

`stepDraftFollowups`, when generating touch N for a row:
1. Reads the row's `ab_variant_id`.
2. If non-null: fetches variant, parses JSON `content`, picks entry where `touch == N`, extracts `intent`.
3. If null: falls back to `default_intent` from `followup_sequence_config[N]`.
4. Passes the intent into the Gemini prompt as `{follow_up_intent}` along with `{touch_number}`, `{total_touches}`, lead/business context, original first-touch subject and body (so Gemini can vary naturally from the original).

### Promotion scoring

Reuses `ab_check_and_promote_active_test()` in `cron/lib/ab_helpers.php`. Reply rate primary, CTR fallback. New wrinkle: a `followup_sequence` variant's "sent count" is the number of *leads* assigned to it whose first-touch went out (the unit of randomization), not the count of follow-up emails sent. Replies/clicks attributed at the lead level as today. `load_variants_with_stats` needs a minor branch for `followup_sequence` typing.

### Mid-sequence variant promotion

If a `followup_sequence` test gets promoted while leads are mid-sequence, those leads keep their originally-assigned variant. Same logic as existing first-touch A/B promotion in-flight behavior.

### Rotation order

`ab_auto_rotation_order()` returns `['subject', 'sender', 'format', 'personalization']` today; append `'followup_sequence'`.

### Auto-cycle generation

Unlike `subject` (AI-generated variants) or `sender` (fixed pool), `followup_sequence` reuses the three seed variants from §5 in a round-robin fashion rather than generating new variants via Gemini. Same pattern as `sender`.

## 8. Cron pipeline changes

Full updated pipeline step list in `cron/outreach_pipeline.php`:

| # | Step | Change |
|---|---|---|
| 1 | Discovery (Google Places) | unchanged |
| 2.5 | Manage A/B Tests | minor: handles `followup_sequence` type in rotation + auto-pause-on-shape-mismatch logic |
| 3 | Generate first-touch AI drafts | unchanged (but now subject to new `OUTREACH_DAILY_DRAFT_LIMIT` shared budget) |
| 4 | Auto-approve first-touch drafts | unchanged |
| 5 | Send first-touch emails | **modified**: after success, insert N `outreach_followups` rows |
| **5.5** | **Halt follow-ups** (new) | bulk-halts pre-sent rows for leads whose status flipped or are in suppressions |
| **5.6** | **Draft follow-ups** (new) | Gemini-drafts rows where `status='scheduled' AND scheduled_for <= NOW() + 1 day`; in auto-send mode, advances to `approved` |
| **6** | **Send approved follow-ups** (rewritten) | queries `outreach_followups WHERE status='approved' AND scheduled_for <= NOW()` |

## 9. Daily caps

| Cap | Default | Notes |
|---|---|---|
| `OUTREACH_DAILY_SEND_LIMIT` | 25 | First-touch only. Unchanged. |
| `OUTREACH_DAILY_FOLLOWUP_LIMIT` | 75 (raised from 30) | All touch positions combined per day. Configurable via env. |
| `OUTREACH_DAILY_DRAFT_LIMIT` (new) | 100 | Gemini calls per cron run. Shared budget across step 3 (first-touch drafts) and step 5.6 (follow-up drafts). |

## 10. Error handling

| Failure | Behavior |
|---|---|
| Gemini draft errors | Row stays `scheduled`, `draft_attempts++`. After 3 attempts: `status='failed'`, surfaces in Halted/failed sub-view. Does not block remaining touches for the lead. |
| SMTP send fails | Atomic claim rolled back; row returns to `approved`; cron retries next run. |
| Cron overlap | `flock` lock file prevents concurrent runs (existing). Per-row atomic UPDATEs are the backstop. |
| Admin changes sequence config mid-flight | Existing rows frozen at old cadence; new first-touch sends use new config. |
| `followup_sequence` A/B variant shape ≠ config touch count | Test auto-paused; admin warned in A/B Tests tab. |
| Lead deleted | `ON DELETE CASCADE` removes all `outreach_followups` rows. |
| Lead manually flipped to halt-condition status from admin UI | Picked up by `stepHaltFollowups` on next tick. |

## 11. Testing

No PHPUnit suite exists in the repo. Match existing convention with manual `--dry-run` plus a new CLI smoke script `cron/test_followups_smoke.php`:

1. Creates fake lead with `status='contacted'`, runs `--dry-run`, asserts N `outreach_followups` rows would be created.
2. Inserts a `scheduled` row with `scheduled_for=NOW()`, asserts draft step picks it up and (with a mocked Gemini call returning fixed text) populates draft fields.
3. Inserts an `approved` row whose lead has `status='replied'`, asserts `stepHaltFollowups` halts it before the send step picks it up.
4. Inserts two `approved` rows for the same lead, asserts only one wins under simulated concurrent claim.

Exits non-zero on any assertion failure. Run via `php cron/test_followups_smoke.php`.

## 12. Removed code

- `cron/lib/followup_template.php` — deleted.
- Old `stepSendFollowups` body in `cron/outreach_pipeline.php` (the existing 780-856 block) — replaced.
- Old `send_outreach_followup()` in `cron/lib/outreach_helpers.php` (lines 431-520) — replaced by a new `send_followup_row()` that takes an `outreach_followups` row.

## 13. Migration / deployment notes

1. Schema changes are additive: new table + ENUM expansion. Existing rows untouched.
2. Schema SQL goes in `mysql_schema.sql` with an existing-install ALTER block in a SQL comment, per CLAUDE.md convention (no migration files).
3. On first deploy: seed `followup_sequence_config` state with defaults; seed the three A/B starter variants as a `status='draft'` test.
4. No retroactive scheduling for leads with `followup_count > 0` (these received their one follow-up under the old system).
5. Cron schedule unchanged — pipeline runs daily at 8 AM, new steps slot into existing run.

## 14. Future work (out of scope for v1)

- Per-touch fully-custom Gemini prompt templates in Settings.
- "Regenerate full sequence for selected leads" bulk action.
- Engagement-aware scheduling (delay touch N+1 if recipient opened touch N today).
- Reply-pattern analysis (e.g., flag leads who reply to touch 3 specifically — those subjects/intents are working).
- A/B testing the `default_intent` per touch independently of full-sequence variants.
