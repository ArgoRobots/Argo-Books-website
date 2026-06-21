<?php
// User Activity tab body, included by admin/app-stats/index.php inside the
// #user-activity tab. Admin auth + page chrome are handled by the host page.
//
// Telemetry lives in JSON files, NOT the database. New uploads land in
// data-logs/telemetry/; the legacy data-logs/ root is still read during the
// transition. Files are named argo_data_{tier}_{date}_{rand}.json and each is
// tagged at the top level with {tier, authId} by api/data/upload.php. This tab
// groups every file by user, shows what each user did, and lets you delete a
// user's files (e.g. your own test installs) one card at a time.

// Direct-access guard. This partial is only valid when included by its parent
// page (app-stats/index.php), which starts the session and verifies the admin
// login. Requested directly, no session is started so $_SESSION is empty and we
// fail closed. (An admin/.htaccess also denies *-tab.php as defense in depth.)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Forbidden');
}

$ua_dataDir   = __DIR__ . '/../data-logs/telemetry/';
$ua_legacyDir = __DIR__ . '/../data-logs/';

// Collect files, de-duping by basename so a file in both dirs counts once.
$ua_files = [];
$ua_seen  = [];
foreach ([$ua_dataDir, $ua_legacyDir] as $dir) {
    if (!is_dir($dir)) continue;
    foreach (glob($dir . '*.json') ?: [] as $f) {
        $name = basename($f);
        if (!isset($ua_seen[$name])) {
            $ua_seen[$name] = true;
            $ua_files[$name] = $f; // map basename -> full path
        }
    }
}

// ---- Delete action: removes the exact files posted from a card. -------------
// Matching by filename (not authId) so legacy files with no authId can be
// deleted too. Each posted name is basename()'d and must already be in the
// collected $ua_files map, so nothing outside data-logs/ can be touched.
$ua_flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['del_files'])) {
    $label   = (string)($_POST['del_label'] ?? '');
    $deleted = 0;
    foreach ((array)$_POST['del_files'] as $reqName) {
        $base = basename((string)$reqName);
        if (isset($ua_files[$base]) && @unlink($ua_files[$base])) {
            $deleted++;
            unset($ua_files[$base]);
        }
    }
    $ua_flash = "Deleted {$deleted} file(s) for " . htmlspecialchars($label) . ".";
}

// ---- Per-event description helper -------------------------------------------
// Returns [type, text] for a single telemetry event. $type drives the colour.
function ua_describe_event(array $ev): array
{
    switch ($ev['dataType'] ?? '') {
        case 'Session':
            if (($ev['action'] ?? '') === 'SessionStart') {
                return ['session', 'Session started'];
            }
            $dur = (int)($ev['durationSeconds'] ?? 0);
            $human = $dur >= 60 ? round($dur / 60, 1) . ' min' : $dur . 's';
            return ['session', "Session ended ({$human})"];

        case 'FeatureUsage':
            $name = $ev['featureName'] ?? 'Unknown';
            $extra = !empty($ev['durationMs']) ? ' (' . (int)$ev['durationMs'] . ' ms)' : '';
            return ['feature', $name . $extra];

        case 'Export':
            $type = $ev['exportType'] ?? 'Unknown';
            $bits = [];
            if (!empty($ev['fileSize']))   $bits[] = number_format((int)$ev['fileSize']) . ' bytes';
            if (!empty($ev['durationMs'])) $bits[] = (int)$ev['durationMs'] . ' ms';
            $suffix = $bits ? ' (' . implode(', ', $bits) . ')' : '';
            return ['export', "Export: {$type}{$suffix}"];

        case 'ApiUsage':
            $api = $ev['apiName'] ?? 'Unknown';
            $ok  = array_key_exists('success', $ev) ? ($ev['success'] ? 'ok' : 'FAILED') : '';
            $bits = [];
            if ($ok !== '')                $bits[] = $ok;
            if (!empty($ev['durationMs'])) $bits[] = (int)$ev['durationMs'] . ' ms';
            $suffix = $bits ? ' (' . implode(', ', $bits) . ')' : '';
            return ['api', "API: {$api}{$suffix}"];

        case 'Error':
            $parts = [];
            if (!empty($ev['errorCategory'])) $parts[] = $ev['errorCategory'];
            if (!empty($ev['errorCode']))     $parts[] = 'code=' . $ev['errorCode'];
            if (!empty($ev['methodName']))    $parts[] = $ev['methodName'] . '()';
            if (!empty($ev['sourceFile'])) {
                $loc = $ev['sourceFile'];
                if (!empty($ev['lineNumber'])) $loc .= ':' . $ev['lineNumber'];
                $parts[] = $loc;
            }
            return ['error', 'Error: ' . ($parts ? implode(' · ', $parts) : 'unknown')];

        default:
            return ['other', $ev['dataType'] ?? 'Unknown'];
    }
}

// ---- Aggregate per authId ---------------------------------------------------
$ua_users = []; // authId => aggregate
foreach ($ua_files as $name => $path) {
    $raw = file_get_contents($path);
    if ($raw === false || trim($raw) === '') continue;
    $d = json_decode($raw, true);
    if (!is_array($d) || !isset($d['events']) || !is_array($d['events'])) continue;

    $tier   = $d['tier'] ?? 'premium';
    $authId = $d['authId'] ?? '(no authId)';
    $geo    = $d['geoLocation'] ?? [];

    if (!isset($ua_users[$authId])) {
        $ua_users[$authId] = [
            'tier'      => $tier,
            'authId'    => $authId,
            'platforms' => [],
            'versions'  => [],
            'country'   => $geo['country'] ?? '',
            'region'    => $geo['region'] ?? '',
            'timezone'  => $geo['timezone'] ?? '',
            'first'     => null,
            'last'      => null,
            'sessions'  => 0,
            'events'    => 0,
            'features'  => [],   // featureName => count
            'exports'   => [],   // exportType  => count
            'apis'      => [],   // apiName     => count
            'errors'    => 0,
            'timeline'  => [],   // every event: ['ts','type','text']
            'files'     => [],
        ];
    }
    $u =& $ua_users[$authId];
    $u['files'][] = $name;
    if (!empty($d['platform']))   $u['platforms'][$d['platform']] = true;
    if (!empty($d['appVersion'])) $u['versions'][$d['appVersion']] = true;
    if (empty($u['country'])  && !empty($geo['country']))  $u['country']  = $geo['country'];
    if (empty($u['region'])   && !empty($geo['region']))   $u['region']   = $geo['region'];
    if (empty($u['timezone']) && !empty($geo['timezone'])) $u['timezone'] = $geo['timezone'];

    foreach ($d['events'] as $ev) {
        $u['events']++;
        $ts = isset($ev['timestamp']) ? strtotime($ev['timestamp']) : false;
        if ($ts !== false) {
            if ($u['first'] === null || $ts < $u['first']) $u['first'] = $ts;
            if ($u['last']  === null || $ts > $u['last'])  $u['last']  = $ts;
        }

        switch ($ev['dataType'] ?? '') {
            case 'Session':
                if (($ev['action'] ?? '') === 'SessionStart') $u['sessions']++;
                break;
            case 'FeatureUsage':
                $f = $ev['featureName'] ?? 'Unknown';
                $u['features'][$f] = ($u['features'][$f] ?? 0) + 1;
                break;
            case 'Export':
                $e = $ev['exportType'] ?? 'Unknown';
                $u['exports'][$e] = ($u['exports'][$e] ?? 0) + 1;
                break;
            case 'ApiUsage':
                $a = $ev['apiName'] ?? 'Unknown';
                $u['apis'][$a] = ($u['apis'][$a] ?? 0) + 1;
                break;
            case 'Error':
                $u['errors']++;
                break;
        }

        [$evType, $evText] = ua_describe_event($ev);
        $u['timeline'][] = ['ts' => ($ts !== false ? $ts : 0), 'type' => $evType, 'text' => $evText];
    }
    unset($u);
}

// Sort users: free first (what you care about), then most-recent activity.
uasort($ua_users, function ($a, $b) {
    if ($a['tier'] !== $b['tier']) return $a['tier'] === 'free' ? -1 : 1;
    return ($b['last'] ?? 0) <=> ($a['last'] ?? 0);
});

if (!function_exists('ua_fmt')) {
    // gmdate() renders in UTC regardless of the server's timezone, so the "UTC"
    // label is always accurate. Telemetry timestamps arrive as UTC (Z-suffixed).
    function ua_fmt($ts) { return $ts ? gmdate('Y-m-d H:i', $ts) . ' UTC' : '—'; }
}
if (!function_exists('ua_kv')) {
    function ua_kv($arr) {
        if (!$arr) return '<span style="color:var(--black)">none</span>';
        arsort($arr);
        $out = [];
        foreach ($arr as $k => $v) $out[] = htmlspecialchars($k) . ' <b>' . $v . '</b>';
        return implode(', ', $out);
    }
}
?>
<style>
.ua-intro { color:var(--black); margin-bottom:1rem; }
.ua-flash { background:#ecfdf5; border:1px solid #6ee7b7; color:#065f46; padding:10px 14px; border-radius:8px; margin-bottom:1rem; }
.ua-card { border:1px solid #e5e7eb; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1rem; background:#fff; }
.ua-card h3 { margin:0 0 .25rem; font-family:monospace; font-size:1rem; word-break:break-all; }
.ua-badge { display:inline-block; font-size:.7rem; font-weight:700; text-transform:uppercase; padding:2px 8px; border-radius:999px; margin-left:.5rem; vertical-align:middle; }
.ua-badge.free { background:#dbeafe; color:#1e40af; }
.ua-badge.premium { background:#fef3c7; color:#92400e; }
.ua-meta { color:var(--black); font-size:.85rem; margin:.4rem 0; }
.ua-meta span { display:inline-block; margin-right:1.25rem; }
.ua-row { font-size:.85rem; margin:.25rem 0; }
.ua-row b { color:var(--black); }
.ua-files { font-family:monospace; font-size:.7rem; color:var(--black); margin-top:.5rem; word-break:break-all; }
.ua-del { float:right; }
.ua-del button { background:#ef4444; color:#fff; border:0; border-radius:6px; padding:6px 12px; font-size:.8rem; cursor:pointer; }
.ua-del button:hover { background:#dc2626; }
.ua-events { margin-top:.6rem; }
.ua-events > summary { cursor:pointer; font-size:.85rem; font-weight:600; color:#2563eb; }
.ua-timeline { margin-top:.5rem; max-height:340px; overflow-y:auto; border:1px solid #e5e7eb; border-radius:8px; }
.ua-evt { display:flex; gap:.75rem; padding:5px 12px; font-size:.78rem; border-bottom:1px solid #f3f4f6; }
.ua-evt:last-child { border-bottom:0; }
.ua-evt-ts { color:var(--black); font-family:monospace; white-space:nowrap; }
.ua-evt-text { color:var(--black); }
.ua-evt.error  .ua-evt-text { color:#b91c1c; font-family:monospace; }
.ua-evt.api    .ua-evt-text { color:#7c3aed; }
.ua-evt.export .ua-evt-text { color:#0369a1; }
.ua-evt.feature .ua-evt-text { color:#047857; }
.ua-evt.session .ua-evt-text { color:var(--black); }
[data-theme="dark"] .ua-card { background:var(--gray-800); border-color:var(--gray-700); }
[data-theme="dark"] .ua-card h3, [data-theme="dark"] .ua-meta, [data-theme="dark"] .ua-row, [data-theme="dark"] .ua-row b, [data-theme="dark"] .ua-evt-text { color:var(--white); }
[data-theme="dark"] .ua-timeline { border-color:var(--gray-700); }
[data-theme="dark"] .ua-evt { border-bottom-color:var(--gray-700); }
</style>

<h2 class="section-title">User Activity</h2>

<?php if ($ua_flash): ?><div class="ua-flash"><?= $ua_flash ?></div><?php endif; ?>

<?php if (!$ua_users): ?>
    <p>No telemetry files found.</p>
<?php else: foreach ($ua_users as $u):
    // Newest-first timeline for display.
    $timeline = $u['timeline'];
    usort($timeline, fn($a, $b) => $b['ts'] <=> $a['ts']);
?>
    <div class="ua-card">
        <div class="ua-del">
            <form method="post" action="?tab=user-activity"
                  onsubmit="return confirm('Delete ALL <?= count($u['files']) ?> file(s) for this user? This cannot be undone.');">
                <input type="hidden" name="del_label" value="<?= htmlspecialchars($u['authId']) ?>">
                <?php foreach ($u['files'] as $fn): ?>
                    <input type="hidden" name="del_files[]" value="<?= htmlspecialchars($fn) ?>">
                <?php endforeach; ?>
                <button type="submit">Delete this user</button>
            </form>
        </div>
        <h3><?= htmlspecialchars($u['authId']) ?><span class="ua-badge <?= $u['tier'] ?>"><?= htmlspecialchars($u['tier']) ?></span></h3>
        <div class="ua-meta">
            <span><b>Platform:</b> <?= htmlspecialchars(implode(', ', array_keys($u['platforms'])) ?: '—') ?></span>
            <span><b>Country:</b> <?= htmlspecialchars($u['country'] ?: '—') ?></span>
            <span><b>Region:</b> <?= htmlspecialchars($u['region'] ?: '—') ?></span>
            <span><b>Timezone:</b> <?= htmlspecialchars($u['timezone'] ?: '—') ?></span>
            <span><b>Version:</b> <?= htmlspecialchars(implode(', ', array_keys($u['versions'])) ?: '—') ?></span>
        </div>
        <div class="ua-meta">
            <span><b>First seen:</b> <?= ua_fmt($u['first']) ?></span>
            <span><b>Last seen:</b> <?= ua_fmt($u['last']) ?></span>
            <span><b>Sessions:</b> <?= $u['sessions'] ?></span>
            <span><b>Total events:</b> <?= $u['events'] ?></span>
            <span><b>Errors:</b> <?= $u['errors'] ?></span>
        </div>
        <div class="ua-row"><b>Features used:</b> <?= ua_kv($u['features']) ?></div>
        <div class="ua-row"><b>Exports:</b> <?= ua_kv($u['exports']) ?></div>
        <div class="ua-row"><b>API calls:</b> <?= ua_kv($u['apis']) ?></div>

        <?php if ($timeline): ?>
        <details class="ua-events">
            <summary>Show all <?= count($timeline) ?> events</summary>
            <div class="ua-timeline">
                <?php foreach ($timeline as $t): ?>
                    <div class="ua-evt <?= $t['type'] ?>">
                        <span class="ua-evt-ts"><?= $t['ts'] ? gmdate('Y-m-d H:i:s', $t['ts']) . ' UTC' : '—' ?></span>
                        <span class="ua-evt-text"><?= htmlspecialchars($t['text']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
        <?php endif; ?>

        <div class="ua-files"><?= count($u['files']) ?> file(s): <?= htmlspecialchars(implode(', ', $u['files'])) ?></div>
    </div>
<?php endforeach; endif; ?>
