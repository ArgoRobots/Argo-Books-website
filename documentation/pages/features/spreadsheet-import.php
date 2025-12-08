<?php
$pageTitle = 'Spreadsheet Import';
$pageDescription = 'Learn how to import data from Excel spreadsheets into Argo Books. Supports multiple currencies and automatic data detection.';
$currentPage = 'spreadsheet-import';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Import your existing business data from Excel spreadsheets into Argo Books. The import system supports multiple currencies and can automatically detect the currency used in your data.</p>

            <h2>Preparing Your Spreadsheet</h2>
            <p>Download our <a class="link" href="../../../../resources/downloads/Argo Books format.xlsx">template spreadsheet</a> to see the exact format required. Your Excel file can include any combination of the following sheets (not all are required):</p>

            <div class="info-box">
                <ul>
                    <li><strong>Accountants</strong> - A simple list with accountant names</li>
                    <li><strong>Companies</strong> - A simple list with company names</li>
                    <li><strong>Purchase products</strong> - Products for purchasing</li>
                    <li><strong>Sale products</strong> - Products for selling</li>
                    <li><strong>Purchases</strong> - Purchase transaction records</li>
                    <li><strong>Sales</strong> - Sales transaction records</li>
                </ul>
            </div>

            <h2>Formatting Requirements</h2>
            <div class="info-box">
                <p>Download our <a class="link" href="../../../../resources/downloads/Argo Books format.xlsx">template spreadsheet</a> and follow the exact format shown. This is much easier than trying to remember formatting rules!</p>

                <p>Key points:</p>
                <ul>
                    <li><strong>Sheet names:</strong> Use "Accountants", "Companies", "Purchase products", "Sale products", "Purchases", "Sales" (case doesn't matter)</li>
                    <li><strong>Date format:</strong> YYYY-MM-DD (e.g., 2025-01-15)</li>
                    <li><strong>Country names:</strong> Must match the <a class="link" href="../reference/accepted-countries.php">accepted country list</a></li>
                    <li><strong>Everything else:</strong> Follow the template format exactly</li>
                </ul>
            </div>

            <h2>Currency Support</h2>
            <p>The import system supports <a class="link" href="../reference/supported-currencies.php">28 different currencies</a>. The system will attempt to automatically detect the currency from your spreadsheet data, but you can also manually select the source currency during import.</p>

            <div class="info-box">
                <strong>Multi-Currency Support:</strong> If your spreadsheet contains data in a different currency than your default, the system will automatically convert all values using real-time exchange rates for the transaction dates.
            </div>

            <h2>How to Import</h2>
            <ol class="steps-list">
                <li>Click "File > Import spreadsheet"</li>
                <li>Select your Excel file</li>
                <li>The system will detect which data sheets are available and show a preview</li>
                <li>Review the detected currency (or select manually if needed)</li>
                <li>Select which data sections you want to import using the checkboxes</li>
                <li>Optionally select a receipts folder if you have receipt files to import</li>
                <li>Click "Import" to begin the process</li>
            </ol>

            <h2>What Gets Created Automatically</h2>
            <p>The import system automatically creates any missing companies, categories, or accountants referenced in your transaction data.</p>

            <h2>Receipt Import</h2>
            <p>If you have receipt files to import alongside your data:</p>
            <ol class="steps-list">
                <li>Organize your receipt files in a folder on your computer</li>
                <li>In your spreadsheet, include the receipt filename in the "Receipt" column</li>
                <li>During import, select the folder containing your receipt files</li>
                <li>The system will automatically link receipts to their corresponding transactions</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> The system automatically looks for a receipts folder next to your spreadsheet file with names like "receipts".
            </div>

            <div class="page-navigation">
                <a href="receipts.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Receipt Management
                </a>
                <a href="spreadsheet-export.php" class="nav-button next">
                    Next: Spreadsheet Export
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
