<?php
$pageTitle = 'Encryption';
$pageDescription = 'Learn about the AES-256 encryption used in Argo Books to protect your business data.';
$currentPage = 'encryption';

$pageCategory = 'security';
include '../../docs-header.php';
include '../../sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Encryption</h1>
                <p>Argo Books uses AES-256 encryption to protect your business data, the same standard used by
                    banks and military organizations.</p>

                <p>Encryption is automatic and requires no additional setup from users. It's enabled by
                    default, but can be disabled in the settings under the "Security" menu.</p>

                <div class="page-navigation">
                    <a href="../reference/supported-languages.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Supported Languages
                    </a>
                    <a href="password.php" class="nav-button next">
                        Next: Password Protection
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include '../../docs-footer.php'; ?>
