<?php
/**
 * Follow-up smoke tests.
 *
 * Run via: php tests/Smoke/test_followups_smoke.php
 *
 * Inserts fake data into the real local DB, runs assertions, then cleans up.
 * DO NOT RUN AGAINST PRODUCTION. Aborts immediately if APP_ENV='production'.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

if (($_ENV['APP_ENV'] ?? '') === 'production') {
    fwrite(STDERR, "REFUSING TO RUN: APP_ENV='production'. Smoke tests touch live data.\n");
    exit(2);
}

require_once __DIR__ . '/../../db_connect.php';
require_once __DIR__ . '/../../cron/lib/outreach_helpers.php';
require_once __DIR__ . '/../../cron/lib/ab_helpers.php';

global $pdo;

$pass = 0;
$fail = 0;
$createdLeadIds = [];

function assert_true($cond, $msg) {
    global $pass, $fail;
    if ($cond) { echo "  ✓ $msg\n"; $pass++; }
    else       { echo "  ✗ $msg\n"; $fail++; }
}

function cleanup($pdo, &$createdLeadIds) {
    foreach ($createdLeadIds as $id) {
        $pdo->prepare("DELETE FROM outreach_leads WHERE id = ?")->execute([$id]);
    }
    $createdLeadIds = [];
}

// ─── Ensure config exists ───
$pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('followup_sequence_config', ?) ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
    ->execute([json_encode([
        ['touch' => 2, 'days_after_prev' => 3,  'default_intent' => 'gentle bump'],
        ['touch' => 3, 'days_after_prev' => 7,  'default_intent' => 'different angle'],
        ['touch' => 4, 'days_after_prev' => 14, 'default_intent' => 'final note before closing'],
    ])]);
$pdo->prepare("INSERT INTO outreach_pipeline_state (state_key, state_value) VALUES ('auto_send_mode', 'review') ON DUPLICATE KEY UPDATE state_value = VALUES(state_value)")
    ->execute();

// ─── Test 1: scheduling creates N rows ───
echo "Test 1: schedule_followups_for_lead creates 3 rows for default config\n";
$pdo->prepare("INSERT INTO outreach_leads (business_name, email, status, sent_at) VALUES ('Smoke1', 'smoke1@example.com', 'contacted', NOW())")->execute();
$leadId = (int) $pdo->lastInsertId();
$createdLeadIds[] = $leadId;

$n = schedule_followups_for_lead($pdo, $leadId);
assert_true($n === 3, "schedule_followups_for_lead returned 3 (got $n)");

$rows = $pdo->prepare("SELECT touch_number, scheduled_for, status FROM outreach_followups WHERE lead_id = ? ORDER BY touch_number");
$rows->execute([$leadId]);
$rowList = $rows->fetchAll(PDO::FETCH_ASSOC);
assert_true(count($rowList) === 3, "3 outreach_followups rows exist for lead");
assert_true($rowList[0]['touch_number'] == 2 && $rowList[0]['status'] === 'scheduled', "Touch 2 is scheduled");

// ─── Test 2: draft step picks up scheduled rows in the window ───
echo "Test 2: draft step picks up scheduled rows whose window has opened\n";
// Set touch 2 scheduled_for to NOW so window is open
$pdo->prepare("UPDATE outreach_followups SET scheduled_for = NOW() WHERE lead_id = ? AND touch_number = 2")->execute([$leadId]);

$inWindowStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_followups WHERE status = 'scheduled' AND scheduled_for <= DATE_ADD(NOW(), INTERVAL 1 DAY) AND lead_id = ?");
$inWindowStmt->execute([$leadId]);
$inWindowCount = (int) $inWindowStmt->fetchColumn();
assert_true($inWindowCount === 1, "Touch 2 (scheduled now) is in the draft window (count=$inWindowCount)");

// (Actual Gemini call not mocked here. The test just verifies the eligibility query.
// To smoke-test the full Gemini path, set GEMINI_API_KEY and run --dry-run separately.)

// ─── Test 3: halt step halts follow-ups when lead replies ───
echo "Test 3: halt step halts follow-ups when lead is in stop-condition status\n";
$pdo->prepare("UPDATE outreach_followups SET status = 'approved' WHERE lead_id = ?")->execute([$leadId]);
$pdo->prepare("UPDATE outreach_leads SET status = 'replied' WHERE id = ?")->execute([$leadId]);

$counts = halt_followups_bulk($pdo);
$haltedStmt = $pdo->prepare("SELECT COUNT(*) FROM outreach_followups WHERE lead_id = ? AND status = 'halted'");
$haltedStmt->execute([$leadId]);
$haltedCount = (int) $haltedStmt->fetchColumn();
assert_true($haltedCount === 3, "All 3 follow-ups halted after lead replied (got $haltedCount)");
$reasonsStmt = $pdo->prepare("SELECT DISTINCT halt_reason FROM outreach_followups WHERE lead_id = ?");
$reasonsStmt->execute([$leadId]);
$reasons = $reasonsStmt->fetchAll(PDO::FETCH_COLUMN);
assert_true(in_array('replied', $reasons, true), "halt_reason 'replied' present");

// ─── Test 4: atomic send claim, second concurrent claim loses ───
echo "Test 4: atomic claim, only one process can flip approved → sent\n";
$pdo->prepare("INSERT INTO outreach_leads (business_name, email, status, sent_at, unsubscribe_token) VALUES ('Smoke4', 'smoke4@example.com', 'contacted', NOW(), 'tok4')")->execute();
$leadId4 = (int) $pdo->lastInsertId();
$createdLeadIds[] = $leadId4;
$pdo->prepare("INSERT INTO outreach_followups (lead_id, touch_number, scheduled_for, status, draft_subject, draft_body) VALUES (?, 2, NOW(), 'approved', 'Re: Smoke4', 'body')")
    ->execute([$leadId4]);
$fuId = (int) $pdo->lastInsertId();

// First claim, should succeed
$claim1 = $pdo->prepare("UPDATE outreach_followups SET status = 'sent', sent_at = NOW() WHERE id = ? AND status = 'approved'");
$claim1->execute([$fuId]);
assert_true($claim1->rowCount() === 1, "First claim wins (rowCount=1)");

// Second claim, should lose (status is now 'sent', not 'approved')
$claim2 = $pdo->prepare("UPDATE outreach_followups SET status = 'sent', sent_at = NOW() WHERE id = ? AND status = 'approved'");
$claim2->execute([$fuId]);
assert_true($claim2->rowCount() === 0, "Second claim loses (rowCount=0)");

// ─── Cleanup ───
cleanup($pdo, $createdLeadIds);

echo "\n========================================\n";
echo "Result: $pass passed, $fail failed\n";
echo "========================================\n";

exit($fail === 0 ? 0 : 1);
