<?php
// free-receipt-scanner/index.php
// Free public AI receipt scanner. Tool-isolated landing in the same design
// system as the Profit Analyzer (Fraunces + Hanken Grotesk, navy hero, light
// cards). Mirrors the desktop scanner's review UI; funnels to the free app +
// Premium at the daily limit.

require_once __DIR__ . '/../invoice-generator/_base.php'; // defines INVGEN_BASE

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php'; // self-loads db_connect.php
    track_page_view('receipt_scanner_tool');
}

require_once __DIR__ . '/../config/pricing.php';
$cfg = get_pricing_config();
$perVisitor = $cfg['web_receipt_scan_daily_limit'];
$base = INVGEN_BASE; // '' in production, '/argo-books-website' on Laragon
$dl = $base . '/downloads/?source=receipt-scanner';

$canonical = 'https://argorobots.com/free-receipt-scanner/';
$title = 'Free Receipt Scanner Online — reads every line and tax | Argo Books';
$description = 'Scan a receipt and get every line item, each tax line (GST, HST, PST), and the total in any currency. Free, no signup. Download as CSV or JSON.';

$schema = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'SoftwareApplication',
  'name' => 'Free Receipt Scanner',
  'applicationCategory' => 'FinanceApplication',
  'operatingSystem' => 'Web',
  'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
  'creator' => ['@id' => 'https://argorobots.com/#organization'],
  'url' => $canonical,
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$turnstileSiteKey = $_ENV['TURNSTILE_SITE_KEY'] ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<link rel="canonical" href="<?= $canonical ?>">
<link rel="icon" href="<?= $base ?>/resources/images/argo-logo/argo-icon.ico" sizes="any">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:url" content="<?= $canonical ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Argo Books">
<script type="application/ld+json"><?= $schema ?></script>
<link rel="stylesheet" href="<?= $base ?>/profit-analyzer/assets/fonts.css">
<link rel="stylesheet" href="<?= $base ?>/resources/styles/custom-colors.css">
<link rel="stylesheet" href="<?= $base ?>/free-receipt-scanner/scanner.css">
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback&render=explicit" async defer></script>
<script>window.RS_CONFIG = <?= json_encode([
  'base' => $base,
  'turnstileSiteKey' => $turnstileSiteKey,
  'perVisitor' => $perVisitor,
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
</head>
<body>

<!-- NAV -->
<nav class="rs-nav">
  <div class="rs-wrap">
    <a class="rs-brand" href="<?= $base ?>/">
      <img class="rs-logo-light" src="<?= $base ?>/resources/images/argo-logo/argo-logo-black.png" alt="Argo Books" width="160" height="30">
      <img class="rs-logo-dark" src="<?= $base ?>/resources/images/argo-logo/argo-logo-white.png" alt="Argo Books" width="160" height="30">
    </a>
    <div class="rs-nav-right">
      <a class="rs-nav-link" href="#how">How it works</a>
      <a class="rs-nav-link" href="#privacy">Privacy</a>
    </div>
  </div>
</nav>

<!-- HERO + TOOL -->
<header class="rs-hero">
  <div class="rs-wrap rs-hero-wrap">
    <div class="rs-eyebrow rs-rise rs-d1">Free receipt scanner · no signup</div>
    <h1 class="rs-rise rs-d2">Read every line on<br><em>any receipt</em>.</h1>
    <p class="rs-hero-sub rs-rise rs-d3">Upload a photo and get every line item, each tax line, and the total in seconds. Most scanners stop at the total. This reads the whole thing.</p>

    <div id="rs-stage" class="rs-stage rs-rise rs-d4" data-state="upload">
      <!-- UPLOAD -->
      <div id="rs-upload" class="rs-upload">
        <div id="rs-dropzone" class="rs-dropzone" tabindex="0" role="button" aria-label="Upload a receipt">
          <div class="rs-dropzone-ic" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 18v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/></svg>
          </div>
          <div class="rs-dropzone-title">Drop your receipt here</div>
          <div class="rs-dropzone-sub">A photo or a scan · JPEG, PNG, or WebP · drop several to scan in bulk</div>
          <span class="rs-pick">Choose files</span>
          <input type="file" id="rs-file-input" accept="image/jpeg,image/png,image/webp" multiple hidden>
        </div>
        <div class="rs-sample">or <a id="rs-sample" href="#">try it with a sample receipt →</a></div>
        <div id="rs-turnstile" class="rs-turnstile"></div>
        <div class="rs-trust-line">
          <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Processed in memory</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Never stored</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Every tax line, separately</span>
          <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> No account needed</span>
        </div>
        <p class="rs-scans-left"><span id="rs-scans-left"><?= (int)$perVisitor ?></span> free scans left today</p>
      </div>

      <!-- REVIEW (filled by scanner.js) -->
      <div id="rs-review" class="rs-review" hidden></div>

      <!-- LIMIT / CAPACITY (filled by scanner.js) -->
      <div id="rs-limit" class="rs-limit" hidden></div>
    </div>
  </div>
</header>

<!-- HOW IT WORKS -->
<section class="rs-block" id="how">
  <div class="rs-wrap">
    <div class="rs-kicker">Three steps</div>
    <h2 class="rs-h2">Receipt to data in seconds</h2>
    <div class="rs-steps">
      <div class="rs-step"><div class="rs-n">1</div><h3>Upload a receipt</h3><p>Snap a photo or drop a scan. Crumpled, faded, tilted, the same AI engine inside Argo Books handles it.</p></div>
      <div class="rs-step"><div class="rs-n">2</div><h3>It reads every line</h3><p>Line items, each tax, discounts, totals, currency, and payment method, pulled out and laid out for you.</p></div>
      <div class="rs-step"><div class="rs-n">3</div><h3>Edit and export</h3><p>Fix anything that needs it, then copy the data or download it as CSV or JSON.</p></div>
    </div>
  </div>
</section>

<!-- WHAT IT READS -->
<section class="rs-block rs-block-alt">
  <div class="rs-wrap">
    <div class="rs-kicker">More than the total</div>
    <h2 class="rs-h2">What it pulls off your receipt</h2>
    <div class="rs-finds">
      <div class="rs-find"><div class="rs-ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Every line item</b><p>Each product, its quantity, unit price, and line total, in the order they appear.</p></div></div>
      <div class="rs-find"><div class="rs-ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Each tax line, separately</b><p>GST, HST, PST, QST, and VAT broken out on their own, so the numbers match your books.</p></div></div>
      <div class="rs-find"><div class="rs-ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Totals and discounts</b><p>Subtotal, discounts, and the final total, with a confidence score on the read.</p></div></div>
      <div class="rs-find"><div class="rs-ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Currency and payment</b><p>The currency is inferred from the receipt, plus how it was paid.</p></div></div>
    </div>
  </div>
</section>

<!-- PRIVACY -->
<section class="rs-block" id="privacy">
  <div class="rs-wrap">
    <div class="rs-trust-card">
      <div class="rs-trust-hd">
        <div class="rs-lock"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div>
        <h3>Your receipt stays yours</h3>
      </div>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Processed in memory, never written to disk</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Nothing is saved after you get your result</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Read by paid AI that never trains on your data</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> No account or email required</li>
      </ul>
    </div>
  </div>
</section>

<!-- BRIDGE CTA -->
<section class="rs-block">
  <div class="rs-wrap">
    <div class="rs-bridge">
      <h2>This reads one receipt.<br>Argo Books files them all.</h2>
      <p>Scan receipts right inside Argo Books and each one is saved and filed as an expense automatically, alongside your invoices, taxes, and reports. The free plan includes <?= (int)$cfg['free_receipt_scan_monthly_limit'] ?> scans a month.</p>
      <div class="rs-bridge-cta">
        <a class="rs-btn rs-btn-primary rs-btn-lg" href="<?= $dl ?>-bridge">Get Argo Books for free →</a>
        <a class="rs-bridge-link" href="<?= $base ?>/pricing/?source=receipt-scanner-bridge">See Premium (<?= (int)$cfg['receipt_scan_monthly_limit'] ?>/mo)</a>
      </div>
    </div>
  </div>
</section>

<footer class="rs-footer">
  <div><a href="<?= $base ?>/legal/privacy.php">Privacy &amp; Data</a> · <a href="<?= $base ?>/legal/terms.php">Terms of Use</a></div>
  © <?= date('Y') ?> Argo Books · Built for small businesses
</footer>

<!-- Scanning overlay (single + bulk progress) -->
<div class="rs-overlay" id="rs-scanning" hidden>
  <div class="rs-overlay-card">
    <div class="rs-overlay-spinner"></div>
    <div class="rs-overlay-title" id="rs-overlay-title">Reading your receipt…</div>
    <div class="rs-overlay-sub" id="rs-overlay-sub">Pulling out every line, tax, and total. This takes a few seconds.</div>
    <div class="rs-bulk" id="rs-bulk" hidden>
      <div class="rs-bulk-bar"><div class="rs-bulk-fill" id="rs-bulk-fill"></div></div>
      <ul class="rs-bulk-list" id="rs-bulk-list"></ul>
    </div>
    <button type="button" class="rs-cancel" id="rs-cancel">Cancel</button>
  </div>
</div>

<!-- FingerprintJS is imported inside scanner.js as an ES module; no separate tag needed. -->
<script src="<?= $base ?>/free-receipt-scanner/scanner.js" type="module"></script>
</body>
</html>
