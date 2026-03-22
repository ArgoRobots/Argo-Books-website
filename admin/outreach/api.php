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

// Ensure tables exist
ensure_outreach_tables($pdo);

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
    case 'approve_draft':
        approve_draft($pdo);
        break;
    case 'send_email':
        send_outreach_email($pdo);
        break;

    // Activity
    case 'get_activity':
        get_activity($pdo);
        break;

    // CSV
    case 'export_csv':
        export_csv($pdo);
        break;
    case 'import_csv':
        import_csv($pdo);
        break;

    // AI enrichment
    case 'summarize_business':
        summarize_business($pdo);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

// ─── Table initialization ───

function ensure_outreach_tables($pdo)
{
    static $checked = false;
    if ($checked) return;
    $checked = true;

    $pdo->exec("CREATE TABLE IF NOT EXISTS outreach_leads (
        id INT PRIMARY KEY AUTO_INCREMENT,
        business_name VARCHAR(255) NOT NULL,
        contact_name VARCHAR(255) DEFAULT NULL,
        email VARCHAR(255) DEFAULT NULL,
        phone VARCHAR(50) DEFAULT NULL,
        website VARCHAR(500) DEFAULT NULL,
        address VARCHAR(500) DEFAULT NULL,
        category VARCHAR(100) DEFAULT NULL,
        city VARCHAR(100) DEFAULT NULL,
        source VARCHAR(100) DEFAULT 'manual',
        status ENUM('new','researching','ready_to_contact','draft_generated','awaiting_approval','approved','contacted','replied','interested','not_interested','follow_up_needed','onboarded') DEFAULT 'new',
        response_status ENUM('no_response','positive','neutral','negative') DEFAULT 'no_response',
        approval_status ENUM('not_drafted','draft_ready','needs_review','approved','sent') DEFAULT 'not_drafted',
        date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
        first_contact_date DATETIME DEFAULT NULL,
        last_contact_date DATETIME DEFAULT NULL,
        follow_up_date DATE DEFAULT NULL,
        offer_sent TINYINT(1) DEFAULT 0,
        notes TEXT DEFAULT NULL,
        feedback_summary TEXT DEFAULT NULL,
        draft_subject VARCHAR(500) DEFAULT NULL,
        draft_body TEXT DEFAULT NULL,
        drafted_at DATETIME DEFAULT NULL,
        approved_at DATETIME DEFAULT NULL,
        sent_at DATETIME DEFAULT NULL,
        contact_page_url VARCHAR(500) DEFAULT NULL,
        places_id VARCHAR(255) DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_outreach_status (status),
        INDEX idx_outreach_city (city),
        INDEX idx_outreach_follow_up (follow_up_date),
        INDEX idx_outreach_approval (approval_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS outreach_activity_log (
        id INT PRIMARY KEY AUTO_INCREMENT,
        lead_id INT NOT NULL,
        action_type VARCHAR(50) NOT NULL,
        details TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_outreach_activity_lead (lead_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Add columns that may be missing if table was created before they were added to schema
    $migrations = [
        "ALTER TABLE outreach_leads ADD COLUMN places_id VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN contact_page_url VARCHAR(500) DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN feedback_summary TEXT DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN draft_subject VARCHAR(500) DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN draft_body TEXT DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN drafted_at DATETIME DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN approved_at DATETIME DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN sent_at DATETIME DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN approval_status ENUM('not_drafted','draft_ready','needs_review','approved','sent') DEFAULT 'not_drafted'",
        "ALTER TABLE outreach_leads ADD COLUMN response_status ENUM('no_response','positive','neutral','negative') DEFAULT 'no_response'",
        "ALTER TABLE outreach_leads ADD COLUMN follow_up_date DATE DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN offer_sent TINYINT(1) DEFAULT 0",
        "ALTER TABLE outreach_leads ADD COLUMN first_contact_date DATETIME DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN last_contact_date DATETIME DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN contact_name VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE outreach_leads ADD COLUMN source VARCHAR(100) DEFAULT 'manual'",
        "ALTER TABLE outreach_leads ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    ];
    foreach ($migrations as $sql) {
        try { $pdo->exec($sql); } catch (\PDOException $e) {
            // Column already exists — ignore error 1060
            if ($e->getCode() != '42S21' && strpos($e->getMessage(), 'Duplicate column') === false) {
                throw $e;
            }
        }
    }
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

// ─── Lead CRUD ───

function get_leads($pdo)
{
    $status = $_GET['status'] ?? '';
    $response_status = $_GET['response_status'] ?? '';
    $approval_status = $_GET['approval_status'] ?? '';
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
    if ($approval_status) {
        $where[] = 'approval_status = ?';
        $params[] = $approval_status;
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
        'follow_up_asc' => 'follow_up_date ASC',
        'follow_up_desc' => 'follow_up_date DESC',
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

function create_lead($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

    if (empty($data['business_name'])) {
        json_response(['success' => false, 'message' => 'Business name is required'], 400);
    }

    $stmt = $pdo->prepare("INSERT INTO outreach_leads
        (business_name, contact_name, email, phone, website, address, category, city, source, status, notes, follow_up_date, contact_page_url)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

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
        $data['follow_up_date'] ?? null,
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
        'category', 'city', 'source', 'status', 'response_status', 'approval_status',
        'notes', 'feedback_summary', 'follow_up_date', 'offer_sent',
        'draft_subject', 'draft_body', 'contact_page_url',
        'first_contact_date', 'last_contact_date',
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
    if (in_array('follow_up_date', $changes)) {
        log_activity($pdo, $id, 'follow_up_set', 'Follow-up set: ' . ($data['follow_up_date'] ?? 'cleared'));
    }

    json_response(['success' => true, 'message' => 'Lead updated']);
}

function delete_lead($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int)($data['id'] ?? 0);

    if (!$id) {
        json_response(['success' => false, 'message' => 'Lead ID is required'], 400);
    }

    $stmt = $pdo->prepare("DELETE FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);

    json_response(['success' => true, 'message' => 'Lead deleted']);
}

function get_stats($pdo)
{
    $stats = [];
    $rows = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(status = 'new') as new_leads,
        SUM(approval_status = 'draft_ready' OR approval_status = 'needs_review') as drafts_pending,
        SUM(approval_status = 'approved') as approved,
        SUM(status = 'contacted') as contacted,
        SUM(status = 'replied') as replied,
        SUM(status = 'interested') as interested,
        SUM(status = 'follow_up_needed') as follow_up_needed
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
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);

    $falsePositives = ['example.com', 'sentry.io', 'wixpress.com', 'wordpress.org', 'w3.org', 'schema.org', 'googleapis.com', 'gravatar.com'];

    // Helper to extract email from HTML
    $extractEmail = function($html) use ($falsePositives) {
        // Look for mailto: links first (most reliable)
        if (preg_match_all('/mailto:([a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,})/', $html, $matches)) {
            foreach ($matches[1] as $email) {
                $dominated = false;
                foreach ($falsePositives as $fp) { if (str_contains(strtolower($email), $fp)) { $dominated = true; break; } }
                if (!$dominated) return $email;
            }
        }
        // Fallback: email patterns in text
        if (preg_match_all('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', $html, $matches)) {
            foreach ($matches[0] as $email) {
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
        $base = rtrim($url, '/');
        $contactPaths = [];
        if (preg_match_all('/href=["\']([^"\']*(?:contact|about|about-us|contact-us|connect|get-in-touch|reach-us)[^"\']*)["\'](?:[^>]*>([^<]*))?/i', $html, $linkMatches, PREG_SET_ORDER)) {
            foreach ($linkMatches as $m) {
                $href = $m[1];
                if (str_starts_with($href, 'http')) {
                    $contactPaths[] = $href;
                } elseif (str_starts_with($href, '/')) {
                    $contactPaths[] = $base . $href;
                }
            }
        }

        // Fallback: try common paths if none found in links
        if (empty($contactPaths)) {
            $contactPaths = [
                $base . '/contact',
                $base . '/contact-us',
                $base . '/about',
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

    if (empty($city)) {
        json_response(['success' => false, 'message' => 'City is required'], 400);
    }

    $apiKey = $_ENV['GOOGLE_PLACES_API_KEY'] ?? '';
    if (empty($apiKey)) {
        json_response(['success' => false, 'message' => 'Google Places API key not configured. Set GOOGLE_PLACES_API_KEY in .env'], 500);
    }

    $query = $category ? "$category in $city" : "businesses in $city";
    if ($province) $query .= ", $province";

    // Text Search
    $url = 'https://maps.googleapis.com/maps/api/place/textsearch/json?' . http_build_query([
        'query' => $query,
        'key' => $apiKey,
    ]);

    $response = file_get_contents($url);
    if ($response === false) {
        json_response(['success' => false, 'message' => 'Failed to connect to Google Places API'], 502);
    }

    $data = json_decode($response, true);
    if (($data['status'] ?? '') !== 'OK' && ($data['status'] ?? '') !== 'ZERO_RESULTS') {
        $errorMsg = $data['error_message'] ?? $data['status'] ?? 'Unknown error';
        json_response(['success' => false, 'message' => 'Google Places API error: ' . $errorMsg], 502);
    }

    $results = array_slice($data['results'] ?? [], 0, $limit);
    $businesses = [];

    foreach ($results as $place) {
        $business = [
            'places_id' => $place['place_id'] ?? '',
            'business_name' => $place['name'] ?? '',
            'address' => $place['formatted_address'] ?? '',
            'category' => $category ?: (isset($place['types'][0]) ? ucfirst(str_replace('_', ' ', $place['types'][0])) : ''),
            'city' => $city,
            'phone' => null,
            'website' => null,
            'email' => null,
        ];

        // Fetch place details for phone and website
        if (!empty($place['place_id'])) {
            $detailUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
                'place_id' => $place['place_id'],
                'fields' => 'formatted_phone_number,website,url',
                'key' => $apiKey,
            ]);
            $detailResp = @file_get_contents($detailUrl);
            if ($detailResp) {
                $detail = json_decode($detailResp, true);
                $r = $detail['result'] ?? [];
                $business['phone'] = $r['formatted_phone_number'] ?? null;
                $business['website'] = $r['website'] ?? null;
                $business['contact_page_url'] = $r['url'] ?? null;
            }
        }

        // Scrape email from business website
        if (!empty($business['website'])) {
            $business['email'] = scrape_email_from_website($business['website']);
        }

        $businesses[] = $business;
    }

    json_response(['success' => true, 'businesses' => $businesses, 'count' => count($businesses)]);
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
                (business_name, phone, website, address, category, city, source, places_id, contact_page_url, email)
                VALUES (?, ?, ?, ?, ?, ?, 'google_places', ?, ?, ?)");
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
            ]);

            $id = $pdo->lastInsertId();
            log_activity($pdo, $id, 'lead_created', 'Imported from Google Places: ' . ($biz['business_name'] ?? 'Unknown'));
            $imported++;
        }

        json_response(['success' => true, 'imported' => $imported, 'skipped' => $skipped, 'message' => "Imported $imported leads, skipped $skipped duplicates"]);
    } catch (Exception $e) {
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

    $systemPrompt = "You are helping write a brief, personal outreach email from Evan, the developer behind Argo Books, to a small business. The goal is to get honest product feedback on Argo Books, a bookkeeping and invoicing app for small businesses.

Rules:
- Keep it short (3-5 short paragraphs max)
- Sound human, friendly, and genuine — not like marketing spam
- The sender's name is Evan — he is a local independent developer building software for small businesses
- Do NOT refer to a \"team\" — Evan is a solo developer
- Mention you are looking for honest feedback from real business owners
- If appropriate, mention offering a free 1-year premium license in exchange for feedback
- Personalize based on the business category and city if possible
- Do NOT invent details about the business you do not have
- If limited info is available, keep it more general
- Use a casual but professional tone
- NEVER use placeholders like [Your Name], [Your Title], [Your Company], etc.
- Always sign the email as: Evan — Argo Books

Return your response as JSON with two fields:
{\"subject\": \"the email subject line\", \"body\": \"the email body text (plain text, use \\n for line breaks)\"}

Return ONLY the JSON, no other text.";

    $details = "Business: {$lead['business_name']}";
    if ($lead['category']) $details .= "\nCategory/Industry: {$lead['category']}";
    if ($lead['city']) $details .= "\nCity: {$lead['city']}";
    if ($lead['website']) $details .= "\nWebsite: {$lead['website']}";
    if ($lead['contact_name']) $details .= "\nContact person: {$lead['contact_name']}";

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

    // Save draft to lead
    $stmt = $pdo->prepare("UPDATE outreach_leads SET draft_subject = ?, draft_body = ?, drafted_at = NOW(), approval_status = 'draft_ready', status = CASE WHEN status = 'new' OR status = 'researching' OR status = 'ready_to_contact' THEN 'draft_generated' ELSE status END WHERE id = ?");
    $stmt->execute([$parsed['subject'], $parsed['body'], $id]);

    log_activity($pdo, $id, 'draft_generated', 'AI draft generated');

    json_response(['success' => true, 'subject' => $parsed['subject'], 'body' => $parsed['body']]);
}

// ─── Email workflow ───

function approve_draft($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        json_response(['success' => false, 'message' => 'Lead not found'], 404);
    }

    if (empty($lead['draft_subject']) || empty($lead['draft_body'])) {
        json_response(['success' => false, 'message' => 'No draft to approve'], 400);
    }

    $stmt = $pdo->prepare("UPDATE outreach_leads SET approval_status = 'approved', approved_at = NOW(), status = CASE WHEN status IN ('new','researching','ready_to_contact','draft_generated','awaiting_approval') THEN 'approved' ELSE status END WHERE id = ?");
    $stmt->execute([$id]);

    log_activity($pdo, $id, 'draft_approved', 'Draft approved for sending');

    json_response(['success' => true, 'message' => 'Draft approved']);
}

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

    if ($lead['approval_status'] !== 'approved') {
        json_response(['success' => false, 'message' => 'Draft must be approved before sending'], 400);
    }

    if (empty($lead['email'])) {
        json_response(['success' => false, 'message' => 'No email address for this lead'], 400);
    }

    if (empty($lead['draft_subject']) || empty($lead['draft_body'])) {
        json_response(['success' => false, 'message' => 'No draft to send'], 400);
    }

    // Format body for HTML email (convert newlines to <br>)
    $htmlBody = '<p>' . nl2br(htmlspecialchars($lead['draft_body'])) . '</p>';

    $result = send_styled_email($lead['email'], $lead['draft_subject'], $htmlBody);

    if ($result) {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET
            approval_status = 'sent',
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
        'Category', 'City', 'Source', 'Status', 'Response Status', 'Approval Status',
        'Date Added', 'First Contact', 'Last Contact', 'Follow-up Date', 'Offer Sent',
        'Notes', 'Feedback Summary', 'Draft Subject', 'Draft Body'];
    fputcsv($output, $headers);

    foreach ($leads as $lead) {
        fputcsv($output, [
            $lead['id'], $lead['business_name'], $lead['contact_name'], $lead['email'],
            $lead['phone'], $lead['website'], $lead['address'], $lead['category'],
            $lead['city'], $lead['source'], $lead['status'], $lead['response_status'],
            $lead['approval_status'], $lead['date_added'], $lead['first_contact_date'],
            $lead['last_contact_date'], $lead['follow_up_date'], $lead['offer_sent'],
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

// ─── AI Enrichment ───

function summarize_business($pdo)
{
    $data = json_decode(file_get_contents('php://input'), true);
    $id = (int)($data['id'] ?? 0);

    $stmt = $pdo->prepare("SELECT * FROM outreach_leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        json_response(['success' => false, 'message' => 'Lead not found'], 404);
    }

    $systemPrompt = "Summarize what this business likely does based on the information provided. Keep it to 1-2 sentences. If very limited info is available, say so briefly. Also suggest 1-2 relevant tags from this list: price concern, interested in demo, wants follow-up later, bookkeeping needs, invoicing needs, new business, established business.

Return JSON: {\"summary\": \"...\", \"tags\": [\"...\"]}
Return ONLY the JSON.";

    $details = "Business: {$lead['business_name']}";
    if ($lead['category']) $details .= "\nCategory: {$lead['category']}";
    if ($lead['city']) $details .= "\nCity: {$lead['city']}";
    if ($lead['website']) $details .= "\nWebsite: {$lead['website']}";

    $result = call_openai($systemPrompt, $details);

    if (isset($result['error'])) {
        json_response(['success' => false, 'message' => $result['error']], 500);
    }

    $content = trim($result['content']);
    $content = preg_replace('/^```json\s*/i', '', $content);
    $content = preg_replace('/\s*```$/', '', $content);
    $parsed = json_decode($content, true);

    $summary = $parsed['summary'] ?? $content;

    $stmt = $pdo->prepare("UPDATE outreach_leads SET feedback_summary = ? WHERE id = ?");
    $stmt->execute([$summary, $id]);

    log_activity($pdo, $id, 'business_summarized', 'AI summary generated');

    json_response(['success' => true, 'summary' => $summary, 'tags' => $parsed['tags'] ?? []]);
}
