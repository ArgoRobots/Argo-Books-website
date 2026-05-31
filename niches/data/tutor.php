<?php
// niches/data/tutor.php
// See niches/data/_template.php for schema.

return [

  'slug' => 'tutor',

  'h1' => 'Free Tutor Invoice Generator',

  'meta_title' => 'Free Tutor Invoice Generator (No Signup) | Argo Books',

  'meta_description' => 'Free invoice generator for private tutors, music teachers, and language coaches. Bill per session, per hour, or per package. Download as PDF or Word.',

  'intro_html' => <<<'HTML'
<p>Built for private tutors, music teachers, language teachers, and academic coaches who bill students or parents on their own. The form covers per-session rates, hourly billing, prepaid packages of lessons, materials and workbooks, and online session surcharges, so the same setup works for an in-person piano teacher, a SAT prep coach, or a video-call language tutor. Set your session rate, add a tax rate if lessons are taxable in your area, list the package discount as its own line if you offer one, and the totals update as you type.</p>
<p>There is no signup, no email gate, and no watermark on the finished file. Everything runs locally in your browser, so student names, session rates, and parent contact details stay on your device. Pick a clean invoice style, drop in your logo or headshot, mark any deposit or earlier package payment as already received, and download the result as a PDF for sending or a Word file if you want to keep editing the wording for a particular family later.</p>
HTML,

  'sample_line_items' => [
    ['description' => '60-minute tutoring session', 'rate' => 60, 'quantity' => 4],
    ['description' => 'Package of 10 sessions (prepaid, 10% off)', 'rate' => 540, 'quantity' => 1],
    ['description' => 'Workbook and printed materials', 'rate' => 35, 'quantity' => 1],
    ['description' => 'Online lesson surcharge (per session)', 'rate' => 10, 'quantity' => 4],
    ['description' => 'Late cancellation (less than 24 hours notice)', 'rate' => 60, 'quantity' => 1],
  ],

  'typical_payment_terms_html' => <<<'HTML'
<p>Tutors are often paid weekly at the end of the week, or monthly on the first lesson of the next month, with parents settling by bank transfer, card, or cash at the door. Prepaid packages of 10 sessions are popular and usually carry a small discount, with the full package billed up front before the first lesson runs. Most tutors state a cancellation policy directly on the invoice, with at least 24 hours notice required to avoid a charge, and no-shows or late cancellations billed at the full session rate. Listing the policy in Terms keeps it visible without a separate email each month.</p>
HTML,

  'tax_notes_html' => <<<'HTML'
<p>Set the tax rate as a percent in the Tax field, and switch between exclusive (tax added on top) and inclusive (tax already in your rates) using the dropdown. Educational and tutoring services are sometimes exempt from sales tax, VAT, or GST depending on the jurisdiction, the subject taught, and whether the tutor is a registered education provider, while in other places lessons are fully taxable once revenue passes a registration threshold. The rules also vary for online lessons sold to students in another country. Check your local rules or ask an accountant before assuming your sessions are exempt.</p>
HTML,

  'faqs' => [
    [
      'q' => 'How do I invoice for a package of prepaid lessons?',
      'a' => 'Add the package as a single line item with a quantity of one and the discounted package total as the rate, label it clearly (for example, "Package of 10 sessions, prepaid"), and send the invoice before the first lesson runs. Note the package expiry in the Terms section if sessions need to be used within a set window, and use the Amount Paid field once the parent pays so the balance due shows zero.',
    ],
    [
      'q' => 'How should I handle a late cancellation or a no-show on the invoice?',
      'a' => 'Add the missed session as its own line item at the full session rate, and label it so the reason is clear (for example, "Late cancellation, less than 24 hours notice" or "No-show, March 12"). Stating the cancellation policy in the Terms section of every invoice means the charge is not a surprise. Some tutors waive the first occurrence and apply the charge from the second onward, which is worth writing into the policy if that is your practice.',
    ],
    [
      'q' => 'Can I invoice a group lesson with several students?',
      'a' => 'Yes. Either send one invoice to the organising parent with the full group rate as a single line item, or send each family a separate invoice for their share. The form supports both, and the second approach is cleaner when families pay separately. List the per-student rate and the quantity as one, and note the lesson date and the group size in the description so the family knows what they are paying for.',
    ],
    [
      'q' => 'Should the invoice be in the parent name or the student name?',
      'a' => 'Use the name of the person who is actually paying, which is usually the parent for school-age students and the student for adult learners. Put the paying person in the Bill To field with their address and email. You can mention the student name in Notes or in the line item description (for example, "Math tutoring for Sam, March") so the parent has a clear record of what the invoice covers.',
    ],
    [
      'q' => 'How do I charge differently for online versus in-person lessons?',
      'a' => 'List the base session rate as one line, then add an online surcharge as a separate line when the lesson runs over video. Keeping them as two lines makes the pricing transparent and lets a family see at a glance which lessons were remote. If most of your sessions are online and only some are in person, flip it the other way and apply an in-person travel charge instead, with the same logic.',
    ],
  ],

  'related_slugs' => [
    'freelance',
    'consultant',
    'developer',
    'designer',
    'usa',
    'canada',
    'uk',
  ],

  'related_template_slugs' => [
    'pdf',
    'word',
  ],

  'cta_text' => 'If you want to handle payments, refunds, and track everything, use Argo Books.',

  // Profession page: not country-specific, and concept=null keeps it out
  // of the hreflang country cluster.
  'country' => null,
  'concept' => null,

  'generator_defaults' => [
    'paymentTerms' => 'Due on receipt',
    'lineItems' => [
      ['description' => '60-minute tutoring session', 'quantity' => 4, 'rate' => 60],
      ['description' => 'Package of 10 sessions (prepaid, 10% off)', 'quantity' => 1, 'rate' => 540],
      ['description' => 'Workbook and printed materials', 'quantity' => 1, 'rate' => 35],
      ['description' => 'Online lesson surcharge (per session)', 'quantity' => 4, 'rate' => 10],
    ],
  ],
];
