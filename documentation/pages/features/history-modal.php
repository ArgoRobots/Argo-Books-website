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
            <p>Click the clock icon (<?= svg_icon('clock', 16) ?>) in the header bar to open the Version History modal.</p>

            <h2>Event Timeline</h2>
            <p>The modal displays a chronological list of all changes. Each event shows:</p>
            <ul>
                <li><strong>Timestamp:</strong> When the change was made (stored in UTC for consistency across time zones)</li>
                <li><strong>Action Type:</strong> The kind of change — Added, Modified, Deleted, Undone, or Redone</li>
                <li><strong>Entity Details:</strong> The type and name of the item that was changed (e.g., a customer, product, or expense)</li>
                <li><strong>Description:</strong> A human-readable summary of what happened</li>
                <li><strong>Field-Level Changes:</strong> For modifications, a summary showing exactly which fields changed with their old and new values (e.g., "Name: 'Old Name' → 'New Name'")</li>
            </ul>

            <h3>Nested Undo/Redo Events</h3>
            <p>When you undo or redo a change, the resulting event is displayed as a nested sub-item under the original event. This keeps your timeline organized and makes it easy to trace the relationship between an action and its reversal.</p>

            <h2>Searching History</h2>
            <p>Use the search bar at the top of the modal to find specific events. The search uses fuzzy matching, so you don't need to type an exact name — close matches will still appear in the results.</p>

            <h2>Filtering Events</h2>
            <p>Use the inline filters to narrow down the event list:</p>
            <ul>
                <li><strong>By Action Type:</strong> Show only Added, Modified, or Deleted events</li>
                <li><strong>Undone:</strong> Filter to show only events that have been undone</li>
            </ul>

            <h2>Undo and Redo</h2>
            <p>You can reverse or reapply changes directly from the history modal.</p>
            <ul>
                <li><strong>Undo:</strong> Revert a specific change to restore the previous state</li>
                <li><strong>Redo:</strong> Reapply a previously undone change</li>
                <li><strong>Selective Undo/Redo:</strong> Undo or redo individual changes without affecting other events that happened before or after</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> The system automatically prevents actions that don't make sense — for example, you can't undo an "Add" if the item was later deleted. This ensures your data stays consistent.
            </div>

            <h2>First-Visit Hints</h2>
            <p>The first time you open Version History, helpful hints will appear to guide you through the interface. You can dismiss these hints or choose "Don't show again" to hide them permanently.</p>

            <h2>Backup and Restore</h2>
            <p>Version history is included when you create backups of your company data:</p>
            <ul>
                <li><strong>Backup:</strong> Export your company data as an <code>.argobk</code> file — the full event history is included in the backup</li>
                <li><strong>Restore:</strong> Restoring a backup creates a new company file with the complete history intact, without affecting your current data</li>
                <li><strong>Attachments:</strong> You can optionally include file attachments in your backup</li>
            </ul>

            <div class="info-box">
                <strong>Tip:</strong> For more details on creating and restoring backups, see <a class="link" href="../security/backups.php">Regular Backups</a>.
            </div>

            <div class="page-navigation">
                <a href="spreadsheet-export.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Spreadsheet Export
                </a>
                <a href="../reference/accepted-countries.php" class="nav-button next">
                    Next: Accepted Countries
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
