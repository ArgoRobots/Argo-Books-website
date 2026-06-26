<?php
require_once __DIR__ . '/../../../config/pricing.php';
require_once __DIR__ . '/../../../resources/icons.php';
$pricing = get_pricing_config();
$pageTitle = 'Bank Statement Import';
$pageDescription = 'Import a bank statement and turn it into categorized expenses and revenue in Argo Books. No bank connection and no manual data entry.';
$currentPage = 'bank-statement-import';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Bank Statement Import turns a bank statement into ready-to-use bookkeeping. Drop in a statement you downloaded from your bank and Argo Books reads each line, sorts it into expenses and revenue, and creates the transactions for you. It is the fastest way to move off spreadsheets or catch up on months of bookkeeping, with no bank connection to set up and no line-by-line typing.</p>

            <div class="info-box">
                <strong>Import vs. Bank Matching:</strong> these two features sound similar but do opposite jobs. Bank Statement Import <em>creates</em> transactions from a statement, so it is how you build your books. <a href="bank-matching.php" class="link">Bank Matching</a> <em>checks</em> a statement against transactions you have already recorded, so it is how you verify your books. Use Import to get data in, and Matching to confirm nothing is missing or recorded twice.
            </div>

            <h2>How It Works</h2>
            <ol class="steps-list">
                <li>Open the <strong>Import</strong> menu, from the File menu or the Import button on the Expenses or Revenue page, and choose <strong>Bank statement</strong></li>
                <li>Select a CSV, Excel, or PDF statement from your bank</li>
                <li>Argo Books reads the lines and prepares a transaction for each one, with a type, amount, description, and category already filled in</li>
                <li>Review the rows, adjust anything you want, and click <strong>Import</strong></li>
                <li>The new transactions appear on your Expenses and Revenue pages, ready to use</li>
            </ol>

            <h2>Automatic Categorization</h2>
            <p>Every line is sorted for you before you import:</p>
            <ul>
                <li><strong>Rules first</strong>: if you have a rule for a merchant (for example, anything from "Shell" is "Fuel"), it is applied instantly.</li>
                <li><strong>AI for the rest</strong>: lines without a rule are categorized by AI based on the merchant and description.</li>
                <li><strong>It learns</strong>: when you correct a category, Argo Books remembers your choice and applies it to that merchant next time.</li>
            </ul>
            <p>If a line references a category, supplier, or customer that does not exist yet, Argo Books creates it for you, so you never have to stop and set things up first.</p>

            <h2>Category Rules</h2>
            <p>Rules are how you teach Argo Books to categorize the merchants you see often. A rule matches part of a statement description (the merchant name) and assigns a category. You can manage your rules under <strong>Settings</strong>, where you can add, edit, or remove them. Rules created automatically as you correct categories during an import appear here too.</p>

            <h2>Reviewing and Editing Rows</h2>
            <p>Before anything is imported, you get a row for each statement line so you stay in control:</p>
            <ul>
                <li><strong>Type</strong>: switch a row between expense and revenue</li>
                <li><strong>Details</strong>: edit the description, amount, category, and supplier or customer</li>
                <li><strong>Include or exclude</strong>: leave out any line you do not want, such as an internal transfer</li>
                <li><strong>Create as you go</strong>: a "Create one" option lets you add a new category, supplier, customer, or product without leaving the import</li>
            </ul>
            <p>The footer shows how many lines will be imported, so there are no surprises.</p>

            <h2>Catch-Up and Ongoing Use</h2>
            <ul>
                <li><strong>Catch-up</strong>: starting from empty books, import several months of statements at once to get current quickly.</li>
                <li><strong>Ongoing</strong>: each month, import your latest statement to keep your books up to date with minimal effort.</li>
            </ul>

            <h2>Supported File Formats</h2>
            <ul>
                <li><strong>CSV (.csv)</strong> and <strong>Excel (.xlsx)</strong>: column detection happens locally and instantly, recognizing common header names. A statement only needs a date and either a signed amount column or separate debit and credit columns.</li>
                <li><strong>PDF (.pdf)</strong>: statements that you only have as a PDF can be imported directly.</li>
            </ul>
            <p>If a statement uses unusual or cryptic headers that cannot be recognized automatically, the AI importer is used as a backup to read the columns.</p>

            <h2>Usage</h2>
            <p>Categorizing a statement with AI counts as one bank statement import. Free accounts get <?= (int) $pricing['bank_import_monthly_limit'] ?> per month and Premium gets <?= (int) $pricing['premium_bank_import_monthly_limit'] ?>. This is a separate allowance from AI spreadsheet imports. If the AI categorization can't run (limit reached or offline), you can still import the statement and fill in the products yourself.</p>

            <h2>Privacy</h2>
            <p>There is no bank login and no third-party connection. You import a file that you download from your bank, your data stays on your device, encrypted, and it is never stored or used for training.</p>

            <div class="page-navigation">
                <a href="invoicing.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Invoicing & Payments</span>
                </a>
                <a href="bank-matching.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Bank Matching &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
