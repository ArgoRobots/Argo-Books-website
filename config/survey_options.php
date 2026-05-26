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
 * Options live in config/survey-options.json (data, no PHP). This file provides
 * accessors with a hardcoded fallback so the survey keeps working if the JSON
 * is missing or malformed.
 */

/**
 * Default options, used when survey-options.json cannot be read or parsed.
 * Keys must stay in sync with the app's bundled SourceSurveyOptionsService.DefaultOptions.
 *
 * @return array<int, array{key:string,label:string,freeform?:bool}>
 */
function _survey_default_options() {
    return [
        ['key' => 'google',      'label' => 'Google'],
        ['key' => 'bing',        'label' => 'Bing'],
        ['key' => 'youtube',     'label' => 'YouTube'],
        ['key' => 'reddit',      'label' => 'Reddit'],
        ['key' => 'friend',      'label' => 'A friend'],
        ['key' => 'email',       'label' => 'Email'],
        ['key' => 'capterra',    'label' => 'Capterra'],
        ['key' => 'producthunt', 'label' => 'Product Hunt'],
        ['key' => 'other',       'label' => 'Other', 'freeform' => true],
    ];
}

/**
 * Returns the survey options as an array of {key, label, freeform?} maps.
 * Reads config/survey-options.json; falls back to defaults on any failure.
 * Result is cached per request.
 *
 * @return array<int, array{key:string,label:string,freeform?:bool}>
 */
function get_survey_options() {
    static $options = null;
    if ($options !== null) {
        return $options;
    }

    $options = _survey_default_options();

    $json = @file_get_contents(__DIR__ . '/survey-options.json');
    if ($json !== false) {
        $data = json_decode($json, true);
        if (is_array($data) && isset($data['options']) && is_array($data['options'])) {
            $parsed = [];
            foreach ($data['options'] as $o) {
                if (!is_array($o) || empty($o['key']) || !isset($o['label'])) {
                    continue;
                }
                $entry = ['key' => (string)$o['key'], 'label' => (string)$o['label']];
                if (!empty($o['freeform'])) {
                    $entry['freeform'] = true;
                }
                $parsed[] = $entry;
            }
            if (count($parsed) > 0) {
                $options = $parsed;
            }
        }
    }

    return $options;
}

/**
 * Returns the set of valid answer keys (lowercased) for server-side validation.
 *
 * @return string[]
 */
function get_survey_option_keys() {
    $keys = [];
    foreach (get_survey_options() as $o) {
        $keys[] = strtolower($o['key']);
    }
    return $keys;
}

/**
 * Returns the set of option keys (lowercased) that carry freeform text.
 *
 * @return string[]
 */
function get_survey_freeform_keys() {
    $keys = [];
    foreach (get_survey_options() as $o) {
        if (!empty($o['freeform'])) {
            $keys[] = strtolower($o['key']);
        }
    }
    return $keys;
}
