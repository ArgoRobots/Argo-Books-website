<?php
$pageTitle = 'Spreadsheet Export';
$pageDescription = 'Learn how to export your Argo Books data to Excel spreadsheets for backup, analysis, or sharing with accountants.';
$currentPage = 'spreadsheet-export';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Export your data to Excel spreadsheets for backup, analysis, or sharing with accountants and business partners.</p>

            <h2>Exporting Your Data</h2>
            <ol class="steps-list">
                <li>Click "File > Export as"</li>
                <li>Select the "Spreadsheet" tab</li>
                <li>Set a date range to filter which records to include</li>
                <li>Select which data categories to export</li>
                <li>Choose your preferred currency for the export</li>
                <li>Click "Export" and select a location to save your file</li>
            </ol>

            <h2>Currency Conversion</h2>
            <p>When exporting, you can choose any of the <a class="link" href="../reference/supported-currencies.php">supported currencies</a>. The system will:</p>
            <ul>
                <li>Convert all monetary values to your chosen currency using historical exchange rates</li>
                <li>Display values with proper currency formatting</li>
                <li>Add a note at the top indicating which currency is being used</li>
            </ul>

            <div class="info-box">
                <strong>Multi-Item Transactions:</strong> Transactions with multiple items are exported with the main transaction details on the first row, and additional items on subsequent rows with empty transaction ID cells.
            </div>

            <h2>Receipt Export</h2>
            <p>Receipt filenames are included in the exported spreadsheet. If you need the actual receipt files:</p>
            <ol class="steps-list">
                <li>Select the transactions you want to export receipts for in the main view</li>
                <li>Right-click and select "Export receipts"</li>
                <li>Choose a save location</li>
            </ol>

            <h2>Chart Export</h2>
            <p>Charts from the Analytics Dashboard can also be exported to Excel with full data:</p>
            <ol class="steps-list">
                <li>Right-click any chart in the Analytics Dashboard</li>
                <li>Select "Export to Microsoft Excel"</li>
                <li>Choose a save location</li>
            </ol>

            <div class="page-navigation">
                <a href="spreadsheet-import.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Spreadsheet Import
                </a>
                <a href="report-generator.php" class="nav-button next">
                    Next: Report Generator
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
