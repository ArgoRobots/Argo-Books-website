<?php
/**
 * Shared pure-PHP helpers for the A/B testing subsystem.
 *
 * Used by:
 *   - admin/outreach/tabs/ab-tests.php  (UI partial)
 *   - cron/lib/outreach_helpers.php     (automation: stepManageAbTests)
 *
 * All functions here are side-effect-free. DB access lives in outreach_helpers.
 */

if (defined('OUTREACH_AB_HELPERS_LOADED')) return;
define('OUTREACH_AB_HELPERS_LOADED', true);

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

/**
 * Two-proportion z-test between a leader variant and another variant.
 * Returns an advisory tag + label for display.
 *
 * 'insufficient' — not enough data, or tied
 * 'trending'     — z >= 1.28 (~80% one-sided)
 * 'significant'  — z >= 1.96 (~95% two-sided)
 *
 * Fewer than 30 sends on either side is always reported as insufficient.
 */
function confidence_vs_leader($leaderSent, $leaderClicks, $otherSent, $otherClicks)
{
    if ($leaderSent < 30 || $otherSent < 30) {
        return ['tag' => 'insufficient', 'label' => 'Low sample'];
    }
    $p1 = $leaderClicks / $leaderSent;
    $p2 = $otherClicks / $otherSent;
    if ($p1 == $p2) {
        return ['tag' => 'insufficient', 'label' => 'Tied'];
    }
    $pPool = ($leaderClicks + $otherClicks) / ($leaderSent + $otherSent);
    $se = sqrt($pPool * (1 - $pPool) * (1 / $leaderSent + 1 / $otherSent));
    if ($se <= 0) {
        return ['tag' => 'insufficient', 'label' => 'Tied'];
    }
    $z = abs(($p1 - $p2) / $se);
    if ($z >= 1.96) {
        return ['tag' => 'significant', 'label' => 'Significant (95%)'];
    }
    if ($z >= 1.28) {
        return ['tag' => 'trending', 'label' => 'Trending (80%)'];
    }
    return ['tag' => 'insufficient', 'label' => 'Not significant'];
}

/**
 * Format a CTR (0.0–1.0 ratio) as a "42.1%" string, or "—" if nothing was sent.
 */
function format_ctr($sent, $clicks)
{
    if ($sent <= 0) return '—';
    return number_format(($clicks / $sent) * 100, 1) . '%';
}

/**
 * Pull sends/clicks/opens/bounces/replies/assigned counts for every variant
 * of a test. Returns each variant row augmented with 'assigned_count',
 * 'sent_count', 'clicked_count', 'opened_count', 'bounced_count',
 * 'replied_count', 'ctr', 'open_rate', 'bounce_rate', 'reply_rate'.
 *
 * 'opened_count' / 'bounced_count' come from outreach_email_events
 * (populated by webhooks/resend.php) and count DISTINCT leads, so a single
 * lead opening multiple times is one open. They will be zero for any test
 * that ran before the Resend webhook was wired up, since events are only
 * recorded going forward.
 *
 * 'replied_count' counts leads whose status indicates a positive human
 * response (replied / interested / onboarded). 'not_interested' is excluded
 * because the unsubscribe flow can flip a lead to that status without it
 * being a real reply, which would inflate variants whose recipients
 * unsubscribed.
 */
function load_variants_with_stats($pdo, $testId)
{
    $stmt = $pdo->prepare("
        SELECT
            v.*,
            (SELECT COUNT(*) FROM outreach_leads ol WHERE ol.ab_variant_id = v.id) AS assigned_count,
            (SELECT COUNT(*) FROM outreach_leads ol WHERE ol.ab_variant_id = v.id AND ol.sent_at IS NOT NULL) AS sent_count,
            (SELECT COUNT(DISTINCT ol.id)
                FROM outreach_leads ol
                JOIN referral_visits rv
                  ON rv.source_code = CONCAT('outreach-', ol.id, '-v', v.id)
                WHERE ol.ab_variant_id = v.id) AS clicked_count,
            (SELECT COUNT(DISTINCT ol.id)
                FROM outreach_leads ol
                JOIN outreach_email_events e
                  ON e.lead_id = ol.id AND e.event_type = 'opened'
                WHERE ol.ab_variant_id = v.id) AS opened_count,
            (SELECT COUNT(DISTINCT ol.id)
                FROM outreach_leads ol
                JOIN outreach_email_events e
                  ON e.lead_id = ol.id AND e.event_type = 'bounced'
                WHERE ol.ab_variant_id = v.id) AS bounced_count,
            (SELECT COUNT(*) FROM outreach_leads ol
                WHERE ol.ab_variant_id = v.id
                  AND ol.status IN ('replied','interested','onboarded')) AS replied_count
        FROM outreach_ab_variants v
        WHERE v.test_id = ?
        ORDER BY v.id ASC
    ");
    $stmt->execute([$testId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$v) {
        $v['assigned_count'] = (int) $v['assigned_count'];
        $v['sent_count']     = (int) $v['sent_count'];
        $v['clicked_count']  = (int) $v['clicked_count'];
        $v['opened_count']   = (int) $v['opened_count'];
        $v['bounced_count']  = (int) $v['bounced_count'];
        $v['replied_count']  = (int) $v['replied_count'];
        $v['ctr']            = $v['sent_count'] > 0 ? $v['clicked_count'] / $v['sent_count'] : 0.0;
        $v['open_rate']      = $v['sent_count'] > 0 ? $v['opened_count']  / $v['sent_count'] : 0.0;
        $v['bounce_rate']    = $v['sent_count'] > 0 ? $v['bounced_count'] / $v['sent_count'] : 0.0;
        $v['reply_rate']     = $v['sent_count'] > 0 ? $v['replied_count'] / $v['sent_count'] : 0.0;
    }
    // (followup_sequence variants use the same lead-level counts: the unit
    // of randomization is the lead, and reply/click attribution is at the
    // lead level regardless of which touch produced the response.)
    return $rows;
}

/**
 * Find the leader index by the given metric (default 'ctr', also accepts
 * 'reply_rate'). Returns null if no variant has any sends yet. Ties broken
 * by lowest id.
 */
function find_leader_idx($variants, $metricKey = 'ctr')
{
    $leaderIdx = null;
    foreach ($variants as $i => $v) {
        if ($v['sent_count'] === 0) continue;
        if ($leaderIdx === null || $v[$metricKey] > $variants[$leaderIdx][$metricKey]) {
            $leaderIdx = $i;
        }
    }
    return $leaderIdx;
}

/**
 * Two-proportion z-test on a chosen metric. Picks the right
 * (count, sent) pair from the variant rows and delegates to
 * confidence_vs_leader(). $metric is 'ctr' or 'reply_rate'.
 */
function confidence_vs_leader_on($metric, $leader, $other)
{
    $countKey = $metric === 'reply_rate' ? 'replied_count' : 'clicked_count';
    return confidence_vs_leader(
        $leader['sent_count'], $leader[$countKey],
        $other['sent_count'],  $other[$countKey]
    );
}

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

/**
 * Checks the active followup_sequence A/B test (if any) against the current
 * followup_sequence_config. If any variant's touch list doesn't match the
 * config's touch list, pauses the test. Caller (stepManageAbTests) surfaces
 * the reason via logPipeline.
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
            return ['action' => 'paused', 'reason' => $reason];
        }
    }

    return ['action' => 'ok', 'reason' => null];
}
