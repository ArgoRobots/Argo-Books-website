<?php
$pageTitle = 'Password Protection';
$pageDescription = 'Learn how to set up password protection and biometric login in Argo Books to secure your business data.';
$currentPage = 'password';
$pageCategory = 'security';

include '../../docs-header.php';
?>

        <div class="docs-content">
            <p>Secure access to your business data with robust password protection and biometric login integration.</p>

            <h2>Setting Up Password Protection</h2>
            <ol class="steps-list">
                <li>Go to "Settings > Security"</li>
                <li>Click "Add Password"</li>
                <li>Create a strong password</li>
            </ol>

            <h2>Setting Up Biometric Login (Paid Version)</h2>
            <p>Argo Books supports biometric authentication on Windows, Linux, and macOS, allowing you to use fingerprint or facial recognition for quick, secure access.</p>

            <ol class="steps-list">
                <li>After setting up a password, a toggle button will appear below</li>
                <li>Click the toggle button and your computer will prompt you to verify your identity</li>
                <li>Once configured, you can use fingerprint, facial recognition, or a PIN instead of your password</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Biometric login options will only appear if your device has compatible hardware (e.g., fingerprint reader or facial recognition camera) and biometric authentication is properly configured in your operating system settings.
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
        </div>

<?php include '../../docs-footer.php'; ?>
