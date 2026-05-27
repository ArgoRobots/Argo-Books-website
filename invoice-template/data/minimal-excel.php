<?php
// invoice-template/data/minimal-excel.php
// /invoice-template/minimal-excel/

return [
  'slug' => 'minimal-excel',
  'kind' => 'style-format',
  'style' => 'minimal',
  'format' => 'excel',

  'h1' => 'Minimal Excel invoice template',
  'meta_title' => 'Minimal Excel Invoice Template (.xlsx) | Argo Books',
  'meta_description' => 'Free Minimal-style Excel invoice template with auto-calculating totals. Stripped-down typography, ready to download and customize.',

  'intro_html' => '<p>The Minimal Excel template strips the visual chrome back to plain typography while keeping the same auto-calculating formulas. Quantity times rate gives line amount, line amounts sum to subtotal, subtotal times tax rate gives tax, and so on. Click the download button to save the .xlsx file.</p>',

  'body_html' =>
    '<p>The Minimal Excel template is best for businesses with strong visual identity elsewhere who want the invoice to stay out of the way. Open the file, fill in the From and Bill To blocks, add line items, save. To send the invoice to a client, either email the .xlsx or export to PDF from Excel.</p>'
    . '<p>The stripped-down look reads as restrained across industries. If your business has a strong color identity, you can change the header fill color to a brand color without breaking the formulas. Cell formulas are unprotected.</p>',

  'faqs' => [
    ['q' => 'Will the formulas survive if I open the Minimal Excel template in Google Sheets?', 'a' => 'Yes. The Excel formulas used here (SUM, basic arithmetic, IF) all work identically in Google Sheets after upload.'],
    ['q' => 'Can I save the Minimal Excel template as PDF?', 'a' => 'Yes. From Excel, choose File then Export then Create PDF/XPS, or use Save As with PDF format. The resulting PDF matches your on-screen layout.'],
    ['q' => 'How do I change the currency symbol in the Minimal Excel template?', 'a' => 'Select the cells in columns D and E from row 16 down, right click, Format Cells, Currency, pick your symbol.'],
  ],

  'related_slugs' => ['excel', 'minimal-pdf', 'minimal-google-sheets', 'classic-excel', 'modern-excel'],
];
