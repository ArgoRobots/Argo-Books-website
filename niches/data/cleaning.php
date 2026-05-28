<?php
// niches/data/cleaning.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'cleaning',

  'h1' => 'Free Cleaning Service Invoice Generator',

  'meta_title' => 'Free Cleaning Service Invoice Generator | Argo Books',

  'meta_description' => 'Free invoice generator for cleaning services. Bill weekly recurring clients, one-off deep cleans, and move-outs. No signup. Download PDF or Word.',

  'intro_html' => <<<'HTML'
<p>Built for residential and commercial cleaning businesses, including solo cleaners, husband-and-wife teams, and small crews. Whether you bill a weekly recurring route, a one-off deep clean, a move-out, or a post-construction job, you can enter your rate, list any add-ons, set a tax rate if cleaning services are taxable where you work, and produce a clean invoice in under a minute. The form handles flat rates, hourly billing, per-bathroom and per-room add-ons, and supplies billed separately, so it fits the way you actually price jobs.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs in your browser, so customer names, addresses, and totals stay on your device. Pick an invoice style, drop in your logo, mark any deposit or auto-pay charge already received, and download the result as a PDF for emailing or a Word file if you want to keep editing it later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Weekly residential cleaning (per visit)', 'rate' => 140, 'quantity' => 4],
    ['description' => 'Deep clean add-on', 'rate' => 275, 'quantity' => 1],
    ['description' => 'Additional bathroom', 'rate' => 25, 'quantity' => 2],
    ['description' => 'Cleaning supplies', 'rate' => 18, 'quantity' => 1],
    ['description' => 'Move-out clean (2 bed, 1 bath)', 'rate' => 350, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Ongoing residential and commercial contracts are usually billed monthly, with the invoice sent on the first of the month covering visits already completed or the month ahead. One-off jobs like deep cleans and move-outs are typically due on receipt, with payment collected the day of service or within a few days. Auto-pay arrangements work well for recurring clients: the customer authorizes a card or bank transfer on file and you charge after each visit or once a month. Supplies and equipment can either be rolled into your rate or billed as a separate line item, whichever you and the client agreed to.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your prices) using the dropdown. Some jurisdictions treat cleaning services as taxable and others do not, and the rules can differ for residential versus commercial work, or for one-off jobs versus ongoing contracts. Thresholds for when you need to register and start charging tax also vary widely. Check the rules in your state, province, or country before assuming cleaning is exempt, and ask an accountant if you are not sure whether your work falls in or out.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I bill a recurring weekly cleaning client?',
      'a' => 'Set up one invoice per month that lists each visit as its own line, or a single line with the weekly rate and a quantity equal to the number of visits in the period. Send it on the same date each month so the client knows when to expect it. If the client is on auto-pay, mark the amount as paid using the Amount paid field once the charge clears.',
    ],
    [
      'q' => 'How should I price a deep clean versus a regular clean?',
      'a' => 'A regular maintenance clean keeps an already-clean home looking good and is usually a flat rate or hourly. A deep clean covers baseboards, inside appliances, blinds, grout, and other spots regular service skips, and runs 1.5 to 3 times the regular rate depending on the condition. List the deep clean as its own line item so the customer sees clearly what is included and why the total is higher than a normal visit.',
    ],
    [
      'q' => 'Should I charge customers for cleaning supplies and equipment?',
      'a' => 'Either approach works. Many cleaners build supplies into their hourly or flat rate and use the same products on every job. Others bill supplies as a separate line item, especially for deep cleans, move-outs, or post-construction work where extra products get used up. Whichever you pick, make sure the customer knows up front so the line item is not a surprise on the invoice.',
    ],
    [
      'q' => 'What is a reasonable cancellation policy for cleaning service invoices?',
      'a' => 'A common policy is full charge for cancellations inside 24 hours, half charge for 24 to 48 hours, and free reschedule with more notice. State the policy in the Terms section so it shows on every invoice, and if a client cancels late, add a clearly labeled cancellation fee line item rather than a vague charge.',
    ],
    [
      'q' => 'How do invoices differ for residential versus commercial cleaning?',
      'a' => 'Residential invoices usually go to a person, list the home address, and often get paid the same day or by auto-pay. Commercial invoices go to a business, often need a purchase order or job number in the Notes field, and follow the customer\'s payment terms, commonly due 15 to 30 days after the invoice date (often written as Net 15 or Net 30). The generator handles both, so pick the format that matches who is paying.',
    ],
  ],

  'related_slugs' => [
    'contractor',
    'plumber',
    'electrician',
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
    'paymentTerms' => 'Due on receipt',
    'lineItems' => [
      ['description' => 'Weekly residential cleaning (per visit)', 'quantity' => 4, 'rate' => 140],
      ['description' => 'Deep clean add-on', 'quantity' => 1, 'rate' => 275],
      ['description' => 'Cleaning supplies', 'quantity' => 1, 'rate' => 18],
      ['description' => 'Additional bathroom', 'quantity' => 2, 'rate' => 25],
    ],
  ],
];
