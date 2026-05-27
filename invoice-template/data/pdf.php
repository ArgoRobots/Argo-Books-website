<?php
// invoice-template/data/pdf.php
// /invoice-template/pdf/ format-generic landing page.

return [
  'slug' => 'pdf',
  'kind' => 'format-generic',
  'style' => null,
  'format' => 'pdf',

  'h1' => 'Free PDF invoice templates',
  'meta_title' => 'Free PDF Invoice Templates | Argo Books',
  'meta_description' => 'Free PDF invoice templates in five styles. Customize every field, download in one click, no signup. Pick Classic, Modern, Minimal, Bold, or Professional.',

  'intro_html' => '<p>PDF is the standard for sending invoices that look the same on every device. Open in any browser, attach to email, print to paper, every reader sees the same layout. Pick a visual style below and you will land in the free invoice generator with that style preselected. Fill in your details and download a finished PDF in one click.</p>',

  'body_html' =>
    '<p>Every PDF template on this page comes from the same free generator, so you only need to learn the tool once. The generator handles totals, tax, discounts, shipping, and balance due automatically. You can save a logo, set a currency, edit any label on the invoice, and the file you download is always a clean light-themed PDF.</p>'
    . '<p>Five styles cover the most common visual preferences. Classic is a traditional dark header with sans-serif body for general business use. Modern is a teal accent with a clean sans-serif feel. Minimal strips the document back to plain typography and lots of whitespace. Bold is a yellow header bar with high contrast that suits agencies and creative shops. Professional uses serif headings with a thin navy rule under the header for law firms, consulting practices, and accountants.</p>',

  'faqs' => [
    ['q' => 'Are these PDF invoice templates really free?', 'a' => 'Yes. There is no signup, no watermark, and no monthly limit. Download as many invoices as you need.'],
    ['q' => 'Can I customize the fields on a PDF template?', 'a' => 'Yes. Click a style below to open the free invoice generator with that style preselected. Every field, including the column labels and the word "INVOICE" itself, is editable.'],
    ['q' => 'Do the PDFs match the on-screen preview?', 'a' => 'Yes. The PDF download is a direct render of the live invoice surface, so what you see on screen is what you get in the PDF.'],
    ['q' => 'Can I add my logo to a PDF invoice template?', 'a' => 'Yes. Click the "Add your logo" slot in the generator and upload any PNG or JPG. The logo travels with the saved draft.'],
  ],

  'related_slugs' => ['word', 'excel', 'classic-pdf', 'modern-pdf', 'minimal-pdf', 'bold-pdf', 'professional-pdf'],
];
