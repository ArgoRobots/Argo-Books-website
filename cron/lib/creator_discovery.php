<?php
/**
 * Creator / affiliate-partner discovery for the outreach pipeline.
 *
 * Finds content creators and publications whose audience is small-business
 * owners, freelancers, bookkeepers, or accountants, YouTubers, newsletter
 * writers, and niche bloggers, so we can pitch them the Argo Books AFFILIATE
 * program (50% recurring for a referred customer's first 12 months). This is a
 * money proposition, unlike the editorial channel's "add me to your roundup"
 * pitch, so the audiences overlap but the ask is different.
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
 *     they can be worked manually or via the assisted-captcha helper.
 *   - LinkedIn blocks scraping; profile URLs are surfaced as a manual list only
 *     (no auto email, no auto draft). See creator_platform_from_url().
 */

require_once __DIR__ . '/shopify_discovery.php'; // serpapi_query_cached(), _shopify_url_is_safe_external(), + outreach_helpers
require_once __DIR__ . '/editorial_discovery.php'; // hunter_find_email(), editorial_domain_from_url(), editorial_detect_affiliate()

/**
 * Topic queries that surface creators/publications in our niche. Kept broad on
 * intent (audience = freelancers / small business / bookkeeping) and mixed
 * across platforms; creator_platform_from_url() then classifies each result.
 */
const CREATOR_QUERY_POOL = [
    'best accounting software youtube review',
    'bookkeeping tips for freelancers youtube',
    'small business finance youtube channel',
    'quickbooks alternative review youtube',
    'freelance finances newsletter',
    'small business bookkeeping newsletter',
    'accounting software review blog',
    'bookkeeping for small business blog',
    'freelancer tax and bookkeeping tips',
    'solopreneur finance newsletter',
    'best free accounting software for freelancers',
    'invoicing tips for small business owners',
];

/** Newsletter-platform hosts we treat as "newsletter" for the pitch angle. */
const CREATOR_NEWSLETTER_HOSTS = [
    'substack.com', 'beehiiv.com', 'ghost.io', 'buttondown.com', 'buttondown.email',
    'convertkit.com', 'kit.com', 'mailchimp.com', 'revue', 'letterdrop.com',
];

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
        // Keep only the channel-identifying first path segment (@handle) or the
        // /channel|/c|/user + name pair; strip /watch, /shorts, etc.
        if (preg_match('#^/(@[^/]+)#', $path, $m)) {
            $path = '/' . $m[1];
        } elseif (preg_match('#^/(channel|c|user)/([^/]+)#', $path, $m)) {
            $path = '/' . $m[1] . '/' . $m[2];
        } else {
            $path = ''; // a bare youtube.com/watch?v=... has no channel identity here
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
        // Skip our own site and generic aggregators that aren't a single creator.
        if ($domain === '' || str_contains($domain, 'argorobots.com')) {
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

    $fetched = fetch_website_text($url);
    if ($fetched === null) {
        // YouTube and some platforms are JS-heavy / block scrapers. Fall back to
        // the SERP title so the candidate is still usable (email may be blank).
        if ($serpTitle === '') {
            return $fail('fetch_failed', 'Could not fetch the page and no SERP title to fall back on', $url, ['platform' => $platform]);
        }
        $fetched = ['text' => $serpTitle, 'html' => ''];
    }

    // One Gemini call: is the audience a fit, who is the creator, what do they cover?
    $systemPrompt = <<<'PROMPT'
You analyze a web page (a YouTube channel/video, a newsletter, or a blog) to decide
whether its AUDIENCE is a good fit to promote Argo Books, a free bookkeeping and
invoicing app for small businesses and freelancers, as an affiliate partner. Return
ONLY a JSON object with exactly these keys:
{"is_relevant": boolean, "creator_name": string|null, "audience": string|null, "topics": string[], "external_site": string|null}

- is_relevant: true only if the creator/publication regularly reaches small-business
  owners, freelancers, solopreneurs, bookkeepers, accountants, or similar people who
  would use accounting/invoicing software. A general/unrelated channel is NOT relevant.
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
    $creatorName  = trim((string) ($parsed['creator_name'] ?? '')) ?: ($serpTitle !== '' ? $serpTitle : ucfirst($domain));
    $audience     = trim((string) ($parsed['audience'] ?? ''));
    $topics       = is_array($parsed['topics'] ?? null) ? array_slice(array_map('strval', $parsed['topics']), 0, 6) : [];
    $externalSite = trim((string) ($parsed['external_site'] ?? ''));

    $baseMeta = [
        'platform'     => $platform,
        'creator_name' => mb_substr($creatorName, 0, 150),
        'audience'     => mb_substr($audience, 0, 200),
        'topics'       => $topics,
        'domain'       => $domain,
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
            ? ' No email auto-found' . ($platform === 'youtube' ? ' (YouTube hides it behind a captcha, contact via their channel or the assisted-email helper).' : '.')
            : '')
        . ' Goal: recruit as an Argo Books affiliate partner.';

    $meta = $baseMeta + [
        'email'            => $email ?? '',
        'email_source'     => $emailSource,
        'external_site'    => $externalSite,
        'needs_manual_email' => $needsManualEmail,
        'business_summary' => mb_substr($summary, 0, 1000),
    ];

    return ['fit' => true, 'reason' => 'fit', 'detail' => 'Relevant creator', 'final_url' => $url, 'metadata' => $meta];
}
