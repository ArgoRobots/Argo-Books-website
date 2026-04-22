<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'AI Receipt Scanning';
$pageDescription = 'Transform paper receipts into digital records instantly with AI-powered scanning in Argo Books.';
$currentPage = 'receipt-scanning';
$pageCategory = 'features';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> AI Receipt Scanning is available with the Premium plan.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Transform paper receipts into digital records instantly with our AI-powered scanner. Simply take a photo and let Argo Books do the rest. Our receipt scanner achieves 99% accuracy on printed receipts, and lets you quickly make adjustments.</p>

            <img src="../../../resources/images/ai-receipt-scanner.webp" alt="AI Receipt Scanner" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>How It Works</h2>
            <ol class="steps-list">
                <li>Take a photo of your receipt with your phone or upload an image</li>
                <li>Our AI analyzes the receipt and extracts all relevant information</li>
                <li>Review the extracted data and make any corrections</li>
                <li>Save to automatically create an expense record</li>
            </ol>

            <h2>What Gets Extracted</h2>
            <p>The AI automatically identifies and extracts:</p>
            <ul>
                <li><strong>Vendor Name:</strong> The business name from the receipt</li>
                <li><strong>Date:</strong> Transaction date</li>
                <li><strong>Line Items:</strong> Individual items with quantities and prices</li>
                <li><strong>Subtotal, Tax, & Total:</strong> All totals are captured</li>
                <li><strong>Payment Method:</strong> If shown on the receipt</li>
            </ul>

            <h2>Tips for Best Results</h2>
            <ul>
                <li><strong>Good Lighting:</strong> Ensure the receipt is well-lit without shadows</li>
                <li><strong>Full Frame:</strong> Capture the entire receipt in the photo</li>
                <li><strong>Flat Surface:</strong> Place the receipt on a flat surface to avoid distortion</li>
                <li><strong>Clear Focus:</strong> Make sure the text is sharp and readable</li>
            </ul>

            <div class="page-navigation">
                <a href="receipts.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Receipt Management</span>
                </a>
                <a href="spreadsheet-import.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Spreadsheet Import &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
