<?php
require_once __DIR__ . '/../../../resources/icons.php';
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
            <p>You can quickly digitize paper receipts by taking a photo with your phone:</p>
            <ol class="steps-list">
                <li>Install Microsoft Lens on your phone - it's free and available for both iOS and Android</li>
                <li>Open Microsoft Lens and select "Document" mode</li>
                <li>Position your phone's camera over the receipt</li>
                <li>The app will automatically detect the receipt's edges and optimize the image</li>
                <li>Save the digitized receipt as a PDF or image file</li>
                <li>Upload the digital copy to your computer</li>
            </ol>

            <h2>Exporting Receipts from the Receipts page</h2>
            <ol class="steps-list">
                <li>Go to "Receipts" in the navigation menu (under Tracking)</li>
                <li>Filter the receipts you want to export (optional)</li>
                <li>Select the receipts you want to export</li>
                <li>Click the "Export Selected" button and choose the location that you want to save the files</li>
            </ol>

            <div class="page-navigation">
                <a href="lost-damaged.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Lost & Damaged Inventory</span>
                </a>
                <a href="receipt-scanning.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">AI Receipt Scanning &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
