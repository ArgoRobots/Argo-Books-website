<?php
// profit-analyzer/lib/xlsx.php
//
// Minimal, dependency-free multi-sheet .xlsx writer (OOXML, pure-PHP STORED zip).
// Produces a polished cleaned export: a filled + bold header row (frozen), sized
// columns, and a thousands number format on numeric cells. No PhpSpreadsheet /
// ZipArchive needed, so it runs on any host.

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

/** Estimate a sensible column width (in Excel character units) from header + cells. */
function pa_xlsx_widths(array $headers, array $rows): array
{
    $widths = [];
    foreach ($headers as $c => $h) {
        $max = strlen((string) $h);
        foreach ($rows as $row) {
            if (isset($row[$c])) {
                $v = $row[$c];
                $len = (is_int($v) || is_float($v))
                    ? strlen(number_format((float) $v, 2))
                    : strlen((string) $v);
                if ($len > $max) { $max = $len; }
            }
        }
        $widths[$c] = max(10, min(48, $max + 2));
    }
    return $widths;
}

/** Build one worksheet's XML: frozen header, sized columns, styled cells. */
function pa_xlsx_sheet(array $headers, array $rows): string
{
    $widths = pa_xlsx_widths($headers, $rows);

    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetViews><sheetView workbookViewId="0">'
        . '<pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/>'
        . '</sheetView></sheetViews>'
        . '<sheetFormatPr defaultRowHeight="15"/>';

    // Column widths.
    $xml .= '<cols>';
    foreach ($widths as $c => $w) {
        $n = $c + 1;
        $xml .= '<col min="' . $n . '" max="' . $n . '" width="' . $w . '" customWidth="1"/>';
    }
    $xml .= '</cols>';

    $xml .= '<sheetData>';
    // Header row (style s=1 = filled + bold white).
    $xml .= '<row r="1" ht="18" customHeight="1">';
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
                $xml .= '<c r="' . $ref . '" s="2"><v>' . $val . '</v></c>'; // s=2 = number format
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

    // styles.xml: default font, bold-white header font; none/gray125/header fills;
    // a thousands number format (s=2) for numeric cells; header xf (s=1).
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<numFmts count="1"><numFmt numFmtId="164" formatCode="#,##0.##"/></numFmts>'
        . '<fonts count="2">'
        . '<font><sz val="11"/><name val="Calibri"/></font>'
        . '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
        . '</fonts>'
        . '<fills count="3">'
        . '<fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FF1D4ED8"/><bgColor indexed="64"/></patternFill></fill>'
        . '</fills>'
        . '<borders count="1"><border/></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="3">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
        . '<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1" applyAlignment="1"><alignment vertical="center"/></xf>'
        . '<xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
        . '</cellXfs>'
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
