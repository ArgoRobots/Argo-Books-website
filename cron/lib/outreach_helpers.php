<?php
/**
 * Shared helper functions for outreach automation.
 * Used by both the admin API (admin/outreach/api.php) and the cron pipeline.
 *
 * Contains: log_activity, send_outreach_lead, scrape_email_from_website,
 *           search_businesses_core, call_openai, summarize_business,
 *           generate_draft_for_lead
 */

// Guard against double-inclusion
if (defined('OUTREACH_HELPERS_LOADED')) return;
define('OUTREACH_HELPERS_LOADED', true);

// ─── Discovery Category Pool ───
// Used by both the cron pipeline (deterministic cycling) and search_businesses_core (random fallback for admin searches)
const OUTREACH_CATEGORY_POOL = [
    'restaurants', 'plumbers', 'electricians', 'dentists', 'lawyers',
    'accountants', 'real estate agents', 'insurance agents', 'auto repair',
    'hair salons', 'fitness gyms', 'chiropractors', 'veterinarians',
    'cleaning services', 'landscaping', 'roofing contractors', 'HVAC',
    'photographers', 'florists', 'bakeries', 'coffee shops', 'pet stores',
    'daycare centers', 'tutoring services', 'martial arts studios',
    'yoga studios', 'massage therapists', 'optometrists', 'pharmacies',
    'printing services', 'moving companies', 'pest control', 'locksmiths',
    'car dealerships', 'tire shops', 'furniture stores', 'jewelry stores',
    'clothing boutiques', 'tattoo parlors', 'breweries', 'catering',
    'wedding planners', 'interior designers', 'architects', 'surveyors',
    'physiotherapists', 'psychologists', 'counsellors', 'notaries',
    'bookkeepers', 'IT support', 'web design', 'marketing agencies',
    'sign shops', 'trophy shops', 'music schools', 'dance studios',
    'dog groomers', 'boarding kennels', 'farm equipment dealers',
    'hardware stores', 'building supplies', 'appliance repair',
    'upholstery services', 'tailors', 'dry cleaners', 'spas',
    'tanning salons', 'nail salons', 'barber shops', 'optical stores',
    'hearing aid clinics', 'home inspectors', 'appraisers',
    'property management', 'storage facilities', 'courier services',
    'towing services', 'glass repair', 'fencing contractors',
    'concrete contractors', 'paving contractors', 'tree services',
    'snow removal', 'pool services', 'septic services',
    'garage door repair', 'security companies', 'staffing agencies',
    'travel agencies', 'event venues', 'food trucks',
];

// ─── Category Pain Points ───

/**
 * Look up 2-3 hand-curated pain points for a lead's category, falling back
 * to a generic '_default' list if the category is not in the map.
 * The map is loaded once per process.
 */
function get_category_pain_points($category)
{
    static $map = null;
    if ($map === null) {
        $map = require __DIR__ . '/category_pain_points.php';
    }
    $key = strtolower(trim((string) $category));
    return $map[$key] ?? $map['_default'];
}

// ─── A/B Test Infrastructure ───

require_once __DIR__ . '/ab_helpers.php';

/**
 * Find the single active A/B test of a given variant type, or null if none.
 * Returns ['test' => row, 'variants' => [rows]] on hit.
 */
function get_active_ab_test($pdo, $variantType)
{
    $stmt = $pdo->prepare("SELECT * FROM outreach_ab_tests
        WHERE status = 'active' AND variant_type = ?
        ORDER BY started_at DESC, id DESC LIMIT 1");
    $stmt->execute([$variantType]);
    $test = $stmt->fetch();
    if (!$test) return null;

    $vStmt = $pdo->prepare("SELECT * FROM outreach_ab_variants WHERE test_id = ? ORDER BY id ASC");
    $vStmt->execute([$test['id']]);
    $variants = $vStmt->fetchAll();
    if (empty($variants)) return null;

    return ['test' => $test, 'variants' => $variants];
}

/**
 * Pick the next variant for an active test using deterministic round-robin.
 * Counts how many leads are already assigned to this test and assigns the
 * next lead to index (count % variant_count).
 */
function pick_ab_variant($pdo, $test, $variants)
{
    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_leads WHERE ab_test_id = ?");
    $cStmt->execute([$test['id']]);
    $assignedSoFar = (int) $cStmt->fetchColumn();
    $idx = $assignedSoFar % count($variants);
    return $variants[$idx];
}

/**
 * Split a variant's `content` into ['mode' => 'directive'|'literal', 'text' => string].
 * Helper used by every per-type instruction builder.
 */
function ab_parse_variant_content($variant)
{
    $content = trim((string) $variant['content']);
    if (stripos($content, 'directive:') === 0) {
        return [
            'mode' => 'directive',
            'text' => trim(substr($content, strlen('directive:'))),
        ];
    }
    return ['mode' => 'literal', 'text' => $content];
}

/**
 * Subject-line instruction builder. Returns a prompt fragment to splice
 * into the system prompt's subject-line bullet.
 */
function ab_subject_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- SUBJECT LINE OVERRIDE: write a subject line that follows this directive exactly: \"" . $parsed['text'] . "\". This overrides any other guidance about subject lines above.";
    }
    return "\n- SUBJECT LINE OVERRIDE: use exactly this subject line, word for word, with no changes: \"" . $parsed['text'] . "\". This overrides any other guidance about subject lines above.";
}

/**
 * Body instruction builder. The override controls body shape — paragraph
 * count, opener style, tone, length — but explicitly preserves the
 * non-negotiable structural elements (website URL, {UNSUBSCRIBE_URL}
 * placeholder, three-line sign-off).
 */
function ab_body_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- BODY OVERRIDE: write the email body in the style described by this directive: \"" . $parsed['text'] . "\". This style guidance overrides the paragraph count, opener style, and tone rules above. You MUST still include the https://argorobots.com/ link, the {UNSUBSCRIBE_URL} placeholder line, and the \"All the best, / Evan / Argo Books\" sign-off exactly as specified.";
    }
    return "\n- BODY OVERRIDE: the email body must be exactly this text, word for word: \"" . $parsed['text'] . "\". The {UNSUBSCRIBE_URL} placeholder and the https://argorobots.com/ link inside this text will be processed before sending — keep them as written.";
}

/**
 * CTA / offer instruction builder. Replaces the standard "free 1-year
 * premium license in exchange for feedback" offer with whatever wording
 * (or directive) the variant specifies.
 */
function ab_cta_instruction_for_variant($variant)
{
    $parsed = ab_parse_variant_content($variant);
    if ($parsed['mode'] === 'directive') {
        return "\n- OFFER OVERRIDE: ignore the \"free 1-year premium license in exchange for feedback\" offer above. Instead, phrase the offer in line with this directive: \"" . $parsed['text'] . "\". Work it in naturally; do not list it like a bullet point.";
    }
    return "\n- OFFER OVERRIDE: ignore the \"free 1-year premium license in exchange for feedback\" offer above. The offer in this email must be exactly: \"" . $parsed['text'] . "\". Phrase it naturally inside the body — do not just paste it as a quoted line.";
}

/**
 * Per-type prompt-injection dispatch. Future phases register new types here
 * (body, cta, personalization). Send-side types (sender, preheader, format)
 * apply at send time, not here, and return ['' for prompt injection.
 *
 * Returns a string to append to the system prompt — empty if the type has no
 * prompt-side effect (or isn't wired yet).
 */
function ab_instruction_for_variant($variant, $variantType = 'subject')
{
    switch ($variantType) {
        case 'subject':
            return ab_subject_instruction_for_variant($variant);
        case 'body':
            return ab_body_instruction_for_variant($variant);
        case 'cta':
            return ab_cta_instruction_for_variant($variant);
        // Phase 6: case 'personalization': handled inline in generate_draft_for_lead, not here
        default:
            return '';
    }
}

// ─── Activity Logging ───

function log_activity($pdo, $lead_id, $action_type, $details = null)
{
    $stmt = $pdo->prepare("INSERT INTO outreach_activity_log (lead_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$lead_id, $action_type, $details]);
}

// ─── Send a Single Outreach Email ───

/**
 * Send an outreach email for a lead and update its DB status.
 * Returns true on success, false on failure.
 */
function send_outreach_lead($pdo, $lead)
{
    $id = $lead['id'];
    $email = $lead['email'];

    // Skip if this email is on the suppression list
    if (!empty($email)) {
        $suppStmt = $pdo->prepare("SELECT 1 FROM email_suppressions WHERE email = ? AND context = 'outreach' LIMIT 1");
        $suppStmt->execute([strtolower(trim($email))]);
        if ($suppStmt->fetchColumn()) {
            log_activity($pdo, $id, 'email_skipped_suppressed', 'Skipped send: email is on outreach suppression list (' . $email . ')');
            return false;
        }
    }

    // Generate and persist an unsubscribe token if we don't have one yet
    $unsubscribeToken = $lead['unsubscribe_token'] ?? null;
    if (empty($unsubscribeToken)) {
        $unsubscribeToken = bin2hex(random_bytes(32));
        $tokStmt = $pdo->prepare("UPDATE outreach_leads SET unsubscribe_token = ? WHERE id = ?");
        $tokStmt->execute([$unsubscribeToken, $id]);
    }

    // Build the per-lead tracking URL. If the lead was assigned to an A/B
    // variant, append -v{variantId} so clicks can be attributed per variant.
    $sourceCode = 'outreach-' . $id;
    $variantId = isset($lead['ab_variant_id']) && $lead['ab_variant_id'] !== null && $lead['ab_variant_id'] !== ''
        ? (int) $lead['ab_variant_id']
        : null;
    if ($variantId) {
        $sourceCode .= '-v' . $variantId;
    }
    $trackedUrl = 'https://argorobots.com/?source=' . $sourceCode;
    $unsubUrl = 'https://argorobots.com/unsubscribe?t=' . $unsubscribeToken;

    // Send-side A/B variants (sender, preheader, format). Look up first since
    // format affects how the body is rendered below.
    $fromName = 'Argo Books';
    $preheader = null;
    $format = 'html';
    if ($variantId) {
        $vStmt = $pdo->prepare("SELECT v.content, t.variant_type
            FROM outreach_ab_variants v
            JOIN outreach_ab_tests t ON t.id = v.test_id
            WHERE v.id = ?");
        $vStmt->execute([$variantId]);
        $vRow = $vStmt->fetch();
        if ($vRow && trim((string) $vRow['content']) !== '') {
            $vContent = trim((string) $vRow['content']);
            if ($vRow['variant_type'] === 'sender') {
                $fromName = $vContent;
            } elseif ($vRow['variant_type'] === 'preheader') {
                $preheader = $vContent;
            } elseif ($vRow['variant_type'] === 'format') {
                $format = ($vContent === 'plain') ? 'plain' : 'html';
            }
        }
    }

    if ($format === 'plain') {
        // Plain text: keep URLs bare so they remain clickable in plain-text
        // clients while still carrying the tracking source param. No HTML
        // escaping, no <a> wrapping.
        $body = (string) $lead['draft_body'];
        $body = preg_replace('#https?://argorobots\.com/?(?![\w?])#', $trackedUrl, $body);
        $body = str_replace('{UNSUBSCRIBE_URL}', $unsubUrl, $body);
        if (strpos($body, 'unsubscribe?t=') === false) {
            $unsubLine = "\n\nNot interested? " . $unsubUrl . " and I'll stop emailing you.";
            $replaced = preg_replace('#(\nAll the best)#i', $unsubLine . "\n$1", $body, 1);
            if ($replaced !== null && strpos($replaced, 'unsubscribe?t=') !== false) {
                $body = $replaced;
            } else {
                $body .= $unsubLine;
            }
        }
        $finalBody = $body;
    } else {
        $anchorHtml = '<a href="' . htmlspecialchars($trackedUrl) . '" style="color:#3b82f6;text-decoration:underline">argorobots.com</a>';
        $escapedBody = htmlspecialchars($lead['draft_body']);
        $escapedBody = preg_replace('#https?://argorobots\.com/?(?![\w?])#', $anchorHtml, $escapedBody);

        $unsubAnchor = '<a href="' . htmlspecialchars($unsubUrl) . '" style="color:#6b7280;text-decoration:underline">unsubscribe</a>';
        $escapedBody = str_replace('{UNSUBSCRIBE_URL}', $unsubAnchor, $escapedBody);

        if (strpos($escapedBody, 'unsubscribe?t=') === false) {
            $unsubLine = "\n\n<span style=\"color:#9ca3af;font-size:13px\">Not interested? " . $unsubAnchor . " and I'll stop emailing you.</span>";
            $replaced = preg_replace('#(\nAll the best)#i', $unsubLine . "\n$1", $escapedBody, 1);
            if ($replaced !== null && strpos($replaced, 'unsubscribe?t=') !== false) {
                $escapedBody = $replaced;
            } else {
                $escapedBody .= $unsubLine;
            }
        }

        $finalBody = '<p>' . nl2br($escapedBody) . '</p>';
    }

    $result = send_styled_email(
        $email,
        $lead['draft_subject'],
        $finalBody,
        '',
        'contact@argorobots.com',
        $fromName,
        'contact@argorobots.com',
        [],
        $preheader,
        $format
    );

    if ($result) {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET
            sent_at = NOW(),
            status = CASE WHEN status NOT IN ('replied','interested','not_interested','onboarded') THEN 'contacted' ELSE status END,
            first_contact_date = COALESCE(first_contact_date, NOW()),
            last_contact_date = NOW()
            WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }

    return false;
}

// ─── Email Scraping Helper ───

function scrape_email_from_website($url)
{
    if (empty($url)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $falsePositives = ['example.com', 'sentry.io', 'wixpress.com', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'gravatar.com'];

    // Clean an extracted email: decode URL encoding, strip non-ASCII and whitespace
    $cleanEmail = function($email) {
        $email = urldecode($email);
        // Strip any non-ASCII characters (emojis, special chars, zero-width spaces, etc.)
        $email = preg_replace('/[^\x20-\x7E]/', '', $email);
        $email = trim($email);
        // Validate it still looks like an email after cleaning
        if (preg_match('/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/', $email)) {
            return $email;
        }
        return null;
    };

    // Helper to extract email from HTML
    $extractEmail = function($html) use ($falsePositives, $cleanEmail) {
        // URL-decode the HTML so mailto:%20info@... becomes mailto: info@...
        $decodedHtml = urldecode($html);

        // Look for mailto: links first (most reliable)
        if (preg_match_all('/mailto:\s*([^\s"\'<>]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $decodedHtml, $matches)) {
            foreach ($matches[1] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $dominated = false;
                foreach ($falsePositives as $fp) { if (str_contains(strtolower($email), $fp)) { $dominated = true; break; } }
                if (!$dominated) return $email;
            }
        }
        // Fallback: email patterns in text (strip HTML tags first to avoid matching attributes)
        $text = strip_tags($decodedHtml);
        // Remove common non-ASCII clutter (emojis, zero-width chars) before matching
        $text = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $text);
        if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $text, $matches)) {
            foreach ($matches[0] as $raw) {
                $email = $cleanEmail($raw);
                if (!$email) continue;
                $dominated = false;
                foreach ($falsePositives as $fp) { if (str_contains(strtolower($email), $fp)) { $dominated = true; break; } }
                if (!$dominated) return $email;
            }
        }
        return null;
    };

    // Try homepage first
    $html = @file_get_contents($url, false, $context);
    if ($html) {
        $email = $extractEmail($html);
        if ($email) return $email;

        // Find contact page links in the HTML
        // Parse base URL properly for resolving relative links
        $parsed = parse_url($url);
        $origin = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
        $basePath = rtrim($url, '/');
        $contactPaths = [];

        // Match all <a> tags - check both href path AND link text for contact-related keywords
        $contactKeywords = 'contact|about|about-us|contact-us|connect|get-in-touch|reach-us|reach out';
        if (preg_match_all('/<a\s[^>]*href=["\']([^"\'#][^"\']*)["\'][^>]*>(.*?)<\/a>/is', $html, $linkMatches, PREG_SET_ORDER)) {
            foreach ($linkMatches as $m) {
                $href = $m[1];
                $text = strip_tags($m[2]);
                // Match if href OR link text contains contact keywords
                if (!preg_match('/' . $contactKeywords . '/i', $href) && !preg_match('/' . $contactKeywords . '/i', $text)) continue;
                // Skip mailto/tel/javascript
                if (preg_match('/^(mailto:|tel:|javascript:)/i', $href)) continue;

                // Resolve relative URLs
                if (str_starts_with($href, 'http')) {
                    $contactPaths[] = $href;
                } elseif (str_starts_with($href, '/')) {
                    $contactPaths[] = $origin . $href;
                } else {
                    $contactPaths[] = $basePath . '/' . $href;
                }
            }
        }

        // Fallback: try common paths if none found in links
        if (empty($contactPaths)) {
            $contactPaths = [
                $basePath . '/contact',
                $basePath . '/contact-us',
                $basePath . '/about',
            ];
        }

        // Try each contact page
        foreach (array_unique(array_slice($contactPaths, 0, 3)) as $contactUrl) {
            $contactHtml = @file_get_contents($contactUrl, false, $context);
            if ($contactHtml) {
                $email = $extractEmail($contactHtml);
                if ($email) return $email;
            }
        }
    }

    return null;
}

// ─── Business Discovery (Google Places API) ───

/**
 * Core business search logic. Returns array with 'businesses', 'count', 'rounds'.
 * Used by both the admin API endpoint and the cron pipeline.
 */
function search_businesses_core($city, $province, $category, $limit, $apiKey, $excludePlaceIds = [], $maxRounds = 5)
{
    $location = $province ? "$city, $province" : $city;
    $businesses = [];
    $seenPlaceIds = [];
    // Pre-seed seen IDs so we skip businesses already known
    foreach ($excludePlaceIds as $id) {
        $seenPlaceIds[trim($id)] = true;
    }
    $roundsUsed = 0;

    // Stream context with timeouts for all Google API calls
    $httpContext = stream_context_create(['http' => [
        'timeout' => 10,
        'ignore_errors' => true,
    ]]);

    // Build query variations to search across multiple rounds
    $queries = [];
    if ($category) {
        $queries[] = "$category in $location";
        $queries[] = "$category near $location";
        $queries[] = "$category services in $location";
        $queries[] = "$category companies in $location";
        $queries[] = "best $category in $location";
    } else {
        // When no category provided (admin dashboard searches), pick random
        // categories from the shared pool so each round searches a different industry
        $categoryPool = OUTREACH_CATEGORY_POOL;
        shuffle($categoryPool);
        for ($i = 0; $i < $maxRounds; $i++) {
            $queries[] = $categoryPool[$i] . " in $location";
        }
    }

    // Track which pool category was searched per round (for labeling when no category provided)
    $queryCategories = [];
    if (!$category) {
        foreach ($queries as $q) {
            $queryCategories[] = ucwords(str_replace(" in $location", '', $q));
        }
    }

    // Map category keywords to Google Places types for more targeted results
    $placeTypeMap = [
        'restaurant' => 'restaurant', 'plumber' => 'plumber',
        'electrician' => 'electrician', 'dentist' => 'dentist',
        'lawyer' => 'lawyer', 'accountant' => 'accounting',
        'gym' => 'gym', 'salon' => 'hair_care', 'veterinarian' => 'veterinary_care',
        'pharmacy' => 'pharmacy', 'car dealership' => 'car_dealer',
        'bakery' => 'bakery', 'cafe' => 'cafe', 'coffee' => 'cafe',
        'spa' => 'spa', 'florist' => 'florist', 'pet store' => 'pet_store',
        'furniture' => 'furniture_store', 'jewelry' => 'jewelry_store',
        'hardware' => 'hardware_store', 'barber' => 'hair_care',
        'locksmith' => 'locksmith', 'storage' => 'storage',
        'travel agenc' => 'travel_agency', 'insurance' => 'insurance_agency',
        'real estate' => 'real_estate_agency',
    ];

    for ($round = 0; $round < $maxRounds && count($businesses) < $limit; $round++) {
        $query = $queries[$round] ?? null;
        if (!$query) break;
        $countBefore = count($businesses);
        $roundsUsed++;

        // Initial search for this round
        $params = ['query' => $query, 'key' => $apiKey];
        // Try to match a Google Places type from the query for better results
        foreach ($placeTypeMap as $keyword => $type) {
            if (stripos($query, $keyword) !== false) {
                $params['type'] = $type;
                break;
            }
        }
        $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query($params);

        $resp = @file_get_contents($url, false, $httpContext);
        if ($resp === false) {
            if ($roundsUsed === 1) {
                return ['error' => 'Failed to connect to Google Places API', 'businesses' => [], 'count' => 0, 'rounds' => 0];
            }
            break;
        }

        $data = json_decode($resp, true);
        $status = $data['status'] ?? '';
        if ($status !== 'OK' && $status !== 'ZERO_RESULTS') {
            if ($roundsUsed === 1) {
                $errorMsg = $data['error_message'] ?? $status ?? 'Unknown error';
                return ['error' => 'Google Places API error: ' . $errorMsg, 'businesses' => [], 'count' => 0, 'rounds' => 0];
            }
            break;
        }

        $candidates = $data['results'] ?? [];
        $nextPageToken = $data['next_page_token'] ?? null;
        $maxPages = 3;
        $pagesUsed = 1;

        // Process candidates from this round, paging through Google results
        while (count($businesses) < $limit) {
            foreach ($candidates as $place) {
                if (count($businesses) >= $limit) break;

                $placeId = $place['place_id'] ?? '';
                // Skip duplicates across rounds
                if ($placeId && isset($seenPlaceIds[$placeId])) continue;
                if ($placeId) $seenPlaceIds[$placeId] = true;

                $business = [
                    'places_id' => $placeId,
                    'business_name' => $place['name'] ?? '',
                    'address' => $place['formatted_address'] ?? '',
                    'category' => $category ?: ($queryCategories[$round] ?? (isset($place['types'][0]) ? ucfirst(str_replace('_', ' ', $place['types'][0])) : '')),
                    'city' => $city,
                    'phone' => null,
                    'website' => null,
                    'email' => null,
                ];

                // Fetch place details for phone and website
                if (!empty($placeId)) {
                    $detailUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                        'place_id' => $placeId,
                        'fields' => 'formatted_phone_number,website,url',
                        'key' => $apiKey,
                    ]);
                    $detailResp = @file_get_contents($detailUrl, false, $httpContext);
                    if ($detailResp) {
                        $detail = json_decode($detailResp, true);
                        $r = $detail['result'] ?? [];
                        $business['phone'] = $r['formatted_phone_number'] ?? null;
                        $business['website'] = $r['website'] ?? null;
                        $business['contact_page_url'] = $r['url'] ?? null;
                    }
                }

                // Skip businesses without a website
                if (empty($business['website'])) continue;

                // Scrape email from business website and validate
                $business['email'] = scrape_email_from_website($business['website']);

                // Skip businesses where we couldn't find a valid email
                if (empty($business['email']) || !filter_var($business['email'], FILTER_VALIDATE_EMAIL)) {
                    $business['email'] = null;
                    continue;
                }

                $businesses[] = $business;
            }

            // If we have enough or no more pages, stop paging
            if (count($businesses) >= $limit || empty($nextPageToken) || $pagesUsed >= $maxPages) break;

            // Google requires a short delay before next_page_token is valid
            sleep(2);

            $nextUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
                'pagetoken' => $nextPageToken,
                'key' => $apiKey,
            ]);
            $nextResp = @file_get_contents($nextUrl, false, $httpContext);
            if (!$nextResp) break;

            $nextData = json_decode($nextResp, true);
            if (($nextData['status'] ?? '') !== 'OK') break;

            $candidates = $nextData['results'] ?? [];
            $nextPageToken = $nextData['next_page_token'] ?? null;
            $pagesUsed++;
        }

        // Bail early if this round produced too few new results (diminishing returns)
        $newThisRound = count($businesses) - $countBefore;
        if ($newThisRound < 2 && $round > 0) {
            break;
        }
    }

    return ['businesses' => $businesses, 'count' => count($businesses), 'rounds' => $roundsUsed];
}

// ─── OpenAI Call ───

function call_openai($systemPrompt, $userPrompt)
{
    $apiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    if (empty($apiKey)) {
        return ['error' => 'OpenAI API key not configured'];
    }

    $model = $_ENV['OPENAI_MODEL'] ?? 'gpt-4o-mini';

    $payload = json_encode([
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ],
        'temperature' => 0.7,
        'max_tokens' => 2000,
    ]);

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'OpenAI request failed';
        return ['error' => $errorMsg];
    }

    $result = json_decode($response, true);
    return ['content' => $result['choices'][0]['message']['content'] ?? ''];
}

// ─── Business Summarization ───

function summarize_business($website)
{
    if (empty($website)) return null;

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0',
            'follow_location' => true,
            'max_redirects' => 3,
        ],
        'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
    ]);

    $html = @file_get_contents($website, false, $context);
    if (!$html) return null;

    // Strip scripts, styles, and tags to get readable text
    $text = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $html);
    $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $text);
    $text = strip_tags($text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim(mb_substr($text, 0, 3000)); // Cap at 3000 chars

    if (strlen($text) < 50) return null;

    $result = call_openai(
        "You summarize businesses based on their website content. Respond with ONLY a concise summary (3-5 sentences) covering:
1. What specific services or products they offer
2. Who their typical customers are
3. How they likely handle billing (e.g. do they invoice clients, do project quotes, charge hourly, sell products, etc.)
4. Any pain points a simple bookkeeping/invoicing tool could solve for them (e.g. tracking job expenses, sending invoices, managing payments)
Be specific and factual based on the website content. Do not include any other text or preamble.",
        "Website content from $website:\n\n$text"
    );

    return $result['content'] ?? null;
}

// ─── Draft Generation ───

/**
 * Generate an AI email draft for a lead. Saves draft to DB.
 * Returns ['success' => true, 'subject' => ..., 'body' => ...] or ['error' => ...].
 */
function generate_draft_for_lead($pdo, $lead)
{
    $id = $lead['id'];

    // A/B variant lookup must happen before the summary block so a
    // personalization test can gate the OpenAI summary call entirely.
    // Only one type can be active at a time per the framework's invariant.
    $abTestId = null;
    $abVariantId = null;
    $abSubjectOverride = '';
    $abBodyOverride = '';
    $abCtaOverride = '';
    $personalizationOff = false;
    foreach (['subject', 'body', 'cta', 'sender', 'personalization'] as $eligibleType) {
        $active = get_active_ab_test($pdo, $eligibleType);
        if (!$active) continue;
        $variant = pick_ab_variant($pdo, $active['test'], $active['variants']);
        $abTestId = (int) $active['test']['id'];
        $abVariantId = (int) $variant['id'];
        $instruction = ab_instruction_for_variant($variant, $eligibleType);
        if ($eligibleType === 'subject') $abSubjectOverride = $instruction;
        elseif ($eligibleType === 'body') $abBodyOverride = $instruction;
        elseif ($eligibleType === 'cta') $abCtaOverride = $instruction;
        elseif ($eligibleType === 'personalization') {
            $personalizationOff = (trim((string) $variant['content']) === 'off');
        }
        // sender / preheader / format: assignment alone is what matters; their
        // dispatched instruction is empty and they apply at send time.
        break;
    }

    // Generate a business summary if we don't have one yet — unless the lead
    // is in the 'off' arm of an active personalization test, in which case we
    // skip the OpenAI call entirely. If a stored summary exists from before
    // the test started, mask it for this draft so the prompt truly operates
    // without personalization.
    $summary = $lead['business_summary'] ?? null;
    if ($personalizationOff) {
        $summary = null;
    } elseif (empty($summary) && !empty($lead['website'])) {
        $summary = summarize_business($lead['website']);
        if ($summary) {
            $stmt = $pdo->prepare("UPDATE outreach_leads SET business_summary = ? WHERE id = ?");
            $stmt->execute([$summary, $id]);
        }
    }

    $isLocal = false;
    $city = strtolower(trim($lead['city'] ?? ''));
    $province = strtolower(trim($lead['province'] ?? ''));
    if ($province === 'saskatchewan' || $province === 'sk' || in_array($city, ['saskatoon','regina','prince albert','moose jaw','swift current','yorkton','north battleford','estevan','weyburn','martensville','warman','humboldt','melfort','meadow lake','lloydminster'])) {
        $isLocal = true;
    }

    $isSaskatoon = in_array($city, ['saskatoon','martensville','warman']);

    $localInstruction = $isLocal
        ? "- The business is in Saskatchewan. Evan is a local Saskatchewan software developer based in Saskatoon. ALWAYS mention being local, e.g. \"I'm a local Saskatoon software developer\" or \"As a fellow Saskatchewan business\". This local connection is important, make it feel personal."
        : "- The business is outside Saskatchewan. Evan is a Canadian software developer. Say \"Canadian software developer\", do NOT say \"local\" and do NOT mention Saskatoon or Saskatchewan.";

    $inPersonInstruction = $isSaskatoon
        ? "\n- IMPORTANT: Since this business is in the Saskatoon area, you MUST include an offer for an in-person visit to help them get set up. Work it in naturally, e.g. \"Since I'm right here in Saskatoon, I'd be happy to stop by and help you get set up in person\" or \"I could even swing by to walk you through it\". This is a key selling point for local businesses."
        : "\n- Do NOT mention any in-person visits or stopping by. The business is not in Saskatoon.";

    // Pull industry-level pain points for this category. These are typical
    // day-to-day headaches for the trade, NOT claims about this business.
    $painPoints = get_category_pain_points($lead['category'] ?? '');
    $painPointsList = '';
    foreach ($painPoints as $pp) {
        $painPointsList .= "  * " . $pp . "\n";
    }
    $categoryLabel = !empty($lead['category']) ? $lead['category'] : 'this industry';
    $painPointsInstruction = "\n- Small businesses in the '" . $categoryLabel . "' category commonly deal with things like:\n"
        . $painPointsList
        . "You MAY gently allude to ONE of these as something Argo Books can help with, phrased as a general industry pattern (e.g. \"businesses like yours often deal with X\"), NEVER as an assertion about this specific business. Pick at most one. If none fit naturally, skip them entirely.";

    $systemPrompt = "You are helping write a brief, personal outreach email from Evan, the developer behind Argo Books, to a small business. The goal is to get honest product feedback on Argo Books, a bookkeeping and invoicing app for small businesses.

About Argo Books:
- It is a simple bookkeeping and invoicing app designed so you do not need any accounting knowledge at all
- Built specifically for small businesses, not a bloated enterprise tool
- Features include invoicing, expense tracking, and simple bookkeeping
- Evan is " . ($isLocal ? "a local independent software developer based in Saskatoon" : "a Canadian independent software developer") . " building this specifically for small businesses

Rules:
- Keep it very short (2-3 short paragraphs max, under 100 words ideally)
- Sound human, friendly, and genuine, not like marketing spam
$localInstruction
$inPersonInstruction
- Do NOT refer to a \"team\", Evan is a solo developer
- Get to the point quickly in the first sentence - say why you are emailing. Do NOT open with generic filler like \"I hope this message finds you well\" or vague flattery like \"I admire your work\"
- Use the business name in the greeting (e.g. \"Hi LVM Landscaping\" or \"Hi [business name]\" if available)

PERSONALIZATION (this is critical):
- You may reference the business's industry/category to explain why Argo Books could be useful (e.g. \"running a landscaping business usually means a lot of invoicing\")
- NEVER claim to know specific details about how the business operates, what tools they use, what payment methods they accept, or how they handle their finances. You do NOT know these things. Do NOT say things like \"I know you handle donations\" or \"I see you use e-transfers\" or \"I noticed you do quotes\". This comes across as creepy and dishonest
- Instead of asserting facts about their business, use general industry knowledge. Say things like \"businesses like yours often deal with...\" or \"in the [industry] space, invoicing can be a hassle\" rather than \"I know you do X\"
- Only reference Argo Books features that are relevant to their general industry. Do not list every feature
- If a business summary is provided, use it ONLY to understand their industry and tailor which Argo features to mention. Do NOT parrot back details from the summary as if you personally know about their business
- If no summary is available, keep it more general but still mention their industry/category if known
$painPointsInstruction

- Briefly describe Argo Books as a simple bookkeeping and invoicing app that requires no accounting knowledge. Do NOT just say \"check it out\" without explaining what it is
- Mention you are looking for honest feedback from small business owners
- Mention offering a free 1-year premium license in exchange for feedback$abCtaOverride
- Use a casual but professional tone
- NEVER use placeholders like [Your Name], [Your Title], [Your Company], etc.
- ALWAYS include the website link https://argorobots.com/ in the email body. This is required in every single email, no exceptions
- NEVER use em dashes in the email. Use commas, periods, or regular hyphens instead
- The subject line should be about the recipient's business, NOT about Argo Books. Make it feel personal and curiosity-driven (e.g. \"Quick question about [business name]\", \"Thought of you guys\")$abSubjectOverride
- You MUST include the line \"You can check it out here: https://argorobots.com/\" (or similar natural phrasing with that exact URL) somewhere in the email body, ideally after mentioning what Argo Books is
- End the email body with a line like \"Feel free to reply to this email if you have any questions!\" or similar, before the sign-off
- After that line, add ONE short, respectful unsubscribe line on its own paragraph, such as: \"Not interested? {UNSUBSCRIBE_URL} and I'll stop emailing you.\" The literal token {UNSUBSCRIBE_URL} will be replaced with a tracked unsubscribe link before sending — include it verbatim, do NOT invent or replace the placeholder yourself. Keep the tone soft, brief, and non-pushy.
- Always sign off with three separate lines: \"All the best,\" then \"Evan\" then \"Argo Books\" (each on its own line, separated by \\n)$abBodyOverride

Return your response as JSON with two fields:
{\"subject\": \"the email subject line\", \"body\": \"the email body text (plain text, use \\n for line breaks)\"}

Return ONLY the JSON, no other text.";

    $details = "Business: {$lead['business_name']}";
    if ($lead['category']) $details .= "\nCategory/Industry: {$lead['category']}";
    if ($lead['city']) $details .= "\nCity: {$lead['city']}";
    if ($isLocal) $details .= "\nLocal: Yes, this business is in Saskatchewan (same province as Evan)";
    if ($lead['website']) $details .= "\nWebsite: {$lead['website']}";
    if ($lead['contact_name']) $details .= "\nContact person: {$lead['contact_name']}";
    if ($summary) $details .= "\nBusiness summary: $summary";

    $result = call_openai($systemPrompt, $details);

    if (isset($result['error'])) {
        return ['error' => $result['error']];
    }

    // Parse JSON response from AI
    $content = trim($result['content']);
    // Strip markdown code fences if present
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $parsed = json_decode($content, true);
    if (!$parsed || !isset($parsed['subject']) || !isset($parsed['body'])) {
        // AI returned invalid JSON — save with needs_review so it won't be auto-approved
        $fallbackSubject = "Quick question for {$lead['business_name']}";
        $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, ab_test_id = ?, ab_variant_id = ?, drafted_at = NOW(), approval_status = 'needs_review' WHERE id = ?");
        $stmt->execute([$fallbackSubject, $content, $abTestId, $abVariantId, $id]);

        return ['success' => true, 'needs_review' => true, 'subject' => $fallbackSubject, 'body' => $content];
    }

    // Ensure the website URL is in the body — inject before sign-off if AI omitted it
    if (stripos($parsed['body'], 'argorobots.com') === false) {
        $parsed['body'] = preg_replace(
            '/(Feel free to|Don\'t hesitate|Let me know|Reply to this)/i',
            "You can check it out here: https://argorobots.com/\n\n$1",
            $parsed['body'],
            1
        );
        // If regex didn't match, append before sign-off
        if (stripos($parsed['body'], 'argorobots.com') === false) {
            $parsed['body'] = preg_replace(
                '/(\nAll the best)/i',
                "\n\nYou can check it out here: https://argorobots.com/\n$1",
                $parsed['body'],
                1
            );
        }

        // Final fallback: if URL is still missing, append it at the end
        if (stripos($parsed['body'], 'argorobots.com') === false) {
            $parsed['body'] .= "\n\nYou can check it out here: https://argorobots.com/";
        }
    }

    // Save draft to lead
    $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, ab_test_id = ?, ab_variant_id = ?, drafted_at = NOW(), status = CASE WHEN status IN ('new','awaiting_approval','approved') THEN 'draft_generated' ELSE status END WHERE id = ?");
    $stmt->execute([$parsed['subject'], $parsed['body'], $abTestId, $abVariantId, $id]);

    return ['success' => true, 'subject' => $parsed['subject'], 'body' => $parsed['body'], 'ab_test_id' => $abTestId, 'ab_variant_id' => $abVariantId];
}

// ─── A/B Test Automation (called from stepManageAbTests in outreach_pipeline.php) ───

/**
 * Variant types the A/B framework knows about. Each later phase that wires
 * in a new type extends this list. The cron iterates over it when looking
 * for tests to promote.
 */
function ab_known_variant_types()
{
    return ['subject', 'body', 'sender', 'cta', 'preheader', 'format', 'personalization'];
}

/**
 * Evaluate the active test of a given variant type and promote a winner if
 * any exit criterion is met. Side effect: on trigger, UPDATE the test row to
 * completed with winner_variant_id set; optionally self-pauses automation if
 * the winning CTR is below the configured safety floor.
 *
 * Returns one of:
 *   ['action' => 'none', 'reason' => '...', 'variant_type' => $variantType, ...]
 *   ['action' => 'promoted', 'test_id' => N, 'variant_type' => $variantType, ...]
 *   ['action' => 'paused_safety', 'test_id' => N, 'variant_type' => $variantType, ...]
 *
 * Exit criteria (any one triggers promotion):
 *   a) leader significant at p<0.05 vs EVERY other variant AND every variant sent >= 30
 *   b) test age >= 14 days AND every variant sent >= 20
 *   c) test age >= 28 days (force-close; leader by CTR, ties by assigned then lowest id)
 */
function ab_check_and_promote_active_test($pdo, $variantType = 'subject')
{
    $active = get_active_ab_test($pdo, $variantType);
    if (!$active) {
        return ['action' => 'none', 'reason' => 'no_active_test', 'variant_type' => $variantType];
    }
    $test = $active['test'];

    $variants = load_variants_with_stats($pdo, (int) $test['id']);
    if (count($variants) < 2) {
        return ['action' => 'none', 'reason' => 'too_few_variants'];
    }

    $leaderIdx = find_leader_idx($variants);
    $startedAt = strtotime($test['started_at'] ?: $test['created_at']);
    $ageDays = (int) floor((time() - $startedAt) / 86400);
    $minSent = min(array_column($variants, 'sent_count'));

    $trigger = null;

    // Criterion (a)
    if ($leaderIdx !== null && $minSent >= 30) {
        $allSig = true;
        foreach ($variants as $i => $v) {
            if ($i === $leaderIdx) continue;
            $c = confidence_vs_leader(
                $variants[$leaderIdx]['sent_count'],
                $variants[$leaderIdx]['clicked_count'],
                $v['sent_count'],
                $v['clicked_count']
            );
            if ($c['tag'] !== 'significant') { $allSig = false; break; }
        }
        if ($allSig) $trigger = 'significance';
    }

    // Criterion (b)
    if (!$trigger && $leaderIdx !== null && $ageDays >= 14 && $minSent >= 20) {
        $trigger = 'timebox';
    }

    // Criterion (c) — force-close even if nothing has been sent yet
    if (!$trigger && $ageDays >= 28) {
        $trigger = 'hard_timeout';
        if ($leaderIdx === null) {
            $leaderIdx = 0;
            foreach ($variants as $i => $v) {
                if ($v['assigned_count'] > $variants[$leaderIdx]['assigned_count']) {
                    $leaderIdx = $i;
                }
            }
        }
    }

    if (!$trigger) {
        return [
            'action' => 'none',
            'reason' => 'criteria_not_met',
            'variant_type' => $variantType,
            'age_days' => $ageDays,
            'min_sent' => $minSent,
            'test_id' => (int) $test['id'],
            'test_name' => $test['name'],
        ];
    }

    $winner = $variants[$leaderIdx];
    $pdo->prepare("UPDATE outreach_ab_tests SET winner_variant_id = ?, status = 'completed', completed_at = NOW() WHERE id = ?")
        ->execute([$winner['id'], $test['id']]);

    // Safety-floor check
    $floorStmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = 'ab_ctr_floor'");
    $floorStmt->execute();
    $floorVal = $floorStmt->fetchColumn();
    $floor = ($floorVal !== false) ? (float) $floorVal : 0.01;

    $pausedForSafety = false;
    if ($winner['sent_count'] >= 20 && $winner['ctr'] < $floor) {
        $pauseStmt = $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)");
        $pauseStmt->execute(['ab_auto_enabled', '0']);
        $pauseStmt->execute([
            'ab_auto_last_pause_reason',
            'Winner CTR ' . number_format($winner['ctr'] * 100, 2) . '% below floor '
                . number_format($floor * 100, 2) . '% on test #' . (int) $test['id'],
        ]);
        $pausedForSafety = true;
    }

    return [
        'action' => $pausedForSafety ? 'paused_safety' : 'promoted',
        'variant_type' => $variantType,
        'test_id' => (int) $test['id'],
        'test_name' => $test['name'],
        'winner_id' => (int) $winner['id'],
        'winner_label' => $winner['label'],
        'winner_ctr' => $winner['ctr'],
        'trigger' => $trigger,
        'age_days' => $ageDays,
    ];
}

/**
 * Ask OpenAI for N fresh subject-line directives for the next A/B cycle.
 * Seeds the prompt with the content of the most-recent winners so proven
 * styles get reinforced. Falls back to a curated seed list if OpenAI errors.
 *
 * Returns ['directives' => [...strings...], 'source' => 'ai'|'fallback'].
 */
function generate_ab_subject_variants($pdo, $count = 3)
{
    $winStmt = $pdo->prepare("
        SELECT v.content
        FROM outreach_ab_tests t
        JOIN outreach_ab_variants v ON v.id = t.winner_variant_id
        WHERE t.status = 'completed' AND t.variant_type = 'subject'
        ORDER BY t.completed_at DESC
        LIMIT 3
    ");
    $winStmt->execute();
    $priorWinners = array_column($winStmt->fetchAll(), 'content');

    $winnersText = '';
    if (!empty($priorWinners)) {
        $winnersText = "\n\nRecent winning subject strategies (most recent first):\n";
        foreach ($priorWinners as $w) {
            $winnersText .= "- " . trim((string) $w) . "\n";
        }
    }

    $systemPrompt = "You generate subject-line directives for an A/B test on a small-business outreach email from Evan, a solo developer, about a simple bookkeeping app called Argo Books.\n\n"
        . "Return STRICT JSON: { \"directives\": [\"...\", \"...\"] } with exactly $count entries.\n\n"
        . "Rules for each directive:\n"
        . "- Start with an imperative verb (Ask, Lead with, Reference, Keep, etc).\n"
        . "- Describe a STYLE the AI should use when writing the subject for each lead; do NOT write a literal subject line.\n"
        . "- 8 to 20 words. No em dashes.\n"
        . "- Each directive must be meaningfully different from the others in tone, angle, or structure (question vs statement vs curiosity tease vs local angle vs ultra-short, etc.).\n"
        . "- Do not invent product names or facts. Refer to placeholders generically: the business name, their industry, their city.\n"
        . "- Optimise for cold B2B open rate: personal, curious, short. Avoid marketing-speak.";

    $userPrompt = "Propose $count distinct subject-line directives for the next A/B cycle." . $winnersText;

    $result = call_openai($systemPrompt, $userPrompt);
    $directives = null;
    if (!isset($result['error'])) {
        $content = trim($result['content'] ?? '');
        $content = preg_replace('/^```json\s*/i', '', $content);
        $content = preg_replace('/\s*```$/', '', $content);
        $parsed = json_decode($content, true);
        if (is_array($parsed) && isset($parsed['directives']) && is_array($parsed['directives'])) {
            $clean = [];
            foreach ($parsed['directives'] as $d) {
                $d = trim((string) $d);
                if ($d !== '') $clean[] = mb_substr($d, 0, 500);
            }
            if (count($clean) >= 2) {
                $directives = array_slice($clean, 0, $count);
            }
        }
    }

    if (!$directives) {
        $fallback = [
            'Ask a short curiosity question that references the business name without making claims about how they operate',
            'Lead with a single concrete pain point the industry commonly has, phrased as a question',
            'Reference the city casually to sound local, under 10 words, no exclamation marks',
            'Keep it ultra-short (under 6 words) and intriguing without mentioning the product',
            'Open with the industry name as a single-word hook then a brief follow-up question',
        ];
        shuffle($fallback);
        return ['directives' => array_slice($fallback, 0, $count), 'source' => 'fallback'];
    }

    return ['directives' => $directives, 'source' => 'ai'];
}

/**
 * Per-type variant generator dispatch. Phase 0 handles 'subject' only;
 * later phases register additional types here. Returns the same shape
 * generate_ab_subject_variants() returns: ['directives' => [...], 'source' => 'ai'|'fallback'].
 *
 * Returns ['directives' => [], 'source' => 'unsupported'] if no generator
 * is registered for the requested type — caller should treat as failure.
 */
function generate_ab_variants_for_type($pdo, $variantType, $count = 3)
{
    switch ($variantType) {
        case 'subject':
            return generate_ab_subject_variants($pdo, $count);
        case 'sender':
            // Small fixed pool — content is the literal from-name.
            return [
                'directives' => ['Evan', 'Evan from Argo Books', 'Argo Books'],
                'source' => 'fixed',
                'literal' => true,
            ];
        case 'format':
            return [
                'directives' => ['html', 'plain'],
                'source' => 'fixed',
                'literal' => true,
            ];
        case 'personalization':
            return [
                'directives' => ['on', 'off'],
                'source' => 'fixed',
                'literal' => true,
            ];
        // body / cta / preheader stay admin-initiated — they need carefully
        // crafted copy and there's no AI generator yet. The rotation in
        // stepManageAbTests skips types not represented here.
        default:
            return ['directives' => [], 'source' => 'unsupported'];
    }
}

/**
 * Rotation order used by stepManageAbTests when ab_auto_rotation is on.
 * Every type listed here must have a generator in generate_ab_variants_for_type.
 */
function ab_auto_rotation_order()
{
    return ['subject', 'sender', 'format', 'personalization'];
}

/**
 * Start a new auto-cycle test for a given variant type. The previous winner
 * for that type (if any) is carried forward as variant A so the established
 * baseline keeps being measured; newly-generated directives fill the other slots.
 *
 * Returns ['action' => 'created', 'test_id' => N, 'variant_type' => ..., ...]
 *      or ['action' => 'failed', 'variant_type' => ..., 'error' => '...'].
 */
function ab_start_new_cycle($pdo, $variantType = 'subject')
{
    $count = 3;

    $gen = generate_ab_variants_for_type($pdo, $variantType, $count);
    $items = $gen['directives'] ?? [];
    $isLiteral = !empty($gen['literal']);

    if (count($items) < 2) {
        return [
            'action' => 'failed',
            'variant_type' => $variantType,
            'error' => 'Variant generation returned fewer than 2 entries (source: ' . ($gen['source'] ?? 'unknown') . ')',
        ];
    }

    // Carry-forward only makes sense for directive-style types where each
    // cycle generates *new* candidate copy and we want the prior winner kept
    // as a baseline. Literal types (sender / format / personalization) cycle
    // over a fixed pool, so carry-forward would just duplicate one variant.
    $prior = null;
    if (!$isLiteral) {
        $priorStmt = $pdo->prepare("
            SELECT v.content
            FROM outreach_ab_tests t
            JOIN outreach_ab_variants v ON v.id = t.winner_variant_id
            WHERE t.status = 'completed' AND t.variant_type = ?
            ORDER BY t.completed_at DESC
            LIMIT 1
        ");
        $priorStmt->execute([$variantType]);
        $prior = $priorStmt->fetchColumn() ?: null;
    }

    $name = 'Auto-cycle ' . $variantType . ' ' . date('Y-m-d H:i');
    $notes = 'Auto-generated by stepManageAbTests. Source: ' . ($gen['source'] ?? 'ai')
        . ($prior ? '. Prior winner carried forward as variant A.' : '.');

    $pdo->beginTransaction();
    try {
        $pdo->prepare("INSERT INTO outreach_ab_tests (name, variant_type, status, started_at, notes) VALUES (?, ?, 'active', NOW(), ?)")
            ->execute([$name, $variantType, $notes]);
        $testId = (int) $pdo->lastInsertId();

        $vStmt = $pdo->prepare("INSERT INTO outreach_ab_variants (test_id, label, content, is_default) VALUES (?, ?, ?, ?)");

        $label = 'A';
        $isDefault = 1;

        if ($prior) {
            $vStmt->execute([$testId, $label, $prior, $isDefault]);
            $label = 'B';
            $isDefault = 0;
        }

        foreach ($items as $d) {
            $content = $isLiteral ? $d : ('directive: ' . $d);
            $vStmt->execute([$testId, $label, $content, $isDefault]);
            $isDefault = 0;
            if ($label === 'D') break;
            $label = chr(ord($label) + 1);
        }

        $pdo->commit();

        $varStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_ab_variants WHERE test_id = ?");
        $varStmt->execute([$testId]);
        $variantCount = (int) $varStmt->fetchColumn();

        return [
            'action' => 'created',
            'variant_type' => $variantType,
            'test_id' => $testId,
            'test_name' => $name,
            'variant_count' => $variantCount,
            'carried_winner' => (bool) $prior,
            'source' => $gen['source'] ?? 'ai',
        ];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['action' => 'failed', 'variant_type' => $variantType, 'error' => $e->getMessage()];
    }
}
