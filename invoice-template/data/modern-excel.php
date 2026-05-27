<?php
// invoice-template/data/modern-excel.php
// /invoice-template/modern-excel/

return [
  'slug' => 'modern-excel',
  'kind' => 'style-format',
  'style' => 'modern',
  'format' => 'excel',

  'h1' => 'Modern Excel invoice template',
  'meta_title' => 'Modern Excel Invoice Template (.xlsx) | Argo Books',
  'meta_description' => 'Free Modern-style Excel invoice template with auto-calculating totals. Teal accent, ready to download and customize.',

  'intro_html' => '<p>The Modern Excel template combines a clean teal accent with the formulas that make Excel invoices fast to fill in. Quantity times rate gives line amount, line amounts sum to subtotal, subtotal times tax rate gives tax, and so on. Click the download button to save the .xlsx file.</p>',

  'body_html' =>
    '<p>The Modern Excel template is best for businesses with a contemporary brand presence who still rely on Excel for record keeping. Open the file, fill in the From and Bill To blocks, add line items, save. To send the invoice to a client, either email the .xlsx or export to PDF from Excel.</p>'
    . '<p>The teal accent reads as contemporary across industries. If your business has a strong color identity, you can change the header fill color to a brand color without breaking the formulas. Cell formulas are unprotected.</p>',

  'faqs' => [
    ['q' => 'Will the formulas survive if I open the Modern Excel template in Google Sheets?', 'a' => 'Yes. The Excel formulas used here (SUM, basic arithmetic, IF) all work identically in Google Sheets after upload.'],
    ['q' => 'Can I save the Modern Excel template as PDF?', 'a' => 'Yes. From Excel, choose File then Export then Create PDF/XPS, or use Save As with PDF format. The resulting PDF matches your on-screen layout.'],
    ['q' => 'How do I change the currency symbol in the Modern Excel template?', 'a' => 'Select the cells in columns D and E from row 16 down, right click, Format Cells, Currency, pick your symbol.'],
  ],

  'related_slugs' => ['excel', 'modern-pdf', 'modern-google-sheets', 'classic-excel', 'professional-excel'],
];
