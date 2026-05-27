<?php
// invoice-template/data/modern-google-docs.php
// /invoice-template/modern-google-docs/

return [
  'slug' => 'modern-google-docs',
  'kind' => 'style-format',
  'style' => 'modern',
  'format' => 'google-docs',

  'h1' => 'Modern Google Docs invoice template',
  'meta_title' => 'Modern Google Docs Invoice Template | Argo Books',
  'meta_description' => 'Free Modern-style Google Docs invoice template. Teal accent, click "Make a copy" to clone into your Drive, edit in the browser, export to PDF.',

  'intro_html' => '<p>The Modern Google Docs template gives you a contemporary, teal-accented invoice you can edit straight from Google Drive. Click "Make a copy in Google Docs" to clone the template into your own account, then fill in the fields and share or export as you like.</p>',

  'body_html' =>
    '<p>The Modern Google Docs template suits modern teams that already collaborate in Google Workspace. Open the copy in your Drive, share it with a colleague for review, then download as PDF before sending to the client.</p>'
    . '<p>Google Docs renders most cells and fonts cleanly, but expect small layout differences from the on-screen Argo Books preview because Docs uses its own font substitution. If you need pixel-exact visual fidelity, the Modern PDF template is a better choice.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Modern Google Docs template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Can I share the Modern Google Docs invoice with a client by link?', 'a' => 'Yes. After making your copy, use Share to grant access. We recommend exporting to PDF before sending to clients, since most clients expect a PDF invoice.'],
    ['q' => 'Will the teal accent survive in Google Docs?', 'a' => 'Yes. Google Docs supports text colors and styling, which is what the teal accent uses.'],
  ],

  'related_slugs' => ['google-docs', 'modern-word', 'modern-google-sheets', 'classic-google-docs', 'minimal-google-docs'],
];
