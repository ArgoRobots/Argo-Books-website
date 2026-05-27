<?php
// invoice-template/data/modern-word.php
// /invoice-template/modern-word/

return [
  'slug' => 'modern-word',
  'kind' => 'style-format',
  'style' => 'modern',
  'format' => 'word',

  'h1' => 'Modern Word invoice template',
  'meta_title' => 'Modern Word Invoice Template (.docx) | Argo Books',
  'meta_description' => 'Free Modern-style Microsoft Word invoice template. Teal accent, clean sans-serif body, fully editable in Word or Google Docs.',

  'intro_html' => '<p>The Modern Word template gives you a polished, contemporary invoice that you can keep editing after the initial save. The teal accent reads as current without feeling trendy, and the sans-serif body keeps line items legible whether you print or send digitally. Click "Customize and download Word" to open the generator with Modern preselected.</p>',

  'body_html' =>
    '<p>Use the Word template when you expect to edit the invoice after downloading. Maybe the client asks for a line item to be rephrased, or the due date shifts. With a Word document you open the file, fix the field, save, and resend. With a PDF you would have to go back to the generator to make the same change.</p>'
    . '<p>The fonts in the Word version match the on-screen Modern style as closely as Microsoft Word allows. Some adjustment is normal: the recipient may have a different default font installed, in which case Word substitutes. If you need exact visual fidelity for every recipient, use the PDF template instead.</p>',

  'faqs' => [
    ['q' => 'Can I open the Modern Word template in Google Docs?', 'a' => 'Yes. Upload the .docx to Google Drive, double click to open. The dark header may render slightly differently because of font substitution, but the structure stays intact.'],
    ['q' => 'Does the Modern Word template include formulas?', 'a' => 'No. Word holds plain numbers, not formulas. If you need totals to recalculate as you edit line items, use the Excel or Google Sheets templates instead.'],
    ['q' => 'How big is the downloaded Word file?', 'a' => 'Under 100 KB without a logo. With a logo, the file size depends on the logo dimensions but is typically still under 500 KB.'],
  ],

  'related_slugs' => ['word', 'modern-pdf', 'modern-google-docs', 'classic-word', 'professional-word'],
];
