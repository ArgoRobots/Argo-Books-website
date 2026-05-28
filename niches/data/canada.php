<?php
// niches/data/canada.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'canada',

  'h1' => 'Free Invoice Generator for Canada',

  'meta_title' => 'Free Invoice Generator for Canada (CAD) | Argo Books',

  'meta_description' => 'Free invoice generator for Canadian freelancers and small businesses. Bill in CAD, set GST/HST, and download a clean PDF or Word file. No signup.',

  'intro_html' => <<<'HTML'
<p>Built for Canadian freelancers, contractors, and small business owners who bill in CAD and need to handle federal GST plus provincial HST, PST, or QST on the same invoice. Enter your business details, list your hours or project fees, set the right tax rate for the customer you are billing, and produce a clean invoice in a minute. The form handles hourly billing, flat project fees, deposits, and materials, so the same setup works whether you are invoicing a Toronto client one day and a Vancouver client the next.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Choose Canada in the country dropdown and the currency switches to CAD and the tax row relabels to GST/HST automatically. Drop in your logo, mark any deposit already received, and download the invoice as a PDF for sending or a Word file if you want to keep editing it later. Everything runs in your browser, so client names and totals stay on your device.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Consulting (per hour)', 'rate' => 125, 'quantity' => 10],
    ['description' => 'Project setup and discovery', 'rate' => 500, 'quantity' => 1],
    ['description' => 'Materials and supplies', 'rate' => 180, 'quantity' => 1],
    ['description' => 'Monthly retainer', 'rate' => 1200, 'quantity' => 1],
    ['description' => 'Revision round', 'rate' => 150, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most Canadian clients pay 30 days after the invoice date (often written as Net 30), with the clock starting the day the invoice is sent. For new clients or larger project work, a deposit up front is normal and the balance falls due once the work is delivered. A late fee of 2 percent per month on overdue balances is common and worth stating in the Terms section so it does not feel like a surprise. Interac e-Transfer is the easiest option for paying small invoices in CAD, while EFT and credit card cover larger amounts and out-of-province clients. You can list any of these methods in Notes or Terms.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Canada layers federal GST on top of provincial rules, so the right rate depends on where your customer is located, not where you are. The federal GST is 5 percent. Ontario, New Brunswick, Nova Scotia, Newfoundland and Labrador, and Prince Edward Island use a harmonized HST that combines the federal and provincial portions into a single rate. British Columbia, Saskatchewan, and Manitoba charge a separate PST alongside GST, and Quebec charges QST. Registration becomes mandatory once your worldwide taxable supplies cross $30,000, measured either in a single calendar quarter or as a running total across the last four quarters (the rolling 12-month threshold). Use place-of-supply rules to pick the right rate for each customer, and consult your accountant or check the CRA guidance for your specific situation.</p>
HTML,

  'faqs' => [
    [
      'q' => 'Do I need to charge GST/HST on my invoices?',
      'a' => 'If your worldwide taxable supplies stay under $30,000 in any single calendar quarter and under $30,000 added up across the last four quarters, you can operate as a small supplier and skip charging GST/HST. Once you cross that threshold, registration becomes mandatory and you have to collect the right rate going forward. Voluntary registration before the threshold is also an option if you want to claim input tax credits on your expenses. Ask an accountant if you are close to the line.',
    ],
    [
      'q' => 'Which province\'s tax rate should I apply?',
      'a' => 'The rate follows the customer, not your own location. If you are a Toronto designer billing a client in Calgary, you charge 5 percent GST because Alberta has no provincial sales tax. The same designer billing a client in Halifax would charge 15 percent HST. Set the rate in the Tax field for each invoice based on where your customer is, and check the CRA place-of-supply rules if you are not sure.',
    ],
    [
      'q' => 'Do I need to put my GST/HST number on the invoice?',
      'a' => 'Yes, if you are registered. The CRA requires registrants to show their GST/HST number on any invoice over $30 that includes tax. Add it to the Notes or Terms section of the generator so it appears on the finished PDF. If you are not registered as a small supplier, leave it off and do not charge GST/HST.',
    ],
    [
      'q' => 'Can clients pay by Interac e-Transfer?',
      'a' => 'Yes. Interac e-Transfer works for any invoice up to your bank\'s daily limit and is the simplest way for Canadian clients to pay small amounts. List the email address tied to your Interac account in the Notes or Terms section, along with the password or autodeposit instructions, so clients know where to send it. For larger invoices, EFT and credit card are more practical.',
    ],
    [
      'q' => 'Can I send the invoice in a currency other than CAD?',
      'a' => 'Yes. CAD is the default when Canada is selected, but you can switch to USD, EUR, GBP, or any other currency from the dropdown near the top of the form. Every line, subtotal, and total updates to the new symbol, and the choice carries through to the PDF and Word download. This is useful when you bill US or international clients in their own currency.',
    ],
  ],

  'related_slugs' => [
    'usa',
    'uk',
    'australia',
    'india',
    'freelance',
    'contractor',
    'consultant',
    'photographer',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Country page: CA joins the invoice-generator hreflang cluster.
  'country' => 'CA',
  'concept' => 'invoice-generator',

  'generator_defaults' => [
    'country' => 'CA',
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 10, 'rate' => 125],
      ['description' => 'Project setup and discovery', 'quantity' => 1, 'rate' => 500],
      ['description' => 'Materials and supplies', 'quantity' => 1, 'rate' => 180],
    ],
  ],
];
