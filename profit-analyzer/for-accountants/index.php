<?php
// profit-analyzer/for-accountants/index.php
// Accountant-facing landing page: clean up a client's messy spreadsheet into a
// tidy, multi-sheet workbook. Same engine as the owner tool, different emphasis.
// CTA uses the referral-channel framing (recommend Argo to clients).

require_once __DIR__ . '/../../shared/_base.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../../statistics.php';
    track_page_view('profit_analyzer_accountants');
}

$canonical = 'https://argorobots.com/profit-analyzer/for-accountants/';
$title = "Clean up a client's messy spreadsheet — Free tool for accountants | Argo Books";
$description = "Drop in a client's disorganized spreadsheet and get back a tidy, categorized, multi-sheet workbook in about 60 seconds. Free tool for accountants and bookkeepers.";
$cta = INVGEN_BASE . '/downloads/?source=profit-analyzer-accountant&amp;utm_source=profit-analyzer&amp;utm_medium=tool&amp;utm_campaign=accountants';
$results = INVGEN_BASE . '/profit-analyzer/results/';

$schema = json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'SoftwareApplication',
  'name' => 'Free Spreadsheet Cleanup for Accountants',
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
<script type="application/ld+json"><?= $schema ?></script>
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/fonts.css">
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/accountant.css">
</head>
<body>

<nav>
  <div class="wrap">
    <a class="brand" href="<?= INVGEN_BASE ?>/profit-analyzer/for-accountants/"><img src="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-logo-white.png" alt="Argo Books" width="160" height="30"></a>
    <div class="links">
      <a href="#how">How it works</a>
      <a href="#trust">Privacy</a>
      <a class="btn btn-primary" href="<?= $cta ?>">Try Argo free</a>
    </div>
  </div>
</nav>

<header class="hero">
  <div class="wrap">
    <div class="eyebrow rise d1">Free tool for accountants &amp; bookkeepers</div>
    <h1 class="rise d2">Turn a client's<br><em style="white-space:nowrap">messy spreadsheet</em><br>into clean, structured books.</h1>
    <p class="sub rise d3">Drop in the disorganized file your client sent. Get back a tidy, categorized, ready-to-work spreadsheet in about 60 seconds. Free.</p>

    <a class="upload rise d4" href="<?= $results ?>?sample=1">
      <div class="ic"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 16V4m0 0L7 9m5-5 5 5"/><path d="M5 18v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1"/></svg></div>
      <div class="big">Drop your client's spreadsheet here</div>
      <div class="small">.xlsx or .csv · however messy it is</div>
      <span class="pick">Choose file</span>
    </a>
    <div class="or-sample rise d4">or <a href="#demo">try it with a sample messy file →</a></div>

    <div class="trust-line rise d5">
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg> Encrypted</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="m3 6 1 14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-14M8 6V4a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v2"/></svg> Deleted after analysis</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> Never used to train AI</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg> No account needed</span>
    </div>
  </div>
</header>

<!-- BEFORE / AFTER -->
<section class="block" id="demo">
  <div class="wrap">
    <div class="kicker">From chaos to clean</div>
    <h2 class="h2">What goes in, and what comes out</h2>
    <p class="sub2">Sample of a real kind of mess: mixed date formats, no categories, blank rows, amounts as text.</p>

    <div class="ba">
      <div class="sheet bad">
        <div class="head">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4m0 4h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/></svg>
          client-stuff-FINAL(2).xlsx
          <span class="tag">unusable</span>
        </div>
        <table class="xls">
          <tbody>
            <tr class="titlerow"><td colspan="4">Jan stuff!!</td></tr>
            <tr><td>date</td><td>what</td><td>amt</td><td>notes</td></tr>
            <tr><td class="muddle">1/5/24</td><td>home depot</td><td class="muddle">45.20</td><td>supplies?</td></tr>
            <tr><td class="muddle">jan 6</td><td>Client lunch</td><td>82</td><td class="blank"></td></tr>
            <tr><td class="blank"></td><td>TRANSFER</td><td class="muddle">1,200.00</td><td>?</td></tr>
            <tr><td class="muddle">01-07-2024</td><td>stripe payout</td><td>980.50</td><td>income</td></tr>
            <tr><td>1/9</td><td>gas</td><td class="muddle">$60</td><td class="blank"></td></tr>
          </tbody>
        </table>
      </div>

      <div class="arrow"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.4"><path d="M5 12h14m-6-6 6 6-6 6"/></svg></div>

      <div class="sheet good">
        <div class="head">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
          cleaned-january-2024.xlsx
          <span class="tag">ready to work</span>
        </div>
        <table class="xls">
          <thead><tr><th>Date</th><th>Description</th><th>Category</th><th>Amount</th></tr></thead>
          <tbody>
            <tr><td>2024-01-05</td><td>Home Depot</td><td><span class="pill cat">Supplies</span></td><td class="amt">45.20</td></tr>
            <tr><td>2024-01-06</td><td>Client lunch</td><td><span class="pill cat">Meals</span></td><td class="amt">82.00</td></tr>
            <tr><td>2024-01-06</td><td>Transfer</td><td><span class="pill cat">Transfer</span></td><td class="amt">1,200.00</td></tr>
            <tr><td>2024-01-07</td><td>Stripe payout</td><td><span class="pill inc">Sales income</span></td><td class="amt inc">980.50</td></tr>
            <tr><td>2024-01-09</td><td>Fuel</td><td><span class="pill cat">Vehicle</span></td><td class="amt">60.00</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<!-- FULL CLEAN SHEET -->
<section class="block" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line)">
  <div class="wrap">
    <div class="kicker">The output</div>
    <h2 class="h2">A spreadsheet you can actually work from</h2>
    <p class="sub2">One messy file becomes a clean, multi-tab workbook: sales, expenses, invoices, customers and products each get their own sheet, with consistent dates, proper amounts, and records linked by ID. Export to Excel and go.</p>

    <div class="cleanwrap">
      <div class="cleanbar">
        <div class="tabs">
          <span class="tab active" data-sheet="sales">Sales</span>
          <span class="tab" data-sheet="expenses">Expenses</span>
          <span class="tab" data-sheet="invoices">Invoices</span>
          <span class="tab" data-sheet="customers">Customers</span>
          <span class="tab" data-sheet="products">Products</span>
        </div>
        <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 4v12m0 0 4-4m-4 4-4-4"/><path d="M4 20h16"/></svg> Download .xlsx</span>
      </div>
      <div style="overflow-x:auto">
        <!-- SALES -->
        <table class="cleantable sheet-table" data-sheet="sales">
          <thead><tr><th>Date</th><th>Customer</th><th>Item</th><th style="text-align:right">Amount</th><th style="text-align:right">Tax</th><th style="text-align:right">Total</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td class="date">2024-01-07</td><td>Riverside Co.</td><td>Totes ×20</td><td class="amt">$400.00</td><td class="amt">$52.00</td><td class="amt">$452.00</td><td><span class="pill inc">Paid</span></td></tr>
            <tr><td class="date">2024-01-12</td><td>Maple Retail</td><td>Mugs ×30</td><td class="amt">$510.00</td><td class="amt">$66.30</td><td class="amt">$576.30</td><td><span class="pill inc">Paid</span></td></tr>
            <tr><td class="date">2024-01-15</td><td>A. Whitfield</td><td>Candles ×12</td><td class="amt">$192.00</td><td class="amt">$24.96</td><td class="amt">$216.96</td><td><span class="pill warn">Partial</span></td></tr>
            <tr><td class="date">2024-01-18</td><td>J. Okafor</td><td>Greeting cards ×40</td><td class="amt">$160.00</td><td class="amt">$20.80</td><td class="amt">$180.80</td><td><span class="pill exp">Unpaid</span></td></tr>
            <tr><td class="date">2024-01-21</td><td>Bianchi Ltd</td><td>Stickers ×60</td><td class="amt">$240.00</td><td class="amt">$31.20</td><td class="amt">$271.20</td><td><span class="pill inc">Paid</span></td></tr>
          </tbody>
        </table>
        <!-- EXPENSES -->
        <table class="cleantable sheet-table" data-sheet="expenses" style="display:none">
          <thead><tr><th>Date</th><th>Supplier</th><th>Description</th><th style="text-align:right">Amount</th><th style="text-align:right">Tax</th><th style="text-align:right">Total</th><th>Method</th></tr></thead>
          <tbody>
            <tr><td class="date">2024-01-05</td><td>Home Depot</td><td>Workshop supplies</td><td class="amt">$45.20</td><td class="amt">$5.88</td><td class="amt">$51.08</td><td>Card</td></tr>
            <tr><td class="date">2024-01-06</td><td>—</td><td>Client lunch</td><td class="amt">$82.00</td><td class="amt">$0.00</td><td class="amt">$82.00</td><td>Card</td></tr>
            <tr><td class="date">2024-01-09</td><td>Petro-Canada</td><td>Fuel</td><td class="amt">$60.00</td><td class="amt">$7.80</td><td class="amt">$67.80</td><td>Card</td></tr>
            <tr><td class="date">2024-01-14</td><td>Meta</td><td>Facebook Ads</td><td class="amt">$210.00</td><td class="amt">$0.00</td><td class="amt">$210.00</td><td>Card</td></tr>
            <tr><td class="date">2024-01-20</td><td>Pak Supplies</td><td>Raw cotton (totes)</td><td class="amt">$1,340.00</td><td class="amt">$174.20</td><td class="amt">$1,514.20</td><td>Transfer</td></tr>
          </tbody>
        </table>
        <!-- INVOICES -->
        <table class="cleantable sheet-table" data-sheet="invoices" style="display:none">
          <thead><tr><th>Invoice #</th><th>Customer</th><th>Issued</th><th>Due</th><th style="text-align:right">Total</th><th style="text-align:right">Balance</th><th>Status</th></tr></thead>
          <tbody>
            <tr><td>INV-2024-001</td><td>Riverside Co.</td><td class="date">2024-01-07</td><td class="date">2024-02-06</td><td class="amt">$452.00</td><td class="amt">$0.00</td><td><span class="pill inc">Paid</span></td></tr>
            <tr><td>INV-2024-002</td><td>Maple Retail</td><td class="date">2024-01-12</td><td class="date">2024-02-11</td><td class="amt">$576.30</td><td class="amt">$0.00</td><td><span class="pill inc">Paid</span></td></tr>
            <tr><td>INV-2024-003</td><td>A. Whitfield</td><td class="date">2024-01-15</td><td class="date">2024-02-14</td><td class="amt">$216.96</td><td class="amt">$108.48</td><td><span class="pill warn">Partial</span></td></tr>
            <tr><td>INV-2024-004</td><td>J. Okafor</td><td class="date">2024-01-18</td><td class="date">2024-02-17</td><td class="amt">$180.80</td><td class="amt">$180.80</td><td><span class="pill exp">Overdue</span></td></tr>
            <tr><td>INV-2024-005</td><td>Bianchi Ltd</td><td class="date">2024-01-21</td><td class="date">2024-02-20</td><td class="amt">$271.20</td><td class="amt">$0.00</td><td><span class="pill inc">Paid</span></td></tr>
          </tbody>
        </table>
        <!-- CUSTOMERS -->
        <table class="cleantable sheet-table" data-sheet="customers" style="display:none">
          <thead><tr><th>Name</th><th>Company</th><th>Email</th><th>City</th><th>Status</th><th style="text-align:right">Total purchases</th></tr></thead>
          <tbody>
            <tr><td>A. Whitfield</td><td>—</td><td>awhit@example.com</td><td>Toronto</td><td><span class="pill inc">Active</span></td><td class="amt">$1,180</td></tr>
            <tr><td>Riverside Co.</td><td>Riverside Co.</td><td>ar@riverside.co</td><td>Vancouver</td><td><span class="pill inc">Active</span></td><td class="amt">$2,100</td></tr>
            <tr><td>J. Okafor</td><td>—</td><td>jokafor@example.com</td><td>Calgary</td><td><span class="pill inc">Active</span></td><td class="amt">$920</td></tr>
            <tr><td>Maple Retail</td><td>Maple Retail Inc.</td><td>buy@mapleretail.ca</td><td>Ottawa</td><td><span class="pill inc">Active</span></td><td class="amt">$1,640</td></tr>
            <tr><td>M. Bianchi</td><td>Bianchi Ltd</td><td>mb@bianchi.it</td><td>Milan</td><td><span class="pill inc">Active</span></td><td class="amt">$760</td></tr>
          </tbody>
        </table>
        <!-- PRODUCTS -->
        <table class="cleantable sheet-table" data-sheet="products" style="display:none">
          <thead><tr><th>Name</th><th>SKU</th><th>Type</th><th>Category</th><th>Supplier</th><th style="text-align:right">Reorder pt</th></tr></thead>
          <tbody>
            <tr><td>Totes</td><td>TOT-01</td><td><span class="pill inc">Revenue</span></td><td><span class="pill cat">Bags</span></td><td>Pak Supplies</td><td class="amt">50</td></tr>
            <tr><td>Mugs</td><td>MUG-01</td><td><span class="pill inc">Revenue</span></td><td><span class="pill cat">Drinkware</span></td><td>Northwind</td><td class="amt">40</td></tr>
            <tr><td>Candles</td><td>CAN-01</td><td><span class="pill inc">Revenue</span></td><td><span class="pill cat">Home</span></td><td>Acme Co.</td><td class="amt">30</td></tr>
            <tr><td>Greeting cards</td><td>CRD-01</td><td><span class="pill inc">Revenue</span></td><td><span class="pill cat">Stationery</span></td><td>Northwind</td><td class="amt">100</td></tr>
            <tr><td>Stickers</td><td>STK-01</td><td><span class="pill inc">Revenue</span></td><td><span class="pill cat">Stationery</span></td><td>Acme Co.</td><td class="amt">80</td></tr>
          </tbody>
        </table>
      </div>
      <div class="cleanfoot"><span>5 sheets · 248 records organized</span><span><b>2</b> rows flagged for your review</span></div>
    </div>
  </div>
</section>

<!-- WHAT IT DOES -->
<section class="block" id="how">
  <div class="wrap">
    <div class="kicker">The cleanup</div>
    <h2 class="h2">What it sorts out for you</h2>
    <p class="sub2"></p>
    <div class="finds">
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Splits one file into proper books</b><p>Separates customers, suppliers, products, invoices, expenses, payments, inventory and more out of a single jumbled sheet into their own clean tabs.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Reads any layout</b><p>Handles pivot tables, cross-tabs and line-item rows, and groups scattered lines back into the right invoice or record.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Matches up the columns</b><p>Works out what each column means even when it's renamed, reordered, or labelled oddly ("Sales", "Purchases", and the like).</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Standardizes dates &amp; amounts</b><p>Every date to one format; every amount, currency symbols, commas, stored-as-text and all, to a real number.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Categorizes and fills gaps</b><p>Assigns sensible categories, marks income vs expense, and generates IDs for records that are missing them.</p></div></div>
      <div class="find"><div class="ck"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg></div><div><b>Flags what to double-check</b><p>Marks ambiguous rows for your review instead of quietly guessing, then hands it all back as a formatted, multi-tab Excel workbook.</p></div></div>
    </div>
  </div>
</section>

<!-- TRUST -->
<section class="block" id="trust" style="background:#fff;border-top:1px solid var(--line)">
  <div class="wrap">
    <div class="trust-card">
      <div class="hd">
        <div class="lock"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg></div>
        <h3>Your clients' data is safe, and stays yours</h3>
      </div>
      <ul>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Encrypted in transit and at rest</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Automatically deleted after analysis</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> Processed by paid AI that never trains on your data</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2"><path d="M20 6 9 17l-5-5"/></svg> No account or email required to use it</li>
      </ul>
    </div>
  </div>
</section>

<!-- BRIDGE (referral framing) -->
<section class="block">
  <div class="wrap">
    <div class="bridge">
      <h2>Keep your clients' books this clean<br>all year, not just at cleanup time.</h2>
      <p>Argo Books is simple, affordable bookkeeping your small-business clients can run themselves, so their records arrive tidy. Recommend it, or set them up yourself.</p>
      <a class="btn btn-primary btn-lg" href="<?= $cta ?>">See Argo for your clients →</a>
    </div>
  </div>
</section>

<footer>
  <div style="margin-bottom:8px"><a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/privacy.php">Privacy &amp; Data</a> · <a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/terms.php">Terms of Use</a></div>
  © <?= date('Y') ?> Argo Books · Free tools for accountants
</footer>

<script>
(function(){
  var tabs = document.querySelectorAll('.cleanbar .tab');
  var tables = document.querySelectorAll('.sheet-table');
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      tabs.forEach(function(t){ t.classList.remove('active'); });
      tab.classList.add('active');
      var s = tab.getAttribute('data-sheet');
      tables.forEach(function(tbl){ tbl.style.display = (tbl.getAttribute('data-sheet') === s) ? '' : 'none'; });
    });
  });
})();
</script>
</body>
</html>
