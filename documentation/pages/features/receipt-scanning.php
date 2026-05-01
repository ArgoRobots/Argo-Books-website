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
                <p><strong>Plan limits:</strong> The Free plan includes 5 AI receipt scans per month. Premium includes 500 per month.
                <a href="../getting-started/version-comparison.php" class="link">Compare versions</a></p>
            </div>

            <p>Turn any receipt into a digital record in under 5 seconds with our AI-powered scanner. It achieves 99% accuracy and lets you quickly make adjustments before saving.</p>

            <img src="../../../resources/images/ai-receipt-scanner.webp" alt="AI Receipt Scanner" style="width: 75%; display: block; margin: 0 auto 2rem auto;">

            <h2>What You Can Scan</h2>
            <p>The scanner isn't limited to paper. You can use:</p>
            <ul>
                <li><strong>Photos</strong> of printed or handwritten receipts</li>
                <li><strong>Screenshots</strong> of digital receipts and email invoices</li>
                <li><strong>Image files</strong> in common formats (JPG, PNG)</li>
                <li><strong>PDFs</strong> of receipts or invoices</li>
            </ul>
            <p>It also handles faded thermal paper, wrinkled receipts, and poor lighting — and supports multi-currency for international purchases.</p>
            <p>As a rule of thumb, if you can read the receipt in the image, Argo Books can too.</p>

            <h2>How It Works</h2>
            <ol class="steps-list">
                <li>Take a photo of your receipt with your phone or upload an image or PDF</li>
                <li>Our AI analyzes the receipt and extracts all relevant information</li>
                <li>Review the extracted data and make any corrections</li>
                <li>Save to automatically create a categorized expense record</li>
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

            <h2>Privacy</h2>
            <p>AI processing uses a secure API call to extract the data, but your receipt images and all extracted information are stored locally on your computer. No receipt data is kept on third-party servers after processing.</p>

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
