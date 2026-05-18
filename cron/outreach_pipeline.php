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
 *   php outreach_pipeline.php --discover-only   # Only run discovery + import (Google Places + Shopify)
 *   php outreach_pipeline.php --shopify-only    # Only run Shopify discovery
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
require_once __DIR__ . '/lib/shopify_discovery.php';

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
// Follow-ups have their own daily cap, separate from first-touch sends.
// With the multi-touch sequence (touches 2 through N), this cap applies
// across ALL touch positions combined. Default 75 — raise via env var
// (OUTREACH_DAILY_FOLLOWUP_LIMIT) once domain reputation supports more.
define('DAILY_FOLLOWUP_LIMIT', (int) ($_ENV['OUTREACH_DAILY_FOLLOWUP_LIMIT'] ?? 75));
define('DAILY_DRAFT_LIMIT', (int) ($_ENV['OUTREACH_DAILY_DRAFT_LIMIT'] ?? 100));

// Parse CLI flags ($argv is null under CGI, fall back to empty array)
$args = array_slice($argv ?? [], 1);
$discoverOnly = in_array('--discover-only', $args);
$shopifyOnly = in_array('--shopify-only', $args);
$draftOnly = in_array('--draft-only', $args);
$sendOnly = in_array('--send-only', $args);
$dryRun = in_array('--dry-run', $args);
$runAll = !$discoverOnly && !$draftOnly && !$sendOnly && !$shopifyOnly;

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

    // ─── STEP 1b: Discover Shopify Stores (parallel discovery source) ───
    if ($runAll || $discoverOnly || $shopifyOnly) {
        stepDiscoverShopify($pdo, $dryRun);
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
    // ('auto' | 'review'). Defaults to 'auto' on a DB that hasn't had the
    // toggle set yet — admin can flip to review-mode in the Settings tab.
    $autoSendMode = getState($pdo, 'auto_send_mode', 'auto');
    if (($runAll || $draftOnly) && $autoSendMode === 'auto') {
        stepAutoApprove($pdo, $dryRun);
    } elseif (($runAll || $draftOnly) && $autoSendMode === 'review') {
        logPipeline('Send mode: review — drafts generated but auto-approve skipped.');
    }

    // ─── STEP 5: Send Emails ───
    if ($runAll || $sendOnly) {
        stepSendEmails($pdo, $dryRun);
    }

    // ─── STEP 5.5: Halt Follow-ups (replies / unsubscribes / bounces) ───
    // Also runs in --draft-only so we don't waste Gemini drafts on leads who
    // have already replied/unsubscribed/bounced since the last run.
    if ($runAll || $sendOnly || $draftOnly) {
        stepHaltFollowups($pdo, $dryRun);
    }

    // ─── STEP 5.6: Draft Follow-ups (Gemini, lazy ~1 day before send) ───
    // Always runs regardless of send mode — Drafting itself is harmless.
    // The review-vs-auto gating happens INSIDE stepDraftFollowups (which
    // advances drafted → approved only when auto_send_mode = 'auto').
    if ($runAll || $sendOnly || $draftOnly) {
        stepDraftFollowups($pdo, $dryRun);
    }

    // ─── STEP 6: Send Follow-ups ───
    // Step 6 always runs — review-vs-auto gating is implicit in row statuses.
    // (Review mode: rows stay 'drafted' awaiting admin approval — not picked
    // up by the WHERE status='approved' query.)
    if ($runAll || $sendOnly) {
        stepSendFollowups($pdo, $dryRun);
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

    // Prune expired scrape-cache rows so the table doesn't grow unbounded as
    // discovery walks new URLs. Matches the 30-day TTL used at read time —
    // older entries can never produce a hit anyway. Indexed scan, runs daily.
    try {
        $deleted = $pdo->exec("DELETE FROM outreach_scrape_cache
            WHERE last_attempted_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if ($deleted > 0) {
            logPipeline("Pruned $deleted expired scrape-cache rows.");
        }
    } catch (PDOException $e) {
        // Cleanup failure shouldn't fail the pipeline — table may not exist yet.
    }
}

function stepDiscoverShopify($pdo, $dryRun)
{
    logPipeline('--- Step 1b: Discover Shopify Stores ---');

    // ─── Guard 1: Feature flag ───
    $enabled = $_ENV['OUTREACH_SHOPIFY_ENABLED'] ?? 'false';
    if ($enabled !== 'true') {
        logPipeline('Shopify discovery is DISABLED via OUTREACH_SHOPIFY_ENABLED. Skipping.');
        return;
    }

    // ─── Guard 2: SerpAPI key ───
    $serpapiKey = $_ENV['SERPAPI_KEY'] ?? '';
    if (empty($serpapiKey)) {
        logPipeline('SERPAPI_KEY not configured. Skipping Shopify discovery.', 'WARN');
        return;
    }

    // ─── Guard 3: Daily counter reset ───
    $today = date('Y-m-d');
    $lastReset = getState($pdo, 'shopify_last_reset_date', '');
    if ($lastReset !== $today) {
        setState($pdo, 'shopify_imports_today', '0');
        setState($pdo, 'serpapi_calls_today', '0');
        setState($pdo, 'shopify_last_reset_date', $today);
        logPipeline("Daily Shopify counters reset for $today.");
    }

    // ─── Guard 4: SerpAPI daily limit ───
    $serpapiLimit = (int) ($_ENV['SERPAPI_DAILY_QUERY_LIMIT'] ?? 3);
    $serpapiCallsToday = (int) getState($pdo, 'serpapi_calls_today', '0');
    if ($serpapiCallsToday >= $serpapiLimit) {
        logPipeline("SerpAPI daily limit reached ($serpapiCallsToday/$serpapiLimit). Skipping Shopify discovery.");
        return;
    }

    // ─── Guard 5: Shopify import daily limit ───
    $shopifyImportLimit = (int) ($_ENV['OUTREACH_DAILY_SHOPIFY_DISCOVERY_LIMIT'] ?? 5);
    $shopifyImportsToday = (int) getState($pdo, 'shopify_imports_today', '0');
    if ($shopifyImportsToday >= $shopifyImportLimit) {
        logPipeline("Shopify import daily limit reached ($shopifyImportsToday/$shopifyImportLimit). Skipping Shopify discovery.");
        return;
    }

    // ─── Pick next dork query ───
    $dorkPool = SHOPIFY_DORK_POOL;
    $cursor = (int) getState($pdo, 'shopify_dork_cursor', '0');
    $query = $dorkPool[$cursor % count($dorkPool)];
    $nextCursor = $cursor + 1;
    setState($pdo, 'shopify_dork_cursor', (string) $nextCursor);

    logPipeline("Shopify discovery: dork cursor=$cursor, query='$query'");

    // ─── Dry run ───
    if ($dryRun) {
        logPipeline("[DRY RUN] Would call SerpAPI with query='$query', then evaluate up to $shopifyImportLimit Shopify candidates.");
        return;
    }

    // ─── Call SerpAPI ───
    $results = serpapi_query($query, $serpapiKey, 10);
    // Increment serpapi_calls_today regardless of result
    setState($pdo, 'serpapi_calls_today', (string) ($serpapiCallsToday + 1));

    if (empty($results)) {
        logPipeline("SerpAPI returned no results for query='$query'. Skipping candidate evaluation.");
        return;
    }

    logPipeline("SerpAPI returned " . count($results) . " results for query='$query'.");

    // ─── Insert candidates via INSERT IGNORE, collect newly-inserted ───
    $newCandidates = [];
    foreach ($results as $result) {
        $rawLink = $result['link'] ?? '';
        if ($rawLink === '') {
            continue;
        }
        $canonical = shopify_canonical_url($rawLink);
        if ($canonical === '') {
            continue;
        }

        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO outreach_shopify_candidates (canonical_url, status, last_query)
             VALUES (?, 'pending', ?)"
        );
        $stmt->execute([$canonical, $query]);

        if ($stmt->rowCount() === 1) {
            // Newly inserted — add to evaluation queue
            $newCandidates[] = $canonical;
        }
    }

    logPipeline("Inserted " . count($newCandidates) . " new candidates into outreach_shopify_candidates.");

    // ─── Evaluate newly-pending candidates ───
    $evaluated = 0;
    $imported  = 0;
    $rejected  = 0;
    $errored   = 0;

    foreach ($newCandidates as $canonical) {
        if ($shopifyImportsToday >= $shopifyImportLimit) {
            break;
        }

        $evaluated++;

        try {
            $evalResult = evaluate_shopify_candidate($canonical);
        } catch (Exception $e) {
            logPipeline("Error evaluating candidate '$canonical': " . $e->getMessage(), 'ERROR');
            $errored++;
            continue;
        }

        if ($evalResult['fit'] === true) {
            $meta      = $evalResult['metadata'];
            $email     = $meta['email'] ?? null;
            $finalUrl  = $evalResult['final_url'] ?? $canonical;

            // Dedup check: email or website already in outreach_leads
            $dupCheck = $pdo->prepare(
                "SELECT id FROM outreach_leads WHERE email = ? OR website = ? LIMIT 1"
            );
            $dupCheck->execute([$email, $finalUrl]);
            if ($dupCheck->fetch()) {
                // Mark candidate as rejected / duplicate
                $upd = $pdo->prepare(
                    "UPDATE outreach_shopify_candidates
                     SET status='rejected', reject_reason='duplicate'
                     WHERE canonical_url = ?"
                );
                $upd->execute([$canonical]);
                logPipeline("Shopify candidate '$canonical' skipped — already in outreach_leads (duplicate).");
                $rejected++;
                continue;
            }

            // Build business_summary
            $productsCount          = $meta['products_count'] ?? 0;
            $firstProductCreatedAt  = $meta['first_product_created_at'] ?? '';
            $businessSummary = "Shopify store. {$productsCount} products."
                . ($firstProductCreatedAt ? " First product created {$firstProductCreatedAt}." : '')
                . " Discovered via: {$query}";

            // Insert lead
            $insLead = $pdo->prepare(
                "INSERT INTO outreach_leads
                 (business_name, email, website, source, contact_page_url, business_summary)
                 VALUES (?, ?, ?, 'shopify_auto', ?, ?)"
            );
            $insLead->execute([
                $meta['business_name'] ?? $canonical,
                $email,
                $finalUrl,
                $finalUrl,
                $businessSummary,
            ]);
            $leadId = (int) $pdo->lastInsertId();

            // Update candidate as imported
            $updCand = $pdo->prepare(
                "UPDATE outreach_shopify_candidates
                 SET status='imported',
                     lead_id=?,
                     harvested_email=?,
                     products_count=?,
                     first_product_created_at=?,
                     detected_country=?,
                     myshopify_url=?
                 WHERE canonical_url = ?"
            );
            $updCand->execute([
                $leadId,
                $email,
                $productsCount,
                $firstProductCreatedAt ?: null,
                $meta['country'] ?? null,
                $canonical,
                $canonical,
            ]);

            log_activity($pdo, $leadId, 'lead_created', "Auto-imported from Shopify via dork: $query");

            $shopifyImportsToday++;
            setState($pdo, 'shopify_imports_today', (string) $shopifyImportsToday);

            logPipeline("Imported Shopify lead #$leadId: '{$meta['business_name']}' <$email> from $canonical");
            $imported++;

        } else {
            // Rejected — update candidate
            $rejectReason = $evalResult['reason'] ?? 'unknown';
            $rejectDetail = substr($evalResult['detail'] ?? '', 0, 500);

            $updRej = $pdo->prepare(
                "UPDATE outreach_shopify_candidates
                 SET status='rejected', reject_reason=?, reject_detail=?
                 WHERE canonical_url = ?"
            );
            $updRej->execute([$rejectReason, $rejectDetail, $canonical]);

            logPipeline("Shopify candidate '$canonical' rejected: $rejectReason — $rejectDetail");
            $rejected++;
        }
    }

    logPipeline(
        "Shopify discovery: query='$query', results=" . count($results)
        . ", evaluated=$evaluated, imported=$imported, rejected=$rejected, errored=$errored"
    );
}

function stepManageAbTests($pdo, $dryRun)
{
    logPipeline('--- Step 2.5: Manage A/B Tests ---');

    $enabled = getState($pdo, 'ab_auto_enabled', '1');
    if ($enabled !== '1') {
        logPipeline('A/B automation is OFF (ab_auto_enabled != 1). Skipping.');
        return;
    }

    // followup_sequence-specific shape check: if the active followup_sequence
    // test's variant intents no longer match the current followup_sequence_config
    // touch list (e.g. admin added a touch), auto-pause it.
    if (!$dryRun) {
        $shapeResult = check_followup_sequence_shape_match($pdo);
        if ($shapeResult['action'] === 'paused') {
            logPipeline($shapeResult['reason'] ?? 'followup_sequence test auto-paused for shape mismatch', 'WARN');
        }
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
        // get stuck. Other failure modes (DB error, Gemini down) are retried
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

    $geminiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    if (empty($geminiKey)) {
        logPipeline('Gemini API key not configured. Skipping draft generation.', 'WARN');
        return;
    }

    // Find leads that have an email but no draft yet
    $stmt = $pdo->prepare("
        SELECT *
        FROM outreach_leads
        WHERE email IS NOT NULL AND email != ''
          AND (draft_subject IS NULL OR draft_subject = '')
          AND sent_at IS NULL
          AND status NOT IN ('contacted', 'replied', 'interested', 'not_interested', 'onboarded', 'email_bounced')
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

            // Rate limit: pause between Gemini calls
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
    $skipCount = 0;

    foreach ($leads as $lead) {
        $id = $lead['id'];
        $businessName = $lead['business_name'];
        $email = $lead['email'];

        try {
            $reason = null;
            if (send_outreach_lead($pdo, $lead, $reason)) {
                $variantTag = !empty($lead['ab_variant_id'])
                    ? ' [A/B test #' . (int) $lead['ab_test_id'] . ', variant #' . (int) $lead['ab_variant_id'] . ']'
                    : '';
                log_activity($pdo, $id, 'email_sent', 'Outreach email sent automatically via pipeline to: ' . $email . $variantTag);
                logPipeline("Sent email to $businessName <$email> (lead #$id)" . $variantTag);

                // Schedule the multi-touch follow-up sequence (if any configured).
                // For the followup_sequence A/B assignment, we look up the active
                // followup_sequence test and pick a variant for this lead now —
                // separate from any A/B variant the first-touch is on (which is
                // usually subject/sender/format/etc., not followup_sequence).
                $fuVariantId = null;
                $fuTestId = null;
                $fuActive = $pdo->query("SELECT id FROM outreach_ab_tests WHERE variant_type = 'followup_sequence' AND status = 'active' LIMIT 1")->fetch();
                if ($fuActive) {
                    $fuTestId = (int) $fuActive['id'];
                    $fuVariants = $pdo->prepare("SELECT * FROM outreach_ab_variants WHERE test_id = ?");
                    $fuVariants->execute([$fuTestId]);
                    $fuVariantList = $fuVariants->fetchAll();
                    if (count($fuVariantList) >= 2) {
                        $picked = pick_ab_variant($pdo, ['id' => $fuTestId], $fuVariantList, $lead);
                        if ($picked) {
                            $fuVariantId = (int) $picked['id'];
                        }
                    }
                }
                $scheduled = schedule_followups_for_lead($pdo, $id, $fuTestId, $fuVariantId);
                if ($scheduled > 0) {
                    logPipeline("Scheduled $scheduled follow-up(s) for lead #$id" . ($fuVariantId ? " [followup A/B variant #$fuVariantId]" : ''));
                }

                $successCount++;
            } elseif ($reason === 'already_sent' || $reason === 'suppressed') {
                // Skip outcomes are already logged inside send_outreach_lead;
                // don't double-log as failures and don't count toward fail tally.
                logPipeline("Skipped $businessName <$email> (lead #$id): $reason");
                $skipCount++;
            } else {
                log_activity($pdo, $id, 'email_failed', 'Pipeline email send failed for: ' . $email . ' (' . ($reason ?? 'unknown') . ')');
                logPipeline("Failed to send email to $businessName <$email> (lead #$id): " . ($reason ?? 'unknown'), 'ERROR');
                $failCount++;
            }

            // Brief pause between sends
            if ($successCount + $failCount + $skipCount < count($leads)) {
                sleep(2);
            }

        } catch (Exception $e) {
            log_activity($pdo, $id, 'email_failed', 'Pipeline email error: ' . $e->getMessage());
            logPipeline("Error sending to $businessName <$email> (lead #$id): " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logPipeline("Send complete. Sent: $successCount, Failed: $failCount, Skipped: $skipCount");
}

function stepSendFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 6: Send Follow-ups ---');

    // Count how many follow-up sends have happened today (across all touch positions)
    $sentToday = (int) $pdo->query(
        "SELECT COUNT(*) FROM outreach_followups WHERE DATE(sent_at) = CURDATE()"
    )->fetchColumn();
    $remaining = DAILY_FOLLOWUP_LIMIT - $sentToday;

    if ($remaining <= 0) {
        logPipeline("Follow-up daily limit of " . DAILY_FOLLOWUP_LIMIT . " reached ($sentToday sent today). Skipping.");
        return;
    }

    $stmt = $pdo->prepare(
        "SELECT * FROM outreach_followups
         WHERE status = 'approved'
           AND scheduled_for <= NOW()
         ORDER BY scheduled_for ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, $remaining, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        logPipeline('No follow-ups ready to send.');
        return;
    }

    logPipeline('Found ' . count($rows) . ' follow-up(s) ready to send (cap remaining: ' . $remaining . ').');

    if ($dryRun) {
        foreach ($rows as $r) {
            logPipeline("[DRY RUN] Would send followup #{$r['id']} (lead #{$r['lead_id']}, touch {$r['touch_number']})");
        }
        return;
    }

    $successCount = 0;
    $failCount = 0;
    $skipCount = 0;

    foreach ($rows as $row) {
        try {
            $reason = null;
            if (send_followup_row($pdo, $row, $reason)) {
                logPipeline("Sent followup #{$row['id']} (lead #{$row['lead_id']}, touch {$row['touch_number']})");
                $successCount++;
            } elseif ($reason === 'not_eligible') {
                $skipCount++;
            } else {
                logPipeline("Failed to send followup #{$row['id']}: " . ($reason ?? 'unknown'), 'WARN');
                $failCount++;
            }

            if ($successCount + $failCount + $skipCount < count($rows)) {
                sleep(2);
            }
        } catch (Throwable $e) {
            logPipeline("Error sending followup #{$row['id']}: " . $e->getMessage(), 'ERROR');
            $failCount++;
        }
    }

    logPipeline("Follow-ups complete. Sent: $successCount, Failed: $failCount, Skipped: $skipCount");
}

function stepHaltFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 5.5: Halt Follow-ups ---');

    if ($dryRun) {
        // Count how many WOULD be halted, but don't write
        $countStmt = $pdo->query(
            "SELECT COUNT(*) FROM outreach_followups f
             JOIN outreach_leads l ON l.id = f.lead_id
             WHERE f.status IN ('scheduled','drafted','approved')
               AND (
                   l.status IN ('replied','interested','not_interested','onboarded','email_bounced')
                   OR EXISTS (SELECT 1 FROM email_suppressions s WHERE LOWER(s.email) = LOWER(l.email) AND s.context = 'outreach')
               )"
        );
        $count = (int) $countStmt->fetchColumn();
        logPipeline("[DRY RUN] Would halt $count follow-up row(s).");
        return;
    }

    $counts = halt_followups_bulk($pdo);
    $total = array_sum($counts);
    if ($total === 0) {
        logPipeline('No follow-ups halted.');
    } else {
        logPipeline("Halted $total follow-up(s): " . json_encode($counts));
    }
}

function stepDraftFollowups($pdo, $dryRun)
{
    logPipeline('--- Step 5.6: Draft Follow-ups ---');

    $geminiKey = $_ENV['GEMINI_API_KEY'] ?? '';
    if (empty($geminiKey)) {
        logPipeline('Gemini API key not configured. Skipping follow-up draft generation.', 'WARN');
        return;
    }

    // Find rows whose draft window has opened (scheduled_for within next 24h)
    $stmt = $pdo->prepare(
        "SELECT * FROM outreach_followups
         WHERE status = 'scheduled'
           AND scheduled_for <= DATE_ADD(NOW(), INTERVAL 1 DAY)
         ORDER BY scheduled_for ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, DAILY_DRAFT_LIMIT, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (empty($rows)) {
        logPipeline('No follow-ups need drafts. Skipping.');
        return;
    }

    logPipeline('Found ' . count($rows) . ' follow-up(s) needing AI drafts (cap: ' . DAILY_DRAFT_LIMIT . ').');

    if ($dryRun) {
        foreach ($rows as $r) {
            logPipeline("[DRY RUN] Would draft followup #{$r['id']} (lead #{$r['lead_id']}, touch {$r['touch_number']})");
        }
        return;
    }

    $autoSendMode = getState($pdo, 'auto_send_mode', 'auto');

    $success = 0;
    $failed = 0;
    foreach ($rows as $row) {
        try {
            $ok = draft_followup_via_gemini($pdo, $row);
            if ($ok) {
                $success++;
                // In auto-send mode, advance drafted → approved immediately
                if ($autoSendMode === 'auto') {
                    $pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE id = ? AND status = 'drafted'")
                        ->execute([(int) $row['id']]);
                }
            } else {
                $failed++;
            }
            sleep(1); // Rate-limit Gemini calls
        } catch (Throwable $e) {
            logPipeline("Draft followup error (followup #{$row['id']}): " . $e->getMessage(), 'ERROR');
            $failed++;
        }
    }

    logPipeline("Follow-up drafts: $success generated, $failed failed.");
}
