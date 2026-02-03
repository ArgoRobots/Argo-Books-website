<?php
$pageTitle = 'Spreadsheet Import';
$pageDescription = 'Learn how to import data from Excel spreadsheets into Argo Books. Supports multiple currencies and automatic data detection.';
$currentPage = 'spreadsheet-import';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Import your existing business data from Excel spreadsheets into Argo Books. The import system supports multiple currencies.</p>

            <h2>Preparing Your Spreadsheet</h2>
            <p>Download our <a class="link" href="../../../resources/downloads/Argo Books format.xlsx">spreadsheet template</a> to see the exact format required.</p>

            <h2>Formatting Requirements</h2>
            <ul>
                <li><strong>Date format:</strong> YYYY-MM-DD (e.g., 2025-01-15)</li>
                <li><strong>Country names:</strong> Must match the <a class="link" href="../reference/accepted-countries.php">accepted country list</a></li>
                <li><strong>Everything else:</strong> Follow the template format exactly</li>
            </ul>

            <h2>How to Import</h2>
            <ol class="steps-list">
                <li>Click "File > Import", then click the "Excel (XLSX)" button</li>
                <li>Select your Excel file</li>
                <li>Select the currency</li>
                <li>Click "Import" to begin the process</li>
            </ol>

            <h2>What Gets Created Automatically</h2>
            <p>The import system automatically creates any missing categories, customers, or suppliers referenced in your transaction data.</p>

            <h2>Receipt Import</h2>
            <p>If you have receipt files to import alongside your data:</p>
            <ol class="steps-list">
                <li>Organize your receipt files in a folder on your computer</li>
                <li>In your spreadsheet, include the receipt file name in the "Receipt" column</li>
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
