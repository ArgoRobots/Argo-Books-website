<?php
// niches/data/developer.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'developer',

  'h1' => 'Free Developer Invoice Generator',

  'meta_title' => 'Free Developer Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for software developers. Bill hourly, by sprint, or by milestone. No signup, no watermark. Download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for freelance software developers, web and mobile developers, and DevOps consultants who handle their own billing. Enter your business details, list your hours by track or your milestone fees, set a tax rate if your work is taxable, and produce a clean invoice in under a minute. The form handles hourly billing across backend, frontend, and infrastructure work, fixed-scope milestone payments, sprint retainers, and pass-through items like server costs or third-party licenses, so the same setup works whether you bill by the hour or by the deliverable.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, so client names, rates, and totals stay on your device. Pick an invoice style, drop in your logo or wordmark, mark any deposit already paid, and download the result as a PDF for sending or a Word file if you want to keep editing it later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Backend development (per hour)', 'rate' => 140, 'quantity' => 24],
    ['description' => 'Frontend development (per hour)', 'rate' => 125, 'quantity' => 18],
    ['description' => 'Code review (per hour)', 'rate' => 150, 'quantity' => 4],
    ['description' => 'Infrastructure setup and deployment', 'rate' => 175, 'quantity' => 6],
    ['description' => 'AWS hosting pass-through (March)', 'rate' => 180, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Developer rates usually fall in the $75 to $200 per hour range, with senior specialists charging more. Hourly billing due 15 to 30 days after the invoice date (often written as Net 15 or Net 30) is the most common setup for ongoing work, with the clock starting the day the invoice is sent. Sprint-based retainers are popular for longer engagements, where you invoice a fixed amount at the end of each two-week sprint. Fixed-scope projects usually run on milestone payments, often split as 50% up front and the balance on delivery. A late fee of 1.5% per month on overdue balances is worth stating in the Terms section.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your rates) using the dropdown. Developers often work across borders, and the tax treatment of digital services varies by jurisdiction. In the United States, sales tax on software and SaaS is uneven from state to state, and most consulting work is not taxed. In the European Union, business-to-consumer digital services usually fall under VAT and the OSS scheme. Other countries have their own thresholds and rules. Check what applies where you and your client are based.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I bill clients on different sprints in the same month?',
      'a' => "Use a single invoice with one line per sprint, label each one clearly (for example, 'Sprint 14: backend API work, March 4 to March 15'), and put the hours or fixed amount in the quantity and rate fields. If two sprints have very different rates, keep them on separate lines so the math is transparent and the client can match each line back to the agreed scope.",
    ],
    [
      'q' => 'How do I bill an international client without losing money on currency?',
      'a' => 'Pick the currency you and the client agreed on from the dropdown and the entire invoice updates to match. Quote in a currency you are comfortable holding, ideally the one your business bank account is in, so you do not eat the spread twice on conversion. Bank transfers via Wise, Stripe, and Payoneer are common ways to receive international payment without large fees.',
    ],
    [
      'q' => 'How do I track hours so my invoices match what I actually worked?',
      'a' => 'Most developers use a timer like Toggl, Harvest, or Clockify and tag entries by client, project, and work type. At the end of the period, export the totals, drop them into the line items here as quantity and rate, and you have an invoice that lines up with your time log. Round to the nearest quarter hour if your contract calls for it.',
    ],
    [
      'q' => 'How do I pass through server, hosting, or third-party costs to a client?',
      'a' => "Add each pass-through cost as its own line item with a quantity of one and the actual dollar amount you paid. Label it clearly (for example, 'AWS hosting, March') and attach the receipt or screenshot in your email when you send the invoice. Some developers add a small handling markup, others bill at cost; either is fine as long as the contract says which.",
    ],
    [
      'q' => 'Can I deduct equipment and software as expenses on a developer invoice?',
      'a' => 'The invoice itself is for client billing, not for tracking your own expenses. Your laptop, monitors, IDE licenses, and cloud bills are business expenses you record in your bookkeeping and claim on your tax return, separate from what you charge clients. If a client specifically agreed to reimburse a piece of hardware or a license, list it as a pass-through line item on the invoice with the receipt attached.',
    ],
  ],

  'related_slugs' => [
    'freelance',
    'designer',
    'consultant',
    'tutor',
    'usa',
    'canada',
    'india',
  ],

  'related_template_slugs' => [
    'pdf',
    'modern-pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => 'Payment due in 15 days',
    'lineItems' => [
      ['description' => 'Backend development (per hour)', 'quantity' => 24, 'rate' => 140],
      ['description' => 'Frontend development (per hour)', 'quantity' => 18, 'rate' => 125],
      ['description' => 'Code review (per hour)', 'quantity' => 4, 'rate' => 150],
      ['description' => 'Infrastructure setup and deployment', 'quantity' => 6, 'rate' => 175],
    ],
  ],
];
