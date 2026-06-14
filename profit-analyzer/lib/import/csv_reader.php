<?php
// profit-analyzer/lib/import/csv_reader.php
//
// Port of CsvReader.cs + the CSV helpers in SpreadsheetAnalysisService: RFC-4180
// parsing (quoted fields with embedded newlines/commas), BOM stripping, and
// delimiter auto-detection from the first non-empty line.

/** Port of SpreadsheetAnalysisService.DetectCsvDelimiter. */
function pa_csv_detect_delimiter(string $headerLine): string
{
    $candidates = [',', "\t", ';', '|'];
    $maxCount = 0;
    $best = ',';
    foreach ($candidates as $delim) {
        $count = 0;
        $inQuotes = false;
        $len = strlen($headerLine);
        for ($i = 0; $i < $len; $i++) {
            $c = $headerLine[$i];
            if ($c === '"') {
                $inQuotes = !$inQuotes;
            } elseif ($c === $delim && !$inQuotes) {
                $count++;
            }
        }
        if ($count > $maxCount) {
            $maxCount = $count;
            $best = $delim;
        }
    }
    return $best;
}

/**
 * Reads a CSV file into headers + data rows. Mirrors CsvReader.ReadAllRows:
 * first record is the header, blank rows are dropped, fields are trimmed.
 *
 * @return array{0: list<string>, 1: list<list<string>>}  [headers, rows]
 */
function pa_read_csv(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        return [[], []];
    }
    // Strip UTF-8 BOM.
    if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
        $content = substr($content, 3);
    }
    $content = str_replace("\r\n", "\n", $content);
    $content = str_replace("\r", "\n", $content);

    // Delimiter from the first non-empty line.
    $firstLine = '';
    foreach (explode("\n", $content) as $line) {
        if (trim($line) !== '') {
            $firstLine = $line;
            break;
        }
    }
    $delimiter = pa_csv_detect_delimiter($firstLine);

    $records = pa_csv_parse($content, $delimiter);
    if (count($records) === 0) {
        return [[], []];
    }

    $headers = array_map('trim', $records[0]);
    $rows = [];
    foreach (array_slice($records, 1) as $rec) {
        $rec = array_map('trim', $rec);
        // Drop fully-blank rows (mirrors the .Where(r => r.Any(non-empty)) filter).
        $hasData = false;
        foreach ($rec as $f) {
            if ($f !== '') { $hasData = true; break; }
        }
        if ($hasData) {
            $rows[] = $rec;
        }
    }
    return [$headers, $rows];
}

/**
 * RFC-4180 record parser over the full content: handles quoted fields with
 * embedded delimiters, newlines, and "" escaped quotes.
 *
 * @return list<list<string>>
 */
function pa_csv_parse(string $content, string $delimiter): array
{
    $records = [];
    $field = '';
    $record = [];
    $inQuotes = false;
    $len = strlen($content);
    $started = false; // whether the current record has any content yet

    for ($i = 0; $i < $len; $i++) {
        $c = $content[$i];
        if ($inQuotes) {
            if ($c === '"') {
                if ($i + 1 < $len && $content[$i + 1] === '"') {
                    $field .= '"';
                    $i++;
                } else {
                    $inQuotes = false;
                }
            } else {
                $field .= $c;
            }
        } else {
            if ($c === '"') {
                $inQuotes = true;
                $started = true;
            } elseif ($c === $delimiter) {
                $record[] = $field;
                $field = '';
                $started = true;
            } elseif ($c === "\n") {
                $record[] = $field;
                $records[] = $record;
                $record = [];
                $field = '';
                $started = false;
            } else {
                $field .= $c;
                $started = true;
            }
        }
    }
    // Flush trailing field/record if the file didn't end with a newline.
    if ($started || $field !== '' || count($record) > 0) {
        $record[] = $field;
        $records[] = $record;
    }
    return $records;
}
