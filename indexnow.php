<?php
// indexnow.php
//
// Thin client for the IndexNow protocol (https://www.indexnow.org/). One POST
// notifies every participating engine at once (Bing, Yandex, DuckDuckGo,
// Seznam, Naver, ...). Google does NOT participate, so this never affects
// Google indexing; keep using Search Console for Google.
//
// Ownership is proven by a key file hosted at the site root. The file name and
// its contents are both the key:
//   https://argorobots.com/<key>.txt   ->   <key>
//
// The key defaults to the committed key file's name and can be overridden with
// the INDEXNOW_KEY env var (e.g. after rotating the key in Bing Webmaster
// Tools). Submitted URLs must share the host of the key file, which they do
// because callers build them from site_url().

require_once __DIR__ . '/env_helper.php';

// Official neutral IndexNow endpoint (operated by the IndexNow initiative,
// founded by Microsoft Bing + Yandex). Submitting here propagates to every
// participating engine. Microsoft's own https://www.bing.com/indexnow is an
// equivalent drop-in if you'd rather POST directly to Bing.
const INDEXNOW_ENDPOINT = 'https://api.indexnow.org/indexnow';

/** The IndexNow key (also the key-file name). */
function indexnow_key(): string
{
    return (string) env('INDEXNOW_KEY', '77469e7877e34a30ab5fab27e275650e');
}

/** Absolute URL of the hosted key file, on the canonical site host. */
function indexnow_key_location(): string
{
    return site_url('/' . indexnow_key() . '.txt');
}

/**
 * Submit a batch of absolute URLs to IndexNow.
 *
 * All URLs must belong to the same host as the key file. The host is derived
 * from the first URL, so pass URLs built with site_url().
 *
 * @param string[] $urls Absolute URLs (deduplicated and re-batched internally).
 * @return array{ok:bool, attempted:int, batches:array<int,array{count:int,http:int,ok:bool,error:?string}>}
 */
function indexnow_submit(array $urls): array
{
    $urls = array_values(array_unique(array_filter($urls, static fn($u) => is_string($u) && $u !== '')));
    $result = ['ok' => true, 'attempted' => count($urls), 'batches' => []];
    if (!$urls) {
        return $result;
    }

    $host = parse_url($urls[0], PHP_URL_HOST);
    if (!$host) {
        return ['ok' => false, 'attempted' => count($urls), 'batches' => [
            ['count' => count($urls), 'http' => 0, 'ok' => false, 'error' => 'could not parse host from first URL'],
        ]];
    }

    // The protocol allows up to 10,000 URLs per request; we chunk well under
    // that so a single oversized batch can never be rejected wholesale.
    foreach (array_chunk($urls, 1000) as $chunk) {
        $payload = json_encode([
            'host'        => $host,
            'key'         => indexnow_key(),
            'keyLocation' => indexnow_key_location(),
            'urlList'     => array_values($chunk),
        ], JSON_UNESCAPED_SLASHES);

        $ch = curl_init(INDEXNOW_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json; charset=utf-8'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = ($body === false) ? curl_error($ch) : null;
        unset($ch); // curl_close() is a deprecated no-op on PHP 8+

        // 200 = accepted, 202 = accepted (queued). Anything else is a failure.
        $ok = in_array($http, [200, 202], true);
        if (!$ok) {
            $result['ok'] = false;
        }
        $result['batches'][] = [
            'count' => count($chunk),
            'http'  => $http,
            'ok'    => $ok,
            'error' => $curlErr ?? ($ok ? null : (trim((string) $body) ?: indexnow_http_reason($http))),
        ];
    }

    return $result;
}

/** Human-readable reason for the documented IndexNow error codes. */
function indexnow_http_reason(int $http): string
{
    return [
        400 => 'Bad request (invalid format)',
        403 => 'Forbidden (key not valid / key file not found)',
        422 => 'Unprocessable (URL host mismatch or key not matching schema)',
        429 => 'Too many requests (throttled as potential spam)',
    ][$http] ?? "HTTP $http";
}
