<?php
/**
 * Shopify-store discovery helpers used by the outreach pipeline.
 *
 * Discovers small, recent Canadian Shopify stores via SerpAPI dorking + storefront
 * fitness checks. CASL implied-consent: emails come exclusively from the store's own
 * contact page via scrape_email_from_website().
 *
 * Pure helpers: no DB writes, no cron-state mutations. Caller (stepDiscoverShopify)
 * orchestrates DB + state.
 */

require_once __DIR__ . '/outreach_helpers.php';

/**
 * Reject URLs whose host resolves to a private, reserved, or loopback address.
 * Defends against SSRF from crafted redirect chains. Returns true if the URL is
 * safe to fetch from a server-side context.
 */
function _shopify_url_is_safe_external(string $url): bool
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!is_string($host) || $host === '') {
        return false;
    }
    // If already a literal IP, validate directly
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    // Resolve hostname (synchronous; OK in cron context)
    $ip = gethostbyname($host);
    if ($ip === $host) {
        return false;  // resolution failed
    }
    return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
}

// Dork pool biased toward NEW Canadian and US Shopify stores. Each dork
// pairs a "newness" phrase (founded/launched/just opened in the last year
// or two) with a quoted country signal ("made in canada" / "made in usa" /
// etc.). Negative terms exclude stores that openly advertise being decades
// old, which the post-fetch business-age check would reject anyway —
// filtering at the search level saves credits on candidates we'd otherwise
// pay to evaluate and throw out.
//
// SHOPIFY_DORK_EXCLUSIONS is appended to every dork. Add new exclusions here
// when you find a class of leads that always rejects.
const SHOPIFY_DORK_EXCLUSIONS = ' -"since 19" -"since 200" -"established 19" -"established 200" -"est. 19" -"est. 200"';

const SHOPIFY_DORK_POOL = [
    // ─── Canada — every dork pairs a newness signal with a quoted CA phrase ───
    'site:myshopify.com "founded in 2024" "made in canada"',
    'site:myshopify.com "founded in 2025" "made in canada"',
    'site:myshopify.com "launched in 2024" "based in canada"',
    'site:myshopify.com "launched in 2025" "based in canada"',
    'site:myshopify.com "just opened" "made in canada"',
    'site:myshopify.com "new shop" "based in canada"',
    'site:myshopify.com "starting out" "based in canada"',
    'site:myshopify.com "small business" "founded in 2024" "made in canada"',
    'site:myshopify.com "small business" "founded in 2025" "made in canada"',
    'site:myshopify.com "young company" "based in canada"',
    'site:myshopify.com "new brand" "made in canada"',
    'site:myshopify.com "small batch" "made in canada"',

    // ─── United States — same structure with US signals ───
    'site:myshopify.com "founded in 2024" "made in usa"',
    'site:myshopify.com "founded in 2025" "made in usa"',
    'site:myshopify.com "launched in 2024" "made in usa"',
    'site:myshopify.com "launched in 2025" "made in usa"',
    'site:myshopify.com "just opened" "made in usa"',
    'site:myshopify.com "new shop" "made in the usa"',
    'site:myshopify.com "starting out" "made in usa"',
    'site:myshopify.com "small business" "founded in 2024" "made in usa"',
    'site:myshopify.com "small business" "founded in 2025" "made in usa"',
    'site:myshopify.com "new brand" "made in usa"',
    'site:myshopify.com "small batch" "made in america"',
    'site:myshopify.com "young company" "made in usa"',
];

/**
 * Query SerpAPI for organic results matching $query.
 *
 * @param string $query   Google dork query string
 * @param string $apiKey  SerpAPI API key
 * @param int    $limit   Max results (default 100 — Google's per-page cap;
 *                        SerpAPI bills per request not per result, so 100
 *                        is the most efficient single-call value)
 * @return array          Array of ['link'=>..., 'title'=>..., 'snippet'=>...] entries
 */
function serpapi_query(string $query, string $apiKey, int $limit = 100): array
{
    $url = 'https://serpapi.com/search.json?engine=google'
        . '&q=' . urlencode($query)
        . '&api_key=' . urlencode($apiKey)
        . '&num=' . (int) $limit
        . '&hl=en&gl=ca';

    $context = stream_context_create([
        'http' => [
            'timeout'          => 30,
            'follow_location'  => 1,
            'max_redirects'    => 3,
            'user_agent'       => 'Mozilla/5.0',
            'ignore_errors'    => true,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $attempt = 0;
    $raw     = false;
    while ($attempt < 2) {
        $raw = @file_get_contents($url, false, $context);
        if ($raw !== false && $raw !== '') {
            break;
        }
        $attempt++;
        if ($attempt < 2) {
            sleep(1);
        }
    }

    if ($raw === false || $raw === '') {
        error_log("serpapi_query: empty/false response for query: {$query}");
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        error_log('serpapi_query: JSON decode failure for query: ' . $query);
        return [];
    }

    if (empty($decoded['organic_results']) || !is_array($decoded['organic_results'])) {
        error_log('serpapi_query: no organic_results for query: ' . $query);
        return [];
    }

    $results = [];
    foreach (array_slice($decoded['organic_results'], 0, $limit) as $item) {
        $results[] = [
            'link'    => isset($item['link'])    ? (string) $item['link']    : '',
            'title'   => isset($item['title'])   ? (string) $item['title']   : '',
            'snippet' => isset($item['snippet']) ? (string) $item['snippet'] : '',
        ];
    }

    return $results;
}

/**
 * Cache-aware wrapper around serpapi_query.
 *
 * Returns ['results' => array, 'from_cache' => bool]. Callers MUST only
 * increment the daily SerpAPI counter when from_cache is false.
 *
 * Cache key: SHA-256 of "{query}|num={limit}" so different limits don't
 * share cache entries. Empty result sets are NOT cached so transient SerpAPI
 * failures get retried on the next run instead of returning [] for 14 days.
 */
const SERPAPI_CACHE_TTL_DAYS = 14;

function serpapi_query_cached(string $query, string $apiKey, int $limit, PDO $pdo): array
{
    $cacheKey = hash('sha256', $query . '|num=' . $limit);

    $stmt = $pdo->prepare(
        "SELECT response_json FROM serpapi_response_cache
         WHERE query_hash = ? AND fetched_at > DATE_SUB(NOW(), INTERVAL " . SERPAPI_CACHE_TTL_DAYS . " DAY)
         LIMIT 1"
    );
    $stmt->execute([$cacheKey]);
    $row = $stmt->fetch();

    if ($row !== false) {
        $cached = json_decode((string) $row['response_json'], true);
        if (is_array($cached)) {
            return ['results' => $cached, 'from_cache' => true];
        }
    }

    // Cache miss → live SerpAPI call
    $results = serpapi_query($query, $apiKey, $limit);

    // Only cache non-empty results. An empty array could be either a
    // legitimately empty result set or a transient HTTP/JSON failure,
    // and caching the latter would block retry for 14 days.
    if (!empty($results)) {
        $ins = $pdo->prepare(
            "INSERT INTO serpapi_response_cache (query_hash, query_text, response_json)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE
                 response_json = VALUES(response_json),
                 fetched_at    = CURRENT_TIMESTAMP"
        );
        $ins->execute([
            $cacheKey,
            mb_substr($query, 0, 500),
            json_encode($results, JSON_UNESCAPED_SLASHES),
        ]);
    }

    return ['results' => $results, 'from_cache' => false];
}

/**
 * Canonicalize a candidate Shopify store URL to its origin.
 *
 * Lowercases scheme + host, drops query, fragment, AND path. The candidate
 * identity is the store (one row per store), not the URL — so a SerpAPI hit
 * on /pages/contact and another on /products/foo for the same store dedup
 * to a single row. Returns the original trimmed string on parse_url failure.
 *
 * @param string $url Raw URL
 * @return string     Origin URL (scheme://host)
 */
function shopify_canonical_url(string $url): string
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

    // Origin only — path is intentionally dropped. SerpAPI dorks that include
    // "contact"/"about" keywords often return deep links, but our candidate
    // identity is the store (one row per store), and downstream evaluator
    // logic needs the origin to build /products.json correctly.
    return $scheme . '://' . $host;
}

/**
 * Fetch and decode a Shopify storefront /products.json endpoint.
 *
 * Returns the decoded JSON array on success, or null on any failure.
 * Callers should check $result['products'] for the actual product list.
 *
 * @param string $storefrontUrl Base storefront URL (no trailing slash required)
 * @param int    $timeout       HTTP timeout in seconds (default 10)
 * @return array|null           Decoded JSON or null on failure
 */
function fetch_shopify_products_json(string $storefrontUrl, int $timeout = 10): ?array
{
    $endpoint = rtrim($storefrontUrl, '/') . '/products.json?limit=50';

    $context = stream_context_create([
        'http' => [
            'timeout'         => $timeout,
            // Redirects disabled: /products.json lives on the same origin as
            // the storefront; a redirect target could be an internal/private
            // URL (SSRF vector). Fail closed instead.
            'follow_location' => 0,
            'max_redirects'   => 0,
            'user_agent'      => 'Mozilla/5.0',
            'ignore_errors'   => true,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $raw = @file_get_contents($endpoint, false, $context, 0, 2097152);  // 2 MB cap

    // Check HTTP status from response headers
    if (isset($http_response_header) && is_array($http_response_header)) {
        // First header line is the HTTP status line, e.g. "HTTP/1.1 200 OK"
        $statusLine = $http_response_header[0] ?? '';
        if (preg_match('/\s(\d{3})\s/', $statusLine, $m)) {
            $statusCode = (int) $m[1];
            if ($statusCode === 404 || $statusCode < 200 || $statusCode >= 300) {
                return null;
            }
        }
    }

    if ($raw === false || $raw === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        if (strlen($raw) >= 2097152) {
            error_log("Shopify /products.json too large (>2MB) at $endpoint");
        }
        return null;
    }

    if (!isset($decoded['products']) || !is_array($decoded['products'])) {
        return null;
    }

    return $decoded;
}

/**
 * Detect whether the storefront advertises an existing accounting tool.
 *
 * Returns the matched tool name (e.g. "QuickBooks") or null. If a tool is
 * detected, the lead is not a good fit — they already have accounting
 * software and don't need a free trial of ours.
 *
 * Patterns use word boundaries and (where ambiguous) require a qualifier
 * like "accounting" to avoid false positives on common-word brand names
 * (e.g. "wave" alone would match "tidal wave surf co.").
 */
function detect_accounting_tool(string $html): ?string
{
    $text = strip_tags($html);

    $patterns = [
        'QuickBooks'   => '/\bquick\s*books\b/i',
        'Xero'         => '/\bxero\b/i',
        'FreshBooks'   => '/\bfresh\s*books\b/i',
        'Wave'         => '/\bwave\s+accounting\b|\bwaveapps\b/i',
        'Sage'         => '/\bsage\s+(?:accounting|50|one|intacct|business)\b/i',
        'Zoho Books'   => '/\bzoho\s+books\b/i',
        'Bench'        => '/\bbench\.co\b|\baccounting\s+by\s+bench\b/i',
        'Kashoo'       => '/\bkashoo\b/i',
        'NetSuite'     => '/\bnetsuite\b/i',
    ];

    foreach ($patterns as $name => $pattern) {
        if (preg_match($pattern, $text)) {
            return $name;
        }
    }

    return null;
}

/**
 * Scan storefront HTML for an explicit business-founding year.
 *
 * Looks for copyright, "since YYYY", "established YYYY", "founded YYYY", and
 * "YYYY-present" patterns and returns the OLDEST plausible founding year
 * (1900..current year) found, or null if no signal is detected.
 *
 * Intended for the business_too_old reject path: a 30-year-old company that
 * just migrated to Shopify is not a startup, even if its oldest product is
 * less than 24 months old.
 */
function detect_storefront_founded_year(string $html): ?int
{
    $text = strip_tags($html);
    $currentYear = (int) date('Y');
    $oldestYear = null;

    $patterns = [
        '/©.{0,80}?(\d{4})/s',                                    // © 1995, © Acme Co 1995-2026
        '/\bcopyright\b.{0,80}?(\d{4})/is',                       // copyright Acme Co 1995
        '/\bsince\s+(\d{4})\b/i',                                 // since 1995
        '/\b(?:est(?:ablished)?\.?)\s+(?:in\s+)?(\d{4})\b/i',     // est. 1995, established in 1995
        '/\bfounded\s+(?:in\s+)?(\d{4})\b/i',                     // founded 1995, founded in 1995
        '/\b(\d{4})\s*[-–—]\s*(?:present|today|now)\b/i',         // 1995-present
    ];

    foreach ($patterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches)) {
            foreach ($matches[1] as $yearStr) {
                $year = (int) $yearStr;
                if ($year >= 1900 && $year <= $currentYear) {
                    if ($oldestYear === null || $year < $oldestYear) {
                        $oldestYear = $year;
                    }
                }
            }
        }
    }

    return $oldestYear;
}

/**
 * Evaluate a Shopify storefront URL for outreach fitness.
 *
 * Runs a pipeline of checks (agency detection, products count, age, country,
 * email) and returns a structured result. The $htmlOverride and
 * $productsOverride params skip network fetches for testing.
 *
 * @param string     $url              Candidate storefront URL
 * @param string|null $htmlOverride    Inject HTML instead of fetching live
 * @param array|null  $productsOverride Inject products.json decoded array instead of fetching live
 * @return array     ['fit'=>bool, 'reason'=>string, 'final_url'=>string, 'metadata'=>array, ...]
 */
function evaluate_shopify_candidate(string $url, ?string $htmlOverride = null, ?array $productsOverride = null): array
{
    $finalUrl = $url;
    $metadata = [];

    // -------------------------------------------------------------------------
    // Step 1: Fetch storefront HTML (or use injected override)
    // -------------------------------------------------------------------------
    if ($htmlOverride !== null) {
        $html = $htmlOverride;
    } else {
        // Use cURL to reliably capture the final URL after redirects
        if (!_shopify_url_is_safe_external($url)) {
            return ['fit' => false, 'reason' => 'fetch_failed', 'detail' => 'Input URL host resolves to private/reserved IP', 'final_url' => $url, 'metadata' => []];
        }
        $ch = curl_init($url);
        if ($ch === false) {
            return ['fit' => false, 'reason' => 'fetch_failed', 'detail' => 'curl_init failed', 'final_url' => $url, 'metadata' => []];
        }
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 3,
            CURLOPT_TIMEOUT         => 10,
            CURLOPT_USERAGENT       => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_ENCODING        => '',  // accept compressed responses
            CURLOPT_MAXFILESIZE     => 2097152,  // 2 MB cap (enforced only when Content-Length is sent)
        ]);
        $html      = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($html === false || $curlError !== '' || $httpCode < 200 || $httpCode >= 400) {
            return [
                'fit'       => false,
                'reason'    => 'fetch_failed',
                'detail'    => 'HTTP fetch failed' . ($curlError ? ": {$curlError}" : " (HTTP {$httpCode})"),
                'final_url' => $url,
                'metadata'  => [],
            ];
        }

        // Backup size guard: bail if response exceeds 2 MB regardless of Content-Length
        if (is_string($html) && strlen($html) >= 2097152) {
            error_log("Shopify storefront HTML too large (>2MB) at $url");
            return [
                'fit'       => false,
                'reason'    => 'fetch_failed',
                'detail'    => 'Storefront HTML response exceeded 2 MB size cap',
                'final_url' => $url,
                'metadata'  => [],
            ];
        }

        if (!empty($effective)) {
            // Normalize to origin — drops any path the redirect chain ended at,
            // so downstream /products.json and contact-page probing work
            // correctly when the candidate URL was a deep link.
            $finalUrl = shopify_canonical_url($effective);
        }

        if (!_shopify_url_is_safe_external($finalUrl)) {
            return ['fit' => false, 'reason' => 'fetch_failed', 'detail' => 'Redirect target resolves to private/reserved IP', 'final_url' => $finalUrl, 'metadata' => []];
        }
    }

    // -------------------------------------------------------------------------
    // Step 2: Agency detection
    // -------------------------------------------------------------------------
    if (preg_match('/powered\s+by\s+(?!shopify\b)([^<\n]{1,80})/i', $html, $agencyMatch)) {
        $detail = strip_tags($agencyMatch[0]);
        $detail = trim($detail);
        $detail = substr($detail, 0, 200);
        return [
            'fit'       => false,
            'reason'    => 'agency_operated',
            'detail'    => $detail,
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    // -------------------------------------------------------------------------
    // Step 2b: Business-age signal check
    // The product-age check below only knows how old a product is on Shopify,
    // not how old the business is. An established business that migrated to
    // Shopify recently will look "new" by product age but is not a startup.
    // Catch those by scanning the homepage for copyright / since / founded
    // year signals and rejecting if the business advertises itself as 3+
    // years old.
    // -------------------------------------------------------------------------
    $foundedYear = detect_storefront_founded_year($html);
    $currentYear = (int) date('Y');
    if ($foundedYear !== null && $foundedYear <= ($currentYear - 3)) {
        $businessAge = $currentYear - $foundedYear;
        return [
            'fit'       => false,
            'reason'    => 'business_too_old',
            'detail'    => "Storefront indicates business founded in {$foundedYear} (~{$businessAge} years old)",
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    // -------------------------------------------------------------------------
    // Step 2c: Accounting-tool detection
    // If the homepage mentions an existing accounting product (QuickBooks,
    // Xero, etc.), they already have a solution — skip before paying for
    // /products.json.
    // -------------------------------------------------------------------------
    $accountingTool = detect_accounting_tool($html);
    if ($accountingTool !== null) {
        return [
            'fit'       => false,
            'reason'    => 'has_accounting_tool',
            'detail'    => "Storefront mentions {$accountingTool}",
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    // -------------------------------------------------------------------------
    // Step 3: Fetch /products.json (or use injected override) + count check
    // -------------------------------------------------------------------------
    if ($productsOverride !== null) {
        $productsData = $productsOverride;
    } else {
        $productsData = fetch_shopify_products_json($finalUrl);
    }

    if ($productsData === null) {
        return [
            'fit'       => false,
            'reason'    => 'not_shopify',
            'detail'    => 'No /products.json or invalid response',
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    $products = $productsData['products'] ?? [];

    if (count($products) < 5) {
        return [
            'fit'       => false,
            'reason'    => 'too_few_products',
            'detail'    => 'Only ' . count($products) . ' products',
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    // -------------------------------------------------------------------------
    // Step 4: Age check — find the OLDEST and NEWEST product created_at.
    // Newest-product timestamp drives the dormancy check below; newest title
    // is surfaced into business_summary so the AI prompt can reference a real
    // product the lead actually sells.
    // -------------------------------------------------------------------------
    $oldestTs = null;
    $oldestDt = null;
    $newestTs = null;
    $newestTitle = null;
    foreach ($products as $product) {
        if (empty($product['created_at'])) {
            continue;
        }
        try {
            $dt = new DateTime($product['created_at']);
            $ts = $dt->getTimestamp();
            if ($oldestTs === null || $ts < $oldestTs) {
                $oldestTs = $ts;
                $oldestDt = $dt;
            }
            if ($newestTs === null || $ts > $newestTs) {
                $newestTs = $ts;
                $title = isset($product['title']) ? trim((string) $product['title']) : '';
                $newestTitle = $title !== '' ? mb_substr($title, 0, 120) : null;
            }
        } catch (Exception) {
            // Skip unparseable dates
        }
    }

    if ($oldestTs === null) {
        return ['fit' => false, 'reason' => 'age_unknown', 'detail' => 'No parseable created_at on any product', 'final_url' => $finalUrl, 'metadata' => $metadata];
    }

    $nowTs     = time();
    $ageMonths = ($nowTs - $oldestTs) / (30.44 * 86400);
    $ageDays   = (int) round(($nowTs - $oldestTs) / 86400);

    if ($ageMonths < 3) {
        return [
            'fit'       => false,
            'reason'    => 'too_new',
            'detail'    => "First product created {$ageDays} days ago",
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    if ($ageMonths > 24) {
        $monthsInt = (int) round($ageMonths);
        return [
            'fit'       => false,
            'reason'    => 'too_old',
            'detail'    => "First product created {$monthsInt} months ago",
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    // Dormancy check: if the NEWEST product is > 6 months old, the store is
    // likely abandoned and won't reply. A live store is adding products.
    if ($newestTs !== null) {
        $newestAgeMonths = ($nowTs - $newestTs) / (30.44 * 86400);
        if ($newestAgeMonths > 6) {
            $newestMonthsInt = (int) round($newestAgeMonths);
            return [
                'fit'       => false,
                'reason'    => 'dormant',
                'detail'    => "Most recent product added {$newestMonthsInt} months ago",
                'final_url' => $finalUrl,
                'metadata'  => $metadata,
            ];
        }
    }

    // Store the oldest created_at as MySQL DATETIME
    /** @var DateTime $oldestDt */
    $mysqlDatetime = $oldestDt->format('Y-m-d H:i:s');
    $metadata['first_product_created_at'] = $mysqlDatetime;
    if ($newestTitle !== null) {
        $metadata['featured_product'] = $newestTitle;
    }

    // -------------------------------------------------------------------------
    // Step 5: Country check — accept Canada OR United States
    // Canadian postal code: letter-digit-letter digit-letter-digit, with
    // restricted first letters. US ZIP: 5 digits, usually preceded by a
    // 2-letter state code to avoid false positives on random numbers.
    // Postal-code matches win over text signals when both are present.
    // -------------------------------------------------------------------------
    $detectedCountry = null;
    $htmlLower = strtolower($html);

    if (preg_match('/\b[ABCEGHJ-NPRSTVXY]\d[A-Z][ \-]?\d[A-Z]\d\b/i', $html)) {
        $detectedCountry = 'CA';
    } elseif (preg_match('/\b(?:AL|AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY)\s+\d{5}(?:-\d{4})?\b/', $html)) {
        $detectedCountry = 'US';
    } else {
        $canadaSignals = [
            'made in canada',
            'based in canada',
            'proudly canadian',
            'ships from canada',
            'canadian small business',
            ', canada',
        ];
        $usSignals = [
            'made in usa',
            'made in the usa',
            'made in u.s.a.',
            'made in america',
            'based in the usa',
            'proudly american',
            'american small business',
            ', united states',
        ];
        foreach ($canadaSignals as $signal) {
            if (str_contains($htmlLower, $signal)) {
                $detectedCountry = 'CA';
                break;
            }
        }
        if ($detectedCountry === null) {
            foreach ($usSignals as $signal) {
                if (str_contains($htmlLower, $signal)) {
                    $detectedCountry = 'US';
                    break;
                }
            }
        }
    }

    if ($detectedCountry === null) {
        return [
            'fit'       => false,
            'reason'    => 'not_target_country',
            'detail'    => 'No Canadian or US postal/address signal',
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    $metadata['country'] = $detectedCountry;

    // -------------------------------------------------------------------------
    // Step 6: Harvest contact email
    // When HTML is injected (test mode), extract email directly from the override
    // so no live network call is needed. For live mode, use the full scraper.
    // -------------------------------------------------------------------------
    if ($htmlOverride !== null) {
        // Extract from the injected HTML (mirrors logic in _scrape_email_from_website_uncached)
        $email = null;
        $falsePositives = ['example.com', 'sentry.io', 'wixpress.com', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'gravatar.com'];
        $decodedHtml = urldecode($htmlOverride);
        if (preg_match_all('/mailto:\s*([^\s"\'<>]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $decodedHtml, $mEmail)) {
            foreach ($mEmail[1] as $raw) {
                $candidate = trim(urldecode($raw));
                if (!preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $candidate)) {
                    continue;
                }
                $isFp = false;
                foreach ($falsePositives as $fp) {
                    if (str_contains(strtolower($candidate), $fp)) { $isFp = true; break; }
                }
                if (!$isFp) { $email = $candidate; break; }
            }
        }
        if ($email === null) {
            $text = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', strip_tags($decodedHtml));
            if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $mEmail)) {
                foreach ($mEmail[0] as $raw) {
                    $candidate = trim($raw);
                    if (!preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $candidate)) {
                        continue;
                    }
                    $isFp = false;
                    foreach ($falsePositives as $fp) {
                        if (str_contains(strtolower($candidate), $fp)) { $isFp = true; break; }
                    }
                    if (!$isFp) { $email = $candidate; break; }
                }
            }
        }
    } else {
        $email = scrape_email_from_website($finalUrl);
    }

    if ($email === null) {
        return [
            'fit'       => false,
            'reason'    => 'no_contact_email',
            'detail'    => 'No email harvested from contact pages',
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    if (filter_gatekept_email($email)) {
        return [
            'fit'       => false,
            'reason'    => 'gatekept_email',
            'detail'    => "Skipped {$email} (role mailbox)",
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    $metadata['email'] = $email;

    // -------------------------------------------------------------------------
    // Step 7: Extract business name from <title>
    // -------------------------------------------------------------------------
    if (preg_match('/<title[^>]*>([^<]+)<\/title>/i', $html, $titleMatch)) {
        $name = html_entity_decode(trim($titleMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $name = substr($name, 0, 200);
    } else {
        $parsed = parse_url($finalUrl);
        $name   = $parsed['host'] ?? $finalUrl;
    }

    $metadata['business_name']  = $name;
    $metadata['products_count'] = count($products);

    // -------------------------------------------------------------------------
    // Step 8: Return fit result
    // -------------------------------------------------------------------------
    return [
        'fit'       => true,
        'reason'    => 'ok',
        'final_url' => $finalUrl,
        'metadata'  => [
            'business_name'            => $metadata['business_name'],
            'email'                    => $metadata['email'],
            'products_count'           => $metadata['products_count'],
            'first_product_created_at' => $metadata['first_product_created_at'],
            'country'                  => $metadata['country'],
            'featured_product'         => $metadata['featured_product'] ?? null,
        ],
    ];
}
