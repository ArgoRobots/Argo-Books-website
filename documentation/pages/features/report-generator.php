<?php
$pageTitle = 'Report Generator';
$pageDescription = 'Learn how to generate professional reports with charts and analytics in Argo Books using the 3-step wizard.';
$currentPage = 'report-generator';

include '../../docs-header.php';
$pageCategory = 'features';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Report Generator</h1>
                <p>Create professional, customized reports with charts and analytics for presentations, and financial analysis. The Report Generator uses a simple 3-step wizard to guide you through the process.</p>

                <h2>How to Generate a Report</h2>
                <ol class="steps-list">
                    <li>Go to "File > Generate Report"</li>
                    <li>Follow the 3-step wizard to create your custom report</li>
                </ol>

                <h2>Step 1: Data Selection</h2>
                <p>Choose what data to include in your report.</p>
                <ul>
                    <li><strong>Start with Templates:</strong> Use pre-built templates like Monthly Sales, Financial Overview, Performance Analysis, Returns Analysis, Losses Analysis, or Geographic Analysis</li>
                    <li><strong>Select Charts:</strong> Choose from available charts including sales, purchases, profits, distributions, returns, losses, and geographic data</li>
                    <li><strong>Set Date Range:</strong> Use quick presets (Last Month, Last 3 Months, etc.) or choose custom dates</li>
                    <li><strong>Apply Filters:</strong> Filter by categories, products, companies, countries, or include/exclude returns and losses</li>
                </ul>

                <h2>Step 2: Layout Designer</h2>
                <p>Arrange your report using drag-and-drop functionality.</p>
                <ul>
                    <li><strong>Add Elements:</strong> Include text labels, images, logos, date ranges, and summary boxes</li>
                    <li><strong>Drag and Drop:</strong> Click and drag elements to position them on the page</li>
                    <li><strong>Resize:</strong> Select an element and drag the corner handles to resize</li>
                    <li><strong>Customize:</strong> Adjust colors, borders, alignment, and other properties</li>
                    <li><strong>Undo/Redo:</strong> Use Ctrl+Z and Ctrl+Y to undo or redo changes</li>
                </ul>

                <h2>Step 3: Preview and Export</h2>
                <p>Review your report and export in your preferred format.</p>
                <ul>
                    <li><strong>Preview:</strong> Use zoom controls to examine your report in detail</li>
                    <li><strong>Export Format:</strong> Choose PNG (high quality), JPG (smaller files), or PDF (professional printing)</li>
                    <li><strong>Quality:</strong> Adjust the quality slider to balance file size and image quality</li>
                    <li><strong>Export:</strong> Select your save location and click "Export"</li>
                </ul>

                <div class="info-box">
                    <strong>Tip:</strong> The Report Generator supports keyboard shortcuts for faster workflow. <a class="link" href="references/keyboard_shortcuts.php">View all available shortcuts</a>.
                </div>

                <div class="page-navigation">
                    <a href="spreadsheet-export.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Spreadsheet Export
                    </a>
                    <a href="advanced-search.php" class="nav-button next">
                        Next: Advanced Search
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
