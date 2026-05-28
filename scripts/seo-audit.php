<?php
// scripts/seo-audit.php
//
// End-to-end SEO audit for the invoice generator ecosystem.
// Reads sitemap-tools.xml and the main sitemap.xml, then visits each URL
// and records: HTTP status, canonical href, robots meta, JSON-LD @type
// values found. Also probes a handful of known-bad slugs to confirm 404s.
//
// Usage:
//   php scripts/seo-audit.php
//   php scripts/seo-audit.php --base=https://argorobots.com
//   php scripts/seo-audit.php --base=http://localhost/argo-books-website --out=read-me/seo/phase-e-audit-2026-05-28.md
//
// Exit code is 0 if no findings, 1 if any finding was recorded.

declare(strict_types=1);

$opts = getopt('', ['base::', 'out::']);
$base = rtrim($opts['base'] ?? 'http://argo-books-website.test', '/');
$out_path = $opts['out'] ?? (__DIR__ . '/../read-me/seo/phase-e-audit-' . date('Y-m-d') . '.md');

$findings = [];
$rows = [];

function fetch(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_USERAGENT => 'argo-seo-audit/1.0',
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HEADER => false,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return ['status' => $status, 'body' => $body === false ? '' : (string) $body];
}

function extract_canonical(string $html): ?string {
    if (preg_match('#<link\s+[^>]*rel=["\']canonical["\'][^>]*href=["\']([^"\']+)["\']#i', $html, $m)) {
        return $m[1];
    }
    if (preg_match('#<link\s+[^>]*href=["\']([^"\']+)["\'][^>]*rel=["\']canonical["\']#i', $html, $m)) {
        return $m[1];
    }
    return null;
}

function extract_robots_meta(string $html): ?string {
    if (preg_match('#<meta\s+[^>]*name=["\']robots["\'][^>]*content=["\']([^"\']+)["\']#i', $html, $m)) {
        return $m[1];
    }
    return null;
}

function extract_jsonld_types(string $html): array {
    if (!preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $matches)) {
        return [];
    }
    $types = [];
    foreach ($matches[1] as $blob) {
        $decoded = json_decode(trim($blob), true);
        if (!is_array($decoded)) continue;
        $walk = function ($node) use (&$walk, &$types) {
            if (is_array($node)) {
                if (isset($node['@type'])) {
                    if (is_array($node['@type'])) $types = array_merge($types, $node['@type']);
                    else $types[] = $node['@type'];
                }
                foreach ($node as $v) if (is_array($v)) $walk($v);
            }
        };
        $walk($decoded);
    }
    return array_values(array_unique($types));
}

function urls_from_sitemap(string $xml): array {
    if (!preg_match_all('#<loc>([^<]+)</loc>#i', $xml, $m)) return [];
    return $m[1];
}

// 1. Pull both sitemaps and collect every URL they emit.
$tools_sitemap = fetch("{$base}/sitemap-tools.xml");
$main_sitemap = fetch("{$base}/sitemap.xml");

if ($tools_sitemap['status'] !== 200) {
    $findings[] = "sitemap-tools.xml returned HTTP {$tools_sitemap['status']}";
}
if ($main_sitemap['status'] !== 200) {
    $findings[] = "sitemap.xml returned HTTP {$main_sitemap['status']}";
}

$urls = array_merge(
    urls_from_sitemap($tools_sitemap['body']),
    urls_from_sitemap($main_sitemap['body'])
);
$urls = array_values(array_unique($urls));

// 2. Visit each URL and capture audit signals.
foreach ($urls as $url) {
    // Rewrite the production host to whatever the audit base is, so we
    // exercise the local server when running against Laragon.
    $audit_url = preg_replace('#^https?://argorobots\.com#', $base, $url);
    $resp = fetch($audit_url);
    $row = [
        'url' => $url,
        'status' => $resp['status'],
        'canonical' => extract_canonical($resp['body']),
        'robots' => extract_robots_meta($resp['body']),
        'jsonld' => extract_jsonld_types($resp['body']),
    ];
    $rows[] = $row;

    if ($resp['status'] !== 200) {
        $findings[] = "{$url}: expected HTTP 200, got {$resp['status']}";
    }
    if ($row['robots'] && stripos($row['robots'], 'noindex') !== false) {
        $findings[] = "{$url}: stray noindex on a happy-path URL ({$row['robots']})";
    }
    // Canonical must point to the production hostname, not the local one,
    // and must match the URL the sitemap emitted (path-only equality).
    if ($row['canonical']) {
        $can_path = parse_url($row['canonical'], PHP_URL_PATH) ?? '';
        $url_path = parse_url($url, PHP_URL_PATH) ?? '';
        if (rtrim($can_path, '/') !== rtrim($url_path, '/')) {
            $findings[] = "{$url}: canonical path mismatch (canonical=" . $row['canonical'] . ")";
        }
        if (strpos($row['canonical'], 'argorobots.com') === false) {
            $findings[] = "{$url}: canonical does not point to argorobots.com (got " . $row['canonical'] . ")";
        }
    } else {
        $findings[] = "{$url}: missing canonical";
    }
}

// 3. Bad-slug probes. Each section should return 404 (not 200, not 500) on garbage slugs.
$bad_slug_probes = [
    '/free-invoice-generator/this-slug-does-not-exist/',
    '/invoice-template/this-slug-does-not-exist/',
];
$bad_slug_results = [];
foreach ($bad_slug_probes as $path) {
    $resp = fetch($base . $path);
    $bad_slug_results[$path] = $resp['status'];
    if ($resp['status'] !== 404) {
        $findings[] = "{$path}: expected 404 for unknown slug, got {$resp['status']}";
    }
}

// 4. robots.txt sanity.
$robots = fetch("{$base}/robots.txt");
if ($robots['status'] !== 200) {
    $findings[] = "robots.txt returned HTTP {$robots['status']}";
} else {
    $expected_allows = ['/invoice-generator/', '/free-invoice-generator/', '/invoice-template/', '/invoice-guides/'];
    foreach ($expected_allows as $path) {
        if (preg_match('#Disallow:\s*' . preg_quote($path, '#') . '\b#i', $robots['body'])) {
            $findings[] = "robots.txt explicitly disallows {$path}";
        }
    }
    if (!preg_match('#Sitemap:\s*\S*sitemap-tools\.xml#i', $robots['body'])) {
        $findings[] = "robots.txt does not reference sitemap-tools.xml";
    }
}

// 5. Write the report.
ob_start();
echo "# Phase E SEO Audit Report\n\n";
echo "Run: " . date('c') . "\n";
echo "Base: {$base}\n\n";
echo "## Summary\n\n";
echo count($findings) === 0
    ? "All checks passed.\n\n"
    : count($findings) . " finding(s):\n\n" . implode("\n", array_map(fn($f) => "- {$f}", $findings)) . "\n\n";
echo "## URL audit\n\n";
echo "| URL | Status | Canonical | Robots | JSON-LD types |\n";
echo "|---|---|---|---|---|\n";
foreach ($rows as $r) {
    $can = $r['canonical'] ?: 'MISSING';
    $robots_field = $r['robots'] ?: '';
    $jsonld = implode(', ', $r['jsonld']);
    echo "| `{$r['url']}` | {$r['status']} | `{$can}` | {$robots_field} | {$jsonld} |\n";
}
echo "\n## Bad-slug 404 probes\n\n";
foreach ($bad_slug_results as $p => $status) {
    $ok = $status === 404 ? 'OK' : 'FAIL';
    echo "- `{$p}`: HTTP {$status} ({$ok})\n";
}
$report = ob_get_clean();

$dir = dirname($out_path);
if (!is_dir($dir)) mkdir($dir, 0775, true);
file_put_contents($out_path, $report);

fwrite(STDOUT, "Audit complete. Report: {$out_path}\n");
fwrite(STDOUT, "Findings: " . count($findings) . "\n");
exit(count($findings) === 0 ? 0 : 1);
