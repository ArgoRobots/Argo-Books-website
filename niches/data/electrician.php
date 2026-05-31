<?php
// niches/data/electrician.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'electrician',

  'h1' => 'Free Electrician Invoice Generator',

  'meta_title' => 'Free Electrician Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free electrician invoice generator for service calls, hourly labor, parts, and permits. No signup. Download a clean PDF or Word file in a minute.',

  'intro_html' => <<<'HTML'
<p>Built for independent electricians and small electrical contracting companies that bill for installations, service calls, hourly labor, parts, and inspections. Enter your business details, list the service-call fee, your hours on site, the breakers, outlets, switches, or fixtures you supplied, and any permit costs, set a tax rate if your work is taxable, and produce a clean invoice in a couple of minutes. The form handles flat service-call charges, hourly labor at different rates for journeyman and apprentice time, itemized parts, and permit pass-throughs on the same invoice.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, which means homeowner addresses, job notes, parts pricing, and totals stay on your device. Pick an invoice style that matches your truck and uniforms, drop in your company logo and license number, record any deposit or card payment already collected, and download the result as a PDF for emailing on the way to the next call or a Word file if the property manager needs a format they can edit.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Service call fee', 'rate' => 125, 'quantity' => 1],
    ['description' => 'Labor (per hour)', 'rate' => 110, 'quantity' => 3],
    ['description' => '20 amp breaker', 'rate' => 22, 'quantity' => 2],
    ['description' => 'Tamper-resistant outlet', 'rate' => 8, 'quantity' => 4],
    ['description' => 'Permit and inspection fee', 'rate' => 180, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most electrician invoices start with a service-call fee, then add hourly labor for the time on site, with parts and fixtures itemized at the prices you actually paid plus any agreed markup. Residential work is usually due on completion, often paid by card or check before the truck leaves the driveway. Commercial clients and property managers typically pay 15 to 30 days after the invoice date (often written as Net 15 or Net 30). Permit fees are billed separately as a pass-through line when a permit is pulled, since the amount is set by the city and is not part of your labor rate. A late fee of 1.5% per month on overdue balances is standard and worth listing in the Terms section.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your rates) using the dropdown. Electricians often deal with two different rules on the same invoice: sales tax on parts and fixtures when they pass through to the customer, and a separate service or labor tax in some jurisdictions. Some states and provinces tax labor on new construction but not on repair work, and some treat the whole invoice as one taxable supply. Permit fees collected on behalf of the city are usually not taxable. Rules vary, so check your local rules or ask an accountant before you settle on a single rate.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I price a panel upgrade on an electrician invoice?',
      'a' => 'List the panel and main breaker as parts at their actual cost plus your markup, add labor as a separate line at your hourly rate for the install and wiring time, and include any permit and inspection fee as its own line. Splitting it this way lets the homeowner see the panel price, the labor, and the city fee separately, which usually heads off questions later.',
    ],
    [
      'q' => 'How should I handle permit fees on an electrician invoice?',
      'a' => "Bill the permit as its own line item at the exact amount the city charged, labeled clearly, for example 'City of Austin electrical permit'. Keeping it separate from labor makes it obvious that the fee is a pass-through, not part of your rate, and gives the customer a clean record for their files if the inspector asks for it later.",
    ],
    [
      'q' => 'Can I charge a code compliance or correction fee?',
      'a' => 'Yes, as long as you agreed on it in the work order before starting. Add it as its own line item with a short description, for example "Code compliance update: replace ungrounded outlets in kitchen", and list the agreed amount. Calling it out on its own line, rather than rolling it into labor, makes the scope clear if the customer asks why the final total is higher than the initial quote.',
    ],
    [
      'q' => 'How do I bill for an after-hours or emergency service call?',
      'a' => 'Use a higher service-call fee and a premium hourly rate for nights, weekends, and holidays, and label the lines so the customer can see why the rate is different. For example, list the call fee as "After-hours service call" and labor as "Emergency labor (per hour)". Keeping the premium visible on the invoice avoids the conversation about why the same job costs more on a Sunday.',
    ],
    [
      'q' => 'Do I send a separate invoice for the follow-up inspection?',
      'a' => 'Usually no. If the inspection visit is part of the original permitted job, include the inspection time and any re-inspection fee on the same invoice as the install, either as separate lines or rolled into the permit-and-inspection line. If the inspection is for a different job or a new permit, send it as its own invoice so the paper trail matches the permit number.',
    ],
  ],

  'related_slugs' => [
    'contractor',
    'plumber',
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
      ['description' => 'Service call fee', 'quantity' => 1, 'rate' => 125],
      ['description' => 'Labor (per hour)', 'quantity' => 3, 'rate' => 110],
      ['description' => 'Parts and fixtures', 'quantity' => 1, 'rate' => 95],
      ['description' => 'Permit and inspection fee', 'quantity' => 1, 'rate' => 180],
    ],
  ],
];
