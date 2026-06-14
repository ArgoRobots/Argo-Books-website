<?php
// profit-analyzer/lib/import/layout.php
//
// Port of the Layout/* services: SheetGrid (row shapes + merges), LayoutGate
// (cheap local "is this messy?" heuristic), SpreadsheetLayoutService (AI layout
// descriptor), and GridExtractor (deterministic descriptor -> clean headers+rows).
//
// The desktop integration (LayoutNormalizationService) round-trips through a temp
// .xlsx so the rest of the pipeline only sees clean files. Here we do the same in
// memory: a messy sheet's matrix is rewritten to a clean [headers, ...rows] matrix
// before analysis. Any failure falls back to the original matrix — never a drop.

require_once __DIR__ . '/gemini.php';
require_once __DIR__ . '/profiler.php';

// Gate thresholds (port of LayoutGate constants).
const PA_MERGE_CHECK_ROWS = 3;
const PA_DENSE_MIN_NONEMPTY = 2;
const PA_MAX_PREAMBLE_ROWS = 2;
const PA_NUMERIC_HEADER_THRESHOLD = 0.6;
const PA_RAGGED_SPREAD_FRACTION = 0.5;
const PA_RAGGED_MIN_ROWS = 3;
const PA_PERIOD_HEADER_MIN_COUNT = 2;
const PA_PERIOD_HEADER_FRACTION = 0.5;

/** Build a grid snapshot {cells, merges(1-based), shapes, rowCount, colCount} from a matrix. */
function pa_build_grid(array $matrix, array $merges = []): array
{
    $rowCount = count($matrix);
    $colCount = 0;
    foreach ($matrix as $row) {
        $colCount = max($colCount, count($row));
    }
    $shapes = [];
    foreach ($matrix as $row) {
        $nonEmpty = 0;
        $numericOrDate = 0;
        for ($c = 0; $c < $colCount; $c++) {
            $val = isset($row[$c]) ? (string)$row[$c] : '';
            if ($val !== '' && trim($val) !== '') {
                $nonEmpty++;
                if (is_numeric(trim($val)) || pa_looks_like_date($val)) {
                    $numericOrDate++;
                }
            }
        }
        $numericFraction = $nonEmpty > 0 ? $numericOrDate / $nonEmpty : 0.0;
        $shapes[] = [
            'nonEmpty' => $nonEmpty,
            'numericFraction' => $numericFraction,
            'textFraction' => $nonEmpty > 0 ? ($nonEmpty - $numericOrDate) / $nonEmpty : 0.0,
        ];
    }
    return [
        'cells' => $matrix,
        'merges' => $merges, // 1-based {firstRow,firstCol,lastRow,lastCol}
        'shapes' => $shapes,
        'rowCount' => $rowCount,
        'colCount' => $colCount,
    ];
}

// ─── LayoutGate ───────────────────────────────────────────────────────────────

/** Port of LayoutGate.NeedsInterpretation. */
function pa_layout_needs_interpretation(array $grid): bool
{
    if ($grid['rowCount'] === 0) {
        return false;
    }
    // Rule 1: merged range in the top few rows.
    foreach ($grid['merges'] as $m) {
        if ($m['firstRow'] <= PA_MERGE_CHECK_ROWS) {
            return true;
        }
    }
    // Rule 2: first dense row not near the top.
    $firstDense = -1;
    for ($i = 0; $i < $grid['rowCount']; $i++) {
        if ($grid['shapes'][$i]['nonEmpty'] >= PA_DENSE_MIN_NONEMPTY) {
            $firstDense = $i;
            break;
        }
    }
    if ($firstDense < 0 || $firstDense > PA_MAX_PREAMBLE_ROWS) {
        return true;
    }
    // Rule 3: first dense row mostly numeric (numbers-as-headers).
    if ($grid['shapes'][$firstDense]['numericFraction'] >= PA_NUMERIC_HEADER_THRESHOLD) {
        return true;
    }
    // Rule 4: first dense row mostly period labels (text cross-tab).
    if (pa_layout_has_period_header($grid, $firstDense)) {
        return true;
    }
    // Rule 5: ragged row widths.
    if (pa_layout_has_ragged_rows($grid)) {
        return true;
    }
    return false;
}

function pa_layout_has_period_header(array $grid, int $headerRow): bool
{
    $cells = $grid['cells'][$headerRow] ?? [];
    $nonEmpty = 0;
    $periodCount = 0;
    foreach ($cells as $cell) {
        if (trim((string)$cell) === '') {
            continue;
        }
        $nonEmpty++;
        if (pa_layout_is_period_label((string)$cell)) {
            $periodCount++;
        }
    }
    if ($periodCount < PA_PERIOD_HEADER_MIN_COUNT || $nonEmpty === 0) {
        return false;
    }
    return ($periodCount / $nonEmpty) >= PA_PERIOD_HEADER_FRACTION;
}

function pa_layout_is_period_label(string $raw): bool
{
    static $months = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'sept',
        'oct', 'nov', 'dec', 'january', 'february', 'march', 'april', 'june', 'july',
        'august', 'september', 'october', 'november', 'december'];

    $normalized = strtolower(trim($raw));
    $normalized = str_replace(['-', '/', '.', "'"], ' ', $normalized);
    $tokens = preg_split('/\s+/', trim($normalized), -1, PREG_SPLIT_NO_EMPTY);
    if (count($tokens) === 0) {
        return false;
    }
    // Drop a trailing 2- or 4-digit year.
    if (count($tokens) > 1) {
        $last = $tokens[count($tokens) - 1];
        if ((strlen($last) === 2 || strlen($last) === 4) && ctype_digit($last)) {
            array_pop($tokens);
        }
    }
    if (count($tokens) !== 1) {
        return false;
    }
    $token = $tokens[0];
    if (in_array($token, $months, true)) {
        return true;
    }
    if (strlen($token) === 2 && $token[0] === 'q' && $token[1] >= '1' && $token[1] <= '4') {
        return true;
    }
    if (strlen($token) >= 2 && $token[0] === 'w' && ctype_digit(substr($token, 1))) {
        $week = (int)substr($token, 1);
        return $week >= 1 && $week <= 53;
    }
    return false;
}

function pa_layout_has_ragged_rows(array $grid): bool
{
    $counts = [];
    foreach ($grid['shapes'] as $shape) {
        if ($shape['nonEmpty'] > 0) {
            $counts[] = $shape['nonEmpty'];
        }
    }
    if (count($counts) < PA_RAGGED_MIN_ROWS) {
        return false;
    }
    sort($counts);
    $spread = $counts[count($counts) - 1] - $counts[0];
    $mid = intdiv(count($counts), 2);
    $median = count($counts) % 2 === 0
        ? ($counts[$mid - 1] + $counts[$mid]) / 2.0
        : $counts[$mid];
    if ($median <= 0) {
        return false;
    }
    return ($spread / $median) > PA_RAGGED_SPREAD_FRACTION;
}

// ─── SpreadsheetLayoutService (AI descriptor) ─────────────────────────────────

const PA_LAYOUT_HEADER_WINDOW = 15;
const PA_LAYOUT_SAMPLE_ROWS = 5;
const PA_LAYOUT_MAX_CELL_CHARS = 40;

/** Returns the parsed descriptor tables[] or null. Port of GetLayoutDescriptorAsync. */
function pa_layout_descriptor(array $grid): ?array
{
    if ($grid['rowCount'] === 0 || $grid['colCount'] === 0) {
        return null;
    }
    $system = pa_layout_system_prompt();
    $user = pa_layout_user_prompt($grid);
    $response = pa_gemini_chat($system, $user, 3000, 0.0);
    if ($response === null || trim($response) === '') {
        return null;
    }
    $clean = pa_strip_markdown_json($response);
    $doc = json_decode($clean, true);
    if (!is_array($doc) || !isset($doc['tables']) || !is_array($doc['tables'])) {
        return [];
    }
    $tables = [];
    foreach ($doc['tables'] as $t) {
        if (!is_array($t)) {
            continue;
        }
        $orientation = (isset($t['orientation']) && strcasecmp((string)$t['orientation'], 'wide') === 0)
            ? 'wide' : 'long';
        $tables[] = [
            'firstDataRow' => (int)round((float)($t['firstDataRow'] ?? 0)),
            'lastDataRow' => (int)round((float)($t['lastDataRow'] ?? 0)),
            'firstCol' => (int)round((float)($t['firstCol'] ?? 0)),
            'lastCol' => (int)round((float)($t['lastCol'] ?? 0)),
            'headerRows' => pa_layout_int_list($t['headerRows'] ?? []),
            'orientation' => $orientation,
            'ignoreRows' => pa_layout_int_list($t['ignoreRows'] ?? []),
            'keyColumns' => isset($t['keyColumns']) && is_array($t['keyColumns'])
                ? pa_layout_int_list($t['keyColumns']) : null,
        ];
    }
    return $tables;
}

function pa_layout_int_list($arr): array
{
    if (!is_array($arr)) {
        return [];
    }
    $out = [];
    foreach ($arr as $v) {
        if (is_numeric($v)) {
            $out[] = (int)round((float)$v);
        }
    }
    return $out;
}

function pa_layout_system_prompt(): string
{
    return "You are a spreadsheet layout analyzer. You are given a compact summary of one\n"
        . "worksheet and you describe where the real data tables are, so a deterministic\n"
        . "program can extract clean header+row tables.\n\n"
        . "Sheets may be messy: long title/preamble rows before the real header, multi-row\n"
        . "or merged headers, subtotal/note rows mixed into the data, multiple stacked\n"
        . "tables, or cross-tab (\"wide\") layouts where values are spread across columns that\n"
        . "are really one category.\n\n"
        . "Respond with STRICT JSON only. No prose, no markdown, no code fences. Output a\n"
        . "single JSON object with this exact shape:\n\n"
        . "{\n"
        . "  \"tables\": [\n"
        . "    {\n"
        . "      \"firstDataRow\": <int>,   // 0-based row index of the first DATA row (not header)\n"
        . "      \"lastDataRow\": <int>,    // 0-based row index of the last data row (inclusive)\n"
        . "      \"firstCol\": <int>,       // 0-based first column of the table region\n"
        . "      \"lastCol\": <int>,        // 0-based last column (inclusive)\n"
        . "      \"headerRows\": [<int>...], // 0-based header row indices, top-to-bottom; may be empty\n"
        . "      \"orientation\": \"long\" | \"wide\",\n"
        . "      \"ignoreRows\": [<int>...], // 0-based data rows to skip (subtotals, notes, blanks)\n"
        . "      \"keyColumns\": [<int>...]  // for \"wide\" only: the row-key columns; null/omit for \"long\"\n"
        . "    }\n"
        . "  ]\n"
        . "}\n\n"
        . "Rules:\n"
        . "- All indices are 0-based into the row/column grid described below.\n"
        . "- \"long\" = one record per row (the normal case). \"wide\" = a cross-tab where data\n"
        . "  columns are really one spread-out category; set keyColumns to the identifying\n"
        . "  columns and the program will transpose the rest into long form.\n"
        . "- Prefer one table unless the sheet clearly contains multiple separate tables.\n"
        . "- If you cannot find any real data table, return {\"tables\": []}.";
}

function pa_layout_user_prompt(array $grid): string
{
    $sb = "Worksheet summary. RowCount={$grid['rowCount']}, ColCount={$grid['colCount']}.\n\n";
    $sb .= "PER-ROW SHAPE (one line per row, all rows). Format: row=<0-based index> nonEmpty=<count> numericFrac=<0..1> textFrac=<0..1>\n";
    for ($r = 0; $r < $grid['rowCount']; $r++) {
        $s = $grid['shapes'][$r];
        $sb .= 'row=' . $r
            . ' nonEmpty=' . $s['nonEmpty']
            . ' numericFrac=' . number_format($s['numericFraction'], 2)
            . ' textFrac=' . number_format($s['textFraction'], 2) . "\n";
    }
    $sb .= "\nCELL CONTENT (focused window; cells shown as col=<0-based>:\"value\").\n";

    $headerWindowEnd = min(PA_LAYOUT_HEADER_WINDOW, $grid['rowCount']);
    for ($r = 0; $r < $headerWindowEnd; $r++) {
        $sb .= pa_layout_row_content($grid, $r);
    }
    if ($grid['rowCount'] > $headerWindowEnd) {
        $sb .= "... sample data rows further down ...\n";
        $remaining = $grid['rowCount'] - $headerWindowEnd;
        $step = max(1, intdiv($remaining, PA_LAYOUT_SAMPLE_ROWS));
        for ($r = $headerWindowEnd; $r < $grid['rowCount']; $r += $step) {
            $sb .= pa_layout_row_content($grid, $r);
        }
    }
    $sb .= "\nMERGED RANGES (1-based row/col coordinates: firstRow,firstCol-lastRow,lastCol).\n";
    if (count($grid['merges']) === 0) {
        $sb .= "(none)\n";
    } else {
        foreach ($grid['merges'] as $m) {
            $sb .= "{$m['firstRow']},{$m['firstCol']}-{$m['lastRow']},{$m['lastCol']}\n";
        }
    }
    $sb .= "\nReturn the JSON layout descriptor now.";
    return $sb;
}

function pa_layout_row_content(array $grid, int $row): string
{
    $cells = $grid['cells'][$row] ?? [];
    $line = 'row=' . $row . ':';
    $any = false;
    foreach ($cells as $c => $val) {
        if (trim((string)$val) === '') {
            continue;
        }
        $any = true;
        $flat = str_replace(["\n", "\r", "\t", '"'], [' ', ' ', ' ', "'"], (string)$val);
        if (mb_strlen($flat) > PA_LAYOUT_MAX_CELL_CHARS) {
            $flat = mb_substr($flat, 0, PA_LAYOUT_MAX_CELL_CHARS) . '...';
        }
        $line .= ' col=' . $c . ':"' . $flat . '"';
    }
    if (!$any) {
        $line .= ' (empty)';
    }
    return $line . "\n";
}

// ─── GridExtractor (deterministic descriptor -> clean table) ──────────────────

/** Port of GridExtractor.Extract. Returns [headers, rows]. */
function pa_grid_extract(array $grid, array $region): array
{
    $rowCount = $grid['rowCount'];
    $colCount = $grid['colCount'];
    if ($rowCount === 0 || $colCount === 0) {
        return [[], []];
    }
    $firstCol = max(0, min($region['firstCol'], $colCount - 1));
    $lastCol = max($firstCol, min($region['lastCol'], $colCount - 1));
    $firstDataRow = max(0, min($region['firstDataRow'], $rowCount - 1));
    $lastDataRow = max($firstDataRow, min($region['lastDataRow'], $rowCount - 1));
    $ignore = array_flip($region['ignoreRows'] ?? []);

    if ($region['orientation'] === 'wide') {
        return pa_grid_extract_wide($grid, $region, $firstCol, $lastCol, $firstDataRow, $lastDataRow, $ignore);
    }
    return pa_grid_extract_long($grid, $region, $firstCol, $lastCol, $firstDataRow, $lastDataRow, $ignore);
}

function pa_grid_extract_long(array $grid, array $region, int $firstCol, int $lastCol, int $firstDataRow, int $lastDataRow, array $ignore): array
{
    $headers = pa_grid_build_headers($grid, $region, $firstCol, $lastCol);
    $rows = [];
    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
        if (isset($ignore[$r])) {
            continue;
        }
        $values = [];
        for ($c = $firstCol; $c <= $lastCol; $c++) {
            $values[] = pa_grid_cell($grid, $r, $c);
        }
        $allEmpty = true;
        foreach ($values as $v) {
            if ($v !== '') { $allEmpty = false; break; }
        }
        if ($allEmpty) {
            continue;
        }
        $rows[] = $values;
    }
    return [$headers, $rows];
}

function pa_grid_extract_wide(array $grid, array $region, int $firstCol, int $lastCol, int $firstDataRow, int $lastDataRow, array $ignore): array
{
    $fullHeaders = pa_grid_build_headers($grid, $region, $firstCol, $lastCol);
    $keyCols = [];
    foreach (($region['keyColumns'] ?? []) as $c) {
        if ($c >= $firstCol && $c <= $lastCol) {
            $keyCols[$c] = true;
        }
    }
    $keyCols = array_keys($keyCols);
    sort($keyCols);
    $keyColSet = array_flip($keyCols);

    $spreadCols = [];
    for ($c = $firstCol; $c <= $lastCol; $c++) {
        if (!isset($keyColSet[$c])) {
            $spreadCols[] = $c;
        }
    }

    $headers = [];
    foreach ($keyCols as $kc) {
        $headers[] = $fullHeaders[$kc - $firstCol];
    }
    $headers[] = 'Column';
    $headers[] = 'Value';

    $rows = [];
    for ($r = $firstDataRow; $r <= $lastDataRow; $r++) {
        if (isset($ignore[$r])) {
            continue;
        }
        $keyValues = [];
        $allEmpty = true;
        foreach ($keyCols as $c) {
            $v = pa_grid_cell($grid, $r, $c);
            $keyValues[] = $v;
            if ($v !== '') { $allEmpty = false; }
        }
        if ($allEmpty) {
            continue;
        }
        foreach ($spreadCols as $sc) {
            $value = pa_grid_cell($grid, $r, $sc);
            if ($value === '') {
                continue;
            }
            $outRow = $keyValues;
            $outRow[] = $fullHeaders[$sc - $firstCol];
            $outRow[] = $value;
            $rows[] = $outRow;
        }
    }
    return [$headers, $rows];
}

function pa_grid_build_headers(array $grid, array $region, int $firstCol, int $lastCol): array
{
    $headers = [];
    for ($c = $firstCol; $c <= $lastCol; $c++) {
        $parts = [];
        foreach (($region['headerRows'] ?? []) as $headerRow) {
            if ($headerRow < 0 || $headerRow >= $grid['rowCount']) {
                continue;
            }
            $value = pa_grid_resolve_header_cell($grid, $headerRow, $c);
            if ($value !== '') {
                $parts[] = $value;
            }
        }
        $headers[] = trim(implode(' > ', $parts));
    }
    return $headers;
}

function pa_grid_resolve_header_cell(array $grid, int $row, int $col): string
{
    $direct = pa_grid_cell($grid, $row, $col);
    if ($direct !== '') {
        return $direct;
    }
    $oneBasedRow = $row + 1;
    $oneBasedCol = $col + 1;
    foreach ($grid['merges'] as $m) {
        if ($oneBasedRow >= $m['firstRow'] && $oneBasedRow <= $m['lastRow']
            && $oneBasedCol >= $m['firstCol'] && $oneBasedCol <= $m['lastCol']) {
            return pa_grid_cell($grid, $m['firstRow'] - 1, $m['firstCol'] - 1);
        }
    }
    return $direct;
}

function pa_grid_cell(array $grid, int $row, int $col): string
{
    if ($row < 0 || $row >= count($grid['cells'])) {
        return '';
    }
    $rowCells = $grid['cells'][$row];
    if ($col < 0 || $col >= count($rowCells)) {
        return '';
    }
    return (string)($rowCells[$col] ?? '');
}

// ─── Integration (port of LayoutNormalizationService, in-memory) ──────────────

/**
 * If a sheet's raw matrix looks messy, rewrite it to a clean [headers, ...rows]
 * matrix via the AI descriptor + deterministic extractor. On any failure or no-op,
 * returns the original matrix unchanged (never drops the sheet).
 *
 * @return array{0: list<list<string>>, 1: list<array>}  [newMatrix, newMerges]
 */
function pa_layout_normalize_matrix(array $matrix, array $merges): array
{
    if (count($matrix) === 0) {
        return [$matrix, $merges];
    }
    $grid = pa_build_grid($matrix, $merges);
    if (!pa_layout_needs_interpretation($grid)) {
        return [$matrix, $merges];
    }
    $tables = pa_layout_descriptor($grid);
    if ($tables === null || count($tables) === 0) {
        return [$matrix, $merges]; // AI failed / found nothing -> keep original
    }
    [$headers, $rows] = pa_grid_extract($grid, $tables[0]);
    if (count($headers) === 0 && count($rows) === 0) {
        return [$matrix, $merges];
    }
    // Clean table: row 0 is the header, no merges remain.
    $clean = array_merge([$headers], $rows);
    return [$clean, []];
}
