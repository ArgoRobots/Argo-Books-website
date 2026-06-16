<?php
/**
 * Affiliate program helpers: commission math, source-code generation, and the
 * per-affiliate stat queries that power both the affiliate dashboard and the
 * admin section. Including this file twice is safe (function_exists guards).
 *
 * Commission model: nothing about earned commission is stored. It is computed
 * on the fly from premium_subscription_payments within each subscription's
 * commission window. The only persisted money is affiliate_payouts (what was
 * actually paid out). owed = earned - paid. A refunded payment simply stops
 * counting, so refunds self-correct with no clawback bookkeeping.
 */

require_once __DIR__ . '/../../db_connect.php';

if (!function_exists('compute_commission')) {
    /**
     * Pure commission math (no DB) so it can be unit-tested in isolation.
     *
     * Given the completed payments for ONE referred subscription and that
     * subscription's start_date, return the commission earned: rate × the sum
     * of payment amounts that fall within [start_date, start_date + window).
     *
     * @param array  $payments   Each: ['amount' => float|string, 'status' => string,
     *                            'created_at' => 'Y-m-d H:i:s', 'environment' => string]
     * @param string $start_date Subscription start_date ('Y-m-d H:i:s')
     * @param float  $rate       e.g. 0.5 for 50%
     * @param int    $window_months Commission window length in months
     * @param string $environment   Only payments in this environment count
     * @return float Commission earned, rounded to 2 decimals
     */
    function compute_commission(array $payments, string $start_date, float $rate, int $window_months, string $environment): float
    {
        $start = strtotime($start_date);
        if ($start === false) {
            return 0.0;
        }
        // Anchor the window to the subscription's own start_date. strtotime with
        // a relative "+N months" matches MySQL DATE_ADD(..., INTERVAL N MONTH).
        $window_end = strtotime('+' . $window_months . ' months', $start);

        $base = 0.0;
        foreach ($payments as $p) {
            if (($p['status'] ?? '') !== 'completed') {
                continue; // excludes pending / failed / refunded
            }
            if (($p['environment'] ?? '') !== $environment) {
                continue;
            }
            $when = strtotime($p['created_at'] ?? '');
            if ($when === false || $when < $start || $when >= $window_end) {
                continue; // outside the per-subscription commission window
            }
            $base += (float) $p['amount'];
        }

        return round($base * $rate, 2);
    }
}

if (!function_exists('affiliate_earned_for_source')) {
    /**
     * Total commission earned across every subscription attributed to a
     * source_code, windowed per-subscription to its own start_date.
     *
     * Mirrors the revenue join in admin/marketing-funnel/index.php
     * (get_funnel_per_source) but adds the 12-month window + self-referral
     * guard. Attribution is via the premium_signup event's subscription_id;
     * the money comes from premium_subscription_payments (the source of truth),
     * so renewals inside the window are captured even though the join is on
     * payments rather than premium_paid events.
     *
     * @param int $affiliate_user_id community_users.id of the affiliate, so
     *                               their own purchases never earn commission.
     */
    function affiliate_earned_for_source(string $source_code, float $rate, int $window_months, int $affiliate_user_id, string $environment): float
    {
        global $pdo;

        $sql = "
            SELECT COALESCE(SUM(p.amount), 0) AS earned_base
            FROM (
                SELECT DISTINCT subscription_id
                FROM referral_events
                WHERE event_type = 'premium_signup'
                  AND source_code = :src
                  AND environment = :env
                  AND subscription_id IS NOT NULL
            ) sub
            JOIN premium_subscriptions ps
              ON ps.subscription_id = sub.subscription_id
             AND ps.environment = :env2
             AND (ps.user_id IS NULL OR ps.user_id <> :aff_user)
            JOIN premium_subscription_payments p
              ON p.subscription_id = sub.subscription_id
             AND p.status = 'completed'
             AND p.environment = :env3
             AND p.created_at >= ps.start_date
             AND p.created_at <  DATE_ADD(ps.start_date, INTERVAL :win MONTH)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':src'      => $source_code,
            ':env'      => $environment,
            ':env2'     => $environment,
            ':env3'     => $environment,
            ':aff_user' => $affiliate_user_id,
            ':win'      => $window_months,
        ]);
        $base = (float) ($stmt->fetchColumn() ?: 0);

        return round($base * $rate, 2);
    }
}

if (!function_exists('affiliate_total_paid')) {
    /** Sum of payouts already recorded for an affiliate (current environment). */
    function affiliate_total_paid(int $affiliate_id, string $environment): float
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM affiliate_payouts WHERE affiliate_id = ? AND environment = ?');
        $stmt->execute([$affiliate_id, $environment]);
        return round((float) $stmt->fetchColumn(), 2);
    }
}

if (!function_exists('affiliate_money_summary')) {
    /**
     * Earned / paid / owed for one affiliate row (as returned by
     * get_affiliate_for_user / the admin list).
     *
     * @return array{earned: float, paid: float, owed: float}
     */
    function affiliate_money_summary(array $affiliate, string $environment): array
    {
        $rate   = (float) $affiliate['commission_rate'];
        $window = (int) $affiliate['commission_window_months'];
        $earned = affiliate_earned_for_source($affiliate['source_code'], $rate, $window, (int) $affiliate['user_id'], $environment);
        $paid   = affiliate_total_paid((int) $affiliate['id'], $environment);
        // owed never goes negative (an over-payment shows as owed 0, not a debt).
        $owed   = max(0.0, round($earned - $paid, 2));
        return ['earned' => $earned, 'paid' => $paid, 'owed' => $owed];
    }
}

if (!function_exists('get_affiliate_for_user')) {
    /** The affiliate row for a community user in the current environment, or null. */
    function get_affiliate_for_user(int $user_id, string $environment): ?array
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM affiliates WHERE user_id = ? AND environment = ?');
        $stmt->execute([$user_id, $environment]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

if (!function_exists('get_affiliate_by_id')) {
    function get_affiliate_by_id(int $id, string $environment): ?array
    {
        global $pdo;
        $stmt = $pdo->prepare('SELECT * FROM affiliates WHERE id = ? AND environment = ?');
        $stmt->execute([$id, $environment]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}

if (!function_exists('get_affiliate_stats')) {
    /**
     * Funnel counts for one affiliate's source_code in the current environment:
     * clicks (referral_visits), plus distinct-visitor counts per funnel stage
     * (referral_events). Shapes mirror admin/referral-links + marketing-funnel.
     *
     * @return array{clicks:int, signups:int, paying:int}
     */
    function get_affiliate_stats(string $source_code, string $environment): array
    {
        global $pdo;

        // Clicks: every recorded visit to this source. referral_visits is not
        // environment-scoped (no column), so this counts all-time clicks; the
        // money figures above are the env-scoped numbers that matter.
        $clicks_stmt = $pdo->prepare('SELECT COUNT(*) FROM referral_visits WHERE source_code = ?');
        $clicks_stmt->execute([$source_code]);
        $clicks = (int) $clicks_stmt->fetchColumn();

        $ev_stmt = $pdo->prepare("
            SELECT
              COUNT(DISTINCT CASE WHEN event_type = 'premium_signup' THEN visitor_id END) AS signups,
              COUNT(DISTINCT CASE WHEN event_type = 'premium_paid'
                                   AND JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.payment_type')) = 'initial'
                                  THEN subscription_id END) AS paying
            FROM referral_events
            WHERE source_code = ? AND environment = ?");
        $ev_stmt->execute([$source_code, $environment]);
        $ev = $ev_stmt->fetch() ?: ['signups' => 0, 'paying' => 0];

        return [
            'clicks'  => $clicks,
            'signups' => (int) $ev['signups'],
            'paying'  => (int) $ev['paying'],
        ];
    }
}

if (!function_exists('generate_affiliate_source_code')) {
    /**
     * Build a unique, URL-safe source_code for an affiliate from their username.
     *
     * Format: aff-{sanitized-username}, lowercased, non [a-z0-9-] stripped,
     * truncated to fit VARCHAR(50). On collision (or empty username) a short
     * random hex suffix is appended. The "aff-" prefix namespaces affiliate
     * codes in every existing dashboard and avoids the "google-ads-" gclid
     * gate in track_referral.php.
     *
     * @param callable|null $exists Optional predicate (string):bool used to test
     *                              uniqueness; defaults to a DB lookup. Injected
     *                              for unit testing without a database.
     */
    function generate_affiliate_source_code(string $username, ?callable $exists = null): string
    {
        if ($exists === null) {
            $exists = function (string $code): bool {
                global $pdo;
                $stmt = $pdo->prepare('SELECT 1 FROM referral_links WHERE source_code = ? UNION SELECT 1 FROM affiliates WHERE source_code = ? LIMIT 1');
                $stmt->execute([$code, $code]);
                return (bool) $stmt->fetchColumn();
            };
        }

        $slug = strtolower($username);
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'user';
        }

        $prefix = 'aff-';
        $max = 50;

        // Base candidate, truncated to leave headroom for a possible suffix.
        $base = $prefix . substr($slug, 0, $max - strlen($prefix));
        $base = rtrim($base, '-');

        if (!$exists($base)) {
            return $base;
        }

        // Collision: append "-XXXX" (4 hex), trimming the slug so the total
        // still fits in 50 chars. Try a handful of random suffixes.
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $suffix = '-' . substr(bin2hex(random_bytes(4)), 0, 4);
            $room = $max - strlen($prefix) - strlen($suffix);
            $candidate = $prefix . rtrim(substr($slug, 0, max(1, $room)), '-') . $suffix;
            if (!$exists($candidate)) {
                return $candidate;
            }
        }

        // Extremely unlikely fallback: prefix + 8 random hex.
        return $prefix . substr(bin2hex(random_bytes(8)), 0, $max - strlen($prefix));
    }
}

if (!function_exists('affiliate_referral_url')) {
    /** The shareable referral URL an affiliate promotes. */
    function affiliate_referral_url(string $source_code): string
    {
        return 'https://argorobots.com/?source=' . rawurlencode($source_code);
    }
}
