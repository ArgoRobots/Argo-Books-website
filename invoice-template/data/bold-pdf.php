<?php
// invoice-template/data/bold-pdf.php
// /invoice-template/bold-pdf/

return [
  'slug' => 'bold-pdf',
  'kind' => 'style-format',
  'style' => 'bold',
  'format' => 'pdf',

  'h1' => 'Bold PDF invoice template',
  'meta_title' => 'Bold PDF Invoice Template | Free Download | Argo Books',
  'meta_description' => 'Free Bold-style PDF invoice template. Yellow accent header, high contrast, ready to customize and download in seconds.',

  'intro_html' => '<p>The Bold style uses a yellow accent header with high contrast against the dark body text. It catches the eye in a stack of paperwork and works well for agencies, retail, and creative shops that want their invoice to feel confident. Click "Customize and download PDF" to open the generator with Bold preselected.</p>',

  'body_html' =>
    '<p>The Bold PDF template suits established businesses that want their invoice to look familiar to every recipient. The yellow header bar catches the eye, the "INVOICE" label is unambiguous at first glance, and the table of line items reads cleanly even when printed on plain paper.</p>'
    . '<p>You can edit every label on the invoice surface. If your business uses "Description" rather than "Service", change the column header. If you charge a flat fee rather than billing by quantity and rate, you can rename those columns too. The PDF download captures whatever you see on screen.</p>',

  'faqs' => [
    ['q' => 'Can I use the Bold template for a freelance invoice?', 'a' => 'Yes. The Bold style works for any business, freelance or otherwise. Fill in your name in the From block, set your terms, and download.'],
    ['q' => 'How do I change the currency on the Bold PDF template?', 'a' => 'Open the generator and pick a currency from the toolbar. The generator covers 27 currencies and the change applies to every total on the invoice.'],
    ['q' => 'Will my logo fit in the Bold header?', 'a' => 'Yes. The generator resizes large logos automatically to keep the saved draft under the local-storage quota. PNG and JPG both work.'],
  ],

  'related_slugs' => ['pdf', 'bold-word', 'bold-excel', 'modern-pdf', 'professional-pdf'],
];
