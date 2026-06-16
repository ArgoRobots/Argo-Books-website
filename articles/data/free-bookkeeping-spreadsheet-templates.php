<?php
// articles/data/free-bookkeeping-spreadsheet-templates.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'free-bookkeeping-spreadsheet-templates',

  'h1' => 'Free bookkeeping spreadsheet templates: what to use and how to set one up',

  'meta_title' => 'Free Bookkeeping Spreadsheet Templates (2026) | Argo Books',

  'meta_description' => 'A practical guide to free bookkeeping spreadsheet templates: what a good one contains, the exact columns to use, where to find them, and how to set one up.',

  'schema_type' => 'Article',

  // Guides hub: category + ordering (lower hub_weight lists first).
  'category' => 'spreadsheets',
  'hub_weight' => 100,

  'published' => '2026-06-15',

  'updated' => '2026-06-15',

  'reading_time_min' => 9,

  'total_time_iso8601' => null,

  'intro_html' => <<<'HTML'
<p>A free spreadsheet is the right place to start your bookkeeping. It costs nothing, it runs on a laptop you already own, and a simple one will carry a small business a long way. The trouble is that most "free bookkeeping template" downloads are either too bare to be useful or so crammed with tabs and formulas that you give up before the first month is logged.</p>
<p>This guide cuts through that. It covers what a genuinely useful bookkeeping spreadsheet contains, the exact columns to put in your income log and expense log, where to find good free templates, how to set one up from scratch in an afternoon, and the honest point at which a spreadsheet stops being the easy option. The goal is to get you tracking money this week with something you will actually keep up with.</p>
HTML,

  'sections' => [

    [
      'h2' => 'What a good bookkeeping template actually contains',
      'anchor' => 'what-it-contains',
      'html' => <<<'HTML'
<p>A bookkeeping spreadsheet doesn't need to be clever. It needs four tabs, each doing one job. If a template has fewer than these, it's missing something; if it has many more, it's probably more than a small business needs to start.</p>
<ul>
<li><strong>An income tab.</strong> One row per payment that comes in: the date, who paid you, what for, and the amount. This is the record of every dollar the business earned.</li>
<li><strong>An expense tab.</strong> One row per business cost going out, with a category on each row. This is the record that turns into your deductions at tax time, so it's the tab that matters most for your tax bill.</li>
<li><strong>A summary tab (your profit and loss).</strong> A handful of formulas that add up income, add up expenses by category, and show the difference. This is the tab you actually look at to know how the business is doing. You don't type into it; it reads from the other two.</li>
<li><strong>An invoice tracker.</strong> A list of invoices you've sent with their status: sent, paid, overdue. This is what stops a forgotten invoice from quietly costing you money. If you don't send invoices, you can skip this one.</li>
</ul>
<p>That's the whole thing. Two tabs you type into, one that does the maths, and one that watches your unpaid invoices. Everything else a fancy template adds, like budgets, forecasts, and dashboards, is optional and can wait until the basics are a habit.</p>
HTML,
    ],

    [
      'h2' => 'The exact columns for your income log',
      'anchor' => 'income-columns',
      'html' => <<<'HTML'
<p>Keep the income tab simple enough that logging a payment takes ten seconds. These columns cover what you'll need at tax time without turning data entry into a chore:</p>
<table>
<thead>
<tr><th>Column</th><th>What goes in it</th></tr>
</thead>
<tbody>
<tr><td>Date</td><td>The date the payment landed in your account.</td></tr>
<tr><td>Client / source</td><td>Who paid you.</td></tr>
<tr><td>Description</td><td>What the payment was for, in a few words.</td></tr>
<tr><td>Invoice number</td><td>The invoice this payment settles, if you sent one. Links your income back to your invoice tracker.</td></tr>
<tr><td>Amount</td><td>The total received.</td></tr>
<tr><td>Tax collected</td><td>Any sales tax or GST/VAT inside that amount, if you charge it. Keep it in its own column so you don't hand it to yourself as income.</td></tr>
<tr><td>Payment method</td><td>Bank transfer, card, cash. Useful when you check against the bank later.</td></tr>
</tbody>
</table>
<p>If you don't charge sales tax, drop that column. If you never take cash, drop the method column. The rule is to add a column only when it earns its keep, because every extra column is one more thing to fill in on every row, and the templates people abandon are always the over-built ones.</p>
HTML,
    ],

    [
      'h2' => 'The exact columns for your expense log',
      'anchor' => 'expense-columns',
      'html' => <<<'HTML'
<p>The expense tab is the one that saves you money, because every cost you log is a deduction you can claim. It needs one extra column the income tab doesn't: a category, so your costs sort themselves into the buckets your tax return wants.</p>
<table>
<thead>
<tr><th>Column</th><th>What goes in it</th></tr>
</thead>
<tbody>
<tr><td>Date</td><td>The date of the purchase.</td></tr>
<tr><td>Supplier</td><td>Who you paid.</td></tr>
<tr><td>Description</td><td>What you bought.</td></tr>
<tr><td>Category</td><td>The bucket it falls in. Pick from a fixed list so totals add up cleanly (see below).</td></tr>
<tr><td>Amount</td><td>The total paid.</td></tr>
<tr><td>Tax paid</td><td>Sales tax or GST/VAT you paid, if you track it for a claim.</td></tr>
<tr><td>Payment method</td><td>How you paid, for the monthly bank check.</td></tr>
<tr><td>Receipt?</td><td>A yes/no so you can see at a glance which costs still need their proof attached or filed.</td></tr>
</tbody>
</table>
<p>For the category column, set a short fixed list and reuse it: materials and supplies, vehicle and travel, tools and equipment, software and subscriptions, insurance and professional fees, and meals kept separate because their tax treatment often differs. In a spreadsheet you can turn this list into a drop-down (Data, then Data validation in both Excel and Google Sheets) so you pick a category instead of typing it. That one step keeps your categories spelled the same way every time, which is what lets the summary tab total them correctly.</p>
HTML,
    ],

    [
      'h2' => 'Where to find free templates',
      'anchor' => 'where-to-find',
      'html' => <<<'HTML'
<p>You don't have to build from nothing. Several genuinely free sources will get you a working template in minutes:</p>
<ul>
<li><strong>Excel's built-in templates.</strong> Open Excel, choose New, and search "expense" or "income" or "small business". Microsoft ships several free bookkeeping and budget templates. They're plain but solid, and they're already on your machine.</li>
<li><strong>Google Sheets template gallery.</strong> In Google Sheets, the template gallery has expense reports and simple budget sheets you can copy for free. The advantage is that it's online, so you can log an expense from your phone, and it saves automatically.</li>
<li><strong>Vertex42, Smartsheet, and similar template sites.</strong> A handful of long-running sites offer free, well-built bookkeeping and small-business templates as Excel downloads. They tend to be more complete than the built-ins, with income, expense, and summary tabs already wired together. Just be a little wary of the ones that bury a free download under newsletter sign-ups or upsells.</li>
<li><strong>Your accountant.</strong> If you have one, ask. Many will hand you a simple spreadsheet set up the way they like to receive it, which makes tax time smoother because the categories already match what they file.</li>
</ul>
<p>Whichever you pick, check it has the four tabs above and the columns you actually need, then strip out anything you don't. A trimmed-down template you understand beats a feature-packed one you don't.</p>
HTML,
    ],

    [
      'h2' => 'How to set one up in an afternoon',
      'anchor' => 'how-to-set-up',
      'html' => <<<'HTML'
<p>Building your own from scratch isn't hard, and doing it once means you understand every cell. Here's the order that works:</p>
<ol>
<li><strong>Make the four tabs.</strong> Name them Income, Expenses, Summary, and Invoices. Three blank sheets and a header row each is the whole skeleton.</li>
<li><strong>Add the column headers</strong> from the two tables above to the Income and Expenses tabs. Put one bold header row at the top and freeze it (View, then Freeze) so it stays visible as the list grows.</li>
<li><strong>Set up the category drop-down</strong> on the Expenses tab using Data validation, pointing at your fixed category list. This is the step that keeps your totals trustworthy.</li>
<li><strong>Wire up the Summary tab.</strong> Use <em>SUM</em> to total income and total expenses, and <em>SUMIF</em> to total expenses by category (it adds every expense row whose category matches). Subtract total expenses from total income and you have your profit. That's the entire profit-and-loss in about six formulas.</li>
<li><strong>Build the invoice tracker.</strong> Columns for invoice number, client, date sent, due date, amount, and a status drop-down of Sent / Paid / Overdue. You now know at a glance what's owed to you.</li>
<li><strong>Log a week, then breathe.</strong> Enter a real week of income and expenses. If a column feels useless, delete it now. The template you keep is the one that fits how you actually work.</li>
</ol>
<p>Keep it somewhere it backs up on its own. Google Sheets does this by default; an Excel file lives more safely in OneDrive, Dropbox, or Google Drive than on a single laptop. A bookkeeping file that exists in only one place is a file you can lose.</p>
HTML,
    ],

    [
      'h2' => 'When a spreadsheet stops being the easy option',
      'anchor' => 'when-it-stops',
      'html' => <<<'HTML'
<p>Here's the honest part. A spreadsheet is the easy option right up until the typing becomes the job, and then it quietly becomes the hard one. The signs are consistent:</p>
<ul>
<li><strong>The manual entry is eating real time.</strong> When you're spending more than an hour a month typing rows and chasing the total that won't match the bank, the spreadsheet is costing you more in time than software would in money.</li>
<li><strong>Receipts and rows have drifted apart.</strong> The spreadsheet has the numbers; the receipts are in a drawer. When matching the two at tax time is an afternoon of detective work, the spreadsheet has stopped doing its main job.</li>
<li><strong>A formula quietly went wrong.</strong> One mistyped range or a row inserted in the wrong place and the summary is off, and you can't fully trust the number you're handing your accountant.</li>
<li><strong>The volume climbed.</strong> Twenty transactions a month is fine in a sheet. Two hundred, across several suppliers, with sales tax and invoices to track, is a part-time data-entry job.</li>
</ul>
<p>None of this means the spreadsheet was a mistake. It did exactly what it should: it got you tracking money for free while the business was small. When the typing outgrows it, the good news is you don't start over. Most accounting software lets you import a spreadsheet directly, so the months you've already logged carry across instead of being retyped. Argo Books, for example, takes a drag-and-drop of your Excel or CSV file and maps your columns to its fields no matter how you laid them out, with one-click undo if a mapping looks off, and the first {argo_free_invoice_limit} imports a month cost nothing. That's the natural next step when the sheet gets heavy: not throwing away your work, but moving it somewhere that does the typing for you.</p>
<p>Until then, a clean spreadsheet you keep current is a complete, valid bookkeeping system, and there's no rule that says you have to leave it. Plenty of small businesses never do.</p>
HTML,
    ],

  ],

  'callout_after_section_index' => 2,

  'tool_callout_text' => 'Outgrown the spreadsheet? Argo Books imports your Excel or CSV file, maps the columns for you, and picks up where the sheet left off.',
  'tool_callout_cta' => 'See spreadsheet import in Argo Books',
  'tool_callout_url' => '/features/spreadsheet-import/',

  'faqs' => [
    [
      'q' => 'Is a free spreadsheet really good enough for small-business bookkeeping?',
      'a' => 'For a lot of small businesses, yes. A clean spreadsheet with an income tab, an expense tab with categories, a summary that totals them, and an invoice tracker is a complete bookkeeping system, and it costs nothing. It works best when your transaction volume is low and you actually keep it current. It stops being the easy option when the manual entry becomes a chore, the receipts drift away from the rows, or a formula quietly breaks the total. The honest test is effort: if you spend more than an hour a month wrestling the sheet, or you avoid it, software that does the typing for you is probably cheaper than the time and the missed deductions.',
    ],
    [
      'q' => 'What columns should a bookkeeping spreadsheet have?',
      'a' => 'For income: date, client or source, description, invoice number, amount, tax collected, and payment method. For expenses, the same idea plus a category column and a yes/no for whether you have the receipt. The category is the important one, because it sorts your costs into the buckets your tax return wants. Use a fixed list of categories and turn it into a drop-down with Data validation so they are spelled the same way every time, which is what lets a summary tab total them correctly. Drop any column you don\'t actually use, like the tax columns if you don\'t charge sales tax. A trimmed sheet you keep up with beats an over-built one you abandon.',
    ],
    [
      'q' => 'Excel or Google Sheets for bookkeeping?',
      'a' => 'Either works, and the difference comes down to how you work. Excel is more powerful for large sheets and complex formulas, and it\'s the format most accountants and most accounting software expect. Google Sheets is free, saves automatically, and lets you log an expense from your phone, which makes capture-as-you-go easier. A practical answer: use Google Sheets if you want to update on the move and never think about saving, and Excel if you prefer a desktop file and richer features. Whichever you pick, keep the file somewhere that backs up on its own, because a bookkeeping file that lives in only one place is one you can lose.',
    ],
    [
      'q' => 'How do I move my spreadsheet into accounting software later?',
      'a' => 'Most accounting tools let you import a spreadsheet directly, so you don\'t retype the months you have already logged. The usual path is to export or save your sheet as Excel or CSV, then use the software\'s import feature, which maps your columns to its fields. Argo Books, for example, takes a drag-and-drop of an Excel or CSV file, maps your columns automatically no matter how you laid them out, and gives you one-click undo if a mapping looks wrong. The point is that outgrowing a spreadsheet does not mean starting over. Your existing records become the foundation, and the software takes over the typing from there.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'No. Most of this guide is about building a free spreadsheet that owes us nothing, including the exact columns and where to download templates from Microsoft, Google, and others. Argo Books is mentioned only at the end, as the natural next step if and when the spreadsheet gets too heavy to keep up with, and there is a callout you can ignore. Yes, this is the Argo Books site, so read it with that in mind. But if you build the spreadsheet, keep it current, and never touch our software, the guide did its job. We would rather you track your money for free than buy a tool you don\'t need yet.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'consultant',
    'generic',
  ],

  'related_article_slugs' => [
    'excel-bookkeeping-template-vs-accounting-software',
    'google-sheets-bookkeeping-pros-and-cons',
    'small-business-bookkeeping-basics',
  ],
];
