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
            <p>When adding an expense or revenue entry, click the "Attach" button, then select the receipt from your computer.</p>
            <p>To add receipts to existing transactions, click the edit button in the actions column, and click the "Attach" button.</p>

            <h2>Digitizing Physical Receipts</h2>
            <p>You can quickly digitize paper receipts by taking a photo with your smartphone:</p>
            <ol class="steps-list">
                <li>Install Microsoft Lens on your smartphone - it's free and available for both iOS and Android</li>
                <li>Open Microsoft Lens and select "Document" mode</li>
                <li>Position your phone's camera over the receipt</li>
                <li>The app will automatically detect the receipt's edges and optimize the image</li>
                <li>Save the digitized receipt as a PDF or image file</li>
                <li>Upload the digital copy to your computer</li>
            </ol>

            <h2>Exporting Receipts from the Receipts page</h2>
            <ol class="steps-list">
                <li>Go to "Receipts" in the sidebar (at the very bottom)</li>
                <li>Filter the receipts you want to export (optional)</li>
                <li>Select the receipts you want to export."</li>
                <li>Click the "Export Selected" button and choose the destination</li>
            </ol>

            <div class="page-navigation">
                <a href="sales-tracking.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Expense/Revenue Tracking
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
