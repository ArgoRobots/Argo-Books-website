<?php
// niches/data/generic.php
//
// The broad, niche-agnostic landing page for the free invoice generator.
// Served at /free-invoice-generator/ (no slug in the canonical URL).
// See niches/data/_template.php for schema documentation.

return [
  'slug' => 'generic',

  'h1' => 'Free Invoice Generator',

  'meta_title' => 'Free Invoice Generator | Argo Books',

  'meta_description' => 'Free online invoice generator. No signup, no watermark. Fill in the details, then download a clean PDF or Word file in seconds.',

  'intro_html' => <<<'HTML'
<p>This is a free invoice generator for freelancers, contractors, and small business owners who just need a clean invoice without signing up for anything. Fill in your business details, add a few line items, set a tax rate if you need one, and download the finished invoice as a PDF or a Word file. Everything happens in your browser, so your numbers and client names never leave your computer.</p>
<p>The generator works for any kind of work you bill for, whether that is hours of consulting, a flat project fee, parts and labor, or a list of products. You can switch between three invoice templates, add a logo, mark amounts already paid, apply a discount, and add shipping. When you are done, the download is yours to keep, edit, and send however you like.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Consulting (per hour)', 'rate' => 100, 'quantity' => 5],
    ['description' => 'Project setup fee', 'rate' => 250, 'quantity' => 1],
    ['description' => 'Design work (per hour)', 'rate' => 75, 'quantity' => 8],
    ['description' => 'Materials and supplies', 'rate' => 45.50, 'quantity' => 3],
    ['description' => 'Travel time (per hour)', 'rate' => 50, 'quantity' => 2],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>The most common payment term for invoices is due 30 days after the invoice date (often written as Net 30). Shorter windows like due within a week or due 15 days after the invoice date (Net 7 or Net 15) are normal for smaller jobs or new clients, and a 50% deposit up front is reasonable for larger projects. If you want to encourage faster payment, add a clear late fee (often 1.5% per month on overdue balances) and state your accepted payment methods directly in the Terms section.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Use the Tax field in the generator to apply sales tax, GST, VAT, or any other rate as a percent of the subtotal. Switch between exclusive (tax added on top) and inclusive (tax already baked into your prices) using the dropdown. The tool does the math, but it does not register you with a tax authority or file returns for you. Check with your local rules to confirm which rate to charge and whether you need to show a tax number on the invoice.</p>
HTML,

  'faqs' => [
    [
      'q' => 'Do I need to sign up to use this invoice generator?',
      'a' => 'No. The generator runs entirely in your browser. There is no account, no email gate, and no watermark on the downloaded file.',
    ],
    [
      'q' => 'What file formats can I download?',
      'a' => 'You can download a PDF for sending to clients or a Word (.docx) file if you want to edit the invoice further in Microsoft Word, Google Docs, or LibreOffice.',
    ],
    [
      'q' => 'How do I add my logo to the invoice?',
      'a' => 'Click the "Add Your Logo" button in the top left of the invoice and pick an image from your device. PNG, JPG, and SVG all work. The logo appears on both the on-screen preview and the downloaded file.',
    ],
    [
      'q' => 'Can I save an invoice and edit it later?',
      'a' => 'The generator does not store invoices on a server, but your most recent invoice is kept in your browser until you clear site data or switch devices. For permanent records, download the PDF and keep a copy with your business files.',
    ],
    [
      'q' => 'How do I handle sales tax, GST, or VAT?',
      'a' => 'Enter the tax rate as a percent in the Tax row. Use exclusive mode to add tax on top of the subtotal, or inclusive mode if your listed prices already include tax. The generator calculates the tax amount and total for you.',
    ],
    [
      'q' => 'What should I put in the Notes and Terms sections?',
      'a' => 'Use Notes for anything specific to this invoice, such as a thank-you message or project reference. Use Terms for the rules that apply every time you bill, such as payment due date, late fees, and accepted payment methods.',
    ],
  ],

  'related_slugs' => [
    'freelance', 'contractor', 'consultant', 'designer', 'developer',
    'photographer', 'cleaning', 'plumber', 'electrician', 'tutor',
    'usa', 'canada', 'uk', 'australia', 'india',
  ],

  'related_template_slugs' => ['pdf', 'word', 'excel'],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // The generic landing page is the hreflang x-default for the
  // 'invoice-generator' concept. No country specialization.
  'country' => null,
  'concept' => 'invoice-generator',

  // Generic landing page intentionally has no pre-fills. Niche pages may set these.
  'generator_defaults' => null,
];
