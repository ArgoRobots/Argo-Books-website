<?php
// niches/data/uk.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'uk',

  'h1' => 'Free Invoice Generator for the UK',

  'meta_title' => 'Free UK Invoice Generator: GBP and VAT | Argo Books',

  'meta_description' => 'Free invoice generator for UK freelancers, sole traders, and limited companies. Bill in GBP, add your VAT number, and download a clean PDF or Word file.',

  'intro_html' => <<<'HTML'
<p>Built for UK freelancers, sole traders, limited companies, and small business owners who need to send a tidy invoice without subscribing to anything. Enter your business details, list your hours or fees in pounds sterling, add a VAT line if you are registered, and produce a professional invoice in under a minute. The form handles hourly work, fixed project fees, retainers, and reimbursable materials, so it fits everyone from a one-person consultancy to a small limited company billing trade clients.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, so client names, rates, and totals stay on your device. Pick a style, drop in your logo, set the currency to GBP, choose the VAT label for your tax field, and download the result as a PDF for sending or a Word file if you want to edit it later.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Consulting (per hour)', 'rate' => 120, 'quantity' => 8],
    ['description' => 'Project setup and discovery', 'rate' => 750, 'quantity' => 1],
    ['description' => 'Design work (per hour)', 'rate' => 95, 'quantity' => 12],
    ['description' => 'Materials and reimbursable expenses', 'rate' => 240, 'quantity' => 1],
    ['description' => 'Monthly retainer', 'rate' => 1500, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most UK clients pay 30 days after the invoice date (often written as Net 30), with the clock starting the day the invoice is issued. If you are VAT registered, your VAT number has to appear on every invoice you send, alongside the VAT rate per line, the total excluding VAT, and the total including VAT. Bank transfer over BACS or Faster Payments is the standard method for B2B work in the UK, and both can be listed in the Notes section along with your sort code and account number. For overdue balances, you can recover statutory interest and a fixed sum under the Late Payment of Commercial Debts (Interest) Act 1998, which is worth flagging in your Terms so it is not a surprise.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>The UK standard VAT rate is 20%, with a 5% reduced rate for items like domestic fuel and power and child car seats, and a 0% zero rate for goods like most food, books, and children's clothes. You must register for VAT once your taxable turnover passes the threshold, which is 90,000 pounds per year as of 2024. Once registered, every invoice must show your VAT number, the VAT rate on each line, the total excluding VAT, the VAT amount, and the total including VAT. Making Tax Digital for VAT also applies, so VAT-registered businesses keep digital records and submit returns through compatible software. Check the current HMRC guidance or speak to your accountant before assuming a rate or exemption.</p>
HTML,

  'faqs' => [
    [
      'q' => 'When do I have to register for VAT in the UK?',
      'a' => 'You must register once your VAT-taxable turnover over any rolling 12-month period passes the threshold, which is 90,000 pounds as of 2024. You can also register voluntarily below that if it suits your business, for example when most of your clients are VAT registered and can reclaim it. Check the current HMRC guidance because the threshold is reviewed from time to time.',
    ],
    [
      'q' => 'What has to appear on a UK VAT invoice?',
      'a' => "If you are VAT registered, every invoice needs your VAT number, an invoice number and date, your business name and address, the customer's name and address, a description of each line, the VAT rate on each line, the total excluding VAT, the VAT amount, and the total including VAT. The generator has fields for all of these, so you can fill them in once and reuse the layout.",
    ],
    [
      'q' => 'Does this work with Making Tax Digital?',
      'a' => 'The generator produces a PDF or Word invoice, so it is a document tool, not an MTD bridging tool. VAT-registered businesses under Making Tax Digital still need compatible software to keep digital records and file VAT returns. Use the generator alongside your bookkeeping software, or step up to the full accounting app linked at the bottom of the page if you want record keeping and reporting handled too.',
    ],
    [
      'q' => 'Can I send an invoice in pounds sterling?',
      'a' => 'Yes. Pick GBP from the currency dropdown near the top of the form and every line, subtotal, and total updates to use the pound symbol. The currency choice carries through to the PDF and Word download, and you can still switch to another currency for clients you bill in euros or dollars.',
    ],
    [
      'q' => 'What can I do when a UK client pays late?',
      'a' => 'Send a polite reminder a day or two after the due date with a fresh copy of the invoice. Under the Late Payment of Commercial Debts (Interest) Act 1998, you can claim statutory interest and a fixed recovery sum on overdue B2B invoices. Stating your late payment terms in the Terms section makes it easier to apply them on the next invoice without it feeling out of the blue.',
    ],
  ],

  'related_slugs' => [
    'usa',
    'canada',
    'australia',
    'india',
    'freelance',
    'consultant',
    'designer',
    'developer',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Country page: GB activates the hreflang cluster for the
  // invoice-generator concept.
  'country' => 'GB',
  'concept' => 'invoice-generator',

  'generator_defaults' => [
    'country' => 'GB',
    'paymentTerms' => 'Payment due in 30 days',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 8, 'rate' => 120],
      ['description' => 'Project setup and discovery', 'quantity' => 1, 'rate' => 750],
      ['description' => 'Materials and reimbursable expenses', 'quantity' => 1, 'rate' => 240],
    ],
  ],
];
