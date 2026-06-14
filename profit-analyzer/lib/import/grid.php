<?php
// profit-analyzer/lib/import/grid.php
//
// The data-extraction helpers from SpreadsheetAnalysisService (FindHeaderRow,
// GetHeaders, GetAllRowsAsStrings, GetSampleIndices) re-expressed over a plain
// string matrix, so CSV and XLSX share one downstream path. A "sheet" struct is:
//   { name, headers[], dataRows[][], sampleRows[][], totalRows, matrix[][], merges[] }

const PA_SAMPLE_FIRST = 5;
const PA_SAMPLE_LAST = 3;
const PA_SAMPLE_RANDOM = 5;

/** Port of FindHeaderRow: first of the top 10 rows with >= 2 non-empty cells (0-based); else 0. */
function pa_grid_find_header_row(array $matrix): int
{
    $last = min(count($matrix), 10);
    for ($r = 0; $r < $last; $r++) {
        $nonEmpty = 0;
        foreach ($matrix[$r] as $cell) {
            if (trim((string)$cell) !== '') {
                $nonEmpty++;
                if ($nonEmpty >= 2) {
                    return $r;
                }
            }
        }
    }
    return 0;
}

/** Port of GetHeaders: placeholder names for gap columns, trailing empties trimmed. */
function pa_grid_headers(array $matrix, int $headerRow): array
{
    $row = $matrix[$headerRow] ?? [];
    $headers = [];
    $trailingEmpty = 0;
    $colCount = count($row);
    for ($col = 0; $col < $colCount; $col++) {
        $cell = trim((string)$row[$col]);
        if ($cell === '') {
            $headers[] = 'Column' . ($col + 1);
            $trailingEmpty++;
        } else {
            $trailingEmpty = 0;
            $headers[] = $cell;
        }
    }
    if ($trailingEmpty > 0) {
        $headers = array_slice($headers, 0, count($headers) - $trailingEmpty);
    }
    return $headers;
}

/** Port of GetAllRowsAsStrings: data rows after the header, dropping fully-empty rows. */
function pa_grid_data_rows(array $matrix, int $headerRow, int $colCount): array
{
    $rows = [];
    $total = count($matrix);
    for ($r = $headerRow + 1; $r < $total; $r++) {
        $src = $matrix[$r];
        $rowData = [];
        $isEmpty = true;
        for ($col = 0; $col < $colCount; $col++) {
            $val = isset($src[$col]) ? (string)$src[$col] : '';
            if (trim($val) !== '') {
                $isEmpty = false;
            }
            $rowData[] = $val;
        }
        if (!$isEmpty) {
            $rows[] = $rowData;
        }
    }
    return $rows;
}

/** Port of GetSampleIndices: first N, last M, P deterministic-random from the middle. */
function pa_sample_indices(int $totalRows): array
{
    $cap = PA_SAMPLE_FIRST + PA_SAMPLE_LAST + PA_SAMPLE_RANDOM;
    if ($totalRows <= $cap) {
        return range(0, max(0, $totalRows - 1));
    }
    $indices = [];
    for ($i = 0; $i < PA_SAMPLE_FIRST; $i++) {
        $indices[$i] = true;
    }
    for ($i = $totalRows - PA_SAMPLE_LAST; $i < $totalRows; $i++) {
        $indices[$i] = true;
    }
    mt_srand(42); // deterministic, mirrors new Random(42)
    $middleStart = PA_SAMPLE_FIRST;
    $middleEnd = $totalRows - PA_SAMPLE_LAST;
    $attempts = 0;
    while (count($indices) < $cap && $attempts < 50) {
        $indices[mt_rand($middleStart, $middleEnd - 1)] = true;
        $attempts++;
    }
    $keys = array_keys($indices);
    sort($keys);
    return $keys;
}

/** Sample data rows by index. */
function pa_sample_rows(array $dataRows): array
{
    $total = count($dataRows);
    $out = [];
    foreach (pa_sample_indices($total) as $i) {
        if ($i < $total) {
            $out[] = $dataRows[$i];
        }
    }
    return $out;
}

/** Build a sheet struct from a raw xlsx matrix (applies header detection). */
function pa_sheet_from_matrix(string $name, array $matrix, array $merges = []): ?array
{
    if (count($matrix) === 0) {
        return null;
    }
    $headerRow = pa_grid_find_header_row($matrix);
    $headers = pa_grid_headers($matrix, $headerRow);
    if (count($headers) === 0) {
        return null;
    }
    $dataRows = pa_grid_data_rows($matrix, $headerRow, count($headers));
    return [
        'name' => $name,
        'headers' => $headers,
        'dataRows' => $dataRows,
        'sampleRows' => pa_sample_rows($dataRows),
        'totalRows' => count($dataRows),
        'matrix' => $matrix,
        'merges' => $merges,
    ];
}

/** Build a sheet struct from CSV output (row 0 is the header; no scanning). */
function pa_sheet_from_csv(string $name, array $headers, array $dataRows): ?array
{
    if (count($headers) === 0 || count($dataRows) === 0) {
        return null;
    }
    return [
        'name' => $name,
        'headers' => $headers,
        'dataRows' => $dataRows,
        'sampleRows' => pa_sample_rows($dataRows),
        'totalRows' => count($dataRows),
        'matrix' => array_merge([$headers], $dataRows),
        'merges' => [],
    ];
}
