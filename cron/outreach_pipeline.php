<?php
/**
 * Fully Automated Outreach Pipeline Cron
 *
 * Runs the complete outreach pipeline automatically:
 *   1. Pick the next target city from the expansion list
 *   2. Discover businesses via Google Places
 *   3. Import them (skip duplicates)
 *   4. Generate AI email drafts for leads without one
 *   5. Auto-approve drafts
 *   6. Send approved emails (up to daily limit)
 *
 * RECOMMENDED SCHEDULE: Daily at 8:00 AM (before the send window)
 *   0 8 * * * /usr/bin/php /path/to/outreach_pipeline.php
 *
 * Manual execution:
 *   php outreach_pipeline.php
 *   php outreach_pipeline.php --discover-only   # Only run discovery + import
 *   php outreach_pipeline.php --draft-only      # Only run draft generation
 *   php outreach_pipeline.php --send-only       # Only run send (same as outreach_email.php)
 *   php outreach_pipeline.php --dry-run         # Log what would happen without doing it
 */

set_time_limit(600); // 10 minutes max for full pipeline

// Only allow CLI execution
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI.');
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../email_sender.php';
require_once __DIR__ . '/lib/outreach_helpers.php';

// ─── Configuration ───

define('DAILY_SEND_LIMIT', (int) ($_ENV['OUTREACH_DAILY_SEND_LIMIT'] ?? 10));
define('DISCOVERY_BATCH_SIZE', (int) ($_ENV['OUTREACH_DISCOVERY_BATCH'] ?? 20));
define('DRAFT_BATCH_SIZE', (int) ($_ENV['OUTREACH_DRAFT_BATCH'] ?? 15));
define('AUTO_APPROVE', filter_var($_ENV['OUTREACH_AUTO_APPROVE'] ?? 'true', FILTER_VALIDATE_BOOLEAN));

// Parse CLI flags
$args = array_slice($argv, 1);
$discoverOnly = in_array('--discover-only', $args);
$draftOnly = in_array('--draft-only', $args);
$sendOnly = in_array('--send-only', $args);
$dryRun = in_array('--dry-run', $args);
$runAll = !$discoverOnly && !$draftOnly && !$sendOnly;

// ─── Target Cities (Saskatchewan first, then expand outward) ───

$targetCities = [
    // Saskatchewan — primary market
    ['city' => 'Saskatoon', 'province' => 'Saskatchewan'],
    ['city' => 'Regina', 'province' => 'Saskatchewan'],
    ['city' => 'Prince Albert', 'province' => 'Saskatchewan'],
    ['city' => 'Moose Jaw', 'province' => 'Saskatchewan'],
    ['city' => 'Swift Current', 'province' => 'Saskatchewan'],
    ['city' => 'Yorkton', 'province' => 'Saskatchewan'],
    ['city' => 'North Battleford', 'province' => 'Saskatchewan'],
    ['city' => 'Estevan', 'province' => 'Saskatchewan'],
    ['city' => 'Weyburn', 'province' => 'Saskatchewan'],
    ['city' => 'Martensville', 'province' => 'Saskatchewan'],
    ['city' => 'Warman', 'province' => 'Saskatchewan'],
    ['city' => 'Humboldt', 'province' => 'Saskatchewan'],
    ['city' => 'Melfort', 'province' => 'Saskatchewan'],
    ['city' => 'Meadow Lake', 'province' => 'Saskatchewan'],
    ['city' => 'Lloydminster', 'province' => 'Saskatchewan'],
    // Alberta — neighboring province
    ['city' => 'Edmonton', 'province' => 'Alberta'],
    ['city' => 'Calgary', 'province' => 'Alberta'],
    ['city' => 'Red Deer', 'province' => 'Alberta'],
    ['city' => 'Lethbridge', 'province' => 'Alberta'],
    ['city' => 'Medicine Hat', 'province' => 'Alberta'],
    ['city' => 'Grande Prairie', 'province' => 'Alberta'],
    ['city' => 'Airdrie', 'province' => 'Alberta'],
    ['city' => 'Spruce Grove', 'province' => 'Alberta'],
    ['city' => 'St. Albert', 'province' => 'Alberta'],
    // Manitoba — neighboring province
    ['city' => 'Winnipeg', 'province' => 'Manitoba'],
    ['city' => 'Brandon', 'province' => 'Manitoba'],
    ['city' => 'Steinbach', 'province' => 'Manitoba'],
    ['city' => 'Thompson', 'province' => 'Manitoba'],
    ['city' => 'Portage la Prairie', 'province' => 'Manitoba'],
    // British Columbia
    ['city' => 'Vancouver', 'province' => 'British Columbia'],
    ['city' => 'Victoria', 'province' => 'British Columbia'],
    ['city' => 'Kelowna', 'province' => 'British Columbia'],
    ['city' => 'Kamloops', 'province' => 'British Columbia'],
    ['city' => 'Nanaimo', 'province' => 'British Columbia'],
    // Ontario
    ['city' => 'Toronto', 'province' => 'Ontario'],
    ['city' => 'Ottawa', 'province' => 'Ontario'],
    ['city' => 'Hamilton', 'province' => 'Ontario'],
    ['city' => 'London', 'province' => 'Ontario'],
    ['city' => 'Kitchener', 'province' => 'Ontario'],
    ['city' => 'Windsor', 'province' => 'Ontario'],
    ['city' => 'Barrie', 'province' => 'Ontario'],
    ['city' => 'Sudbury', 'province' => 'Ontario'],
    ['city' => 'Thunder Bay', 'province' => 'Ontario'],
];

// ─── Logging ───

function logPipeline($message, $type = 'INFO')
{
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$type] $message\n";

    $logFile = __DIR__ . '/logs/outreach_pipeline_' . date('Y-m-d') . '.log';
    if (!is_dir(__DIR__ . '/logs')) {
        mkdir(__DIR__ . '/logs', 0755, true);
    }
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

function log_activity_cli($pdo, $lead_id, $action_type, $details = null)
{
    $stmt = $pdo->prepare("INSERT INTO outreach_activity_log (lead_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->execute([$lead_id, $action_type, $details]);
}

// ─── Ensure outreach_pipeline_state table exists ───

function ensureStateTable($pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS outreach_pipeline_state (
        id INT AUTO_INCREMENT PRIMARY KEY,
        state_key VARCHAR(100) NOT NULL UNIQUE,
        state_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
}

function getState($pdo, $key, $default = null)
{
    $stmt = $pdo->prepare("SELECT state_value FROM outreach_pipeline_state WHERE state_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['state_value'] : $default;
}

function setState($pdo, $key, $value)
{
    $stmt = $pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES (?, ?)
        ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)");
    $stmt->execute([$key, $value]);
}

// ══════════════════════════════════════════════════════════════
//  MAIN PIPELINE
// ══════════════════════════════════════════════════════════════

logPipeline('=== Outreach Pipeline Starting ===');
if ($dryRun) logPipeline('DRY RUN MODE — no changes will be made');

try {
    global $pdo;
    ensureStateTable($pdo);

    // ─── STEP 1 & 2: Discover + Import ───
    if ($runAll || $discoverOnly) {
        stepDiscover($pdo, $dryRun);
    }

    // ─── STEP 3: Generate AI Drafts ───
    if ($runAll || $draftOnly) {
        stepGenerateDrafts($pdo, $dryRun);
    }

    // ─── STEP 4: Auto-Approve ───
    if (($runAll || $draftOnly) && AUTO_APPROVE) {
        stepAutoApprove($pdo, $dryRun);
    }

    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    logPipeline('=== Outreach Pipeline Complete ===');

} catch (Exception $e) {
    logPipeline("Pipeline fatal error: " . $e->getMessage(), 'ERROR');
    exit(1);
}

// ══════════════════════════════════════════════════════════════
//  STEP IMPLEMENTATIONS
// ══════════════════════════════════════════════════════════════

function stepDiscover($pdo, $dryRun)
{
    global $targetCities;

    $apiKey = $_ENV['GOOGLE_PLACES_API_KEY'] ?? '';
    if (empty($apiKey)) {
        logPipeline('Google Places API key not configured. Skipping discovery.', 'WARN');
        return;
    }

    // Determine which city to search next
    $cityIndex = (int) getState($pdo, 'current_city_index', '0');
    if ($cityIndex >= count($targetCities)) {
        // Wrap around to start — re-search cities for new businesses
        $cityIndex = 0;
        setState($pdo, 'current_city_index', '0');
        logPipeline('All cities searched. Wrapping around to start.');
    }

    $target = $targetCities[$cityIndex];
    $city = $target['city'];
    $province = $target['province'];

    logPipeline("--- Step 1: Discovery for $city, $province (city #" . ($cityIndex + 1) . "/" . count($targetCities) . ") ---");

    if ($dryRun) {
        logPipeline("[DRY RUN] Would search Google Places for businesses in $city, $province");
        setState($pdo, 'current_city_index', (string)($cityIndex + 1));
        return;
    }

    // Get existing place IDs to skip duplicates during search
    $stmt = $pdo->prepare("SELECT places_id FROM outreach_leads WHERE places_id IS NOT NULL AND places_id != ''");
    $stmt->execute();
    $existingPlaceIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'places_id');

    $result = search_businesses_core($city, $province, '', DISCOVERY_BATCH_SIZE, $apiKey, $existingPlaceIds);
    $businesses = $result['businesses'];
    $count = $result['count'];

    logPipeline("Discovered $count businesses with emails in $city (searched {$result['rounds']} round(s))");

    if (empty($businesses)) {
        logPipeline("No new businesses found in $city. Moving to next city.");
        setState($pdo, 'current_city_index', (string)($cityIndex + 1));
        return;
    }

    // Import discovered businesses
    $imported = 0;
    $skipped = 0;

    foreach ($businesses as $biz) {
        // Double-check dedup by places_id
        if (!empty($biz['places_id'])) {
            $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE places_id = ?");
            $check->execute([$biz['places_id']]);
            if ($check->fetch()) {
                $skipped++;
                continue;
            }
        }

        // Also dedup by email to avoid emailing same address twice
        if (!empty($biz['email'])) {
            $check = $pdo->prepare("SELECT id FROM outreach_leads WHERE email = ?");
            $check->execute([$biz['email']]);
            if ($check->fetch()) {
                $skipped++;
                continue;
            }
        }

        $stmt = $pdo->prepare("INSERT INTO outreach_leads
            (business_name, phone, website, address, category, city, source, places_id, contact_page_url, email)
            VALUES (?, ?, ?, ?, ?, ?, 'google_places_auto', ?, ?, ?)");
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
        log_activity_cli($pdo, $id, 'lead_created', "Auto-imported from Google Places ($city, $province)");
        $imported++;
    }

    logPipeline("Imported $imported new leads, skipped $skipped duplicates from $city");

    // Advance to next city for tomorrow
    setState($pdo, 'current_city_index', (string)($cityIndex + 1));
    setState($pdo, 'last_discovery_date', date('Y-m-d'));
    setState($pdo, 'last_discovery_city', "$city, $province");
}

function stepGenerateDrafts($pdo, $dryRun)
{
    logPipeline('--- Step 3: Generate AI Drafts ---');

    $openaiKey = $_ENV['OPENAI_API_KEY'] ?? '';
    if (empty($openaiKey)) {
        logPipeline('OpenAI API key not configured. Skipping draft generation.', 'WARN');
        return;
    }

    // Find leads that have an email but no draft yet
    $stmt = $pdo->prepare("
        SELECT *
        FROM outreach_leads
        WHERE email IS NOT NULL AND email != ''
          AND (draft_subject IS NULL OR draft_subject = '')
          AND sent_at IS NULL
          AND status NOT IN ('contacted', 'replied', 'interested', 'not_interested', 'onboarded')
        ORDER BY date_added ASC
        LIMIT ?
    ");
    $stmt->execute([DRAFT_BATCH_SIZE]);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        logPipeline('No leads need drafts. Skipping.');
        return;
    }

    logPipeline("Found " . count($leads) . " leads needing AI drafts");

    if ($dryRun) {
        foreach ($leads as $lead) {
            logPipeline("[DRY RUN] Would generate draft for: {$lead['business_name']} ({$lead['email']})");
        }
        return;
    }

    $success = 0;
    $failed = 0;

    foreach ($leads as $lead) {
        try {
            $result = generate_draft_for_lead($pdo, $lead);

            if (isset($result['error'])) {
                logPipeline("Draft generation failed for {$lead['business_name']}: {$result['error']}", 'ERROR');
                $failed++;
            } else {
                log_activity_cli($pdo, $lead['id'], 'draft_generated', 'AI draft auto-generated by pipeline');
                logPipeline("Draft generated for {$lead['business_name']}");
                $success++;
            }

            // Rate limit: pause between OpenAI calls
            sleep(1);

        } catch (Exception $e) {
            logPipeline("Draft error for {$lead['business_name']}: " . $e->getMessage(), 'ERROR');
            $failed++;
        }
    }

    logPipeline("Drafts generated: $success, failed: $failed");
}

function stepAutoApprove($pdo, $dryRun)
{
    logPipeline('--- Step 4: Auto-Approve Drafts ---');

    // Approve all leads that have a draft but haven't been approved yet
    $stmt = $pdo->prepare("
        SELECT id, business_name
        FROM outreach_leads
        WHERE draft_subject IS NOT NULL AND draft_subject != ''
          AND draft_body IS NOT NULL AND draft_body != ''
          AND email IS NOT NULL AND email != ''
          AND approval_status != 'approved'
          AND sent_at IS NULL
    ");
    $stmt->execute();
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        logPipeline('No drafts to approve.');
        return;
    }

    logPipeline("Found " . count($leads) . " drafts to auto-approve");

    if ($dryRun) {
        foreach ($leads as $lead) {
            logPipeline("[DRY RUN] Would auto-approve: {$lead['business_name']}");
        }
        return;
    }

    $count = 0;
    foreach ($leads as $lead) {
        $stmt = $pdo->prepare("UPDATE outreach_leads SET approval_status = 'approved' WHERE id = ?");
        $stmt->execute([$lead['id']]);
        log_activity_cli($pdo, $lead['id'], 'auto_approved', 'Draft auto-approved by pipeline');
        $count++;
    }

    logPipeline("Auto-approved $count drafts");
}

function stepSendEmails($pdo, $dryRun)
{
    logPipeline('--- Step 5: Send Emails ---');

    // Check how many already sent today
    $stmt = $pdo->prepare("SELECT COUNT(*) as sent_today FROM outreach_leads WHERE DATE(sent_at) = CURDATE()");
    $stmt->execute();
    $sentToday = (int) $stmt->fetch(PDO::FETCH_ASSOC)['sent_today'];

    $remaining = DAILY_SEND_LIMIT - $sentToday;

    if ($remaining <= 0) {
        logPipeline("Daily limit of " . DAILY_SEND_LIMIT . " emails already reached ($sentToday sent today). Skipping.");
        return;
    }

    logPipeline("Already sent $sentToday today. Will send up to $remaining more.");

    // Find approved leads with drafts that haven't been sent
    $stmt = $pdo->prepare("
        SELECT id, business_name, email, draft_subject, draft_body
        FROM outreach_leads
        WHERE approval_status = 'approved'
          AND draft_subject IS NOT NULL AND draft_subject != ''
          AND draft_body IS NOT NULL AND draft_body != ''
          AND email IS NOT NULL AND email != ''
          AND sent_at IS NULL
        ORDER BY date_added ASC
        LIMIT ?
    ");
    $stmt->execute([$remaining]);
    $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($leads)) {
        logPipeline('No approved leads ready to send.');
        return;
    }

    logPipeline("Found " . count($leads) . " approved leads to send");

    if ($dryRun) {
        foreach ($leads as $lead) {
            logPipeline("[DRY RUN] Would send email to: {$lead['business_name']} <{$lead['email']}>");
        }
        return;
    }

    $successCount = 0;
    $failCount = 0;

    foreach ($leads as $lead) {
        $id = $lead['id'];
        $businessName = $lead['business_name'];
        $email = $lead['email'];

        try {
            $htmlBody = '<p>' . nl2br(htmlspecialchars($lead['draft_body'])) . '</p>';

            $result = send_styled_email(
                $email,
                $lead['draft_subject'],
                $htmlBody,
                '',
                'contact@argorobots.com',
                'Argo Books',
                'contact@argorobots.com'
            );

            if ($result) {
                $stmt = $pdo->prepare("UPDATE outreach_leads SET
                    sent_at = NOW(),
                    status = CASE WHEN status NOT IN ('replied','interested','not_interested','onboarded') THEN 'contacted' ELSE status END,
                    first_contact_date = COALESCE(first_contact_date, NOW()),
                    last_contact_date = NOW()
                    WHERE id = ?");
                $stmt->execute([$id]);

                log_activity_cli($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email);
                logPipeline("Sent email to $businessName <$email> (lead #$id)");
                $successCount++;
            } else {
                log_activity_cli($pdo, $id, 'email_failed', 'Pipeline email send failed for: ' . $email);
                logPipeline("Failed to send email to $businessName <$email> (lead #$id)", 'ERROR');
                $failCount++;
            }

            // Brief pause between sends
            if ($successCount + $failCount < count($leads)) {
                sleep(2);
            }

        } catch (Exception $e) {
            log_activity_cli($pdo, $id, 'email_failed', 'Pipeline email error: ' . $e->getMessage());
            logPipeline("Error sending to $businessName <$email> (lead #$id): " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logPipeline("Send complete. Sent: $successCount, Failed: $failCount");
}
