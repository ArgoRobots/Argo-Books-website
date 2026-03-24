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

// ─── Lock file to prevent overlapping runs ───

$lockFile = __DIR__ . '/logs/outreach_pipeline.lock';
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}
$lockFp = fopen($lockFile, 'c');
if (!flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "Pipeline already running. Exiting.\n";
    exit(0);
}

// ─── Configuration ───

define('DAILY_SEND_LIMIT', (int) ($_ENV['OUTREACH_DAILY_SEND_LIMIT'] ?? 10));
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

// log_activity() is provided by cron/lib/outreach_helpers.php

// ─── Ensure outreach_pipeline_state table exists ───

function ensureStateTable($pdo)
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS outreach_pipeline_state (
        id INT AUTO_INCREMENT PRIMARY KEY,
        state_key VARCHAR(100) NOT NULL UNIQUE,
        state_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
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

// The category pool used for discovery. Defined here so the pipeline can
// cycle through them deterministically instead of randomly.
$discoveryCategoryPool = [
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

function stepDiscover($pdo, $dryRun)
{
    global $targetCities, $discoveryCategoryPool;

    $apiKey = $_ENV['GOOGLE_PLACES_API_KEY'] ?? '';
    if (empty($apiKey)) {
        logPipeline('Google Places API key not configured. Skipping discovery.', 'WARN');
        return;
    }

    $totalCategories = count($discoveryCategoryPool);

    // Determine which city to search next
    $cityIndex = (int) getState($pdo, 'current_city_index', '0');
    if ($cityIndex >= count($targetCities)) {
        // Wrap around to start — re-search cities for new businesses
        $cityIndex = 0;
        setState($pdo, 'current_city_index', '0');
        setState($pdo, 'current_city_category_offset', '0');
        logPipeline('All cities searched. Wrapping around to start.');
    }

    // Track which categories we've searched for the current city.
    // Each run searches 5 categories starting from this offset.
    // Only when we've cycled through ALL categories without finding
    // new leads do we consider the city truly exhausted.
    $categoryOffset = (int) getState($pdo, 'current_city_category_offset', '0');

    $target = $targetCities[$cityIndex];
    $city = $target['city'];
    $province = $target['province'];

    // Pick the next 5 categories to search (wrapping around the pool)
    $categoriesToSearch = [];
    for ($i = 0; $i < 5; $i++) {
        $categoriesToSearch[] = $discoveryCategoryPool[($categoryOffset + $i) % $totalCategories];
    }

    logPipeline("--- Step 1: Discovery for $city, $province (city #" . ($cityIndex + 1) . "/" . count($targetCities) . ", categories " . ($categoryOffset + 1) . "-" . ($categoryOffset + 5) . "/$totalCategories) ---");
    logPipeline("Searching categories: " . implode(', ', $categoriesToSearch));

    if ($dryRun) {
        logPipeline("[DRY RUN] Would search Google Places for businesses in $city, $province");
        return;
    }

    // Get existing place IDs to skip duplicates during search
    $stmt = $pdo->prepare("SELECT places_id FROM outreach_leads WHERE places_id IS NOT NULL AND places_id != ''");
    $stmt->execute();
    $existingPlaceIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'places_id');

    // Search each category individually and collect results
    $businesses = [];
    $roundsUsed = 0;

    foreach ($categoriesToSearch as $cat) {
        if (count($businesses) >= DAILY_SEND_LIMIT) break;

        $remaining = DAILY_SEND_LIMIT - count($businesses);
        $result = search_businesses_core($city, $province, $cat, $remaining, $apiKey, $existingPlaceIds);
        $roundsUsed++;

        if (isset($result['error'])) {
            logPipeline("API error searching '$cat' in $city: {$result['error']}", 'WARN');
            continue;
        }

        // Add new place IDs to exclude list so next category doesn't re-find them
        foreach ($result['businesses'] as $biz) {
            if (!empty($biz['places_id'])) {
                $existingPlaceIds[] = $biz['places_id'];
            }
            $businesses[] = $biz;
        }
    }

    logPipeline("Discovered " . count($businesses) . " businesses with emails in $city ($roundsUsed category searches)");

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
        log_activity($pdo, $id, 'lead_created', "Auto-imported from Google Places ($city, $province)");
        $imported++;
    }

    logPipeline("Imported $imported new leads, skipped $skipped duplicates from $city");

    // Advance the category offset for the next run
    $newOffset = $categoryOffset + 5;

    if ($newOffset >= $totalCategories) {
        // We've cycled through every category for this city
        if ($imported === 0) {
            // Went through all categories and found nothing new — city is exhausted
            logPipeline("All $totalCategories categories searched for $city with no new leads. City exhausted, advancing.");
            setState($pdo, 'current_city_index', (string)($cityIndex + 1));
            setState($pdo, 'current_city_category_offset', '0');
        } else {
            // Found some leads on the last pass — reset offset and search again
            logPipeline("Completed full category cycle for $city but still finding leads. Resetting categories.");
            setState($pdo, 'current_city_category_offset', '0');
        }
    } else {
        if ($imported > 0) {
            // Still finding leads, advance to next batch of categories
            logPipeline("Still finding leads in $city. Next run will search categories " . ($newOffset + 1) . "-" . min($newOffset + 5, $totalCategories) . ".");
            setState($pdo, 'current_city_category_offset', (string)$newOffset);
        } else {
            // This batch found nothing, but there are more categories to try
            logPipeline("No new leads from these categories, but " . ($totalCategories - $newOffset) . " categories remaining for $city.");
            setState($pdo, 'current_city_category_offset', (string)$newOffset);
        }
    }

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
    $stmt->execute([DAILY_SEND_LIMIT]);
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
                log_activity($pdo, $lead['id'], 'draft_generated', 'AI draft auto-generated by pipeline');
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
        log_activity($pdo, $lead['id'], 'auto_approved', 'Draft auto-approved by pipeline');
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
            if (send_outreach_lead($pdo, $lead)) {
                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email);
                logPipeline("Sent email to $businessName <$email> (lead #$id)");
                $successCount++;
            } else {
                log_activity($pdo, $id, 'email_failed', 'Pipeline email send failed for: ' . $email);
                logPipeline("Failed to send email to $businessName <$email> (lead #$id)", 'ERROR');
                $failCount++;
            }

            // Brief pause between sends
            if ($successCount + $failCount < count($leads)) {
                sleep(2);
            }

        } catch (Exception $e) {
            log_activity($pdo, $id, 'email_failed', 'Pipeline email error: ' . $e->getMessage());
            logPipeline("Error sending to $businessName <$email> (lead #$id): " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logPipeline("Send complete. Sent: $successCount, Failed: $failCount");
}
