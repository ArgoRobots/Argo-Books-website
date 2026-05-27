<?php
// invoice-generator/index.php
// Standalone free invoice generator tool page.
// Thin shell: sets page metadata, embeds the invoice surface from _fragment.php,
// then defers to layout.php for the HTML shell.

$page_title = 'Free Invoice Generator | Argo Books';
$page_description = 'Free online invoice generator. No signup required. Download PDF or Word.';
$canonical_url = 'https://argorobots.com/invoice-generator/';

// Server-side page view. track_page_view() filters admins, bots, and duplicates
// itself, so calling it unconditionally is safe. Skip during PHP CLI smoke
// tests (no $_SERVER['REMOTE_ADDR'], no real visitor).
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('invgen_tool');
}

// Referral source baked into every conversion-pitch CTA inside _fragment.php.
// Standalone tool page uses 'invgen-tool'.
$invgen_ref = 'invgen-tool';

// Show the hero (H1 + tagline) only on the standalone tool page. Niche pages
// have their own H1 from the niche data file and skip this.
$show_tool_hero = true;

$page_schema_json = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'SoftwareApplication',
  'name' => 'Free Invoice Generator',
  'applicationCategory' => 'BusinessApplication',
  'operatingSystem' => 'Web',
  'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
  'creator' => ['@id' => 'https://argorobots.com/#organization'],
  'url' => 'https://argorobots.com/invoice-generator/',
], JSON_UNESCAPED_SLASHES);

ob_start();
include __DIR__ . '/_fragment.php';
$body_content = ob_get_clean();

require_once __DIR__ . '/_base.php';
$extra_scripts = '<script type="module" src="' . INVGEN_BASE . '/invoice-generator/scripts/main.js"></script>';

include __DIR__ . '/layout.php';
