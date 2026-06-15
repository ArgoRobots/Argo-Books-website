<?php
// profit-analyzer/lib/import/profiler.php
//
// Port of ColumnProfiler.cs. Computes per-column type/cardinality/examples and
// detects simple arithmetic relationships (Total = Qty * Price, etc.). These feed
// the analysis prompt to sharpen classification and column mapping.

/** Parse a numeric string the way the C# TryNum does (invariant, lenient). */
function pa_try_num(string $s): ?float
{
    $s = trim($s);
    if ($s === '') {
        return null;
    }
    // Accept plain numbers, optional sign, thousands handled only as plain decimals.
    if (is_numeric($s)) {
        return (float)$s;
    }
    return null;
}

/** Whether a string parses as a date (loose, like DateTime.TryParse). */
function pa_looks_like_date(string $s): bool
{
    $s = trim($s);
    if ($s === '' || is_numeric($s)) {
        return false;
    }
    $ts = strtotime($s);
    return $ts !== false;
}

/**
 * Port of ColumnProfiler.Profile.
 * @return list<array{header:string,type:string,distinct:int,empty:int,examples:list<string>}>
 */
function pa_profile_columns(array $headers, array $rows): array
{
    $profiles = [];
    $colCount = count($headers);
    for ($c = 0; $c < $colCount; $c++) {
        $values = [];
        foreach ($rows as $r) {
            if ($c < count($r)) {
                $values[] = $r[$c];
            }
        }
        $nonEmpty = array_values(array_filter($values, fn($v) => trim((string)$v) !== ''));
        $nums = [];
        foreach ($nonEmpty as $v) {
            $n = pa_try_num((string)$v);
            if ($n !== null) {
                $nums[] = $n;
            }
        }
        $allNumeric = count($nonEmpty) > 0 && count($nums) === count($nonEmpty);

        if ($allNumeric) {
            $type = 'number';
        } else {
            $allDates = count($nonEmpty) > 0;
            foreach ($nonEmpty as $v) {
                if (!pa_looks_like_date((string)$v)) { $allDates = false; break; }
            }
            $type = $allDates ? 'date' : 'string';
        }

        $distinct = [];
        foreach ($nonEmpty as $v) {
            $distinct[strtolower((string)$v)] = true;
        }

        $profiles[] = [
            'header' => $headers[$c],
            'type' => $type,
            'distinct' => count($distinct),
            'empty' => count($values) - count($nonEmpty),
            'examples' => array_slice(array_map('strval', $nonEmpty), 0, 3),
        ];
    }
    return $profiles;
}

/**
 * Port of ColumnProfiler.DetectRelationships: finds t ~= a*b or t ~= a+b across
 * numeric columns, confirmed on at least 2 rows.
 * @return list<string>  relationship descriptions
 */
function pa_detect_relationships(array $headers, array $rows): array
{
    $colCount = count($headers);
    // Numeric columns: every present value parses as a number (blanks allowed).
    $numericCols = [];
    for ($c = 0; $c < $colCount; $c++) {
        $isNumeric = true;
        foreach ($rows as $r) {
            if ($c >= count($r)) { continue; }
            $v = trim((string)$r[$c]);
            if ($v === '') { continue; }
            if (pa_try_num($v) === null) { $isNumeric = false; break; }
        }
        if ($isNumeric) {
            $numericCols[] = $c;
        }
    }

    $rels = [];
    foreach ($numericCols as $t) {
        foreach ($numericCols as $a) {
            foreach ($numericCols as $b) {
                if ($t === $a || $t === $b || $a >= $b) { continue; }
                if (pa_relationship_holds($rows, $t, $a, $b, fn($x, $y) => $x * $y)) {
                    $rels[] = "{$headers[$t]} ~= {$headers[$a]} * {$headers[$b]}";
                } elseif (pa_relationship_holds($rows, $t, $a, $b, fn($x, $y) => $x + $y)) {
                    $rels[] = "{$headers[$t]} ~= {$headers[$a]} + {$headers[$b]}";
                }
            }
        }
    }
    return $rels;
}

/** Port of ColumnProfiler.HoldsOver: op(a,b) == t within 0.01 on >= 2 rows. */
function pa_relationship_holds(array $rows, int $t, int $a, int $b, callable $op): bool
{
    $confirmed = 0;
    foreach ($rows as $r) {
        if ($t >= count($r) || $a >= count($r) || $b >= count($r)) { continue; }
        $vt = pa_try_num(trim((string)$r[$t]));
        $va = pa_try_num(trim((string)$r[$a]));
        $vb = pa_try_num(trim((string)$r[$b]));
        if ($vt === null || $va === null || $vb === null) { continue; }
        if (abs($op($va, $vb) - $vt) > 0.01) { return false; }
        $confirmed++;
    }
    return $confirmed >= 2;
}
