<?php
// invoice-template/data/formal-google-docs.php
// /invoice-template/formal-google-docs/

return [
  'slug' => 'formal-google-docs',
  'kind' => 'style-format',
  'style' => 'formal',
  'format' => 'google-docs',

  'h1' => 'Formal Google Docs invoice template',
  'meta_title' => 'Formal Google Docs Invoice Template | Argo Books',
  'meta_description' => 'Free Formal-style Google Docs invoice template. Stripped-down typography, click "Make a copy" to clone into your Drive, edit in the browser, export to PDF.',

  'intro_html' => '<p>The Formal Google Docs template gives you a stripped-down, whitespace-rich invoice you can edit straight from Google Drive. Click "Make a copy in Google Docs" to clone the template into your own account, then fill in the fields and share or export as you like.</p>',

  'body_html' =>
    '<p>The Formal Google Docs template suits brands with strong visual identity who collaborate in Google Workspace. Open the copy in your Drive, share it with a colleague for review, then download as PDF before sending to the client.</p>'
    . '<p>Google Docs renders most cells and fonts cleanly, but expect small layout differences from the on-screen Argo Books preview because Docs uses its own font substitution. If you need pixel-exact visual fidelity, the Formal PDF template is a better choice.</p>',

  'faqs' => [
    ['q' => 'Where does the copy of the Formal Google Docs template go?', 'a' => 'Into your own Google Drive. The original template stays untouched; every visitor gets their own copy.'],
    ['q' => 'Can I share the Formal Google Docs invoice with a client by link?', 'a' => 'Yes. After making your copy, use Share to grant access. We recommend exporting to PDF before sending to clients, since most clients expect a PDF invoice.'],
    ['q' => 'Will the minimal layout survive in Google Docs?', 'a' => 'Yes. Google Docs renders the stripped-down layout faithfully because there are no complex backgrounds to preserve.'],
  ],

  'related_slugs' => ['google-docs', 'formal-word', 'formal-google-sheets', 'modern-google-docs', 'elegant-google-docs'],
];
