<?php
// invoice-template/data/google-sheets.php
// /invoice-template/google-sheets/

return [
  'slug' => 'google-sheets',
  'kind' => 'format-generic',
  'style' => null,
  'format' => 'google-sheets',

  'h1' => 'Free Google Sheets invoice templates',
  'meta_title' => 'Free Google Sheets Invoice Templates | Argo Books',
  'meta_description' => 'Free Google Sheets invoice templates with auto-calculating totals. Five styles, one-click "Make a copy" into your Google Drive.',

  'intro_html' => '<p>Google Sheets templates pair real-time collaboration with auto-calculating totals. Pick a style and click "Make a copy in Google Sheets" to clone into your Drive. Edit line items, watch subtotal, tax, and balance due update as you type, and share with colleagues without juggling file versions.</p>',

  'body_html' =>
    '<p>Every Google Sheets template is a fresh spreadsheet in your Drive: you own the copy, the original stays untouched, and other users get their own copies when they click the same link. There is no shared editing of the master template.</p>'
    . '<p>Pick a Google Sheets template if you want totals that recalculate as you type. Pick Google Docs if you prefer the document-editing experience without formulas.</p>',

  'faqs' => [
    ['q' => 'Do I need a Google account to use these templates?', 'a' => 'Yes. You need a free Google account to copy the template into your Drive.'],
    ['q' => 'Will the formulas survive when I edit?', 'a' => 'Yes. The formulas (SUM, basic arithmetic, IF) work identically in Google Sheets and survive any cell edits inside the line items range.'],
    ['q' => 'Can I export a Google Sheets invoice to PDF?', 'a' => 'Yes. In Sheets, choose File then Download then PDF Document (.pdf).'],
    ['q' => 'Are the totals auto-calculated in Google Sheets?', 'a' => 'Yes. Line amount, subtotal, tax, total, and balance due are all formulas in the Google Sheets templates.'],
  ],

  'related_slugs' => ['excel', 'google-docs', 'classic-google-sheets', 'modern-google-sheets', 'minimal-google-sheets', 'bold-google-sheets', 'professional-google-sheets'],
];
