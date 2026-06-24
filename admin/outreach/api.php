<?php
session_start();
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
require_once __DIR__ . '/../../cron/lib/reddit_helpers.php';

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
$pipelineActions = ['send_email', 'generate_draft', 'import_leads', 'search_businesses', 'regenerate_followup', 'shopify_run_dork', 'shopify_import'];
if (in_array($action, $pipelineActions) && is_pipeline_running()) {
    echo json_encode([
        'success' => false,
        'message' => 'The outreach pipeline cron job is currently running. Please wait a few minutes and try again.'
    ]);
    exit;
}

// Block Reddit actions while the Reddit cron is running
function is_reddit_monitor_running(): bool
{
    $lockFile = __DIR__ . '/../../cron/logs/reddit_monitor.lock';
    if (!file_exists($lockFile)) return false;
    $fp = @fopen($lockFile, 'r');
    if (!$fp) return false;
    $locked = !flock($fp, LOCK_EX | LOCK_NB);
    fclose($fp);
    return $locked;
}
$redditCronActions = ['reddit_run_now', 'reddit_regenerate_draft', 'reddit_generate_pending_draft'];
if (in_array($action, $redditCronActions) && is_reddit_monitor_running()) {
    echo json_encode([
        'success' => false,
        'message' => 'The Reddit discovery cron is currently running. Please wait a few minutes and try again.'
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

    // Reddit outreach
    case 'reddit_get_threads':
        reddit_api_get_threads($pdo);
        break;
    case 'reddit_get_thread':
        reddit_api_get_thread($pdo);
        break;
    case 'reddit_get_stats':
        reddit_api_get_stats($pdo);
        break;
    case 'reddit_pipeline_progress':
        reddit_api_pipeline_progress();
        break;
    case 'reddit_run_now':
        reddit_api_run_now($pdo);
        break;
    case 'reddit_mark_replied':
        reddit_api_mark_replied($pdo);
        break;
    case 'reddit_mark_skipped':
        reddit_api_mark_skipped($pdo);
        break;
    case 'reddit_save_draft':
        reddit_api_save_draft($pdo);
        break;
    case 'reddit_regenerate_draft':
        reddit_api_regenerate_draft($pdo);
        break;
    case 'reddit_generate_pending_draft':
        reddit_api_generate_pending_draft($pdo);
        break;
    case 'reddit_get_account_info':
        reddit_api_get_account_info($pdo);
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

    // Match both legacy source codes ("outreach-42") and A/B-tagged ones ("outreach-42-v7")
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

    // Follow-ups pending review (drafted, awaiting admin approval)
    $rows['followups_pending'] = (int) $pdo->query("SELECT COUNT(*) FROM outreach_followups WHERE status = 'drafted'")->fetchColumn();

    // Count distinct leads clicked. SUBSTRING_INDEX collapses "outreach-42-v7" → "outreach-42"
    // so a lead that received multiple variants and had any of them clicked counts once.
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
    $fits                 = [];
    $rejectedCount        = 0;
    $rejectReasons        = [];
    $alreadyImportedCount = 0;
    $totalEvaluated       = 0;
    $queriesRun           = [];

    while (count($fits) < $limit && $callsToday < $serpapiLimit) {
        $query = SHOPIFY_DORK_POOL[$cursor % $poolSize];
        $cursor++;
        _shopify_state_set($pdo, 'shopify_dork_cursor', (string) $cursor);

        $queryWithExclusions = $query . SHOPIFY_DORK_EXCLUSIONS;
        $queryResult = serpapi_query_cached($queryWithExclusions, $apiKey, 100, $pdo);
        $serpResults = $queryResult['results'];
        if (!$queryResult['from_cache']) {
            $callsToday++;
            _shopify_state_set($pdo, 'serpapi_calls_today', (string) $callsToday);
        }
        $queriesRun[] = $query;

        if (empty($serpResults)) continue;
        $totalEvaluated += count($serpResults);

        foreach ($serpResults as $r) {
            if (count($fits) >= $limit) break 2;

            $canonical = shopify_canonical_url($r['link'] ?? '');
            if ($canonical === '') continue;

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

            $result = evaluate_shopify_candidate($canonical);

            if (empty($result['fit'])) {
                $rejectedCount++;
                $reason = $result['reason'] ?? 'unknown';
                $rejectReasons[$reason] = ($rejectReasons[$reason] ?? 0) + 1;
                continue;
            }

            $meta = $result['metadata'] ?? [];
            $fits[] = [
                'canonical_url'    => $canonical,
                'serp_title'       => $r['title'] ?? '',
                'fit'              => true,
                'final_url'        => $result['final_url'] ?? $canonical,
                'business_name'    => $meta['business_name'] ?? '',
                'email'            => $meta['email'] ?? '',
                'products_count'   => $meta['products_count'] ?? null,
                'first_product_at' => $meta['first_product_created_at'] ?? null,
                'country'          => $meta['country'] ?? '',
            ];
        }
    }

    $quotaExhausted = $callsToday >= $serpapiLimit && count($fits) < $limit;

    json_response([
        'success'                => true,
        'results'                => $fits,
        'requested_limit'        => $limit,
        'quota_exhausted'        => $quotaExhausted,
        'rejected_count'         => $rejectedCount,
        'reject_reasons'         => $rejectReasons,
        'already_imported_count' => $alreadyImportedCount,
        'total_evaluated'        => $totalEvaluated,
        'queries_run'            => $queriesRun,
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
        $variantTag = !empty($lead['ab_variant_id'])
            ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
            : '';
        log_activity($pdo, $id, 'email_sent', 'Outreach email sent to: ' . $lead['email'] . $variantTag);
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

    $sql = "SELECT f.*, l.business_name, l.email AS lead_email, l.city, l.draft_subject AS original_subject,
                   v.label AS ab_variant_label
            FROM outreach_followups f
            JOIN outreach_leads l ON l.id = f.lead_id
            LEFT JOIN outreach_ab_variants v ON v.id = f.ab_variant_id
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
    $stmt = $pdo->prepare("SELECT f.*, v.label AS ab_variant_label FROM outreach_followups f LEFT JOIN outreach_ab_variants v ON v.id = f.ab_variant_id WHERE f.lead_id = ? ORDER BY f.touch_number ASC");
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

// ─── Reddit outreach endpoints ───

function reddit_api_get_threads($pdo)
{
    $status = (string) ($_GET['status'] ?? 'actionable');
    $subreddit = (string) ($_GET['subreddit'] ?? '');
    $source = (string) ($_GET['source'] ?? '');
    $days = max(0, (int) ($_GET['days'] ?? 30));

    $where = [];
    $params = [];

    if ($status === 'actionable') {
        $where[] = "status IN ('drafted', 'drafted_pending')";
    } elseif ($status === 'reply_removed') {
        $where[] = "status = 'replied' AND reply_status IN ('removed', 'removed_or_shadowbanned')";
    } elseif ($status !== '' && $status !== 'all') {
        $where[] = 'status = ?';
        $params[] = $status;
    }

    if ($subreddit !== '') {
        $where[] = 'subreddit = ?';
        $params[] = $subreddit;
    }

    if (in_array($source, ['watchlist', 'keyword', 'both'], true)) {
        $where[] = 'discovery_source = ?';
        $params[] = $source;
    }

    if ($days > 0) {
        $where[] = "discovered_at > DATE_SUB(NOW(), INTERVAL $days DAY)";
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    // List endpoint deliberately omits draft_body (could be up to ~2KB per row,
    // not used by the table UI). The thread-detail modal fetches the full row
    // via reddit_get_thread when opened. `has_draft` lets the UI show a flag.
    $sql = "SELECT id, reddit_id, subreddit, title, url, ai_relevance, ai_relevance_reason, status,
                   reply_status, reply_upvotes, reply_replies_count, reply_permalink,
                   (draft_body IS NOT NULL AND draft_body <> '') AS has_draft,
                   discovered_at, posted_at, comment_count, rules_score, discovery_source, mentioned_product
            FROM reddit_threads $whereClause
            ORDER BY (ai_relevance IS NULL) ASC, ai_relevance DESC, discovered_at DESC
            LIMIT 200";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $threads = $stmt->fetchAll();
    } catch (PDOException $e) {
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }

    json_response(['success' => true, 'threads' => $threads]);
}

function reddit_api_get_thread($pdo)
{
    $id = (int) ($_GET['id'] ?? 0);
    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);

    $stmt = $pdo->prepare("SELECT * FROM reddit_threads WHERE id = ?");
    $stmt->execute([$id]);
    $thread = $stmt->fetch();
    if (!$thread) json_response(['success' => false, 'message' => 'Thread not found'], 404);

    json_response(['success' => true, 'thread' => $thread]);
}

function reddit_api_get_stats($pdo)
{
    try {
        $stats = [
            'total' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads")->fetchColumn(),
            'drafted' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'drafted'")->fetchColumn(),
            'drafted_pending' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'drafted_pending'")->fetchColumn(),
            'replied_7d' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'replied' AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
            'daily_used' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'replied' AND mentioned_product = 1 AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 1 DAY)")->fetchColumn(),
            'weekly_used' => (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'replied' AND mentioned_product = 1 AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn(),
        ];

        // Reply survival: of replies whose status has been checked at least once,
        // what % are still live? Excludes deleted_by_user (founder's own action).
        $survival = $pdo->query("
            SELECT
                SUM(CASE WHEN reply_status = 'live' THEN 1 ELSE 0 END) AS live_count,
                SUM(CASE WHEN reply_status IN ('live','removed','removed_or_shadowbanned') THEN 1 ELSE 0 END) AS judged_count
            FROM reddit_threads
            WHERE status = 'replied' AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
        ")->fetch();
        $judged = (int) ($survival['judged_count'] ?? 0);
        $live = (int) ($survival['live_count'] ?? 0);
        $stats['survival_pct'] = $judged > 0 ? round(($live / $judged) * 100, 1) : null;
        $stats['survival_n'] = $judged;

        // Avg upvotes on live replies
        $avg = $pdo->query("
            SELECT AVG(reply_upvotes) AS avg_upvotes
            FROM reddit_threads
            WHERE reply_status = 'live' AND reply_upvotes IS NOT NULL
        ")->fetch();
        $stats['avg_upvotes'] = $avg && $avg['avg_upvotes'] !== null ? round((float) $avg['avg_upvotes'], 1) : null;

        // Profile-link clicks (30d). statistics.php emits a 'reddit_referrer'
        // event row whenever an inbound page view has a reddit.com Referer.
        try {
            $clicks = $pdo->query("
                SELECT COUNT(*) FROM statistics
                WHERE event_type = 'reddit_referrer'
                  AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
            ")->fetchColumn();
            $stats['profile_clicks_30d'] = (int) $clicks;
        } catch (PDOException $e) {
            $stats['profile_clicks_30d'] = 0;
        }

        // Post limits (for safety meter)
        $cfg = $pdo->query("SELECT daily_post_limit, weekly_post_limit FROM reddit_settings WHERE id = 1")->fetch();
        $stats['daily_limit'] = (int) ($cfg['daily_post_limit'] ?? 3);
        $stats['weekly_limit'] = (int) ($cfg['weekly_post_limit'] ?? 12);

        json_response(['success' => true, 'stats' => $stats]);
    } catch (PDOException $e) {
        json_response(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function reddit_api_pipeline_progress()
{
    json_response([
        'success' => true,
        'running' => is_reddit_monitor_running(),
        'progress' => reddit_progress_read(),
    ]);
}

function reddit_api_run_now($pdo)
{
    // This host disables exec/shell_exec/proc_open, so we can't spawn a CLI
    // subprocess, and running the full discovery inline in this web request is
    // hard-killed by PHP-FPM's request_terminate_timeout (~30s) long before it
    // finishes. Instead we record a run request; the reddit_run_dispatcher cron
    // (every 2 min) claims it and runs discovery via CLI, which has no such time
    // limit. A manual request runs even when the master enable toggle is off.
    $pdo->prepare("UPDATE reddit_settings SET manual_run_requested_at = NOW() WHERE id = 1")->execute();

    // Show "queued" in the progress UI the Reddit threads tab polls, so the
    // admin sees immediate feedback before the dispatcher picks it up.
    reddit_progress_reset([
        'message'    => 'Queued — discovery will start within ~2 minutes…',
        'started_at' => date('Y-m-d H:i:s'),
    ]);

    outreach_log('Reddit discovery run requested from admin (queued for dispatcher)');
    json_response([
        'success' => true,
        'message' => 'Discovery queued. It starts within a couple of minutes — watch this tab for progress.',
    ]);
}

function reddit_api_mark_replied($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int) ($data['id'] ?? 0);
    $permalink = trim((string) ($data['permalink'] ?? ''));
    $mentionedProduct = !empty($data['mentioned_product']) ? 1 : 0;
    $overrideLimit = !empty($data['override_limit']) ? 1 : 0;

    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);

    $commentId = reddit_extract_comment_id_from_permalink($permalink);
    if ($commentId === null) {
        json_response(['success' => false, 'message' => 'Invalid Reddit comment permalink. Expected something like https://www.reddit.com/r/.../comments/.../slug/<comment_id>/'], 400);
    }

    // Enforce post limits unless override is set
    if ($mentionedProduct && !$overrideLimit) {
        $cfg = $pdo->query("SELECT daily_post_limit, weekly_post_limit FROM reddit_settings WHERE id = 1")->fetch();
        $dailyLimit = (int) ($cfg['daily_post_limit'] ?? 3);
        $weeklyLimit = (int) ($cfg['weekly_post_limit'] ?? 12);
        $dailyUsed = (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'replied' AND mentioned_product = 1 AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 1 DAY)")->fetchColumn();
        $weeklyUsed = (int) $pdo->query("SELECT COUNT(*) FROM reddit_threads WHERE status = 'replied' AND mentioned_product = 1 AND reply_posted_at > DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        if ($dailyUsed >= $dailyLimit || $weeklyUsed >= $weeklyLimit) {
            json_response([
                'success' => false,
                'message' => "You're at the post limit (daily $dailyUsed/$dailyLimit, weekly $weeklyUsed/$weeklyLimit). Override required.",
                'requires_override' => true,
                'daily_used' => $dailyUsed,
                'daily_limit' => $dailyLimit,
                'weekly_used' => $weeklyUsed,
                'weekly_limit' => $weeklyLimit,
            ]);
        }
    }

    $stmt = $pdo->prepare("
        UPDATE reddit_threads
        SET status = 'replied',
            reply_permalink = ?,
            reply_comment_id = ?,
            reply_posted_at = NOW(),
            reply_status = 'pending',
            reply_status_check_count = 0,
            mentioned_product = ?,
            override_limit = ?
        WHERE id = ?
    ");
    $stmt->execute([$permalink, $commentId, $mentionedProduct, $overrideLimit, $id]);

    if ($stmt->rowCount() === 0) {
        json_response(['success' => false, 'message' => 'Thread not found'], 404);
    }

    json_response(['success' => true]);
}

function reddit_api_mark_skipped($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int) ($data['id'] ?? 0);
    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);

    $stmt = $pdo->prepare("UPDATE reddit_threads SET status = 'skipped' WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['success' => true]);
}

function reddit_api_save_draft($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int) ($data['id'] ?? 0);
    $body = trim((string) ($data['draft_body'] ?? ''));

    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);
    if ($body === '') json_response(['success' => false, 'message' => 'Draft body cannot be empty'], 400);
    if (strlen($body) > 10000) json_response(['success' => false, 'message' => 'Draft too long (max 10000 chars)'], 400);

    $stmt = $pdo->prepare("UPDATE reddit_threads SET draft_body = ?, status = CASE WHEN status = 'drafted_pending' THEN 'drafted' ELSE status END WHERE id = ?");
    $stmt->execute([$body, $id]);
    json_response(['success' => true]);
}

function reddit_api_regenerate_draft($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int) ($data['id'] ?? 0);
    $feedback = trim((string) ($data['feedback'] ?? ''));
    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);
    if (mb_strlen($feedback) > 2000) {
        json_response(['success' => false, 'message' => 'Feedback too long (max 2000 chars)'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM reddit_threads WHERE id = ?");
    $stmt->execute([$id]);
    $thread = $stmt->fetch();
    if (!$thread) json_response(['success' => false, 'message' => 'Thread not found'], 404);

    $comments = reddit_fetch_top_comments($pdo, $thread['subreddit'], $thread['reddit_id']);
    $draft = reddit_generate_draft($thread, $comments, [
        'previous_draft' => $thread['draft_body'] ?? '',
        'feedback' => $feedback,
    ]);
    if (!empty($draft['error'])) {
        json_response(['success' => false, 'message' => 'Draft generation failed: ' . $draft['error']], 500);
    }

    $upd = $pdo->prepare("UPDATE reddit_threads SET draft_body = ?, draft_generated_at = NOW(), status = 'drafted' WHERE id = ?");
    $upd->execute([$draft['body'], $id]);

    json_response(['success' => true, 'draft_body' => $draft['body']]);
}

function reddit_api_generate_pending_draft($pdo)
{
    // Same as regenerate but only valid for drafted_pending rows
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int) ($data['id'] ?? 0);
    if ($id <= 0) json_response(['success' => false, 'message' => 'Missing thread id'], 400);

    $stmt = $pdo->prepare("SELECT * FROM reddit_threads WHERE id = ? AND status = 'drafted_pending'");
    $stmt->execute([$id]);
    $thread = $stmt->fetch();
    if (!$thread) json_response(['success' => false, 'message' => 'Thread not found or not pending'], 404);

    $comments = reddit_fetch_top_comments($pdo, $thread['subreddit'], $thread['reddit_id']);
    $draft = reddit_generate_draft($thread, $comments);
    if (!empty($draft['error'])) {
        json_response(['success' => false, 'message' => 'Draft generation failed: ' . $draft['error']], 500);
    }

    $upd = $pdo->prepare("UPDATE reddit_threads SET draft_body = ?, draft_generated_at = NOW(), status = 'drafted' WHERE id = ?");
    $upd->execute([$draft['body'], $id]);

    json_response(['success' => true, 'draft_body' => $draft['body']]);
}

function reddit_api_get_account_info($pdo)
{
    if (empty($_ENV['REDDIT_USERNAME'])) {
        json_response([
            'success' => false,
            'message' => 'REDDIT_USERNAME is not set in .env, so we don\'t know which account to look up. Set it to enable this card.',
        ]);
    }

    $info = reddit_fetch_account_about($pdo);
    if ($info === null) {
        $hint = reddit_oauth_configured()
            ? 'OAuth token request failed. Verify REDDIT_CLIENT_ID / SECRET / PASSWORD in .env.'
            : 'Without OAuth, Reddit blocks the account-about endpoint from this server\'s IP. Configure REDDIT_* OAuth env vars to enable.';
        json_response(['success' => false, 'message' => $hint]);
    }

    $info['account_age_days'] = $info['created_utc'] > 0 ? floor((time() - $info['created_utc']) / 86400) : null;
    json_response(['success' => true, 'account' => $info]);
}

