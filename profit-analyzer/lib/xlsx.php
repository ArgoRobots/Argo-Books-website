<?php
// profit-analyzer/lib/xlsx.php
//
// Minimal, dependency-free multi-sheet .xlsx writer (OOXML via ZipArchive).
// Enough for clean tabular export: a bold header row, text + number cells.
// Avoids pulling the heavy PhpSpreadsheet dependency into the repo for a
// marketing tool.

/** 0-based column index -> spreadsheet column letters (0->A, 26->AA). */
function pa_xlsx_col(int $i): string
{
    $s = '';
    $i++;
    while ($i > 0) {
        $m = ($i - 1) % 26;
        $s = chr(65 + $m) . $s;
        $i = intdiv($i - 1, 26);
    }
    return $s;
}

function pa_xlsx_esc($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

/** Build one worksheet's XML. $headers = string[]; $rows = array of arrays. */
function pa_xlsx_sheet(array $headers, array $rows): string
{
    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
    // header row (style s=1 = bold)
    $xml .= '<row r="1">';
    foreach (array_values($headers) as $c => $h) {
        $xml .= '<c r="' . pa_xlsx_col($c) . '1" s="1" t="inlineStr"><is><t>' . pa_xlsx_esc($h) . '</t></is></c>';
    }
    $xml .= '</row>';
    foreach (array_values($rows) as $ri => $row) {
        $r = $ri + 2;
        $xml .= '<row r="' . $r . '">';
        foreach (array_values($row) as $c => $val) {
            $ref = pa_xlsx_col($c) . $r;
            if (is_int($val) || is_float($val)) {
                $xml .= '<c r="' . $ref . '"><v>' . $val . '</v></c>';
            } else {
                $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . pa_xlsx_esc($val) . '</t></is></c>';
            }
        }
        $xml .= '</row>';
    }
    $xml .= '</sheetData></worksheet>';
    return $xml;
}

/**
 * Build an .xlsx and return its raw bytes.
 * $sheets = [ 'Sheet name' => ['headers' => [...], 'rows' => [[...], ...]], ... ]
 */
function pa_write_xlsx(array $sheets): string
{
    $names = array_keys($sheets);
    $count = count($names);

    // styles.xml: default + bold header
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
        . '<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
        . '<borders count="1"><border/></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
        . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
        . '</styleSheet>';

    // workbook.xml + rels
    $sheetTags = ''; $rels = ''; $overrides = '';
    foreach ($names as $i => $name) {
        $n = $i + 1;
        $safe = pa_xlsx_esc(mb_substr($name, 0, 31));
        $sheetTags .= '<sheet name="' . $safe . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
        $rels .= '<Relationship Id="rId' . $n . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $n . '.xml"/>';
        $overrides .= '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
    }
    $stylesRid = 'rId' . ($count + 1);
    $rels .= '<Relationship Id="' . $stylesRid . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';

    $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>'
        . $sheetTags . '</sheets></workbook>';

    $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $rels . '</Relationships>';

    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . $overrides . '</Types>';

    $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $files = [
        '[Content_Types].xml' => $contentTypes,
        '_rels/.rels' => $rootRels,
        'xl/workbook.xml' => $workbook,
        'xl/_rels/workbook.xml.rels' => $workbookRels,
        'xl/styles.xml' => $styles,
    ];
    foreach ($names as $i => $name) {
        $s = $sheets[$name];
        $files['xl/worksheets/sheet' . ($i + 1) . '.xml'] = pa_xlsx_sheet($s['headers'] ?? [], $s['rows'] ?? []);
    }
    return pa_zip($files);
}

/**
 * Build a ZIP archive (STORED, no compression) from [path => content]. Pure
 * PHP, no zip extension required — portable across hosts. Excel reads stored
 * .xlsx parts fine.
 */
function pa_zip(array $files): string
{
    $local = '';
    $central = '';
    $offset = 0;
    foreach ($files as $name => $content) {
        $crc = crc32($content);
        $len = strlen($content);
        $nameLen = strlen($name);

        $header = "\x50\x4b\x03\x04" . pack('v', 20) . pack('v', 0) . pack('v', 0)
            . pack('v', 0) . pack('v', 0) . pack('V', $crc) . pack('V', $len)
            . pack('V', $len) . pack('v', $nameLen) . pack('v', 0) . $name;
        $local .= $header . $content;

        $central .= "\x50\x4b\x01\x02" . pack('v', 20) . pack('v', 20) . pack('v', 0)
            . pack('v', 0) . pack('v', 0) . pack('v', 0) . pack('V', $crc) . pack('V', $len)
            . pack('V', $len) . pack('v', $nameLen) . pack('v', 0) . pack('v', 0) . pack('v', 0)
            . pack('v', 0) . pack('V', 0) . pack('V', $offset) . $name;

        $offset += strlen($header) + $len;
    }
    $eocd = "\x50\x4b\x05\x06" . pack('v', 0) . pack('v', 0)
        . pack('v', count($files)) . pack('v', count($files))
        . pack('V', strlen($central)) . pack('V', strlen($local)) . pack('v', 0);

    return $local . $central . $eocd;
}
