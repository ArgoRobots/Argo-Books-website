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

// Load shared outreach helpers (scrape_email_from_website, search_businesses_core, call_openai, etc.)
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
$pipelineActions = ['send_email', 'generate_draft', 'import_leads', 'search_businesses'];
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

// ─── AI Draft Generation ───
// Core logic (call_openai, summarize_business, generate_draft_for_lead) lives in cron/lib/outreach_helpers.php

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

    // Guard against re-sending to the same lead. Cold-outreach resends are
    // a spam-filter red flag and we never want this to happen by accident,
    // whether from the detail modal, the bulk-send flow, or a stray API call.
    if (!empty($lead['sent_at'])) {
        json_response([
            'success' => false,
            'message' => 'This lead was already emailed on ' . $lead['sent_at'] . '. Outreach does not resend to the same address.'
        ], 409);
    }

    if (send_outreach_lead($pdo, $lead)) {
        $variantTag = !empty($lead['ab_variant_id'])
            ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
            : '';
        log_activity($pdo, $id, 'email_sent', 'Outreach email sent to: ' . $lead['email'] . $variantTag);
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

