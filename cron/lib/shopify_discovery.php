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

// Broad Canadian-signal dorks. No city-specific terms — the eligible market
// is anywhere in Canada, not just metros, and the storefront-level country
// check in evaluate_shopify_candidate confirms CA before any lead is created.
const SHOPIFY_DORK_POOL = [
    'site:myshopify.com "based in canada" "contact"',
    'site:myshopify.com "ships from canada" "founded"',
    'site:myshopify.com "made in canada" "small batch"',
    'site:myshopify.com "proudly canadian" "contact us"',
    'site:myshopify.com "shipping across canada" "about"',
    'site:myshopify.com "canadian small business" "contact"',
    'site:myshopify.com "canadian-owned" "shop"',
    'site:myshopify.com "from canada" "small batch"',
    'site:myshopify.com "canada" "founded in 2024"',
    'site:myshopify.com "canada" "founded in 2025"',
];

/**
 * Query SerpAPI for organic results matching $query.
 *
 * @param string $query   Google dork query string
 * @param string $apiKey  SerpAPI API key
 * @param int    $limit   Max results (default 10)
 * @return array          Array of ['link'=>..., 'title'=>..., 'snippet'=>...] entries
 */
function serpapi_query(string $query, string $apiKey, int $limit = 10): array
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
    // Step 4: Age check — find the OLDEST product created_at
    // -------------------------------------------------------------------------
    $oldestTs = null;
    $oldestDt = null;
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

    // Store the oldest created_at as MySQL DATETIME
    /** @var DateTime $oldestDt */
    $mysqlDatetime = $oldestDt->format('Y-m-d H:i:s');
    $metadata['first_product_created_at'] = $mysqlDatetime;

    // -------------------------------------------------------------------------
    // Step 5: Country (Canada) check
    // -------------------------------------------------------------------------
    // Canadian postal code: valid first letters only (no D, F, I, O, Q, U, W, Z)
    $isCanadian = false;

    if (preg_match('/\b[ABCEGHJ-NPRSTVXY]\d[A-Z][ \-]?\d[A-Z]\d\b/i', $html)) {
        $isCanadian = true;
    } else {
        $canadaSignals = [
            'made in canada',
            'based in canada',
            'proudly canadian',
            'ships from canada',
            'canadian small business',
            ', canada',
        ];
        $htmlLower = strtolower($html);
        foreach ($canadaSignals as $signal) {
            if (str_contains($htmlLower, $signal)) {
                $isCanadian = true;
                break;
            }
        }
    }

    if (!$isCanadian) {
        return [
            'fit'       => false,
            'reason'    => 'not_canadian',
            'detail'    => 'No CA postal code or Canadian address signal',
            'final_url' => $finalUrl,
            'metadata'  => $metadata,
        ];
    }

    $metadata['country'] = 'CA';

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
        ],
    ];
}
