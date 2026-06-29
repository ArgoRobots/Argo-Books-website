<?php
// profit-analyzer/legal/privacy.php — Profit Analyzer data & privacy policy.
require_once __DIR__ . '/../../shared/_base.php';
if (PHP_SAPI !== 'cli') {
    require_once __DIR__ . '/../../statistics.php';
    track_page_view('profit_analyzer_privacy');
}
$canonical = 'https://argorobots.com/profit-analyzer/legal/privacy.php';
$title = 'Profit Analyzer Privacy & Data Policy | Argo Books';
$description = 'How the free Argo Books Profit Analyzer handles the spreadsheet you upload: deleted immediately after analysis, never used to train AI, nothing stored.';
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
    <h1>Profit Analyzer Privacy &amp; Data Policy</h1>

    <h2>About this policy</h2>
    <p>This policy explains how the Argo Books Profit Analyzer (the "Tool"), our free spreadsheet analysis tool, handles the file and the data you upload to it. It is specific to the Tool. Our general <a class="link" href="<?= INVGEN_BASE ?>/legal/privacy.php">Website Privacy Policy</a> covers everything else.</p>

    <h2>What you upload</h2>
    <p>When you use the Tool, you upload a spreadsheet (.xlsx or .csv). Its contents may include business and financial information such as sales, expenses, invoices, customers, and products. You are never asked to create an account or provide your name to use the Tool.</p>

    <h2>How we handle your file</h2>
    <ul>
      <li><strong>One purpose only</strong>: We process your file solely to produce the analysis, charts, and cleaned spreadsheet you requested.</li>
      <li><strong>AI processing</strong>: To read and organize messy spreadsheets, the contents of your file are sent to a third-party AI service, Google Gemini, operating under a paid plan. Under that plan, your data is <strong>not used to train Google's models</strong>.</li>
      <li><strong>Immediate deletion</strong>: Your original uploaded file is deleted from our servers immediately after the analysis completes.</li>
      <li><strong>Nothing is stored</strong>: We do not save your file, your results, your charts, or your cleaned spreadsheet on our servers. They exist only during your session, in your browser. When you close the page, they are gone.</li>
      <li><strong>No account, no profile</strong>: We do not link your upload to any account, name, or stored identity.</li>
    </ul>

    <h2>If you ask us to email your results</h2>
    <p>Emailing your results is optional. If you choose it, we use the email address you provide only to send that one message, together with a copy of your cleaned spreadsheet generated at the time of sending. We do not store your spreadsheet or results to support this, and we do not add you to a mailing list without your consent.</p>

    <h2>Technical information</h2>
    <p>As with any website, our server temporarily sees your IP address and basic browser information when you make a request. We use your IP address only to prevent abuse and apply rate limits. It is not stored alongside your spreadsheet data.</p>

    <h2>What we never do</h2>
    <ul>
      <li>We never sell or share the data you upload.</li>
      <li>We never use your uploaded data to train AI models.</li>
      <li>We never keep your file or results after your session ends.</li>
    </ul>

    <h2>Third-party processing</h2>
    <p>Your file contents are processed by Google Gemini as described above. Google's handling of data sent to its paid API is governed by its own terms. Our use adheres to the applicable <a class="link" href="https://ai.google.dev/gemini-api/terms" target="_blank" rel="noopener">Google Gemini API terms</a>.</p>

    <h2>Children's privacy</h2>
    <p>The Tool is not directed to anyone under the age of 18, and we do not knowingly collect personal data from children.</p>

    <h2>Changes to this policy</h2>
    <p>We may update this policy from time to time. Changes are effective when posted on this page, and we will update the "Last updated" date below.</p>

    <h2>Contact us</h2>
    <p>If you have any questions about how the Tool handles your data, contact us:</p>
    <ul>
      <li>By email: <a class="link" href="mailto:contact@argorobots.com">contact@argorobots.com</a></li>
    </ul>

    <p class="last-updated">Last updated: <?= date('F j, Y') ?></p>
  </div>
</div>
</body>
</html>
