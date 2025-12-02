<?php
$pageTitle = 'Password Protection';
$pageDescription = 'Learn how to set up password protection and Windows Hello in Argo Books to secure your business data.';
$currentPage = 'password';

include 'docs-header.php';
include 'sidebar.php';
?>

        <!-- Main Content -->
        <main class="content">
            <section class="article">
                <h1>Password Protection</h1>
                <p>Secure access to your business data with robust password protection and Windows Hello integration.
                </p>

                <h2>Setting Up Password Protection</h2>
                <ol class="steps-list">
                    <li>Go to "Account > Settings > Security"</li>
                    <li>Click "Enable Password Protection"</li>
                    <li>Create a strong password</li>
                </ol>

                <h2>Setting Up Windows Hello (Paid Version)</h2>
                <ol class="steps-list">
                    <li>After setting up password protection, a "Enable Windows Hello" button will appear in the
                        Security settings</li>
                    <li>Click the button and Windows will prompt you to verify your identity</li>
                    <li>Once configured, you can use Windows Hello instead of your password for future logins</li>
                </ol>

                <div class="info-box">
                    <strong>Tip:</strong> Windows Hello options will only appear if your device has compatible hardware
                    (e.g., fingerprint reader or facial recognition camera) and Windows Hello is properly configured in
                    Windows Settings.
                </div>

                <div class="warning-box">
                    <strong>Important:</strong> Store your password securely in multiple locations. If you forget it,
                    your data cannot be recovered and will be lost forever!
                </div>

                <div class="page-navigation">
                    <a href="encryption.php" class="nav-button prev">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                        Previous: Encryption
                    </a>
                    <a href="backups.php" class="nav-button next">
                        Next: Regular Backups
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 18l6-6-6-6"></path>
                        </svg>
                    </a>
                </div>
            </section>
        </main>

<?php include 'docs-footer.php'; ?>
