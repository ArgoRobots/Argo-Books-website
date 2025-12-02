<?php
$pageTitle = 'Regular Backups';
$pageDescription = 'Learn how to create regular backups of your Argo Books data to prevent data loss.';
$currentPage = 'backups';

$pageCategory = 'security';
include '../../docs-header.php';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Regular Backups</h1>
                <p>It's crucial to regularly back up your business data to prevent any potential loss. We recommend
                    making backups at least weekly, or after entering significant amounts of new data.</p>

                <h2>Creating a Backup</h2>
                <ol class="steps-list">
                    <li>Click "File > Export as..."</li>
                    <li>Select "ArgoSales (.zip)" from the drop-down menu</li>
                    <li>Choose a location for your backup</li>
                    <li>Store backups in a secure location, preferably on a different device or in the cloud</li>
                </ol>

                <div class="warning-box">
                    <strong>Important:</strong> Regular backups are your safeguard against data loss due to hardware
                    failure, accidents, or other unforeseen circumstances. Make it a habit to back up your data
                    frequently!
                </div>

                <div class="page-navigation">
                    <a href="password.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Password Protection
                    </a>
                    <a href="anonymous-data.php" class="nav-button next">
                        Next: Anonymous Usage Data
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
