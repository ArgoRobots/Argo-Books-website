<?php
/**
 * One-shot backfill that applies the new outreach auto-filters to leads
 * already in the database. Marks chain/franchise/government/role-mailbox/AI-
 * classified-chain leads as status='disqualified' so they won't be drafted
 * or sent.
 *
 * Why: prior to the filters in cron/lib/outreach_helpers.php, Google Places
 * discovery returned things like the Air Canada Maple Leaf Lounge and
 * scraped their phishing.hameconnage@aircanada.ca contact. Those rows are
 * still sitting in the database with status='draft_generated' (or 'new'),
 * ready to send. This script catches them retroactively.
 *
 * Layers applied (cheapest first, AI gate last):
 *   - chain_domain      filter_chain_domain on website OR email
 *   - role_mailbox      filter_gatekept_email on the scraped email
 *   - ai_*              classify_lead_size_with_ai (chain_or_corp / uncertain)
 *
 * Place-type and review-count filters are NOT re-checked: those signals
 * weren't stored at import time, and re-querying Google Places per lead
 * would cost real money. The AI gate covers the gap for those.
 *
 * Usage:
 *   php cron/backfill_outreach_filters.php              # do it
 *   php cron/backfill_outreach_filters.php --dry-run    # report only
 *   php cron/backfill_outreach_filters.php --no-ai      # skip Layer 3
 *   php cron/backfill_outreach_filters.php --limit=50   # cap rows touched
 *
 * Safe to re-run: disqualify_lead() is idempotent.
 */

set_time_limit(0);

if (php_sapi_name() !== 'cli' && !empty($_SERVER['REMOTE_ADDR'])) {
    http_response_code(403);
    die("CLI only.\n");
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/lib/outreach_helpers.php';

// CLI args
$args = array_slice($argv ?? [], 1);
$dryRun = in_array('--dry-run', $args, true);
$noAi   = in_array('--no-ai', $args, true);
$limit  = null;
foreach ($args as $a) {
    if (preg_match('/^--limit=(\d+)$/', $a, $m)) {
        $limit = (int) $m[1];
    }
}

global $pdo;

// Only touch leads that could still be drafted or sent. Skip anything
// already in a terminal state (contacted/replied/onboarded/etc.).
$where = "status IN ('new', 'draft_generated', 'approved')
          AND sent_at IS NULL";
$sql = "SELECT id, business_name, email, website, address, city, category, source, status
        FROM outreach_leads
        WHERE $where
        ORDER BY id ASC";
if ($limit !== null && $limit > 0) {
    $sql .= " LIMIT " . $limit;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = count($leads);
echo "Scanning $total active leads (dry_run=" . ($dryRun ? 'yes' : 'no')
    . ", no_ai=" . ($noAi ? 'yes' : 'no') . ").\n";

$counters = [
    'chain_domain_website' => 0,
    'chain_domain_email'   => 0,
    'role_mailbox'         => 0,
    'ai_chain_or_corp'     => 0,
    'ai_uncertain'         => 0,
    'ai_skipped_error'     => 0,
    'kept'                 => 0,
];

foreach ($leads as $lead) {
    $id = (int) $lead['id'];
    $name = $lead['business_name'] ?? '(unnamed)';
    $email = $lead['email'] ?? '';
    $website = $lead['website'] ?? '';

    // ─── Layer 1: cheap deterministic checks ───

    if (!empty($website) && filter_chain_domain($website)) {
        echo "  DISQUALIFY id=$id chain_domain_website: $name <{$website}>\n";
        $counters['chain_domain_website']++;
        if (!$dryRun) {
            disqualify_lead($pdo, $id, 'chain_domain_website',
                "Website domain matches mega-brand/government blocklist: {$website}");
        }
        continue;
    }

    if (!empty($email) && filter_chain_domain($email)) {
        echo "  DISQUALIFY id=$id chain_domain_email: $name <{$email}>\n";
        $counters['chain_domain_email']++;
        if (!$dryRun) {
            disqualify_lead($pdo, $id, 'chain_domain_email',
                "Email domain matches mega-brand/government blocklist: {$email}");
        }
        continue;
    }

    // ─── Layer 2: role-mailbox ───

    if (!empty($email) && filter_gatekept_email($email)) {
        echo "  DISQUALIFY id=$id role_mailbox: $name <{$email}>\n";
        $counters['role_mailbox']++;
        if (!$dryRun) {
            disqualify_lead($pdo, $id, 'role_mailbox',
                "Email is a role mailbox (support/abuse/phishing/etc.): {$email}");
        }
        continue;
    }

    // ─── Layer 3: AI size gate ───

    if ($noAi) {
        $counters['kept']++;
        continue;
    }

    $gate = classify_lead_size_with_ai($lead);
    if (isset($gate['error'])) {
        echo "  KEEP id=$id (AI error: {$gate['error']}): $name\n";
        $counters['ai_skipped_error']++;
        continue;
    }

    if ($gate['classification'] === 'chain_or_corp') {
        echo "  DISQUALIFY id=$id ai_chain_or_corp: $name -- {$gate['reason']}\n";
        $counters['ai_chain_or_corp']++;
        if (!$dryRun) {
            disqualify_lead($pdo, $id, 'ai_chain_or_corp',
                'AI size gate: ' . $gate['reason']);
        }
        // Throttle Gemini a bit so we don't burn quota on a big backfill.
        usleep(750000); // 0.75s
        continue;
    }

    if ($gate['classification'] === 'uncertain') {
        echo "  DISQUALIFY id=$id ai_uncertain: $name -- {$gate['reason']}\n";
        $counters['ai_uncertain']++;
        if (!$dryRun) {
            disqualify_lead($pdo, $id, 'ai_uncertain',
                'AI size gate: ' . $gate['reason']);
        }
        usleep(750000);
        continue;
    }

    $counters['kept']++;
    usleep(750000);
}

echo "\n=== Backfill summary ===\n";
echo "Scanned:                  $total\n";
foreach ($counters as $key => $count) {
    echo str_pad($key, 25) . " $count\n";
}
if ($dryRun) {
    echo "\nDry run: no rows were modified. Re-run without --dry-run to apply.\n";
}
