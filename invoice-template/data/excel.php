<?php
// invoice-template/data/excel.php
// /invoice-template/excel/

return [
  'slug' => 'excel',
  'kind' => 'format-generic',
  'style' => null,
  'format' => 'excel',

  'h1' => 'Free Excel invoice templates',
  'meta_title' => 'Free Excel Invoice Templates (.xlsx) | Argo Books',
  'meta_description' => 'Free Microsoft Excel invoice templates with auto-calculating totals. Five styles, downloadable .xlsx files, no signup.',

  'intro_html' => '<p>Excel templates are the right choice when you want the totals to recalculate as you type. Change a quantity or rate and the line amount, subtotal, tax, and balance due all update. Pick a style below to download the .xlsx file.</p>',

  'body_html' =>
    '<p>Every Excel template here includes formulas for line-item amounts, subtotal, tax, total, and balance due. The tax rate is a single percentage cell you edit, so changing the rate updates every downstream total at once. The line items table holds 10 rows; you can insert more rows manually if you need them.</p>'
    . '<p>Open the .xlsx in Microsoft Excel, LibreOffice Calc, Apple Numbers, or upload to Google Sheets. Numbers and Sheets may render a few cell borders or fonts slightly differently from the Excel original, but the formulas work identically.</p>',

  'faqs' => [
    ['q' => 'Do these Excel templates auto-calculate?', 'a' => 'Yes. Line amount, subtotal, tax, total, and balance due are all formulas. Change a quantity or rate and the totals update.'],
    ['q' => 'How do I add more line items?', 'a' => 'Insert a row inside the line items range (rows 16 to 25 by default), then drag the formulas down. The subtotal formula auto-includes inserted rows.'],
    ['q' => 'Can I open these in Google Sheets?', 'a' => 'Yes. Upload the .xlsx to Google Drive and double click to open. Formulas convert cleanly.'],
    ['q' => 'Are the templates protected?', 'a' => 'No. There is no password, no locked cells, no macros. Edit anything you like.'],
  ],

  'related_slugs' => ['google-sheets', 'pdf', 'classic-excel', 'modern-excel', 'formal-excel', 'elegant-excel', 'ribbon-excel'],
];
