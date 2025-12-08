<?php
$pageTitle = 'Receipt Management';
$pageDescription = 'Learn how to manage receipts in Argo Books. Add, digitize, and export receipts for all your transactions.';
$currentPage = 'receipts';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Keep your records organized by attaching and managing digital receipts for all transactions.</p>

            <h2>Adding Receipts</h2>
            <p>When adding a purchase or sale, click the "Add Receipt" button, then select the receipt file from your computer.</p>
            <p>To add receipts to existing transactions, right-click the transaction, select "Modify", and click the "Add receipt" button.</p>

            <h2>Digitizing Physical Receipts</h2>
            <p>You can quickly digitize paper receipts by taking a photo with your smartphone:</p>
            <ol class="steps-list">
                <li>Install Microsoft Lens on your smartphone - it's free and available for both iOS and Android</li>
                <li>Open Microsoft Lens and select "Document" mode</li>
                <li>Position your phone's camera over the receipt</li>
                <li>The app will automatically detect the receipt's edges and optimize the image</li>
                <li>Save the digitized receipt as a PDF or image file</li>
                <li>Upload the digital copy to your computer or sync with your cloud storage</li>
            </ol>

            <h2>Exporting Receipts from the main screen</h2>
            <ol class="steps-list">
                <li>Select the transactions you want to export receipts for. You can hold down the Ctrl key or use the Shift key</li>
                <li>Right-click on any of the selected transaction and click "Export receipts"</li>
                <li>Choose a destination folder</li>
            </ol>

            <h2>Exporting Receipts from the Receipt Manager</h2>
            <ol class="steps-list">
                <li>Click the file button on the top left, then click "Export Receipts"</li>
                <li>Optionally filter the receipts you want to export</li>
                <li>Select the receipts you want to export. You can click the "Select all" button or press "Ctrl+A"</li>
                <li>Click the "Export" button and choose the destination</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> When exporting multiple receipts, they will be organized in a dated folder.
            </div>

            <div class="page-navigation">
                <a href="sales-tracking.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Purchase/Sales Tracking
                </a>
                <a href="spreadsheet-import.php" class="nav-button next">
                    Next: Spreadsheet Import
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
