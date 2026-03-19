<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Keyboard Shortcuts';
$pageDescription = 'Reference guide for keyboard shortcuts in the Argo Books Report Generator. Speed up your workflow with shortcuts for movement, alignment, and editing.';
$currentPage = 'keyboard_shortcuts';
$pageCategory = 'reference';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Speed up your workflow in the Report Generator with these keyboard shortcuts. These shortcuts are available when working in the layout designer (Step 2) of the Report Generator.</p>

            <h2>General Actions</h2>
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
                    </tbody>
                </table>
            </div>

            <h2>Selection & Editing</h2>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Ctrl + A</strong></td><td>Select all elements on the canvas</td></tr>
                        <tr><td><strong>Ctrl + D</strong></td><td>Duplicate selected element(s)</td></tr>
                        <tr><td><strong>Delete</strong></td><td>Delete selected element(s)</td></tr>
                    </tbody>
                </table>
            </div>

            <h2>Element Movement (Fine Control)</h2>
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

            <h2>Element Movement (Large Steps)</h2>
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

            <h2>Alignment</h2>
            <div class="comparison-table-wrapper">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Shortcut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>Ctrl + &larr;</strong></td><td>Align selected elements to the left</td></tr>
                        <tr><td><strong>Ctrl + &rarr;</strong></td><td>Align selected elements to the right</td></tr>
                        <tr><td><strong>Ctrl + &uarr;</strong></td><td>Align selected elements to the top</td></tr>
                        <tr><td><strong>Ctrl + &darr;</strong></td><td>Align selected elements to the bottom</td></tr>
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

<?php include '../../docs-footer.php'; ?>
