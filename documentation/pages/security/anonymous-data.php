<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Anonymous Usage Data';
$pageDescription = 'Learn about the anonymous usage data collected by Argo Books and how to manage your privacy settings.';
$currentPage = 'anonymous-data';
$pageCategory = 'security';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Anonymous usage data is collected to help us improve Argo Books. We use it to understand how the software is used, identify and fix bugs, diagnose performance issues, prioritize new features, and optimize the app for different regions.</p>

            <h2>What Data is Collected</h2>
            <p>Only anonymous usage statistics about the desktop application are collected:</p>
            <ul>
                <li>Export operations (type, duration, file size)</li>
                <li>API usage (type, duration, success/failure)</li>
                <li>Error tracking (error category, exception type)</li>
                <li>Session data (session start/end, duration, app version, operating system)</li>
                <li>Geographic location (country, region, timezone)</li>
                <li>Feature usage (which features are used, e.g., receipt scanned, reports generated)</li>
            </ul>

            <p><strong>No personal information or business data is ever collected.</strong> Filenames, company names, customer or vendor names, transaction data, document contents, city-level location, and per-user identifiers are never sent.</p>

            <h2>Viewing and Deleting Your Data</h2>
            <p>The Privacy section under <em>Settings &gt; General</em> has two buttons:</p>
            <ul>
                <li><strong>View Data:</strong> Opens the folder where anonymous telemetry files are stored on your device, so you can review them directly. The files are plain JSON.</li>
                <li><strong>Delete All Data:</strong> Permanently removes all collected anonymous data from your device. Deleting clears the local copy. To also remove data that has already been uploaded to our servers, email <a class="link" href="mailto:contact@argorobots.com">contact@argorobots.com</a> and we will delete it.</li>
            </ul>

            <div class="page-navigation">
                <a href="backups.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Regular Backups</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
