<?php
$pageTitle = 'AI Receipt Scanning';
$pageDescription = 'Transform paper receipts into digital records instantly with AI-powered scanning in Argo Books.';
$currentPage = 'receipt-scanning';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <div class="info-box">
                <p><strong>Premium Feature:</strong> AI Receipt Scanning is available with the Premium subscription.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Transform paper receipts into digital records instantly with our AI-powered scanner. Simply take a photo and let the AI do the rest.</p>

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

            <h2>Supported Receipt Types</h2>
            <ul>
                <li>Printed retail receipts</li>
                <li>Restaurant bills</li>
                <li>Gas station receipts</li>
                <li>Online order confirmations</li>
                <li>Handwritten receipts (with reduced accuracy)</li>
            </ul>

            <h2>Tips for Best Results</h2>
            <div class="info-box">
                <ul>
                    <li><strong>Good Lighting:</strong> Ensure the receipt is well-lit without shadows</li>
                    <li><strong>Full Frame:</strong> Capture the entire receipt in the photo</li>
                    <li><strong>Flat Surface:</strong> Place the receipt on a flat surface to avoid distortion</li>
                    <li><strong>Clear Focus:</strong> Make sure the text is sharp and readable</li>
                </ul>
            </div>

            <h2>Scanning from Mobile</h2>
            <p>Use the Argo Books mobile app for the fastest scanning experience:</p>
            <ol class="steps-list">
                <li>Open the app and tap the scan button</li>
                <li>Point your camera at the receipt</li>
                <li>The app automatically detects and captures the receipt</li>
                <li>Review and save - it syncs to your desktop automatically</li>
            </ol>

            <h2>Uploading from Desktop</h2>
            <p>You can also scan receipts from your computer:</p>
            <ol class="steps-list">
                <li>Go to "Add Expense" in Argo Books</li>
                <li>Click "Scan Receipt"</li>
                <li>Select an image file from your computer</li>
                <li>Review the extracted data and save</li>
            </ol>

            <h2>Accuracy and Corrections</h2>
            <p>Our AI achieves 99% accuracy on printed receipts. After scanning:</p>
            <ul>
                <li>Review all extracted fields before saving</li>
                <li>Make corrections directly in the review screen</li>
                <li>The AI learns from your corrections to improve future scans</li>
            </ul>

            <div class="page-navigation">
                <a href="ai-features.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: AI Features Overview
                </a>
                <a href="predictive-analytics.php" class="nav-button next">
                    Next: Predictive Analytics
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
