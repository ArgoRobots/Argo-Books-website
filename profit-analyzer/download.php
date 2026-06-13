<?php
// profit-analyzer/download.php
//
// Streams the cleaned, multi-sheet .xlsx for the "Download organized
// spreadsheet" button. Under Option A (store nothing) the download is
// generated in-session from the just-analyzed data; here it is generated from
// the sample fixture as a stub until the real upload->analysis flow is wired.

require_once __DIR__ . '/lib/export.php';

$normalized = pa_load_fixture('maple-goods');
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
