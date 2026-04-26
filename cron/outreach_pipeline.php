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

// Only allow CLI, or CGI cron (no REMOTE_ADDR means not a web request)
if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. This script can only be run via CLI/cron.');
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

// Parse CLI flags ($argv is null under CGI, fall back to empty array)
$args = array_slice($argv ?? [], 1);
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

    if (!isset($_SERVER['HTTP_HOST'])) {
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

    // ─── Master kill-switch: admin can disable the entire outreach system
    // from the Settings tab. When off, the server cron still fires but does
    // nothing until re-enabled.
    $outreachEnabled = getState($pdo, 'outreach_enabled', '1');
    if ($outreachEnabled !== '1') {
        logPipeline('Outreach is DISABLED via admin Settings (outreach_enabled != "1"). Pipeline exiting without running any steps.');
        return;
    }

    // ─── STEP 1 & 2: Discover + Import ───
    if ($runAll || $discoverOnly) {
        stepDiscover($pdo, $dryRun);
    }

    // ─── STEP 2.5: Manage A/B Tests (auto-promote winners, auto-create next cycle) ───
    if ($runAll || $draftOnly) {
        stepManageAbTests($pdo, $dryRun);
    }

    // ─── STEP 3: Generate AI Drafts ───
    if ($runAll || $draftOnly) {
        stepGenerateDrafts($pdo, $dryRun);
    }

    // ─── STEP 4: Auto-Approve ───
    // Runtime-toggled via outreach_pipeline_state.auto_send_mode
    // ('auto' | 'review'); falls back to the OUTREACH_AUTO_APPROVE env seed
    // on a DB that hasn't had the toggle set yet.
    $autoSendMode = getState($pdo, 'auto_send_mode', AUTO_APPROVE ? 'auto' : 'review');
    if (($runAll || $draftOnly) && $autoSendMode === 'auto') {
        stepAutoApprove($pdo, $dryRun);
    } elseif (($runAll || $draftOnly) && $autoSendMode === 'review') {
        logPipeline('Send mode: review — drafts generated but auto-approve skipped.');
    }

    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    logPipeline('=== Outreach Pipeline Complete ===');

} catch (Exception $e) {
    logPipeline("Pipeline fatal error: " . $e->getMessage(), 'ERROR');
    exit(1);
} finally {
    // Release lock file
    if (isset($lockFp) && is_resource($lockFp)) {
        flock($lockFp, LOCK_UN);
        fclose($lockFp);
    }
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

    $totalCategories = count(OUTREACH_CATEGORY_POOL);

    // Determine which city to search next
    $cityIndex = (int) getState($pdo, 'current_city_index', '0');
    if ($cityIndex >= count($targetCities)) {
        $cityIndex = 0;
        if (!$dryRun) {
            setState($pdo, 'current_city_index', '0');
            setState($pdo, 'current_city_category_offset', '0');
        }
        logPipeline('All cities searched. Wrapping around to start.');
    }

    // Track which categories we've searched for the current city. We keep
    // walking the pool starting from this offset and only stop when we've
    // collected DAILY_SEND_LIMIT businesses or cycled through every
    // category. Each run picks up where the last left off so the daily
    // cap actually gets hit instead of being limited by an arbitrary
    // categories-per-run constant.
    $categoryOffset = (int) getState($pdo, 'current_city_category_offset', '0');

    $target = $targetCities[$cityIndex];
    $city = $target['city'];
    $province = $target['province'];

    logPipeline("--- Step 1: Discovery for $city, $province (city #" . ($cityIndex + 1) . "/" . count($targetCities) . ", starting at category " . ($categoryOffset + 1) . "/$totalCategories) ---");

    if ($dryRun) {
        logPipeline("[DRY RUN] Would search Google Places for businesses in $city, $province until cap of " . DAILY_SEND_LIMIT . " is reached or all $totalCategories categories are exhausted");
        return;
    }

    // Get existing place IDs to skip duplicates during search
    $stmt = $pdo->prepare("SELECT places_id FROM outreach_leads WHERE places_id IS NOT NULL AND places_id != ''");
    $stmt->execute();
    $existingPlaceIds = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'places_id');

    // Walk the category pool from $categoryOffset, wrapping at the end. Stop
    // as soon as we have DAILY_SEND_LIMIT businesses OR have visited every
    // category in the pool (which signals the city is exhausted for this
    // pass). Pass maxRounds=1 since each category is already specific — we
    // don't need 5 query variations per category like the admin dashboard.
    $businesses = [];
    $categoriesSearched = 0;
    $apiErrors = 0;
    $categoriesUsed = [];

    for ($i = 0; $i < $totalCategories; $i++) {
        if (count($businesses) >= DAILY_SEND_LIMIT) break;

        $catIdx = ($categoryOffset + $i) % $totalCategories;
        $cat = OUTREACH_CATEGORY_POOL[$catIdx];
        $categoriesUsed[] = $cat;

        $remaining = DAILY_SEND_LIMIT - count($businesses);
        $result = search_businesses_core($city, $province, $cat, $remaining, $apiKey, $existingPlaceIds, 1);
        $categoriesSearched++;

        if (isset($result['error'])) {
            logPipeline("API error searching '$cat' in $city: {$result['error']}", 'WARN');
            $apiErrors++;
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

    // If every single API call failed, don't advance — retry same categories next run
    if ($apiErrors > 0 && $apiErrors === $categoriesSearched) {
        logPipeline("All $apiErrors API calls failed for $city. Will retry same categories next run.", 'ERROR');
        return;
    }

    logPipeline("Discovered " . count($businesses) . " businesses with emails in $city ($categoriesSearched category searches: " . implode(', ', $categoriesUsed) . "; $apiErrors errors)");

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

    // Advance the category offset by however many categories we actually
    // walked. If we hit the daily cap early, the pointer stays close to
    // where we stopped so the next run picks up at the next category. If
    // we cycled through the entire pool without hitting the cap, the city
    // is considered exhausted and we move on.
    $cycledThroughAll = ($categoriesSearched >= $totalCategories);

    if ($cycledThroughAll) {
        if ($imported === 0) {
            logPipeline("All $totalCategories categories searched for $city with no new leads. City exhausted, advancing.");
            setState($pdo, 'current_city_index', (string)($cityIndex + 1));
            setState($pdo, 'current_city_category_offset', '0');
        } else {
            logPipeline("Completed full category cycle for $city while still finding leads. Resetting offset for next run.");
            setState($pdo, 'current_city_category_offset', '0');
        }
    } else {
        $newOffset = ($categoryOffset + $categoriesSearched) % $totalCategories;
        setState($pdo, 'current_city_category_offset', (string)$newOffset);
        logPipeline("Hit daily cap after $categoriesSearched categories. Next run picks up at category " . ($newOffset + 1) . "/$totalCategories for $city.");
    }

    setState($pdo, 'last_discovery_date', date('Y-m-d'));
    setState($pdo, 'last_discovery_city', "$city, $province");
}

function stepManageAbTests($pdo, $dryRun)
{
    logPipeline('--- Step 2.5: Manage A/B Tests ---');

    $enabled = getState($pdo, 'ab_auto_enabled', '1');
    if ($enabled !== '1') {
        logPipeline('A/B automation is OFF (ab_auto_enabled != 1). Skipping.');
        return;
    }

    $allTypes = ab_known_variant_types();

    if ($dryRun) {
        $anyActive = false;
        foreach ($allTypes as $type) {
            $active = get_active_ab_test($pdo, $type);
            if ($active) {
                logPipeline("[DRY RUN] Would evaluate active $type test #{$active['test']['id']} '{$active['test']['name']}' for promotion.");
                $anyActive = true;
            }
        }
        if (!$anyActive) {
            $order = ab_auto_rotation_order();
            $next = getState($pdo, 'ab_auto_next_type', $order[0]);
            if (!in_array($next, $order, true)) $next = $order[0];
            logPipeline("[DRY RUN] Would create a new auto-cycle for variant_type '$next'.");
        }
        return;
    }

    // 1) Evaluate the active test of every known type for promotion. Only
    //    one test can be active at a time per the framework's invariant; the
    //    loop is type-agnostic so any wired type can promote cleanly.
    $anyStillRunning = false;
    $anyPaused = false;
    foreach ($allTypes as $type) {
        $evalResult = ab_check_and_promote_active_test($pdo, $type);
        $type = $evalResult['variant_type'] ?? $type;

        if ($evalResult['action'] === 'promoted') {
            $metric = $evalResult['metric'] ?? 'ctr';
            logPipeline(sprintf(
                "A/B auto-promoted winner: %s test #%d '%s' — variant %s wins (reply rate %.2f%%, CTR %.2f%%) via %s by %s after %d days.",
                $type,
                $evalResult['test_id'],
                $evalResult['test_name'],
                $evalResult['winner_label'],
                ($evalResult['winner_reply_rate'] ?? 0) * 100,
                $evalResult['winner_ctr'] * 100,
                $evalResult['trigger'],
                $metric,
                $evalResult['age_days']
            ));
        } elseif ($evalResult['action'] === 'paused_safety') {
            $metric = $evalResult['metric'] ?? 'ctr';
            $rate = $metric === 'reply_rate'
                ? ($evalResult['winner_reply_rate'] ?? 0)
                : $evalResult['winner_ctr'];
            logPipeline(sprintf(
                "A/B auto-paused for safety: %s test #%d promoted variant %s but %s %.2f%% fell below floor. ab_auto_enabled set to 0.",
                $type,
                $evalResult['test_id'],
                $evalResult['winner_label'],
                $metric === 'reply_rate' ? 'reply rate' : 'CTR',
                $rate * 100
            ), 'WARN');
            $anyPaused = true;
        } elseif ($evalResult['action'] === 'none' && ($evalResult['reason'] ?? '') === 'criteria_not_met') {
            logPipeline(sprintf(
                "A/B %s test #%d '%s' still running (age %d days, min sent %d) — no promotion criteria met yet.",
                $type,
                $evalResult['test_id'] ?? 0,
                $evalResult['test_name'] ?? '',
                $evalResult['age_days'] ?? 0,
                $evalResult['min_sent'] ?? 0
            ));
            $anyStillRunning = true;
        }
    }

    // Safety-pause short-circuits new-cycle creation across all types — admin
    // needs to flip A/B automation back on in Settings before anything moves.
    if ($anyPaused) return;

    // If any test is still running (any type), don't start a new cycle yet
    // — keep the one-active-test-at-a-time invariant.
    if ($anyStillRunning) return;

    // 2) Defensive backstop: never start a new cycle while ANY status='active'
    //    test row exists, even one the per-type promotion sweep couldn't
    //    evaluate (e.g. zero or one variant attached, which can't happen via
    //    the UI but could from direct DB edits). Two active tests would
    //    silently corrupt the one-active-test invariant.
    $activeCheck = $pdo->query("SELECT id, name, variant_type,
        (SELECT COUNT(*) FROM outreach_ab_variants v WHERE v.test_id = t.id) AS variant_count
        FROM outreach_ab_tests t WHERE status = 'active' ORDER BY id ASC LIMIT 1")->fetch();
    if ($activeCheck) {
        if ((int) $activeCheck['variant_count'] < 2) {
            logPipeline(sprintf(
                "A/B auto-cycle blocked: active %s test #%d '%s' is misconfigured (%d variant(s)). Admin intervention required.",
                $activeCheck['variant_type'],
                (int) $activeCheck['id'],
                $activeCheck['name'],
                (int) $activeCheck['variant_count']
            ), 'WARN');
        } else {
            logPipeline(sprintf(
                "A/B auto-cycle blocked: active %s test #%d '%s' still exists. Not creating a second active test.",
                $activeCheck['variant_type'],
                (int) $activeCheck['id'],
                $activeCheck['name']
            ));
        }
        return;
    }

    // 3) Auto-create the next cycle. The ab_auto_next_type pointer rotates
    //    across ab_auto_rotation_order() so the pipeline tests one lever,
    //    then the next, and so on. body / cta / preheader aren't in the
    //    rotation — they need crafted copy and stay admin-initiated.
    $order = ab_auto_rotation_order();
    $cycleType = getState($pdo, 'ab_auto_next_type', $order[0]);
    if (!in_array($cycleType, $order, true)) {
        $cycleType = $order[0];
    }

    $newCycle = ab_start_new_cycle($pdo, $cycleType);
    if ($newCycle['action'] === 'created') {
        logPipeline(sprintf(
            "A/B auto-created %s cycle: test #%d '%s' with %d variants (source: %s%s).",
            $cycleType,
            $newCycle['test_id'],
            $newCycle['test_name'],
            $newCycle['variant_count'],
            $newCycle['source'],
            $newCycle['carried_winner'] ? ', prior winner carried forward' : ''
        ));
        $idx = array_search($cycleType, $order, true);
        $advanced = $order[($idx + 1) % count($order)];
        setState($pdo, 'ab_auto_next_type', $advanced);
        logPipeline("A/B auto-rotation pointer advanced: next type will be '$advanced'.");
    } else {
        logPipeline('A/B auto-create failed for ' . $cycleType . ': ' . ($newCycle['error'] ?? 'unknown'), 'ERROR');
        // Advance the pointer past an unsupported type so rotation doesn't
        // get stuck. Other failure modes (DB error, OpenAI down) are retried
        // on the next run with the same pointer.
        if (strpos((string) ($newCycle['error'] ?? ''), 'unsupported') !== false) {
            $idx = array_search($cycleType, $order, true);
            $advanced = $order[($idx + 1) % count($order)];
            setState($pdo, 'ab_auto_next_type', $advanced);
            logPipeline("A/B auto-rotation skipped unsupported type '$cycleType'; pointer advanced to '$advanced'.");
        }
    }
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
        SELECT id, business_name, email, draft_subject, draft_body, unsubscribe_token, ab_test_id, ab_variant_id
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
                $variantTag = !empty($lead['ab_variant_id'])
                    ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
                    : '';
                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email . $variantTag);
                logPipeline("Sent email to $businessName <$email> (lead #$id)" . $variantTag);
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
