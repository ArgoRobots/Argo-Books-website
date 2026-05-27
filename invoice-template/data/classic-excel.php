<?php
// invoice-template/data/classic-excel.php
// /invoice-template/classic-excel/

return [
  'slug' => 'classic-excel',
  'kind' => 'style-format',
  'style' => 'classic',
  'format' => 'excel',

  'h1' => 'Classic Excel invoice template',
  'meta_title' => 'Classic Excel Invoice Template (.xlsx) | Argo Books',
  'meta_description' => 'Free Classic-style Excel invoice template with auto-calculating totals. Traditional dark header, ready to download and customize.',

  'intro_html' => '<p>The Classic Excel template combines the traditional dark header look with the formulas that make Excel invoices fast to fill in. Quantity times rate gives line amount, line amounts sum to subtotal, subtotal times tax rate gives tax, and so on. Click the download button to save the .xlsx file.</p>',

  'body_html' =>
    '<p>The Classic Excel template is best for businesses that already keep records in Excel and want the invoice to live in the same workflow. Open the file, fill in the From and Bill To blocks, add line items, save. To send the invoice to a client, either email the .xlsx or export to PDF from Excel.</p>'
    . '<p>The dark header band reads as conservative across industries. If your business has a strong color identity, you can change the header fill color to a brand color without breaking the formulas. Cell formulas are unprotected.</p>',

  'faqs' => [
    ['q' => 'Will the formulas survive if I open the Classic Excel template in Google Sheets?', 'a' => 'Yes. The Excel formulas used here (SUM, basic arithmetic, IF) all work identically in Google Sheets after upload.'],
    ['q' => 'Can I save the Classic Excel template as PDF?', 'a' => 'Yes. From Excel, choose File then Export then Create PDF/XPS, or use Save As with PDF format. The resulting PDF matches your on-screen layout.'],
    ['q' => 'How do I change the currency symbol in the Classic Excel template?', 'a' => 'Select the cells in columns D and E from row 16 down, right click, Format Cells, Currency, pick your symbol.'],
  ],

  'related_slugs' => ['excel', 'classic-pdf', 'classic-google-sheets', 'modern-excel', 'professional-excel'],
];
