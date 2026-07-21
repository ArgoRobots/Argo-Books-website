<?php
/**
 * Creator / affiliate-partner discovery for the outreach pipeline.
 *
 * Finds YouTubers and newsletter writers whose audience is small-business
 * owners, freelancers, bookkeepers, or accountants, so we can pitch them the Argo
 * Books AFFILIATE program (50% recurring for a referred customer's first 12
 * months). This is a money proposition, unlike the editorial channel's "add me to
 * your roundup" pitch. Blogs and roundup/listicle sites deliberately belong to the
 * Editorial channel, not here, so this channel skips the 'blog' platform.
 *
 * Reuses the shared discovery helpers (paginated SerpAPI cache, page fetch,
 * email scrape, Gemini) so it inherits the same pagination + target-count loop
 * behavior the editorial and shopify channels use. Pure helpers only: no DB
 * writes here beyond the SerpAPI cache the wrapper owns; the caller orchestrates
 * candidate/lead inserts.
 *
 * Honest limits, baked into the design:
 *   - YouTube deliberately hides the About-page business email behind a captcha,
 *     so email is harvested only when a creator links a scrapeable site/Linktree.
 *     Channels without a findable email are still imported (email left blank) so
 *     they can be worked from the lead's "Get email" button (opens the channel).
 *   - LinkedIn blocks scraping; profile URLs are surfaced as a manual list only
 *     (no auto email, no auto draft). See creator_platform_from_url().
 */

require_once __DIR__ . '/shopify_discovery.php'; // serpapi_query_cached(), _shopify_url_is_safe_external(), + outreach_helpers
require_once __DIR__ . '/editorial_discovery.php'; // hunter_find_email(), editorial_domain_from_url(), editorial_detect_affiliate()

/**
 * Topic queries that surface YouTubers and newsletter writers in our niche
 * (audience = freelancers / small business / bookkeeping). Deliberately steered
 * at video + newsletter results; creator_platform_from_url() classifies each, and
 * plain blogs are dropped (they are Editorial-channel targets).
 */
const CREATOR_QUERY_POOL = [
    'best accounting software youtube review',
    'bookkeeping tips for freelancers youtube',
    'small business finance youtube channel',
    'quickbooks alternative review youtube',
    'accounting software review youtube',
    'bookkeeping tutorial youtube channel',
    'freelance finances newsletter',
    'small business bookkeeping newsletter',
    'solopreneur finance newsletter',
    'freelancer money newsletter substack',
    'small business accounting substack',
    'bookkeeping newsletter for freelancers',
];

/** Newsletter-platform hosts we treat as "newsletter" for the pitch angle. */
const CREATOR_NEWSLETTER_HOSTS = [
    'substack.com', 'beehiiv.com', 'ghost.io', 'buttondown.com', 'buttondown.email',
    'convertkit.com', 'kit.com', 'mailchimp.com', 'revue', 'letterdrop.com',
];

/**
 * Hosts that are never a recruitable affiliate partner and should be dropped
 * before we spend any AI on them. Three kinds:
 *   - communities / forums / Q&A (a subreddit is not a creator you can recruit)
 *   - software marketplaces / review aggregators (not a creator at all)
 *   - major mainstream publishers (they won't join a small app's affiliate
 *     program via a cold email; those belong in the Editorial channel instead)
 * Matched as a substring of the registrable host.
 */
const CREATOR_EXCLUDED_HOSTS = [
    // Communities / forums / social / Q&A
    'reddit.com', 'quora.com', 'wikipedia.org', 'wikihow.com', 'stackexchange.com',
    'stackoverflow.com', 'facebook.com', 'twitter.com', 'x.com', 'pinterest.com',
    'yelp.com', 'glassdoor.com', 'indeed.com',
    // Software marketplaces / review aggregators
    'g2.com', 'capterra.com', 'getapp.com', 'softwareadvice.com', 'trustpilot.com',
    'producthunt.com', 'crunchbase.com', 'sourceforge.net', 'slashdot.org',
    // Major mainstream publishers (Editorial-channel targets, not affiliates to recruit)
    'forbes.com', 'nerdwallet.com', 'pcmag.com', 'cnet.com', 'techradar.com',
    'businessinsider.com', 'investopedia.com', 'entrepreneur.com', 'inc.com',
    'nytimes.com', 'wsj.com', 'bankrate.com', 'fool.com', 'usnews.com', 'zdnet.com',
];

/**
 * Extract a YouTube channel URL from a watch/video page's raw HTML, so a video
 * result becomes a channel lead (the "open" link and the Get-email flow both want
 * the channel, not the video). Returns a channel URL or null.
 */
function youtube_channel_from_html(string $html): ?string
{
    if ($html === '') {
        return null;
    }
    if (preg_match('#"canonicalBaseUrl":"(/@[^"/]+)"#', $html, $m)) {
        return 'https://www.youtube.com' . $m[1];
    }
    if (preg_match('#ownerProfileUrl":"https?://www\.youtube\.com/(@[^"/]+)"#', $html, $m)) {
        return 'https://www.youtube.com/' . $m[1];
    }
    if (preg_match('#"canonicalBaseUrl":"(/channel/UC[\w-]+)"#', $html, $m)) {
        return 'https://www.youtube.com' . $m[1];
    }
    if (preg_match('#"channelId":"(UC[\w-]+)"#', $html, $m)) {
        return 'https://www.youtube.com/channel/' . $m[1];
    }
    return null;
}

/**
 * Extract the CHANNEL name (not the video title) from a YouTube page's raw HTML,
 * so the lead is named after the creator instead of a truncated video headline.
 * Returns the channel name or null.
 */
function youtube_channel_name_from_html(string $html): ?string
{
    if ($html === '') {
        return null;
    }
    if (preg_match('#"ownerChannelName":"([^"]{1,120})"#', $html, $m)) {
        return json_decode('"' . $m[1] . '"') ?: $m[1];
    }
    if (preg_match('#"author":"([^"]{1,120})"#', $html, $m)) {
        return json_decode('"' . $m[1] . '"') ?: $m[1];
    }
    if (preg_match('#<link itemprop="name" content="([^"]{1,120})">#', $html, $m)) {
        return html_entity_decode($m[1], ENT_QUOTES);
    }
    return null;
}

/**
 * Derive a human-readable creator name from a URL when no real channel/creator
 * name could be extracted (YouTube fetches are frequently blocked). Prefer the
 * @handle or channel slug that is already in the URL over the bare domain, so a
 * lead never shows as just "youtube.com". Falls back to the domain only when the
 * URL carries no usable identity.
 */
function creator_name_from_url(string $url): string
{
    $parts = parse_url($url);
    $host  = strtolower((string) ($parts['host'] ?? ''));
    $path  = (string) ($parts['path'] ?? '');

    if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
        if (preg_match('~/(@[^/?#]+)~', $path, $m)) {
            return $m[1];                       // @handle, e.g. @paperandspark
        }
        if (preg_match('~/(?:channel|c|user)/([^/?#]+)~', $path, $m)) {
            return $m[1];                       // legacy channel slug
        }
    }

    return editorial_domain_from_url($url);
}

/** True if a URL's host is on the never-recruit blocklist above. */
function creator_host_excluded(string $url): bool
{
    $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
    if ($host === '') {
        return false;
    }
    foreach (CREATOR_EXCLUDED_HOSTS as $bad) {
        if (str_contains($host, $bad)) {
            return true;
        }
    }
    return false;
}

/**
 * Classify a result URL into a creator platform. Drives the pitch angle and,
 * critically, whether we even try to auto-harvest an email.
 *
 * @return string one of 'youtube' | 'newsletter' | 'linkedin' | 'blog'
 */
function creator_platform_from_url(string $url): string
{
    $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?: ''));
    if ($host === '') {
        return 'blog';
    }
    if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
        return 'youtube';
    }
    if (str_contains($host, 'linkedin.com')) {
        return 'linkedin';
    }
    foreach (CREATOR_NEWSLETTER_HOSTS as $n) {
        if (str_contains($host, $n)) {
            return 'newsletter';
        }
    }
    return 'blog';
}

/**
 * Canonicalize a creator URL to a stable identity for dedup.
 *
 * For YouTube, collapse to the channel (/@handle, /channel/ID, /c/name, /user/name)
 * so many video links to one creator dedup to a single candidate. For everything
 * else, lowercase scheme+host, keep the path, drop query/fragment (same rule as
 * editorial). Returns the trimmed input on parse failure.
 */
function creator_canonical_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }
    $parts = parse_url($url);
    if ($parts === false || empty($parts['host'])) {
        return $url;
    }
    $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : 'https';
    $host   = strtolower($parts['host']);
    $path   = $parts['path'] ?? '';

    if (str_contains($host, 'youtube.com')) {
        // Prefer the channel identity (@handle or /channel|/c|/user + name).
        if (preg_match('#^/(@[^/]+)#', $path, $m)) {
            $path = '/' . $m[1];
        } elseif (preg_match('#^/(channel|c|user)/([^/]+)#', $path, $m)) {
            $path = '/' . $m[1] . '/' . $m[2];
        } else {
            // A /watch, /shorts, or other non-channel URL: keep the full path AND
            // query (the ?v= id) so the "open" link actually resolves to the video
            // instead of collapsing to youtube.com. Identity is then the video.
            $query = (isset($parts['query']) && $parts['query'] !== '') ? '?' . $parts['query'] : '';
            return $scheme . '://' . $host . rtrim($path, '/') . $query;
        }
    } else {
        $path = rtrim($path, '/');
    }

    return $scheme . '://' . $host . $path;
}

/**
 * Run one creator SERP query (paginated) and return canonicalized candidate URLs
 * with their SERP title and detected platform. Reuses the shared SerpAPI cache
 * wrapper, so it inherits the same page offset + 14-day cache behavior.
 *
 * @return array{results: array<int, array{url:string,title:string,platform:string}>, from_cache: bool}
 */
function creator_search(string $query, string $apiKey, int $limit, PDO $pdo, int $start = 0): array
{
    $resp = serpapi_query_cached($query, $apiKey, $limit, $pdo, $start);
    $out  = [];
    $seen = [];
    foreach ($resp['results'] as $r) {
        $link = (string) ($r['link'] ?? '');
        if ($link === '') {
            continue;
        }
        $canon = creator_canonical_url($link);
        if ($canon === '' || isset($seen[$canon])) {
            continue;
        }
        $domain = editorial_domain_from_url($canon);
        // Skip our own site, and communities/aggregators/major publishers that are
        // not recruitable affiliate partners (drop them before spending any AI).
        if ($domain === '' || str_contains($domain, 'argorobots.com') || creator_host_excluded($canon)) {
            continue;
        }
        $seen[$canon] = true;
        $out[] = [
            'url'      => $canon,
            'title'    => (string) ($r['title'] ?? ''),
            'platform' => creator_platform_from_url($canon),
        ];
    }
    return ['results' => $out, 'from_cache' => $resp['from_cache']];
}

/**
 * Evaluate a candidate creator/publication: is its audience a fit for an Argo
 * Books affiliate pitch, who is it, and can we find a contact email? Returns a
 * result shaped like the other evaluators (fit/reason/detail/final_url/metadata).
 *
 * $force skips the relevance gate for a hand-added URL (a deliberate operator
 * choice), and always allows import even with no email found.
 *
 * @return array{fit: bool, reason: string, detail: string, final_url: string, metadata: array<string,mixed>}
 */
function evaluate_creator_candidate(string $url, PDO $pdo, string $serpTitle = '', bool $force = false): array
{
    $fail = static function (string $reason, string $detail, string $finalUrl, array $meta = []): array {
        return ['fit' => false, 'reason' => $reason, 'detail' => $detail, 'final_url' => $finalUrl, 'metadata' => $meta];
    };

    if (!_shopify_url_is_safe_external($url)) {
        return $fail('unsafe_url', 'URL resolves to a private/reserved address', $url);
    }

    $platform = creator_platform_from_url($url);
    $domain   = editorial_domain_from_url($url);

    // LinkedIn is a manual list only: we can surface the profile URL but cannot
    // scrape contents or an email (blocked + against ToS). Import with no email
    // and no draft so the operator can reach out by hand.
    if ($platform === 'linkedin') {
        if (!$force) {
            return $fail('linkedin_manual', 'LinkedIn profiles are a manual list (no scraping)', $url, [
                'platform' => 'linkedin', 'domain' => $domain,
            ]);
        }
        return [
            'fit' => true, 'reason' => 'fit', 'detail' => 'LinkedIn profile (manual outreach)', 'final_url' => $url,
            'metadata' => [
                'platform'         => 'linkedin',
                'creator_name'     => $serpTitle !== '' ? mb_substr($serpTitle, 0, 150) : $domain,
                'domain'           => $domain,
                'email'            => '',
                'email_source'     => null,
                'audience'         => '',
                'business_summary' => mb_substr('LinkedIn profile: ' . ($serpTitle !== '' ? $serpTitle : $url) . '. Manual outreach only; no email harvested.', 0, 1000),
            ],
        ];
    }

    // Plain blogs and roundup/listicle sites belong in the Editorial channel, not
    // here. Partners is YouTubers + newsletter writers only. (A hand-added URL with
    // $force still imports, so the operator can override for a specific site.)
    if ($platform === 'blog' && !$force) {
        return $fail('belongs_in_editorial', 'Blogs/roundup sites belong in the Editorial channel', $url, [
            'platform' => 'blog', 'domain' => $domain,
        ]);
    }

    $fetched = fetch_website_text($url);
    if ($fetched === null) {
        // YouTube and some platforms are JS-heavy / block scrapers. Fall back to
        // the SERP title so the candidate is still usable (email may be blank).
        if ($serpTitle === '') {
            return $fail('fetch_failed', 'Could not fetch the page and no SERP title to fall back on', $url, ['platform' => $platform]);
        }
        $fetched = ['text' => $serpTitle, 'html' => ''];
    }

    // For YouTube video results, resolve the channel so the lead points at the
    // channel (its About page is where the email lives), not a single video.
    $channelUrl = ($platform === 'youtube') ? youtube_channel_from_html($fetched['html'] ?? '') : null;

    // One Gemini call: is the audience a fit, who is the creator, what do they cover?
    $systemPrompt = <<<'PROMPT'
You analyze a web page (a YouTube channel/video or a newsletter) to decide
whether its AUDIENCE is a good fit to promote Argo Books, a free bookkeeping and
invoicing app for small businesses and freelancers, as an affiliate partner. Return
ONLY a JSON object with exactly these keys:
{"is_relevant": boolean, "creator_name": string|null, "audience": string|null, "topics": string[], "external_site": string|null}

- is_relevant: true ONLY if BOTH hold: (a) it regularly reaches small-business
  owners, freelancers, solopreneurs, bookkeepers, or accountants who would use
  accounting/invoicing software, AND (b) it is an INDEPENDENT YouTube channel or
  newsletter that could realistically join an affiliate program and promote a tool
  to its audience. It is NOT relevant if it is a plain blog or a roundup/listicle
  site (those are handled by a separate channel), a major mainstream publisher
  (e.g. Forbes, NerdWallet, PCMag), a community/forum/Q&A site (e.g. Reddit,
  Quora), a software marketplace or review aggregator (e.g. G2, Capterra), or a
  software vendor's own site. When unsure, return false.
- creator_name: the person's or publication's name/handle, or null.
- audience: one short phrase describing who they reach (e.g. "freelance designers", "small e-commerce owners").
- topics: up to 6 short topic tags the creator covers.
- external_site: if the page shows a personal website, Linktree, or contact link OFF this
  domain, return that URL (used to find a contact email). Otherwise null.
No preamble, JSON only.
PROMPT;

    $res = call_gemini($systemPrompt, "URL: {$url}\nPlatform: {$platform}\nSERP title: {$serpTitle}\n\nPage text:\n" . $fetched['text']);
    if (!empty($res['error']) || empty($res['content'])) {
        return $fail('ai_error', 'AI evaluation failed: ' . ($res['error'] ?? 'empty'), $url, ['platform' => $platform]);
    }
    $content = preg_replace('/^```json\s*/i', '', trim((string) $res['content']));
    $content = preg_replace('/\s*```$/', '', $content);
    $parsed  = json_decode($content, true);
    if (!is_array($parsed)) {
        return $fail('ai_parse_failed', 'AI returned malformed JSON', $url, ['platform' => $platform]);
    }

    $isRelevant   = !empty($parsed['is_relevant']);
    $creatorName  = trim((string) ($parsed['creator_name'] ?? '')) ?: ($serpTitle !== '' ? $serpTitle : creator_name_from_url($url));
    // For YouTube, prefer the real channel name off the page over the AI's guess
    // or the SERP video title (which is what was making leads show a video headline).
    if ($platform === 'youtube') {
        $ytName = youtube_channel_name_from_html($fetched['html'] ?? '');
        if ($ytName !== null && trim($ytName) !== '') {
            $creatorName = trim($ytName);
        }
    }
    $audience     = trim((string) ($parsed['audience'] ?? ''));
    $topics       = is_array($parsed['topics'] ?? null) ? array_slice(array_map('strval', $parsed['topics']), 0, 6) : [];
    $externalSite = trim((string) ($parsed['external_site'] ?? ''));

    $baseMeta = [
        'platform'     => $platform,
        'creator_name' => mb_substr($creatorName, 0, 150),
        'audience'     => mb_substr($audience, 0, 200),
        'topics'       => $topics,
        'domain'       => $domain,
        'channel_url'  => $channelUrl ?? '',
    ];

    if (!$isRelevant && !$force) {
        return $fail('not_relevant', 'Audience is not a fit for Argo Books', $url, $baseMeta);
    }

    // Find a contact email. Blogs/newsletters: scrape the site directly. YouTube:
    // the email is captcha-gated, so the only automatable path is a site/Linktree
    // the creator links (external_site) or their own domain if it isn't youtube.
    $email       = null;
    $emailSource = null;

    $scrapeTargets = [];
    if ($externalSite !== '' && filter_var($externalSite, FILTER_VALIDATE_EMAIL) === false) {
        $scrapeTargets[] = $externalSite;
    }
    if ($platform !== 'youtube' && $domain !== '') {
        $scrapeTargets[] = 'https://' . $domain . '/';
    }
    foreach ($scrapeTargets as $target) {
        if (!_shopify_url_is_safe_external($target)) {
            continue;
        }
        $scraped = scrape_email_from_website($target);
        if ($scraped && filter_var($scraped, FILTER_VALIDATE_EMAIL)) {
            $email = $scraped;
            $emailSource = 'scraped';
            break;
        }
    }
    // Hunter fallback by creator name on a real (non-youtube) domain.
    if ($email === null && $platform !== 'youtube' && $creatorName !== '' && $domain !== '') {
        $hunterKey = $_ENV['HUNTER_API_KEY'] ?? '';
        if ($hunterKey !== '') {
            $hit = hunter_find_email($domain, $creatorName, $hunterKey);
            if ($hit !== null) {
                $email = $hit['email'];
                $emailSource = 'hunter(score ' . $hit['score'] . ')';
            }
        }
    }

    $topicList = $topics ? implode(', ', $topics) : 'general';
    $needsManualEmail = ($email === null);
    $summary = ucfirst($platform) . " creator: \"{$creatorName}\""
        . ($audience !== '' ? " reaching {$audience}" : '')
        . ". Covers: {$topicList}."
        . ($needsManualEmail
            ? ' No email auto-found' . ($platform === 'youtube' ? ' (YouTube hides it behind a captcha; use Get email on the lead to grab it from the channel).' : '.')
            : '')
        . ' Goal: recruit as an Argo Books affiliate partner.';

    $meta = $baseMeta + [
        'email'            => $email ?? '',
        'email_source'     => $emailSource,
        'external_site'    => $externalSite,
        'needs_manual_email' => $needsManualEmail,
        'business_summary' => mb_substr($summary, 0, 1000),
    ];

    // Point the lead at the channel when we resolved one (better "open" target
    // and the right page for the Get-email flow); otherwise the original URL.
    return ['fit' => true, 'reason' => 'fit', 'detail' => 'Relevant creator', 'final_url' => $channelUrl ?: $url, 'metadata' => $meta];
}
