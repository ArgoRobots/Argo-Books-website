<?php
// sitemap-tools.xml.php
// XML sitemap for the invoice generator tool and its niche landing pages.
// Served at /sitemap-tools.xml via .htaccess rewrite.
//
// Kept separate from the main site sitemap so the tool ecosystem can be
// submitted to Search Console as its own property if needed.

require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [];

// The standalone tool page.
$urls[] = [
  'loc' => site_url('/invoice-generator/'),
  'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/invoice-generator/index.php')),
  'priority' => '0.9',
];

// Niche landing pages. Every niches/data/*.php (except the schema template)
// becomes one URL.
foreach (glob(__DIR__ . '/niches/data/*.php') as $file) {
  $slug = basename($file, '.php');
  if ($slug === '_template') continue;
  $data = require $file;
  $url_slug = $data['slug'] ?? $slug;
  // Generic seed lives at /free-invoice-generator/ (no slug in URL).
  $loc = $url_slug === 'generic'
    ? site_url('/free-invoice-generator/')
    : site_url("/free-invoice-generator/{$url_slug}/");
  $urls[] = [
    'loc' => $loc,
    'lastmod' => date('Y-m-d', filemtime($file)),
    'priority' => $url_slug === 'generic' ? '0.9' : '0.8',
  ];
}

// Template hub.
$urls[] = [
  'loc' => site_url('/invoice-template/'),
  'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/invoice-template/index.php')),
  'priority' => '0.8',
];

// Template format-generic and style-format pages. Every
// invoice-template/data/*.php (except the schema template) becomes one URL.
foreach (glob(__DIR__ . '/invoice-template/data/*.php') as $file) {
  $slug = basename($file, '.php');
  if ($slug === '_template') continue;
  $data = require $file;
  $url_slug = $data['slug'] ?? $slug;
  $kind = $data['kind'] ?? 'style-format';
  $urls[] = [
    'loc' => site_url("/invoice-template/{$url_slug}/"),
    'lastmod' => date('Y-m-d', filemtime($file)),
    // Format-generic pages slightly higher than style-format pages so
    // Search Console picks them as the entry point of the cluster.
    'priority' => $kind === 'format-generic' ? '0.8' : '0.7',
  ];
}

// Articles hub: curated editorial index of all the guides.
$urls[] = [
  'loc' => site_url('/invoice-guides/'),
  'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/invoice-guides/index.php')),
  'priority' => '0.8',
];

// Articles. Every articles/data/*.php (except the schema template) becomes
// one URL at the root-level path matching the slug.
foreach (glob(__DIR__ . '/articles/data/*.php') as $file) {
  $slug = basename($file, '.php');
  if ($slug === '_template') continue;
  $data = require $file;
  $url_slug = $data['slug'] ?? $slug;
  $urls[] = [
    'loc' => site_url("/{$url_slug}/"),
    'lastmod' => date('Y-m-d', filemtime($file)),
    'priority' => '0.7',
  ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap-0.9">' . "\n";
foreach ($urls as $u) {
  echo "  <url>\n";
  echo "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
  echo "    <lastmod>" . htmlspecialchars($u['lastmod']) . "</lastmod>\n";
  echo "    <priority>" . htmlspecialchars($u['priority']) . "</priority>\n";
  echo "  </url>\n";
}
echo '</urlset>' . "\n";
