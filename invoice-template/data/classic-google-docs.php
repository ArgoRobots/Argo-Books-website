<?php
// invoice-template/data/classic-google-docs.php
// /invoice-template/classic-google-docs/

return [
  'slug' => 'classic-google-docs',
  'kind' => 'style-format',
  'style' => 'classic',
  'format' => 'google-docs',

  'h1' => 'Classic Google Docs invoice template',
  'meta_title' => 'Classic Google Docs Invoice Template | Argo Books',
  'meta_description' => 'Free Classic-style Google Docs invoice template. Click "Make a copy" to clone into your Drive, edit in the browser, export to PDF.',

  'intro_html' => '<p>The Classic Google Docs template gives you a traditional dark-header invoice you can edit straight from Google Drive. Click "Make a copy in Google Docs" to clone the template into your own account, then fill in the fields and share or export as you like.</p>',

  'body_html' =>
    '<p>The Classic Google Docs template suits established businesses that already collaborate in Google Workspace. Open the copy in your Drive, share it with a colleague for review, then download as PDF before sending to the client.</p>'
    . '<p>Google Docs renders most cells and fonts cleanly, but expect small layout differences from the on-screen Argo Books preview because Docs uses its own font substitution. If you need pixel-exact visual fidelity, the Classic PDF template is a better choice.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Classic Google Docs template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Can I share the Classic Google Docs invoice with a client by link?', 'a' => 'Yes. After making your copy, use Share to grant access. We recommend exporting to PDF before sending to clients, since most clients expect a PDF invoice.'],
    ['q' => 'Will the dark header band survive in Google Docs?', 'a' => 'Yes. Google Docs supports cell shading and bold text, which is what the dark header band uses.'],
  ],

  'related_slugs' => ['google-docs', 'classic-word', 'classic-google-sheets', 'modern-google-docs', 'professional-google-docs'],
];
