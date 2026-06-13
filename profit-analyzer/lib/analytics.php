<?php
// profit-analyzer/lib/analytics.php
//
// The analytics engine: pure computation over NormalizedData → the data the
// result page renders (insights, KPIs, chart series, the money-flow, the
// cleaned table). No importer dependency; tested against fixtures.
//
// Only the tabs the uploaded data actually supports are returned, so a
// sales-only export yields Dashboard/Products/Customers/Taxes/Geographic, not
// all nine.

require_once __DIR__ . '/contract.php';

function pa_money(float $n): string { return '$' . number_format(round($n)); }
function pa_pct(float $n, float $of): int { return $of > 0 ? (int) round($n / $of * 100) : 0; }
function pa_month(string $date): string { return date('M', strtotime($date)); }

/** Sum a numeric field over an array of rows. */
function pa_sum(array $rows, string $field): float
{
    $t = 0.0;
    foreach ($rows as $r) { $t += (float) ($r[$field] ?? 0); }
    return $t;
}

/** Group rows by a key callback, summing $field; returns [key => total]. */
function pa_group(array $rows, callable $keyFn, string $field): array
{
    $out = [];
    foreach ($rows as $r) {
        $k = $keyFn($r);
        if ($k === null || $k === '') { continue; }
        $out[$k] = ($out[$k] ?? 0) + (float) ($r[$field] ?? 0);
    }
    return $out;
}

/** Ordered month buckets (Jan..Dec) that actually appear in the rows. */
function pa_month_series(array $rows, string $dateField, string $valField): array
{
    $byMonth = [];
    foreach ($rows as $r) {
        $m = (int) date('n', strtotime($r[$dateField] ?? 'now'));
        $byMonth[$m] = ($byMonth[$m] ?? 0) + (float) ($r[$valField] ?? 0);
    }
    ksort($byMonth);
    $cats = []; $data = [];
    foreach ($byMonth as $m => $v) { $cats[] = date('M', mktime(0, 0, 0, $m, 1)); $data[] = round($v, 2); }
    return ['cats' => $cats, 'data' => $data];
}

/** Top-N [name=>total] map plus an "Other" remainder, as pie data. */
function pa_top_pie(array $map, int $n, string $otherLabel = 'Other'): array
{
    arsort($map);
    $out = []; $i = 0; $other = 0;
    foreach ($map as $name => $val) {
        if ($i < $n) { $out[] = ['name' => $name, 'value' => round($val)]; }
        else { $other += $val; }
        $i++;
    }
    if ($other > 0) { $out[] = ['name' => $otherLabel, 'value' => round($other)]; }
    return $out;
}

/**
 * Compute the full result-page payload from NormalizedData.
 */
function pa_compute_analytics(array $normalized): array
{
    $n = pa_normalize($normalized);
    $E = $n['entities'];
    $meta = $n['meta'];

    $revenue   = $E['revenue'];
    $expenses  = $E['expenses'];
    $products  = $E['products'];
    $customers = $E['customers'];
    $suppliers = $E['suppliers'];

    $revTotal = pa_sum($revenue, 'amount');
    $expTotal = pa_sum($expenses, 'amount');
    $netProfit = $revTotal - $expTotal;
    $margin = pa_pct($netProfit, $revTotal);

    // Expense category buckets for the money-flow.
    $catTotals = pa_group($expenses, fn($r) => $r['category'] ?? 'Other', 'amount');
    $cogs = $catTotals['Cost of goods'] ?? 0;
    $ads  = $catTotals['Advertising'] ?? 0;
    $fees = $catTotals['Fees'] ?? 0;
    $other = max(0, $expTotal - $cogs - $ads - $fees);

    $result = [
        'meta' => [
            'filename' => $meta['filename'] ?? 'your-spreadsheet.xlsx',
            'rows' => count($revenue) + count($expenses),
            'flagged' => (int) ($meta['flagged'] ?? 0),
        ],
        'tabs' => [],
        'headline' => null,
    ];

    // ---- Headline insight (fees + margin, both supportable) ----
    if ($revTotal > 0) {
        $feePct = pa_pct($fees, $revTotal);
        if ($fees > 0) {
            $result['headline'] = [
                'title' => "You're losing {$feePct}% of revenue to payment &amp; processing fees",
                'detail' => "That's " . pa_money($fees) . " over the period, and after every cost you keep just <b>{$margin}%</b> as profit.",
            ];
        } else {
            $result['headline'] = [
                'title' => "You keep {$margin}% of your revenue as profit",
                'detail' => "On " . pa_money($revTotal) . " in revenue, " . pa_money($netProfit) . " is left after every cost.",
            ];
        }
    }

    // ---- Dashboard ----
    if ($revTotal > 0 || $expTotal > 0) {
        $result['tabs'][] = 'dashboard';

        // staged money-flow (Profit = revenue - all expenses, always correct)
        $nodes = ['Revenue' => round($revTotal)];
        $links = [];
        $surv = 'Revenue'; $survVal = $revTotal;
        $stages = [
            ['leak' => 'Cost of goods',    'val' => $cogs,  'next' => 'Gross profit'],
            ['leak' => 'Ads & marketing',  'val' => $ads,   'next' => 'After ads'],
            ['leak' => 'Fees',             'val' => $fees,  'next' => 'After fees'],
            ['leak' => 'Other costs',      'val' => $other, 'next' => 'Profit'],
        ];
        // drop zero leaks, and make the last surviving stage land on "Profit"
        $stages = array_values(array_filter($stages, fn($s) => $s['val'] > 0));
        foreach ($stages as $i => $s) {
            $isLast = ($i === count($stages) - 1);
            $nextName = $isLast ? 'Profit' : $s['next'];
            $nextVal = $survVal - $s['val'];
            $nodes[$s['leak']] = round($s['val']);
            $nodes[$nextName] = round($nextVal);
            $links[] = [$surv, $s['leak'], round($s['val'])];
            $links[] = [$surv, $nextName, round($nextVal)];
            $surv = $nextName; $survVal = $nextVal;
        }

        $revByProductMap = [];
        foreach ($revenue as $r) {
            $pid = $r['productId'] ?? null;
            $name = $pid ? (pa_product_name($products, $pid) ?? $r['description'] ?? 'Other') : ($r['description'] ?? 'Other');
            $revByProductMap[$name] = ($revByProductMap[$name] ?? 0) + (float) ($r['amount'] ?? 0);
        }

        $result['dashboard'] = [
            'kpis' => [
                ['lbl' => 'Total Expenses', 'val' => pa_money($expTotal), 'cls' => 'bad', 'sub' => pa_pct($expTotal, $revTotal) . '% of revenue', 'subcls' => 'down'],
                ['lbl' => 'Total Revenue',  'val' => pa_money($revTotal), 'cls' => 'good'],
                ['lbl' => 'Net Profit',     'val' => pa_money($netProfit), 'cls' => ''],
                ['lbl' => 'Profit Margin',  'val' => $margin . '%', 'cls' => ''],
            ],
            'flow' => ['kept' => $margin, 'nodes' => $nodes, 'links' => $links],
            'profitTrend' => pa_month_diff_series($revenue, $expenses),
            'salesVsExp' => [
                'revenue' => pa_month_series($revenue, 'date', 'amount'),
                'expenses' => pa_month_series($expenses, 'date', 'amount'),
            ],
            'revByProduct' => pa_top_pie($revByProductMap, 6),
            'expDist' => pa_top_pie($catTotals, 6),
        ];
    }

    // ---- Products ----
    if ($products && $revenue) {
        $result['tabs'][] = 'products';
        $byProd = [];
        foreach ($revenue as $r) {
            $pid = $r['productId'] ?? null; if (!$pid) { continue; }
            $byProd[$pid] ??= ['units' => 0, 'rev' => 0];
            $byProd[$pid]['units'] += (float) ($r['quantity'] ?? 0);
            $byProd[$pid]['rev'] += (float) ($r['amount'] ?? 0);
        }
        $rows = [];
        $totalUnits = 0; $totalRev = 0;
        foreach ($byProd as $pid => $d) {
            $rows[] = ['name' => pa_product_name($products, $pid) ?? $pid, 'units' => $d['units'], 'rev' => $d['rev'], 'avg' => $d['units'] > 0 ? $d['rev'] / $d['units'] : 0];
            $totalUnits += $d['units']; $totalRev += $d['rev'];
        }
        usort($rows, fn($a, $b) => $b['rev'] <=> $a['rev']);
        $result['products'] = [
            'kpis' => [
                ['lbl' => 'Product Revenue', 'val' => pa_money($totalRev), 'cls' => 'good'],
                ['lbl' => 'Units Sold', 'val' => number_format($totalUnits), 'cls' => ''],
                ['lbl' => 'Avg Sale Price', 'val' => '$' . number_format($totalUnits > 0 ? $totalRev / $totalUnits : 0, 2), 'cls' => ''],
                ['lbl' => 'Products Sold', 'val' => (string) count($rows), 'cls' => ''],
            ],
            'table' => array_map(fn($r) => [
                'name' => $r['name'], 'units' => number_format($r['units']),
                'rev' => pa_money($r['rev']), 'avg' => '$' . number_format($r['avg'], 2),
            ], $rows),
        ];
    }

    // ---- Customers ----
    if ($customers && $revenue) {
        $result['tabs'][] = 'customers';
        $byCust = pa_group($revenue, fn($r) => pa_customer_name($customers, $r['customerId'] ?? '') ?? ($r['customerId'] ?? ''), 'amount');
        $payMap = [];
        foreach ($revenue as $r) { $s = $r['paymentStatus'] ?? 'Unknown'; $payMap[$s] = ($payMap[$s] ?? 0) + 1; }
        $result['customers'] = [
            'kpis' => [
                ['lbl' => 'Total Customers', 'val' => (string) count($customers), 'cls' => ''],
                ['lbl' => 'Active', 'val' => (string) count(array_filter($customers, fn($c) => ($c['status'] ?? '') === 'Active')), 'cls' => 'good'],
                ['lbl' => 'Avg Customer Value', 'val' => pa_money(count($customers) > 0 ? $revTotal / count($customers) : 0), 'cls' => ''],
            ],
            'topCustomers' => pa_top_pie($byCust, 5),
            'paymentStatus' => array_map(fn($k, $v) => ['name' => $k, 'value' => $v], array_keys($payMap), array_values($payMap)),
        ];
    }

    // ---- Taxes ----
    $taxCollected = pa_sum($revenue, 'taxAmount');
    $taxPaid = pa_sum($expenses, 'taxAmount');
    if ($taxCollected > 0 || $taxPaid > 0) {
        $result['tabs'][] = 'taxes';
        $taxByCat = pa_group($expenses, fn($r) => $r['category'] ?? 'Other', 'taxAmount');
        $result['taxes'] = [
            'kpis' => [
                ['lbl' => 'Tax Collected', 'val' => pa_money($taxCollected), 'cls' => 'good'],
                ['lbl' => 'Tax Paid', 'val' => pa_money($taxPaid), 'cls' => 'bad'],
                ['lbl' => 'Net Tax Liability', 'val' => pa_money($taxCollected - $taxPaid), 'cls' => ''],
            ],
            'collectedVsPaid' => [
                'collected' => pa_month_series($revenue, 'date', 'taxAmount'),
                'paid' => pa_month_series($expenses, 'date', 'taxAmount'),
            ],
            'byCategory' => pa_top_pie(array_filter($taxByCat, fn($v) => $v > 0), 6),
        ];
    }

    // ---- Geographic (from customer + supplier countries) ----
    $destCountries = [];
    foreach ($revenue as $r) {
        $c = pa_customer_country($customers, $r['customerId'] ?? '');
        if ($c) { $destCountries[$c] = ($destCountries[$c] ?? 0) + (float) ($r['amount'] ?? 0); }
    }
    $origCountries = [];
    foreach ($expenses as $r) {
        $c = pa_supplier_country($suppliers, $r['supplierId'] ?? '');
        if ($c) { $origCountries[$c] = ($origCountries[$c] ?? 0) + (float) ($r['amount'] ?? 0); }
    }
    if ($destCountries || $origCountries) {
        $result['tabs'][] = 'geographic';
        $result['geographic'] = [
            'destination' => pa_top_pie($destCountries, 6),
            'origin' => pa_top_pie($origCountries, 6),
            'map' => array_map(fn($k, $v) => ['name' => $k, 'value' => round($v)], array_keys($destCountries), array_values($destCountries)),
        ];
    }

    // ---- Cleaned transactions table ----
    $cleaned = [];
    foreach ($revenue as $r) {
        $cleaned[] = ['date' => $r['date'] ?? '', 'description' => $r['description'] ?? '', 'category' => 'Sales income', 'type' => 'income', 'amount' => (float) ($r['amount'] ?? 0)];
    }
    foreach ($expenses as $r) {
        $cleaned[] = ['date' => $r['date'] ?? '', 'description' => $r['description'] ?? '', 'category' => $r['category'] ?? 'Other', 'type' => 'expense', 'amount' => (float) ($r['amount'] ?? 0)];
    }
    usort($cleaned, fn($a, $b) => strcmp($a['date'], $b['date']));
    $result['cleaned'] = $cleaned;

    return $result;
}

function pa_product_name(array $products, string $id): ?string
{
    foreach ($products as $p) { if (($p['id'] ?? '') === $id) { return $p['name'] ?? null; } }
    return null;
}
function pa_customer_name(array $customers, string $id): ?string
{
    foreach ($customers as $c) { if (($c['id'] ?? '') === $id) { return $c['name'] ?? null; } }
    return null;
}
function pa_customer_country(array $customers, string $id): ?string
{
    foreach ($customers as $c) { if (($c['id'] ?? '') === $id) { return $c['country'] ?? null; } }
    return null;
}
function pa_supplier_country(array $suppliers, string $id): ?string
{
    foreach ($suppliers as $s) { if (($s['id'] ?? '') === $id) { return $s['country'] ?? null; } }
    return null;
}

/** Monthly net profit (revenue - expenses) series. */
function pa_month_diff_series(array $revenue, array $expenses): array
{
    $rev = []; $exp = [];
    foreach ($revenue as $r) { $m = (int) date('n', strtotime($r['date'] ?? 'now')); $rev[$m] = ($rev[$m] ?? 0) + (float) ($r['amount'] ?? 0); }
    foreach ($expenses as $r) { $m = (int) date('n', strtotime($r['date'] ?? 'now')); $exp[$m] = ($exp[$m] ?? 0) + (float) ($r['amount'] ?? 0); }
    $months = array_unique(array_merge(array_keys($rev), array_keys($exp)));
    sort($months);
    $cats = []; $data = [];
    foreach ($months as $m) { $cats[] = date('M', mktime(0, 0, 0, $m, 1)); $data[] = round(($rev[$m] ?? 0) - ($exp[$m] ?? 0), 2); }
    return ['cats' => $cats, 'data' => $data];
}
