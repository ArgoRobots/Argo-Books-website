<?php
// articles/data/how-long-to-keep-business-receipts.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'how-long-to-keep-business-receipts',

  'h1' => 'How long to keep business receipts and records',

  'meta_title' => 'How Long to Keep Business Receipts | Argo Books',

  'meta_description' => "How long to keep business receipts and records in the US, Canada, UK, and Australia, what to save, and why scanning beats a shoebox that's fading.",

  'schema_type' => 'Article',

  'category' => 'receipts-expenses',
  'hub_weight' => 40,

  'published' => '2026-07-21',

  'updated' => '2026-07-21',

  'reading_time_min' => 8,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>Every business receipt is proof of something: money you spent, tax you paid, an expense you're claiming against your income. If the tax office ever asks you to back up a number on your return, the receipt is the answer. Throw it out too soon, and you're left arguing from memory. Keep everything forever in a drawer, and you end up with a box of curled-up paper you can't read.</p>
<p>This guide covers how long you actually have to keep business records, broken down by country, plus what to keep, whether digital copies count, and the smartest way to store it all so it's still readable years from now. The short version: most tax authorities are fine with clear digital copies, and scanning as you go beats a shoebox every time. Here's the detail.</p>
HTML,

  'sections' => [

    [
      'h2' => 'How long you have to keep records, by country',
      'anchor' => 'how-long-by-country',
      'html' => <<<'HTML'
<p>The rule is roughly the same everywhere: keep your records long enough that the tax office can still check the return they relate to. The exact number of years is what changes. Here are the general rules for the four most common places our readers file.</p>
<div class="comparison-table-wrapper">
<table>
<thead>
<tr>
<th>Country / tax office</th>
<th>General rule</th>
<th>When it runs longer</th>
</tr>
</thead>
<tbody>
<tr>
<td><strong>United States (IRS)</strong></td>
<td>3 years from the date you filed</td>
<td>6 years if you under-reported income by more than 25%, 7 years for a bad-debt or worthless-securities claim, and no limit at all if you never filed or filed a fraudulent return</td>
</tr>
<tr>
<td><strong>Canada (CRA)</strong></td>
<td>6 years from the end of the tax year the records relate to</td>
<td>Longer if you file late (the six years starts from when you filed) or if the CRA asks you in writing to hold them</td>
</tr>
<tr>
<td><strong>United Kingdom (HMRC)</strong></td>
<td>Self-employed and landlords keep records at least 5 years after the 31 January filing deadline; limited companies keep them 6 years from the end of the accounting period</td>
<td>Longer if you filed late, or if the records cover a transaction that spans several years (like an asset you're still depreciating)</td>
</tr>
<tr>
<td><strong>Australia (ATO)</strong></td>
<td>5 years from when you lodged the return the records support</td>
<td>Longer for records tied to an asset you'll later claim a capital gain or loss on: keep those 5 years past the year you sell the asset</td>
</tr>
</tbody>
</table>
</div>
{{illustration:calendar-due}}
<p>A safe rule of thumb that covers all four: keep everything for at least seven years. That's longer than any of the standard windows above, it costs almost nothing once your records are digital, and it means you're never the person hunting for a receipt you shredded eleven months too early. These are the general rules, and they do have exceptions, so check with an accountant for your own situation before you throw anything out.</p>
HTML,
    ],

    [
      'h2' => 'What you actually need to keep',
      'anchor' => 'what-to-keep',
      'html' => <<<'HTML'
<p>"Records" is broader than the till receipts in your wallet. If a number appears on your tax return, you want the paper trail behind it. Here's the list that covers most small businesses and sole traders:</p>
<ul>
<li><strong>Receipts for things you bought.</strong> The purchase receipt for every business expense you claim: tools, software, stock, fuel, a coffee with a client. This is the evidence that the expense was real and business-related. If you're claiming it, keep it.</li>
<li><strong>Invoices you received from suppliers.</strong> A supplier bill is the fuller version of a receipt, and it's what backs up a larger purchase. Keep the invoice and any proof you paid it.</li>
<li><strong>Invoices you sent to customers.</strong> These are your proof of income. Every invoice you issued, whether it was paid, partly paid, or written off, belongs in your records. The tax office matches your reported income against these.</li>
<li><strong>Bank and credit card statements.</strong> Your statements tie the receipts to real money moving in and out. They're also your backup if a paper receipt goes missing, because the line on the statement still shows the date and amount.</li>
<li><strong>Tax returns and their working papers.</strong> Keep a copy of each return you filed and the numbers you built it from. If a later year's figures depend on an earlier year (a loss carried forward, an asset you're depreciating), you'll need the old return to explain the new one.</li>
<li><strong>Payroll records, if you have staff.</strong> Wages, tax withheld, superannuation or pension contributions, and any benefits. These often have their own, sometimes longer, retention rules, so treat them as their own pile.</li>
</ul>
<p>You don't need to keep marketing junk mail, duplicate copies, or the packaging. The test is simple: could this document help you explain a figure on your return? If yes, keep it. If no, let it go.</p>
HTML,
    ],

    [
      'h2' => 'Do digital copies count, or do you need the paper?',
      'anchor' => 'paper-vs-digital',
      'html' => <<<'HTML'
<p>Here's the good news that saves you a filing cabinet: the four tax offices above all accept clear digital copies of your records. The IRS, CRA, HMRC, and ATO each have written guidance saying an electronic copy is fine, as long as it's complete, readable, and a true representation of the original. You do not have to keep the paper once you have a good scan.</p>
<p>The practical conditions are common sense:</p>
<ul>
<li><strong>The whole document has to be legible.</strong> A blurry photo with the total cut off isn't a record, it's a photo. The vendor, date, amount, and tax all need to be readable.</li>
<li><strong>It has to be a faithful copy.</strong> You're capturing the original, not retyping a summary. A scan or a clear photo of the actual receipt is what they want.</li>
<li><strong>You have to be able to produce it on request.</strong> The copy needs to be stored somewhere you can search it and pull it up if the tax office asks. A photo lost in a camera roll of 9,000 pictures technically exists, but you'll never find it under pressure.</li>
</ul>
<p>The practical implication is big. Once you know a good digital copy is enough, there's no reason to babysit boxes of paper. You can scan a receipt the moment it lands in your hand, then recycle the paper. Your records get smaller, searchable, and backed up, and you stop losing the one receipt you needed. The only thing that matters is that the scan is clear and you can find it later.</p>
HTML,
    ],

    [
      'h2' => 'Why the shoebox fails: the faded-thermal problem',
      'anchor' => 'faded-thermal-problem',
      'html' => <<<'HTML'
<p>Most receipts are printed on thermal paper. That's the shiny, slightly waxy paper that comes out of card terminals, fuel pumps, and shop tills. Thermal paper doesn't use ink. The printer heats the paper and the coating darkens to form the text. That's why it's fast and cheap, and it's also why it doesn't last.</p>
<p>Heat, light, and time all fade thermal receipts. Leave one in a hot car, a sunny windowsill, or just a drawer for a year or two, and the print goes grey, then vanishes. Plenty of people have opened their tax-time shoebox to find a stack of blank slips where the receipts used to be. A blank receipt proves nothing. As far as the tax office is concerned, an expense you can't evidence is an expense you didn't have, which means you could lose the deduction and pay more tax than you owed.</p>
{{illustration:receipt-scan}}
<p>Scanning fixes this at the root. The moment you capture a clear image, the fade stops mattering. The paper can go blank in the drawer, or go in the recycling that same day, because the readable copy is already saved. A receipt scanned in July of this year looks exactly the same when you open it seven years from now. That's the whole case for scanning over stacking: the shoebox is a slow-motion way to lose the very records you're keeping, and a digital copy simply doesn't fade.</p>
<p>The habit that works is scan-as-you-go. Capture each receipt when you get it, while it's still crisp and while you still remember what it was for, instead of facing a shoebox of curled, greying paper the week before your filing deadline.</p>
HTML,
    ],

    [
      'h2' => 'How to store records so you can actually find them',
      'anchor' => 'how-to-store',
      'html' => <<<'HTML'
<p>Keeping records and being able to use them are two different things. A drawer of paper technically satisfies the rule, right up until you need one specific receipt from three years ago and have to empty the drawer onto the floor. Storage worth having does three jobs: it's readable, it's searchable, and it's backed up.</p>
<ul>
<li><strong>Capture it digitally.</strong> Scan or photograph each receipt so it can't fade. This is step one and it solves the thermal-paper problem for good.</li>
<li><strong>Store the copy with the expense it belongs to.</strong> A folder of loose images is only half the job. The receipt is far more useful sitting attached to the expense record it backs up, so the amount, date, vendor, and image all live in one place. When a question comes up, you open the expense and the proof is right there.</li>
<li><strong>Keep a backup.</strong> One copy is a single point of failure. Whether that's a second drive, an export you tuck away, or software that keeps your data safe, make sure the records survive a dead laptop.</li>
<li><strong>Keep it in order by year.</strong> Because the retention clock runs in tax years, storing records by year means that when a window finally closes, you can clear out that whole year at once and know exactly what's still inside the keep-for-seven-years line.</li>
</ul>
<p>The point of all this isn't to please the tax office. It's that good records are worth money to you. Every receipt you can produce is a deduction you get to keep, and a few minutes of tidy filing today saves hours of digging later. For a full walkthrough of building the habit, see <a href="/how-to-scan-and-organize-receipts/">how to scan and organize receipts</a>.</p>
HTML,
    ],

    [
      'h2' => 'Scan once, keep it forever, with Argo Books',
      'anchor' => 'scan-with-argo',
      'html' => <<<'HTML'
<p>This is exactly the job Argo Books is built for. Snap a photo of a receipt and the app's AI reads it for you, pulling out the vendor, date, amount, and tax, then filing it as an expense. You're not retyping anything, and the receipt image is saved right alongside the expense record it proves. One action does the capturing, the sorting, and the storing at the same time.</p>
<p>That's the retention rule handled without a shoebox. The paper receipt can fade to blank or go in the recycling the same afternoon, because the readable digital copy is stored with the expense and stays crisp for as long as you keep your books. Years from now, when you or your accountant need to back up a figure, you open the expense and the original receipt is right there, exactly as clear as the day you scanned it.</p>
<p>Because Argo Books is a desktop app, your records stay on your own machine rather than on someone else's server. Receipt scanning is free for up to 10 receipts a month, and Premium raises that to 500 a month at <strong>${argo_premium_monthly}/month</strong> if you're running higher volume. There's also a free web receipt scanner on the site if you just want to try it on a receipt sitting on your desk right now. Either way, the receipt gets captured once and stays readable for the whole retention window, which is the entire point of keeping it.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => "See how snapping one photo turns a fading paper receipt into a permanent record filed with the expense.",
  'tool_callout_cta' => 'See receipt scanning',
  'tool_callout_url' => '/features/receipt-scanning/',

  'faqs' => [
    [
      'q' => 'How many years should I keep receipts?',
      'a' => "It depends on where you file. The IRS generally wants records for 3 years, and up to 6 or 7 in some cases. The CRA generally wants 6 years, HMRC generally 5 to 6, and the ATO generally 5. A simple rule that safely covers all of them is to keep everything for at least seven years. Once your records are digital that costs almost nothing, and it means you're never short a document if a return gets reviewed. Check with an accountant for your own situation, since some records need to be kept longer.",
    ],
    [
      'q' => 'Are digital copies of receipts acceptable?',
      'a' => "Yes. The IRS, CRA, HMRC, and ATO all accept clear electronic copies of receipts and records. The conditions are straightforward: the copy has to be complete and legible, it has to be a faithful image of the original rather than a retyped summary, and you have to be able to produce it if you're asked. A good scan or clear photo meets all three, so you don't need to keep the paper as well once you have it.",
    ],
    [
      'q' => 'Do I need to keep the paper receipt too?',
      'a' => "In most cases, no. Once you have a clear, complete digital copy, the tax offices in the US, Canada, UK, and Australia let you rely on that and dispose of the paper. This is actually the safer choice for thermal receipts, which fade over time, because the scan captures the text before it disappears. If you have a small number of unusual or high-value documents you feel uneasy about, you can keep those originals as well, but for everyday receipts a good scan is enough.",
    ],
    [
      'q' => 'What if a receipt fades before I file my taxes?',
      'a' => "A faded receipt that can no longer be read is treated as no receipt at all, and an expense you can't evidence is one you may not be able to claim. Most receipts are printed on thermal paper, which fades with heat, light, and time, so this happens more often than people expect. The fix is to scan or photograph receipts as soon as you get them, while the print is still crisp. Once you have a clear copy saved, it won't fade, so the state of the paper stops mattering.",
    ],
    [
      'q' => 'Which records do I need for taxes?',
      'a' => "Anything that backs up a number on your return. That means receipts for the expenses you claim, invoices from suppliers, the invoices you sent customers as proof of income, your bank and credit card statements, copies of the tax returns you filed, and payroll records if you have staff. You don't need junk mail, duplicates, or packaging. The test is whether a document could help you explain a figure on your return. If it could, keep it; if not, let it go.",
    ],
    [
      'q' => 'Where should I store my business receipts?',
      'a' => "Somewhere that keeps them readable, searchable, and backed up. A drawer of paper meets the rule but fails the moment you need one specific receipt from years ago. Scanning each receipt and storing the image with the expense it belongs to is far more useful, because the amount, date, vendor, and proof all sit together. Keep a backup so a dead laptop can't wipe your records, and file by tax year so you can clear out a year cleanly once its retention window closes.",
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'usa',
    'canada',
  ],

  'related_article_slugs' => [
    'how-to-scan-and-organize-receipts',
    'business-expense-categories',
    'what-counts-as-a-business-expense',
    'small-business-tax-deductions',
  ],
];
