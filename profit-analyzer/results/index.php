<?php
// profit-analyzer/results/index.php
// Post-upload result page: the full analytics view (sample data for now;
// the real build feeds NormalizedData into the same markup + charts).

require_once __DIR__ . '/../../invoice-generator/_base.php';

if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../../statistics.php';
    track_page_view('profit_analyzer_results');
}

$canonical = 'https://argorobots.com/profit-analyzer/results/';
$title = 'Your results — Free Profit Analyzer | Argo Books';
$description = 'See your full profit breakdown: money-flow, products, customers, taxes, returns and more, plus a cleaned, organized spreadsheet you can download.';
$cta = INVGEN_BASE . '/downloads/?source=profit-analyzer-result&amp;utm_source=profit-analyzer&amp;utm_medium=tool&amp;utm_campaign=launch';
$home = INVGEN_BASE . '/profit-analyzer/';
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
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/fonts.css">
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/result.css">
</head>
<body>

<div class="topbar"><div class="wrap">
  <a class="brand" href="<?= $home ?>"><img class="logo-light" src="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-logo-black.png" alt="Argo Books" width="150" height="28"><img class="logo-dark" src="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-logo-white.png" alt="Argo Books" width="150" height="28"></a>
  <div style="display:flex;align-items:center;gap:16px">
    <button class="themetoggle" id="themeToggle" aria-label="Toggle dark mode">
      <svg class="ic-moon" viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></svg>
      <svg class="ic-sun" viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4 12H2m20 0h-2m-2.8-7.2-1.4 1.4M6.2 17.8l-1.4 1.4m0-14.4 1.4 1.4m11.6 11.6 1.4 1.4"/></svg>
    </button>
    <a class="start" href="<?= $home ?>"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9 9 0 0 0-6.5 2.8M3 4v4h4"/></svg> Analyze another file</a>
  </div>
</div></div>

<div class="wrap">
  <div class="rhead">
    <div>
      <div class="file">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M4 9h16M4 15h16M10 3v18"/></svg>
        maple-goods-sales-2024.xlsx <span class="badge">✓ 248 rows analyzed</span>
      </div>
      <h1>Your numbers, <em>made clear</em>.</h1>
    </div>
    <button type="button" class="btn-download"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 4v12m0 0 4-4m-4 4-4-4"/><path d="M4 20h16"/></svg> Download organized spreadsheet</button>
  </div>
</div>

<!-- TAB BAR -->
<div class="tabbar"><div class="inner" id="tabbar">
  <div class="tabbtn active" data-tab="dashboard">Dashboard</div>
  <div class="tabbtn" data-tab="products">Products</div>
  <div class="tabbtn" data-tab="geographic">Geographic</div>
  <div class="tabbtn" data-tab="performance">Performance</div>
  <div class="tabbtn" data-tab="customers">Customers</div>
  <div class="tabbtn" data-tab="taxes">Taxes</div>
  <div class="tabbtn" data-tab="returns">Returns</div>
  <div class="tabbtn" data-tab="losses">Losses</div>
  <div class="tabbtn" data-tab="refunds">Refunds</div>
</div></div>

<div class="wrap">

  <!-- ============ DASHBOARD ============ -->
  <div class="panel active" data-panel="dashboard">
    <div class="kpis">
      <div class="kpi bad"><div class="lbl">Total Expenses</div><div class="val">$20,090</div><div class="sub down">81% of revenue</div></div>
      <div class="kpi good"><div class="lbl">Total Revenue</div><div class="val">$24,800</div><div class="sub up">▲ 12% vs last period</div></div>
      <div class="kpi"><div class="lbl">Net Profit</div><div class="val">$4,710</div><div class="sub up">▲ 8%</div></div>
      <div class="kpi"><div class="lbl">Profit Margin</div><div class="val">19%</div><div class="sub">healthy</div></div>
    </div>

    <div class="flowcard">
      <div class="flowhead">
        <div><h3 class="flowttl">Follow your money</h3><div class="meta">Where every dollar of revenue goes before it reaches you</div></div>
        <div class="keptstat"><b>19%</b><span>kept as profit</span></div>
      </div>
      <div id="sankeyChart" style="width:100%;height:330px"></div>
    </div>

    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Profit Trends Over Time</div><div class="cmeta">Net profit by month</div><div class="ec" id="c_profitTrend"></div></div>
      <div class="chartcard"><div class="ttl">Sales vs Expenses</div><div class="cmeta">Monthly comparison</div><div class="ec" id="c_salesVsExp"></div></div>
      <div class="chartcard"><div class="ttl">Sales Trends</div><div class="cmeta">Revenue by month</div><div class="ec" id="c_salesTrend"></div></div>
      <div class="chartcard"><div class="ttl">Revenue Distribution</div><div class="cmeta">By category</div><div class="ec" id="c_revDist"></div></div>
      <div class="chartcard"><div class="ttl">Purchase Trends</div><div class="cmeta">Expenses by month</div><div class="ec" id="c_purchTrend"></div></div>
      <div class="chartcard"><div class="ttl">Expense Distribution</div><div class="cmeta">By category</div><div class="ec" id="c_expDist"></div></div>
    </div>
  </div>

  <!-- ============ PRODUCTS ============ -->
  <div class="panel" data-panel="products">
    <div class="kpis">
      <div class="kpi good"><div class="lbl">Product Revenue</div><div class="val">$23,700</div></div>
      <div class="kpi"><div class="lbl">Units Sold</div><div class="val">1,997</div></div>
      <div class="kpi"><div class="lbl">Avg Sale Price</div><div class="val">$11.87</div></div>
      <div class="kpi"><div class="lbl">Products Sold</div><div class="val">6</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard span2">
        <div class="ttl">Sales by Product</div><div class="cmeta">Sortable in-app · top rows shown</div>
        <table class="dtable">
          <thead><tr><th>Product</th><th style="text-align:right">Units</th><th style="text-align:right">Revenue</th><th style="text-align:right">Avg price</th></tr></thead>
          <tbody>
            <tr><td>Totes</td><td class="num">412</td><td class="num">$8,240</td><td class="num">$20.00</td></tr>
            <tr><td>Mugs</td><td class="num">380</td><td class="num">$6,460</td><td class="num">$17.00</td></tr>
            <tr><td>Candles</td><td class="num">295</td><td class="num">$4,720</td><td class="num">$16.00</td></tr>
            <tr><td>Greeting cards</td><td class="num">510</td><td class="num">$2,040</td><td class="num">$4.00</td></tr>
            <tr><td>Enamel pins</td><td class="num">160</td><td class="num">$1,280</td><td class="num">$8.00</td></tr>
            <tr><td>Stickers</td><td class="num">240</td><td class="num">$960</td><td class="num">$4.00</td></tr>
          </tbody>
        </table>
      </div>
      <div class="chartcard span2"><div class="ttl">Product Revenue Trend — Totes</div><div class="cmeta">Selected product, revenue by month</div><div class="ec" id="c_prodTrend"></div></div>
    </div>
  </div>

  <!-- ============ GEOGRAPHIC ============ -->
  <div class="panel" data-panel="geographic">
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Countries of Origin</div><div class="cmeta">Where your supply comes from</div><div class="ec" id="c_cOrigin"></div></div>
      <div class="chartcard"><div class="ttl">Companies of Origin</div><div class="cmeta">Top suppliers</div><div class="ec" id="c_compOrigin"></div></div>
      <div class="chartcard"><div class="ttl">Countries of Destination</div><div class="cmeta">Where your sales go</div><div class="ec" id="c_cDest"></div></div>
      <div class="chartcard"><div class="ttl">Companies of Destination</div><div class="cmeta">Top customers by company</div><div class="ec" id="c_compDest"></div></div>
      <div class="chartcard span2"><div class="ttl">World Map Overview</div><div class="cmeta">Revenue by destination country</div><div class="ec" id="c_geoMap" style="height:380px"></div></div>
    </div>
  </div>

  <!-- ============ PERFORMANCE ============ -->
  <div class="panel" data-panel="performance">
    <div class="kpis">
      <div class="kpi good"><div class="lbl">Revenue Growth</div><div class="val">+12%</div></div>
      <div class="kpi"><div class="lbl">Total Transactions</div><div class="val">1,284</div></div>
      <div class="kpi"><div class="lbl">Avg Transaction Value</div><div class="val">$19.30</div></div>
      <div class="kpi"><div class="lbl">Avg Shipping Cost</div><div class="val">$4.85</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Average Transaction Value</div><div class="cmeta">By month</div><div class="ec" id="c_avgTxn"></div></div>
      <div class="chartcard"><div class="ttl">Total Transactions Over Time</div><div class="cmeta">Count by month</div><div class="ec" id="c_totalTxn"></div></div>
      <div class="chartcard span2"><div class="ttl">Average Shipping Costs</div><div class="cmeta">By month</div><div class="ec" id="c_shipping"></div></div>
    </div>
  </div>

  <!-- ============ CUSTOMERS ============ -->
  <div class="panel" data-panel="customers">
    <div class="kpis">
      <div class="kpi"><div class="lbl">Total Customers</div><div class="val">486</div></div>
      <div class="kpi good"><div class="lbl">New Customers</div><div class="val">63</div></div>
      <div class="kpi"><div class="lbl">Retention Rate</div><div class="val">71%</div></div>
      <div class="kpi"><div class="lbl">Avg Customer Value</div><div class="val">$51</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Top Customers by Revenue</div><div class="cmeta">Highest spenders</div><div class="ec" id="c_topCust"></div></div>
      <div class="chartcard"><div class="ttl">Customer Payment Status</div><div class="cmeta">Paid / partial / unpaid</div><div class="ec" id="c_payStatus"></div></div>
      <div class="chartcard"><div class="ttl">Customer Growth</div><div class="cmeta">New customers by month</div><div class="ec" id="c_custGrowth"></div></div>
      <div class="chartcard"><div class="ttl">Customer Lifetime Value</div><div class="cmeta">By month</div><div class="ec" id="c_clv"></div></div>
      <div class="chartcard"><div class="ttl">Active vs Inactive Customers</div><div class="cmeta">Last 90 days</div><div class="ec" id="c_activeInactive"></div></div>
      <div class="chartcard"><div class="ttl">Rentals per Customer</div><div class="cmeta">Rental count distribution</div><div class="ec" id="c_rentalsPer"></div></div>
    </div>
  </div>

  <!-- ============ TAXES ============ -->
  <div class="panel" data-panel="taxes">
    <div class="kpis">
      <div class="kpi good"><div class="lbl">Tax Collected</div><div class="val">$3,224</div></div>
      <div class="kpi bad"><div class="lbl">Tax Paid</div><div class="val">$1,742</div></div>
      <div class="kpi"><div class="lbl">Net Tax Liability</div><div class="val">$1,482</div></div>
      <div class="kpi"><div class="lbl">Effective Tax Rate</div><div class="val">13%</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Tax Collected vs Paid</div><div class="cmeta">By month</div><div class="ec" id="c_taxVsPaid"></div></div>
      <div class="chartcard"><div class="ttl">Tax Rate Distribution</div><div class="cmeta">Transactions by rate</div><div class="ec" id="c_taxRate"></div></div>
      <div class="chartcard"><div class="ttl">Tax Liability Trend</div><div class="cmeta">Net liability by month</div><div class="ec" id="c_taxLiab"></div></div>
      <div class="chartcard"><div class="ttl">Tax by Category</div><div class="cmeta">Share by category</div><div class="ec" id="c_taxCat"></div></div>
      <div class="chartcard"><div class="ttl">Tax by Product</div><div class="cmeta">Top taxed products</div><div class="ec" id="c_taxProd"></div></div>
      <div class="chartcard"><div class="ttl">Expense vs Revenue Tax</div><div class="cmeta">By month</div><div class="ec" id="c_expRevTax"></div></div>
    </div>
  </div>

  <!-- ============ RETURNS ============ -->
  <div class="panel" data-panel="returns">
    <div class="kpis">
      <div class="kpi"><div class="lbl">Total Returns</div><div class="val">38</div></div>
      <div class="kpi"><div class="lbl">Return Rate</div><div class="val">3.0%</div></div>
      <div class="kpi bad"><div class="lbl">Financial Impact</div><div class="val">$1,120</div></div>
      <div class="kpi good"><div class="lbl">Avg Resolution Time</div><div class="val">2.4 days</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Returns Over Time</div><div class="cmeta">Count by month</div><div class="ec" id="c_retTime"></div></div>
      <div class="chartcard"><div class="ttl">Return Reasons</div><div class="cmeta">Why items came back</div><div class="ec" id="c_retReasons"></div></div>
      <div class="chartcard"><div class="ttl">Financial Impact of Returns</div><div class="cmeta">$ by month</div><div class="ec" id="c_retImpact"></div></div>
      <div class="chartcard"><div class="ttl">Returns by Category</div><div class="cmeta">Share by category</div><div class="ec" id="c_retCat"></div></div>
      <div class="chartcard"><div class="ttl">Returns by Product</div><div class="cmeta">Most returned</div><div class="ec" id="c_retProd"></div></div>
      <div class="chartcard"><div class="ttl">Purchase vs Sale Returns</div><div class="cmeta">By month</div><div class="ec" id="c_retPurchSale"></div></div>
    </div>
  </div>

  <!-- ============ LOSSES ============ -->
  <div class="panel" data-panel="losses">
    <div class="kpis">
      <div class="kpi bad"><div class="lbl">Total Losses</div><div class="val">$1,640</div></div>
      <div class="kpi"><div class="lbl">Loss Rate</div><div class="val">1.8%</div></div>
      <div class="kpi"><div class="lbl">Financial Impact</div><div class="val">$1,640</div></div>
      <div class="kpi good"><div class="lbl">Insurance Claims</div><div class="val">$420</div></div>
    </div>
    <div class="cgrid">
      <div class="chartcard"><div class="ttl">Losses Over Time</div><div class="cmeta">$ by month</div><div class="ec" id="c_lossTime"></div></div>
      <div class="chartcard"><div class="ttl">Loss Reasons</div><div class="cmeta">Cause breakdown</div><div class="ec" id="c_lossReasons"></div></div>
      <div class="chartcard"><div class="ttl">Financial Impact of Losses</div><div class="cmeta">$ by month</div><div class="ec" id="c_lossImpact"></div></div>
      <div class="chartcard"><div class="ttl">Losses by Category</div><div class="cmeta">Share by category</div><div class="ec" id="c_lossCat"></div></div>
      <div class="chartcard"><div class="ttl">Losses by Product</div><div class="cmeta">Most affected</div><div class="ec" id="c_lossProd"></div></div>
      <div class="chartcard"><div class="ttl">Purchase vs Sale Losses</div><div class="cmeta">By month</div><div class="ec" id="c_lossPurchSale"></div></div>
    </div>
  </div>

  <!-- ============ REFUNDS ============ -->
  <div class="panel" data-panel="refunds">
    <div class="kpis" style="grid-template-columns:repeat(3,1fr)">
      <div class="kpi bad"><div class="lbl">Total Refunded</div><div class="val">$1,310</div><div class="sub">last 90 days</div></div>
      <div class="kpi"><div class="lbl">Refund Rate</div><div class="val">5.3%</div><div class="sub">of revenue</div></div>
      <div class="kpi good"><div class="lbl">Avg Time to Refund</div><div class="val">1.8 days</div></div>
    </div>
    <div class="cgrid">
      <div class="listcard"><h4>Top Refunded Customers</h4>
        <div class="lrow"><span class="n">A. Whitfield</span><span class="v">$280</span></div>
        <div class="lrow"><span class="n">Riverside Co.</span><span class="v">$210</span></div>
        <div class="lrow"><span class="n">J. Okafor</span><span class="v">$145</span></div>
        <div class="lrow"><span class="n">M. Bianchi</span><span class="v">$120</span></div>
      </div>
      <div class="listcard"><h4>Top Refunded Items</h4>
        <div class="lrow"><span class="n">Stickers</span><span class="v">$340</span></div>
        <div class="lrow"><span class="n">Enamel pins</span><span class="v">$300</span></div>
        <div class="lrow"><span class="n">Mugs</span><span class="v">$190</span></div>
        <div class="lrow"><span class="n">Candles</span><span class="v">$150</span></div>
      </div>
      <div class="listcard"><h4>Top Reasons</h4>
        <div class="lrow"><span class="n">Damaged in transit <span class="meta">· 14</span></span><span class="v">$520</span></div>
        <div class="lrow"><span class="n">Wrong item <span class="meta">· 9</span></span><span class="v">$310</span></div>
        <div class="lrow"><span class="n">Changed mind <span class="meta">· 7</span></span><span class="v">$280</span></div>
        <div class="lrow"><span class="n">Late delivery <span class="meta">· 4</span></span><span class="v">$200</span></div>
      </div>
      <div class="listcard"><h4>By Channel</h4>
        <div class="lrow"><span class="n">Etsy</span><span class="v">$610</span></div>
        <div class="lrow"><span class="n">Shopify</span><span class="v">$430</span></div>
        <div class="lrow"><span class="n">In person</span><span class="v">$180</span></div>
        <div class="lrow"><span class="n">Wholesale</span><span class="v">$90</span></div>
      </div>
      <div class="chartcard span2"><div class="ttl">Refunds by Month</div><div class="cmeta">Last 12 months</div><div class="ec" id="c_refundMonth"></div></div>
    </div>
  </div>

  <!-- CLEANED DATA (shared, below tabs) -->
  <div class="sectitle">Your data, cleaned</div>
  <div class="cleanwrap">
    <div class="cleanbar">
      <div class="tabs">
        <span class="ctab active" data-filter="all">Transactions</span>
        <span class="ctab" data-filter="income">Income</span>
        <span class="ctab" data-filter="expense">Expenses</span>
      </div>
      <span class="dl"><svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M12 4v12m0 0 4-4m-4 4-4-4"/><path d="M4 20h16"/></svg> Download .xlsx</span>
    </div>
    <table class="cleantable">
      <thead><tr><th>Date</th><th>Description</th><th>Category</th><th>Type</th><th style="text-align:right">Amount</th></tr></thead>
      <tbody>
        <tr data-type="expense"><td class="date">2024-01-05</td><td>Home Depot</td><td><span class="pill cat">Supplies</span></td><td><span class="pill exp">Expense</span></td><td class="amt">$45.20</td></tr>
        <tr data-type="expense"><td class="date">2024-01-06</td><td>Client lunch</td><td><span class="pill cat">Meals &amp; entertainment</span></td><td><span class="pill exp">Expense</span></td><td class="amt">$82.00</td></tr>
        <tr data-type="income"><td class="date">2024-01-07</td><td>Stripe payout</td><td><span class="pill cat">Sales income</span></td><td><span class="pill inc">Income</span></td><td class="amt inc">$980.50</td></tr>
        <tr data-type="expense"><td class="date">2024-01-09</td><td>Fuel</td><td><span class="pill cat">Vehicle &amp; fuel</span></td><td><span class="pill exp">Expense</span></td><td class="amt">$60.00</td></tr>
        <tr data-type="income"><td class="date">2024-01-12</td><td>Etsy deposit</td><td><span class="pill cat">Sales income</span></td><td><span class="pill inc">Income</span></td><td class="amt inc">$430.00</td></tr>
        <tr data-type="expense"><td class="date">2024-01-14</td><td>Facebook Ads</td><td><span class="pill cat">Advertising</span></td><td><span class="pill exp">Expense</span></td><td class="amt">$210.00</td></tr>
      </tbody>
    </table>
    <div class="cleanfoot"><span id="rowcount">6 of 248 rows shown</span><span><b>Download</b> the full cleaned spreadsheet above</span></div>
  </div>

  <div class="email">
    <div class="t"><b>Want your results emailed to you?</b><span>We'll send the summary plus your cleaned spreadsheet. Optional.</span></div>
    <form onsubmit="return false"><input type="email" placeholder="you@business.com"><button class="send">Email it</button></form>
  </div>

  <div class="bridge">
    <h2>This is a snapshot. Argo keeps it true every day.</h2>
    <p>Every chart on this page updates automatically inside Argo Books, all year, plus invoices, expenses, and tax-ready reports.</p>
    <a class="btn btn-primary btn-lg" href="<?= $cta ?>">Try Argo Books free →</a>
  </div>
</div>

<footer>
  <div style="margin-bottom:8px"><a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/privacy.php">Privacy &amp; Data</a> · <a href="<?= INVGEN_BASE ?>/profit-analyzer/legal/terms.php">Terms of Use</a></div>
  © <?= date('Y') ?> Argo Books
</footer>

<script>window.PA_ASSETS = <?= json_encode(INVGEN_BASE . '/profit-analyzer/assets/', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<script src="<?= INVGEN_BASE ?>/profit-analyzer/assets/echarts.min.js"></script>
<script src="<?= INVGEN_BASE ?>/profit-analyzer/assets/result.js"></script>
</body>
</html>
