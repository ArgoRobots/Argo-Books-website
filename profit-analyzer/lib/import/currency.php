<?php
// profit-analyzer/lib/import/currency.php
//
// Multi-currency support for the analyzer. The importer captures a per-row
// `originalCurrency` (an ISO code, or a symbol the AI lifted from the amount
// cell). This module:
//   1. picks the sheet's DOMINANT currency (the one most rows are in),
//   2. converts every monetary field on the money entities into it, using the
//      HISTORICAL USD-based rate for each row's own date, and
//   3. reports the dominant ISO code so meta/formatting can label it correctly.
//
// Rates come from the same `exchange_rates` table the desktop app's rate proxy
// (api/exchange-rates.php) fills, fetched on demand from OpenExchangeRates and
// cached permanently per date. Conversion is best-effort: if rates can't be
// obtained (no API key, network failure), it degrades to no conversion, so a
// single-currency sheet is still labeled right and a mixed sheet is no worse
// than before this feature existed.

// Cap on live (uncached) historical-rate fetches per analysis, so a file
// spanning hundreds of distinct dates can't fan out into hundreds of API calls.
// Past the cap, the most recent successfully-fetched rate set is reused.
const PA_FX_MAX_LIVE_FETCHES = 40;

/** Symbols that map unambiguously to one ISO code (used to read captured tokens). */
function pa_currency_symbol_map(): array
{
    return [
        '$' => 'USD', '€' => 'EUR', '£' => 'GBP', '¥' => 'JPY', '₹' => 'INR',
        '₩' => 'KRW', '₽' => 'RUB', '₺' => 'TRY', '₪' => 'ILS', '฿' => 'THB',
        'R$' => 'BRL', 'C$' => 'CAD', 'A$' => 'AUD', 'NZ$' => 'NZD', 'HK$' => 'HKD',
        'CHF' => 'CHF', 'zł' => 'PLN',
    ];
}

/** ISO code -> the symbol used when displaying amounts in that currency. */
function pa_currency_symbol(string $code): string
{
    $code = strtoupper(trim($code));
    static $m = [
        'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥', 'CNY' => '¥',
        'INR' => '₹', 'KRW' => '₩', 'BRL' => 'R$', 'CAD' => '$', 'AUD' => '$',
        'NZD' => '$', 'MXN' => '$', 'HKD' => '$', 'SGD' => '$', 'ZAR' => 'R',
        'RUB' => '₽', 'TRY' => '₺', 'ILS' => '₪', 'THB' => '฿',
        'CHF' => 'CHF ', 'SEK' => 'kr ', 'NOK' => 'kr ', 'DKK' => 'kr ', 'PLN' => 'zł ',
    ];
    if (isset($m[$code])) {
        return $m[$code];
    }
    // Unknown but plausible code: prefix the code itself (e.g. "AED 1,200").
    return $code !== '' ? $code . ' ' : '$';
}

/**
 * Normalize a captured currency token (a symbol like "£" or a code like "gbp")
 * to an ISO-4217 code, or null if blank/unrecognized. Ambiguous "$" -> USD.
 */
function pa_currency_canon(?string $raw): ?string
{
    if ($raw === null) {
        return null;
    }
    $s = trim($raw);
    if ($s === '') {
        return null;
    }

    $u = strtoupper($s);
    if (preg_match('/^[A-Z]{3}$/', $u)) {
        return $u; // already an ISO code
    }

    $map = pa_currency_symbol_map();
    if (isset($map[$s])) {
        return $map[$s];
    }
    if (isset($map[$u])) {
        return $map[$u];
    }
    // Token contains a known glyph somewhere (e.g. "£100" slipped through).
    foreach ($map as $sym => $code) {
        if (mb_strpos($s, $sym) !== false) {
            return $code;
        }
    }
    return null;
}

/**
 * USD-based rate table (code => units per 1 USD, with 'USD' => 1.0) for a date.
 * Reads the exchange_rates cache first, then fetches+caches from
 * OpenExchangeRates. Returns null only when nothing usable can be obtained.
 * Memoized per date; live fetches are capped per request.
 */
function pa_currency_rates(string $date): ?array
{
    global $pdo;
    static $memo = [];
    static $liveFetches = 0;
    static $lastGood = null;

    $today = date('Y-m-d');
    $date = preg_match('/^\d{4}-\d{2}-\d{2}/', $date) ? substr($date, 0, 10) : $today;
    if ($date > $today) {
        $date = $today; // no future rates
    }

    if (array_key_exists($date, $memo)) {
        return $memo[$date];
    }

    // 1. Cache table (historical rates are permanent; today's may be refreshed).
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare('SELECT rates FROM exchange_rates WHERE rate_date = ?');
            $stmt->execute([$date]);
            $raw = $stmt->fetchColumn();
            if ($raw) {
                $rates = json_decode($raw, true);
                if (is_array($rates) && $rates) {
                    $rates['USD'] = 1.0;
                    $memo[$date] = $rates;
                    $lastGood = $rates;
                    return $rates;
                }
            }
        } catch (Throwable $e) {
            // fall through to fetch
        }
    }

    // 2. Bounded live fetch from OpenExchangeRates.
    if ($liveFetches >= PA_FX_MAX_LIVE_FETCHES) {
        return $memo[$date] = $lastGood;
    }
    $apiKey = $_ENV['OPENEXCHANGERATES_API_KEY'] ?? getenv('OPENEXCHANGERATES_API_KEY') ?: '';
    if (!$apiKey) {
        return $memo[$date] = $lastGood;
    }

    $isLatest = ($date === $today);
    $url = $isLatest
        ? "https://openexchangerates.org/api/latest.json?app_id={$apiKey}&base=USD"
        : "https://openexchangerates.org/api/historical/{$date}.json?app_id={$apiKey}&base=USD";

    $liveFetches++;
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 12, CURLOPT_CONNECTTIMEOUT => 5]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resp === false || $code !== 200) {
        return $memo[$date] = $lastGood;
    }
    $data = json_decode($resp, true);
    if (!isset($data['rates']) || !is_array($data['rates'])) {
        return $memo[$date] = $lastGood;
    }

    if (isset($pdo)) {
        try {
            $pdo->prepare(
                'INSERT INTO exchange_rates (rate_date, rates, fetched_at) VALUES (?, ?, NOW())
                 ON DUPLICATE KEY UPDATE rates = VALUES(rates), fetched_at = NOW()'
            )->execute([$date, json_encode($data['rates'])]);
        } catch (Throwable $e) {
            // caching is best-effort
        }
    }

    $rates = $data['rates'];
    $rates['USD'] = 1.0;
    $memo[$date] = $rates;
    $lastGood = $rates;
    return $rates;
}

/** Convert an amount between two ISO codes via the USD-based rate table. */
function pa_currency_convert(float $amount, string $from, string $to, ?array $rates): float
{
    if ($from === $to || $amount == 0.0) {
        return $amount;
    }
    if (!$rates) {
        return $amount; // no rates available: leave untouched
    }
    $rf = $rates[$from] ?? null;
    $rt = $rates[$to] ?? null;
    if ($rf === null || $rt === null || (float) $rf == 0.0) {
        return $amount; // unknown currency: leave untouched
    }
    return $amount / (float) $rf * (float) $rt; // from -> USD -> to
}

/**
 * Detect the dominant currency across the money entities and convert every
 * monetary field into it (historical per-row rate). Mutates $entities in place
 * and returns the dominant ISO code. When no currency is detected anywhere,
 * returns 'USD' and changes nothing (legacy behavior).
 */
function pa_apply_currency(array &$entities): string
{
    // Money entities -> the fields on each that hold an amount.
    $moneyFields = [
        'revenue'  => ['unitPrice', 'amount', 'taxAmount', 'total'],
        'expenses' => ['amount', 'taxAmount', 'total'],
        'invoices' => ['subtotal', 'taxAmount', 'total', 'amountPaid', 'balance'],
        'payments' => ['amount', 'taxAmount', 'total'],
    ];

    // 1. Tally detected currencies to find the dominant (by row count).
    $counts = [];
    foreach (['revenue', 'expenses', 'invoices'] as $k) {
        foreach ($entities[$k] ?? [] as $r) {
            $c = pa_currency_canon($r['originalCurrency'] ?? null);
            if ($c) {
                $counts[$c] = ($counts[$c] ?? 0) + 1;
            }
        }
    }
    if (!$counts) {
        return 'USD'; // no currency signal anywhere
    }
    arsort($counts);
    $dominant = array_key_first($counts);

    // 2. If more than one currency is present, convert the minority rows.
    if (count($counts) > 1) {
        foreach ($moneyFields as $k => $fields) {
            if (empty($entities[$k])) {
                continue;
            }
            foreach ($entities[$k] as &$r) {
                $from = pa_currency_canon($r['originalCurrency'] ?? null) ?? $dominant;
                if ($from === $dominant) {
                    continue;
                }
                $date = (string) ($r['date'] ?? ($r['issueDate'] ?? ''));
                $rates = pa_currency_rates($date);
                foreach ($fields as $f) {
                    if (isset($r[$f]) && is_numeric($r[$f])) {
                        $r[$f] = round(pa_currency_convert((float) $r[$f], $from, $dominant, $rates), 2);
                    }
                }
                $r['originalCurrency'] = $dominant; // row is now expressed in the dominant currency
            }
            unset($r);
        }
    }

    return $dominant;
}
