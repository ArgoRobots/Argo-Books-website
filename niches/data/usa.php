<?php
// niches/data/usa.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'usa',

  'h1' => 'Free Invoice Generator for the USA',

  'meta_title' => 'Free USA Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for US freelancers and small businesses. Bill in USD, set state sales tax, and download a clean PDF or Word file. No signup.',

  'intro_html' => <<<'HTML'
<p>Built for US freelancers, independent contractors, sole proprietors, LLC owners, and small business owners who bill in USD and need to handle state-level sales tax rules without a separate tool for each project. Enter your business details, list your hours or project fees, set the right sales tax rate for the customer you are billing, and produce a clean invoice in a minute. The form handles hourly billing, flat project fees, deposits, materials, and monthly retainers, so the same setup works whether you invoice a client in Texas one day and a client in New York the next.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Choose the United States in the country dropdown and the currency defaults to USD and the tax row relabels to Sales Tax. Drop in your logo, mark any deposit already received, add your EIN in Notes if a business client needs it, and download the invoice as a PDF for sending or a Word file if you want to keep editing it later. Everything runs in your browser, so client names and totals stay on your device.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Consulting (per hour)', 'rate' => 150, 'quantity' => 10],
    ['description' => 'Project setup and discovery', 'rate' => 750, 'quantity' => 1],
    ['description' => 'Materials and supplies', 'rate' => 220, 'quantity' => 1],
    ['description' => 'Monthly retainer', 'rate' => 1500, 'quantity' => 1],
    ['description' => 'Revision round', 'rate' => 175, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most US clients pay 15 to 30 days after the invoice date (often written as Net 15 or Net 30), with the clock starting the day the invoice is sent. For new clients or larger project work, a deposit up front is normal and the balance falls due once the work is delivered. A late fee of 1.5% per month on overdue balances is common, but state usury caps limit how high you can go, so check your state rules before setting a higher rate. ACH bank transfer is the lowest-cost option for paying invoices in USD, while checks, credit cards, Zelle, and wire transfers cover faster turnarounds, larger amounts, and out-of-state clients. You can list any of these methods in Notes or Terms.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>There is no federal sales tax in the US, so the rules are set state by state. Forty-five states plus the District of Columbia have a statewide sales tax, and many of those also allow counties and cities to add local rates on top, so a single ZIP code can land at a different combined rate than the one next door. The five states with no statewide sales tax are Alaska, Delaware, Montana, New Hampshire, and Oregon, though Alaska does allow local sales tax. Most services are not subject to sales tax in most states, but specific services like SaaS, digital goods, repair labor, and personal services are taxed in some states and the rules vary widely. Out-of-state sales can trigger economic nexus once you cross thresholds many states set at $100,000 in sales or 200 transactions a year, following the 2018 Wayfair decision. Consult a CPA in your state or check your state revenue department before assuming a sale is exempt.</p>
HTML,

  'faqs' => [
    [
      'q' => 'Do I need to collect sales tax on my invoices?',
      'a' => 'It depends on what you sell and where your customer is. Most services are not subject to sales tax in most states, but tangible goods almost always are, and some states tax specific services like SaaS, digital products, or repair work. Start by checking the rules in your home state, then check the rules in any state where you have customers. A CPA in your state can confirm what applies to your specific work.',
    ],
    [
      'q' => 'What is economic nexus and when does it apply to me?',
      'a' => 'Economic nexus means a state can require you to collect its sales tax once your sales into that state cross a threshold, even if you have no office or employees there. Many states use a $100,000 in sales or 200 transactions per year threshold, following the 2018 Wayfair Supreme Court decision. The exact threshold and what counts toward it vary, so check each state where you have meaningful customer volume.',
    ],
    [
      'q' => 'Should I put my EIN on the invoice?',
      'a' => 'Add your EIN when you are billing a business client, especially if they are likely to send you a 1099 at year end or need it for their own accounting. You can drop it into the Notes or Terms section so it appears on the finished invoice. For invoices to individual consumers, the EIN is not required and most freelancers leave it off.',
    ],
    [
      'q' => 'How does this work for 1099 contractors versus W-2 employees?',
      'a' => 'This generator is for billing as a self-employed contractor, sole proprietor, LLC owner, or small business. W-2 employees do not invoice their employer, they get paid through payroll. If you are a 1099 contractor, you send invoices to your clients and they pay the full amount with no tax withheld, then they may issue a Form 1099-NEC at the end of the year for amounts over $600.',
    ],
    [
      'q' => 'I work for clients in multiple states. Which sales tax rate do I use?',
      'a' => 'For most services and remote sales, the rate follows the customer location, not your own. Set the Sales Tax rate on each invoice based on where the customer is and whether you have nexus in their state. If you only have nexus in your home state, you generally only collect tax on sales to customers in that state. Once you cross another state\'s economic nexus threshold, you may need to register there and start collecting too.',
    ],
  ],

  'related_slugs' => [
    'canada',
    'uk',
    'australia',
    'india',
    'freelance',
    'contractor',
    'consultant',
    'designer',
    'developer',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Country page: US joins the invoice-generator hreflang cluster.
  'country' => 'US',
  'concept' => 'invoice-generator',

  'generator_defaults' => [
    'country' => 'US',
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 10, 'rate' => 150],
      ['description' => 'Project setup and discovery', 'quantity' => 1, 'rate' => 750],
      ['description' => 'Materials and supplies', 'quantity' => 1, 'rate' => 220],
    ],
  ],
];
