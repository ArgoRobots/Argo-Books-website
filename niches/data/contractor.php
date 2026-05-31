<?php
// niches/data/contractor.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'contractor',

  'h1' => 'Free Contractor Invoice Generator',

  'meta_title' => 'Free Contractor Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free contractor invoice generator for tradespeople. No signup. Handle deposits, progress payments, and materials, then download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for general contractors, sub-contractors, and tradespeople who bill for construction, renovation, and repair work. Enter your business details, list your labor hours, materials, and any permits or disposal fees, set a tax rate if your work is taxable, and produce a clean invoice in a couple of minutes. The form handles a 50% deposit up front, a midpoint draw, and a final balance, so progress billing on multi-week jobs lines up the way most contracts already read.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, which means client addresses, job site details, rates, and totals stay on your device. Pick an invoice style that matches your brand, drop in your company logo, record any deposit already collected, and download the result as a PDF for emailing to the homeowner or a Word file if you want to tweak the wording before sending.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Project deposit (50%)', 'rate' => 2500, 'quantity' => 1],
    ['description' => 'Labor (per hour)', 'rate' => 75, 'quantity' => 40],
    ['description' => 'Materials and supplies', 'rate' => 1850, 'quantity' => 1],
    ['description' => 'Permit and inspection fees', 'rate' => 320, 'quantity' => 1],
    ['description' => 'Disposal and haul-away', 'rate' => 180, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most contractor invoices are billed in stages: a deposit before any work begins, a midpoint draw once framing or rough-in is complete, and the balance due on substantial completion. Payment due 15 days after the invoice date (often written as Net 15) is common for homeowners, and 30 days after the invoice date (Net 30) is typical for commercial clients or property managers. Materials can either be invoiced separately at cost, marked up and bundled into a single line, or itemized line by line so the customer sees exactly what was bought. A late fee of 1.5% per month on overdue balances is standard, and worth listing in the Terms section so it does not feel like a surprise later.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive and inclusive using the dropdown. Contractors often deal with two different taxes on the same job: sales tax on materials when they pass through to the customer, and a separate service or labor tax in jurisdictions that charge one. Some states tax labor on new construction but not on repair work, and some provinces treat the whole invoice as one taxable supply. Rules vary by state, province, and the type of work, so check your local rules or ask an accountant before you settle on a single rate.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I add my company logo to a contractor invoice?',
      'a' => 'Click the logo placeholder in the top left of the invoice, pick a file from your device, and it appears in both the preview and the download. PNG, JPG, and SVG all work, and the logo carries through to the PDF and Word file so your branding stays consistent across every job.',
    ],
    [
      'q' => 'How do I invoice progress payments on a multi-stage job?',
      'a' => "Send one invoice per stage. The first lists the deposit as a single line at its full dollar amount. The midpoint invoice covers the work completed up to that point. The final invoice shows the full contract value with the deposit and midpoint already received entered in the 'Amount paid' field, so the balance due matches what the customer still owes.",
    ],
    [
      'q' => 'Should I mark up materials on a contractor invoice?',
      'a' => 'Most contractors do, with a markup somewhere between 10 and 30% to cover sourcing, pickup, and storage. You can either roll the markup into a single materials line, or list materials at cost and add a separate handling fee. Either way, agree on the approach with the customer in the written contract so the final invoice does not surprise them.',
    ],
    [
      'q' => 'Can I record a partial payment on a contractor invoice?',
      'a' => "Yes. Enter the amount the customer has already paid in the 'Amount paid' field, and the balance due at the bottom of the invoice updates to show what is left. This works for deposits, midpoint draws, or any check the customer dropped off before the final invoice went out.",
    ],
    [
      'q' => 'How do I bill for change orders on a contractor invoice?',
      'a' => "Add each change order as its own line item with a clear label, for example 'Change order 1: upgrade to quartz counters', and list the agreed extra cost. Keeping change orders on separate lines, rather than rolling them into the original scope, makes it easy for the customer to see exactly what changed and what they signed off on.",
    ],
  ],

  'related_slugs' => [
    'plumber',
    'electrician',
    'cleaning',
    'photographer',
    'usa',
    'canada',
    'australia',
  ],

  'related_template_slugs' => [
    'pdf',
    'excel',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => '50% deposit, balance due in 15 days',
    'lineItems' => [
      ['description' => 'Project deposit (50%)', 'quantity' => 1, 'rate' => 2500],
      ['description' => 'Materials and supplies', 'quantity' => 1, 'rate' => 1850],
      ['description' => 'Labor (per hour)', 'quantity' => 40, 'rate' => 75],
    ],
  ],
];
