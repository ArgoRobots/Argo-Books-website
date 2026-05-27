<?php
// invoice-template/data/professional-google-docs.php
// /invoice-template/professional-google-docs/

return [
  'slug' => 'professional-google-docs',
  'kind' => 'style-format',
  'style' => 'professional',
  'format' => 'google-docs',

  'h1' => 'Professional Google Docs invoice template',
  'meta_title' => 'Professional Google Docs Invoice Template | Argo Books',
  'meta_description' => 'Free Professional-style Google Docs invoice template. Serif headings, click "Make a copy" to clone into your Drive, edit in the browser, export to PDF.',

  'intro_html' => '<p>The Professional Google Docs template gives you a formal, serif-headed invoice you can edit straight from Google Drive. Click "Make a copy in Google Docs" to clone the template into your own account, then fill in the fields and share or export as you like.</p>',

  'body_html' =>
    '<p>The Professional Google Docs template suits law firms, accountants, and consultants who already collaborate in Google Workspace. Open the copy in your Drive, share it with a colleague for review, then download as PDF before sending to the client.</p>'
    . '<p>Google Docs renders most cells and fonts cleanly, but expect small layout differences from the on-screen Argo Books preview because Docs uses its own font substitution. If you need pixel-exact visual fidelity, the Professional PDF template is a better choice.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Professional Google Docs template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Can I share the Professional Google Docs invoice with a client by link?', 'a' => 'Yes. After making your copy, use Share to grant access. We recommend exporting to PDF before sending to clients, since most clients expect a PDF invoice.'],
    ['q' => 'Will the serif headings and accent rule survive in Google Docs?', 'a' => 'Yes. Google Docs supports custom fonts and underlines, which are what the serif headings and accent rule use.'],
  ],

  'related_slugs' => ['google-docs', 'professional-word', 'professional-google-sheets', 'classic-google-docs', 'bold-google-docs'],
];
