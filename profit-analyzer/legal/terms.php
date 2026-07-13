<?php
// profit-analyzer/legal/terms.php — Profit Analyzer terms of use.
require_once __DIR__ . '/../../shared/_base.php';
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../../statistics.php';
    track_page_view('profit_analyzer_terms');
}
$canonical = 'https://argorobots.com/profit-analyzer/legal/terms.php';
$title = 'Profit Analyzer Terms of Use | Argo Books';
$description = 'Terms of use for the free Argo Books Profit Analyzer. The tool is provided as-is; its output is informational, not financial, accounting, or tax advice.';
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title) ?></title>
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<link rel="canonical" href="<?= $canonical ?>">
<link rel="icon" href="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-icon.ico" sizes="any">
<link rel="stylesheet" href="<?= INVGEN_BASE ?>/profit-analyzer/assets/legal.css">
</head>
<body>
<div class="topbar"><div class="inner">
  <a class="brand" href="<?= INVGEN_BASE ?>/profit-analyzer/"><img src="<?= INVGEN_BASE ?>/resources/images/argo-logo/argo-logo-white.png" alt="Argo Books" width="150" height="28"></a>
  <a class="back" href="<?= INVGEN_BASE ?>/profit-analyzer/">← Back to the tool</a>
</div></div>

<div class="legal-container">
  <div class="legal-content">
    <h1>Profit Analyzer Terms of Use</h1>

    <h2>Acceptance</h2>
    <p>These terms apply to the Argo Books Profit Analyzer (the "Tool"), a free spreadsheet analysis tool. By using the Tool, you agree to these terms. If you do not agree, please do not use the Tool.</p>

    <h2>A free tool, provided "as is"</h2>
    <p>The Tool is offered free of charge and on an "as is" and "as available" basis, without warranties of any kind, whether express or implied. We do not guarantee that it will be uninterrupted, error-free, or available at any particular time.</p>

    <h2>Not professional advice</h2>
    <p>The figures, categories, charts, and cleaned spreadsheet the Tool produces are generated automatically and are provided for general information only. <strong>They are not financial, accounting, tax, or legal advice.</strong> You should always have a qualified professional review your records before relying on them for business decisions, filings, or reporting.</p>

    <h2>Accuracy and your responsibility</h2>
    <p>The Tool uses AI to interpret messy spreadsheets. It can make mistakes, miscategorize items, or misread values. Rows the Tool is unsure about are flagged for your review, but you remain responsible for checking the output before you rely on it.</p>
    <p>You are responsible for the file you upload. Only upload data that you have the right to share, and do not upload anything unlawful or that infringes someone else's rights.</p>

    <h2>Acceptable use</h2>
    <ul>
      <li>Do not attempt to overload, scrape, reverse-engineer, or otherwise abuse the Tool.</li>
      <li>We apply rate limits and may restrict or block access in cases of misuse.</li>
    </ul>

    <h2>Limitation of liability</h2>
    <p>To the fullest extent permitted by law, Argo Books will not be liable for any loss or damage, including lost profits or data, arising from your use of the Tool or your reliance on any output it produces.</p>

    <h2>Privacy</h2>
    <p>How the Tool handles the data you upload is described in our <a class="link" href="<?= INVGEN_BASE ?>/profit-analyzer/legal/privacy.php">Profit Analyzer Privacy &amp; Data Policy</a>.</p>

    <h2>Changes</h2>
    <p>We may update these terms from time to time. Changes are effective when posted on this page, and we will update the "Last updated" date below.</p>

    <h2>Contact us</h2>
    <p>Questions about these terms? Contact us by email: <a class="link" href="mailto:contact@argorobots.com">contact@argorobots.com</a></p>

    <p class="last-updated">Last updated: <?= date('F j, Y') ?></p>
  </div>
</div>
</body>
</html>
