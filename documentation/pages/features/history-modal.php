<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Version History';
$pageDescription = 'Learn how to use the Version History modal in Argo Books to review changes, undo or redo actions, search past events, and restore previous states of your data.';
$currentPage = 'history-modal';
$pageCategory = 'features';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>The Version History modal gives you a complete timeline of every change made to your company data. You can review past actions, undo or redo specific changes, and search through your history to find exactly what you're looking for.</p>

            <h2>Opening Version History</h2>
            <p>Click the clock icon in the header bar to open the Version History modal.</p>

            <h2>Event Timeline</h2>
            <p>The modal displays a chronological list of all changes. Each event shows:</p>
            <ul>
                <li><strong>Timestamp:</strong> When the change was made</li>
                <li><strong>Action Type:</strong> The kind of change — Added, Modified, Deleted, Undone, or Redone</li>
                <li><strong>Entity Details:</strong> The type and name of the item that was changed (e.g., a customer, product, or expense)</li>
                <li><strong>Description:</strong> A summary of what happened</li>
            </ul>

            <h3>Nested Undo/Redo Events</h3>
            <p>When you undo or redo a change, the resulting event is displayed as a nested sub-item under the original event. This keeps your timeline organized and makes it easy to trace the relationship between an action and its reversal.</p>

            <h2>Searching History</h2>
            <p>Use the search bar or filter controls to find specific events.</p>

            <div class="info-box">
                <strong>Tip:</strong> The system automatically prevents actions that don't make sense — for example, you can't undo an "Add" if the item was later deleted. This ensures your data stays consistent.
            </div>

            <div class="page-navigation">
                <a href="spreadsheet-export.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Spreadsheet Export</span>
                </a>
                <a href="../reference/supported-currencies.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Supported Currencies &rarr;</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
