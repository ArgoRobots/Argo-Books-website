<?php
/**
 * Marketing-funnel analytics data layer.
 *
 * Powers the Plausible-style dashboard on the Funnel tab: the channel donut,
 * the referrer / campaign / keyword bar lists, and the map / country / region /
 * city bar lists. Every breakdown carries both a visits count and attributed
 * revenue so the UI can draw the twin blue (visits) + orange (revenue) bars.
 *
 * Attribution model:
 *   - Visits are distinct visitors with a `landing` event in the period,
 *     grouped by the dimension recorded on that landing row.
 *   - Revenue is first-touch: each paying subscription's revenue is credited to
 *     the referer host / keyword captured on that visitor's earliest landing,
 *     and to the source_code / country recorded on the premium_signup event.
 *
 * All queries are environment-scoped and use plain GROUP BY (no window
 * functions) so they run on any MySQL 8 / MariaDB 10 build.
 */

/**
 * SQL fragment that extracts the bare host from the referer URL stored in
 * event_data.$.referer: strips scheme, path, query, port and a leading "www.".
 * Yields NULL for a missing/empty/self referer so those roll up to Direct/None.
 */
const FUNNEL_REF_HOST_SQL =
    "NULLIF(TRIM(LEADING 'www.' FROM LOWER(" .
    "SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(" .
    "JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.referer'))" .
    ", '://', -1), '/', 1), '?', 1), ':', 1))), '')";

/** Label used for visitors/customers with no usable referer. */
const FUNNEL_DIRECT_LABEL = 'Direct / None';

/** Category label per key, matching admin/referral-links "By category". */
const FUNNEL_CATEGORY_LABELS = [
    'paid'      => 'Paid ads',
    'website'   => 'My website (guides & articles)',
    'invgen'    => 'Invoice generator',
    'outreach'  => 'Outreach',
    'social'    => 'Social media',
    'youtube'   => 'YouTube',
    'ai'        => 'AI assistants',
    'directory' => 'Directories',
    'other'     => 'Other',
];

/**
 * Roll a source_code up into a referral category from its naming-prefix, the
 * same convention admin/referral-links uses. Returns null for untracked
 * (direct/organic) traffic that has no source_code.
 */
function funnel_category_key(?string $source_code): ?string
{
    if ($source_code === null || $source_code === '') {
        return null;
    }
    $code = strtolower($source_code);
    if (strncmp($code, 'google-ads-', 11) === 0 || strncmp($code, 'ads-', 4) === 0
        || strncmp($code, 'paid-', 5) === 0 || strncmp($code, 'bing-ads-', 9) === 0) {
        return 'paid';
    }
    if (strncmp($code, 'guide-', 6) === 0 || $code === 'guides-hub') {
        return 'website';
    }
    if (strncmp($code, 'invgen-', 7) === 0) {
        return 'invgen';
    }
    if (strncmp($code, 'outreach-', 9) === 0) {
        return 'outreach';
    }
    if (strncmp($code, 'social-', 7) === 0) {
        return 'social';
    }
    if (strncmp($code, 'youtube-', 8) === 0) {
        return 'youtube';
    }
    if (strncmp($code, 'ai-', 3) === 0) {
        return 'ai';
    }
    if (strncmp($code, 'dir-', 4) === 0) {
        return 'directory';
    }
    return 'other';
}

/**
 * True when $host equals one of $domains or is a subdomain of it.
 */
function funnel_host_matches(?string $host, array $domains): bool
{
    if ($host === null || $host === '') {
        return false;
    }
    foreach ($domains as $d) {
        if ($host === $d || str_ends_with($host, '.' . $d)) {
            return true;
        }
    }
    return false;
}

/**
 * Classify a referer host (+ its resolved source_code) into a marketing
 * channel: Direct, Organic search, Organic social, AI, Newsletter, Referral.
 */
function funnel_classify_channel(?string $host, ?string $source_code): string
{
    $host = ($host === null || $host === '' || $host === 'null') ? null : strtolower($host);

    if ($host === null) {
        // No referer. A source_code that looks like an email blast still counts
        // as Newsletter; otherwise it's genuinely direct/untracked.
        if ($source_code !== null && (str_contains($source_code, 'email') || str_contains($source_code, 'newsletter'))) {
            return 'Newsletter';
        }
        return 'Direct';
    }

    if (str_contains($host, 'newsletter')) {
        return 'Newsletter';
    }

    // AI assistants first so gemini.google.com / copilot.microsoft.com don't
    // fall through to the search or referral buckets.
    $ai = ['chatgpt.com', 'chat.openai.com', 'openai.com', 'claude.ai', 'perplexity.ai',
           'gemini.google.com', 'copilot.microsoft.com', 'you.com', 'phind.com', 'poe.com', 'meta.ai'];
    if (funnel_host_matches($host, $ai)) {
        return 'AI';
    }

    // Search engines. Google has many country TLDs, so match it by pattern.
    if (preg_match('/(^|\.)google\.[a-z.]+$/', $host)) {
        return 'Organic search';
    }
    $search = ['bing.com', 'duckduckgo.com', 'yahoo.com', 'ecosia.org', 'yandex.com',
               'yandex.ru', 'baidu.com', 'brave.com', 'startpage.com', 'qwant.com', 'search.marginalia.nu'];
    if (funnel_host_matches($host, $search)) {
        return 'Organic search';
    }

    $social = ['reddit.com', 'x.com', 'twitter.com', 't.co', 'linkedin.com', 'lnkd.in',
               'facebook.com', 'instagram.com', 'youtube.com', 'youtu.be', 'tiktok.com',
               'news.ycombinator.com', 'pinterest.com', 'mastodon.social', 'threads.net',
               'producthunt.com'];
    if (funnel_host_matches($host, $social)) {
        return 'Organic social';
    }

    return 'Referral';
}

/**
 * Build the shared WHERE clause + bound params for landing-event breakdowns.
 */
function funnel_landing_scope(?string $period_start, ?string $source_filter): array
{
    $where  = "event_type = 'landing' AND js_confirmed = 1 AND environment = ? AND visitor_id IS NOT NULL";
    $params = [current_environment()];
    if ($period_start !== null) {
        $where .= ' AND created_at >= ?';
        $params[] = $period_start;
    }
    if ($source_filter !== null && $source_filter !== '') {
        $where .= ' AND source_code = ?';
        $params[] = $source_filter;
    }
    return [$where, $params];
}

/**
 * Distinct-visitor counts grouped by an arbitrary landing-row dimension.
 * Returns rows of ['k' => <value|null>, 'visitors' => int].
 */
function funnel_visits_by(string $expr, ?string $period_start, ?string $source_filter): array
{
    global $pdo;
    [$where, $params] = funnel_landing_scope($period_start, $source_filter);
    $sql = "SELECT $expr AS k, COUNT(DISTINCT visitor_id) AS visitors
              FROM referral_events
             WHERE $where
             GROUP BY k";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Paying subscriptions in the period with total revenue, plus the visitor,
 * source_code and country recorded on their premium_signup event.
 * Returns rows of subscription_id, visitor_id, source_code, country_code, amount.
 */
function funnel_paying_rows(?string $period_start, ?string $source_filter): array
{
    global $pdo;

    $pay_where = "status = 'completed'";
    $params    = [];
    if ($period_start !== null) {
        $pay_where .= ' AND created_at >= ?';
        $params[]   = $period_start;
    }

    $sql = "
        SELECT re.subscription_id,
               MAX(re.visitor_id)   AS visitor_id,
               MAX(re.source_code)  AS source_code,
               MAX(re.country_code) AS country_code,
               pay.amount           AS amount
          FROM referral_events re
          JOIN (
                SELECT subscription_id, SUM(amount) AS amount
                  FROM premium_subscription_payments
                 WHERE $pay_where
                 GROUP BY subscription_id
               ) pay ON pay.subscription_id = re.subscription_id
         WHERE re.event_type = 'premium_signup'
           AND re.environment = ?
           AND re.subscription_id IS NOT NULL";
    $params[] = current_environment();
    if ($source_filter !== null && $source_filter !== '') {
        $sql .= ' AND re.source_code = ?';
        $params[] = $source_filter;
    }
    $sql .= ' GROUP BY re.subscription_id, pay.amount';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * First-touch referer host + keyword for the given visitor ids.
 * Returns [visitor_id => ['host' => ?string, 'keyword' => ?string]].
 */
function funnel_first_touch(array $visitor_ids): array
{
    global $pdo;
    $visitor_ids = array_values(array_unique(array_filter($visitor_ids, fn($v) => $v !== null && $v !== '')));
    if (empty($visitor_ids)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($visitor_ids), '?'));
    $expr = FUNNEL_REF_HOST_SQL;
    $sql = "SELECT visitor_id, $expr AS host, keyword
              FROM referral_events
             WHERE event_type = 'landing' AND js_confirmed = 1 AND environment = ?
               AND visitor_id IN ($placeholders)
             ORDER BY created_at ASC, id ASC";
    $params = array_merge([current_environment()], $visitor_ids);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $out = [];
    while ($row = $stmt->fetch()) {
        $vid = $row['visitor_id'];
        if (!isset($out[$vid])) { // first row per visitor wins (earliest)
            $out[$vid] = ['host' => $row['host'], 'keyword' => $row['keyword']];
        }
    }
    return $out;
}

/**
 * Merge a visits map and a revenue map keyed by the same dimension value into a
 * sorted list of ['label','key','visits','revenue'], visits desc then revenue.
 */
function funnel_merge_breakdown(array $visits_map, array $revenue_map, callable $labeler): array
{
    $keys = array_unique(array_merge(array_keys($visits_map), array_keys($revenue_map)));
    $rows = [];
    foreach ($keys as $k) {
        $rows[] = [
            'key'     => $k,
            'label'   => $labeler((string)$k),
            'visits'  => (int)($visits_map[$k] ?? 0),
            'revenue' => (float)($revenue_map[$k] ?? 0),
        ];
    }
    usort($rows, function ($a, $b) {
        if ($b['visits'] !== $a['visits']) return $b['visits'] - $a['visits'];
        return $b['revenue'] <=> $a['revenue'];
    });
    return $rows;
}

/**
 * Per-stage top-N breakdown of an event dimension (country_code or source_code)
 * across every funnel stage, for the funnel hover tooltip.
 *
 * Returns [event_type => ['total' => int, 'rows' => [['label','visitors','pct'], ...]]].
 */
function funnel_stage_dimension(string $column, ?string $period_start, ?string $source_filter, int $top = 3): array
{
    global $pdo;

    $where  = 'environment = ? AND js_confirmed = 1';
    $params = [current_environment()];
    if ($period_start !== null) {
        $where .= ' AND created_at >= ?';
        $params[] = $period_start;
    }
    if ($source_filter !== null && $source_filter !== '') {
        $where .= ' AND source_code = ?';
        $params[] = $source_filter;
    }

    $sql = "SELECT event_type, $column AS k, COUNT(DISTINCT visitor_id) AS visitors
              FROM referral_events
             WHERE $where
             GROUP BY event_type, k";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $by_stage = [];
    while ($row = $stmt->fetch()) {
        $by_stage[$row['event_type']][] = ['k' => $row['k'], 'visitors' => (int)$row['visitors']];
    }

    $out = [];
    foreach ($by_stage as $stage => $rows) {
        usort($rows, fn($a, $b) => $b['visitors'] - $a['visitors']);
        $total = array_sum(array_map(fn($r) => $r['visitors'], $rows));
        $picked = [];
        foreach (array_slice($rows, 0, $top) as $r) {
            $picked[] = [
                'k'        => $r['k'],
                'visitors' => $r['visitors'],
                'pct'      => $total > 0 ? round(($r['visitors'] / $total) * 100) : 0,
            ];
        }
        $out[$stage] = ['total' => $total, 'rows' => $picked];
    }
    return $out;
}

/**
 * Orchestrator: assemble every breakdown the Funnel dashboard needs.
 *
 * @return array{
 *   channels: list<array>, referrers: list<array>, campaigns: list<array>,
 *   keywords: list<array>, countries: list<array>, regions: list<array>,
 *   cities: list<array>, total_visits:int, total_revenue:float,
 *   stage_countries: array, stage_sources: array
 * }
 */
function build_funnel_analytics(?string $period_start, ?string $source_filter, array $referral_links): array
{
    // ---- Visits (distinct visitors) per dimension ----
    $host_visits    = funnel_visits_by(FUNNEL_REF_HOST_SQL, $period_start, $source_filter);
    $source_visits  = funnel_visits_by('source_code',  $period_start, $source_filter);
    $keyword_visits = funnel_visits_by('keyword',       $period_start, $source_filter);
    $country_visits = funnel_visits_by('country_code',  $period_start, $source_filter);
    $region_visits  = funnel_visits_by('region',        $period_start, $source_filter);
    $city_visits    = funnel_visits_by('city',          $period_start, $source_filter);

    // Referrer + channel visit maps.
    $ref_visits_map = [];
    $channel_visits = [];
    foreach ($host_visits as $r) {
        $host  = $r['k'];
        $label = ($host === null || $host === '' || $host === 'null') ? FUNNEL_DIRECT_LABEL : $host;
        $ref_visits_map[$label] = ($ref_visits_map[$label] ?? 0) + (int)$r['visitors'];
        $ch = funnel_classify_channel($host, null);
        $channel_visits[$ch] = ($channel_visits[$ch] ?? 0) + (int)$r['visitors'];
    }

    $mapize = function (array $rows, string $null_label): array {
        $m = [];
        foreach ($rows as $r) {
            $k = ($r['k'] === null || $r['k'] === '') ? $null_label : $r['k'];
            $m[$k] = ($m[$k] ?? 0) + (int)$r['visitors'];
        }
        return $m;
    };
    $keyword_visits_map = $mapize($keyword_visits, '(no keyword)');
    $country_visits_map = $mapize($country_visits, 'Unknown');
    $region_visits_map  = $mapize($region_visits,  'Unknown');
    $city_visits_map    = $mapize($city_visits,    'Unknown');

    // ---- Revenue (first-touch) per dimension ----
    $paying = funnel_paying_rows($period_start, $source_filter);
    $visitor_ids = array_map(fn($r) => $r['visitor_id'], $paying);
    $first_touch = funnel_first_touch($visitor_ids);

    $rev_by_ref = $rev_by_channel = $rev_by_category = [];
    $rev_by_country = $rev_by_keyword = [];
    $total_revenue = 0.0;
    foreach ($paying as $p) {
        $amt = (float)$p['amount'];
        $total_revenue += $amt;

        // Category rollup: only tracked sources contribute (direct excluded).
        $cat = funnel_category_key($p['source_code']);
        if ($cat !== null) {
            $rev_by_category[$cat] = ($rev_by_category[$cat] ?? 0) + $amt;
        }

        $cc = ($p['country_code'] === null || $p['country_code'] === '') ? 'Unknown' : $p['country_code'];
        $rev_by_country[$cc] = ($rev_by_country[$cc] ?? 0) + $amt;

        $ft   = $first_touch[$p['visitor_id']] ?? ['host' => null, 'keyword' => null];
        $host = $ft['host'];
        $ref_label = ($host === null || $host === '' || $host === 'null') ? FUNNEL_DIRECT_LABEL : $host;
        $rev_by_ref[$ref_label] = ($rev_by_ref[$ref_label] ?? 0) + $amt;

        $ch = funnel_classify_channel($host, $p['source_code']);
        $rev_by_channel[$ch] = ($rev_by_channel[$ch] ?? 0) + $amt;

        $kw = ($ft['keyword'] === null || $ft['keyword'] === '') ? '(no keyword)' : $ft['keyword'];
        $rev_by_keyword[$kw] = ($rev_by_keyword[$kw] ?? 0) + $amt;
    }

    // ---- Category rollup (matches referral-links "By category") ----
    // Bucket tracked-source visits by category; direct/untracked is excluded.
    $category_visits = [];
    foreach ($source_visits as $r) {
        $cat = funnel_category_key($r['k']);
        if ($cat !== null) {
            $category_visits[$cat] = ($category_visits[$cat] ?? 0) + (int)$r['visitors'];
        }
    }

    // ---- Labelers ----
    $identity = fn(string $k) => $k;
    $category_labeler = fn(string $k) => FUNNEL_CATEGORY_LABELS[$k] ?? ucfirst($k);
    // Country keys stay ISO-2 codes (flag + map need them); only the label
    // becomes the readable country name.
    $country_labeler = fn(string $k) => $k === 'Unknown' ? 'Unknown' : (country_name($k) ?: $k);

    // ---- Merge into sorted breakdown lists ----
    $channels  = funnel_merge_breakdown($channel_visits,     $rev_by_channel, $identity);
    $referrers = funnel_merge_breakdown($ref_visits_map,     $rev_by_ref,     $identity);
    $campaigns = funnel_merge_breakdown($category_visits,    $rev_by_category, $category_labeler);
    $keywords  = funnel_merge_breakdown($keyword_visits_map, $rev_by_keyword, $identity);
    $countries = funnel_merge_breakdown($country_visits_map, $rev_by_country, $country_labeler);
    $regions   = funnel_merge_breakdown($region_visits_map,  [],              $identity);
    $cities    = funnel_merge_breakdown($city_visits_map,    [],              $identity);

    $total_visits = array_sum(array_map(fn($r) => $r['visits'], $referrers));

    return [
        'channels'        => $channels,
        'referrers'       => $referrers,
        'campaigns'       => $campaigns,
        'keywords'        => $keywords,
        'countries'       => $countries,
        'regions'         => $regions,
        'cities'          => $cities,
        'total_visits'    => $total_visits,
        'total_revenue'   => $total_revenue,
        'stage_countries' => funnel_stage_dimension('country_code', $period_start, $source_filter),
        'stage_sources'   => funnel_stage_dimension('source_code',  $period_start, $source_filter),
    ];
}
