<?php
// articles/data/import-bank-transactions-from-csv-into-accounting-software.php
// See articles/data/_template.php for schema.

return [

  'slug' => 'import-bank-transactions-from-csv-into-accounting-software',

  'h1' => 'How to import bank transactions from CSV into accounting software',

  'meta_title' => 'Import Bank Transactions From CSV to Accounting Software | Argo Books',

  'meta_description' => 'How to import bank transactions from a CSV into accounting software: download a statement, understand the columns, map them, categorize, and match the total.',

  'schema_type' => 'HowTo',

  'category' => 'spreadsheets',
  'hub_weight' => 30,

  'published' => '2026-06-15',

  'updated' => '2026-06-26',

  'reading_time_min' => 9,

  'total_time_iso8601' => 'PT20M',

  'intro_html' => <<<'HTML'
<p>Typing your bank transactions into your books by hand is one of the most tedious jobs in bookkeeping, and one of the easiest to get wrong. A transposed number here, a skipped line there, and your records quietly stop matching your account. The fix is to let the bank do the typing for you: download your statement as a CSV file and import it, so every transaction lands in your books in seconds instead of an evening.</p>
<p>Nearly every bank lets you export a statement as a CSV, and most accounting software can read one. The trick is doing it cleanly: getting the right file, understanding its columns, tidying anything odd, importing with the columns mapped correctly, categorizing the transactions, and then checking the imported total matches the statement so you know nothing slipped through. This guide walks through all of it. It's a job you'll do regularly, so it's worth getting the routine right once.</p>
HTML,

  'sections' => [

    [
      'h2' => 'Step 1: download a CSV statement from your bank',
      'anchor' => 'download-the-csv',
      'html' => <<<'HTML'
<p>Start in your online banking. Almost every bank lets you export transactions, usually under a "Download", "Export", or "Statement" option on the account screen. When you export, you'll typically choose a date range and a format. Pick CSV. Some banks call it "CSV", some "Comma delimited", and some offer Excel (.xlsx), which works just as well for an import.</p>
<p>A few things to get right at this stage:</p>
<ul>
<li><strong>Choose a sensible date range.</strong> Match it to the period you're bringing in, often a single month. Exporting one month at a time keeps each file manageable and makes the total easy to check later.</li>
<li><strong>Don't overlap with what you already imported.</strong> If you imported up to the end of last month, start this file at the first of this month, so you don't bring the same transactions in twice.</li>
<li><strong>Note the closing balance or the period total.</strong> You'll use this at the end to confirm everything came across. Jot it down or keep the statement page open.</li>
</ul>
<p>If your bank only offers PDF statements, that's harder, because a PDF isn't a data file. Check for a separate transaction export, or for a CSV option in the account view rather than the statements section, as the two are often in different places. Most banks have a CSV somewhere even when the headline option is a PDF.</p>
HTML,
      'step_name' => 'Download a CSV statement from your bank',
      'step_text' => 'In online banking, export your transactions as CSV for a sensible date range, usually one month. Avoid overlapping with what you already imported, and note the closing balance or period total for later.',
    ],

    [
      'h2' => 'Step 2: understand the columns in the file',
      'anchor' => 'understand-columns',
      'html' => <<<'HTML'
<p>Open the CSV in Excel or any spreadsheet program and look at what your bank actually gave you. Bank exports vary a lot, but they almost always contain the same handful of pieces of information, just named and arranged differently from bank to bank:</p>
<ul>
<li><strong>Date.</strong> When the transaction happened. Banks format dates in all sorts of ways, so note whether yours is day-month-year or month-day-year.</li>
<li><strong>Description.</strong> Who the money went to or came from. This is often messy, with reference codes and extra text, but it's what tells you how to categorize each line.</li>
<li><strong>Amount.</strong> Here banks differ most. Some use one column with negatives for money out and positives for money in. Others use two separate columns, often labelled "Debit" and "Credit" or "Money out" and "Money in". Knowing which style your bank uses matters, because it changes how the import reads what's a payment and what's income.</li>
<li><strong>Balance.</strong> A running balance after each transaction. Useful for checking, but you usually won't import it.</li>
</ul>
<p>You don't need to change anything yet. The goal is just to know what each column is, so that when the import asks, you can confirm the date column is the date and the amount column is the amount. The two-column versus one-column amount is the single thing most worth noticing, because it's where an import most often needs a moment's attention.</p>
HTML,
      'step_name' => 'Understand the columns in the file',
      'step_text' => 'Open the CSV and identify the date, description, and amount columns. Note your date format and whether amounts are one column with negatives or two columns for money in and money out.',
    ],

    [
      'h2' => 'Step 3: tidy the file if it needs it',
      'anchor' => 'tidy-the-file',
      'html' => <<<'HTML'
<p>Many bank CSVs import fine with no changes at all. But some come with clutter at the top that can confuse an import, and a quick tidy takes a minute and saves trouble. Open the file and check for:</p>
<ul>
<li><strong>Header junk above the data.</strong> Some banks put your name, account number, and a few blank rows before the actual columns start. If there are rows above the header row, delete them so the column titles sit in row one with the transactions directly underneath.</li>
<li><strong>A missing header row.</strong> A few banks export raw data with no column titles at all. If so, add a simple header row: Date, Description, Amount, or Date, Description, Money out, Money in, matching what's actually in each column.</li>
<li><strong>Summary rows at the bottom.</strong> An opening or closing balance line tacked onto the end isn't a transaction, so delete it, or just leave it unmapped and skip it during the import.</li>
</ul>
<p>What you do <em>not</em> need to do is rename columns to match the software or reorder them. A good importer maps your columns however the bank named and arranged them. You're only removing the stuff that isn't transaction data, so the import sees a clean table: one header row, then one row per transaction. If your file is already clean, skip this step entirely and go straight to importing.</p>
HTML,
      'step_name' => 'Tidy the file if it needs it',
      'step_text' => 'Remove any header junk above the column titles, add a header row if the bank left one out, and delete summary balance rows at the bottom. You don\'t need to rename or reorder columns.',
    ],

    [
      'h2' => 'Step 4: import with automatic column mapping',
      'anchor' => 'import-with-mapping',
      'html' => <<<'HTML'
<p>Now bring the file into your accounting software. In a tool with a proper importer, you drag the CSV in and it reads your column headers and proposes a mapping: this column is the date, this is the description, this is the amount. Because every bank lays out its export differently, an importer that reads any column layout is what saves you from reformatting each bank's file to fit a fixed template.</p>
<p>This is what the spreadsheet importer in Argo Books is built to handle. You drop in the CSV your bank gave you, and it reads whatever the columns are called and however they're ordered, then maps them to the right fields on its own. A messy bank export comes in without you reshaping it first. It works with CSV and Excel files specifically, which is exactly what bank statements export as. And every import has a one-click undo, so if a file goes in wrong, you reverse it and try again rather than deleting lines by hand.</p>
<p>Pay attention to the amount mapping in particular. If your bank uses one amount column with negatives, confirm the import reads negatives as money out. If it uses two columns, make sure money-out and money-in each map to the right side, so payments and income don't get crossed. The importer's preview shows you how the transactions will land before anything is saved, so check it there. The first time you import from a given bank, it's worth a careful look; after that, the same bank's files import the same way each time.</p>
HTML,
      'step_name' => 'Import with automatic column mapping',
      'step_text' => 'Drag the CSV into the importer and let it read your headers and map the columns. Pay special attention to the amount mapping so money out and money in land on the right side. Check the preview before saving.',
    ],

    [
      'h2' => 'Step 5: categorize the imported transactions',
      'anchor' => 'categorize',
      'html' => <<<'HTML'
<p>An imported transaction is a record of money moving, but it isn't yet useful for bookkeeping until you've said what it was. Categorizing is what turns a list of bank lines into something that produces a tax return: this payment was fuel, this one was materials, this deposit was a customer paying an invoice.</p>
<p>You don't need an accountant's full chart of accounts. A handful of buckets that match how your business actually spends and earns is enough to start:</p>
<ul>
<li><strong>Income</strong> for money coming in, ideally matched to the invoice it pays.</li>
<li><strong>Materials and supplies</strong> for the things you buy to do the work.</li>
<li><strong>Vehicle and travel</strong> for fuel, parking, and trips.</li>
<li><strong>Software and subscriptions</strong> for recurring digital costs.</li>
<li><strong>Fees, insurance, and professional costs</strong> for the overheads.</li>
</ul>
<p>Good software learns as you go: once you've categorized a few payments from the same supplier, it suggests the same category next time, so a recurring subscription or a regular supplier gets categorized almost automatically. The description column you imported is what makes this possible, so it's worth having. Work down the list and assign a category to each line. This is the one part of the process that still needs your judgement, because only you know what a given payment was for, but it goes quickly once the recurring ones are recognized. For more on this, see our guide on <a href="/how-to-track-business-expenses-without-spreadsheets/">tracking business expenses without spreadsheets</a>.</p>
HTML,
      'step_name' => 'Categorize the imported transactions',
      'step_text' => 'Assign a category to each imported line: income, materials, vehicle and travel, subscriptions, fees, and so on. Software learns from recurring suppliers, so it goes quickly once the regular ones are recognized.',
    ],

    [
      'h2' => 'Step 6: check the imported total matches the statement',
      'anchor' => 'check-the-total',
      'html' => <<<'HTML'
<p>This is the step that proves nothing slipped through, and it's the reason you noted the statement total back at step one. Add up the transactions you imported and check the total against the bank statement for the same period. If the money-in and money-out you imported add up to the same change in balance the statement shows, every transaction came across. If the totals don't match, the gap tells you something is missing or doubled.</p>
<p>Common causes of a mismatch are easy to find once you know to look:</p>
<ul>
<li><strong>A row that didn't import,</strong> often a summary line you meant to skip but the software counted, or a transaction the file split awkwardly.</li>
<li><strong>A doubled import,</strong> where the date range overlapped a previous file and some transactions came in twice.</li>
<li><strong>An amount read with the wrong sign,</strong> where a payment was counted as income or the reverse, which throws the total out by twice the amount.</li>
</ul>
<p>When the total matches, your books and your bank agree for the period, which is exactly what you want. This is the routine check that keeps your records trustworthy, so when you pull a report or hand figures to your accountant, the numbers are facts and not hopes. If something is off, the one-click undo lets you roll the import back, fix the file or the mapping, and import cleanly. Get this matching right each time and your books stay in step with your bank all year.</p>
HTML,
      'step_name' => 'Check the imported total matches the statement',
      'step_text' => 'Add up the imported transactions and compare the total against the bank statement for the same period. If they match, nothing slipped through. If not, look for a missing row, a doubled import, or a wrong sign, and use undo to redo it cleanly.',
    ],

  ],

  'callout_after_section_index' => 3,

  'tool_callout_text' => 'Argo Books reads the CSV your bank exports, maps the columns however your bank named them, and brings every transaction in without reformatting. One-click undo on every import.',
  'tool_callout_cta' => 'See the spreadsheet importer',
  'tool_callout_url' => '/features/spreadsheet-import/',

  'faqs' => [
    [
      'q' => 'How do I get a CSV of my bank transactions?',
      'a' => 'Log into your online banking and look for a Download, Export, or Statement option on the account screen. When you export, choose a date range and pick CSV as the format, sometimes labelled Comma delimited. Excel format works for an import too. Exporting one month at a time keeps the file manageable and makes the total easy to check afterward. If your bank only shows PDF statements in the statements section, look for a separate transaction export or a CSV option in the account view, since the two are often in different places and most banks offer a CSV somewhere.',
    ],
    [
      'q' => 'My bank CSV has two amount columns. Will that import correctly?',
      'a' => 'Yes, a good importer handles both styles. Some banks use one amount column with negatives for money out and positives for money in; others use two separate columns, often labelled Debit and Credit or Money out and Money in. The thing to do is check the amount mapping during the import: confirm that money out and money in each land on the right side, so payments are not read as income or the reverse. The importer shows a preview before anything is saved, so you can verify it there. The first import from a given bank is worth a careful look; after that the same bank files behave the same way.',
    ],
    [
      'q' => 'Do I need to reformat the bank file before importing it?',
      'a' => 'Usually only a little, if at all. Many bank CSVs import fine as-is. The tidying that sometimes helps is removing header junk above the column titles, adding a header row if the bank left one out, and deleting a summary balance line at the bottom. What you don\'t need to do is rename columns to match the software or put them in a particular order, because a good importer maps your columns however the bank named and arranged them. You are only clearing out the rows that are not transaction data, so the import sees a clean table of one row per transaction.',
    ],
    [
      'q' => 'How often should I import my bank transactions?',
      'a' => 'Monthly is a sensible rhythm for most small businesses. Importing once a month, right after the statement period closes, keeps your books current without becoming a daily chore, and matching the monthly total against the statement is an easy check. If your volume is high or your cash flow is tight, importing more often, say weekly, keeps you closer to real time. The main rule is to be consistent about where each period starts and ends so you neither skip transactions nor import the same ones twice. A regular routine is what keeps your records in step with your bank all year.',
    ],
    [
      'q' => 'Is this article just trying to sell me Argo Books?',
      'a' => 'This is the Argo Books site, so read it with that in mind, and yes, the importer described is one of our features. But the process here works with any accounting tool that imports a CSV, and most do. Downloading a statement, understanding the columns, tidying the file, importing with the columns mapped, categorizing, and checking the total against the statement are universal steps that apply whatever software you use. If you take the routine and run it in a competitor, the guide did its job. We would rather you keep your books in step with your bank, with whatever tool fits you, than leave them to drift.',
    ],
  ],

  'related_niche_slugs' => [
    'freelance',
    'contractor',
    'consultant',
  ],

  'related_article_slugs' => [
    'how-to-convert-excel-spreadsheet-to-accounting-software',
    'how-to-track-business-expenses-without-spreadsheets',
    'small-business-bookkeeping-basics',
  ],
];
