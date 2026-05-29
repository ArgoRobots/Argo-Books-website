<?php
// invoice-template/data/elegant-excel.php
// /invoice-template/elegant-excel/

return [
  'slug' => 'elegant-excel',
  'kind' => 'style-format',
  'style' => 'elegant',
  'format' => 'excel',

  'h1' => 'Elegant Excel invoice template',
  'meta_title' => 'Elegant Excel Invoice Template (.xlsx) | Argo Books',
  'meta_description' => 'Free Elegant-style Excel invoice template with auto-calculating totals. Yellow accent header, ready to download and customize.',

  'intro_html' => '<p>The Elegant Excel template pairs a yellow accent header with auto-calculating formulas for a confident, eye-catching invoice. Quantity times rate gives line amount, line amounts sum to subtotal, subtotal times tax rate gives tax, and so on. Click the download button to save the .xlsx file.</p>',

  'body_html' =>
    '<p>The Elegant Excel template is best for agencies, creative shops, and other businesses that want their invoice to feel confident. Open the file, fill in the From and Bill To blocks, add line items, save. To send the invoice to a client, either email the .xlsx or export to PDF from Excel.</p>'
    . '<p>The yellow header reads as confident across industries. If your business has a strong color identity, you can change the header fill color to a brand color without breaking the formulas. Cell formulas are unprotected.</p>',

  'faqs' => [
    ['q' => 'Will the formulas survive if I open the Elegant Excel template in Google Sheets?', 'a' => 'Yes. The Excel formulas used here (SUM, basic arithmetic, IF) all work identically in Google Sheets after upload.'],
    ['q' => 'Can I save the Elegant Excel template as PDF?', 'a' => 'Yes. From Excel, choose File then Export then Create PDF/XPS, or use Save As with PDF format. The resulting PDF matches your on-screen layout.'],
    ['q' => 'How do I change the currency symbol in the Elegant Excel template?', 'a' => 'Select the cells in columns D and E from row 16 down, right click, Format Cells, Currency, pick your symbol.'],
  ],

  'related_slugs' => ['excel', 'elegant-pdf', 'elegant-google-sheets', 'modern-excel', 'ribbon-excel'],
];
