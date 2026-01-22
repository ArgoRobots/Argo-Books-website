<?php
$pageTitle = 'Report Generator';
$pageDescription = 'Learn how to generate professional reports with charts and analytics in Argo Books using the 3-step wizard.';
$currentPage = 'report-generator';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Create professional, customized reports with charts and analytics for presentations and financial analysis. The Report Generator uses a simple 3-step wizard to guide you through the process.</p>

            <h2>How to Generate a Report</h2>
            <ol class="steps-list">
                <li>Go to "Reports" in the sidebar (under Main)</li>
                <li>Follow the 3-step wizard to create your custom report</li>
            </ol>

            <h2>Step 1: Template & Settings</h2>
            <p>Choose a starting point for your report.</p>
            <ul>
                <li><strong>Select a Template:</strong> Choose from pre-built templates or create your own</li>
                <li><strong>Name Your Report:</strong> Enter a name for your report</li>
                <li><strong>Set Date Range:</strong> Use quick presets (Last Month, Last 3 Months, etc.) or choose start and end dates</li>
            </ul>

            <h2>Step 2: Layout Designer</h2>
            <p>Design your report using drag-and-drop functionality.</p>
            <ul>
                <li><strong>Add Charts:</strong> Choose from 40+ chart types across categories like Revenue, Expenses, Financial, Geographic, Customers, Returns, and more</li>
                <li><strong>Add Elements:</strong> Include text labels, images, date ranges, summary statistics, and tables</li>
                <li><strong>Drag and Drop:</strong> Click and drag elements to position them on the canvas</li>
                <li><strong>Resize:</strong> Select an element and drag the corner handles to resize</li>
                <li><strong>Customize:</strong> Use the properties panel to adjust colors, fonts, borders, and alignment</li>
                <li><strong>Alignment Tools:</strong> Align and distribute multiple elements using the toolbar</li>
                <li><strong>Undo/Redo:</strong> Use Ctrl+Z and Ctrl+Y to undo or redo changes</li>
            </ul>

            <h2>Step 3: Preview and Export</h2>
            <p>Review your report and export in your preferred format.</p>
            <ul>
                <li><strong>Preview:</strong> See how your finished report will look</li>
                <li><strong>Export Format:</strong> Choose PNG, JPEG, or PDF</li>
                <li><strong>Export:</strong> Select your save location and click "Export"</li>
            </ul>

            <div class="page-navigation">
                <a href="spreadsheet-export.php" class="nav-button prev">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6"></path>
                    </svg>
                    Previous: Spreadsheet Export
                </a>
                <a href="advanced-search.php" class="nav-button next">
                    Next: Quick Actions
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 18l6-6-6-6"></path>
                    </svg>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
