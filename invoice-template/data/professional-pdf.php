<?php
// invoice-template/data/professional-pdf.php
// /invoice-template/professional-pdf/

return [
  'slug' => 'professional-pdf',
  'kind' => 'style-format',
  'style' => 'professional',
  'format' => 'pdf',

  'h1' => 'Professional PDF invoice template',
  'meta_title' => 'Professional PDF Invoice Template | Free Download | Argo Books',
  'meta_description' => 'Free Professional-style PDF invoice template. Serif headings, thin navy rule, ready to customize and download in seconds.',

  'intro_html' => '<p>The Professional style uses serif headings with a thin navy rule under the header. It reads as formal in the way a law firm letterhead does, which makes it a good fit for legal practices, accountants, and consultants who bill on long-form engagements. Click "Customize and download PDF" to open the generator with Professional preselected.</p>',

  'body_html' =>
    '<p>The Professional PDF template suits established businesses that want their invoice to look familiar to every recipient. The thin navy rule anchors the header, the "INVOICE" label is unambiguous at first glance, and the table of line items reads cleanly even when printed on plain paper.</p>'
    . '<p>You can edit every label on the invoice surface. If your business uses "Description" rather than "Service", change the column header. If you charge a flat fee rather than billing by quantity and rate, you can rename those columns too. The PDF download captures whatever you see on screen.</p>',

  'faqs' => [
    ['q' => 'Can I use the Professional template for a freelance invoice?', 'a' => 'Yes. The Professional style works for any business, freelance or otherwise. Fill in your name in the From block, set your terms, and download.'],
    ['q' => 'How do I change the currency on the Professional PDF template?', 'a' => 'Open the generator and pick a currency from the toolbar. The generator covers 27 currencies and the change applies to every total on the invoice.'],
    ['q' => 'Will my logo fit in the Professional header?', 'a' => 'Yes. The generator resizes large logos automatically to keep the saved draft under the local-storage quota. PNG and JPG both work.'],
  ],

  'related_slugs' => ['pdf', 'professional-word', 'professional-excel', 'classic-pdf', 'modern-pdf'],
];
