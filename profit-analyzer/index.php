<?php
// profit-analyzer/index.php
// Owner-facing landing page for the free Profit Analyzer tool.
// Tool-isolated (own CSS, no main site header/footer): a focused, one-way
// conversion funnel into Argo Books, matching the approved design mockup.

require_once __DIR__ . '/../invoice-generator/_base.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../statistics.php';
    track_page_view('profit_analyzer');
}

$canonical = 'https://argorobots.com/profit-analyzer/';
$title = 'Free Profit Analyzer — see where your business is losing money | Argo Books';
$description = 'Upload your spreadsheet and instantly see where your business is losing money: fees, unprofitable products, and your true margin. Free, no signup.';

// Conversion CTA target + tracking. "Try Argo" funnels into the download page.
$cta = INVGEN_BASE . '/downloads/?source=profit-analyzer-tool&amp;utm_source=profit-analyzer&amp;utm_medium=tool&amp;utm_campaign=launch';
$results = INVGEN_BASE . '/profit-analyzer/results/';

$schema = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'SoftwareApplication',
  'name' => 'Free Profit Analyzer',
  'applicationCategory' => 'FinanceApplication',
  'operatingSystem' => 'Web',
  'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
  'creator' => ['@id' => 'https://argorobots.com/#organization'],
  'url' => $canonical,
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<link rel="canonical" href="<?= $canonical ?>">
<link rel="icon" href="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-icon.ico" sizes="any">
<meta property="og:title" content="<?= htmlspecialchars($title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:url" content="<?= $canonical ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Argo Books">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
<script type="application/ld+json"><?= $schema ?></script>
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/fonts.css">
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/profit-analyzer.css">
</head>
<body>

<!-- NAV -->
<nav>
  <div class="wrap">
    <a class="brand" href="<?= INVGEN_BASE ?>/profit-analyzer/"><img src="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-logo-white.png" alt="Argo Books" width="160" height="30"></a>
    <div class="links">
      <a href="#how">How it works</a>
      <a href="#trust">Privacy</a>
      <a class="btn btn-primary" href="<?= $cta ?>">Try Argo free</a>
    </div>
  </div>
</nav>

<!-- HERO -->
<header class="hero">
  <div class="wrap">
    <div class="eyebrow rise d1">Free profit analyzer · no signup</div>
    <h1 class="rise d2">See exactly where your<br>business is <em>losing money</em>.</h1>
    <p class="sub rise d3">Upload your spreadsheet and get a clear, honest picture of your numbers in about 60 seconds. Free.</p>

    <a class="upload rise d4" href="<?= $results ?>">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 18v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/></svg></div>
      <div class="big">Drop your spreadsheet here</div>
      <div class="small">.xlsx or .csv · any spreadsheet or export</div>
      <span class="pick">Choose file</span>
    </a>
    <div class="or-sample rise d4">or <a href="<?= $results ?>">try it with sample data →</a></div>

    <div class="trust-line rise d5">
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg> Encrypted</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="m3 6 1 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-14M8 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg> Deleted after analysis</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Never used to train AI</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> No account needed</span>
    </div>
  </div>
</header>

<!-- SAMPLE DASHBOARD -->
<section class="block" id="sample">
  <div class="wrap">
    <div class="kicker">Here's what you'll see</div>
    <h2 class="h2">A clear read on your money</h2>
    <p class="sub2">Example below uses a sample Shopify seller's data, the real thing renders on your own numbers.</p>

    <div class="preview-shell">
      <div class="preview-bar">
        <svg class="file-ic" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 9h16M4 15h16M10 3v18"/></svg>
        <span class="file">maple-goods-sales-2024.xlsx</span>
        <span class="badge">✓ 1,284 rows analyzed</span>
      </div>
      <div class="preview-body">

        <div class="insight">
          <div class="warn"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg></div>
          <div>
            <h3>You're losing 9% of revenue to payment &amp; processing fees</h3>
            <p>That's <b>$1,840</b> over the last 90 days, and after every cost you keep just <b>19%</b> as profit.</p>
          </div>
        </div>

        <div class="grid">
          <!-- MONEY FLOW SANKEY -->
          <div class="flowcard" id="flowCard">
            <div class="flowhead">
              <div>
                <h3 class="flowttl">Follow your money</h3>
                <div class="meta">Where every dollar of revenue goes before it reaches you</div>
              </div>
              <div class="keptstat"><b>19%</b><span>kept as profit</span></div>
            </div>
            <div id="sankeyChart" style="width:100%;height:360px"></div>
          </div>

          <!-- REVENUE BY PRODUCT -->
          <div class="chartcard row2">
            <div class="ttl">Top products by revenue</div>
            <div class="meta">Revenue per product, last 90 days</div>
            <div class="revbars">
              <div class="rrow"><div class="rname">Totes</div><div class="rtrack"><div class="rbar" style="width:100%;--dl:.05s"></div></div><div class="rval">$8,240</div></div>
              <div class="rrow"><div class="rname">Mugs</div><div class="rtrack"><div class="rbar" style="width:78%;--dl:.12s"></div></div><div class="rval">$6,460</div></div>
              <div class="rrow"><div class="rname">Candles</div><div class="rtrack"><div class="rbar" style="width:57%;--dl:.19s"></div></div><div class="rval">$4,720</div></div>
              <div class="rrow"><div class="rname">Greeting cards</div><div class="rtrack"><div class="rbar" style="width:25%;--dl:.26s"></div></div><div class="rval">$2,040</div></div>
              <div class="rrow"><div class="rname">Enamel pins</div><div class="rtrack"><div class="rbar" style="width:16%;--dl:.33s"></div></div><div class="rval">$1,280</div></div>
              <div class="rrow"><div class="rname">Stickers</div><div class="rtrack"><div class="rbar" style="width:12%;--dl:.4s"></div></div><div class="rval">$960</div></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="block" id="how" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="kicker">Dead simple</div>
    <h2 class="h2">Three steps, sixty seconds</h2>
    <p class="sub2"></p>
    <div class="steps">
      <div class="step"><div class="n">1</div><h3>Upload your file</h3><p>Drag in any spreadsheet or export. Messy, multi-tab, weird column names, our importer handles it.</p></div>
      <div class="step"><div class="n">2</div><h3>We read it instantly</h3><p>The same AI engine inside Argo Books figures out what each column means, no manual mapping.</p></div>
      <div class="step"><div class="n">3</div><h3>See your money leaks</h3><p>Clear charts and plain-language insights about where your profit is actually going.</p></div>
    </div>
  </div>
</section>

<!-- WHAT IT FINDS -->
<section class="block">
  <div class="wrap">
    <div class="kicker">No fluff</div>
    <h2 class="h2">What it finds in your numbers</h2>
    <p class="sub2"></p>
    <div class="finds">
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Fee &amp; processing drag</b><p>How much of every sale disappears before it reaches you.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Top and bottom sellers</b><p>Which products and services bring in the most revenue, and which barely move.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Your biggest expenses</b><p>Where the money goes, ranked, with no digging required.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Your true margin</b><p>The real number after everything, not the one you hope for.</p></div></div>
    </div>
  </div>
</section>

<!-- TRUST -->
<section class="block" id="trust" style="background:#fff;border-top:1px solid var(--line)">
  <div class="wrap">
    <div class="trust-card">
      <div class="hd">
        <div class="lock"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div>
        <h3>Your data is safe, and stays yours</h3>
      </div>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Encrypted in transit and at rest</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Automatically deleted after analysis</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Processed by paid AI that never trains on your data</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> No account or email required to see results</li>
      </ul>
    </div>
  </div>
</section>

<!-- BRIDGE CTA -->
<section class="block">
  <div class="wrap">
    <div class="bridge">
      <h2>This is a one-time snapshot.<br>Argo keeps it true every day.</h2>
      <p>Argo Books tracks your profit automatically, all year, plus invoices, expenses, and tax-ready reports. One affordable app instead of a spreadsheet you rebuild every month.</p>
      <a class="btn btn-primary btn-lg" href="<?= $cta ?>">Try Argo Books free →</a>
      <a class="mini-upload" href="<?= $results ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 18v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/></svg>
        Analyze another spreadsheet — free
      </a>
    </div>
  </div>
</section>

<footer>
  <div style="margin-bottom:8px"><a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/privacy.php">Privacy &amp; Data</a> · <a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/terms.php">Terms of Use</a></div>
  © <?= date('Y') ?> Argo Books · Built for small businesses
</footer>

<script src="<?= INVGEN_BASE ?>/profit-analyzer/assets/echarts.min.js"></script>
<script src="<?= INVGEN_BASE ?>/profit-analyzer/assets/owner-sample.js"></script>
</body>
</html>
