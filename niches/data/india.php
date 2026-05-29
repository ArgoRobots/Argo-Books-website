<?php
// niches/data/india.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'india',

  'h1' => 'Free Invoice Generator for India',

  'meta_title' => 'Free Invoice Generator for India (GST) | Argo Books',

  'meta_description' => 'Free invoice generator for Indian freelancers and MSME owners. Bill in INR, add GST with CGST/SGST or IGST, and download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for Indian freelancers, MSME owners, and service providers who bill in INR and need to handle GST correctly on every invoice. Enter your business details, list your hours, day rates, or project fees, set the right GST rate for the customer you are billing, and produce a clean invoice in under a minute. The form handles hourly billing, day rates, retainers, and project setup fees, so the same setup works whether you are invoicing a client in your own state or one across the country.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Pick India in the country dropdown, set the currency to INR, and the tax row relabels to GST so you can add the right rate per line. Drop in your logo, add your GSTIN, list the SAC for each service, mark any advance already received, and download the invoice as a PDF for sending or a Word file if you want to keep editing it later. Everything runs in your browser, so client names and totals stay on your device.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Software development (per hour)', 'rate' => 3000, 'quantity' => 20],
    ['description' => 'Consulting day rate', 'rate' => 35000, 'quantity' => 2],
    ['description' => 'Project setup and discovery', 'rate' => 75000, 'quantity' => 1],
    ['description' => 'Monthly advisory retainer', 'rate' => 50000, 'quantity' => 1],
    ['description' => 'Materials and reimbursable expenses', 'rate' => 12000, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most Indian clients pay 30 days after the invoice date (often written as Net 30), with the clock starting the day the invoice is issued. For new clients or larger projects, an advance of 25 to 50 percent up front is normal and the balance falls due once the work is delivered. UPI is the quickest way to settle small invoices, while NEFT and RTGS cover larger B2B payments, and cheque is still used by some legacy buyers. List your bank name, account number, IFSC code, and UPI ID in the Notes or Terms section so the client has everything they need. Many B2B clients deduct TDS at 1 to 10 percent depending on the service category, so the actual amount credited to your bank may be lower than the invoice total.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>GST in India splits based on where your customer is located. If your customer is in the same state as you, the sale is intra-state and you charge CGST plus SGST, typically 9 percent each for most services, totalling 18 percent. If the customer is in a different state, the sale is inter-state and you charge IGST instead, typically 18 percent for most services. 18 percent is the standard rate for the majority of services, but other slabs of 5, 12, and 28 percent exist for specific categories. Registration becomes mandatory once your aggregate annual turnover from services crosses 20 lakh rupees, or 10 lakh in the special category states like the North-Eastern states. Every GST invoice must show your 15-character GSTIN and the SAC for each service line, since HSN is for goods and SAC is for services. E-invoicing through the IRP is mandatory for businesses above the relevant turnover threshold, which has stepped down over the years and now covers most mid-sized businesses. Check the GST portal or speak to a CA before assuming a rate or exemption.</p>
HTML,

  'faqs' => [
    [
      'q' => 'Do I need an HSN or SAC code on my invoice?',
      'a' => 'For services, you need the SAC, which stands for Services Accounting Code. HSN, the Harmonized System of Nomenclature, is the equivalent for goods. Most professional services sit under SAC headings that start with 99, for example 998314 for IT consulting. Add the relevant code next to each line in the description field of the generator so it appears on the finished PDF, and confirm the right code for your work on the GST portal.',
    ],
    [
      'q' => 'When do I charge CGST and SGST versus IGST?',
      'a' => 'It depends on where your customer is, not where you are. If you and the customer are in the same state, the sale is intra-state and you charge CGST plus SGST, usually 9 percent each, for a total of 18 percent. If the customer is in a different state, the sale is inter-state and you charge IGST at the combined rate, usually 18 percent. Set the right combination in the Tax field for each invoice based on the place of supply.',
    ],
    [
      'q' => 'What is the format of a GSTIN and where do I put it?',
      'a' => 'GSTIN is a 15-character alphanumeric identifier. The first two digits are the state code, the next ten are your PAN, the thirteenth is the entity number for that PAN in that state, the fourteenth is the letter Z by default, and the last is a check character. Add your GSTIN to the business details section so it appears at the top of the invoice, and ask your client for theirs so you can include it under the bill-to block.',
    ],
    [
      'q' => 'How do I show TDS on my invoice?',
      'a' => 'You do not deduct TDS yourself, the client deducts it before paying you. So the invoice still shows the full amount due including GST, and the client transfers the net of TDS amount and issues you a Form 16A or 26AS entry for the deducted portion. If you want to flag this to the client, add a note in the Terms section explaining that the invoice value is before TDS so there is no confusion when the bank credit lands lower than expected.',
    ],
    [
      'q' => 'Does this invoice work for e-invoicing under GST?',
      'a' => 'The generator produces a PDF or Word invoice, which is fine for clients who fall below the e-invoicing turnover threshold. Once your aggregate annual turnover crosses the threshold set by the GST Council, e-invoicing through the Invoice Registration Portal becomes mandatory and each invoice needs an IRN and a QR code from the IRP before it goes to the customer. Check your current obligation on the GST portal or ask your CA if you are close to the line.',
    ],
  ],

  'related_slugs' => [
    'usa',
    'canada',
    'uk',
    'australia',
    'freelance',
    'developer',
    'consultant',
    'designer',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Country page: IN joins the invoice-generator hreflang cluster.
  'country' => 'IN',
  'concept' => 'invoice-generator',

  'generator_defaults' => [
    'country' => 'IN',
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 20, 'rate' => 3000],
      ['description' => 'Project setup and discovery', 'quantity' => 1, 'rate' => 75000],
      ['description' => 'Advisory day rate', 'quantity' => 2, 'rate' => 35000],
    ],
  ],
];
