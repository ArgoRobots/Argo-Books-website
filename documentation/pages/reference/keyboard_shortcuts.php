<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Keyboard Shortcuts';
$pageDescription = 'Reference guide for keyboard shortcuts in Argo Books, including the Report Generator layout designer.';
$currentPage = 'keyboard_shortcuts';
$pageCategory = 'reference';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <h2>Application Shortcuts</h2>
            <p>These shortcuts are available throughout the application:</p>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Ctrl + K</strong></td><td>Open search / quick actions</td></tr>
                        <tr><td><strong>Ctrl + Scroll</strong></td><td>Zoom a chart or an invoice preview</td></tr>
                        <tr><td><strong>Esc</strong></td><td>Close the open dialog or panel</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <strong>In the quick actions panel:</strong> type to search, use <strong>&uarr;</strong> and <strong>&darr;</strong> to move through the results, <strong>Enter</strong> to run the highlighted one, and <strong>Esc</strong> to close.
            </div>

            <h2>Report Generator Layout Designer</h2>
            <p>The following shortcuts are available when working in the layout designer (Step 2) of the Report Generator.</p>

            <h3>General Actions</h3>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Ctrl + Z</strong></td><td>Undo last action</td></tr>
                        <tr><td><strong>Ctrl + Y</strong></td><td>Redo last undone action</td></tr>
                        <tr><td><strong>Ctrl + Shift + Z</strong></td><td>Redo last undone action (alternative)</td></tr>
                        <tr><td><strong>Ctrl + S</strong></td><td>Save the current layout as a template</td></tr>
                        <tr><td><strong>Ctrl + G</strong></td><td>Show or hide the alignment grid</td></tr>
                    </tbody>
                </table>
            </div>

            <h3>Selection &amp; Editing</h3>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Ctrl + A</strong></td><td>Select all elements on the current page</td></tr>
                        <tr><td><strong>Ctrl + D</strong></td><td>Duplicate selected element(s)</td></tr>
                        <tr><td><strong>Delete</strong> or <strong>Backspace</strong></td><td>Delete selected element(s)</td></tr>
                        <tr><td><strong>Esc</strong></td><td>Clear the selection</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="info-box">
                <strong>Note:</strong> The canvas shortcuts above act on the current selection, so click an element first. Ctrl + A extends a selection to everything on the page rather than starting one from nothing.
            </div>

            <h3>Element Movement (Fine Control)</h3>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>&larr;</strong></td><td>Move element 1 pixel left</td></tr>
                        <tr><td><strong>&rarr;</strong></td><td>Move element 1 pixel right</td></tr>
                        <tr><td><strong>&uarr;</strong></td><td>Move element 1 pixel up</td></tr>
                        <tr><td><strong>&darr;</strong></td><td>Move element 1 pixel down</td></tr>
                    </tbody>
                </table>
            </div>

            <h3>Element Movement (Large Steps)</h3>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Shift + &larr;</strong></td><td>Move element 10 pixels left</td></tr>
                        <tr><td><strong>Shift + &rarr;</strong></td><td>Move element 10 pixels right</td></tr>
                        <tr><td><strong>Shift + &uarr;</strong></td><td>Move element 10 pixels up</td></tr>
                        <tr><td><strong>Shift + &darr;</strong></td><td>Move element 10 pixels down</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="page-navigation">
                <a href="supported-languages.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Supported Languages</span>
                </a>
                <a href="../security/encryption.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Encryption &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
