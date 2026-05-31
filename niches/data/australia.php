<?php
// niches/data/australia.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'australia',

  'h1' => 'Free Invoice Generator for Australia',

  'meta_title' => 'Free Invoice Generator for Australia (AUD) | Argo Books',

  'meta_description' => 'Free invoice generator for Australian sole traders and small businesses. Bill in AUD, show your ABN, add GST, and download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for Australian sole traders, freelancers, contractors, and small business owners who bill in AUD and work under ATO rules. Enter your business details, list your hours or project fees, add your ABN, set GST at 10% if you are registered, and produce a clean invoice in a minute. The form handles hourly work, fixed project fees, deposits, materials, and monthly retainers, so the same setup works whether you are a tradie quoting a kitchen job in Brisbane or a consultant billing a client in Sydney.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Pick Australia in the country dropdown and the currency switches to AUD and the tax row relabels to GST. Drop in your logo, add your ABN to the business details, mark any deposit already received, and download the invoice as a PDF for sending or a Word file if you want to keep editing it later. Everything runs in your browser, so client names and totals stay on your device.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Consulting (per hour)', 'rate' => 150, 'quantity' => 8],
    ['description' => 'Project setup and discovery', 'rate' => 600, 'quantity' => 1],
    ['description' => 'Materials and supplies', 'rate' => 220, 'quantity' => 1],
    ['description' => 'Monthly retainer', 'rate' => 1500, 'quantity' => 1],
    ['description' => 'Revision round', 'rate' => 180, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most Australian clients pay 14 to 30 days after the invoice date (often written as Net 14 or Net 30), with the clock starting the day the invoice is issued. Late fees are not fixed by law and depend on what your contract says, so list the rate in your Terms section so the client sees it up front. BPAY and PayID are the easiest ways to settle small-to-medium invoices in AUD, and direct debit or EFT works well for ongoing clients. Your ABN has to appear on every invoice. If it is missing, the buyer is required to withhold PAYG from the payment.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>The standard GST rate in Australia is a flat 10% on most taxable supplies, and you must register for GST once your annual turnover passes $75,000 AUD (taxi and rideshare drivers have to register from the first dollar). Once registered, any sale over $82.50 AUD including GST means you have to issue a tax invoice rather than a regular one, and that tax invoice must show your ABN, the words "Tax invoice", the GST amount, and the GST-inclusive total. Most small businesses report GST quarterly through a Business Activity Statement, with monthly or annual cycles for higher and lower turnovers. If you do not quote an ABN, the buyer must withhold PAYG at 47% from the payment. Check the ATO guidance or ask your accountant before making a call on registration or GST-free supplies.</p>
HTML,

  'faqs' => [
    [
      'q' => 'When do I have to register for GST in Australia?',
      'a' => 'Registration becomes mandatory once your GST turnover reaches $75,000 AUD over a 12-month period, looking either at the last 12 months or the next projected 12 months. Taxi and rideshare drivers have to register from the start regardless of turnover. You can also register voluntarily below the threshold if most of your clients are GST registered and you want to claim GST credits on your business expenses. Check the ATO guidance or ask an accountant if you are close to the line.',
    ],
    [
      'q' => 'Do I have to put my ABN on every invoice?',
      'a' => 'Yes. If you run a business in Australia and you do not quote your ABN on the invoice, the buyer is required to withhold PAYG at 47% from the payment and send it to the ATO. That applies whether or not you are registered for GST. Add your ABN to the business details so it appears on every invoice you generate, and you avoid the withholding rule entirely.',
    ],
    [
      'q' => 'What is the difference between a tax invoice and a regular invoice?',
      'a' => 'A regular invoice is just a bill. A tax invoice is a specific document required when a GST-registered seller makes a taxable sale over $82.50 AUD including GST. It has to show the words "Tax invoice", your ABN, the date, the buyer\'s details for sales of $1,000 or more, a description of each item, the GST amount, and the GST-inclusive total. If you are not registered for GST, you issue a regular invoice without GST and you do not call it a tax invoice.',
    ],
    [
      'q' => 'How often do I lodge a BAS?',
      'a' => 'Most small businesses lodge a Business Activity Statement quarterly, which is the standard cadence the ATO assigns when you register for GST. Businesses with annual GST turnover of $20 million or more report monthly, and voluntarily registered businesses under $75,000 can choose to lodge annually. The BAS covers GST collected and paid, PAYG instalments, and any other relevant taxes. Your accounting software or bookkeeper usually handles the prep, and you lodge through the ATO portal or your tax agent.',
    ],
    [
      'q' => 'Which supplies are GST-free in Australia?',
      'a' => 'Some categories are GST-free, meaning you do not add GST to the sale but you can still claim GST credits on related expenses. Common examples include most basic food, certain medical and health services, some education courses, and exports. Input-taxed supplies, like residential rent and most financial services, work differently because you do not charge GST but you also cannot claim GST credits on costs. The categories have specific rules, so check the ATO guidance or ask your accountant before treating a sale as GST-free.',
    ],
  ],

  'related_slugs' => [
    'usa',
    'canada',
    'uk',
    'india',
    'freelance',
    'contractor',
    'cleaning',
    'plumber',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Country page: AU joins the invoice-generator hreflang cluster.
  'country' => 'AU',
  'concept' => 'invoice-generator',

  'generator_defaults' => [
    'country' => 'AU',
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 8, 'rate' => 150],
      ['description' => 'Project setup and discovery', 'quantity' => 1, 'rate' => 600],
      ['description' => 'Materials and supplies', 'quantity' => 1, 'rate' => 220],
    ],
  ],
];
