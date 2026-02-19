<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Regular Backups';
$pageDescription = 'Learn how to create and restore backups of your Argo Books data to prevent data loss.';
$currentPage = 'backups';
$pageCategory = 'security';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Regularly backing up your business data protects you against data loss from hardware failure, accidental deletion, or other unexpected events. We recommend creating backups at least weekly, or after entering significant amounts of new data.</p>

            <h2>Creating a Backup</h2>
            <ol class="steps-list">
                <li>Open your company file in Argo Books</li>
                <li>Click "File" in the top menu, then select "Export as..."</li>
                <li>Make sure you're in the "Backup" tab</li>
                <li>Choose a location to save the backup file</li>
                <li>Click "Save" to create the backup</li>
            </ol>

            <h2>Restoring from a Backup</h2>
            <p>To restore your data from a backup file:</p>
            <ol class="steps-list">
                <li>Open Argo Books</li>
                <li>Click "File" and select "Open"</li>
                <li>Navigate to your backup file and open it</li>
                <li>Your data will be loaded from the backup</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Argo Books stores all your company data in a single <strong>.argo</strong> file. This means you can also back up simply by copying the file to another location.
            </div>

            <h2>Backup Best Practices</h2>
            <ul>
                <li><strong>Back up regularly:</strong> At least once a week, or after any major data entry session</li>
                <li><strong>Use multiple locations:</strong> Store backups on a different device, external drive, or cloud storage service</li>
                <li><strong>Keep multiple versions:</strong> Don't overwrite old backups. Keep several recent copies in case you need to go back further</li>
                <li><strong>Test your backups:</strong> Periodically open a backup file to verify it contains your data correctly</li>
            </ul>

            <div class="page-navigation">
                <a href="password.php" class="nav-button prev">
                    <?= svg_icon('chevron-left', 16) ?>
                    Previous: Password Protection
                </a>
                <a href="anonymous-data.php" class="nav-button next">
                    Next: Anonymous Usage Data
                    <?= svg_icon('chevron-right', 16) ?>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
