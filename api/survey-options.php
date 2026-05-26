<?php
/**
 * Source Survey Options Endpoint
 *
 * GET /api/survey-options.php
 *
 * Returns the option list shown in the desktop app's post-onboarding
 * "Where did you hear about Argo Books?" survey. Options are defined in
 * config/survey-options.json so a new option (e.g. a new platform) can be
 * added without releasing a new app version.
 *
 * The app falls back to a bundled default list when this endpoint is
 * unreachable or returns a non-2xx.
 *
 * Response (200):
 *   {
 *     "options": [
 *       { "key": "google", "label": "Google" },
 *       ...
 *       { "key": "other", "label": "Other", "freeform": true }
 *     ]
 *   }
 */

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/survey_options.php';

// Allow brief client/proxy caching; option changes propagate within minutes.
header('Cache-Control: public, max-age=300');
echo json_encode(['options' => get_survey_options()]);
