<?php
session_start();
require_once '../../db_connect.php';
require_once '../../email_sender.php';

// Admin auth check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// CSRF protection for state-changing (non-GET) requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $csrfSession = $_SESSION['csrf_token'] ?? null;
    $csrfRequest = $_POST['csrf_token'] ?? '';
    if (empty($csrfRequest)) {
        $input = json_decode(file_get_contents('php://input'), true);
        $csrfRequest = $input['csrf_token'] ?? '';
    }
    if (!$csrfSession || !$csrfRequest || !hash_equals($csrfSession, $csrfRequest)) {
        outreach_log("CSRF token validation failed for action: " . ($_GET['action'] ?? 'unknown'));
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }
}

// Ensure tables exist


$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    // Lead CRUD
    case 'get_leads':
        get_leads($pdo);
        break;
    case 'get_lead':
        get_lead($pdo);
        break;
    case 'create_lead':
        create_lead($pdo);
        break;
    case 'update_lead':
        update_lead($pdo);
        break;
    case 'delete_lead':
        delete_lead($pdo);
        break;
    case 'get_stats':
        get_stats($pdo);
        break;

    // Business discovery
    case 'search_businesses':
        search_businesses();
        break;
    case 'import_leads':
        import_leads($pdo);
        break;

    // AI draft
    case 'generate_draft':
        generate_draft($pdo);
        break;
    // Email workflow
    case 'send_email':
        send_outreach_email($pdo);
        break;
    case 'bulk_get_leads':
        bulk_get_leads($pdo);
        break;

    // Activity
    case 'get_activity':
        get_activity($pdo);
        break;

    // AI classification
    case 'classify_company_sizes':
        classify_company_sizes($pdo);
        break;

    // CSV
    case 'export_csv':
        export_csv($pdo);
        break;
    case 'import_csv':
        import_csv($pdo);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}


// ─── Activity logging helper ───

function log_activity($pdo, $lead_id, $action_type, $details = null)
{
    $stmt = $pdo->prepare("INSERT INTO outreach_activity_log (lead_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$lead_id, $action_type, $details]);
}

// ─── JSON response helper ───

function json_response($data, $code = 200)
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// ─── File-based error logging ───

function outreach_log($message)
{
    // Sanitize message to avoid log injection (strip newlines)
    $sanitizedMessage = preg_replace('/[\r\n]+/', ' ', (string) $message);
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[outreach][$timestamp] " . $sanitizedMessage;
    // Use PHP's error_log to avoid writing to web-accessible directory
    @error_log($entry);
}

// ─── Lead CRUD ───

function get_leads($pdo)
{
    $status = $_GET['status'] ?? '';
    $response_status = $_GET['response_status'] ?? '';
    $company_size = $_GET['company_size'] ?? '';
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'date_added_desc';

    $where = [];
    $params = [];

    if ($status) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    if ($response_status) {
        $where[] = 'response_status = ?';
        $params[] = $response_status;
    }
    if ($company_size) {
        $where[] = 'company_size = ?';
        $params[] = $company_size;
    }
    if ($search) {
        $where[] = '(business_name LIKE ? OR email LIKE ? OR contact_name LIKE ? OR city LIKE ? OR category LIKE ?)';
        $s = "%$search%";
        $params = array_merge($params, [$s, $s, $s, $s, $s]);
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $orderMap = [
        'date_added_desc' => 'date_added DESC',
        'date_added_asc' => 'date_added ASC',
        'last_contact_desc' => 'last_contact_date DESC',
        'business_name_asc' => 'business_name ASC',
        'status_asc' => 'status ASC',
    ];
    $orderBy = $orderMap[$sort] ?? 'date_added DESC';

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads $whereClause ORDER BY $orderBy");
    $stmt->execute($params);
    $leads = $stmt->fetchAll();

    json_response(['success' => true, 'leads' => $leads]);
}

function get_lead($pdo)
{
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        json_response(['success' => false, 'message' => 'Lead not found'], 404);
    }

    json_response(['success' => true, 'lead' => $lead]);
}

function bulk_get_leads($pdo)
{
    $idsParam = $_GET['ids'] ?? '';
    $ids = array_filter(array_map('intval', explode(',', $idsParam)));

    if (empty($ids)) {
        json_response(['success' => false, 'message' => 'No IDs provided'], 400);
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $leads = $stmt->fetchAll();

    json_response(['success' => true, 'leads' => $leads]);
}

function create_lead($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    if (empty($data['business_name'])) {
        json_response(['success' => false, 'message' => 'Business name is required'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO outreach_leads
        (business_name, contact_name, email, phone, website, address, category, city, source, status, notes, contact_page_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['business_name'],
        $data['contact_name'] ?? null,
        $data['email'] ?? null,
        $data['phone'] ?? null,
        $data['website'] ?? null,
        $data['address'] ?? null,
        $data['category'] ?? null,
        $data['city'] ?? null,
        $data['source'] ?? 'manual',
        $data['status'] ?? 'new',
        $data['notes'] ?? null,
        $data['contact_page_url'] ?? null,
    ]);

    $id = $pdo->lastInsertId();
    log_activity($pdo, $id, 'lead_created', 'Lead created: ' . $data['business_name']);

    json_response(['success' => true, 'id' => $id, 'message' => 'Lead created']);
}

function update_lead($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int)($data['id'] ?? 0);

    if (!$id) {
        json_response(['success' => false, 'message' => 'Lead ID is required'], 400);
    }

    $fields = [
        'business_name', 'contact_name', 'email', 'phone', 'website', 'address',
        'category', 'city', 'source', 'status', 'response_status',
        'notes', 'feedback_summary', 'offer_sent',
        'draft_subject', 'draft_body', 'contact_page_url',
        'first_contact_date', 'last_contact_date', 'company_size',
    ];

    $setClauses = [];
    $params = [];
    $changes = [];

    foreach ($fields as $field) {
        if (array_key_exists($field, $data)) {
            $setClauses[] = "$field = ?";
            $value = $data[$field];
            if ($value === '') $value = null;
            if ($field === 'offer_sent') $value = $value ? 1 : 0;
            $params[] = $value;
            $changes[] = $field;
        }
    }

    if (empty($setClauses)) {
        json_response(['success' => false, 'message' => 'No fields to update'], 400);
    }

    $params[] = $id;
    $sql = "UPDATE outreach_leads SET " . implode(', ', $setClauses) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Log notable changes
    if (in_array('status', $changes)) {
        log_activity($pdo, $id, 'status_changed', 'Status changed to: ' . $data['status']);
    }
    if (in_array('notes', $changes)) {
        log_activity($pdo, $id, 'notes_updated', 'Notes updated');
    }

    json_response(['success' => true, 'message' => 'Lead updated']);
}

function delete_lead($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int)($data['id'] ?? 0);

    if (!$id) {
        outreach_log("Delete failed: no lead ID provided");
        json_response(['success' => false, 'message' => 'Lead ID is required'], 400);
    }

    try {
        // Check lead exists before deleting anything
        $stmt = $pdo->prepare("SELECT id FROM outreach_leads WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            outreach_log("Delete failed: lead ID $id not found");
            json_response(['success' => false, 'message' => 'Lead not found'], 404);
        }

        $pdo->beginTransaction();

        // Delete activity log entries first
        $stmt = $pdo->prepare("DELETE FROM outreach_activity_log WHERE lead_id = ?");
        $stmt->execute([$id]);

        // Delete the lead
        $stmt = $pdo->prepare("DELETE FROM outreach_leads WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit();

        json_response(['success' => true, 'message' => 'Lead deleted']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        outreach_log("Delete failed for lead ID $id: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Failed to delete lead'], 500);
    }
}

function get_stats($pdo)
{
    $stats = [];
    $rows = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(status = 'new') as new_leads,
        SUM(status = 'draft_generated') as drafts_pending,
        SUM(status = 'contacted') as contacted,
        SUM(status = 'replied') as replied,
        SUM(status = 'interested') as interested
    FROM outreach_leads")->fetch();

    json_response(['success' => true, 'stats' => $rows]);
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

function search_businesses()
{
    $city = trim($_GET['city'] ?? '');
    $province = trim($_GET['province'] ?? '');
    $category = trim($_GET['category'] ?? '');
    $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
    $excludePlaceIds = array_filter(explode(',', $_GET['exclude_place_ids'] ?? ''));

    if (empty($city)) {
        json_response(['success' => false, 'message' => 'City is required'], 400);
    }

    $apiKey = $_ENV['GOOGLE_PLACES_API_KEY'] ?? '';
    if (empty($apiKey)) {
        json_response(['success' => false, 'message' => 'Google Places API key not configured. Set GOOGLE_PLACES_API_KEY in .env'], 500);
    }

    $location = $province ? "$city, $province" : $city;
    $businesses = [];
    $seenPlaceIds = [];
    // Pre-seed seen IDs so we skip businesses the client already has
    foreach ($excludePlaceIds as $id) {
        $seenPlaceIds[trim($id)] = true;
    }
    $maxRounds = 5;
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
        // When no category provided, use a wide spread of real business categories
        // so each round searches a different industry instead of generic synonyms
        $categoryPool = [
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
                json_response(['success' => false, 'message' => 'Failed to connect to Google Places API'], 502);
            }
            break;
        }

        $data = json_decode($resp, true);
        $status = $data['status'] ?? '';
        if ($status !== 'OK' && $status !== 'ZERO_RESULTS') {
            if ($roundsUsed === 1) {
                $errorMsg = $data['error_message'] ?? $status ?? 'Unknown error';
                json_response(['success' => false, 'message' => 'Google Places API error: ' . $errorMsg], 502);
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

                // Scrape email from business website
                $business['email'] = scrape_email_from_website($business['website']);

                // Skip businesses where we couldn't find an email
                if (empty($business['email'])) continue;

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

    $response = ['success' => true, 'businesses' => $businesses, 'count' => count($businesses), 'rounds' => $roundsUsed];
    if (count($businesses) < $limit) {
        $response['note'] = "Found " . count($businesses) . " of $limit requested after searching $roundsUsed round(s). Only businesses with a website and scrapeable email are included.";
    }
    json_response($response);
}

function import_leads($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $businesses = $data['businesses'] ?? [];

    if (empty($businesses)) {
        json_response(['success' => false, 'message' => 'No businesses to import'], 400);
    }

    try {
        $imported = 0;
        $skipped = 0;

        foreach ($businesses as $biz) {
            // Deduplicate by places_id
            if (!empty($biz['places_id'])) {
                $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE places_id = ?");
                $check->execute([$biz['places_id']]);
                if ($check->fetch()) {
                    $skipped++;
                    continue;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO outreach_leads
                (business_name, phone, website, address, category, city, source, places_id, contact_page_url, email, company_size)
                VALUES (?, ?, ?, ?, ?, ?, 'google_places', ?, ?, ?, ?)");
            $stmt->execute([
                $biz['business_name'] ?? 'Unknown',
                $biz['phone'] ?? null,
                $biz['website'] ?? null,
                $biz['address'] ?? null,
                $biz['category'] ?? null,
                $biz['city'] ?? null,
                $biz['places_id'] ?? null,
                $biz['contact_page_url'] ?? null,
                $biz['email'] ?? null,
                $biz['company_size'] ?? null,
            ]);

            $id = $pdo->lastInsertId();
            log_activity($pdo, $id, 'lead_created', 'Imported from Google Places: ' . ($biz['business_name'] ?? 'Unknown'));
            $imported++;
        }

        json_response(['success' => true, 'imported' => $imported, 'skipped' => $skipped, 'message' => "Imported $imported leads, skipped $skipped duplicates"]);
    } catch (Exception $e) {
        outreach_log("Import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

// ─── AI Draft Generation ───

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
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['error']['message'] ?? 'OpenAI request failed';
        return ['error' => $errorMsg];
    }

    $result = json_decode($response, true);
    return ['content' => $result['choices'][0]['message']['content'] ?? ''];
}

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
    $text = preg_replace('/<style[^>]*>.*?<\/style>/is', '', $html);
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

function generate_draft($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        json_response(['success' => false, 'message' => 'Lead not found'], 404);
    }

    // Generate business summary if we don't have one yet
    $summary = $lead['business_summary'] ?? null;
    if (empty($summary) && !empty($lead['website'])) {
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

    $localInstruction = $isLocal
        ? "- The business is in Saskatchewan. Evan is a local Saskatchewan software developer based in Saskatoon. ALWAYS mention being local, e.g. \"I'm a local Saskatoon software developer\" or \"As a fellow Saskatchewan business\". This local connection is important, make it feel personal."
        : "- Evan is an independent software developer based in Saskatoon, Saskatchewan. Mention this briefly for context.";

    $systemPrompt = "You are helping write a brief, personal outreach email from Evan, the developer behind Argo Books, to a small business. The goal is to get honest product feedback on Argo Books, a bookkeeping and invoicing app for small businesses.

About Argo Books:
- It is like QuickBooks but way simpler, designed so you do not need any accounting knowledge at all
- Built specifically for small businesses, not a bloated enterprise tool
- Features include invoicing, expense tracking, and simple bookkeeping
- Evan is a local independent software developer based in Saskatoon building this specifically for small businesses

Rules:
- Keep it very short (2-3 short paragraphs max, under 100 words ideally)
- Sound human, friendly, and genuine, not like marketing spam
$localInstruction
- Do NOT refer to a \"team\", Evan is a solo developer
- Get to the point quickly in the first sentence - say why you are emailing. Do NOT open with generic filler like \"I hope this message finds you well\" or vague flattery like \"I admire your work\"
- Use the business name in the greeting (e.g. \"Hi LVM Landscaping\" or \"Hi [contact name]\" if available)

PERSONALIZATION (this is critical):
- If a business summary is provided, you MUST use it to make the email specific to their business. Do not write a generic email when you have summary info
- Connect Argo Books features directly to their business needs. Examples:
  - If they do services/contracting: mention how easy it is to invoice clients after a job
  - If they sell products: mention simple expense tracking and bookkeeping
  - If they likely deal with quotes/estimates: mention invoicing features
  - If they have multiple revenue streams: mention how it keeps everything organized without accounting knowledge
- Reference their actual business type naturally (e.g. \"I know running a landscaping business means a lot of invoicing\" not just \"I see you run a business\")
- Only reference Argo Books features that are relevant to what they do. Do not list every feature
- Do NOT invent details about the business you do not have
- If no summary is available, keep it more general but still mention their industry/category if known

- Briefly describe Argo Books as a simpler alternative to QuickBooks that requires no accounting knowledge. Do NOT just say \"check it out\" without explaining what it is
- Mention you are looking for honest feedback from small business owners
- If appropriate, mention offering a free 1-year premium license in exchange for feedback
- Use a casual but professional tone
- NEVER use placeholders like [Your Name], [Your Title], [Your Company], etc.
- ALWAYS include the website link https://argorobots.com/ in the email body. This is required in every single email, no exceptions
- NEVER use em dashes in the email. Use commas, periods, or regular hyphens instead
- The subject line should be about the recipient's business, NOT about Argo Books. Make it feel personal and curiosity-driven (e.g. \"Quick question about [business name]\", \"Thought of you guys\")
- You MUST include the line \"You can check it out here: https://argorobots.com/\" (or similar natural phrasing with that exact URL) somewhere in the email body, ideally after mentioning what Argo Books is
- End the email body with a line like \"Feel free to reply to this email if you have any questions!\" or similar, before the sign-off
- Always sign off with three separate lines: \"All the best,\" then \"Evan\" then \"Argo Books\" (each on its own line, separated by \\n)

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
        json_response(['success' => false, 'message' => $result['error']], 500);
    }

    // Parse JSON response from AI
    $content = trim($result['content']);
    // Strip markdown code fences if present
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $parsed = json_decode($content, true);
    if (!$parsed || !isset($parsed['subject']) || !isset($parsed['body'])) {
        // Fallback: use content as body
        $parsed = [
            'subject' => "Quick question for {$lead['business_name']}",
            'body' => $content,
        ];
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
    }

    // Save draft to lead
    $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, drafted_at = NOW(), status = CASE WHEN status IN ('new','awaiting_approval','approved') THEN 'draft_generated' ELSE status END WHERE id = ?");
    $stmt->execute([$parsed['subject'], $parsed['body'], $id]);

    log_activity($pdo, $id, 'draft_generated', 'AI draft generated');

    json_response(['success' => true, 'subject' => $parsed['subject'], 'body' => $parsed['body']]);
}

// ─── Email workflow ───

function send_outreach_email($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        json_response(['success' => false, 'message' => 'Lead not found'], 404);
    }

    if (empty($lead['email'])) {
        json_response(['success' => false, 'message' => 'No email address for this lead'], 400);
    }

    if (empty($lead['draft_subject']) || empty($lead['draft_body'])) {
        json_response(['success' => false, 'message' => 'No draft to send'], 400);
    }

    // Format body for HTML email (convert newlines to <br>)
    $htmlBody = '<p>' . nl2br(htmlspecialchars($lead['draft_body'])) . '</p>';

    $result = send_styled_email($lead['email'], $lead['draft_subject'], $htmlBody, '', 'contact@argorobots.com', 'Argo Books', 'contact@argorobots.com');

    if ($result) {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET
            sent_at = NOW(),
            status = CASE WHEN status NOT IN ('replied','interested','not_interested','onboarded') THEN 'contacted' ELSE status END,
            first_contact_date = COALESCE(first_contact_date, NOW()),
            last_contact_date = NOW()
            WHERE id = ?");
        $stmt->execute([$id]);

        log_activity($pdo, $id, 'email_sent', 'Outreach email sent to: ' . $lead['email']);

        json_response(['success' => true, 'message' => 'Email sent successfully']);
    } else {
        log_activity($pdo, $id, 'email_failed', 'Email send failed for: ' . $lead['email']);
        json_response(['success' => false, 'message' => 'Failed to send email. Check SMTP configuration.'], 500);
    }
}

// ─── Activity log ───

function get_activity($pdo)
{
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM outreach_activity_log WHERE lead_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$id]);
    $activity = $stmt->fetchAll();

    json_response(['success' => true, 'activity' => $activity]);
}

// ─── CSV Export/Import ───

function export_csv($pdo)
{
    $status = $_GET['status'] ?? '';
    $where = '';
    $params = [];
    if ($status) {
        $where = 'WHERE status = ?';
        $params[] = $status;
    }

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads $where ORDER BY date_added DESC");
    $stmt->execute($params);
    $leads = $stmt->fetchAll();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="outreach_leads_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    $headers = ['ID', 'Business Name', 'Contact Name', 'Email', 'Phone', 'Website', 'Address',
        'Category', 'City', 'Source', 'Status', 'Response Status',
        'Date Added', 'First Contact', 'Last Contact', 'Offer Sent',
        'Notes', 'Feedback Summary', 'Draft Subject', 'Draft Body'];
    fputcsv($output, $headers);

    foreach ($leads as $lead) {
        fputcsv($output, [
            $lead['id'], $lead['business_name'], $lead['contact_name'], $lead['email'],
            $lead['phone'], $lead['website'], $lead['address'], $lead['category'],
            $lead['city'], $lead['source'], $lead['status'], $lead['response_status'],
            $lead['date_added'], $lead['first_contact_date'],
            $lead['last_contact_date'], $lead['offer_sent'],
            $lead['notes'], $lead['feedback_summary'], $lead['draft_subject'], $lead['draft_body'],
        ]);
    }

    fclose($output);
    exit;
}

function import_csv($pdo)
{
    if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        json_response(['success' => false, 'message' => 'No CSV file uploaded or upload error'], 400);
    }

    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');
    if (!$file) {
        json_response(['success' => false, 'message' => 'Could not open CSV file'], 500);
    }

    $headerRow = fgetcsv($file);
    if (!$headerRow) {
        json_response(['success' => false, 'message' => 'Empty CSV file'], 400);
    }

    // Normalize headers
    $headerMap = array_map(function ($h) {
        return strtolower(trim(str_replace([' ', '-'], '_', $h)));
    }, $headerRow);

    $fieldMap = [
        'business_name' => ['business_name', 'business', 'name', 'company', 'company_name'],
        'contact_name' => ['contact_name', 'contact', 'contact_person'],
        'email' => ['email', 'email_address', 'e_mail'],
        'phone' => ['phone', 'phone_number', 'telephone'],
        'website' => ['website', 'url', 'web'],
        'address' => ['address', 'street_address', 'location'],
        'category' => ['category', 'industry', 'type', 'business_type'],
        'city' => ['city', 'town', 'area'],
        'notes' => ['notes', 'note', 'comments'],
    ];

    $columnIndex = [];
    foreach ($fieldMap as $field => $aliases) {
        foreach ($aliases as $alias) {
            $idx = array_search($alias, $headerMap);
            if ($idx !== false) {
                $columnIndex[$field] = $idx;
                break;
            }
        }
    }

    if (!isset($columnIndex['business_name'])) {
        json_response(['success' => false, 'message' => 'CSV must have a Business Name column'], 400);
    }

    $imported = 0;
    while (($row = fgetcsv($file)) !== false) {
        $businessName = trim($row[$columnIndex['business_name']] ?? '');
        if (empty($businessName)) continue;

        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, phone, website, address, category, city, source, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'csv_import', ?)");

        $stmt->execute([
            $businessName,
            isset($columnIndex['contact_name']) ? trim($row[$columnIndex['contact_name']] ?? '') ?: null : null,
            isset($columnIndex['email']) ? trim($row[$columnIndex['email']] ?? '') ?: null : null,
            isset($columnIndex['phone']) ? trim($row[$columnIndex['phone']] ?? '') ?: null : null,
            isset($columnIndex['website']) ? trim($row[$columnIndex['website']] ?? '') ?: null : null,
            isset($columnIndex['address']) ? trim($row[$columnIndex['address']] ?? '') ?: null : null,
            isset($columnIndex['category']) ? trim($row[$columnIndex['category']] ?? '') ?: null : null,
            isset($columnIndex['city']) ? trim($row[$columnIndex['city']] ?? '') ?: null : null,
            isset($columnIndex['notes']) ? trim($row[$columnIndex['notes']] ?? '') ?: null : null,
        ]);

        $id = $pdo->lastInsertId();
        log_activity($pdo, $id, 'lead_created', 'Imported from CSV: ' . $businessName);
        $imported++;
    }

    fclose($file);
    json_response(['success' => true, 'imported' => $imported, 'message' => "Imported $imported leads from CSV"]);
}

// ─── AI Company Size Classification ───

function classify_company_sizes($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $businesses = $data['businesses'] ?? [];

    if (empty($businesses)) {
        json_response(['success' => false, 'message' => 'No businesses to classify'], 400);
    }

    // Build a list of businesses with their details for AI classification
    $businessList = [];
    foreach ($businesses as $i => $biz) {
        $entry = ($i + 1) . '. ' . ($biz['business_name'] ?? 'Unknown');
        if (!empty($biz['category'])) $entry .= ' (Category: ' . $biz['category'] . ')';
        if (!empty($biz['address'])) $entry .= ' - ' . $biz['address'];
        if (!empty($biz['website'])) $entry .= ' [' . $biz['website'] . ']';
        $businessList[] = $entry;
    }

    $systemPrompt = "You classify businesses by company size. For each business in the list, determine if it is 'small', 'medium', or 'large' based on available information.

Guidelines:
- Small: Solo operators, freelancers, local mom-and-pop shops, single-location businesses with likely fewer than 20 employees. Most local service businesses (plumbers, landscapers, cleaners, small restaurants, local retail) are small.
- Medium: Businesses with multiple locations, established regional presence, or likely 20-200 employees. Regional chains, mid-size professional firms, established contractors with large teams.
- Large: Major corporations, national/international chains, franchises of well-known brands, businesses with likely 200+ employees.

When in doubt, lean toward 'small' for local businesses found via Google Places search.

Return ONLY a JSON array of size classifications in the same order as the input list.
Example: [\"small\", \"medium\", \"small\", \"large\"]";

    $userPrompt = "Classify these businesses by size:\n\n" . implode("\n", $businessList);

    $result = call_openai($systemPrompt, $userPrompt);

    if (isset($result['error'])) {
        json_response(['success' => false, 'message' => $result['error']], 500);
    }

    $content = trim($result['content']);
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);

    $sizes = json_decode($content, true);

    if (!is_array($sizes)) {
        json_response(['success' => false, 'message' => 'Failed to parse AI classification response'], 500);
    }

    // Validate and normalize sizes
    $validSizes = ['small', 'medium', 'large'];
    $normalized = [];
    foreach ($sizes as $size) {
        $s = strtolower(trim($size));
        $normalized[] = in_array($s, $validSizes) ? $s : 'small';
    }

    json_response(['success' => true, 'sizes' => $normalized]);
}

