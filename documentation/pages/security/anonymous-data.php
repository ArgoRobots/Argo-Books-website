<?php
$pageTitle = 'Anonymous Usage Data';
$pageDescription = 'Learn about the anonymous usage data collected by Argo Books and how to manage your privacy settings.';
$currentPage = 'anonymous-data';

include '../../docs-header.php';
$pageCategory = 'security';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Anonymous Usage Data</h1>
                <p>Argo Books desktop application collects anonymous usage statistics and geo-location data to
                    help us improve the software by understanding how it's being used, identifying performance issues,
                    and prioritizing new features. This feature is enabled by default.</p>

                <h2>Managing Anonymous Data Collection</h2>
                <ol class="steps-list">
                    <li>Go to "Settings > General" in the desktop application</li>
                    <li>Find the "Anonymous Usage Data" setting</li>
                    <li>Toggle the switch to disable data collection if desired</li>
                </ol>

                <h2>What Data is Collected</h2>
                <p>Only anonymous usage statistics about the desktop application are collected, such as:</p>
                <ul>
                    <li>Export operations (type, duration, file size)</li>
                    <li>API usage (type, duration, tokens)</li>
                    <li>Error tracking (error category, error code, timestamp) - helps us identify and fix software
                        issues</li>
                    <li>Session data (session start/end, duration)</li>
                    <li>Geographic location (country, region, city, timezone)</li>
                    <li>Hashed IP addresses (one-way encrypted, cannot be reversed to identify you)</li>
                </ul>
                <br>
                <p><b>No personal information or business data is ever collected.</b></p>

                <h2>Exporting Your Anonymous Data</h2>
                <p>You can export and review all the anonymous data stored on your device:</p>
                <ol class="steps-list">
                    <li>Go to "Settings > General" in the desktop application</li>
                    <li>Next to the "Anonymous Usage Data" setting, click "Export Data"</li>
                    <li>Choose a location to save the JSON file</li>
                    <li>Open the file with any text editor to review its contents</li>
                </ol>

                <div class="page-navigation">
                    <a href="backups.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Regular Backups
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
