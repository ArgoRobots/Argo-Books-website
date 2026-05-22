<?php
/**
 * Reddit discovery cron.
 *
 * Runs daily at 8am (and manually via admin "Run discovery now").
 *
 * Pipeline:
 *   1. Pull last 24h from each enabled watchlist subreddit
 *   2. Run global Reddit search for each enabled keyword (t=day)
 *   3. Dedup by reddit_id (a thread hitting both sources gets discovery_source=both)
 *   4. Score each new thread (rules-based 0-100)
 *   5. Threads scoring < rules_score_floor → status='skipped'
 *   6. For passing threads: fetch top comments, run AI relevance check
 *   7. Threads with ai_relevance < ai_relevance_floor → status='skipped'
 *   8. Threads with ai_relevance ≥ 8 → pre-generate draft, status='drafted'
 *   9. Threads with ai_relevance 6-7 → status='drafted_pending' (on-demand draft)
 *  10. Auto-expire `drafted`/`drafted_pending` threads older than 3 days
 *  11. Update reddit_settings diagnostics
 *
 * Manual flags:
 *   --dry-run    Log what would happen without writing drafts or status changes
 *   --verbose    Print each step's progress to stdout
 */

set_time_limit(900); // 15 min max

// Allow execution from CLI/cron OR from an admin web request that has already
// authenticated and detached the response (sets REDDIT_MONITOR_INLINE).
if (!defined('REDDIT_MONITOR_INLINE') && php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die('Access denied. CLI/cron only.');
}

// Skip duplicate bootstrap when included from admin/outreach/api.php — the
// autoloader, env vars, and DB connection are already in scope. Loading
// Dotenv twice is safe (immutable) but cheaper to skip.
if (!defined('REDDIT_MONITOR_INLINE')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    require_once __DIR__ . '/../db_connect.php';
}
require_once __DIR__ . '/lib/outreach_helpers.php';
require_once __DIR__ . '/lib/reddit_helpers.php';

// ─── Lock file ───

$lockDir = __DIR__ . '/logs';
if (!is_dir($lockDir)) mkdir($lockDir, 0755, true);
$lockFile = $lockDir . '/reddit_monitor.lock';
$lockFp = fopen($lockFile, 'c');
if (!$lockFp || !flock($lockFp, LOCK_EX | LOCK_NB)) {
    echo "Reddit monitor already running. Exiting.\n";
    exit(0);
}

// ─── Args ───

$args = array_slice($argv ?? [], 1);
$dryRun = in_array('--dry-run', $args, true);
$verbose = in_array('--verbose', $args, true);

function rmon_say($msg) {
    global $verbose;
    if ($verbose) echo "[reddit_monitor] $msg\n";
    reddit_log($msg);
}

// ─── Pipeline state ───

$startedAt = date('Y-m-d H:i:s');
$threadsFound = 0;
$threadsDrafted = 0;
$lastError = null;

reddit_progress_reset([
    'message' => 'Starting discovery…',
    'started_at' => $startedAt,
]);

try {
    // Ensure singleton row + read floors
    $pdo->exec("INSERT IGNORE INTO reddit_settings (id) VALUES (1)");
    $cfg = $pdo->query("SELECT rules_score_floor, ai_relevance_floor FROM reddit_settings WHERE id = 1")->fetch();
    $rulesFloor = (int) ($cfg['rules_score_floor'] ?? 30);
    $aiFloor = (int) ($cfg['ai_relevance_floor'] ?? 6);

    // ─── Step 1+2: Discovery ───

    $subs = $pdo->query("SELECT name FROM reddit_subreddits WHERE enabled = 1 AND auto_disabled_at IS NULL")
                ->fetchAll(PDO::FETCH_COLUMN);
    $keywords = $pdo->query("SELECT keyword FROM reddit_keywords WHERE enabled = 1")->fetchAll(PDO::FETCH_COLUMN);

    rmon_say("Discovery: " . count($subs) . " subreddits, " . count($keywords) . " keywords");

    $discovered = []; // keyed by reddit_id

    foreach ($subs as $i => $sub) {
        reddit_progress_write(['message' => "Pulling r/$sub (" . ($i + 1) . '/' . count($subs) . ')…']);
        $threads = reddit_fetch_subreddit_new($pdo, $sub);
        rmon_say("  r/$sub → " . count($threads) . " threads");
        foreach ($threads as $t) {
            $rid = $t['reddit_id'];
            $discovered[$rid] = $t;
        }
    }

    foreach ($keywords as $i => $kw) {
        reddit_progress_write(['message' => "Searching \"$kw\" (" . ($i + 1) . '/' . count($keywords) . ')…']);
        $threads = reddit_fetch_search($pdo, $kw);
        rmon_say("  keyword \"$kw\" → " . count($threads) . " threads");
        foreach ($threads as $t) {
            $rid = $t['reddit_id'];
            if (isset($discovered[$rid])) {
                // Already from watchlist — upgrade to 'both' and append keyword
                $discovered[$rid]['discovery_source'] = 'both';
                $existing = $discovered[$rid]['matched_keywords'] ?? [];
                $discovered[$rid]['matched_keywords'] = array_values(array_unique(array_merge($existing, [$kw])));
            } else {
                // Keyword-only thread; check if subreddit IS in watchlist
                $inWatchlist = in_array($t['subreddit'], $subs, true);
                $t['discovery_source'] = $inWatchlist ? 'both' : 'keyword';
                $discovered[$rid] = $t;
            }
        }
    }

    rmon_say("Discovery total (deduplicated): " . count($discovered) . " threads");
    $threadsFound = count($discovered);
    reddit_progress_write(['found' => $threadsFound, 'message' => "Found $threadsFound threads, scoring…"]);

    // ─── Step 3-9: Score, AI relevance, draft (skip already-seen reddit_ids) ───

    $existingStmt = $pdo->prepare("SELECT 1 FROM reddit_threads WHERE reddit_id = ?");
    $insertStmt = $pdo->prepare("
        INSERT INTO reddit_threads
        (reddit_id, subreddit, title, body, url, author, post_score, comment_count,
         posted_at, discovery_source, matched_keywords, rules_score, ai_relevance,
         ai_relevance_reason, draft_body, draft_generated_at, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $processed = 0;
    $totalToProcess = count($discovered);

    foreach ($discovered as $rid => $t) {
        $processed++;
        $existingStmt->execute([$rid]);
        if ($existingStmt->fetchColumn()) continue; // already discovered earlier

        $rulesScore = reddit_score_thread($t);

        if ($rulesScore < $rulesFloor) {
            // Below rules floor → store as skipped, no AI call
            if (!$dryRun) {
                $insertStmt->execute([
                    $t['reddit_id'], $t['subreddit'], $t['title'], $t['body'], $t['url'],
                    $t['author'], $t['post_score'], $t['comment_count'], $t['posted_at'],
                    $t['discovery_source'], json_encode($t['matched_keywords']),
                    $rulesScore, null, null, null, null, 'skipped',
                ]);
            }
            rmon_say("  $rid skipped (rules_score=$rulesScore < $rulesFloor)");
            continue;
        }

        // Fetch top comments for relevance context
        $comments = reddit_fetch_top_comments($pdo, $t['subreddit'], $rid);

        reddit_progress_write(['message' => "AI scoring thread $processed/$totalToProcess (r/{$t['subreddit']})…"]);

        // AI relevance (passes $pdo so it can include the founder's recent
        // not_fit / replied labels as few-shot examples in the prompt).
        $rel = reddit_ai_relevance($t, $comments, $pdo);
        $aiScore = $rel['score'];
        $aiReason = $rel['reason'];

        if ($aiScore === null) {
            rmon_say("  $rid AI relevance failed: $aiReason — storing as new");
            // Persist so we don't re-process; status='new' lets retry on next run
            if (!$dryRun) {
                $insertStmt->execute([
                    $t['reddit_id'], $t['subreddit'], $t['title'], $t['body'], $t['url'],
                    $t['author'], $t['post_score'], $t['comment_count'], $t['posted_at'],
                    $t['discovery_source'], json_encode($t['matched_keywords']),
                    $rulesScore, null, null, null, null, 'new',
                ]);
            }
            continue;
        }

        if ($aiScore < $aiFloor) {
            if (!$dryRun) {
                $insertStmt->execute([
                    $t['reddit_id'], $t['subreddit'], $t['title'], $t['body'], $t['url'],
                    $t['author'], $t['post_score'], $t['comment_count'], $t['posted_at'],
                    $t['discovery_source'], json_encode($t['matched_keywords']),
                    $rulesScore, $aiScore, $aiReason, null, null, 'skipped',
                ]);
            }
            rmon_say("  $rid skipped (ai_relevance=$aiScore < $aiFloor)");
            continue;
        }

        // Passes both floors
        $draftBody = null;
        $draftGeneratedAt = null;
        $status = 'drafted_pending';

        if ($aiScore >= 8) {
            reddit_progress_write(['message' => "Generating draft for r/{$t['subreddit']} (relevance $aiScore)…"]);
            $draft = reddit_generate_draft($t, $comments);
            if (!empty($draft['body'])) {
                $draftBody = $draft['body'];
                $draftGeneratedAt = date('Y-m-d H:i:s');
                $status = 'drafted';
                $threadsDrafted++;
                reddit_progress_write(['drafted' => $threadsDrafted]);
                rmon_say("  $rid pre-drafted (ai_relevance=$aiScore)");
            } else {
                rmon_say("  $rid draft generation failed: " . ($draft['error'] ?? 'unknown') . ' — keeping as drafted_pending');
            }
        } else {
            rmon_say("  $rid drafted_pending (ai_relevance=$aiScore, will draft on demand)");
        }

        if (!$dryRun) {
            $insertStmt->execute([
                $t['reddit_id'], $t['subreddit'], $t['title'], $t['body'], $t['url'],
                $t['author'], $t['post_score'], $t['comment_count'], $t['posted_at'],
                $t['discovery_source'], json_encode($t['matched_keywords']),
                $rulesScore, $aiScore, $aiReason, $draftBody, $draftGeneratedAt, $status,
            ]);
        }
    }

    // ─── Step 10: Auto-expire stale drafts (> 3 days) ───

    if (!$dryRun) {
        $expired = $pdo->exec("
            UPDATE reddit_threads
            SET status = 'expired'
            WHERE status IN ('drafted', 'drafted_pending', 'new')
              AND discovered_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
        ");
        if ($expired > 0) rmon_say("Auto-expired $expired stale drafts");
    }

    // ─── Step 11: Update diagnostics ───

    if (!$dryRun) {
        $upd = $pdo->prepare("
            UPDATE reddit_settings
            SET last_run_at = ?, last_run_threads_found = ?, last_run_threads_drafted = ?, last_run_error = NULL
            WHERE id = 1
        ");
        $upd->execute([$startedAt, $threadsFound, $threadsDrafted]);
    }

    rmon_say("Done. found=$threadsFound drafted=$threadsDrafted");
    reddit_progress_write([
        'message' => "Done. Found $threadsFound threads, pre-drafted $threadsDrafted.",
        'completed' => true,
        'found' => $threadsFound,
        'drafted' => $threadsDrafted,
    ]);
    echo "Reddit monitor complete: $threadsFound threads found, $threadsDrafted pre-drafted.\n";
} catch (Throwable $e) {
    $lastError = $e->getMessage();
    reddit_log("Reddit monitor crashed: $lastError");
    reddit_progress_write([
        'message' => 'Crashed: ' . $lastError,
        'completed' => true,
        'error' => $lastError,
    ]);
    if (!$dryRun) {
        $upd = $pdo->prepare("UPDATE reddit_settings SET last_run_at = ?, last_run_error = ? WHERE id = 1");
        $upd->execute([$startedAt, $lastError]);
    }
    echo "Reddit monitor FAILED: $lastError\n";
    exit(1);
} finally {
    flock($lockFp, LOCK_UN);
    fclose($lockFp);
}
