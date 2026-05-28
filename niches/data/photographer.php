<?php
// niches/data/photographer.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'photographer',

  'h1' => 'Free Photographer Invoice Generator',

  'meta_title' => 'Free Photographer Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for wedding, portrait, event, and commercial photographers. Bill session fees, editing time, image licenses, and prints in a clean PDF.',

  'intro_html' => <<<'HTML'
<p>Built for wedding, portrait, event, and commercial photographers who handle their own billing between shoots. Enter your studio details, list the session fee, editing hours, image licensing, and any print or album add-ons, then download a clean invoice for the client. The form handles deposits already collected, balance-due amounts, per-image licensing rates, and travel charges, so the same setup works whether you shoot a half-day portrait session or a full wedding weekend.</p>
<p>There is no signup, no email gate, and no watermark. Everything runs in your browser, so client names, shoot dates, and rates stay on your device. Drop in your studio logo, pick a layout that fits the way you present your work, mark the retainer that came in at booking, and download the result as a PDF for sending or a Word file if you want to keep editing. The same invoice can carry the deposit on one line and the balance on the next.</p>
HTML,

  'sample_line_items' => [
    ['description' => 'Wedding photography (full day coverage)', 'rate' => 3200, 'quantity' => 1],
    ['description' => 'Editing and retouching (per hour)', 'rate' => 65, 'quantity' => 12],
    ['description' => 'Image license (high resolution, commercial use)', 'rate' => 150, 'quantity' => 25],
    ['description' => 'Fine art print package (10 prints, matted)', 'rate' => 450, 'quantity' => 1],
    ['description' => 'Travel and lodging', 'rate' => 280, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Most photographers ask for a retainer up front, usually 30 to 50 percent, with the balance due on delivery of the final gallery or prints. The retainer holds the date and covers planning time, so it is normally non-refundable and worth stating in the Terms section. Image licensing is best itemized on its own line so the client sees what the fee covers, separate from shoot time and editing. Prints, albums, and extra hours are typically billed as add-ons after the shoot, often on a second invoice once the client picks their selections. Bank transfer, Stripe, and major cards are the usual payment options.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive and inclusive using the dropdown. Photography services and physical goods are taxed differently in many places: digital files and licensing may be exempt or taxed at a lower rate, while physical prints, albums, and frames often carry sales tax even when the digital side does not. Some jurisdictions treat the whole package as taxable once any tangible item is included. Rules vary by state, province, or country, so it is worth confirming the local treatment for prints and digital delivery before assuming either is exempt, or asking an accountant who handles photographers in your area.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I bill a retainer or deposit on a photography invoice?',
      'a' => "Add the retainer as its own line item with a clear label (for example, '40 percent booking retainer') and send it as the first invoice when the client books. When the shoot is delivered, send the final invoice with the full package price listed and use the 'Amount paid' field to subtract the retainer so the balance due is correct. Both invoices stay tied to the same job in the client's records.",
    ],
    [
      'q' => 'Should image licensing be a separate line item?',
      'a' => "Yes, listing licensing on its own line makes the scope clear and protects you if the client later wants to use the images in ways the original fee did not cover. Spell out the use (personal, editorial, or commercial), the term (one year, perpetual), and the territory if it matters. Selling a license is not the same as selling the copyright, and keeping them separate avoids that confusion later.",
    ],
    [
      'q' => 'How do I add prints, albums, or other physical products to the invoice?',
      'a' => "Add each product as its own line item with the unit price and quantity, so the client sees what they ordered. Many photographers send a second invoice for prints and albums after the gallery is delivered and the client has made their selections, since the order is not final until then. Keeping physical goods on a separate line also helps with sales tax if your jurisdiction taxes prints differently from services.",
    ],
    [
      'q' => 'What should the cancellation policy say on a photography invoice?',
      'a' => "The Terms section is a good place for a short cancellation policy: that the retainer is non-refundable, that cancellations within a certain window forfeit a larger share, and how rescheduling works. Keep it short and plain so clients actually read it. The full contract still governs the booking, but a one or two line summary on the invoice prevents most disputes about why the retainer does not come back.",
    ],
    [
      'q' => 'How do I bill travel fees, mileage, or lodging for a shoot?',
      'a' => 'Add travel as a separate line item rather than rolling it into the session fee, so the client sees what they are paying for. Mileage can be billed at a flat per-mile rate, flights and lodging at cost or with a small handling markup, and a day rate for travel time on longer trips. Stating the travel policy in the Terms section, and itemizing the actual cost on the invoice, keeps the conversation simple.',
    ],
  ],

  'related_slugs' => [
    'freelance',
    'designer',
    'contractor',
    'consultant',
    'usa',
    'canada',
    'uk',
  ],

  'related_template_slugs' => [
    'pdf',
    'elegant-pdf',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => '30% deposit, balance on delivery',
    'lineItems' => [
      ['description' => 'Session fee (full day coverage)', 'quantity' => 1, 'rate' => 2400],
      ['description' => 'Editing and retouching (per hour)', 'quantity' => 10, 'rate' => 65],
      ['description' => 'Image license (high resolution)', 'quantity' => 20, 'rate' => 120],
      ['description' => 'Fine art print package', 'quantity' => 1, 'rate' => 450],
    ],
  ],
];
