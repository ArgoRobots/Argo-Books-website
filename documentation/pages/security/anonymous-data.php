<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Anonymous Usage Data';
$pageDescription = 'Learn about the anonymous usage data collected by Argo Books and how to manage your privacy settings.';
$currentPage = 'anonymous-data';
$pageCategory = 'security';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Argo Books desktop application collects anonymous usage statistics and geo-location data to help us improve the software by understanding how it's being used, identifying performance issues, and prioritizing new features. This feature is enabled by default.</p>

            <h2>Managing Anonymous Data Collection</h2>
            <ol class="steps-list">
                <li>Go to "Settings > General" in the desktop application</li>
                <li>Scroll to the Privacy section and find the "Anonymous Data Collection" toggle</li>
                <li>Toggle the switch to disable data collection if desired</li>
            </ol>

            <h2>What Data is Collected</h2>
            <p>Only anonymous usage statistics about the desktop application are collected, such as:</p>
            <ul>
                <li>Export operations (type, duration, file size)</li>
                <li>API usage (type, duration, tokens)</li>
                <li>Error tracking (error category, error code, timestamp) - helps us identify and fix software issues</li>
                <li>Session data (session start/end, duration)</li>
                <li>Geographic location (country, region, city, timezone)</li>
                <li>Hashed IP addresses (one-way encrypted, cannot be reversed to identify you)</li>
            </ul>

            <p><strong>No personal information or business data is ever collected.</strong></p>

            <h2>Viewing and Deleting Your Data</h2>
            <p>When data collection is enabled, two buttons appear below the toggle:</p>
            <ul>
                <li><strong>View Data:</strong> Opens the folder where anonymous telemetry files are stored on your device, so you can review them directly.</li>
                <li><strong>Delete All Data:</strong> Permanently removes all collected anonymous data from your device.</li>
            </ul>

            <div class="page-navigation">
                <a href="backups.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Regular Backups</span>
                </a>
            </div>
        </div>

<?php include '../../docs-footer.php'; ?>
