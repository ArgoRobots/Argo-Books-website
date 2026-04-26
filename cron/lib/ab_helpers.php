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
 * Pull sends/clicks/replies/assigned counts for every variant of a test.
 * Returns each variant row augmented with 'assigned_count', 'sent_count',
 * 'clicked_count', 'replied_count', 'ctr', 'reply_rate'.
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
        $v['replied_count']  = (int) $v['replied_count'];
        $v['ctr']            = $v['sent_count'] > 0 ? $v['clicked_count'] / $v['sent_count'] : 0.0;
        $v['reply_rate']     = $v['sent_count'] > 0 ? $v['replied_count'] / $v['sent_count'] : 0.0;
    }
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
