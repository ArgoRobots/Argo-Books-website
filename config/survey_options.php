<?php
/**
 * Source Survey Options Configuration
 *
 * Single source of truth for the desktop app's post-onboarding
 * "Where did you hear about Argo Books?" survey options. Both the app-facing
 * endpoint (api/survey-options.php) and the answer receiver
 * (api/track-app-event.php) read from here, so adding/removing an option is a
 * single edit to config/survey-options.json with no other code changes.
 *
 * There is intentionally NO hardcoded fallback list here. The desktop app ships
 * its own bundled default list and uses it whenever this endpoint is unreachable
 * or returns a non-2xx, so the app is the single source of the offline fallback.
 * If the JSON cannot be read, accessors return null and callers degrade
 * accordingly (the options endpoint 500s; the receiver validates leniently).
 */

/**
 * Allowed format for an option key, shared by JSON parsing and the receiver's
 * degraded-mode validation so both stay consistent. Lowercase letters, digits,
 * underscores and hyphens; 2-40 chars.
 */
const SURVEY_KEY_PATTERN = '/^[a-z0-9_-]{2,40}$/';

/**
 * Returns the survey options as an array of {key, label, freeform?} maps, or
 * null if config/survey-options.json is missing/malformed. Cached per request.
 *
 * @return array<int, array{key:string,label:string,freeform?:bool}>|null
 */
function get_survey_options() {
    static $options = false; // false = not yet computed (null is a valid result)
    if ($options !== false) {
        return $options;
    }

    $options = null;

    $json = @file_get_contents(__DIR__ . '/survey-options.json');
    if ($json === false) {
        // Log so a broken deploy (missing JSON) is noticeable rather than silently
        // degrading to the app's bundled defaults + lenient server validation.
        error_log('survey_options: unable to read config/survey-options.json');
        return $options;
    }

    $data = json_decode($json, true);
    if (!is_array($data) || !isset($data['options']) || !is_array($data['options'])) {
        error_log('survey_options: config/survey-options.json is missing or malformed');
        return $options;
    }

    $parsed = [];
    foreach ($data['options'] as $o) {
        if (!is_array($o) || empty($o['key']) || !isset($o['label'])) {
            continue;
        }
        // Normalize and validate the key so a config typo fails fast (and visibly)
        // instead of producing a key the app can never match.
        $key = strtolower(trim((string)$o['key']));
        if (!preg_match(SURVEY_KEY_PATTERN, $key)) {
            error_log('survey_options: skipping option with invalid key: ' . json_encode($o['key']));
            continue;
        }
        $entry = ['key' => $key, 'label' => (string)$o['label']];
        if (!empty($o['freeform'])) {
            $entry['freeform'] = true;
        }
        $parsed[] = $entry;
    }

    if (count($parsed) > 0) {
        $options = $parsed;
    } else {
        error_log('survey_options: config/survey-options.json contained no valid options');
    }

    return $options;
}

/**
 * Returns the valid answer keys (lowercased), or null if options are unavailable.
 *
 * @return string[]|null
 */
function get_survey_option_keys() {
    $opts = get_survey_options();
    if ($opts === null) {
        return null;
    }
    $keys = [];
    foreach ($opts as $o) {
        $keys[] = strtolower($o['key']);
    }
    return $keys;
}

/**
 * Returns the option keys (lowercased) that carry freeform text, or null if
 * options are unavailable.
 *
 * @return string[]|null
 */
function get_survey_freeform_keys() {
    $opts = get_survey_options();
    if ($opts === null) {
        return null;
    }
    $keys = [];
    foreach ($opts as $o) {
        if (!empty($o['freeform'])) {
            $keys[] = strtolower($o['key']);
        }
    }
    return $keys;
}
