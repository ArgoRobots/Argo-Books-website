<?php
/**
 * Editorial / roundup discovery helpers for the outreach pipeline.
 *
 * Finds the "best free accounting software" / "QuickBooks alternatives" style
 * listicles that outrank us, then finds the article's author so we can pitch
 * getting Argo Books added to the list. Being included in a page that already
 * ranks is worth more than our own page climbing.
 *
 * Pipeline per article:
 *   SerpAPI query -> candidate article URLs -> fetch + AI-extract (is it a real
 *   roundup? who wrote it? which tools does it already list? does it already
 *   mention Argo?) -> find the author's email (Hunter.io, else scrape the
 *   outlet's contact page) -> import as an outreach lead (source='editorial_auto').
 *
 * Reuses the SerpAPI cache wrapper + scrape/fetch/Gemini helpers already used by
 * the Shopify channel. Pure helpers: no DB writes here beyond the SerpAPI cache
 * the wrapper owns; the caller orchestrates candidate/lead inserts.
 */

require_once __DIR__ . '/shopify_discovery.php'; // serpapi_query_cached(), _shopify_url_is_safe_external(), + outreach_helpers

/**
 * Search queries we want Argo Books listed in. Each maps to a roundup/listicle
 * SERP; the top organic results are the pages that outrank us and are worth
 * getting added to.
 */
const EDITORIAL_QUERY_POOL = [
    'best free accounting software',
    'best free accounting software for small business',
    'best quickbooks alternatives',
    'quickbooks alternatives for small business',
    'best free invoicing software',
    'best free invoicing software for small business',
    'best free bookkeeping software',
    'best accounting software for freelancers',
    'best accounting software for self employed',
    'wave accounting alternatives',
    'best free accounting software for mac',
    'free desktop accounting software',
];

/**
 * Canonicalize an article URL: lowercase scheme+host, drop query/fragment, keep
 * the path (identity here is the specific article, not the outlet, unlike the
 * Shopify origin-only canonicalization). Strips a trailing slash so
 * "/best-tools" and "/best-tools/" dedup to one row.
 */
function editorial_canonical_url(string $url): string
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
    $path   = rtrim($path, '/');
    return $scheme . '://' . $host . $path;
}

/** Registrable-ish domain from a URL host (strips a leading www.). */
function editorial_domain_from_url(string $url): string
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!is_string($host) || $host === '') {
        return '';
    }
    return strtolower(preg_replace('/^www\./i', '', $host));
}

/**
 * Hunter.io Email Finder: given an outlet domain and an author's full name,
 * return ['email' => string, 'score' => int] or null. Best-effort; never throws.
 */
function hunter_find_email(string $domain, string $fullName, string $apiKey): ?array
{
    $domain   = trim($domain);
    $fullName = trim($fullName);
    if ($apiKey === '' || $domain === '' || $fullName === '') {
        return null;
    }

    $url = 'https://api.hunter.io/v2/email-finder?domain=' . urlencode($domain)
        . '&full_name=' . urlencode($fullName)
        . '&api_key=' . urlencode($apiKey);

    $context = stream_context_create([
        'http' => ['timeout' => 15, 'ignore_errors' => true, 'user_agent' => 'Mozilla/5.0'],
        'ssl'  => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);
    $raw = @file_get_contents($url, false, $context);
    if ($raw === false || $raw === '') {
        return null;
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded) || empty($decoded['data']['email'])) {
        return null;
    }
    $email = (string) $decoded['data']['email'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return null;
    }
    return ['email' => $email, 'score' => (int) ($decoded['data']['score'] ?? 0)];
}

/**
 * Run one editorial SERP query and return canonicalized candidate article URLs
 * with their SERP title/snippet. Reuses the shared SerpAPI cache wrapper.
 *
 * @return array{results: array<int, array{url:string,title:string,snippet:string}>, from_cache: bool}
 */
function editorial_search(string $query, string $apiKey, int $limit, PDO $pdo): array
{
    $resp = serpapi_query_cached($query, $apiKey, $limit, $pdo);
    $out  = [];
    $seen = [];
    foreach ($resp['results'] as $r) {
        $link = (string) ($r['link'] ?? '');
        if ($link === '') {
            continue;
        }
        $canon = editorial_canonical_url($link);
        if ($canon === '' || isset($seen[$canon])) {
            continue;
        }
        // Skip our own site and obvious non-article hosts.
        $domain = editorial_domain_from_url($canon);
        if ($domain === '' || str_contains($domain, 'argorobots.com')) {
            continue;
        }
        $seen[$canon] = true;
        $out[] = [
            'url'     => $canon,
            'title'   => (string) ($r['title'] ?? ''),
            'snippet' => (string) ($r['snippet'] ?? ''),
        ];
    }
    return ['results' => $out, 'from_cache' => $resp['from_cache']];
}

/**
 * Evaluate a candidate article: is it a real multi-tool roundup, who wrote it,
 * which tools does it already list, and can we find a contact email for the
 * author or outlet? Returns a Shopify-evaluator-shaped result.
 *
 * @return array{
 *   fit: bool, reason: string, detail: string, final_url: string,
 *   metadata: array<string,mixed>
 * }
 */
function evaluate_editorial_candidate(string $url, PDO $pdo, string $serpTitle = '', bool $force = false): array
{
    $fail = static function (string $reason, string $detail, string $finalUrl, array $meta = []): array {
        return ['fit' => false, 'reason' => $reason, 'detail' => $detail, 'final_url' => $finalUrl, 'metadata' => $meta];
    };

    if (!_shopify_url_is_safe_external($url)) {
        return $fail('unsafe_url', 'URL resolves to a private/reserved address', $url);
    }

    $fetched = fetch_website_text($url);
    if ($fetched === null) {
        return $fail('fetch_failed', 'Could not fetch the article page', $url);
    }

    $host   = parse_url($url, PHP_URL_HOST) ?: $url;
    $domain = editorial_domain_from_url($url);

    // AI extraction: one Gemini call over the article text.
    $systemPrompt = <<<'PROMPT'
You analyze a web page to decide if it is a "best/top software" ROUNDUP article
(a listicle comparing several accounting, bookkeeping, or invoicing tools) and to
pull structured facts from it. Return ONLY a JSON object with exactly these keys:
{"is_roundup": boolean, "article_title": string|null, "outlet_name": string|null, "author_name": string|null, "listed_tools": string[], "mentions_argo_books": boolean, "article_angle": string|null}

- is_roundup: true only if the page lists and compares MULTIPLE distinct software
  products (e.g. "10 best free accounting tools"). A single-product page, a
  pricing page, a vendor's own homepage, or a generic blog post is NOT a roundup.
- article_title: the article's real headline/title exactly as it appears on the
  page (the H1 or page title, e.g. "The 8 Best Free Accounting Software of 2025").
  Return the actual words on the page. Do NOT construct a title from the URL or its
  slug, and do NOT title-case a URL path. If you cannot find a real title, null.
- outlet_name: the publication/site name (e.g. "Forbes Advisor", "PCMag").
- author_name: the article's author/byline full name, or null if none is shown.
- listed_tools: the software product names the article lists (e.g. ["Wave","Zoho Books","FreshBooks"]). Empty array if none.
- mentions_argo_books: true if "Argo Books" is already listed or named on the page.
- article_angle: one short phrase describing the list's focus (e.g. "free tools for freelancers").
No preamble, JSON only.
PROMPT;

    $res = call_gemini($systemPrompt, "Article URL: {$url}\nSERP title: {$serpTitle}\n\nPage text:\n" . $fetched['text']);
    if (!empty($res['error']) || empty($res['content'])) {
        return $fail('ai_error', 'AI extraction failed: ' . ($res['error'] ?? 'empty'), $url);
    }
    $content = preg_replace('/^```json\s*/i', '', trim((string) $res['content']));
    $content = preg_replace('/\s*```$/', '', $content);
    $parsed  = json_decode($content, true);
    if (!is_array($parsed)) {
        return $fail('ai_parse_failed', 'AI returned malformed JSON', $url);
    }

    $isRoundup = !empty($parsed['is_roundup']);
    $articleTitle = trim((string) ($parsed['article_title'] ?? ''));
    $outlet    = trim((string) ($parsed['outlet_name'] ?? '')) ?: ucfirst($domain);
    $author    = trim((string) ($parsed['author_name'] ?? ''));
    $tools     = is_array($parsed['listed_tools'] ?? null) ? array_slice(array_map('strval', $parsed['listed_tools']), 0, 20) : [];
    $angle     = trim((string) ($parsed['article_angle'] ?? ''));
    $mentions  = !empty($parsed['mentions_argo_books']);

    $baseMeta = [
        'article_title' => mb_substr($articleTitle, 0, 200),
        'outlet_name'  => mb_substr($outlet, 0, 150),
        'author_name'  => mb_substr($author, 0, 120),
        'listed_tools' => $tools,
        'article_angle' => mb_substr($angle, 0, 200),
        'domain'       => $domain,
    ];

    // $force skips the roundup/mentions gates: a URL the operator adds by hand is
    // a deliberate choice, so import it even if the AI is unsure it's a roundup or
    // sees Argo already mentioned.
    if (!$isRoundup && !$force) {
        return $fail('not_a_roundup', 'Page is not a multi-tool roundup', $url, $baseMeta);
    }
    if ($mentions && !$force) {
        return $fail('already_lists_argo', 'Article already mentions Argo Books', $url, $baseMeta);
    }

    // Find a contact email: Hunter.io by author name first, then scrape the
    // outlet's own site for a general/contact address as a fallback.
    $email       = null;
    $emailSource = null;
    $hunterKey   = $_ENV['HUNTER_API_KEY'] ?? '';
    if ($author !== '' && $hunterKey !== '' && $domain !== '') {
        $hit = hunter_find_email($domain, $author, $hunterKey);
        if ($hit !== null) {
            $email = $hit['email'];
            $emailSource = 'hunter(score ' . $hit['score'] . ')';
        }
    }
    if ($email === null) {
        $scraped = scrape_email_from_website('https://' . $domain . '/');
        if ($scraped && filter_var($scraped, FILTER_VALIDATE_EMAIL)) {
            $email = $scraped;
            $emailSource = 'scraped';
        }
    }
    if ($email === null && !$force) {
        return $fail('no_contact_found', 'No author or outlet email found', $url, $baseMeta);
    }

    // Article context stored on the lead so the draft prompt can personalize.
    // Prefer the AI-extracted on-page title; fall back to the SERP title. Only a
    // real title is quoted, so the draft never invents one from the URL slug.
    $toolList  = $tools ? implode(', ', array_slice($tools, 0, 10)) : 'none detected';
    $realTitle = $articleTitle !== '' ? $articleTitle : $serpTitle;
    $summary   = ($realTitle !== ''
            ? "Roundup article titled \"{$realTitle}\" on {$outlet}"
            : "Roundup article on {$outlet} (real title unknown, do not quote a title)")
        . ($author !== '' ? " by {$author}" : '')
        . ". Already lists: {$toolList}."
        . ($angle !== '' ? " Focus: {$angle}." : '')
        . " Argo Books is NOT yet included. Goal: get it added to the list.";

    $meta = $baseMeta + [
        'email'        => $email ?? '',
        'email_source' => $emailSource,
        'article_url'  => $url,
        'business_summary' => mb_substr($summary, 0, 1000),
    ];

    return ['fit' => true, 'reason' => 'fit', 'detail' => 'Roundup with a findable contact', 'final_url' => $url, 'metadata' => $meta];
}
