<?php
// niches/data/plumber.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'plumber',

  'h1' => 'Free Plumber Invoice Generator',

  'meta_title' => 'Free Plumber Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free plumber invoice generator for call-outs, hourly labor, parts, and fixture installs. No signup. Itemize the job and download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for independent plumbers and small plumbing companies who bill for emergency call-outs, hourly labor, parts, and fixture installation across residential and light commercial jobs. Enter your business details, set your call-out fee, list the hours on site, itemize each part with its marked-up price, and produce a clean invoice in a couple of minutes. The form handles standard daytime rates and after-hours premiums on the same invoice, so a Saturday night service call can be billed exactly the way it ran without rebuilding the layout.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, which means customer addresses, fixture details, and totals stay on your device. Pick an invoice style, drop in your company logo and license number, note any payment taken at the door, and download the result as a PDF to hand to the homeowner before you pack the van, or a Word file if you want to adjust the wording for a property manager.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Service call-out fee', 'rate' => 95, 'quantity' => 1],
    ['description' => 'Labor (per hour)', 'rate' => 125, 'quantity' => 2],
    ['description' => 'Replacement P-trap and fittings', 'rate' => 28, 'quantity' => 1],
    ['description' => 'Single-handle kitchen faucet', 'rate' => 185, 'quantity' => 1],
    ['description' => 'After-hours premium (per hour)', 'rate' => 60, 'quantity' => 2],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most plumber invoices combine a flat call-out fee with hourly labor for the time spent on site, plus parts billed at a marked-up rate that covers sourcing and the trip to the supply house. Markups commonly run from 20 to 40 percent on smaller fittings and a little lower on big-ticket fixtures. Residential customers typically pay on completion by card, e-transfer, or check before you leave the property, while commercial clients and property managers usually settle 15 days after the invoice date (often written as Net 15). After-hours, weekend, and holiday calls carry a premium rate, often time-and-a-half on the labor line, and it helps to spell that out in the Terms section.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive and inclusive using the dropdown. Plumbers often hit two different tax situations on the same job: sales tax on parts and fixtures that pass through to the customer, and a separate tax on labor in jurisdictions that charge one. Some states and provinces tax repair labor but not new construction, others treat the full invoice as one taxable supply, and a handful exempt residential service work entirely. The rules vary by state, province, and the type of work, so check your local rules or ask an accountant before settling on a single rate.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I charge a call-out fee on a plumber invoice?',
      'a' => 'Add the call-out fee as its own line item at the top of the invoice with a quantity of one and your flat rate, for example 95 dollars. Keeping it on a separate line, rather than burying it inside the first hour of labor, shows the customer what the trip itself cost and what the time on site cost, which usually heads off questions on the doorstep.',
    ],
    [
      'q' => 'How do I bill for parts and mark them up?',
      'a' => "List each part on its own line with the marked-up price, not the cost from the supply house. A P-trap, a shutoff valve, and a faucet should each be separate lines so the customer sees exactly what went into the wall. Most plumbers run a markup of 20 to 40 percent on small parts to cover the trip, storage, and the time spent sourcing the right fitting.",
    ],
    [
      'q' => 'What rate should I charge for after-hours or emergency calls?',
      'a' => 'After-hours, weekend, and holiday work is commonly billed at time-and-a-half on the labor line, with the call-out fee sometimes doubled as well. Add the premium as a separate line, for example "After-hours premium (per hour)" at the difference between your standard and premium rates, so the customer can see how the night call added to the bill rather than wondering why the hourly looks high.',
    ],
    [
      'q' => 'How do I handle a warranty callback on the same job?',
      'a' => 'If the return visit falls inside the warranty period you stated on the original invoice, send a zero-dollar invoice that lists the work performed and shows "Warranty service, no charge" so there is a paper trail. If the callback is for a separate issue, or the original part is past warranty, invoice it as a new job and explain the scope in Notes.',
    ],
    [
      'q' => 'How do I invoice a job that took multiple visits?',
      'a' => "You can send one invoice per visit as you go, or hold everything until the work is finished and group it into a single invoice with each visit on its own block of lines. Either way, label the call-out fee and labor by date, for example 'Labor, Tuesday' and 'Labor, Thursday', so the customer can see how the hours add up across the week.",
    ],
  ],

  'related_slugs' => [
    'contractor',
    'electrician',
    'cleaning',
    'photographer',
    'usa',
    'canada',
    'australia',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => 'Due on completion',
    'lineItems' => [
      ['description' => 'Service call-out fee', 'quantity' => 1, 'rate' => 95],
      ['description' => 'Labor (per hour)', 'quantity' => 2, 'rate' => 125],
      ['description' => 'Replacement parts and fittings', 'quantity' => 1, 'rate' => 65],
    ],
  ],
];
