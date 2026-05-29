<?php
// invoice-template/data/ribbon-word.php
// /invoice-template/ribbon-word/

return [
  'slug' => 'ribbon-word',
  'kind' => 'style-format',
  'style' => 'ribbon',
  'format' => 'word',

  'h1' => 'Ribbon Word invoice template',
  'meta_title' => 'Ribbon Word Invoice Template (.docx) | Argo Books',
  'meta_description' => 'Free Ribbon-style Microsoft Word invoice template. Serif headings, thin navy rule, fully editable in Word or Google Docs.',

  'intro_html' => '<p>The Ribbon Word template gives you a formal, letterhead-style invoice that you can keep editing after the initial save. The serif headings and thin navy rule read as official, and the sans-serif body keeps line items legible whether you print or send digitally. Click "Customize and download Word" to open the generator with Ribbon preselected.</p>',

  'body_html' =>
    '<p>Use the Word template when you expect to edit the invoice after downloading. Maybe the client asks for a line item to be rephrased, or the due date shifts. With a Word document you open the file, fix the field, save, and resend. With a PDF you would have to go back to the generator to make the same change.</p>'
    . '<p>The fonts in the Word version match the on-screen Ribbon style as closely as Microsoft Word allows. Some adjustment is normal: the recipient may have a different default font installed, in which case Word substitutes. If you need exact visual fidelity for every recipient, use the PDF template instead.</p>',

  'faqs' => [
    ['q' => 'Can I open the Ribbon Word template in Google Docs?', 'a' => 'Yes. Upload the .docx to Google Drive, double click to open. The dark header may render slightly differently because of font substitution, but the structure stays intact.'],
    ['q' => 'Does the Ribbon Word template include formulas?', 'a' => 'No. Word holds plain numbers, not formulas. If you need totals to recalculate as you edit line items, use the Excel or Google Sheets templates instead.'],
    ['q' => 'How big is the downloaded Word file?', 'a' => 'Under 100 KB without a logo. With a logo, the file size depends on the logo dimensions but is typically still under 500 KB.'],
  ],

  'related_slugs' => ['word', 'ribbon-pdf', 'ribbon-google-docs', 'classic-word', 'modern-word'],
];
