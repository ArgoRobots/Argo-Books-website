<?php
// niches/data/freelance.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'freelance',

  'h1' => 'Free Freelance Invoice Generator',

  'meta_title' => 'Free Freelance Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free freelance invoice generator for writers, designers, and developers. No signup. Add your logo, set your rate, and download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for freelance writers, designers, developers, and other solo professionals who bill clients on their own. Enter your business details, list your hours or project fees, set a tax rate if your work is taxable, and produce a clean invoice in under a minute. The form handles hourly billing, flat project fees, deposits against future work, and per-deliverable rates, so the same setup works whether you charge by the hour or by the job.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, which means client names, rates, and totals stay on your device. Pick from a few invoice styles, drop in your logo, mark any deposit already received, and download the result as a PDF for sending or a Word file if you want to keep editing it later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Copywriting (per hour)', 'rate' => 85, 'quantity' => 8],
    ['description' => 'Web design (per hour)', 'rate' => 95, 'quantity' => 12],
    ['description' => 'Frontend development (per hour)', 'rate' => 125, 'quantity' => 16],
    ['description' => 'Project setup and discovery', 'rate' => 350, 'quantity' => 1],
    ['description' => 'Revision round', 'rate' => 150, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most freelance clients pay 15 to 30 days after the invoice date (often written as Net 15 or Net 30), with the clock starting the day the invoice is sent. For new clients or larger projects, a 50 percent deposit up front protects your time, and the balance falls due once the work is delivered. A late fee of 1.5 percent per month on overdue balances is common and worth stating in the Terms section so it does not feel like a surprise. Bank transfer, Stripe, and PayPal are the usual options, and you can list any of them in Notes or Terms.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your rates) using the dropdown. Many freelancers under a certain revenue threshold do not need to charge sales tax, VAT, or GST, but the threshold and the rules around digital versus physical work vary by country and state. Once you cross the local threshold, registration is usually mandatory and the tax number often has to appear on the invoice. Check your local rules or ask an accountant before assuming you are exempt.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I add my logo to a freelance invoice?',
      'a' => 'Click the logo placeholder in the top left of the invoice, pick an image from your device, and it appears in both the preview and the download. PNG, JPG, and SVG all work, and the logo travels with the file when you save it as a PDF or Word document.',
    ],
    [
      'q' => 'How do I bill a deposit on a freelance project?',
      'a' => "Add the deposit as its own line item with the dollar amount and a quantity of one, label it clearly (for example, '50 percent project deposit'), and send it as the first invoice. When you send the final invoice, list the full project fee and use the 'Amount paid' field to subtract the deposit so the balance due is correct.",
    ],
    [
      'q' => 'Can I send an invoice in a currency other than US dollars?',
      'a' => 'Yes. Pick your currency from the dropdown near the top of the form and every line, subtotal, and total updates to use the right symbol. The currency choice carries through to the PDF and Word download.',
    ],
    [
      'q' => 'What should I do when a client pays a freelance invoice late?',
      'a' => 'Send a polite reminder a day or two after the due date with a fresh copy of the invoice attached. If your Terms section already lists a late fee, apply it on the next invoice as a separate line item. Most late payments come from forgotten emails, so a quick follow-up usually settles it without harder steps.',
    ],
    [
      'q' => 'What goes in Notes versus Terms on a freelance invoice?',
      'a' => 'Notes are for things that apply only to this invoice, like the project name, a thank-you message, or a reference number from the client. Terms are the standing rules of how you do business: payment window, late fee, accepted methods, and any deposit policy. Keep Terms short so clients actually read them.',
    ],
  ],

  'related_slugs' => [
    'consultant',
    'designer',
    'developer',
    'photographer',
    'tutor',
    'usa',
    'canada',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Design work (per hour)', 'quantity' => 10, 'rate' => 90],
      ['description' => 'Project setup fee', 'quantity' => 1, 'rate' => 350],
      ['description' => 'Revision round', 'quantity' => 1, 'rate' => 150],
    ],
  ],
];
