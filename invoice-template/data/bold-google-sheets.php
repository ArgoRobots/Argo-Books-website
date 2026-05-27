<?php
// invoice-template/data/bold-google-sheets.php
// /invoice-template/bold-google-sheets/

return [
  'slug' => 'bold-google-sheets',
  'kind' => 'style-format',
  'style' => 'bold',
  'format' => 'google-sheets',

  'h1' => 'Bold Google Sheets invoice template',
  'meta_title' => 'Bold Google Sheets Invoice Template | Argo Books',
  'meta_description' => 'Free Bold-style Google Sheets invoice template with auto-calculating totals. Yellow accent header, clone into your Drive in one click.',

  'intro_html' => '<p>The Bold Google Sheets template pairs a yellow accent header with the formulas that make spreadsheet invoices fast to fill in. Click "Make a copy in Google Sheets" to clone the template into your own Drive, then edit line items and watch totals update.</p>',

  'body_html' =>
    '<p>The Bold Google Sheets template suits businesses that already track invoices in spreadsheets. Open the copy in your Drive, fill in the From and Bill To blocks, add line items in the table, watch the totals update, and download as PDF when ready to send.</p>'
    . '<p>The yellow header reads as confident across industries. Formulas live in unprotected cells, so you can edit them freely. Adding more line items is a matter of inserting a row inside the table range.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Bold Google Sheets template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Do the formulas in the Bold Google Sheets template auto-recalculate?', 'a' => 'Yes. Change a quantity or rate and the line amount, subtotal, tax, total, and balance due all update.'],
    ['q' => 'How do I add more line items to the Bold Google Sheets template?', 'a' => 'Right click a row inside the line items range, choose Insert row, then drag the formulas down. The subtotal includes the inserted rows automatically.'],
  ],

  'related_slugs' => ['google-sheets', 'bold-excel', 'bold-google-docs', 'minimal-google-sheets', 'professional-google-sheets'],
];
