<?php
// invoice-template/data/ribbon-excel.php
// /invoice-template/ribbon-excel/

return [
  'slug' => 'ribbon-excel',
  'kind' => 'style-format',
  'style' => 'ribbon',
  'format' => 'excel',

  'h1' => 'Ribbon Excel invoice template',
  'meta_title' => 'Ribbon Excel Invoice Template (.xlsx) | Argo Books',
  'meta_description' => 'Free Ribbon-style Excel invoice template with auto-calculating totals. Serif headings, thin navy rule, ready to download and customize.',

  'intro_html' => '<p>The Ribbon Excel template uses serif headings and a thin accent rule with the same auto-calculating formulas underneath. Quantity times rate gives line amount, line amounts sum to subtotal, subtotal times tax rate gives tax, and so on. Click the download button to save the .xlsx file.</p>',

  'body_html' =>
    '<p>The Ribbon Excel template is best for law firms, accountants, and consultants who bill on long-form engagements. Open the file, fill in the From and Bill To blocks, add line items, save. To send the invoice to a client, either email the .xlsx or export to PDF from Excel.</p>'
    . '<p>The serif headings read as formal across industries. If your business has a strong color identity, you can change the header fill color to a brand color without breaking the formulas. Cell formulas are unprotected.</p>',

  'faqs' => [
    ['q' => 'Will the formulas survive if I open the Ribbon Excel template in Google Sheets?', 'a' => 'Yes. The Excel formulas used here (SUM, basic arithmetic, IF) all work identically in Google Sheets after upload.'],
    ['q' => 'Can I save the Ribbon Excel template as PDF?', 'a' => 'Yes. From Excel, choose File then Export then Create PDF/XPS, or use Save As with PDF format. The resulting PDF matches your on-screen layout.'],
    ['q' => 'How do I change the currency symbol in the Ribbon Excel template?', 'a' => 'Select the cells in columns D and E from row 16 down, right click, Format Cells, Currency, pick your symbol.'],
  ],

  'related_slugs' => ['excel', 'ribbon-pdf', 'ribbon-google-sheets', 'classic-excel', 'modern-excel'],
];
