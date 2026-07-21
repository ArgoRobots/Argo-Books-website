<?php
require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../email_sender.php';

// Admin auth check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Load shared outreach helpers (scrape_email_from_website, search_businesses_core, call_gemini, etc.)
require_once __DIR__ . '/../../cron/lib/outreach_helpers.php';

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

// Release the session lock now that auth + CSRF have been read. PHP's default
// file session handler holds an EXCLUSIVE lock from session_start() until the
// request ends, so without this a long-running action (discovery can run for
// minutes; shopify_run_dork even sets a 300s time limit) blocks every other
// request carrying the same session cookie at its own session_start() — admin
// pages AND public pages like the landing page would hang until it finished,
// and concurrent discovery runs would serialize instead of overlapping. No
// handler below writes to $_SESSION, so closing it here is safe.
session_write_close();

// Ensure tables exist

// Check if the cron pipeline is currently running
function is_pipeline_running(): bool
{
    $lockFile = __DIR__ . '/../../cron/logs/outreach_pipeline.lock';
    if (!file_exists($lockFile)) {
        return false;
    }
    $fp = @fopen($lockFile, 'r');
    if (!$fp) {
        return false;
    }
    // If we can get the lock, the pipeline is NOT running
    $locked = !flock($fp, LOCK_EX | LOCK_NB);
    fclose($fp);
    return $locked;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Block actions that conflict with a running pipeline
$pipelineActions = ['send_email', 'generate_draft', 'import_leads', 'search_businesses', 'regenerate_followup', 'shopify_run_dork', 'shopify_import', 'editorial_run_discovery', 'editorial_import', 'editorial_add_url', 'creator_run_discovery', 'creator_import', 'creator_add_url'];
if (in_array($action, $pipelineActions) && is_pipeline_running()) {
    echo json_encode([
        'success' => false,
        'message' => 'The outreach pipeline cron job is currently running. Please wait a few minutes and try again.'
    ]);
    exit;
}

switch ($action) {
    // Lead CRUD
    case 'get_leads':
        get_leads($pdo);
        break;
    case 'get_lead':
        get_lead($pdo);
        break;
    case 'create_lead_from_website':
        create_lead_from_website($pdo);
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

    // Shopify discovery
    case 'shopify_get_status':
        shopify_get_status($pdo);
        break;
    case 'shopify_run_dork':
        shopify_run_dork($pdo);
        break;
    case 'shopify_import':
        shopify_import($pdo);
        break;

    // Editorial / roundup discovery
    case 'editorial_get_status':
        editorial_get_status($pdo);
        break;
    case 'editorial_run_discovery':
        editorial_run_discovery($pdo);
        break;
    case 'editorial_import':
        editorial_import($pdo);
        break;
    case 'editorial_add_url':
        editorial_add_url($pdo);
        break;

    // Creators / affiliate-partner discovery
    case 'creator_get_status':
        creator_get_status($pdo);
        break;
    case 'creator_run_discovery':
        creator_run_discovery($pdo);
        break;
    case 'creator_import':
        creator_import($pdo);
        break;
    case 'creator_add_url':
        creator_add_url($pdo);
        break;
    case 'creator_set_email':
        creator_set_email($pdo);
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

    // Follow-ups
    case 'get_followups':
        get_followups($pdo);
        break;
    case 'approve_followup':
        approve_followup($pdo);
        break;
    case 'regenerate_followup':
        regenerate_followup($pdo);
        break;
    case 'skip_followup':
        skip_followup($pdo);
        break;
    case 'halt_followup_sequence':
        halt_followup_sequence($pdo);
        break;
    case 'bulk_approve_followups':
        bulk_approve_followups($pdo);
        break;
    case 'bulk_skip_followups':
        bulk_skip_followups($pdo);
        break;
    case 'bulk_halt_followups':
        bulk_halt_followups($pdo);
        break;
    case 'get_followups_for_lead':
        get_followups_for_lead($pdo);
        break;
    case 'save_followup_draft':
        save_followup_draft($pdo);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}


// log_activity() is provided by cron/lib/outreach_helpers.php

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
    $source = $_GET['source'] ?? '';
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'date_added_desc';

    $where = [];
    $params = [];

    if ($status) {
        $where[] = 'ol.status = ?';
        $params[] = $status;
    }
    if ($response_status) {
        $where[] = 'ol.response_status = ?';
        $params[] = $response_status;
    }
    if ($company_size) {
        $where[] = 'ol.company_size = ?';
        $params[] = $company_size;
    }
    // Source filter groups UI + cron variants together (google_places and
    // google_places_auto both match "google_places"; shopify_auto matches
    // "shopify") so admins don't have to think about the channel split.
    if ($source) {
        if ($source === 'google_places') {
            $where[] = "ol.source IN ('google_places', 'google_places_auto')";
        } elseif ($source === 'shopify') {
            $where[] = "ol.source IN ('shopify', 'shopify_auto')";
        } else {
            $where[] = 'ol.source = ?';
            $params[] = $source;
        }
    } else {
        // Editorial and Creator leads have their own channel tabs (their source
        // is requested explicitly there). Keep them out of the default Email
        // leads list so the channels stay organized and separate.
        $where[] = "ol.source NOT IN ('editorial_auto', 'creator_auto')";
    }
    if ($search) {
        $where[] = '(ol.business_name LIKE ? OR ol.email LIKE ? OR ol.contact_name LIKE ? OR ol.city LIKE ? OR ol.category LIKE ?)';
        $s = "%$search%";
        $params = array_merge($params, [$s, $s, $s, $s, $s]);
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $orderMap = [
        'date_added_desc' => 'ol.date_added DESC',
        'date_added_asc' => 'ol.date_added ASC',
        'last_contact_desc' => 'ol.last_contact_date DESC',
        'business_name_asc' => 'ol.business_name ASC',
        'status_asc' => 'ol.status ASC',
    ];
    $orderBy = $orderMap[$sort] ?? 'ol.date_added DESC';

    // Match the current source code ("outreach-42") plus any legacy variant-tagged
    // codes from before A/B was removed ("outreach-42-v7") so old clicks still count.
    $stmt = $pdo->prepare("SELECT ol.*, MIN(rv.visited_at) AS clicked_at FROM outreach_leads ol LEFT JOIN referral_visits rv ON (rv.source_code = CONCAT('outreach-', ol.id) OR rv.source_code LIKE CONCAT('outreach-', ol.id, '-v%')) $whereClause GROUP BY ol.id ORDER BY $orderBy");
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

/**
 * Create a lead from just a website URL: fetch the site, auto-fill business
 * name / email / phone / category / city / summary via enrich_lead_from_website,
 * then insert. Backs the slimmed-down "Add Lead" modal (one website field).
 */
function create_lead_from_website($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $website = trim((string) ($data['website'] ?? ''));

    if ($website === '') {
        json_response(['success' => false, 'message' => 'Website is required'], 400);
    }
    if (!preg_match('#^https?://#i', $website)) {
        $website = 'https://' . $website;
    }
    if (!filter_var($website, FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => "That doesn't look like a valid website URL"], 400);
    }

    // Enrichment fetches the site + makes one Gemini call, so give it room.
    @set_time_limit(60);
    $enriched = enrich_lead_from_website($website);

    $finalWebsite = $enriched['website'] ?: $website;
    $email = $enriched['email'] ?: null;

    // Dedup by website (and email when we found one), like the other channels.
    if ($email) {
        $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? OR email = ? LIMIT 1");
        $check->execute([$finalWebsite, $email]);
    } else {
        $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? LIMIT 1");
        $check->execute([$finalWebsite]);
    }
    if ($existing = $check->fetchColumn()) {
        json_response([
            'success'          => false,
            'message'          => 'A lead with this website or email already exists',
            'existing_lead_id' => (int) $existing,
        ], 409);
    }

    $stmt = $pdo->prepare("INSERT INTO outreach_leads
        (business_name, email, phone, website, address, category, city, source, status, business_summary)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'manual', 'new', ?)");
    $stmt->execute([
        $enriched['business_name'] ?: $finalWebsite,
        $email,
        $enriched['phone'] ?: null,
        $finalWebsite,
        $enriched['address'] ?: null,
        $enriched['category'] ?: null,
        $enriched['city'] ?: null,
        $enriched['business_summary'] ?: null,
    ]);

    $id = (int) $pdo->lastInsertId();
    log_activity($pdo, $id, 'lead_created', 'Lead auto-added from website: ' . $finalWebsite);

    json_response([
        'success'  => true,
        'id'       => $id,
        'enriched' => $enriched,
        'message'  => 'Lead added: ' . ($enriched['business_name'] ?: $finalWebsite),
    ]);
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

    // Count distinct leads clicked. SUBSTRING_INDEX collapses any legacy
    // variant-tagged code "outreach-42-v7" → "outreach-42" so a lead counts once.
    $rows['clicked'] = $pdo->query("SELECT COUNT(DISTINCT SUBSTRING_INDEX(rv.source_code, '-v', 1)) FROM referral_visits rv WHERE rv.source_code LIKE 'outreach-%'")->fetchColumn();

    json_response([
        'success' => true,
        'stats' => $rows,
        'pipeline_running' => is_pipeline_running(),
    ]);
}

// ─── Business Discovery (Google Places API) ───
// Core logic lives in cron/lib/outreach_helpers.php (scrape_email_from_website, search_businesses_core)

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

    $result = search_businesses_core($city, $province, $category, $limit, $apiKey, $excludePlaceIds);

    if (isset($result['error'])) {
        json_response(['success' => false, 'message' => $result['error']], 502);
    }

    $businesses = $result['businesses'];
    $response = ['success' => true, 'businesses' => $businesses, 'count' => $result['count'], 'rounds' => $result['rounds']];
    if ($result['count'] < $limit) {
        $response['note'] = "Found {$result['count']} of $limit requested after searching {$result['rounds']} round(s). Only businesses with a website and scrapeable email are included.";
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

            // Deduplicate by email to avoid emailing same address twice
            if (!empty($biz['email'])) {
                $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE email = ?");
                $check->execute([$biz['email']]);
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

// ─── Shopify Discovery ───
// Helpers (serpapi_query, shopify_canonical_url, evaluate_shopify_candidate) live in cron/lib/shopify_discovery.php

function _shopify_state_get($pdo, string $key, string $default = ''): string
{
    $stmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = ?");
    $stmt->execute([$key]);
    $val = $stmt->fetchColumn();
    return $val === false ? $default : (string) $val;
}

function _shopify_state_set($pdo, string $key, string $value): void
{
    $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value)
        VALUES (?, ?) ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
        ->execute([$key, $value]);
}

function _shopify_reset_daily_counters_if_needed($pdo): void
{
    $today = date('Y-m-d');
    if (_shopify_state_get($pdo, 'shopify_last_reset_date', '') !== $today) {
        _shopify_state_set($pdo, 'serpapi_calls_today', '0');
        _shopify_state_set($pdo, 'shopify_imports_today', '0');
        _shopify_state_set($pdo, 'shopify_last_reset_date', $today);
    }
}

function shopify_get_status($pdo)
{
    _shopify_reset_daily_counters_if_needed($pdo);
    json_response([
        'success'             => true,
        'enabled'             => ($_ENV['OUTREACH_SHOPIFY_ENABLED'] ?? 'false') === 'true',
        'has_key'             => !empty($_ENV['SERPAPI_KEY']),
        'serpapi_calls_today' => (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0'),
        'serpapi_limit'       => (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3),
        'imports_today'       => (int) _shopify_state_get($pdo, 'shopify_imports_today', '0'),
        'imports_limit'       => (int) ($_ENV['OUTREACH_DAILY_SHOPIFY_DISCOVERY_LIMIT'] ?? 5),
    ]);
}

function shopify_run_dork($pdo)
{
    require_once __DIR__ . '/../../cron/lib/shopify_discovery.php';

    // Each evaluator call does multiple HTTP requests (storefront cURL,
    // /products.json, contact-page email scrape), so a multi-round run with
    // 10 candidates per dork can take a few minutes. Bump the time limit so
    // the request can finish; the daily SerpAPI quota provides the upper
    // bound on how many rounds can stack up.
    @set_time_limit(300);

    $data  = json_decode(file_get_contents('php://input'), true) ?: [];
    $limit = min(50, max(1, (int) ($data['limit'] ?? 10)));

    $apiKey = $_ENV['SERPAPI_KEY'] ?? '';
    if ($apiKey === '') {
        json_response(['success' => false, 'message' => 'SerpAPI key not configured. Set SERPAPI_KEY in .env'], 500);
    }

    _shopify_reset_daily_counters_if_needed($pdo);
    $serpapiLimit = (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3);
    $callsToday   = (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0');
    if ($callsToday >= $serpapiLimit) {
        json_response([
            'success' => false,
            'message' => "SerpAPI daily limit reached ($callsToday/$serpapiLimit). Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.",
        ], 429);
    }

    // Auto-rotate through SHOPIFY_DORK_POOL until we have $limit fits or the
    // daily SerpAPI quota is exhausted. Cursor is shared with the cron via
    // shopify_dork_cursor so consecutive UI clicks (and cron ticks) don't
    // keep hitting the same query.
    $cursor               = (int) _shopify_state_get($pdo, 'shopify_dork_cursor', '0');
    $poolSize             = count(SHOPIFY_DORK_POOL);
    $pages                = json_decode(_shopify_state_get($pdo, 'shopify_dork_pages', '{}'), true);
    if (!is_array($pages)) {
        $pages = [];
    }
    $fits                 = [];
    $rejectedCount        = 0;
    $rejectReasons        = [];
    $alreadyImportedCount = 0;
    $totalEvaluated       = 0;
    $queriesRun           = [];

    // Per-run guardrails (mirror the editorial channel): a SerpAPI-call budget so
    // one Run can't drain the daily quota, a page-depth cap per dork, and a
    // dry-streak exit so we stop once the whole pool stops yielding new stores.
    $serpCallsThisRun = 0;
    $perRunSerpBudget = max(1, (int) ($_ENV['SERPAPI_MAX_CALLS_PER_RUN'] ?? 12));
    $maxPageDepth     = max(1, (int) ($_ENV['SHOPIFY_MAX_PAGE_DEPTH'] ?? 3));
    $pageSize         = 100;
    $maxStart         = ($maxPageDepth - 1) * $pageSize;
    $dryQueries       = 0;
    $stopReason       = 'target_reached';

    // Within-run dedup. The same store surfaces across multiple dork queries
    // (and via different deep links that all canonicalize to one origin), so
    // without these we'd show (and re-evaluate) the same storefront many times.
    //   $seenCanonicals: skip a canonical URL we've already processed this run,
    //                    BEFORE the expensive evaluator call.
    //   $seenIdentities: backstop for the case where two different canonical
    //                    URLs resolve to the same final store + email (e.g. a
    //                    .myshopify.com link and a custom-domain link for the
    //                    same shop). Keyed on final-URL host + harvested email.
    $seenCanonicals = [];
    $seenIdentities = [];

    while (true) {
        if (count($fits) >= $limit)                 { $stopReason = 'target_reached'; break; }
        if ($callsToday >= $serpapiLimit)           { $stopReason = 'daily_quota';    break; }
        if ($serpCallsThisRun >= $perRunSerpBudget) { $stopReason = 'run_budget';     break; }
        if ($dryQueries >= $poolSize)               { $stopReason = 'exhausted';      break; }

        $idx   = $cursor % $poolSize;
        $query = SHOPIFY_DORK_POOL[$idx];
        $cursor++;
        _shopify_state_set($pdo, 'shopify_dork_cursor', (string) $cursor);

        $start = (int) ($pages[(string) $idx] ?? 0);
        if ($start > $maxStart) {
            $dryQueries++;
            continue;
        }

        $queryWithExclusions = $query . SHOPIFY_DORK_EXCLUSIONS;
        $queryResult = serpapi_query_cached($queryWithExclusions, $apiKey, $pageSize, $pdo, $start);
        $serpResults = $queryResult['results'];
        if (!$queryResult['from_cache']) {
            $callsToday++;
            $serpCallsThisRun++;
            _shopify_state_set($pdo, 'serpapi_calls_today', (string) $callsToday);
        }
        $pages[(string) $idx] = $start + $pageSize;
        _shopify_state_set($pdo, 'shopify_dork_pages', json_encode($pages));
        $queriesRun[] = $query . ' (p' . (intdiv($start, $pageSize) + 1) . ')';

        if (empty($serpResults)) {
            $dryQueries++;
            continue;
        }
        $totalEvaluated += count($serpResults);

        $foundNew = false;
        foreach ($serpResults as $r) {
            if (count($fits) >= $limit) break;

            $canonical = shopify_canonical_url($r['link'] ?? '');
            if ($canonical === '') continue;

            // Skip canonicals already handled this run (dup SERP hits / deep
            // links to the same store). Marked seen even if it later rejects,
            // so we never pay to evaluate the same origin twice.
            if (isset($seenCanonicals[$canonical])) continue;
            $seenCanonicals[$canonical] = true;

            // Already-imported check FIRST, before the expensive evaluator
            // call. Uses outreach_shopify_candidates.canonical_url (stable,
            // populated at import time) rather than outreach_leads.website
            // (which holds the post-redirect custom domain and won't match
            // a SerpAPI result that points at .myshopify.com).
            $importedCheck = $pdo->prepare("SELECT 1 FROM outreach_shopify_candidates
                WHERE canonical_url = ? AND status = 'imported' AND lead_id IS NOT NULL LIMIT 1");
            $importedCheck->execute([$canonical]);
            if ($importedCheck->fetchColumn() !== false) {
                $alreadyImportedCount++;
                continue;
            }

            $foundNew = true;
            $result = evaluate_shopify_candidate($canonical);

            if (empty($result['fit'])) {
                $rejectedCount++;
                $reason = $result['reason'] ?? 'unknown';
                $rejectReasons[$reason] = ($rejectReasons[$reason] ?? 0) + 1;
                continue;
            }

            $meta = $result['metadata'] ?? [];
            $finalUrl = $result['final_url'] ?? $canonical;

            // Identity dedup: two different canonical URLs can resolve to the
            // same shop (the Scholar's Choice case: many .myshopify.com links
            // all redirect to scholarschoice.ca with the same email). Key on
            // final-URL host + email so we only surface the store once.
            $identityHost = parse_url($finalUrl, PHP_URL_HOST) ?: $canonical;
            $identityHost = strtolower(preg_replace('/^www\./', '', (string) $identityHost));
            $identityKey  = $identityHost . '|' . strtolower(trim((string) ($meta['email'] ?? '')));
            if (isset($seenIdentities[$identityKey])) continue;
            $seenIdentities[$identityKey] = true;

            $fits[] = [
                'canonical_url'    => $canonical,
                'serp_title'       => $r['title'] ?? '',
                'fit'              => true,
                'final_url'        => $finalUrl,
                'business_name'    => $meta['business_name'] ?? '',
                'email'            => $meta['email'] ?? '',
                'products_count'   => $meta['products_count'] ?? null,
                'first_product_at' => $meta['first_product_created_at'] ?? null,
                'country'          => $meta['country'] ?? '',
            ];
        }

        if ($foundNew) {
            $dryQueries = 0;
        } else {
            $dryQueries++;
        }
    }

    $quotaExhausted = $callsToday >= $serpapiLimit && count($fits) < $limit;

    json_response([
        'success'                => true,
        'results'                => $fits,
        'requested_limit'        => $limit,
        'found_count'            => count($fits),
        'stop_reason'            => $stopReason,
        'quota_exhausted'        => $quotaExhausted,
        'rejected_count'         => $rejectedCount,
        'reject_reasons'         => $rejectReasons,
        'already_imported_count' => $alreadyImportedCount,
        'total_evaluated'        => $totalEvaluated,
        'queries_run'            => $queriesRun,
        'serpapi_calls_this_run' => $serpCallsThisRun,
        'serpapi_calls_today'    => $callsToday,
        'serpapi_limit'          => $serpapiLimit,
        'imports_today'          => (int) _shopify_state_get($pdo, 'shopify_imports_today', '0'),
        'imports_limit'          => (int) ($_ENV['OUTREACH_DAILY_SHOPIFY_DISCOVERY_LIMIT'] ?? 5),
    ]);
}

function shopify_import($pdo)
{
    require_once __DIR__ . '/../../cron/lib/shopify_discovery.php';

    $data         = json_decode(file_get_contents('php://input'), true) ?: [];
    $canonicalUrl = trim($data['canonical_url'] ?? '');

    if ($canonicalUrl === '') {
        json_response(['success' => false, 'message' => 'canonical_url is required'], 400);
    }

    // No daily-imports cap on the UI path: the operator clicking Import is
    // making a deliberate choice and shouldn't be rationed against the cron's
    // automation throttle. The cron still self-rations via shopify_imports_today
    // (OUTREACH_DAILY_SHOPIFY_DISCOVERY_LIMIT), which the UI deliberately does
    // not touch.

    // Re-evaluate server-side, don't trust client-passed metadata
    $result = evaluate_shopify_candidate($canonicalUrl);
    if (empty($result['fit'])) {
        json_response([
            'success' => false,
            'message' => 'Store no longer passes evaluation: ' . ($result['reason'] ?? 'unknown'),
            'reason'  => $result['reason'] ?? '',
            'detail'  => $result['detail'] ?? '',
        ], 400);
    }

    $meta         = $result['metadata'];
    $finalUrl     = $result['final_url'];
    $email        = $meta['email'] ?? '';
    $businessName = $meta['business_name'] ?? '';

    if ($email === '') {
        json_response(['success' => false, 'message' => 'Re-evaluation returned fit but no email, refusing to import'], 500);
    }

    // Dedup against existing leads (email OR website)
    $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE email = ? OR website = ? LIMIT 1");
    $check->execute([$email, $finalUrl]);
    if ($existing = $check->fetchColumn()) {
        json_response([
            'success'          => false,
            'message'          => 'A lead with this email or website already exists',
            'existing_lead_id' => (int) $existing,
        ], 409);
    }

    try {
        $businessSummary = sprintf(
            "Shopify store. %s products. First product created %s.",
            $meta['products_count'] ?? '?',
            $meta['first_product_created_at'] ?? 'unknown date'
        );
        if (!empty($meta['featured_product'])) {
            $businessSummary .= sprintf(' Recently added product: "%s".', $meta['featured_product']);
        }

        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, email, website, source, contact_page_url, business_summary, country)
            VALUES (?, ?, ?, 'shopify_auto', ?, ?, ?)");
        $stmt->execute([
            $businessName ?: $canonicalUrl,
            $email,
            $finalUrl,
            $finalUrl,
            $businessSummary,
            $meta['country'] ?? 'CA',
        ]);
        $leadId = (int) $pdo->lastInsertId();

        // UPSERT the candidate row so the cron's INSERT IGNORE skips it next time
        $stmt = $pdo->prepare("INSERT INTO outreach_shopify_candidates
            (canonical_url, myshopify_url, status, lead_id, harvested_email,
             products_count, first_product_created_at, detected_country, last_query)
            VALUES (?, ?, 'imported', ?, ?, ?, ?, ?, 'admin-ui')
            ON DUPLICATE KEY UPDATE
                status                   = 'imported',
                lead_id                  = VALUES(lead_id),
                harvested_email          = VALUES(harvested_email),
                products_count           = VALUES(products_count),
                first_product_created_at = VALUES(first_product_created_at),
                detected_country         = VALUES(detected_country)");
        $stmt->execute([
            $canonicalUrl,
            $canonicalUrl,
            $leadId,
            $email,
            $meta['products_count'] ?? null,
            $meta['first_product_created_at'] ?? null,
            $meta['country'] ?? null,
        ]);

        log_activity($pdo, $leadId, 'lead_created', 'Imported from Shopify discovery UI');

        // Intentionally does not increment shopify_imports_today, since that
        // counter belongs to the cron's automation throttle and shouldn't
        // be consumed by deliberate user-driven imports.

        json_response([
            'success' => true,
            'lead_id' => $leadId,
        ]);
    } catch (Exception $e) {
        outreach_log("Shopify import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

// ─── Editorial / roundup discovery ───
// Finds "best free accounting software" / "QuickBooks alternatives" listicles
// and their authors, so we can pitch getting Argo Books added. Mirrors the
// Shopify discovery handlers. Shares the SerpAPI daily budget with Shopify.

function editorial_get_status($pdo)
{
    _shopify_reset_daily_counters_if_needed($pdo);
    json_response([
        'success'             => true,
        'enabled'             => ($_ENV['OUTREACH_EDITORIAL_ENABLED'] ?? 'false') === 'true',
        'has_key'             => !empty($_ENV['SERPAPI_KEY']),
        'has_hunter'          => !empty($_ENV['HUNTER_API_KEY']),
        'serpapi_calls_today' => (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0'),
        'serpapi_limit'       => (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3),
    ]);
}

function editorial_run_discovery($pdo)
{
    require_once __DIR__ . '/../../cron/lib/editorial_discovery.php';

    // Each candidate does a page fetch + a Gemini extraction + a Hunter/scrape
    // lookup, so a run can take a few minutes. The daily SerpAPI quota bounds it.
    @set_time_limit(300);

    $data  = json_decode(file_get_contents('php://input'), true) ?: [];
    $limit = min(30, max(1, (int) ($data['limit'] ?? 8)));

    $apiKey = $_ENV['SERPAPI_KEY'] ?? '';
    if ($apiKey === '') {
        json_response(['success' => false, 'message' => 'SerpAPI key not configured. Set SERPAPI_KEY in .env'], 500);
    }

    _shopify_reset_daily_counters_if_needed($pdo);
    $serpapiLimit = (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3);
    $callsToday   = (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0');
    if ($callsToday >= $serpapiLimit) {
        json_response([
            'success' => false,
            'message' => "SerpAPI daily limit reached ($callsToday/$serpapiLimit). Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.",
        ], 429);
    }

    $cursor               = (int) _shopify_state_get($pdo, 'editorial_query_cursor', '0');
    $poolSize             = count(EDITORIAL_QUERY_POOL);
    $pages                = json_decode(_shopify_state_get($pdo, 'editorial_pages', '{}'), true);
    if (!is_array($pages)) {
        $pages = [];
    }

    $fits                 = [];
    $rejectedCount        = 0;
    $rejectReasons        = [];
    $alreadyImportedCount = 0;
    $alreadyRejectedCount = 0;
    $totalEvaluated       = 0;
    $queriesRun           = [];
    $seen                 = [];

    // Target-count loop: keep pulling deeper result pages and rotating queries
    // until we've collected $limit NEW fits, bounded by guardrails so a run can't
    // time out or run up the SerpAPI/AI bill. Everything that makes this resumable
    // is persisted (query cursor + per-query page offset), so clicking Run again
    // continues from where this left off instead of re-scanning page 1.
    $evaluated        = 0;
    $serpCallsThisRun = 0;
    $maxEval          = max(1, (int) ($_ENV['EDITORIAL_MAX_EVAL_PER_RUN'] ?? 20));   // Gemini/scrape ceiling per run
    $perRunSerpBudget = max(1, (int) ($_ENV['SERPAPI_MAX_CALLS_PER_RUN'] ?? 12));    // SerpAPI calls per run
    $maxPageDepth     = max(1, (int) ($_ENV['EDITORIAL_MAX_PAGE_DEPTH'] ?? 6));      // how deep per query before it's "tapped out"
    $pageSize         = 15;
    $maxStart         = ($maxPageDepth - 1) * $pageSize;
    $deadline         = microtime(true) + 50;
    $dryQueries       = 0;   // consecutive queries that yielded no NEW candidate
    $stopReason       = 'target_reached';

    while (true) {
        if (count($fits) >= $limit)                 { $stopReason = 'target_reached'; break; }
        if ($callsToday >= $serpapiLimit)           { $stopReason = 'daily_quota';    break; }
        if ($serpCallsThisRun >= $perRunSerpBudget) { $stopReason = 'run_budget';     break; }
        if ($evaluated >= $maxEval)                 { $stopReason = 'eval_cap';       break; }
        if (microtime(true) >= $deadline)           { $stopReason = 'time';           break; }
        if ($dryQueries >= $poolSize)               { $stopReason = 'exhausted';      break; }

        $idx   = $cursor % $poolSize;
        $query = EDITORIAL_QUERY_POOL[$idx];
        $cursor++;
        _shopify_state_set($pdo, 'editorial_query_cursor', (string) $cursor);

        // Key the page offset by the query text (not its index) so editing the
        // query pool never misaligns cursors: new queries start at page 1, and an
        // existing query keeps its own depth regardless of where it sits now.
        $pageKey = substr(hash('sha1', $query), 0, 12);
        $start = (int) ($pages[$pageKey] ?? 0);
        if ($start > $maxStart) {
            // This query is tapped out to our page-depth cap; skip without a call.
            $dryQueries++;
            continue;
        }

        $searchResult = editorial_search($query, $apiKey, $pageSize, $pdo, $start);
        if (!$searchResult['from_cache']) {
            $callsToday++;
            $serpCallsThisRun++;
            _shopify_state_set($pdo, 'serpapi_calls_today', (string) $callsToday);
        }
        // Advance this query's page offset so the next iteration/run goes deeper.
        $pages[$pageKey] = $start + $pageSize;
        _shopify_state_set($pdo, 'editorial_pages', json_encode($pages));
        $queriesRun[] = $query . ' (p' . (intdiv($start, $pageSize) + 1) . ')';

        $serpResults = $searchResult['results'];
        if (empty($serpResults)) {
            $dryQueries++;
            continue;
        }
        $totalEvaluated += count($serpResults);

        $foundNew = false;
        foreach ($serpResults as $r) {
            if (count($fits) >= $limit || $evaluated >= $maxEval || microtime(true) >= $deadline) {
                break;
            }

            $canonical = $r['url'];
            if (isset($seen[$canonical])) continue;
            $seen[$canonical] = true;

            // Pre-dedup BEFORE spending any AI: skip already-imported or recently
            // rejected articles so we never pay Gemini/Hunter to re-evaluate a
            // candidate we've already handled.
            $existing = $pdo->prepare("SELECT status FROM outreach_editorial_candidates
                WHERE canonical_url = ?
                  AND (status = 'imported'
                       OR (status = 'rejected' AND checked_at > DATE_SUB(NOW(), INTERVAL 14 DAY)))
                LIMIT 1");
            $existing->execute([$canonical]);
            $existingStatus = $existing->fetchColumn();
            if ($existingStatus !== false) {
                if ($existingStatus === 'imported') {
                    $alreadyImportedCount++;
                } else {
                    $alreadyRejectedCount++;
                }
                continue;
            }

            $foundNew = true;
            $evaluated++;
            try {
                $result = evaluate_editorial_candidate($canonical, $pdo, $r['title'] ?? '');
            } catch (Throwable $e) {
                outreach_log("Editorial evaluate error for $canonical: " . $e->getMessage());
                $result = ['fit' => false, 'reason' => 'eval_error', 'detail' => $e->getMessage()];
            }

            if (empty($result['fit'])) {
                $rejectedCount++;
                $reason = $result['reason'] ?? 'unknown';
                $rejectReasons[$reason] = ($rejectReasons[$reason] ?? 0) + 1;
                // Persist the rejection so future runs skip this article.
                $up = $pdo->prepare("INSERT INTO outreach_editorial_candidates
                    (canonical_url, status, reject_reason, reject_detail, last_query)
                    VALUES (?, 'rejected', ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = 'rejected',
                        reject_reason = VALUES(reject_reason),
                        reject_detail = VALUES(reject_detail),
                        checked_at = NOW()");
                $up->execute([
                    $canonical,
                    mb_substr($reason, 0, 100),
                    mb_substr((string) ($result['detail'] ?? ''), 0, 500),
                    mb_substr($query, 0, 255),
                ]);
                continue;
            }

            $meta = $result['metadata'] ?? [];
            $fits[] = [
                'canonical_url'    => $canonical,
                'serp_title'       => $r['title'] ?? '',
                'outlet_name'      => $meta['outlet_name'] ?? '',
                'author_name'      => $meta['author_name'] ?? '',
                'email'            => $meta['email'] ?? '',
                'email_source'     => $meta['email_source'] ?? '',
                'listed_tools'     => implode(', ', array_slice($meta['listed_tools'] ?? [], 0, 8)),
                'business_summary' => $meta['business_summary'] ?? '',
            ];
        }

        // A query that surfaced no new candidate counts toward exhaustion; one
        // that did resets the dry streak so we keep mining productive queries.
        if ($foundNew) {
            $dryQueries = 0;
        } else {
            $dryQueries++;
        }
    }

    $quotaExhausted = $callsToday >= $serpapiLimit && count($fits) < $limit;

    json_response([
        'success'                => true,
        'results'                => $fits,
        'requested_limit'        => $limit,
        'found_count'            => count($fits),
        'stop_reason'            => $stopReason,
        'quota_exhausted'        => $quotaExhausted,
        'rejected_count'         => $rejectedCount,
        'reject_reasons'         => $rejectReasons,
        'already_imported_count' => $alreadyImportedCount,
        'already_rejected_count' => $alreadyRejectedCount,
        'total_evaluated'        => $totalEvaluated,
        'queries_run'            => $queriesRun,
        'serpapi_calls_this_run' => $serpCallsThisRun,
        'serpapi_calls_today'    => $callsToday,
        'serpapi_limit'          => $serpapiLimit,
    ]);
}

function editorial_add_url($pdo)
{
    require_once __DIR__ . '/../../cron/lib/editorial_discovery.php';
    @set_time_limit(120);

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $url  = trim($data['url'] ?? '');
    if ($url !== '' && !preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => 'A valid article URL is required'], 400);
    }

    $canonical = editorial_canonical_url($url);

    $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? LIMIT 1");
    $check->execute([$canonical]);
    if ($existing = $check->fetchColumn()) {
        json_response(['success' => false, 'message' => 'This article is already a lead', 'existing_lead_id' => (int) $existing], 409);
    }

    // Best-effort research in forced mode: a URL the operator added by hand gets
    // imported even if it is blocked, borderline, or already mentions Argo. We
    // keep whatever the evaluator extracts (outlet, author, listed tools, email,
    // summary); a missing email is fine, the operator can fill it in on the lead.
    $meta = [];
    try {
        $result = evaluate_editorial_candidate($canonical, $pdo, '', true);
        $meta = $result['metadata'] ?? [];
    } catch (Throwable $e) {
        outreach_log("Editorial add-url evaluate error for $canonical: " . $e->getMessage());
    }

    $domain  = editorial_domain_from_url($canonical);
    $outlet  = trim((string) ($meta['outlet_name'] ?? '')) ?: ucfirst($domain);
    $author  = trim((string) ($meta['author_name'] ?? ''));
    $email   = trim((string) ($meta['email'] ?? ''));
    $summary = trim((string) ($meta['business_summary'] ?? ''));
    if ($summary === '') {
        $summary = "Roundup/listicle to pitch: {$canonical}. Goal: get Argo Books added to the list.";
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, website, source, contact_page_url, business_summary, category, country)
            VALUES (?, ?, ?, ?, 'editorial_auto', ?, ?, 'editorial', 'US')");
        $stmt->execute([
            $outlet,
            $author !== '' ? $author : null,
            $email !== '' ? $email : null,
            $canonical,
            $canonical,
            $summary,
        ]);
        $leadId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO outreach_editorial_candidates
            (canonical_url, status, lead_id, outlet_name, author_name, harvested_email, last_query)
            VALUES (?, 'imported', ?, ?, ?, ?, 'admin-url')
            ON DUPLICATE KEY UPDATE status = 'imported', lead_id = VALUES(lead_id),
                outlet_name = VALUES(outlet_name), author_name = VALUES(author_name),
                harvested_email = VALUES(harvested_email)");
        $stmt->execute([$canonical, $leadId, $outlet, $author, $email]);

        log_activity($pdo, $leadId, 'lead_created', 'Added by URL to the Editorial channel');
        json_response([
            'success'   => true,
            'lead_id'   => $leadId,
            'outlet'    => $outlet,
            'author'    => $author,
            'email'     => $email,
            'has_email' => $email !== '',
        ]);
    } catch (Exception $e) {
        outreach_log("Editorial add-url import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function editorial_import($pdo)
{
    $data         = json_decode(file_get_contents('php://input'), true) ?: [];
    $canonicalUrl = trim($data['canonical_url'] ?? '');
    $email        = trim($data['email'] ?? '');
    $outlet       = trim($data['outlet_name'] ?? '');
    $author       = trim($data['author_name'] ?? '');
    $summary      = trim($data['business_summary'] ?? '');
    $articleUrl   = $canonicalUrl;

    // Import from the data the discovery run already computed, rather than
    // re-evaluating. The editorial evaluator does a live page fetch plus a
    // non-deterministic Gemini extraction, so re-running it here is slow and
    // flaky (a borderline page can flip its roundup verdict on the second pass,
    // which was causing spurious import failures). This is an authenticated
    // admin action reviewing results it just generated, so trusting them is the
    // right tradeoff; we still validate the email and dedup against existing leads.
    if ($canonicalUrl === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'canonical_url and a valid email are required'], 400);
    }
    if ($summary === '') {
        $summary = 'Roundup article' . ($outlet !== '' ? " on {$outlet}" : '') . '. Goal: get Argo Books added to the list.';
    }

    $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE email = ? OR website = ? LIMIT 1");
    $check->execute([$email, $articleUrl]);
    if ($existing = $check->fetchColumn()) {
        json_response([
            'success'          => false,
            'message'          => 'A lead with this email or article already exists',
            'existing_lead_id' => (int) $existing,
        ], 409);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, website, source, contact_page_url, business_summary, category, country)
            VALUES (?, ?, ?, ?, 'editorial_auto', ?, ?, 'editorial', 'US')");
        $stmt->execute([
            $outlet ?: $canonicalUrl,
            $author !== '' ? $author : null,
            $email,
            $articleUrl,
            $articleUrl,
            $summary,
        ]);
        $leadId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO outreach_editorial_candidates
            (canonical_url, status, lead_id, outlet_name, author_name, harvested_email, last_query)
            VALUES (?, 'imported', ?, ?, ?, ?, 'admin-ui')
            ON DUPLICATE KEY UPDATE
                status          = 'imported',
                lead_id         = VALUES(lead_id),
                outlet_name     = VALUES(outlet_name),
                author_name     = VALUES(author_name),
                harvested_email = VALUES(harvested_email)");
        $stmt->execute([$canonicalUrl, $leadId, $outlet, $author, $email]);

        log_activity($pdo, $leadId, 'lead_created', 'Imported from Editorial discovery UI');
        json_response(['success' => true, 'lead_id' => $leadId]);
    } catch (Exception $e) {
        outreach_log("Editorial import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

// ─── Creators / affiliate-partner discovery ───
// Finds YouTubers, newsletter writers, and niche bloggers whose audience is small
// businesses / freelancers, so we can recruit them as affiliate partners. Mirrors
// the editorial handlers (paginated, resumable target-count loop) but with the
// affiliate pitch. Shares the SerpAPI daily budget with the other channels.

function creator_get_status($pdo)
{
    _shopify_reset_daily_counters_if_needed($pdo);
    json_response([
        'success'             => true,
        'enabled'             => ($_ENV['OUTREACH_CREATOR_ENABLED'] ?? 'false') === 'true',
        'has_key'             => !empty($_ENV['SERPAPI_KEY']),
        'has_hunter'          => !empty($_ENV['HUNTER_API_KEY']),
        'serpapi_calls_today' => (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0'),
        'serpapi_limit'       => (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3),
    ]);
}

function creator_run_discovery($pdo)
{
    require_once __DIR__ . '/../../cron/lib/creator_discovery.php';
    @set_time_limit(300);

    $data  = json_decode(file_get_contents('php://input'), true) ?: [];
    $limit = min(30, max(1, (int) ($data['limit'] ?? 8)));

    $apiKey = $_ENV['SERPAPI_KEY'] ?? '';
    if ($apiKey === '') {
        json_response(['success' => false, 'message' => 'SerpAPI key not configured. Set SERPAPI_KEY in .env'], 500);
    }

    _shopify_reset_daily_counters_if_needed($pdo);
    $serpapiLimit = (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3);
    $callsToday   = (int) _shopify_state_get($pdo, 'serpapi_calls_today', '0');
    if ($callsToday >= $serpapiLimit) {
        json_response([
            'success' => false,
            'message' => "SerpAPI daily limit reached ($callsToday/$serpapiLimit). Resets at midnight or raise SERPAPI_DAILY_QUERY_LIMIT in .env.",
        ], 429);
    }

    $cursor               = (int) _shopify_state_get($pdo, 'creator_query_cursor', '0');
    $poolSize             = count(CREATOR_QUERY_POOL);
    $pages                = json_decode(_shopify_state_get($pdo, 'creator_pages', '{}'), true);
    if (!is_array($pages)) {
        $pages = [];
    }

    $fits                 = [];
    $rejectedCount        = 0;
    $rejectReasons        = [];
    $alreadyImportedCount = 0;
    $alreadyRejectedCount = 0;
    $totalEvaluated       = 0;
    $queriesRun           = [];
    $seen                 = [];

    // Resumable target-count loop (same shape as the editorial channel): keep
    // paging deeper and rotating queries until $limit NEW fits, bounded so a run
    // can't time out or run up the SerpAPI/AI bill.
    $evaluated        = 0;
    $serpCallsThisRun = 0;
    $maxEval          = max(1, (int) ($_ENV['CREATOR_MAX_EVAL_PER_RUN'] ?? 20));
    $perRunSerpBudget = max(1, (int) ($_ENV['SERPAPI_MAX_CALLS_PER_RUN'] ?? 12));
    $maxPageDepth     = max(1, (int) ($_ENV['CREATOR_MAX_PAGE_DEPTH'] ?? 6));
    $pageSize         = 15;
    $maxStart         = ($maxPageDepth - 1) * $pageSize;
    $deadline         = microtime(true) + 50;
    $dryQueries       = 0;
    $stopReason       = 'target_reached';

    while (true) {
        if (count($fits) >= $limit)                 { $stopReason = 'target_reached'; break; }
        if ($callsToday >= $serpapiLimit)           { $stopReason = 'daily_quota';    break; }
        if ($serpCallsThisRun >= $perRunSerpBudget) { $stopReason = 'run_budget';     break; }
        if ($evaluated >= $maxEval)                 { $stopReason = 'eval_cap';       break; }
        if (microtime(true) >= $deadline)           { $stopReason = 'time';           break; }
        if ($dryQueries >= $poolSize)               { $stopReason = 'exhausted';      break; }

        $idx   = $cursor % $poolSize;
        $query = CREATOR_QUERY_POOL[$idx];
        $cursor++;
        _shopify_state_set($pdo, 'creator_query_cursor', (string) $cursor);

        $start = (int) ($pages[(string) $idx] ?? 0);
        if ($start > $maxStart) {
            $dryQueries++;
            continue;
        }

        $searchResult = creator_search($query, $apiKey, $pageSize, $pdo, $start);
        if (!$searchResult['from_cache']) {
            $callsToday++;
            $serpCallsThisRun++;
            _shopify_state_set($pdo, 'serpapi_calls_today', (string) $callsToday);
        }
        $pages[(string) $idx] = $start + $pageSize;
        _shopify_state_set($pdo, 'creator_pages', json_encode($pages));
        $queriesRun[] = $query . ' (p' . (intdiv($start, $pageSize) + 1) . ')';

        $serpResults = $searchResult['results'];
        if (empty($serpResults)) {
            $dryQueries++;
            continue;
        }
        $totalEvaluated += count($serpResults);

        $foundNew = false;
        foreach ($serpResults as $r) {
            if (count($fits) >= $limit || $evaluated >= $maxEval || microtime(true) >= $deadline) {
                break;
            }

            $canonical = $r['url'];
            if (isset($seen[$canonical])) continue;
            $seen[$canonical] = true;

            // Pre-dedup before any AI spend.
            $existing = $pdo->prepare("SELECT status FROM outreach_creator_candidates
                WHERE canonical_url = ?
                  AND (status = 'imported'
                       OR (status = 'rejected' AND checked_at > DATE_SUB(NOW(), INTERVAL 14 DAY)))
                LIMIT 1");
            $existing->execute([$canonical]);
            $existingStatus = $existing->fetchColumn();
            if ($existingStatus !== false) {
                if ($existingStatus === 'imported') {
                    $alreadyImportedCount++;
                } else {
                    $alreadyRejectedCount++;
                }
                continue;
            }

            $foundNew = true;
            $evaluated++;
            try {
                $result = evaluate_creator_candidate($canonical, $pdo, $r['title'] ?? '');
            } catch (Throwable $e) {
                outreach_log("Creator evaluate error for $canonical: " . $e->getMessage());
                $result = ['fit' => false, 'reason' => 'eval_error', 'detail' => $e->getMessage()];
            }

            if (empty($result['fit'])) {
                $rejectedCount++;
                $reason = $result['reason'] ?? 'unknown';
                $rejectReasons[$reason] = ($rejectReasons[$reason] ?? 0) + 1;
                $up = $pdo->prepare("INSERT INTO outreach_creator_candidates
                    (canonical_url, status, reject_reason, reject_detail, platform, last_query)
                    VALUES (?, 'rejected', ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = 'rejected',
                        reject_reason = VALUES(reject_reason),
                        reject_detail = VALUES(reject_detail),
                        checked_at = NOW()");
                $up->execute([
                    $canonical,
                    mb_substr($reason, 0, 100),
                    mb_substr((string) ($result['detail'] ?? ''), 0, 500),
                    mb_substr((string) ($result['metadata']['platform'] ?? $r['platform'] ?? ''), 0, 20),
                    mb_substr($query, 0, 255),
                ]);
                continue;
            }

            $meta = $result['metadata'] ?? [];
            $fits[] = [
                'canonical_url'    => $canonical,
                'profile_url'      => !empty($meta['channel_url']) ? $meta['channel_url'] : $canonical,
                'serp_title'       => $r['title'] ?? '',
                'platform'         => $meta['platform'] ?? ($r['platform'] ?? ''),
                'creator_name'     => $meta['creator_name'] ?? '',
                'audience'         => $meta['audience'] ?? '',
                'topics'           => implode(', ', array_slice($meta['topics'] ?? [], 0, 6)),
                'email'            => $meta['email'] ?? '',
                'email_source'     => $meta['email_source'] ?? '',
                'needs_manual_email' => !empty($meta['needs_manual_email']),
                'business_summary' => $meta['business_summary'] ?? '',
            ];
        }

        if ($foundNew) {
            $dryQueries = 0;
        } else {
            $dryQueries++;
        }
    }

    $quotaExhausted = $callsToday >= $serpapiLimit && count($fits) < $limit;

    json_response([
        'success'                => true,
        'results'                => $fits,
        'requested_limit'        => $limit,
        'found_count'            => count($fits),
        'stop_reason'            => $stopReason,
        'quota_exhausted'        => $quotaExhausted,
        'rejected_count'         => $rejectedCount,
        'reject_reasons'         => $rejectReasons,
        'already_imported_count' => $alreadyImportedCount,
        'already_rejected_count' => $alreadyRejectedCount,
        'total_evaluated'        => $totalEvaluated,
        'queries_run'            => $queriesRun,
        'serpapi_calls_this_run' => $serpCallsThisRun,
        'serpapi_calls_today'    => $callsToday,
        'serpapi_limit'          => $serpapiLimit,
    ]);
}

function creator_add_url($pdo)
{
    require_once __DIR__ . '/../../cron/lib/creator_discovery.php';
    @set_time_limit(120);

    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $url  = trim($data['url'] ?? '');
    if ($url !== '' && !preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => 'A valid creator URL is required'], 400);
    }

    $canonical = creator_canonical_url($url);

    $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? LIMIT 1");
    $check->execute([$canonical]);
    if ($existing = $check->fetchColumn()) {
        json_response(['success' => false, 'message' => 'This creator is already a lead', 'existing_lead_id' => (int) $existing], 409);
    }

    // Forced evaluate: a hand-added URL is imported even if borderline or with no
    // email found (common for YouTube). The operator can fill in the email later.
    $meta = [];
    try {
        $result = evaluate_creator_candidate($canonical, $pdo, '', true);
        $meta = $result['metadata'] ?? [];
    } catch (Throwable $e) {
        outreach_log("Creator add-url evaluate error for $canonical: " . $e->getMessage());
    }

    $platform = trim((string) ($meta['platform'] ?? creator_platform_from_url($canonical)));
    $name     = trim((string) ($meta['creator_name'] ?? '')) ?: creator_name_from_url($canonical);
    $email    = trim((string) ($meta['email'] ?? ''));
    $summary  = trim((string) ($meta['business_summary'] ?? ''));
    if ($summary === '') {
        $summary = "Creator to recruit as an affiliate: {$canonical} (platform: {$platform}). Goal: recruit as an Argo Books affiliate partner.";
    }
    // Prefer the resolved channel/profile URL as the lead website (its About page
    // is where the email lives), otherwise the URL as given.
    $channelUrl = trim((string) ($meta['channel_url'] ?? ''));
    $website = ($channelUrl !== '' && filter_var($channelUrl, FILTER_VALIDATE_URL)) ? $channelUrl : $canonical;
    if ($website !== $canonical) {
        $dupe = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? LIMIT 1");
        $dupe->execute([$website]);
        if ($existing = $dupe->fetchColumn()) {
            json_response(['success' => false, 'message' => 'This creator is already a lead', 'existing_lead_id' => (int) $existing], 409);
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, website, source, contact_page_url, business_summary, category, country)
            VALUES (?, ?, ?, ?, 'creator_auto', ?, ?, ?, 'US')");
        $stmt->execute([
            $name,
            $name !== '' ? $name : null,
            $email !== '' ? $email : null,
            $website,
            $website,
            $summary,
            'creator:' . $platform,
        ]);
        $leadId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO outreach_creator_candidates
            (canonical_url, status, lead_id, platform, creator_name, harvested_email, last_query)
            VALUES (?, 'imported', ?, ?, ?, ?, 'admin-url')
            ON DUPLICATE KEY UPDATE status = 'imported', lead_id = VALUES(lead_id),
                platform = VALUES(platform), creator_name = VALUES(creator_name),
                harvested_email = VALUES(harvested_email)");
        $stmt->execute([$canonical, $leadId, $platform, $name, $email]);

        log_activity($pdo, $leadId, 'lead_created', 'Added by URL to the Creators channel');
        json_response([
            'success'   => true,
            'lead_id'   => $leadId,
            'platform'  => $platform,
            'name'      => $name,
            'email'     => $email,
            'has_email' => $email !== '',
        ]);
    } catch (Exception $e) {
        outreach_log("Creator add-url import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function creator_import($pdo)
{
    $data         = json_decode(file_get_contents('php://input'), true) ?: [];
    $canonicalUrl = trim($data['canonical_url'] ?? '');
    $profileUrl   = trim($data['profile_url'] ?? '');
    $email        = trim($data['email'] ?? '');
    $platform     = trim($data['platform'] ?? '');
    $name         = trim($data['creator_name'] ?? '');
    $summary      = trim($data['business_summary'] ?? '');

    // Import from the discovery-computed data (no re-evaluate). Unlike editorial,
    // an email is NOT required: many creators (especially YouTubers) have no
    // auto-harvestable email and are worked from the lead's Get email button.
    if ($canonicalUrl === '') {
        json_response(['success' => false, 'message' => 'canonical_url is required'], 400);
    }
    // The lead's website is the channel/profile when we resolved one (its About
    // page is where the email lives), otherwise the original canonical URL.
    $website = ($profileUrl !== '' && filter_var($profileUrl, FILTER_VALIDATE_URL)) ? $profileUrl : $canonicalUrl;
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['success' => false, 'message' => 'The provided email is not valid'], 400);
    }
    require_once __DIR__ . '/../../cron/lib/creator_discovery.php';
    if ($platform === '') {
        $platform = creator_platform_from_url($canonicalUrl);
    }
    if ($name === '') {
        $name = creator_name_from_url($canonicalUrl);
    }
    if ($summary === '') {
        $summary = 'Creator to recruit as an affiliate' . ($platform !== '' ? " (platform: {$platform})" : '') . '. Goal: recruit as an Argo Books affiliate partner.';
    }

    // Dedup by the lead website (channel/profile) always, and by email only when
    // one was provided (empty email must not collide with other email-less leads).
    if ($email !== '') {
        $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? OR email = ? LIMIT 1");
        $check->execute([$website, $email]);
    } else {
        $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE website = ? LIMIT 1");
        $check->execute([$website]);
    }
    if ($existing = $check->fetchColumn()) {
        json_response([
            'success'          => false,
            'message'          => 'A lead with this creator or email already exists',
            'existing_lead_id' => (int) $existing,
        ], 409);
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, website, source, contact_page_url, business_summary, category, country)
            VALUES (?, ?, ?, ?, 'creator_auto', ?, ?, ?, 'US')");
        $stmt->execute([
            $name,
            $name,
            $email !== '' ? $email : null,
            $website,
            $website,
            $summary,
            'creator:' . $platform,
        ]);
        $leadId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO outreach_creator_candidates
            (canonical_url, status, lead_id, platform, creator_name, harvested_email, last_query)
            VALUES (?, 'imported', ?, ?, ?, ?, 'admin-ui')
            ON DUPLICATE KEY UPDATE
                status          = 'imported',
                lead_id         = VALUES(lead_id),
                platform        = VALUES(platform),
                creator_name    = VALUES(creator_name),
                harvested_email = VALUES(harvested_email)");
        $stmt->execute([$canonicalUrl, $leadId, $platform, $name, $email]);

        log_activity($pdo, $leadId, 'lead_created', 'Imported from Creators discovery UI');
        json_response(['success' => true, 'lead_id' => $leadId]);
    } catch (Exception $e) {
        outreach_log("Creator import failed: " . $e->getMessage());
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

// Apply an email captured via the lead's "Get email" button back onto a creator
// lead. Accepts a single {lead_id, email}, or {results: [{lead_id, url, email}]}.
function creator_set_email($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    if (is_array($data) && array_is_list($data)) {
        // Raw top-level array (the helper's output.json posted directly).
        $results = $data;
    } else {
        $results = $data['results'] ?? null;
        if (!is_array($results)) {
            // Single-item form.
            if (isset($data['email'])) {
                $results = [['lead_id' => $data['lead_id'] ?? null, 'url' => $data['url'] ?? null, 'email' => $data['email']]];
            } else {
                json_response(['success' => false, 'message' => 'Provide results[] or a single {lead_id/url, email}'], 400);
            }
        }
    }

    $updated = 0;
    $skipped = 0;
    foreach ($results as $row) {
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $skipped++; continue; }

        $leadId = (int) ($row['lead_id'] ?? 0);
        $url    = trim((string) ($row['url'] ?? ''));

        if ($leadId > 0) {
            $stmt = $pdo->prepare("UPDATE outreach_leads SET email = ? WHERE id = ? AND source = 'creator_auto' AND (email IS NULL OR email = '')");
            $stmt->execute([$email, $leadId]);
        } elseif ($url !== '') {
            $stmt = $pdo->prepare("UPDATE outreach_leads SET email = ? WHERE website = ? AND source = 'creator_auto' AND (email IS NULL OR email = '')");
            $stmt->execute([$email, $url]);
        } else {
            $skipped++;
            continue;
        }

        if ($stmt->rowCount() > 0) {
            $updated++;
            // Mirror onto the candidate row's harvested_email where we can match it.
            if ($url !== '') {
                $c = $pdo->prepare("UPDATE outreach_creator_candidates SET harvested_email = ? WHERE canonical_url = ?");
                $c->execute([$email, $url]);
            }
        } else {
            $skipped++;
        }
    }

    json_response(['success' => true, 'updated' => $updated, 'skipped' => $skipped]);
}

// ─── AI Draft Generation ───
// Core logic (call_gemini, summarize_business, generate_draft_for_lead) lives in cron/lib/outreach_helpers.php

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

    $result = generate_draft_for_lead($pdo, $lead);

    if (isset($result['error'])) {
        json_response(['success' => false, 'message' => $result['error']], 500);
    }

    // The AI size gate (Layer 3 of the outreach auto-filter) can decide
    // mid-draft that this lead is a chain/corp/institution and disqualify it
    // instead of returning a draft. log_activity + status update were already
    // done inside disqualify_lead(); surface it to the admin with a 409 so
    // the UI can show "Disqualified" rather than render an empty subject/body.
    if (!empty($result['disqualified'])) {
        json_response([
            'success' => false,
            'disqualified' => true,
            'reason' => $result['reason'] ?? 'auto_filter',
            'message' => 'Lead disqualified by the auto-filter (' . ($result['reason'] ?? 'auto_filter') . '): ' . ($result['detail'] ?? ''),
        ], 409);
    }

    log_activity($pdo, $id, 'draft_generated', 'AI draft generated');

    json_response(['success' => true, 'subject' => $result['subject'], 'body' => $result['body']]);
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

    // Guard against sending to disqualified leads. The auto-filter (chain
    // domain, place type, AI size gate) caught this lead for a reason; if
    // the admin really wants to override, they can clear status='disqualified'
    // on the row directly. Refusing here keeps the UI's bulk-send flow safe.
    if (($lead['status'] ?? '') === 'disqualified') {
        $reasonTag = $lead['disqualified_reason'] ?? 'unspecified';
        json_response([
            'success' => false,
            'message' => 'Lead was disqualified by the auto-filter (' . $reasonTag . '). Clear the disqualification first if you really want to send.'
        ], 409);
    }

    // Guard against re-sending to the same lead. Cold-outreach resends are
    // a spam-filter red flag and we never want this to happen by accident,
    // whether from the detail modal, the bulk-send flow, or a stray API call.
    //
    // Exception: if the previous send bounced (status='email_bounced'), the
    // earlier attempt didn't reach a real inbox, so a deliberate retry after
    // the admin has fixed the address isn't a duplicate. Reset sent_at and
    // status so send_outreach_lead's atomic claim works and the post-send
    // CASE in cron/lib/outreach_helpers.php can promote status back to
    // 'contacted'. The suppression list still blocks sends to the bounced
    // address itself; if the admin didn't actually fix the email, the new
    // send attempt will be caught and refused as 'suppressed' instead.
    if (!empty($lead['sent_at'])) {
        if (($lead['status'] ?? '') === 'email_bounced') {
            $pdo->prepare("UPDATE outreach_leads SET sent_at = NULL, status = 'approved' WHERE id = ?")
                ->execute([$id]);
            $lead['sent_at'] = null;
            $lead['status']  = 'approved';
            log_activity($pdo, $id, 'resend_after_bounce', 'Admin retrying send after previous bounce; email is now: ' . $lead['email']);
        } else {
            json_response([
                'success' => false,
                'message' => 'This lead was already emailed on ' . $lead['sent_at'] . '. Outreach does not resend to the same address.'
            ], 409);
        }
    }

    $reason = null;
    if (send_outreach_lead($pdo, $lead, $reason)) {
        log_activity($pdo, $id, 'email_sent', 'Outreach email sent to: ' . $lead['email']);
        json_response(['success' => true, 'message' => 'Email sent successfully']);
    }

    // Skip outcomes (already sent / suppressed) are logged inside
    // send_outreach_lead, so don't double-log them as failures here. Map each
    // to an honest user-facing message and HTTP code.
    if ($reason === 'already_sent') {
        json_response([
            'success' => false,
            'message' => 'This lead was just sent by the automated pipeline. No action needed.',
        ], 409);
    }
    if ($reason === 'suppressed') {
        json_response([
            'success' => false,
            'message' => 'This email is on the outreach suppression list (previous unsubscribe). Skipped.',
        ], 409);
    }

    // Genuine failure (smtp_failed or unknown).
    log_activity($pdo, $id, 'email_failed', 'Email send failed for: ' . $lead['email']);
    json_response(['success' => false, 'message' => 'Failed to send email. Check SMTP configuration.'], 500);
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
    $skipped = 0;
    // Pre-prepare the dedup lookup so we don't re-prepare per row
    $dedupStmt = $pdo->prepare("SELECT id FROM outreach_leads WHERE LOWER(email) = LOWER(?) LIMIT 1");
    while (($row = fgetcsv($file)) !== false) {
        $businessName = trim($row[$columnIndex['business_name']] ?? '');
        if (empty($businessName)) continue;

        $email = isset($columnIndex['email']) ? trim($row[$columnIndex['email']] ?? '') : '';

        // Dedup by email so re-importing the same CSV (or one that overlaps
        // with previously imported leads) doesn't create duplicate rows that
        // would each get their own outreach email.
        if ($email !== '') {
            $dedupStmt->execute([$email]);
            if ($dedupStmt->fetchColumn()) {
                $skipped++;
                continue;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, contact_name, email, phone, website, address, category, city, source, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'csv_import', ?)");

        $stmt->execute([
            $businessName,
            isset($columnIndex['contact_name']) ? trim($row[$columnIndex['contact_name']] ?? '') ?: null : null,
            $email !== '' ? $email : null,
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
    $message = "Imported $imported leads from CSV";
    if ($skipped > 0) {
        $message .= " ($skipped skipped as duplicates)";
    }
    json_response(['success' => true, 'imported' => $imported, 'skipped' => $skipped, 'message' => $message]);
}

// ─── Follow-up endpoints ───

function get_followups($pdo)
{
    $view = $_GET['view'] ?? 'pending_review';
    $validViews = ['pending_review', 'approved', 'upcoming', 'sent', 'halted'];
    if (!in_array($view, $validViews, true)) {
        $view = 'pending_review';
    }

    $sql = "SELECT f.*, l.business_name, l.email AS lead_email, l.city, l.draft_subject AS original_subject
            FROM outreach_followups f
            JOIN outreach_leads l ON l.id = f.lead_id
            WHERE ";

    switch ($view) {
        case 'pending_review':
            $sql .= "f.status = 'drafted' AND f.scheduled_for <= DATE_ADD(NOW(), INTERVAL 2 DAY)";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'approved':
            $sql .= "f.status = 'approved'";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'upcoming':
            $sql .= "f.status = 'scheduled'";
            $sql .= " ORDER BY f.scheduled_for ASC";
            break;
        case 'sent':
            $sql .= "f.status = 'sent' AND f.sent_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $sql .= " ORDER BY f.sent_at DESC";
            break;
        case 'halted':
            $sql .= "f.status IN ('halted','failed','skipped') AND f.updated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $sql .= " ORDER BY f.updated_at DESC";
            break;
    }
    $sql .= " LIMIT 200";

    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'view' => $view, 'rows' => $rows]);
}

function approve_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'approved'
    WHERE id = ? AND status = 'drafted'
      AND draft_subject IS NOT NULL AND draft_subject <> ''
      AND draft_body IS NOT NULL AND draft_body <> ''");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Row not in drafted state, or draft subject/body is empty']); return;
    }
    echo json_encode(['success' => true]);
}

function regenerate_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("SELECT * FROM outreach_followups WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Not found']); return;
    }
    if (!in_array($row['status'], ['drafted', 'failed'], true)) {
        echo json_encode(['success' => false, 'message' => 'Can only regenerate drafted or failed rows']); return;
    }

    // Reset attempts so regen has a fresh budget
    $pdo->prepare("UPDATE outreach_followups SET draft_attempts = 0, status = 'scheduled' WHERE id = ?")
        ->execute([$id]);
    // Re-fetch with the updated state
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $ok = draft_followup_via_gemini($pdo, $row);
    if ($ok) {
        // Return the new draft for the UI
        $newRow = $pdo->prepare("SELECT draft_subject, draft_body FROM outreach_followups WHERE id = ?");
        $newRow->execute([$id]);
        $r = $newRow->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'draft_subject' => $r['draft_subject'], 'draft_body' => $r['draft_body']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gemini draft failed']);
    }
}

function skip_followup($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid id']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'skipped', halt_reason = 'manual' WHERE id = ? AND status IN ('drafted','approved','scheduled')");
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Row already sent or halted']); return;
    }
    echo json_encode(['success' => true]);
}

function halt_followup_sequence($pdo)
{
    $leadId = (int) ($_POST['lead_id'] ?? 0);
    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid lead_id']); return;
    }
    $count = halt_followups_for_lead($pdo, $leadId, 'manual');
    echo json_encode(['success' => true, 'halted_count' => $count]);
}

function bulk_approve_followups($pdo)
{
    $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
    $ids = array_filter($ids, fn($i) => $i > 0);
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No ids']); return;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'approved'
    WHERE status = 'drafted'
      AND draft_subject IS NOT NULL AND draft_subject <> ''
      AND draft_body IS NOT NULL AND draft_body <> ''
      AND id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    echo json_encode(['success' => true, 'approved_count' => $stmt->rowCount()]);
}

function bulk_skip_followups($pdo)
{
    $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
    $ids = array_filter($ids, fn($i) => $i > 0);
    if (empty($ids)) {
        echo json_encode(['success' => false, 'message' => 'No ids']); return;
    }
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("UPDATE outreach_followups SET status = 'skipped', halt_reason = 'manual' WHERE status IN ('drafted','approved','scheduled') AND id IN ($placeholders)");
    $stmt->execute(array_values($ids));
    echo json_encode(['success' => true, 'skipped_count' => $stmt->rowCount()]);
}

function bulk_halt_followups($pdo)
{
    $leadIds = array_map('intval', (array) ($_POST['lead_ids'] ?? []));
    $leadIds = array_filter($leadIds, fn($i) => $i > 0);
    if (empty($leadIds)) {
        echo json_encode(['success' => false, 'message' => 'No lead_ids']); return;
    }
    $total = 0;
    foreach ($leadIds as $lid) {
        $total += halt_followups_for_lead($pdo, $lid, 'manual');
    }
    echo json_encode(['success' => true, 'halted_count' => $total]);
}

function get_followups_for_lead($pdo)
{
    $leadId = (int) ($_GET['lead_id'] ?? 0);
    if ($leadId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid lead_id']); return;
    }
    $stmt = $pdo->prepare("SELECT f.* FROM outreach_followups f WHERE f.lead_id = ? ORDER BY f.touch_number ASC");
    $stmt->execute([$leadId]);
    echo json_encode(['success' => true, 'rows' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
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
- Small: Solo operators, freelancers, local mom-and-pop shops, single-location businesses with likely fewer than 20 employees. Most local service businesses (plumbers, landscapers, cleaners) are small.
- Medium: Businesses with multiple locations, established regional presence, or likely 20-200 employees. Regional chains, mid-size professional firms, established contractors with large teams.
- Large: Major corporations, national/international chains, franchises of well-known brands, businesses with likely 200+ employees.

When in doubt, lean toward 'small' for local businesses found via Google Places search.

Return ONLY a JSON array of size classifications in the same order as the input list.
Example: [\"small\", \"medium\", \"small\", \"large\"]";

    $userPrompt = "Classify these businesses by size:\n\n" . implode("\n", $businessList);

    $result = call_gemini($systemPrompt, $userPrompt);

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

function save_followup_draft($pdo)
{
    $id = (int) ($_POST['id'] ?? 0);
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));
    if ($id <= 0 || $subject === '' || $body === '') {
        echo json_encode(['success' => false, 'message' => 'Missing fields']); return;
    }
    if (strlen($subject) > 500) {
        echo json_encode(['success' => false, 'message' => 'Subject too long (max 500 chars)']); return;
    }
    if (strlen($body) > 10000) {
        echo json_encode(['success' => false, 'message' => 'Body too long (max 10000 chars)']); return;
    }
    $stmt = $pdo->prepare("UPDATE outreach_followups SET draft_subject = ?, draft_body = ? WHERE id = ? AND status = 'drafted'");
    $stmt->execute([$subject, $body, $id]);
    echo json_encode(['success' => true]);
}
