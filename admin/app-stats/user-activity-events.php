<?php
/**
 * Shared interpretation of a single telemetry event.
 *
 * Two things read the same telemetry files and must agree about what an event
 * means: user-activity-tab.php, which renders the on-screen timeline, and
 * download-user.php, which exports it as CSV. Keeping these functions here is
 * what stops the download from drifting away from the screen it came from.
 *
 * Pulled out of user-activity-tab.php, which cannot be required from anywhere:
 * it is a partial that renders HTML on include, and admin/.htaccess denies it
 * over HTTP.
 */

if (!function_exists('ua_unwrap_event')) {
    /**
     * Returns the event payload itself. The compact upload format nests the real
     * event one level down under 'event'; the flat format is already the payload.
     * Every reader has to normalize this before touching a field, or the flat
     * readers silently see nothing on a nested file.
     */
    function ua_unwrap_event(array $ev): array
    {
        if (isset($ev['event']) && is_array($ev['event'])) {
            return $ev['event'];
        }
        return $ev;
    }
}

if (!function_exists('ua_is_warning')) {
    /**
     * True when an Error-dataType event is really a warning: an expected, handled
     * condition rather than a failure. The app stamps this from its own LogLevel.
     * Events uploaded before the field existed have no severity and stay errors,
     * which is deliberate: we don't guess severity from the error code.
     */
    function ua_is_warning(array $ev): bool
    {
        return strcasecmp((string)($ev['severity'] ?? ''), 'Warning') === 0;
    }
}

if (!function_exists('ua_describe_event')) {
    /**
     * Returns [type, text] for a single telemetry event. $type drives the colour
     * on screen and lands in the CSV's event_type column.
     */
    function ua_describe_event(array $ev): array
    {
        switch ($ev['dataType'] ?? '') {
            case 'Session':
                if (($ev['action'] ?? '') === 'SessionStart') {
                    return ['session', 'Session started'];
                }
                $dur = (int)($ev['durationSeconds'] ?? 0);
                $human = $dur >= 60 ? round($dur / 60, 1) . ' min' : $dur . 's';
                // Only an explicit false is unclean. Ends uploaded before the flag existed
                // have no value at all and must not be shown as if they'd been force-quit.
                // The duration on an unclean end is accurate to the app's heartbeat interval,
                // not to the moment it died.
                if (array_key_exists('clean', $ev) && $ev['clean'] === false) {
                    return ['unclean', "Session ended unexpectedly ({$human})"];
                }
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

            case 'CompanyProfile':
                $name = $ev['companyName'] ?? '(unnamed)';
                $bits = array_filter([
                    $ev['industry'] ?? null,
                    $ev['businessType'] ?? null,
                    $ev['country'] ?? null,
                    $ev['currency'] ?? null,
                ]);
                $suffix = $bits ? ' — ' . implode(', ', $bits) : '';
                // The demo company's details are ours, not theirs, so label them rather than
                // letting "Argo Robots Inc." read as a real user's business.
                $prefix = !empty($ev['isSample']) ? 'Sample company' : 'Company';
                return ['company', "{$prefix}: {$name}{$suffix}"];

            case 'Startup':
                $bits = [];
                if (isset($ev['toFirstPaintMs'])) $bits[] = 'blank screen ' . (int)$ev['toFirstPaintMs'] . ' ms';
                if (isset($ev['toReadyMs']))      $bits[] = 'ready ' . (int)$ev['toReadyMs'] . ' ms';
                if (array_key_exists('coldStart', $ev)) {
                    $bits[] = $ev['coldStart'] ? 'cold' : 'warm';
                }
                return ['startup', 'Launch' . ($bits ? ': ' . implode(', ', $bits) : '')];

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
                // The message carries the detail a warning needs to be actionable; error
                // rows already say enough via category + code + location.
                if (ua_is_warning($ev)) {
                    if (!empty($ev['message'])) $parts[] = $ev['message'];
                    return ['warning', 'Warning: ' . ($parts ? implode(' · ', $parts) : 'unknown')];
                }
                return ['error', 'Error: ' . ($parts ? implode(' · ', $parts) : 'unknown')];

            default:
                return ['other', $ev['dataType'] ?? 'Unknown'];
        }
    }
}
