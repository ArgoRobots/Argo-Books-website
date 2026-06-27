<?php
// articles/data/tax-on-invoices-country-guide.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'tax-on-invoices-country-guide',

  'h1' => 'Tax on invoices: country guide',

  'meta_title' => 'Tax on Invoices by Country: a Quick Guide | Argo Books',

  'meta_description' => 'Tax on invoices by country: how sales tax, GST, HST, and VAT work in the US, Canada, UK, Australia, and India, and how to set the rate in the generator.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'invoicing',
  'hub_weight' => 60,

  'published' => '2026-05-30',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Tax on invoices is one of the few parts of running a small business where the rules really do change every time you cross a border. The country you're billing from sets the label and the rate. The country your customer is in can change which rate applies. The product or service you're selling can shift the rate again, and the threshold for whether you need to register at all is different in every market.</p>
<p>This guide is a quick orientation to charging tax on an invoice in the five markets the free invoice generator on this site supports as a one-click country pick: the United States, Canada, the United Kingdom, Australia, and India, plus how to handle every other country. None of it is legal or tax advice. Use it to set up a clean invoice, then confirm the rate with a local accountant before you send one with tax on it.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What this guide covers (and what it does not)',
      'anchor' => 'what-this-covers',
      'html' => <<<'HTML'
<p>When you pick one of the five built-in countries at the top of the generator, it relabels the tax row (sales tax, GST, or VAT) and sets the currency for you. The per-country sections below give the standard rate, the registration threshold, and the main fields each invoice has to include.</p>
<p>For every other country, the generator still works. You just set the tax row manually instead of having a built-in default. Pick the currency, type the rate, choose whether the rate is exclusive or inclusive of the line totals, and the math runs the same way. That's enough for invoicing a client in Germany, Singapore, Brazil, the UAE, South Africa, or anywhere else, as long as you know what rate you're supposed to charge.</p>
<p>This guide is not tax advice for your specific situation. Rates change, thresholds change, and categories get reclassified. A photographer in California has different sales-tax exposure than a software developer in California, and both can change again if the customer is across a state line. The rates and thresholds below were accurate as of writing, but check the current rules with a local accountant before you rely on any of them. Use this to set up an invoice with the right labels and fields; confirm the rate itself with someone who knows your books and your jurisdiction.</p>
HTML,
    ],

    [
      'h2' => 'United States: sales tax',
      'anchor' => 'united-states',
      'html' => <<<'HTML'
<p>The United States doesn't have a federal sales tax. Instead, 45 of the 50 states run their own sales tax, and many cities and counties stack a local rate on top. That's why a coffee in one town can be taxed at 6% and the same coffee twenty miles down the road taxed at 9.25%. The rate is set at the state, county, and sometimes city level, all rolled into a single number on the invoice.</p>
<p>Five states have no statewide sales tax at all: Alaska, Delaware, Montana, New Hampshire, and Oregon. Some Alaskan cities and boroughs run a local sales tax even though the state does not. Montana does the same on a much smaller scale: certain resort and tourism districts can collect a local sales tax, though most of the state has none. In Delaware, New Hampshire, and Oregon, you typically don't charge sales tax on a standard invoice to a customer in those states.</p>
<p>Nexus is the rule that decides whether you have to collect sales tax in a state where you don't have a physical office. Since the 2018 Wayfair decision (the US Supreme Court ruling that let states require tax collection based on sales volume, not just physical presence), many states use an economic nexus threshold of around 100,000 US dollars in sales into that state per year, though the exact threshold varies. Some states still use a combined "100,000 dollars or 200 transactions" test, but a growing list (including Alaska, Illinois, Kentucky, Utah, and others) has dropped the 200-transactions prong since 2024 to avoid catching small sellers who happen to make a lot of low-value sales. Once you cross the applicable threshold for a given state, you have to register, collect, and file there.</p>
<p>Services versus products is the other big distinction. Most states tax tangible goods by default and exempt most professional services, though some states tax specific service categories like data processing, repair labor, or telecommunications. Software-as-a-service is taxable in some states and exempt in others. A graphic designer billing a Texas client may owe sales tax on the design files but not on the consulting hours, depending on how the work is described on the invoice. This is exactly the kind of detail to confirm with a local accountant before your first invoice in a new state.</p>
HTML,
    ],

    [
      'h2' => 'Canada: GST, HST, PST, and QST',
      'anchor' => 'canada',
      'html' => <<<'HTML'
<p>Canada layers a federal Goods and Services Tax on top of provincial rules, so the rate on a Canadian invoice depends on where the customer is located, not where the seller is. The federal GST is 5%. Some provinces harmonize federal and provincial tax into a single Harmonized Sales Tax (HST). Other provinces charge a separate Provincial Sales Tax (PST) alongside the 5% GST. Quebec runs its own Quebec Sales Tax (QST).</p>
<p>As of writing, the standard rates by province are:</p>
<ul>
<li><strong>Alberta, Northwest Territories, Nunavut, Yukon.</strong> 5% GST only.</li>
<li><strong>Ontario.</strong> 13% HST.</li>
<li><strong>New Brunswick, Newfoundland and Labrador, Prince Edward Island.</strong> 15% HST.</li>
<li><strong>Nova Scotia.</strong> 14% HST (lowered from 15% on 1 April 2025).</li>
<li><strong>British Columbia.</strong> 5% GST plus 7% PST.</li>
<li><strong>Saskatchewan.</strong> 5% GST plus 6% PST.</li>
<li><strong>Manitoba.</strong> 5% GST plus 7% PST.</li>
<li><strong>Quebec.</strong> 5% GST plus 9.975% QST.</li>
</ul>
<p>Registration becomes mandatory once your worldwide taxable supplies cross 30,000 Canadian dollars, measured either in a single calendar quarter or as a running total across the last four quarters. Below that, you can stay a small supplier and skip charging GST/HST, though voluntary registration is allowed if you want to claim input tax credits on your business expenses.</p>
<p>If you're registered, the Canada Revenue Agency requires you to show your GST/HST number on any invoice over 30 dollars that includes tax. Place of supply rules determine which province's rate applies on each invoice, and they aren't always intuitive for services or for clients with offices in multiple provinces. The country page for <a href="/free-invoice-generator/canada/">Canadian invoices</a> covers the most common cases, but ask your accountant whenever a new province enters the picture.</p>
HTML,
    ],

    [
      'h2' => 'United Kingdom: VAT',
      'anchor' => 'united-kingdom',
      'html' => <<<'HTML'
<p>The UK uses Value Added Tax (VAT) as its single consumption tax. As of writing, the standard rate is 20% and covers most goods and services. A reduced rate of 5% applies to a narrow list of categories, including domestic fuel and power, energy-saving materials in some cases, and child car seats. A zero rate of 0% applies to most food, books, newspapers, and children's clothes, which is different from being exempt because zero-rated sales still count toward your taxable turnover.</p>
<p>The VAT registration threshold is 90,000 pounds in taxable turnover over a rolling 12-month period, which has been the level since April 2024. Once you cross that line, you have to register within 30 days and start charging VAT on every taxable invoice going forward. You can also register voluntarily below the threshold, which can make sense when most of your clients are VAT-registered themselves and can reclaim the VAT you charge.</p>
<p>A UK VAT invoice has to show <a href="/what-to-include-on-an-invoice/">specific fields</a>. As of writing, HMRC expects all of the following on a full VAT invoice:</p>
<ul>
<li>A unique <a href="/invoice-numbering-best-practices/">sequential invoice number</a>.</li>
<li>Your business name, address, and VAT registration number.</li>
<li>The customer's name and address.</li>
<li>The invoice date and the tax point (date of supply) if different.</li>
<li>A description of each line, with the quantity, the rate, and the line total.</li>
<li>The VAT rate applied to each line.</li>
<li>The total excluding VAT, the total VAT amount, and the total including VAT.</li>
</ul>
<p>Making Tax Digital for VAT means VAT-registered businesses also need to keep digital records and file VAT returns through compatible software. The free generator produces a clean VAT invoice that includes the fields above, but it doesn't file your returns. Pair it with bookkeeping software or a full accounting app, and confirm the rate on each line with an accountant if any of your services sit near a reduced or zero band. The country page for <a href="/free-invoice-generator/uk/">UK invoices</a> covers the layout details.</p>
HTML,
    ],

    [
      'h2' => 'Australia: GST',
      'anchor' => 'australia',
      'html' => <<<'HTML'
<p>Australia uses a single flat Goods and Services Tax of 10%. There are no state-level sales taxes to layer on top, and there's no reduced rate for most categories, which makes Australian invoicing one of the simpler cases on this list. A handful of supplies are GST-free, including most basic food, some health and education services, and exports, but the default for a typical professional services invoice is 10% flat.</p>
<p>The registration threshold is 75,000 Australian dollars in annual turnover, or 150,000 for non-profits. Taxi and ride-share drivers must register regardless of turnover. Once you cross the threshold, you have to register for GST within 21 days and start including GST on your invoices from the effective registration date.</p>
<p>Your Australian Business Number (ABN) is the single most important field on an Australian invoice. The ABN identifies you to the Australian Taxation Office, and it has to appear on every invoice you send. If you leave it off, the customer is required to withhold Pay As You Go (PAYG) at 47% of the invoice amount, which isn't a position you want to put a client in.</p>
<p>For any invoice of 82.50 Australian dollars or more (including GST), the document has to qualify as a tax invoice. The ATO requires that a tax invoice clearly states "Tax invoice" somewhere on the document, shows the seller's name and ABN, shows the date, lists what's being sold with quantities and prices, and either shows the GST amount or shows that the total includes GST. For sales of 1,000 AUD or more, the tax invoice has to show either the buyer's identity (business name) or the buyer's ABN. Either one is acceptable; you don't need both.</p>
<p>The country page for <a href="/free-invoice-generator/australia/">Australian invoices</a> covers ABN placement and the tax invoice label. Ask your accountant if you're unsure whether something you sell is GST-free or not.</p>
HTML,
    ],

    [
      'h2' => 'India: GST and IGST',
      'anchor' => 'india',
      'html' => <<<'HTML'
<p>India runs GST as a destination-based tax, which means the rate on a given invoice depends on whether the sale is inside your home state or across state lines. If the customer is in the same state as you, the sale is intra-state and you split GST into two equal halves: Central GST (CGST) goes to the central government and State GST (SGST) goes to your state government. If the customer is in a different state, the sale is inter-state and you charge Integrated GST (IGST) at the combined rate instead.</p>
<p>The rate itself is set by the GST Council. Following the GST 2.0 reform that took effect on September 22, 2025, the structure was simplified from four main slabs to three:</p>
<ul>
<li><strong>5%.</strong> Essentials, basic food, some agricultural items, and common services like restaurants without input tax credit.</li>
<li><strong>18%.</strong> The standard rate for most professional services, software, and most goods. This is the one that applies to a typical consulting, software, or design invoice.</li>
<li><strong>40%.</strong> Luxury and sin goods, including tobacco, certain large vehicles, and soft drinks. This slab replaced the old 28% band.</li>
</ul>
<p>The previous 12% and 28% slabs were abolished in the reform. Items that used to sit at 12% moved to either 5 or 18%, and items that used to sit at 28% moved to either 18 or 40%. If you remember the old four-slab structure, the practical effect is that most everyday goods and services are now either at 5% or 18%, with a much smaller list at 40%.</p>
<p>For a consulting invoice taxed at 18% within the same state, you charge 9% CGST plus 9% SGST. For the same invoice to a client in another state, you charge 18% IGST in a single line. The total is the same, but the breakdown is different, and the tax flows to a different place behind the scenes.</p>
<p>Registration becomes mandatory once your aggregate annual turnover from services crosses 20 lakh rupees, or 10 lakh in special category states like the North-Eastern states. Every GST invoice must show your 15-character GSTIN, and Harmonized System of Nomenclature (HSN) codes or Services Accounting Code (SAC) codes have to appear on each line. The HSN requirement scales with turnover: businesses above 5 crore rupees in turnover use 6-digit HSN, while smaller businesses can use 4-digit codes or none at all depending on the current notification. E-invoicing through the Invoice Registration Portal is mandatory once your turnover crosses the relevant threshold (currently 5 crore rupees of aggregate annual turnover, in effect since August 2023, though the threshold has been progressively lowered over the years). The country page for <a href="/free-invoice-generator/india/">Indian invoices</a> covers GSTIN placement and the SAC field. Talk to an accountant before you assume a slab for your services.</p>
HTML,
    ],

    [
      'h2' => 'Set your tax rate in the generator',
      'anchor' => 'set-tax-rate',
      'html' => <<<'HTML'
<p>The free invoice generator on this site adapts to the country you pick at the top of the form. The tax row relabels itself based on the country selection, so the invoice that lands in the client's inbox uses the right vocabulary for the place you're billing from.</p>
<ul>
<li><strong>United States.</strong> The tax row is labelled "Sales Tax". You enter the combined state plus local rate as a percentage. The <a href="/free-invoice-generator/usa/">US generator</a> defaults the currency to USD.</li>
<li><strong>Canada.</strong> The tax row labels itself "GST/HST" so the right acronym appears. You enter the combined rate for the customer's province (5% for Alberta, 13% for Ontario, 15% for the Atlantic HST provinces, and so on).</li>
<li><strong>United Kingdom.</strong> The tax row is labelled "VAT". You enter 20% for standard rate, 5% for reduced rate, or 0% for zero-rated lines.</li>
<li><strong>Australia.</strong> The tax row is labelled "GST" and you enter 10%. The form also leaves room for your ABN in the business details so it shows on every invoice.</li>
<li><strong>India.</strong> The tax row is labelled "GST" and you can enter the combined rate (for example 18% for the standard slab) or break it into CGST and SGST lines for intra-state sales.</li>
</ul>
<p>Two more controls matter, and they sit right next to the tax field. The exclusive versus inclusive toggle decides whether the tax is added on top of your line totals or pulled out of them. Exclusive is the default, and it's what most B2B invoices in the US, Canada, UK, and India use: a line of 1,000 dollars at 10% tax becomes a 1,000 dollar subtotal plus 100 dollars tax for a 1,100 dollar total. Inclusive flips the math: the 1,000 dollar line already includes the tax, so the subtotal works out to about 909 dollars plus about 91 dollars tax for a 1,000 dollar total. Inclusive is common for retail receipts in Australia and the UK where the displayed price already includes tax.</p>
<p>Set the country, set the rate, pick exclusive or inclusive, and the totals update as you type. No formulas to write, no separate spreadsheet to keep.</p>
HTML,
    ],

    [
      'h2' => 'Everywhere else: use a manual rate',
      'anchor' => 'everywhere-else',
      'html' => <<<'HTML'
<p>The five countries above are the ones the generator handles with a one-click selection, but the manual tax row works for any country in the world. Pick the currency that fits your customer, type the rate into the tax field, choose exclusive or inclusive, and the invoice math runs exactly the same way. That covers billing a client in Germany at 19% VAT, in Singapore at 9% GST, in the UAE at 5% VAT, in South Africa at 15% VAT, in New Zealand at 15% GST, or anywhere else you happen to be invoicing.</p>
<p>A few quick orientation points for common other markets, as of writing:</p>
<ul>
<li><strong>European Union.</strong> Each member state runs its own VAT. Standard rates range from 17% in Luxembourg to 27% in Hungary, with most countries sitting between 19% and 25%. Cross-border B2B services within the EU often use the reverse charge mechanism, where the customer accounts for the VAT on their own return and you invoice without VAT but with a reverse-charge note.</li>
<li><strong>Singapore.</strong> GST is 9% as of 2024.</li>
<li><strong>New Zealand.</strong> GST is a flat 15% and the registration threshold is 60,000 New Zealand dollars.</li>
<li><strong>United Arab Emirates.</strong> VAT is 5%.</li>
<li><strong>South Africa.</strong> VAT is 15%. The compulsory registration threshold rose to 2.3 million rand in any 12-month period as of April 1 2026 (up from 1 million), with voluntary registration available from 120,000 rand.</li>
</ul>
<p>None of those numbers should be taken as the final word for your specific situation. Rates and thresholds move. Categories get reclassified. The reverse-charge rules inside the EU are different for digital services versus physical goods versus B2B services versus B2C sales, and an accountant can save you a real headache by walking through the mapping for your business. When in doubt, ask a local accountant before you send the first invoice with tax on it.</p>
<p>One thing worth repeating: the generator produces a clean invoice document, but it doesn't file your returns. It doesn't register you for tax. It doesn't work out which slab a particular product falls into. Those are jobs for your accountant and your local tax authority. The generator handles the layout, the labels, and the math, so the part that does sit on your screen looks right.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 6,

  'tool_callout_text' => 'The free generator labels the tax row by country and supports exclusive or inclusive rates.',
  'tool_callout_cta' => 'Open the invoice generator',

  'faqs' => [
    [
      'q' => 'Is the tool a substitute for an accountant?',
      'a' => 'No. The free invoice generator is a document tool. It produces a clean PDF or Word invoice with the right labels, the right currency symbol, and the math worked out for you. It doesn\'t register you for tax, file your returns, decide which rate applies to a specific service, or tell you whether a particular sale is exempt. Those are jobs for a local accountant who knows your books and your jurisdiction. Use the generator to handle the layout and the math, then check with an accountant for the rate, the threshold, and the filing schedule that applies to your business.',
    ],
    [
      'q' => 'Can I show two tax rates on one invoice?',
      'a' => 'Yes. The simplest way is to break the invoice into separate line items and apply the right rate to each line, then let the subtotal and total math work itself out at the bottom. This is common in India for intra-state sales where you show CGST and SGST as two lines on the same invoice, or in the UK if you have a mix of standard-rated and zero-rated items in the same order. If you aren\'t sure whether two rates apply to the same job, ask a local accountant, because the answer often depends on how the work is described and bundled.',
    ],
    [
      'q' => 'What is reverse charge?',
      'a' => 'Reverse charge is a VAT or GST rule where the customer accounts for the tax on their own return instead of the seller charging it on the invoice. It comes up most often on cross-border B2B sales inside the EU and on some inter-state services in other markets. In practice, you invoice the customer without VAT or GST on the line, and you add a short note on the invoice stating that reverse charge applies and citing the relevant rule. The customer then reports the tax on both sides of their own return so it nets to zero. The mechanics vary by country, so check with an accountant before applying reverse charge.',
    ],
    [
      'q' => 'Do I include tax on a US invoice for a Canadian client?',
      'a' => 'Usually no. Sales tax in the United States is collected on sales to US customers in states where you have nexus. A sale to a customer in Canada is an export from the US perspective, so US sales tax typically doesn\'t apply. On the Canadian side, your client may have to self-assess GST on the import, depending on what you\'re selling and whether you have any presence in Canada. The reverse is also true: a Canadian business invoicing a US client typically doesn\'t charge GST/HST on exported services, though there are exceptions. Check with an accountant on both sides of the border before assuming an answer.',
    ],
    [
      'q' => 'What is exclusive vs inclusive tax?',
      'a' => 'Exclusive means the tax is added on top of your line totals. A line of 1,000 at 10% tax becomes a 1,000 subtotal plus 100 tax for a 1,100 total. Inclusive means the tax is already baked into the line price, so the same 1,000 line at 10% inclusive becomes about 909 subtotal plus about 91 tax for a 1,000 total. Most B2B invoices in the US, Canada, UK, and India use exclusive pricing so the customer sees the tax listed as its own line. Retail receipts in Australia and the UK often use inclusive pricing because shoppers are used to seeing the final price on the shelf.',
    ],
  ],

  'related_niche_slugs' => [
    'usa',
    'canada',
    'uk',
    'australia',
    'india',
  ],

  'related_article_slugs' => [
    'what-to-include-on-an-invoice',
    'how-to-invoice-clients',
  ],
];
