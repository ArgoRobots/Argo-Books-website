<?php
/**
 * CSV export of one user's entire telemetry history.
 *
 * Reached from the "Download CSV" button on each card of the app-stats User
 * Activity tab. Deliberately ignores that page's date range and tier filter:
 * the button means "everything this user ever sent", the same scope the Delete
 * button beside it operates on, so the file you get never depends on page state.
 *
 * Interpretation of each event is shared with the tab via user-activity-events.php
 * rather than duplicated, so the CSV always says what the screen says.
 */

require_once __DIR__ . '/../admin_session.php';
require_once __DIR__ . '/telemetry-dedupe.php';        // telemetry_is_duplicate_event()
require_once __DIR__ . '/user-activity-events.php';    // ua_describe_event() etc.

// Same guard as the parent page. This endpoint is a normal admin page as far as
// .htaccess is concerned (that only denies *-tab.php), so it must check for itself.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Forbidden');
}

// admin_session.php records every GET as the page to return to after re-login.
// A file download is not a page: landing on it post-login would hand the admin a
// CSV instead of a dashboard. Point it back at the tab the button lives on.
$_SESSION['admin_return_to'] = '/admin/app-stats/?tab=user-activity';

$authId = (string)($_GET['authId'] ?? '');
if ($authId === '') {
    http_response_code(400);
    exit('Missing authId');
}

// Collect candidate files exactly as the tab does: new location first, legacy root
// during the transition, de-duped by basename so a file present in both counts once.
$dirs  = [__DIR__ . '/../data-logs/telemetry/', __DIR__ . '/../data-logs/'];
$files = [];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    foreach (glob($dir . '*.json') ?: [] as $f) {
        $name = basename($f);
        if (!isset($files[$name])) {
            $files[$name] = $f;
        }
    }
}
ksort($files);

/**
 * Neutralizes a value that a spreadsheet would treat as a formula.
 *
 * Error messages now travel in telemetry and originate from uploaded files, so a
 * message could begin with =, +, - or @ and execute on open. Prefixing with a
 * single quote is the standard defence and is stripped back out by the spreadsheet
 * on display. Leading whitespace is trimmed first, because Excel looks past it.
 */
function ua_csv_safe($value): string
{
    if ($value === null || $value === false) return '';
    if ($value === true) return 'true';
    $s = (string)$value;
    if ($s !== '' && preg_match('/^[\s]*[=+\-@]/', $s)) {
        return "'" . $s;
    }
    return $s;
}

// ---- Gather this user's events ----------------------------------------------
// Rows are built in memory rather than streamed, because they are sorted newest
// first at the end and one user's history is small (thousands of rows at most).
$rows        = [];
$seenIds     = [];
$matchedFile = false;

foreach ($files as $name => $path) {
    $raw = @file_get_contents($path);
    if ($raw === false || trim($raw) === '') continue;
    $d = json_decode($raw, true);
    if (!is_array($d) || !isset($d['events']) || !is_array($d['events'])) continue;

    // The parameter is only ever compared against file contents. It never touches a
    // path, so no value of authId can reach a file outside data-logs/.
    if ((string)($d['authId'] ?? '') !== $authId) continue;
    $matchedFile = true;

    $geo = $d['geoLocation'] ?? [];
    $fileMeta = [
        'tier'        => $d['tier'] ?? 'premium',
        'app_version' => $d['appVersion'] ?? '',
        'platform'    => $d['platform'] ?? '',
        'country'     => $geo['country'] ?? '',
        'region'      => $geo['region'] ?? '',
        'timezone'    => $geo['timezone'] ?? '',
    ];

    foreach ($d['events'] as $ev) {
        // A re-uploaded event is the same action, not a second one. Matches the tab,
        // so the CSV row count agrees with the "Show all N events" figure.
        if (telemetry_is_duplicate_event($ev, $authId, $seenIds)) {
            continue;
        }
        $ev = ua_unwrap_event($ev);

        [$type, $text] = ua_describe_event($ev);
        $ts = isset($ev['timestamp']) ? strtotime($ev['timestamp']) : false;

        $rows[] = [
            'sort'             => $ts === false ? 0 : $ts,
            'timestamp_utc'    => $ts === false ? '' : gmdate('Y-m-d H:i:s', $ts),
            'event_type'       => $type,
            'description'      => $text,
            'severity'         => $ev['severity'] ?? '',
            'tier'             => $fileMeta['tier'],
            'app_version'      => $fileMeta['app_version'],
            'platform'         => $fileMeta['platform'],
            'country'          => $fileMeta['country'],
            'region'           => $fileMeta['region'],
            'timezone'         => $fileMeta['timezone'],
            'feature_name'     => $ev['featureName'] ?? '',
            'export_type'      => $ev['exportType'] ?? '',
            'api_name'         => $ev['apiName'] ?? '',
            'error_category'   => $ev['errorCategory'] ?? '',
            'error_code'       => $ev['errorCode'] ?? '',
            'message'          => $ev['message'] ?? '',
            'source_file'      => $ev['sourceFile'] ?? '',
            'line_number'      => $ev['lineNumber'] ?? '',
            'method_name'      => $ev['methodName'] ?? '',
            'duration_ms'      => $ev['durationMs'] ?? '',
            'duration_seconds' => $ev['durationSeconds'] ?? '',
            'clean'            => array_key_exists('clean', $ev) ? ($ev['clean'] ? 'true' : 'false') : '',
            'file_size'        => $ev['fileSize'] ?? '',
            'success'          => array_key_exists('success', $ev) ? ($ev['success'] ? 'true' : 'false') : '',
            'telemetry_file'   => $name,
        ];
    }
}

// An authId nobody uploaded under is a bad request, not an empty spreadsheet. A
// zero-byte CSV looks like the user genuinely did nothing, which is worse than an error.
if (!$matchedFile) {
    http_response_code(404);
    exit('No telemetry found for that user');
}

// Newest first, matching the on-screen timeline. Undated events sort to the bottom.
usort($rows, fn($a, $b) => $b['sort'] <=> $a['sort']);

// ---- Emit ---------------------------------------------------------------------
// authId contains a colon ('device:...' / 'subscription:...'), which is illegal in a
// Windows filename, and is long enough to be unwieldy. Keep it recognisable instead.
$slug = preg_replace('/[^A-Za-z0-9]+/', '-', $authId);
$slug = trim((string)$slug, '-');
if (strlen($slug) > 40) {
    $slug = substr($slug, 0, 40);
}
$filename = 'argo-activity-' . ($slug !== '' ? $slug : 'user') . '-' . gmdate('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$out = fopen('php://output', 'w');
// UTF-8 BOM so Excel reads accented company names and the '·' separator correctly
// instead of showing mojibake.
fwrite($out, "\xEF\xBB\xBF");

$columns = [
    'timestamp_utc', 'event_type', 'description', 'severity', 'tier', 'app_version',
    'platform', 'country', 'region', 'timezone', 'feature_name', 'export_type',
    'api_name', 'error_category', 'error_code', 'message', 'source_file',
    'line_number', 'method_name', 'duration_ms', 'duration_seconds', 'clean',
    'file_size', 'success', 'telemetry_file',
];
fputcsv($out, $columns);

foreach ($rows as $row) {
    $line = [];
    foreach ($columns as $col) {
        $line[] = ua_csv_safe($row[$col]);
    }
    fputcsv($out, $line);
}
fclose($out);
