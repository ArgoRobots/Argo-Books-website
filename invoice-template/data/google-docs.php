<?php
// invoice-template/data/google-docs.php
// /invoice-template/google-docs/

return [
  'slug' => 'google-docs',
  'kind' => 'format-generic',
  'style' => null,
  'format' => 'google-docs',

  'h1' => 'Free Google Docs invoice templates',
  'meta_title' => 'Free Google Docs Invoice Templates | Argo Books',
  'meta_description' => 'Free Google Docs invoice templates. Five styles, one-click "Make a copy" into your Google Drive. Edit, share, export to PDF.',

  'intro_html' => '<p>Google Docs templates are the easiest way to collaborate on an invoice in real time. Pick a style and click "Make a copy in Google Docs" to clone the template into your own Google Drive. Edit freely, share with colleagues, and export to PDF from inside Docs when you are ready to send.</p>',

  'body_html' =>
    '<p>Every Google Docs template is a fresh document in your Drive: you own the copy, the original stays untouched, and other users get their own copies when they click the same link. There is no shared editing of the master template.</p>'
    . '<p>Pick a Google Docs template if you want the convenience of in-browser editing without paying for Microsoft Word, or if you are already collaborating with colleagues inside Google Workspace. Pick a Google Sheets template instead if you want the totals to recalculate as you type.</p>',

  'faqs' => [
    ['q' => 'Do I need a Google account to use these templates?', 'a' => 'Yes. You need a free Google account to copy the template into your Drive.'],
    ['q' => 'Will the layout survive when I edit?', 'a' => 'Google Docs may shift the layout slightly if you change the font size or add long text. The structure (header, line items table, totals) stays intact.'],
    ['q' => 'Can I export a Google Docs invoice to PDF?', 'a' => 'Yes. In Docs, choose File then Download then PDF Document (.pdf).'],
    ['q' => 'Are the totals auto-calculated in Google Docs?', 'a' => 'No. Google Docs holds plain numbers. If you want auto-calculation, use the Google Sheets templates.'],
  ],

  'related_slugs' => ['word', 'google-sheets', 'classic-google-docs', 'modern-google-docs', 'formal-google-docs', 'elegant-google-docs', 'ribbon-google-docs'],
];
