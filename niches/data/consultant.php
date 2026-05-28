<?php
// niches/data/consultant.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'consultant',

  'h1' => 'Free Consultant Invoice Generator',

  'meta_title' => 'Free Consultant Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for independent consultants. Bill hourly, monthly retainers, or flat-fee projects. No signup. Download as PDF or Word.',

  'intro_html' => <<<'HTML'
<p>Built for independent consultants who bill clients on their own, whether that means management advisors, IT and marketing consultants, HR specialists, or solo strategy and operations shops. The form covers hourly billing in 15-minute increments, monthly retainers paid in advance, flat-fee deliverables such as written reports, and pass-through expenses like travel or third-party tools. Set your hourly rate, add a tax rate if your engagement is taxable, and the totals update as you type. The same setup works for a one-off advisory call and for an ongoing retainer client.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, so client names, hourly rates, and project totals stay on your device. Pick a clean invoice style, drop in your firm logo, mark any retainer already paid against the current invoice, and download the result as a PDF for sending or a Word file if you want to keep editing the wording later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Strategy session (per hour)', 'rate' => 250, 'quantity' => 4],
    ['description' => 'Advisory hours (per hour)', 'rate' => 200, 'quantity' => 12],
    ['description' => 'Monthly retainer', 'rate' => 5000, 'quantity' => 1],
    ['description' => 'Written report and recommendations', 'rate' => 3500, 'quantity' => 1],
    ['description' => 'Travel and out-of-pocket expenses', 'rate' => 425, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most consulting clients pay 15 to 30 days after the invoice date (often written as Net 15 or Net 30), with the clock starting the day the invoice is sent. Monthly retainers are usually billed in advance, with the invoice going out at the start of the period rather than the end. Hourly work is commonly tracked in 15-minute increments and rounded up, which is worth stating in your Terms so the math is not a surprise. A late fee of 1.5 percent per month on overdue balances is standard for B2B engagements. Bank transfer and credit card via Stripe are the usual options for larger invoices.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your rates) using the dropdown. Consulting services are taxable in some jurisdictions and exempt in others, and the rules often differ between B2B work, B2C work, and anything sold as a digital product or downloadable report. Some countries and states also tax professional services only above a registration threshold. Once you cross the local threshold, a tax number often has to appear on the invoice. Check your local rules or ask an accountant before assuming you are exempt.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I invoice for a monthly retainer?',
      'a' => 'Add the retainer as a single line item with a quantity of one and the full monthly fee as the rate, label it with the month it covers (for example, "Strategy retainer, March"), and send the invoice at the start of the period. If the retainer is already paid against this engagement, use the Amount Paid field to zero out the balance due, or send the invoice as a paid receipt for the client\'s records.',
    ],
    [
      'q' => 'Should I bill hourly or by the project as a consultant?',
      'a' => 'Both work, and the generator handles either. Hourly billing fits open-ended advisory work and discovery phases where the scope is still moving. Flat-fee billing fits well-defined deliverables like an audit, a written report, or a fixed-length engagement. Many consultants run hybrid invoices that combine a monthly retainer line with hourly overflow above an agreed cap, which the form supports by listing each as its own line item.',
    ],
    [
      'q' => 'How do I pass through expenses on a consulting invoice?',
      'a' => 'Add each expense as its own line item with a quantity of one and the actual cost as the rate, and label it clearly so the client knows it is a reimbursement rather than a fee (for example, "Travel: flight to Chicago" or "Third-party tool licence"). Keep receipts on file in case the client asks. If your agreement caps expenses or marks them up by a fixed percent, apply that before entering the line.',
    ],
    [
      'q' => 'How do I bill hours in 15-minute increments?',
      'a' => 'Enter the quantity as a decimal: 0.25 for 15 minutes, 0.5 for half an hour, 1.25 for an hour and 15 minutes, and so on. The line total multiplies the rate by the quantity, so a $250 rate at 1.25 hours shows as $312.50. State in your Terms that time is tracked in 15-minute increments and rounded up, so the rounding is documented and not a per-invoice debate.',
    ],
    [
      'q' => 'How do I add my firm logo to a consultant invoice?',
      'a' => 'Click the logo placeholder in the top left of the invoice, pick an image from your device, and it appears in both the preview and the download. PNG, JPG, and SVG all work. The logo travels with the file when you save it as a PDF or Word document, so the version your client receives matches what you see on screen.',
    ],
  ],

  'related_slugs' => [
    'freelance',
    'developer',
    'designer',
    'tutor',
    'usa',
    'canada',
    'uk',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
    'classic-pdf',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => 'Payment due in 15 days',
    'lineItems' => [
      ['description' => 'Strategy session (per hour)', 'quantity' => 4, 'rate' => 250],
      ['description' => 'Advisory hours (per hour)', 'quantity' => 10, 'rate' => 200],
      ['description' => 'Written report and recommendations', 'quantity' => 1, 'rate' => 3500],
    ],
  ],
];
