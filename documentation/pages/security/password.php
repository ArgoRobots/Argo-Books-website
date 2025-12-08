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
                <li>Go to "Account > Settings > Security"</li>
                <li>Click "Enable Password Protection"</li>
                <li>Create a strong password</li>
            </ol>

            <h2>Setting Up Biometric Login (Paid Version)</h2>
            <p>Argo Books supports biometric authentication on all major operating systems, allowing you to use fingerprint or facial recognition for quick, secure access.</p>

            <h3>Windows (Windows Hello)</h3>
            <ol class="steps-list">
                <li>After setting up password protection, an "Enable Biometric Login" button will appear in the Security settings</li>
                <li>Click the button and Windows Hello will prompt you to verify your identity</li>
                <li>Once configured, you can use fingerprint, facial recognition, or PIN instead of your password</li>
            </ol>

            <h3>macOS (Touch ID)</h3>
            <ol class="steps-list">
                <li>Ensure Touch ID is set up in System Preferences > Touch ID</li>
                <li>After setting up password protection, click "Enable Biometric Login" in Security settings</li>
                <li>Authenticate with Touch ID when prompted</li>
                <li>You can now use your fingerprint to unlock Argo Books</li>
            </ol>

            <h3>Linux (Fingerprint Authentication)</h3>
            <ol class="steps-list">
                <li>Ensure fingerprint authentication is configured in your system settings (fprintd)</li>
                <li>After setting up password protection, click "Enable Biometric Login" in Security settings</li>
                <li>Scan your fingerprint when prompted</li>
                <li>You can now use your fingerprint to unlock Argo Books</li>
            </ol>

            <div class="info-box">
                <strong>Tip:</strong> Biometric login options will only appear if your device has compatible hardware (e.g., fingerprint reader or facial recognition camera) and biometric authentication is properly configured in your operating system settings.
            </div>

            <div class="warning-box">
                <strong>Important:</strong> Store your password securely in multiple locations. If you forget it, your data cannot be recovered and will be lost forever!
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
