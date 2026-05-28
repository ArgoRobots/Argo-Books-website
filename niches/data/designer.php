<?php
// niches/data/designer.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'designer',

  'h1' => 'Free Designer Invoice Generator',

  'meta_title' => 'Free Designer Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for graphic, brand, web, and illustration designers. No signup. Bill by project, asset, or hour and download a clean PDF.',

  'intro_html' => <<<'HTML'
<p>Built for graphic designers, brand designers, web designers, and illustrators who bill by the project, by the asset, or by the hour. Drop in your business details, list your design fees, set a tax rate if your work is taxable in your area, and produce a clean invoice in under a minute. The form handles flat project fees, hourly rates, deposits, revision rounds, and one-off file format add-ons, so the same setup works whether you are sending a deposit invoice for a logo or a final bill for a full brand system.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs in your browser, so client names, project fees, and totals stay on your device. Pick from a few invoice styles, drop in your studio logo, mark any deposit already collected, and download the result as a PDF for sending or a Word file if you want to keep editing it later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Logo design (flat project)', 'rate' => 1200, 'quantity' => 1],
    ['description' => 'Brand guidelines document', 'rate' => 1800, 'quantity' => 1],
    ['description' => 'Revision round', 'rate' => 200, 'quantity' => 2],
    ['description' => 'Social asset pack (10 templates)', 'rate' => 650, 'quantity' => 1],
    ['description' => 'Additional file format (EPS, favicon)', 'rate' => 75, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most design projects bill 50 percent up front to lock the schedule, with the balance due on final delivery. For established clients, a single project invoice due 30 days after the invoice date (often written as Net 30) is also common. List revision rounds beyond the agreed scope as their own line items rather than absorbing them into the project fee, so the client sees what each round costs. Final files are usually released once payment clears, and stating that in the Terms section saves a lot of awkward email later. A late fee of 1.5 percent per month on overdue balances is standard and worth spelling out the same way.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Designers often work across borders, with a client in one country and a designer in another, and the tax treatment depends on where each side is based and what is being delivered. Digital deliverables, like source files or licensed artwork, are sometimes taxed differently from physical prints in the same region, and some countries only require tax to be charged once you cross a revenue threshold. Set the tax rate as a percent in the Tax field, and switch between exclusive and inclusive using the dropdown. Check your local rules or ask an accountant before assuming an international invoice is tax free.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I bill a project deposit on a design invoice?',
      'a' => "Add the deposit as its own line item with a quantity of one and the dollar amount of the deposit, label it clearly (for example, '50 percent project deposit'), and send it as the first invoice. On the final invoice, list the full project fee and use the 'Amount paid' field to subtract the deposit so the balance due is correct.",
    ],
    [
      'q' => 'How should I charge for extra revision rounds?',
      'a' => 'Quote a fixed number of revision rounds in your proposal, then bill any rounds beyond that as separate line items on the next invoice. A flat fee per round, somewhere in the 150 to 300 dollar range for most freelance work, keeps the math simple and signals to the client that endless tweaks have a real cost.',
    ],
    [
      'q' => 'When should I hand over final design files?',
      'a' => 'The common practice is to release final files, including source files and any additional formats, only after the final invoice has been paid in full. Watermarked previews or low resolution proofs are fine to share during the project, and the Terms section is the right place to state that full file delivery follows payment.',
    ],
    [
      'q' => 'What can I do about scope creep on a design project?',
      'a' => 'Catch it on the invoice. When a client asks for work outside what was quoted, write it up as its own line item with a clear description and a separate fee, and send it on the next invoice. The bigger the gap between the original scope and the new ask, the more useful it is to send a short written estimate before starting the extra work.',
    ],
    [
      'q' => 'Which currency should I use for international design clients?',
      'a' => "Pick the currency you agreed on in the proposal, usually the client's local currency or US dollars if neither side is in the US. Use the currency dropdown near the top of the form and every line, subtotal, and total updates to match. The currency choice carries through to the PDF and Word download, and stating the currency in the Notes field avoids any doubt on the client side.",
    ],
  ],

  'related_slugs' => [
    'freelance',
    'developer',
    'photographer',
    'consultant',
    'usa',
    'canada',
    'uk',
  ],

  'related_template_slugs' => [
    'pdf',
    'modern-pdf',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => '50% upfront, balance on delivery',
    'lineItems' => [
      ['description' => 'Logo design (flat project)', 'quantity' => 1, 'rate' => 1200],
      ['description' => 'Brand guidelines document', 'quantity' => 1, 'rate' => 1800],
      ['description' => 'Revision round', 'quantity' => 1, 'rate' => 200],
      ['description' => 'Additional file format (EPS, favicon)', 'quantity' => 1, 'rate' => 75],
    ],
  ],
];
