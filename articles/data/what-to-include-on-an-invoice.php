<?php
// articles/data/what-to-include-on-an-invoice.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'what-to-include-on-an-invoice',

  'h1' => 'What to include on an invoice',

  'meta_title' => 'What to Include on an Invoice | Argo Books',

  'meta_description' => 'What to include on an invoice: the 9 fields every invoice needs, plus extras for products, taxes, regulated countries, and bonus fields for faster pay.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 20,

  'published' => '2026-05-30',

  'updated' => '2026-05-30',

  'reading_time_min' => 8,

  'intro_html' => <<<'HTML'
<p>An invoice is a bill. It also serves as a tax record, and a payment instruction. If a field is missing, the invoice can sit on someone's desk for two weeks waiting for clarification. If a field is wrong, the tax office can refuse to accept it as proof of a sale.</p>
<p>This guide lists every field that belongs on an invoice and explains why each one matters. Start with the 9 fields every invoice needs, then add the extras that apply to your business: product codes if you sell goods, a tax ID if you charge tax, country-specific labels if you operate in the UK, Canada, or the US. By the end you'll know exactly what to put on the page and what to leave off.</p>
HTML,

  'sections' => [

    [
      'h2' => 'The 9 fields every invoice needs',
      'anchor' => 'core-fields',
      'html' => <<<'HTML'
<p>These nine fields show up on almost every legal invoice template in the world. Miss any of them and you've got a problem, either with the client paying late or with the tax office disputing the record.</p>
<ul>
  <li><strong>The word "Invoice" at the top.</strong> One word, clearly visible. It tells the recipient this is a bill that needs paying, not a quote or a receipt. This is convention everywhere, and in Australia GST-registered sellers must show "tax invoice" prominently on documents. The UK doesn't mandate the title wording, but requires several specific fields covered below.</li>
  <li><strong>Invoice number.</strong> A unique identifier you can refer back to. Sequential numbers work fine (1001, 1002, 1003) and so do dated patterns like 2026-001. Whatever system you pick, never repeat a number and never skip one without a reason you can explain.</li>
  <li><strong>Issue date.</strong> The date you sent the invoice. This starts the clock on payment terms, so if your terms say due in 30 days and you issue the invoice on June 1st, the due date is July 1st.</li>
  <li><strong>Due date.</strong> The exact day payment is expected, written as a date not a phrase. "Due June 30 2026" is clearer than "Due in 30 days" because there's no room to argue about when the 30 days started.</li>
  <li><strong>Your business name and address.</strong> The legal name you trade under, plus a physical address. For sole traders this can be a registered business address rather than your home (more on that below).</li>
  <li><strong>Client name and address.</strong> The legal entity you're billing, not just a contact name. "Acme Holdings Pty Ltd" is correct. "Sarah at Acme" isn't, because Sarah can't be invoiced.</li>
  <li><strong>Line items with quantity and rate.</strong> One row per item you're charging for, with a clear description, the quantity, the unit rate, and the line total. Vague entries like "Services rendered" slow down approval. Specific entries like "Logo design (1 round of revisions) x 1 at $850" are more likely to get signed off on the same day.</li>
  <li><strong>Totals.</strong> A subtotal before tax, the tax amount (if any), the total including tax, and the balance due if a deposit has already been paid. Show the math on the page so the client doesn't have to redo it on a calculator.</li>
  <li><strong>Payment instructions.</strong> How and where to pay. Bank account number and sort code or routing number, a payment link, an email address for a card transfer, whatever applies. If the client has to email you back to ask how to pay, you've already lost three days.</li>
</ul>
<p>Those nine fields are the floor, not the ceiling. Every other section in this guide adds fields on top, depending on what you sell, where you operate, and whether you charge tax. But if you nail these nine, the invoice is legally valid in most countries and clear enough for any accounts payable team to process.</p>
<p>Do a quick sanity check before you hit send: read the invoice top to bottom and ask yourself, "Could a stranger pay this without asking me a single question?" If the answer's no, something is missing.</p>
HTML,
    ],

    [
      'h2' => 'What to include if you sell products (vs services)',
      'anchor' => 'products',
      'html' => <<<'HTML'
<p>Service invoices are simpler. You describe the work, list the hours or the fixed fee, and total it up. Product invoices need a few extra fields because physical goods get tracked, stocked, returned, and audited in ways that hours of service generally dont do.</p>
<p><strong>SKU or product code.</strong> Every product you sell should have a unique code that ties the line item back to your inventory system. A SKU like "TSHIRT-BLK-L" tells the client (and your warehouse) exactly which item they bought. Without it, returns and exchanges turn into guessing games about which black t-shirt in which size left the building.</p>
<p><strong>Unit of measure.</strong> Spell out what one unit is. "10 hours" is clear. "10" isn't. Common units: each (for discrete items), hour (for time), kg or lb (for weight), m or ft (for length), litre or gallon (for volume), box, case, or pallet (for bulk packaging). On a product invoice the unit of measure removes any ambiguity about what 10 of something actually means.</p>
<p><strong>Taxable flag per line.</strong> Not every product is taxed at the same rate, and some aren't taxed at all. Basic food in Australia is GST-free. Books in the UK are zero-rated for VAT. Children's clothing in some Canadian provinces sits outside the standard rate. If your invoice mixes taxable and non-taxable lines, mark each one so the totals tie back to the correct tax bucket. The free invoice generator handles this with a tick box on each line.</p>
<p><strong>Description detail.</strong> Service line items can get away with "Project setup and discovery". Product line items need the colour, size, model number, etc., and optionally the serial number. The more an item costs, the more detail the buyer expects.</p>
<p><strong>Shipping and delivery.</strong> If you charge for shipping, list it as its own line with the shipping method, the delivery address if it differs from the billing address, and the shipping cost.</p>
HTML,
    ],

    [
      'h2' => 'What to include if you charge tax',
      'anchor' => 'tax',
      'html' => <<<'HTML'
<p>If you collect sales tax, VAT, GST, or HST, the invoice has to show enough information for both the buyer and the tax office to confirm the tax was charged correctly. Skip these fields and the buyer may not be able to claim the tax back, which makes you the person they call when their return is rejected.</p>
<p><strong>Your tax ID.</strong> The label changes by country: VAT number in the UK and most of Europe, GST/HST number in Canada, ABN in Australia, EIN or sales tax permit number in the US. The number itself is usually 9 to 15 digits and gets printed near your business address. For a country-by-country breakdown of which label goes with which country, see the <a href="/tax-on-invoices-country-guide/">tax on invoices country guide</a>.</p>
<p><strong>Tax rate.</strong> Show the percentage explicitly. "GST 10%" or "VAT 20%" or "Sales tax 8.875%". Don't just print the dollar amount. The buyer's accounts team needs the rate to match it against the goods category and to file their own return.</p>
<p><strong>Tax label per country.</strong> Use the local term, not a generic word. In the UK you say VAT. In Canada you say GST, HST, or PST depending on the province. In Australia you say GST. In the US you say sales tax (and sometimes specify state and county). Mislabelling tax as "VAT" on a US invoice or "sales tax" on a UK invoice signals you don't know the local rules, and clients notice.</p>
<p><strong>Tax point or supply date.</strong> Some jurisdictions, particularly the UK and most of Europe, want a separate "tax point" date if it differs from the invoice issue date. For most small businesses these two dates are the same, but if you invoice in arrears for a service delivered weeks earlier, the tax point is when the service was completed, not when you got around to sending the bill.</p>
<p><strong>Tax inclusive or exclusive.</strong> Make it obvious whether the line item rates already include tax or whether tax gets added on top. The free invoice generator has a dropdown that flips the calculation, but the wording on the page should also say "Subtotal (tax exclusive)" or "Total (tax inclusive)" so there's no confusion when the client reviews it.</p>
HTML,
    ],

    [
      'h2' => 'Country-specific invoice rules',
      'anchor' => 'country-rules',
      'html' => <<<'HTML'
<p>Some countries have very specific rules about what a tax-compliant invoice has to show. Get any of the required fields wrong and the buyer can't reclaim the tax, which puts you on the hook for fixing the document or losing the client.</p>
<p><strong>United Kingdom.</strong> A full UK VAT invoice has to show your VAT registration number, the invoice number (unique and sequential), the issue date, the tax point if different, the supplier's full name and address, the customer's full name and address, a description of the goods or services, the quantity of goods or hours of service per line item, the unit price excluding VAT, the VAT rate, the net (taxable) amount per line, the VAT amount per line, the total excluding VAT, and the total including VAT. Simplified VAT invoices, for supplies of 250 pounds or less including VAT, can leave out some fields, but the VAT number and breakdown are still required. See the <a href="/free-invoice-generator/uk/">UK invoice generator</a> for a form that handles all the VAT fields automatically.</p>
<p><strong>Canada.</strong> The CRA has a three-tier rule based on the invoice total including tax. Under $100, you just need your business or trading name and the invoice date. From $100 to $499.99, you also need your GST/HST registration number, the total amount paid, and the tax rate or amount (with each item flagged as taxable, zero-rated, or exempt). At $500 or more, you also need the recipient's (buyer's) name, the payment terms, and a description of each item supplied. The <a href="/free-invoice-generator/canada/">Canada invoice generator</a> sets the right labels and supports GST, HST, and the various provincial sales taxes.</p>
<p><strong>United States.</strong> There's no single federal invoice format. Rules vary by state, and within states they can vary by city or county for sales tax. Most states want the seller's name and address, the buyer's name, the date, a description of the goods or services, and the sales tax shown on a separate line. If you're sales tax registered, some states require the permit number on resale or exemption certificates, but most states do NOT require it on standard customer invoices. Check your state's revenue department site if you're unsure. The <a href="/free-invoice-generator/usa/">USA invoice generator</a> handles state-level tax rates and labels.</p>
<p><strong>Other countries.</strong> Australia, India, the EU, and most of South America have their own specific rules. The niche pages for each country list the required fields, the tax label, the registration number format, and the threshold at which a full tax invoice becomes mandatory rather than optional. Always check the local requirements, or ask an accountant in that country, before treating a new market as identical to your home country.</p>
HTML,
    ],

    [
      'h2' => 'What you do NOT need to include',
      'anchor' => 'leave-off',
      'html' => <<<'HTML'
<p>Just as important as knowing what goes on, is knowing what stays off. The following items either compromise your security, dilute the message, or just clutter the page.</p>
<p><strong>Bank passwords or login details.</strong> Never. The invoice should show the account number, the sort code or routing number, and the account name, nothing more. If the recipient needs anything beyond that to send the payment, your bank has a problem, not your invoice. Online banking passwords, security questions, and one-time codes never appear on a customer document, ever.</p>
<p><strong>Your home address if you operate as a sole trader.</strong> If you work from a kitchen table, you don't have to print your home address on every invoice you send. In the UK, sole traders aren't registered with Companies House, only limited companies are. So this point applies more to limited-company directors who need to publish a registered office. UK sole traders can simply use a business mailing address (a virtual office or PO box, typically $10 to $20 a month) without changing their tax status. The privacy benefit is real: invoices end up in customer email systems, accountant inboxes, and sometimes data leaks, and you don't want your home address indexable on every one.</p>
<p><strong>Long disclaimers and legalese.</strong> Two lines of payment terms is plenty. "Payment due 30 days from issue date. Late payments incur a 1.5% monthly fee." Anything longer belongs in the master services agreement or the engagement letter, not on the invoice. A 400-word legal block at the bottom signals that you don't trust the client, and it tends to scare off the people who pay fastest.</p>
<p><strong>Marketing copy.</strong> The invoice is a bill, not a brochure. The only acceptable marketing line on an invoice is a one-sentence thank-you or a one-line referral offer like "Refer a friend, get $50 off your next invoice." Anything more turns the invoice into a flyer and makes accounts payable take it less seriously.</p>
<p><strong>Internal codes the client can't use.</strong> Your project tracker ID, your time-sheet code, your internal cost centre. None of those mean anything to the buyer. Keep them in your accounting software, not on the invoice the customer reads.</p>
HTML,
    ],

    [
      'h2' => 'Bonus fields that make life easier',
      'anchor' => 'bonus-fields',
      'html' => <<<'HTML'
<p>Once the required and country-specific fields are in place, a handful of optional fields can shave days off how long the invoice takes to get paid. None of these are mandatory, but the ones below tend to pay for themselves.</p>
<p><strong>Purchase order (PO) number.</strong> If the client gave you a PO before the work started, put the PO number near the top of the invoice. Larger companies route every invoice through a matching system that checks the invoice against the PO, and if your PO number is missing, the invoice gets parked until somebody figures out which PO it belongs to. Adding a single line like "PO: 78421" can move you from a 45-day pay cycle to 15 days.</p>
<p><strong>Project reference or job number.</strong> If you do multiple jobs for the same client, a short project label tells their finance team which budget the invoice should hit. "Project: Q2 brand refresh" or "Job: 14 Smith Street kitchen". This is especially helpful for contractors with multiple jobs running for the same property manager.</p>
<p><strong>Thank-you line.</strong> A single line at the bottom: "Thanks for your business, looking forward to the next project." Costs you nothing, signals you're a professional, and slightly increases the odds the client pays on time because there's a tiny social tug at the end.</p>
<p><strong>Signed-off-by line.</strong> A small line at the bottom for the client to sign or initial, especially useful on multi-stage contractor invoices, custom product builds, or any time approval matters. The client signs the printed copy, scans it, and that becomes the proof that they accepted the work before payment ran.</p>
<p><strong>Late fee statement.</strong> One short sentence stating the late fee rate (commonly 1.5% or 2% per month) and when it applies. This rarely gets used in practice, but the fact that it's on the invoice tends to keep payments inside the agreed window.</p>
<p><strong>Currency code.</strong> If you bill internationally, write the three-letter currency code (USD, GBP, EUR, CAD, AUD) next to every total. The dollar sign alone is ambiguous because Canada, Australia, New Zealand, the US, and several other countries all use it. "$1,200 USD" leaves no room for confusion when the buyer pays.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 0,

  'tool_callout_text' => 'The free invoice generator already includes every field on this list and labels them clearly.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [

    [
      'q' => 'Do I need to put my address on every invoice?',
      'a' => 'Yes, in almost every country. The buyer (and the tax office) needs to be able to identify exactly which business issued the invoice, and a physical address is part of that. Sole traders working from home don\'t need to print their home address: a registered business address from a mail-forwarding service costs $10 to $20 a month and keeps your personal address out of customer inboxes and accounting systems. Whatever address you use, keep it consistent across every invoice, your website, and your business registration.',
    ],

    [
      'q' => 'Do I need to include my tax ID?',
      'a' => 'Only if you\'re registered for tax. Sole traders below the registration threshold (for example, under 90,000 pounds turnover in the UK or under $75,000 AUD in Australia) don\'t have a tax ID to include. Once you cross the threshold and register, you must show your tax ID on every invoice. The label differs by country: VAT number in the UK and Europe, GST/HST in Canada, ABN in Australia, EIN or state sales tax permit number in the US. Get it wrong and the buyer can\'t claim the tax back.',
    ],

    [
      'q' => 'Can I leave the due date off?',
      'a' => 'You can, but you shouldn\'t. If the due date is missing, the buyer can argue payment isn\'t due until they say so, which usually means you get paid significantly later. Even a generic "Net 30" is better than nothing, and a specific date like "Due 30 June 2026" is better still. Invoices with a clear due date get paid noticeably faster on average than invoices that just say "please pay soon", with multiple invoicing studies showing the gap can stretch to a week or two. It\'s the single most useful field for cash flow.',
    ],

    [
      'q' => 'What should the file name be when I save the PDF?',
      'a' => 'Use a pattern the client can search for later. A good format is "Invoice-1001-AcmeHoldings-2026-06-01.pdf". That way both you and the client can find it in any folder six months from now, and your accounting software (or theirs) can sort by date automatically. Avoid generic names like "invoice.pdf" or "invoice-final-v2.pdf", because those vanish into the depths of a downloads folder within a week and become impossible to track during an audit.',
    ],

    [
      'q' => 'Should an invoice include the customer\'s purchase order?',
      'a' => 'If they gave you one, yes, and prominently. Most mid-sized and larger companies won\'t pay an invoice that lacks the PO number, because their accounts payable system matches the invoice against the PO automatically and rejects anything that doesn\'t match. Put "PO: 78421" near the top of the invoice, either next to the invoice number or just below the client address. For small clients who don\'t use POs, you can skip the field entirely.',
    ],

  ],

  'related_niche_slugs' => [
    'usa',
    'canada',
    'uk',
  ],

  'related_article_slugs' => [
    'how-to-invoice-clients',
    'tax-on-invoices-country-guide',
    'invoice-numbering-best-practices',
  ],
];
