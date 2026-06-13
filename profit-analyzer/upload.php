<?php
// profit-analyzer/upload.php
//
// Upload endpoint for the Profit Analyzer. Validates the file, enforces the
// per-IP rate limit, runs the analysis, and returns the computed result.
//
// The analysis step (file -> NormalizedData) is the ONLY piece deferred until
// the desktop importer stabilizes; here it is a stub returning sample data so
// the whole pipeline (upload -> validate -> rate-limit -> analytics -> delete)
// runs end to end. Swap pa_analyze() for the real port when it lands.

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../rate_limit_helper.php';
require_once __DIR__ . '/lib/analytics.php';

const PA_MAX_BYTES = 5 * 1024 * 1024;       // 5 MB
const PA_DAILY_LIMIT = 5;                    // analyses per IP per day
const PA_WINDOW = 86400;                     // 24h
const PA_ALLOWED_EXT = ['xlsx', 'csv'];

function pa_fail(int $code, string $error, string $message, array $extra = []): void
{
    http_response_code($code);
    echo json_encode(array_merge(['ok' => false, 'error' => $error, 'message' => $message], $extra));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    pa_fail(405, 'method_not_allowed', 'Use POST to upload a file.');
}

// --- Rate limit (the limit message IS the pitch). Check only here; we record
// AFTER a successful analysis so failed/invalid uploads (which never hit the
// paid AI) don't burn a user's daily quota. ---
$ip = get_client_ip();
if (is_rate_limited($ip, PA_DAILY_LIMIT, PA_WINDOW, 'profit_analyzer')) {
    pa_fail(429, 'rate_limited',
        "You've used your free analyses for today. Want unlimited? Try Argo Books free.",
        ['cta' => '/downloads/?source=profit-analyzer-limit&utm_source=profit-analyzer&utm_medium=tool']);
}

// --- File validation ---
$file = $_FILES['file'] ?? null;
if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    pa_fail(400, 'no_file', 'No spreadsheet was uploaded.');
}
if (($file['error'] ?? 0) !== UPLOAD_ERR_OK) {
    pa_fail(400, 'upload_error', 'The upload did not complete. Please try again.');
}
if (($file['size'] ?? 0) > PA_MAX_BYTES) {
    pa_fail(413, 'too_large', 'That file is over 5 MB. Trim it down or split it and try again.');
}
$ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
if (!in_array($ext, PA_ALLOWED_EXT, true)) {
    pa_fail(415, 'bad_type', 'Please upload an .xlsx or .csv spreadsheet.');
}

// Move out of PHP's transient upload slot so we control deletion.
$tmp = tempnam(sys_get_temp_dir(), 'pa_');
if (!$tmp || !move_uploaded_file($file['tmp_name'], $tmp)) {
    pa_fail(500, 'io_error', 'Could not read the uploaded file. Please try again.');
}

try {
    $normalized = pa_analyze($tmp, $ext);          // DEFERRED step (stub for now)
    $analytics = pa_compute_analytics($normalized);
} catch (Throwable $e) {
    @unlink($tmp);
    pa_fail(422, 'unreadable', "We couldn't make sense of that spreadsheet. Try a cleaner export.");
}

// Delete the raw upload immediately after analysis (Option A: store nothing).
@unlink($tmp);

// Only successful analyses count toward the daily quota.
record_rate_limit_attempt($ip, 'profit_analyzer', PA_WINDOW);

echo json_encode(['ok' => true, 'analytics' => $analytics]);

/**
 * DEFERRED: turn an uploaded spreadsheet into NormalizedData. Real implementation
 * (PHP port of the desktop importer's analysis: PhpSpreadsheet read + Gemini via
 * api/ai/completions.php + column mapping + Tier-2) replaces this stub body. The
 * contract it must return is documented in lib/contract.php.
 */
function pa_analyze(string $path, string $ext): array
{
    $sample = pa_load_fixture('maple-goods');
    if ($sample === null) {
        throw new RuntimeException('analysis unavailable');
    }
    return $sample;
}
