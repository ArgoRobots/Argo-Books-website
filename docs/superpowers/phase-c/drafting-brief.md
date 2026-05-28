# Phase C niche-page drafting brief

Fill in the values in the <FILL> tags below, paste the whole thing into a
fresh LLM session (Claude or GPT-4), and use the output as the FIRST draft
of the niche data file. Every draft must then pass the hand-edit checklist
(see `hand-edit-checklist.md`) before committing.

## Inputs (you fill these in before sending to the LLM)

- SLUG:           <FILL: e.g. "freelance">
- H1:             <FILL: e.g. "Free Freelance Invoice Generator">
- AUDIENCE:       <FILL: "freelance writers, designers, consultants, and
                   other one-person businesses billing clients directly">
- COUNTRY:        <FILL: ISO code if this is a country page, else null>
- TAX_LABEL:      <FILL: if country page, the local label (e.g. "GST/HST"),
                   else "Tax">
- TYPICAL_TERMS:  <FILL: "Net 30 for established clients, 50% upfront for
                   new clients, late fee 1.5% per month">
- RELATED_SLUGS:  <FILL: paste exact array from the cross-link graph>

## Output spec

Produce a PHP `return [...];` block matching the schema in
`niches/data/_template.php`. Do NOT include `<?php` tags or comments,
just the array literal. I will paste it into the file myself.

## Field constraints

- `slug`: exact match of SLUG.
- `h1`: exact H1.
- `meta_title`: 50-60 characters total INCLUDING " | Argo Books" suffix.
- `meta_description`: 140-160 characters. Lead with value, not the brand.
  Mention the niche by name. End with a verb-led action ("Download in
  seconds.", "Pick a template, fill it in, download.")
- `intro_html`: two paragraphs, ~150 words total. First paragraph explains
  what the tool does specifically for AUDIENCE. Second paragraph explains
  what to expect from the workflow (no signup, browser-local edits, PDF
  or Word download). Wrap each paragraph in <p>...</p>. No em dashes.
- `sample_line_items`: 4 to 5 entries. Each `description` is something
  this AUDIENCE actually bills for. Rates and quantities are realistic
  (e.g., a tutor's rate is $35-80/hour and they bill 4-20 hours/month,
  not 200 hours at $5).
- `typical_payment_terms_html`: one paragraph, ~80-100 words. Cover the
  common terms for THIS niche, including any niche-specific patterns
  (contractors use progress payments, freelancers use 50% upfront,
  cleaning uses recurring monthly billing, etc.). Wrap in <p>...</p>.
- `tax_notes_html`: one paragraph, ~80-100 words. If COUNTRY is set,
  cover that country's tax structure using TAX_LABEL. If COUNTRY is
  null, give generic guidance about setting the tax rate in the
  generator and recommend the user check local rules. Never give
  legal advice. Wrap in <p>...</p>.
- `faqs`: exactly 5 entries. Questions are natural-search phrasing
  ("How do I", "When should I", "What is", "Do I need"). Answers are
  1-3 sentences each, plain text, NO HTML tags. Mix of practical
  workflow questions (logo, format, currency) and niche-specific
  questions (deposit structure, parts vs labor, tutoring sessions,
  recurring service billing, etc.).
- `related_slugs`: exact RELATED_SLUGS array.
- `related_template_slugs`: 1-3 slugs from this list that suit the
  niche best: pdf, word, excel, google-docs, google-sheets,
  classic-pdf, modern-pdf, formal-pdf, elegant-pdf, ribbon-pdf,
  classic-word, modern-word, etc. Default for most professions is
  ['pdf', 'word']. Pick relevant style+format for niche-fit (e.g.
  designers -> 'modern-pdf', law-adjacent consultants -> 'classic-pdf').
- `cta_text`: keep the standard "If you want to handle payments,
  refunds, and track everything, use Argo Books." (Do not rewrite.)
- `country`: ISO code if country page, else null.
- `concept`: 'invoice-generator' for country pages, null for
  profession pages.
- `generator_defaults`: an array with `country` (if applicable),
  `paymentTerms` (the dominant term for the niche), and `lineItems`
  (3 entries matching the niche). Use real state-shape keys: see
  `niches/data/_template.php` line 112 for the example.

## Voice rules (HARD constraints)

- Plain English. The audience is small-business owners, not MBAs.
- No em dashes (the "—" character). Use commas, colons, or periods.
  A regular hyphen is NOT a substitute in prose.
- No emojis.
- No "in today's fast-paced world", "when it comes to", "look no
  further", "robust", "streamline", "leverage", "delve", "navigate
  the complexities", "tapestry", "realm".
- No exhaustive parenthetical disclaimers.
- No name-dropping "Argo Books" inside intro_html or FAQ answers.
  The CTA is the only place the brand name appears.
- Never make the generator or Argo Books sound buggy. Avoid words
  like crash, broken, error, lost, corrupted.
- Never use the word "reconciliation" or "reconcile". Use
  "matching", "checking", "balancing", or "comparing".
- Do not give legal or tax advice. Phrasing like "consult your
  accountant" or "check your local rules" is the right tone.

## Worked example 1: profession page (consultant)

INPUTS:
- SLUG: consultant
- H1: Free Consultant Invoice Generator
- AUDIENCE: independent consultants billing clients by the hour or by
  retainer, including management, IT, marketing, and HR consultants
- COUNTRY: null
- TAX_LABEL: Tax
- TYPICAL_TERMS: Net 15 to Net 30, occasional retainers paid monthly
  in advance
- RELATED_SLUGS: [freelance, developer, designer, tutor, usa, canada, uk]

EXPECTED OUTPUT (shape only, the actual content is the LLM's job):

```php
[
  'slug' => 'consultant',
  'h1' => 'Free Consultant Invoice Generator',
  'meta_title' => 'Free Consultant Invoice Generator | Argo Books',
  'meta_description' => 'Free invoice generator for consultants. Hourly...
    ...',
  'intro_html' => '<p>...two paragraphs of real consultant copy...</p>',
  ...
]
```

## Worked example 2: country page (canada)

INPUTS:
- SLUG: canada
- H1: Free Invoice Generator for Canada
- AUDIENCE: Canadian freelancers, contractors, and small business owners
  billing clients in CAD
- COUNTRY: CA
- TAX_LABEL: GST/HST
- TYPICAL_TERMS: Net 30, late fee 2% per month, payments via Interac
  e-Transfer or bank transfer
- RELATED_SLUGS: [usa, uk, australia, india, freelance, contractor,
  consultant, photographer]

EXPECTED OUTPUT (shape only):

```php
[
  'slug' => 'canada',
  'h1' => 'Free Invoice Generator for Canada',
  'meta_title' => 'Free Invoice Generator for Canada | Argo Books',
  'meta_description' => 'Free invoice generator built for Canadian...',
  'intro_html' => '<p>...two paragraphs covering CAD currency, the
    GST/HST/PST landscape at a high level, and what a Canadian invoice
    typically shows...</p>',
  ...
  'tax_notes_html' => '<p>Canada combines a federal GST (5%) with...</p>',
  ...
  'country' => 'CA',
  'concept' => 'invoice-generator',
  'generator_defaults' => [
    'country' => 'CA',
    'paymentTerms' => 'Net 30',
    'lineItems' => [
      ['description' => 'Consulting (per hour)', 'quantity' => 8, 'rate' => 125],
      ...
    ],
  ],
]
```

## How to use the output

1. Paste the inputs above (substituting <FILL> values).
2. Run the LLM.
3. Open `niches/data/{slug}.php` and paste a standard wrapper:

```php
<?php
// niches/data/{slug}.php
// See niches/data/_template.php for schema.

return [
  // ... LLM output goes here ...
];
```

4. Apply the hand-edit checklist line by line.
5. Visit the page locally to confirm it renders, the FAQ schema is
   present, and the hreflang block appears (country pages only).
6. Commit (only when user explicitly approves).
