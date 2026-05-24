<?php
/**
 * Shared helpers for the Reddit outreach channel.
 *
 * - OAuth client (script-app password grant, token cached in reddit_settings)
 * - Reddit API wrappers (subreddit /new, global /search, thread comments, /api/info)
 * - Rules-based pre-filter scoring
 * - Neutral AI relevance check (Gemini)
 * - Voice-doc-driven draft generation (Gemini)
 *
 * Used by:
 *   cron/reddit_monitor.php       — daily discovery + draft pipeline
 *   cron/reddit_status_check.php  — every-2h posted-reply status check
 *   admin/outreach/api.php        — admin actions (run now, regenerate, on-demand draft)
 *
 * No DB writes here; callers orchestrate persistence.
 */

if (defined('REDDIT_HELPERS_LOADED')) return;
define('REDDIT_HELPERS_LOADED', true);

require_once __DIR__ . '/outreach_helpers.php'; // for call_gemini()
require_once __DIR__ . '/reddit_voice.php';

const REDDIT_USER_AGENT_PREFIX = 'argobooks-outreach/1.0 by /u/';
const REDDIT_API_BASE_OAUTH = 'https://oauth.reddit.com';
const REDDIT_API_BASE_PUBLIC = 'https://www.reddit.com';
const REDDIT_TOKEN_URL = 'https://www.reddit.com/api/v1/access_token';
const REDDIT_API_CALL_SLEEP_US = 600000; // 0.6s between calls — stay polite

/**
 * True when full OAuth credentials are configured. When false, the helpers
 * fall back to Reddit's public JSON endpoints (no app, no auth, lower rate
 * limits but plenty for our volume). Lets you flip to OAuth later just by
 * filling in the REDDIT_* env vars — no code change needed.
 */
function reddit_oauth_configured(): bool
{
    return !empty($_ENV['REDDIT_CLIENT_ID'])
        && !empty($_ENV['REDDIT_CLIENT_SECRET'])
        && !empty($_ENV['REDDIT_USERNAME'])
        && !empty($_ENV['REDDIT_PASSWORD']);
}

/**
 * User-Agent string for outbound Reddit requests. Reddit asks for a
 * descriptive UA on all traffic — we include the configured username if set,
 * otherwise a generic suffix for unauthenticated calls.
 */
function reddit_user_agent(): string
{
    $username = $_ENV['REDDIT_USERNAME'] ?? 'anonymous';
    return REDDIT_USER_AGENT_PREFIX . $username;
}

// ─── Logging ───

function reddit_log(string $message): void
{
    $sanitized = preg_replace('/[\r\n]+/', ' ', $message);
    @error_log('[reddit][' . date('Y-m-d H:i:s') . '] ' . $sanitized);
}

// ─── Live progress (file-backed) ───
// The discovery cron writes JSON to a small progress file at each milestone.
// The admin Threads tab polls it via api.php?action=reddit_pipeline_progress
// so the founder sees what step the cron is on (instead of staring at a
// "running…" banner with no detail).

function reddit_progress_file_path(): string
{
    return __DIR__ . '/../logs/reddit_monitor_progress.json';
}

function reddit_progress_write(array $update): void
{
    $path = reddit_progress_file_path();
    $dir = dirname($path);
    if (!is_dir($dir)) @mkdir($dir, 0755, true);

    $current = reddit_progress_read();
    $merged = array_merge($current, $update, ['updated_at' => date('Y-m-d H:i:s')]);
    @file_put_contents($path, json_encode($merged), LOCK_EX);
}

function reddit_progress_read(): array
{
    $path = reddit_progress_file_path();
    if (!file_exists($path)) return [];
    $raw = @file_get_contents($path);
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function reddit_progress_reset(array $initial = []): void
{
    $base = [
        'message' => '',
        'found' => 0,
        'drafted' => 0,
        'started_at' => date('Y-m-d H:i:s'),
        'completed' => false,
    ];
    $merged = array_merge($base, $initial, ['updated_at' => date('Y-m-d H:i:s')]);
    @file_put_contents(reddit_progress_file_path(), json_encode($merged), LOCK_EX);
}

// ─── OAuth ───

/**
 * Returns a valid OAuth access token, refreshing if missing or expired.
 * Token is stored encrypted via portal_encrypt() in reddit_settings.
 * Returns null on auth failure (logs the error).
 */
function reddit_get_access_token($pdo): ?string
{
    $clientId = $_ENV['REDDIT_CLIENT_ID'] ?? '';
    $clientSecret = $_ENV['REDDIT_CLIENT_SECRET'] ?? '';
    $username = $_ENV['REDDIT_USERNAME'] ?? '';
    $password = $_ENV['REDDIT_PASSWORD'] ?? '';

    if ($clientId === '' || $clientSecret === '' || $username === '' || $password === '') {
        reddit_log('Missing Reddit OAuth env vars; cannot authenticate.');
        return null;
    }

    // Cached token?
    $stmt = $pdo->prepare("SELECT access_token, access_token_expires_at FROM reddit_settings WHERE id = 1");
    $stmt->execute();
    $row = $stmt->fetch();

    if ($row && !empty($row['access_token']) && !empty($row['access_token_expires_at'])
        && strtotime($row['access_token_expires_at']) > (time() + 60)) {
        try {
            $decrypted = portal_decrypt($row['access_token']);
            if ($decrypted !== '') {
                return $decrypted;
            }
        } catch (Throwable $e) {
            // Stale or corrupt cipher (e.g. encryption key rotated). Fall through to refresh.
            reddit_log('Cached token decrypt failed; refreshing: ' . $e->getMessage());
        }
    }

    // Refresh
    $ch = curl_init(REDDIT_TOKEN_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ]),
        CURLOPT_HTTPHEADER => [
            'User-Agent: ' . REDDIT_USER_AGENT_PREFIX . $username,
        ],
        CURLOPT_USERPWD => $clientId . ':' . $clientSecret,
        CURLOPT_TIMEOUT => 20,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        reddit_log("Token refresh failed (HTTP $httpCode): " . substr((string) $response, 0, 200));
        return null;
    }

    $data = json_decode($response, true);
    $token = $data['access_token'] ?? null;
    $expiresIn = (int) ($data['expires_in'] ?? 3600);
    if (!$token) {
        reddit_log('Token refresh returned no access_token.');
        return null;
    }

    $encrypted = portal_encrypt($token);
    $expiresAt = date('Y-m-d H:i:s', time() + $expiresIn);
    $upd = $pdo->prepare("UPDATE reddit_settings SET access_token = ?, access_token_expires_at = ? WHERE id = 1");
    $upd->execute([$encrypted, $expiresAt]);

    return $token;
}

/**
 * GET against the Reddit API. Dispatches to OAuth when credentials are
 * configured, otherwise falls back to Reddit's public JSON endpoints (no
 * app, no auth required). The path is the same in both modes — the helper
 * handles the base URL difference and the `.json` suffix for public mode.
 *
 * Returns decoded JSON array on success, null on failure (logs).
 */
function reddit_api_get($pdo, string $path, array $query = []): ?array
{
    if (reddit_oauth_configured()) {
        return reddit_api_get_oauth($pdo, $path, $query);
    }
    return reddit_api_get_public($path, $query);
}

/**
 * Authenticated GET via the OAuth endpoint. Sleeps REDDIT_API_CALL_SLEEP_US
 * after each call to stay polite under any rate limit.
 */
function reddit_api_get_oauth($pdo, string $path, array $query = []): ?array
{
    $token = reddit_get_access_token($pdo);
    if ($token === null) return null;

    $url = REDDIT_API_BASE_OAUTH . $path;
    if (!empty($query)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'User-Agent: ' . reddit_user_agent(),
        ],
        CURLOPT_TIMEOUT => 30,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    usleep(REDDIT_API_CALL_SLEEP_US);

    if ($response === false) {
        reddit_log("API call failed: $path");
        return null;
    }
    if ($httpCode === 401) {
        // Token may have been revoked; clear cache so next call refreshes.
        $upd = $pdo->prepare("UPDATE reddit_settings SET access_token = NULL, access_token_expires_at = NULL WHERE id = 1");
        $upd->execute();
        reddit_log("API call 401 ($path) — cleared cached token.");
        return null;
    }
    if ($httpCode !== 200) {
        reddit_log("API call HTTP $httpCode ($path): " . substr((string) $response, 0, 200));
        return null;
    }
    $data = json_decode($response, true);
    if (!is_array($data)) {
        reddit_log("API call returned non-JSON ($path).");
        return null;
    }
    return $data;
}

/**
 * Unauthenticated GET via Reddit's public endpoints.
 *
 * Reddit's JSON endpoints (`.json` suffix) are aggressively IP-blocked for
 * datacenter / shared-hosting traffic, which returns 403 even with a
 * descriptive User-Agent. RSS / Atom endpoints (`.rss` suffix) are typically
 * less restricted because Reddit doesn't want to break legitimate RSS readers.
 *
 * Strategy: discovery-shaped paths (subreddit listings, search, thread
 * comments) try `.rss` and parse the Atom feed back into Reddit's JSON shape
 * so downstream code doesn't care which format we got. Endpoints with no RSS
 * equivalent (`/api/info`, `/user/X/about`) keep using `.json` and will
 * silently 403 from blocked IPs — those features (reply-status check, account
 * info card) require OAuth to work.
 *
 * Fill in the REDDIT_* OAuth env vars to upgrade automatically.
 */
function reddit_api_get_public(string $path, array $query = []): ?array
{
    if (reddit_path_supports_rss($path)) {
        $result = reddit_api_get_public_rss($path, $query);
        if ($result !== null) {
            return $result;
        }
        // Fall through to JSON only if RSS *didn't* return a parseable feed
        // (rare — usually it's blocked or it's parseable, no in-between).
    }
    return reddit_api_get_public_json($path, $query);
}

/**
 * Endpoints that Reddit serves over RSS/Atom AND that our parser knows how
 * to handle. Comments listings (`/r/X/comments/Y`) also have RSS but a
 * different shape (mix of post + comments) that the current parser doesn't
 * convert — so they fall through to JSON, which will 403 from blocked IPs.
 * Net effect: AI relevance + draft generation runs without top-comments
 * context, which is a soft signal loss but doesn't break the flow.
 */
function reddit_path_supports_rss(string $path): bool
{
    return (bool) preg_match('#^/r/[^/]+/new$|^/search$#', $path);
}

/**
 * JSON-flavoured fetch (legacy path; still used by /api/info and user-about).
 */
function reddit_api_get_public_json(string $path, array $query = []): ?array
{
    if (!str_ends_with($path, '.json')) {
        $path .= '.json';
    }

    $url = REDDIT_API_BASE_PUBLIC . $path;
    if (!empty($query)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
    }

    $response = reddit_http_get($url, ['Accept: application/json']);
    if ($response === null) return null;

    $data = json_decode($response['body'], true);
    if (!is_array($data)) {
        reddit_log("Public JSON returned non-JSON ($path).");
        return null;
    }
    return $data;
}

/**
 * RSS-flavoured fetch — same endpoints, `.rss` suffix, parsed back into
 * Reddit's JSON listing shape so callers don't care.
 */
function reddit_api_get_public_rss(string $path, array $query = []): ?array
{
    $rssPath = $path . '.rss';
    $url = REDDIT_API_BASE_PUBLIC . $rssPath;
    if (!empty($query)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($query);
    }

    $response = reddit_http_get($url, ['Accept: application/atom+xml, application/rss+xml, text/xml']);
    if ($response === null) return null;

    return reddit_parse_atom_to_listing($response['body'], $rssPath);
}

/**
 * Low-level HTTP GET against Reddit with our standard headers + rate-limit
 * sleep. Returns ['body' => string, 'http_code' => int] on success, null on
 * 403/429/network failure (with the failure logged so we can see it).
 */
function reddit_http_get(string $url, array $extraHeaders = []): ?array
{
    $headers = array_merge(['User-Agent: ' . reddit_user_agent()], $extraHeaders);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
    ]);
    $body = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    usleep(REDDIT_API_CALL_SLEEP_US);

    if ($body === false) {
        reddit_log("HTTP request failed: $url");
        return null;
    }
    if ($httpCode === 429) {
        reddit_log("Rate-limited (429) on $url");
        return null;
    }
    if ($httpCode === 403) {
        reddit_log("403 on $url — Reddit blocks unauthenticated traffic from this IP. Fill in REDDIT_* OAuth env vars to upgrade.");
        return null;
    }
    if ($httpCode !== 200) {
        reddit_log("HTTP $httpCode ($url): " . substr((string) $body, 0, 200));
        return null;
    }
    return ['body' => $body, 'http_code' => $httpCode];
}

/**
 * Parse a Reddit Atom feed into the same shape Reddit's JSON listing returns,
 * so downstream code (reddit_normalize_post, score, etc.) works unchanged.
 *
 * Returns null on parse failure. Missing fields default sensibly:
 *   - score and num_comments default to 0 (RSS doesn't expose these)
 *   - is_self defaults to true (RSS doesn't distinguish; we treat as self-post
 *     and use the HTML content as the body)
 *   - stickied defaults to false
 */
function reddit_parse_atom_to_listing(string $xml, string $sourcePath): ?array
{
    $prev = libxml_use_internal_errors(true);
    $feed = simplexml_load_string($xml);
    libxml_use_internal_errors($prev);
    if ($feed === false) {
        reddit_log("Atom parse failed for $sourcePath");
        return null;
    }

    $children = [];
    foreach ($feed->entry ?? [] as $entry) {
        // The full id is like "t3_xxxxxx"; strip the prefix for the bare id field
        $fullId = (string) ($entry->id ?? '');
        if (!preg_match('/t3_([a-z0-9]+)/', $fullId, $m)) continue;
        $id = $m[1];

        // <category term="smallbusiness" label="r/smallbusiness"/>
        $subreddit = '';
        foreach ($entry->category ?? [] as $cat) {
            $term = (string) $cat['term'];
            if ($term !== '') { $subreddit = $term; break; }
        }

        // <link href="https://www.reddit.com/r/.../comments/.../slug/"/>
        $permalinkAbs = '';
        foreach ($entry->link ?? [] as $link) {
            $href = (string) $link['href'];
            if ($href !== '') { $permalinkAbs = $href; break; }
        }
        // reddit_normalize_post expects post['permalink'] to be the relative path
        $permalinkRel = '';
        if (preg_match('#https?://[^/]+(/.*)$#', $permalinkAbs, $m)) {
            $permalinkRel = $m[1];
        }

        // <author><name>/u/username</name></author>
        $author = '';
        if (isset($entry->author->name)) {
            $author = ltrim((string) $entry->author->name, '/');
            if (strpos($author, 'u/') === 0) $author = substr($author, 2);
        }

        $createdUtc = strtotime((string) ($entry->published ?? $entry->updated ?? ''));
        if ($createdUtc === false) $createdUtc = time();

        // <content type="html"> — Reddit wraps the body in HTML. Decode entities,
        // strip tags, collapse whitespace to a reasonable plaintext body.
        $contentHtml = (string) ($entry->content ?? '');
        $body = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode($contentHtml, ENT_QUOTES | ENT_HTML5))));

        $title = (string) ($entry->title ?? '');

        $children[] = [
            'kind' => 't3',
            'data' => [
                'id' => $id,
                'subreddit' => $subreddit,
                'title' => $title,
                'selftext' => $body,
                'permalink' => $permalinkRel,
                'url' => $permalinkAbs,
                'author' => $author ?: null,
                'score' => 0,
                'num_comments' => 0,
                'created_utc' => $createdUtc,
                'is_self' => true,
                'stickied' => false,
            ],
        ];
    }

    return [
        'data' => [
            'children' => $children,
            'after' => null,
            'before' => null,
        ],
    ];
}

// ─── Discovery: fetch + normalize ───

/**
 * Fetch the latest posts from a single subreddit. Returns an array of
 * normalized thread rows ready for scoring + insertion.
 */
function reddit_fetch_subreddit_new($pdo, string $subreddit, int $limit = 50, int $hoursBack = 24): array
{
    $data = reddit_api_get($pdo, "/r/$subreddit/new", ['limit' => $limit]);
    if (!$data || !isset($data['data']['children'])) return [];

    $cutoff = time() - ($hoursBack * 3600);
    $out = [];
    foreach ($data['data']['children'] as $child) {
        $post = $child['data'] ?? [];
        if (empty($post['id']) || empty($post['created_utc'])) continue;
        if ((int) $post['created_utc'] < $cutoff) continue;
        if (!empty($post['stickied'])) continue;
        $out[] = reddit_normalize_post($post, 'watchlist');
    }
    return $out;
}

/**
 * Run Reddit's global search for a keyword and return normalized threads.
 */
function reddit_fetch_search($pdo, string $keyword, int $limit = 50, string $timeRange = 'day'): array
{
    $data = reddit_api_get($pdo, '/search', [
        'q' => $keyword,
        't' => $timeRange,
        'sort' => 'new',
        'restrict_sr' => 'false',
        'limit' => $limit,
        'type' => 'link',
    ]);
    if (!$data || !isset($data['data']['children'])) return [];

    $out = [];
    foreach ($data['data']['children'] as $child) {
        $post = $child['data'] ?? [];
        if (empty($post['id'])) continue;
        if (!empty($post['stickied'])) continue;
        $normalized = reddit_normalize_post($post, 'keyword');
        $normalized['matched_keywords'] = [$keyword];
        $out[] = $normalized;
    }
    return $out;
}

/**
 * Top-level comments on a thread, used as context for AI relevance + draft.
 * Returns a plain array of comment bodies (strings).
 */
function reddit_fetch_top_comments($pdo, string $subreddit, string $postId36, int $limit = 10): array
{
    // The comments endpoint has no RSS parser shape and 403s from blocked
    // IPs in unauth mode — skip the request entirely and let the AI score
    // from title + body only. Restored automatically once OAuth is set up.
    if (!reddit_oauth_configured()) {
        return [];
    }

    $data = reddit_api_get($pdo, "/r/$subreddit/comments/$postId36", [
        'limit' => $limit,
        'depth' => 1,
        'sort' => 'top',
    ]);
    if (!is_array($data) || count($data) < 2) return [];

    $listing = $data[1]['data']['children'] ?? [];
    $out = [];
    foreach ($listing as $child) {
        if (($child['kind'] ?? '') !== 't1') continue;
        $body = $child['data']['body'] ?? '';
        if ($body === '' || $body === '[deleted]' || $body === '[removed]') continue;
        $out[] = $body;
        if (count($out) >= $limit) break;
    }
    return $out;
}

/**
 * Check a posted comment's current state via /api/info. Returns one of
 * 'live', 'removed', 'removed_or_shadowbanned', 'deleted_by_user', or null
 * (on API failure). Also returns upvotes and reply count if observable.
 */
function reddit_check_comment_status($pdo, string $commentId36): array
{
    $fullName = 't1_' . $commentId36;
    $data = reddit_api_get($pdo, '/api/info', ['id' => $fullName]);
    if ($data === null) {
        return ['status' => null, 'upvotes' => null, 'replies' => null];
    }
    $children = $data['data']['children'] ?? [];
    if (empty($children)) {
        // API returned 200 but no comment — removed by mod or shadowbanned.
        return ['status' => 'removed_or_shadowbanned', 'upvotes' => null, 'replies' => null];
    }
    $c = $children[0]['data'] ?? [];
    $body = $c['body'] ?? '';
    $upvotes = isset($c['ups']) ? (int) $c['ups'] : null;
    // Reddit comment objects expose replies via a `replies` Listing (or empty
    // string when there are none). There's no `num_replies` scalar — we have
    // to count children in the Listing when one is returned. /api/info often
    // doesn't expand the replies tree (it returns ""), in which case we
    // record null rather than misleadingly logging 0.
    $replies = null;
    if (isset($c['replies']) && is_array($c['replies'])) {
        $replies = count($c['replies']['data']['children'] ?? []);
    } elseif (isset($c['replies']) && $c['replies'] === '') {
        $replies = 0;
    }

    if ($body === '[removed]') {
        return ['status' => 'removed', 'upvotes' => $upvotes, 'replies' => $replies];
    }
    if ($body === '[deleted]') {
        return ['status' => 'deleted_by_user', 'upvotes' => $upvotes, 'replies' => $replies];
    }
    return ['status' => 'live', 'upvotes' => $upvotes, 'replies' => $replies];
}

/**
 * Fetch the founder's Reddit account info (age + karma). Used by admin
 * Settings to suggest post limits.
 */
function reddit_fetch_account_about($pdo): ?array
{
    $username = $_ENV['REDDIT_USERNAME'] ?? '';
    if ($username === '') return null;
    $data = reddit_api_get($pdo, "/user/$username/about");
    if (!$data || !isset($data['data'])) return null;
    $d = $data['data'];
    return [
        'username' => $username,
        'created_utc' => (int) ($d['created_utc'] ?? 0),
        'total_karma' => (int) ($d['total_karma'] ?? 0),
        'link_karma' => (int) ($d['link_karma'] ?? 0),
        'comment_karma' => (int) ($d['comment_karma'] ?? 0),
    ];
}

/**
 * Reshape a Reddit API post object into our DB row shape (subset; caller
 * fills in scoring fields after).
 */
function reddit_normalize_post(array $post, string $defaultSource): array
{
    $isSelf = !empty($post['is_self']);
    return [
        'reddit_id' => $post['id'],
        'subreddit' => $post['subreddit'] ?? '',
        'title' => mb_substr((string) ($post['title'] ?? ''), 0, 500),
        'body' => $isSelf ? (string) ($post['selftext'] ?? '') : null,
        'url' => 'https://www.reddit.com' . ($post['permalink'] ?? ''),
        'author' => $post['author'] ?? null,
        'author_karma' => null, // Filled lazily if needed
        'post_score' => (int) ($post['score'] ?? 0),
        'comment_count' => (int) ($post['num_comments'] ?? 0),
        'posted_at' => date('Y-m-d H:i:s', (int) ($post['created_utc'] ?? time())),
        'discovery_source' => $defaultSource,
        'matched_keywords' => [],
    ];
}

// ─── Rules-based scoring ───

/**
 * Score a normalized thread 0–100. See spec for the breakdown.
 * Watchlist-source threads get a bigger subreddit-relevance bonus than
 * keyword-only threads. Multi-source ('both') gets a small extra boost.
 */
function reddit_score_thread(array $thread, array $context = []): int
{
    $now = time();
    $postedAt = strtotime($thread['posted_at'] ?? 'now');
    $hoursOld = max(0, ($now - $postedAt) / 3600);

    // Recency 0-30: linear decay over 24h
    $recency = max(0, 30 - (int) round($hoursOld * (30 / 24)));

    // Comment sweet-spot 0-20
    $n = (int) ($thread['comment_count'] ?? 0);
    if ($n === 0) $comments = 5;
    elseif ($n <= 20) $comments = 20;
    elseif ($n <= 50) $comments = 10;
    else $comments = 0;

    // Subreddit relevance 0-20
    $source = $thread['discovery_source'] ?? 'keyword';
    if ($source === 'both') $subRel = 25;
    elseif ($source === 'watchlist') $subRel = 20;
    else $subRel = 5;

    // Keyword match strength 0-20
    $kwHits = is_array($thread['matched_keywords'] ?? null) ? count($thread['matched_keywords']) : 0;
    if ($kwHits >= 2) $kwScore = 20;
    elseif ($kwHits === 1) $kwScore = 10;
    else $kwScore = 5;

    // OP karma sanity 0-10 (defaults to 5 if unknown)
    $karma = $thread['author_karma'] ?? null;
    if ($karma === null) $karmaScore = 5;
    elseif ($karma >= 50) $karmaScore = 10;
    elseif ($karma >= 10) $karmaScore = 5;
    else $karmaScore = 0;

    $total = $recency + $comments + $subRel + $kwScore + $karmaScore;
    return max(0, min(100, $total));
}

// ─── AI relevance (neutral framing) ───

/**
 * Ask Gemini to score software-recommendation intent on a 0–10 scale.
 * Neutral prompt — does NOT ask "should the founder reply?" since that
 * biases toward yes-answers. Caller decides whether to reply.
 *
 * Returns ['score' => int 0-10, 'reason' => string] on success,
 * or ['score' => null, 'reason' => '<error>'] on failure.
 */
function reddit_ai_relevance(array $thread, array $topComments, $pdo = null): array
{
    $systemPrompt = <<<'SYS'
You are evaluating a Reddit thread for software-recommendation intent. Be conservative — most threads do NOT qualify.

A thread qualifies (score 7+) only if ALL of the following are true:
- The OP is actively looking for, or open to switching, bookkeeping / accounting / invoicing software
- The OP describes a real problem in their own words (not a generic "what do you guys use" karma-farming post)
- The thread is not already saturated with software recommendations (look at the top comments — if 3+ commenters already recommended QuickBooks, Wave, FreshBooks, etc., the conversation is done)
- The OP appears to be a real person running a small business, freelancing, or doing a side hustle (not a corporate procurement question or a student homework post)

A thread does NOT qualify (score ≤4) if:
- It's a question about tax filing, accounting concepts, or bookkeeping methodology (not software)
- It's a "best of" / "favorite tool" karma post with no specific problem
- It's a complaint thread with no openness to alternatives
- It's about an industry too large for Argo Books (enterprise, mid-market with employees, multi-entity)

Score 5–6 for borderline cases — relevant but ambiguous fit.

Return JSON only: {"relevance": <0-10 integer>, "reason": "<one sentence>"}

Do NOT consider whether any specific product is a good fit. Do NOT recommend a reply. Just score software-recommendation intent.
SYS;

    $commentsBlock = empty($topComments)
        ? '(no comments yet)'
        : implode("\n---\n", array_map(fn($c) => mb_substr($c, 0, 500), $topComments));

    $userPrompt = "Subreddit: r/" . ($thread['subreddit'] ?? '')
        . "\nTitle: " . ($thread['title'] ?? '')
        . "\nBody: " . (empty($thread['body']) ? '(link post, no body)' : mb_substr((string) $thread['body'], 0, 2000))
        . "\nComment count: " . (int) ($thread['comment_count'] ?? 0)
        . "\nTop 10 comments:\n" . $commentsBlock;

    // Few-shot learning: include recent founder-accepted threads (drafted or
    // replied) so the AI gradually calibrates to what's actually worked. Only
    // triggers when there's a meaningful sample (3+) to avoid noise from a
    // tiny set of early labels. The negatives block was removed to save
    // tokens — rejections are inferred from the rules text only.
    if ($pdo !== null) {
        $examples = reddit_fetch_label_examples($pdo);
        if (!empty($examples['positives']) && count($examples['positives']) >= 3) {
            $userPrompt .= "\n\n--- Examples the founder ACCEPTED (drafted or replied — treat similar threads as 7+) ---";
            foreach ($examples['positives'] as $ex) {
                $userPrompt .= "\n- r/{$ex['subreddit']} | {$ex['title']}";
            }
        }
    }

    $resp = call_gemini($systemPrompt, $userPrompt);
    if (!empty($resp['error'])) {
        return ['score' => null, 'reason' => 'gemini_error: ' . $resp['error']];
    }
    $content = $resp['content'] ?? '';
    $parsed = reddit_extract_json($content);
    if (!is_array($parsed) || !isset($parsed['relevance'])) {
        return ['score' => null, 'reason' => 'invalid_json'];
    }
    $score = (int) $parsed['relevance'];
    $score = max(0, min(10, $score));
    $reason = mb_substr((string) ($parsed['reason'] ?? ''), 0, 500);
    return ['score' => $score, 'reason' => $reason];
}

/**
 * Pull the most recent founder-accepted threads (drafted or replied) to use
 * as positive few-shot examples in the AI relevance prompt. Capped to a few
 * so the prompt doesn't balloon.
 *
 * Returns ['positives' => [...]] — kept as a single-key array (rather than a
 * flat list) so callers can be extended later without changing the shape.
 */
function reddit_fetch_label_examples($pdo, int $limitEach = 5): array
{
    $out = ['positives' => []];
    try {
        $posStmt = $pdo->prepare("
            SELECT subreddit, title
            FROM reddit_threads
            WHERE status IN ('replied', 'drafted')
              AND draft_body IS NOT NULL
              AND draft_body != ''
            ORDER BY status_updated_at DESC
            LIMIT $limitEach
        ");
        $posStmt->execute();
        $out['positives'] = $posStmt->fetchAll() ?: [];
    } catch (PDOException $e) {
        // Schema not ready or table empty — just return empties; AI runs without examples.
    }
    return $out;
}

// ─── Draft generation ───

/**
 * Generate a Reddit reply draft for a thread. Uses REDDIT_VOICE_DOC as the
 * system instruction. Returns ['body' => string] on success or
 * ['error' => string] on failure.
 */
function reddit_generate_draft(array $thread, array $topComments, array $regenContext = []): array
{
    $systemPrompt = REDDIT_VOICE_DOC . "\n\n# Output rules for this task\n"
        . "You are drafting a Reddit reply for the founder to copy-paste manually. "
        . "Follow the voice rules strictly. Output ONLY the reply text — no preamble, no explanation, "
        . "no markdown formatting unless natural for Reddit. Do not include a URL. "
        . "Do not include disclosure unless mentioning Argo Books. Keep it short: 2–4 short paragraphs at most.";

    $commentsBlock = empty($topComments)
        ? '(no comments yet)'
        : implode("\n---\n", array_map(fn($c) => mb_substr($c, 0, 400), $topComments));

    $userPrompt = "Subreddit: r/" . ($thread['subreddit'] ?? '')
        . "\nThread title: " . ($thread['title'] ?? '')
        . "\nBody: " . (empty($thread['body']) ? '(link post, no body)' : mb_substr((string) $thread['body'], 0, 2000))
        . "\nTop comments (for context — don't repeat what others already said):\n" . $commentsBlock;

    // Regeneration context: when the founder rejected a previous draft, show
    // the AI what was wrong with it so the next version actually addresses
    // their feedback instead of producing a randomly-varied near-duplicate.
    $previousDraft = trim((string) ($regenContext['previous_draft'] ?? ''));
    $feedback = trim((string) ($regenContext['feedback'] ?? ''));
    if ($previousDraft !== '' || $feedback !== '') {
        $userPrompt .= "\n\n--- This is a regeneration request ---";
        if ($previousDraft !== '') {
            $userPrompt .= "\nPrevious draft you produced (the founder rejected this):\n"
                . mb_substr($previousDraft, 0, 2000);
        }
        if ($feedback !== '') {
            $userPrompt .= "\n\nFounder feedback on what to change:\n" . mb_substr($feedback, 0, 2000);
            $userPrompt .= "\n\nWrite a new reply that ACTUALLY addresses the feedback — not a cosmetic variation. If the feedback contradicts the voice doc, prefer the feedback.";
        } else {
            $userPrompt .= "\n\nWrite a meaningfully different reply (different angle or framing — not just reworded). Don't repeat the previous draft's structure.";
        }
    }

    $userPrompt .= "\n\nWrite the reply now.";

    $resp = call_gemini($systemPrompt, $userPrompt);
    if (!empty($resp['error'])) {
        return ['error' => $resp['error']];
    }
    $body = trim((string) ($resp['content'] ?? ''));
    if ($body === '') {
        return ['error' => 'empty_draft'];
    }
    // Defensive cleanup: strip em-dashes the voice doc bans, just in case.
    $body = str_replace(['—', '–'], '-', $body);
    return ['body' => $body];
}

// ─── Utility ───

/**
 * Best-effort extraction of a JSON object from a string that may include
 * Markdown code fences or surrounding prose. Gemini sometimes wraps JSON
 * in ```json ... ``` despite instruction to return only JSON.
 */
function reddit_extract_json(string $s): ?array
{
    $s = trim($s);
    if ($s === '') return null;

    // Strip code fences
    if (preg_match('/```(?:json)?\s*(\{.*\})\s*```/s', $s, $m)) {
        $s = $m[1];
    } else {
        // Find the first {...} block
        $start = strpos($s, '{');
        $end = strrpos($s, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $s = substr($s, $start, $end - $start + 1);
        }
    }

    $parsed = json_decode($s, true);
    return is_array($parsed) ? $parsed : null;
}

/**
 * Extract the t1_xxxxxx comment ID from a Reddit permalink. Accepts both
 * /r/.../comments/<post_id>/<slug>/<comment_id> and the bare-comment form.
 * Returns null if the URL is malformed.
 */
function reddit_extract_comment_id_from_permalink(string $url): ?string
{
    if (!preg_match('#reddit\.com/r/[^/]+/comments/[a-z0-9]+/[^/]*/([a-z0-9]+)/?#i', $url, $m)) {
        return null;
    }
    return $m[1];
}
