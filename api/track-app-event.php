<?php
/**
 * Desktop-app telemetry receiver for funnel events.
 *
 * The Avalonia app POSTs here on first launch after install:
 *   {
 *     "token":       "8c4e2f1a",        // HMAC token from installer filename
 *     "event":       "app_first_run",
 *     "platform":    "win|mac|linux",
 *     "app_version": "2.1.0",
 *     "machine_uuid": "<stable per-machine id>"
 *   }
 *
 * Verification path:
 *   1. Compute the expected HMAC for each visitor_id that has a recent landing
 *      event and compare to the submitted token. The match yields the
 *      originating visitor_id, which we use to write the new event.
 *   2. If no visitor matches, we still log the event with visitor_id=null so
 *      the funnel sees "first run without attribution" rather than dropping it.
 *
 * Dedupes on (visitor_id|null, machine_uuid) so a retry from the app doesn't
 * double-count.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

require_once __DIR__ . '/../db_connect.php';
require_once __DIR__ . '/../track_referral_event.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

$event_type   = (string)($data['event']        ?? '');
$token        = (string)($data['token']        ?? '');
$platform     = (string)($data['platform']     ?? '');
$app_version  = (string)($data['app_version']  ?? '');
$machine_uuid = (string)($data['machine_uuid'] ?? '');

if ($event_type !== 'app_first_run') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Unsupported event']);
    exit;
}

if (!preg_match('/^[a-z]{3,8}$/', $platform)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid platform']);
    exit;
}

/**
 * Resolve a visitor_id from an installer token by recomputing the HMAC for
 * every distinct visitor_id seen in the last ~14 days. Returns null on miss
 * (untokenized installer, manually renamed file, etc).
 */
function resolve_visitor_from_token(string $token): ?string
{
    global $pdo;
    $secret = $_ENV['REFERRAL_TOKEN_SECRET'] ?? '';
    if ($secret === '' || !preg_match('/^[0-9a-f]{8}$/i', $token)) {
        return null;
    }
    // Limit the scan to recent visitors so this stays fast as the table grows.
    $stmt = $pdo->prepare(
        'SELECT DISTINCT visitor_id FROM referral_events
          WHERE event_type IN ("landing","downloads_page","download_click")
            AND created_at >= NOW() - INTERVAL 14 DAY'
    );
    $stmt->execute();
    while ($row = $stmt->fetch()) {
        $candidate = $row['visitor_id'];
        $expected = substr(hash_hmac('sha256', $candidate, $secret), 0, 8);
        if (hash_equals($expected, strtolower($token))) {
            return $candidate;
        }
    }
    return null;
}

$visitor_id = $token !== '' ? resolve_visitor_from_token($token) : null;

// Resolve source_code from the most recent landing event for this visitor
$source_code = null;
if ($visitor_id !== null) {
    try {
        $stmt = $pdo->prepare(
            "SELECT source_code FROM referral_events
              WHERE visitor_id = ? AND event_type = 'landing' AND source_code IS NOT NULL
              ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute([$visitor_id]);
        $row = $stmt->fetch();
        if ($row !== false) {
            $source_code = $row['source_code'];
        }
    } catch (PDOException $e) {
        error_log('track-app-event source lookup failed: ' . $e->getMessage());
    }
}

// Dedup: skip if we've already logged a first_run for this machine.
// When visitor_id is null (token didn't resolve), dedupe by machine_uuid
// alone so retries from an untokenized installer don't create multiple rows.
if ($machine_uuid !== '') {
    try {
        if ($visitor_id !== null) {
            $dedup = $pdo->prepare(
                "SELECT 1 FROM referral_events
                  WHERE event_type = 'app_first_run'
                    AND visitor_id = ?
                    AND JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.machine_uuid')) = ?
                  LIMIT 1"
            );
            $dedup->execute([$visitor_id, $machine_uuid]);
        } else {
            $dedup = $pdo->prepare(
                "SELECT 1 FROM referral_events
                  WHERE event_type = 'app_first_run'
                    AND visitor_id IS NULL
                    AND JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.machine_uuid')) = ?
                  LIMIT 1"
            );
            $dedup->execute([$machine_uuid]);
        }
        if ($dedup->fetch() !== false) {
            echo json_encode(['success' => true, 'duplicate' => true]);
            exit;
        }
    } catch (PDOException $e) {
        // Continue — duplicate detection failure shouldn't drop the event.
        error_log('track-app-event dedup check failed: ' . $e->getMessage());
    }
}

$ok = track_referral_event('app_first_run', [
    'visitor_id'  => $visitor_id,
    'source_code' => $source_code,
    'event_data'  => [
        'platform'     => $platform,
        'app_version'  => $app_version,
        'machine_uuid' => $machine_uuid,
        'token_match'  => $visitor_id !== null,
    ],
    'allow_bot' => true,  // desktop app HTTP client has no browser UA
]);

echo json_encode(['success' => (bool)$ok]);
