<?php
// profit-analyzer/download.php
//
// Streams the cleaned, multi-sheet .xlsx for the "Download organized
// spreadsheet" button. Under Option A (store nothing) the client holds the
// analyzed NormalizedData for the session and POSTs it here to build the file
// on demand. With no posted data (e.g. the sample-data demo) it falls back to
// the sample fixture.

require_once __DIR__ . '/lib/export.php';

$normalized = null;
$raw = file_get_contents('php://input');
if ($raw !== false && $raw !== '') {
    $body = json_decode($raw, true);
    if (is_array($body) && isset($body['normalized']) && is_array($body['normalized'])) {
        $normalized = pa_normalize($body['normalized']);
    }
}
if ($normalized === null) {
    $normalized = pa_load_fixture('maple-goods');
}
if ($normalized === null) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo 'Export unavailable.';
    exit;
}

$sheets = pa_build_workbook($normalized);
$bytes = pa_write_xlsx($sheets);

$filename = 'cleaned-' . date('Y-m-d') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($bytes));
header('Cache-Control: no-store');
echo $bytes;
