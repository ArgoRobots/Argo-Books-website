<?php
// invoice-template/data/minimal-google-sheets.php
// /invoice-template/minimal-google-sheets/

return [
  'slug' => 'minimal-google-sheets',
  'kind' => 'style-format',
  'style' => 'minimal',
  'format' => 'google-sheets',

  'h1' => 'Minimal Google Sheets invoice template',
  'meta_title' => 'Minimal Google Sheets Invoice Template | Argo Books',
  'meta_description' => 'Free Minimal-style Google Sheets invoice template with auto-calculating totals. Stripped-down typography, clone into your Drive in one click.',

  'intro_html' => '<p>The Minimal Google Sheets template strips the visual chrome back while keeping the formulas that make spreadsheet invoices fast to fill in. Click "Make a copy in Google Sheets" to clone the template into your own Drive, then edit line items and watch totals update.</p>',

  'body_html' =>
    '<p>The Minimal Google Sheets template suits businesses that already track invoices in spreadsheets. Open the copy in your Drive, fill in the From and Bill To blocks, add line items in the table, watch the totals update, and download as PDF when ready to send.</p>'
    . '<p>The stripped-down look reads as restrained across industries. Formulas live in unprotected cells, so you can edit them freely. Adding more line items is a matter of inserting a row inside the table range.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Minimal Google Sheets template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Do the formulas in the Minimal Google Sheets template auto-recalculate?', 'a' => 'Yes. Change a quantity or rate and the line amount, subtotal, tax, total, and balance due all update.'],
    ['q' => 'How do I add more line items to the Minimal Google Sheets template?', 'a' => 'Right click a row inside the line items range, choose Insert row, then drag the formulas down. The subtotal includes the inserted rows automatically.'],
  ],

  'related_slugs' => ['google-sheets', 'minimal-excel', 'minimal-google-docs', 'modern-google-sheets', 'bold-google-sheets'],
];
